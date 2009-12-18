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
 * $Revision: 3060 $
 * $Id: ExceptionPolicyFactory.class.php 3060 2009-11-13 16:08:37Z ipso $
 * $Date: 2009-11-13 08:08:37 -0800 (Fri, 13 Nov 2009) $
 */

/**
 * @package Module_Policy
 */
class ExceptionPolicyFactory extends Factory {
	protected $table = 'exception_policy';
	protected $pk_sequence_name = 'exception_policy_id_seq'; //PK Sequence name

	protected $enable_grace = array('S3', 'S4', 'S5', 'S6', 'L1', 'L2', 'B1','B2', 'S7', 'S8', 'S9');
	protected $enable_watch_window = array('S3', 'S4', 'S5', 'S6', 'O1','O2');
	protected static $premature_exceptions = array('M1', 'M2', 'M3', 'M4', 'L3', 'B4', 'S8');
	//16hrs... If punches are older then this time, its no longer premature.
	//This should actually be the PayPeriod Schedule maximum shift time.
	protected static $premature_delay = 57600;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'type':
				$retval = array(
										//Schedule Exceptions
										'S1' /* A */ => TTi18n::gettext('Unscheduled Absence'),
										'S2' /* B */ => TTi18n::gettext('Not Scheduled'),
										'S3' /* C */ => TTi18n::gettext('In Early'),
										'S4' /* D */ => TTi18n::gettext('In Late'),
										'S5' /* E */ => TTi18n::gettext('Out Early'),
										'S6' /* F */ => TTi18n::gettext('Out Late'),

										'S7' /* G */ => TTi18n::gettext('Over Daily Scheduled Time'),
										'S8' /* H */ => TTi18n::gettext('Under Daily Scheduled Time'),
										'S9' => TTi18n::gettext('Over Weekly Scheduled Time'),

//Add setting to set some sort of "Grace" period, or early warning system? Approaching overtime?
//Have exception where they can set the cutoff in hours, and it triggers once the employee has exceeded the weekly hours.
										'O1' => TTi18n::gettext('Over Daily Time'),
										'O2' => TTi18n::gettext('Over Weekly Time'),

										//Punch Exceptions
										'M1' /* K */ => TTi18n::gettext('Missing In Punch'),
										'M2' /* L */ => TTi18n::gettext('Missing Out Punch'),
										'M3' /* P */  => TTi18n::gettext('Missing Lunch In/Out Punch'),
										'M4' => TTi18n::gettext('Missing Break In/Out Punch'),


										'L1' /* M */ => TTi18n::gettext('Long Lunch'),
										'L2' /* N */ => TTi18n::gettext('Short Lunch'),
										'L3' /* O */ => TTi18n::gettext('No Lunch'),

										'B1' => TTi18n::gettext('Long Break'),
										'B2' => TTi18n::gettext('Short Break'),
										'B3' => TTi18n::gettext('Too Many Breaks'),
										'B4' => TTi18n::gettext('Too Few Breaks'),
//Worked too long without break/lunch, allow to set the time frame.
//Make grace period the amount of time a break has to exceed, and watch window the longest they can work without a break?
//										'B5' => TTi18n::gettext('Worked Too Long without Break')

										//Job Exceptions
										'J1' /* T J1 */  => TTi18n::gettext('Not Allowed On Job'),
										'J2' /* U J2 */  => TTi18n::gettext('Not Allowed On Task'),
										'J3' /* V J3 */  => TTi18n::gettext('Job Completed'),
										'J4' /* W J4 */  => TTi18n::gettext('No Job or Task'),

//Add location based exceptions, ie: Restricted Location.
									);
				break;
			case 'severity':
				$retval = array(
											10 => TTi18n::gettext('Low'),
											20 => TTi18n::gettext('Medium'),
											30 => TTi18n::gettext('High')
								);
				break;
			case 'email_notification':
				$retval = array(
											//Flex returns an empty object if 0 => None, so we make it a string and add a space infront ' 0' => None as a work around.
											' 0' => TTi18n::gettext('None'),
											10 => TTi18n::gettext('Employee'),
											20 => TTi18n::gettext('Supervisor'),
											//20 => TTi18n::gettext('Immediate Supervisor'),
											//20 => TTi18n::gettext('All Supervisors'),
											100 => TTi18n::gettext('Both')
								);
				break;
		}

		return $retval;
	}

	function _getVariableToFunctionMap() {
		$variable_function_map = array(
										'id' => 'ID',
										'exception_policy_control_id' => 'ExceptionPolicyControl',
										'name' => 'Name',
										'type_id' => 'Type',
										'severity_id' => 'Severity',
										'is_enabled_watch_window' => 'isEnabledWatchWindow',
										'watch_window' => 'WatchWindow',
										'is_enabled_grace' => 'isEnabledGrace',
										'grace' => 'Grace',
										//'demerit' => 'Demerit',
										'email_notification_id' => 'EmailNotification',
										'active' => 'Active',
										'deleted' => 'Deleted',
										);
		return $variable_function_map;
	}

	function getExceptionPolicyControl() {
		if ( isset($this->data['exception_policy_control_id']) ) {
			return $this->data['exception_policy_control_id'];
		}

		return FALSE;
	}
	function setExceptionPolicyControl($id) {
		$id = trim($id);

		$epclf = new ExceptionPolicyControlListFactory();

		if ( $this->Validator->isResultSetWithRows(	'exception_policy_control',
													$epclf->getByID($id),
													TTi18n::gettext('Exception Policy Control is invalid')
													) ) {

			$this->data['exception_policy_control_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getExceptionTypeDefaultValues( $exclude_exceptions, $product_edition = 10 ) {
		if ( !is_array($exclude_exceptions) ) {
			$exclude_exceptions = array();
		}
		$type_options = $this->getTypeOptions( $product_edition );

		$retarr = array();

		foreach ( $type_options as $type_id => $exception_name ) {
			//Skip excluded exceptions
			if ( in_array( $type_id, $exclude_exceptions ) ) {
				continue;
			}

			switch ( $type_id ) {
				case 'S1': //UnSchedule Absence
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 10,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'S3': //In Early
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 10,
												'email_notification_id' => 20,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 7200,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'S4': //In Late
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 20,
												'email_notification_id' => 20,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 7200,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'S5': //Out Early
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 20,
												'email_notification_id' => 20,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 7200,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'S6': //Out Late
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 10,
												'email_notification_id' => 20,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 7200,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'S7': //Over scheduled time
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 10,
												'email_notification_id' => 0,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'S8': //Under scheduled time
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 0,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'S9': //Over Weekly Scheduled Time
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'O1': //Over Daily Time
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => (3600*8),
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'O2': //Over Weekly Time
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => (3600*40),
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'M1': //Missing In Punch
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 30,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'M2': //Missing Out Punch
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 30,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'M3': //Missing Lunch In/Out Punch
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 30,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'M4': //Missing Break In/Out Punch
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 30,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'L1': //Long Lunch
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 0,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'L2': //Short Lunch
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 0,
												'demerit' => 0,
												'grace' => 900,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'L3': //No Lunch
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'B1': //Long Break
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 0,
												'demerit' => 0,
												'grace' => 300,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'B2': //Short Break
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 0,
												'demerit' => 0,
												'grace' => 300,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'B3': //Too Many Breaks
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'B4': //Too Few Breaks
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 20,
												'email_notification_id' => 100,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'J1': //Not allowed on job
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 20,
												'email_notification_id' => 20,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'J2': //Not allowed on task
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 20,
												'email_notification_id' => 20,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'J3': //Job completed
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => TRUE,
												'severity_id' => 20,
												'email_notification_id' => 20,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				case 'J4': //No Job Or Task
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 10,
												'email_notification_id' => 0,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
				default:
					$retarr[$type_id] = array(
												'id' => -1,
												'type_id' => $type_id,
												'name' => $type_options[$type_id],
												'active' => FALSE,
												'severity_id' => 10,
												'email_notification_id' => 0,
												'demerit' => 0,
												'grace' => 0,
												'is_enabled_grace' => $this->isEnabledGrace( $type_id ),
												'watch_window' => 0,
												'is_enabled_watch_window' => $this->isEnabledWatchWindow( $type_id )
												);
					break;
			}
		}

		return $retarr;
	}

	function getName() {
		return Option::getByKey( $this->getType(), $this->getTypeOptions( getTTProductEdition() ) );
	}

	function getTypeOptions( $product_edition = 10 ) {
		$options = $this->getOptions('type');

		if ( getTTProductEdition() != TT_PRODUCT_PROFESSIONAL OR $product_edition != 20 ) {
			$professional_exceptions = array('J1','J2','J3','J4');
			foreach( $professional_exceptions as $professional_exception ) {
				unset($options[$professional_exception]);
			}
		}

		return $options;
	}

	function getType() {
		if ( isset($this->data['type_id']) ) {
			return $this->data['type_id'];
		}

		return FALSE;
	}
	function setType($value) {
		$value = trim($value);

		if ( $this->Validator->inArrayKey(	'type',
											$value,
											TTi18n::gettext('Incorrect Type'),
											$this->getOptions('type')) ) {

			$this->data['type_id'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getSeverity() {
		if ( isset($this->data['severity_id']) ) {
			return $this->data['severity_id'];
		}

		return FALSE;
	}
	function setSeverity($value) {
		$value = trim($value);

		$key = Option::getByValue($value, $this->getOptions('severity') );
		if ($key !== FALSE) {
			$value = $key;
		}

		if ( $this->Validator->inArrayKey(	'severity',
											$value,
											TTi18n::gettext('Incorrect Severity'),
											$this->getOptions('severity')) ) {

			$this->data['severity_id'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getWatchWindow() {
		if ( isset($this->data['watch_window']) ) {
			return $this->data['watch_window'];
		}

		return FALSE;
	}
	function setWatchWindow($value) {
		$value = trim($value);

		if 	(	$value == 0
				OR $this->Validator->isNumeric(		'watch_window',
													$value,
													TTi18n::gettext('Incorrect Watch Window')) ) {

			$this->data['watch_window'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getGrace() {
		if ( isset($this->data['grace']) ) {
			return $this->data['grace'];
		}

		return FALSE;
	}
	function setGrace($value) {
		$value = trim($value);

		if 	(	$value == 0
				OR $this->Validator->isNumeric(		'grace',
													$value,
													TTi18n::gettext('Incorrect grace value')) ) {

			$this->data['grace'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getDemerit() {
		if ( isset($this->data['demerit']) ) {
			return $this->data['demerit'];
		}

		return FALSE;
	}
	function setDemerit($value) {
		$value = trim($value);

		if 	(	$value == 0
				OR $this->Validator->isNumeric(		'demerit',
													$value,
													TTi18n::gettext('Incorrect demerit value')) ) {

			$this->data['demerit'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getEmailNotification() {
		if ( isset($this->data['email_notification_id']) ) {
			return $this->data['email_notification_id'];
		}

		return FALSE;
	}
	function setEmailNotification($value) {
		$value = (int)trim($value);

		if ( $this->Validator->inArrayKey(	'email_notification',
											$value,
											TTi18n::gettext('Incorrect Email Notification'),
											$this->getOptions('email_notification')) ) {

			$this->data['email_notification_id'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getActive() {
		return $this->fromBool( $this->data['active'] );
	}
	function setActive($bool) {
		$this->data['active'] = $this->toBool($bool);

		return TRUE;
	}

	function isEnabledGrace( $code = NULL ) {
		if ( $code == NULL ) {
			$code = $this->getType();
		}

		if ( in_array( $code, $this->enable_grace ) ) {
			return TRUE;
		}

		return FALSE;
	}

	function isEnabledWatchWindow( $code = NULL ) {
		if ( $code == NULL ) {
			$code = $this->getType();
		}

		if ( in_array( $code, $this->enable_watch_window ) ) {
			return TRUE;
		}

		return FALSE;
	}

	function isPreMature( $code ) {
		if ( in_array( $code, self::$premature_exceptions ) ) {
			return TRUE;
		}

		return FALSE;
	}

	function calcExceptions( $user_date_id, $enable_premature_exceptions = FALSE, $enable_future_exceptions = TRUE ) {
		global $profiler;

		$profiler->startTimer( "ExceptionPolicy::calcExceptions()");

		if ( $user_date_id == '' ) {
			return FALSE;
		}
		Debug::text(' User Date ID: '. $user_date_id .' PreMature: '. (int)$enable_premature_exceptions , __FILE__, __LINE__, __METHOD__,10);

		//Get user date info
		$udlf = new UserDateListFactory();
		$udlf->getById( $user_date_id );
		if ( $udlf->getRecordCount() > 0 ) {
			$user_date_obj = $udlf->getCurrent();

			if ( $enable_future_exceptions == FALSE
					AND $user_date_obj->getDateStamp() > TTDate::getEndDayEpoch() ) {
				return FALSE;
			}
		} else {
			return FALSE;
		}

		//Since we are not usng demerits yet, just always delete exceptions and re-calculate them
		$elf = new ExceptionListFactory();
		$elf->getByUserDateID( $user_date_id );
		if ( $elf->getRecordCount() > 0 ) {
			foreach( $elf as $e_obj ) {
				Debug::text(' Deleting Exception: '.  $e_obj->getID(), __FILE__, __LINE__, __METHOD__,10);
				$e_obj->Delete();
			}
		}

		//Get all Punches on this date for this user.
		$plf = new PunchListFactory();
		$plf->getByUserDateId( $user_date_id );
		if ( $plf->getRecordCount() > 0 ) {
			Debug::text(' Found Punches: '.  $plf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);
		}

		$slf = new ScheduleListFactory();
		$slf->getByUserDateIdAndStatusId( $user_date_id, 10 );
		if ( $slf->getRecordCount() > 0 ) {
			Debug::text(' Found Schedule: '.  $slf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);
		}

		$schedule_id_cache = NULL; //Cache schedule IDs so we don't need to do a lookup for every exception.

		//Get all active exceptions.
		$eplf = new ExceptionPolicyListFactory();
		$eplf->getByPolicyGroupUserIdAndActive( $user_date_obj->getUser(), TRUE );
		if ( $eplf->getRecordCount() > 0 ) {
			Debug::text(' Found Active Exceptions: '.  $eplf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);

			foreach ( $eplf as $ep_obj )  {
				//Debug::text(' Found Exception Type: '. $ep_obj->getType() .' ID: '. $ep_obj->getID() .' Control ID: '. $ep_obj->getExceptionPolicyControl(), __FILE__, __LINE__, __METHOD__,10);

				if ( $enable_premature_exceptions == TRUE AND self::isPreMature( $ep_obj->getType() ) == TRUE ) {
					//Debug::text(' Premature Exception: '. $ep_obj->getType() , __FILE__, __LINE__, __METHOD__,10);
					$type_id = 5; //Pre-Mature
				} else {
					//Debug::text(' NOT Premature Exception: '. $ep_obj->getType() , __FILE__, __LINE__, __METHOD__,10);
					$type_id = 50; //Active
				}

				switch ( strtolower( $ep_obj->getType() ) ) {
					case 's1': 	//Unscheduled Absence... Anytime they are scheduled and have not punched in.
								//Ignore these exceptions if the schedule is after today (not including today),
								//so if a supervisors schedules an employee two days in advance they don't get a unscheduled
								//absence appearing right away.
						if ( $plf->getRecordCount() == 0 ) {
							if ( $slf->getRecordCount() > 0 ) {
								foreach( $slf as $s_obj ) {
									if ( $s_obj->getStatus() == 10 AND ( TTDate::getBeginDayEpoch( $s_obj->getStartTime() ) - TTDate::getBeginDayEpoch( TTDate::getTime() ) ) <= 0 ) {
										$ef = new ExceptionFactory();
										$ef->setUserDateID( $user_date_id );
										$ef->setExceptionPolicyID( $ep_obj->getId() );
										$ef->setType( $type_id );
										$ef->setEnableDemerits( TRUE );
										if ( $ef->isValid() ) {
											if ( $enable_premature_exceptions == TRUE ) {
												$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
											}
											$ef->Save();
										}
									}
								}
							} else {
								Debug::text(' NOT Scheduled', __FILE__, __LINE__, __METHOD__,10);
							}
						}
						break;
					case 's2': //Not Scheduled
						$schedule_total_time = 0;

						if ( $slf->getRecordCount() == 0 ) {
							if ( $plf->getRecordCount() > 0 ) {
								Debug::text(' Worked when wasnt scheduled', __FILE__, __LINE__, __METHOD__,10);

								$ef = new ExceptionFactory();
								$ef->setUserDateID( $user_date_id );
								$ef->setExceptionPolicyID( $ep_obj->getId() );
								$ef->setType( $type_id );
								$ef->setEnableDemerits( TRUE );
								if ( $ef->isValid() ) {
									if ( $enable_premature_exceptions == TRUE ) {
										$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
									}
									$ef->Save();
								}

							}
						} else {
							Debug::text(' IS Scheduled', __FILE__, __LINE__, __METHOD__,10);
						}
						break;
					case 's3': //In Early
						if ( $plf->getRecordCount() > 0 ) {
							//Loop through each punch, find out if they are scheduled, and if they are in early
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getType() == 10 AND $p_obj->getStatus() == 10 ) { //Normal In
									if ( !isset($scheduled_id_cache[$p_obj->getID()]) ) {
										$scheduled_id_cache[$p_obj->getID()] = $p_obj->findScheduleID( NULL, $user_date_obj->getUser() );
									}
									if ( $p_obj->setScheduleID( $scheduled_id_cache[$p_obj->getID()] ) == TRUE ) {
										if ( $p_obj->getTimeStamp() < $p_obj->getScheduleObject()->getStartTime() ) {
											if ( TTDate::inWindow( $p_obj->getTimeStamp(), $p_obj->getScheduleObject()->getStartTime(), $ep_obj->getGrace() ) == TRUE ) {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Within Grace time, IGNORE EXCEPTION: ', __FILE__, __LINE__, __METHOD__,10);
											} elseif ( TTDate::inWindow( $p_obj->getTimeStamp(), $p_obj->getScheduleObject()->getStartTime(), $ep_obj->getWatchWindow() ) == TRUE ) {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;NOT Within Grace time, SET EXCEPTION: ', __FILE__, __LINE__, __METHOD__,10);

												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												$ef->setPunchID( $p_obj->getID() );
												$ef->setType( $type_id );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											}

										}
									} else {
										Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;NO Schedule Found', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}
						}
						break;
					case 's4': //In Late
						if ( $plf->getRecordCount() > 0 ) {
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getType() == 10 AND $p_obj->getStatus() == 10 ) { //Normal In
									if ( !isset($scheduled_id_cache[$p_obj->getID()]) ) {
										$scheduled_id_cache[$p_obj->getID()] = $p_obj->findScheduleID( NULL, $user_date_obj->getUser() );
									}
									if ( $p_obj->setScheduleID( $scheduled_id_cache[$p_obj->getID()] ) == TRUE ) {
										if ( $p_obj->getTimeStamp() > $p_obj->getScheduleObject()->getStartTime() ) {
											if ( TTDate::inWindow( $p_obj->getTimeStamp(), $p_obj->getScheduleObject()->getStartTime(), $ep_obj->getGrace() ) == TRUE ) {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Within Grace time, IGNORE EXCEPTION: ', __FILE__, __LINE__, __METHOD__,10);
											} elseif (  TTDate::inWindow( $p_obj->getTimeStamp(), $p_obj->getScheduleObject()->getStartTime(), $ep_obj->getWatchWindow() ) == TRUE ) {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;NOT Within Grace time, SET EXCEPTION: ', __FILE__, __LINE__, __METHOD__,10);

												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												$ef->setPunchID( $p_obj->getID() );
												$ef->setType( $type_id );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											}

										}
									} else {
										Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;NO Schedule Found', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}
						}
						break;
					case 's5': //Out Early
						if ( $plf->getRecordCount() > 0 ) {
							//Loop through each punch, find out if they are scheduled, and if they are in early
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getType() == 10 AND $p_obj->getStatus() == 20 ) { //Normal Out
									if ( !isset($scheduled_id_cache[$p_obj->getID()]) ) {
										$scheduled_id_cache[$p_obj->getID()] = $p_obj->findScheduleID( NULL, $user_date_obj->getUser() );
									}
									if ( $p_obj->setScheduleID( $scheduled_id_cache[$p_obj->getID()] ) == TRUE ) {
										if ( $p_obj->getTimeStamp() < $p_obj->getScheduleObject()->getEndTime() ) {
											if ( TTDate::inWindow( $p_obj->getTimeStamp(), $p_obj->getScheduleObject()->getEndTime(), $ep_obj->getGrace() ) == TRUE ) {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Within Grace time, IGNORE EXCEPTION: ', __FILE__, __LINE__, __METHOD__,10);
											} elseif ( TTDate::inWindow( $p_obj->getTimeStamp(), $p_obj->getScheduleObject()->getEndTime(), $ep_obj->getWatchWindow() ) == TRUE ) {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;NOT Within Grace time, SET EXCEPTION: ', __FILE__, __LINE__, __METHOD__,10);

												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												$ef->setPunchID( $p_obj->getID() );
												$ef->setType( $type_id );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											}

										}
									} else {
										Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;NO Schedule Found', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}
						}
						break;
					case 's6': //Out Late
						if ( $plf->getRecordCount() > 0 ) {
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getType() == 10 AND $p_obj->getStatus() == 20 ) { //Normal Out
									if ( !isset($scheduled_id_cache[$p_obj->getID()]) ) {
										$scheduled_id_cache[$p_obj->getID()] = $p_obj->findScheduleID( NULL, $user_date_obj->getUser() );
									}
									if ( $p_obj->setScheduleID( $scheduled_id_cache[$p_obj->getID()] ) == TRUE ) {
										if ( $p_obj->getTimeStamp() > $p_obj->getScheduleObject()->getEndTime() ) {
											if ( TTDate::inWindow( $p_obj->getTimeStamp(), $p_obj->getScheduleObject()->getEndTime(), $ep_obj->getGrace() ) == TRUE ) {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Within Grace time, IGNORE EXCEPTION: ', __FILE__, __LINE__, __METHOD__,10);
											} elseif ( TTDate::inWindow( $p_obj->getTimeStamp(), $p_obj->getScheduleObject()->getEndTime(), $ep_obj->getWatchWindow() ) == TRUE ) {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;NOT Within Grace time, SET EXCEPTION: ', __FILE__, __LINE__, __METHOD__,10);

												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												$ef->setPunchID( $p_obj->getID() );
												$ef->setType( $type_id );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											}

										}
									} else {
										Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;NO Schedule Found', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}
						}
						break;
					case 'm1': //Missing In Punch
						if ( $plf->getRecordCount() > 0 ) {
							foreach ( $plf as $p_obj ) {
								//Debug::text(' Punch: Status: '. $p_obj->getStatus() .' Punch Control ID: '. $p_obj->getPunchControlID() .' Punch ID: '. $p_obj->getId() .' TimeStamp: '. $p_obj->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);

								if ( $type_id == 5 AND $p_obj->getTimeStamp() < (time()-self::$premature_delay) ) {
									$type_id = 50;
								}

								$punch_pairs[$p_obj->getPunchControlID()][] = array( 'status_id' => $p_obj->getStatus(), 'punch_control_id' => $p_obj->getPunchControlID(), 'punch_id' => $p_obj->getId() );
							}

							if ( isset($punch_pairs) ) {
								foreach($punch_pairs as $punch_control_id => $punch_pair) {
									//Debug::Arr($punch_pair, 'Punch Pair for Control ID:'. $punch_control_id, __FILE__, __LINE__, __METHOD__,10);

									if ( count($punch_pair) != 2 ) {
										Debug::text('aFound Missing Punch: ', __FILE__, __LINE__, __METHOD__,10);

										if ( $punch_pair[0]['status_id'] == 20 ) { //Missing In Punch
											Debug::text('bFound Missing In Punch: ', __FILE__, __LINE__, __METHOD__,10);

											$ef = new ExceptionFactory();
											$ef->setUserDateID( $user_date_id );
											$ef->setExceptionPolicyID( $ep_obj->getId() );
											$ef->setPunchControlID( $punch_pair[0]['punch_control_id'] );
											$ef->setType( $type_id );
											$ef->setEnableDemerits( TRUE );
											if ( $ef->isValid() ) {
												if ( $enable_premature_exceptions == TRUE ) {
													$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
												}
												$ef->Save();
											}

										}
									} else {
										Debug::text('No Missing Punches...', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}
							unset($punch_pairs, $punch_pair);
						}
						break;
					case 'm2': //Missing Out Punch
						if ( $plf->getRecordCount() > 0 ) {
							foreach ( $plf as $p_obj ) {
								Debug::text(' Punch: Status: '. $p_obj->getStatus() .' Punch Control ID: '. $p_obj->getPunchControlID() .' Punch ID: '. $p_obj->getId() .' TimeStamp: '. $p_obj->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);

								if ( $type_id == 5 AND $p_obj->getTimeStamp() < (time()-self::$premature_delay) ) {
									$type_id = 50;
								}

								$punch_pairs[$p_obj->getPunchControlID()][] = array( 'status_id' => $p_obj->getStatus(), 'punch_control_id' => $p_obj->getPunchControlID() );
							}

							if ( isset($punch_pairs) ) {
								foreach($punch_pairs as $punch_control_id => $punch_pair) {
									if ( count($punch_pair) != 2 ) {
										Debug::text('aFound Missing Punch: ', __FILE__, __LINE__, __METHOD__,10);

										if ( $punch_pair[0]['status_id'] == 10 ) { //Missing Out Punch
											Debug::text('bFound Missing Out Punch: ', __FILE__, __LINE__, __METHOD__,10);

											$ef = new ExceptionFactory();
											$ef->setUserDateID( $user_date_id );
											$ef->setExceptionPolicyID( $ep_obj->getId() );
											$ef->setPunchControlID( $punch_pair[0]['punch_control_id'] );
											$ef->setType( $type_id );
											$ef->setEnableDemerits( TRUE );
											if ( $ef->isValid() ) {
												if ( $enable_premature_exceptions == TRUE ) {
													$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
												}
												$ef->Save();
											}

										}
									} else {
										Debug::text('No Missing Punches...', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}
							unset($punch_pairs, $punch_pair);
						}

						break;
					case 'm3': //Missing Lunch In/Out punch
						if ( $plf->getRecordCount() > 0 ) {
							//We need to account for cases where they may punch IN from lunch first, then Out.
							//As well as just a Lunch In punch and nothing else.
							foreach ( $plf as $p_obj ) {
								if ( $type_id == 5 AND $p_obj->getTimeStamp() < (time()-self::$premature_delay) ) {
									$type_id = 50;
								}

								$punches[] = $p_obj;
							}

							if ( isset($punches) AND is_array($punches) ) {
								foreach( $punches as $key => $p_obj ) {
									if ( $p_obj->getType() == 20 ) { //Lunch
										Debug::text(' Punch: Status: '. $p_obj->getStatus() .' Punch Control ID: '. $p_obj->getPunchControlID() .' TimeStamp: '. $p_obj->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);
										if ( $p_obj->getStatus() == 10 ) {
											//Make sure previous punch is Lunch/Out
											if ( !isset($punches[$key-1])
													OR ( isset($punches[$key-1]) AND is_object($punches[$key-1])
															AND ( $punches[$key-1]->getType() != 20
																OR $punches[$key-1]->getStatus() != 20 ) ) ) {
												//Invalid punch
												$invalid_punches[] = array('punch_id' => $p_obj->getId() );
											}
										} else {
											//Make sure next punch is Lunch/In
											if ( !isset($punches[$key+1]) OR ( isset($punches[$key+1]) AND is_object($punches[$key+1]) AND ( $punches[$key+1]->getType() != 20 OR $punches[$key+1]->getStatus() != 10 ) ) ) {
												//Invalid punch
												$invalid_punches[] = array('punch_id' => $p_obj->getId() );
											}
										}
									}
								}
								unset($punches, $key, $p_obj);

								if ( isset($invalid_punches) AND count($invalid_punches) > 0 ) {
									foreach( $invalid_punches as $invalid_punch_arr ) {
										Debug::text('Found Missing Lunch In/Out Punch: ', __FILE__, __LINE__, __METHOD__,10);

										$ef = new ExceptionFactory();
										$ef->setUserDateID( $user_date_id );
										$ef->setExceptionPolicyID( $ep_obj->getId() );
										//$ef->setPunchControlID( $invalid_punch_arr['punch_id'] );
										$ef->setPunchID( $invalid_punch_arr['punch_id'] );
										$ef->setType( $type_id );
										$ef->setEnableDemerits( TRUE );
										if ( $ef->isValid() ) {
											if ( $enable_premature_exceptions == TRUE ) {
												$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
											}
											$ef->Save();
										}
									}
									unset($invalid_punch_arr);
								} else {
									Debug::text('Lunch Punches match up.', __FILE__, __LINE__, __METHOD__,10);
								}
								unset($invalid_punches);
							}
						}
						break;
					case 'm4': //Missing Break In/Out punch
						if ( $plf->getRecordCount() > 0 ) {
							//We need to account for cases where they may punch IN from break first, then Out.
							//As well as just a break In punch and nothing else.
							foreach ( $plf as $p_obj ) {
								if ( $type_id == 5 AND $p_obj->getTimeStamp() < (time()-self::$premature_delay) ) {
									$type_id = 50;
								}

								$punches[] = $p_obj;
							}

							if ( isset($punches) AND is_array($punches) ) {
								foreach( $punches as $key => $p_obj ) {
									if ( $p_obj->getType() == 30 ) { //Break
										Debug::text(' Punch: Status: '. $p_obj->getStatus() .' Type: '. $p_obj->getType() .' Punch Control ID: '. $p_obj->getPunchControlID() .' TimeStamp: '. $p_obj->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);
										if ( $p_obj->getStatus() == 10 ) {
											//Make sure previous punch is Break/Out
											if ( !isset($punches[$key-1])
													OR ( isset($punches[$key-1]) AND is_object($punches[$key-1])
															AND ( $punches[$key-1]->getType() != 30
																OR $punches[$key-1]->getStatus() != 20 ) ) ) {
												//Invalid punch
												$invalid_punches[] = array('punch_id' => $p_obj->getId() );
											}
										} else {
											//Make sure next punch is Break/In
											if ( !isset($punches[$key+1]) OR ( isset($punches[$key+1]) AND is_object($punches[$key+1]) AND ( $punches[$key+1]->getType() != 30 OR $punches[$key+1]->getStatus() != 10 ) ) ) {
												//Invalid punch
												$invalid_punches[] = array('punch_id' => $p_obj->getId() );
											}
										}
									}
								}
								unset($punches, $key, $p_obj);

								if ( isset($invalid_punches) AND count($invalid_punches) > 0 ) {
									foreach( $invalid_punches as $invalid_punch_arr ) {
										Debug::text('Found Missing Break In/Out Punch: ', __FILE__, __LINE__, __METHOD__,10);

										$ef = new ExceptionFactory();
										$ef->setUserDateID( $user_date_id );
										$ef->setExceptionPolicyID( $ep_obj->getId() );
										//$ef->setPunchControlID( $invalid_punch_arr['punch_id'] );
										$ef->setPunchID( $invalid_punch_arr['punch_id'] );
										$ef->setType( $type_id );
										$ef->setEnableDemerits( TRUE );
										if ( $ef->isValid() ) {
											if ( $enable_premature_exceptions == TRUE ) {
												$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
											}
											$ef->Save();
										}
									}
									unset($invalid_punch_arr);
								} else {
									Debug::text('Lunch Punches match up.', __FILE__, __LINE__, __METHOD__,10);
								}
								unset($invalid_punches);
							}
						}
						break;
					case 's7': //Over Scheduled Hours
						if ( $plf->getRecordCount() > 0 ) {
							//This ONLY takes in to account WORKED hours, not paid absence hours.
							$schedule_total_time = 0;

							if ( $slf->getRecordCount() > 0 ) {
								//Check for schedule policy
								foreach ( $slf as $s_obj ) {
									Debug::text(' Schedule Total Time: '. $s_obj->getTotalTime(), __FILE__, __LINE__, __METHOD__,10);

									$schedule_total_time += $s_obj->getTotalTime();
								}

								$daily_total_time = 0;
								if ( $schedule_total_time > 0 ) {
									//Get daily total time.
									$udtlf = new UserDateTotalListFactory();

									//Take into account auto-deduct/add meal policies
									//$udtlf->getByUserDateIdAndStatus( $user_date_id, 20 );
									$udtlf->getByUserDateIdAndStatusAndType( $user_date_id, 10, 10 );
									if ( $udtlf->getRecordCount() > 0 ) {
										foreach( $udtlf as $udt_obj ) {
											$daily_total_time += $udt_obj->getTotalTime();
										}
									}

									Debug::text(' Daily Total Time: '. $daily_total_time .' Schedule Total Time: '. $schedule_total_time, __FILE__, __LINE__, __METHOD__,10);

									if ( $daily_total_time > 0 AND $daily_total_time > ( $schedule_total_time + $ep_obj->getGrace() ) ) {
										Debug::text(' Worked Over Scheduled Hours', __FILE__, __LINE__, __METHOD__,10);

										$ef = new ExceptionFactory();
										$ef->setUserDateID( $user_date_id );
										$ef->setExceptionPolicyID( $ep_obj->getId() );
										$ef->setType( $type_id );
										$ef->setEnableDemerits( TRUE );
										if ( $ef->isValid() ) {
											if ( $enable_premature_exceptions == TRUE ) {
												$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
											}
											$ef->Save();
										}

									} else {
										Debug::text(' DID NOT Work Over Scheduled Hours', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							} else {
								Debug::text(' Not Scheduled', __FILE__, __LINE__, __METHOD__,10);
							}
						}
						break;
					case 's8': //Under Scheduled Hours
						if ( $plf->getRecordCount() > 0 ) {
							//This ONLY takes in to account WORKED hours, not paid absence hours.
							$schedule_total_time = 0;

							if ( $slf->getRecordCount() > 0 ) {
								//Check for schedule policy
								foreach ( $slf as $s_obj ) {
									Debug::text(' Schedule Total Time: '. $s_obj->getTotalTime(), __FILE__, __LINE__, __METHOD__,10);

									$schedule_total_time += $s_obj->getTotalTime();
								}

								$daily_total_time = 0;
								if ( $schedule_total_time > 0 ) {
									//Get daily total time.
									$udtlf = new UserDateTotalListFactory();

									//Take into account auto-deduct/add meal policies
									//$udtlf->getByUserDateIdAndStatus( $user_date_id, 20 );
									$udtlf->getByUserDateIdAndStatusAndType( $user_date_id, 10, 10 );
									if ( $udtlf->getRecordCount() > 0 ) {
										foreach( $udtlf as $udt_obj ) {
											$daily_total_time += $udt_obj->getTotalTime();
										}
									}

									Debug::text(' Daily Total Time: '. $daily_total_time .' Schedule Total Time: '. $schedule_total_time, __FILE__, __LINE__, __METHOD__,10);

									if ( $daily_total_time < ( $schedule_total_time - $ep_obj->getGrace() ) ) {
										Debug::text(' Worked Under Scheduled Hours', __FILE__, __LINE__, __METHOD__,10);

										if ( $type_id == 5 AND $user_date_obj->getDateStamp() < TTDate::getBeginDayEpoch( (time()-self::$premature_delay) ) ) {
											$type_id = 50;
										}

										$ef = new ExceptionFactory();
										$ef->setUserDateID( $user_date_id );
										$ef->setExceptionPolicyID( $ep_obj->getId() );
										$ef->setType( $type_id );
										$ef->setEnableDemerits( TRUE );
										if ( $ef->isValid() ) {
											if ( $enable_premature_exceptions == TRUE ) {
												$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
											}
											$ef->Save();
										}

									} else {
										Debug::text(' DID NOT Work Under Scheduled Hours', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							} else {
								Debug::text(' Not Scheduled', __FILE__, __LINE__, __METHOD__,10);
							}
						}
						break;
					case 'o1': //Over Daily Time.
						if ( $plf->getRecordCount() > 0 ) {
							//This ONLY takes in to account WORKED hours, not paid absence hours.
							$daily_total_time = 0;

							//Get daily total time.
							$udtlf = new UserDateTotalListFactory();

							//Take into account auto-deduct/add meal policies
							$udtlf->getByUserDateIdAndStatusAndType( $user_date_id, 10, 10 );
							if ( $udtlf->getRecordCount() > 0 ) {
								foreach( $udtlf as $udt_obj ) {
									$daily_total_time += $udt_obj->getTotalTime();
								}
							}

							Debug::text(' Daily Total Time: '. $daily_total_time .' Watch Window: '. $ep_obj->getWatchWindow() .' User Date ID: '. $user_date_id, __FILE__, __LINE__, __METHOD__,10);

							if ( $daily_total_time > 0 AND $daily_total_time > $ep_obj->getWatchWindow() ) {
								Debug::text(' Worked Over Daily Hours', __FILE__, __LINE__, __METHOD__,10);

								$ef = new ExceptionFactory();
								$ef->setUserDateID( $user_date_id );
								$ef->setExceptionPolicyID( $ep_obj->getId() );
								$ef->setType( $type_id );
								$ef->setEnableDemerits( TRUE );
								if ( $ef->isValid() ) {
									if ( $enable_premature_exceptions == TRUE ) {
										$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
									}
									$ef->Save();
								}

							} else {
								Debug::text(' DID NOT Work Over Scheduled Hours', __FILE__, __LINE__, __METHOD__,10);
							}
						}
						break;
					case 'o2': //Over Weekly Time.
					case 's9': //Over Weekly Scheduled Time.
						if ( $plf->getRecordCount() > 0 ) {
							//Get Pay Period Schedule info
							if ( is_object($user_date_obj->getPayPeriodObject())
									AND is_object($user_date_obj->getPayPeriodObject()->getPayPeriodScheduleObject()) ) {
								$start_week_day_id = $user_date_obj->getPayPeriodObject()->getPayPeriodScheduleObject()->getStartWeekDay();
							} else {
								$start_week_day_id = 0;
							}
							Debug::text('Start Week Day ID: '. $start_week_day_id, __FILE__, __LINE__, __METHOD__, 10);

							$weekly_scheduled_total_time = 0;

							if ( strtolower( $ep_obj->getType() ) == 's9' ) {
								$tmp_slf = new ScheduleListFactory();
								$tmp_slf->getByUserIdAndStartDateAndEndDate( $user_date_obj->getUser(), TTDate::getBeginWeekEpoch($user_date_obj->getDateStamp(), $start_week_day_id), $user_date_obj->getDateStamp() );
								if ( $tmp_slf->getRecordCount() > 0 ) {
									foreach( $tmp_slf as $s_obj ) {
										$weekly_scheduled_total_time += $s_obj->getTotalTime();
									}
								}
								unset($tmp_slf, $s_obj);
							}

							//This ONLY takes in to account WORKED hours, not paid absence hours.
							$weekly_total_time = 0;

							//Get daily total time.
							$udtlf = new UserDateTotalListFactory();
							$weekly_total_time = $udtlf->getWorkedTimeSumByUserIDAndStartDateAndEndDate( $user_date_obj->getUser(), TTDate::getBeginWeekEpoch($user_date_obj->getDateStamp(), $start_week_day_id), $user_date_obj->getDateStamp() );

							Debug::text(' Weekly Total Time: '. $weekly_total_time .' Weekly Scheduled Total Time: '. $weekly_scheduled_total_time .' Watch Window: '. $ep_obj->getWatchWindow() .' Grace: '. $ep_obj->getGrace() .' User Date ID: '. $user_date_id, __FILE__, __LINE__, __METHOD__,10);

							if ( ( strtolower( $ep_obj->getType() ) == 'o2' AND $weekly_total_time > 0 AND $weekly_total_time > $ep_obj->getWatchWindow() )
									OR ( strtolower( $ep_obj->getType() ) == 's9' AND $weekly_total_time > 0 AND $weekly_total_time > ( $weekly_scheduled_total_time + $ep_obj->getGrace() ) ) ) {
								Debug::text(' Worked Over Weekly Hours', __FILE__, __LINE__, __METHOD__,10);

								$ef = new ExceptionFactory();
								$ef->setUserDateID( $user_date_id );
								$ef->setExceptionPolicyID( $ep_obj->getId() );
								$ef->setType( $type_id );
								$ef->setEnableDemerits( TRUE );
								if ( $ef->isValid() ) {
									if ( $enable_premature_exceptions == TRUE ) {
										$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
									}
									$ef->Save();
								}
							} else {
								Debug::text(' DID NOT Work Over Scheduled Hours', __FILE__, __LINE__, __METHOD__,10);
							}
						}

						break;
					case 'l1': //Long Lunch
					case 'l2': //Short Lunch
						if ( $plf->getRecordCount() > 0 ) {
							//Get all lunch punches.
							$pair = 0;
							$x = 0;
							$out_for_lunch = FALSE;
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getStatus() == 20 AND $p_obj->getType() == 20 ) {
									$lunch_out_timestamp = $p_obj->getTimeStamp();
									$lunch_punch_arr[$pair]['punch_id'] = $p_obj->getId();
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
							}

							if ( isset($lunch_punch_arr) ) {
								Debug::Arr($lunch_punch_arr, 'Lunch Punch Array: ', __FILE__, __LINE__, __METHOD__,10);

								foreach( $lunch_punch_arr as $pair => $time_stamp_arr ) {
									if ( isset($time_stamp_arr[10]) AND isset($time_stamp_arr[20]) ) {
										$lunch_total_time = bcsub($time_stamp_arr[10], $time_stamp_arr[20] );
										Debug::text(' Lunch Total Time: '. $lunch_total_time, __FILE__, __LINE__, __METHOD__, 10);

										if ( !isset($scheduled_id_cache[$p_obj->getID()]) ) {
											$scheduled_id_cache[$p_obj->getID()] = $p_obj->findScheduleID( NULL, $user_date_obj->getUser() );
										}

										//Check to see if they have a schedule policy
										if ( $p_obj->setScheduleID( $scheduled_id_cache[$p_obj->getID()] ) == TRUE
												AND is_object( $p_obj->getScheduleObject() ) == TRUE
												AND is_object( $p_obj->getScheduleObject()->getSchedulePolicyObject() ) == TRUE ) {
											$mp_obj = $p_obj->getScheduleObject()->getSchedulePolicyObject()->getMealPolicyObject();
										} else {
											$mplf = new MealPolicyListFactory();
											$mplf->getByPolicyGroupUserId( $user_date_obj->getUserObject()->getId() );
											if ( $mplf->getRecordCount() > 0 ) {
												Debug::text('Found Meal Policy to apply.', __FILE__, __LINE__, __METHOD__, 10);
												$mp_obj = $mplf->getCurrent();
											}
										}

										if ( isset($mp_obj) AND is_object($mp_obj) ) {
											$meal_policy_lunch_time = $mp_obj->getAmount();
											Debug::text('Meal Policy Time: '. $meal_policy_lunch_time, __FILE__, __LINE__, __METHOD__, 10);

											$add_exception = FALSE;
											if ( strtolower( $ep_obj->getType() ) == 'l1'
													AND $meal_policy_lunch_time > 0
													AND $lunch_total_time > 0
													AND $lunch_total_time > ($meal_policy_lunch_time + $ep_obj->getGrace() ) ) {
												$add_exception = TRUE;
											} elseif ( strtolower( $ep_obj->getType() ) == 'l2'
													AND $meal_policy_lunch_time > 0
													AND $lunch_total_time > 0
													AND $lunch_total_time < ( $meal_policy_lunch_time - $ep_obj->getGrace() ) ) {
												$add_exception = TRUE;
											}

											if ( $add_exception == TRUE ) {
												Debug::text('Adding Exception!', __FILE__, __LINE__, __METHOD__, 10);

												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												if ( isset($time_stamp_arr['punch_id']) ) {
													$ef->setPunchID( $time_stamp_arr['punch_id'] );
												}
												$ef->setType( $type_id );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											} else {
												Debug::text('Not Adding Exception!', __FILE__, __LINE__, __METHOD__, 10);
											}
										}

									} else {
										Debug::text(' Lunch Punches not paired... Skipping!', __FILE__, __LINE__, __METHOD__, 10);
									}
								}
							} else {
								Debug::text(' No Lunch Punches found, or none are paired.', __FILE__, __LINE__, __METHOD__, 10);
							}
						}
						break;
					case 'l3': //No Lunch
						if ( $plf->getRecordCount() > 0 ) {
							//If they are scheduled or not, we can check for a meal policy and base our
							//decision off that. We don't want a No Lunch exception on a 3hr shift though.
							//Also ignore this exception if the lunch is auto-deduct.
							$daily_total_time = 0;

							$udtlf = new UserDateTotalListFactory();
							$udtlf->getByUserDateIdAndStatus( $user_date_id, 20 );
							if ( $udtlf->getRecordCount() > 0 ) {
								foreach( $udtlf as $udt_obj ) {
									$daily_total_time += $udt_obj->getTotalTime();
								}
							}
							Debug::text('Day Total Time: '. $daily_total_time, __FILE__, __LINE__, __METHOD__,10);

							if ( $daily_total_time > 0 ) {
								//Check for lunch punch.
								$lunch_punch = FALSE;
								foreach ( $plf as $p_obj ) {
									if ( $p_obj->getType() == 20 ) {
										Debug::text('Found Lunch Punch: '. $p_obj->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);
										$lunch_punch = TRUE;
										break;
									}
								}

								if ( $lunch_punch == FALSE ) {
									Debug::text('DID NOT Find Lunch Punch... Checking meal policies. ', __FILE__, __LINE__, __METHOD__,10);

									//Use scheduled meal policy first.
									if ( $slf->getRecordCount() > 0 ) {
										Debug::text('Schedule Found...', __FILE__, __LINE__, __METHOD__,10);
										foreach ( $slf as $s_obj ) {
											if ( $s_obj->getSchedulePolicyObject() !== FALSE
													AND $s_obj->getSchedulePolicyObject()->getMealPolicyObject() !== FALSE
													AND $s_obj->getSchedulePolicyObject()->getMealPolicyObject()->getType() != 10 ) {
												Debug::text('Found Schedule Meal Policy... Trigger Time: '. $s_obj->getSchedulePolicyObject()->getMealPolicyObject()->getTriggerTime(), __FILE__, __LINE__, __METHOD__,10);
												if ( $daily_total_time > $s_obj->getSchedulePolicyObject()->getMealPolicyObject()->getTriggerTime() ) {
													Debug::text('Daily Total Time is After Schedule Meal Policy Trigger Time: ', __FILE__, __LINE__, __METHOD__,10);

													$ef = new ExceptionFactory();
													$ef->setUserDateID( $user_date_id );
													$ef->setExceptionPolicyID( $ep_obj->getId() );
													$ef->setType( $type_id );
													$ef->setEnableDemerits( TRUE );
													if ( $ef->isValid() ) {
														if ( $enable_premature_exceptions == TRUE ) {
															$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
														}
														$ef->Save();
													}
												}
											} else {
												Debug::text('Schedule Meal Policy does not exist, or is auto-deduct?', __FILE__, __LINE__, __METHOD__,10);
											}
										}
									} else {
										Debug::text('No Schedule Found...', __FILE__, __LINE__, __METHOD__,10);

										//Check if they have a meal policy, with no schedule.
										$mplf = new MealPolicyListFactory();
										$mplf->getByPolicyGroupUserId( $user_date_obj->getUser() );
										if ( $mplf->getRecordCount() > 0 ) {
											Debug::text('Found UnScheduled Meal Policy...', __FILE__, __LINE__, __METHOD__,10);

											$m_obj = $mplf->getCurrent();
											if ( $daily_total_time > $m_obj->getTriggerTime()
													AND $m_obj->getType() == 20 ) {
												Debug::text('Daily Total Time is After Schedule Meal Policy Trigger Time: '. $m_obj->getTriggerTime(), __FILE__, __LINE__, __METHOD__,10);

												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												$ef->setType( $type_id );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											} else {
												Debug::text('Auto-deduct meal policy, ignorning this exception.', __FILE__, __LINE__, __METHOD__,10);
											}
										} else {
											//There is no  meal policy or schedule policy with a meal policy assigned to it
											//With out this we could still apply No Lunch exceptions, but they will happen even on
											//a 2minute shift.
											Debug::text('No meal policy, applying No Lunch exception.', __FILE__, __LINE__, __METHOD__,10);

											$ef = new ExceptionFactory();
											$ef->setUserDateID( $user_date_id );
											$ef->setExceptionPolicyID( $ep_obj->getId() );
											$ef->setType( $type_id );
											$ef->setEnableDemerits( TRUE );
											if ( $ef->isValid() ) {
												if ( $enable_premature_exceptions == TRUE ) {
													$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
												}
												$ef->Save();
											}
										}
									}

								} else {
									Debug::text('Found Lunch Punch... Ignoring this exception. ', __FILE__, __LINE__, __METHOD__,10);
								}
							}
						}
						break;
					case 'b1': //Long Break
					case 'b2': //Short Break
						if ( $plf->getRecordCount() > 0 ) {
							//Get all break punches.
							$pair = 0;
							$x = 0;
							$out_for_break = FALSE;
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getStatus() == 20 AND $p_obj->getType() == 30 ) {
									$break_out_timestamp = $p_obj->getTimeStamp();
									$break_punch_arr[$pair]['punch_id'] = $p_obj->getId();
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
							}
							unset($pair);

							if ( isset($break_punch_arr) ) {
								Debug::Arr($break_punch_arr, 'Break Punch Array: ', __FILE__, __LINE__, __METHOD__,10);

								foreach( $break_punch_arr as $pair => $time_stamp_arr ) {
									if ( isset($time_stamp_arr[10]) AND isset($time_stamp_arr[20]) ) {
										$break_total_time = bcsub($time_stamp_arr[10], $time_stamp_arr[20] );
										Debug::text(' Break Total Time: '. $break_total_time, __FILE__, __LINE__, __METHOD__, 10);

										if ( !isset($scheduled_id_cache[$p_obj->getID()]) ) {
											$scheduled_id_cache[$p_obj->getID()] = $p_obj->findScheduleID( NULL, $user_date_obj->getUser() );
										}

										//Check to see if they have a schedule policy
										$bplf = new BreakPolicyListFactory();
										if ( $p_obj->setScheduleID( $scheduled_id_cache[$p_obj->getID()] ) == TRUE
												AND is_object( $p_obj->getScheduleObject() ) == TRUE
												AND is_object( $p_obj->getScheduleObject()->getSchedulePolicyObject() ) == TRUE ) {
											$break_policy_ids = $p_obj->getScheduleObject()->getSchedulePolicyObject()->getBreakPolicyObject();
											$bplf->getByIdAndCompanyId( $break_policy_ids, $user_date_obj->getUserObject()->getCompany() );
										} else {
											$bplf->getByPolicyGroupUserId( $user_date_obj->getUser() );
										}
										unset($break_policy_ids);

										if ( $bplf->getRecordCount() > 0 ) {
											Debug::text('Found Break Policy(ies) to apply: '. $bplf->getRecordCount() .' Pair: '. $pair, __FILE__, __LINE__, __METHOD__, 10);

											foreach( $bplf as $bp_obj ) {
												$bp_objs[] = $bp_obj;
											}
											unset($bplf, $bp_obj);

											if ( isset($bp_objs[$pair]) AND is_object($bp_objs[$pair]) ) {
												$bp_obj = $bp_objs[$pair];

												$break_policy_break_time = $bp_obj->getAmount();
												Debug::text('Break Policy Time: '. $break_policy_break_time .' ID: '. $bp_obj->getID(), __FILE__, __LINE__, __METHOD__, 10);

												$add_exception = FALSE;
												if ( strtolower( $ep_obj->getType() ) == 'b1'
														AND $break_policy_break_time > 0
														AND $break_total_time > 0
														AND $break_total_time > ($break_policy_break_time + $ep_obj->getGrace() ) ) {
													$add_exception = TRUE;
												} elseif ( strtolower( $ep_obj->getType() ) == 'b2'
														AND $break_policy_break_time > 0
														AND $break_total_time > 0
														AND $break_total_time < ( $break_policy_break_time - $ep_obj->getGrace() ) ) {
													$add_exception = TRUE;
												}

												if ( $add_exception == TRUE ) {
													Debug::text('Adding Exception! '. $ep_obj->getType(), __FILE__, __LINE__, __METHOD__, 10);

													$ef = new ExceptionFactory();
													$ef->setUserDateID( $user_date_id );
													$ef->setExceptionPolicyID( $ep_obj->getId() );
													if ( isset($time_stamp_arr['punch_id']) ) {
														$ef->setPunchID( $time_stamp_arr['punch_id'] );
													}
													$ef->setType( $type_id );
													$ef->setEnableDemerits( TRUE );
													if ( $ef->isValid() ) {
														if ( $enable_premature_exceptions == TRUE ) {
															$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
														}
														$ef->Save();
													}
												} else {
													Debug::text('Not Adding Exception!', __FILE__, __LINE__, __METHOD__, 10);
												}

												unset($bp_obj);
											}
											unset( $bp_objs );
										}
									} else {
										Debug::text(' Break Punches not paired... Skipping!', __FILE__, __LINE__, __METHOD__, 10);
									}
								}
							} else {
								Debug::text(' No Break Punches found, or none are paired.', __FILE__, __LINE__, __METHOD__, 10);
							}
						}
						break;
					case 'b3': //Too Many Breaks
					case 'b4': //Too Few Breaks
						if ( $plf->getRecordCount() > 0 ) {
							//Get all break punches.
							$pair = 0;
							$x = 0;
							$out_for_break = FALSE;
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getStatus() == 20 AND $p_obj->getType() == 30 ) {
									$break_out_timestamp = $p_obj->getTimeStamp();
									$break_punch_arr[$pair]['punch_id'] = $p_obj->getId();
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
							}
							unset($pair);


							if ( isset($break_punch_arr) ) {
								$total_breaks = count($break_punch_arr);

								Debug::Arr($break_punch_arr, 'Break Punch Array: ', __FILE__, __LINE__, __METHOD__,10);

								foreach( $break_punch_arr as $pair => $time_stamp_arr ) {
									if ( isset($time_stamp_arr[10]) AND isset($time_stamp_arr[20]) ) {
										$break_total_time = bcsub($time_stamp_arr[10], $time_stamp_arr[20] );
										Debug::text(' Break Total Time: '. $break_total_time, __FILE__, __LINE__, __METHOD__, 10);

										if ( !isset($scheduled_id_cache[$p_obj->getID()]) ) {
											$scheduled_id_cache[$p_obj->getID()] = $p_obj->findScheduleID( NULL, $user_date_obj->getUser() );
										}

										//Check to see if they have a schedule policy
										$bplf = new BreakPolicyListFactory();
										if ( $p_obj->setScheduleID( $scheduled_id_cache[$p_obj->getID()] ) == TRUE
												AND is_object( $p_obj->getScheduleObject() ) == TRUE
												AND is_object( $p_obj->getScheduleObject()->getSchedulePolicyObject() ) == TRUE ) {
											$break_policy_ids = $p_obj->getScheduleObject()->getSchedulePolicyObject()->getBreakPolicyObject();
											$bplf->getByIdAndCompanyId( $break_policy_ids, $user_date_obj->getUserObject()->getCompany() );
										} else {
											$bplf->getByPolicyGroupUserId( $user_date_obj->getUser() );
										}
										unset($break_policy_ids);

										$allowed_breaks = $bplf->getRecordCount();

										$add_exception = FALSE;
										if ( strtolower( $ep_obj->getType() ) == 'b3' AND $total_breaks > $allowed_breaks ) {
											Debug::text(' Too many breaks taken...', __FILE__, __LINE__, __METHOD__, 10);
											$add_exception = TRUE;
										} elseif ( strtolower( $ep_obj->getType() ) == 'b4' AND $total_breaks < $allowed_breaks )  {
											Debug::text(' Too few breaks taken...', __FILE__, __LINE__, __METHOD__, 10);
											$add_exception = TRUE;
										} else {
											Debug::text(' Proper number of breaks taken...', __FILE__, __LINE__, __METHOD__, 10);
										}

										if ( $add_exception == TRUE
												AND ( strtolower( $ep_obj->getType() ) == 'b4'
													 OR ( strtolower( $ep_obj->getType() ) == 'b3' AND $pair > ($allowed_breaks-1) )  ) ) {
											Debug::text('Adding Exception! '. $ep_obj->getType(), __FILE__, __LINE__, __METHOD__, 10);

											$ef = new ExceptionFactory();
											$ef->setUserDateID( $user_date_id );
											$ef->setExceptionPolicyID( $ep_obj->getId() );
											if ( isset($time_stamp_arr['punch_id']) AND strtolower( $ep_obj->getType() ) == 'b3' ) {
												$ef->setPunchID( $time_stamp_arr['punch_id'] );
											}
											$ef->setType( $type_id );
											$ef->setEnableDemerits( TRUE );
											if ( $ef->isValid() ) {
												if ( $enable_premature_exceptions == TRUE ) {
													$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
												}
												$ef->Save();
											}
										} else {
											Debug::text('Not Adding Exception!', __FILE__, __LINE__, __METHOD__, 10);
										}

									}
								}
							}
						}
						break;
					case 'j1': //Not Allowed on Job
						if ( $plf->getRecordCount() > 0 ) {
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getStatus() == 10 ) { //In punches
									if ( is_object( $p_obj->getPunchControlObject() ) AND $p_obj->getPunchControlObject()->getJob() > 0 ) {
										//Found job punch, check job settings.
										$jlf = new JobListFactory();
										$jlf->getById( $p_obj->getPunchControlObject()->getJob() );
										if ( $jlf->getRecordCount() > 0 ) {
											$j_obj = $jlf->getCurrent();

											if ( $j_obj->isAllowedUser( $user_date_obj->getUser() ) == FALSE ) {
												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												$ef->setType( $type_id );
												$ef->setPunchControlId( $p_obj->getPunchControlId() );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											} else {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;User allowed on Job!', __FILE__, __LINE__, __METHOD__,10);
											}
										} else {
											Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Job not found!', __FILE__, __LINE__, __METHOD__,10);
										}
									} else {
										Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Not a Job Punch...', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}
							unset($j_obj);
						}
						break;
					case 'j2': //Not Allowed on Task
						if ( $plf->getRecordCount() > 0 ) {
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getStatus() == 10 ) { //In punches
									if ( is_object( $p_obj->getPunchControlObject() ) AND $p_obj->getPunchControlObject()->getJob() > 0 AND $p_obj->getPunchControlObject()->getJobItem() > 0 ) {
										//Found job punch, check job settings.
										$jlf = new JobListFactory();
										$jlf->getById( $p_obj->getPunchControlObject()->getJob() );
										if ( $jlf->getRecordCount() > 0 ) {
											$j_obj = $jlf->getCurrent();

											if ( $j_obj->isAllowedItem( $p_obj->getPunchControlObject()->getJobItem() ) == FALSE ) {
												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												$ef->setType( $type_id );
												$ef->setPunchControlId( $p_obj->getPunchControlId() );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											} else {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Job item allowed on job!', __FILE__, __LINE__, __METHOD__,10);
											}
										} else {
											Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Job not found!', __FILE__, __LINE__, __METHOD__,10);
										}
									} else {
										Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Not a Job Punch...', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}

							unset($j_obj);
						}
						break;
					case 'j3': //Job already completed
						if ( $plf->getRecordCount() > 0 ) {
							foreach ( $plf as $p_obj ) {
								if ( $p_obj->getStatus() == 10 ) { //In punches
									if ( is_object( $p_obj->getPunchControlObject() ) AND $p_obj->getPunchControlObject()->getJob() > 0 ) {
										//Found job punch, check job settings.
										$jlf = new JobListFactory();
										$jlf->getById( $p_obj->getPunchControlObject()->getJob() );
										if ( $jlf->getRecordCount() > 0 ) {
											$j_obj = $jlf->getCurrent();

											//Status is completed and the User Date Stamp is greater then the job end date.
											//If no end date is set, ignore this.
											if ( $j_obj->getStatus() == 30 AND $j_obj->getEndDate() != FALSE AND $user_date_obj->getDateStamp() > $j_obj->getEndDate() ) {
												$ef = new ExceptionFactory();
												$ef->setUserDateID( $user_date_id );
												$ef->setExceptionPolicyID( $ep_obj->getId() );
												$ef->setType( $type_id );
												$ef->setPunchControlId( $p_obj->getPunchControlId() );
												$ef->setEnableDemerits( TRUE );
												if ( $ef->isValid() ) {
													if ( $enable_premature_exceptions == TRUE ) {
														$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
													}
													$ef->Save();
												}
											} else {
												Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Job Not Completed!', __FILE__, __LINE__, __METHOD__,10);
											}
										} else {
											Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Job not found!', __FILE__, __LINE__, __METHOD__,10);
										}
									} else {
										Debug::text('&nbsp;&nbsp;&nbsp;&nbsp;Not a Job Punch...', __FILE__, __LINE__, __METHOD__,10);
									}
								}
							}
							unset($j_obj);
						}
						break;
					case 'j4': //No Job or Task
						if ( $plf->getRecordCount() > 0 ) {
							foreach ( $plf as $p_obj ) {
								//In punches only
								if ( $p_obj->getStatus() == 10 AND is_object( $p_obj->getPunchControlObject() )
										AND
										( $p_obj->getPunchControlObject()->getJob() == ''
											OR $p_obj->getPunchControlObject()->getJob() == 0
											OR $p_obj->getPunchControlObject()->getJob() == FALSE
											OR $p_obj->getPunchControlObject()->getJobItem() == ''
											OR $p_obj->getPunchControlObject()->getJobItem() == 0
											OR $p_obj->getPunchControlObject()->getJobItem() == FALSE
										) ) {

									$ef = new ExceptionFactory();
									$ef->setUserDateID( $user_date_id );
									$ef->setExceptionPolicyID( $ep_obj->getId() );
									$ef->setType( $type_id );
									$ef->setPunchControlId( $p_obj->getPunchControlId() );
									$ef->setPunchId( $p_obj->getId() );
									$ef->setEnableDemerits( TRUE );
									if ( $ef->isValid() ) {
										if ( $enable_premature_exceptions == TRUE ) {
											$ef->emailException( $user_date_obj->getUserObject(), $user_date_obj, $ep_obj );
										}
										$ef->Save();
									}
								}
							}
						}
						break;
					default:
						Debug::text('BAD, should never get here: ', __FILE__, __LINE__, __METHOD__,10);
						break;
				}
			}
		}

		$profiler->stopTimer( "ExceptionPolicy::calcExceptions()");

		return TRUE;
	}

	function Validate() {
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
						case 'is_enabled_watch_window':
						case 'is_enabled_grace':
							$function = str_replace('_', '', $variable);
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

}
?>
