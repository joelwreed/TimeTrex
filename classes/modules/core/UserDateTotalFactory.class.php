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
 * $Id: UserDateTotalFactory.class.php 3091 2009-11-18 18:00:31Z ipso $
 * $Date: 2009-11-18 10:00:31 -0800 (Wed, 18 Nov 2009) $
 */

/**
 * @package Core
 */
class UserDateTotalFactory extends Factory {
	protected $table = 'user_date_total';
	protected $pk_sequence_name = 'user_date_total_id_seq'; //PK Sequence name

	protected $user_date_obj = NULL;
	protected $punch_control_obj = NULL;
	protected $overtime_policy_obj = NULL;
	protected $premium_policy_obj = NULL;
	protected $absence_policy_obj = NULL;
	protected $meal_policy_obj = NULL;
	protected $break_policy_obj = NULL;
	protected $job_obj = NULL;
	protected $job_item_obj = NULL;
	protected $calc_system_total_time = FALSE;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('System'),
										20 => TTi18n::gettext('Worked'),
										30 => TTi18n::gettext('Absence')
									);
				break;
			case 'type':
				$retval = array(
										10 => TTi18n::gettext('Total'),
										20 => TTi18n::gettext('Regular'),
										30 => TTi18n::gettext('Overtime'),
										40 => TTi18n::gettext('Premium'),
										100 => TTi18n::gettext('Lunch'),
										110 => TTi18n::gettext('Break')
									);
				break;
			case 'status_type':
				$retval = array(
										10 => array(10,20,30,40,100,110),
										20 => array(10),
										30 => array(10),
									);
				break;

		}

		return $retval;
	}

	function getUserDateObject() {
		if ( is_object($this->user_date_obj) ) {
			return $this->user_date_obj;
		} else {
			$udlf = new UserDateListFactory();
			$udlf->getById( $this->getUserDateID() );
			if ( $udlf->getRecordCount() > 0 ) {
				$this->user_date_obj = $udlf->getCurrent();
			}

			return $this->user_date_obj;
		}
	}

	function getPunchControlObject() {
		if ( is_object($this->punch_control_obj) ) {
			return $this->punch_control_obj;
		} else {
			$pclf = new PunchControlListFactory();
			$pclf->getById( $this->getPunchControlID() );
			if ( $pclf->getRecordCount() > 0 ) {
				$this->punch_control_obj = $pclf->getCurrent();
			}

			return $this->punch_control_obj;
		}
	}

	function getOverTimePolicyObject() {
		if ( is_object($this->overtime_policy_obj) ) {
			return $this->overtime_policy_obj;
		} else {
			$otplf = new OverTimePolicyListFactory();
			$otplf->getById( $this->getOverTimePolicyID() );
			if ( $otplf->getRecordCount() > 0 ) {
				$this->overtime_policy_obj = $otplf->getCurrent();
			}

			return $this->overtime_policy_obj;
		}
	}

	function getPremiumPolicyObject() {
		if ( is_object($this->premium_policy_obj) ) {
			return $this->premium_policy_obj;
		} else {
			$pplf = new PremiumPolicyListFactory();
			$pplf->getById( $this->getPremiumPolicyID() );
			if ( $pplf->getRecordCount() > 0 ) {
				$this->premium_policy_obj = $pplf->getCurrent();
			}

			return $this->premium_policy_obj;
		}
	}

	function getAbsencePolicyObject() {
		if ( is_object($this->absence_policy_obj) ) {
			return $this->absence_policy_obj;
		} else {
			$aplf = new AbsencePolicyListFactory();
			$aplf->getById( $this->getAbsencePolicyID() );
			if ( $aplf->getRecordCount() > 0 ) {
				$this->absence_policy_obj = $aplf->getCurrent();
			}

			return $this->absence_policy_obj;
		}
	}

	function getMealPolicyObject() {
		if ( is_object($this->meal_policy_obj) ) {
			return $this->meal_policy_obj;
		} else {
			$mplf = new MealPolicyListFactory();
			$mplf->getById( $this->getMealPolicyID() );
			if ( $mplf->getRecordCount() > 0 ) {
				$this->meal_policy_obj = $mplf->getCurrent();
				return $this->meal_policy_obj;
			}

			return FALSE;
		}
	}

	function getBreakPolicyObject() {
		if ( is_object($this->break_policy_obj) ) {
			return $this->break_policy_obj;
		} else {
			$bplf = new BreakPolicyListFactory();
			$bplf->getById( $this->getBreakPolicyID() );
			if ( $bplf->getRecordCount() > 0 ) {
				$this->break_policy_obj = $bplf->getCurrent();
				return $this->break_policy_obj;
			}

			return FALSE;
		}
	}

	function getJobObject() {
		if ( is_object($this->job_obj) ) {
			return $this->job_obj;
		} else {
			$jlf = new JobListFactory();
			$jlf->getById( $this->getJob() );
			if ( $jlf->getRecordCount() > 0 ) {
				$this->job_obj = $jlf->getCurrent();
				return $this->job_obj;
			}

			return FALSE;
		}
	}

	function getJobItemObject() {
		if ( is_object($this->job_item_obj) ) {
			return $this->job_item_obj;
		} else {
			$jilf = new JobItemListFactory();
			$jilf->getById( $this->getJobItem() );
			if ( $jilf->getRecordCount() > 0 ) {
				$this->job_item_obj = $jilf->getCurrent();
				return $this->job_item_obj;
			}

			return FALSE;
		}
	}

	function getUserDateID() {
		if ( isset($this->data['user_date_id']) ) {
			return $this->data['user_date_id'];
		}

		return FALSE;
	}
	function setUserDateID($id) {
		$id = trim($id);

		$udlf = new UserDateListFactory();

		if (  $this->Validator->isResultSetWithRows(	'user_date',
														$udlf->getByID($id),
														TTi18n::gettext('Invalid User Date ID')
														) ) {
			$this->data['user_date_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getOverTimePolicyID() {
		if ( isset($this->data['over_time_policy_id']) ) {
			return $this->data['over_time_policy_id'];
		}

		return FALSE;
	}
	function setOverTimePolicyID($id) {
		$id = trim($id);

		$otplf = new OverTimePolicyListFactory();

		if ( $id == FALSE OR $id == 0 OR $id == '' ) {
			$id = 0;
		}

		if (  	$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'over_time_policy',
														$otplf->getByID($id),
														TTi18n::gettext('Invalid Overtime Policy')
														) ) {
			$this->data['over_time_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPremiumPolicyID() {
		if ( isset($this->data['premium_policy_id']) ) {
			return $this->data['premium_policy_id'];
		}

		return FALSE;
	}
	function setPremiumPolicyID($id) {
		$id = trim($id);

		$pplf = new PremiumPolicyListFactory();

		if ( $id == FALSE OR $id == 0 OR $id == '') {
			$id = 0;
		}

		if (	$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'premium_policy',
														$pplf->getByID($id),
														TTi18n::gettext('Invalid Premium Policy ID')
														) ) {
			$this->data['premium_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getAbsencePolicyID() {
		if ( isset($this->data['absence_policy_id']) ) {
			return $this->data['absence_policy_id'];
		}

		return FALSE;
	}
	function setAbsencePolicyID($id) {
		$id = trim($id);

		$aplf = new AbsencePolicyListFactory();

		if ( $id == FALSE OR $id == 0 OR $id == '') {
			$id = 0;
		}

		if (
				$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'absence_policy',
														$aplf->getByID($id),
														TTi18n::gettext('Invalid Absence Policy ID')
														) ) {
			$this->data['absence_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getMealPolicyID() {
		if ( isset($this->data['meal_policy_id']) ) {
			return $this->data['meal_policy_id'];
		}

		return FALSE;
	}
	function setMealPolicyID($id) {
		$id = trim($id);

		$mplf = new MealPolicyListFactory();

		if ( $id == FALSE OR $id == 0 OR $id == '') {
			$id = 0;
		}

		if (	$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'meal_policy',
														$mplf->getByID($id),
														TTi18n::gettext('Invalid Meal Policy ID')
														) ) {
			$this->data['meal_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getBreakPolicyID() {
		if ( isset($this->data['break_policy_id']) ) {
			return $this->data['break_policy_id'];
		}

		return FALSE;
	}
	function setBreakPolicyID($id) {
		$id = trim($id);

		$bplf = new BreakPolicyListFactory();

		if ( $id == FALSE OR $id == 0 OR $id == '') {
			$id = 0;
		}

		if (	$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'break_policy',
														$bplf->getByID($id),
														TTi18n::gettext('Invalid Break Policy ID')
														) ) {
			$this->data['break_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPunchControlID() {
		if ( isset($this->data['punch_control_id']) ) {
			return $this->data['punch_control_id'];
		}

		return FALSE;
	}
	function setPunchControlID($id) {
		$id = trim($id);

		$pclf = new PunchControlListFactory();

		if ( $id == FALSE OR $id == 0 OR $id == '') {
			$id = 0;
		}

		if (  $id == 0
				OR
				$this->Validator->isResultSetWithRows(	'punch_control',
														$pclf->getByID($id),
														TTi18n::gettext('Invalid Punch Control ID')
														) ) {
			$this->data['punch_control_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getStatus() {
		if ( isset($this->data['status_id']) ) {
			return $this->data['status_id'];
		}

		return FALSE;
	}
	function setStatus($status) {
		$status = trim($status);

		$key = Option::getByValue($status, $this->getOptions('status') );
		if ($key !== FALSE) {
			$status = $key;
		}

		if ( $this->Validator->inArrayKey(	'status',
											$status,
											TTi18n::gettext('Incorrect Status'),
											$this->getOptions('status')) ) {

			$this->data['status_id'] = $status;

			return FALSE;
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

			return FALSE;
		}

		return FALSE;
	}

	function getBranch() {
		if ( isset($this->data['branch_id']) ) {
			return $this->data['branch_id'];
		}

		return FALSE;
	}
	function setBranch($id) {
		$id = trim($id);

		if ( $id == FALSE OR $id == 0 OR $id == '' ) {
			$id = 0;
		}

		$blf = new BranchListFactory();

		if (  $id == 0
				OR
				$this->Validator->isResultSetWithRows(	'branch',
														$blf->getByID($id),
														TTi18n::gettext('Branch does not exist')
														) ) {
			$this->data['branch_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getDepartment() {
		if ( isset($this->data['department_id']) ) {
			return $this->data['department_id'];
		}

		return FALSE;
	}
	function setDepartment($id) {
		$id = trim($id);

		if ( $id == FALSE OR $id == 0 OR $id == '' ) {
			$id = 0;
		}

		$dlf = new DepartmentListFactory();

		if (  $id == 0
				OR
				$this->Validator->isResultSetWithRows(	'department',
														$dlf->getByID($id),
														TTi18n::gettext('Department does not exist')
														) ) {
			$this->data['department_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getJob() {
		if ( isset($this->data['job_id']) ) {
			return $this->data['job_id'];
		}

		return FALSE;
	}
	function setJob($id) {
		$id = trim($id);

		if ( $id == FALSE OR $id == 0 OR $id == '' ) {
			$id = 0;
		}

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$jlf = new JobListFactory();
		}

		if (  $id == 0
				OR
				$this->Validator->isResultSetWithRows(	'job',
														$jlf->getByID($id),
														TTi18n::gettext('Job does not exist')
														) ) {
			$this->data['job_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getJobItem() {
		if ( isset($this->data['job_item_id']) ) {
			return $this->data['job_item_id'];
		}

		return FALSE;
	}
	function setJobItem($id) {
		$id = trim($id);

		if ( $id == FALSE OR $id == 0 OR $id == '' ) {
			$id = 0;
		}

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$jilf = new JobItemListFactory();
		}

		if (  $id == 0
				OR
				$this->Validator->isResultSetWithRows(	'job_item',
														$jilf->getByID($id),
														TTi18n::gettext('Job Item does not exist')
														) ) {
			$this->data['job_item_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getQuantity() {
		if ( isset($this->data['quantity']) ) {
			return (float)$this->data['quantity'];
		}

		return FALSE;
	}
	function setQuantity($val) {
		$val = (float)$val;

		if ( $val == FALSE OR $val == 0 OR $val == '' ) {
			$val = 0;
		}

		if 	(	$val == 0
				OR
				$this->Validator->isFloat(			'quantity',
													$val,
													TTi18n::gettext('Incorrect quantity')) ) {
			$this->data['quantity'] = $val;

			return TRUE;
		}

		return FALSE;
	}

	function getBadQuantity() {
		if ( isset($this->data['bad_quantity']) ) {
			return (float)$this->data['bad_quantity'];
		}

		return FALSE;
	}
	function setBadQuantity($val) {
		$val = (float)$val;

		if ( $val == FALSE OR $val == 0 OR $val == '' ) {
			$val = 0;
		}


		if 	(	$val == 0
				OR
				$this->Validator->isFloat(			'bad_quantity',
													$val,
													TTi18n::gettext('Incorrect bad quantity')) ) {
			$this->data['bad_quantity'] = $val;

			return TRUE;
		}

		return FALSE;
	}

	function getStartTimeStamp( $raw = FALSE ) {
		if ( isset($this->data['start_time_stamp']) ) {
			if ( $raw === TRUE ) {
				return $this->data['start_time_stamp'];
			} else {
				//return $this->db->UnixTimeStamp( $this->data['start_date'] );
				//strtotime is MUCH faster than UnixTimeStamp
				//Must use ADODB for times pre-1970 though.
				return TTDate::strtotime( $this->data['start_time_stamp'] );
			}
		}

		return FALSE;
	}
	function setStartTimeStamp($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'start_times_tamp',
												$epoch,
												TTi18n::gettext('Incorrect start time stamp'))

			) {

			$this->data['start_time_stamp'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getEndTimeStamp( $raw = FALSE ) {
		if ( isset($this->data['end_time_stamp']) ) {
			if ( $raw === TRUE ) {
				return $this->data['end_time_stamp'];
			} else {
				return TTDate::strtotime( $this->data['end_time_stamp'] );
			}
		}

		return FALSE;
	}
	function setEndTimeStamp($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'end_times_tamp',
												$epoch,
												TTi18n::gettext('Incorrect end time stamp'))

			) {

			$this->data['end_time_stamp'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getTotalTime() {
		if ( isset($this->data['total_time']) ) {
			return (int)$this->data['total_time'];
		}
		return FALSE;
	}
	function setTotalTime($int) {
		$int = (int)$int;

		if 	(	$this->Validator->isNumeric(		'total_time',
													$int,
													TTi18n::gettext('Incorrect total time')) ) {
			$this->data['total_time'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getActualTotalTime() {
		if ( isset($this->data['actual_total_time']) ) {
			return (int)$this->data['actual_total_time'];
		}
		return FALSE;
	}
	function setActualTotalTime($int) {
		$int = (int)$int;

		if 	(	$this->Validator->isNumeric(		'actual_total_time',
													$int,
													TTi18n::gettext('Incorrect actual total time')) ) {
			$this->data['actual_total_time'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getOverride() {
		return $this->fromBool( $this->data['override'] );
	}
	function setOverride($bool) {
		$this->data['override'] = $this->toBool($bool);

		return true;
	}

	function getName() {
		switch ( $this->getType() ) {
			case 10:
				$name = TTi18n::gettext('Total Time');
				break;
			case 20:
				$name = TTi18n::gettext('Regular Time');
				break;
			case 30:
				if ( is_object($this->getOverTimePolicyObject()) ) {
					$name = $this->getOverTimePolicyObject()->getName();
				}
				break;
			case 40:
				if ( is_object($this->getPremiumPolicyObject()) ) {
					$name = $this->getPremiumPolicyObject()->getName();
				}
				break;
			case 100:
				if ( is_object($this->getMealPolicyObject()) ) {
					$name = $this->getMealPolicyObject()->getName();
				}
				break;
			case 110:
				if ( is_object($this->getBreakPolicyObject()) ) {
					$name = $this->getBreakPolicyObject()->getName();
				}
				break;
			default:
				$name = TTi18n::gettext('N/A');
				break;
		}

		if ( isset($name) ) {
			return $name;
		}

		return FALSE;
	}

	function getEnableCalcSystemTotalTime() {
		if ( isset($this->calc_system_total_time) ) {
			return $this->calc_system_total_time;
		}

		return FALSE;
	}
	function setEnableCalcSystemTotalTime($bool) {
		$this->calc_system_total_time = $bool;

		return TRUE;
	}

	function getEnableCalcWeeklySystemTotalTime() {
		if ( isset($this->calc_weekly_system_total_time) ) {
			return $this->calc_weekly_system_total_time;
		}

		return FALSE;
	}
	function setEnableCalcWeeklySystemTotalTime($bool) {
		$this->calc_weekly_system_total_time = $bool;

		return TRUE;
	}

	function getEnableCalcException() {
		if ( isset($this->calc_exception) ) {
			return $this->calc_exception;
		}

		return FALSE;
	}
	function setEnableCalcException($bool) {
		$this->calc_exception = $bool;

		return TRUE;
	}

	function getEnablePreMatureException() {
		if ( isset($this->premature_exception) ) {
			return $this->premature_exception;
		}

		return FALSE;
	}
	function setEnablePreMatureException($bool) {
		$this->premature_exception = $bool;

		return TRUE;
	}

	function getEnableCalcAccrualPolicy() {
		if ( isset($this->calc_accrual_policy) ) {
			return $this->calc_accrual_policy;
		}

		return FALSE;
	}
	function setEnableCalcAccrualPolicy($bool) {
		$this->calc_accrual_policy = $bool;

		return TRUE;
	}

	function getDailyTotalTime() {
		$udtlf = new UserDateTotalListFactory();

		$daily_total_time = $udtlf->getTotalSumByUserDateID( $this->getUserDateID() );
		Debug::text('Daily Total Time for Day: '. $daily_total_time, __FILE__, __LINE__, __METHOD__, 10);

		return $daily_total_time;
	}

	function deleteSystemTotalTime() {
		//Delete everything that is not overrided.
		$udtlf = new UserDateTotalListFactory();
		$pcf = new PunchControlFactory();

		//Optimize for a direct delete query.
		if ( $this->getUserDateID() > 0 ) {

			//Due to a MySQL gotcha: http://dev.mysql.com/doc/refman/5.0/en/subquery-errors.html
			//We need to wrap the subquery in a subquery of itself to hide it from MySQL
			//So it doesn't complain about updating a table and selecting from it at the same time.
			//MySQL v5.0.22 DOES NOT like this query, it takes 10+ seconds to run and seems to cause a deadlock.
			//Switch back to a select then a bulkDelete instead. Still fast enough I think.
			$udtlf->getByUserDateIdAndStatusAndOverrideAndMisMatchPunchControlUserDateId( $this->getUserDateID(), array(10,30), FALSE ); //System totals
			$this->bulkDelete( $this->getIDSByListFactory( $udtlf ) );
		} else {
			Debug::text('NO System Total Records to delete...', __FILE__, __LINE__, __METHOD__, 10);
		}

		return TRUE;
	}

	function processTriggerTimeArray( $trigger_time_arr, $weekly_total_time = 0 ) {
		if ( is_array($trigger_time_arr) == FALSE OR count($trigger_time_arr) == 0 ) {
			return FALSE;
		}

		//Debug::Arr($trigger_time_arr, 'Source Trigger Arr: ', __FILE__, __LINE__, __METHOD__, 10);

		//Create a duplicate trigger_time_arr that we can sort so we know the
		//first trigger time is always the first in the array.
		//We don't want to use this array in the loop though, because it throws off
		//other ordering.
		$tmp_trigger_time_arr = Sort::multiSort( $trigger_time_arr, 'trigger_time' );
		$first_trigger_time = $tmp_trigger_time_arr[0]['trigger_time']; 		//Get first trigger time.
		//Debug::Arr($tmp_trigger_time_arr, 'Trigger Time After Sort: ', __FILE__, __LINE__, __METHOD__, 10);
		Debug::text('Weekly Total Time: '. $weekly_total_time .' First Trigger Time: '. $first_trigger_time, __FILE__, __LINE__, __METHOD__, 10);
		unset($tmp_trigger_time_arr);

		//Sort trigger_time array by calculation order before looping over it.
		$trigger_time_arr = Sort::multiSort( $trigger_time_arr, 'calculation_order', 'trigger_time', 'asc', 'desc' );
		//Debug::Arr($tmp_trigger_time_arr, 'Source Trigger Arr After Calculation Order Sort: ', __FILE__, __LINE__, __METHOD__, 10);

		//We need to calculate regular time as early as possible so we can adjust the trigger time
		//of weekly overtime policies and re-sort the array.
		$tmp_trigger_time_arr = array();
		foreach( $trigger_time_arr as $key => $trigger_time_data ) {
			if ( $trigger_time_data['over_time_policy_type_id'] == 20 ) {
				if ( is_numeric($weekly_total_time)
						AND $weekly_total_time > 0
						AND $weekly_total_time >= $trigger_time_data['trigger_time'] ) {
					//Worked more then weekly trigger time already.
					Debug::Text('Worked more then weekly trigger time...', __FILE__, __LINE__, __METHOD__, 10);

					$tmp_trigger_time = 0;
				} else {
					//Haven't worked more then the weekly trigger time yet.
					$tmp_trigger_time = $trigger_time_data['trigger_time'] - $weekly_total_time;
					Debug::Text('NOT Worked more then weekly trigger time... TMP Trigger Time: '. $tmp_trigger_time, __FILE__, __LINE__, __METHOD__, 10);

					if ( is_numeric($weekly_total_time)
						AND $weekly_total_time > 0
						AND $tmp_trigger_time > $first_trigger_time ) {
						Debug::Text('Using First Trigger Time: '. $first_trigger_time, __FILE__, __LINE__, __METHOD__, 10);
						$tmp_trigger_time = $first_trigger_time;
					}
				}

				$trigger_time_arr[$key]['trigger_time'] = $tmp_trigger_time;
			} else {
				Debug::Text('NOT weekly overtime policy...', __FILE__, __LINE__, __METHOD__, 10);

				$tmp_trigger_time = $trigger_time_data['trigger_time'];
			}

			Debug::Text('Trigger Time: '. $tmp_trigger_time .' Overtime Policy Id: '. $trigger_time_data['over_time_policy_id'], __FILE__, __LINE__, __METHOD__, 10);
			if ( !in_array( $tmp_trigger_time, $tmp_trigger_time_arr) ) {
				Debug::Text('Adding policy to final array...', __FILE__, __LINE__, __METHOD__, 10);
				$trigger_time_data['trigger_time'] = $tmp_trigger_time;
				$retval[] = $trigger_time_data;
			} else {
				Debug::Text('NOT Adding policy to final array...', __FILE__, __LINE__, __METHOD__, 10);
			}

			$tmp_trigger_time_arr[] = $trigger_time_arr[$key]['trigger_time'];
		}

		$retval = Sort::multiSort( $retval, 'trigger_time' );
		//Debug::Arr($retval, 'Dest Trigger Arr: ', __FILE__, __LINE__, __METHOD__, 10);

		return $retval;
	}

	function calcOverTimePolicyTotalTime( $udt_meal_policy_adjustment_arr, $udt_break_policy_adjustment_arr ) {
		global $profiler;

		$profiler->startTimer( "UserDateTotal::calcOverTimePolicyTotalTime() - Part 1");

		//If this user is scheduled, get schedule overtime policy id.
		$schedule_total_time = 0;
		$schedule_over_time_policy_id = 0;
		$slf = new ScheduleListFactory();
		$slf->getByUserDateIdAndStatusId( $this->getUserDateID(), 10 );
		if ( $slf->getRecordCount() > 0 ) {
			//Check for schedule policy
			foreach ( $slf as $s_obj ) {
				Debug::text(' Schedule Total Time: '. $s_obj->getTotalTime(), __FILE__, __LINE__, __METHOD__,10);
				$schedule_total_time += $s_obj->getTotalTime();

				if ( is_object($s_obj->getSchedulePolicyObject()) AND $s_obj->getSchedulePolicyObject()->getOverTimePolicyID() != FALSE ) {
					$schedule_over_time_policy_id = $s_obj->getSchedulePolicyObject()->getOverTimePolicyID();
					Debug::text('Found New Schedule Overtime Policies to apply: '. $schedule_over_time_policy_id, __FILE__, __LINE__, __METHOD__, 10);
				}
			}
		} else {
			//If they are not scheduled, we use the PolicyGroup list to get a Over Schedule / No Schedule overtime policy.
			//We could check for an active recurring schedule, but there could be multiple, and which
			//one do we use?
		}

		//Apply policies for OverTime hours
		$otplf = new OverTimePolicyListFactory();
		$otp_calculation_order = $otplf->getOptions('calculation_order');
		$otplf->getByPolicyGroupUserIdOrId( $this->getUserDateObject()->getUser(), $schedule_over_time_policy_id );
		if ( $otplf->getRecordCount() > 0 ) {
			Debug::text('Found Overtime Policies to apply.', __FILE__, __LINE__, __METHOD__, 10);

			//Get Pay Period Schedule info
			if ( is_object($this->getUserDateObject()->getPayPeriodObject())
					AND is_object($this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject()) ) {
				$start_week_day_id = $this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject()->getStartWeekDay();
			} else {
				$start_week_day_id = 0;
			}
			Debug::text('Start Week Day ID: '. $start_week_day_id, __FILE__, __LINE__, __METHOD__, 10);

			//Convert all OT policies to daily before applying.
			//For instance, 40+hrs/week policy if they are currently at 35hrs is a 5hr daily policy.
			//For weekly OT policies, they MUST include regular time + other WEEKLY over time rules.
			$udtlf = new UserDateTotalListFactory();
			$weekly_total = $udtlf->getWeekRegularTimeSumByUserIDAndEpochAndStartWeekEpoch( $this->getUserDateObject()->getUser(), $this->getUserDateObject()->getDateStamp(), TTDate::getBeginWeekEpoch($this->getUserDateObject()->getDateStamp(), $start_week_day_id) );
			Debug::text('Weekly Total: '. $weekly_total, __FILE__, __LINE__, __METHOD__, 10);

			//Daily policy always takes precedence, then Weekly, Bi-Weekly, Day Of Week etc...
			//So unless the next policy in the list has a lower trigger time then the previous policy
			//We ignore it.
			//ie: if Daily OT is after 8hrs, and Day Of Week is after 10. Day of week will be ignored.
			//	If Daily OT is after 8hrs, and Weekly is after 40, and they worked 35 up to yesterday,
			//	and 12 hrs today, from 5hrs to 8hrs will be weekly, then anything after that is daily.
			$tmp_trigger_time_arr = array();
			foreach( $otplf as $otp_obj ) {
				Debug::text('&nbsp;&nbsp;Checking Against Policy: '. $otp_obj->getName() .' Trigger Time: '. $otp_obj->getTriggerTime() , __FILE__, __LINE__, __METHOD__, 10);
				$trigger_time = NULL;

				switch( $otp_obj->getType() ) {
					case 10: //Daily
						$trigger_time = $otp_obj->getTriggerTime();
						Debug::text(' Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						break;
					case 20: //Weekly
						//Trigger time minus currently weekly time
						//$trigger_time = $otp_obj->getTriggerTime() - $weekly_total;
						$trigger_time = $otp_obj->getTriggerTime();
						Debug::text(' Weekly Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						break;
					case 40: //Sunday
						if ( date('w', $this->getUserDateObject()->getDateStamp() ) == 0 ) {
							$trigger_time = $otp_obj->getTriggerTime();
							Debug::text(' DayOfWeek OT for Sat ... Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text(' NOT DayOfWeek OT for Sat...', __FILE__, __LINE__, __METHOD__, 10);
							continue;
						}
						break;
					case 50: //Monday
						if ( date('w', $this->getUserDateObject()->getDateStamp() ) == 1 ) {
							$trigger_time = $otp_obj->getTriggerTime();
							Debug::text(' DayOfWeek OT for Sat ... Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text(' NOT DayOfWeek OT for Sat...', __FILE__, __LINE__, __METHOD__, 10);
							continue;
						}
						break;
					case 60: //Tuesday
						if ( date('w', $this->getUserDateObject()->getDateStamp() ) == 2 ) {
							$trigger_time = $otp_obj->getTriggerTime();
							Debug::text(' DayOfWeek OT for Sat ... Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text(' NOT DayOfWeek OT for Sat...', __FILE__, __LINE__, __METHOD__, 10);
							continue;
						}
						break;
					case 70: //Wed
						if ( date('w', $this->getUserDateObject()->getDateStamp() ) == 3 ) {
							$trigger_time = $otp_obj->getTriggerTime();
							Debug::text(' DayOfWeek OT for Sat ... Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text(' NOT DayOfWeek OT for Sat...', __FILE__, __LINE__, __METHOD__, 10);
							continue;
						}
						break;
					case 80: //Thu
						if ( date('w', $this->getUserDateObject()->getDateStamp() ) == 4 ) {
							$trigger_time = $otp_obj->getTriggerTime();
							Debug::text(' DayOfWeek OT for Sat ... Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text(' NOT DayOfWeek OT for Sat...', __FILE__, __LINE__, __METHOD__, 10);
							continue;
						}
						break;
					case 90: //Fri
						if ( date('w', $this->getUserDateObject()->getDateStamp() ) == 5 ) {
							$trigger_time = $otp_obj->getTriggerTime();
							Debug::text(' DayOfWeek OT for Sat ... Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text(' NOT DayOfWeek OT for Sat...', __FILE__, __LINE__, __METHOD__, 10);
							continue;
						}
						break;
					case 100: //Sat
						if ( date('w', $this->getUserDateObject()->getDateStamp() ) == 6 ) {
							$trigger_time = $otp_obj->getTriggerTime();
							Debug::text(' DayOfWeek OT for Sat ... Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text(' NOT DayOfWeek OT for Sat...', __FILE__, __LINE__, __METHOD__, 10);
							continue;
						}
						break;
					case 150: //2-day Consecutive
					case 151: //3-day Consecutive
					case 152: //4-day Consecutive
					case 153: //5-day Consecutive
					case 154: //6-day Consecutive
					case 155: //7-day Consecutive
						switch ( $otp_obj->getType() ) {
							case 150:
								$minimum_days_worked = 2;
								break;
							case 151:
								$minimum_days_worked = 3;
								break;
							case 152:
								$minimum_days_worked = 4;
								break;
							case 153:
								$minimum_days_worked = 5;
								break;
							case 154:
								$minimum_days_worked = 6;
								break;
							case 155:
								$minimum_days_worked = 7;
								break;
						}

						//Should these be reset on the week boundary or should any consecutive days worked apply? Or should we offer both options?
						//We should probably break this out to just a general "consecutive days worked" and add a field to specify any number of days
						//and a field to specify if its only per week, or any timeframe.
						//Will probably want to include a flag to consider scheduled days only too.
						$weekly_days_worked = $udtlf->getDaysWorkedByUserIDAndStartDateAndEndDate( $this->getUserDateObject()->getUser(), TTDate::getBeginWeekEpoch($this->getUserDateObject()->getDateStamp(), $start_week_day_id), $this->getUserDateObject()->getDateStamp() );
						Debug::text(' Weekly Days Worked: '. $weekly_days_worked .' Minimum Required: '. $minimum_days_worked, __FILE__, __LINE__, __METHOD__, 10);

						if ( $weekly_days_worked >= $minimum_days_worked ) {
							$trigger_time = $otp_obj->getTriggerTime();
							Debug::text(' After Days Consecutive... Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text(' NOT After Days Consecutive Worked...', __FILE__, __LINE__, __METHOD__, 10);
							continue;
						}
						unset($weekly_days_worked, $minimum_days_worked);
						break;
					case 180: //Holiday
						$hlf = new HolidayListFactory();
						$hlf->getByPolicyGroupUserIdAndDate( $this->getUserDateObject()->getUser(), $this->getUserDateObject()->getDateStamp() );
						if ( $hlf->getRecordCount() > 0 ) {
							$holiday_obj = $hlf->getCurrent();
							Debug::text(' Found Holiday: '. $holiday_obj->getName(), __FILE__, __LINE__, __METHOD__,10);

							if ( $holiday_obj->getHolidayPolicyObject()->getForceOverTimePolicy() == TRUE
									OR $holiday_obj->isEligible( $this->getUserDateObject()->getUser() ) ) {
								$trigger_time = $otp_obj->getTriggerTime();
								Debug::text(' Is Eligible for Holiday: '. $holiday_obj->getName() .' Daily Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);

							} else {
								Debug::text(' Not Eligible for Holiday: '. $holiday_obj->getName(), __FILE__, __LINE__, __METHOD__, 10);
								continue 2; //Skip to next policy
							}
						} else {
							Debug::text(' Not Holiday...', __FILE__, __LINE__, __METHOD__, 10);
							continue 2; //Skip to next policy
						}
						unset($hlf, $holiday_obj);

						break;
					case 200: //Over schedule / No Schedule
						$trigger_time = $schedule_total_time;
						Debug::text(' Over Schedule/No Schedule Trigger Time: '. $trigger_time , __FILE__, __LINE__, __METHOD__, 10);
						break;
				}

				if ( is_numeric($trigger_time) AND $trigger_time < 0 ) {
					$trigger_time = 0;
				}

				if ( is_numeric($trigger_time) ) {
					$trigger_time_arr[] = array('calculation_order' => $otp_calculation_order[$otp_obj->getType()],  'trigger_time' => $trigger_time, 'over_time_policy_id' => $otp_obj->getId(), 'over_time_policy_type_id' => $otp_obj->getType() );
				}

				unset($trigger_time);
			}

			if ( isset($trigger_time_arr) ) {
				//sort($trigger_time_arr);
				$trigger_time_arr = $this->processTriggerTimeArray( $trigger_time_arr, $weekly_total );
			}

			//Debug::Arr($trigger_time_arr, 'Trigger Time Array', __FILE__, __LINE__, __METHOD__, 10);
		} else {
			Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;No OverTime Policies found for this user.', __FILE__, __LINE__, __METHOD__, 10);
		}
		unset($otp_obj, $otplf);

		if ( isset($trigger_time_arr) ) {
			$total_daily_hours = 0;
			//$total_daily_hours = -1800;
			$total_daily_hours_used = 0;
			//get all worked total hours.

			$udtlf = new UserDateTotalListFactory();
			$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), 20 );
			if ( $udtlf->getRecordCount() > 0 ) {
				Debug::text('Found Total Hours to attempt to apply policy: Record Count: '. $udtlf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);

				if ( $trigger_time_arr[0]['trigger_time'] > 0 ) {
					//No trigger time set at 0.
					$enable_regular_hour_calculating = TRUE;
				} else {
					$enable_regular_hour_calculating = FALSE;
				}
				$tmp_policy_total_time = NULL;
				foreach( $udtlf as $udt_obj ) {
					//Ignore incomplete punches
					if ( $udt_obj->getTotalTime() == 0 ) {
						continue;
					}

					$udt_total_time = $udt_obj->getTotalTime();
					if ( isset( $udt_meal_policy_adjustment_arr[$udt_obj->getId()] ) ) {
						$udt_total_time = bcadd( $udt_total_time, $udt_meal_policy_adjustment_arr[$udt_obj->getId()] );
					}
					if ( isset( $udt_break_policy_adjustment_arr[$udt_obj->getId()] ) ) {
						$udt_total_time = bcadd( $udt_total_time, $udt_break_policy_adjustment_arr[$udt_obj->getId()] );
					}
					$total_daily_hours = bcadd( $total_daily_hours, $udt_total_time );

					//Loop through each trigger.
					$i=0;

					Debug::text('Total Hour: ID: '. $udt_obj->getId() .' Status: '. $udt_obj->getStatus() .' Total Time: '. $udt_obj->getTotalTime() .' Total Daily Hours: '. $total_daily_hours .' Used Total Time: '. $total_daily_hours_used .' Branch ID: '. $udt_obj->getBranch() .' Department ID: '. $udt_obj->getDepartment() .' Job ID: '. $udt_obj->getJob() .' Job Item ID: '. $udt_obj->getJobItem() .' Quantity: '. $udt_obj->getQuantity(), __FILE__, __LINE__, __METHOD__, 10);

					foreach( $trigger_time_arr as $trigger_time_data ) {

						if ( isset($trigger_time_arr[$i+1]['trigger_time']) AND $total_daily_hours_used >= $trigger_time_arr[$i+1]['trigger_time'] ) {
							Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; '. $i .': SKIPPING THIS TRIGGER TIME: '. $trigger_time_data['trigger_time'], __FILE__, __LINE__, __METHOD__, 10);
							$i++;
							continue;
						}

						Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; '. $i .': Trigger Time Data: Trigger Time: '. $trigger_time_data['trigger_time'] .' ID: '. $trigger_time_data['over_time_policy_id'], __FILE__, __LINE__, __METHOD__, 10);
						Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; '. $i .': Used Total Time: '. $total_daily_hours_used, __FILE__, __LINE__, __METHOD__, 10);

						//Only consider Regular Time ONCE per user date total row.
						if ( $i == 0
								AND $trigger_time_arr[$i]['trigger_time'] > 0
								AND $total_daily_hours_used < $trigger_time_arr[$i]['trigger_time'] ) {
							Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; '. $i .': Trigger Time: '. $trigger_time_arr[$i]['trigger_time'] .' greater then 0, found Regular Time.', __FILE__, __LINE__, __METHOD__, 10);

							if ( $total_daily_hours > $trigger_time_arr[$i]['trigger_time'] ) {
								$regular_total_time = $trigger_time_arr[$i]['trigger_time'] - $total_daily_hours_used;

								$regular_quantity_percent = bcdiv($trigger_time_arr[$i]['trigger_time'], $udt_obj->getTotalTime() );
								$regular_quantity = round( bcmul($udt_obj->getQuantity(), $regular_quantity_percent) , 2);
								$regular_bad_quantity = round( bcmul( $udt_obj->getBadQuantity(), $regular_quantity_percent), 2);
							} else {
								//$regular_total_time = $udt_obj->getTotalTime();
								$regular_total_time = $udt_total_time;
								$regular_quantity = $udt_obj->getQuantity();
								$regular_bad_quantity = $udt_obj->getBadQuantity();
							}
							Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; '. $i .': Regular Total Time: '. $regular_total_time .' Regular Quantity: '. $regular_quantity, __FILE__, __LINE__, __METHOD__, 10);

							if ( isset($user_data_total_compact_arr[20][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()] ) ) {
								Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; Adding to Compact Array: Branch: '. (int)$udt_obj->getBranch() .' Department: '. (int)$udt_obj->getDepartment(), __FILE__, __LINE__, __METHOD__, 10);
								$user_data_total_compact_arr[20][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['total_time'] += $regular_total_time;
								$user_data_total_compact_arr[20][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['quantity'] += $regular_quantity;
								$user_data_total_compact_arr[20][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['bad_quantity'] += $regular_bad_quantity;
							} else {
								Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; Initiating Compact Sub-Array: Branch: '. (int)$udt_obj->getBranch() .' Department: '. (int)$udt_obj->getDepartment() , __FILE__, __LINE__, __METHOD__, 10);
								$user_data_total_compact_arr[20][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()] = array( 'total_time' => $regular_total_time, 'quantity' => $regular_quantity, 'bad_quantity' => $regular_bad_quantity );
							}
							Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; Compact Array Regular Total: '. $user_data_total_compact_arr[20][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['total_time'] , __FILE__, __LINE__, __METHOD__, 10);

							$total_daily_hours_used += $regular_total_time;
						}

						Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; '. $i .': Daily Total Time: '. $total_daily_hours .' Trigger Time: '. $trigger_time_arr[$i]['trigger_time'] .' Used Total Time: '. $total_daily_hours_used .' Overtime Policy Type: '. $trigger_time_arr[$i]['over_time_policy_type_id'], __FILE__, __LINE__, __METHOD__, 10);

						if ( $total_daily_hours > $trigger_time_arr[$i]['trigger_time'] ) {
							Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; '. $i .': Trigger Time: '. $trigger_time_arr[$i]['trigger_time'] .' greater then 0, found Over Time.', __FILE__, __LINE__, __METHOD__, 10);

							if ( isset($trigger_time_arr[$i+1]['trigger_time'] ) ) {
								Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; '. $i .': Found trigger time after this one: '. $trigger_time_arr[$i+1]['trigger_time'] , __FILE__, __LINE__, __METHOD__, 10);
								$max_trigger_time = $trigger_time_arr[$i+1]['trigger_time'] - $trigger_time_arr[$i]['trigger_time'];
							} else {
								$max_trigger_time = $trigger_time_arr[$i]['trigger_time'];
							}
							Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; aMax Trigger Time '. $max_trigger_time, __FILE__, __LINE__, __METHOD__, 10);

							if ( isset($trigger_time_arr[$i+1]['trigger_time']) AND $total_daily_hours_used > $trigger_time_arr[$i]['trigger_time'] ) {
								//$max_trigger_time = $max_trigger_time - ($total_daily_hours_used - $max_trigger_time);
								$max_trigger_time = $max_trigger_time - ($total_daily_hours_used - $trigger_time_arr[$i]['trigger_time']) ;
							}
							Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; bMax Trigger Time '. $max_trigger_time, __FILE__, __LINE__, __METHOD__, 10);

							$over_time_total = $total_daily_hours - $total_daily_hours_used;
							if ( isset($trigger_time_arr[$i+1]['trigger_time'])
									AND $max_trigger_time > 0
									AND $over_time_total > $max_trigger_time ) {
								$over_time_total = $max_trigger_time;
							}

							if ( $over_time_total > 0 ) {
								$over_time_quantity_percent = bcdiv( $over_time_total, $udt_obj->getTotalTime() );
								$over_time_quantity = round( bcmul($udt_obj->getQuantity(), $over_time_quantity_percent), 2);
								$over_time_bad_quantity = round( bcmul($udt_obj->getBadQuantity(), $over_time_quantity_percent), 2);

								Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; Inserting Hours ('. $over_time_total .') for Policy ID: '. $trigger_time_arr[$i]['over_time_policy_id'], __FILE__, __LINE__, __METHOD__, 10);

								if ( isset($user_data_total_compact_arr[30][$trigger_time_arr[$i]['over_time_policy_id']][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()] ) ) {
									Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; Adding to Compact Array: Policy ID: '.$trigger_time_arr[$i]['over_time_policy_id'] .' Branch: '. (int)$udt_obj->getBranch() .' Department: '. (int)$udt_obj->getDepartment(), __FILE__, __LINE__, __METHOD__, 10);
									$user_data_total_compact_arr[30][$trigger_time_arr[$i]['over_time_policy_id']][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['total_time'] += $over_time_total;
									$user_data_total_compact_arr[30][$trigger_time_arr[$i]['over_time_policy_id']][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['quantity'] += $over_time_quantity;
									$user_data_total_compact_arr[30][$trigger_time_arr[$i]['over_time_policy_id']][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['bad_quantity'] += $over_time_bad_quantity;
								} else {
									Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; Initiating Compact Sub-Array: Policy ID: '.$trigger_time_arr[$i]['over_time_policy_id'] .' Branch: '. (int)$udt_obj->getBranch() .' Department: '. (int)$udt_obj->getDepartment() , __FILE__, __LINE__, __METHOD__, 10);
									$user_data_total_compact_arr[30][$trigger_time_arr[$i]['over_time_policy_id']][(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()] = array( 'total_time' => $over_time_total, 'quantity' => $over_time_quantity, 'bad_quantity' => $over_time_bad_quantity );
								}

								$total_daily_hours_used += $over_time_total;
							} else {
								Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; Over Time Total is 0: '. $over_time_total, __FILE__, __LINE__, __METHOD__, 10);
							}

							unset($over_time_total, $over_time_quantity_percent, $over_time_quantity, $over_time_bad_quantity);
						} else {
							break;
						}

						$i++;

					}
					unset($udt_total_time);
				}
				unset($tmp_policy_total_time, $trigger_time_data, $trigger_time_arr);
			}
		}

		$profiler->stopTimer( "UserDateTotal::calcOverTimePolicyTotalTime() - Part 1");

		if ( isset($user_data_total_compact_arr) ) {
			return $user_data_total_compact_arr;
		}

		return FALSE;
	}


	//Take all punches for a given day, take into account the minimum time between shifts,
	//and return an array of shifts, with their start/end and total time calculated.
	function getShiftDataByUserDateID( $user_date_id = NULL ) {
		if ( $user_date_id == '' ) {
			$user_date_id = $this->getUserDateObject()->getId();
		}

		$new_shift_trigger_time = 3600*4; //Default to 8hrs
		if ( is_object( $this->getUserDateObject()->getPayPeriodObject() )
				AND is_object( $this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject() ) ) {
			$new_shift_trigger_time = $this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject()->getNewDayTriggerTime();
		}

		$plf = new PunchListFactory();
		$plf->getByUserDateId( $user_date_id );
		if ( $plf->getRecordCount() > 0 ) {
			$shift = 0;
			$i=0;
			foreach( $plf as $p_obj ) {
				$total_time = $p_obj->getPunchControlObject()->getTotalTime();

				if ( $total_time == 0 ) {
					continue;
				}

				if ( $i > 0 AND isset($shift_data[$shift]['last_out'])
						AND $p_obj->getStatus() == 10) {
					Debug::text('Checking for new shift...', __FILE__, __LINE__, __METHOD__, 10);
					if ( ($p_obj->getTimeStamp() - $shift_data[$shift]['last_out']) > $new_shift_trigger_time ) {
						$shift++;
					}
				}

				if ( !isset($shift_data[$shift]['total_time']) ) {
					$shift_data[$shift]['total_time'] = 0;
				}

				$shift_data[$shift]['punches'][] = $p_obj->getTimeStamp();
				if ( !isset($shift_data[$shift]['first_in']) AND $p_obj->getStatus() == 10 ) {
					$shift_data[$shift]['first_in'] = $p_obj->getTimeStamp();
				} elseif ( $p_obj->getStatus() == 20 ) {
					$shift_data[$shift]['last_out'] = $p_obj->getTimeStamp();
					$shift_data[$shift]['total_time'] += $total_time;
				}

				$i++;
			}

			if ( isset($shift_data)) {
				return $shift_data;
			}
		}

		return FALSE;
	}

	function calcPremiumPolicyTotalTime( $udt_meal_policy_adjustment_arr, $udt_break_policy_adjustment_arr, $daily_total_time = FALSE ) {
		global $profiler;

		$profiler->startTimer( "UserDateTotal::calcPremiumPolicyTotalTime() - Part 1");

		if ( $daily_total_time === FALSE ) {
			$daily_total_time = $this->getDailyTotalTime();
		}

		$pplf = new PremiumPolicyListFactory();
		$pplf->getByPolicyGroupUserId( $this->getUserDateObject()->getUser() );
		if ( $pplf->getRecordCount() > 0 ) {
			Debug::text('Found Premium Policies to apply.', __FILE__, __LINE__, __METHOD__, 10);

			foreach( $pplf as $pp_obj ) {
				Debug::text('Found Premium Policy: ID: '. $pp_obj->getId() .' Type: '. $pp_obj->getType(), __FILE__, __LINE__, __METHOD__, 10);

				//FIXME: Support manually setting a premium policy through the Edit Hours page?
				//In those cases, just skip auto-calculating it and accept it?
				switch( $pp_obj->getType() ) {
					case 10: //Date/Time
						Debug::text(' Date/Time Premium Policy...', __FILE__, __LINE__, __METHOD__, 10);

						//Make sure this is a valid day
						//Take into account shifts that span midnight though, where one half of the shift is eligilble for premium time.
						//ie: Premium Policy starts 7AM to 7PM on Sat/Sun. Punches in at 9PM Friday and out at 9AM Sat, we need to check if both days are valid.
						//FIXME: Handle shifts that are longer than 24hrs in length.
						if ( $pp_obj->isActive( $this->getUserDateObject()->getDateStamp()-86400, $this->getUserDateObject()->getDateStamp()+86400 ) ) {
							Debug::text(' Premium Policy Is Active On OR Around This Day.', __FILE__, __LINE__, __METHOD__, 10);

							$total_daily_time_used = 0;
							$daily_trigger_time = 0;

							$udtlf = new UserDateTotalListFactory();

							if ( $pp_obj->isHourRestricted() == TRUE ) {
								if ( $pp_obj->getWeeklyTriggerTime() > 0 ) {
									//Get Pay Period Schedule info
									if ( is_object( $this->getUserDateObject()->getPayPeriodObject() )
											AND is_object( $this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject() ) ) {
										$start_week_day_id = $this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject()->getStartWeekDay();
									} else {
										$start_week_day_id = 0;
									}
									Debug::text('Start Week Day ID: '. $start_week_day_id, __FILE__, __LINE__, __METHOD__, 10);

									$weekly_total_time = $udtlf->getWeekRegularTimeSumByUserIDAndEpochAndStartWeekEpoch( $this->getUserDateObject()->getUser(), $this->getUserDateObject()->getDateStamp(), TTDate::getBeginWeekEpoch($this->getUserDateObject()->getDateStamp(), $start_week_day_id) );
									if ( $weekly_total_time > $pp_obj->getWeeklyTriggerTime() ) {
										$daily_trigger_time = 0;
									} else {
										$daily_trigger_time = $pp_obj->getWeeklyTriggerTime() - $weekly_total_time;
									}
									Debug::text(' Weekly Trigger Time: '. $daily_trigger_time, __FILE__, __LINE__, __METHOD__, 10);
								}

								if ( $pp_obj->getDailyTriggerTime() > 0 AND $pp_obj->getDailyTriggerTime() > $daily_trigger_time) {
									$daily_trigger_time = $pp_obj->getDailyTriggerTime();
								}
							}
							Debug::text(' Daily Trigger Time: '. $daily_trigger_time, __FILE__, __LINE__, __METHOD__, 10);

							//Loop through all worked (status: 20) UserDateTotalRows
							$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), 20 );
							$i = 1;
							if ( $udtlf->getRecordCount() > 0 ) {
								Debug::text('Found Total Hours to attempt to apply premium policy... Record Count: '. $udtlf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);

								foreach( $udtlf as $udt_obj ) {
									Debug::text('UserDateTotal ID: '. $udt_obj->getID() .' Total Time: '. $udt_obj->getTotalTime(), __FILE__, __LINE__, __METHOD__, 10);

									//Ignore incomplete punches
									if ( $udt_obj->getTotalTime() == 0 ) {
										continue;
									}

									//How do we handle actual shifts for premium time?
									//So if premium policy starts at 1PM for shifts, to not
									//include employees who return from lunch at 1:30PM.
									//Create a function that takes all punches for a day, and returns
									//the first in and last out time for a given shift when taking
									//into account minimum time between shifts, as well as the total time for that shift.
									//We can then use that time for ActiveTime on premium policies, and determine if a
									//punch falls within the active time, then we add it to the total.
									if ( $pp_obj->isTimeRestricted() == TRUE AND $udt_obj->getPunchControlID() != FALSE ) {
										Debug::text('Time Restricted Premium Policy, lookup punches to get times.', __FILE__, __LINE__, __METHOD__, 10);

										if ( $pp_obj->getIncludePartialPunch() == FALSE ) {
											$shift_data = $this->getShiftDataByUserDateID( $this->getUserDateID() );
										}

										$plf = new PunchListFactory();
										$plf->getByPunchControlId( $udt_obj->getPunchControlID() );
										if ( $plf->getRecordCount() > 0 ) {
											Debug::text('Found Punches: '. $plf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
											foreach( $plf as $punch_obj ) {
												if ( $pp_obj->getIncludePartialPunch() == TRUE ) {
													//Debug::text('Including Partial Punches...', __FILE__, __LINE__, __METHOD__, 10);

													if ( $punch_obj->getStatus() == 10 ) {
														$punch_times['in'] = $punch_obj->getTimeStamp();
													} elseif ( $punch_obj->getStatus() == 20 ) {
														$punch_times['out'] = $punch_obj->getTimeStamp();
													}
												} else {
													if ( isset($shift_data) AND is_array($shift_data) ) {
														foreach( $shift_data as $shift ) {
															if ( $punch_obj->getTimeStamp() >= $shift['first_in']
																	AND $punch_obj->getTimeStamp() <= $shift['last_out'] ) {
																//Debug::Arr($shift,'Shift Data...', __FILE__, __LINE__, __METHOD__, 10);
																Debug::text('Punch ('. TTDate::getDate('DATE+TIME', $punch_obj->getTimeStamp() ).') inside shift time...', __FILE__, __LINE__, __METHOD__, 10);
																$punch_times['in'] = $shift['first_in'];
																$punch_times['out'] = $shift['last_out'];
																break;
															} else {
																Debug::text('Punch ('. TTDate::getDate('DATE+TIME', $punch_obj->getTimeStamp() ).') outside shift time...', __FILE__, __LINE__, __METHOD__, 10);
															}
														}
													}
												}
											}

											if ( isset($punch_times) AND count($punch_times) == 2
													AND $pp_obj->isActiveTime( $punch_times['in'], $punch_times['out'] ) == TRUE ) {
												//Debug::Arr($punch_times, 'Punch Times: ', __FILE__, __LINE__, __METHOD__, 10);
												$punch_total_time = $pp_obj->getPartialPunchTotalTime( $punch_times['in'], $punch_times['out'], $udt_obj->getTotalTime() );
												Debug::text('Valid Punch pair in active time, Partial Punch Total Time: '. $punch_total_time, __FILE__, __LINE__, __METHOD__, 10);
											} else {
												Debug::text('InValid Punch Pair or outside Active Time...', __FILE__, __LINE__, __METHOD__, 10);
												$punch_total_time = 0;
											}
										}
									} elseif ( $pp_obj->isActive( $udt_obj->getUserDateObject()->getDateStamp() ) == TRUE )  {
										$punch_total_time = $udt_obj->getTotalTime();
									} else {
										$punch_total_time = 0;
									}
									Debug::text('aPunch Total Time: '. $punch_total_time, __FILE__, __LINE__, __METHOD__, 10);

									//Apply meal policy adjustment as early as possible.
									if ( $pp_obj->getIncludeMealPolicy() == TRUE AND isset( $udt_meal_policy_adjustment_arr[$udt_obj->getId()] ) ) {
										Debug::text(' Meal Policy Adjustment Found: '. $udt_meal_policy_adjustment_arr[$udt_obj->getId()], __FILE__, __LINE__, __METHOD__, 10);
										$punch_total_time = bcadd($punch_total_time, $udt_meal_policy_adjustment_arr[$udt_obj->getId()] );
										$tmp_punch_total_time = bcadd( $udt_obj->getTotalTime(), $udt_meal_policy_adjustment_arr[$udt_obj->getId()] );
									} else {
										$tmp_punch_total_time = $udt_obj->getTotalTime();
									}
									Debug::text('bPunch Total Time: '. $punch_total_time, __FILE__, __LINE__, __METHOD__, 10);

									//Apply break policy adjustment as early as possible.
									if ( $pp_obj->getIncludeBreakPolicy() == TRUE AND isset( $udt_break_policy_adjustment_arr[$udt_obj->getId()] ) ) {
										Debug::text(' Break Policy Adjustment Found: '. $udt_break_policy_adjustment_arr[$udt_obj->getId()], __FILE__, __LINE__, __METHOD__, 10);
										$punch_total_time = bcadd($punch_total_time, $udt_break_policy_adjustment_arr[$udt_obj->getId()] );
										$tmp_punch_total_time = bcadd( $udt_obj->getTotalTime(), $udt_break_policy_adjustment_arr[$udt_obj->getId()] );
									} else {
										$tmp_punch_total_time = $udt_obj->getTotalTime();
									}
									Debug::text('cPunch Total Time: '. $punch_total_time, __FILE__, __LINE__, __METHOD__, 10);

									$total_daily_time_used += $tmp_punch_total_time;
									Debug::text('Daily Total Time Used: '. $total_daily_time_used, __FILE__, __LINE__, __METHOD__, 10);

									//FIXME: Should the daily/weekly trigger time be >= instead of >.
									//That way if the policy is active after 7.5hrs, punch time of exactly 7.5hrs will still
									//activate the policy, rather then requiring 7.501hrs+
									if ( $punch_total_time > 0 AND $total_daily_time_used > $daily_trigger_time ) {
										Debug::text('Past Trigger Time!!', __FILE__, __LINE__, __METHOD__, 10);

										//Calculate how far past trigger time we are.
										$past_trigger_time = $total_daily_time_used - $daily_trigger_time;
										if ( $punch_total_time > $past_trigger_time ) {
											$punch_total_time = $past_trigger_time;
											Debug::text('Using Past Trigger Time as punch total time: '. $past_trigger_time, __FILE__, __LINE__, __METHOD__, 10);
										} else {
											Debug::text('NOT Using Past Trigger Time as punch total time: '. $past_trigger_time, __FILE__, __LINE__, __METHOD__, 10);
										}

										$total_time = $punch_total_time;
										if ( $pp_obj->getMinimumTime() > 0 OR $pp_obj->getMaximumTime() > 0 ) {
											$premium_policy_daily_total_time = (int)$udtlf->getPremiumPolicySumByUserDateIDAndPremiumPolicyID( $this->getUserDateID(), $pp_obj->getId() );
											Debug::text(' Premium Policy Daily Total Time: '. $premium_policy_daily_total_time .' Minimum Time: '. $pp_obj->getMinimumTime() .' Maximum Time: '. $pp_obj->getMaximumTime() .' Total Time: '. $total_time, __FILE__, __LINE__, __METHOD__, 10);

											if ( $pp_obj->getMinimumTime() > 0 ) {
												//FIXME: Split the minimum time up between all the punches somehow.
												//Apply the minimum time on the last punch, otherwise if there are two punch pairs of 15min each
												//and a 1hr minimum time, if the minimum time is applied to the first, it will be 1hr and 15min
												//for the day. If its applied to the last it will be just 1hr.
												//Min & Max time is based on the shift time, rather then per punch pair time.
												//FIXME: If there is a minimum time set to say 9hrs, and the punches go like this:
												// In: 7:00AM Out: 3:00:PM, Out: 3:30PM (missing 2nd In Punch), the minimum time won't be calculated due to the invalid punch pair.
												if ( $i == $udtlf->getRecordCount() AND bcadd( $premium_policy_daily_total_time, $total_time ) < $pp_obj->getMinimumTime() ) {
													$total_time = bcsub( $pp_obj->getMinimumTime(), $premium_policy_daily_total_time );
												}
											}

											Debug::text(' Total Time After Minimum is applied: '. $total_time, __FILE__, __LINE__, __METHOD__, 10);
											if ( $pp_obj->getMaximumTime() > 0 ) {
												//Min & Max time is based on the shift time, rather then per punch pair time.
												if ( bcadd( $premium_policy_daily_total_time, $total_time ) > $pp_obj->getMaximumTime() ) {
													Debug::text(' bMore than Maximum Time...', __FILE__, __LINE__, __METHOD__, 10);
													$total_time = bcsub( $total_time, bcsub( bcadd( $premium_policy_daily_total_time, $total_time ), $pp_obj->getMaximumTime() ) );
												}
											}
										}

										Debug::text(' Premium Punch Total Time: '. $total_time, __FILE__, __LINE__, __METHOD__, 10);
										if ( $total_time > 0 ) {
											Debug::text(' Applying  Premium Time!: '. $total_time , __FILE__, __LINE__, __METHOD__, 10);

											$udtf = new UserDateTotalFactory();
											$udtf->setUserDateID( $this->getUserDateID() );
											$udtf->setStatus( 10 ); //System
											$udtf->setType( 40 ); //Premium
											$udtf->setPremiumPolicyId( $pp_obj->getId() );
											$udtf->setBranch( $udt_obj->getBranch() );
											$udtf->setDepartment( $udt_obj->getDepartment() );
											$udtf->setJob( $udt_obj->getJob() );
											$udtf->setJobItem( $udt_obj->getJobItem() );

											$udtf->setQuantity( $udt_obj->getQuantity() );
											$udtf->setBadQuantity( $udt_obj->getBadQuantity() );

											$udtf->setTotalTime( $total_time );
											$udtf->setEnableCalcSystemTotalTime(FALSE);
											if ( $udtf->isValid() == TRUE ) {
												$udtf->Save();
											}
											unset($udtf);
										} else {
											Debug::text(' Premium Punch Total Time is 0...', __FILE__, __LINE__, __METHOD__, 10);
										}
									} else {
										Debug::text('Not Past Trigger Time Yet or Punch Time is 0...', __FILE__, __LINE__, __METHOD__, 10);
									}

									$i++;
								}
							}
						}
						break;
					case 20: //Differential
						Debug::text(' Differential Premium Policy...', __FILE__, __LINE__, __METHOD__, 10);

						//Loop through all worked (status: 20) UserDateTotalRows
						$udtlf = new UserDateTotalListFactory();
						$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), 20 );
						if ( $udtlf->getRecordCount() > 0 ) {
							Debug::text('Found Total Hours to attempt to apply premium policy... Record Count: '. $udtlf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);

							foreach( $udtlf as $udt_obj ) {
								//Ignore incomplete punches
								if ( $udt_obj->getTotalTime() == 0 ) {
									continue;
								}

								if ( ( $pp_obj->getBranchSelectionType() == 10
											AND ( $pp_obj->getExcludeDefaultBranch() == FALSE
													OR ( $pp_obj->getExcludeDefaultBranch() == TRUE
															AND $udt_obj->getBranch() != $this->getUserDateObject()->getUserObject()->getDefaultBranch() ) ) )

										OR ( $pp_obj->getBranchSelectionType() == 20
												AND in_array( $udt_obj->getBranch(), (array)$pp_obj->getBranch() ) )
												AND ( $pp_obj->getExcludeDefaultBranch() == FALSE
														OR ( $pp_obj->getExcludeDefaultBranch() == TRUE
																AND $udt_obj->getBranch() != $this->getUserDateObject()->getUserObject()->getDefaultBranch() ) )

										OR ( $pp_obj->getBranchSelectionType() == 30
												AND !in_array( $udt_obj->getBranch(), (array)$pp_obj->getBranch() ) )
												AND ( $pp_obj->getExcludeDefaultBranch() == FALSE
														OR ( $pp_obj->getExcludeDefaultBranch() == TRUE
																AND $udt_obj->getBranch() != $this->getUserDateObject()->getUserObject()->getDefaultBranch() ) )

										) {
									Debug::text(' Shift Differential... Meets Branch Criteria! Select Type: '. $pp_obj->getBranchSelectionType() .' Exclude Default Branch: '. (int)$pp_obj->getExcludeDefaultBranch() .' Default Branch: '.  $this->getUserDateObject()->getUserObject()->getDefaultBranch(), __FILE__, __LINE__, __METHOD__, 10);

									if ( ( $pp_obj->getDepartmentSelectionType() == 10
												AND ( $pp_obj->getExcludeDefaultDepartment() == FALSE
														OR ( $pp_obj->getExcludeDefaultDepartment() == TRUE
																AND $udt_obj->getDepartment() != $this->getUserDateObject()->getUserObject()->getDefaultDepartment() ) ) )

											OR ( $pp_obj->getDepartmentSelectionType() == 20
													AND in_array( $udt_obj->getDepartment(), (array)$pp_obj->getDepartment() ) )
													AND ( $pp_obj->getExcludeDefaultDepartment() == FALSE
															OR ( $pp_obj->getExcludeDefaultDepartment() == TRUE
																	AND $udt_obj->getDepartment() != $this->getUserDateObject()->getUserObject()->getDefaultDepartment() ) )

											OR ( $pp_obj->getDepartmentSelectionType() == 30
													AND !in_array( $udt_obj->getDepartment(), (array)$pp_obj->getDepartment() ) )
													AND ( $pp_obj->getExcludeDefaultDepartment() == FALSE
															OR ( $pp_obj->getExcludeDefaultDepartment() == TRUE
																	AND $udt_obj->getDepartment() != $this->getUserDateObject()->getUserObject()->getDefaultDepartment() ) )

											) {
										Debug::text(' Shift Differential... Meets Department Criteria! Select Type: '. $pp_obj->getDepartmentSelectionType() .' Exclude Default Department: '. (int)$pp_obj->getExcludeDefaultDepartment() .' Default Department: '.  $this->getUserDateObject()->getUserObject()->getDefaultDepartment(), __FILE__, __LINE__, __METHOD__, 10);


										if ( $pp_obj->getJobGroupSelectionType() == 10
												OR ( $pp_obj->getJobGroupSelectionType() == 20
														AND ( is_object( $udt_obj->getJobObject() ) AND in_array( $udt_obj->getJobObject()->getGroup(), (array)$pp_obj->getJobGroup() ) ) )
												OR ( $pp_obj->getJobGroupSelectionType() == 30
														AND ( is_object( $udt_obj->getJobObject() ) AND !in_array( $udt_obj->getJobObject()->getGroup(), (array)$pp_obj->getJobGroup() ) ) )
												) {
											Debug::text(' Shift Differential... Meets Job Group Criteria! Select Type: '. $pp_obj->getJobGroupSelectionType(), __FILE__, __LINE__, __METHOD__, 10);

											if ( $pp_obj->getJobSelectionType() == 10
													OR ( $pp_obj->getJobSelectionType() == 20
															AND in_array( $udt_obj->getJob(), (array)$pp_obj->getJob() ) )
													OR ( $pp_obj->getJobSelectionType() == 30
															AND !in_array( $udt_obj->getJob(), (array)$pp_obj->getJob() ) )
													) {
												Debug::text(' Shift Differential... Meets Job Criteria! Select Type: '. $pp_obj->getJobSelectionType(), __FILE__, __LINE__, __METHOD__, 10);

												if ( $pp_obj->getJobItemGroupSelectionType() == 10
														OR ( $pp_obj->getJobItemGroupSelectionType() == 20
																AND ( is_object( $udt_obj->getJobItemObject() ) AND in_array( $udt_obj->getJobItemObject()->getGroup(), (array)$pp_obj->getJobItemGroup() ) ) )
														OR ( $pp_obj->getJobItemGroupSelectionType() == 30
																AND ( is_object( $udt_obj->getJobItemObject() ) AND !in_array( $udt_obj->getJobItemObject()->getGroup(), (array)$pp_obj->getJobItemGroup() ) ) )
														) {
													Debug::text(' Shift Differential... Meets Task Group Criteria! Select Type: '. $pp_obj->getJobItemGroupSelectionType(), __FILE__, __LINE__, __METHOD__, 10);

													if ( $pp_obj->getJobItemSelectionType() == 10
															OR ( $pp_obj->getJobItemSelectionType() == 20
																	AND in_array( $udt_obj->getJobItem(), (array)$pp_obj->getJobItem() ) )
															OR ( $pp_obj->getJobItemSelectionType() == 30
																	AND !in_array( $udt_obj->getJobItem(), (array)$pp_obj->getJobItem() ) )
															) {
														Debug::text(' Shift Differential... Meets Task Criteria! Select Type: '. $pp_obj->getJobSelectionType(), __FILE__, __LINE__, __METHOD__, 10);

														$premium_policy_daily_total_time = 0;
														$punch_total_time = $udt_obj->getTotalTime();
														$total_time = 0;

														//Apply meal policy adjustment BEFORE min/max times
														if ( $pp_obj->getIncludeMealPolicy() == TRUE AND isset( $udt_meal_policy_adjustment_arr[$udt_obj->getId()] ) ) {
															Debug::text(' Meal Policy Adjustment Found: '. $udt_meal_policy_adjustment_arr[$udt_obj->getId()], __FILE__, __LINE__, __METHOD__, 10);
															$punch_total_time = bcadd( $punch_total_time, $udt_meal_policy_adjustment_arr[$udt_obj->getId()] );
														}
														if ( $pp_obj->getIncludeBreakPolicy() == TRUE AND isset( $udt_break_policy_adjustment_arr[$udt_obj->getId()] ) ) {
															Debug::text(' Break Policy Adjustment Found: '. $udt_break_policy_adjustment_arr[$udt_obj->getId()], __FILE__, __LINE__, __METHOD__, 10);
															$punch_total_time = bcadd( $punch_total_time, $udt_break_policy_adjustment_arr[$udt_obj->getId()] );
														}

														if ( $pp_obj->getMinimumTime() > 0 OR $pp_obj->getMaximumTime() > 0 ) {
															$premium_policy_daily_total_time = $udtlf->getPremiumPolicySumByUserDateIDAndPremiumPolicyID( $this->getUserDateID(), $pp_obj->getId() );
															Debug::text(' Premium Policy Daily Total Time: '. $premium_policy_daily_total_time .' Minimum Time: '. $pp_obj->getMinimumTime() .' Maximum Time: '. $pp_obj->getMaximumTime(), __FILE__, __LINE__, __METHOD__, 10);

															if ( $pp_obj->getMinimumTime() > 0 ) {
																if ( $daily_total_time < $pp_obj->getMinimumTime() ) {
																	//Split the minimum time up between all punches
																	//We only get IN punches, so we don't need to divide $total_punches by 2.
																	//This won't calculate the proper amount if punches aren't paired, but everything
																	//is broken then anyways.
																	$total_time = bcdiv( $pp_obj->getMinimumTime(), $total_punches );
																	Debug::text(' Daily Total Time is less the Minimum, using: '. $total_time .' Total Punches: '. $total_punches, __FILE__, __LINE__, __METHOD__, 10);
																} else {
																	Debug::text(' Daily Total is more then minimum...', __FILE__, __LINE__, __METHOD__, 10);
																	$total_time = $punch_total_time;
																}
															} else {
																$total_time = $punch_total_time;
															}

															Debug::text(' Total Time After Minimum is applied: '. $total_time, __FILE__, __LINE__, __METHOD__, 10);
															if ( $pp_obj->getMaximumTime() > 0 ) {
																if ( $total_time > $pp_obj->getMaximumTime() ) {
																	Debug::text(' aMore than Maximum Time...', __FILE__, __LINE__, __METHOD__, 10);
																	$total_time = $pp_obj->getMaximumTime();
																} elseif ( bcadd( $premium_policy_daily_total_time, $total_time ) > $pp_obj->getMaximumTime() ) {
																	Debug::text(' bMore than Maximum Time...', __FILE__, __LINE__, __METHOD__, 10);
																	$total_time = bcsub( bcadd( $premium_policy_daily_total_time, $total_time ), $pp_obj->getMaximumTime() );
																}
															}
														} else {
															$total_time = $punch_total_time;
														}

														Debug::text(' Premium Punch Total Time: '. $total_time, __FILE__, __LINE__, __METHOD__, 10);
														if ( $total_time > 0 ) {
															Debug::text(' Applying  Premium Time!: '. $total_time , __FILE__, __LINE__, __METHOD__, 10);

															$udtf = new UserDateTotalFactory();
															$udtf->setUserDateID( $this->getUserDateID() );
															$udtf->setStatus( 10 ); //System
															$udtf->setType( 40 ); //Premium
															$udtf->setPremiumPolicyId( $pp_obj->getId() );
															$udtf->setBranch( $udt_obj->getBranch() );
															$udtf->setDepartment( $udt_obj->getDepartment() );
															$udtf->setJob( $udt_obj->getJob() );
															$udtf->setJobItem( $udt_obj->getJobItem() );

															$udtf->setQuantity( $udt_obj->getQuantity() );
															$udtf->setBadQuantity( $udt_obj->getBadQuantity() );

															$udtf->setTotalTime( $total_time );
															$udtf->setEnableCalcSystemTotalTime(FALSE);
															if ( $udtf->isValid() == TRUE ) {
																$udtf->Save();
															}
															unset($udtf);
														} else {
															Debug::text(' Premium Punch Total Time is 0...', __FILE__, __LINE__, __METHOD__, 10);
														}
													} else {
														Debug::text(' Shift Differential... DOES NOT Meet Task Criteria!', __FILE__, __LINE__, __METHOD__, 10);
													}
												} else {
													Debug::text(' Shift Differential... DOES NOT Meet Task Group Criteria!', __FILE__, __LINE__, __METHOD__, 10);
												}
											} else {
												Debug::text(' Shift Differential... DOES NOT Meet Job Criteria!', __FILE__, __LINE__, __METHOD__, 10);
											}
										} else {
											Debug::text(' Shift Differential... DOES NOT Meet Job Group Criteria!', __FILE__, __LINE__, __METHOD__, 10);
										}
									} else {
										Debug::text(' Shift Differential... DOES NOT Meet Department Criteria!', __FILE__, __LINE__, __METHOD__, 10);
									}
								} else {
									Debug::text(' Shift Differential... DOES NOT Meet Branch Criteria!', __FILE__, __LINE__, __METHOD__, 10);
								}
							}
						}
						break;
					case 30: //Meal/Break
						Debug::text(' Meal/Break Premium Policy...', __FILE__, __LINE__, __METHOD__, 10);

						if ( $pp_obj->getDailyTriggerTime() == 0
								OR ( $pp_obj->getDailyTriggerTime() > 0 AND $daily_total_time >= $pp_obj->getDailyTriggerTime() ) ) {
							//Find maximum worked without a break.
							$plf = new PunchListFactory();
							$plf->getByUserDateId( $this->getUserDateID() ); //Get all punches for the day.
							if ( $plf->getRecordCount() > 0 ) {
								Debug::text('Found Punches: '. $plf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
								foreach( $plf as $p_obj ) {
									Debug::text('TimeStamp: '. $p_obj->getTimeStamp() .' Status: '. $p_obj->getStatus(), __FILE__, __LINE__, __METHOD__, 10);
									$punch_pairs[$p_obj->getPunchControlID()][] = array(
																						'status_id' => $p_obj->getStatus(),
																						'punch_control_id' => $p_obj->getPunchControlID(),
																						'time_stamp' => $p_obj->getTimeStamp()
																					);
								}

								if ( isset($punch_pairs) ) {
									$prev_punch_timestamp = NULL;
									$maximum_time_worked_without_break = 0;

									foreach( $punch_pairs as $punch_pair ) {
										if ( count($punch_pair) > 1 ) {
											//Total Punch Time
											$total_punch_pair_time = $punch_pair[1]['time_stamp'] - $punch_pair[0]['time_stamp'];
											$maximum_time_worked_without_break += $total_punch_pair_time;
											Debug::text('Total Punch Pair Time: '. $total_punch_pair_time .' Maximum No Break Time: '. $maximum_time_worked_without_break, __FILE__, __LINE__, __METHOD__, 10);

											if ( $prev_punch_timestamp !== NULL ) {
												$break_time = $punch_pair[0]['time_stamp'] - $prev_punch_timestamp;
												if ( $break_time > $pp_obj->getMinimumBreakTime() ) {
													Debug::text('Exceeded Minimum Break Time: '. $break_time .' Minimum: '. $pp_obj->getMinimumBreakTime(), __FILE__, __LINE__, __METHOD__, 10);
													$maximum_time_worked_without_break = 0;
												}
											}

											if ( $maximum_time_worked_without_break > $pp_obj->getMaximumNoBreakTime() ) {
												Debug::text('Exceeded maximum no break time!', __FILE__, __LINE__, __METHOD__, 10);

												if ( $pp_obj->getMaximumTime() > $pp_obj->getMinimumTime() ) {
													$total_time = $pp_obj->getMaximumTime();
												} else {
													$total_time = $pp_obj->getMinimumTime();
												}

												if ( $total_time > 0 ) {
													Debug::text(' Applying Meal/Break Premium Time!: '. $total_time , __FILE__, __LINE__, __METHOD__, 10);

													//Get Punch Control obj.
													$pclf = new PunchControlListFactory();
													$pclf->getById( $punch_pair[0]['punch_control_id'] );
													if ( $pclf->getRecordCount() > 0 ) {
														$pc_obj = $pclf->getCurrent();
													}

													$udtf = new UserDateTotalFactory();
													$udtf->setUserDateID( $this->getUserDateID() );
													$udtf->setStatus( 10 ); //System
													$udtf->setType( 40 ); //Premium
													$udtf->setPremiumPolicyId( $pp_obj->getId() );

													if ( isset($pc_obj) AND is_object( $pc_obj ) ) {
														$udtf->setBranch( $pc_obj->getBranch() );
														$udtf->setDepartment( $pc_obj->getDepartment() );
														$udtf->setJob( $pc_obj->getJob() );
														$udtf->setJobItem( $pc_obj->getJobItem() );
													}

													$udtf->setTotalTime( $total_time );
													$udtf->setEnableCalcSystemTotalTime(FALSE);
													if ( $udtf->isValid() == TRUE ) {
														$udtf->Save();
													}
													unset($udtf);

													break; //Stop looping through punches.
												}
											} else {
												Debug::text('Did not exceed maximum no break time yet...', __FILE__, __LINE__, __METHOD__, 10);
											}

											$prev_punch_timestamp = $punch_pair[1]['time_stamp'];
										} else {
											Debug::text('Found UnPaired Punch, Ignorning...', __FILE__, __LINE__, __METHOD__, 10);
										}
									}
									unset($plf, $punch_pairs, $punch_pair, $prev_punch_timestamp, $maximum_time_worked_without_break, $total_time);
								}
							}
						} else {
							Debug::text(' Not within Daily Total Time: '. $daily_total_time .' Trigger Time: '. $pp_obj->getDailyTriggerTime(), __FILE__, __LINE__, __METHOD__, 10);
						}
						break;
					case 100: //Advanced
						Debug::text(' Advanced Premium Policy...', __FILE__, __LINE__, __METHOD__, 10);

						//Make sure this is a valid day
						if ( $pp_obj->isActive( $this->getUserDateObject()->getDateStamp()-86400, $this->getUserDateObject()->getDateStamp()+86400 ) ) {

							Debug::text(' Premium Policy Is Active On This Day.', __FILE__, __LINE__, __METHOD__, 10);

							$total_daily_time_used = 0;
							$daily_trigger_time = 0;

							$udtlf = new UserDateTotalListFactory();

							if ( $pp_obj->isHourRestricted() == TRUE ) {
								if ( $pp_obj->getWeeklyTriggerTime() > 0 ) {
									//Get Pay Period Schedule info
									if ( is_object( $this->getUserDateObject()->getPayPeriodObject() )
											AND is_object( $this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject() ) ) {
										$start_week_day_id = $this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject()->getStartWeekDay();
									} else {
										$start_week_day_id = 0;
									}
									Debug::text('Start Week Day ID: '. $start_week_day_id, __FILE__, __LINE__, __METHOD__, 10);

									$weekly_total_time = $udtlf->getWeekRegularTimeSumByUserIDAndEpochAndStartWeekEpoch( $this->getUserDateObject()->getUser(), $this->getUserDateObject()->getDateStamp(), TTDate::getBeginWeekEpoch($this->getUserDateObject()->getDateStamp(), $start_week_day_id) );
									if ( $weekly_total_time > $pp_obj->getWeeklyTriggerTime() ) {
										$daily_trigger_time = 0;
									} else {
										$daily_trigger_time = $pp_obj->getWeeklyTriggerTime() - $weekly_total_time;
									}
									Debug::text(' Weekly Trigger Time: '. $daily_trigger_time, __FILE__, __LINE__, __METHOD__, 10);
								}

								if ( $pp_obj->getDailyTriggerTime() > 0 AND $pp_obj->getDailyTriggerTime() > $daily_trigger_time) {
									$daily_trigger_time = $pp_obj->getDailyTriggerTime();
								}
							}
							Debug::text(' Daily Trigger Time: '. $daily_trigger_time, __FILE__, __LINE__, __METHOD__, 10);

							//Loop through all worked (status: 20) UserDateTotalRows
							$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), 20 );
							$i = 1;
							if ( $udtlf->getRecordCount() > 0 ) {
								Debug::text('Found Total Hours to attempt to apply premium policy... Record Count: '. $udtlf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);

								foreach( $udtlf as $udt_obj ) {
									//Ignore incomplete punches
									if ( $udt_obj->getTotalTime() == 0 ) {
										continue;
									}

									//Check Shift Differential criteria before calculatating daily/weekly time which
									//is more resource intensive.
									if ( ( $pp_obj->getBranchSelectionType() == 10
												AND ( $pp_obj->getExcludeDefaultBranch() == FALSE
														OR ( $pp_obj->getExcludeDefaultBranch() == TRUE
																AND $udt_obj->getBranch() != $this->getUserDateObject()->getUserObject()->getDefaultBranch() ) ) )

											OR ( $pp_obj->getBranchSelectionType() == 20
													AND in_array( $udt_obj->getBranch(), (array)$pp_obj->getBranch() ) )
													AND ( $pp_obj->getExcludeDefaultBranch() == FALSE
															OR ( $pp_obj->getExcludeDefaultBranch() == TRUE
																	AND $udt_obj->getBranch() != $this->getUserDateObject()->getUserObject()->getDefaultBranch() ) )

											OR ( $pp_obj->getBranchSelectionType() == 30
													AND !in_array( $udt_obj->getBranch(), (array)$pp_obj->getBranch() ) )
													AND ( $pp_obj->getExcludeDefaultBranch() == FALSE
															OR ( $pp_obj->getExcludeDefaultBranch() == TRUE
																	AND $udt_obj->getBranch() != $this->getUserDateObject()->getUserObject()->getDefaultBranch() ) )

											) {
										Debug::text(' Shift Differential... Meets Branch Criteria! Select Type: '. $pp_obj->getBranchSelectionType() .' Exclude Default Branch: '. (int)$pp_obj->getExcludeDefaultBranch() .' Default Branch: '.  $this->getUserDateObject()->getUserObject()->getDefaultBranch(), __FILE__, __LINE__, __METHOD__, 10);

										if ( ( $pp_obj->getDepartmentSelectionType() == 10
													AND ( $pp_obj->getExcludeDefaultDepartment() == FALSE
															OR ( $pp_obj->getExcludeDefaultDepartment() == TRUE
																	AND $udt_obj->getDepartment() != $this->getUserDateObject()->getUserObject()->getDefaultDepartment() ) ) )

												OR ( $pp_obj->getDepartmentSelectionType() == 20
														AND in_array( $udt_obj->getDepartment(), (array)$pp_obj->getDepartment() ) )
														AND ( $pp_obj->getExcludeDefaultDepartment() == FALSE
																OR ( $pp_obj->getExcludeDefaultDepartment() == TRUE
																		AND $udt_obj->getDepartment() != $this->getUserDateObject()->getUserObject()->getDefaultDepartment() ) )

												OR ( $pp_obj->getDepartmentSelectionType() == 30
														AND !in_array( $udt_obj->getDepartment(), (array)$pp_obj->getDepartment() ) )
														AND ( $pp_obj->getExcludeDefaultDepartment() == FALSE
																OR ( $pp_obj->getExcludeDefaultDepartment() == TRUE
																		AND $udt_obj->getDepartment() != $this->getUserDateObject()->getUserObject()->getDefaultDepartment() ) )

												) {
											Debug::text(' Shift Differential... Meets Department Criteria! Select Type: '. $pp_obj->getDepartmentSelectionType() .' Exclude Default Department: '. (int)$pp_obj->getExcludeDefaultDepartment() .' Default Department: '.  $this->getUserDateObject()->getUserObject()->getDefaultDepartment(), __FILE__, __LINE__, __METHOD__, 10);


											if ( $pp_obj->getJobGroupSelectionType() == 10
													OR ( $pp_obj->getJobGroupSelectionType() == 20
															AND is_object( $udt_obj->getJobObject() )
															AND in_array( $udt_obj->getJobObject()->getGroup(), (array)$pp_obj->getJobGroup() ) )
													OR ( $pp_obj->getJobGroupSelectionType() == 30
															AND is_object( $udt_obj->getJobObject() )
															AND !in_array( $udt_obj->getJobObject()->getGroup(), (array)$pp_obj->getJobGroup() ) )
													) {
												Debug::text(' Shift Differential... Meets Job Group Criteria! Select Type: '. $pp_obj->getJobGroupSelectionType(), __FILE__, __LINE__, __METHOD__, 10);

												if ( $pp_obj->getJobSelectionType() == 10
														OR ( $pp_obj->getJobSelectionType() == 20
																AND in_array( $udt_obj->getJob(), (array)$pp_obj->getJob() ) )
														OR ( $pp_obj->getJobSelectionType() == 30
																AND !in_array( $udt_obj->getJob(), (array)$pp_obj->getJob() ) )
														) {
													Debug::text(' Shift Differential... Meets Job Criteria! Select Type: '. $pp_obj->getJobSelectionType(), __FILE__, __LINE__, __METHOD__, 10);

													if ( $pp_obj->getJobItemGroupSelectionType() == 10
															OR ( $pp_obj->getJobItemGroupSelectionType() == 20
																	AND is_object( $udt_obj->getJobItemObject() )
																	AND in_array( $udt_obj->getJobItemObject()->getGroup(), (array)$pp_obj->getJobItemGroup() ) )
															OR ( $pp_obj->getJobItemGroupSelectionType() == 30
																	AND is_object( $udt_obj->getJobItemObject() )
																	AND !in_array( $udt_obj->getJobItemObject()->getGroup(), (array)$pp_obj->getJobItemGroup() ) )
															) {
														Debug::text(' Shift Differential... Meets Task Group Criteria! Select Type: '. $pp_obj->getJobItemGroupSelectionType(), __FILE__, __LINE__, __METHOD__, 10);

														if ( $pp_obj->getJobItemSelectionType() == 10
																OR ( $pp_obj->getJobItemSelectionType() == 20
																		AND in_array( $udt_obj->getJobItem(), (array)$pp_obj->getJobItem() ) )
																OR ( $pp_obj->getJobItemSelectionType() == 30
																		AND !in_array( $udt_obj->getJobItem(), (array)$pp_obj->getJobItem() ) )
																) {
															Debug::text(' Shift Differential... Meets Task Criteria! Select Type: '. $pp_obj->getJobSelectionType(), __FILE__, __LINE__, __METHOD__, 10);

															if ( $pp_obj->isTimeRestricted() == TRUE AND $udt_obj->getPunchControlID() != FALSE ) {
																Debug::text('Time Restricted Premium Policy, lookup punches to get times.', __FILE__, __LINE__, __METHOD__, 10);

																if ( $pp_obj->getIncludePartialPunch() == FALSE ) {
																	$shift_data = $this->getShiftDataByUserDateID( $this->getUserDateID() );
																}

																$plf = new PunchListFactory();
																$plf->getByPunchControlId( $udt_obj->getPunchControlID() );
																if ( $plf->getRecordCount() > 0 ) {
																	Debug::text('Found Punches: '. $plf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
																	foreach( $plf as $punch_obj ) {
																		if ( $pp_obj->getIncludePartialPunch() == TRUE ) {
																			//Debug::text('Including Partial Punches...', __FILE__, __LINE__, __METHOD__, 10);

																			if ( $punch_obj->getStatus() == 10 ) {
																				$punch_times['in'] = $punch_obj->getTimeStamp();
																			} elseif ( $punch_obj->getStatus() == 20 ) {
																				$punch_times['out'] = $punch_obj->getTimeStamp();
																			}
																		} else {
																			if ( isset($shift_data) AND is_array($shift_data) ) {
																				foreach( $shift_data as $shift ) {
																					if ( $punch_obj->getTimeStamp() >= $shift['first_in']
																							AND $punch_obj->getTimeStamp() <= $shift['last_out'] ) {
																						//Debug::Arr($shift,'Shift Data...', __FILE__, __LINE__, __METHOD__, 10);
																						Debug::text('Punch ('. TTDate::getDate('DATE+TIME', $punch_obj->getTimeStamp() ).') inside shift time...', __FILE__, __LINE__, __METHOD__, 10);
																						$punch_times['in'] = $shift['first_in'];
																						$punch_times['out'] = $shift['last_out'];
																						break;
																					} else {
																						Debug::text('Punch ('. TTDate::getDate('DATE+TIME', $punch_obj->getTimeStamp() ).') outside shift time...', __FILE__, __LINE__, __METHOD__, 10);
																					}
																				}
																			}
																		}
																	}

																	if ( isset($punch_times) AND count($punch_times) == 2
																			AND $pp_obj->isActiveTime( $punch_times['in'], $punch_times['out'] ) == TRUE ) {
																		//Debug::Arr($punch_times, 'Punch Times: ', __FILE__, __LINE__, __METHOD__, 10);
																		$punch_total_time = $pp_obj->getPartialPunchTotalTime( $punch_times['in'], $punch_times['out'], $udt_obj->getTotalTime() );
																		Debug::text('Valid Punch pair in active time, Partial Punch Total Time: '. $punch_total_time, __FILE__, __LINE__, __METHOD__, 10);
																	} else {
																		Debug::text('InValid Punch Pair or outside Active Time...', __FILE__, __LINE__, __METHOD__, 10);
																		$punch_total_time = 0;
																	}
																}
															} elseif ( $pp_obj->isActive( $udt_obj->getUserDateObject()->getDateStamp() ) == TRUE )  {
																$punch_total_time = $udt_obj->getTotalTime();
															} else {
																$punch_total_time = 0;
															}
															Debug::text('aPunch Total Time: '. $punch_total_time, __FILE__, __LINE__, __METHOD__, 10);

															//Apply meal policy adjustment as early as possible.
															if ( $pp_obj->getIncludeMealPolicy() == TRUE AND isset( $udt_meal_policy_adjustment_arr[$udt_obj->getId()] ) ) {
																Debug::text(' Meal Policy Adjustment Found: '. $udt_meal_policy_adjustment_arr[$udt_obj->getId()], __FILE__, __LINE__, __METHOD__, 10);
																$punch_total_time = bcadd($punch_total_time, $udt_meal_policy_adjustment_arr[$udt_obj->getId()] );
																$tmp_punch_total_time = bcadd( $udt_obj->getTotalTime(), $udt_meal_policy_adjustment_arr[$udt_obj->getId()] );
															} else {
																$tmp_punch_total_time = $udt_obj->getTotalTime();
															}
															Debug::text('bPunch Total Time: '. $punch_total_time, __FILE__, __LINE__, __METHOD__, 10);

															if ( $pp_obj->getIncludeBreakPolicy() == TRUE AND isset( $udt_break_policy_adjustment_arr[$udt_obj->getId()] ) ) {
																Debug::text(' Break Policy Adjustment Found: '. $udt_break_policy_adjustment_arr[$udt_obj->getId()], __FILE__, __LINE__, __METHOD__, 10);
																$punch_total_time = bcadd($punch_total_time, $udt_break_policy_adjustment_arr[$udt_obj->getId()] );
																$tmp_punch_total_time = bcadd( $udt_obj->getTotalTime(), $udt_break_policy_adjustment_arr[$udt_obj->getId()] );
															} else {
																$tmp_punch_total_time = $udt_obj->getTotalTime();
															}
															Debug::text('cPunch Total Time: '. $punch_total_time, __FILE__, __LINE__, __METHOD__, 10);

															$total_daily_time_used += $tmp_punch_total_time;
															Debug::text('Daily Total Time Used: '. $total_daily_time_used, __FILE__, __LINE__, __METHOD__, 10);

															if ( $punch_total_time > 0 AND $total_daily_time_used > $daily_trigger_time ) {
																Debug::text('Past Trigger Time!!', __FILE__, __LINE__, __METHOD__, 10);

																//Calculate how far past trigger time we are.
																$past_trigger_time = $total_daily_time_used - $daily_trigger_time;
																if ( $punch_total_time > $past_trigger_time ) {
																	$punch_total_time = $past_trigger_time;
																	Debug::text('Using Past Trigger Time as punch total time: '. $past_trigger_time, __FILE__, __LINE__, __METHOD__, 10);
																} else {
																	Debug::text('NOT Using Past Trigger Time as punch total time: '. $past_trigger_time, __FILE__, __LINE__, __METHOD__, 10);
																}

																$total_time = $punch_total_time;
																if ( $pp_obj->getMinimumTime() > 0 OR $pp_obj->getMaximumTime() > 0 ) {
																	$premium_policy_daily_total_time = (int)$udtlf->getPremiumPolicySumByUserDateIDAndPremiumPolicyID( $this->getUserDateID(), $pp_obj->getId() );
																	Debug::text(' Premium Policy Daily Total Time: '. $premium_policy_daily_total_time .' Minimum Time: '. $pp_obj->getMinimumTime() .' Maximum Time: '. $pp_obj->getMaximumTime(), __FILE__, __LINE__, __METHOD__, 10);

																	if ( $pp_obj->getMinimumTime() > 0 ) {
																		//FIXME: Split the minimum time up between all the punches somehow.
																		if ( $i == $udtlf->getRecordCount() AND bcadd( $premium_policy_daily_total_time, $total_time ) < $pp_obj->getMinimumTime() ) {
																			$total_time = bcsub( $pp_obj->getMinimumTime(), $premium_policy_daily_total_time );
																		}
																	}

																	Debug::text(' Total Time After Minimum is applied: '. $total_time, __FILE__, __LINE__, __METHOD__, 10);
																	if ( $pp_obj->getMaximumTime() > 0 ) {
																		if ( $total_time > $pp_obj->getMaximumTime() ) {
																			Debug::text(' aMore than Maximum Time...', __FILE__, __LINE__, __METHOD__, 10);
																			$total_time = $pp_obj->getMaximumTime();
																		} elseif ( bcadd( $premium_policy_daily_total_time, $total_time ) > $pp_obj->getMaximumTime() ) {
																			Debug::text(' bMore than Maximum Time...', __FILE__, __LINE__, __METHOD__, 10);
																			//$total_time = bcsub( bcadd( $premium_policy_daily_total_time, $total_time ), $pp_obj->getMaximumTime() );
																			$total_time = bcsub( $total_time, bcsub( bcadd( $premium_policy_daily_total_time, $total_time ), $pp_obj->getMaximumTime() ) );
																		}
																	}
																}

																Debug::text(' Premium Punch Total Time: '. $total_time, __FILE__, __LINE__, __METHOD__, 10);
																if ( $total_time > 0 ) {
																	Debug::text(' Applying  Premium Time!: '. $total_time , __FILE__, __LINE__, __METHOD__, 10);

																	$udtf = new UserDateTotalFactory();
																	$udtf->setUserDateID( $this->getUserDateID() );
																	$udtf->setStatus( 10 ); //System
																	$udtf->setType( 40 ); //Premium
																	$udtf->setPremiumPolicyId( $pp_obj->getId() );
																	$udtf->setBranch( $udt_obj->getBranch() );
																	$udtf->setDepartment( $udt_obj->getDepartment() );
																	$udtf->setJob( $udt_obj->getJob() );
																	$udtf->setJobItem( $udt_obj->getJobItem() );

																	$udtf->setQuantity( $udt_obj->getQuantity() );
																	$udtf->setBadQuantity( $udt_obj->getBadQuantity() );

																	$udtf->setTotalTime( $total_time );
																	$udtf->setEnableCalcSystemTotalTime(FALSE);
																	if ( $udtf->isValid() == TRUE ) {
																		$udtf->Save();
																	}
																	unset($udtf);
																} else {
																	Debug::text(' Premium Punch Total Time is 0...', __FILE__, __LINE__, __METHOD__, 10);
																}
															} else {
																Debug::text('Not Past Trigger Time Yet or Punch Time is 0...', __FILE__, __LINE__, __METHOD__, 10);
															}
														} else {
															Debug::text(' Shift Differential... DOES NOT Meet Task Criteria!', __FILE__, __LINE__, __METHOD__, 10);
														}
													} else {
														Debug::text(' Shift Differential... DOES NOT Meet Task Group Criteria!', __FILE__, __LINE__, __METHOD__, 10);
													}
												} else {
													Debug::text(' Shift Differential... DOES NOT Meet Job Criteria!', __FILE__, __LINE__, __METHOD__, 10);
												}
											} else {
												Debug::text(' Shift Differential... DOES NOT Meet Job Group Criteria!', __FILE__, __LINE__, __METHOD__, 10);
											}
										} else {
											Debug::text(' Shift Differential... DOES NOT Meet Department Criteria!', __FILE__, __LINE__, __METHOD__, 10);
										}
									} else {
										Debug::text(' Shift Differential... DOES NOT Meet Branch Criteria!', __FILE__, __LINE__, __METHOD__, 10);
									}

									$i++;
								}
							}
						}
						break;
				}
			}
		}

		$profiler->stopTimer( "UserDateTotal::calcPremiumPolicyTotalTime() - Part 1");

		return TRUE;
	}

	function calcAbsencePolicyTotalTime() {
		//Don't do this, because it doubles up on paid time?
		//Only issue is if we want to add these hours to weekly OT hours or anything.
		//Does it double up on paid time, as it is paid time after all?

		/*
		Debug::text(' Adding Paid Absence Policy time to Regular Time: '. $this->getUserDateID(), __FILE__, __LINE__, __METHOD__,10);
		$udtlf = new UserDateTotalListFactory();
		$udtlf->getPaidAbsenceByUserDateID( $this->getUserDateID() );
		if ( $udtlf->getRecordCount() > 0 ) {
			foreach ($udtlf as $udt_obj) {
				Debug::text(' Found some Paid Absence Policy time entries: '. $udt_obj->getTotalTime(), __FILE__, __LINE__, __METHOD__,10);
				$udtf = new UserDateTotalFactory();
				$udtf->setUserDateID( $this->getUserDateID() );
				$udtf->setStatus( 10 ); //System
				$udtf->setType( 20 ); //Regular
				$udtf->setBranch( $udt_obj->getBranch() );
				$udtf->setDepartment( $udt_obj->getDepartment() );
				$udtf->setTotalTime( $udt_obj->getTotalTime() );
				$udtf->Save();
			}

			return TRUE;
		} else {
			Debug::text(' Found zero Paid Absence Policy time entries: '. $this->getUserDateID(), __FILE__, __LINE__, __METHOD__,10);
		}

		return FALSE;
		*/

		return TRUE;
	}

	//Meal policy deduct/include time should be calculated on a percentage basis between all branches/departments/jobs/tasks
	//rounded to the nearest 60 seconds. This is the only way to keep things "fair"
	//as we can never know which individual branch/department/job/task to deduct/include the time for.
	//
	//Use the Worked Time UserTotal rows to calculate the adjustment for each worked time row.
	//Since we need this information BEFORE any compaction occurs.
	function calcUserTotalMealPolicyAdjustment( $meal_policy_time ) {
		if ( $meal_policy_time == '' OR $meal_policy_time == 0 ) {
			return array();
		}
		Debug::text('Meal Policy Time: '. $meal_policy_time, __FILE__, __LINE__, __METHOD__, 10);

		$day_total_time = 0;
		$retarr = array();

		$udtlf = new UserDateTotalListFactory();
		$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), 20 );
		if ( $udtlf->getRecordCount() > 0 ) {
			foreach( $udtlf as $udt_obj ) {
				$udt_arr[$udt_obj->getId()] = $udt_obj->getTotalTime();

				$day_total_time = bcadd($day_total_time, $udt_obj->getTotalTime() );
			}
			Debug::text('Day Total Time: '. $day_total_time, __FILE__, __LINE__, __METHOD__, 10);

			if ( is_array($udt_arr) ) {
				$remainder = 0;
				foreach( $udt_arr as $udt_id => $total_time ) {
					$udt_raw_meal_policy_time = bcmul( bcdiv( $total_time, $day_total_time ), $meal_policy_time );
					if ( $meal_policy_time > 0 ) {
						$rounded_udt_raw_meal_policy_time = floor($udt_raw_meal_policy_time);
						$remainder = bcadd( $remainder, bcsub( $udt_raw_meal_policy_time, $rounded_udt_raw_meal_policy_time ) );
					} else {
						$rounded_udt_raw_meal_policy_time = ceil($udt_raw_meal_policy_time);
						$remainder = bcadd( $remainder, bcsub( $udt_raw_meal_policy_time, $rounded_udt_raw_meal_policy_time ) );
					}
					$retarr[$udt_id] = (int)$rounded_udt_raw_meal_policy_time;

					Debug::text('UserDateTotal Row ID: '. $udt_id .' Raw Meal Policy Time: '. $udt_raw_meal_policy_time .'('. $rounded_udt_raw_meal_policy_time .') Remainder: '. $remainder, __FILE__, __LINE__, __METHOD__, 10);
				}

				//Add remainder rounded to the nearest second to the last row.
				if ( $meal_policy_time > 0 ) {
					$remainder = ceil( $remainder );
				} else {
					$remainder = floor( $remainder );
				}
				$retarr[$udt_id] = (int)bcadd($retarr[$udt_id], $remainder);
			}
		} else {
			Debug::text('No UserDateTotal rows...', __FILE__, __LINE__, __METHOD__, 10);
		}

		return $retarr;
	}

	function calcMealPolicyTotalTime( $meal_policy_obj = NULL ) {
		//Debug::arr($meal_policy_obj, 'MealPolicyObject param:', __FILE__, __LINE__, __METHOD__, 10);

		//Get total worked time for the day.
		$udtlf = new UserDateTotalListFactory();
		$daily_total_time = $udtlf->getWorkedTimeSumByUserDateID( $this->getUserDateID() );

		if ( is_object( $meal_policy_obj ) == FALSE ) {
			//Lookup meal policy
			$mplf = new MealPolicyListFactory();
			//$mplf->getByPolicyGroupUserId( $this->getUserDateObject()->getUser() );
			$mplf->getByPolicyGroupUserIdAndDayTotalTime( $this->getUserDateObject()->getUser(), $daily_total_time );
			if ( $mplf->getRecordCount() > 0 ) {
				Debug::text('Found Meal Policy to apply.', __FILE__, __LINE__, __METHOD__, 10);
				$meal_policy_obj = $mplf->getCurrent();
			}
		}

		$meal_policy_time = 0;

		if ( is_object( $meal_policy_obj ) AND $daily_total_time >= $meal_policy_obj->getTriggerTime() ) {
			Debug::text('Meal Policy ID: '. $meal_policy_obj->getId() .' Type ID: '. $meal_policy_obj->getType() .' Amount: '. $meal_policy_obj->getAmount() .' Daily Total TIme: '. $daily_total_time, __FILE__, __LINE__, __METHOD__, 10);

			//Get lunch total time.
			$lunch_total_time = 0;

			$plf = new PunchListFactory();
			$plf->getByUserDateIdAndTypeId( $this->getUserDateId(), 20 ); //Only Lunch punches
			if ( $plf->getRecordCount() > 0 ) {
				$pair = 0;
				$x = 0;
				$out_for_lunch = FALSE;
				foreach ( $plf as $p_obj ) {
					if ( $p_obj->getStatus() == 20 AND $p_obj->getType() == 20 ) {
						$lunch_out_timestamp = $p_obj->getTimeStamp();
						$out_for_lunch = TRUE;
					} elseif ( $out_for_lunch == TRUE AND $p_obj->getStatus() == 10 AND $p_obj->getType() == 20) {
						$lunch_punch_arr[$pair][20] = $lunch_out_timestamp;
						$lunch_punch_arr[$pair][10] = $p_obj->getTimeStamp();
						$out_for_lunch = FALSE;
						$pair++;
						unset($lunch_out_timestamp);
					} else {
						$out_for_lunch = FALSE;
					}

					$x++;
				}

				if ( isset($lunch_punch_arr) ) {
					foreach( $lunch_punch_arr as $punch_control_id => $time_stamp_arr ) {
						if ( isset($time_stamp_arr[10]) AND isset($time_stamp_arr[20]) ) {
							$lunch_total_time = bcadd($lunch_total_time, bcsub($time_stamp_arr[10], $time_stamp_arr[20] ) );
						} else {
							Debug::text(' Lunch Punches not paired... Skipping!', __FILE__, __LINE__, __METHOD__, 10);
						}
					}
				} else {
					Debug::text(' No Lunch Punches found, or none are paired.', __FILE__, __LINE__, __METHOD__, 10);
				}
			}

			Debug::text(' Lunch Total Time: '. $lunch_total_time, __FILE__, __LINE__, __METHOD__, 10);
			switch ( $meal_policy_obj->getType() ) {
				case 10: //Auto-Deduct
					Debug::text(' Lunch AutoDeduct.', __FILE__, __LINE__, __METHOD__, 10);
					if ( $meal_policy_obj->getIncludeLunchPunchTime() == TRUE ) {
						$meal_policy_time = bcsub( $meal_policy_obj->getAmount(), $lunch_total_time )*-1;
						//If they take more then their alloted lunch, zero it out so time isn't added.
						if ( $meal_policy_time > 0 ) {
							$meal_policy_time = 0;
						}
					} else {
						$meal_policy_time = $meal_policy_obj->getAmount()*-1;
					}
					break;
				case 15: //Auto-Include
					Debug::text(' Lunch AutoInclude.', __FILE__, __LINE__, __METHOD__, 10);
					if ( $meal_policy_obj->getIncludeLunchPunchTime() == TRUE ) {
						if ( $lunch_total_time > $meal_policy_obj->getAmount() ) {
							$meal_policy_time = $meal_policy_obj->getAmount();
						} else {
							$meal_policy_time = $lunch_total_time;
						}
					} else {
						$meal_policy_time = $meal_policy_obj->getAmount();
					}
					break;
			}

			Debug::text(' Meal Policy Total Time: '. $meal_policy_time, __FILE__, __LINE__, __METHOD__, 10);

			if ( $meal_policy_time != 0 ) {
				$udtf = new UserDateTotalFactory();
				$udtf->setUserDateID( $this->getUserDateID() );
				$udtf->setStatus( 10 ); //System
				$udtf->setType( 100 ); //Lunch
				$udtf->setMealPolicyId( $meal_policy_obj->getId() );
				$udtf->setBranch( $this->getUserDateObject()->getUserObject()->getDefaultBranch() );
				$udtf->setDepartment( $this->getUserDateObject()->getUserObject()->getDefaultDepartment() );

				$udtf->setTotalTime( $meal_policy_time );
				$udtf->setEnableCalcSystemTotalTime(FALSE);
				if ( $udtf->isValid() == TRUE ) {
					$udtf->Save();
				}
				unset($udtf);
			}
		} else {
			Debug::text(' No Meal Policy found, or not after meal policy trigger time yet...', __FILE__, __LINE__, __METHOD__, 10);
		}

		return $meal_policy_time;
	}


	//Break policy deduct/include time should be calculated on a percentage basis between all branches/departments/jobs/tasks
	//rounded to the nearest 60 seconds. This is the only way to keep things "fair"
	//as we can never know which individual branch/department/job/task to deduct/include the time for.
	//
	//Use the Worked Time UserTotal rows to calculate the adjustment for each worked time row.
	//Since we need this information BEFORE any compaction occurs.
	function calcUserTotalBreakPolicyAdjustment( $break_policy_time ) {
		if ( $break_policy_time == '' OR $break_policy_time == 0 ) {
			return array();
		}
		Debug::text('Break Policy Time: '. $break_policy_time, __FILE__, __LINE__, __METHOD__, 10);

		$day_total_time = 0;
		$retarr = array();

		$udtlf = new UserDateTotalListFactory();
		$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), 20 );
		if ( $udtlf->getRecordCount() > 0 ) {
			foreach( $udtlf as $udt_obj ) {
				$udt_arr[$udt_obj->getId()] = $udt_obj->getTotalTime();

				$day_total_time = bcadd($day_total_time, $udt_obj->getTotalTime() );
			}
			Debug::text('Day Total Time: '. $day_total_time, __FILE__, __LINE__, __METHOD__, 10);

			if ( is_array($udt_arr) ) {
				$remainder = 0;
				foreach( $udt_arr as $udt_id => $total_time ) {
					$udt_raw_break_policy_time = bcmul( bcdiv( $total_time, $day_total_time ), $break_policy_time );
					if ( $break_policy_time > 0 ) {
						$rounded_udt_raw_break_policy_time = floor($udt_raw_break_policy_time);
						$remainder = bcadd( $remainder, bcsub( $udt_raw_break_policy_time, $rounded_udt_raw_break_policy_time ) );
					} else {
						$rounded_udt_raw_break_policy_time = ceil($udt_raw_break_policy_time);
						$remainder = bcadd( $remainder, bcsub( $udt_raw_break_policy_time, $rounded_udt_raw_break_policy_time ) );
					}
					$retarr[$udt_id] = (int)$rounded_udt_raw_break_policy_time;

					Debug::text('UserDateTotal Row ID: '. $udt_id .' Raw Break Policy Time: '. $udt_raw_break_policy_time .'('. $rounded_udt_raw_break_policy_time .') Remainder: '. $remainder, __FILE__, __LINE__, __METHOD__, 10);
				}

				//Add remainder rounded to the nearest second to the last row.
				if ( $break_policy_time > 0 ) {
					$remainder = ceil( $remainder );
				} else {
					$remainder = floor( $remainder );
				}
				$retarr[$udt_id] = (int)bcadd($retarr[$udt_id], $remainder);
			}
		} else {
			Debug::text('No UserDateTotal rows...', __FILE__, __LINE__, __METHOD__, 10);
		}

		return $retarr;
	}

	function calcBreakPolicyTotalTime( $break_policy_ids = NULL ) {
		//Debug::arr($meal_policy_obj, 'MealPolicyObject param:', __FILE__, __LINE__, __METHOD__, 10);

		//Get total worked time for the day.
		$udtlf = new UserDateTotalListFactory();
		$daily_total_time = $udtlf->getWorkedTimeSumByUserDateID( $this->getUserDateID() );
		Debug::text('Daily Total Time: '. $daily_total_time, __FILE__, __LINE__, __METHOD__, 10);

		$bplf = new BreakPolicyListFactory();
		if ( is_array($break_policy_ids) ) {
			$bplf->getByIdAndCompanyId( $break_policy_ids, $this->getUserDateObject()->getUserObject()->getCompany() );
		} else {
			//Lookup break policy
			$bplf->getByPolicyGroupUserIdAndDayTotalTime( $this->getUserDateObject()->getUser(), $daily_total_time );
		}

		$break_policy_total_time = 0;

		if ( $bplf->getRecordCount() > 0 ) {
			Debug::text('Found Break Policy(ies) to apply: '. $bplf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);

			$break_total_time = array();
			$break_overall_total_time = 0;

			$plf = new PunchListFactory();
			$plf->getByUserDateIdAndTypeId( $this->getUserDateId(), 30 ); //Only Break punches
			if ( $plf->getRecordCount() > 0 ) {
				$pair = 0;
				$x = 0;
				$out_for_break = FALSE;
				foreach ( $plf as $p_obj ) {
					if ( $p_obj->getStatus() == 20 AND $p_obj->getType() == 30 ) {
						$break_out_timestamp = $p_obj->getTimeStamp();
						$out_for_break = TRUE;
					} elseif ( $out_for_break == TRUE AND $p_obj->getStatus() == 10 AND $p_obj->getType() == 30) {
						$break_punch_arr[$pair][20] = $break_out_timestamp;
						$break_punch_arr[$pair][10] = $p_obj->getTimeStamp();
						$out_for_break = FALSE;
						$pair++;
						unset($break_out_timestamp);
					} else {
						$out_for_break = FALSE;
					}

					$x++;
				}

				if ( isset($break_punch_arr) ) {
					foreach( $break_punch_arr as $punch_control_id => $time_stamp_arr ) {
						if ( isset($time_stamp_arr[10]) AND isset($time_stamp_arr[20]) ) {
							$break_overall_total_time = bcadd($break_overall_total_time, bcsub($time_stamp_arr[10], $time_stamp_arr[20] ) );
							$break_total_time[] = bcsub($time_stamp_arr[10], $time_stamp_arr[20] );
						} else {
							Debug::text(' Break Punches not paired... Skipping!', __FILE__, __LINE__, __METHOD__, 10);
						}
					}
				} else {
					Debug::text(' No Break Punches found, or none are paired.', __FILE__, __LINE__, __METHOD__, 10);
				}
			}
			//Debug::Arr($break_punch_arr, ' Break Punch Arr: ', __FILE__, __LINE__, __METHOD__, 10);
			//Debug::Arr($break_total_time, ' Break Total Time Arr: ', __FILE__, __LINE__, __METHOD__, 10);

			Debug::text(' Break Overall Total Time: '. $break_overall_total_time, __FILE__, __LINE__, __METHOD__, 10);

			$i = 0;
			foreach( $bplf as $break_policy_obj ) {
				Debug::text('Break Policy ID: '. $break_policy_obj->getId() .' Type ID: '. $break_policy_obj->getType() .' Amount: '. $break_policy_obj->getAmount() .' Daily Total Time: '. $daily_total_time, __FILE__, __LINE__, __METHOD__, 10);
/*
				//If we skip this, then we can't force a break auto-deduct even when the employee doesn't punch in/out for breaks.
				//However the opposite is true if we want to auto-add break time only when the employee does take a break, this will always add it.
				if ( !isset( $break_total_time[$i] ) ) {
					Debug::text(' No Break Total Time for this break policy...Skipping...: ', __FILE__, __LINE__, __METHOD__, 10);
					continue;
				}
*/
				$break_policy_time = 0;

				switch ( $break_policy_obj->getType() ) {
					case 10: //Auto-Deduct
						Debug::text(' Break AutoDeduct.', __FILE__, __LINE__, __METHOD__, 10);
						if ( $break_policy_obj->getIncludeBreakPunchTime() == TRUE ) {
							$break_policy_time = bcsub( $break_policy_obj->getAmount(), $break_total_time[$i] )*-1;
							//If they take more then their alloted break, zero it out so time isn't added.
							if ( $break_policy_time > 0 ) {
								$break_policy_time = 0;
							}
						} else {
							$break_policy_time = $break_policy_obj->getAmount()*-1;
						}
						break;
					case 15: //Auto-Include
						Debug::text(' Break AutoInclude... Break Total Time: '. $break_total_time[$i] .' Break Policy Amount: '. $break_policy_obj->getAmount(), __FILE__, __LINE__, __METHOD__, 10);
						if ( $break_policy_obj->getIncludeBreakPunchTime() == TRUE ) {
							if ( $break_total_time[$i] > $break_policy_obj->getAmount() ) {
								$break_policy_time = $break_policy_obj->getAmount();
							} else {
								$break_policy_time = $break_total_time[$i];
							}
						} else {
							$break_policy_time = $break_policy_obj->getAmount();
						}
						break;
				}

				Debug::text(' Break Policy Total Time: '. $break_policy_time .' Break Policy ID: '. $break_policy_obj->getId(), __FILE__, __LINE__, __METHOD__, 10);

				if ( $break_policy_time != 0 ) {
					$break_policy_total_time = bcadd( $break_policy_total_time, $break_policy_time );

					$udtf = new UserDateTotalFactory();
					$udtf->setUserDateID( $this->getUserDateID() );
					$udtf->setStatus( 10 ); //System
					$udtf->setType( 110 ); //Break
					$udtf->setBreakPolicyId( $break_policy_obj->getId() );
					$udtf->setBranch( $this->getUserDateObject()->getUserObject()->getDefaultBranch() );
					$udtf->setDepartment( $this->getUserDateObject()->getUserObject()->getDefaultDepartment() );

					$udtf->setTotalTime( $break_policy_time );
					$udtf->setEnableCalcSystemTotalTime(FALSE);
					if ( $udtf->isValid() == TRUE ) {
						$udtf->Save();
					}
					unset($udtf);
				}

				$i++;
			}
		} else {
			Debug::text(' No Break Policy found, or not after break policy trigger time yet...', __FILE__, __LINE__, __METHOD__, 10);
		}

		Debug::text(' Final Break Policy Total Time: '. $break_policy_total_time, __FILE__, __LINE__, __METHOD__, 10);

		return $break_policy_total_time;
	}

	function calcAccrualPolicy() {
		//FIXME: There is a minor bug for hour based accruals that if a milestone has a maximum limit,
		//  and an employee recalculates there timesheet, and the limit is reached midweek, if its recalculated
		//  again, the days that get the accrual time won't always be in order because the accrual balance is deleted
		//  only for the day currently being calculated, so on Monday it will delete 1hr of accrual, but the balance will
		//  still include Tue,Wed,Thu and the limit may already be reached.

		//We still need to calculate accruals even if the total time is 0, because we may want to override a
		//policy to 0hrs, and if we skip entries with TotalTime() == 0, the accruals won't be updated.
		if ( $this->getDeleted() == FALSE ) {
			Debug::text('Calculating Accrual Policies... Total Time: '. $this->getTotalTime() .' Date: '. TTDate::getDate('DATE', $this->getUserDateObject()->getDateStamp() ), __FILE__, __LINE__, __METHOD__, 10);

			//Calculate accrual policies assigned to other overtime/premium/absence policies
			//Debug::text('ID: '. $this->getId() .' Overtime Policy ID: '. (int)$this->getOverTimePolicyID()  .' Premium Policy ID: '. (int)$this->getPremiumPolicyID() .' Absence Policy ID: '. (int)$this->getAbsencePolicyID(), __FILE__, __LINE__, __METHOD__, 10);

			//If overtime, premium or absence policy is an accrual, handle that now.
			if ( $this->getOverTimePolicyID() != FALSE ) {
				$accrual_policy_id = $this->getOverTimePolicyObject()->getAccrualPolicyID();
				Debug::text('Over Time Accrual Policy ID: '. $accrual_policy_id, __FILE__, __LINE__, __METHOD__, 10);

				if ( $accrual_policy_id > 0 ) {
					Debug::text('Over Time Accrual Rate: '. $this->getOverTimePolicyObject()->getAccrualRate() .' Policy ID: '. $this->getOverTimePolicyObject()->getAccrualPolicyID() , __FILE__, __LINE__, __METHOD__, 10);
					$af = new AccrualFactory();
					$af->setUser( $this->getUserDateObject()->getUser() );
					$af->setAccrualPolicyID( $accrual_policy_id );
					$af->setUserDateTotalID( $this->getID() );

					$accrual_amount = bcmul( $this->getTotalTime(), $this->getOverTimePolicyObject()->getAccrualRate() );
					if ( $accrual_amount > 0 ) {
						$af->setType(10); //Banked
					} else {
						$af->setType(20); //Used
					}
					$af->setAmount( $accrual_amount );
					$af->setEnableCalcBalance(TRUE);
					if ( $af->isValid() ) {
						$af->Save();
					}

					unset($accrual_amount);
				} else {
					Debug::text('Skipping Over Time Accrual Policy ID: '. $accrual_policy_id, __FILE__, __LINE__, __METHOD__, 10);
				}
			}
			if ( $this->getPremiumPolicyID() != FALSE ) {
				$accrual_policy_id = $this->getPremiumPolicyObject()->getAccrualPolicyID();
				Debug::text('Premium Accrual Policy ID: '. $accrual_policy_id, __FILE__, __LINE__, __METHOD__, 10);

				if ( $accrual_policy_id > 0 ) {
					$af = new AccrualFactory();
					$af->setUser( $this->getUserDateObject()->getUser() );
					$af->setAccrualPolicyID( $accrual_policy_id );
					$af->setUserDateTotalID( $this->getID() );

					$accrual_amount = bcmul( $this->getTotalTime(), $this->getPremiumPolicyObject()->getAccrualRate() );
					if ( $accrual_amount > 0 ) {
						$af->setType(10); //Banked
					} else {
						$af->setType(20); //Used
					}
					$af->setAmount( $accrual_amount );
					$af->setEnableCalcBalance(TRUE);
					if ( $af->isValid() ) {
						$af->Save();
					}

					unset($accrual_amount);
				}
			}
			if ( $this->getAbsencePolicyID() != FALSE ) {
				$accrual_policy_id = $this->getAbsencePolicyObject()->getAccrualPolicyID();
				Debug::text('Absence Accrual Policy ID: '. $accrual_policy_id, __FILE__, __LINE__, __METHOD__, 10);

				if ( $accrual_policy_id > 0 ) {
					$af = new AccrualFactory();
					$af->setUser( $this->getUserDateObject()->getUser() );
					$af->setAccrualPolicyID( $accrual_policy_id );
					$af->setUserDateTotalID( $this->getID() );

					//By default we withdraw from accrual policy, so if there is a negative rate, deposit instead.
					$accrual_amount = bcmul( $this->getTotalTime(), bcmul( $this->getAbsencePolicyObject()->getAccrualRate(), -1 ) );
					if ( $accrual_amount > 0 ) {
						$af->setType(10); //Banked
					} else {
						$af->setType(20); //Used
					}
					$af->setAmount( $accrual_amount );

					$af->setEnableCalcBalance(TRUE);
					if ( $af->isValid() ) {
						$af->Save();
					}
				}
			}
			unset($af, $accrual_policy_id);


			//Calculate any hour based accrual policies.
			//if ( $this->getType() == 10 AND $this->getStatus() == 10 ) {
			if ( $this->getStatus() == 10 AND in_array( $this->getType(), array(20,30) ) ) { //Calculate hour based accruals on regular/overtime only.
				$aplf = new AccrualPolicyListFactory();
				$aplf->getByPolicyGroupUserIdAndType( $this->getUserDateObject()->getUser(), 30 );
				if ( $aplf->getRecordCount() > 0 ) {
					Debug::text('Found Hour Based Accrual Policies to apply.', __FILE__, __LINE__, __METHOD__, 10);
					foreach( $aplf as $ap_obj  ) {
						if ( $ap_obj->getMinimumEmployedDays() == 0
								OR TTDate::getDays( ($this->getUserDateObject()->getDateStamp()-$this->getUserDateObject()->getUserObject()->getHireDate()) ) >= $ap_obj->getMinimumEmployedDays() ) {
							Debug::Text('&nbsp;&nbsp;User has been employed long enough.', __FILE__, __LINE__, __METHOD__,10);

							$milestone_obj = $ap_obj->getActiveMilestoneObject( $this->getUserDateObject()->getUserObject(), $this->getUserDateObject()->getDateStamp() );
							$accrual_balance = $ap_obj->getCurrentAccrualBalance( $this->getUserDateObject()->getUserObject()->getId(), $ap_obj->getId() );

							//If Maximum time is set to 0, make that unlimited.
							if ( is_object($milestone_obj) AND ( $milestone_obj->getMaximumTime() == 0 OR $accrual_balance < $milestone_obj->getMaximumTime() ) ) {
								$accrual_amount = $ap_obj->calcAccrualAmount( $milestone_obj, $this->getTotalTime(), 0);

								if ( $accrual_amount > 0 ) {
									$new_accrual_balance = bcadd( $accrual_balance, $accrual_amount);

									//If Maximum time is set to 0, make that unlimited.
									if ( $milestone_obj->getMaximumTime() > 0 AND $new_accrual_balance > $milestone_obj->getMaximumTime() ) {
										$accrual_amount = bcsub( $milestone_obj->getMaximumTime(), $accrual_balance, 4 );
									}
									Debug::Text('&nbsp;&nbsp; Min/Max Adjusted Accrual Amount: '. $accrual_amount .' Limits: Min: '. $milestone_obj->getMinimumTime() .' Max: '. $milestone_obj->getMaximumTime(), __FILE__, __LINE__, __METHOD__,10);

									$af = new AccrualFactory();
									$af->setUser( $this->getUserDateObject()->getUserObject()->getId() );
									$af->setType( 75 ); //Accrual Policy
									$af->setAccrualPolicyID( $ap_obj->getId() );
									$af->setUserDateTotalID( $this->getID() );
									$af->setAmount( $accrual_amount );
									$af->setTimeStamp( $this->getUserDateObject()->getDateStamp() );
									$af->setEnableCalcBalance( TRUE );

									if ( $af->isValid() ) {
										$af->Save();
									}
									unset($accrual_amount, $accrual_balance, $new_accrual_balance);
								} else {
									Debug::Text('&nbsp;&nbsp; Accrual Amount is 0...', __FILE__, __LINE__, __METHOD__,10);
								}
							} else {
								Debug::Text('&nbsp;&nbsp; Accrual Balance is outside Milestone Range. Or no milestone found. Skipping...', __FILE__, __LINE__, __METHOD__,10);

							}
						} else {
							Debug::Text('&nbsp;&nbsp;User has only been employed: '. TTDate::getDays( ($this->getUserDateObject()->getDateStamp()-$this->getUserDateObject()->getUserObject()->getHireDate()) ) .' Days, not enough.', __FILE__, __LINE__, __METHOD__,10);
						}
					}
				} else {
					Debug::text('No Hour Based Accrual Policies to apply.', __FILE__, __LINE__, __METHOD__, 10);
				}
			} else {
				Debug::text('No worked time on this day or not proper type/status, skipping hour based accrual policies...', __FILE__, __LINE__, __METHOD__, 10);
			}
		}

		/*
		//FIXME: Figure a better way to re-calculate accrual policies assigned to absences to update accrual balances
		if ( $this->getEnableCalcAccrualPolicy() == TRUE ) {
			Debug::text('Recalculating Accruals assigned to absence policies...', __FILE__, __LINE__, __METHOD__, 10);

			$udtlf = new UserDateTotalListFactory();
			$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), 30 ); //Absences only
			if ( $udtlf->getRecordCount() > 0 ) {
				foreach( $udtlf as $udt_obj ) {
					$accrual_policy_id = $udt_obj->getAbsencePolicyObject()->getAccrualPolicyID();
					Debug::text('Absence Accrual Policy ID: '. $accrual_policy_id, __FILE__, __LINE__, __METHOD__, 10);

					if ( $accrual_policy_id > 0 AND $this->getTotalTime() > 0 ) {
						$af = new AccrualFactory();
						$af->setUser( $this->getUserDateObject()->getUser() );
						$af->setAccrualPolicyID( $accrual_policy_id );
						$af->setType(20);
						$af->setUserDateTotalID( $udt_obj->getID() );
						$af->setAmount( bcmul( $udt_obj->getTotalTime(), -1 ) );
						$af->setEnableCalcBalance(TRUE);
						if ( $af->isValid() ) {
							$af->Save();
						}
					}
				}
			}
		} else {
			Debug::text('NOT Recalculating Accruals assigned to absence policies...', __FILE__, __LINE__, __METHOD__, 10);
		}
		*/

		return TRUE;
	}

	function calcSystemTotalTime() {
		global $profiler;

		$profiler->startTimer( "UserDateTotal::calcSystemTotalTime() - Part 1");

		if ( is_object( $this->getUserDateObject() )
				AND is_object( $this->getUserDateObject()->getPayPeriodObject() )
				AND $this->getUserDateObject()->getPayPeriodObject()->getStatus() == 20 ) {
			Debug::text(' Pay Period is closed!', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		//Take the worked hours, and calculate Total,Regular,Overtime,Premium hours from that.
		//This is where many of the policies will be applied
		//Such as any meal/overtime/premium policies.
		$return_value = FALSE;

		$udtlf = new UserDateTotalListFactory();

		$this->deleteSystemTotalTime();

		//We can't assign a dock absence to a given branch/dept automatically,
		//Because several punches with different branches could fall within a schedule punch pair.
		//Just total up entire day, and entire scheduled time to see if we're over/under
		//FIXME: Handle multiple schedules on a single day better.
		$schedule_total_time = 0;
		$meal_policy_obj = NULL;
		$slf = new ScheduleListFactory();

		$profiler->startTimer( "UserDateTotal::calcSystemTotalTime() - Holiday");
		//Check for Holidays
		$holiday_time = 0;
		$hlf = new HolidayListFactory();
		$hlf->getByPolicyGroupUserIdAndDate( $this->getUserDateObject()->getUser(), $this->getUserDateObject()->getDateStamp() );
		if ( $hlf->getRecordCount() > 0 ) {
			$holiday_obj = $hlf->getCurrent();
			Debug::text(' Found Holiday: '. $holiday_obj->getName(), __FILE__, __LINE__, __METHOD__,10);

			if ( $holiday_obj->isEligible( $this->getUserDateObject()->getUser() ) ) {
				Debug::text(' User is Eligible for Holiday: '. $holiday_obj->getName(), __FILE__, __LINE__, __METHOD__,10);

				$holiday_time = $holiday_obj->getHolidayTime( $this->getUserDateObject()->getUser() );
				Debug::text(' User average time for Holiday: '. TTDate::getHours($holiday_time), __FILE__, __LINE__, __METHOD__,10);

				if ( $holiday_time > 0 AND $holiday_obj->getHolidayPolicyObject()->getAbsencePolicyID() != FALSE ) {
					Debug::text(' Adding Holiday hours: '. TTDate::getHours($holiday_time), __FILE__, __LINE__, __METHOD__,10);
					$udtf = new UserDateTotalFactory();
					$udtf->setUserDateID( $this->getUserDateID() );
					$udtf->setStatus( 30 ); //Absence
					$udtf->setType( 10 ); //Total
					$udtf->setBranch( $this->getUserDateObject()->getUserObject()->getDefaultBranch() );
					$udtf->setDepartment( $this->getUserDateObject()->getUserObject()->getDefaultDepartment() );
					$udtf->setAbsencePolicyID( $holiday_obj->getHolidayPolicyObject()->getAbsencePolicyID() );
					$udtf->setTotalTime( $holiday_time );
					$udtf->setEnableCalcSystemTotalTime(FALSE);
					if ( $udtf->isValid() ) {
						$udtf->Save();
					}
				}
			}

			$slf->getByUserDateIdAndStatusId( $this->getUserDateID(), 20 );
			$schedule_absence_total_time = 0;
			if ( $slf->getRecordCount() > 0 ) {
				//Check for schedule policy
				foreach ( $slf as $s_obj ) {
					Debug::text(' Schedule Absence Total Time: '. $s_obj->getTotalTime(), __FILE__, __LINE__, __METHOD__,10);

					$schedule_absence_total_time += $s_obj->getTotalTime();
					if ( is_object($s_obj->getSchedulePolicyObject() ) AND $s_obj->getSchedulePolicyObject()->getAbsencePolicyID() > 0 ) {
						$holiday_absence_policy_id = $s_obj->getSchedulePolicyObject()->getAbsencePolicyID();
						Debug::text(' Found Absence Policy for docking: '. $holiday_absence_policy_id, __FILE__, __LINE__, __METHOD__,10);
					} else {
						Debug::text(' NO Absence Policy : ', __FILE__, __LINE__, __METHOD__,10);
					}
				}
			}

			$holiday_total_under_time = $schedule_absence_total_time - $holiday_time;
			if ( isset($holiday_absence_policy_id) AND $holiday_total_under_time > 0 ) {
				Debug::text(' Schedule Under Time Case: '. $holiday_total_under_time, __FILE__, __LINE__, __METHOD__,10);
				$udtf = new UserDateTotalFactory();
				$udtf->setUserDateID( $this->getUserDateID() );
				$udtf->setStatus( 30 ); //Absence
				$udtf->setType( 10 ); //Total
				$udtf->setBranch( $this->getUserDateObject()->getUserObject()->getDefaultBranch() );
				$udtf->setDepartment( $this->getUserDateObject()->getUserObject()->getDefaultDepartment() );
				$udtf->setAbsencePolicyID( $holiday_absence_policy_id );
				$udtf->setTotalTime( $holiday_total_under_time );
				$udtf->setEnableCalcSystemTotalTime(FALSE);
				if ( $udtf->isValid() ) {
					$udtf->Save();
				}
			}
			unset($holiday_total_under_time, $holiday_absence_policy_id, $schedule_absence_total_time);
		}
		$profiler->stopTimer( "UserDateTotal::calcSystemTotalTime() - Holiday");

		//Do this after holiday policies have been applied, so if someone
		//schedules a holiday manually, we don't double up on the time.
		$slf->getByUserDateId( $this->getUserDateID() );
		if ( $slf->getRecordCount() > 0 ) {
			//Check for schedule policy
			foreach ( $slf as $s_obj ) {
				Debug::text(' Schedule Total Time: '. $s_obj->getTotalTime(), __FILE__, __LINE__, __METHOD__,10);
				if ( $s_obj->getStatus() == 20 AND $s_obj->getAbsencePolicyID() != '' ) {
					Debug::text(' Scheduled Absence Found of Total Time: '. $s_obj->getTotalTime(), __FILE__, __LINE__, __METHOD__,10);

					//If a holiday policy is applied on this day, ignore the schedule so we don't duplicate it.
					//We could take the difference, and use the greatest of the two,
					//But I think that will just open the door for errors.
					if ( !isset($holiday_obj) OR ( $holiday_time == 0 AND is_object($holiday_obj) AND $holiday_obj->getHolidayPolicyObject()->getAbsencePolicyID() != $s_obj->getAbsencePolicyID() ) ) {
						$udtf = new UserDateTotalFactory();
						$udtf->setUserDateID( $this->getUserDateID() );
						$udtf->setStatus( 30 ); //Absence
						$udtf->setType( 10 ); //Total
						$udtf->setBranch( $s_obj->getBranch() );
						$udtf->setDepartment( $s_obj->getDepartment() );
						$udtf->setJob( $s_obj->getJob() );
						$udtf->setJobItem( $s_obj->getJobItem() );
						$udtf->setAbsencePolicyID( $s_obj->getAbsencePolicyID() );
						$udtf->setTotalTime( $s_obj->getTotalTime() );
						$udtf->setEnableCalcSystemTotalTime(FALSE);
						if ( $udtf->isValid() ) {
							$udtf->Save();
						}
					} else {
						Debug::text(' Holiday Time Found, ignoring schedule!', __FILE__, __LINE__, __METHOD__,10);
					}
				} elseif ( $s_obj->getStatus() == 10 ) {
					$schedule_total_time += $s_obj->getTotalTime();
					if ( is_object($s_obj->getSchedulePolicyObject() ) ) {
						$schedule_absence_policy_id = $s_obj->getSchedulePolicyObject()->getAbsencePolicyID();
						$meal_policy_obj = $s_obj->getSchedulePolicyObject()->getMealPolicyObject();
						Debug::text(' Found Absence Policy for docking: '. $schedule_absence_policy_id, __FILE__, __LINE__, __METHOD__,10);
					} else {
						Debug::text(' NO Absence Policy : ', __FILE__, __LINE__, __METHOD__,10);
					}
				}
			}
		} else {
			Debug::text(' No Schedules found. ', __FILE__, __LINE__, __METHOD__,10);
		}
		unset($s_obj);
		unset($holiday_time, $holiday_obj);

		//Handle Meal Policy time.
		//Do this after schedule meal policies have been looked up, as those override any policy group meal policies.
		$meal_policy_time = $this->calcMealPolicyTotalTime( $meal_policy_obj );
		$udt_meal_policy_adjustment_arr = $this->calcUserTotalMealPolicyAdjustment( $meal_policy_time );
		//Debug::Arr($udt_meal_policy_adjustment_arr, 'UserDateTotal Meal Policy Adjustment: ', __FILE__, __LINE__, __METHOD__,10);

		$break_policy_time = $this->calcBreakPolicyTotalTime();
		$udt_break_policy_adjustment_arr = $this->calcUserTotalBreakPolicyAdjustment( $break_policy_time );
		//Debug::Arr($udt_break_policy_adjustment_arr, 'UserDateTotal Break Policy Adjustment: ', __FILE__, __LINE__, __METHOD__,10);

		$daily_total_time = $this->getDailyTotalTime();
		Debug::text(' Daily Total Time: '. $daily_total_time .' Schedule Total Time: '. $schedule_total_time, __FILE__, __LINE__, __METHOD__,10);

		//Check for overtime policies or undertime absence policies
		if ( $daily_total_time > $schedule_total_time ) {
			Debug::text(' Schedule Over Time Case: ', __FILE__, __LINE__, __METHOD__,10);
		} elseif ( isset($schedule_absence_policy_id) AND $schedule_absence_policy_id != '' AND $daily_total_time < $schedule_total_time ) {
			$total_under_time = bcsub($schedule_total_time, $daily_total_time);

			if ( $total_under_time > 0 ) {
				Debug::text(' Schedule Under Time Case: '. $total_under_time .' Absence Policy ID: '. $schedule_absence_policy_id, __FILE__, __LINE__, __METHOD__,10);
				$udtf = new UserDateTotalFactory();
				$udtf->setUserDateID( $this->getUserDateID() );
				$udtf->setStatus( 30 ); //Absence
				$udtf->setType( 10 ); //Total
				$udtf->setBranch( $this->getUserDateObject()->getUserObject()->getDefaultBranch() );
				$udtf->setDepartment( $this->getUserDateObject()->getUserObject()->getDefaultDepartment() );
				$udtf->setAbsencePolicyID( $schedule_absence_policy_id );
				$udtf->setTotalTime( $total_under_time );
				$udtf->setEnableCalcSystemTotalTime(FALSE);
				if ( $udtf->isValid() ) {
					$udtf->Save();
				}
			} else {
				Debug::text(' Schedule Under Time is a negative value, skipping dock time: '. $total_under_time .' Absence Policy ID: '. $schedule_absence_policy_id, __FILE__, __LINE__, __METHOD__,10);
			}
		} else {
			Debug::text(' No Dock Absenses', __FILE__, __LINE__, __METHOD__,10);
		}
		unset($schedule_absence_policy_id);

		//Do this AFTER the UnderTime absence policy is submitted.
		$recalc_daily_total_time = $this->calcAbsencePolicyTotalTime();

		if ( $recalc_daily_total_time == TRUE ) {
			//Total up all "worked" hours for the day again, this time include
			//Paid Absences.
			$daily_total_time = $this->getDailyTotalTime();
			//$daily_total_time = $udtlf->getTotalSumByUserDateID( $this->getUserDateID() );
			Debug::text('ReCalc Daily Total Time for Day: '. $daily_total_time, __FILE__, __LINE__, __METHOD__, 10);
		}

		$profiler->stopTimer( "UserDateTotal::calcSystemTotalTime() - Part 1");

		$user_data_total_compact_arr = $this->calcOverTimePolicyTotalTime( $udt_meal_policy_adjustment_arr, $udt_break_policy_adjustment_arr );
		//Debug::Arr($user_data_total_compact_arr, 'User Data Total Compact Array: ', __FILE__, __LINE__, __METHOD__, 10);

		//Insert User Date Total rows for each compacted array entry.
		//The reason for compacting is to reduce the amount of rows as much as possible.
		if ( is_array($user_data_total_compact_arr) ) {
			$profiler->startTimer( "UserDateTotal::calcSystemTotalTime() - Part 2");

			Debug::text('Compact Array Exists: ', __FILE__, __LINE__, __METHOD__, 10);
			foreach($user_data_total_compact_arr as $type_id => $udt_arr ) {
				Debug::text('Compact Array Entry: Type ID: '. $type_id, __FILE__, __LINE__, __METHOD__, 10);

				if ( $type_id == 20 ) {
					//Regular Time
					//Debug::text('Compact Array Entry: Branch ID: '. $udt_arr[' , __FILE__, __LINE__, __METHOD__, 10);
					foreach($udt_arr as $branch_id => $branch_arr ) {
						//foreach($branch_arr as $department_id => $total_time ) {
						foreach($branch_arr as $department_id => $department_arr ) {
							foreach($department_arr as $job_id => $job_arr ) {
								foreach($job_arr as $job_item_id => $data_arr ) {

									Debug::text('Compact Array Entry: Regular Time - Branch ID: '. $branch_id .' Department ID: '. $department_id .' Job ID: '. $job_id .' Job Item ID: '. $job_item_id .' Total Time: '. $data_arr['total_time'] , __FILE__, __LINE__, __METHOD__, 10);
									$user_data_total_expanded[] = array(
																		'type_id' => $type_id,
																		'over_time_policy_id' => NULL,
																		'branch_id' => $branch_id,
																		'department_id' => $department_id,
																		'job_id' => $job_id,
																		'job_item_id' => $job_item_id,
																		'total_time' => $data_arr['total_time'],
																		'quantity' => $data_arr['quantity'],
																		'bad_quantity' => $data_arr['bad_quantity']
																		);
								}
							}
						}
					}
				} else {
					//Overtime
					//Overtime array is completely different then regular time array!
					foreach($udt_arr as $over_time_policy_id => $policy_arr ) {
						foreach($policy_arr as $branch_id => $branch_arr ) {
							//foreach($branch_arr as $department_id => $total_time ) {
							foreach($branch_arr as $department_id => $department_arr ) {
								foreach($department_arr as $job_id => $job_arr ) {
									foreach($job_arr as $job_item_id => $data_arr ) {

										Debug::text('Compact Array Entry: Policy ID: '. $over_time_policy_id .' Branch ID: '. $branch_id .' Department ID: '. $department_id .' Job ID: '. $job_id .' Job Item ID: '. $job_item_id .' Total Time: '. $data_arr['total_time'] , __FILE__, __LINE__, __METHOD__, 10);
										$user_data_total_expanded[] = array(
																			'type_id' => $type_id,
																			'over_time_policy_id' => $over_time_policy_id,
																			'branch_id' => $branch_id,
																			'department_id' => $department_id,
																			'job_id' => $job_id,
																			'job_item_id' => $job_item_id,
																			'total_time' => $data_arr['total_time'],
																			'quantity' => $data_arr['quantity'],
																			'bad_quantity' => $data_arr['bad_quantity']
																			);
									}
								}
							}
						}
					}
				}

				unset($policy_arr, $branch_arr, $department_arr, $job_arr, $over_time_policy_id, $branch_id, $department_id, $job_id, $job_item_id, $data_arr);
			}
			$profiler->stopTimer( "UserDateTotal::calcSystemTotalTime() - Part 2");

			//var_dump($user_data_total_expanded);
			//Do the actual inserts now.
			if ( isset($user_data_total_expanded) ) {
				foreach($user_data_total_expanded as $data_arr) {
					$profiler->startTimer( "UserDateTotal::calcSystemTotalTime() - Part 2b");

					Debug::text('Inserting from expanded array, Type ID: '.  $data_arr['type_id'], __FILE__, __LINE__, __METHOD__, 10);
					$udtf = new UserDateTotalFactory();
					$udtf->setUserDateID( $this->getUserDateID() );
					$udtf->setStatus( 10 ); //System
					$udtf->setType( $data_arr['type_id'] );
					if ( isset($data_arr['over_time_policy_id']) ) {
						$udtf->setOverTimePolicyId( $data_arr['over_time_policy_id'] );
					}

					$udtf->setBranch( $data_arr['branch_id'] );
					$udtf->setDepartment( $data_arr['department_id'] );
					$udtf->setJob( $data_arr['job_id'] );
					$udtf->setJobItem( $data_arr['job_item_id'] );

					$udtf->setQuantity( $data_arr['quantity'] );
					$udtf->setBadQuantity( $data_arr['bad_quantity'] );

					$udtf->setTotalTime( $data_arr['total_time'] );
					$udtf->setEnableCalcSystemTotalTime(FALSE);
					if ( $udtf->isValid() ) {
						$udtf->Save();
					} else {
						Debug::text('aINVALID UserDateTotal Entry!!: ', __FILE__, __LINE__, __METHOD__, 10);
					}

					$profiler->stopTimer( "UserDateTotal::calcSystemTotalTime() - Part 2b");

				}
				unset($user_data_total_expanded);
			}

		} else {
			$profiler->startTimer( "UserDateTotal::calcSystemTotalTime() - Part 3");

			//We need to break this out by branch, dept, job, task
			$udtlf = new UserDateTotalListFactory();

			//FIXME: Should Absence time be included as "regular time". We do this on
			//the timesheet view manually as of 12-Jan-06. If we included it in the
			//regular time system totals, we wouldn't have to do it manually.
			//$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), array(20,30) );
			$udtlf->getByUserDateIdAndStatus( $this->getUserDateID(), array(20) );
			if ( $udtlf->getRecordCount() > 0 ) {
				Debug::text('Found Total Hours for just regular time: Record Count: '. $udtlf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
				$user_date_regular_time_compact_arr = NULL;
				foreach( $udtlf as $udt_obj ) {
					//Create compact array, so we don't make as many system entries.
					//Check if this is a paid absence or not.
					if ( $udt_obj->getStatus() == 20 AND $udt_obj->getTotalTime() > 0 ) {

						$udt_total_time = $udt_obj->getTotalTime();
						if ( isset( $udt_meal_policy_adjustment_arr[$udt_obj->getId()] ) ) {
							$udt_total_time = bcadd( $udt_total_time, $udt_meal_policy_adjustment_arr[$udt_obj->getId()] );
						}
						if ( isset( $udt_break_policy_adjustment_arr[$udt_obj->getId()] ) ) {
							$udt_total_time = bcadd( $udt_total_time, $udt_break_policy_adjustment_arr[$udt_obj->getId()] );
						}

						if ( isset($user_date_regular_time_compact_arr[(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]) ) {
							Debug::text('&nbsp;&nbsp;&nbsp;&nbsp; Adding to Compact Array: Regular Time -  Branch: '. (int)$udt_obj->getBranch() .' Department: '. (int)$udt_obj->getDepartment(), __FILE__, __LINE__, __METHOD__, 10);
							$user_date_regular_time_compact_arr[(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['total_time'] += $udt_total_time;
							$user_date_regular_time_compact_arr[(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['quantity'] += $udt_obj->getQuantity();
							$user_date_regular_time_compact_arr[(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()]['bad_quantity'] += $udt_obj->getBadQuantity();
						} else {
							$user_date_regular_time_compact_arr[(int)$udt_obj->getBranch()][(int)$udt_obj->getDepartment()][(int)$udt_obj->getJob()][(int)$udt_obj->getJobItem()] = array( 'total_time' => $udt_total_time, 'quantity' => $udt_obj->getQuantity(), 'bad_quantity' => $udt_obj->getBadQuantity() );
						}
						unset($udt_total_time);
					} else {
						Debug::text('Total Time is 0!!: '. $udt_obj->getTotalTime() .' Or its an UNPAID absence: '. $udt_obj->getStatus(), __FILE__, __LINE__, __METHOD__, 10);
					}
				}

				if ( isset($user_date_regular_time_compact_arr) ) {
					foreach($user_date_regular_time_compact_arr as $branch_id => $branch_arr ) {
						//foreach($branch_arr as $department_id => $total_time ) {
						foreach($branch_arr as $department_id => $department_arr ) {
							foreach($department_arr as $job_id => $job_arr ) {
								foreach($job_arr as $job_item_id => $data_arr ) {

									Debug::text('Compact Array Entry: bRegular Time - Branch ID: '. $branch_id .' Department ID: '. $department_id .' Job ID: '. $job_id .' Job Item ID: '. $job_item_id .' Total Time: '. $data_arr['total_time'] , __FILE__, __LINE__, __METHOD__, 10);

									$udtf = new UserDateTotalFactory();
									$udtf->setUserDateID( $this->getUserDateID() );
									$udtf->setStatus( 10 ); //System
									$udtf->setType( 20 ); //Regular

									$udtf->setBranch( $branch_id );
									$udtf->setDepartment( $department_id );

									$udtf->setJob( $job_id );
									$udtf->setJobItem( $job_item_id );

									$udtf->setQuantity( $data_arr['quantity']  );
									$udtf->setBadQuantity( $data_arr['bad_quantity'] );

									$udtf->setTotalTime( $data_arr['total_time'] );
									$udtf->setEnableCalcSystemTotalTime(FALSE);
									$udtf->Save();
								}
							}
						}
					}
				}
				unset($user_date_regular_time_compact_arr);
			}
		}

		//Handle Premium time.
		$this->calcPremiumPolicyTotalTime( $udt_meal_policy_adjustment_arr, $udt_break_policy_adjustment_arr, $daily_total_time );

		//Total Hours
		$udtf = new UserDateTotalFactory();
		$udtf->setUserDateID( $this->getUserDateID() );
		$udtf->setStatus( 10 ); //System
		$udtf->setType( 10 ); //Total
		$udtf->setTotalTime( $daily_total_time );
		$udtf->setEnableCalcSystemTotalTime(FALSE);
		if ( $udtf->isValid() ) {
			$return_value = $udtf->Save();
		} else {
			$return_value = FALSE;
		}

		$profiler->stopTimer( "UserDateTotal::calcSystemTotalTime() - Part 3");

		if ( $this->getEnableCalcException() == TRUE ) {
			ExceptionPolicyFactory::calcExceptions( $this->getUserDateID(), $this->getEnablePreMatureException() );
		}

		return $return_value;
	}

	function calcWeeklySystemTotalTime() {
		if ( $this->getEnableCalcWeeklySystemTotalTime() == TRUE ) {
			global $profiler;

			$profiler->startTimer( "UserDateTotal::postSave() - reCalculateRange 1");

			//Get Pay Period Schedule info
			if ( is_object($this->getUserDateObject()->getPayPeriodObject())
					AND is_object($this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject()) ) {
				$start_week_day_id = $this->getUserDateObject()->getPayPeriodObject()->getPayPeriodScheduleObject()->getStartWeekDay();
			} else {
				$start_week_day_id = 0;
			}
			Debug::text('Start Week Day ID: '. $start_week_day_id .' Date Stamp: '. TTDate::getDate('DATE+TIME', $this->getUserDateObject()->getDateStamp()), __FILE__, __LINE__, __METHOD__, 10);

			UserDateTotalFactory::reCalculateRange( $this->getUserDateObject()->getUser(), ($this->getUserDateObject()->getDateStamp()+86400), TTDate::getEndWeekEpoch( $this->getUserDateObject()->getDateStamp(), $start_week_day_id ) );
			unset($start_week_day_id);

			$profiler->stopTimer( "UserDateTotal::postSave() - reCalculateRange 1");
			return TRUE;
		}

		return FALSE;
	}

	function getHolidayUserDateIDs() {
		Debug::text('reCalculating Holiday...', __FILE__, __LINE__, __METHOD__, 10);

		//Get Holiday policies and determine how many days we need to look ahead/behind in order
		//to recalculate the holiday eligilibility/time.
		$holiday_before_days = 0;
		$holiday_after_days = 0;

		$hplf = new HolidayPolicyListFactory();
		$hplf->getByCompanyId( $this->getUserDateObject()->getUserObject()->getCompany() );
		if ( $hplf->getRecordCount() > 0 ) {
			foreach( $hplf as $hp_obj ) {
				if ( $hp_obj->getMinimumWorkedPeriodDays() > $holiday_before_days ) {
					$holiday_before_days = $hp_obj->getMinimumWorkedPeriodDays();
				}
				if ( $hp_obj->getAverageTimeDays() > $holiday_before_days ) {
					$holiday_before_days = $hp_obj->getAverageTimeDays();
				}
				if ( $hp_obj->getMinimumWorkedAfterPeriodDays() > $holiday_after_days ) {
					$holiday_after_days = $hp_obj->getMinimumWorkedAfterPeriodDays();
				}
			}
		}
		Debug::text('Holiday Before Days: '. $holiday_before_days .' Holiday After Days: '. $holiday_after_days, __FILE__, __LINE__, __METHOD__, 10);

		if ( $holiday_before_days > 0 OR $holiday_after_days > 0 ) {
			$retarr = array();

			$search_start_date = TTDate::getBeginWeekEpoch( ($this->getUserDateObject()->getDateStamp()-($holiday_after_days*86400)) );
			$search_end_date = TTDate::getEndWeekEpoch( TTDate::getEndDayEpoch($this->getUserDateObject()->getDateStamp())+($holiday_before_days*86400)+3601 );
			Debug::text('Holiday search start date: '. TTDate::getDate('DATE', $search_start_date ) .' End date: '. TTDate::getDate('DATE', $search_end_date ) .' Current Date: '. TTDate::getDate('DATE', $this->getUserDateObject()->getDateStamp() ), __FILE__, __LINE__, __METHOD__, 10);

			$hlf = new HolidayListFactory();
			//$hlf->getByPolicyGroupUserIdAndStartDateAndEndDate( $this->getUserDateObject()->getUser(), TTDate::getEndWeekEpoch( $this->getUserDateObject()->getDateStamp() )+86400, TTDate::getEndDayEpoch()+($max_average_time_days*86400)+3601 );
			$hlf->getByPolicyGroupUserIdAndStartDateAndEndDate( $this->getUserDateObject()->getUser(), $search_start_date, $search_end_date  );
			if ( $hlf->getRecordCount() > 0 ) {
				Debug::text('Found Holidays within range: '. $hlf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);

				$udlf = new UserDateListFactory();
				foreach( $hlf as $h_obj ) {
					Debug::text('ReCalculating Day due to Holiday: '. TTDate::getDate('DATE', $h_obj->getDateStamp() ), __FILE__, __LINE__, __METHOD__, 10);
					$user_date_ids = $udlf->getArrayByListFactory( $udlf->getByUserIdAndDate( $this->getUserDateObject()->getUser(), $h_obj->getDateStamp() ) );
					if ( is_array( $user_date_ids ) ) {
						$retarr = array_merge( $retarr, $user_date_ids );
					}
					unset($user_date_ids);
				}
			}
		}

		if ( isset($retarr) AND is_array( $retarr ) AND count($retarr) > 0 ) {
			//Debug::Arr($retarr, 'Holiday UserDateIDs: ', __FILE__, __LINE__, __METHOD__, 10);
			return $retarr;
		}

		Debug::text('No Holidays within range...', __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
	}

	static function reCalculateDay( $user_date_id, $enable_exception = FALSE, $enable_premature_exceptions = FALSE, $enable_future_exceptions = TRUE, $enable_holidays = FALSE ) {
		Debug::text('Re-calculating User Date ID: '. $user_date_id .' Enable Exception: '. (int)$enable_exception, __FILE__, __LINE__, __METHOD__, 10);
		$udtf = new UserDateTotalFactory();
		$udtf->setUserDateId( $user_date_id );
		$udtf->calcSystemTotalTime();

		if ( $enable_holidays == TRUE ) {
			$holiday_user_date_ids = $udtf->getHolidayUserDateIDs();
			//var_dump($holiday_user_date_ids);
			if ( is_array($holiday_user_date_ids) ) {
				foreach( $holiday_user_date_ids as $holiday_user_date_id ) {
					Debug::Text('reCalculating Holiday...', __FILE__, __LINE__, __METHOD__, 10);
					UserDateTotalFactory::reCalculateDay( $holiday_user_date_id, FALSE, FALSE, FALSE, FALSE );
				}
			}
			unset($holiday_user_date_ids, $holiday_user_date_id);
		}

		if ( !isset(self::$calc_exception) AND $enable_exception == TRUE ) {
			ExceptionPolicyFactory::calcExceptions( $user_date_id, $enable_premature_exceptions, $enable_future_exceptions );
		}

		return TRUE;
	}

	static function reCalculateRange( $user_id, $start_date, $end_date ) {
		Debug::text('Re-calculating Range for User: '. $user_id .' Start: '. $start_date .' End: '. $end_date , __FILE__, __LINE__, __METHOD__, 10);

		$udlf = new UserDateListFactory();
		$udlf->getByUserIdAndStartDateAndEndDate( $user_id, $start_date, $end_date );
		if ( $udlf->getRecordCount() > 0 ) {
			Debug::text('Found days to re-calculate: '.$udlf->getRecordCount() , __FILE__, __LINE__, __METHOD__, 10);

			$udlf->StartTransaction();
			$x = 0;
			$x_max = $udlf->getRecordCount();
			foreach($udlf as $ud_obj ) {

				if ( $x == $x_max ) {
					//At the end of each range, make sure we calculate holidays.
					UserDateTotalFactory::reCalculateDay( $ud_obj->getId(), FALSE, FALSE, FALSE, TRUE );
				} else {
					UserDateTotalFactory::reCalculateDay( $ud_obj->getId(), FALSE, FALSE, FALSE, FALSE );
				}

				$x++;
			}
			$udlf->CommitTransaction();

			return TRUE;
		}

		Debug::text('DID NOT find days to re-calculate: ', __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
	}

	static function smartReCalculate( $user_id, $user_date_ids, $enable_exception = TRUE, $enable_premature_exceptions = FALSE, $enable_future_exceptions = TRUE ) {
		if ( $user_id == '' ) {
			return FALSE;
		}
		//Debug::Arr($user_date_ids, 'aUser Date IDs: ', __FILE__, __LINE__, __METHOD__, 10);

		if ( !is_array($user_date_ids) AND is_int($user_date_ids ) ) {
			$user_date_ids = array($user_date_ids);
		}

		if ( !is_array($user_date_ids ) ) {
			return FALSE;
		}

		$user_date_ids = array_unique( $user_date_ids );
		//Debug::Arr($user_date_ids, 'bUser Date IDs: ', __FILE__, __LINE__, __METHOD__, 10);

		$start_week_day_id = 0;
		$ppslf = new PayPeriodScheduleListFactory();
		$ppslf->getByUserId( $user_id );
		if ( $ppslf->getRecordCount() == 1 ) {
			$pps_obj = $ppslf->getCurrent();
			$start_week_day_id = $pps_obj->getStartWeekDay();
		}
		Debug::text('Start Week Day ID: '. $start_week_day_id, __FILE__, __LINE__, __METHOD__, 10);

		//Get date stamps for all user_date_ids.
		$udlf = new UserDateListFactory();
		$udlf->getByIds( $user_date_ids, NULL, array('date_stamp' => 'asc') ); //Order by date asc
		if ( $udlf->getRecordCount() > 0 ) {
			//Order them, and get the one or more sets of date ranges that need to be recalculated.
			//Need to consider re-calculating multiple weeks at once.

			$i=0;
			foreach( $udlf as $ud_obj ) {
				$start_week_epoch = TTDate::getBeginWeekEpoch( $ud_obj->getDateStamp(), $start_week_day_id );
				$end_week_epoch = TTDate::getEndWeekEpoch( $ud_obj->getDateStamp(), $start_week_day_id );

				Debug::text('Current Date: '. TTDate::getDate('DATE', $ud_obj->getDateStamp() )  .' Start Week: '. TTDate::getDate('DATE', $start_week_epoch) .' End Week: '. TTDate::getDate('DATE', $end_week_epoch) , __FILE__, __LINE__, __METHOD__, 10);

				if ( $i == 0 ) {
					$range_arr[$start_week_epoch] = array('start_date' => $ud_obj->getDateStamp(), 'end_date' => $end_week_epoch );
				} else {
					//Loop through each range extending it if needed.
					foreach( $range_arr as $tmp_start_week_epoch => $tmp_range ) {
						if ( $ud_obj->getDateStamp() >= $tmp_range['start_date'] AND $ud_obj->getDateStamp() <= $tmp_range['end_date'] ) {
							//Date falls within already existing range
							continue;
						} elseif ( $ud_obj->getDateStamp() < $tmp_range['start_date'] AND $ud_obj->getDateStamp() >= $tmp_start_week_epoch) {
							//Date falls within the same week, but before the current start date.
							$range_arr[$tmp_start_week_epoch]['start_date'] = $ud_obj->getDateStamp();
							Debug::text('Pushing Start Date back...', __FILE__, __LINE__, __METHOD__, 10);
						} else {
							//Outside current range. Check to make sure it isn't within another range.
							if ( isset($range_arr[$start_week_epoch]) ) {
								//Within another existing week, check to see if we need to extend it.
								if ( $ud_obj->getDateStamp() < $range_arr[$start_week_epoch]['start_date'] ) {
									Debug::text('bPushing Start Date back...', __FILE__, __LINE__, __METHOD__, 10);
									$range_arr[$start_week_epoch]['start_date'] = $ud_obj->getDateStamp();
								}
							} else {
								//Not within another existing week
								Debug::text('Adding new range...', __FILE__, __LINE__, __METHOD__, 10);
								$range_arr[$start_week_epoch] = array('start_date' => $ud_obj->getDateStamp(), 'end_date' => $end_week_epoch );
							}
						}
					}
					unset($tmp_range, $tmp_start_week_epoch);
				}

				$i++;
			}
			unset($start_week_epoch, $end_week_epoch,  $udlf, $ud_obj);

			if ( is_array( $range_arr ) ) {
				ksort($range_arr); //Sort range by start week, so recalculating goes in date order.
				//Debug::Arr($range_arr, 'Range Array: ', __FILE__, __LINE__, __METHOD__, 10);
				foreach( $range_arr as $week_range ) {
					$udlf = new UserDateListFactory();
					$udlf->getByUserIdAndStartDateAndEndDate( $user_id, $week_range['start_date'], $week_range['end_date'] );
					if ( $udlf->getRecordCount() > 0 ) {
						Debug::text('Found days to re-calculate: '. $udlf->getRecordCount() , __FILE__, __LINE__, __METHOD__, 10);

						$udlf->StartTransaction();

						$z = 1;
						$z_max = $udlf->getRecordCount();
						foreach($udlf as $ud_obj ) {
							//We only need to re-calculate exceptions on the exact days specified by user_date_ids.
							//This was the case before we Over Weekly Time/Over Scheduled Weekly Time exceptions,
							//Now we have to enable calculating exceptions for the entire week.
							/*
							if ( in_array( $ud_obj->getId(), $user_date_ids ) ) {
								//Calculate exceptions
								Debug::text('Re-calculating day with exceptions: '. $ud_obj->getId() , __FILE__, __LINE__, __METHOD__, 10);
								UserDateTotalFactory::reCalculateDay( $ud_obj->getId(), $enable_exception, $enable_premature_exceptions, $enable_future_exceptions );
							} else {
								//Don't calculate exceptions.
								UserDateTotalFactory::reCalculateDay( $ud_obj->getId() );
							}
							*/

							Debug::text('Re-calculating day with exceptions: '. $ud_obj->getId() , __FILE__, __LINE__, __METHOD__, 10);
							if ( $z == $z_max ) {
								//Enable recalculating holidays at the end of each week.
								UserDateTotalFactory::reCalculateDay( $ud_obj->getId(), $enable_exception, $enable_premature_exceptions, $enable_future_exceptions, TRUE );
							} else {
								UserDateTotalFactory::reCalculateDay( $ud_obj->getId(), $enable_exception, $enable_premature_exceptions, $enable_future_exceptions );
							}

							$z++;
						}
						$udlf->CommitTransaction();
					}
				}

				return TRUE;
			}

		}

		Debug::text('Returning FALSE!', __FILE__, __LINE__, __METHOD__, 10);

		return FALSE;
	}

	function Validate() {
		//Make sure status/type combinations are correct.
		if ( !in_array($this->getType(), $this->getOptions('status_type', $this->getStatus() ) ) ) {
				Debug::text('Type doesnt match status: Type: '. $this->getType() .' Status: '. $this->getStatus() , __FILE__, __LINE__, __METHOD__, 10);
				$this->Validator->isTRUE(	'type',
											FALSE,
											TTi18n::gettext('Incorrect Type'));
		}

		//Check to make sure if this is an absence row, the absence policy is actually set.
		if ( $this->getStatus() == 30 AND $this->getAbsencePolicyID() == FALSE ) {
				$this->Validator->isTRUE(	'absence_policy',
											FALSE,
											TTi18n::gettext('Invalid Absence Policy'));
		}

		//Check to make sure if this is an overtime row, the overtime policy is actually set.
		if ( $this->getStatus() == 10 AND $this->getType() == 30 AND $this->getOverTimePolicyID() == FALSE ) {
				$this->Validator->isTRUE(	'over_time_policy',
											FALSE,
											TTi18n::gettext('Invalid Overtime Policy'));
		}

		//Check to make sure if this is an premium row, the premium policy is actually set.
		if ( $this->getStatus() == 10 AND $this->getType() == 40 AND $this->getPremiumPolicyID() == FALSE ) {
				$this->Validator->isTRUE(	'premium_policy',
											FALSE,
											TTi18n::gettext('Invalid Premium Policy'));
		}

		//Check to make sure if this is an meal row, the meal policy is actually set.
		if ( $this->getStatus() == 10 AND $this->getType() == 100 AND $this->getMealPolicyID() == FALSE ) {
				$this->Validator->isTRUE(	'meal_policy',
											FALSE,
											TTi18n::gettext('Invalid Meal Policy'));
		}

		//Make sure that we aren't trying to overwrite an already overridden entry made by the user for some special purpose.
		if ( $this->getDeleted() == FALSE
				AND $this->isNew() == TRUE
				AND in_array( $this->getStatus(), array(10,20,30) ) ) {

			Debug::text('Checking over already existing overridden entries ... User Date ID: '. $this->getUserDateID() .' Status ID: '. $this->getStatus() .' Type ID: '. $this->getType(), __FILE__, __LINE__, __METHOD__, 10);

			$udtlf = new UserDateTotalListFactory();

			if ( $this->getStatus() == 20 AND $this->getPunchControlID() > 0 ) {
				$udtlf->getByUserDateIdAndStatusAndTypeAndPunchControlIdAndOverride( $this->getUserDateID(), $this->getStatus(), $this->getType(), $this->getPunchControlID(), TRUE );
			} elseif ( $this->getStatus() == 30 ) {
				$udtlf->getByUserDateIdAndStatusAndTypeAndAbsencePolicyIDAndOverride( $this->getUserDateID(), $this->getStatus(), $this->getType(), $this->getAbsencePolicyID(), TRUE );
			} elseif ( $this->getStatus() == 10 AND $this->getType() == 30 ) {
				$udtlf->getByUserDateIdAndStatusAndTypeAndOvertimePolicyIDAndOverride( $this->getUserDateID(), $this->getStatus(), $this->getType(), $this->getOverTimePolicyID(), TRUE );
			} elseif ( $this->getStatus() == 10 AND $this->getType() == 40 ) {
				$udtlf->getByUserDateIdAndStatusAndTypeAndPremiumPolicyIDAndOverride( $this->getUserDateID(), $this->getStatus(), $this->getType(), $this->getPremiumPolicyID(), TRUE );
			} elseif ( $this->getStatus() == 10 AND $this->getType() == 100 ) {
				$udtlf->getByUserDateIdAndStatusAndTypeAndMealPolicyIDAndOverride( $this->getUserDateID(), $this->getStatus(), $this->getType(), $this->getMealPolicyID(), TRUE );
			} elseif ( $this->getStatus() == 10 AND ( $this->getType() == 10 OR ( $this->getType() == 20 AND $this->getPunchControlID() > 0 ) ) ) {
				$udtlf->getByUserDateIdAndStatusAndTypeAndPunchControlIdAndOverride( $this->getUserDateID(), $this->getStatus(), $this->getType(), $this->getPunchControlID(), TRUE );
			}

			Debug::text('Record Count: '. $udtlf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
			if ( $udtlf->getRecordCount() > 0 ) {
				Debug::text('Found an overridden row... NOT SAVING: '. $udtlf->getCurrent()->getId(), __FILE__, __LINE__, __METHOD__, 10);
				$this->Validator->isTRUE(	'override',
											FALSE,
											TTi18n::gettext('Similar entry already exists, not overriding'));
			}
		}

		return TRUE;
	}

	function preSave() {
		if ( $this->getPunchControlID() === FALSE ) {
			$this->setPunchControlID(0);
		}

		if ( $this->getOverTimePolicyID() === FALSE ) {
			$this->setOverTimePolicyId(0);
		}

		if ( $this->getAbsencePolicyID() === FALSE ) {
			$this->setAbsencePolicyID(0);
		}

		if ( $this->getPremiumPolicyID() === FALSE ) {
			$this->setPremiumPolicyId(0);
		}

		if ( $this->getMealPolicyID() === FALSE ) {
			$this->setMealPolicyId(0);
		}

		if ( $this->getBranch() === FALSE ) {
			$this->setBranch(0);
		}

		if ( $this->getDepartment() === FALSE ) {
			$this->setDepartment(0);
		}

		if ( $this->getJob() === FALSE ) {
			$this->setJob(0);
		}

		if ( $this->getJobItem() === FALSE ) {
			$this->setJobItem(0);
		}

		if ( $this->getQuantity() === FALSE ) {
			$this->setQuantity(0);
		}

		if ( $this->getBadQuantity() === FALSE ) {
			$this->setBadQuantity(0);
		}

		return TRUE;
	}

	function postSave() {
		if ( $this->getEnableCalcSystemTotalTime() == TRUE ) {
			Debug::text('Calc System Total Time Enabled: ', __FILE__, __LINE__, __METHOD__, 10);
			$this->calcSystemTotalTime();
		} else {
			Debug::text('Calc System Total Time Disabled: ', __FILE__, __LINE__, __METHOD__, 10);
		}

		if ( $this->getDeleted() == FALSE ) {
			//Handle accruals here, instead of in calcSystemTime as that is too early in the process and user_date_total ID's don't exist yet.
			$this->calcAccrualPolicy();

			AccrualFactory::deleteOrphans( $this->getUserDateObject()->getUser() );
		}

		return TRUE;
	}
}
?>
