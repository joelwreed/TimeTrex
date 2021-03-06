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
 * $Revision: 2875 $
 * $Id: EditSchedulePolicy.php 2875 2009-10-07 20:27:06Z ipso $
 * $Date: 2009-10-07 13:27:06 -0700 (Wed, 07 Oct 2009) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

if ( !$permission->Check('schedule_policy','enabled')
		OR !( $permission->Check('schedule_policy','edit') OR $permission->Check('schedule_policy','edit_own') ) ) {

	$permission->Redirect( FALSE ); //Redirect

}

$smarty->assign('title', TTi18n::gettext($title = 'Edit Schedule Policy')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'id',
												'data'
												) ) );

if ( isset($data['start_stop_window'] ) ) {
	$data['start_stop_window'] = TTDate::parseTimeUnit($data['start_stop_window']);
}

$spf = new SchedulePolicyFactory();

$action = Misc::findSubmitButton();
$action = strtolower($action);
switch ($action) {
	case 'submit':
		Debug::Text('Submit!', __FILE__, __LINE__, __METHOD__,10);

		$spf->setId( $data['id'] );
		$spf->setCompany( $current_company->getId() );
		$spf->setName( $data['name'] );
		$spf->setMealPolicyID( $data['meal_policy_id'] );
		$spf->setOverTimePolicyID( $data['over_time_policy_id'] );
		$spf->setAbsencePolicyID( $data['absence_policy_id'] );
		$spf->setStartStopWindow( $data['start_stop_window'] );

		if ( $spf->isValid() ) {
			$spf->Save(FALSE);

			if ( isset($data['break_policy_ids']) ) {
				$spf->setBreakPolicy( $data['break_policy_ids'] );
			} else {
				$spf->setBreakPolicy( array() );
			}

			Redirect::Page( URLBuilder::getURL( NULL, 'SchedulePolicyList.php') );

			break;
		}

	default:
		if ( isset($id) ) {
			BreadCrumb::setCrumb($title);

			$splf = new SchedulePolicyListFactory();
			$splf->getByIdAndCompanyID( $id, $current_company->getID() );

			foreach ($splf as $sp_obj) {
				//Debug::Arr($station,'Department', __FILE__, __LINE__, __METHOD__,10);

				$data = array(
									'id' => $sp_obj->getId(),
									'name' => $sp_obj->getName(),
									'over_time_policy_id' => $sp_obj->getOverTimePolicyID(),
									'absence_policy_id' => $sp_obj->getAbsencePolicyID(),
									'meal_policy_id' => $sp_obj->getMealPolicyID(),
									'break_policy_ids' => $sp_obj->getBreakPolicy(),
									'start_stop_window' => $sp_obj->getStartStopWindow(),
									'created_date' => $sp_obj->getCreatedDate(),
									'created_by' => $sp_obj->getCreatedBy(),
									'updated_date' => $sp_obj->getUpdatedDate(),
									'updated_by' => $sp_obj->getUpdatedBy(),
									'deleted_date' => $sp_obj->getDeletedDate(),
									'deleted_by' => $sp_obj->getDeletedBy()
								);
			}
		} elseif ( $action != 'submit' ) {
			$data = array(
							'start_stop_window' => 3600
							);
		}

		$aplf = new AbsencePolicyListFactory();
		$absence_options = $aplf->getByCompanyIDArray( $current_company->getId(), TRUE );

		$otplf = new OverTimePolicyListFactory();
		$over_time_options = $otplf->getByCompanyIDArray( $current_company->getId(), TRUE, array('type_id' => '= 200') );

		$mplf = new MealPolicyListFactory();
		$meal_options = $mplf->getByCompanyIDArray( $current_company->getId(), TRUE );

		$bplf = new BreakPolicyListFactory();
		$break_options = $bplf->getByCompanyIdArray( $current_company->getId(), TRUE );

		//Select box options;
		$data['over_time_options'] = $over_time_options;
		$data['absence_options'] = $absence_options;
		$data['meal_options'] = $meal_options;
		$data['break_options'] = $break_options;

		$smarty->assign_by_ref('data', $data);

		break;
}

$smarty->assign_by_ref('spf', $spf);

$smarty->display('policy/EditSchedulePolicy.tpl');
?>