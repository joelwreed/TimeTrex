<?php
/*********************************************************************************
 * TimeTrex is a Payroll and Time Management program developed by
 * TimeTrex Payroll Services Copyright (C) 2003 - 2010 TimeTrex Payroll Services.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by
 * the Free Software Foundation with the addition of the following permission
 * added to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED
 * WORK IN WHICH THE COPYRIGHT IS OWNED BY TIMETREX, TIMETREX DISCLAIMS THE
 * WARRANTY OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along
 * with this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact TimeTrex headquarters at Unit 22 - 2475 Dobbin Rd. Suite
 * #292 Westbank, BC V4T 2E9, Canada or at email address info@timetrex.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * "Powered by TimeTrex" logo. If the display of the logo is not reasonably
 * feasible for technical reasons, the Appropriate Legal Notices must display
 * the words "Powered by TimeTrex".
 ********************************************************************************/
/*
 * $Revision: 1246 $
 * $Id: ForgotPassword.php 1246 2007-09-14 23:47:42Z ipso $
 * $Date: 2007-09-14 16:47:42 -0700 (Fri, 14 Sep 2007) $
 */
require_once('../includes/global.inc.php');

//Debug::setVerbosity( 11 );

$authenticate=FALSE;
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

$smarty->assign('title', TTi18n::gettext('Password Reset'));

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'email',
												'key',
												'email_sent',
												'password',
												'password2',
												) ) );

$validator = new Validator();

$action = Misc::findSubmitButton();
Debug::Text('Action: '. $action, __FILE__, __LINE__, __METHOD__,10);
switch ($action) {
	case 'change_password':
		Debug::Text('Change Password: '. $key, __FILE__, __LINE__, __METHOD__,10);
		$ulf = new UserListFactory();
		$ulf->getByPasswordResetKey( $key );
		if ( $ulf->getRecordCount() == 1 ) {
			Debug::Text('FOUND Password reset key! ', __FILE__, __LINE__, __METHOD__,10);

			$user_obj = $ulf->getCurrent();
			$user_name = $user_obj->getUserName();

			//Make sure passwords match
			if ( $password == $password2 ) {
				//Change password

				$user_obj->setPassword( $password );
				$user_obj->setPasswordResetKey('');
				$user_obj->setPasswordResetDate('');
				if ( $user_obj->isValid() ) {
					$user_obj->Save();

					Debug::Text('Password Change succesful!', __FILE__, __LINE__, __METHOD__,10);

					Redirect::Page( URLBuilder::getURL( array('password_reset' => 1 ), 'Login.php' ) );
				}

			} else {

				$validator->isTrue('password',FALSE, 'Passwords do not match');
			}

		} else {
			Debug::Text('DID NOT FIND Password reset key! ', __FILE__, __LINE__, __METHOD__,10);
			$action = 'reset_password';
		}

		break;
	case 'password_reset':
		//Debug::setVerbosity( 11 );
		Debug::Text('Key: '. $key, __FILE__, __LINE__, __METHOD__,10);
		$ulf = new UserListFactory();
		$ulf->getByPasswordResetKey( $key );
		if ( $ulf->getRecordCount() == 1 ) {
			Debug::Text('FOUND Password reset key! ', __FILE__, __LINE__, __METHOD__,10);
			$user_obj = $ulf->getCurrent();

			$user_name = $user_obj->getUserName();

		} else {
			Debug::Text('DID NOT FIND Password reset key! ', __FILE__, __LINE__, __METHOD__,10);
			$action = 'reset_password';
		}

		break;
	case 'reset_password':
		//Debug::setVerbosity( 11 );
		Debug::Text('Email: '. $email, __FILE__, __LINE__, __METHOD__,10);

		$ulf = new UserListFactory();
		$ulf->getByHomeEmailOrWorkEmail( $email );
		if ( $ulf->getRecordCount() == 1 ) {
			$user_obj = $ulf->getCurrent();
			$user_obj->sendPasswordResetEmail();
			Debug::Text('Found USER! ', __FILE__, __LINE__, __METHOD__,10);

			Redirect::Page( URLBuilder::getURL( array('email_sent' => 1, 'email' => $email ), 'ForgotPassword.php' ) );
		} else {
			//Error
			Debug::Text('DID NOT FIND USER! ', __FILE__, __LINE__, __METHOD__,10);
			$validator->isTrue('email',FALSE, 'Email address was not found in our database');
		}
/*
		$validator->isTrue('user_name',FALSE, 'User Name or Password is incorrect');
*/
		break;
	default:
		break;
}

$smarty->assign_by_ref('email', $email);
$smarty->assign_by_ref('email_sent', $email_sent);
$smarty->assign_by_ref('key', $key);
$smarty->assign_by_ref('user_name', $user_name);
$smarty->assign_by_ref('action', $action);

$smarty->assign_by_ref('validator', $validator);

$smarty->display('ForgotPassword.tpl');
?>