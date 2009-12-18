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
 * $Id: CalculatePayStub.class.php 3021 2009-11-11 23:33:03Z ipso $
 * $Date: 2009-11-11 15:33:03 -0800 (Wed, 11 Nov 2009) $
 */

/**
 * @package Core
 */
class CalculatePayStub extends PayStubFactory {

	var $wage_obj = NULL;
	var $user_obj = NULL;
	var $user_wage_obj = NULL;
	var $pay_period_obj = NULL;
	var $pay_period_schedule_obj = NULL;
	var $payroll_deduction_obj = NULL;
	var $pay_stub_entry_account_link_obj = NULL;
	var $pay_stub_entry_accounts_type_obj = NULL;

	function getUser() {
		if ( isset($this->data['user_id']) ) {
			return $this->data['user_id'];
		}
	}
	function setUser($id) {
		$id = trim($id);

		$ulf = new UserListFactory();

		if ( $id == 0
				OR $this->Validator->isResultSetWithRows(	'user',
															$ulf->getByID($id),
															TTi18n::gettext('Invalid User')
															) ) {
			$this->data['user_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPayPeriod() {
		if ( isset($this->data['pay_period_id']) ) {
			return $this->data['pay_period_id'];
		}

		return FALSE;
	}
	function setPayPeriod($id) {
		$id = trim($id);

		$pplf = new PayPeriodListFactory();

		if (  $this->Validator->isResultSetWithRows(	'pay_period',
														$pplf->getByID($id),
														TTi18n::gettext('Invalid Pay Period')
														) ) {
			$this->data['pay_period_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getEnableCorrection() {
		if ( isset($this->correction) ) {
			return $this->correction;
		}

		return FALSE;
	}
	function setEnableCorrection($bool) {
		$this->correction = (bool)$bool;

		return TRUE;
	}

	function getUserObject() {
		if ( is_object($this->user_obj) ) {
			return $this->user_obj;
		} else {
			$ulf = new UserListFactory();
			//$this->user_obj = $ulf->getById( $this->getUser() )->getCurrent();
			$ulf->getById( $this->getUser() );
			if ( $ulf->getRecordCount() > 0 ) {
				$this->user_obj = $ulf->getCurrent();

				return $this->user_obj;
			}

			return FALSE;
		}
	}

	function getPayStubEntryAccountLinkObject() {
		if ( is_object($this->pay_stub_entry_account_link_obj) ) {
			return $this->pay_stub_entry_account_link_obj;
		} else {
			$pseallf = new PayStubEntryAccountLinkListFactory();
			$pseallf->getByCompanyId( $this->getUserObject()->getCompany() );
			if ( $pseallf->getRecordCount() > 0 ) {
				$this->pay_stub_entry_account_link_obj = $pseallf->getCurrent();
				return $this->pay_stub_entry_account_link_obj;
			}

			return FALSE;
		}
	}

	function getPayPeriodObject() {
		if ( is_object($this->pay_period_obj) ) {
			return $this->pay_period_obj;
		} else {
			$pplf = new PayPeriodListFactory();
			//$this->pay_period_obj = $pplf->getById( $this->getPayPeriod() )->getCurrent();
			$pplf->getById( $this->getPayPeriod() );
			if ( $pplf->getRecordCount() > 0 ) {
				$this->pay_period_obj = $pplf->getCurrent();

				return $this->pay_period_obj;
			}

			return FALSE;
		}
	}

	function getPayPeriodScheduleObject() {
		if ( is_object($this->pay_period_schedule_obj) ) {
			return $this->pay_period_schedule_obj;
		} else {
			$ppslf = new PayPeriodScheduleListFactory();
			$this->pay_period_schedule_obj = $ppslf->getById( $this->getPayPeriodObject()->getPayPeriodSchedule() )->getCurrent();

			return $this->pay_period_schedule_obj;
		}
	}

	function getWageObject() {
		if ( is_object($this->wage_obj) ) {
			return $this->wage_obj;
		} else {

			$this->wage_obj = new Wage( $this->getUser(), $this->getPayPeriod() );

			return $this->wage_obj;
		}
	}

	function getPayStubEntryAccountsTypeArray() {
		if ( is_array($this->pay_stub_entry_accounts_type_obj) ) {
			//Debug::text('Returning Cached data...' , __FILE__, __LINE__, __METHOD__,10);
			return $this->pay_stub_entry_accounts_type_obj;
		} else {
			$psealf = new PayStubEntryAccountListFactory();
			$this->pay_stub_entry_accounts_type_obj = $psealf->getByTypeArrayByCompanyIdAndStatusId( $this->getUserObject()->getCompany(), 10 );

			if ( is_array( $this->pay_stub_entry_accounts_type_obj ) ) {
				return $this->pay_stub_entry_accounts_type_obj;
			}

			Debug::text('Returning FALSE...' , __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}
	}

	function getDeductionObjectArrayForSorting( $obj ) {
		//$psealf = new PayStubEntryAccountListFactory();
		//$type_map_arr = $psealf->getByTypeArrayByCompanyIdAndStatusId( $this->getUserObject()->getCompany(), 10 );
		$type_map_arr = $this->getPayStubEntryAccountsTypeArray();

		//Debug::Arr($type_map_arr, 'PS Account Type Map Array: ', __FILE__, __LINE__, __METHOD__,10);

		if ( !is_object($obj) ) {
			return FALSE;
		}

		if ( get_class($obj) == 'UserDeductionListFactory' ) {
			if ( !is_object( $obj->getCompanyDeductionObject() ) ) {
				return FALSE;
			}

			if ( !is_object( $obj->getCompanyDeductionObject()->getPayStubEntryAccountObject() ) ) {
				Debug::text('Bad PS Entry Account(s) for Company Deduction. Skipping... ID: '. $obj->getCompanyDeductionObject()->getId(), __FILE__, __LINE__, __METHOD__,10);
				return FALSE;
			}
			//Debug::Arr($obj->getCompanyDeductionObject()->getIncludePayStubEntryAccount(), 'Include Accounts: ', __FILE__, __LINE__, __METHOD__,10);
			//Debug::Arr($obj->getCompanyDeductionObject()->getExcludePayStubEntryAccount(), 'Exclude Accounts: ', __FILE__, __LINE__, __METHOD__,10);

			$arr['type'] = get_class( $obj );
			$arr['obj_id'] = $obj->getId();
			$arr['id'] = substr($arr['type'],0,1).$obj->getId();
			$arr['name'] = $obj->getCompanyDeductionObject()->getName();
			$arr['order'] = $obj->getCompanyDeductionObject()->getPayStubEntryAccountObject()->getTypeCalculationOrder();
			$arr['obj'] = $obj;
			$arr['require_accounts'] = array();

			$include_accounts = $obj->getCompanyDeductionObject()->getIncludePayStubEntryAccount();
			if ( is_array($include_accounts) ) {
				foreach( $include_accounts as $include_account ) {
					if ( isset($type_map_arr[$include_account]) ) {
						foreach ($type_map_arr[$include_account] as $type_account ) {
							$arr['require_accounts'][] = $type_account;
						}
					} else {
						$arr['require_accounts'][] = $include_account;
					}
				}
			}
			unset($include_accounts, $include_account, $type_account);

			$exclude_accounts = $obj->getCompanyDeductionObject()->getExcludePayStubEntryAccount();
			if ( is_array($exclude_accounts) ) {
				foreach( $exclude_accounts as $exclude_account ) {
					if ( isset($type_map_arr[$exclude_account]) ) {
						foreach ($type_map_arr[$exclude_account] as $type_account ) {
							$arr['require_accounts'][] = $type_account;
						}
					} else {
						$arr['require_accounts'][] = $exclude_account;
					}
				}
			}
			unset($exclude_accounts, $exclude_account, $type_account);

			$arr['affect_accounts'] = $obj->getCompanyDeductionObject()->getPayStubEntryAccount();

			return $arr;
		} elseif ( get_class($obj) == 'PayStubAmendmentListFactory' ) {
			$arr['type'] = get_class( $obj );
			$arr['obj_id'] = $obj->getId();
			$arr['id'] = substr($arr['type'],0,1).$obj->getId();
			$arr['name'] = $obj->getDescription();
			$arr['order'] = $obj->getPayStubEntryNameObject()->getTypeCalculationOrder();
			$arr['obj'] = $obj;

			$arr['affect_accounts'] = $obj->getPayStubEntryNameId();

			if ( $obj->getType() == 10 ) { //Fixed
				$arr['require_accounts'][] = NULL;
			} else { //Percent
				$arr['require_accounts'][] = $obj->getPercentAmountEntryNameId();
			}

			return $arr;
		}

		return FALSE;
	}

	function getOrderedDeductionAndPSAmendment( $udlf, $psalf ) {
		global $profiler;

		$dependency_tree = new DependencyTree();

		$deduction_order_arr = array();
		if ( is_object($udlf) ) {
			//Loop over all User Deductions getting Include/Exclude and PS accounts.
			if ( $udlf->getRecordCount() > 0 ) {
				foreach ( $udlf as $ud_obj ) {
					Debug::text('User Deduction: ID: '. $ud_obj->getId(), __FILE__, __LINE__, __METHOD__,10);
					if ( $ud_obj->getCompanyDeductionObject()->getStatus() == 10 ) {
						//$deduction_order_arr = $this->calculateDeductionOrder( $deduction_order_arr, $ud_obj );
						$global_id = substr(get_class( $ud_obj ),0,1) . $ud_obj->getId();
						$deduction_order_arr[$global_id] = $this->getDeductionObjectArrayForSorting( $ud_obj );

						$dependency_tree->addNode( $global_id, $deduction_order_arr[$global_id]['require_accounts'], $deduction_order_arr[$global_id]['affect_accounts'], $deduction_order_arr[$global_id]['order']);
					} else {
						Debug::text('Company Deduction is DISABLED!', __FILE__, __LINE__, __METHOD__,10);
					}
				}
			}
		}
		unset($udlf, $ud_obj);

		if ( is_object( $psalf) ) {
			if ( $psalf->getRecordCount() > 0 ) {
				foreach ( $psalf as $psa_obj ) {
					Debug::text('PS Amendment ID: '. $psa_obj->getId(), __FILE__, __LINE__, __METHOD__,10);
					//$deduction_order_arr = $this->calculateDeductionOrder( $deduction_order_arr, $ud_obj );
					$global_id = substr(get_class( $psa_obj ),0,1) . $psa_obj->getId();
					$deduction_order_arr[$global_id] = $this->getDeductionObjectArrayForSorting( $psa_obj );

					$dependency_tree->addNode( $global_id, $deduction_order_arr[$global_id]['require_accounts'], $deduction_order_arr[$global_id]['affect_accounts'], $deduction_order_arr[$global_id]['order']);
				}
			}
		}
		unset($psalf, $psa_obj);

		$profiler->startTimer( "Calculate Dependency Tree");
		Debug::text('Calculate Dependency Tree: Start: '. TTDate::getDate('DATE+TIME', time() ), __FILE__, __LINE__, __METHOD__,10);

		$sorted_deduction_ids = $dependency_tree->getAllNodesInOrder();

		Debug::text('Calculate Dependency Tree: End: '. TTDate::getDate('DATE+TIME', time() ), __FILE__, __LINE__, __METHOD__,10);
		$profiler->stopTimer( "Calculate Dependency Tree");

		if ( is_array($sorted_deduction_ids) ) {
			foreach( $sorted_deduction_ids as $tmp => $deduction_id ) {
				$retarr[$deduction_id] = $deduction_order_arr[$deduction_id];
			}
		}

		//Debug::Arr($retarr, 'AFTER - Deduction Order Array: ', __FILE__, __LINE__, __METHOD__,10);

		if ( isset($retarr) ) {
			return $retarr;
		}

		return FALSE;
	}

	function calculate($epoch = NULL) {

		if ( $this->getUserObject() == FALSE OR $this->getUserObject()->getStatus() !== 10 ) {
			return FALSE;
		}

		$generic_queue_status_label = $this->getUserObject()->getFullName(TRUE).' - '. TTi18n::gettext('Pay Stub');

		if ( $epoch == NULL OR $epoch == '' ) {
			$epoch = TTDate::getTime();
		}

		if (  $this->getPayPeriodObject() == FALSE ) {
			return FALSE;
		}

		Debug::text('bbUser Id: '. $this->getUser() .' Pay Period End Date: '. TTDate::getDate('DATE+TIME', $this->getPayPeriodObject()->getEndDate() ), __FILE__, __LINE__, __METHOD__,10);

		$pay_stub = new PayStubFactory();
		$pay_stub->StartTransaction();

		$old_pay_stub_id = NULL;
		if ( $this->getEnableCorrection() == TRUE ) {
			Debug::text('Correction Enabled!', __FILE__, __LINE__, __METHOD__,10);
			$pay_stub->setTemp(TRUE);

			//Check for current pay stub ID so we can compare against it.
			$pslf = new PayStubListFactory();
			$pslf->getByUserIdAndPayPeriodId( $this->getUser(), $this->getPayPeriod() );
			if ( $pslf->getRecordCount() > 0 ) {
				$old_pay_stub_id = $pslf->getCurrent()->getId();
				Debug::text('Comparing Against Pay Stub ID: '. $old_pay_stub_id, __FILE__, __LINE__, __METHOD__,10);
			}
		}
		$pay_stub->setUser( $this->getUser() );
		$pay_stub->setPayPeriod( $this->getPayPeriod() );
		$pay_stub->setCurrency( $this->getUserObject()->getCurrency() );
		$pay_stub->setStatus('NEW');

		//Use User Termination Date instead of ROE.
		if ( $this->getUserObject()->getTerminationDate() != ''
				AND $this->getUserObject()->getTerminationDate() >= $this->getPayPeriodObject()->getStartDate()
				AND $this->getUserObject()->getTerminationDate() <= $this->getPayPeriodObject()->getEndDate() ) {
			Debug::text('User has been terminated in this pay period!', __FILE__, __LINE__, __METHOD__,10);

			$is_terminated = TRUE;
		} else {
			$is_terminated = FALSE;
		}

		if ( $is_terminated == TRUE ) {
			Debug::text('User is Terminated, assuming final pay, setting End Date to terminated date: '. TTDate::getDate('DATE+TIME', $this->getUserObject()->getTerminationDate() ), __FILE__, __LINE__, __METHOD__,10);

			$pay_stub->setStartDate( $pay_stub->getPayPeriodObject()->getStartDate() );
			$pay_stub->setEndDate( $this->getUserObject()->getTerminationDate() );

			//Use the PS generation date instead of terminated date...
			//Unlikely they would pay someone before the pay stub is generated.
			//Perhaps still use the pay period transaction date for this too?
			//Anything we set won't be correct for everyone. Maybe a later date is better though?
			//Perhaps add to the user factory under Termination Date a: "Final Transaction Date" for this purpose?
			//Use the end of the current date for the transaction date, as if the employee is terminated
			//on the same day they are generating the pay stub, the transaction date could be before the end date
			//as the end date is at 11:59PM
			$pay_stub->setTransactionDate( TTDate::getEndDayEpoch( TTDate::getTime() ) );
		} else {
			Debug::text('User Termination Date is NOT set, assuming normal pay.', __FILE__, __LINE__, __METHOD__,10);
			$pay_stub->setDefaultDates();
		}

		//This must go after setting advance
		if ( $this->getEnableCorrection() == FALSE AND $pay_stub->IsUniquePayStub() == FALSE ) {
			Debug::text('Pay Stub already exists', __FILE__, __LINE__, __METHOD__,10);
			$this->CommitTransaction();

			UserGenericStatusFactory::queueGenericStatus( $generic_queue_status_label, 20, TTi18n::gettext('Pay Stub for this employee already exists, skipping...'), NULL );

			return FALSE;
		}

		if ( $pay_stub->isValid() == TRUE ) {
			$pay_stub->Save(FALSE);
			$pay_stub->setStatus('Open');
		} else {
			Debug::text('Pay Stub isValid failed!', __FILE__, __LINE__, __METHOD__,10);

			UserGenericStatusFactory::queueGenericStatus( $generic_queue_status_label, 10, $pay_stub->Validator->getTextErrors(), NULL );

			$this->FailTransaction();
			$this->CommitTransaction();
			return FALSE;
		}

		$pay_stub->loadPreviousPayStub();

		$user_date_total_arr = $this->getWageObject()->getUserDateTotalArray();

		if ( isset($user_date_total_arr['entries']) AND is_array( $user_date_total_arr['entries'] ) ) {
			foreach( $user_date_total_arr['entries'] as $udt_arr ) {
				//Allow negative amounts so flat rate premium policies can reduce an employees wage if need be.
				if ( $udt_arr['amount'] != 0 ) {
					Debug::text('Adding Pay Stub Entry: '. $udt_arr['pay_stub_entry'] .' Amount: '. $udt_arr['amount'], __FILE__, __LINE__, __METHOD__,10);
					$pay_stub->addEntry( $udt_arr['pay_stub_entry'], $udt_arr['amount'], TTDate::getHours( $udt_arr['total_time'] ), $udt_arr['rate'] );
				} else {
					Debug::text('NOT Adding ($0 amount) Pay Stub Entry: '. $udt_arr['pay_stub_entry'] .' Amount: '. $udt_arr['amount'], __FILE__, __LINE__, __METHOD__,10);
				}
			}
		} else {
			//No Earnings, CHECK FOR PS AMENDMENTS next for earnings.
			Debug::text('NO TimeSheet EARNINGS ON PAY STUB... Checking for PS amendments', __FILE__, __LINE__, __METHOD__,10);
		}

		//Get all PS amendments and Tax / Deductions so we can determine the proper order to calculate them in.
		$psalf = new PayStubAmendmentListFactory();
		$psalf->getByUserIdAndAuthorizedAndStartDateAndEndDate( $this->getUser(), TRUE, $this->getPayPeriodObject()->getStartDate(), $this->getPayPeriodObject()->getEndDate() );

		$udlf = new UserDeductionListFactory();
		$udlf->getByCompanyIdAndUserId( $this->getUserObject()->getCompany(), $this->getUserObject()->getId() );

		$deduction_order_arr = $this->getOrderedDeductionAndPSAmendment( $udlf, $psalf );

		if ( is_array($deduction_order_arr) AND count($deduction_order_arr) > 0 ) {
			foreach($deduction_order_arr as $calculation_order => $data_arr ) {

				Debug::text('Found PS Amendment/Deduction: Type: '. $data_arr['type'] .' Name: '. $data_arr['name'] .' Order: '. $calculation_order, __FILE__, __LINE__, __METHOD__,10);
				if ( isset($data_arr['obj']) AND is_object($data_arr['obj']) ) {
					if ( $data_arr['type'] == 'UserDeductionListFactory' ) {
						$ud_obj = $data_arr['obj'];

						//Determine if this deduction is valid based on start/end dates.
						//Determine if this deduction is valid based on min/max length of service.
						if ( $ud_obj->getCompanyDeductionObject()->isActiveDate( $pay_stub->getPayPeriodObject()->getEndDate() ) == TRUE
								AND $ud_obj->getCompanyDeductionObject()->isActiveLengthOfService( $this->getUserObject(), $pay_stub->getPayPeriodObject()->getEndDate() ) == TRUE ) {

								$amount = $ud_obj->getDeductionAmount( $this->getUserObject()->getId(), $pay_stub, $this->getPayPeriodObject() );
								Debug::text('User Deduction: '. $ud_obj->getCompanyDeductionObject()->getName() .' Amount: '. $amount .' Calculation Order: '. $ud_obj->getCompanyDeductionObject()->getCalculationOrder(), __FILE__, __LINE__, __METHOD__,10);

								//Allow negative amounts, so they can reduce previously calculated deductions or something.
								if ( isset($amount) AND $amount != 0 ) {
									$pay_stub->addEntry( $ud_obj->getCompanyDeductionObject()->getPayStubEntryAccount(), $amount );
								} else {
									Debug::text('Amount is 0, skipping...', __FILE__, __LINE__, __METHOD__,10);
								}
						}
						unset($amount, $ud_obj);
					} elseif ( $data_arr['type'] == 'PayStubAmendmentListFactory' ) {
						$psa_obj = $data_arr['obj'];

						Debug::text('Found Pay Stub Amendment: ID: '. $psa_obj->getID() .' Entry Name ID: '. $psa_obj->getPayStubEntryNameId() .' Type: '. $psa_obj->getType() , __FILE__, __LINE__, __METHOD__,10);

						$amount = $psa_obj->getCalculatedAmount( $pay_stub );

						if ( isset($amount) AND $amount != 0 ) {
							Debug::text('Pay Stub Amendment Amount: '. $amount , __FILE__, __LINE__, __METHOD__,10);

							$pay_stub->addEntry( $psa_obj->getPayStubEntryNameId() , $amount, $psa_obj->getUnits(), $psa_obj->getRate(), $psa_obj->getDescription(), $psa_obj->getID(), NULL, NULL, $psa_obj->getYTDAdjustment() );

							//Keep in mind this causes pay stubs to be re-generated every time, as this modifies the updated time
							//to slightly more then the pay stub creation time.
							$psa_obj->setStatus('IN USE');
							$psa_obj->Save();

						} else {
							Debug::text('bPay Stub Amendment Amount is not set...', __FILE__, __LINE__, __METHOD__,10);
						}
						unset($amount, $psa_obj);

					}
				}

			}

		}
		unset($deduction_order_arr, $calculation_order, $data_arr);

		$pay_stub_id = $pay_stub->getId();

		$pay_stub->setEnableProcessEntries(TRUE);
		$pay_stub->processEntries();
		if ( $pay_stub->isValid() == TRUE ) {
			Debug::text('Pay Stub is valid, final save.', __FILE__, __LINE__, __METHOD__,10);
			$pay_stub->Save();

			if ( $this->getEnableCorrection() == TRUE ) {
				if ( isset($old_pay_stub_id) ) {
					Debug::text('bCorrection Enabled - Doing Comparison here', __FILE__, __LINE__, __METHOD__,10);
					PayStubFactory::CalcDifferences( $pay_stub_id, $old_pay_stub_id );
				}

				//Delete newly created temp paystub.
				//This used to be in the above IF block that depended on $old_pay_stub_id
				//being set, however in cases where the old pay stub didn't exist
				//TimeTrex wouldn't delete these temporary pay stubs.
				//Moving this code outside that IF statement so it only depends on EnableCorrection()
				//to be TRUE should fix that issue.
				$pslf = new PayStubListFactory();
				$pslf->getById( $pay_stub_id );
				if ( $pslf->getRecordCount() > 0 ) {
					$tmp_ps_obj = $pslf->getCurrent();
					$tmp_ps_obj->setDeleted(TRUE);
					$tmp_ps_obj->Save();
					unset($tmp_ps_obj);
				}
			}

			$pay_stub->CommitTransaction();

			UserGenericStatusFactory::queueGenericStatus( $generic_queue_status_label, 30, NULL, NULL );

			return TRUE;
		}

		Debug::text('Pay Stub is NOT valid returning FALSE', __FILE__, __LINE__, __METHOD__,10);

		UserGenericStatusFactory::queueGenericStatus( $generic_queue_status_label, 10, $pay_stub->Validator->getTextErrors(), NULL );

		$pay_stub->FailTransaction(); //Reduce transaction count by one.
		//$pay_stub->FailTransaction(); //Reduce transaction count by one.

		$pay_stub->CommitTransaction();

		return FALSE;
	}
}
?>
