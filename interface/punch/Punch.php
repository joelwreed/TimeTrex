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
 * $Revision: 2858 $
 * $Id: Punch.php 2858 2009-09-29 18:12:05Z ipso $
 * $Date: 2009-09-29 11:12:05 -0700 (Tue, 29 Sep 2009) $
 */
require_once('../../includes/global.inc.php');

//Debug::setVerbosity(11);
$skip_message_check = TRUE;
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

if ( !$permission->Check('punch','enabled')
		OR !( $permission->Check('punch','punch_in_out') ) ) {

	$permission->Redirect( FALSE ); //Redirect

}

$smarty->assign('title', TTi18n::gettext($title = 'Punch In / Out')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'id',
												'data'
												) ) );

if ( isset($data) AND isset($data['time_stamp']) ) {
	$data['punch_full_time_stamp'] = $data['time_stamp'];

	//Make sure employees don't try to circumvent the disabled timestamp field. By allowing a small variance.
	$max_variance = 300; //5minutes.
	if ( $data['punch_full_time_stamp'] > (TTDate::getTime()+$max_variance) OR $data['punch_full_time_stamp'] < (TTDate::getTime()-$max_variance) ) {
		Debug::Text('TimeStamp is outside allowed variance window, resetting to actual time.', __FILE__, __LINE__, __METHOD__,10);
		$data['punch_full_time_stamp'] = TTDate::getTime();
	}
}

$pcf = new PunchControlFactory();
$pf = new PunchFactory();

$action = Misc::findSubmitButton();
switch ($action) {
	case 'submit':
		//Debug::setVerbosity(11);
		Debug::Text('Submit!', __FILE__, __LINE__, __METHOD__,10);

		$pf->StartTransaction();

		//Set User before setTimeStamp so rounding can be done properly.
		$pf->setUser( $current_user->getId() );

		if ( isset($data['transfer']) ) {
			$data['type_id'] = 10;
			$data['status_id'] = 10;
			$pf->setTransfer( TRUE );
		}

		$pf->setType( $data['type_id'] );
		$pf->setStatus( $data['status_id'] );
		$pf->setTimeStamp( $data['punch_full_time_stamp'] );

		if ( isset($data['status_id']) AND $data['status_id'] == 20 AND isset( $pc_data['punch_control_id'] ) AND $pc_data['punch_control_id']  != '' ) {
			$pf->setPunchControlID( $pc_data['punch_control_id'] );
		} else {
			$pf->setPunchControlID( $pf->findPunchControlID() );
		}

		$pf->setStation( $current_station->getID() );

		if ( $pf->isNew() ) {
			$pf->setActualTimeStamp( $data['punch_full_time_stamp'] );
			$pf->setOriginalTimeStamp( $pf->getTimeStamp() );
		}

		if ( $pf->isValid() == TRUE ) {

			if ( $pf->Save( FALSE ) == TRUE ) {
				$pcf = new PunchControlFactory();
				$pcf->setId( $pf->getPunchControlID() );
				$pcf->setPunchObject( $pf );

				if ( isset($data['user_date_id']) AND $data['user_date_id'] == '' ) {
					//$pcf->setUserDateID( $data['user_date_id'] );
				}

				if ( isset($data['branch_id']) ) {
					$pcf->setBranch( $data['branch_id'] );
				}
				if ( isset($data['department_id']) ) {
					$pcf->setDepartment( $data['department_id'] );
				}

				if ( isset($data['job_id']) ) {
					$pcf->setJob( $data['job_id'] );
				}
				if ( isset($data['job_item_id']) ) {
					$pcf->setJobItem( $data['job_item_id'] );
				}
				if ( isset($data['quantity']) ) {
					$pcf->setQuantity( $data['quantity'] );
				}
				if ( isset($data['bad_quantity']) ) {
					$pcf->setBadQuantity( $data['bad_quantity'] );
				}
				if ( isset($data['note']) ) {
					$pcf->setNote( $data['note'] );
				}

				if ( isset($data['other_id1']) ) {
					$pcf->setOtherID1( $data['other_id1'] );
				}
				if ( isset($data['other_id2']) ) {
					$pcf->setOtherID2( $data['other_id2'] );
				}
				if ( isset($data['other_id3']) ) {
					$pcf->setOtherID3( $data['other_id3'] );
				}
				if ( isset($data['other_id4']) ) {
					$pcf->setOtherID4( $data['other_id4'] );
				}
				if ( isset($data['other_id5']) ) {
					$pcf->setOtherID5( $data['other_id5'] );
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
						//$pf->FailTransaction();
						$pf->CommitTransaction();

						Redirect::Page( URLBuilder::getURL( NULL, '../CloseWindow.php') );

						break;
					}
				}
			}
		}

		$pf->FailTransaction();
	default:
		$epoch = TTDate::getTime();

		$slf = new ScheduleListFactory();

		//Get last punch for this day, for this user.
		$plf = new PunchListFactory();

		if ( $action != 'submit' ) {
			$plf->getPreviousPunchByUserIDAndEpoch( $current_user->getId(), $epoch );
			if ($plf->getRecordCount() > 0 ) {
				$prev_punch_obj = $plf->getCurrent();
				Debug::Text(' Found Previous Punch within Continuous Time from now: '. TTDate::getDate('DATe+TIME', $prev_punch_obj->getTimeStamp() ), __FILE__, __LINE__, __METHOD__,10);

				$branch_id = $prev_punch_obj->getPunchControlObject()->getBranch();
				$department_id = $prev_punch_obj->getPunchControlObject()->getDepartment();
				$job_id = $prev_punch_obj->getPunchControlObject()->getJob();
				$job_item_id = $prev_punch_obj->getPunchControlObject()->getJobItem();

				//Don't enable transfer by default if the previous punch was any OUT punch.
				//Transfer does the OUT punch for them, so if the previous punch is an OUT punch
				//we don't gain anything anyways.
				if ( $permission->Check('punch','default_transfer') AND $prev_punch_obj->getStatus() == 10 ) {
					$transfer = TRUE;
				} else {
					$transfer = FALSE;
				}

				if ( $branch_id == '' OR empty($branch_id)
						OR $department_id == '' OR empty($department_id)
						OR $job_id == '' OR empty($job_id)
						OR $job_item_id == '' OR empty($job_item_id) ) {
					Debug::Text(' Branch or department are null. ', __FILE__, __LINE__, __METHOD__,10);

					$s_obj = $slf->getScheduleObjectByUserIdAndEpoch( $current_user->getId(), $epoch );

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
					$prev_punch_obj->setUser( $current_user->getId() );
					$prev_punch_obj->setScheduleID( $prev_punch_obj->findScheduleID( $epoch ) );
					if ( $prev_punch_obj->inBreakPolicyWindow( $epoch, $prev_punch_obj->getTimeStamp() ) == TRUE ) {
						Debug::Text(' Setting Type to Break: ', __FILE__, __LINE__, __METHOD__,10);
						$next_type = 30;
					}
				}

				//Check for meal policy window.
				if ( $next_type != 20 AND ( $prev_punch_obj->getStatus() != 20 AND $prev_punch_obj->getType() != 20 ) ) {
					$prev_punch_obj->setUser( $current_user->getId() );
					$prev_punch_obj->setScheduleID( $prev_punch_obj->findScheduleID( $epoch ) );
					if ( $prev_punch_obj->inMealPolicyWindow( $epoch, $prev_punch_obj->getTimeStamp() ) == TRUE ) {
						Debug::Text(' Setting Type to Lunch: ', __FILE__, __LINE__, __METHOD__,10);
						$next_type = 20;
					}
				}

				if ( $prev_punch_obj->getNextStatus() == 10 ) {
					//In punch - Carry over just certain data
					$data = array(
									'user_id' => $current_user->getId(),
									'user_full_name' => $current_user->getFullName(),
									'time_stamp' => $epoch,
									'date_stamp' => $epoch,
									'transfer' => $transfer,
									'branch_id' => $branch_id,
									'department_id' => $department_id,
									'job_id' => $job_id,
									'job_item_id' => $job_item_id,
									'quantity' => 0,
									'bad_quantity' => 0,
									'status_id' => $prev_punch_obj->getNextStatus(),
									'type_id' => $next_type,
									'punch_control_id' => $prev_punch_obj->getNextPunchControlID(),
									//'user_date_id' => $prev_punch_obj->getPunchControlObject()->getUserDateID()
									);
				} else {
					//Out punch
					$data = array(
									'user_id' => $current_user->getId(),
									'user_full_name' => $current_user->getFullName(),
									'time_stamp' => $epoch,
									'date_stamp' => $epoch,
									'transfer' => $transfer,
									'branch_id' => $branch_id,
									'department_id' => $department_id,
									'job_id' => $job_id,
									'job_item_id' => $job_item_id,
									'quantity' => (float)$prev_punch_obj->getPunchControlObject()->getQuantity(),
									'bad_quantity' => (float)$prev_punch_obj->getPunchControlObject()->getBadQuantity(),
									'note' => $prev_punch_obj->getPunchControlObject()->getNote(),
									'other_id1' => $prev_punch_obj->getPunchControlObject()->getOtherID1(),
									'other_id2' => $prev_punch_obj->getPunchControlObject()->getOtherID2(),
									'other_id3' => $prev_punch_obj->getPunchControlObject()->getOtherID3(),
									'other_id4' => $prev_punch_obj->getPunchControlObject()->getOtherID4(),
									'other_id5' => $prev_punch_obj->getPunchControlObject()->getOtherID5(),
									'status_id' => $prev_punch_obj->getNextStatus(),
									'type_id' => $next_type,
									'punch_control_id' => $prev_punch_obj->getNextPunchControlID(),
									//'user_date_id' => $prev_punch_obj->getPunchControlObject()->getUserDateID()
									);

				}
			} else {
				Debug::Text(' DID NOT Find Previous Punch within Continuous Time from now: ', __FILE__, __LINE__, __METHOD__,10);
				$branch_id = NULL;
				$department_id = NULL;
				$job_id = NULL;
				$job_item_id = NULL;

				$s_obj = $slf->getScheduleObjectByUserIdAndEpoch( $current_user->getId(), $epoch );
				if ( is_object($s_obj) ) {
					Debug::Text(' Found Schedule!: ', __FILE__, __LINE__, __METHOD__,10);
					$branch_id = $s_obj->getBranch();
					$department_id = $s_obj->getDepartment();
					$job_id = $s_obj->getJob();
					$job_item_id = $s_obj->getJobItem();
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

				$data = array(
								'user_id' => $current_user->getId(),
								'user_full_name' => $current_user->getFullName(),
								'time_stamp' => $epoch,
								'date_stamp' => $epoch,
								'branch_id' => $branch_id,
								'department_id' => $department_id,
								'job_id' => $job_id,
								'job_item_id' => $job_item_id,
								'quantity' => 0,
								'bad_quantity' => 0,
								'status_id' => 10, //In
								'type_id' => 10, //Normal
								);
			}
		} else {
			$data['user_id'] = $current_user->getId();
			$data['user_full_name'] = $current_user->getFullName();
			$data['time_stamp'] = $epoch;
			$data['date_stamp'] = $epoch;
		}

		$blf = new BranchListFactory();
		$blf->getByCompanyId( $current_company->getId() );
		$branch_options = $blf->getArrayByListFactory( $blf, TRUE, FALSE );
		//$branch_options = $blf->getByCompanyIdArray( $current_company->getId() );

		$dlf = new DepartmentListFactory();
		$dlf->getByCompanyId( $current_company->getId() );
		$department_options = $dlf->getArrayByListFactory( $dlf, TRUE, FALSE);
		//$department_options = $dlf->getByCompanyIdArray( $current_company->getId() );

		if ( $current_company->getProductEdition() == 20 ) {
			$jlf = new JobListFactory();
			$jlf->getByCompanyIdAndUserIdAndStatus( $current_company->getId(), $current_user->getId(), array(10) );
			$data['job_options'] = $jlf->getArrayByListFactory( $jlf, TRUE, TRUE );
			$data['job_manual_id_options'] = $jlf->getManualIDArrayByListFactory($jlf, TRUE);

			$jilf = new JobItemListFactory();
			$jilf->getByCompanyIdAndStatus( $current_company->getId(), 10 );
			$data['job_item_options'] = $jilf->getArrayByListFactory( $jilf, TRUE, FALSE );
			$data['job_item_manual_id_options'] = $jilf->getManualIdArrayByListFactory( $jilf, FALSE );
		}

		//Select box options;
		$data['status_options'] = $pf->getOptions('status');
		$data['type_options'] = $pf->getOptions('type');
		$data['branch_options'] = $branch_options;
		$data['department_options'] = $department_options;

		//Get other field names
		$oflf = new OtherFieldListFactory();
		$data['other_field_names'] = $oflf->getByCompanyIdAndTypeIdArray( $current_company->getId(), 15 );

		//Make sure current station is allowed.
		if ( isset( $current_station ) AND is_object( $current_station ) ) {
			if ( isset($_GET['ibutton']) ) {
				$station_is_allowed = $current_station->checkAllowed( NULL, NULL, 'iBUTTON' );
			} else {
				$station_is_allowed = $current_station->checkAllowed();
			}
		} else {
			Debug::Text('No Station Found!', __FILE__, __LINE__, __METHOD__,10);
			$station_is_allowed = FALSE; //No station present.
		}
		//var_dump($pc_data);

		$smarty->assign_by_ref('data', $data);
		$smarty->assign_by_ref('station_is_allowed', $station_is_allowed);

		break;
}

$smarty->assign_by_ref('pcf', $pcf);
$smarty->assign_by_ref('pf', $pf);

$smarty->display('punch/Punch.tpl');
?>