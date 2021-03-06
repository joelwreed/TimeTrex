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
 * $Revision: 2900 $
 * $Id: EditAbsencePolicy.php 2900 2009-10-15 17:43:01Z ipso $
 * $Date: 2009-10-15 10:43:01 -0700 (Thu, 15 Oct 2009) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

if ( !$permission->Check('absence_policy','enabled')
		OR !( $permission->Check('absence_policy','edit') OR $permission->Check('absence_policy','edit_own') ) ) {

	$permission->Redirect( FALSE ); //Redirect

}

$smarty->assign('title', TTi18n::gettext($title = 'Edit Absence Policy')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'id',
												'data'
												) ) );

$apf = new AbsencePolicyFactory();

$action = Misc::findSubmitButton();
$action = strtolower($action);
switch ($action) {
	case 'submit':
		Debug::Text('Submit!', __FILE__, __LINE__, __METHOD__,10);

		$apf->setId( $data['id'] );
		$apf->setCompany( $current_company->getId() );
		$apf->setName( $data['name'] );
		$apf->setType( $data['type_id'] );
		$apf->setRate( $data['rate'] );
		$apf->setWageGroup( $data['wage_group_id'] );
		$apf->setAccrualRate( $data['accrual_rate'] );
		$apf->setAccrualPolicyID( $data['accrual_policy_id'] );
		$apf->setPayStubEntryAccountID( $data['pay_stub_entry_account_id'] );

		if ( $apf->isValid() ) {
			$apf->Save();

			Redirect::Page( URLBuilder::getURL( NULL, 'AbsencePolicyList.php') );

			break;
		}

	default:
		if ( isset($id) ) {
			BreadCrumb::setCrumb($title);

			$aplf = new AbsencePolicyListFactory();
			$aplf->getByIdAndCompanyID( $id, $current_company->getId() );

			foreach ($aplf as $ap_obj) {
				//Debug::Arr($station,'Department', __FILE__, __LINE__, __METHOD__,10);

				$data = array(
									'id' => $ap_obj->getId(),
									'name' => $ap_obj->getName(),
									'type_id' => $ap_obj->getType(),
									'rate' => Misc::removeTrailingZeros( $ap_obj->getRate() ),
									'wage_group_id' => $ap_obj->getWageGroup(),
									'accrual_rate' => Misc::removeTrailingZeros( $ap_obj->getAccrualRate() ),
									'pay_stub_entry_account_id' => $ap_obj->getPayStubEntryAccountID(),
									'accrual_policy_id' => $ap_obj->getAccrualPolicyID(),
									'created_date' => $ap_obj->getCreatedDate(),
									'created_by' => $ap_obj->getCreatedBy(),
									'updated_date' => $ap_obj->getUpdatedDate(),
									'updated_by' => $ap_obj->getUpdatedBy(),
									'deleted_date' => $ap_obj->getDeletedDate(),
									'deleted_by' => $ap_obj->getDeletedBy()
								);
			}
		} else {
			$data = array(
						  'rate' => '1.00',
						  );

		}

		$aplf = new AccrualPolicyListFactory();
		$accrual_options = $aplf->getByCompanyIDArray( $current_company->getId(), TRUE );

		$psealf = new PayStubEntryAccountListFactory();
		$pay_stub_entry_options = $psealf->getByCompanyIdAndStatusIdAndTypeIdArray( $current_company->getId(), 10, array(10,20,30,50) );

		$wglf = new WageGroupListFactory();
		$data['wage_group_options'] = $wglf->getArrayByListFactory( $wglf->getByCompanyId( $current_company->getId() ), TRUE );

		//Select box options;
		$data['type_options'] = $apf->getOptions('type');
		$data['accrual_options'] = $accrual_options;
		$data['pay_stub_entry_options'] = $pay_stub_entry_options;

		$smarty->assign_by_ref('data', $data);

		break;
}

$smarty->assign_by_ref('apf', $apf);

$smarty->display('policy/EditAbsencePolicy.tpl');
?>