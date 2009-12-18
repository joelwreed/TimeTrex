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
 * $Revision: 3021 $
 * $Id: PunchControlFactory.class.php 3021 2009-11-11 23:33:03Z ipso $
 * $Date: 2009-11-11 15:33:03 -0800 (Wed, 11 Nov 2009) $
 */

/**
 * @package Module_Punch
 */
class PunchControlFactory extends Factory {
	protected $table = 'punch_control';
	protected $pk_sequence_name = 'punch_control_id_seq'; //PK Sequence name

	protected $tmp_data = NULL;
	protected $old_user_date_ids = array();
	protected $shift_data = NULL;

	protected $user_date_obj = NULL;
	protected $pay_period_schedule_obj = NULL;
	protected $job_obj = NULL;
	protected $job_item_obj = NULL;
	protected $meal_policy_obj = NULL;
	protected $punch_obj = NULL;

	protected $plf = NULL;
	protected $is_total_time_calculated = FALSE;

	function getUserDateObject( $id = NULL ) {
		if ( $id == '' AND is_object( $this->user_date_obj ) ) {
			return $this->user_date_obj;
		} else {
			if ( $id == '' ) {
				$id = $this->getUserDateID();
			}

			$udlf = new UserDateListFactory();
			$udlf->getById( $id );
			if ( $udlf->getRecordCount() > 0 ) {
				$this->user_date_obj = $udlf->getCurrent();
				return $this->user_date_obj;
			}

			return FALSE;
		}
	}

	function getPLFByPunchControlID() {
		if ( $this->plf == NULL AND $this->getID() != FALSE ) {
			$this->plf = new PunchListFactory();
			$this->plf->getByPunchControlID( $this->getID() );
		}

		return $this->plf;
	}

	function getPayPeriodScheduleObject() {
		if ( is_object($this->pay_period_schedule_obj) ) {
			return $this->pay_period_schedule_obj;
		} else {
			if ( $this->getUser() > 0 ) {
				$ppslf = new PayPeriodScheduleListFactory();
				$ppslf->getByUserId( $this->getUser() );
				if ( $ppslf->getRecordCount() == 1 ) {
					$this->pay_period_schedule_obj = $ppslf->getCurrent();
					return $this->pay_period_schedule_obj;
				}
			}

			return FALSE;
		}
	}

	function getShiftData() {
		if ( $this->shift_data == NULL AND is_object( $this->getPunchObject() ) AND $this->getUser() > 0 ) {
			if ( is_object( $this->getPayPeriodScheduleObject() ) ) {
				$this->shift_data = $this->getPayPeriodScheduleObject()->getShiftData( NULL, $this->getUser(), $this->getPunchObject()->getTimeStamp(), 'nearest_shift', $this );
			} else {
				Debug::Text('No pay period schedule found for user ID: '. $this->getUser(), __FILE__, __LINE__, __METHOD__,10);
			}
		}

		return $this->shift_data;
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

	function getPunchObject() {
		if ( is_object($this->punch_obj) ) {
			return $this->punch_obj;
		}

		return FALSE;
	}
	function setPunchObject($obj) {
		if ( is_object($obj) ) {
			$this->punch_obj = $obj;

			return TRUE;
		}

		return FALSE;
	}

	function getUser() {
		$user_id = FALSE;
		if ( is_object( $this->getPunchObject() ) AND $this->getPunchObject()->getUser() != FALSE ) {
			$user_id = $this->getPunchObject()->getUser();
		} elseif ( is_object( $this->getUserDateObject() ) ) {
			$user_id = $this->getUserDateObject()->getUser();
		}

		return $user_id;
	}

	//This must be called after PunchObject() has been set and before isValid() is called.
	function findUserDate() {
		/*
			Issues to consider:
				** Timezones, if one employee is in PST and the payroll administrator/pay period is in EST, if the employee
				** punches in at 11:00PM PST, its actually 2AM EST on the next day, so which day does the time get assigned to?
				** Use the employees preferred timezone to determine the proper date, otherwise if we use the PP schedule timezone it may
				** be a little confusing to employees because they may punch in on one day and have the time appears under different day.

				1. Employee punches out at 11:00PM, then is called in early at 4AM to start a new shift.
				Don't want to pair these punches.

				2. Employee starts 11:00PM shift late at 1:00AM the next day. Works until 7AM, then comes in again
				at 11:00PM the same day and works until 4AM, then 4:30AM to 7:00AM. The 4AM-7AM punches need to be paired on the same day.

				3. Ambulance EMT works 36hours straight in a single punch.

				*Perhaps we should handle lunch punches and normal punches differently? Lunch punches have
				a different "continuous time setting then normal punches.

				*Change daily continuous time to:
				* Group (Normal) Punches: X hours before midnight to punches X hours after midnight
				* Group (Lunch/Break) Punches: X hours before midnight to punches X hours after midnight
				*	Normal punches X hours after midnight group to punches X hours before midnight.
				*	Lunch/Break punches X hours after midnight group to punches X hours before midnight.

				OR, what if we change continuous time to be just the gap between punches that cause
					a new day to start? Combine this with daily cont. time so we know what the window
					is for punches to begin the gap search. Or we can always just search for a previous
					punch Xhrs before the current punch.
					- Do we look back to a In punch, or look back to an Out punch though? I think an Out Punch.
						What happens if they forgot to punch out though?
					Logic:
						If this is an Out punch:
							Find previous punch back to maximum shift time to find an In punch to pair it with.
						Else, if this is an In punch:
							Find previous punch back to maximum shift time to find an Out punch to combine it with.
							If out punch is found inside of new_shift trigger time, we place this punch on the previous day.
							Else: we place this punch on todays date.


				* Minimum time between punches to cause a new shift to start: Xhrs (default: 4hrs)
					new_day_trigger_time
					Call it: Minimum time-off that triggers new shift:
						Minimum Time-Off Between Shifts:
				* Maximum shift time: Xhrs (for ambulance service) default to 16 or 24hrs?
					This is essentially how far back we look for In punch to pair out punches with.
					maximum_shift_length
					- Add checks to ensure that no punch pair exceeds the maximum_shift_length
		*/

		/*
		 This needs to be able to run before Validate is called, so we can validate the pay period schedule.
		*/
		if ( $this->getUserDateID() == FALSE ) {
			$this->setUserDate( $this->getUser(), $this->getPunchObject()->getTimeStamp() );
		}

		Debug::Text(' Finding User Date ID: '. TTDate::getDate('DATE+TIME', $this->getPunchObject()->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);
		$shift_data = $this->getShiftData();

		if ( is_array($shift_data) ) {
			switch ( $this->getPayPeriodScheduleObject()->getShiftAssignedDay() ) {
				default:
				case 10: //Day they start on
				case 40: //Split at midnight
					if ( !isset($shift_data['first_in']['time_stamp']) ) {
						$shift_data['first_in']['time_stamp'] = $shift_data['last_out']['time_stamp'];
					}
					//Can't use the First In user_date_id because it may need to be changed when editing a punch.
					Debug::Text('Assign Shifts to the day they START on... Date: '. TTDate::getDate('DATE', $shift_data['first_in']['time_stamp']) , __FILE__, __LINE__, __METHOD__,10);
					$user_date_id = UserDateFactory::findOrInsertUserDate( $this->getUser(), $shift_data['first_in']['time_stamp'] );
					break;
				case 20: //Day they end on
					if ( !isset($shift_data['last_out']['time_stamp']) ) {
						$shift_data['last_out']['time_stamp'] = $shift_data['first_in']['time_stamp'];
					}
					Debug::Text('Assign Shifts to the day they END on... Date: '. TTDate::getDate('DATE', $shift_data['last_out']['time_stamp']) , __FILE__, __LINE__, __METHOD__,10);
					$user_date_id = UserDateFactory::findOrInsertUserDate( $this->getUser(), $shift_data['last_out']['time_stamp'] );
					break;
				case 30: //Day with most time worked
					Debug::Text('Assign Shifts to the day they WORK MOST on... Date: '. TTDate::getDate('DATE', $shift_data['day_with_most_time']) , __FILE__, __LINE__, __METHOD__,10);
					$user_date_id = UserDateFactory::findOrInsertUserDate( $this->getUser(), $shift_data['day_with_most_time'] );
					break;
			}

			if ( isset($user_date_id) AND $user_date_id > 0 ) {
				Debug::Text('Found UserDateID: '. $user_date_id, __FILE__, __LINE__, __METHOD__,10);
				return $this->setUserDateID( $user_date_id );
			}
		}

		Debug::Text('No shift data to use to find UserDateID, using timestamp only: '. TTDate::getDate('DATE+TIME', $this->getPunchObject()->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);
		return TRUE;
	}

/*
	function findUserDate($user_id = NULL, $epoch = NULL, $status_id = 10, $punch_id = 0 ) {
		//
			Issues to consider:
				1. Employee punches out at 11:00PM, then is called in early at 4AM to start a new shift.
				Don't want to pair these punches.

				2. Employee starts 11:00PM shift late at 1:00AM the next day. Works until 7AM, then comes in again
				at 11:00PM the same day and works until 4AM, then 4:30AM to 7:00AM. The 4AM-7AM punches need to be paired on the same day.

				3. Ambulance EMT works 36hours straight in a single punch.

				*Perhaps we should handle lunch punches and normal punches differently? Lunch punches have
				a different "continuous time setting then normal punches.

				*Change daily continuous time to:
				* Group (Normal) Punches: X hours before midnight to punches X hours after midnight
				* Group (Lunch/Break) Punches: X hours before midnight to punches X hours after midnight
				*	Normal punches X hours after midnight group to punches X hours before midnight.
				*	Lunch/Break punches X hours after midnight group to punches X hours before midnight.

				OR, what if we change continuous time to be just the gap between punches that cause
					a new day to start? Combine this with daily cont. time so we know what the window
					is for punches to begin the gap search. Or we can always just search for a previous
					punch Xhrs before the current punch.
					- Do we look back to a In punch, or look back to an Out punch though? I think an Out Punch.
						What happens if they forgot to punch out though?
					Logic:
						If this is an Out punch:
							Find previous punch back to maximum shift time to find an In punch to pair it with.
						Else, if this is an In punch:
							Find previous punch back to maximum shift time to find an Out punch to combine it with.
							If out punch is found inside of new_shift trigger time, we place this punch on the previous day.
							Else: we place this punch on todays date.


				* Minimum time between punches to cause a new shift to start: Xhrs (default: 4hrs)
					new_day_trigger_time
					Call it: Minimum time-off that triggers new shift:
						Minimum Time-Off Between Shifts:
				* Maximum shift time: Xhrs (for ambulance service) default to 16 or 24hrs?
					This is essentially how far back we look for In punch to pair out punches with.
					maximum_shift_length
					- Add checks to ensure that no punch pair exceeds the maximum_shift_length

		//
		if ( is_object( $this->getPunchObject() ) ) {
			if ( $this->getPunchObject()->getUser() != FALSE ) {
				$user_id = $this->getPunchObject()->getUser();
			} elseif ( is_object( $this->getUserDateObject() ) ) {
				$user_id = $this->getUserDateObject()->getUser();
			}

			$epoch = $this->getPunchObject()->getTimeStamp();
			//Get pay period new_day_trigger/maximum_shift time
			$ppslf = new PayPeriodScheduleListFactory();
			$ppslf->getByUserId( $user_id );
			if ( $ppslf->getRecordCount() == 1 ) {
				$pps_obj = $ppslf->getCurrent();
				Debug::Text(' Pay Period Schedule Maximum Shift Time: '. $pps_obj->getMaximumShiftTime() .' Shift Assigned Day: '. $pps_obj->getShiftAssignedDay(), __FILE__, __LINE__, __METHOD__,10);

				$this->shift_data = $pps_obj->getShiftData( NULL, $user_id, $epoch, 'nearest_shift', $this);

				if ( is_array($this->shift_data) ) {
					switch ( $pps_obj->getShiftAssignedDay() ) {
						case 10: //Day they start on
							//Can't use the First In user_date_id because it may need to be changed when editing a punch.
							Debug::Text('Assign Shifts to the day they START on... Date: '. TTDate::getDate('DATE', $this->shift_data['first_in']['time_stamp']) , __FILE__, __LINE__, __METHOD__,10);
							$user_date_id = UserDateFactory::findOrInsertUserDate( $user_id, $this->shift_data['first_in']['time_stamp'] );
							break;
						case 20: //Day they end on
							Debug::Text('Assign Shifts to the day they END on... Date: '. TTDate::getDate('DATE', $this->shift_data['last_out']['time_stamp']) , __FILE__, __LINE__, __METHOD__,10);
							$user_date_id = UserDateFactory::findOrInsertUserDate( $user_id, $this->shift_data['last_out']['time_stamp'] );
							break;
						case 30: //Day with most time worked
							Debug::Text('Assign Shifts to the day they WORK MOST on... Date: '. TTDate::getDate('DATE', $this->shift_data['day_with_most_time']) , __FILE__, __LINE__, __METHOD__,10);
							$user_date_id = UserDateFactory::findOrInsertUserDate( $user_id, $this->shift_data['day_with_most_time'] );
							break;
						case 40: //Split at midnight
							Debug::Text('Assign Shifts to each day (split at midnight)...', __FILE__, __LINE__, __METHOD__,10);
							break;
					}
					Debug::Text('Assigning to User Date ID: '. $user_date_id, __FILE__, __LINE__, __METHOD__,10);

					if ( isset($user_date_id) AND $user_date_id > 0 ) {
						if ( $this->getUserDateID() != FALSE AND $this->getUserDateID() != $user_date_id ) {
							$this->setOldUserDateID( $this->getUserDateID() );
						}
						$this->setUserDateID( $user_date_id );

						return TRUE;
					}
				}
			}

			Debug::Text(' Pay Period Schedule Record Count: '. $ppslf->getRecordCount() .' Epoch: '. TTDate::getDate('DATE+TIME', $epoch)  , __FILE__, __LINE__, __METHOD__,10);
			Debug::Text('bSkipping find...', __FILE__, __LINE__, __METHOD__,10);
			return $this->setUserDate( $user_id, $epoch );
		}

		Debug::Text('Returning FALSE!', __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}
*/
/*
	function findUserDate($user_id, $epoch, $status_id = 10, $punch_id = 0 ) {
		//
			Issues to consider:
				1. Employee punches out at 11:00PM, then is called in early at 4AM to start a new shift.
				Don't want to pair these punches.

				2. Employee starts 11:00PM shift late at 1:00AM the next day. Works until 7AM, then comes in again
				at 11:00PM the same day and works until 4AM, then 4:30AM to 7:00AM. The 4AM-7AM punches need to be paired on the same day.

				3. Ambulance EMT works 36hours straight in a single punch.

				*Perhaps we should handle lunch punches and normal punches differently? Lunch punches have
				a different "continuous time setting then normal punches.

				*Change daily continuous time to:
				* Group (Normal) Punches: X hours before midnight to punches X hours after midnight
				* Group (Lunch/Break) Punches: X hours before midnight to punches X hours after midnight
				*	Normal punches X hours after midnight group to punches X hours before midnight.
				*	Lunch/Break punches X hours after midnight group to punches X hours before midnight.

				OR, what if we change continuous time to be just the gap between punches that cause
					a new day to start? Combine this with daily cont. time so we know what the window
					is for punches to begin the gap search. Or we can always just search for a previous
					punch Xhrs before the current punch.
					- Do we look back to a In punch, or look back to an Out punch though? I think an Out Punch.
						What happens if they forgot to punch out though?
					Logic:
						If this is an Out punch:
							Find previous punch back to maximum shift time to find an In punch to pair it with.
						Else, if this is an In punch:
							Find previous punch back to maximum shift time to find an Out punch to combine it with.
							If out punch is found inside of new_shift trigger time, we place this punch on the previous day.
							Else: we place this punch on todays date.


				* Minimum time between punches to cause a new shift to start: Xhrs (default: 4hrs)
					new_day_trigger_time
					Call it: Minimum time-off that triggers new shift:
						Minimum Time-Off Between Shifts:
				* Maximum shift time: Xhrs (for ambulance service) default to 16 or 24hrs?
					This is essentially how far back we look for In punch to pair out punches with.
					maximum_shift_length
					- Add checks to ensure that no punch pair exceeds the maximum_shift_length

		//
		//Get pay period new_day_trigger/maximum_shift time
		$ppslf = new PayPeriodScheduleListFactory();
		$ppslf->getByUserId( $user_id );
		if ( $ppslf->getRecordCount() == 1 ) {
			$pps_obj = $ppslf->getCurrent();
			Debug::Text(' Pay Period Schedule Maximum Shift Time: '. $pps_obj->getMaximumShiftTime(), __FILE__, __LINE__, __METHOD__,10);

			//If this is an out punch, we need to find the timestamp of the In punch to base
			//maximum shift time off of.
			$plf = new PunchListFactory();
			if ( $status_id == 20 ) {
				//getPreviousPunchByUserIdAndEpoch uses <= $epoch, so we minus one second from it
				//so it doesn't find the punch we may be editing.
				$plf->getPreviousPunchByUserIdAndEpochAndNotPunchIDAndMaximumShiftTime( $user_id, $epoch-1, $punch_id, $pps_obj->getMaximumShiftTime() );
				if ( $plf->getRecordCount() > 0 ) {
					$p_obj = $plf->getCurrent();

					//Make sure the previous punch is an In punch only. This fixes the bug where the employee forgot
					//to punch In the next day after a regular shift, so the first punch of the day was an Out punch
					//causing TimeTrex to place it on the previous day.
					if ( $p_obj->getStatus() == 10 ) {
						$epoch = $p_obj->getTimeStamp();
						Debug::Text('Found In Punch Epoch: '. $epoch .' - '. TTDate::getDate('DATE+TIME', $epoch) , __FILE__, __LINE__, __METHOD__,10);
					} else {
						Debug::Text('Previous punch was an Out punch, not pairing with it.', __FILE__, __LINE__, __METHOD__,10);
					}
					unset($p_obj);
				} else {
					Debug::Text('DID NOT Find In Punch Epoch: '. $epoch, __FILE__, __LINE__, __METHOD__,10);
				}
			}

			//Get previous punch, if its an Out punch, make sure this punch is within the new_day_trigger_time to be grouped
			//on the same day as it.
			$plf->getPreviousPunchByUserIdAndEpochAndNotPunchIDAndMaximumShiftTime( $user_id, $epoch-1, $punch_id, $pps_obj->getNewDayTriggerTime() );
			if ( $plf->getRecordCount() > 0 ) {
				$p_obj = $plf->getCurrent();
				//Debug::Text('Current Punch Epoch: '. $epoch .' First Punch Epoch: '.$p_obj->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);

				if ( ( $epoch - $p_obj->getTimeStamp() ) <= $pps_obj->getNewDayTriggerTime() ) {
					$user_date_id = $p_obj->getPunchControlObject()->getUserDateID();
					Debug::text(' User Date ID found: '. $user_date_id, __FILE__, __LINE__, __METHOD__,10);

					$this->setUserDateID( $user_date_id );
					return TRUE;
				}
			}
		}

		Debug::Text('Skipping Find...', __FILE__, __LINE__, __METHOD__,10);
		Debug::Text(' Pay Period Schedule Record Count: '. $ppslf->getRecordCount() .' Epoch: '. TTDate::getDate('DATE+TIME', $epoch)  , __FILE__, __LINE__, __METHOD__,10);

		return $this->setUserDate( $user_id, $epoch );
	}
*/

	function setUserDate($user_id, $date) {
		$user_date_id = UserDateFactory::findOrInsertUserDate( $user_id, $date );
		Debug::text(' User Date ID: '. $user_date_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $user_date_id != '' ) {
			$this->setUserDateID( $user_date_id );
			return TRUE;
		}
		Debug::text(' No User Date ID found', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function getOldUserDateID() {
		if ( isset($this->tmp_data['old_user_date_id']) ) {
			return $this->tmp_data['old_user_date_id'];
		}

		return FALSE;
	}
	function setOldUserDateID($id) {
		Debug::Text(' Setting Old User Date ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$this->tmp_data['old_user_date_id'] = $id;

		return TRUE;
	}

	function getUserDateID() {
		if ( isset($this->data['user_date_id']) ) {
			return $this->data['user_date_id'];
		}

		return FALSE;
	}

	function setUserDateID( $id ) {
		$id = trim($id);

		$udlf = new UserDateListFactory();
		if (  $this->Validator->isResultSetWithRows(	'user_date',
														$udlf->getByID($id),
														TTi18n::gettext('Invalid User Date ID')
														) ) {

			if ( $this->getUserDateID() !== $id AND $this->getOldUserDateID() != $this->getUserDateID() ) {
				Debug::Text(' Setting Old User Date ID... Current Old ID: '. (int)$this->getOldUserDateID() .' Current ID: '. (int)$this->getUserDateID(), __FILE__, __LINE__, __METHOD__,10);
				$this->setOldUserDateID( $this->getUserDateID() );
			}

			$this->data['user_date_id'] = $id;

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

		if ( $int < 0 ) {
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'actual_total_time',
													$int,
													TTi18n::gettext('Incorrect actual total time')) ) {
			$this->data['actual_total_time'] = $int;

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

		if ( $id == '' OR empty($id) ) {
			$id = NULL;
		}

		$mplf = new MealPolicyListFactory();

		if ( $id == NULL
				OR
				$this->Validator->isResultSetWithRows(	'meal_policy',
														$mplf->getByID($id),
														TTi18n::gettext('Meal Policy is invalid')
													) ) {

			$this->data['meal_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getNote() {
		if ( isset($this->data['note']) ) {
			return $this->data['note'];
		}
	}
	function setNote($val) {
		$val = trim($val);

		if 	(	$val == ''
				OR
				$this->Validator->isLength(		'note',
												$val,
												TTi18n::gettext('Note is too short or too long'),
												0,
												1024) ) {

			$this->data['note'] = $val;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID1() {
		if ( isset($this->data['other_id1']) ) {
			return $this->data['other_id1'];
		}

		return FALSE;
	}
	function setOtherID1($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id1',
											$value,
											TTi18n::gettext('Other ID 1 is invalid'),
											1,255) ) {

			$this->data['other_id1'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID2() {
		if ( isset($this->data['other_id2']) ) {
			return $this->data['other_id2'];
		}

		return FALSE;
	}
	function setOtherID2($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id2',
											$value,
											TTi18n::gettext('Other ID 2 is invalid'),
											1,255) ) {

			$this->data['other_id2'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID3() {
		if ( isset($this->data['other_id3']) ) {
			return $this->data['other_id3'];
		}

		return FALSE;
	}
	function setOtherID3($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id3',
											$value,
											TTi18n::gettext('Other ID 3 is invalid'),
											1,255) ) {

			$this->data['other_id3'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID4() {
		if ( isset($this->data['other_id4']) ) {
			return $this->data['other_id4'];
		}

		return FALSE;
	}
	function setOtherID4($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id4',
											$value,
											TTi18n::gettext('Other ID 4 is invalid'),
											1,255) ) {

			$this->data['other_id4'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID5() {
		if ( isset($this->data['other_id5']) ) {
			return $this->data['other_id5'];
		}

		return FALSE;
	}
	function setOtherID5($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id5',
											$value,
											TTi18n::gettext('Other ID 5 is invalid'),
											1,255) ) {

			$this->data['other_id5'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function calcTotalTime( $force = TRUE ) {
		if ( $force == TRUE OR $this->is_total_time_calculated == FALSE ) {
			$this->is_total_time_calculated == TRUE;

			$plf = new PunchListFactory();
			$plf->getByPunchControlId( $this->getId() );
			//Make sure punches are in In/Out pairs before we bother calculating.
			if ( $plf->getRecordCount() > 0 AND ( $plf->getRecordCount() % 2 ) == 0 ) {
				Debug::text(' Found Punches to calculate.', __FILE__, __LINE__, __METHOD__,10);
				$in_pair = FALSE;
				$schedule_obj = NULL;
				foreach( $plf as $punch_obj ) {
					//Check for proper in/out pairs
					//First row should be an Out status (reverse ordering)
					Debug::text(' Punch: Status: '. $punch_obj->getStatus() .' TimeStamp: '. $punch_obj->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);
					if ( $punch_obj->getStatus() == 20 ) {
						Debug::text(' Found Out Status, starting pair: ', __FILE__, __LINE__, __METHOD__,10);
						$out_stamp = $punch_obj->getTimeStamp();
						$out_actual_stamp = $punch_obj->getActualTimeStamp();
						$in_pair = TRUE;
					} elseif ( $in_pair == TRUE ) {
						$punch_obj->setScheduleID( $punch_obj->findScheduleID( NULL, $this->getUser() ) ); //Find Schedule Object for this Punch
						$schedule_obj = $punch_obj->getScheduleObject();
						$in_stamp = $punch_obj->getTimeStamp();
						$in_actual_stamp = $punch_obj->getActualTimeStamp();
						//Got a pair... Totaling.
						Debug::text(' Found a pair... Totaling: ', __FILE__, __LINE__, __METHOD__,10);
						if ( $out_stamp != '' AND $in_stamp != '' ) {
							$total_time = $out_stamp - $in_stamp;
						}
						if ( $out_actual_stamp != '' AND $in_actual_stamp != '' ) {
							$actual_total_time = $out_actual_stamp - $in_actual_stamp;
						}
					}
				}

				if ( isset($total_time) ) {
					Debug::text(' Setting TotalTime...', __FILE__, __LINE__, __METHOD__,10);

					$this->setTotalTime( $total_time );
					$this->setActualTotalTime( $actual_total_time );

					return TRUE;
				}
			} else {
				Debug::text(' No Punches to calculate, or punches arent in pairs. Set total to 0', __FILE__, __LINE__, __METHOD__,10);
				$this->setTotalTime( 0 );
				$this->setActualTotalTime( 0 );

				return TRUE;
			}
		}

		return FALSE;
	}

	function changePreviousPunchType() {
		Debug::text(' Previous Punch to Lunch/Break...', __FILE__, __LINE__, __METHOD__,10);

		if ( is_object( $this->getPunchObject() ) ) {
			if ( $this->getPunchObject()->getType() == 20 AND $this->getPunchObject()->getStatus() == 10 ) {
				Debug::text(' bbPrevious Punch to Lunch...', __FILE__, __LINE__, __METHOD__,10);

				$shift_data = $this->getShiftData();

				if ( isset($shift_data['previous_punch_key'])
						AND isset($shift_data['punches'][$shift_data['previous_punch_key']])
						AND $shift_data['punches'][$shift_data['previous_punch_key']]['type_id'] != 20 ) {
					$previous_punch_arr = $shift_data['punches'][$shift_data['previous_punch_key']];

					Debug::text(' Previous Punch ID: '. $previous_punch_arr['id'], __FILE__, __LINE__, __METHOD__,10);

					if ( $this->getPunchObject()->inMealPolicyWindow( $this->getPunchObject()->getTimeStamp(), $previous_punch_arr['time_stamp'] ) == TRUE ) {
						Debug::text(' Previous Punch needs to change to Lunch...', __FILE__, __LINE__, __METHOD__,10);

						$plf = new PunchListFactory();
						$plf->getById( $previous_punch_arr['id'] );
						if ( $plf->getRecordCount() == 1 ) {
							Debug::text(' Modifying previous punch...', __FILE__, __LINE__, __METHOD__,10);

							$pf = $plf->getCurrent();
							$pf->setType( 20 ); //Lunch
							//If we start re-rounding this punch we have to recalculate the total for the previous punch_control too.
							//$p_obj->setTimeStamp( $p_obj->getTimeStamp() ); //Re-round timestamp now that its a lunch punch.
							if ( $pf->Save( FALSE ) == TRUE ) {
								Debug::text(' Returning TRUE!', __FILE__, __LINE__, __METHOD__,10);

								return TRUE;
							}

						}

					}

				}
			} elseif ( $this->getPunchObject()->getType() == 30 AND $this->getPunchObject()->getStatus() == 10 ) {
				Debug::text(' bbPrevious Punch to Break...', __FILE__, __LINE__, __METHOD__,10);

				$shift_data = $this->getShiftData();

				if ( isset($shift_data['previous_punch_key'])
						AND isset($shift_data['punches'][$shift_data['previous_punch_key']])
						AND $shift_data['punches'][$shift_data['previous_punch_key']]['type_id'] != 30 ) {
					$previous_punch_arr = $shift_data['punches'][$shift_data['previous_punch_key']];

					Debug::text(' Previous Punch ID: '. $previous_punch_arr['id'], __FILE__, __LINE__, __METHOD__,10);

					if ( $this->getPunchObject()->inBreakPolicyWindow( $this->getPunchObject()->getTimeStamp(), $previous_punch_arr['time_stamp'] ) == TRUE ) {
						Debug::text(' Previous Punch needs to change to Break...', __FILE__, __LINE__, __METHOD__,10);

						$plf = new PunchListFactory();
						$plf->getById( $previous_punch_arr['id'] );
						if ( $plf->getRecordCount() == 1 ) {
							Debug::text(' Modifying previous punch...', __FILE__, __LINE__, __METHOD__,10);

							$pf = $plf->getCurrent();
							$pf->setType( 30 ); //Break
							//If we start re-rounding this punch we have to recalculate the total for the previous punch_control too.
							//$p_obj->setTimeStamp( $p_obj->getTimeStamp() ); //Re-round timestamp now that its a lunch punch.
							if ( $pf->Save( FALSE ) == TRUE ) {
								Debug::text(' Returning TRUE!', __FILE__, __LINE__, __METHOD__,10);

								return TRUE;
							}

						}

					}

				}
			}

		}

		Debug::text(' Returning false!', __FILE__, __LINE__, __METHOD__,10);

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

	function getEnableCalcUserDateTotal() {
		if ( isset($this->calc_user_date_total) ) {
			return $this->calc_user_date_total;
		}

		return FALSE;
	}
	function setEnableCalcUserDateTotal($bool) {
		$this->calc_user_date_total = $bool;

		return TRUE;
	}
	function getEnableCalcUserDateID() {
		if ( isset($this->calc_user_date_id) ) {
			return $this->calc_user_date_id;
		}

		return FALSE;
	}
	function setEnableCalcUserDateID($bool) {
		$this->calc_user_date_id = $bool;

		return TRUE;
	}

	function getEnableCalcTotalTime() {
		if ( isset($this->calc_total_time) ) {
			return $this->calc_total_time;
		}

		return FALSE;
	}
	function setEnableCalcTotalTime($bool) {
		$this->calc_total_time = $bool;

		return TRUE;
	}

	function getEnableStrictJobValidation() {
		if ( isset($this->strict_job_validiation) ) {
			return $this->strict_job_validiation;
		}

		return FALSE;
	}
	function setEnableStrictJobValidation($bool) {
		$this->strict_job_validiation = $bool;

		return TRUE;
	}

	function Validate() {
		Debug::text('Validating...', __FILE__, __LINE__, __METHOD__,10);

		//Call this here so getShiftData can get the correct total time, before we call findUserDate.
		if ( $this->getEnableCalcTotalTime() == TRUE ) {
			$this->calcTotalTime();
		}

		if ( is_object( $this->getPunchObject() ) ) {
			$this->findUserDate();
		}
		Debug::text('User Date Id: '. $this->getUserDateID(), __FILE__, __LINE__, __METHOD__,10);

		if ( $this->getUserDateObject() == FALSE OR $this->getUserDateObject()->getPayPeriodObject() == FALSE ) {
			$this->Validator->isTRUE(	'pay_period',
										FALSE,
										TTi18n::gettext('Date/Time is incorrect, or pay period does not exist for this date. Please create a pay period schedule if you have not done so already') );
		} elseif ( $this->getUserDateObject() == FALSE OR $this->getUserDateObject()->getPayPeriodObject()->getIsLocked() == TRUE ) {
			$this->Validator->isTRUE(	'pay_period',
										FALSE,
										TTi18n::gettext('Pay Period is Currently Locked') );
		}

		$plf = $this->getPLFByPunchControlID();
		if ( $plf !== NULL AND ( ( $this->isNew() AND $plf->getRecordCount() == 2 )
				OR $plf->getRecordCount() > 2 ) ) {
			//TTi18n::gettext('Punch Control can not have more than two punches. Please use the Add Punch button instead')
			//They might be trying to insert a punch inbetween two others?
			$this->Validator->isTRUE(	'punch_control',
										FALSE,
										TTi18n::gettext('Time conflicts with another punch on this day (c)'));
		}

		//Skip these checks if they are deleting a punch.
		if ( is_object( $this->getPunchObject() ) AND $this->getPunchObject()->getDeleted() == FALSE ) {
			$shift_data = $this->getShiftData();
			if ( is_array($shift_data) ) {
				foreach ( $shift_data['punches'] as $punch_data ) {
					//Make sure there aren't two In punches, or two Out punches in the same pair.
					//This fixes the bug where if you have an In punch, then click the blank cell below it
					//to add a new punch, but change the status from Out to In instead.
					if ( isset($punches[$punch_data['punch_control_id']][$punch_data['status_id']]) ) {
						if ( $punch_data['status_id'] == 10 ) {
							$this->Validator->isTRUE(	'time_stamp',
														FALSE,
														TTi18n::gettext('In punches cannot occur twice in the same punch pair, you may want to make this an out punch instead'));
						} else {
							$this->Validator->isTRUE(	'time_stamp',
														FALSE,
														TTi18n::gettext('Out punches cannot occur twice in the same punch pair, you may want to make this an in punch instead'));
						}
					}


					Debug::text(' Current Punch Object: ID: '. $this->getPunchObject()->getId() .' TimeStamp: '. $this->getPunchObject()->getTimeStamp() .' Status: '. $this->getPunchObject()->getStatus(), __FILE__, __LINE__, __METHOD__,10);
					Debug::text(' Looping Punch Object: ID: '. $punch_data['id'] .' TimeStamp: '. $punch_data['time_stamp'] .' Status: '.$punch_data['status_id'], __FILE__, __LINE__, __METHOD__,10);

					//Check for another punch that matches the timestamp and status.
					if ( $this->getPunchObject()->getID() != $punch_data['id'] ) {
						if ( $this->getPunchObject()->getTimeStamp() == $punch_data['time_stamp'] AND $this->getPunchObject()->getStatus() == $punch_data['status_id'] ) {
							$this->Validator->isTRUE(	'time_stamp',
														FALSE,
														TTi18n::gettext('Time and status match that of another punch, this could be due to rounding (a)') );
						}
					}

					//Check for another punch that matches the timestamp and NOT status in the SAME punch pair.
					if ( $this->getPunchObject()->getID() != $punch_data['id'] AND $this->getID() == $punch_data['punch_control_id'] ) {
						if ( $this->getPunchObject()->getTimeStamp() == $punch_data['time_stamp'] AND $this->getPunchObject()->getStatus() != $punch_data['status_id'] ) {
							$this->Validator->isTRUE(	'time_stamp',
														FALSE,
														TTi18n::gettext('Time matches another punch in the same punch pair, this could be due to rounding (b)') );
						}
					}

					$punches[$punch_data['punch_control_id']][$punch_data['status_id']] = $punch_data;
				}
				unset($punch_data);

				if ( isset($punches[$this->getID()]) ) {
					Debug::text('Current Punch ID Id: '. $this->getPunchObject()->getId() .' Punch Control ID: '. $this->getID() .' Status: '. $this->getPunchObject()->getStatus(), __FILE__, __LINE__, __METHOD__,10);
					//Debug::Arr($punches, 'Punches Arr: ', __FILE__, __LINE__, __METHOD__,10);

					if ( $this->getPunchObject()->getStatus() == 10 AND isset($punches[$this->getID()][20]) AND $this->getPunchObject()->getTimeStamp() > $punches[$this->getID()][20]['time_stamp'] ) {
							$this->Validator->isTRUE(	'time_stamp',
														FALSE,
														TTi18n::gettext('In punches cannot occur after an out punch, in the same punch pair'));
					} elseif ( $this->getPunchObject()->getStatus() == 20 AND isset($punches[$this->getID()][10]) AND $this->getPunchObject()->getTimeStamp() < $punches[$this->getID()][10]['time_stamp'] ) {
							$this->Validator->isTRUE(	'time_stamp',
														FALSE,
														TTi18n::gettext('Out punches cannot occur before an in punch, in the same punch pair'));
					} else {
						Debug::text('bPunch does not match any other punch pair.', __FILE__, __LINE__, __METHOD__,10);

						$punch_neighbors = Misc::getArrayNeighbors( $punches, $this->getID(), 'both');
						//Debug::Arr($punch_neighbors, ' Punch Neighbors: ', __FILE__, __LINE__, __METHOD__,10);

						if ( isset($punch_neighbors['next']) AND isset($punches[$punch_neighbors['next']]) ) {
							Debug::text('Found Next Punch...', __FILE__, __LINE__, __METHOD__,10);
							if ( ( isset($punches[$punch_neighbors['next']][10]) AND $this->getPunchObject()->getTimeStamp() > $punches[$punch_neighbors['next']][10]['time_stamp'] )
										OR ( isset($punches[$punch_neighbors['next']][20]) AND $this->getPunchObject()->getTimeStamp() > $punches[$punch_neighbors['next']][20]['time_stamp'] ) ) {
								$this->Validator->isTRUE(	'time_stamp',
															FALSE,
															TTi18n::gettext('Time conflicts with another punch on this day').' (a)');
							}
						}

						if ( isset($punch_neighbors['prev']) AND isset($punches[$punch_neighbors['prev']]) ) {
							Debug::text('Found prev Punch...', __FILE__, __LINE__, __METHOD__,10);
							if ( ( isset($punches[$punch_neighbors['prev']][10]) AND $this->getPunchObject()->getTimeStamp() < $punches[$punch_neighbors['prev']][10]['time_stamp'] )
										OR ( isset($punches[$punch_neighbors['prev']][20]) AND $this->getPunchObject()->getTimeStamp() < $punches[$punch_neighbors['prev']][20]['time_stamp'] ) ) {
								$this->Validator->isTRUE(	'time_stamp',
															FALSE,
															TTi18n::gettext('Time conflicts with another punch on this day').' (b)');
							}
						}
					}

					//Check to make sure punches don't exceed maximum shift time.
					$maximum_shift_time = $plf->getPayPeriodMaximumShiftTime( $this->getPunchObject()->getUser() );
					Debug::text('aaaMaximum shift time: '. $maximum_shift_time, __FILE__, __LINE__, __METHOD__,10);
					if ( $shift_data['total_time'] > $maximum_shift_time ) {
						$this->Validator->isTRUE(	'time_stamp',
													FALSE,
													TTi18n::gettext('Punch exceeds maximum shift time of') .' '. TTDate::getTimeUnit( $maximum_shift_time )  .' '. TTi18n::getText('hrs set for this pay period schedule') );
					}
				}
				unset($punches);
			}
		}

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL AND $this->getEnableStrictJobValidation() == TRUE ) {
			if ( $this->getJob() > 0 ) {
				$jlf = new JobListFactory();
				$jlf->getById( $this->getJob() );
				if ( $jlf->getRecordCount() > 0 ) {
					$j_obj = $jlf->getCurrent();

					if ( is_object( $this->getUserDateObject() ) AND $j_obj->isAllowedUser( $this->getUserDateObject()->getUser() ) == FALSE ) {
						$this->Validator->isTRUE(	'job',
													FALSE,
													TTi18n::gettext('Employee is not assigned to this job') );
					}

					if ( $j_obj->isAllowedItem( $this->getJobItem() ) == FALSE ) {
						$this->Validator->isTRUE(	'job_item',
													FALSE,
													TTi18n::gettext('Task is not assigned to this job') );
					}
				}
			}
		}

		return TRUE;
	}

	function preSave() {
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
/*
		if ( $this->getUserDateID() == FALSE AND is_object( $this->getPunchObject() ) ) {
			$this->setUserDate( $this->getUser(), $this->getPunchObject()->getTimeStamp() );
		}
*/
		//Set Job default Job Item if required.
		if ( $this->getJob() != FALSE AND $this->getJobItem() == '' ) {
			Debug::text(' Job is set ('.$this->getJob().'), but no task is... Using default job item...', __FILE__, __LINE__, __METHOD__,10);

			if ( is_object( $this->getJobObject() ) ){
				Debug::text(' Default Job Item: '. $this->getJobObject()->getDefaultItem(), __FILE__, __LINE__, __METHOD__,10);
				$this->setJobItem( $this->getJobObject()->getDefaultItem() );
			}
		}

		if ( $this->getEnableCalcTotalTime() == TRUE ) {
			$this->calcTotalTime();
		}

		if ( is_object( $this->getPunchObject() ) ) {
			$this->findUserDate();
		}

		$this->changePreviousPunchType();

		return TRUE;
	}

	function calcUserDate() {
		if ( $this->getEnableCalcUserDateID() == TRUE ) {
			Debug::Text(' Calculating User Date ID...', __FILE__, __LINE__, __METHOD__,10);

			$shift_data = $this->getShiftData();
			if ( is_array($shift_data) ) {
				$user_date_id = $this->getUserDateID(); //preSave should already be called before running this function.

				if ( isset($user_date_id) AND $user_date_id > 0 AND isset($shift_data['punch_control_ids']) AND is_array($shift_data['punch_control_ids']) ) {
					Debug::Text('Assigning all punch_control_ids to User Date ID: '. $user_date_id, __FILE__, __LINE__, __METHOD__,10);

					$this->old_user_date_ids[] = $user_date_id;
					$this->old_user_date_ids[] = $this->getOldUserDateID();

					foreach( $shift_data['punch_control_ids'] as $punch_control_id ) {
						$pclf = new PunchControlListFactory();
						$pclf->getById( $punch_control_id );
						if ( $pclf->getRecordCount() == 1 ) {
							$pc_obj = $pclf->getCurrent();
							if ( $pc_obj->getUserDateID() != $user_date_id ) {
								Debug::Text(' Saving Punch Control ID: '. $punch_control_id .' with new User Date Total ID: '. $user_date_id , __FILE__, __LINE__, __METHOD__,10);

								$this->old_user_date_ids[] = $pc_obj->getUserDateID();
								$pc_obj->setUserDateID( $user_date_id );
								$pc_obj->setEnableCalcUserDateTotal( TRUE );
								$pc_obj->Save();
							} else {
								Debug::Text(' NOT Saving Punch Control ID, as User Date ID didnt change: '. $punch_control_id, __FILE__, __LINE__, __METHOD__,10);
							}
						}
					}
					Debug::Arr($this->old_user_date_ids, 'aOld User Date IDs: ', __FILE__, __LINE__, __METHOD__,10);

					return TRUE;
				}
			}
		}

		return FALSE;
	}

	function calcUserDateTotal() {
		if ( $this->getEnableCalcUserDateTotal() == TRUE ) {
			Debug::Text(' Calculating User Date Total...', __FILE__, __LINE__, __METHOD__,10);

			//Add a row to the user date total table, as "worked" hours.
			//Edit if it already exists and is not set as override.
			$udtlf = new UserDateTotalListFactory();
			$udtlf->getByUserDateIdAndPunchControlId( $this->getUserDateID(), $this->getId() );
			Debug::text(' Checking for Conflicting User Date Total Records, count: '. $udtlf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);
			if ( $udtlf->getRecordCount() > 0 ) {
				Debug::text(' Found Conflicting User Date Total Records, removing them before re-calc', __FILE__, __LINE__, __METHOD__,10);
				foreach($udtlf as $udt_obj) {
					if ( $udt_obj->getOverride() == FALSE ) {
						Debug::text(' bFound Conflicting User Date Total Records, removing them before re-calc', __FILE__, __LINE__, __METHOD__,10);
						$udt_obj->Delete();
					}
				}
			}

			Debug::text(' cFound Conflicting User Date Total Records, removing them before re-calc: PreMature: '. (int)$this->getEnablePreMatureException(), __FILE__, __LINE__, __METHOD__,10);
			if ( $this->getDeleted() == FALSE ) {
				Debug::text(' Calculating Total Time for day. Punch Control ID: '. $this->getId(), __FILE__, __LINE__, __METHOD__,10);
				$udtf = new UserDateTotalFactory();
				$udtf->setUserDateID( $this->getUserDateID() );
				$udtf->setPunchControlID( $this->getId() );
				$udtf->setStatus( 20 ); //Worked
				$udtf->setType( 10 ); //Total

				$udtf->setBranch( $this->getBranch() );
				$udtf->setDepartment( $this->getDepartment() );

				$udtf->setJob( $this->getJob() );
				$udtf->setJobItem( $this->getJobItem() );
				$udtf->setQuantity( $this->getQuantity() );
				$udtf->setBadQuantity( $this->getBadQuantity() );

				$udtf->setTotalTime( $this->getTotalTime() );
				$udtf->setActualTotalTime( $this->getActualTotalTime() );

				//Let smartReCalculate handle calculating totals/exceptions.
				if ( $udtf->isValid() ) {
					$udtf->Save();
				}
			}
		}

		return FALSE;
	}

	function postSave() {
		$this->removeCache( $this->getId() );

		$this->calcUserDate();
		$this->calcUserDateTotal();

		if ( $this->getEnableCalcSystemTotalTime() == TRUE ) {
			$this->old_user_date_ids[] = $this->getUserDateID();
			if ( $this->getUser() > 0 ) {
				//var_dump($this->old_user_date_ids);
				UserDateTotalFactory::smartReCalculate( $this->getUser(), $this->old_user_date_ids, $this->getEnableCalcException(), $this->getEnablePreMatureException() );
			}
		}

		return TRUE;
	}
}
?>
