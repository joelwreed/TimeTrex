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
 * $Revision: 2816 $
 * $Id: PermissionFactory.class.php 2816 2009-09-14 23:03:18Z ipso $
 * $Date: 2009-09-14 16:03:18 -0700 (Mon, 14 Sep 2009) $
 */

/**
 * @package Core
 */
class PermissionFactory extends Factory {
	protected $table = 'permission';
	protected $pk_sequence_name = 'permission_id_seq'; //PK Sequence name

	protected $permission_control_obj = NULL;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'preset':
				$retval = array(
										//-1 => TTi18n::gettext('--'),
										10 => TTi18n::gettext('Regular Employee'),
										18 => TTi18n::gettext('Supervisor (Subordinates Only)'),
										20 => TTi18n::gettext('Supervisor (All Employees)'),
										30 => TTi18n::gettext('Payroll Administrator'),
										40 => TTi18n::gettext('Administrator')
									);
				break;
			case 'section_group':
				$retval = array(
											0 => TTi18n::gettext('-- Please Choose --'),
											'all' => TTi18n::gettext('-- All --'),
											'company' => TTi18n::gettext('Company'),
											'user' => TTi18n::gettext('Employee'),
											'schedule' => TTi18n::gettext('Schedule'),
											'attendance' => TTi18n::gettext('Attendance'),
											'job' => TTi18n::gettext('Job Tracking'),
											'invoice' => TTi18n::gettext('Invoicing'),
											'payroll' => TTi18n::gettext('Payroll'),
											'policy' => TTi18n::gettext('Policies'),
											'report' => TTi18n::gettext('Reports'),
											);
				break;
			case 'section_group_map':
				$retval = array(
										'company' => array(
															'system',
															'company',
															'currency',
															'branch',
															'department',
															'station',
															'hierarchy',
															'authorization',
															'message',
															'other_field',
															'document',
															'help',
															'permission'
															),
										'user' 	=> array(
															'user',
															'user_preference',
															'user_tax_deduction',
														),
										'schedule' 	=> array(
															'schedule',
															'recurring_schedule',
															'recurring_schedule_template',
														),
										'attendance' 	=> array(
															'punch',
															'absence',
															'accrual',
															'request',
														),
										'job' 	=> array(
															'job',
															'job_item',
															'job_report',
														),
										'invoice' 	=> array(
															'invoice_config',
															'client',
															'client_payment',
															'product',
															'tax_policy',
															'area_policy',
															'shipping_policy',
															'payment_gateway',
															'transaction',
															'invoice',
															'invoice_report'
														),
										'policy' 	=> array(
															'policy_group',
															'schedule_policy',
															'meal_policy',
															'break_policy',
															'over_time_policy',
															'premium_policy',
															'accrual_policy',
															'absence_policy',
															'round_policy',
															'exception_policy',
															'holiday_policy',
														),
										'payroll' 	=> array(
															'pay_stub_account',
															'pay_stub',
															'pay_stub_amendment',
															'wage',
															'pay_period_schedule',
															'roe',
															'company_tax_deduction',
														),
										'report' 	=> array(
															'report',
														),

										);
				break;

			case 'section':
				$retval = array(
										'system' => TTi18n::gettext('System'),
										'company' => TTi18n::gettext('Company'),
										'currency' => TTi18n::gettext('Currency'),
										'branch' => TTi18n::gettext('Branch'),
										'department' => TTi18n::gettext('Department'),
										'station' => TTi18n::gettext('Station'),
										'hierarchy' => TTi18n::gettext('Hierarchy'),
										'authorization' => TTi18n::gettext('Authorization'),
										'other_field' => TTi18n::gettext('Other Fields'),
										'document' => TTi18n::gettext('Documents'),
										'message' => TTi18n::gettext('Message'),
										'help' => TTi18n::gettext('Help'),
										'permission' => TTi18n::gettext('Permissions'),

										'user' => TTi18n::gettext('Employees'),
										'user_preference' => TTi18n::gettext('Employee Preferences'),
										'user_tax_deduction' => TTi18n::gettext('Employee Tax / Deductions'),

										'schedule' => TTi18n::gettext('Schedule'),
										'recurring_schedule' => TTi18n::gettext('Recurring Schedule'),
										'recurring_schedule_template' => TTi18n::gettext('Recurring Schedule Template'),

										'request' => TTi18n::gettext('Requests'),
										'accrual' => TTi18n::gettext('Accruals'),
										'punch' => TTi18n::gettext('Punch'),
										'absence' => TTi18n::gettext('Absence'),

										'job' => TTi18n::gettext('Jobs'),
										'job_item' => TTi18n::gettext('Job Tasks'),
										'job_report' => TTi18n::gettext('Job Reports'),

										'invoice_config' => TTi18n::gettext('Invoice Settings'),
										'client' => TTi18n::gettext('Invoice Clients'),
										'client_payment' => TTi18n::gettext('Client Payment Methods'),
										'product' => TTi18n::gettext('Products'),
										'tax_policy' => TTi18n::gettext('Tax Policies'),
										'shipping_policy' => TTi18n::gettext('Shipping Policies'),
										'area_policy' => TTi18n::gettext('Area Policies'),
										'payment_gateway' => TTi18n::gettext('Payment Gateway'),
										'transaction' => TTi18n::gettext('Invoice Transactions'),
										'invoice' => TTi18n::gettext('Invoices'),
										'invoice_report' => TTi18n::gettext('Invoice Reports'),

										'policy_group' => TTi18n::gettext('Policy Group'),
										'schedule_policy' => TTi18n::gettext('Schedule Policies'),
										'meal_policy' => TTi18n::gettext('Meal Policies'),
										'break_policy' => TTi18n::gettext('Break Policies'),
										'over_time_policy' => TTi18n::gettext('Overtime Policies'),
										'premium_policy' => TTi18n::gettext('Premium Policies'),
										'accrual_policy' => TTi18n::gettext('Accrual Policies'),
										'absence_policy' => TTi18n::gettext('Absence Policies'),
										'round_policy' => TTi18n::gettext('Rounding Policies'),
										'exception_policy' => TTi18n::gettext('Exception Policies'),
										'holiday_policy' => TTi18n::gettext('Holiday Policies'),

										'pay_stub_account' => TTi18n::gettext('Pay Stub Accounts'),
										'pay_stub' => TTi18n::gettext('Employee Pay Stubs'),
										'pay_stub_amendment' => TTi18n::gettext('Employee Pay Stub Amendments'),
										'wage' => TTi18n::gettext('Wages'),
										'pay_period_schedule' => TTi18n::gettext('Pay Period Schedule'),
										'roe' => TTi18n::gettext('Record of Employment'),
										'company_tax_deduction' => TTi18n::gettext('Company Tax / Deductions'),

										'report' => TTi18n::gettext('Reports'),
									);
				break;
			case 'name':
				$retval = array(
											'system' => array(
																'login' => TTi18n::gettext('Login Enabled'),
															),
											'company' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
																'edit_own_bank' => TTi18n::gettext('Edit Own Banking Information'),
																'login_other_user' => TTi18n::gettext('Login as Other Employee')
															),
											'user' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'edit_advanced' => TTi18n::gettext('Edit Advanced'),
																'edit_own_bank' => TTi18n::gettext('Edit Own Bank Info'),
																'edit_child_bank' => TTi18n::gettext('Edit Subordinate Bank Info'),
																'edit_bank' => TTi18n::gettext('Edit Bank Info'),
																'edit_permission_group' => TTi18n::gettext('Edit Permission Group'),
																'edit_pay_period_schedule' => TTi18n::gettext('Edit Pay Period Schedule'),
																'edit_policy_group' => TTi18n::gettext('Edit Policy Group'),
																'edit_hierarchy' => TTi18n::gettext('Edit Hierarchy'),
																'enroll' => TTi18n::gettext('Enroll Employees'),
																'enroll_child' => TTi18n::gettext('Enroll Subordinate'),
																'timeclock_admin' => TTi18n::gettext('TimeClock Administrator'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																'view_sin' => TTi18n::gettext('View SIN/SSN'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'user_preference' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'user_tax_deduction' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'roe' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'company_tax_deduction' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'pay_stub_account' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'pay_stub' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'pay_stub_amendment' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'wage' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'currency' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'branch' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'department' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
																'assign' => TTi18n::gettext('Assign Employees')

															),
											'station' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
																'assign' => TTi18n::gettext('Assign Employees')
															),
											'pay_period_schedule' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
																'assign' => TTi18n::gettext('Assign Employees')
															),
											'schedule' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
															),
											'other_field' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
															),
											'document' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'view_private' => TTi18n::gettext('View Private'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'edit_private' => TTi18n::gettext('Edit Private'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																'delete_private' => TTi18n::gettext('Delete Private'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
															),
											'accrual' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'policy_group' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'schedule_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'meal_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'break_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'absence_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'accrual_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'over_time_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'premium_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'round_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view' => TTi18n::gettext('View'),
																'view_own' => TTi18n::gettext('View Own'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'exception_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'holiday_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),

											'recurring_schedule_template' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'recurring_schedule' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'request' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
																'authorize' => TTi18n::gettext('Authorize')
															),
											'punch' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
																'verify_time_sheet' => TTi18n::gettext('Verify TimeSheet'),
																'authorize' => TTi18n::gettext('Authorize TimeSheet'),
																'punch_in_out' => TTi18n::gettext('Punch In/Out'),
																'edit_transfer' => TTi18n::gettext('Edit Transfer Field'),
																'default_transfer' => TTi18n::gettext('Default Transfer On'),
																'edit_branch' => TTi18n::gettext('Edit Branch Field'),
																'edit_department' => TTi18n::gettext('Edit Department Field'),
																'edit_job' => TTi18n::gettext('Edit Job Field'),
																'edit_job_item' => TTi18n::gettext('Edit Task Field'),
																'edit_quantity' => TTi18n::gettext('Edit Quantity Field'),
																'edit_bad_quantity' => TTi18n::gettext('Edit Bad Quantity Field'),
																'edit_note' => TTi18n::gettext('Edit Note Field'),
																'edit_other_id1' => TTi18n::gettext('Edit Other ID1 Field'),
																'edit_other_id2' => TTi18n::gettext('Edit Other ID2 Field'),
																'edit_other_id3' => TTi18n::gettext('Edit Other ID3 Field'),
																'edit_other_id4' => TTi18n::gettext('Edit Other ID4 Field'),
																'edit_other_id5' => TTi18n::gettext('Edit Other ID5 Field'),
															),
											'absence' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete'),
															),
											'hierarchy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'authorization' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view' => TTi18n::gettext('View')
															),
											'message' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'add_advanced' => TTi18n::gettext('Add Advanced'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																'send_to_any' => TTi18n::gettext('Send to Any Employee'),
																'send_to_child' => TTi18n::gettext('Send to Subordinate')
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'help' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'report' => 		array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_active_shift' => TTi18n::gettext('View Whos In Summary'),
																'view_user_information' => TTi18n::gettext('View Employee Information'),
																'view_user_detail' => TTi18n::gettext('View Employee Detail'),
																'view_pay_stub_summary' => TTi18n::gettext('Pay Stub Summary'),
																'view_payroll_export' => TTi18n::gettext('Payroll Export'),
																'view_wages_payable_summary' => TTi18n::gettext('Wages Payable Summary'),
																'view_system_log' => TTi18n::gettext('Audit Trail'),
																//'view_employee_pay_stub_summary' => TTi18n::gettext('Employee Pay Stub Summary'),
																//'view_shift_amendment_summary' => TTi18n::gettext('Shift Amendment Summary'),
																'view_timesheet_summary' => TTi18n::gettext('Timesheet Summary'),
																'view_accrual_balance_summary' => TTi18n::gettext('Accrual Balance Summary'),
																'view_punch_summary' => TTi18n::gettext('Punch Summary'),
																'view_remittance_summary' => TTi18n::gettext('Remittance Summary'),
																//'view_branch_summary' => TTi18n::gettext('Branch Summary'),
																'view_employee_summary' => TTi18n::gettext('Employee Summary'),
																'view_t4_summary' => TTi18n::gettext('T4 Summary'),
																'view_generic_tax_summary' => TTi18n::gettext('Generic Tax Summary'),
																'view_form941' => TTi18n::gettext('Form 941'),
																'view_form940' => TTi18n::gettext('Form 940'),
																'view_form940ez' => TTi18n::gettext('Form 940-EZ'),
																'view_form1099misc' => TTi18n::gettext('Form 1099-Misc'),
																'view_formW2' => TTi18n::gettext('Form W2 / W3'),
																'view_user_barcode' => TTi18n::gettext('Employee Barcodes'),
																'view_general_ledger_summary' => TTi18n::gettext('General Ledger Summary'),
															),
											'job' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'job_item' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'job_report' => 		array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_job_summary' => TTi18n::gettext('View Job Summary'),
																'view_job_analysis' => TTi18n::gettext('View Job Analysis'),
																'view_job_payroll_analysis' => TTi18n::gettext('View Job Payroll Analysis'),
																'view_job_barcode' => TTi18n::gettext('View Job Barcode')
															),
											'invoice_config' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'add' => TTi18n::gettext('Add'),
																'edit' => TTi18n::gettext('Edit'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'client' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'client_payment' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																'view_credit_card' => TTi18n::gettext('View Credit Card #'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'product' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'tax_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'shipping_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'area_policy' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'payment_gateway' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'transaction' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'invoice' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															),
											'invoice_report' => 		array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_transaction_summary' => TTi18n::gettext('View Transaction Summary'),
															),
											'permission' => 	array(
																'enabled' => TTi18n::gettext('Enabled'),
																'view_own' => TTi18n::gettext('View Own'),
																'view_child' => TTi18n::gettext('View Subordinate'),
																'view' => TTi18n::gettext('View'),
																'add' => TTi18n::gettext('Add'),
																'edit_own' => TTi18n::gettext('Edit Own'),
																'edit_child' => TTi18n::gettext('Edit Subordinate'),
																'edit' => TTi18n::gettext('Edit'),
																'delete_own' => TTi18n::gettext('Delete Own'),
																'delete_child' => TTi18n::gettext('Delete Subordinate'),
																'delete' => TTi18n::gettext('Delete'),
																//'undelete' => TTi18n::gettext('Un-Delete')
															)
									);
				break;

		}

		return $retval;
	}

	function getCompany() {
		$company_id = $this->getPermissionControlObject()->getCompany();

		return $company_id;
	}

	function getPermissionControlObject() {
		if ( is_object($this->permission_control_obj) ) {
			return $this->permission_control_obj;
		} else {

			$pclf = new PermissionControlListFactory();
			$pclf->getById( $this->getPermissionControl() );

			if ( $pclf->getRecordCount() == 1 ) {
				$this->permission_control_obj = $pclf->getCurrent();

				return $this->permission_control_obj;
			}

			return FALSE;
		}
	}

	function getPermissionControl() {
		if ( isset($this->data['permission_control_id']) ) {
			return $this->data['permission_control_id'];
		}

		return FALSE;
	}
	function setPermissionControl($id) {
		$id = trim($id);

		$pclf = new PermissionControlListFactory();

		if ( $id == -1
				OR
				$this->Validator->isResultSetWithRows(	'permission_control',
													$pclf->getByID($id),
													TTi18n::gettext('Permission Group is invalid')
													) ) {

			$this->data['permission_control_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getSection() {
		if ( isset($this->data['section']) ) {
			return $this->data['section'];
		}

		return FALSE;
	}
	function setSection($section) {
		$section = trim($section);
/*
		$key = Option::getByValue($section, $this->getOptions('section') );
		if ($key !== FALSE) {
			$section = $key;
		}
*/
		if ( $this->Validator->inArrayKey(	'section',
											$section,
											TTi18n::gettext('Incorrect section'),
											$this->getOptions('section')) ) {

			$this->data['section'] = $section;

			return FALSE;
		}

		return FALSE;
	}

	function getName() {
		if ( isset($this->data['name']) ) {
			return $this->data['name'];
		}

		return FALSE;
	}
	function setName($name) {
		$name = trim($name);
/*
		$key = Option::getByValue($name, $this->getOptions('name', $this->getSection() ) );
		if ($key !== FALSE) {
			$name = $key;
		}
*/
		if ( $this->Validator->inArrayKey(	'name',
											$name,
											TTi18n::gettext('Incorrect permission name'),
											$this->getOptions('name', $this->getSection() ) ) ) {

			$this->data['name'] = $name;

			return FALSE;
		}

		return FALSE;
	}

	function getValue() {
		if ( isset($this->data['value']) AND $this->data['value'] == 1 ) {
			return TRUE;
		} else {
			return FALSE;
		}
		//return $this->data['value'];
	}
	function setValue($value) {
		$value = trim($value);

		//Debug::Arr($value, 'Value: ', __FILE__, __LINE__, __METHOD__,10);

		if 	(	$this->Validator->isLength(		'value',
												$value,
												TTi18n::gettext('Value is invalid'),
												1,
												255) ) {

			$this->data['value'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getPresetPermissions( $preset, $preset_flags ) {
		$key = Option::getByValue($preset, $this->getOptions('preset') );
		if ($key !== FALSE) {
			$preset = $key;
		}

		if ( getTTProductEdition() != TT_PRODUCT_PROFESSIONAL ) {
			$preset_flags = array();
		}
/*
										10 => 'Regular Employee',
										20 => 'Supervisor',
										30 => 'Payroll Administrator',
										40 => 'Administrator'
*/

		Debug::Text('Preset: '. $preset, __FILE__, __LINE__, __METHOD__,10);
		Debug::Arr($preset_flags, 'Preset Flags... ', __FILE__, __LINE__, __METHOD__,10);

		if ( !isset($preset) OR $preset == '' OR $preset == -1 ) {
			Debug::Text('No Preset set... Skipping!', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		$preset_permissions_40 = array();
		$preset_permissions_30 = array();
		$preset_permissions_20 = array();
		$preset_permissions_18 = array();
		$preset_permissions_10 = array();
		switch( $preset ) {
			case 40:
				//Can do everything
				$preset_permissions_40 = array(
											'user' => 	array(
																'timeclock_admin' => TRUE,
															),
											'policy_group' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'schedule_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'meal_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'break_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'over_time_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'premium_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'accrual_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'absence_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'round_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'exception_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'holiday_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'currency' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'branch' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'department' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
																'assign' => TRUE
															),
											'station' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
																'assign' => TRUE
															),
											'report' => 		array(
																//'view_shift_actual_time' => TRUE,
															),
											'hierarchy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'round_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'other_field' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'currency' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'permission' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															)
											);
				if ( isset($preset_flags['invoice']) AND $preset_flags['invoice'] == 1 ) {
					Debug::Text('Applying Invoice Permissions for Admin Preset', __FILE__, __LINE__, __METHOD__,10);
					$invoice_preset_permissions_40 = array(
											'invoice_config' => 	array(
																'enabled' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
													);
					$preset_permissions_40 = array_merge_recursive( $preset_permissions_40, $invoice_preset_permissions_40);
					unset($invoice_preset_permissions_40);
				} else {
					Debug::Text('NOT Applying Invoice Permissions for Admin Preset', __FILE__, __LINE__, __METHOD__,10);
				}
			case 30:
				//Payroll Admin, can do wages, taxes, etc...
				$preset_permissions_30 = array(
											'company' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
																'edit_own' => TRUE,
																'edit_own_bank' => TRUE
															),
											'user' => 	array(
																'add' => TRUE,
																'edit_bank' => TRUE,
																'view_sin' => TRUE,
															),
											'user_tax_deduction' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'roe' => 		array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'company_tax_deduction' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'pay_stub_account' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE
															),
											'pay_stub' => 	array(
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE
															),
											'pay_stub_amendment' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE
															),
											'wage' => 		array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE
															),
											'pay_period_schedule' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
																'assign' => TRUE
															),
											'report' => 		array(
																'view_pay_stub_summary' => TRUE,
																'view_payroll_export' => TRUE,
																//'view_employee_pay_stub_summary' => TRUE,
																'view_remittance_summary' => TRUE,
																'view_system_log' => TRUE,
																'view_employee_summary' => TRUE,
																'view_wages_payable_summary' => TRUE,
																'view_t4_summary' => TRUE,
																'view_generic_tax_summary' => TRUE,
																'view_form941' => TRUE,
																'view_form940' => TRUE,
																'view_form940ez' => TRUE,
																'view_form1099misc' => TRUE,
																'view_formW2' => TRUE,
																'view_general_ledger_summary' => TRUE
															),
											);
				if ( isset($preset_flags['invoice']) AND $preset_flags['invoice'] == 1 ) {
					Debug::Text('Applying Invoice Permissions for Payroll Admin Preset', __FILE__, __LINE__, __METHOD__,10);
					$invoice_preset_permissions_30 = array(
											'product' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'tax_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'shipping_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'area_policy' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'payment_gateway' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'invoice_report' => 	array(
																'enabled' => TRUE,
																'view_transaction_summary' => TRUE,
															),
													);
					$preset_permissions_30 = array_merge_recursive( $preset_permissions_30, $invoice_preset_permissions_30);
					unset($invoice_preset_permissions_30);
				} else {
					Debug::Text('NOT Applying Invoice Permissions for Payroll Admin Preset', __FILE__, __LINE__, __METHOD__,10);
				}
			case 20:
				//Supervisor (All Employees), can see all schedules and shifts, and can do authorizations
				$preset_permissions_20 = array(
											'user' => 	array(
																'view' => TRUE,
																'edit' => TRUE,
																'enroll' => TRUE,
																'delete' => TRUE
															),
											'user_preference' => 	array(
																'edit' => TRUE,
															),
											'recurring_schedule_template' => 	array(
																'view' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'recurring_schedule' => 	array(
																'view' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'punch' => 	array(
																'view' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'absence' => 	array(
																'view' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'accrual' => 	array(
																'view' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'request' => 	array(
																'view' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'schedule' => 	array(
																'view' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE
															),
											'message' => 	array(
																'send_to_any' => TRUE,
															),
											);

				//
				// Most of this is done on level 18;
				//
				if ( isset($preset_flags['job']) AND $preset_flags['job'] == 1 ) {
					Debug::Text('Applying Job Permissions for Supervisor Preset', __FILE__, __LINE__, __METHOD__,10);
					$job_preset_permissions_20 = array(
											'job' => 	array(
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'job_item' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
													);
					$preset_permissions_20 = array_merge_recursive( $preset_permissions_20, $job_preset_permissions_20);
					unset($job_preset_permissions_20);
				} else {
					Debug::Text('NOT Applying Job Permissions for Supervisor Preset', __FILE__, __LINE__, __METHOD__,10);
				}
			case 18:
				//Supervisor (Suborindates Only), can see all schedules and shifts, and can do authorizations
				$preset_permissions_18 = array(
											'user' => 	array(
																'view_child' => TRUE,
																'edit_child' => TRUE,
																'edit_advanced' => TRUE,
																'enroll_child' => TRUE,
																'delete_child' => TRUE,
																'edit_pay_period_schedule' => TRUE,
																'edit_policy_group' => TRUE,
																'edit_hierarchy' => TRUE,
															),
											'user_preference' => 	array(
																'edit_child' => TRUE,
															),
											'recurring_schedule_template' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
																'add' => TRUE,
																'edit_own' => TRUE,
																'delete_own' => TRUE,
															),
											'recurring_schedule' => 	array(
																'enabled' => TRUE,
																'view_child' => TRUE,
																'add' => TRUE,
																'edit_child' => TRUE,
																'delete_child' => TRUE,
															),
											'punch' => 	array(
																'view_child' => TRUE,
																'edit_child' => TRUE,
																'delete_child' => TRUE,
																'authorize' => TRUE
															),
											'absence' => 	array(
																'add' => TRUE,
																'view_child' => TRUE,
																'edit_child' => TRUE,
																'delete_child' => TRUE,
															),
											'accrual' => 	array(
																'view_child' => TRUE,
																'add' => TRUE,
																'edit_child' => TRUE,
																'delete_child' => TRUE,
															),
											'request' => 	array(
																'view_child' => TRUE,
																'edit_child' => TRUE,
																'delete_child' => TRUE,
																'authorize' => TRUE
															),
											'schedule' => 	array(
																'add' => TRUE,
																'view_child' => TRUE,
																'edit_child' => TRUE,
																'delete_child' => TRUE
															),
											'authorization' => 	array(
																'enabled' => TRUE,
																'view' => TRUE
															),
											'message' => 	array(
																'add_advanced' => TRUE,
																'send_to_child' => TRUE,
															),
											'report' => 		array(
																'enabled' => TRUE,
																'view_active_shift' => TRUE,
																'view_user_information' => TRUE,
																'view_user_detail' => TRUE,
																'view_timesheet_summary' => TRUE,
																'view_punch_summary' => TRUE,
																'view_accrual_balance_summary' => TRUE,
																'view_user_barcode' => TRUE,
															)
											);

				if ( isset($preset_flags['job']) AND $preset_flags['job'] == 1 ) {
					Debug::Text('Applying Job Permissions for Supervisor Preset', __FILE__, __LINE__, __METHOD__,10);
					$job_preset_permissions_18 = array(
											'job' => 	array(
																'view_own' => TRUE,
																'add' => TRUE,
																'edit_own' => TRUE,
																'delete_own' => TRUE,
															),
											'job_item' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit_own' => TRUE,
																'delete_own' => TRUE,
															),
											'job_report' => array(
																'enabled' => TRUE,
																'view_job_summary' => TRUE,
																'view_job_analysis' => TRUE,
																'view_job_payroll_analysis' => TRUE,
																'view_job_barcode' => TRUE
															),
													);
					$preset_permissions_18 = array_merge_recursive( $preset_permissions_18, $job_preset_permissions_18);
					unset($job_preset_permissions_18);
				} else {
					Debug::Text('NOT Applying Job Permissions for Supervisor Preset', __FILE__, __LINE__, __METHOD__,10);
				}

				if ( isset($preset_flags['invoice']) AND $preset_flags['invoice'] == 1 ) {
					Debug::Text('Applying Invoice Permissions for Supervisor Preset', __FILE__, __LINE__, __METHOD__,10);
					$invoice_preset_permissions_18 = array(
											'client' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'client_payment' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'transaction' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
											'invoice' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
																'add' => TRUE,
																'edit' => TRUE,
																'delete' => TRUE,
															),
													);
					$preset_permissions_18 = array_merge_recursive( $preset_permissions_18, $invoice_preset_permissions_18);
					unset($invoice_preset_permissions_18);
				} else {
					Debug::Text('NOT Applying Invoice Permissions for Supervisor Preset', __FILE__, __LINE__, __METHOD__,10);
				}

				if ( isset($preset_flags['document']) AND $preset_flags['document'] == 1 ) {
					Debug::Text('Applying Document Permissions for Supervisor Preset', __FILE__, __LINE__, __METHOD__,10);
					$document_preset_permissions_18 = array(
											'document' => 	array(
																'add' => TRUE,
																'view_private' => TRUE,
																'edit' => TRUE,
																'edit_private' => TRUE,
																'delete' => TRUE,
																'delete_private' => TRUE,
															),
													);
					$preset_permissions_18 = array_merge_recursive( $preset_permissions_18, $document_preset_permissions_18);
					unset($document_preset_permissions_18);
				} else {
					Debug::Text('NOT Applying Document Permissions for Supervisor Preset', __FILE__, __LINE__, __METHOD__,10);
				}
			case 10:
				$preset_permissions_10 = array(
											'system' => array(
																'login' => TRUE,
															),
											'user' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
																'edit_own' => TRUE,
																'edit_own_bank' => TRUE,
															),
											'user_preference' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
																'add' => TRUE,
																'edit_own' => TRUE,
																'delete_own' => TRUE,
															),
											'pay_stub' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
															),
											'accrual' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE
															),
											'request' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
																'add' => TRUE,
																'edit_own' => TRUE,
																'delete_own' => TRUE,
															),
											'schedule' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
															),
											'punch' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
																'add' => TRUE,
																'verify_time_sheet' => TRUE,
																'punch_in_out' => TRUE,
																'edit_transfer' => TRUE,
																'edit_branch' => TRUE,
																'edit_department' => TRUE,
																'edit_note' => TRUE,
																'edit_other_id1' => TRUE,
																'edit_other_id2' => TRUE,
																'edit_other_id3' => TRUE,
																'edit_other_id4' => TRUE,
																'edit_other_id5' => TRUE,
															),
											'absence' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
															),
											'message' => 	array(
																'enabled' => TRUE,
																'view_own' => TRUE,
																'add' => TRUE,
																'edit_own' => TRUE,
																'delete_own' => TRUE,
															),
											'help' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
															)
										);

				if ( isset($preset_flags['job']) AND $preset_flags['job'] == 1 ) {
					Debug::Text('Applying Job Permissions for Regular Preset', __FILE__, __LINE__, __METHOD__,10);
					$job_preset_permissions_10 = array(
											'punch' =>	array(
																'edit_job' => TRUE,
																'edit_job_item' => TRUE,
																'edit_quantity' => TRUE,
																'edit_bad_quantity' => TRUE,
															),
											'job' => 	array(
																'enabled' => TRUE,
															),
													);
					$preset_permissions_10 = array_merge_recursive( $preset_permissions_10, $job_preset_permissions_10);
					unset($job_preset_permissions_10);
				} else {
					Debug::Text('NOT Applying Job Permissions for Regular Preset', __FILE__, __LINE__, __METHOD__,10);
				}

				if ( isset($preset_flags['document']) AND $preset_flags['document'] == 1 ) {
					Debug::Text('Applying Document Permissions for Regular Preset', __FILE__, __LINE__, __METHOD__,10);
					$document_preset_permissions_10 = array(
											'document' => 	array(
																'enabled' => TRUE,
																'view' => TRUE,
															),
													);
					$preset_permissions_10 = array_merge_recursive( $preset_permissions_10, $document_preset_permissions_10);
					unset($document_preset_permissions_10);
				} else {
					Debug::Text('NOT Applying Document Permissions for Regular Preset', __FILE__, __LINE__, __METHOD__,10);
				}
		}

		//Merge all permissions
		$preset_permissions = array_merge_recursive($preset_permissions_10, $preset_permissions_18, $preset_permissions_20, $preset_permissions_30, $preset_permissions_40);
		//var_dump($preset_permissions);

		return $preset_permissions;
	}

	function applyPreset($permission_control_id, $preset, $preset_flags) {
		$preset_permissions = $this->getPresetPermissions( $preset, $preset_flags );

		if ( !is_array($preset_permissions) ) {
			return FALSE;
		}

		$this->setPermissionControl( $permission_control_id );

		$pf = new PermissionFactory();
		$pf->StartTransaction();

		//Delete all previous permissions for this user.
		$this->deletePermissions( $this->getCompany(), $permission_control_id);

		foreach($preset_permissions as $section => $permissions) {
			foreach($permissions as $name => $value) {
				Debug::Text('Setting Permission - Section: '. $section .' Name: '. $name .' Value: '. (int)$value, __FILE__, __LINE__, __METHOD__,10);

				$pf->setPermissionControl( $permission_control_id );
				$pf->setSection( $section );
				$pf->setName( $name );
				$pf->setValue( (int)$value );

				if ( $pf->isValid() ) {
					$pf->save();
				}
			}
		}

		//Clear cache for all users assigned to this permission_control_id
		$pclf = new PermissionControlListFactory();
		$pclf->getById( $permission_control_id );
		if ( $pclf->getRecordCount() > 0 ) {
			$pc_obj = $pclf->getCurrent();

			if ( is_array($pc_obj->getUser() ) ) {
				foreach( $pc_obj->getUser() as $user_id ) {
					$pf->clearCache( $user_id, $this->getCompany() );
				}
			}
		}
		unset($pclf, $pc_obj, $user_id);

		//$pf->FailTransaction();
		$pf->CommitTransaction();

		return TRUE;
	}

	function deletePermissions( $company_id, $permission_control_id ){
		if ( $company_id == '' ) {
			return FALSE;
		}

		if ( $permission_control_id == '' ) {
			return FALSE;
		}

		$plf = new PermissionListFactory();
		$plf->getByCompanyIDAndPermissionControlId( $company_id, $permission_control_id );
		foreach($plf as $permission_obj) {
			$permission_obj->delete(TRUE);
			$this->removeCache( $this->getCacheID() );
		}

		return TRUE;
	}

	static function isIgnore( $section, $name = NULL, $product_edition = 10 ) {
		global $current_company;

		//Ignore by default
		if ( $section == '' ) {
			return TRUE;
		}

		Debug::Text(' Product Edition: '. $product_edition .' Primary Company ID: '. PRIMARY_COMPANY_ID, __FILE__, __LINE__, __METHOD__,10);
		if ( $product_edition == 20 ) {
			$ignore_permissions = array('help' => 'ALL',
										'company' => array('add','delete','delete_own','undelete','view','edit','login_other_user'),
										);
		} else {
			$ignore_permissions = array('help' => 'ALL',
										'company' => array('add','delete','delete_own','undelete','view','edit','login_other_user'),
										'job_item' => 'ALL',
										'invoice_config' => 'ALL',
										'client' => 'ALL',
										'client_payment' => 'ALL',
										'product' => 'ALL',
										'tax_policy' => 'ALL',
										'area_policy' => 'ALL',
										'shipping_policy' => 'ALL',
										'payment_gateway' => 'ALL',
										'transaction' => 'ALL',
										'job_report' => 'ALL',
										'invoice_report' => 'ALL',
										'invoice' => 'ALL',
										'job' => 'ALL',
										'document' => 'ALL',
										);
		}

		//If they are currently logged in as the primary company ID, allow multiple company permissions.
		if ( isset($current_company) AND $current_company->getProductEdition() > 10 AND $current_company->getId() == PRIMARY_COMPANY_ID ) {
			unset($ignore_permissions['company']);
		}

		if ( isset($ignore_permissions[$section])
				AND
					(
						(
							$name != ''
							AND
							($ignore_permissions[$section] == 'ALL'
							OR ( is_array($ignore_permissions[$section]) AND in_array($name, $ignore_permissions[$section]) ) )
						)
						OR
						(
							$name == ''
							AND
							$ignore_permissions[$section] == 'ALL'
						)
					)

					) {
			Debug::Text(' IGNORING... Section: '. $section .' Name: '. $name, __FILE__, __LINE__, __METHOD__,10);
			return TRUE;
		} else {
			Debug::Text(' NOT IGNORING... Section: '. $section .' Name: '. $name, __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}
	}

	function preSave() {
		//Just update any existing permissions. It would probably be faster to delete them all and re-insert though.
		$plf = new PermissionListFactory();
		$obj = $plf->getByCompanyIdAndPermissionControlIdAndSectionAndName( $this->getCompany(), $this->getPermissionControl(), $this->getSection(), $this->getName() )->getCurrent();
		$this->setId( $obj->getId() );

		return TRUE;
	}

	function getCacheID() {
		$cache_id = 'permission::query_'.$this->getSection().$this->getName().$this->getPermissionControl().$this->getCompany();

		return $cache_id;
	}

	function clearCache( $user_id, $company_id ) {
		Debug::Text(' Clearing Cache for User ID: '. $user_id, __FILE__, __LINE__, __METHOD__,10);

		$cache_id = 'permission::all'.$user_id.$company_id;
		return $this->removeCache( $cache_id );
	}

	function postSave() {
		//$cache_id = 'permission::query_'.$this->getSection().$this->getName().$this->getUser().$this->getCompany();
		//$this->removeCache( $this->getCacheID() );

		return TRUE;
	}
}
?>
