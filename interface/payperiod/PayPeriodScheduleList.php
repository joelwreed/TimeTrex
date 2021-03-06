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
 * $Id: PayPeriodScheduleList.php 1246 2007-09-14 23:47:42Z ipso $
 * $Date: 2007-09-14 16:47:42 -0700 (Fri, 14 Sep 2007) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

if ( !$permission->Check('pay_period_schedule','enabled')
		OR !( $permission->Check('pay_period_schedule','view') OR $permission->Check('pay_period_schedule','view_own') ) ) {

	$permission->Redirect( FALSE ); //Redirect

}

$smarty->assign('title', TTi18n::gettext($title = 'Pay Period Schedule List')); // See index.php
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
												) ) );

URLBuilder::setURL($_SERVER['SCRIPT_NAME'],
											array(
													'sort_column' => $sort_column,
													'sort_order' => $sort_order,
													'page' => $page
												) );



//$ppslf = new PayPeriodScheduleFactory();

Debug::Arr($ids,'Selected Objects', __FILE__, __LINE__, __METHOD__,10);

$action = Misc::findSubmitButton();
switch ($action) {
	case 'add':

		Redirect::Page( URLBuilder::getURL(NULL, 'EditPayPeriodSchedule.php', FALSE) );

		break;
	case 'delete' OR 'undelete':
		if ( strtolower($action) == 'delete' ) {
			$delete = TRUE;
		} else {
			$delete = FALSE;
		}

		$ppslf = new PayPeriodScheduleListFactory();

		foreach ($ids as $id) {
			$ppslf->GetByIdAndCompanyId($id, $current_company->getId() );
			foreach ($ppslf as $pay_period_schedule) {
				$pay_period_schedule->setDeleted($delete);
				$pay_period_schedule->Save();
			}
		}

		Redirect::Page( URLBuilder::getURL(NULL, 'PayPeriodScheduleList.php') );

		break;

	default:
		$ppslf = new PayPeriodScheduleListFactory();

		$ppslf->getByCompanyId($current_company->getId(), $current_user_prefs->getItemsPerPage(), $page, NULL, array($sort_column => $sort_order) );

		$pager = new Pager($ppslf);

		foreach ($ppslf as $pay_period_schedule) {

			$pay_period_schedules[] = array(
											'id' => $pay_period_schedule->getId(),
											'company_id' => $pay_period_schedule->getCompany(),
											'name' => $pay_period_schedule->getName(),
											'description' => $pay_period_schedule->getDescription(),
											'type' => Option::getByKey($pay_period_schedule->getType(), $pay_period_schedule->getOptions('type') ),
											/*
											'anchor_date' => TTDate::getDate( 'DATE', $pay_period_schedule->getAnchorDate() ),
											'primary_date' => TTDate::getDate( 'DATE', $pay_period_schedule->getPrimaryDate() ),
											'primary_transaction_date' => TTDate::getDate( 'DATE', $pay_period_schedule->getPrimaryTransactionDate() ),
											'secondary_date' => TTDate::getDate( 'DATE', $pay_period_schedule->getSecondaryDate() ),
											'secondary_transaction_date' => TTDate::getDate( 'DATE', $pay_period_schedule->getSecondaryTransactionDate() ),
											*/
											'deleted' => $pay_period_schedule->getDeleted()
											);

		}
		$smarty->assign_by_ref('pay_period_schedules', $pay_period_schedules);

		$smarty->assign_by_ref('sort_column', $sort_column );
		$smarty->assign_by_ref('sort_order', $sort_order );

		$smarty->assign_by_ref('paging_data', $pager->getPageVariables() );

		break;
}
$smarty->display('payperiod/PayPeriodScheduleList.tpl');
?>