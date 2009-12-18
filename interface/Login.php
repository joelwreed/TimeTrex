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
 * $Revision: 2058 $
 * $Id: Login.php 2058 2008-08-21 19:16:38Z ipso $
 * $Date: 2008-08-21 12:16:38 -0700 (Thu, 21 Aug 2008) $
 */
require_once('../includes/global.inc.php');

$authenticate=FALSE;
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

$smarty->assign('title', TTi18n::gettext('Secure Login'));

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'user_name',
												'password',
												'password_reset',
												'language',
												) ) );

$validator = new Validator();

$action = Misc::findSubmitButton();
switch ($action) {
	case 'submit':
		//Debug::setVerbosity( 11 );
		Debug::Text('User Name: '. $user_name, __FILE__, __LINE__, __METHOD__,10);

		if ( $authentication->Login($user_name, $password) ) {
			$authentication->Check();

			Debug::text('Login Language: '. $language, __FILE__, __LINE__, __METHOD__, 10);

			TTi18n::setCountry( TTi18n::getCountryFromLocale() );
			TTi18n::setLanguage( $language );
			TTi18n::setLocale();
			TTi18n::setLocaleCookie();

			Debug::text('Locale: '. TTi18n::getLocale(), __FILE__, __LINE__, __METHOD__, 10);

			$clf = new CompanyListFactory();
			$clf->getByID( $authentication->getObject()->getCompany() );
			$current_company = $clf->getCurrent();
			unset($clf);

			$create_new_station = FALSE;
			//If this is a new station, insert it now.
			if ( isset( $_COOKIE['StationID'] ) ) {
				Debug::text('Station ID Cookie found! '. $_COOKIE['StationID'], __FILE__, __LINE__, __METHOD__, 10);

				$slf = new StationListFactory();
				$slf->getByStationIdandCompanyId( $_COOKIE['StationID'], $current_company->getId() );
				$current_station = $slf->getCurrent();
				unset($slf);

				if ( $current_station->isNew() ) {
					Debug::text('Station ID is NOT IN DB!! '. $_COOKIE['StationID'], __FILE__, __LINE__, __METHOD__, 10);
					$create_new_station = TRUE;
				}
			} else {
				$create_new_station = TRUE;
			}

			if ( $create_new_station == TRUE ) {
				//Insert new station
				$sf = new StationFactory();

				$sf->setCompany( $current_company->getId() );
				$sf->setStatus( 'ENABLED' );
				$sf->setType( 'PC' );
				$sf->setSource( $_SERVER['REMOTE_ADDR'] );
				$sf->setStation();
				$sf->setDescription( substr( $_SERVER['HTTP_USER_AGENT'], 0, 250) );
				if ( $sf->Save(FALSE) ) {
					$sf->setCookie();
				}
			}

			Redirect::Page( URLBuilder::getURL( NULL, 'index.php' ) );
		} else {
			$error_message = TTi18n::gettext('User Name or Password is incorrect');

			//Get company status from user_name, so we can display messages for ONHOLD/Cancelled accounts.
			$clf = new CompanyListFactory();
			$clf->getByUserName( $user_name );
			if ( $clf->getRecordCount() > 0 ) {
				$c_obj = $clf->getCurrent();
				if ( $c_obj->getStatus() == 20 ) {
					$error_message = TTi18n::gettext('Sorry, your company\'s account has been placed ON HOLD, please contact customer support immediately');
				} elseif ( $c_obj->getStatus() == 30 ) {
					$error_message = TTi18n::gettext('Sorry, your company\'s account has been CANCELLED, please contact customer support if you believe this is an error');
				}
			}

			$validator->isTrue('user_name',FALSE, $error_message );
		}

		break;
	default:
		break;
}

$smarty->assign_by_ref('user_name', $user_name);
$smarty->assign_by_ref('password', $password);
$smarty->assign_by_ref('password_reset', $password_reset);

$smarty->assign('language_options', TTi18n::getLanguageArray() );

if ( $language == '' ) {
	$language = TTi18n::getLanguageFromLocale();
} elseif ( strlen($language) >= 4) {
	$language = TTi18n::getLanguageFromLocale( $language );
}

$smarty->assign('language', $language );

$smarty->assign_by_ref('validator', $validator);

$smarty->display('Login.tpl');
?>