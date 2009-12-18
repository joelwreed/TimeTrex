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
 * $Revision: 2753 $
 * $Id: AccrualPolicyFactory.class.php 2753 2009-08-27 00:18:14Z ipso $
 * $Date: 2009-08-26 17:18:14 -0700 (Wed, 26 Aug 2009) $
 */

/**
 * @package Module_Policy
 */
class AccrualPolicyFactory extends Factory {
	protected $table = 'accrual_policy';
	protected $pk_sequence_name = 'accrual_policy_id_seq'; //PK Sequence name

	protected $company_obj = NULL;


	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'type':
				$retval = array(
										10 => TTi18n::gettext('Standard'),
										20 => TTi18n::gettext('Calendar Based'),
										30 => TTi18n::gettext('Hour Based'),
									);
				break;
			case 'apply_frequency':
				$retval = array(
										10 => TTi18n::gettext('each Pay Period'),
										20 => TTi18n::gettext('Yearly'),
										30 => TTi18n::gettext('Monthly'),
										40 => TTi18n::gettext('Weekly'),
									);
				break;
			case 'columns':
				$retval = array(
										'-1010-type' => TTi18n::gettext('Type'),
										'-1030-name' => TTi18n::gettext('Name'),

										'-2000-created_by' => TTi18n::gettext('Created By'),
										'-2010-created_date' => TTi18n::gettext('Created Date'),
										'-2020-updated_by' => TTi18n::gettext('Updated By'),
										'-2030-updated_date' => TTi18n::gettext('Updated Date'),
							);
				break;
			case 'default_display_columns': //Columns that are displayed by default.
				$retval = array(
								'type',
								'name',
								);
				break;
			case 'unique_columns': //Columns that are unique, and disabled for mass editing.
				$retval = array(
								'name',
								);
				break;
			case 'linked_columns': //Columns that are linked together, mainly for Mass Edit, if one changes, they all must.
				$retval = array(
								);
				break;
		}

		return $retval;
	}

	function _getVariableToFunctionMap() {
			$variable_function_map = array(
											'id' => 'ID',
											'company_id' => 'Company',
											'type_id' => 'Type',
											'type' => FALSE,
											'name' => 'Name',
											'enable_pay_stub_balance_display' => 'EnablePayStubBalanceDisplay',
											'minimum_time' => 'MinimumTime',
											'maximum_time' => 'MaximumTime',
											'apply_frequency_id' => 'ApplyFrequency',
											'apply_frequency' => 'ApplyFrequency',
											'apply_frequency_month' => 'ApplyFrequencyMonth',
											'apply_frequency_day_of_month' => 'ApplyFrequencyDayOfMonth',
											'apply_frequency_day_of_week' => 'ApplyFrequencyDayOfWeek',
											'milestone_rollover_hire_date' => 'MilestoneRolloverHireDate',
											'milestone_rollover_month' => 'MilestoneRolloverMonth',
											'milestone_rollover_day_of_month' => 'MilestoneRolloverDayOfMonth',
											'minimum_employed_days' => 'MinimumEmployedDays',
											'deleted' => 'Deleted',
											);
			return $variable_function_map;
	}

	function getCompanyObject() {
		if ( is_object($this->company_obj) ) {
			return $this->company_obj;
		} else {
			$clf = new CompanyListFactory();
			$this->company_obj = $clf->getById( $this->getCompany() )->getCurrent();

			return $this->company_obj;
		}
	}

	function getCompany() {
		if ( isset($this->data['company_id']) ) {
			return $this->data['company_id'];
		}

		return FALSE;
	}
	function setCompany($id) {
		$id = trim($id);

		Debug::Text('Company ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$clf = new CompanyListFactory();

		if ( $this->Validator->isResultSetWithRows(	'company',
													$clf->getByID($id),
													TTi18n::gettext('Company is invalid')
													) ) {

			$this->data['company_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getType() {
		if ( isset($this->data['type_id']) ) {
			return $this->data['type_id'];
		}

		return FALSE;
	}
	function setType($value) {
		$value = trim($value);

		$key = Option::getByValue($value, $this->getOptions('type') );
		if ($key !== FALSE) {
			$value = $key;
		}

		if ( $this->Validator->inArrayKey(	'type',
											$value,
											TTi18n::gettext('Incorrect Type'),
											$this->getOptions('type')) ) {

			$this->data['type_id'] = $value;

			return TRUE;
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
		if (	$this->Validator->isLength(	'name',
											$name,
											TTi18n::gettext('Name is invalid'),
											2,50)
						) {

			$this->data['name'] = $name;

			return TRUE;
		}

		return FALSE;
	}

	function getEnablePayStubBalanceDisplay() {
		return $this->fromBool( $this->data['enable_pay_stub_balance_display'] );
	}
	function setEnablePayStubBalanceDisplay($bool) {
		$this->data['enable_pay_stub_balance_display'] = $this->toBool($bool);

		return TRUE;
	}

	function getMinimumTime() {
		if ( isset($this->data['minimum_time']) ) {
			return (int)$this->data['minimum_time'];
		}

		return FALSE;
	}
	function setMinimumTime($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'minimum_time',
													$int,
													TTi18n::gettext('Incorrect Minimum Time')) ) {
			$this->data['minimum_time'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getMaximumTime() {
		if ( isset($this->data['maximum_time']) ) {
			return (int)$this->data['maximum_time'];
		}

		return FALSE;
	}
	function setMaximumTime($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'maximum_time',
													$int,
													TTi18n::gettext('Incorrect Maximum Time')) ) {
			$this->data['maximum_time'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	//
	// Calendar
	//
	function getApplyFrequency() {
		if ( isset($this->data['apply_frequency_id']) ) {
			return $this->data['apply_frequency_id'];
		}

		return FALSE;
	}
	function setApplyFrequency($value) {
		$value = trim($value);

		if ( 	$value == 0
				OR
				$this->Validator->inArrayKey(	'apply_frequency_id',
												$value,
												TTi18n::gettext('Incorrect frequency'),
												$this->getOptions('apply_frequency')) ) {

			$this->data['apply_frequency_id'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getApplyFrequencyMonth() {
		if ( isset($this->data['apply_frequency_month']) ) {
			return $this->data['apply_frequency_month'];
		}

		return FALSE;
	}
	function setApplyFrequencyMonth($value) {
		$value = trim($value);

		if ( $value == 0
				OR
				$this->Validator->inArrayKey(	'apply_frequency_month',
											$value,
											TTi18n::gettext('Incorrect frequency month'),
											TTDate::getMonthOfYearArray() ) ) {

			$this->data['apply_frequency_month'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getApplyFrequencyDayOfMonth() {
		if ( isset($this->data['apply_frequency_day_of_month']) ) {
			return $this->data['apply_frequency_day_of_month'];
		}

		return FALSE;
	}
	function setApplyFrequencyDayOfMonth($value) {
		$value = trim($value);

		if ( $value == 0
				OR
				$this->Validator->inArrayKey(	'apply_frequency_day_of_month',
											$value,
											TTi18n::gettext('Incorrect frequency day of month'),
											TTDate::getDayOfMonthArray() ) ) {

			$this->data['apply_frequency_day_of_month'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getApplyFrequencyDayOfWeek() {
		if ( isset($this->data['apply_frequency_day_of_week']) ) {
			return $this->data['apply_frequency_day_of_week'];
		}

		return FALSE;
	}
	function setApplyFrequencyDayOfWeek($value) {
		$value = trim($value);

		if ( $value == 0
				OR
				$this->Validator->inArrayKey(	'apply_frequency_day_of_week',
											$value,
											TTi18n::gettext('Incorrect frequency day of week'),
											TTDate::getDayOfWeekArray() ) ) {

			$this->data['apply_frequency_day_of_week'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getMilestoneRolloverHireDate() {
		return $this->fromBool( $this->data['milestone_rollover_hire_date'] );
	}
	function setMilestoneRolloverHireDate($bool) {
		$this->data['milestone_rollover_hire_date'] = $this->toBool($bool);

		return TRUE;
	}

	function getMilestoneRolloverMonth() {
		if ( isset($this->data['milestone_rollover_month']) ) {
			return $this->data['milestone_rollover_month'];
		}

		return FALSE;
	}
	function setMilestoneRolloverMonth($value) {
		$value = trim($value);

		if ( $value == 0
				OR
				$this->Validator->inArrayKey(	'milestone_rollover_month',
											$value,
											TTi18n::gettext('Incorrect milestone rollover month'),
											TTDate::getMonthOfYearArray() ) ) {

			$this->data['milestone_rollover_month'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getMilestoneRolloverDayOfMonth() {
		if ( isset($this->data['milestone_rollover_day_of_month']) ) {
			return $this->data['milestone_rollover_day_of_month'];
		}

		return FALSE;
	}
	function setMilestoneRolloverDayOfMonth($value) {
		$value = trim($value);

		if ( $value == 0
				OR
				$this->Validator->inArrayKey(	'milestone_rollover_day_of_month',
												$value,
												TTi18n::gettext('Incorrect milestone rollover day of month'),
												TTDate::getDayOfMonthArray() ) ) {

			$this->data['milestone_rollover_day_of_month'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getMinimumEmployedDays() {
		if ( isset($this->data['minimum_employed_days']) ) {
			return (int)$this->data['minimum_employed_days'];
		}

		return FALSE;
	}
	function setMinimumEmployedDays($int) {
		$int = trim($int);

		if  ( empty($int) ) {
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'minimum_employed_days',
													$int,
													TTi18n::gettext('Incorrect Minimum Employed days')) ) {
			$this->data['minimum_employed_days'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getMilestoneRolloverDate( $user_hire_date = NULL ) {
		if ( $user_hire_date == '' ) {
			return FALSE;
		}

		if ( $this->getMilestoneRolloverHireDate() == TRUE ) {
			$retval = $user_hire_date;
		} else {
			$user_hire_date_arr = getdate( $user_hire_date );
			$retval = mktime( $user_hire_date_arr['hours'], $user_hire_date_arr['minutes'], $user_hire_date_arr['seconds'], $this->getMilestoneRolloverMonth(), $this->getMilestoneRolloverDayOfMonth(), $user_hire_date_arr['year'] );
		}

		Debug::Text('Milestone Rollover Date: '. TTDate::getDate('DATE+TIME', $retval) .' Hire Date: '. TTDate::getDate('DATE+TIME', $user_hire_date), __FILE__, __LINE__, __METHOD__,10);
		return $retval;
	}

	function getAccrualRatePerTimeFrequency( $accrual_rate, $annual_pay_periods = NULL ) {
		$retval = FALSE;
		switch( $this->getApplyFrequency() ) {
			case 10: //Pay Period
				if ( $annual_pay_periods == '' ) {
					return FALSE;
				}
				$retval = bcdiv( $accrual_rate, $annual_pay_periods,0);
				break;
			case 20: //Year
				$retval = $accrual_rate;

				break;
			case 30: //Month
				$retval = bcdiv( $accrual_rate, 12,0);
				break;
			case 40: //Week
				$retval = bcdiv( $accrual_rate, 52,0);
				break;
		}

		//Round to nearest minute, or 15mins?
		$retval = TTDate::roundTime( $retval, 60, 20 );

		Debug::Text('Accrual Rate Per Frequency: '. $retval .' Accrual Rate: '. $accrual_rate .' Pay Periods: '. $annual_pay_periods , __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	function inApplyFrequencyWindow( $current_epoch, $offset, $pay_period_end_date = NULL ) {
		$retval = FALSE;
		switch( $this->getApplyFrequency() ) {
			case 10: //Pay Period
				if ( $pay_period_end_date == '' ) {
					return FALSE;
				}
				if ( $pay_period_end_date >= ($current_epoch-$offset)
						AND $pay_period_end_date <= $current_epoch ) {
					$retval = TRUE;
				}
				break;
			case 20: //Year
				$year_epoch = mktime( 0,0,0, $this->getApplyFrequencyMonth(), $this->getApplyFrequencyDayOfMonth(), TTDate::getYear( $current_epoch ) );
				Debug::Text('Year EPOCH: '. TTDate::getDate('DATE+TIME', $year_epoch), __FILE__, __LINE__, __METHOD__,10);

				if ( $year_epoch >= ($current_epoch-$offset)
						AND $year_epoch <= $current_epoch ) {
					$retval = TRUE;
				}
				break;
			case 30: //Month
				$apply_frequency_day_of_month = $this->getApplyFrequencyDayOfMonth();

				//Make sure if they specify the day of month to be 31, that is still works for months with 30, or 28-29 days, assuming 31 basically means the last day of the month
				if ( $apply_frequency_day_of_month > TTDate::getDaysInMonth( $current_epoch ) ) {
					$apply_frequency_day_of_month = TTDate::getDaysInMonth( $current_epoch );
					Debug::Text('Apply frequency day of month exceeds days in this month, using last day of the month instead: '. $apply_frequency_day_of_month, __FILE__, __LINE__, __METHOD__,10);
				}

				$month_epoch = mktime( 0,0,0, TTDate::getMonth( $current_epoch ), $apply_frequency_day_of_month, TTDate::getYear( $current_epoch ) );
				Debug::Text('Day of Month: '. $this->getApplyFrequencyDayOfMonth() .' Month EPOCH: '. TTDate::getDate('DATE+TIME', $month_epoch) .' Current Month: '. TTDate::getMonth( $current_epoch ), __FILE__, __LINE__, __METHOD__,10);
				Debug::Text('Month EPOCH: '. TTDate::getDate('DATE+TIME', $month_epoch) .' Greater Than: '. TTDate::getDate('DATE+TIME', ($current_epoch-$offset)) .' Less Than: '.  TTDate::getDate('DATE+TIME', $current_epoch), __FILE__, __LINE__, __METHOD__,10);

				if ( $month_epoch >= ($current_epoch-$offset)
						AND $month_epoch <= $current_epoch ) {
					$retval = TRUE;
				}
				break;
			case 40: //Week
				Debug::Text('Current Day Of Week: '. TTDate::getDayOfWeek($current_epoch-$offset), __FILE__, __LINE__, __METHOD__,10);
				if ( $this->getApplyFrequencyDayOfWeek() == TTDate::getDayOfWeek( ($current_epoch-$offset) ) ) {
					$retval = TRUE;
				}
				break;
		}

		Debug::Text('RetVal: '. (int)$retval, __FILE__, __LINE__, __METHOD__,10);
		return $retval;
	}

	function getWorkedTimeByUserIdAndEndDate( $user_id, $end_date = NULL ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		$udtlf = new UserDateTotalListFactory();
		$retval = $udtlf->getWorkedTimeSumByUserIDAndStartDateAndEndDate( $user_id, 1, $end_date );

		Debug::Text('Worked Seconds: '. (int)$retval .' Before: '. TTDate::getDate('DATE+TIME', $end_date), __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	function getActiveMilestoneObject( $u_obj, $epoch = NULL ) {
		if ( !is_object( $u_obj ) ) {
			return FALSE;
		}

		if ( $epoch == '' ) {
			$epoch = TTDate::getTime();
		}

		$milestone_obj = FALSE;

		$apmlf = new AccrualPolicyMilestoneListFactory();
		$apmlf->getByAccrualPolicyId($this->getId(), NULL, array('length_of_service_days' => 'desc' ) );
		Debug::Text('&nbsp;&nbsp;Total Accrual Policy MileStones: '. (int)$apmlf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);
		if ( $apmlf->getRecordCount() > 0 ) {
			$worked_time = NULL;
			$length_of_service_days = NULL;

			foreach( $apmlf as $apm_obj ) {
				if ( $apm_obj->getLengthOfServiceUnit() == 50 ) {
					Debug::Text('&nbsp;&nbsp;MileStone is in Hours...', __FILE__, __LINE__, __METHOD__,10);
					//Hour based
					if ( $worked_time == NULL ) {
						//Get users worked time.
						$worked_time = TTDate::getHours( $this->getWorkedTimeByUserIdAndEndDate( $u_obj->getId(), $epoch ) );
						Debug::Text('&nbsp;&nbsp;Worked Time: '. $worked_time .'hrs', __FILE__, __LINE__, __METHOD__,10);
					}

					if ( $worked_time >= $apm_obj->getLengthOfService() ) {
						Debug::Text('&nbsp;&nbsp;bLength Of Service: '. $apm_obj->getLengthOfService() .'hrs', __FILE__, __LINE__, __METHOD__,10);
						$milestone_obj = $apmlf->getCurrent();
						break;
					} else {
						Debug::Text('&nbsp;&nbsp;Skipping Milestone...', __FILE__, __LINE__, __METHOD__,10);
					}
				} else {
					Debug::Text('&nbsp;&nbsp;MileStone is in Days...', __FILE__, __LINE__, __METHOD__,10);
					//Calendar based
					if ( $length_of_service_days == NULL ) {
						$length_of_service_days = TTDate::getDays( ($epoch-$this->getMilestoneRolloverDate( $u_obj->getHireDate() )) );
						if ( $length_of_service_days < 0 ) {
							$length_of_service_days = 0;
						}
						Debug::Text('&nbsp;&nbsp;Length of Service Days: '. $length_of_service_days, __FILE__, __LINE__, __METHOD__,10);
					}

					if ( $length_of_service_days >= $apm_obj->getLengthOfServiceDays() ) {
						$milestone_obj = $apmlf->getCurrent();
						break;
					} else {
						Debug::Text('&nbsp;&nbsp;Skipping Milestone...', __FILE__, __LINE__, __METHOD__,10);
					}
				}
			}
		}
		unset($apmlf, $apm_obj);

		return $milestone_obj;
	}

	function getCurrentAccrualBalance( $user_id, $accrual_policy_id = NULL ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $accrual_policy_id == '' ) {
			$accrual_policy_id = $this->getId();
		}

		//Check min/max times of accrual policy.
		$ablf = new AccrualBalanceListFactory();
		$ablf->getByUserIdAndAccrualPolicyId( $user_id, $accrual_policy_id );
		if ( $ablf->getRecordCount() > 0 ) {
			$accrual_balance = $ablf->getCurrent()->getBalance();
		} else {
			$accrual_balance = 0;
		}

		Debug::Text('&nbsp;&nbsp; Current Accrual Balance: '. $accrual_balance, __FILE__, __LINE__, __METHOD__,10);

		return $accrual_balance;
	}

	function calcAccrualAmount( $milestone_obj, $total_time, $annual_pay_periods ) {
		if ( !is_object( $milestone_obj ) ) {
			return FALSE;
		}

		$accrual_amount = 0;
		if ( $this->getType() == 30 AND $total_time > 0 ) {
			//Calculate the fixed amount based off the rate.
			$accrual_amount = bcmul( $milestone_obj->getAccrualRate(), $total_time, 4);
		} elseif ( $this->getType() == 20 ) {
			$accrual_amount = $this->getAccrualRatePerTimeFrequency( $milestone_obj->getAccrualRate(), $annual_pay_periods );
		}
		Debug::Text('&nbsp;&nbsp; Accrual Amount: '. $accrual_amount .' Total Time: '. $total_time .' Rate: '. $milestone_obj->getAccrualRate() .' Annual Pay Periods: '. $annual_pay_periods, __FILE__, __LINE__, __METHOD__,10);

		return $accrual_amount;
	}

	function addAccrualPolicyTime( $epoch = NULL, $offset = 79200, $daily_total_time = NULL ) { //22hr offset
		if ( $epoch == '' ) {
			$epoch = TTDate::getTime();
		}

		Debug::Text('Accrual Policy ID: '. $this->getId() .' Current EPOCH: '. TTDate::getDate('DATE+TIME', $epoch), __FILE__, __LINE__, __METHOD__,10);

		$pglf = new PolicyGroupListFactory();

		$pglf->StartTransaction();

		$pglf->getSearchByCompanyIdAndArrayCriteria( $this->getCompany(), array( 'accrual_policy_id' => array( $this->getId() ) ) );
		if ( $pglf->getRecordCount() > 0 ) {
			Debug::Text('Found Policy Group...', __FILE__, __LINE__, __METHOD__,10);
			foreach( $pglf as $pg_obj ) {
				//Get all users assigned to this policy group.
				$policy_group_users = $pg_obj->getUser();
				if ( is_array($policy_group_users) AND count($policy_group_users) > 0 ) {
					Debug::Text('Found Policy Group Users: '. count($policy_group_users), __FILE__, __LINE__, __METHOD__,10);
					foreach( $policy_group_users as $user_id ) {
						Debug::Text('Policy Group User ID: '. $user_id, __FILE__, __LINE__, __METHOD__,10);

						//Get User Object
						$ulf = new UserListFactory();
						$ulf->getByIDAndCompanyID( $user_id, $this->getCompany() );
						if ( $ulf->getRecordCount() == 1 ) {
							$u_obj = $ulf->getCurrent();
							Debug::Text('User: '. $u_obj->getFullName() .' Status: '. $u_obj->getStatus(), __FILE__, __LINE__, __METHOD__,10);
							//Make sure only active employees accrue time. Will this negative affect
							//Employees who may be on leave?
							if ( $u_obj->getStatus() == 10
									AND ( $this->getMinimumEmployedDays() == 0
										OR TTDate::getDays( ($epoch-$u_obj->getHireDate()) ) >= $this->getMinimumEmployedDays() ) ) {
								Debug::Text('&nbsp;&nbsp;User is active and has been employed long enough.', __FILE__, __LINE__, __METHOD__,10);

								$annual_pay_periods = 0;
								$in_apply_frequency_window = FALSE;
								$accrual_balance = 0;
								$accrual_amount = 0;
								if ( $this->getType() == 30 ) {
									Debug::Text('&nbsp;&nbsp;Accrual policy is hour based, real-time window.', __FILE__, __LINE__, __METHOD__,10);

									//Hour based, apply frequency is real-time.
									$in_apply_frequency_window = TRUE;
								} else {
									if ( $this->getApplyFrequency() == 10 ) {
										//Because of pay period frequencies, and users being assigned to different
										//pay period schedules we need to get the last pay period of each user individually.
										//This will return the pay period that just ended in the offset time.
										$pplf = new PayPeriodListFactory();
										$pplf->getByUserIdAndEndDate( $user_id, ($epoch-$offset) );
										if ( $pplf->getRecordCount() > 0 ) {
											foreach( $pplf as $pp_obj ) {
												Debug::Text('&nbsp;&nbsp;Pay Period End Date: '. TTDate::getDate('DATE+TIME', $pp_obj->getEndDate() ), __FILE__, __LINE__, __METHOD__,10);
												if ( $this->inApplyFrequencyWindow( $epoch, $offset, $pp_obj->getEndDate() ) == TRUE ) {
													$in_apply_frequency_window = TRUE;
													$annual_pay_periods = $pp_obj->getPayPeriodScheduleObject()->getAnnualPayPeriods();
													break;
												} else {
													Debug::Text('&nbsp;&nbsp;User not in Apply Frequency Window: ', __FILE__, __LINE__, __METHOD__,10);
												}
											}
										} else {
											Debug::Text('&nbsp;&nbsp; No Pay Period Found.', __FILE__, __LINE__, __METHOD__,10);
										}
									} elseif ( $this->inApplyFrequencyWindow( $epoch, $offset ) == TRUE ) {
										Debug::Text('&nbsp;&nbsp;User IS in NON-PayPeriod Apply Frequency Window.', __FILE__, __LINE__, __METHOD__,10);
										$in_apply_frequency_window = TRUE;
									} else {
										Debug::Text('&nbsp;&nbsp;User is not in Apply Frequency Window.', __FILE__, __LINE__, __METHOD__,10);
										$in_apply_frequency_window = FALSE;
									}
								}

								if ( $in_apply_frequency_window == TRUE ) {
									$milestone_obj = $this->getActiveMilestoneObject( $u_obj, $epoch );

									if ( isset($milestone_obj) AND is_object( $milestone_obj ) ) {
										Debug::Text('&nbsp;&nbsp;Found Matching Milestone, Accrual Rate: (ID: '. $milestone_obj->getId() .') '. $milestone_obj->getAccrualRate() .'/year', __FILE__, __LINE__, __METHOD__,10);
										$accrual_balance = $this->getCurrentAccrualBalance( $user_id, $this->getId() );

										if ( $accrual_balance < $milestone_obj->getMaximumTime() ) {
											$accrual_amount = $this->calcAccrualAmount( $milestone_obj, 0, $annual_pay_periods);

											if ( $accrual_amount > 0 ) {
												$new_accrual_balance = bcadd( $accrual_balance, $accrual_amount);

												//If Maximum time is set to 0, make that unlimited.
												if ( $milestone_obj->getMaximumTime() > 0 AND $new_accrual_balance > $milestone_obj->getMaximumTime() ) {
													$accrual_amount = bcsub( $milestone_obj->getMaximumTime(), $accrual_balance, 0 );
												}
												Debug::Text('&nbsp;&nbsp; Min/Max Adjusted Accrual Amount: '. $accrual_amount .' Limits: Min: '. $milestone_obj->getMinimumTime() .' Max: '. $milestone_obj->getMaximumTime(), __FILE__, __LINE__, __METHOD__,10);

												//Check to make sure there isn't an identical entry already made.
												$alf = new AccrualListFactory();
												$alf->getByCompanyIdAndUserIdAndAccrualPolicyIDAndTimeStampAndAmount( $u_obj->getCompany(), $user_id, $this->getId(), TTDate::getMiddleDayEpoch( $epoch ),  $accrual_amount );
												if ( $alf->getRecordCount() == 0 ) {

													//Round to nearest 1min
													$af = new AccrualFactory();
													$af->setUser( $user_id );
													$af->setType( 75 ); //Accrual Policy
													$af->setAccrualPolicyID( $this->getId() );
													$af->setAmount( $accrual_amount );
													$af->setTimeStamp( TTDate::getMiddleDayEpoch( $epoch ) );
													$af->setEnableCalcBalance( TRUE );

													if ( $af->isValid() ) {
														$af->Save();
													}
												} else {
													Debug::Text('&nbsp;&nbsp; Found duplicate accrual entry, skipping...', __FILE__, __LINE__, __METHOD__,10);
												}
												unset($accrual_amount, $accrual_balance, $new_accrual_balance);
											} else {
												Debug::Text('&nbsp;&nbsp; Accrual Amount is 0...', __FILE__, __LINE__, __METHOD__,10);
											}
										} else {
											Debug::Text('&nbsp;&nbsp; Accrual Balance is outside Milestone Range. Skipping...', __FILE__, __LINE__, __METHOD__,10);
										}
									} else {
										Debug::Text('&nbsp;&nbsp;DID NOT Find Matching Milestone.', __FILE__, __LINE__, __METHOD__,10);
									}
									unset($milestone_obj);
								}
							} else {
								Debug::Text('&nbsp;&nbsp;User is not active (Status: '. $u_obj->getStatus() .') or has only been employed: '. TTDate::getDays( ($epoch-$u_obj->getHireDate()) ) .' Days, not enough.', __FILE__, __LINE__, __METHOD__,10);
							}
						} else {
							Debug::Text('No User Found. Company ID: '. $this->getCompany(), __FILE__, __LINE__, __METHOD__,10);
						}
					}
				}
			}
		}

		$pglf->CommitTransaction();

		return TRUE;
	}

	function Validate() {
		if ( $this->getDeleted() == TRUE ){
			//Check to make sure there are no hours using this OT policy.
			$alf = new AccrualListFactory();
			$alf->getByAccrualPolicyId( $this->getId() );
			if ( $alf->getRecordCount() > 0 ) {
				$this->Validator->isTRUE(	'in_use',
											FALSE,
											TTi18n::gettext('This accrual policy is in use'));

			}
		}

		return TRUE;
	}

	function preSave() {
		return TRUE;
	}

	function postSave() {
		return TRUE;
	}

	function setObjectFromArray( $data ) {
		if ( is_array( $data ) ) {
			$variable_function_map = $this->getVariableToFunctionMap();
			foreach( $variable_function_map as $key => $function ) {
				if ( isset($data[$key]) ) {

					$function = 'set'.$function;
					switch( $key ) {
						default:
							if ( method_exists( $this, $function ) ) {
								$this->$function( $data[$key] );
							}
							break;
					}
				}
			}

			$this->setCreatedAndUpdatedColumns( $data );

			return TRUE;
		}

		return FALSE;
	}

	function getObjectAsArray( $include_columns = NULL ) {
		$variable_function_map = $this->getVariableToFunctionMap();
		if ( is_array( $variable_function_map ) ) {
			foreach( $variable_function_map as $variable => $function_stub ) {
				if ( $include_columns == NULL OR ( isset($include_columns[$variable]) AND $include_columns[$variable] == TRUE ) ) {

					$function = 'get'.$function_stub;
					switch( $variable ) {
						case 'type':
							$function = 'get'.$variable;
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = Option::getByKey( $this->$function(), $this->getOptions( $variable ) );
							}
							break;
						case 'apply_frequency':
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = Option::getByKey( $this->$function(), $this->getOptions( $variable ) );
							}
							break;
						default:
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = $this->$function();
							}
							break;
					}

				}
			}
			$this->getCreatedAndUpdatedColumns( &$data, $include_columns );
		}


		return $data;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Accrual Policy'), NULL, $this->getTable() );
	}

}
?>
