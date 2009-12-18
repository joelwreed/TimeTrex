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
 * $Id: RecurringScheduleControlFactory.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */

/**
 * @package Module_Schedule
 */
class RecurringScheduleControlFactory extends Factory {
	protected $table = 'recurring_schedule_control';
	protected $pk_sequence_name = 'recurring_schedule_control_id_seq'; //PK Sequence name

	protected $recurring_schedule_template_obj = NULL;
	function getCompany() {
		if ( isset($this->data['company_id']) ) {
			return $this->data['company_id'];
		}

		return FALSE;
	}
	function setCompany($id) {
		$id = trim($id);

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

	function getStartWeek() {
		if ( isset($this->data['start_week']) ) {
			return (int)$this->data['start_week'];
		}

		return FALSE;
	}
	function setStartWeek($int) {
		$int = trim($int);

		if 	(	$int > 0
				AND
				$this->Validator->isNumeric(		'week',
													$int,
													TTi18n::gettext('Week is invalid')) ) {
			$this->data['start_week'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getStartDate( $raw = FALSE ) {
		if ( isset($this->data['start_date']) ) {
			if ( $raw === TRUE ) {
				return $this->data['start_date'];
			} else {
				return TTDate::strtotime( $this->data['start_date'] );
			}
		}

		return FALSE;
	}
	function setStartDate($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'start_date',
												$epoch,
												TTi18n::gettext('Incorrect start date'))
			) {

			$this->data['start_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getEndDate( $raw = FALSE ) {
		if ( isset($this->data['end_date']) ) {
			if ( $raw === TRUE ) {
				return $this->data['end_date'];
			} else {
				return TTDate::strtotime( $this->data['end_date'] );
			}
		}

		return FALSE;
	}
	function setEndDate($epoch) {
		$epoch = trim($epoch);

		if ( $epoch == '' ){
			$epoch = NULL;
		}

		if 	(	$epoch == NULL
				OR
				$this->Validator->isDate(		'end_date',
												$epoch,
												TTi18n::gettext('Incorrect end date'))
			) {

			$this->data['end_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getAutoFill() {
		if ( isset($this->data['auto_fill']) ) {
			return $this->fromBool( $this->data['auto_fill'] );
		}

		return FALSE;
	}
	function setAutoFill($bool) {
		$this->data['auto_fill'] = $this->toBool($bool);

		return true;
	}

	function getUser() {
		$rsulf = new RecurringScheduleUserListFactory();
		$rsulf->getByRecurringScheduleControlId( $this->getId() );
		foreach ($rsulf as $obj) {
			$list[] = $obj->getUser();
		}

		if ( isset($list) ) {
			return $list;
		}

		return FALSE;
	}
	function setUser($ids) {
		Debug::text('Setting User IDs : ', __FILE__, __LINE__, __METHOD__, 10);
		if (is_array($ids) and count($ids) > 0) {
			if ( !$this->isNew() ) {
				//If needed, delete mappings first.
				$rsulf = new RecurringScheduleUserListFactory();
				$rsulf->getByRecurringScheduleControlId( $this->getId() );

				$tmp_ids = array();
				foreach ($rsulf as $obj) {
					$id = $obj->getUser();
					Debug::text('Recurring Schedule ID: '. $obj->getRecurringScheduleControl() .' ID: '. $id, __FILE__, __LINE__, __METHOD__, 10);

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
			$ulf = new UserListFactory();

			foreach ($ids as $id) {
				if ( isset($ids) AND !in_array($id, $tmp_ids) ) {
					$ulf->getById( $id );
					if ( $ulf->getRecordCount() > 0 ) {
						$obj = $ulf->getCurrent();

						$rsuf = new RecurringScheduleUserFactory();
						$rsuf->setRecurringScheduleControl( $this->getId() );
						$rsuf->setUser( $id );

						if ( $this->Validator->isTrue(		'user',
															$rsuf->Validator->isValid(),
															TTi18n::gettext('Selected Employee is invalid').' ('. $obj->getFullName() .')' )) {
							$rsuf->save();
						}
					}
				}
			}

			return TRUE;
		}

		Debug::text('No User IDs to set.', __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
	}

	function reMapWeek( $current, $start, $max ) {
		 return ((($current-1)+$max-($start-1))%$max) + 1;
	}

	function ReMapWeeks($week_arr) {
		//We should be able to re-map weeks with simple math:
		//For example:
		//  Start Week = 3, Max Week = 5
		// If template week is less then start week, we add the start week.
		// If template week is greater or equal then start week, we minus the 1-start_week.
		//  Template Week 1 -- 1 + 3(start week)   = ReMapped Week 4
		//  Template Week 2 -- 2 + 3               = ReMapped Week 5
		//  Template Week 3 -- 3 - 2(start week-1) = ReMapped Week 1
		//  Template Week 4 -- 4 - 2               = ReMapped Week 2
		//  Template Week 5 -- 5 - 2               = ReMapped Week 3

		//Remaps weeks based on start week
		Debug::text('Start Week: '.  $this->getStartWeek(), __FILE__, __LINE__, __METHOD__, 10);

		if ( $this->getStartWeek() > 1 AND in_array( $this->getStartWeek(), $week_arr) ) {
			Debug::text('Weeks DO need reordering: ', __FILE__, __LINE__, __METHOD__, 10);
			$max_week = count($week_arr);

			$i=1;
			foreach( $week_arr as $key => $val ) {
				$new_val = $key - ($this->getStartWeek()-1);

				if ( $key < $this->getStartWeek() ) {
					$new_val = $new_val + $max_week;
				}

				$arr[$new_val] = $key;

				$i++;
			}
			//var_dump($arr);
			return $arr;
		}

		Debug::text('Weeks do not need reordering: ', __FILE__, __LINE__, __METHOD__, 10);

		return $week_arr;
	}

	//Used when taking recurring schedules and committing shifts for them in maintenance jobs.
	function getShiftsByStartDateAndEndDate($start_date, $end_date) {

		//Make sure timezone isn't in the time format. Because recurring schedules
		//are timezone agnostic. 7:00AM in PST is also 7:00AM in EST.
		//This causes an issue where the previous users timezone carries over to the next
		//users timezone, causing errors.
		//TTDate::setTimeFormat('g:i A');

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		if ( $start_date < $this->getStartDate() ) {
			$start_date = $this->getStartDate();
		}

		if ( $this->getEndDate(TRUE) != NULL AND $end_date > $this->getEndDate() ) {
			$end_date = $this->getEndDate();
		}
		Debug::text('Start Date: '. TTDate::getDate('DATE+TIME', $start_date) .' End Date: '. TTDate::getDate('DATE+TIME', $end_date), __FILE__, __LINE__, __METHOD__, 10);

		//Get week data
		$rstlf = new RecurringScheduleTemplateListFactory();
		$rstlf->getByRecurringScheduleTemplateControlId( $this->getRecurringScheduleTemplateControl() )->getCurrent();
		$max_week = 1;
		$weeks = array();
		if ( $rstlf->getRecordCount() > 0 ) {
			foreach($rstlf as $rst_obj) {
				Debug::text('Week: '. $rst_obj->getWeek(), __FILE__, __LINE__, __METHOD__, 10);
				$template_week_rows[$rst_obj->getWeek()][] = $rst_obj->getObjectAsArray();

				$weeks[$rst_obj->getWeek()] = $rst_obj->getWeek();

				if ( $rst_obj->getWeek() > $max_week ) {
					$max_week = $rst_obj->getWeek();
				}
			}
		}

		$weeks = $this->ReMapWeeks( $weeks );

		//Get week of start_date
		$start_date_week = TTDate::getWeek( $this->getStartDate(), 0 ); //Start week on Sunday to match Recurring Schedule.
		Debug::text('Week of Start Date: '. $start_date_week ,__FILE__, __LINE__, __METHOD__, 10);

		for ( $i=$start_date; $i <= $end_date; $i+=86400) {
			$current_week = TTDate::getWeek( $i, 0 ); //Start week on Sunday to match Recurring Schedule.
			//Debug::text('Current Date: '. TTDate::getDate('DATE+TIME', $i) .' Current Week: '. $current_week,__FILE__, __LINE__, __METHOD__, 10);

			$template_week = ( ( ( abs($current_week-$start_date_week) ) % $max_week ) ) + 1;
			//Debug::text('Template Week: '. $template_week .' Max Week: '. $max_week,__FILE__, __LINE__, __METHOD__, 10);

			$day_of_week = strtolower(date('D', $i));
			//Debug::text('Day Of Week: '. $day_of_week,__FILE__, __LINE__, __METHOD__, 10);

			if ( isset($weeks[$template_week] ) ) {
				$mapped_template_week = $weeks[$template_week];
				//Debug::text('&nbsp;&nbsp;Mapped Template Week: '. $mapped_template_week,__FILE__, __LINE__, __METHOD__, 10);

				if ( isset($template_week_rows[$mapped_template_week]) ) {
					//Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Starting Looping...!',__FILE__, __LINE__, __METHOD__, 10);

					foreach( $template_week_rows[$mapped_template_week] as $template_week_arr ) {
						if ( $template_week_arr['days'][$day_of_week] == TRUE ) {
							//Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Found Scheduled Time: Start Time: '. TTDate::getDate('DATE+TIME', TTDate::getTimeLockedDate( $template_week_arr['start_time'], $i ) ),__FILE__, __LINE__, __METHOD__, 10);

							$start_time = TTDate::getTimeLockedDate( $template_week_arr['start_time'], $i );
							$end_time = TTDate::getTimeLockedDate( $template_week_arr['end_time'], $i );
							if ( $end_time < $start_time ) {
								//Spans the day boundary, add 86400 to end_time
								$end_time = $end_time + 86400;
								//Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Schedule spans day boundary, bumping endtime to next day: ',__FILE__, __LINE__, __METHOD__, 10);
							}
							//Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Start Date: '. TTDate::getDate('DATE+TIME', $start_time) .' End Date: '. TTDate::getDate('DATE+TIME', $end_time),__FILE__, __LINE__, __METHOD__, 10);

							//$shifts[TTDate::getBeginDayEpoch($i)][] = array(
							$shifts[TTDate::getISODateStamp($i)][] = array(
																'status_id' => $template_week_arr['status_id'],
																'start_time' => $start_time,
																'raw_start_time' => TTDate::getDate('DATE+TIME', $start_time ),
																'end_time' => $end_time,
																'raw_end_time' => TTDate::getDate('DATE+TIME', $end_time ),
																'total_time' => $template_week_arr['total_time'],
																'schedule_policy_id' => $template_week_arr['schedule_policy_id'],
																'branch_id' => $template_week_arr['branch_id'],
																'department_id' => $template_week_arr['department_id'],
																'job_id' => $template_week_arr['job_id'],
																'job_item_id' => $template_week_arr['job_item_id']
																);
							unset($start_time, $end_time);
						} else {
							//Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;aSkipping!',__FILE__, __LINE__, __METHOD__, 10);
						}
					}

				} else {
					//Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;bSkipping!',__FILE__, __LINE__, __METHOD__, 10);
				}
			} else {
				//Debug::text('&nbsp;&nbsp;cSkipping!',__FILE__, __LINE__, __METHOD__, 10);
			}

		}

		//var_dump($shifts);
		if ( isset($shifts) ) {
			return $shifts;
		}

		return FALSE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Recurring Schedule'), NULL, $this->getTable() );
	}
}
?>
