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
 * $Revision: 2638 $
 * $Id: ViewTimeSheetVerification.php 2638 2009-07-07 21:43:08Z ipso $
 * $Date: 2009-07-07 14:43:08 -0700 (Tue, 07 Jul 2009) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

//Debug::setVerbosity(11);

if ( !$permission->Check('punch','enabled')
		OR !( $permission->Check('punch','edit')
				OR $permission->Check('punch','edit_own')
				OR $permission->Check('punch','edit_child')) ) {
	$permission->Redirect( FALSE ); //Redirect
}

$smarty->assign('title', TTi18n::gettext($title = 'View TimeSheet Verification')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'id',
												'timesheet_id',
												'timesheet_queue_ids',
												'selected_level'
												) ) );

if ( isset($timesheet_queue_ids) ) {
	$timesheet_queue_ids = unserialize( base64_decode( urldecode($timesheet_queue_ids) ) );
	Debug::Arr($timesheet_queue_ids, ' Input TimeSheet Queue IDs '. $action, __FILE__, __LINE__, __METHOD__,10);
}
if ( isset($data) ) {
	$data['date_stamp'] = TTDate::parseDateTime($data['date_stamp']);
}

$pptsvf = new PayPeriodTimeSheetVerifyFactory();

$action = Misc::findSubmitButton();
switch ($action) {
	case 'pass':
		if ( count($timesheet_queue_ids) > 1 ) {
			//Remove the authorized/declined timesheet from the stack.
			array_shift($timesheet_queue_ids);
			Redirect::Page( URLBuilder::getURL( array('id' => $timesheet_queue_ids[0], 'selected_level' => $selected_level, 'timesheet_queue_ids' => base64_encode( serialize($timesheet_queue_ids) ) ), 'ViewTimeSheetVerification.php') );
		} else {
			Redirect::Page( URLBuilder::getURL( array('refresh' => TRUE ), '../CloseWindow.php') );
		}
	case 'decline':
	case 'authorize':
		//Debug::setVerbosity(11);
		Debug::text(' Authorizing TimeSheet: Action: '. $action, __FILE__, __LINE__, __METHOD__,10);
		if ( !empty($timesheet_id) ) {
			Debug::text(' Authorizing TimeSheet ID: '. $timesheet_id, __FILE__, __LINE__, __METHOD__,10);

			$af = new AuthorizationFactory();
			$af->setObjectType('timesheet');
			$af->setObject( $timesheet_id );

			if ( $action == 'authorize' ) {
				Debug::text(' Approving Authorization: ', __FILE__, __LINE__, __METHOD__,10);
				$af->setAuthorized(TRUE);
			} else {
				Debug::text(' Declining Authorization: ', __FILE__, __LINE__, __METHOD__,10);
				$af->setAuthorized(FALSE);
			}

			if ( $af->isValid() ) {
				$af->Save();

				if ( count($timesheet_queue_ids) > 1 ) {
					//Remove the authorized/declined timesheet from the stack.
					array_shift($timesheet_queue_ids);
					Redirect::Page( URLBuilder::getURL( array('id' => $timesheet_queue_ids[0], 'selected_level' => $selected_level, 'timesheet_queue_ids' => base64_encode( serialize($timesheet_queue_ids) ) ), 'ViewTimeSheetVerification.php') );
				} else {
					Redirect::Page( URLBuilder::getURL( array('refresh' => TRUE ), '../CloseWindow.php') );
				}

				break;
			}
		}
	default:
		if ( (int)$id > 0 ) {
			Debug::Text(' ID was passed: '. $id, __FILE__, __LINE__, __METHOD__,10);

			$pptsvlf = new PayPeriodTimeSheetVerifyListFactory();
			$pptsvlf->getByIDAndCompanyID( $id, $current_company->getId() );

			$status_options = $pptsvlf->getOptions('type');
			foreach ($pptsvlf as $pptsv_obj) {
				//Debug::Arr($station,'Department', __FILE__, __LINE__, __METHOD__,10);

				$data = array(
									'id' => $pptsv_obj->getId(),
									'pay_period_id' => $pptsv_obj->getPayPeriod(),
									'user_id' => $pptsv_obj->getUser(),
									'user_full_name' => $pptsv_obj->getUserObject()->getFullName(),
									'pay_period_start_date' => $pptsv_obj->getPayPeriodObject()->getStartDate(),
									'pay_period_end_date' => $pptsv_obj->getPayPeriodObject()->getEndDate(),
									'status_id' => $pptsv_obj->getStatus(),
									'status' => $status_options[$pptsv_obj->getStatus()],
									'created_date' => $pptsv_obj->getCreatedDate(),
									'created_by' => $pptsv_obj->getCreatedBy(),
									'updated_date' => $pptsv_obj->getUpdatedDate(),
									'updated_by' => $pptsv_obj->getUpdatedBy(),
									'deleted_date' => $pptsv_obj->getDeletedDate(),
									'deleted_by' => $pptsv_obj->getDeletedBy()
								);
			}

			//Get Next TimeSheet to authorize:
			if ( $permission->Check('punch','authorize')
					AND $selected_level != NULL
					AND count($timesheet_queue_ids) <= 1 ) {

				Debug::Text('Get TimeSheet Queue: ', __FILE__, __LINE__, __METHOD__,10);

				$ulf = new UserListFactory();
				$hlf = new HierarchyListFactory();
				$hllf = new HierarchyLevelListFactory();
				
				$timesheet_levels = $hllf->getLevelsByUserIdAndObjectTypeID( $current_user->getId(), 90 );
				Debug::Arr( $timesheet_levels , 'TimeSheet Levels', __FILE__, __LINE__, __METHOD__,10);

				if ( isset($selected_levels['timesheet']) AND isset($request_levels[$selected_levels['timesheet']]) ) {
					$timesheet_selected_level = $timesheet_levels[$selected_levels['timesheet']];
					Debug::Text(' Switching Levels to Level: '. $request_selected_level, __FILE__, __LINE__, __METHOD__,10);
				} else {
					$timesheet_selected_level = 1;
				}

				Debug::Text( 'Request Selected Level: '. $timesheet_selected_level, __FILE__, __LINE__, __METHOD__,10);

				//Get all relevant hierarchy ids
				$timesheet_hierarchy_user_ids = $hlf->getByUserIdAndObjectTypeIDAndLevel( $current_user->getId(), 90, (int)$timesheet_selected_level );
				//Debug::Arr( $request_hierarchy_user_ids, 'Request Hierarchy Ids', __FILE__, __LINE__, __METHOD__,10);

				if ( is_array($timesheet_hierarchy_user_ids)
						AND isset($timesheet_hierarchy_user_ids['child_level'])
						AND isset($timesheet_hierarchy_user_ids['parent_level'])
						AND isset($timesheet_hierarchy_user_ids['current_level']) ) {

					$pptsvlf = new PayPeriodTimeSheetVerifyListFactory();
					$pptsvlf->getByUserIdListAndStatusAndLevelAndMaxLevelAndNotAuthorized( $timesheet_hierarchy_user_ids['child_level'], 30, (int)$timesheet_selected_level, (int)end($timesheet_levels)  );

					//Get all IDs that need authorizing.
					//Only do 25 at a time, then grab more.
					$i=0;
					$start=FALSE;
					foreach( $pptsvlf as $pptsv_obj) {
						if ( $id == $pptsv_obj->getId() ) {
							$start = TRUE;
						}

						if ( $start == TRUE ) {
							$timesheet_queue_ids[] = $pptsv_obj->getId();
						}

						if ( $i > 25 ) {
							break;
						}
						$i++;
					}

					if ( isset($timesheet_queue_ids) ) {
						$timesheet_queue_ids = array_unique($timesheet_queue_ids);
					}
				}
			}
		}

		//Select box options;
		$data['status_options'] = $pptsvf->getOptions('status');

		if ( isset($timesheet_queue_ids) ) {
			Debug::Arr($timesheet_queue_ids, ' Output TimeSheet Queue IDs '. $action, __FILE__, __LINE__, __METHOD__,10);
			$smarty->assign_by_ref('timesheet_queue_ids', urlencode( base64_encode( serialize($timesheet_queue_ids) ) ) );
		}

		$smarty->assign_by_ref('selected_level', $selected_level);
		$smarty->assign_by_ref('data', $data);

		break;
}

$smarty->assign_by_ref('pptsvf', $pptsvf);

$smarty->display('timesheet/ViewTimeSheetVerification.tpl');
?>