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
 * $Revision: 2391 $
 * $Id: EditUserAbsence.php 2391 2009-01-27 01:04:39Z ipso $
 * $Date: 2009-01-26 17:04:39 -0800 (Mon, 26 Jan 2009) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

//Debug::setVerbosity(11);
if ( !$permission->Check('absence','enabled')
		OR !( $permission->Check('absence','edit')
				OR $permission->Check('absence','edit_own')
				OR $permission->Check('absence','edit_child')
				 ) ) {
	$permission->Redirect( FALSE ); //Redirect
}

$smarty->assign('title', TTi18n::gettext($title = 'Edit Absence')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'id',
												'user_id',
												'date_stamp',
												'udt_data'
												) ) );

if ( isset($udt_data) ) {
	if ( $udt_data['total_time'] != '') {
		$udt_data['total_time'] = TTDate::parseTimeUnit( $udt_data['total_time'] ) ;
	}
}

$udtf = new UserDateTotalFactory();

$action = Misc::findSubmitButton();
$action = strtolower($action);
switch ($action) {
	case 'delete':
		Debug::Text('Delete!', __FILE__, __LINE__, __METHOD__,10);
		//Debug::setVerbosity(11);

		$udtlf = new UserDateTotalListFactory();
		$udtlf->getById( $udt_data['id'] );
		if ( $udtlf->getRecordCount() > 0 ) {
			foreach($udtlf as $udt_obj) {
				$udt_obj->setDeleted(TRUE);
				if ( $udt_obj->isValid() ) {
					$udt_obj->setEnableCalcSystemTotalTime( TRUE );
					$udt_obj->setEnableCalcWeeklySystemTotalTime( TRUE );
					$udt_obj->setEnableCalcException( TRUE );
					$udt_obj->Save();
				}
			}
		}

		Redirect::Page( URLBuilder::getURL( array('refresh' => TRUE ), '../CloseWindow.php') );

		break;
	case 'submit':
		Debug::Text('Submit!', __FILE__, __LINE__, __METHOD__,10);
		//Debug::setVerbosity(11);

		//Limit it to 31 days.
		if ( $udt_data['repeat'] > 31 ) {
			$udt_data['repeat'] = 31;
		}
		Debug::Text('Repeating Punch For: '. $udt_data['repeat'] .' Days', __FILE__, __LINE__, __METHOD__,10);

		$udtf->StartTransaction();

		$fail_transaction = FALSE;
		for($i=0; $i <= (int)$udt_data['repeat']; $i++ ) {
			Debug::Text('Absence Repeat: '. $i, __FILE__, __LINE__, __METHOD__,10);

			if ( $i == 0 ) {
				$date_stamp = $udt_data['date_stamp'];
			} else {
				$date_stamp = $udt_data['date_stamp'] + (86400 * $i);
			}
			Debug::Text('Date Stamp: '. TTDate::getDate('DATE+TIME', $date_stamp), __FILE__, __LINE__, __METHOD__,10);

			if ( $i == 0 AND $udt_data['id'] != '' ) {
				//Because if a user modifies the type of absence, the accrual balances
				//may come out of sync. Instead of just editing the entry directly, lets
				//delete the old one, and insert it as new.
				if ( $udt_data['absence_policy_id'] == $udt_data['old_absence_policy_id'] ) {
					Debug::Text('Editing absence, absence policy DID NOT change', __FILE__, __LINE__, __METHOD__,10);
					$udtf->setId($udt_data['id']);
				} else {
					Debug::Text('Editing absence, absence policy changed, deleting old record ID: '. $udt_data['id'] , __FILE__, __LINE__, __METHOD__,10);
					$udtlf = new UserDateTotalListFactory();
					$udtlf->getById( $udt_data['id'] );
					if ( $udtlf->getRecordCount() == 1 ) {
						$udt_obj = $udtlf->getCurrent();
						$udt_obj->setDeleted(TRUE);
						if ( $udt_obj->isValid() ) {
							$udt_obj->Save();
						}
					}
					unset($udtlf, $udt_obj);
				}
			}

			$udtf->setUserDateId( UserDateFactory::findOrInsertUserDate($udt_data['user_id'], $date_stamp) );
			$udtf->setStatus( 30 ); //Absence
			$udtf->setType( 10 ); //Total
			$udtf->setAbsencePolicyID( $udt_data['absence_policy_id'] ); //Total
			$udtf->setBranch($udt_data['branch_id']);
			$udtf->setDepartment($udt_data['department_id']);
			$udtf->setTotalTime($udt_data['total_time']);
			if ( isset($udt_data['override']) ) {
				$udtf->setOverride(TRUE);
			} else {
				$udtf->setOverride(FALSE);
			}

			if ( $udtf->isValid() ) {
				$udtf->setEnableCalcSystemTotalTime(TRUE);
				$udtf->setEnableCalcWeeklySystemTotalTime( TRUE );
				$udtf->setEnableCalcException( TRUE );

				if ( $udtf->Save() != TRUE ) {
					$fail_transaction = TRUE;
					break;
				}
			} else {
				$fail_transaction = TRUE;
				break;
			}
		}

		if ( $fail_transaction == FALSE ) {
			//$udtf->FailTransaction();
			$udtf->CommitTransaction();

			Redirect::Page( URLBuilder::getURL( array('refresh' => TRUE ), '../CloseWindow.php') );
			break;
		} else {
			$udtf->FailTransaction();
		}

	default:
		/*

		Don't allow editing System time. If they want to force a bank time
		they can just add that to the accrual, and either set a time pair to 0
		or enter a Absense Dock (only for salary) employees.

		However when you do a Absense dock, what hours is it docking from,
		total, regular,overtime?

		*/
		if ( $id != '' ) {
			Debug::Text(' ID was passed: '. $id, __FILE__, __LINE__, __METHOD__,10);

			$udtlf = new UserDateTotalListFactory();
			$udtlf->getById( $id );

			foreach ($udtlf as $udt_obj) {
				//Debug::Arr($station,'Department', __FILE__, __LINE__, __METHOD__,10);

				$udt_data = array(
									'id' => $udt_obj->getId(),
									'user_date_id' => $udt_obj->getUserDateId(),
									'date_stamp' => $udt_obj->getUserDateObject()->getDateStamp(),
									'user_id' => $udt_obj->getUserDateObject()->getUser(),
									'user_full_name' => $udt_obj->getUserDateObject()->getUserObject()->getFullName(),
									'status_id' => $udt_obj->getStatus(),
									'type_id' => $udt_obj->getType(),
									'total_time' => $udt_obj->getTotalTime(),
									'absence_policy_id' => $udt_obj->getAbsencePolicyID(),
									'branch_id' => $udt_obj->getBranch(),
									'department_id' => $udt_obj->getDepartment(),
									'override' => $udt_obj->getOverride(),
									'created_date' => $udt_obj->getCreatedDate(),
									'created_by' => $udt_obj->getCreatedBy(),
									'updated_date' => $udt_obj->getUpdatedDate(),
									'updated_by' => $udt_obj->getUpdatedBy(),
									'deleted_date' => $udt_obj->getDeletedDate(),
									'deleted_by' => $udt_obj->getDeletedBy()
								);
			}
		} elseif ( $action != 'submit' ) {
			Debug::Text(' ID was NOT passed: '. $id, __FILE__, __LINE__, __METHOD__,10);

			//Get user full name
			$ulf = new UserListFactory();
			$user_obj = $ulf->getById( $user_id )->getCurrent();
			$user_date_id = UserDateFactory::getUserDateID($user_id, $date_stamp);

			$udt_data = array(
								'user_id' => $user_id,
								'date_stamp' => $date_stamp,
								'user_date_id' => $user_date_id,
								'user_full_name' => $user_obj->getFullName(),
								'branch_id' => $user_obj->getDefaultBranch(),
								'department_id' => $user_obj->getDefaultDepartment(),
								'total_time' => 0,
								'override' => TRUE
							);
		}

		$aplf = new AbsencePolicyListFactory();
		$absence_policy_options = Misc::prependArray( array( 0 => TTi18n::gettext('-- Please Choose --') ), $aplf->getByCompanyIdArray( $current_company->getId() ) );

		$blf = new BranchListFactory();
		$branch_options = $blf->getByCompanyIdArray( $current_company->getId() );

		$dlf = new DepartmentListFactory();
		$department_options = $dlf->getByCompanyIdArray( $current_company->getId() );

		//Select box options;
		//$udt_data['status_options'] = $udtf->getOptions('status');
		//$udt_data['type_options'] = $udtf->getOptions('type');
		$udt_data['absence_policy_options'] = $absence_policy_options;
		$udt_data['branch_options'] = $branch_options;
		$udt_data['department_options'] = $department_options;

		$smarty->assign_by_ref('udt_data', $udt_data);

		break;
}

$smarty->assign_by_ref('udtf', $udtf);

$smarty->display('punch/EditUserAbsence.tpl');
?>