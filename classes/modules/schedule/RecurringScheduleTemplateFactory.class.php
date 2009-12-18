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
 * $Revision: 3143 $
 * $Id: RecurringScheduleTemplateFactory.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */

/**
 * @package Module_Schedule
 */
class RecurringScheduleTemplateFactory extends Factory {
	protected $table = 'recurring_schedule_template';
	protected $pk_sequence_name = 'recurring_schedule_template_id_seq'; //PK Sequence name

	protected $schedule_policy_obj = NULL;
	function getSchedulePolicyObject() {
		if ( is_object($this->schedule_policy_obj) ) {
			return $this->schedule_policy_obj;
		} else {
			$splf = new SchedulePolicyListFactory();
			$splf->getById( $this->getSchedulePolicyID() );
			if ( $splf->getRecordCount() > 0 ) {
				$this->schedule_policy_obj = $splf->getCurrent();
				return $this->schedule_policy_obj;
			}

			return FALSE;
		}
	}

	function getRecurringScheduleTemplateControl() {
		if ( isset($this->data['recurring_schedule_template_control_id']) ) {
			return $this->data['recurring_schedule_template_control_id'];
		}

		return FALSE;
	}
	function setRecurringScheduleTemplateControl($id) {
		$id = trim($id);

		$rstclf = new RecurringScheduleTemplateControlListFactory();

		if ( $this->Validator->isResultSetWithRows(	'recurring_schedule_template_control',
													$rstclf->getByID($id),
													TTi18n::gettext('Recurring Schedule Template Control is invalid')
													) ) {

			$this->data['recurring_schedule_template_control_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getWeek() {
		if ( isset($this->data['week']) ) {
			return (int)$this->data['week'];
		}

		return FALSE;
	}
	function setWeek($int) {
		$int = trim($int);

		if 	(	$int > 0
				AND
				$this->Validator->isNumeric(		'week'.$this->getLabelID(),
													$int,
													TTi18n::gettext('Week is invalid')) ) {
			$this->data['week'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getSun() {
		if ( isset($this->data['sun']) ) {
			return $this->fromBool( $this->data['sun'] );
		}

		return FALSE;
	}
	function setSun($bool) {
		$this->data['sun'] = $this->toBool($bool);

		return TRUE;
	}

	function getMon() {
		if ( isset($this->data['mon']) ) {
			return $this->fromBool( $this->data['mon'] );
		}

		return FALSE;
	}
	function setMon($bool) {
		$this->data['mon'] = $this->toBool($bool);

		return TRUE;
	}
	function getTue() {
		if ( isset($this->data['tue']) ) {
			return $this->fromBool( $this->data['tue'] );
		}

		return FALSE;
	}
	function setTue($bool) {
		$this->data['tue'] = $this->toBool($bool);

		return TRUE;
	}
	function getWed() {
		if ( isset($this->data['wed']) ) {
			return $this->fromBool( $this->data['wed'] );
		}

		return FALSE;
	}
	function setWed($bool) {
		$this->data['wed'] = $this->toBool($bool);

		return TRUE;
	}
	function getThu() {
		if ( isset($this->data['thu']) ) {
			return $this->fromBool( $this->data['thu'] );
		}

		return FALSE;
	}
	function setThu($bool) {
		$this->data['thu'] = $this->toBool($bool);

		return TRUE;
	}
	function getFri() {
		if ( isset($this->data['fri']) ) {
			return $this->fromBool( $this->data['fri'] );
		}

		return FALSE;
	}
	function setFri($bool) {
		$this->data['fri'] = $this->toBool($bool);

		return TRUE;
	}
	function getSat() {
		if ( isset($this->data['sat']) ) {
			return $this->fromBool( $this->data['sat'] );
		}

		return FALSE;
	}
	function setSat($bool) {
		$this->data['sat'] = $this->toBool($bool);

		return TRUE;
	}

	function getStartTime( $raw = FALSE ) {
		if ( isset($this->data['start_time']) ) {
			if ( $raw === TRUE) {
				return $this->data['start_time'];
			} else {
				return TTDate::strtotime( $this->data['start_time'] );
			}
		}

		return FALSE;
	}
	function setStartTime($epoch) {
		$epoch = trim($epoch);

		Debug::Text('Start Time: '. $epoch, __FILE__, __LINE__, __METHOD__,10);

		if 	(	$this->Validator->isDate(		'start_time'.$this->getLabelID(),
												$epoch,
												TTi18n::gettext('Incorrect In time'))

			) {

			$this->data['start_time'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getEndTime( $raw = FALSE ) {
		if ( isset($this->data['end_time']) ) {
			if ( $raw === TRUE) {
				return $this->data['end_time'];
			} else {
				return TTDate::strtotime( $this->data['end_time'] );
			}
		}

		return FALSE;
	}
	function setEndTime($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'end_time'.$this->getLabelID(),
												$epoch,
												TTi18n::gettext('Incorrect Out time'))

			) {

			$this->data['end_time'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getTotalTime() {
		if ( $this->getSchedulePolicyObject() != FALSE ) {
			if ( $this->getSchedulePolicyObject()->getMealPolicyObject() != FALSE ) {
				$total_time = ( $this->getEndTime() - $this->getStartTime() ) - $this->getSchedulePolicyObject()->getMealPolicyObject()->getAmount();
				//Debug::Text('Meal Policy Deduct Amount: '. $this->getSchedulePolicyObject()->getMealPolicyObject()->getAmount() .' Total Time: '. TTDate::getTimeUnit( $total_time ), __FILE__, __LINE__, __METHOD__,10);
				return $total_time;
			}
		}

		$total_time = ( $this->getEndTime() - $this->getStartTime() );

		return $total_time;
	}

	function getSchedulePolicyID() {
		if ( isset($this->data['schedule_policy_id']) ) {
			return $this->data['schedule_policy_id'];
		}

		return FALSE;
	}
	function setSchedulePolicyID($id) {
		$id = trim($id);

		if ( $id == '' OR empty($id) ) {
			$id = NULL;
		}

		$splf = new SchedulePolicyListFactory();

		if ( $id == NULL
				OR
				$this->Validator->isResultSetWithRows(	'schedule_policy',
														$splf->getByID($id),
														TTi18n::gettext('Schedule Policy is invalid')
													) ) {

			$this->data['schedule_policy_id'] = $id;

			return TRUE;
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

		$blf = new BranchListFactory();

		//-1 is for user default branch.
		if (  $id == 0 OR $id == -1
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

		$dlf = new DepartmentListFactory();

		//-1 is for user default department.
		if (  $id == 0 OR $id == -1
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

		if (  $id == NULL
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

		if (  $id == NULL
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

	function isActiveShiftDay( $epoch ) {

		$day_of_week = strtolower( date('D', $epoch) );
		if ( isset( $this->data[$day_of_week] ) ) {
			return $this->fromBool( $this->data[$day_of_week] );
		}

		return FALSE;
	}

	function getShifts( $start_date, $end_date, &$holiday_data = array(), &$branch_options = array(), &$department_options = array(), &$shifts = array(), &$shifts_index = array() ) {
		//Debug::text('Start Date: '. TTDate::getDate('DATE+TIME', $start_date) .' End Date: '. TTDate::getDate('DATE+TIME', $end_date), __FILE__, __LINE__, __METHOD__, 10);

		$recurring_schedule_control_start_date = TTDate::strtotime( $this->getColumn('recurring_schedule_control_start_date') );
		//Debug::text('Recurring Schedule Control Start Date: '. TTDate::getDate('DATE+TIME', $recurring_schedule_control_start_date),__FILE__, __LINE__, __METHOD__, 10);

		$current_template_week = $this->getColumn('remapped_week');
		$max_week = $this->getColumn('max_week');
		//Debug::text('Template Week: '. $current_template_week .' Max Week: '. $this->getColumn('max_week') .' ReMapped Week: '. $this->getColumn('remapped_week') ,__FILE__, __LINE__, __METHOD__, 10);

		if ( $recurring_schedule_control_start_date == ''  ) {
			return FALSE;
		}

		//Get week of start_date
		$start_date_week = TTDate::getWeek( $recurring_schedule_control_start_date, 0 ); //Start week on Sunday to match Recurring Schedule.
		//Debug::text('Week of Start Date: '. $start_date_week ,__FILE__, __LINE__, __METHOD__, 10);

		for ( $i=$start_date; $i <= $end_date; $i+=(86400+43200)) {
			//Handle DST by adding 12hrs to the date to get the mid-day epoch, then forcing it back to the beginning of the day.
			$i = TTDate::getBeginDayEpoch( $i );

			if ( ( $this->getColumn('hire_date') != '' AND $i <= $this->getColumn('hire_date') )
					OR ( $this->getColumn('termination_date') != '' AND $i > $this->getColumn('termination_date') )
					) {
				//Debug::text('Skipping due to Hire/Termination date: User ID: '. $this->getColumn('user_id') .' I: '. $i .' Hire Date: '. $this->getColumn('hire_date') .' Termination Date: '. $this->getColumn('termination_date') ,__FILE__, __LINE__, __METHOD__, 10);
				continue;
			}

			$current_week = TTDate::getWeek( $i, 0 ); //Start week on Sunday to match Recurring Schedule.
			//Debug::text('I: '. $i .' User ID: '. $this->getColumn('user_id') .' Current Date: '. TTDate::getDate('DATE+TIME', $i) .' Current Week: '. $current_week,__FILE__, __LINE__, __METHOD__, 10);

			$template_week = ( ( ( abs($current_week-$start_date_week) ) % $max_week ) ) + 1;
			//Debug::text('Template Week: '. $template_week .' Max Week: '. $max_week,__FILE__, __LINE__, __METHOD__, 10);

			if ( $template_week == $current_template_week ) {
				//Debug::text('Current Date: '. TTDate::getDate('DATE+TIME', $i) .' Current Week: '. $current_week,__FILE__, __LINE__, __METHOD__, 10);
				//Debug::text('&nbsp;Template Week: '. $template_week .' Max Week: '. $max_week,__FILE__, __LINE__, __METHOD__, 10);

				if ( $this->isActiveShiftDay( $i ) ) {
					//Debug::text('&nbsp;&nbsp;Active Shift on this day...',__FILE__, __LINE__, __METHOD__, 10);
					$iso_date_stamp = TTDate::getISODateStamp( $i );

					$start_time = TTDate::getTimeLockedDate( $this->getStartTime(), $i );
					$end_time = TTDate::getTimeLockedDate( $this->getEndTime(), $i );
					if ( $end_time < $start_time ) {
						//Spans the day boundary, add 86400 to end_time
						$end_time = $end_time + 86400;
						//Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Schedule spans day boundary, bumping endtime to next day: ',__FILE__, __LINE__, __METHOD__, 10);
					}

					if ( isset($shifts_index[$iso_date_stamp][$this->getColumn('user_id')]) ) {
						//User has previous recurring schedule shifts, check for overlap.
						//Loop over each employees shift for this day and check for conflicts
						foreach( $shifts_index[$iso_date_stamp][$this->getColumn('user_id')] as $shift_key ) {
							if ( isset($shifts[$iso_date_stamp][$shift_key]) ) {
								if ( TTDate::isTimeOverLap( $shifts[$iso_date_stamp][$shift_key]['start_time'], $shifts[$iso_date_stamp][$shift_key]['end_time'], $start_time, $end_time ) == TRUE ) {
									//Debug::text('&nbsp;&nbsp;Found overlapping recurring schedules! User ID: '. $this->getColumn('user_id') .' Start Time: '. $start_time,__FILE__, __LINE__, __METHOD__, 10);
									continue 2;
								}
							}
						}
						unset($shift_key);
					}

					//This check has to occurr after the committed schedule check, otherwise no committed schedules will appear.
					if ( ( $this->getColumn('recurring_schedule_control_start_date') != '' AND $i < TTDate::strtotime( $this->getColumn('recurring_schedule_control_start_date') ) )
							OR ( $this->getColumn('recurring_schedule_control_end_date') != '' AND $i > TTDate::strtotime( $this->getColumn('recurring_schedule_control_end_date') ) ) ) {
						//Debug::text('Skipping due to Recurring Schedule Start/End date: ID: '. $this->getColumn('id') .' User ID: '. $this->getColumn('user_id') .' I: '. $i .' Start Date: '. $this->getColumn('recurring_schedule_control_start_date') .' ('. TTDate::strtotime( $this->getColumn('recurring_schedule_control_start_date') ) .') End Date: '. $this->getColumn('recurring_schedule_control_end_date') ,__FILE__, __LINE__, __METHOD__, 10);
						continue;
					}

					//Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Start Date: '. TTDate::getDate('DATE+TIME', $start_time) .' End Date: '. TTDate::getDate('DATE+TIME', $end_time),__FILE__, __LINE__, __METHOD__, 10);

					$status_id = 10; //Working
					$absence_policy_id = FALSE;
					$absence_policy = NULL;

					if ( isset($holiday_data[$iso_date_stamp]) ) {
						//We have to assume they are eligible, because we really won't know
						//if they will have worked enough days or not. We could assume they
						//work whatever their schedule is, but chances are they will be eligible then anyways.
						Debug::text('&nbsp;&nbsp;Found Holiday on this day...',__FILE__, __LINE__, __METHOD__, 10);
						$status_id = $holiday_data[$iso_date_stamp]['status_id'];
						if ( isset($holiday_data[$iso_date_stamp]['absence_policy_id']) ) {
							$absence_policy_id = $holiday_data[$iso_date_stamp]['absence_policy_id'];
							$absence_policy = $holiday_data[$iso_date_stamp]['absence_policy'];
						}
					}

					//Debug::text('I: '. $i .' User ID: '. $this->getColumn('user_id') .' Current Date: '. TTDate::getDate('DATE+TIME', $i) .' Current Week: '. $current_week .' Start Time: '. TTDate::getDate('DATE+TIME', $start_time ),__FILE__, __LINE__, __METHOD__, 10);
					$shifts[$iso_date_stamp][$this->getColumn('user_id').$start_time] = array(
														'user_id' => $this->getColumn('user_id'),
														'user_full_name' => Misc::getFullName( $this->getColumn('first_name'), NULL, $this->getColumn('last_name'), FALSE, FALSE ),
														'user_created_by' => $this->getColumn('user_created_by'),
														'status_id' => $status_id,

														'date_stamp' => TTDate::getAPIDate('DATE', $start_time ),
														'start_date' => ( defined('TIMETREX_API') ) ? TTDate::getAPIDate('DATE+TIME', $start_time ) : $start_time,
														'end_date' => ( defined('TIMETREX_API') ) ? TTDate::getAPIDate('DATE+TIME', $end_time ) : $end_time,
														'start_time' => ( defined('TIMETREX_API') ) ? TTDate::getAPIDate('TIME', $start_time ) : $start_time,
														'end_time' => ( defined('TIMETREX_API') ) ? TTDate::getAPIDate('TIME', $end_time ) : $end_time,

														//These are no longer used.
														//'raw_start_time' => TTDate::getDate('DATE+TIME', $start_time ),
														//'raw_end_time' => TTDate::getDate('DATE+TIME', $end_time ),

														'total_time' => $this->getTotalTime(),
														'schedule_policy_id' => $this->getSchedulePolicyID(),
														'absence_policy_id' => $absence_policy_id,
														'absence_policy' => $absence_policy,
														'branch_id' => $this->getColumn('schedule_branch_id'),
														'branch' => Option::getByKey($this->getColumn('schedule_branch_id'), $branch_options, NULL ),
														'department_id' => $this->getColumn('schedule_department_id'),
														'department' =>  Option::getByKey($this->getColumn('schedule_department_id'), $department_options, NULL ),
														'job_id' => $this->getJob(),
														'job_item_id' => $this->getJobItem(),
														);
					$shifts_index[$iso_date_stamp][$this->getColumn('user_id')][] = $this->getColumn('user_id').$start_time;

					unset($start_time, $end_time);
				} else {
					//Debug::text('&nbsp;&nbsp;NOT active shift on this day... ID: '. $this->getColumn('id') .' User ID: '. $this->getColumn('user_id') .' Start Time: '. TTDate::getDate('DATE+TIME', $i),__FILE__, __LINE__, __METHOD__, 10);
				}
			}
		}

		if ( isset($shifts) ) {
			//Debug::Arr($shifts, 'Template Shifts: ',__FILE__, __LINE__, __METHOD__, 10);
			return $shifts;
		}

		return FALSE;
	}

	function getObjectAsArray( $raw_dates = FALSE ) {
		/*
			array( 'date_stamp' => array( 0 => ALLDATA 1=> ALLDATA ) );
		*/

		//Calculate offset for day of the week
		$data = array(
						'week' => $this->getWeek(),
						'days' => array(
										'sun' => $this->getSun(),
										'mon' => $this->getMon(),
										'tue' => $this->getTue(),
										'wed' => $this->getWed(),
										'thu' => $this->getThu(),
										'fri' => $this->getFri(),
										'sat' => $this->getSat(),
										),
						'status_id' => 10, //Working
						'start_time' => $this->getStartTime( $raw_dates ), //Get Raw value
						'end_time' => $this->getEndTime( $raw_dates ),
						'total_time' => $this->getTotalTime(),
						'schedule_policy_id' => $this->getSchedulePolicyID(),
						'branch_id' => $this->getBranch(),
						'department_id' => $this->getDepartment(),
						'job_id' => $this->getJob(),
						'job_item_id' => $this->getJobItem(),
					);

		return $data;
	}

	function preSave() {
		if ( $this->getEndTime() < $this->getStartTime() ) {
			Debug::Text('EndTime spans midnight boundary! Increase by 24hrs ', __FILE__, __LINE__, __METHOD__,10);
			$this->setEndTime( $this->getEndTime() + 86400 ); //End time spans midnight, add 24hrs.
		}
	}
}
?>
