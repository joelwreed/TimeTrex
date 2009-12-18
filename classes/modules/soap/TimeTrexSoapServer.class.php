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
 * $Id: TimeTrexSoapServer.class.php 3021 2009-11-11 23:33:03Z ipso $
 * $Date: 2009-11-11 15:33:03 -0800 (Wed, 11 Nov 2009) $
 */

/**
 * @package Module_SOAP
 */
class TimeTrexSoapServer extends TimeTrexSoapServerUnAuthenticated {
	var $authentication = NULL;
	var $permission_obj = NULL;

	var $user_obj = NULL;
	var $company_obj = NULL;
	var $schedule_shift_obj = NULL;
	var $shift_obj = NULL;
	var $station_type = NULL;

	function __construct() {
		global $authentication, $current_user, $current_company, $current_station;

		$this->authentication = $authentication;

		$this->permission_obj = new Permission();

		$this->user_obj = $authentication->getObject();

		$current_user = $this->getUserObject();
		$current_company = $this->getCompanyObject();

		//Set Date Prefs
		$current_user->getUserPreferenceObject()->setDateTimePreferences();

		return TRUE;
	}

	function getUserObject() {
		return $this->user_obj;
	}

	function getUserPreferenceObject() {
		return $this->getUserObject()->getUserPreferenceObject();
	}

	function getStationObject() {
		$station_id = trim($_GET['StationID']);

		if ( $station_id == '' ) {
			Debug::text('Station ID NOT SET! '. $station_id, __FILE__, __LINE__, __METHOD__, 10);
			return FALSE;
		}

		$slf = new StationListFactory();
		$slf->getByStationIdandCompanyId( $station_id, $this->getCompanyObject()->getId() );
		$current_station = $slf->getCurrent();
		unset($slf);

		return $current_station;
	}

	function getCompanyObject() {
		$clf = new CompanyListFactory();
		$current_company = $clf->getByID( $this->getUserObject()->getCompany() )->getCurrent();

		return $current_company;
	}

	function getScheduleShiftObject() {
		$sslf = new ScheduleShiftListFactory();
		$schedule_shift_obj = $sslf->getByUserIdAndCurrentScheduleShift( $this->getUserObject()->getId() )->getCurrent();

		Debug::text('Schedule Shift ID: '. $schedule_shift_obj->getId(), __FILE__, __LINE__, __METHOD__, 10);

		return $schedule_shift_obj;
	}

	function getPermissionObject() {
		return $this->permission_obj;
	}

	function getClientVersion() {
		if ( isset($_GET['v']) AND $_GET['v'] != '' ) {
			return $_GET['v'];
		}

		return FALSE;
	}

	function helloworld2($text) {
		return "Hello World22: $text - ". $this->getUserFullName() ."";
	}

	function getUserPreferenceData() {
		$retarr = array(
						'date_format' => $this->getUserPreferenceObject()->getDateFormat(),
						'time_format' => $this->getUserPreferenceObject()->getTimeFormat(),
						'time_unit_format' => $this->getUserPreferenceObject()->getTimeUnitFormat(),
						);

		return $retarr;
	}

	function StationCheckAllowed() {
		if ( is_object( $this->getStationObject() ) ) {
			return $this->getStationObject()->checkAllowed( $this->getUserObject()->getId(), $this->getStationObject()->getStation(), $this->getStationObject()->getType() );
		}

		return FALSE;
	}

	function setStation($source, $station, $description = NULL, $type = 'PHONE') {
		Debug::text('Setting Station: ('. $station .') Source: '. $source .' Description: '. $description .' Type: '. $type, __FILE__, __LINE__, __METHOD__, 10);

		if ( $type == '' ) {
			return FALSE;
		}

		//Make sure we don't strtolower() type, as it will cause the lookup to fail.
		$type = trim($type);

		//We using SOAP, we always have the IP address, always set it unless we're using
		//the phone punch in.
		if ( strtolower($type) == 'phone' OR $type == 20 ) {
			$source = Misc::parseCallerID( $source );
			$station = Misc::parseCallerID( $station );
			Debug::text('Filtered Source: '. $source .' Station: '. $station, __FILE__, __LINE__, __METHOD__, 10);
		} else {
			$source = $_SERVER['REMOTE_ADDR'];
		}

		if ($source == '') {
			$source = 'Unavailable';
		}

		if ( $description == '' ) {
			$description = 'N/A';
		}

		if ($source == '') {
			$source = NULL;
		}

		$slf = new StationListFactory();
		$slf->getByStationIdandCompanyId( $station, $this->getCompanyObject()->getId() );
		$current_station = $slf->getCurrent();
		unset($slf);

		if ( $current_station->isNew() ) {
			Debug::text('Station not found... Adding new one...', __FILE__, __LINE__, __METHOD__, 10);

			$sf = new StationFactory();

			$sf->setCompany( $this->getCompanyObject()->getId() );
			$sf->setStatus( 'ENABLED' );
			$sf->setType( $type );

			//If this is a new iButton,Fingerprint, or Barcode station, default to 'ANY' for the source, so we aren't restricted by IP.
			if ( in_array( $sf->getType(), array(30,40,50) ) ) {
				$sf->setSource( 'ANY' );
			} else {
				$sf->setSource( $source );
			}
			$sf->setStation( $station );
			$sf->setDescription( $description );

			//If this is a new iButton,Fingerprint, or Barcode station, default to allow all employees.
			if ( in_array( $sf->getType(), array(30,40,50) ) ) {
				$sf->setGroupSelectionType( 10 );
				$sf->setBranchSelectionType( 10 );
				$sf->setDepartmentSelectionType( 10 );
			}

			if ( $sf->isValid() ) {
				if ( $sf->Save(FALSE) ) {
					//return $source;
					return $sf->getStation();
				}
			}
		} else {
			Debug::text('Station FOUND!', __FILE__, __LINE__, __METHOD__, 10);
			return $current_station->getStation();
		}

		return FALSE;
	}

	function getUserFullName() {
		return $this->getUserObject()->getFullName();
	}

	function getEnrollUsers() {
		//Check to make sure the logged in user has edit_advance permissions
		//Take into account the station allowed employees, as well as child employees.
		//This is important for companies with many thousands of employees.
		if ( $this->getPermissionObject()->Check('user','enroll') OR $this->getPermissionObject()->Check('user','enroll_child') ) {
			$ulf = new UserListFactory();
			$user_list = $ulf->getByCompanyIdArray( $this->getUserObject()->getCompany(), FALSE, FALSE);

			//For ease in Java.
			$user_list= array_flip($user_list);

			return $user_list;
		}

		return FALSE;
	}

	function enroll($user_id, $id, $type = 'iButton', $number = 0, $extra_value = NULL ) {
		Debug::text('Client Version: '. $this->getClientVersion() .' User ID : '. $user_id .' ID: '. substr($id,0,100) .' Type: '. $type .' Number: '. $number, __FILE__, __LINE__, __METHOD__, 10);

		$user_id = trim($user_id);
		$id = trim($id);
		$type = strtolower($type);
		$number = trim($number);

		$ulf = new UserListFactory();

		if ( version_compare( $this->getClientVersion(), '2.7.0', '<' ) ) {
			Debug::text('aVersion: '. $this->getClientVersion(), __FILE__, __LINE__, __METHOD__, 10);

			//Fingerprints can send $id=NULL to clear an enrollment.
			if ( strpos( $type, 'finger_print' ) === FALSE AND $id == '') {
				Debug::text('ID is not valid: '. $id, __FILE__, __LINE__, __METHOD__, 10);
				return FALSE;
			}

			//Check to make sure the logged in user has edit_advance permissions
			if ( $this->getPermissionObject()->Check('user','enroll') OR $this->getPermissionObject()->Check('user','enroll_child') ) {
				Debug::text('User : '. $this->getUserObject()->getFullName() .' has Enroll permission', __FILE__, __LINE__, __METHOD__, 10);

				switch( $type ) {
					case 'finger_print_1':
					case 'finger_print_2':
					case 'finger_print_3':
					case 'finger_print_4':
						$enroll_user_obj = $ulf->getByIdAndCompanyId( $user_id, $this->getUserObject()->getCompany() );

						if ( $enroll_user_obj->getRecordCount() == 1 ) {
							Debug::text('Enroller User ID : '. $user_id .' FOUND', __FILE__, __LINE__, __METHOD__, 10);

							$enroll_user_obj = $enroll_user_obj->getCurrent();

							switch ( $type ) {
								case 'finger_print_1':
									$number = 10;
									break;
								case 'finger_print_2':
									$number = 20;
									break;
								case 'finger_print_3':
									$number = 30;
									break;
								case 'finger_print_4':
									$number = 40;
									break;
							}

							$uilf = new UserIdentificationListFactory();
							$uilf->getByUserIdAndTypeIdAndNumber($enroll_user_obj->getID(), 20, $number );
							if ( $uilf->getRecordCount() > 0 ) {
								$uif = $uilf->getCurrent();
							} else {
								$uif = new UserIdentificationFactory();
							}

							if ( $id == '' AND $uilf->getRecordCount() > 0 ) {
								Debug::text('Deleting Fingerprint...', __FILE__, __LINE__, __METHOD__, 10);

								$uif->setDeleted(TRUE);
								if ( $uif->isValid() ) {
									if ( $uif->save() ) {
										return TRUE;
									}
								}
							} elseif ( $id != '' ) {
								$uif->setUser( $enroll_user_obj->getId() );
								$uif->setType( 20 ); //Griaule
								$uif->setNumber( $number );
								$uif->setValue( $id );
								if ( $uif->isValid() ) {
									if ( $uif->save() ) {
										return TRUE;
									}
								}
							}
							Debug::text('Enroll User Object not valid : '. $user_id, __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text('User ID : '. $user_id .' NOT FOUND', __FILE__, __LINE__, __METHOD__, 10);
						}

						break;
					case 'ibutton':
						//Remove ibutton from current user.
						$uilf = new UserIdentificationListFactory();
						$uilf->getByCompanyIdAndTypeIdAndValue( $this->getUserObject()->getCompany(), 10, $id );
						if ( $uilf->getRecordCount() > 0 ) {
							foreach( $uilf as $ui_obj ) {
								Debug::text('Removing iButton ID from User: '. $ui_obj->getUserObject()->getUserName(), __FILE__, __LINE__, __METHOD__, 10);
								$ui_obj->setDeleted(TRUE);
								if ( $ui_obj->isValid() ) {
									$ui_obj->Save();
								}
							}
							unset($ui_obj);
						} else {
							Debug::text('Didnt find current user with iButton assigned...', __FILE__, __LINE__, __METHOD__, 10);
						}

						$enroll_user_obj = $ulf->getByIdAndCompanyId( $user_id, $this->getUserObject()->getCompany() );
						if ( $enroll_user_obj->getRecordCount() == 1 ) {
							Debug::text('Enroller User ID : '. $user_id .' FOUND', __FILE__, __LINE__, __METHOD__, 10);

							$enroll_user_obj = $enroll_user_obj->getCurrent();

							$uilf = new UserIdentificationListFactory();
							$uilf->getByUserIdAndTypeIdAndNumber($enroll_user_obj->getID(), 10, 0 );
							if ( $uilf->getRecordCount() > 0 ) {
								$uif = $uilf->getCurrent();
							} else {
								$uif = new UserIdentificationFactory();
							}
							$uif->setUser( $enroll_user_obj->getId() );
							$uif->setType( 10 ); //iButton
							$uif->setNumber( 0 );
							$uif->setValue( $id );
							if ( $uif->isValid() ) {
								if ( $uif->save() ) {
									return TRUE;
								}
							}

							Debug::text('Enroll User Object not valid : '. $user_id, __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text('User ID : '. $user_id .' NOT FOUND', __FILE__, __LINE__, __METHOD__, 10);
						}

						/*
						$ulf->getByCompanyIDAndIButtonId( $this->getUserObject()->getCompany(), $id );
						if ( $ulf->getRecordCount() > 0 ) {
							$old_user_obj = $ulf->getCurrent();

							Debug::text('Removing iButton ID from User: '. $old_user_obj->getUserName(), __FILE__, __LINE__, __METHOD__, 10);
							$old_user_obj->setIButtonID(NULL);
							if ( $old_user_obj->isValid() ) {
								$old_user_obj->Save();
							}

							unset($old_user_obj);
						} else {
							Debug::text('Didnt find current user with iButton assigned...', __FILE__, __LINE__, __METHOD__, 10);
						}

						//$enroll_user_obj = $ulf->getByUserNameAndCompanyId( $user_name, $this->getUserObject()->getCompany() );
						$enroll_user_obj = $ulf->getByIdAndCompanyId( $user_id, $this->getUserObject()->getCompany() );

						if ( $enroll_user_obj->getRecordCount() == 1 ) {
							Debug::text('Enroller User ID : '. $user_id .' FOUND', __FILE__, __LINE__, __METHOD__, 10);

							$enroll_user_obj = $enroll_user_obj->getCurrent();

							//Update ibutton id.
							$enroll_user_obj->setIButtonID( $id );
							if ( $enroll_user_obj->isValid() ) {
								if ( $enroll_user_obj->save() ) {
									return TRUE;
								}
							}

							Debug::text('Enroll User Object not valid : '. $user_id, __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text('User ID : '. $user_id .' NOT FOUND', __FILE__, __LINE__, __METHOD__, 10);
						}
						*/
						break;
				}
			} else {
				Debug::text('User : '. $this->getUserObject()->getFullName() .' DOES NOT HAVE edit advance permission', __FILE__, __LINE__, __METHOD__, 10);
			}
		} else {
			//This code path needs to handle re-enrolling iButtons, proximity cards, etc... for a different employees.
			Debug::text('bVersion: '. $this->getClientVersion(), __FILE__, __LINE__, __METHOD__, 10);

			$ulf->getByIdAndCompanyId( $user_id, $this->getUserObject()->getCompany() );
			if ( $ulf->getRecordCount() == 1 ) {
				Debug::text('Enroller User ID : '. $user_id .' FOUND', __FILE__, __LINE__, __METHOD__, 10);

				//Checking to see if value is enrolled for a different user first.
				$uilf = new UserIdentificationListFactory();
				$uilf->getByCompanyIdAndTypeIdAndValue( $this->getUserObject()->getCompany(), $type, $id );
				if ( $uilf->getRecordCount() > 0 ) {
					foreach( $uilf as $ui_obj ) {
						Debug::text('Removing enolled value from User: '. $ui_obj->getUserObject()->getUserName(), __FILE__, __LINE__, __METHOD__, 10);
						$ui_obj->setDeleted(TRUE);
						if ( $ui_obj->isValid() ) {
							$ui_obj->Save();
						}
					}
					unset($ui_obj);
				} else {
					Debug::text('Didnt find current user with enrolled value assigned...', __FILE__, __LINE__, __METHOD__, 10);
				}

				$enroll_user_obj = $ulf->getCurrent();

				$uilf = new UserIdentificationListFactory();
				$uilf->getByUserIdAndTypeIdAndNumber($enroll_user_obj->getID(), $type, $number );
				if ( $uilf->getRecordCount() > 0 ) {
					$uif = $uilf->getCurrent();
				} else {
					$uif = new UserIdentificationFactory();
				}

				if ( $id == '' AND $uilf->getRecordCount() > 0 ) {
					Debug::text('Deleting User Identification...', __FILE__, __LINE__, __METHOD__, 10);

					$uif->setDeleted(TRUE);
					if ( $uif->isValid() ) {
						if ( $uif->save() ) {
							return TRUE;
						}
					}
				} elseif ( $id != '' ) {
					Debug::text('Adding/Modifying User Identification...', __FILE__, __LINE__, __METHOD__, 10);

					$uif->setUser( $enroll_user_obj->getId() );
					$uif->setType( $type );
					$uif->setNumber( $number );
					$uif->setValue( $id );

					//Primarily used to store raw fingerprint images during enroll.
					if ( $extra_value != '' ) {
						$uif->setExtraValue( $extra_value );
					}

					if ( $uif->isValid() ) {
						if ( $uif->save() ) {
							return TRUE;
						}
					}
				}

				Debug::text('Enroll User Object not valid : '. $user_id, __FILE__, __LINE__, __METHOD__, 10);
			} else {
				Debug::text('User ID : '. $user_id .' NOT FOUND', __FILE__, __LINE__, __METHOD__, 10);
			}
		}

		return FALSE;
	}

	function getPunchData() {
		if ( $this->StationCheckAllowed() !== TRUE) {
			Debug::text('Station NOT allowed: ', __FILE__, __LINE__, __METHOD__, 10);
			return FALSE;
		}

		$epoch = TTDate::getTime();

		$slf = new ScheduleListFactory();

		//Get last punch for this day, for this user.
		$plf = new PunchListFactory();
		$plf->getPreviousPunchByUserIDAndEpoch( $this->getUserObject()->getId(), $epoch );
		if ( $plf->getRecordCount() > 0 ) {
			$prev_punch_obj = $plf->getCurrent();
			Debug::Text(' Found Previous Punch within Continuous Time from now, ID: '. $prev_punch_obj->getId(), __FILE__, __LINE__, __METHOD__,10);

			$branch_id = $prev_punch_obj->getPunchControlObject()->getBranch();
			$department_id = $prev_punch_obj->getPunchControlObject()->getDepartment();
			$job_id = $prev_punch_obj->getPunchControlObject()->getJob();
			$job_item_id = $prev_punch_obj->getPunchControlObject()->getJobItem();

			//Don't enable transfer by default if the previous punch was any OUT punch.
			//Transfer does the OUT punch for them, so if the previous punch is an OUT punch
			//we don't gain anything anyways.
			if ( $this->getPermissionObject()->Check('punch','default_transfer') AND $prev_punch_obj->getStatus() == 10 ) {
				$transfer = TRUE;
			} else {
				$transfer = FALSE;
			}

			if ( $branch_id == '' OR empty($branch_id)
					OR $department_id == '' OR empty($department_id)
					OR $job_id == '' OR empty($job_id)
					OR $job_item_id == '' OR empty($job_item_id) ) {
				Debug::Text(' Branch or department are null. ', __FILE__, __LINE__, __METHOD__,10);

				$s_obj = $slf->getScheduleObjectByUserIdAndEpoch( $this->getUserObject()->getId(), $epoch );

				if ( is_object($s_obj) ) {
					Debug::Text(' Found Schedule!: ', __FILE__, __LINE__, __METHOD__,10);

					if ( $branch_id == '' OR empty($branch_id) ) {
						Debug::Text(' overrriding branch: '. $s_obj->getBranch(), __FILE__, __LINE__, __METHOD__,10);
						$branch_id = $s_obj->getBranch();
					}
					if ( $department_id == '' OR empty($department_id) ) {
						Debug::Text(' overrriding department: '. $s_obj->getDepartment(), __FILE__, __LINE__, __METHOD__,10);
						$department_id = $s_obj->getDepartment();
					}

					if ( $job_id == '' OR empty($job_id) ) {
						Debug::Text(' overrriding job: '. $s_obj->getJob(), __FILE__, __LINE__, __METHOD__,10);
						$job_id = $s_obj->getJob();
					}
					if ( $job_item_id == '' OR empty($job_item_id) ) {
						Debug::Text(' overrriding job item: '. $s_obj->getJobItem(), __FILE__, __LINE__, __METHOD__,10);
						$job_item_id = $s_obj->getJobItem();
					}
				}
			}

			$next_type = $prev_punch_obj->getNextType();

			//Check for break policy window.
			if ( $next_type != 30 AND ( $prev_punch_obj->getStatus() != 30 AND $prev_punch_obj->getType() != 30 ) ) {
				$prev_punch_obj->setUser( $this->getUserObject()->getId() );
				$prev_punch_obj->setScheduleID( $prev_punch_obj->findScheduleID( $epoch ) );
				if ( $prev_punch_obj->inBreakPolicyWindow( $epoch, $prev_punch_obj->getTimeStamp() ) == TRUE ) {
					Debug::Text(' Setting Type to Break: ', __FILE__, __LINE__, __METHOD__,10);
					$next_type = 30;
				}
			}

			//Check for meal policy window.
			if ( $next_type != 20 AND ( $prev_punch_obj->getStatus() != 20 AND $prev_punch_obj->getType() != 20 ) ) {
				$prev_punch_obj->setUser( $this->getUserObject()->getId() );
				$prev_punch_obj->setScheduleID( $prev_punch_obj->findScheduleID( $epoch ) );
				if ( $prev_punch_obj->inMealPolicyWindow( $epoch, $prev_punch_obj->getTimeStamp() ) == TRUE ) {
					Debug::Text(' Setting Type to Lunch: ', __FILE__, __LINE__, __METHOD__,10);
					$next_type = 20;
				}
			}

			Debug::Text(' cJob Item ID: '. $job_item_id, __FILE__, __LINE__, __METHOD__,10);
			$note = '';
			if ( (int)$prev_punch_obj->getNextStatus() == 20 ) {
				$note = $prev_punch_obj->getPunchControlObject()->getNote();
			}

			$data = array(
							'user_id' => (int)$this->getUserObject()->getId(),
							'user_full_name' => $this->getUserObject()->getFullName(),
							'time_stamp' => TTDate::getDate('TIME', $epoch),
							'date_stamp' => TTDate::getDate('DATE', $epoch),
							'full_time_stamp' => $epoch,
							'iso_time_stamp' => TTDate::getDBTimeStamp( $epoch, FALSE ),
							'transfer' => $transfer,
							'branch_id' => (int)$branch_id,
							'department_id' => (int)$department_id,
							'job_id' => $job_id,
							'job_item_id' => $job_item_id,
							'quantity' => $prev_punch_obj->getPunchControlObject()->getQuantity(),
							'bad_quantity' => $prev_punch_obj->getPunchControlObject()->getBadQuantity(),
							'note' => (string)$note, //Must not be NULL
							'other_id1' => $prev_punch_obj->getPunchControlObject()->getOtherID1(),
							'other_id2' => $prev_punch_obj->getPunchControlObject()->getOtherID2(),
							'other_id3' => $prev_punch_obj->getPunchControlObject()->getOtherID3(),
							'other_id4' => $prev_punch_obj->getPunchControlObject()->getOtherID4(),
							'other_id5' => $prev_punch_obj->getPunchControlObject()->getOtherID5(),
							'status_id' => (int)$prev_punch_obj->getNextStatus(),
							'type_id' => (int)$next_type,
							'punch_control_id' => (int)$prev_punch_obj->getNextPunchControlID(),
							//'user_date_id' => (int)$prev_punch_obj->getPunchControlObject()->getUserDateID()
							);
			unset($note);

		} else {
			Debug::Text(' DID NOT Find Previous Punch within Continuous Time from now: ', __FILE__, __LINE__, __METHOD__,10);
			//These used to be NULLs, but as of TT v3.0 they cause deserilizer errors with a Java client.
			$branch_id = '';
			$department_id = '';
			$job_id = '';
			$job_item_id = '';

			$s_obj = $slf->getScheduleObjectByUserIdAndEpoch( $this->getUserObject()->getId(), $epoch );
			if ( is_object($s_obj) ) {
				Debug::Text(' Found Schedule! ID:'. $s_obj->getID() .' Job ID: '. $s_obj->getJob(), __FILE__, __LINE__, __METHOD__,10);
				$branch_id = $s_obj->getBranch();
				$department_id = $s_obj->getDepartment();
				$job_id = $s_obj->getJob();
				$job_item_id = $s_obj->getJobItem();
			} else {
				//Check for defaults
				$branch_id = $this->getUserObject()->getDefaultBranch();
				$department_id = $this->getUserObject()->getDefaultDepartment();

				//Check station for default/forced settings.
				if ( is_object( $this->getStationObject() ) ) {
					if ( $this->getStationObject()->getDefaultBranch() !== FALSE AND $this->getStationObject()->getDefaultBranch() != 0 ) {
						$branch_id = $this->getStationObject()->getDefaultBranch();
					}
					if ( $this->getStationObject()->getDefaultDepartment() !== FALSE AND $this->getStationObject()->getDefaultDepartment() != 0 ) {
						$department_id = $this->getStationObject()->getDefaultDepartment();
					}
					if ( $this->getStationObject()->getDefaultJob() !== FALSE AND $this->getStationObject()->getDefaultJob() != 0 ) {
						$job_id = $this->getStationObject()->getDefaultJob();
					}
					if ( $this->getStationObject()->getDefaultJobItem() !== FALSE AND $this->getStationObject()->getDefaultJobItem() != 0 ) {
						$job_item_id = $this->getStationObject()->getDefaultJobItem();
					}
				}
			}

			$data = array(
							'user_id' => (int)$this->getUserObject()->getId(),
							'user_full_name' => $this->getUserObject()->getFullName(),
							'time_stamp' => TTDate::getDate('TIME', $epoch),
							'date_stamp' => TTDate::getDate('DATE', $epoch),
							'full_time_stamp' => $epoch,
							'iso_time_stamp' => TTDate::getDBTimeStamp( $epoch, FALSE ),
							'transfer' => FALSE,
							'branch_id' => (int)$branch_id,
							'department_id' => (int)$department_id,
							'job_id' => $job_id,
							'job_item_id' => $job_item_id,
							'status_id' => 10, //In
							'type_id' => 10, //Normal
							);
		}

		//Get options.
		$blf = new BranchListFactory();
		$blf->getByCompanyId( $this->getCompanyObject()->getId() );
		$branch_options = $blf->getArrayByListFactory( $blf, TRUE, FALSE );

		$dlf = new DepartmentListFactory();
		$dlf->getByCompanyId( $this->getCompanyObject()->getId() );
		$department_options = $dlf->getArrayByListFactory( $dlf, TRUE, FALSE);

		$job_options = array();
		$job_item_options = array();
		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$jlf = new JobListFactory();
			$job_options = $jlf->getByCompanyIdAndUserIdAndStatusArray( $this->getCompanyObject()->getId(),  $this->getUserObject()->getId(), array(10), TRUE );

			$jilf = new JobItemListFactory();
			$job_item_options = $jilf->getByCompanyIdArray( $this->getCompanyObject()->getId(), TRUE );
		}

		$pf = new PunchFactory();

		//Select box options;
		$data['status_options'] = $pf->getOptions('status');
		$data['type_options'] = $pf->getOptions('type');
		$data['branch_options'] = $branch_options;
		$data['department_options'] = $department_options;
		$data['job_options'] = $job_options;
		$data['job_item_options'] = $job_item_options;

		//Hack for PHP v5.0.4 shotty SOAP.
		//If it can cast the first array key to a INT, it rekeys the entire array.
		//02-Nov-09: Using NULL values causes the Java client to throw a deserlizer error. Using '' causes blank entries.
		/*
		$data['status_options'] = Misc::prependArray( array('_' => FALSE ), $data['status_options'] );
		$data['type_options'] = Misc::prependArray( array('_' => FALSE ), $data['type_options'] );
		$data['branch_options'] = Misc::prependArray( array('_' => FALSE ), $data['branch_options'] );
		$data['department_options'] = Misc::prependArray( array('_' => FALSE ), $data['department_options'] );
		$data['job_options'] = Misc::prependArray( array('_' => FALSE ), $data['job_options'] );
		$data['job_item_options'] = Misc::prependArray( array('_' => FALSE ), $data['job_item_options'] );
		*/

		$data['timeout'] = 5;
		$data['date_format_example'] = (string)$this->getUserObject()->getUserPreferenceObject()->getDateFormatExample();
		$data['time_format_example'] = (string)$this->getUserObject()->getUserPreferenceObject()->getTimeFormatExample();
		//Debug::Arr($data, 'punchDataArray', __FILE__, __LINE__, __METHOD__,10);

		if ( !$this->getPermissionObject()->Check('job', 'enabled') ) {
			unset($data['job_options']);
			unset($data['job_item_options']);
		}

		//Debug::Arr($data, 'Return Data: ', __FILE__, __LINE__, __METHOD__,10);

		return $data;
	}

	function setPunchData($data) {
		//Debug::Arr($data, 'punchDataArray', __FILE__, __LINE__, __METHOD__,10);
		/*
		<b>TimeTrexSoapServer::setPunchData()</b>:  Data:  <pre> array(10) {
		["date_stamp"]=>   string(9) "05-Nov-05"
		["transfer"]=>   int(0)
		["branch_id"]=>   string(1) "0"
		["time_stamp"]=>   string(7) "4:42 PM"
		["user_date_id"]=>   string(5) "14774"
		["punch_control_id"]=>   string(5) "26614"
		["type_id"]=>   string(2) "10"
		["department_id"]=>   string(1) "0"
		["status_id"]=>   string(2) "20"
		["user_id"]=>   string(1) "1" } </pre><br>
		*/
		//Debug::Arr($data, ' Data: ', __FILE__, __LINE__, __METHOD__,10);

		//User prefs should be set before we parse the date/time.
		$punch_full_time_stamp = TTDate::parseDateTime($data['date_stamp'].' '.$data['time_stamp']);
		Debug::Text(' Punch Full TimeStamp: '. date('r'. $punch_full_time_stamp) .' ('.$punch_full_time_stamp.') TimeZone: '. $this->getUserObject()->getUserPreferenceObject()->getTimeZone(), __FILE__, __LINE__, __METHOD__,10);

		$pf = new PunchFactory();
		$pf->StartTransaction();

		//Set User before setTimeStamp so rounding can be done properly.
		$pf->setUser( $this->getUserObject()->getId() );

		if ( isset($data['transfer']) AND $data['transfer'] == 1 ) {
			Debug::Text(' Enabling Transfer!: ', __FILE__, __LINE__, __METHOD__,10);
			$data['type_id'] = 10;
			$data['status_id'] = 10;
			$pf->setTransfer( TRUE );
		}

		$pf->setType( $data['type_id'] );
		$pf->setStatus( $data['status_id'] );
		$pf->setTimeStamp( $punch_full_time_stamp );

		if ( isset($data['status_id']) AND $data['status_id'] == 20 AND isset( $data['punch_control_id'] ) AND $data['punch_control_id']  != '' ) {
			$pf->setPunchControlID( $data['punch_control_id'] );
		} else {
			$pf->setPunchControlID( $pf->findPunchControlID() );
		}

		$pf->setStation( $this->getStationObject()->getId() );

		if ( $pf->isNew() ) {
			$pf->setActualTimeStamp( $punch_full_time_stamp );
			$pf->setOriginalTimeStamp( $pf->getTimeStamp() );
		}

		if ( $pf->isValid() == TRUE ) {
			$return_date = $pf->getTimeStamp();
			if ( $pf->getStatus() == 10 ) {
				$label = 'In';
			} else {
				$label = 'Out';
			}

			if ( $pf->Save( FALSE ) == TRUE ) {
				$pcf = new PunchControlFactory();
				$pcf->setId( $pf->getPunchControlID() );
				$pcf->setPunchObject( $pf );

				if ( isset($data['branch_id']) AND $data['branch_id'] != '') {
					$pcf->setBranch( $data['branch_id'] );
				}
				if ( isset($data['department_id']) AND $data['department_id'] != '' ) {
					$pcf->setDepartment( $data['department_id'] );
				}

				if ( isset($data['job_id']) AND $data['job_id'] != '' ) {
					$pcf->setJob( $data['job_id'] );
				}
				if ( isset($data['job_item_id']) AND $data['job_item_id'] != '') {
					$pcf->setJobItem( $data['job_item_id'] );
				}
				if ( isset($data['quantity']) AND $data['quantity'] != '' ) {
					$pcf->setQuantity( $data['quantity'] );
				}
				if ( isset($data['bad_quantity']) AND $data['bad_quantity'] != '' ) {
					$pcf->setBadQuantity( $data['bad_quantity'] );
				}

				//Don't overwrite note if a new one isn't set. This makes it more difficult to delete a note if they want to,
				//But thats better then accidently deleting it.
				if ( isset($data['note']) AND $data['note'] != '' ) {
					$pcf->setNote( $data['note'] );
				}

				if ( isset($data['other_id1']) AND $data['other_id1'] != '' ) {
					$pcf->setOtherID1( $data['other_id1'] );
				}
				if ( isset($data['other_id2']) AND $data['other_id2'] != '' ) {
					$pcf->setOtherID2( $data['other_id2'] );
				}
				if ( isset($data['other_id3']) AND $data['other_id3'] != '' ) {
					$pcf->setOtherID3( $data['other_id3'] );
				}
				if ( isset($data['other_id4']) AND $data['other_id4'] != '' ) {
					$pcf->setOtherID4( $data['other_id4'] );
				}
				if ( isset($data['other_id5']) AND $data['other_id5'] != '' ) {
					$pcf->setOtherID5( $data['other_id5'] );
				}

				//$pcf->setEnableStrictJobValidation( TRUE );
				$pcf->setEnableCalcUserDateID( TRUE );
				$pcf->setEnableCalcTotalTime( TRUE );
				$pcf->setEnableCalcSystemTotalTime( TRUE );
				$pcf->setEnableCalcUserDateTotal( TRUE );
				$pcf->setEnableCalcException( TRUE );
				$pcf->setEnablePreMatureException( TRUE ); //Enable pre-mature exceptions at this point.

				if ( $pcf->isValid() == TRUE ) {
					Debug::Text(' Punch Control is valid, saving...: ', __FILE__, __LINE__, __METHOD__,10);

					if ( $pcf->Save( TRUE, TRUE ) == TRUE ) { //Force isNew() lookup.

						Debug::text('Return Date: '. $return_date, __FILE__, __LINE__, __METHOD__, 10);
						$retval = '<div style="font-size:28px; font-weight: bold">
						<table>
						<tr>
							<td>'.$this->getUserObject()->getFullName() .'</td>
						</tr>
						<tr>
							<td>
							Punch '.$label.': '. TTDate::getDate('TIME', $return_date) .'
							</td>
						</tr>
						</table>
						</div>';

						Debug::text('RetVal: '. $retval, __FILE__, __LINE__, __METHOD__, 10);

						//Set stations last punch time stamp so we can filter out duplicates later on.
						//$pf->FailTransaction();
						$pf->CommitTransaction();

						return $retval;
					} else {
						Debug::text('Punch Control save failed!', __FILE__, __LINE__, __METHOD__, 10);
					}
				} else {
					Debug::text('Punch Control is NOT VALID!', __FILE__, __LINE__, __METHOD__, 10);
				}
			} else {
				Debug::text('Punch save failed!', __FILE__, __LINE__, __METHOD__, 10);
			}
		} else {
			Debug::text('Punch is NOT VALID: ', __FILE__, __LINE__, __METHOD__, 10);
		}

		$pf->FailTransaction();

		Debug::text('Returning FALSE: Action Failed! ', __FILE__, __LINE__, __METHOD__, 10);

		//Get text errors to display to the user.
		$errors = NULL;
		if ( isset($pf) AND is_object($pf) ) {
			$errors .= $pf->Validator->getErrors();
		}
		if ( isset($pcf) AND is_object($pcf) ) {
			$errors .= $pcf->Validator->getErrors();
		}
		$errors = wordwrap( $errors, 40, "<br>\n");

		$retval = '<table bgcolor="red">
		<tr>
			<td style="font-size:28px; font-weight: bold">Action Failed!</td>
		</tr>
		<tr>
			<td style="font-size:14px; font-weight: bold">
				'. $errors .'
			</td>
		</tr>
		</table>';

		return $retval;
		//return FALSE;
	}

	function Logout() {
		return $this->authentication->Logout();
	}

	function addLogEntry( $object_id, $action_id, $description, $user_id, $table ) {
		TTLog::addEntry( $object_id, $action_id, $description, $user_id, $table );
	}

	function __call( $name, $arguments ) {
		Debug::text('ERROR: Attempting to call function that does not exist in this class! Class: '. __CLASS__ .' Function: '. $name, __FILE__, __LINE__, __METHOD__, 10);

		return FALSE;
	}
}
?>
