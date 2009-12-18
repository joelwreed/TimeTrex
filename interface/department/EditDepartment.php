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
 * $Id: EditDepartment.php 1246 2007-09-14 23:47:42Z ipso $
 * $Date: 2007-09-14 16:47:42 -0700 (Fri, 14 Sep 2007) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

if ( !$permission->Check('department','enabled')
		OR !( $permission->Check('department','view') OR $permission->Check('department','view_own') ) ) {
	$permission->Redirect( FALSE ); //Redirect
}

$smarty->assign('title', TTi18n::gettext($title = 'Edit Deparment')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'id',
												'department_data'
												) ) );

$df = new DepartmentFactory();

$action = Misc::findSubmitButton();
switch ($action) {
	case 'submit':
		Debug::Text('Submit!', __FILE__, __LINE__, __METHOD__,10);

		$df->setId($department_data['id']);
		$df->setCompany( $current_company->getId() );
		$df->setStatus($department_data['status']);
		$df->setName($department_data['name']);
		$df->setManualId($department_data['manual_id']);

		if ( $df->isValid() ) {
			$df->Save(FALSE);

			if ( isset($department_data['branch_list']) ){
				$df->setBranch( $department_data['branch_list'] );
				$df->Save(TRUE);
			}

			Redirect::Page( URLBuilder::getURL(NULL, 'DepartmentList.php') );

			break;
		}
	default:
		if ( isset($id) ) {
			BreadCrumb::setCrumb($title);

			$dlf = new DepartmentListFactory();

			$dlf->GetByIdAndCompanyId($id, $current_company->getId() );

			foreach ($dlf as $department) {
				Debug::Arr($department,'Department', __FILE__, __LINE__, __METHOD__,10);

				$department_data = array(
									'id' => $department->getId(),
									'company_name' => $current_company->getName(),
									'status' => $department->getStatus(),
									'name' => $department->getName(),
									'manual_id' => $department->getManualID(),
									'branch_list' => $department->getBranch(),
									'created_date' => $department->getCreatedDate(),
									'created_by' => $department->getCreatedBy(),
									'updated_date' => $department->getUpdatedDate(),
									'updated_by' => $department->getUpdatedBy(),
									'deleted_date' => $department->getDeletedDate(),
									'deleted_by' => $department->getDeletedBy()
								);
			}
		} elseif ( $action != 'submit' ) {
			$next_available_manual_id = DepartmentListFactory::getNextAvailableManualId( $current_company->getId() );

			$department_data = array(
							'next_available_manual_id' => $next_available_manual_id,
							);
		}

		//Select box options;
		$department_data['status_options'] = $df->getOptions('status');
		$blf = new BranchListFactory();
		$blf->getByCompanyId( $current_company->getId() );
		$department_data['branch_list_options'] = $blf->getArrayByListFactory( $blf, FALSE);

		$smarty->assign_by_ref('department_data', $department_data);

		break;
}

$smarty->assign_by_ref('df', $df);

$smarty->display('department/EditDepartment.tpl');
?>