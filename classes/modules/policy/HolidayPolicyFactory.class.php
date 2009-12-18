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
 * $Revision: 2741 $
 * $Id: HolidayPolicyFactory.class.php 2741 2009-08-19 22:11:46Z ipso $
 * $Date: 2009-08-19 15:11:46 -0700 (Wed, 19 Aug 2009) $
 */

/**
 * @package Module_Policy
 */
class HolidayPolicyFactory extends Factory {
	protected $table = 'holiday_policy';
	protected $pk_sequence_name = 'holiday_policy_id_seq'; //PK Sequence name

	protected $company_obj = NULL;
	protected $round_interval_policy_obj = NULL;
	protected $absence_policy_obj = NULL;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'default_schedule_status':
				$sf = new ScheduleFactory();
				$retval = $sf->getOptions('status');
				break;
			case 'type':
				$retval = array(
										10 => TTi18n::gettext('Standard'),
										20 => TTi18n::gettext('Advanced: Fixed'),
										30 => TTi18n::gettext('Advanced: Average'),
									);
				break;
			case 'scheduled_day':
				$retval = array(
										0 => TTi18n::gettext('Calendar'),
										1 => TTi18n::gettext('Scheduled'),
									);
				break;
			case 'columns':
				$retval = array(
										'-1020-name' => TTi18n::gettext('Name'),
										'-1010-type' => TTi18n::gettext('Type'),

										'-2000-created_by' => TTi18n::gettext('Created By'),
										'-2010-created_date' => TTi18n::gettext('Created Date'),
										'-2020-updated_by' => TTi18n::gettext('Updated By'),
										'-2030-updated_date' => TTi18n::gettext('Updated Date'),
							);
				break;
			case 'default_display_columns': //Columns that are displayed by default.
				$retval = array(
								'name',
								'type',
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
										'default_schedule_status_id' => 'DefaultScheduleStatus',
										'minimum_employed_days' => 'MinimumEmployedDays',
										'minimum_worked_period_days' => 'MinimumWorkedPeriodDays',
										'minimum_worked_days' => 'MinimumWorkedDays',
										'worked_scheduled_days' => 'WorkedScheduledDays',
										'minimum_worked_after_period_days' => 'MinimumWorkedAfterPeriodDays',
										'minimum_worked_after_days' => 'MinimumWorkedAfterDays',
										'worked_after_scheduled_days' => 'WorkedAfterScheduledDays',
										'average_time_days' => 'AverageTimeDays',
										'average_time_worked_days' => 'AverageTimeWorkedDays',
										'minimum_time' => 'MinimumTime',
										'maximum_time' => 'MaximumTime',
										'round_interval_policy_id' => 'RoundIntervalPolicyID',
										'time' => 'Time',
										'paid_absence_as_worked' => 'PaidAbsenceAsWorked',
										'force_over_time_policy' => 'ForceOverTimePolicy',
										'include_over_time' => 'IncludeOverTime',
										'include_paid_absence_time' => 'IncludePaidAbsenceTime',
										'absence_policy_id' => 'AbsencePolicyID',
										'recurring_holiday_id' => 'RecurringHoliday',
										'deleted' => 'Deleted',
										);
		return $variable_function_map;
	}

	function getRoundIntervalPolicyObject() {
		if ( is_object($this->round_interval_policy_obj) ) {
			return $this->round_interval_policy_obj;
		} else {
			$riplf = new RoundIntervalPolicyListFactory();
			$riplf->getById( $this->getRoundIntervalPolicyID() );
			if ( $riplf->getRecordCount() > 0 ) {
				$this->round_interval_policy_obj = $riplf->getCurrent();
			}

			return $this->round_interval_policy_obj;
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

	function getDefaultScheduleStatus() {
		if ( isset($this->data['default_schedule_status_id']) ) {
			return $this->data['default_schedule_status_id'];
		}

		return FALSE;
	}
	function setDefaultScheduleStatus($value) {
		$value = trim($value);

		$sf = new ScheduleFactory();

		$key = Option::getByValue($value, $sf->getOptions('status') );
		if ($key !== FALSE) {
			$value = $key;
		}

		if ( $this->Validator->inArrayKey(	'default_schedule_status',
											$value,
											TTi18n::gettext('Incorrect Default Schedule Status'),
											$sf->getOptions('status')) ) {

			$this->data['default_schedule_status_id'] = $value;

			return FALSE;
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

		if  ( empty($int) ){
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

	function getMinimumWorkedPeriodDays() {
		if ( isset($this->data['minimum_worked_period_days']) ) {
			return (int)$this->data['minimum_worked_period_days'];
		}

		return FALSE;
	}
	function setMinimumWorkedPeriodDays($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'minimum_worked_period_days',
													$int,
													TTi18n::gettext('Incorrect Minimum Worked Period days')) ) {
			$this->data['minimum_worked_period_days'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getMinimumWorkedDays() {
		if ( isset($this->data['minimum_worked_days']) ) {
			return (int)$this->data['minimum_worked_days'];
		}

		return FALSE;
	}
	function setMinimumWorkedDays($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'minimum_worked_days',
													$int,
													TTi18n::gettext('Incorrect Minimum Worked days')) ) {
			$this->data['minimum_worked_days'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getWorkedScheduledDays() {
		return $this->fromBool( $this->data['worked_scheduled_days'] );
	}
	function setWorkedScheduledDays($bool) {
		$this->data['worked_scheduled_days'] = $this->toBool($bool);

		return true;
	}

	function getMinimumWorkedAfterPeriodDays() {
		if ( isset($this->data['minimum_worked_after_period_days']) ) {
			return (int)$this->data['minimum_worked_after_period_days'];
		}

		return FALSE;
	}
	function setMinimumWorkedAfterPeriodDays($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'minimum_worked_after_period_days',
													$int,
													TTi18n::gettext('Incorrect Minimum Worked After Period days')) ) {
			$this->data['minimum_worked_after_period_days'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getMinimumWorkedAfterDays() {
		if ( isset($this->data['minimum_worked_after_days']) ) {
			return (int)$this->data['minimum_worked_after_days'];
		}

		return FALSE;
	}
	function setMinimumWorkedAfterDays($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'minimum_worked_after_days',
													$int,
													TTi18n::gettext('Incorrect Minimum Worked After days')) ) {
			$this->data['minimum_worked_after_days'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getWorkedAfterScheduledDays() {
		return $this->fromBool( $this->data['worked_after_scheduled_days'] );
	}
	function setWorkedAfterScheduledDays($bool) {
		$this->data['worked_after_scheduled_days'] = $this->toBool($bool);

		return true;
	}

	function getAverageTimeDays() {
		if ( isset($this->data['average_time_days']) ) {
			return (int)$this->data['average_time_days'];
		}

		return FALSE;
	}
	function setAverageTimeDays($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'average_time_days',
													$int,
													TTi18n::gettext('Incorrect Days to Average over')) ) {
			$this->data['average_time_days'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	//If true, uses only worked days to average time over.
	//If false, always uses the average time days to average time over.
	function getAverageTimeWorkedDays() {
		return $this->fromBool( $this->data['average_time_worked_days'] );
	}
	function setAverageTimeWorkedDays($bool) {
		$this->data['average_time_worked_days'] = $this->toBool($bool);

		return true;
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

	function getRoundIntervalPolicyID() {
		if ( isset($this->data['round_interval_policy_id']) ) {
			return $this->data['round_interval_policy_id'];
		}

		return FALSE;
	}
	function setRoundIntervalPolicyID($id) {
		$id = trim($id);

		if ( $id == '' OR empty($id) ) {
			$id = NULL;
		}

		$riplf = new RoundIntervalPolicyListFactory();

		if ( $id == NULL
				OR
				$this->Validator->isResultSetWithRows(	'round_interval_policy',
													$riplf->getByID($id),
													TTi18n::gettext('Round Interval Policy is invalid')
													) ) {

			$this->data['round_interval_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getTime() {
		if ( isset($this->data['time']) ) {
			return (int)$this->data['time'];
		}

		return FALSE;
	}
	function setTime($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'time',
													$int,
													TTi18n::gettext('Incorrect Time')) ) {
			$this->data['time'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	//Count all paid absence time as worked time.
	function getPaidAbsenceAsWorked() {
		return $this->fromBool( $this->data['paid_absence_as_worked'] );
	}
	function setPaidAbsenceAsWorked($bool) {
		$this->data['paid_absence_as_worked'] = $this->toBool($bool);

		return true;
	}

	//Always applies over time policy even if they are not eligible for the holiday.
	function getForceOverTimePolicy() {
		return $this->fromBool( $this->data['force_over_time_policy'] );
	}
	function setForceOverTimePolicy($bool) {
		$this->data['force_over_time_policy'] = $this->toBool($bool);

		return true;
	}

	function getIncludeOverTime() {
		return $this->fromBool( $this->data['include_over_time'] );
	}
	function setIncludeOverTime($bool) {
		$this->data['include_over_time'] = $this->toBool($bool);

		return true;
	}

	function getIncludePaidAbsenceTime() {
		return $this->fromBool( $this->data['include_paid_absence_time'] );
	}
	function setIncludePaidAbsenceTime($bool) {
		$this->data['include_paid_absence_time'] = $this->toBool($bool);

		return true;
	}

	function getAbsencePolicyID() {
		if ( isset($this->data['absence_policy_id']) ) {
			return $this->data['absence_policy_id'];
		}

		return FALSE;
	}
	function setAbsencePolicyID($id) {
		$id = trim($id);

		if ( $id == '' OR empty($id) ) {
			$id = 0;
		}

		$aplf = new AbsencePolicyListFactory();

		if ( $id == 0
				OR
				$this->Validator->isResultSetWithRows(	'absence_policy_id',
													$aplf->getByID($id),
													TTi18n::gettext('Absence Policy is invalid')
													) ) {

			$this->data['absence_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getRecurringHoliday() {
		$hprhlf = new HolidayPolicyRecurringHolidayListFactory();
		$hprhlf->getByHolidayPolicyId( $this->getId() );
		Debug::text('Found Recurring Holidays Attached to this Policy: '. $hprhlf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
		foreach ($hprhlf as $obj) {
			$list[] = $obj->getRecurringHoliday();
		}

		if ( isset($list) ) {
			return $list;
		}

		return FALSE;
	}
	function setRecurringHoliday($ids) {
		Debug::text('Setting Recurring Holiday IDs : ', __FILE__, __LINE__, __METHOD__, 10);
		if (is_array($ids) and count($ids) > 0) {
			$tmp_ids = array();
			if ( !$this->isNew() ) {
				//If needed, delete mappings first.
				$hprhlf = new HolidayPolicyRecurringHolidayListFactory();
				$hprhlf->getByHolidayPolicyId( $this->getId() );

				foreach ($hprhlf as $obj) {
					$id = $obj->getRecurringHoliday();
					Debug::text('Policy ID: '. $obj->getHolidayPolicy() .' ID: '. $id, __FILE__, __LINE__, __METHOD__, 10);

					//Delete users that are not selected.
					if ( !in_array($id, $ids) ) {
						Debug::text('Deleting: '. $id, __FILE__, __LINE__, __METHOD__, 10);
						$obj->Delete();
					} else {
						//Save ID's that need to be updated.
						Debug::text('NOT Deleting : '. $id, __FILE__, __LINE__, __METHOD__, 10);
						$tmp_ids[] = $id;
					}
				}
				unset($id, $obj);
			}

			//Insert new mappings.
			$rhlf = new RecurringHolidayListFactory();

			foreach ($ids as $id) {
				if ( isset($ids) AND !in_array($id, $tmp_ids) AND $id > 0 ) {
					$hprhf = new HolidayPolicyRecurringHolidayFactory();
					$hprhf->setHolidayPolicy( $this->getId() );
					$hprhf->setRecurringHoliday( $id );

					$obj = $rhlf->getById( $id )->getCurrent();

					if ($this->Validator->isTrue(		'recurring_holiday',
														$hprhf->Validator->isValid(),
														TTi18n::gettext('Selected Recurring Holiday is invalid').' ('. $obj->getName() .')' )) {
						$hprhf->save();
					}
				}
			}

			return TRUE;
		}

		Debug::text('No User IDs to set.', __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
	}


	function Validate() {
		//If we always do this check, it breaks mass editing of holiday policies.
		/*
		if ( $this->isNew() == TRUE AND $this->isSave() == TRUE AND $this->getAbsencePolicyID() == FALSE ) {
			$this->Validator->isTrue(		'absence_policy_id',
											FALSE,
											TTi18n::gettext('Absence Policy is invalid') );
		}
		*/

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
							$function = 'get'.str_replace('_','',$variable);
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
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Holiday Policy'), NULL, $this->getTable() );
	}
}
?>
