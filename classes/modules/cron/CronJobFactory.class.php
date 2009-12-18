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
 * $Revision: 2710 $
 * $Id: CronJobFactory.class.php 2710 2009-08-07 22:46:58Z ipso $
 * $Date: 2009-08-07 15:46:58 -0700 (Fri, 07 Aug 2009) $
 */

/**
 * @package Module_Cron
 */
class CronJobFactory extends Factory {
	protected $table = 'cron';
	protected $pk_sequence_name = 'cron_id_seq'; //PK Sequence name

	protected $temp_time = NULL;
	protected $execute_flag = FALSE;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'limit':
				$retval = array(
							'minute' => array('min' => 0, 'max' => 59 ),
							'hour' => array('min' => 0, 'max' => 23 ),
							'day_of_month' => array('min' => 1, 'max' => 31 ),
							'month' => array('min' => 1, 'max' => 12 ),
							'day_of_week' => array('min' => 0, 'max' => 7 ),
							);
				break;
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('READY'),
										20 => TTi18n::gettext('RUNNING'),
									);
				break;

		}

		return $retval;
	}

	function getStatus() {
		if ( isset($this->data['status_id']) ) {
			return (int)$this->data['status_id'];
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
											1,250)
						) {

			$this->data['name'] = $name;

			return FALSE;
		}

		return FALSE;
	}

	function isValidLimit( $value_arr, $limit_arr ) {
		if ( is_array($value_arr) AND is_array($limit_arr) ) {
			foreach($value_arr as $value ) {
				if ( $value == '*' ) {
					$retval = TRUE;
				}

				if ( $value >= $limit_arr['min'] AND $value <= $limit_arr['max'] ) {
					$retval = TRUE;
				} else {
					return FALSE;
				}
			}

			return $retval;
		}

		return FALSE;
	}

	function getMinute() {
		if ( isset($this->data['minute']) ) {
			return $this->data['minute'];
		}

		return FALSE;
	}
	function setMinute($value) {
		$value = trim($value);

		if (	$this->Validator->isLength(	'minute',
											$value,
											TTi18n::gettext('Minute is invalid'),
											1,250)
						) {

			$this->data['minute'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getHour() {
		if ( isset($this->data['hour']) ) {
			return $this->data['hour'];
		}

		return FALSE;
	}
	function setHour($value) {
		$value = trim($value);

		if (	$this->Validator->isLength(	'hour',
											$value,
											TTi18n::gettext('Hour is invalid'),
											1,250)
						) {

			$this->data['hour'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getDayOfMonth() {
		if ( isset($this->data['day_of_month']) ) {
			return $this->data['day_of_month'];
		}

		return FALSE;
	}
	function setDayOfMonth($value) {
		$value = trim($value);

		if (	$this->Validator->isLength(	'day_of_month',
											$value,
											TTi18n::gettext('Day of Month is invalid'),
											1,250)
						) {

			$this->data['day_of_month'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getMonth() {
		if ( isset($this->data['month']) ) {
			return $this->data['month'];
		}

		return FALSE;
	}
	function setMonth($value) {
		$value = trim($value);

		if (	$this->Validator->isLength(	'month',
											$value,
											TTi18n::gettext('Month is invalid'),
											1,250)
						) {

			$this->data['month'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getDayOfWeek() {
		if ( isset($this->data['day_of_week']) ) {
			return $this->data['day_of_week'];
		}

		return FALSE;
	}
	function setDayOfWeek($value) {
		$value = trim($value);

		if (	$this->Validator->isLength(	'day_of_week',
											$value,
											TTi18n::gettext('Day of Week is invalid'),
											1,250)
						) {

			$this->data['day_of_week'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getCommand() {
		if ( isset($this->data['command']) ) {
			return $this->data['command'];
		}

		return FALSE;
	}
	function setCommand($value) {
		$value = trim($value);

		if (	$this->Validator->isLength(	'command',
											$value,
											TTi18n::gettext('Command is invalid'),
											1,250)
						) {

			$this->data['command'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getLastRunDate( $raw = FALSE ) {
		if ( isset($this->data['last_run_date']) ) {
			if ( $raw === TRUE ) {
				return $this->data['last_run_date'];
			} else {
				return TTDate::strtotime( $this->data['last_run_date'] );
			}
		}

		return FALSE;
	}
	function setLastRunDate($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'last_run',
												$epoch,
												TTi18n::gettext('Incorrect last run'))
			) {

			$this->data['last_run_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	private function setTempTime( $epoch ) {
		$this->temp_time = $epoch;
	}

	private function getTempTime() {
		return $this->temp_time;
	}

	private function setExecuteFlag( $bool ) {
		$this->execute_flag = (bool)$bool;
	}

	private function getExecuteFlag() {
		return $this->execute_flag;
	}

	function getSystemLoad() {
		if ( OPERATING_SYSTEM == 'LINUX' ) {
			$loadavg_file = '/proc/loadavg';
			if ( file_exists( $loadavg_file ) AND is_readable( $loadavg_file ) ) {
				$buffer = '0 0 0';
				$buffer = file_get_contents( $loadavg_file );
				$load = explode(' ',$buffer);

				$retval = max((float)$load[0], (float)$load[1], (float)$load[2]);
				Debug::text(' Load Average: '. $retval , __FILE__, __LINE__, __METHOD__,10);

				return $retval;
			}
		}

		return 0;
	}

	function isSystemLoadValid() {
		global $config_vars;

		if ( !isset($config_vars['other']['max_cron_system_load']) ) {
			$config_vars['other']['max_cron_system_load'] = 9999;
		}

		$system_load = $this->getSystemLoad();
		if ( isset($config_vars['other']['max_cron_system_load']) AND $system_load <= $config_vars['other']['max_cron_system_load'] ) {
			Debug::text(' Load average within valid limits: Current: '. $system_load .' Max: '. $config_vars['other']['max_cron_system_load'], __FILE__, __LINE__, __METHOD__,10);

			return TRUE;
		}

		Debug::text(' Load average NOT within valid limits: Current: '. $system_load .' Max: '. $config_vars['other']['max_cron_system_load'], __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	//Parses any column into a complete list of entries.
	//ie: converts: 	0-59 to an array of: 0,1,2,3,4,5,6,...
	//					0-2,16,18 to array of 0,1,2,16,18
	//					*/2 to array of 0,2,4,6,8,...
	function parseScheduleString( $str, $type ) {
		$split_str = explode(',', $str);

		if ( count($split_str) == 0 ) {
			//Debug::text('Schedule String DOES NOT have multiple commas: '. count($split_str), __FILE__, __LINE__, __METHOD__, 10);
			$split_str = array($split_str);
		} else {
			//Debug::text('Schedule String has multiple commas: '. count($split_str), __FILE__, __LINE__, __METHOD__, 10);
		}

		$retarr = array();
		$limit_options = $this->getOptions('limit');
		foreach( $split_str as $str_atom ) {
			if ( strpos($str_atom, '-') !== FALSE ) {
				//Debug::text('Schedule atom has basic range: '. $str_atom, __FILE__, __LINE__, __METHOD__, 10);
				//Found basic range
				//get Min/Max of range
				$str_atom_range = explode('-', $str_atom);

				$retarr = array_merge( $retarr, range($str_atom_range[0], $str_atom_range[1]) );
				unset($str_atom_range);
			} elseif ( strpos($str_atom, '/') !== FALSE ) {
				//Debug::text('Schedule atom has advanced range: '. $str_atom, __FILE__, __LINE__, __METHOD__, 10);
				//Found basic range
				//get Min/Max of range
				$str_atom_range = explode('/', $str_atom);

				$retarr = array_merge( $retarr, range($limit_options[$type]['min'],$limit_options[$type]['max'], $str_atom_range[1]) );
				unset($str_atom_range);
			} else {
				//No Range found
				//Debug::text('Schedule atom does not have range: '. $str_atom, __FILE__, __LINE__, __METHOD__, 10);

				if ( trim($str_atom) == '*' ) {
					//Debug::text('Found Full Range!: '. $str_atom, __FILE__, __LINE__, __METHOD__, 10);
					$retarr = array_merge( $retarr, range($limit_options[$type]['min'],$limit_options[$type]['max']) );
				} else {
					//Debug::text('Singleton: '. $str_atom, __FILE__, __LINE__, __METHOD__, 10);
					$retarr[] = (int)$str_atom;
				}
			}
		}

		rsort($retarr);

		//Debug::Arr($retarr, 'Final Array: ', __FILE__, __LINE__, __METHOD__, 10);
		return $retarr;
	}

	//Check if job is scheduled to run right NOW.
	//If the job has missed a run, it will run immediately.
	function isScheduledToRun( $epoch = NULL, $last_run_date = NULL ) {
		//Debug::text('Checking if Cron Job is scheduled to run: '. $this->getName(), __FILE__, __LINE__, __METHOD__, 10);
		if ( $epoch == '' ) {
			$epoch = time();
		}

		//Debug::text('Checking if Cron Job is scheduled to run: '. $this->getName(), __FILE__, __LINE__, __METHOD__, 10);
		if ( $last_run_date == '' ) {
			$last_run_date = $this->getLastRunDate();
		}
		//Debug::text(' Name: '. $this->getName() .' Current Epoch: '. TTDate::getDate('DATE+TIME', $epoch) .' Last Run Date: '. TTDate::getDate('DATE+TIME', $last_run_date) , __FILE__, __LINE__, __METHOD__,10);

		$current_date_arr = getdate( $epoch );
		//Debug::Arr($current_date_arr, 'Current Date Arr: ', __FILE__, __LINE__, __METHOD__,10);

		//
		// In cases where cron has fallen behind, or wasn't being run for days on end,
		// It will run old jobs only once the hour/minute has passed, so it could take up to one hour for all jobs to catch-up.
		//
		//Debug::text(' Checking if Job: '. $this->getName() .' Is Scheduled to Run...', __FILE__, __LINE__, __METHOD__,10);

		$day_of_week_arr = $this->parseScheduleString( $this->getDayOfWeek(), 'day_of_week' );
		//Debug::Arr($day_of_week_arr, 'Day Of Week Arr: ', __FILE__, __LINE__, __METHOD__,10);
		foreach( $day_of_week_arr as $day_of_week ) {
			if ( $day_of_week <= $current_date_arr['wday'] ) {
				//Debug::text(' Day Of Week: '. $day_of_week, __FILE__, __LINE__, __METHOD__,10);

				$month_arr = $this->parseScheduleString( $this->getMonth(), 'month' );
				//Debug::Arr($month_arr, 'Month Arr: ', __FILE__, __LINE__, __METHOD__,10);
				foreach( $month_arr as $month ) {
					if ( $month <= $current_date_arr['mon'] ) {
						//Debug::text(' Month: '. $month, __FILE__, __LINE__, __METHOD__,10);

						$day_of_month_arr = $this->parseScheduleString( $this->getDayOfMonth(), 'day_of_month' );
						//Debug::Arr($day_of_month_arr, 'Day Of Month Arr: ', __FILE__, __LINE__, __METHOD__,10);
						foreach( $day_of_month_arr as $day_of_month ) {
							if ( $day_of_month <= $current_date_arr['mday'] ) {
								//Debug::text(' Day Of Month: '. $day_of_month, __FILE__, __LINE__, __METHOD__,10);

								$hour_arr = $this->parseScheduleString( $this->getHour(), 'hour' );
								//Debug::Arr($hour_arr, 'Hour Arr: ', __FILE__, __LINE__, __METHOD__,10);
								foreach( $hour_arr as $hour ) {
									if ( $hour <= $current_date_arr['hours'] ) {
										//Debug::text(' Hour: '. $hour, __FILE__, __LINE__, __METHOD__,10);

										$minute_arr = $this->parseScheduleString( $this->getMinute(), 'minute' );
										//Debug::Arr($minute_arr, 'Minute Arr: ', __FILE__, __LINE__, __METHOD__,10);
										foreach( $minute_arr as $minute ) {
											if ( $minute <= $current_date_arr['minutes'] ) {
												//Debug::text(' Minute: '. $minute, __FILE__, __LINE__, __METHOD__,10);

												$prev_scheduled_time = mktime( $hour, $minute, 0, $month, $day_of_month, $current_date_arr['year'] );
												Debug::text(' Name: '. $this->getName() .' Previous Scheduled Date: '. date('r', $prev_scheduled_time ) .'('.$prev_scheduled_time.') Last Run Time: '. date('r', $last_run_date ) .'('.$last_run_date.') Current Epoch: '. $epoch , __FILE__, __LINE__, __METHOD__,10);

												if ( $last_run_date < $prev_scheduled_time ) {
													Debug::text('  JOB is SCHEDULED TO RUN NOW!', __FILE__, __LINE__, __METHOD__,10);
													return TRUE;
												} else {
													Debug::text('  JOB is NOT scheduled to run right now...' , __FILE__, __LINE__, __METHOD__,10);
												}

												break;
											}
										}

										break;
									}
								}

								break;
							}
						}

						break;
					}
				}

				break;
			}
		}

		//Debug::text('  JOB is NOT SCHEDULED TO RUN YET!', __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}

	//Executes the CronJob
	function Execute( $php_cli = NULL, $dir = NULL ) {
		global $config_vars;
		$lock_file = new LockFile( $config_vars['cache']['dir'] . DIRECTORY_SEPARATOR . $this->getName().'.lock' );

		//Check job last updated date, if its more then 12hrs and its still in the "running" status,
		//chances are its an orphan. Change status.
		//if ( $this->getStatus() != 10 AND $this->getLastRunDate() < time()-(12*3600) ) {
		if ( $this->getStatus() != 10 AND $this->getUpdatedDate() > 0 AND $this->getUpdatedDate() < time()-(12*3600) ) {
			Debug::text('ERROR: Job has been running for more then 12 hours! Asssuming its an orphan, marking as ready for next run.', __FILE__, __LINE__, __METHOD__, 10);
			$this->setStatus(10);
			$this->Save(FALSE);

			$lock_file->delete();
		}

		if ( !is_executable( $php_cli ) ) {
			Debug::text('ERROR: PHP CLI is not executable: '. $php_cli , __FILE__, __LINE__, __METHOD__, 10);
			return FALSE;
		}

		if ( $this->isSystemLoadValid() == FALSE ) {
			Debug::text('System load is too high, skipping...', __FILE__, __LINE__, __METHOD__, 10);
			return FALSE;
		}

		//Cron script to execute
		$script = $dir . DIRECTORY_SEPARATOR . $this->getCommand();

		if ( $this->getStatus() == 10 AND $lock_file->exists() == FALSE ) {
			$lock_file->create();

			$this->setExecuteFlag(TRUE);

			Debug::text('Job is NOT currently running, running now...', __FILE__, __LINE__, __METHOD__, 10);
			//Mark job as running
			$this->setStatus(20); //Running
			$this->Save(FALSE);

			//Even if the file does not exist, we still need to "pretend" the cron job ran (set last ran date) so we don't
			//display the big red error message saying that NO jobs have run in the last 24hrs.
			if ( file_exists( $script ) ) {
				$command = '"'. $php_cli .'" "'. $script .'"';
				if ( OPERATING_SYSTEM == 'WIN' ) {
					//Windows requires quotes around the entire command, and each individual section with that might have spaces.
					$command = '"'. $command .'"';
				}
				Debug::text('Command: '. $command, __FILE__, __LINE__, __METHOD__, 10);

				$start_time = microtime(TRUE);
				exec($command, $output, $retcode);
				Debug::Arr($output, 'Time: '. (microtime(TRUE)-$start_time) .'s - Command RetCode: '. $retcode .' Output: ', __FILE__, __LINE__, __METHOD__, 10);

				TTLog::addEntry( $this->getId(), 500,  TTi18n::getText('Executing Cron Job').': '. $this->getID() .' '.  TTi18n::getText('Command').': '. $command .' '.  TTi18n::getText('Return Code').': '. $retcode, NULL, $this->getTable() );
			} else {
				Debug::text('WARNING: File does not exist, skipping: '. $script , __FILE__, __LINE__, __METHOD__, 10);
			}

			$this->setStatus(10); //Ready
			$this->setLastRunDate( TTDate::roundTime( time(), 60, 30) );
			$this->Save(FALSE);

			$this->setExecuteFlag(FALSE);

			$lock_file->delete();
			return TRUE;
		} else {
			Debug::text('Job is currently running, skipping...', __FILE__, __LINE__, __METHOD__, 10);
		}

		return FALSE;
	}

	function preSave() {
		if ( $this->getStatus() == '' ) {
			$this->setStatus(10); //Ready
		}

		if ( $this->getMinute() == '' ) {
			$this->setMinute('*');
		}

		if ( $this->getHour() == '' ) {
			$this->setHour('*');
		}

		if ( $this->getDayOfMonth() == '' ) {
			$this->setDayOfMonth('*');
		}

		if ( $this->getMonth() == '' ) {
			$this->setMonth('*');
		}

		if ( $this->getDayOfWeek() == '' ) {
			$this->setDayOfWeek('*');
		}

		return TRUE;
	}

	function postSave() {
		$this->removeCache( $this->getId() );

		return TRUE;
	}

	function addLog( $log_action ) {
		if ( $this->getExecuteFlag() == FALSE ) {
			return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Cron Job'), NULL, $this->getTable() );
		}

		return TRUE;
	}
}
?>
