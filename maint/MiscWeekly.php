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
 * $Revision: 1396 $
 * $Id: CheckForUpdate.php 1396 2007-11-07 16:49:35Z ipso $
 * $Date: 2007-11-07 08:49:35 -0800 (Wed, 07 Nov 2007) $
 */
/*
 * Checks for any version updates...
 *
 */
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'includes'. DIRECTORY_SEPARATOR .'global.inc.php');
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'includes'. DIRECTORY_SEPARATOR .'CLI.inc.php');

//
// Purge database tables
//
if ( !isset($config_vars['other']['disable_database_purging'])
		OR isset($config_vars['other']['disable_database_purging']) AND $config_vars['other']['disable_database_purging'] != TRUE ) {

	//Make array of tables to purge, and the timeperiod to purge them at.
	Debug::Text('Purging database tables: '. TTDate::getDate('DATE+TIME', time() ), __FILE__, __LINE__, __METHOD__,10);
	$purge_tables = array(
							'user_generic_status' => 2,
							'user_date' => 30,
							'punch' => 30,
							'punch_control' => 30,
							'absence_policy' => 30,
							'accrual' => 30,
							// 'accrual_balance' => 30, //Doesnt have updated_date column
							'accrual_policy' => 30,
							'accrual_policy_milestone' => 30,
							'authorizations' => 30,
							'bank_account' => 30,
							'branch' => 30,
							'break_policy' => 30,
							'wage_group' => 30,
							'company_deduction' => 120,
							'cron' => 30,
							'currency' => 120,
							'department' => 30,
							'exception' => 30,
							'exception_policy' => 30,
							'exception_policy_control' => 30,
							'hierarchy_control' => 30,
							'holiday_policy' => 30,
							'holidays' => 30,
							'meal_policy' => 30,
							'message' => 30,
							'other_field' => 30,
							'over_time_policy' => 30,
							'pay_period' => 30,
							'pay_period_schedule' => 30,
							'pay_period_time_sheet_verify' => 30,
							'pay_stub' => 420,
							'pay_stub_amendment' => 420,
							'pay_stub_entry' => 420,
							'pay_stub_entry_account' => 420,
							'permission' => 30,
							'permission_control' => 30,
							'policy_group' => 30,
							'premium_policy' => 30,
							'recurring_holiday' => 30,
							'recurring_ps_amendment' => 30,
							'recurring_schedule_control' => 30,
							'recurring_schedule_template' => 30,
							'recurring_schedule_template_control' => 30,
							'request' => 30,
							'roe' => 30,
							'round_interval_policy' => 30,
							'schedule' => 30,
							'schedule_policy' => 30,
							'station' => 30,
							'user_date_total' => 30,
							'user_deduction' => 30,
							'user_default' => 30,
							'user_generic_data' => 30,
							'user_group' => 30,
							'user_identification' => 30,
							'user_preference' => 30,
							'user_title' => 30,
							'user_wage' => 120,
							'users' => 120,
						  );

	if ( getTTProductEdition() == 20 ) {
		$purge_extra_tables = array(
							'client' => 30,
							'client_contact' => 30,
							'client_group' => 30,
							'client_payment' => 30,
							'area_policy' => 30,
							'document' => 30,
							'document_attachment' => 30,
							'document_group' => 30,
							'document_revision' => 30,
							'invoice' => 30,
							'invoice_config' => 30,
							'invoice_district' => 30,
							'invoice_transaction' => 30,
							'job' => 30,
							'job_group' => 30,
							'job_item' => 30,
							'job_item_amendment' => 30,
							'job_item_group' => 30,
							'payment_gateway' => 30,
							'product' => 30,
							'product_group' => 30,
							'product_price' => 30,
							'shipping_policy' => 30,
							'shipping_table_rate' => 30,
							'tax_policy' => 30,
							);

		$purge_tables = array_merge( $purge_tables, $purge_extra_tables );
	}

	$current_tables = $db->MetaTables();

	if ( is_array( $purge_tables ) AND is_array( $current_tables ) ) {
		$db->StartTrans();
		foreach( $purge_tables as $table => $expire_days ) {
			if ( in_array($table, $current_tables) ) {
				//Treat the user_generic_status table differently, as rows are never marked as deleted in it.
				if ( $table == 'user_generic_status' ) {
					$query = 'delete from '. $table .' where updated_date <= '. (time()-(86400*($expire_days)));
				} else {
					$query = 'delete from '. $table .' where deleted = 1 AND updated_date <= '. (time()-(86400*($expire_days)));
				}

				//FIXME: With new punch method in v3.0 add query to make sure orphaned punches without punch_control rows are cleaned out
				//select a.id,a.deleted,b.id,b.deleted from punch as a LEFT JOIN punch_control as b ON (a.punch_control_id = b.id) WHERE b.id is NULL AND a.deleted = 0;

				$db->Execute( $query );
				Debug::Text('Table found for purging: '. $table .' Expire Days: '. $expire_days .' Purged Rows: '. $db->Affected_Rows(), __FILE__, __LINE__, __METHOD__,10);
			} else {
				Debug::Text('Table not found for purging: '. $table, __FILE__, __LINE__, __METHOD__,10);
			}

		}
		$db->CompleteTrans();
	}
	unset($purge_tables, $purge_extra_tables, $current_tables, $query);
	Debug::Text('Purging database tables complete: '. TTDate::getDate('DATE+TIME', time() ), __FILE__, __LINE__, __METHOD__,10);
}

//
// Clean cache directories
// - Make sure cache directory is set, and log/storage directories are not contained within it.
//
if ( !isset($config_vars['other']['disable_cache_purging'])
		OR isset($config_vars['other']['disable_cache_purging']) AND $config_vars['other']['disable_cache_purging'] != TRUE ) {

	if ( isset($config_vars['cache']['dir'])
			AND $config_vars['cache']['dir'] != ''
			AND strpos( $config_vars['path']['log'], $config_vars['cache']['dir'] ) === FALSE
			AND strpos( $config_vars['path']['storage'], $config_vars['cache']['dir'] ) === FALSE ) {

		Debug::Text('Purging Cache directory: '. $config_vars['cache']['dir'] .' - '. TTDate::getDate('DATE+TIME', time() ), __FILE__, __LINE__, __METHOD__,10);
		$cache_files = Misc::getFileList( $config_vars['cache']['dir'], NULL, TRUE );
		if ( is_array($cache_files) ) {
			foreach( $cache_files as $cache_file ) {
				if ( strpos( $cache_file, '.lock') === FALSE ) {
					@unlink($cache_file);
				}
			}
		}
		Debug::Text('Purging Cache directory complete: '. TTDate::getDate('DATE+TIME', time() ), __FILE__, __LINE__, __METHOD__,10);
	} else {
		Debug::Text('Cache directory is invalid: '. TTDate::getDate('DATE+TIME', time() ), __FILE__, __LINE__, __METHOD__,10);
	}
}
Debug::writeToLog();
Debug::Display();
?>