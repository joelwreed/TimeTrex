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
 * $Revision: 1246 $
 * $Id: InstallSchema_1001B.class.php 1246 2007-09-14 23:47:42Z ipso $
 * $Date: 2007-09-14 16:47:42 -0700 (Fri, 14 Sep 2007) $
 */

/**
 * @package Module_Install
 */
class InstallSchema_1031A extends InstallSchema_Base {

	function preInstall() {
		Debug::text('preInstall: '. $this->getVersion() , __FILE__, __LINE__, __METHOD__,9);

		return TRUE;
	}


	function postInstall() {
		Debug::text('postInstall: '. $this->getVersion(), __FILE__, __LINE__, __METHOD__,9);

		//Go through all pay period schedules and update the annual pay period column
		$ppslf = new PayPeriodScheduleListFactory();
		$ppslf->getAll();
		if ( $ppslf->getRecordCount() > 0 ) {
			foreach( $ppslf as $pps_obj ) {
				$pps_obj->setAnnualPayPeriods( $pps_obj->calcAnnualPayPeriods() );
				if ( $pps_obj->isValid() ) {
					$pps_obj->Save();
				}
			}
		}

		//Go through all employee wages and update HourlyRate to the accurate annual hourly rate.
		//**Handle this in 1034A postInstall() instead, as it needs to handle incorrect effective_dates properly.
		/*
		$uwlf = new UserWageListFactory();
		$uwlf->getAll();
		if ( $uwlf->getRecordCount() > 0 ) {
			foreach( $uwlf as $uw_obj ) {
				$uw_obj->setHourlyRate( $uw_obj->calcHourlyRate( time(), TRUE ) );
				if ( $uw_obj->isValid() ) {
					$uw_obj->Save();
				}
			}
		}
		*/

		//Upgrade to new hierarchy format.
		$clf = new CompanyListFactory();
		$clf->getAll();
		if ( $clf->getRecordCount() > 0 ) {
			foreach ( $clf as $c_obj ) {
				if ( $c_obj->getStatus() != 30 ) {
/*
					if ( !($c_obj->getId() == 1052) ) { //$c_obj->getId() == 1009 OR $c_obj->getId() == 1087 OR
						continue;
					}
*/
					$company_id = $c_obj->getId();
					Debug::Text(' Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

					$hclf = new HierarchyControlListFactory();
					$hclf->StartTransaction();
					$hclf->getByCompanyId( $company_id );
					if ( $hclf->getRecordCount() > 0 ) {
						foreach( $hclf as $hc_obj ) {
							$paths_to_root = array();
							$hierarchy_id = $hc_obj->getId();

							$hlf = new HierarchyListFactory();
							$hierarchy_users = $hlf->getByCompanyIdAndHierarchyControlId( $company_id, $hierarchy_id );
							if ( is_array($hierarchy_users) AND count($hierarchy_users) > 0 ) {
								$hotlf = new HierarchyObjectTypeListFactory();
								$hotlf->getByHierarchyControlId( $hierarchy_id );
								if ( $hotlf->getRecordCount() > 0 ) {
									foreach( $hotlf as $hot_obj ) {
										$object_types[$hierarchy_id][] = $hot_obj->getObjectType();
									}
								}

								foreach( $hierarchy_users as $hierarchy_user_arr ) {
									Debug::Text(' Checking User ID: '. $hierarchy_user_arr['id'], __FILE__, __LINE__, __METHOD__,10);
									$id = $hierarchy_user_arr['id'];

									$tmp_id = $id;
									$i = 0;
									do {
										Debug::Text(' Iteration...', __FILE__, __LINE__, __METHOD__,10);
										$hlf_b = new HierarchyListFactory();
										$parents = $hlf_b->getParentLevelIdArrayByHierarchyControlIdAndUserId( $hierarchy_id, $tmp_id);
										sort($parents);

										$level = $hlf_b->getFastTreeObject()->getLevel( $tmp_id )-1;

										if ( is_array($parents) AND count($parents) > 0 ) {
											$parent_users = array();
											foreach($parents as $user_id) {
												$parent_users[] = $user_id;
												unset($user);
											}

											$parent_groups[$level] = $parent_users;
											unset($parent_users);
										}

										if ( isset($parents[0]) ) {
											$tmp_id = $parents[0];
										}

										$i++;
									} while ( is_array($parents) AND count($parents) > 0 AND $i < 100 );

									if ( isset($parent_groups) ) {
										$serialized_path = serialize($parent_groups);
										$paths_to_root[$serialized_path][] = $id;
										unset($serialized_path);
									}
									unset($parent_groups, $parents);
								}
							}
							Debug::Arr($paths_to_root, ' Paths To Root: ', __FILE__, __LINE__, __METHOD__,10);

							//Decode path_to_root array
							if ( isset($paths_to_root) AND count($paths_to_root) > 0 ) {
								foreach( $paths_to_root as $serialized_path => $children ) {
									$path_arr = unserialize( $serialized_path );
									$decoded_paths[] = array( 'hierarchy_control_id' => $hierarchy_id , 'path' => $path_arr, 'children' => $children );
								}
								unset($path_arr, $children);

								Debug::Arr($decoded_paths, ' Decoded Paths: ', __FILE__, __LINE__, __METHOD__,10);

								if ( isset($decoded_paths) AND is_array($decoded_paths) ) {
									foreach( $decoded_paths as $decoded_path ) {
										Debug::Text(' Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

										//Create new hierarchy_control
										$hcf = new HierarchyControlFactory();
										$hcf->setCompany( $company_id );
										$hcf->setObjectType(  $object_types[$decoded_path['hierarchy_control_id']] );

										//Generate meaningful name
										$name = FALSE;
										if ( isset($decoded_path['path']) AND is_array($decoded_path['path']) ) {
											ksort($decoded_path['path']); //Sort by level.

											foreach( $decoded_path['path'] as $level => $superior_ids ) {
												foreach( $superior_ids as $superior_id) {
													$ulf = new UserListFactory();
													$ulf->getById( $superior_id );

													if ( $ulf->getRecordCount() > 0 ) {
														$name[] = $level.'. '.$ulf->getCurrent()->getFullName();
													}
												}
											}
											unset($level, $superior_ids, $superior_id);
										}

										if ( isset($name) ) {
											$name = $hc_obj->getName() .' '. implode(', ', $name ) .' (#'.rand(1000,9999).')';
										} else {
											$name = $hc_obj->getName() .' (#'.rand(1000,9999).')';
										}

										$hcf->setName( substr($name, 0, 249) );
										$hcf->setDescription( TTi18n::getText('Automatically created by TimeTrex') );

										if ( $hcf->isValid() ) {
											$hc_id = $hcf->Save(FALSE);

											Debug::Text('Hierarchy Control ID: '. $hc_id , __FILE__, __LINE__, __METHOD__,10);
											$hcf->setUser( $decoded_path['children'] );

											if ( isset($decoded_path['path']) AND is_array($decoded_path['path']) ) {
												foreach( $decoded_path['path'] as $level => $superior_ids ) {
													foreach( $superior_ids as $superior_id ) {
														$hlf = new HierarchyLevelFactory();
														$hlf->setHierarchyControl( $hc_id );
														$hlf->setLevel( $level );
														$hlf->setUser( $superior_id );

														if ( $hlf->isValid() ) {
															$hlf->Save();
															Debug::Text('Saving Level Row ID... User ID: '. $superior_id, __FILE__, __LINE__, __METHOD__,10);
														}
													}
												}
												unset($level, $superior_ids, $superior_id);
											}
										}
									}
								}
								unset($decoded_paths);
							}

							//Delete existing hierarchy control.
							$hc_obj->setDeleted(TRUE);
							if ( $hc_obj->isValid() == TRUE ) {
								$hc_obj->Save();
							}
						}
					}

					//$hclf->FailTransaction();
					$hclf->CommitTransaction();
				}
			}
		}

		//Go through each permission group, and enable break policies for anyone who can see meal policies
		$clf = new CompanyListFactory();
		$clf->getAll();
		if ( $clf->getRecordCount() > 0 ) {
			foreach( $clf as $c_obj ) {
				Debug::text('Company: '. $c_obj->getName(), __FILE__, __LINE__, __METHOD__,9);
				if ( $c_obj->getStatus() != 30 ) {
					$pclf = new PermissionControlListFactory();
					$pclf->getByCompanyId( $c_obj->getId() );
					if ( $pclf->getRecordCount() > 0 ) {
						foreach( $pclf as $pc_obj ) {
							Debug::text('Permission Group: '. $pc_obj->getName(), __FILE__, __LINE__, __METHOD__,9);
							$plf = new PermissionListFactory();
							$plf->getByCompanyIdAndPermissionControlIdAndSectionAndName( $c_obj->getId(), $pc_obj->getId(), 'meal_policy', 'enabled');
							if ( $plf->getRecordCount() > 0 ) {
								Debug::text('Found permission group with meal policy enabled: '. $plf->getCurrent()->getValue(), __FILE__, __LINE__, __METHOD__,9);
								$pc_obj->setPermission(
													   array(   'break_policy' => array(
																					'enabled' => TRUE,
																					'view' => TRUE,
																					'add' => TRUE,
																					'edit' => TRUE,
																					'delete' => TRUE,
																				  )
															)

													   );
							} else {
								Debug::text('Permission group does NOT have meal policy enabled...', __FILE__, __LINE__, __METHOD__,9);
							}
						}
					}
				}
			}
		}

		//Add MiscDaily cronjob to database.
		$cjf = new CronJobFactory();
		$cjf->setName('MiscDaily');
		$cjf->setMinute(55);
		$cjf->setHour(1);
		$cjf->setDayOfMonth('*');
		$cjf->setMonth('*');
		$cjf->setDayOfWeek('*');
		$cjf->setCommand('MiscDaily.php');
		$cjf->Save();

		//Add MiscWeekly cronjob to database.
		$cjf = new CronJobFactory();
		$cjf->setName('MiscWeekly');
		$cjf->setMinute(55);
		$cjf->setHour(1);
		$cjf->setDayOfMonth('*');
		$cjf->setMonth('*');
		$cjf->setDayOfWeek('0'); //Sunday morning.
		$cjf->setCommand('MiscWeekly.php');
		$cjf->Save();

		return TRUE;
	}
}
?>
