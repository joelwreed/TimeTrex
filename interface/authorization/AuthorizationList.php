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
 * $Revision: 3091 $
 * $Id: AuthorizationList.php 3091 2009-11-18 18:00:31Z ipso $
 * $Date: 2009-11-18 10:00:31 -0800 (Wed, 18 Nov 2009) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

if ( !$permission->Check('authorization','enabled')
		OR !( $permission->Check('authorization','view') ) ) {

	$permission->Redirect( FALSE ); //Redirect
}

//Debug::setVerbosity(11);

$smarty->assign('title', TTi18n::gettext($title = 'Authorization List')); // See index.php
BreadCrumb::setCrumb($title);

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'page',
												'sort_column',
												'sort_order',
												'ids',
												'selected_levels'
												) ) );

URLBuilder::setURL($_SERVER['SCRIPT_NAME'],
											array(
													'sort_column' => $sort_column,
													'sort_order' => $sort_order,
													'page' => $page
												) );

switch ($action) {
	case 'submit':
		Debug::Text('Submit!', __FILE__, __LINE__, __METHOD__,10);

	default:
		$sort_array = NULL;
		if ( $sort_column != '' ) {
			$sort_array = array(Misc::trimSortPrefix($sort_column) => $sort_order);
		}

		$ulf = new UserListFactory();
		$hlf = new HierarchyListFactory();
		$hllf = new HierarchyLevelListFactory();
		$hotlf = new HierarchyObjectTypeListFactory();

		if ( $permission->Check('request','authorize') ) {
			//Debug::Text('Request: Selected Level: '. $selected_levels['request'], __FILE__, __LINE__, __METHOD__,10);
			$request_levels = $hllf->getLevelsByUserIdAndObjectTypeID( $current_user->getId(), 50 );
			Debug::Arr( $request_levels, 'Request Levels', __FILE__, __LINE__, __METHOD__,10);

			if ( isset($selected_levels['request']) AND isset($request_levels[$selected_levels['request']]) ) {
				$request_selected_level = $request_levels[$selected_levels['request']];
				Debug::Text(' Switching Levels to Level: '. $request_selected_level, __FILE__, __LINE__, __METHOD__,10);
			} elseif ( isset($request_levels[1]) ) {
				$request_selected_level = $request_levels[1];
			} else {
				Debug::Text( 'No Request Levels... Not in hierarchy?', __FILE__, __LINE__, __METHOD__,10);
				$request_selected_level = 0;
			}
			Debug::Text( 'Request Selected Level: '. $request_selected_level, __FILE__, __LINE__, __METHOD__,10);

			//Get all relevant hierarchy ids
			$request_hierarchy_user_ids = $hlf->getByUserIdAndObjectTypeIDAndLevel( $current_user->getId(), 50, (int)$request_selected_level );

			if ( is_array($request_hierarchy_user_ids)
					AND isset($request_hierarchy_user_ids['child_level'])
					AND isset($request_hierarchy_user_ids['parent_level'])
					AND isset($request_hierarchy_user_ids['current_level']) ) {
				Debug::Text( 'Hierarchy information found...', __FILE__, __LINE__, __METHOD__,10);
				$rlf = new RequestListFactory();
				$rlf->getByUserIdListAndStatusAndLevelAndMaxLevelAndNotAuthorized( $request_hierarchy_user_ids['child_level'], 30, (int)$request_selected_level, (int)end($request_levels), NULL, NULL, NULL, $sort_array );

				$status_options = $rlf->getOptions('status');
				$type_options = $rlf->getOptions('type');

				foreach( $rlf as $r_obj) {
					//Grab authorizations for this object.
					$requests[] = array(
											'id' => $r_obj->getId(),
											'user_date_id' => $r_obj->getId(),
											'user_id' => $r_obj->getUserDateObject()->getUser(),
											'user_full_name' => $r_obj->getUserDateObject()->getUserObject()->getFullName(),
											'date_stamp' => $r_obj->getUserDateObject()->getDateStamp(),
											'type_id' => $r_obj->getType(),
											'type' => $type_options[$r_obj->getType()],
											'status_id' => $r_obj->getStatus(),
											'status' => $status_options[$r_obj->getStatus()]
										);
				}
				$smarty->assign_by_ref('requests', $requests);

				if ( isset($request_levels) AND is_array($request_levels) ) {
					$smarty->assign_by_ref('request_levels', $request_levels );
					$smarty->assign_by_ref('selected_request_level', $request_selected_level);
				}
			} else {
				Debug::Text( 'No hierarchy information found...', __FILE__, __LINE__, __METHOD__,10);
			}
		}

		if ( $permission->Check('punch','authorize') ) {
			//Debug::Text('TimeSheet: Selected Level: '. $selected_levels['timesheet'], __FILE__, __LINE__, __METHOD__,10);

			$timesheet_levels = $hllf->getLevelsByUserIdAndObjectTypeID( $current_user->getId(), 90 );
			Debug::Arr( $timesheet_levels , 'TimeSheet Levels', __FILE__, __LINE__, __METHOD__,10);

			if ( isset($selected_levels['timesheet']) AND isset($timesheet_levels[$selected_levels['timesheet']]) ) {
				$timesheet_selected_level = $timesheet_levels[$selected_levels['timesheet']];
				Debug::Text(' Switching Levels to Level: '. $timesheet_selected_level, __FILE__, __LINE__, __METHOD__,10);
			} elseif ( isset($timesheet_levels[1]) ) {
				$timesheet_selected_level = $request_levels[1];
			} else {
				Debug::Text( 'No TimeSheet Levels... Not in hierarchy?', __FILE__, __LINE__, __METHOD__,10);
				$timesheet_selected_level = 0;
			}
			Debug::Text( 'TimeSheet Selected Level: '. $timesheet_selected_level, __FILE__, __LINE__, __METHOD__,10);

			//Get all relevant hierarchy ids
			$timesheet_hierarchy_user_ids = $hlf->getByUserIdAndObjectTypeIDAndLevel( $current_user->getId(), 90, (int)$timesheet_selected_level );
			//Debug::Arr( $request_hierarchy_user_ids, 'Request Hierarchy Ids', __FILE__, __LINE__, __METHOD__,10);

			if ( is_array($timesheet_hierarchy_user_ids)
					AND isset($timesheet_hierarchy_user_ids['child_level'])
					AND isset($timesheet_hierarchy_user_ids['parent_level'])
					AND isset($timesheet_hierarchy_user_ids['current_level']) ) {

				$pptsvlf = new PayPeriodTimeSheetVerifyListFactory();
				$pptsvlf->getByUserIdListAndStatusAndLevelAndMaxLevelAndNotAuthorized( $timesheet_hierarchy_user_ids['child_level'], 30, (int)$timesheet_selected_level, (int)end($timesheet_levels), NULL, NULL, NULL, $sort_array  );

				$status_options = $pptsvlf->getOptions('status');

				foreach( $pptsvlf as $pptsv_obj) {
					//Grab authorizations for this object.
					$timesheets[] = array(
											'id' => $pptsv_obj->getId(),
											'pay_period_id' => $pptsv_obj->getPayPeriod(),
											'user_id' => $pptsv_obj->getUser(),
											'user_full_name' => $pptsv_obj->getUserObject()->getFullName(),
											'pay_period_start_date' => $pptsv_obj->getPayPeriodObject()->getStartDate(),
											'pay_period_end_date' => $pptsv_obj->getPayPeriodObject()->getEndDate(),
											'status_id' => $pptsv_obj->getStatus(),
											'status' => $status_options[$pptsv_obj->getStatus()]
										);
				}
				$smarty->assign_by_ref('timesheets', $timesheets);

				if ( isset($timesheet_levels) AND is_array($timesheet_levels) ) {
					$smarty->assign_by_ref('timesheet_levels', $timesheet_levels );
					$smarty->assign_by_ref('selected_timesheet_level', $timesheet_selected_level );
				}
			} else {
				Debug::Text( 'No hierarchy information found...', __FILE__, __LINE__, __METHOD__,10);
			}
		}

		$smarty->assign_by_ref('selected_levels', $selected_levels );

		$smarty->assign_by_ref('sort_column', $sort_column );
		$smarty->assign_by_ref('sort_order', $sort_order );

		break;
}
$smarty->display('authorization/AuthorizationList.tpl');
?>