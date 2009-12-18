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
 * $Id: TimeTrexSoapServerUnAuthenticated.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */

/**
 * @package Module_SOAP
 */
class TimeTrexSoapServerUnAuthenticated {

	function helloworld($text) {
		return "Hello World: $text - ". $_COOKIE['SessionID'] ."";
	}

	function getClientVersion() {
		if ( isset($_GET['v']) AND $_GET['v'] != '' ) {
			return $_GET['v'];
		}

		return FALSE;
	}

	//PING function to test internet connection.
	function ping() {
		Debug::Text('Ping: '. TTDate::getDate('DATE+TIME', time() ), __FILE__, __LINE__, __METHOD__,10);
		return TRUE;
	}

	function getTime() {
		TTDate::setTimeZone('GMT');
		return TTDate::getTime();
	}

	function getTimeOffset( $client_epoch ) {
		TTDate::setTimeZone('GMT');
		$server_epoch = TTDate::getTime();

		$offset = $client_epoch - $server_epoch;

		Debug::Text('Client Time: '.  TTDate::getDate('DATE+TIME', $client_epoch ) .' Server Time: '.  TTDate::getDate('DATE+TIME', $server_epoch ) .' Offset: '. $offset, __FILE__, __LINE__, __METHOD__,10);

		if ( $offset == 0 ) {
			$offset = 1;
		}

		return (int)$offset;
	}

	function getStationObject() {
		if ( $_GET['StationID'] == '' ) {
			return FALSE;
		}

		$slf = new StationListFactory();
		$slf->getByStationId( $_GET['StationID'] );
		$current_station = $slf->getCurrent();
		unset($slf);

		return $current_station;
	}

	function Login($user_name, $password = NULL, $type = NULL) {
		$authentication = new Authentication();

		Debug::text('User Name: '. $user_name .' Password Length: '. strlen($password) .' Type: '. $type, __FILE__, __LINE__, __METHOD__, 10);

		if ( $authentication->Login($user_name, $password, $type) === TRUE ) {
			$retval = $authentication->getSessionId();
			Debug::text('Success, Session ID: '. $retval, __FILE__, __LINE__, __METHOD__, 10);
			return $retval;
		}

		return FALSE;
	}

	//Check for updates each day... And on startup, and allow a menu option to force a check.
	function getLatestVersion() {
		return '2.5.4';
	}

	function isLatestVersion($current_version) {
		if ( $current_version == '' ) {
			return FALSE;
		}

		$current_version = trim($current_version);

		Debug::text('Current Version: '. $current_version .' Latest Version: '. $this->getLatestVersion(), __FILE__, __LINE__, __METHOD__, 10);

		if ( version_compare( $current_version, $this->getLatestVersion(), '<') == TRUE ) {
			Debug::text('Current version Returning FALSE!', __FILE__, __LINE__, __METHOD__, 10);
			return FALSE;
		}

		Debug::text('Current version Returning TRUE!', __FILE__, __LINE__, __METHOD__, 10);
		return TRUE;
	}

	function getLatestVersionURL() {
		if ( isset($_SERVER["HTTPS"]) ) {
			$prefix = 'https';
		} else {
			$prefix = 'http';
		}

		$retval = $prefix.'://'. $_SERVER["HTTP_HOST"].'/'.Environment::getBaseURL().'/help/Timetrex_Client.exe';

		Debug::text('Client URL: '. $retval , __FILE__, __LINE__, __METHOD__, 10);

		return $retval;
	}

	function getLatestVersionMD5() {
		$file_name = Environment::getBasePATH().'client/binary/Timetrex_Client_v'. $this->getLatestVersion().'.exe';
		Debug::text('File Name: '. $file_name, __FILE__, __LINE__, __METHOD__, 10);
		return md5_file($file_name);
	}

	//Ask server if its a good time to upgrade.
	//That way the server can take load in to account,
	//as well as check the schedule on this station, and make sure
	//people aren't about to punch in!
	//
	//Return offset in seconds of when to upgrade perhaps?
	//0 means now, 3600 means in one hour?
	//Or just keep returning false, and cache the data...
	//Returning a new offset is better I think, because that way
	//the 24hr timer can be sync'd to a good time to do the upgrades.

	function isGoodTimeToUpgrade( $station_id ) {
		Debug::Text('Checking isGoodTimeToUpgrade for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return 0;
		}

/*
		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			Debug::Text('Found Station!!', __FILE__, __LINE__, __METHOD__,10);
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();
			if ( $company_id == 1 ) {
				Debug::Text('Station is member of Company ID: 1... Upgrading', __FILE__, __LINE__, __METHOD__,10);
				return 0;
			}
		}
*/

		//Debug::Text('Not Good time to upgrade...', __FILE__, __LINE__, __METHOD__,10);

		//Always upgrade everyone from now on.
		return 0;

		/*
		if ( $station_id == '7800000021370B81' ) {
			return 0;
		} else {
			return 600;
		}
		*/
		/*
		//explain analyze select date_part('hour', time_stamp),count(*) from punch where station_id = 325 group by date_part('hour', time_stamp) order by date_part('hour', time_stamp);

		$cache->save(serialize($data), $station_id, 'isGoodTimeToUpgrade' );

		$retval = 5 * $data['count'];

		Debug::text('Returning Delay Seconds: '. $retval, __FILE__, __LINE__, __METHOD__, 10);

		return $retval;
		*/
	}

	//Just a function to log the update start.
	function startingUpgrade( $station_id ) {
		Debug::text('Starting Upgrade for Station ID: '. $station_id .' Date: '. TTDate::getDate('DATE+TIME', TTDate::getTime() ), __FILE__, __LINE__, __METHOD__, 10);

		return TRUE;

	}

	function isLatestData( $station_id, $last_check_epoch ) {
		//$last_check_epoch = strtotime('27-Jan-06 14:20');

		Debug::Text('Checking for latest User/Branch/Dept data for Station ID: '. $station_id  .' Last Checked: '. TTDate::getDate('DATE+TIME', $last_check_epoch ) .' ('.$last_check_epoch.')', __FILE__, __LINE__, __METHOD__,10);

		//If last_check_epoch is NULL, or 0, return FALSE to force an update.
		if ( $last_check_epoch == '' OR $last_check_epoch == 0 ) {
			return FALSE;
		}

		//Gets all users allowed to punch in/out from this station
		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {

			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();

			Debug::Text('Found Station!! ID: '. $s_obj->getId() .' Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

			if ( $company_id != FALSE ){

				$ulf = new UserListFactory();
				$modified_arr['user_modified'] = $ulf->getIsModifiedByCompanyIdAndDate( $company_id, $last_check_epoch);
				Debug::Text('Are Users Modified: '. (int)$modified_arr['user_modified'], __FILE__, __LINE__, __METHOD__,10);

				$blf = new BranchListFactory();
				$modified_arr['branch_modified'] = $blf->getIsModifiedByCompanyIdAndDate( $company_id, $last_check_epoch);
				Debug::Text('Are Branches Modified: '. (int)$modified_arr['branch_modified'] , __FILE__, __LINE__, __METHOD__,10);

				$dlf = new DepartmentListFactory();
				$modified_arr['department_modified'] = $dlf->getIsModifiedByCompanyIdAndDate( $company_id, $last_check_epoch);
				Debug::Text('Are Departments Modified: '. (int)$modified_arr['department_modified'] , __FILE__, __LINE__, __METHOD__,10);

				if ( version_compare( $this->getClientVersion(), '2.7.0', '>=' ) ) {
					$uilf = new UserIdentificationListFactory();
					$modified_arr['user_identifiers_modified'] = $uilf->getIsModifiedByCompanyIdAndDate( $company_id, $last_check_epoch);
					Debug::Text('Are User Identifiers Modified: '. (int)$modified_arr['user_identifiers_modified'] , __FILE__, __LINE__, __METHOD__,10);
				}

				if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
					$jlf = new JobListFactory();
					$modified_arr['job_modified'] = $jlf->getIsModifiedByCompanyIdAndDate( $company_id, $last_check_epoch);
					Debug::Text('Are Jobs Modified: '. (int)$modified_arr['job_modified'], __FILE__, __LINE__, __METHOD__,10);
					if ( $modified_arr['job_modified'] == TRUE ) {
						$modified_arr['job_to_user_map_modified'] = TRUE;
						$modified_arr['job_to_job_item_map_modified'] = TRUE;
					}
				}

				if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
					$jilf = new JobItemListFactory();
					$modified_arr['job_item_modified'] = $jilf->getIsModifiedByCompanyIdAndDate( $company_id, $last_check_epoch);
					Debug::Text('Are Job Items Modified: '. (int)$modified_arr['job_item_modified'], __FILE__, __LINE__, __METHOD__,10);
				}

				$oflf = new OtherFieldListFactory();
				$modified_arr['other_field_modified'] = $oflf->getIsModifiedByCompanyIdAndDate( $company_id, $last_check_epoch);
				Debug::Text('Are Other Fields Modified: '. (int)$modified_arr['other_field_modified'], __FILE__, __LINE__, __METHOD__,10);

				return $modified_arr;
			}
		}

		return TRUE;
		//return FALSE;
	}

	function getClientConfig( $station_id ) {
		Debug::Text('Getting Client Settings for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		$data = array(
						'latest_version_schedule' => 3600,
						'latest_version_schedule_random' => 600,
						'latest_data_schedule' => 3600,
						'auto_submit_timeout' => 10,
						'auto_submit_timeout_increment' => 5,
						'auto_submit_timeout_max' => 20,
						'punch_dump_schedule' => 1600,
						'flap_offline_time' => 1600,
						'flap_window' => 600,
						'flap_trigger' => 5,
						'debug_verbosity' => 10,
						'upload_debug_verbosity' => 10,
						'punch_upload_batch' => 25,
						//'enable_keyboard_shortcuts' => TRUE,
					);

		return $data;
	}

	function getUsers( $station_id ) {
		Debug::Text('Getting Users for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		$permission = new Permission();

		//Gets all users allowed to punch in/out from this station
		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			Debug::Text('Found Station!!', __FILE__, __LINE__, __METHOD__,10);
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();

			if ( $company_id != FALSE ){
				Debug::Text('Found Company: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

				$ulf = new UserListFactory();
				$ulf->getByCompanyId( $company_id );
				if ( $ulf->getRecordCount() > 0 ) {
					Debug::Text('Found Users '. $ulf->getRecordCount() .' for Company: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

					$x=0;
					foreach( $ulf as $u_obj ) {
						//Debug::Text('User ID: '. $u_obj->getId() .' iButton ID: '. $u_obj->getiButtonId() , __FILE__, __LINE__, __METHOD__,10);
						$enable_job_tracking = $permission->Check('job', 'enabled', $u_obj->getId(), $u_obj->getCompany());
						//Debug::Text('Enable Job Tracking for User: '. $u_obj->getUserName() .' Result: '. (int)$enable_job_tracking, __FILE__, __LINE__, __METHOD__,10);

						if ( version_compare( $this->getClientVersion(), '2.7.0', '<' ) ) {

							//In offline punch mode, we use the system local time
							//as we assume it is in the proper timezone for the user.
							/*
							$u_obj_prefs = $u_obj->getUserPreferenceObject();
							if ( is_object( $u_obj_prefs ) ) {
								$time_zone = $u_obj_prefs->getTimeZone();
								$language = $u_obj_prefs->getLanguage();
							} else {
								$time_zone = NULL;
								$language = NULL;
							}
							*/

							$ibutton = FALSE;
							$finger_print_1 = FALSE;
							$finger_print_2 = FALSE;
							$finger_print_3 = FALSE;
							$finger_print_4 = FALSE;

							//Get User identification rows for ibutton and fingerprints.
							$uilf = new UserIdentificationListFactory();
							$uilf->getByUserIdAndTypeId( $u_obj->getId(), array(10,20) ); //iButton and Griaule Fingerprints.
							//Debug::Text('User Identication Records: '. $uilf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);
							if ( $uilf->getRecordCount() > 0 ) {
								foreach( $uilf as $ui_obj ) {
									if ( $ui_obj->getType() == 10 ) {
										Debug::Text('Found iButton... User:  '. $u_obj->getID() .' Number: '. $ui_obj->getNumber(), __FILE__, __LINE__, __METHOD__,10);
										$ibutton = $ui_obj->getValue();
									} elseif ( $ui_obj->getType() == 20) {
										Debug::Text('Found Griaule FingerPrint... User:  '. $u_obj->getID() .' Number: '. $ui_obj->getNumber(), __FILE__, __LINE__, __METHOD__,10);
										switch ( $ui_obj->getNumber() ) {
											case 10:
												$finger_print_1 = $ui_obj->getValue();
												break;
											case 20:
												$finger_print_2 = $ui_obj->getValue();
												break;
											case 30:
												$finger_print_3 = $ui_obj->getValue();
												break;
											case 40:
												$finger_print_4 = $ui_obj->getValue();
												break;
										}
									}
								}
							}

							$user_list["'$x'"] = array(
											'id' => (int)$u_obj->getId(),
											'ibutton_id' => $ibutton,
											'employee_number' => $u_obj->getEmployeeNumber(),
											'user_name' => $u_obj->getUserName(),
											'full_name' => $u_obj->getFullName(),
											'default_branch_id' => (int)$u_obj->getDefaultBranch(),
											'default_department_id' => (int)$u_obj->getDefaultDepartment(),
											//'country' => $u_obj->getCountry(),
											//'language' => $language,
											//'time_zone' => $time_zone,
											'finger_print_1' => $finger_print_1,
											'finger_print_2' => $finger_print_2,
											'finger_print_3' => $finger_print_3,
											'finger_print_4' => $finger_print_4,
											'enable_job_tracking' => $enable_job_tracking,
											);
						} else {
							$user_list["'$x'"] = array(
											'id' => (int)$u_obj->getId(),
											'employee_number' => $u_obj->getEmployeeNumber(),
											'user_name' => $u_obj->getUserName(),
											'full_name' => $u_obj->getFullName(),
											'default_branch_id' => (int)$u_obj->getDefaultBranch(),
											'default_department_id' => (int)$u_obj->getDefaultDepartment(),
											//'country' => $u_obj->getCountry(),
											//'language' => $language,
											//'time_zone' => $time_zone,
											'enable_job_tracking' => $enable_job_tracking,
											);
						}

						unset($enable_job_tracking, $time_zone, $language );
						$x++;
					}

					if ( isset($user_list) ) {
						Debug::Text('Returning User List!', __FILE__, __LINE__, __METHOD__,10);
						return $user_list;
					}
				}
			}
		}

		Debug::Text('Returning FALSE!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function getUserIdentifiers( $station_id ) {
		Debug::Text('Getting User Identifiers for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		//Gets all users allowed to punch in/out from this station
		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();

			if ( $company_id != FALSE ){
				$uilf = new UserIdentificationListFactory();

				if ( version_compare( $this->getClientVersion(), '3.0.0', '>=' ) ) {
					$uilf->getByCompanyIdAndTypeId( $company_id, array(10,25,30,40) ); //iButtons, LIBFP Fingerprints, Barcodes, Proximity Cards
				} else {
					$uilf->getByCompanyIdAndTypeId( $company_id, array(10,20,30,40) ); //iButtons, Griaule Fingerprints Barcodes, Proximity Cards
				}

				if ( $uilf->getRecordCount() > 0 ) {
					$x=0;
					foreach( $uilf as $ui_obj ) {
						$identification_list["'$x'"] = array(
										'user_id' => (int)$ui_obj->getUser(),
										'type_id' => (int)$ui_obj->getType(),
										'number' => (int)$ui_obj->getNumber(),
										'value' => $ui_obj->getValue(),
										);
						$x++;
					}

					if ( isset($identification_list) ) {
						return $identification_list;
					}
				}
			}
		}

		return FALSE;
	}

	function getBranches( $station_id ) {
		Debug::Text('Getting Branches for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		//Gets all users allowed to punch in/out from this station
		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();

			if ( $company_id != FALSE ){
				$blf = new BranchListFactory();
				$blf->getByCompanyIdAndStatus( $company_id, 10 );
				if ( $blf->getRecordCount() > 0 ) {
					$x=0;
					foreach( $blf as $b_obj ) {
						$branch_list["'$x'"] = array(
										'id' => (int)$b_obj->getId(),
										'manual_id' => (int)$b_obj->getManualID(),
										'name' => $b_obj->getName(),
										);
						$x++;
					}

					if ( isset($branch_list) ) {
						return $branch_list;
					}
				}
			}
		}

		return FALSE;
	}

	function getDepartments( $station_id ) {
		Debug::Text('Getting Departments for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		//Gets all users allowed to punch in/out from this station
		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();

			if ( $company_id != FALSE ) {
				$dlf = new DepartmentListFactory();
				$dlf->getByCompanyIdAndStatus( $company_id, 10 );
				if ( $dlf->getRecordCount() > 0 ) {
					$x=0;
					foreach( $dlf as $d_obj ) {
						$department_list["'$x'"] = array(
										'id' => (int)$d_obj->getId(),
										'manual_id' => (int)$d_obj->getManualID(),
										'name' => $d_obj->getName(),
										);
						$x++;
					}

					if ( isset($department_list) ) {
						return $department_list;
					}
				}
			}
		}

		return FALSE;
	}

	function getJobs( $station_id ) {
		Debug::Text('Getting Jobs for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		if ( getTTProductEdition() != TT_PRODUCT_PROFESSIONAL ) {
			return FALSE;
		}

		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();
			Debug::Text('Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

			if ( $company_id != FALSE ) {
				//Gets all users allowed to punch in/out from this station
				$jlf = new JobListFactory();
				//$jlf->getByCompanyIdAndUserIdAndStatus( $company_id,  $current_user->getId(), array(10), TRUE );
				$jlf->getByStatusIdAndCompanyId( 10, $company_id);
				if ( $jlf->getRecordCount() > 0 ) {
					$x=0;
					foreach( $jlf as $j_obj) {
						$job_list["'$x'"] = array(
										'id' => (int)$j_obj->getId(),
										'manual_id' => (int)$j_obj->getManualID(),
										'name' => $j_obj->getName(),
										);

						$x++;
					}

					if ( isset($job_list) ) {
						return $job_list;
					}
				}
			}

		}

		Debug::Text('Returning FALSE!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function getJobtoUserMap( $station_id ) {
		//If the user is not in the list, that means they are allowed to ALL jobs.
		Debug::Text('Getting JobToUser Map for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		if ( getTTProductEdition() != TT_PRODUCT_PROFESSIONAL ) {
			return FALSE;
		}

		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();
			Debug::Text('Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

			if ( $company_id != FALSE ) {
				//Gets all users allowed to punch in/out from this station
				$jlf = new JobListFactory();
				$job_to_user_map = $jlf->getJobToUserMapByCompanyIdAndStatus( $company_id, 10 );
				if ( is_array($job_to_user_map) ) {
					foreach( $job_to_user_map as $key => $arr ) {
						$list["'$key'"] = array(
										'job_id' => (int)$arr['job_id'],
										'user_id' => (int)$arr['user_id'],
										);
					}

					if ( isset($list) ) {
						return $list;
					}
				}

				/*
				$jualf = new JobUserAllowListFactory();
				$jualf->getByCompanyIdAndStatus( $company_id, 10);
				if ( $jualf->getRecordCount() > 0 ) {
					$x=0;
					foreach( $jualf as $jua_obj) {
						$list["'$x'"] = array(
										'job_id' => (int)$jua_obj->getJob(),
										'user_id' => (int)$jua_obj->getUser(),
										);

						$x++;
					}

					if ( isset($list) ) {
						return $list;
					}
				}
				*/
			}

		}

		Debug::Text('Returning FALSE!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function getJobItems( $station_id ) {
		Debug::Text('Getting Jobs for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		if ( getTTProductEdition() != TT_PRODUCT_PROFESSIONAL ) {
			return FALSE;
		}

		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();
			Debug::Text('Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

			if ( $company_id != FALSE ) {
				//Gets all users allowed to punch in/out from this station
				$jilf = new JobItemListFactory();
				$jilf->getByCompanyId( $company_id );
				if ( $jilf->getRecordCount() > 0 ) {
					$x=0;
					foreach( $jilf as $ji_obj) {
						$job_item_list["'$x'"] = array(
										'id' => (int)$ji_obj->getId(),
										'manual_id' => (int)$ji_obj->getManualID(),
										'name' => $ji_obj->getName(),
										);

						$x++;
					}

					if ( isset($job_item_list) ) {
						return $job_item_list;
					}
				}
			}

		}

		Debug::Text('Returning FALSE!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function getJobtoJobItemMap( $station_id ) {
		//If the job is not in the list, that means all items are allowed
		Debug::Text('Getting JobToUser Map for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		if ( getTTProductEdition() != TT_PRODUCT_PROFESSIONAL ) {
			return FALSE;
		}

		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();
			Debug::Text('Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

			if ( $company_id != FALSE ) {
				//Gets all users allowed to punch in/out from this station
				$jilf = new JobItemListFactory();
				$job_to_job_item_map = $jilf->getJobToJobItemMapByCompanyIdAndStatus( $company_id, 10 );
				if ( is_array($job_to_job_item_map) ) {
					foreach( $job_to_job_item_map as $key => $arr ) {
						$list["'$key'"] = array(
										'job_id' => (int)$arr['job_id'],
										'job_item_id' => (int)$arr['job_item_id'],
										);
					}

					if ( isset($list) ) {
						return $list;
					}
				}
/*
				$jialf = new JobItemAllowListFactory();
				$jialf->getByCompanyIdAndStatus( $company_id, 10);
				if ( $jialf->getRecordCount() > 0 ) {
					$x=0;
					foreach( $jialf as $jia_obj) {
						$list["'$x'"] = array(
										'job_id' => (int)$jia_obj->getJob(),
										'job_item_id' => (int)$jia_obj->getItem(),
										);

						$x++;
					}

					if ( isset($list) ) {
						return $list;
					}
				}
*/
			}

		}

		Debug::Text('Returning FALSE!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function getOtherFields( $station_id ) {
		Debug::Text('Getting Other Fields Map for Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $station_id == '' ) {
			return FALSE;
		}

		$slf = new StationListFactory();
		$slf->getByStationID( $station_id );
		if ( $slf->getRecordCount() > 0 ) {
			$s_obj = $slf->getCurrent();
			$company_id = $s_obj->getCompany();
			Debug::Text('Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

			if ( $company_id != FALSE ) {
				$oflf = new OtherFieldListFactory();
				$retval = $oflf->getByCompanyIdAndTypeIdArray( $company_id, 15 );

				return $retval;
			}
		}

		Debug::Text('Returning FALSE!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function setClientLog( $data ) {
		global $config_vars;

		if ( !is_array($data) OR count($data) == 0 ) {
			return FALSE;
		}

		ksort($data);

		$base_dir = $config_vars['path']['log'] . DIRECTORY_SEPARATOR .'client' . DIRECTORY_SEPARATOR;
		$upload_time = time();

		Debug::Text('Setting Log Lines: '.count($data), __FILE__, __LINE__, __METHOD__,10);

		//Binary data could be coming in through this. Eric should Base64 encode his end
		//And Decode it here.

		//Log all data to log files based on station IDs. If no station id (ie: no probes plugged in)
		//Use IP address and send to a "generic" log.

		foreach( $data as $row_key => $row ) {
			//Debug::Text('Row Key: '. $row_key .' Date/Time: '. TTDate::getDate('DATE+TIME', $row['epoch']) .' Verbosity: '. $row['verbosity'] .' Station ID: '. $row['station_id'] .' Text: '. $row['msg'], __FILE__, __LINE__, __METHOD__,10);

			if ( $row['station_id'] == '' ) {
				$station_id = 'no_station_id';
			} else {
				$station_id = $row['station_id'];
			}

			$retarr[$station_id][] = '['.TTDate::getDate('DATE+TIME', $row['epoch']).'] ('. $row['verbosity'] .') - '. $row['msg']."\n";
		}

		foreach( $retarr as $station_id => $line_arr) {
			array_unshift( $line_arr, '---------------- Data Uploaded By: '. $_SERVER['REMOTE_ADDR'] .' At: '. TTDate::getDate('DATE+TIME', $upload_time) .' ----------------'."\n");

			Debug::Text('Station ID: '. $station_id, __FILE__, __LINE__, __METHOD__,10);
			if ( $station_id != 'no_station_id' ) {
				//Get Company Short name for station_id
				$slf = new StationListFactory();
				$slf->getByStationID( $station_id );
				if ( $slf->getRecordCount() > 0 ) {
					$s_obj = $slf->getCurrent();
					$company_name = $s_obj->getCompanyObject()->getShortName();

					//FIXME: Check to make sure the company is still active before proceeding.
				} else {
					$company_name = 'no_company';
				}
				unset($slf, $s_obj);
			} else {
				$company_name = 'no_company';
			}

			$dir = $base_dir . preg_replace('/[^a-zA-Z0-9]/', '', escapeshellarg( trim($company_name) ) ) . DIRECTORY_SEPARATOR; //Remove all non-alphanumeric chars.

			if ( !file_exists($dir) ) {
				mkdir($dir);
			}

			$file_name = $dir.$station_id;

			Debug::Text('Company Name: '. $company_name .' File Name: '. $file_name , __FILE__, __LINE__, __METHOD__,10);

			file_put_contents( $file_name, $line_arr, FILE_APPEND );

			unset($company_name);
		}

		return TRUE;
	}

	function setOfflinePunch( $data ) {
		Debug::Text('Setting Offline Punches... Rows: '. count($data), __FILE__, __LINE__, __METHOD__,10);

		//
		//WHen in Offline mode, default Type/Status to "AUTO"...
		//That way once I get the punches, I can determine what they should be on my end.
		//

		if ( !is_array($data) OR count($data) == 0 ) {
			return FALSE;
		}

		ksort($data);

		//Debug::Arr($data, 'offlinePunchDataArr', __FILE__, __LINE__, __METHOD__,10);

/*
		//Original
		$data[] = array(
						'user_id' => 1,
						'time_stamp' => '12:00 PM',
						'date_stamp' => '03-Dec-05',
						'branch_id' => 1,
						'department_id' => NULL,
						'status_id' => 20,
						'type_id' => 20,
						'punch_control_id' => 0,
						'station_id' => '7D00000023352A81'
						);
*/
/*
		unset($data);

		$data[] = array(
						'user_id' => 1001,
						'time_stamp' => '08:00 AM',
						'date_stamp' => '05-Dec-05',
						'branch_id' => 5,
						'department_id' => 3,
						'status_id' => 0,
						'type_id' => 0,
						'punch_control_id' => 0,
						'station_id' => '7D00000023352A81'
						);

		$data[] = array(
						'user_id' => 1001,
						'time_stamp' => '12:00 PM',
						'date_stamp' => '05-Dec-05',
						'branch_id' => 0,
						'department_id' => 3,
						'status_id' => 20,
						'type_id' => 0,
						'punch_control_id' => 0,
						'station_id' => '7D00000023352A81'
						);
*/
/*
		$data[] = array(
						'user_id' => 1001,
						'time_stamp' => '1:00 PM',
						'date_stamp' => '05-Dec-05',
						'branch_id' => 6,
						'department_id' => 0,
						'status_id' => 0,
						'type_id' => 20,
						'punch_control_id' => 0,
						'station_id' => '7D00000023352A81'
						);
*/
/*
		$data[] = array(
						'user_id' => 1001,
						'time_stamp' => '5:00 PM',
						'date_stamp' => '05-Dec-05',
						'branch_id' => 0,
						'department_id' => 0,
						'status_id' => 0,
						'type_id' => 0,
						'punch_control_id' => 0,
						'station_id' => '7D00000023352A81'
						);
*/
		//Debug::Arr($data, 'offlinePunchDataArr', __FILE__, __LINE__, __METHOD__,10);


		//One punch per row
		foreach( $data as $row_key => $punch_row ) {

			Debug::Text('--------------------------========================---------------------------', __FILE__, __LINE__, __METHOD__,10);
			Debug::Text('--------------------------========================---------------------------', __FILE__, __LINE__, __METHOD__,10);

			Debug::Text('Row Key: '. $row_key .' Date: '. $punch_row['date_stamp'] .' Time: '. $punch_row['time_stamp'] .' Station ID: '. $punch_row['station_id'], __FILE__, __LINE__, __METHOD__,10);

			if ( isset( $punch_row['station_id'] ) )  {
				$slf = new StationListFactory();
				$slf->getByStationId( $punch_row['station_id'] );
				if ( $slf->getRecordCount() > 0 ) {
					Debug::Text('Found Station Data...', __FILE__, __LINE__, __METHOD__,10);
					$current_station = $slf->getCurrent();
				} else {
					Debug::Text('DID NOT Find Station Data...', __FILE__, __LINE__, __METHOD__,10);
					continue;
				}
				unset($slf);
			}

			if ( isset( $punch_row['user_id'] ) AND $punch_row['user_id'] != '' ) {
				$ulf = new UserListFactory();
				$ulf->getById( $punch_row['user_id'] );
				if ( $ulf->getRecordCount() > 0 ) {
					$current_user = $ulf->getCurrent();
					Debug::Text('Valid User ID: '. $punch_row['user_id'] .' User Name: '. $current_user->getFullName(), __FILE__, __LINE__, __METHOD__,10);

					//Need to handle timezone somehow. The station should send us the system's timezone
					//so we can calculate based on that.
					//Or just use the employees date preference.
					$current_user->getUserPreferenceObject()->setDateTimePreferences();
				} else {
					Debug::Text('aInValid User ID: '. $punch_row['user_id'], __FILE__, __LINE__, __METHOD__,10);
					continue;
				}
			} else {
				Debug::Text('bInValid User ID: '. $punch_row['user_id'], __FILE__, __LINE__, __METHOD__,10);
				continue;
			}

			//Check to make sure the station is allowed.
			if ( is_object($current_station) AND is_object($current_user) AND $current_station->checkAllowed( $current_user->getId(), $current_station->getStation(), $current_station->getType() ) == FALSE ) {
				Debug::text('Station NOT allowed: Station ID: '. $current_station->getId() .' User: '. $current_user->getId(), __FILE__, __LINE__, __METHOD__, 10);
				continue;
			}

			$punch_full_time_stamp = TTDate::strtotime( $punch_row['date_stamp'].' '.$punch_row['time_stamp'] );
			//Make sure time stamp converts properly, otherwise skip this punch.
			if ( !is_int($punch_full_time_stamp) ) {
				Debug::Text('Failed TimeStamp: '. $punch_full_time_stamp, __FILE__, __LINE__, __METHOD__,10);
				continue;
			}

			Debug::Text('Punch Date/Time: '. $punch_full_time_stamp .' Offset that was already applied: '. $punch_row['offset'], __FILE__, __LINE__, __METHOD__,10);

			$fail_transaction = FALSE;

			$pf = new PunchFactory();
			$pf->StartTransaction();

			$slf = new ScheduleListFactory();

			//Auto Punch
			if ( ( isset($punch_row['status_id']) AND $punch_row['status_id'] == 0 )
					OR ( isset($punch_row['type_id']) AND $punch_row['type_id'] == 0 )
					OR ( isset($punch_row['branch_id']) AND $punch_row['branch_id'] == 0 )
					OR ( isset($punch_row['department_id']) AND $punch_row['department_id'] == 0 )
					OR ( isset($punch_row['job_id']) AND $punch_row['job_id'] == 0 )
					OR ( isset($punch_row['job_item_id']) AND $punch_row['job_item_id'] == 0 )
					) {

				$plf = new PunchListFactory();
				$plf->getPreviousPunchByUserIDAndEpoch( $punch_row['user_id'], $punch_full_time_stamp );
				if ($plf->getRecordCount() > 0 ) {
					Debug::Text(' Found Previous Punch within Continuous Time from now: ', __FILE__, __LINE__, __METHOD__,10);
					$prev_punch_obj = $plf->getCurrent();

					$branch_id = $prev_punch_obj->getPunchControlObject()->getBranch();
					$department_id = $prev_punch_obj->getPunchControlObject()->getDepartment();
					$job_id = $prev_punch_obj->getPunchControlObject()->getJob();
					$job_item_id = $prev_punch_obj->getPunchControlObject()->getJobItem();
					$quantity = $prev_punch_obj->getPunchControlObject()->getQuantity();
					$bad_quantity = $prev_punch_obj->getPunchControlObject()->getBadQuantity();

					if ( $branch_id == '' OR empty($branch_id) OR $department_id == '' OR empty($department_id) ) {
						Debug::Text(' Branch or department are null. ', __FILE__, __LINE__, __METHOD__,10);

						$s_obj = $slf->getScheduleObjectByUserIdAndEpoch( $punch_row['user_id'], $punch_full_time_stamp );

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
						}
					}

					$type_id = $prev_punch_obj->getNextType();
					$status_id = $prev_punch_obj->getNextStatus();

					$next_type = $prev_punch_obj->getNextType();

					//Check for break policy window.
					if ( $next_type != 30 AND ( $prev_punch_obj->getStatus() != 30 AND $prev_punch_obj->getType() != 30 ) ) {
						$prev_punch_obj->setUser( $current_user->getId() );
						$prev_punch_obj->setScheduleID( $prev_punch_obj->findScheduleID( $punch_full_time_stamp ) );
						if ( $prev_punch_obj->inBreakPolicyWindow( $punch_full_time_stamp, $prev_punch_obj->getTimeStamp() ) == TRUE ) {
							Debug::Text(' Setting Type to Break: ', __FILE__, __LINE__, __METHOD__,10);
							$next_type = 30;
						}
					}

					//Check for meal policy window.
					if ( $next_type != 20 AND ( $prev_punch_obj->getStatus() != 20 AND $prev_punch_obj->getType() != 20 ) ) {
						$prev_punch_obj->setUser( $current_user->getId() );
						$prev_punch_obj->setScheduleID( $prev_punch_obj->findScheduleID( $punch_full_time_stamp ) );
						if ( $prev_punch_obj->inMealPolicyWindow( $punch_full_time_stamp, $prev_punch_obj->getTimeStamp() ) == TRUE ) {
							Debug::Text(' Setting Type to Lunch: ', __FILE__, __LINE__, __METHOD__,10);
							$next_type = 20;
						}
					}
				} else {
					Debug::Text(' DID NOT Find Previous Punch within Continuous Time from now: ', __FILE__, __LINE__, __METHOD__,10);
					$branch_id = NULL;
					$department_id = NULL;
					$job_id = NULL;
					$job_item_id = NULL;

					$s_obj = $slf->getScheduleObjectByUserIdAndEpoch( $punch_row['user_id'], $punch_full_time_stamp );
					if ( is_object($s_obj) ) {
						Debug::Text(' Found Schedule!: ', __FILE__, __LINE__, __METHOD__,10);
						$branch_id = $s_obj->getBranch();
						$department_id = $s_obj->getDepartment();
					} else {
						$branch_id = $current_user->getDefaultBranch();
						$department_id = $current_user->getDefaultDepartment();

						//Check station for default/forced settings.
						if ( is_object($current_station) ) {
							if ( $current_station->getDefaultBranch() !== FALSE AND $current_station->getDefaultBranch() != 0 ) {
								$branch_id = $current_station->getDefaultBranch();
							}
							if ( $current_station->getDefaultDepartment() !== FALSE AND $current_station->getDefaultDepartment() != 0 ) {
								$department_id = $current_station->getDefaultDepartment();
							}
							if ( $current_station->getDefaultJob() !== FALSE AND $current_station->getDefaultJob() != 0 ) {
								$job_id = $current_station->getDefaultJob();
							}
							if ( $current_station->getDefaultJobItem() !== FALSE AND $current_station->getDefaultJobItem() != 0 ) {
								$job_item_id = $current_station->getDefaultJobItem();
							}
						}
					}

					$status_id = 10; //In
					$type_id = 10; //Normal
				}

				if ( isset($punch_row['status_id']) AND $punch_row['status_id'] != 0 ) {
					Debug::Text(' Status ID is NOT AUTO: '. $punch_row['status_id'], __FILE__, __LINE__, __METHOD__,10);
					$status_id = $punch_row['status_id'];
				}

				if ( isset($punch_row['type_id']) AND $punch_row['type_id'] != 0 ) {
					Debug::Text(' Type ID is NOT AUTO: '. $punch_row['type_id'], __FILE__, __LINE__, __METHOD__,10);
					$type_id = $punch_row['type_id'];
				}

				if ( isset($punch_row['branch_id']) AND $punch_row['branch_id'] != 0 ) {
					Debug::Text(' Branch ID is NOT AUTO: '. $punch_row['branch_id'], __FILE__, __LINE__, __METHOD__,10);
					$branch_id = $punch_row['branch_id'];
				}

				if ( isset($punch_row['department_id']) AND $punch_row['department_id'] != 0 ) {
					Debug::Text(' Department ID is NOT AUTO: '. $punch_row['department_id'], __FILE__, __LINE__, __METHOD__,10);
					$department_id = $punch_row['department_id'];
				}

				if ( isset($punch_row['job_id']) AND $punch_row['job_id'] != 0 ) {
					Debug::Text(' Job ID is NOT AUTO: '. $punch_row['job_id'], __FILE__, __LINE__, __METHOD__,10);
					$job_id = $punch_row['job_id'];
				}

				if ( isset($punch_row['job_item_id']) AND $punch_row['job_item_id'] != 0 ) {
					Debug::Text(' Job Item ID is NOT AUTO: '. $punch_row['job_item_id'], __FILE__, __LINE__, __METHOD__,10);
					$job_item_id = $punch_row['job_item_id'];
				}

				if ( isset($punch_row['quantity']) ) {
					Debug::Text(' Quantity is NOT AUTO: '. $punch_row['quantity'], __FILE__, __LINE__, __METHOD__,10);
					$quantity = $punch_row['quantity'];
				}

				if ( isset($punch_row['bad_quantity']) ) {
					Debug::Text(' Bad Quantity is NOT AUTO: '. $punch_row['bad_quantity'], __FILE__, __LINE__, __METHOD__,10);
					$bad_quantity = $punch_row['bad_quantity'];
				}

			} else {
				$status_id = $punch_row['status_id'];
				$type_id = $punch_row['type_id'];
				$branch_id = $punch_row['branch_id'];
				$department_id = $punch_row['department_id'];
				$job_id = $punch_row['job_id'];
				$job_item_id = $punch_row['job_item_id'];
				$quantity = $punch_row['quantity'];
				$bad_quantity = $punch_row['bad_quantity'];
			}

			//Set User before setTimeStamp so rounding can be done properly.
			$pf->setUser( $punch_row['user_id'] );

			if ( isset($punch_row['transfer']) AND $punch_row['transfer'] == 1 ) {
				Debug::Text(' Enabling Transfer!: ', __FILE__, __LINE__, __METHOD__,10);
				$type_id = 10;
				$status_id = 10;
				$pf->setTransfer( TRUE );
			}

			$pf->setType( $type_id );
			$pf->setStatus( $status_id );
			$pf->setTimeStamp( $punch_full_time_stamp, TRUE ); //Make sure we round here.

			if ( isset($status_id) AND $status_id == 20 AND isset( $punch_row['punch_control_id'] ) AND $punch_row['punch_control_id']  != '' AND $punch_row['punch_control_id'] != 0) {
				$pf->setPunchControlID( $punch_row['punch_control_id'] );
			} else {
				$pf->setPunchControlID( $pf->findPunchControlID() );
			}

			$pf->setStation( $current_station->getId() );

			if ( $pf->isNew() ) {
				$pf->setActualTimeStamp( $punch_full_time_stamp );
				$pf->setOriginalTimeStamp( $pf->getTimeStamp() );
			}

			if ( $pf->isValid() == TRUE ) {

				if ( $pf->Save( FALSE ) == TRUE ) {
					$pcf = new PunchControlFactory();
					$pcf->setId( $pf->getPunchControlID() );
					$pcf->setPunchObject( $pf );

					if ( isset($branch_id) AND $branch_id != '') {
						$pcf->setBranch( $branch_id );
					}
					if ( isset($department_id) AND $department_id != '' ) {
						$pcf->setDepartment( $department_id );
					}

					if ( isset($job_id) AND $job_id != '' ) {
						$pcf->setJob( $job_id );
					}
					if ( isset($job_item_id) AND $job_item_id != '') {
						$pcf->setJobItem( $job_item_id );
					}
					if ( isset($quantity) AND $quantity != '' ) {
						$pcf->setQuantity( $quantity );
					}
					if ( isset($bad_quantity) AND $bad_quantity != '' ) {
						$pcf->setBadQuantity( $bad_quantity );
					}

					if ( isset($punch_row['note']) AND $punch_row['note'] != '' ) {
						$pcf->setNote( $punch_row['note'] );
					}

					if ( isset($punch_row['other_id1']) AND $punch_row['other_id1'] != '' ) {
						$pcf->setOtherID1( $punch_row['other_id1'] );
					}
					if ( isset($punch_row['other_id2']) AND $punch_row['other_id2'] != '' ) {
						$pcf->setOtherID2( $punch_row['other_id2'] );
					}
					if ( isset($punch_row['other_id3']) AND $punch_row['other_id3'] != '' ) {
						$pcf->setOtherID3( $punch_row['other_id3'] );
					}
					if ( isset($punch_row['other_id4']) AND $punch_row['other_id4'] != '' ) {
						$pcf->setOtherID4( $punch_row['other_id4'] );
					}
					if ( isset($punch_row['other_id5']) AND $punch_row['other_id5'] != ''  ) {
						$pcf->setOtherID5( $punch_row['other_id5'] );
					}

					$pcf->setEnableStrictJobValidation( TRUE );
					$pcf->setEnableCalcUserDateID( TRUE );
					$pcf->setEnableCalcTotalTime( TRUE );
					$pcf->setEnableCalcSystemTotalTime( TRUE );
					$pcf->setEnableCalcUserDateTotal( TRUE );
					$pcf->setEnableCalcException( TRUE );
					$pcf->setEnablePreMatureException( TRUE ); //Enable pre-mature exceptions at this point.

					if ( $pcf->isValid() == TRUE ) {
						Debug::Text(' Punch Control is valid, saving...: ', __FILE__, __LINE__, __METHOD__,10);

						if ( $pcf->Save( TRUE, TRUE ) == TRUE ) { //Force isNew() lookup.
							Debug::text('Saved Punch!', __FILE__, __LINE__, __METHOD__, 10);
						} else {
							Debug::text('PCF Save failed... Failing Transaction!', __FILE__, __LINE__, __METHOD__, 10);
							$fail_transaction = TRUE;
						}
					} else {
						Debug::text('PCF Validate failed... Failing Transaction!', __FILE__, __LINE__, __METHOD__, 10);
						$fail_transaction = TRUE;
					}
				} else {
					Debug::text('PF Save failed... Failing Transaction!', __FILE__, __LINE__, __METHOD__, 10);
					$fail_transaction = TRUE;
				}
			} else {
				Debug::text('PF Validate failed... Failing Transaction!', __FILE__, __LINE__, __METHOD__, 10);
				$fail_transaction = TRUE;
			}

			if ( $fail_transaction == FALSE ) {
				$pf->CommitTransaction();
			} else {
				$pf->FailTransaction();
			}

			unset($punch_full_time_stamp, $current_station, $current_user);
			//End Foreach
		}

		return TRUE;
	}

	function __call( $name, $arguments ) {
		Debug::text('ERROR: Attempting to call function that does not exist in this class! Class: '. __CLASS__ .' Function: '. $name, __FILE__, __LINE__, __METHOD__, 10);

		return FALSE;
	}
}
?>
