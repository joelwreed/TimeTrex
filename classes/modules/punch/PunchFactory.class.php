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
 * $Id: PunchFactory.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */

/**
 * @package Module_Punch
 */
class PunchFactory extends Factory {
	protected $table = 'punch';
	protected $pk_sequence_name = 'punch_id_seq'; //PK Sequence name

	var $punch_control_obj = NULL;
	protected $schedule_obj = NULL;
	var $tmp_data = NULL;


	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('In'),
										20 => TTi18n::gettext('Out'),
									);
				break;
			case 'type':
				$retval = array(
										10 => TTi18n::gettext('Normal'),
										20 => TTi18n::gettext('Lunch'),
										30 => TTi18n::gettext('Break'),
									);
				break;
			case 'transfer':
				$retval = array(
										0 => TTi18n::gettext('No'),
										1 => TTi18n::gettext('Yes'),
									);
				break;

		}

		return $retval;
	}

	function getPunchControlObject() {
		if ( is_object($this->punch_control_obj) ) {
			return $this->punch_control_obj;
		} else {
			if ( $this->getPunchControlID() !== FALSE ) {
				$pclf = new PunchControlListFactory();
				$this->punch_control_obj = $pclf->getById( $this->getPunchControlID() )->getCurrent();

				return $this->punch_control_obj;
			}

			return FALSE;
		}
	}
/*
	function getSchedulePolicyObject() {
		if ( is_object($this->schedule_policy_obj) ) {
			return $this->schedule_policy_obj;
		} else {
			$splf = new SchedulePolicyListFactory();
			$splf->getById( $this->getScheduleObject()->getSchedulePolicyID() );
			if ( $splf->getRecordCount() > 0 ) {
				$this->schedule_policy_obj = $splf->getCurrent();
				return $this->schedule_policy_obj;
			}

			return FALSE;
		}
	}
*/
	function getScheduleObject() {
		if ( is_object($this->schedule_obj) ) {
			return $this->schedule_obj;
		} else {
			if ( $this->getScheduleID() !== FALSE ) {
				$slf = new ScheduleListFactory();
				$slf->getById( $this->getScheduleID() );
				if ( $slf->getRecordCount() > 0 ) {
					$this->schedule_obj = $slf->getCurrent();
					return $this->schedule_obj;
				}
			}

			return FALSE;
		}
	}

	function getNextPunchControlID() {
		//This is normally the PREVIOUS punch,
		//so if it was IN (10), return its punch control ID
		//so the next OUT punch is a new punch_control_id.
		if ( $this->getStatus() == 10 ) {
			return $this->getPunchControlID();
		}

		return FALSE;
	}

	function getUser() {
		if ( isset($this->tmp_data['user_id']) ) {
			return $this->tmp_data['user_id'];
		}

		return FALSE;
	}
	function setUser($id) {
		$this->tmp_data['user_id'] = $id;

		return TRUE;
	}

	function findPunchControlID() {
		if ( $this->getPunchControlID() != FALSE ) {
			$retval = $this->getPunchControlID();
		} else {
			$pclf = new PunchControlListFactory();
			Debug::Text('Checking for incomplete punch control... User: '. $this->getUser() .' TimeStamp: '. $this->getTimeStamp() .' Status: '. $this->getStatus(), __FILE__, __LINE__, __METHOD__,10);

			//Need to make sure the punch is rounded before we can get the proper punch_control_id. However
			// roundTimeStamp requires punch_control_id before it can round properly.
			$retval = (int)$pclf->getInCompletePunchControlIdByUserIdAndEpoch( $this->getUser(), $this->getTimeStamp(), $this->getStatus() );
			if ( $retval == FALSE ) {
				Debug::Text('Couldnt find already existing PunchControlID, generating new one...', __FILE__, __LINE__, __METHOD__,10);
				$retval = (int)$pclf->getNextInsertId();
			}
		}

		Debug::Text('Punch Control ID: '. $retval, __FILE__, __LINE__, __METHOD__,10);
		return $retval;
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

		//Can't check to make sure the PunchControl row exists, as it may be inserted later. So just
		//make sure its an non-zero INT.
		if (  $this->Validator->isNumeric(	'punch_control',
											$id,
											TTi18n::gettext('Invalid Punch Control ID')
										) ) {
			$this->data['punch_control_id'] = $id;

			return TRUE;
		}

/*
		if (  $this->Validator->isResultSetWithRows(	'punch_control',
														$pclf->getByID($id),
														TTi18n::gettext('Invalid Punch Control ID')
														) ) {
			$this->data['punch_control_id'] = $id;

			return TRUE;
		}
*/
		return FALSE;
	}

	function getTransfer() {
		if ( isset($this->data['transfer']) ) {
			return $this->fromBool( $this->data['transfer'] );
		}

		return FALSE;
	}
	function setTransfer($bool) {
		$this->data['transfer'] = $this->toBool($bool);

		return TRUE;
	}

	function getNextStatus() {
		if ( $this->getStatus() == 10 ) {
			return 20;
		}

		return 10;
	}

	function getStatus() {
		if ( isset($this->data['status_id']) ) {
			return $this->data['status_id'];
		}

		return FALSE;
	}
	function setStatus($status) {
		$status = trim($status);

		Debug::text(' Status: '. $status , __FILE__, __LINE__, __METHOD__,10);

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

	function getNextType() {
		if ( $this->getStatus() == 10 ) { //In
			return 10;
		} else { //Out
			return $this->getType();
		}

		return FALSE;
	}

	function getTypeCode() {
		if ( $this->getType() != 10 ) {
			$options = $this->getOptions('type');
			return substr( $options[$this->getType()], 0, 1);
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

	function getStation() {
		if ( isset($this->data['station_id']) ) {
			return $this->data['station_id'];
		}

		return FALSE;
	}
	function setStation($id) {
		$id = trim($id);

		$slf = new StationListFactory();
/*
		if (	$id == 0
				OR
				$this->Validator->isResultSetWithRows(		'station',
															$slf->getByID($id),
															TTi18n::gettext('Station does not exist')
															) ) {
*/
			$this->data['station_id'] = $id;

			return TRUE;
//		}

		return FALSE;
	}

	function roundTimeStamp($epoch) {
		$original_epoch = $epoch;

		Debug::text(' Rounding Timestamp: '. TTDate::getDate('DATE+TIME', $epoch ) .' Status ID: '. $this->getStatus() .' Type ID: '. $this->getType() , __FILE__, __LINE__, __METHOD__,10);
/*
		//Punch control is no longer used for rounding.
		if ( !is_object( $this->getPunchControlObject() ) ) {
			return FALSE;
		}
*/
		//Check for rounding policies.
		$riplf = new RoundIntervalPolicyListFactory();
		$type_id = $riplf->getPunchTypeFromPunchStatusAndType( $this->getStatus(), $this->getType() );

		Debug::text(' Round Interval Punch Type: '. $type_id .' User: '. $this->getUser(), __FILE__, __LINE__, __METHOD__,10);
		//$riplf->getByPolicyGroupUserIdAndTypeId( $this->getPunchControlObject()->getUserDateObject()->getUser(), $type_id );
		$riplf->getByPolicyGroupUserIdAndTypeId( $this->getUser(), $type_id );
		if ( $riplf->getRecordCount() == 1 ) {
			$round_policy_obj = $riplf->getCurrent();
			Debug::text(' Found Rounding Policy: '. $round_policy_obj->getId() .' Punch Type: '. $round_policy_obj->getPunchType(), __FILE__, __LINE__, __METHOD__,10);

			//FIXME: It will only do proper total rounding if they edit the Lunch Out punch.
			//We need to account for cases when they edit just the Lunch In Punch.
			if ( $round_policy_obj->getPunchType() == 100 ) {
				Debug::text('Lunch Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);

				//On Lunch Punch In (back from lunch) do the total rounding.
				if ( $this->getStatus() == 10 AND $this->getType() == 20 ) {
					Debug::text('bLunch Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);
					//If strict is set, round to scheduled lunch time?
					//Find Lunch Punch In.

					$plf = new PunchListFactory();
					//$plf->getPreviousPunchByUserDateIdAndStatusAndTypeAndEpoch( $this->getPunchControlObject()->getUserDateID(), 20, 20, $epoch );
					$plf->getPreviousPunchByUserIdAndStatusAndTypeAndEpoch( $this->getUser(), 20, 20, $epoch );
					if ( $plf->getRecordCount() == 1 ) {
						Debug::text('Found Lunch Punch Out: '. TTDate::getDate('DATE+TIME', $plf->getCurrent()->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);

						$total_lunch_time = $epoch - $plf->getCurrent()->getTimeStamp();
						Debug::text('Total Lunch Time: '. $total_lunch_time, __FILE__, __LINE__, __METHOD__,10);

						//Set the ScheduleID
						$has_schedule = $this->setScheduleID( $this->findScheduleID( $epoch ) );

						if ( $has_schedule == TRUE AND $round_policy_obj->getGrace() > 0
								AND is_object( $this->getScheduleObject()->getSchedulePolicyObject() )
								AND is_object( $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject() ) )  {
							Debug::text(' Applying Grace Period: ', __FILE__, __LINE__, __METHOD__,10);
							$total_lunch_time = TTDate::graceTime($total_lunch_time, $round_policy_obj->getGrace(), $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getAmount() );
							Debug::text('After Grace: '. $total_lunch_time, __FILE__, __LINE__, __METHOD__,10);
						}

						if ( $round_policy_obj->getInterval() > 0 )  {
							Debug::Text(' Rounding to interval: '. $round_policy_obj->getInterval(), __FILE__, __LINE__, __METHOD__,10);
							$total_lunch_time = TTDate::roundTime($total_lunch_time, $round_policy_obj->getInterval(), $round_policy_obj->getRoundType(), $round_policy_obj->getGrace() );
							Debug::text('After Rounding: '. $total_lunch_time, __FILE__, __LINE__, __METHOD__,10);
						}

						if (  $has_schedule == TRUE AND $round_policy_obj->getStrict() == TRUE
								AND is_object( $this->getScheduleObject()->getSchedulePolicyObject() )
								AND is_object( $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject() ) ) {
							Debug::Text(' Snap Time: Round Type: '. $round_policy_obj->getRoundType() , __FILE__, __LINE__, __METHOD__,10);
							if ( $round_policy_obj->getRoundType() == 10 ) {
								Debug::Text(' Snap Time DOWN ' , __FILE__, __LINE__, __METHOD__,10);
								$total_lunch_time = TTDate::snapTime($total_lunch_time, $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getAmount(), 'DOWN');
							} elseif ( $round_policy_obj->getRoundType() == 30 ) {
								Debug::Text(' Snap Time UP' , __FILE__, __LINE__, __METHOD__,10);
								$total_lunch_time = TTDate::snapTime($total_lunch_time, $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getAmount(), 'UP');
							} else {
								Debug::Text(' Not Snaping Time' , __FILE__, __LINE__, __METHOD__,10);
							}
						}

						$epoch = $plf->getCurrent()->getTimeStamp() + $total_lunch_time;
						Debug::text('Epoch after total rounding is: '. $epoch .' - '. TTDate::getDate('DATE+TIME', $epoch) , __FILE__, __LINE__, __METHOD__, 10);

					} else {
						Debug::text('DID NOT Find Lunch Punch Out: '. TTDate::getDate('DATE+TIME', $plf->getCurrent()->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);
					}

				} else {
					Debug::text('Skipping Lunch Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);
				}
			} elseif ( $round_policy_obj->getPunchType() == 110 ) { //Break Total
				Debug::text('break Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);

				//On break Punch In (back from break) do the total rounding.
				if ( $this->getStatus() == 10 AND $this->getType() == 30 ) {
					Debug::text('bbreak Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);
					//If strict is set, round to scheduled break time?
					//Find break Punch In.

					$plf = new PunchListFactory();
					//$plf->getPreviousPunchByUserDateIdAndStatusAndTypeAndEpoch( $this->getPunchControlObject()->getUserDateID(), 20, 30, $epoch );
					$plf->getPreviousPunchByUserIdAndStatusAndTypeAndEpoch( $this->getUser(), 20, 30, $epoch );
					if ( $plf->getRecordCount() == 1 ) {
						Debug::text('Found break Punch Out: '. TTDate::getDate('DATE+TIME', $plf->getCurrent()->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);

						$total_break_time = $epoch - $plf->getCurrent()->getTimeStamp();
						Debug::text('Total break Time: '. $total_break_time, __FILE__, __LINE__, __METHOD__,10);

						//Set the ScheduleID
						$has_schedule = $this->setScheduleID( $this->findScheduleID( $epoch ) );

						if ( $has_schedule == TRUE AND $round_policy_obj->getGrace() > 0
								AND is_object( $this->getScheduleObject()->getSchedulePolicyObject() )
								AND is_object( $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject() ) )  {
							Debug::text(' Applying Grace Period: ', __FILE__, __LINE__, __METHOD__,10);
							$total_break_time = TTDate::graceTime($total_break_time, $round_policy_obj->getGrace(), $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getAmount() );
							Debug::text('After Grace: '. $total_break_time, __FILE__, __LINE__, __METHOD__,10);
						}

						if ( $round_policy_obj->getInterval() > 0 )  {
							Debug::Text(' Rounding to interval: '. $round_policy_obj->getInterval(), __FILE__, __LINE__, __METHOD__,10);
							$total_break_time = TTDate::roundTime($total_break_time, $round_policy_obj->getInterval(), $round_policy_obj->getRoundType(), $round_policy_obj->getGrace() );
							Debug::text('After Rounding: '. $total_break_time, __FILE__, __LINE__, __METHOD__,10);
						}

						if (  $has_schedule == TRUE AND $round_policy_obj->getStrict() == TRUE
								AND is_object( $this->getScheduleObject()->getSchedulePolicyObject() )
								AND is_object( $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject() ) ) {
							Debug::Text(' Snap Time: Round Type: '. $round_policy_obj->getRoundType() , __FILE__, __LINE__, __METHOD__,10);
							if ( $round_policy_obj->getRoundType() == 10 ) {
								Debug::Text(' Snap Time DOWN ' , __FILE__, __LINE__, __METHOD__,10);
								$total_break_time = TTDate::snapTime($total_break_time, $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getAmount(), 'DOWN');
							} elseif ( $round_policy_obj->getRoundType() == 30 ) {
								Debug::Text(' Snap Time UP' , __FILE__, __LINE__, __METHOD__,10);
								$total_break_time = TTDate::snapTime($total_break_time, $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getAmount(), 'UP');
							} else {
								Debug::Text(' Not Snaping Time' , __FILE__, __LINE__, __METHOD__,10);
							}
						}

						$epoch = $plf->getCurrent()->getTimeStamp() + $total_break_time;
						Debug::text('Epoch after total rounding is: '. $epoch .' - '. TTDate::getDate('DATE+TIME', $epoch) , __FILE__, __LINE__, __METHOD__, 10);

					} else {
						Debug::text('DID NOT Find break Punch Out: '. TTDate::getDate('DATE+TIME', $plf->getCurrent()->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);
					}

				} else {
					Debug::text('Skipping break Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);
				}

			} elseif ( $round_policy_obj->getPunchType() == 120 ) { //Day Total Rounding
				Debug::text('Day Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);
				if ( $this->getStatus() == 20 AND $this->getType() == 10 ) { //Out, Type Normal
					Debug::text('bDay Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);

					//If strict is set, round to scheduled time?

					$plf = new PunchListFactory();
					$plf->getPreviousPunchByUserIdAndEpochAndNotPunchIDAndMaximumShiftTime( $this->getUser(), $epoch, $this->getId() );
					if ( $plf->getRecordCount() == 1 ) {
						Debug::text('Found Previous Punch In: '. TTDate::getDate('DATE+TIME', $plf->getCurrent()->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);

						//Get day total time prior to this punch control.
						$pclf = new PunchControlListFactory();
						$pclf->getByUserDateId( $plf->getCurrent()->getPunchControlObject()->getUserDateID() );
						if ( $pclf->getRecordCount() > 0 ) {
							$day_total_time = $epoch - $plf->getCurrent()->getTimeStamp();
							Debug::text('aDay Total Time: '. $day_total_time .' Current Punch Control ID: '. $this->getPunchControlID(), __FILE__, __LINE__, __METHOD__,10);

							foreach( $pclf as $pc_obj ) {
								if ( $plf->getCurrent()->getPunchControlID() != $pc_obj->getID() ) {
									Debug::text('Punch Control Total Time: '. $pc_obj->getTotalTime() .' ID: '. $pc_obj->getId(), __FILE__, __LINE__, __METHOD__,10);
									$day_total_time += $pc_obj->getTotalTime();
								}
							}

							Debug::text('bDay Total Time: '. $day_total_time, __FILE__, __LINE__, __METHOD__,10);
							$original_day_total_time = $day_total_time;

							if ( $day_total_time > 0 ) {
								//Set the ScheduleID
								$has_schedule = $this->setScheduleID( $this->findScheduleID( $epoch ) );

								if ( $has_schedule == TRUE AND $round_policy_obj->getGrace() > 0
										AND is_object( $this->getScheduleObject()->getSchedulePolicyObject() )
										AND is_object( $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject() ) )  {
									Debug::text(' Applying Grace Period: ', __FILE__, __LINE__, __METHOD__,10);
									$day_total_time = TTDate::graceTime($day_total_time, $round_policy_obj->getGrace(), $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getAmount() );
									Debug::text('After Grace: '. $day_total_time, __FILE__, __LINE__, __METHOD__,10);
								}

								if ( $round_policy_obj->getInterval() > 0 )  {
									Debug::Text(' Rounding to interval: '. $round_policy_obj->getInterval(), __FILE__, __LINE__, __METHOD__,10);
									$day_total_time = TTDate::roundTime($day_total_time, $round_policy_obj->getInterval(), $round_policy_obj->getRoundType(), $round_policy_obj->getGrace() );
									Debug::text('After Rounding: '. $day_total_time, __FILE__, __LINE__, __METHOD__,10);
								}

								if (  $has_schedule == TRUE AND $round_policy_obj->getStrict() == TRUE
										AND is_object( $this->getScheduleObject()->getSchedulePolicyObject() )
										AND is_object( $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject() ) ) {
									Debug::Text(' Snap Time: Round Type: '. $round_policy_obj->getRoundType() , __FILE__, __LINE__, __METHOD__,10);
									if ( $round_policy_obj->getRoundType() == 10 ) {
										Debug::Text(' Snap Time DOWN ' , __FILE__, __LINE__, __METHOD__,10);
										$day_total_time = TTDate::snapTime($day_total_time, $this->getScheduleObject()->getTotalTime(), 'DOWN');
									} elseif ( $round_policy_obj->getRoundType() == 30 ) {
										Debug::Text(' Snap Time UP' , __FILE__, __LINE__, __METHOD__,10);
										$day_total_time = TTDate::snapTime($day_total_time, $this->getScheduleObject()->getTotalTime(), 'UP');
									} else {
										Debug::Text(' Not Snaping Time' , __FILE__, __LINE__, __METHOD__,10);
									}
								}

								Debug::text('cDay Total Time: '. $day_total_time, __FILE__, __LINE__, __METHOD__,10);

								$day_total_time_diff = $day_total_time - $original_day_total_time;
								Debug::text('Day Total Diff: '. $day_total_time_diff, __FILE__, __LINE__, __METHOD__,10);

								$epoch = $original_epoch + $day_total_time_diff;
							}
						}
					} else {
						Debug::text('DID NOT Find Normal Punch Out: '. TTDate::getDate('DATE+TIME', $plf->getCurrent()->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);
					}

				} else {
					Debug::text('Skipping Lunch Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);
				}
			} else {
				Debug::text('NOT Total Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);

				if ( $this->inScheduleStartStopWindow( $epoch ) AND $round_policy_obj->getGrace() > 0 )  {
					Debug::text(' Applying Grace Period: ', __FILE__, __LINE__, __METHOD__,10);
					$epoch = TTDate::graceTime($epoch, $round_policy_obj->getGrace(), $this->getScheduleWindowTime() );
					Debug::text('After Grace: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);
				}

				$grace_time = $round_policy_obj->getGrace();
				//If strict scheduling is enabled, handle grace times differently.
				//Only apply them above if we are near the schedule start/stop time.
				//This allows for grace time to apply if an employee punches in late,
				//but afterwards not apply at all.
				if ( $round_policy_obj->getStrict() == TRUE ) {
					$grace_time = 0;
				}

				if ( $round_policy_obj->getInterval() > 0 )  {
					Debug::Text(' Rounding to interval: '. $round_policy_obj->getInterval(), __FILE__, __LINE__, __METHOD__,10);
					$epoch = TTDate::roundTime($epoch, $round_policy_obj->getInterval(), $round_policy_obj->getRoundType(), $grace_time );
					Debug::text('After Rounding: '. TTDate::getDate('DATE+TIME', $epoch ), __FILE__, __LINE__, __METHOD__,10);
				}

				//ONLY perform strict rounding on Normal punches, not break/lunch punches?
				//Modify the UI to restrict this as well perhaps?
				if ( $round_policy_obj->getStrict() == TRUE AND $this->getScheduleWindowTime() !== FALSE ) {
					Debug::Text(' Snap Time: Round Type: '. $round_policy_obj->getRoundType() , __FILE__, __LINE__, __METHOD__,10);
					if ( $round_policy_obj->getRoundType() == 10 ) {
						Debug::Text(' Snap Time DOWN ' , __FILE__, __LINE__, __METHOD__,10);
						$epoch = TTDate::snapTime($epoch, $this->getScheduleWindowTime(), 'DOWN');
					} elseif ( $round_policy_obj->getRoundType() == 30 ) {
						Debug::Text(' Snap Time UP' , __FILE__, __LINE__, __METHOD__,10);
						$epoch = TTDate::snapTime($epoch, $this->getScheduleWindowTime(), 'UP');
					} else {
						//If its an In Punch, snap up, if its out punch, snap down?
						Debug::Text(' Average rounding type, automatically determining snap direction.' , __FILE__, __LINE__, __METHOD__,10);
						if ( $this->getStatus() == 10 ) {
							Debug::Text(' Snap Time UP' , __FILE__, __LINE__, __METHOD__,10);
							$epoch = TTDate::snapTime($epoch, $this->getScheduleWindowTime(), 'UP');
						} else {
							Debug::Text(' Snap Time DOWN ' , __FILE__, __LINE__, __METHOD__,10);
							$epoch = TTDate::snapTime($epoch, $this->getScheduleWindowTime(), 'DOWN');
						}
					}
				}
			}

		} else {
			Debug::text(' NO Rounding Policy(s) Found', __FILE__, __LINE__, __METHOD__,10);
		}

		Debug::text(' Rounded TimeStamp: '. TTDate::getDate('DATE+TIME', $epoch ) .' Original TimeStamp: '. TTDate::getDate('DATE+TIME', $original_epoch ), __FILE__, __LINE__, __METHOD__,10);

		return $epoch;
	}

	function getTimeStamp( $raw = FALSE ) {
		if ( isset($this->data['time_stamp']) ) {
			if ( $raw === TRUE) {
				return $this->data['time_stamp'];
			} else {
				//return $this->db->UnixTimeStamp( $this->data['start_date'] );
				//strtotime is MUCH faster than UnixTimeStamp
				//Must use ADODB for times pre-1970 though.
				return TTDate::strtotime( $this->data['time_stamp'] );
			}
		}

		return FALSE;
	}
	function setTimeStamp($epoch, $enable_rounding = TRUE) {
		$epoch = $original_epoch = trim($epoch);

		if ( $enable_rounding == TRUE AND $this->getTransfer() == FALSE ) {
			$epoch = $this->roundTimeStamp($epoch);
		} else {
			Debug::text(' Rounding Disabled... ', __FILE__, __LINE__, __METHOD__,10);
		}

		//Always round to one min, no matter what. Even on a transfer.
		$epoch = TTDate::roundTime($epoch, 60);

		if 	(	$this->Validator->isDate(		'time_stamp',
												$epoch,
												TTi18n::gettext('Incorrect time stamp'))

			) {

			Debug::text(' Set: '. $epoch , __FILE__, __LINE__, __METHOD__,10);
			$this->data['time_stamp'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getOriginalTimeStamp( $raw = FALSE ) {
		if ( isset($this->data['original_time_stamp']) ) {
			if ( $raw === TRUE ) {
				return $this->data['original_time_stamp'];
			} else {
				//return $this->db->UnixTimeStamp( $this->data['start_date'] );
				//strtotime is MUCH faster than UnixTimeStamp
				//Must use ADODB for times pre-1970 though.
				return TTDate::strtotime( $this->data['original_time_stamp'] );
			}
		}

		return FALSE;
	}
	function setOriginalTimeStamp($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'original_time_stamp',
												$epoch,
												TTi18n::gettext('Incorrect original time stamp'))

			) {

			$this->data['original_time_stamp'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getActualTimeStamp( $raw = FALSE ) {
		if ( isset($this->data['actual_time_stamp']) ) {
			if ( $raw === TRUE ) {
				return $this->data['actual_time_stamp'];
			} else {
				//return $this->db->UnixTimeStamp( $this->data['start_date'] );
				//strtotime is MUCH faster than UnixTimeStamp
				//Must use ADODB for times pre-1970 though.
				return TTDate::strtotime( $this->data['actual_time_stamp'] );
			}
		}

		return FALSE;
	}
	function setActualTimeStamp($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'actual_time_stamp',
												$epoch,
												TTi18n::gettext('Incorrect actual time stamp'))

			) {

			$this->data['actual_time_stamp'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getLongitude() {
		if ( isset($this->data['longitude']) ) {
			return (float)$this->data['longitude'];
		}

		return FALSE;
	}
	function setLongitude($value) {
		$value = trim((float)$value);

		if (	$value == 0
				OR
				$this->Validator->isFloat(	'longitude',
											$value,
											TTi18n::gettext('Longitude is invalid')
											) ) {
			$this->data['longitude'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getLatitude() {
		if ( isset($this->data['latitude']) ) {
			return (float)$this->data['latitude'];
		}

		return FALSE;
	}
	function setLatitude($value) {
		$value = trim((float)$value);

		if (	$value == 0
				OR
				$this->Validator->isFloat(	'latitude',
											$value,
											TTi18n::gettext('Latitude is invalid')
											) ) {
			$this->data['latitude'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getScheduleID() {
		if ( isset($this->tmp_data['schedule_id']) ) {
			return $this->tmp_data['schedule_id'];
		}

		return FALSE;
	}
	function setScheduleID( $id ) {
		if ( $id != FALSE ) {
			//Each time this is called, clear the ScheduleObject() cache.
			$this->schedule_obj = NULL;

			$this->tmp_data['schedule_id'] = $id;
			return TRUE;
		}

		return FALSE;
	}

	function findScheduleID( $epoch = NULL, $user_id = NULL ) {
		//Debug::text(' aFinding SchedulePolicyID for this Punch: '. $epoch .' User ID: '. $user_id, __FILE__, __LINE__, __METHOD__,10);
		if ( $epoch == '' ) {
			$epoch = $this->getTimeStamp();
		}

		if ( $epoch == FALSE ) {
			return FALSE;
		}

		if ( $user_id == '' AND $this->getUser() == '' ) {
			Debug::text(' User ID not specified, cant find schedule... ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		} elseif ( $user_id == '' ) {
			$user_id = $this->getUser();
		}
		//Debug::text(' bFinding SchedulePolicyID for this Punch: '. $epoch .' User ID: '. $user_id, __FILE__, __LINE__, __METHOD__,10);

		//Check to see if this punch is within the start/stop window for the schedule.
		//We need to make sure we get schedules within about a 24hr
		//window of this punch, because if punch is at 11:55AM and the schedule starts at 12:30AM it won't
		//be found by a user_date_id.
		$slf = new ScheduleListFactory();
		$slf->getByUserIdAndStartDateAndEndDate( $user_id, ($epoch-43200), ($epoch+43200) );
		if ( $slf->getRecordCount() > 0 ) {
			//Check for schedule policy
			foreach ( $slf as $s_obj ) {
				Debug::text(' Checking Schedule ID: '. $s_obj->getID(), __FILE__, __LINE__, __METHOD__,10);
				if ( $s_obj->inSchedule( $epoch ) ) {
					Debug::text(' Within Start/Stop window. ', __FILE__, __LINE__, __METHOD__,10);

					return $s_obj->getId();

					//$this->tmp_data['schedule_id'] = $s_obj->getId();
					//return TRUE;
				} else {
					Debug::text(' NOT Within Start/Stop window.', __FILE__, __LINE__, __METHOD__,10);
					//Continue looping through all schedule shifts.
				}
			}
		} else {
			Debug::text(' Did not find Schedule...', __FILE__, __LINE__, __METHOD__,10);
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

	function getEnableAutoTransfer() {
		if ( isset($this->auto_transfer) ) {
			return $this->auto_transfer;
		}

		return TRUE;
	}
	function setEnableAutoTransfer($bool) {
		$this->auto_transfer = $bool;

		return TRUE;
	}

	function getEnableSplitAtMidnight() {
		if ( isset($this->split_at_midnight) ) {
			return $this->split_at_midnight;
		}

		return TRUE;
	}
	function setEnableSplitAtMidnight($bool) {
		$this->split_at_midnight = $bool;

		return TRUE;
	}

	function getScheduleWindowTime() {
		if ( isset($this->tmp_data['schedule_window_time']) ) {
			return $this->tmp_data['schedule_window_time'];
		}

		return FALSE;
	}

	function inScheduleStartStopWindow( $epoch ) {
		if ( $epoch == '' ) {
			return FALSE;
		}

		$this->setScheduleID( $this->findScheduleID( $epoch ) );

		if ( $this->getScheduleObject() == FALSE ) {
			return FALSE;
		}

		if ( $this->getScheduleObject()->inStartWindow( $epoch ) == TRUE ) {
			Debug::text(' Within Start window: '. $this->getScheduleObject()->getSchedulePolicyObject()->getStartStopWindow() .' Schedule Policy ID: '. $this->getScheduleObject()->getSchedulePolicyID() , __FILE__, __LINE__, __METHOD__,10);

			$this->tmp_data['schedule_window_time'] = $this->getScheduleObject()->getStartTime();

			return TRUE;
		} elseif ( $this->getScheduleObject()->inStopWindow( $epoch ) == TRUE ) {
			Debug::text(' Within Stop window: '. $this->getScheduleObject()->getSchedulePolicyObject()->getStartStopWindow() .' Schedule Policy ID: '. $this->getScheduleObject()->getSchedulePolicyID() , __FILE__, __LINE__, __METHOD__,10);

			$this->tmp_data['schedule_window_time'] = $this->getScheduleObject()->getEndTime();

			return TRUE;
		} else {
			Debug::text(' NOT Within Start/Stop window.', __FILE__, __LINE__, __METHOD__,10);
		}

		return FALSE;
	}

	//Run this function on the previous punch object normally.
	function inMealPolicyWindow( $current_epoch, $previous_epoch ) {
		Debug::Text(' Checking if we are in meal policy window/punch time...', __FILE__, __LINE__, __METHOD__,10);

		if ( $current_epoch == '' ) {
			return FALSE;
		}

		if ( $previous_epoch == '' ) {
			return FALSE;
		}

		Debug::Text(' bChecking if we are in meal policy window/punch time...', __FILE__, __LINE__, __METHOD__,10);

		if ( is_object( $this->getScheduleObject() )
				AND is_object( $this->getScheduleObject()->getSchedulePolicyObject() )
				AND is_object($this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject() )
				) {
			Debug::Text(' Found Schedule Meal Policy Object: Start Window: '. $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getStartWindow(), __FILE__, __LINE__, __METHOD__,10);

			$mp_obj = $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject();
			$start_epoch = $this->getScheduleObject()->getStartTime();
/*
			if ( $current_epoch >= ($this->getScheduleObject()->getStartTime() + $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getStartWindow() )
					AND $current_epoch <= ($this->getScheduleObject()->getStartTime() + $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getStartWindow() + $this->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject()->getWindowLength() ) ) {
				Debug::Text(' Setting Type to Lunch: ', __FILE__, __LINE__, __METHOD__,10);
				//$next_type = 20;

				return TRUE;
			}
*/
		} else {
			//Make sure prev punch is a Lunch Out Punch
			//Check NON-scheduled meal policies
			$mplf = new MealPolicyListFactory();
			$mplf->getByPolicyGroupUserId( $this->getUser() );
			if ( $mplf->getRecordCount() > 0 ) {
				$mp_obj = $mplf->getCurrent();

				//FIXME: Start time should be the shift start time, not the previous punch start time.
				$start_epoch = $previous_epoch;
				Debug::Text(' Found NON Schedule Meal Policy start Window: '. $mp_obj->getStartWindow(), __FILE__, __LINE__, __METHOD__,10);

/*
				if ( $current_epoch >= ($previous_epoch + $mp_obj->getStartWindow())
						AND $current_epoch <= ($previous_epoch + $mp_obj->getStartWindow() + $mp_obj->getWindowLength() ) ) {
					Debug::Text(' Setting Type to Lunch: ', __FILE__, __LINE__, __METHOD__,10);
					//$next_type = 20;
					return TRUE;
				}
*/
			} else {
				Debug::Text(' DID NOT Find NON Schedule Meal Policy start Window: ', __FILE__, __LINE__, __METHOD__,10);
			}
			unset($mplf);
		}

		if ( isset($mp_obj) AND is_object( $mp_obj ) ) {
			if ( $mp_obj->getAutoDetectType() == 10 ) { //Meal window
				Debug::Text(' Auto Detect Type: Meal Window...', __FILE__, __LINE__, __METHOD__,10);

				if ( $current_epoch >= ( $start_epoch + $mp_obj->getStartWindow() )
						AND $current_epoch <= ( $start_epoch + $mp_obj->getStartWindow() + $mp_obj->getWindowLength() ) ) {
					Debug::Text(' aPunch is in meal policy window!', __FILE__, __LINE__, __METHOD__,10);

					return TRUE;
				}
			} else { //Punch time.
				Debug::Text(' Auto Detect Type: Punch Time...', __FILE__, __LINE__, __METHOD__,10);
				if ( ( $current_epoch - $previous_epoch ) >= $mp_obj->getMinimumPunchTime()
						AND ( $current_epoch - $previous_epoch ) <= $mp_obj->getMaximumPunchTime() )  {
					Debug::Text(' bPunch is in meal policy window!', __FILE__, __LINE__, __METHOD__,10);

					return TRUE;
				}
			}
		} else {
			Debug::Text(' Unable to find meal policy object...', __FILE__, __LINE__, __METHOD__,10);
		}

		return FALSE;
	}

	//Run this function on the previous punch object normally.
	function inBreakPolicyWindow( $current_epoch, $previous_epoch ) {
		Debug::Text(' Checking if we are in break policy window/punch time... Current: '. TTDate::getDate('DATe+TIME', $current_epoch) .' Previous: '. TTDate::getDate('DATe+TIME', $previous_epoch), __FILE__, __LINE__, __METHOD__,10);

		if ( $current_epoch == '' ) {
			return FALSE;
		}

		if ( $previous_epoch == '' ) {
			return FALSE;
		}

		if ( is_object( $this->getScheduleObject() )
				AND is_object( $this->getScheduleObject()->getSchedulePolicyObject() )
				AND is_array($this->getScheduleObject()->getSchedulePolicyObject()->getBreakPolicy() )
				) {
			Debug::Text(' Found Schedule Break Policies...', __FILE__, __LINE__, __METHOD__,10);
			$bp_ids = $this->getScheduleObject()->getSchedulePolicyObject()->getBreakPolicy();
			foreach( $bp_ids as $bp_id ) {
				$bp_objs[] = $this->getScheduleObject()->getSchedulePolicyObject()->getBreakPolicyObject( $bp_id );
			}
			unset($bp_ids);

			$start_epoch = $this->getScheduleObject()->getStartTime();
		} else {
			//Make sure prev punch is a Break Out Punch
			//Check NON-scheduled break policies
			$bplf = new BreakPolicyListFactory();
			$bplf->getByPolicyGroupUserId( $this->getUser() );
			if ( $bplf->getRecordCount() > 0 ) {
				$bp_objs[] = $bplf->getCurrent();

				//FIXME: Start time should be the shift start time, not the previous punch start time.
				$start_epoch = $previous_epoch;
				Debug::Text(' Found NON Schedule Break Policy...', __FILE__, __LINE__, __METHOD__,10);
			} else {
				Debug::Text(' DID NOT Find NON Schedule Break Policy...', __FILE__, __LINE__, __METHOD__,10);
			}
			unset($mplf);
		}

		if ( isset($bp_objs) AND is_array( $bp_objs ) ) {
			foreach( $bp_objs as $bp_obj ) {
				if ( $bp_obj->getAutoDetectType() == 10 ) { //Meal window
					Debug::Text(' Auto Detect Type: Break Window...', __FILE__, __LINE__, __METHOD__,10);

					if ( $current_epoch >= ( $start_epoch + $bp_obj->getStartWindow() )
							AND $current_epoch <= ( $start_epoch + $bp_obj->getStartWindow() + $bp_obj->getWindowLength() ) ) {
						Debug::Text(' aPunch is in break policy (ID:'. $bp_obj->getId().') window!', __FILE__, __LINE__, __METHOD__,10);

						return TRUE;
					}
				} else { //Punch time.
					Debug::Text(' Auto Detect Type: Punch Time...', __FILE__, __LINE__, __METHOD__,10);
					if ( ( $current_epoch - $previous_epoch ) >= $bp_obj->getMinimumPunchTime()
							AND ( $current_epoch - $previous_epoch ) <= $bp_obj->getMaximumPunchTime() )  {
						Debug::Text(' bPunch is in break policy (ID:'. $bp_obj->getId().') window!', __FILE__, __LINE__, __METHOD__,10);

						return TRUE;
					}
				}
			}
		} else {
			Debug::Text(' Unable to find break policy object...', __FILE__, __LINE__, __METHOD__,10);
		}

		return FALSE;
	}

	function Validate() {
		if ( $this->Validator->hasError('punch_control') == FALSE
				AND $this->getPunchControlID() == FALSE ) {
			$this->Validator->isTRUE(	'punch_control',
										FALSE,
										TTi18n::gettext('Invalid Punch Control ID'));
		}

		return TRUE;
	}

	function preSave() {
		if ( $this->isNew() ) {
			//Debug::text(' Setting Original TimeStamp: '. $this->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);
			$this->setOriginalTimeStamp( $this->getTimeStamp() );
		}

		if ( $this->getDeleted() == FALSE ) {

			if ( $this->getTransfer() == TRUE AND $this->getEnableAutoTransfer() == TRUE ) {
				Debug::text(' Transfer is Enabled, automatic punch out of last punch pair: ', __FILE__, __LINE__, __METHOD__,10);

				//Check to make sure there is an open punch pair.
				$plf = new PunchListFactory();
				$plf->getPreviousPunchByUserIdAndEpoch( $this->getUser(), $this->getTimeStamp() );
				if ( $plf->getRecordCount() > 0 ) {
					$p_obj = $plf->getCurrent();
					Debug::text(' Found Last Punch: ', __FILE__, __LINE__, __METHOD__,10);

					if ( $p_obj->getStatus() == 10 ) {
						Debug::text(' Last Punch was in. Auto Punch Out now: ', __FILE__, __LINE__, __METHOD__,10);
						//Make sure the current punch status is IN
						$this->setStatus(10); //In
						$this->setType(10); //Normal (can't transfer in/out of lunches?)

						$pf = new PunchFactory();
						$pf->setUser( $this->getUser() );
						$pf->setEnableAutoTransfer( FALSE );
						$pf->setPunchControlID( $p_obj->getPunchControlID() );
						$pf->setTransfer( TRUE );
						$pf->setType( $p_obj->getNextType() );
						$pf->setStatus( 20 ); //Out
						$pf->setTimeStamp( $this->getTimeStamp(), FALSE ); //Disable rounding.
						$pf->setActualTimeStamp( $this->getTimeStamp() );
						$pf->setOriginalTimeStamp( $this->getTimeStamp() );
						if ( $pf->isValid() ) {
							if ( $pf->Save( FALSE ) == TRUE ) {
								$p_obj->getPunchControlObject()->setEnableCalcTotalTime( TRUE );
								$p_obj->getPunchControlObject()->setEnableCalcSystemTotalTime( TRUE );
								$p_obj->getPunchControlObject()->setEnableCalcUserDateTotal( TRUE );
								$p_obj->getPunchControlObject()->setEnableCalcException( TRUE );
								$p_obj->getPunchControlObject()->setEnablePreMatureException( TRUE );
								if ( $p_obj->getPunchControlObject()->isValid() ) {
									$p_obj->getPunchControlObject()->Save();
								} else {
									Debug::text(' aError saving auto out punch...', __FILE__, __LINE__, __METHOD__,10);
								}
							} else {
								Debug::text(' bError saving auto out punch...', __FILE__, __LINE__, __METHOD__,10);
							}
						} else {
							Debug::text(' cError saving auto out punch...', __FILE__, __LINE__, __METHOD__,10);
						}
					} else {
						Debug::text(' Last Punch was out. No Auto Punch ', __FILE__, __LINE__, __METHOD__,10);
					}
				}
				unset($plf, $p_obj, $pf);
			}

			//Split punch at midnight.
			//This has to be an Out punch, and the previous punch has to be an in punch in order for the split to occur.
			//Check to make sure there is an open punch pair.
			//Make sure this punch isn't right at midnight either, as no point in splitting a punch at that time.
			//FIXME: What happens if a supervisor edits a 11:30PM punch and makes it 5:00AM the next day?
			//		We can't split punches when editing, because we have to split punch_control_ids prior to saving etc...
			if ( $this->isNew() == TRUE
					AND $this->getStatus() == 20
					AND $this->getEnableSplitAtMidnight() == TRUE
					AND $this->getTimeStamp() != TTDate::getBeginDayEpoch( $this->getTimeStamp() )
					AND ( is_object( $this->getPunchControlObject() )
							AND is_object( $this->getPunchControlObject()->getPayPeriodScheduleObject() )
							AND $this->getPunchControlObject()->getPayPeriodScheduleObject()->getShiftAssignedDay() == 40 ) ) {

				$plf = new PunchListFactory();
				$plf->getPreviousPunchByUserIdAndEpoch( $this->getUser(), $this->getTimeStamp() );
				if ( $plf->getRecordCount() > 0 ) {
					$p_obj = $plf->getCurrent();
					Debug::text(' Found Last Punch: ', __FILE__, __LINE__, __METHOD__,10);

					if ( $p_obj->getStatus() == 10 AND TTDate::doesRangeSpanMidnight( $this->getTimeStamp(), $p_obj->getTimeStamp() ) ) {
						Debug::text(' Last Punch was in and this is an out punch that spans midnight. Split Punch at midnight now: ', __FILE__, __LINE__, __METHOD__,10);

						//FIXME: This will fail if a shift spans multiple days!

						//Make sure the current punch status is OUT
						//But we can split LUNCH/Break punches, because someone could punch in at 8PM, then out for lunch at 1:00AM, this would need to be split.
						$this->setStatus(20); //Out

						//Reduce the out punch by 60 seconds, and increase the current punch by 60seconds so no time is lost.
						$this->setTimeStamp( ($this->getTimeStamp()+60) ); //FIXME: May need to use ActualTimeStamp here so we aren't double rounding.

						//Get new punch control ID for the midnight punch and this one.
						$new_punch_control_id = $this->getPunchControlObject()->getNextInsertId();
						$this->setPunchControlID( $new_punch_control_id );

						Debug::text(' Split Punch: Punching out just before midnight yesterday...', __FILE__, __LINE__, __METHOD__,10);

						//
						//Punch out just before midnight
						//
						$pf = new PunchFactory();
						$pf->setUser( $this->getUser() );
						$pf->setEnableSplitAtMidnight( FALSE );
						$pf->setTransfer( FALSE );
						$pf->setEnableAutoTransfer( FALSE );

						$pf->setType( 10 ); //Normal
						$pf->setStatus( 20 ); //Out

						$before_midnight_timestamp = (TTDate::getBeginDayEpoch( $this->getTimeStamp() )-60);
						$pf->setTimeStamp( $before_midnight_timestamp, FALSE ); //Disable rounding.

						$pf->setActualTimeStamp( $before_midnight_timestamp );
						$pf->setOriginalTimeStamp( $before_midnight_timestamp );

						$pf->setPunchControlID( $p_obj->getPunchControlID() );
						if ( $pf->isValid() ) {
							if ( $pf->Save( FALSE ) == TRUE ) {
								$p_obj->getPunchControlObject()->setEnableCalcTotalTime( TRUE );
								$p_obj->getPunchControlObject()->setEnableCalcSystemTotalTime( TRUE );
								$p_obj->getPunchControlObject()->setEnableCalcUserDateTotal( TRUE );
								$p_obj->getPunchControlObject()->setEnableCalcException( TRUE );
								$p_obj->getPunchControlObject()->setEnablePreMatureException( TRUE );
								$p_obj->getPunchControlObject()->Save();
							}
						}
						unset($pf, $p_obj, $before_midnight_timestamp);

						Debug::text(' Split Punch: Punching int at midnight today...', __FILE__, __LINE__, __METHOD__,10);

						//
						//Punch in again right at midnight.
						//
						$pf = new PunchFactory();
						$pf->setUser( $this->getUser() );
						$pf->setEnableSplitAtMidnight( FALSE );
						$pf->setTransfer( FALSE );
						$pf->setEnableAutoTransfer( FALSE );

						$pf->setType( 10 ); //Normal
						$pf->setStatus( 10 ); //In

						$at_midnight_timestamp = TTDate::getBeginDayEpoch( $this->getTimeStamp() );
						$pf->setTimeStamp( $at_midnight_timestamp, FALSE ); //Disable rounding.

						$pf->setActualTimeStamp( $at_midnight_timestamp );
						$pf->setOriginalTimeStamp( $at_midnight_timestamp );

						$pf->setPunchControlID( $new_punch_control_id );
						if ( $pf->isValid() ) {
							if ( $pf->Save( FALSE ) == TRUE ) {
								$pcf = new PunchControlFactory();
								$pcf->setId( $pf->getPunchControlID() );
								$pcf->setPunchObject( $pf );

								$pcf->setBranch( $this->getPunchControlObject()->getBranch() );
								$pcf->setDepartment( $this->getPunchControlObject()->getDepartment() );
								$pcf->setJob( $this->getPunchControlObject()->getJob() );
								$pcf->setJobItem( $this->getPunchControlObject()->getJobItem() );
								$pcf->setOtherID1( $this->getPunchControlObject()->getOtherID1() );
								$pcf->setOtherID2( $this->getPunchControlObject()->getOtherID2() );
								$pcf->setOtherID3( $this->getPunchControlObject()->getOtherID3() );
								$pcf->setOtherID4( $this->getPunchControlObject()->getOtherID4() );
								$pcf->setOtherID5( $this->getPunchControlObject()->getOtherID5() );

								$pcf->setEnableStrictJobValidation( TRUE );
								$pcf->setEnableCalcUserDateID( TRUE );
								$pcf->setEnableCalcTotalTime( TRUE );
								$pcf->setEnableCalcSystemTotalTime( TRUE );
								$pcf->setEnableCalcWeeklySystemTotalTime( TRUE );
								$pcf->setEnableCalcUserDateTotal( TRUE );
								$pcf->setEnableCalcException( TRUE );

								if ( $pcf->isValid() == TRUE ) {
									$pcf->Save( TRUE, TRUE ); //Force isNEW() lookup.
								}
							}
						}
						unset($pf, $at_midnight_timestamp, $new_punch_control_id);

					} else {
						Debug::text(' Last Punch was out. No Auto Punch ', __FILE__, __LINE__, __METHOD__,10);
					}
				}
			}
		}

		return TRUE;
	}

	function postSave() {

		if ( $this->getDeleted() == TRUE ) {
			$plf = new PunchListFactory();
			$plf->getByPunchControlId( $this->getPunchControlID() );
			if ( $plf->getRecordCount() == 0 ) {
				//Check to see if any other punches are assigned to this punch_control_id
				Debug::text(' Deleted Last Punch for Punch Control Object.', __FILE__, __LINE__, __METHOD__,10);
				$this->getPunchControlObject()->setDeleted( TRUE );
			}

			//Make sure we recalculate system time.
			$this->getPunchControlObject()->setPunchObject( $this );
			//$this->getPunchControlObject()->setEnableCalcUserDateID( $this->getEnableCalcUserDateID() );
			$this->getPunchControlObject()->setEnableCalcUserDateID( TRUE );
			$this->getPunchControlObject()->setEnableCalcSystemTotalTime( $this->getEnableCalcSystemTotalTime() );
			$this->getPunchControlObject()->setEnableCalcWeeklySystemTotalTime( $this->getEnableCalcWeeklySystemTotalTime() );
			$this->getPunchControlObject()->setEnableCalcException( $this->getEnableCalcException() );
			$this->getPunchControlObject()->setEnablePreMatureException( $this->getEnablePreMatureException() );
			$this->getPunchControlObject()->setEnableCalcUserDateTotal( $this->getEnableCalcUserDateTotal() );
			$this->getPunchControlObject()->setEnableCalcTotalTime( $this->getEnableCalcTotalTime() );
			$this->getPunchControlObject()->Save();
		}

		return TRUE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Punch - Employee').': '. $this->getUser() . TTi18n::getText(' Timestamp').': '. TTDate::getDate('DATE+TIME', $this->getTimeStamp() ) , NULL, $this->getTable() );
	}
}
?>
