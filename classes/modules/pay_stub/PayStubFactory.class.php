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
 * $Id: PayStubFactory.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */
require_once( 'Numbers/Words.php');


/**
 * @package Module_Pay_Stub
 */
class PayStubFactory extends Factory {
	protected $table = 'pay_stub';
	protected $pk_sequence_name = 'pay_stub_id_seq'; //PK Sequence name

	protected $tmp_data = array('previous_pay_stub' => NULL, 'current_pay_stub' => NULL );
	protected $is_unique_pay_stub = NULL;

	protected $pay_period_obj = NULL;
	protected $currency_obj = NULL;
	protected $user_obj = NULL;
	protected $pay_stub_entry_account_link_obj = NULL;

	protected $pay_stub_entry_accounts_obj = NULL;


	function _getFactoryOptions( $name, $country = NULL ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('NEW'),
										20 => TTi18n::gettext('LOCKED'),
										25 => TTi18n::gettext('Open'),
										30 => TTi18n::gettext('Pending Transaction'),
										40 => TTi18n::gettext('Paid')
									);
				break;
			case 'export_type':
				$retval = array(
										'00' => TTi18n::gettext('-- Direct Deposit --'),
										'ACH' => TTi18n::gettext('United States - ACH (94-Byte)'),
										'1464' => TTi18n::gettext('Canada - EFT (CPA 005/1464-Byte)'),
										'105' => TTi18n::gettext('Canada - EFT (105-Byte)'),
										'HSBC' => TTi18n::gettext('Canada - HSBC EFT-PC (CSV)'),

										//Cheque formats must start with "cheque_"
										'01' => '',
										'02' => TTi18n::gettext('-- Laser Cheques --'),
										'cheque_9085' =>   TTi18n::gettext('NEBS #9085'),
										'cheque_9209p' =>  TTi18n::gettext('NEBS #9209P'),
										'cheque_dlt103' => TTi18n::gettext('NEBS #DLT103'),
										'cheque_dlt104' => TTi18n::gettext('NEBS #DLT104'),
										'cheque_cr_standard_form_1' => TTi18n::gettext('Costa Rica - Std Form 1'),
										'cheque_cr_standard_form_2' => TTi18n::gettext('Costa Rica - Std Form 2'),
									);
				break;

		}

		return $retval;
	}


	function getPayPeriodObject() {
		if ( is_object($this->pay_period_obj) ) {
			return $this->pay_period_obj;
		} else {
			$pplf = new PayPeriodListFactory();

			$this->pay_period_obj = $pplf->getById( $this->getPayPeriod() )->getCurrent();

			return $this->pay_period_obj;
		}

		return FALSE;
	}

	function getCurrencyObject() {
		if ( is_object($this->currency_obj) ) {
			return $this->currency_obj;
		} else {
			$clf = new CurrencyListFactory();

			$clf->getById( $this->getCurrency() );
			if ( $clf->getRecordCount() > 0 ) {
				$this->currency_obj = $clf->getCurrent();
				return $this->currency_obj;
			}
		}

		return FALSE;
	}

	function getUserObject() {
		if ( is_object($this->user_obj) ) {
			return $this->user_obj;
		} else {
			$ulf = new UserListFactory();
			$ulf->getById( $this->getUser() );
			if ( $ulf->getRecordCount() > 0 ) {
				$this->user_obj = $ulf->getCurrent();
				return $this->user_obj;
			}
		}

		return FALSE;
	}

	function getUser() {
		if ( isset($this->data['user_id']) ) {
			return $this->data['user_id'];
		}

		return FALSE;
	}
	function setUser($id) {
		$id = trim($id);

		$ulf = new UserListFactory();

		if ( $this->Validator->isResultSetWithRows(	'user',
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

	function getCurrency() {
		if ( isset($this->data['currency_id']) ) {
			return $this->data['currency_id'];
		}

		return FALSE;
	}
	function setCurrency($id) {
		$id = trim($id);

		Debug::Text('Currency ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$culf = new CurrencyListFactory();

		$old_currency_id = $this->getCurrency();

		if (
				$this->Validator->isResultSetWithRows(	'currency',
														$culf->getByID($id),
														TTi18n::gettext('Invalid Currency')
													) ) {

			$this->data['currency_id'] = $id;

			if ( $culf->getRecordCount() == 1
					AND ( $this->isNew() OR $old_currency_id != $id ) ) {
				$this->setCurrencyRate( $culf->getCurrent()->getReverseConversionRate() );
			}

			return TRUE;
		}

		return FALSE;
	}

	function getCurrencyRate() {
		if ( isset($this->data['currency_rate']) ) {
			return $this->data['currency_rate'];
		}

		return FALSE;
	}
	function setCurrencyRate( $value ) {
		$value = trim($value);

		//Pull out only digits and periods.
		$value = $this->Validator->stripNonFloat($value);

		if (	$this->Validator->isFloat(	'currency_rate',
											$value,
											TTi18n::gettext('Incorrect Currency Rate')) ) {

			$this->data['currency_rate'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function isValidStartDate($epoch) {
		if ( $epoch >= $this->getPayPeriodObject()->getStartDate()
				AND $epoch < $this->getPayPeriodObject()->getEndDate() ) {
			return TRUE;
		}

		return FALSE;
	}

	function getStartDate( $raw = FALSE ) {
		if ( isset($this->data['start_date']) ) {
			if ( $raw === TRUE ) {
				return $this->data['start_date'];
			} else {
				//return $this->db->UnixTimeStamp( $this->data['start_date'] );
				//strtotime is MUCH faster than UnixTimeStamp
				//Must use ADODB for times pre-1970 though.
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
				AND
				$this->Validator->isTrue(		'start_date',
												$this->isValidStartDate($epoch),
												TTi18n::gettext('Conflicting start date'))

			) {

			//$this->data['start_date'] = $epoch;
			$this->data['start_date'] = TTDate::getDBTimeStamp($epoch, FALSE);

			return TRUE;
		}

		return FALSE;
	}

	function isValidEndDate($epoch) {
		if ( $epoch <= $this->getPayPeriodObject()->getEndDate()
				AND $epoch >= $this->getPayPeriodObject()->getStartDate() ) {
			return TRUE;
		}

		return FALSE;
	}

	function getEndDate( $raw = FALSE ) {
		if ( isset($this->data['end_date']) ) {
			if ( $raw === TRUE ) {
				return $this->data['end_date'];
			} else {
				//In cases where you set the date, then immediately read it again, it will return -1 unless do this.
				return TTDate::strtotime( $this->data['end_date'] );
			}
		}

		return FALSE;
	}
	function setEndDate($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'end_date',
												$epoch,
												TTi18n::gettext('Incorrect end date'))
				AND
				$this->Validator->isTrue(		'end_date',
												$this->isValidEndDate($epoch),
												TTi18n::gettext('Conflicting end date'))

			) {

			//$this->data['end_date'] = $epoch;
			$this->data['end_date'] = TTDate::getDBTimeStamp($epoch, FALSE);

			return TRUE;
		}

		return FALSE;
	}

	function isValidTransactionDate($epoch) {
		Debug::Text('Epoch: '. $epoch .' ( '. TTDate::getDate('DATE+TIME', $epoch) .' ) Pay Stub End Date: '. TTDate::getDate('DATE+TIME', $this->getEndDate() ) , __FILE__, __LINE__, __METHOD__,10);
		if ( $epoch >= $this->getEndDate() ) {
			return TRUE;
		}

		return FALSE;
	}

	function getTransactionDate( $raw = FALSE ) {
		//Debug::Text('Transaction Date: '. $this->data['transaction_date'] .' - '. TTDate::getDate('DATE+TIME', $this->data['transaction_date']) , __FILE__, __LINE__, __METHOD__,10);
		if ( isset($this->data['transaction_date']) ) {
			if ( $raw === TRUE ) {
				return $this->data['transaction_date'];
			} else {
				return TTDate::strtotime( $this->data['transaction_date'] );
			}
		}

		return FALSE;
	}
	function setTransactionDate($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'transaction_date',
												$epoch,
												TTi18n::gettext('Incorrect transaction date'))
			) {

			$this->data['transaction_date'] = TTDate::getDBTimeStamp($epoch, FALSE);

			return TRUE;
		}

		return FALSE;
	}

	function getStatus() {
		return $this->data['status_id'];
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

			$this->setStatusDate();
			$this->setStatusBy();

			$this->data['status_id'] = $status;

			return FALSE;
		}

		return FALSE;
	}

	function getStatusDate() {
		return $this->data['status_date'];
	}
	function setStatusDate($epoch = NULL) {
		$epoch = trim($epoch);

		if ($epoch == NULL) {
			$epoch = TTDate::getTime();
		}

		if 	(	$this->Validator->isDate(		'status_date',
												$epoch,
												TTi18n::gettext('Incorrect Date')) ) {

			$this->data['status_date'] = $epoch;

			return TRUE;
		}

		return FALSE;

	}

	function getStatusBy() {
		return $this->data['status_by'];
	}
	function setStatusBy($id = NULL) {
		$id = trim($id);

		if ( empty($id) ) {
			global $current_user;

			if ( is_object($current_user) ) {
				$id = $current_user->getID();
			} else {
				return FALSE;
			}
		}

		$ulf = new UserListFactory();

		if ( $this->Validator->isResultSetWithRows(	'created_by',
													$ulf->getByID($id),
													TTi18n::gettext('Incorrect User')
													) ) {
			$this->data['status_by'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getTainted() {
		if ( isset($this->data['tainted']) ) {
			return $this->fromBool( $this->data['tainted'] );
		}

		return FALSE;
	}
	function setTainted($bool) {
		$this->data['tainted'] = $this->toBool($bool);

		return true;
	}

	function getTemp() {
		if ( isset($this->data['temp']) ) {
			return $this->fromBool( $this->data['temp'] );
		}

		return FALSE;
	}
	function setTemp($bool) {
		$this->data['temp'] = $this->toBool($bool);

		return TRUE;
	}

	function isUniquePayStub() {
		if ( $this->getTemp() == TRUE ) {
			return TRUE;
		}

		if ( $this->is_unique_pay_stub === NULL ) {
			$ph = array(
						'pay_period_id' => $this->getPayPeriod(),
						'user_id' => $this->getUser(),
						);

			$query = 'select id from '. $this->getTable() .' where pay_period_id = ? AND user_id = ? AND deleted = 0';
			$pay_stub_id = $this->db->GetOne($query, $ph);

			if ( $pay_stub_id === FALSE ) {
				$this->is_unique_pay_stub = TRUE;
			} else {
				if ($pay_stub_id == $this->getId() ) {
					$this->is_unique_pay_stub = TRUE;
				} else {
					$this->is_unique_pay_stub = FALSE;
				}
			}
		}

		return $this->is_unique_pay_stub;
	}

	function setDefaultDates() {
		Debug::text(' NOT Advance!!: ', __FILE__, __LINE__, __METHOD__,10);
		$start_date = $this->getPayPeriodObject()->getStartDate();
		$end_date = $this->getPayPeriodObject()->getEndDate();
		$transaction_date = $this->getPayPeriodObject()->getTransactionDate();

		Debug::Text('Start Date: '. TTDate::getDate('DATE+TIME', $start_date), __FILE__, __LINE__, __METHOD__,10);
		Debug::Text('End Date: '. TTDate::getDate('DATE+TIME', $end_date), __FILE__, __LINE__, __METHOD__,10);
		Debug::Text('Transaction Date: '. TTDate::getDate('DATE+TIME', $transaction_date), __FILE__, __LINE__, __METHOD__,10);

		$this->setStartDate( $start_date);
		$this->setEndDate( $end_date );
		$this->setTransactionDate( $transaction_date );

		Debug::Text('bTransaction Date: '. TTDate::getDate('DATE+TIME', $this->getTransactionDate() ), __FILE__, __LINE__, __METHOD__,10);
		return TRUE;
	}

	function getEnableProcessEntries() {
		if ( isset($this->process_entries) ) {
			return $this->process_entries;
		}

		return FALSE;
	}
	function setEnableProcessEntries($bool) {
		$this->process_entries = (bool)$bool;

		return TRUE;
	}

	function getEnableCalcYTD() {
		if ( isset($this->calc_ytd) ) {
			return $this->calc_ytd;
		}

		return FALSE;
	}
	function setEnableCalcYTD($bool) {
		$this->calc_ytd = (bool)$bool;

		return TRUE;
	}

	function getEnableLinkedAccruals() {
		if ( isset($this->linked_accruals) ) {
			return $this->linked_accruals;
		}

		return TRUE;
	}
	function setEnableLinkedAccruals($bool) {
		$this->linked_accruals = (bool)$bool;

		return TRUE;
	}

	static function CalcDifferences( $pay_stub_id1, $pay_stub_id2, $ps_amendment_date = NULL ) {
		//PayStub 1 is new.
		//PayStub 2 is old.
		if ( $pay_stub_id1 == '' ) {
			return FALSE;
		}

		if ( $pay_stub_id2 == '' ) {
			return FALSE;
		}

		if ( $pay_stub_id1 == $pay_stub_id2 ) {
			return FALSE;
		}

		Debug::Text('Calculating the differences between Pay Stub: '. $pay_stub_id1 .' And: '. $pay_stub_id2, __FILE__, __LINE__, __METHOD__,10);

		$pslf = new PayStubListFactory();

		$pslf->StartTransaction();

		$pslf->getById( $pay_stub_id1 );
		if ( $pslf->getRecordCount() > 0 ) {
			$pay_stub1_obj = $pslf->getCurrent();
		} else {
			Debug::Text('Pay Stub1 does not exist: ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		$pslf->getById( $pay_stub_id2 );
		if ( $pslf->getRecordCount() > 0 ) {
			$pay_stub2_obj = $pslf->getCurrent();
		} else {
			Debug::Text('Pay Stub2 does not exist: ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ($pay_stub1_obj->getUser() != $pay_stub2_obj->getUser() ) {
			Debug::Text('Pay Stubs are from different users!', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( $ps_amendment_date == NULL OR $ps_amendment_date == '' ) {
			Debug::Text('PS Amendment Date not set, trying to figure it out!', __FILE__, __LINE__, __METHOD__,10);
			//Take a guess at the end of the newest open pay period.
			$ppslf = new PayPeriodScheduleListFactory();
			$ppslf->getByUserId( $pay_stub2_obj->getUser() );
			if ( $ppslf->getRecordCount() > 0 ) {
				Debug::Text('Found Pay Period Schedule', __FILE__, __LINE__, __METHOD__,10);
				$pplf = new PayPeriodListFactory();
				$pplf->getByPayPeriodScheduleIdAndTransactionDate( $ppslf->getCurrent()->getId(), time() );
				if ( $pplf->getRecordCount() > 0 ) {
					Debug::Text('Using Pay Period End Date.', __FILE__, __LINE__, __METHOD__,10);
					$ps_amendment_date = TTDate::getBeginDayEpoch( $pplf->getCurrent()->getEndDate() );

				}
			} else {
				Debug::Text('Using Today.', __FILE__, __LINE__, __METHOD__,10);
				$ps_amendment_date = time();
			}
		}
		Debug::Text('Using Date: '. TTDate::getDate('DATE+TIME', $ps_amendment_date), __FILE__, __LINE__, __METHOD__,10);

		//Only do Earnings for now.
		//Get all earnings, EE/ER deduction PS entries.
		$pay_stub1_entry_ids = NULL;
		$pay_stub1_entries = new PayStubEntryListFactory();
		$pay_stub1_entries->getByPayStubIdAndType( $pay_stub1_obj->getId(), array(10,20,30) );
		if ( $pay_stub1_entries->getRecordCount() > 0 ) {
			Debug::Text('Pay Stub1 Entries DO exist: ', __FILE__, __LINE__, __METHOD__,10);

			foreach( $pay_stub1_entries as $pay_stub1_entry_obj ) {
				$pay_stub1_entry_ids[] = $pay_stub1_entry_obj->getPayStubEntryNameId();
			}
		} else {
			Debug::Text('Pay Stub1 Entries does not exist: ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}
		Debug::Arr( $pay_stub1_entry_ids, 'Pay Stub1 Entry IDs: ', __FILE__, __LINE__, __METHOD__,10);

		//var_dump($pay_stub1_entry_ids);

		$pay_stub2_entry_ids = NULL;
		$pay_stub2_entries = new PayStubEntryListFactory();
		$pay_stub2_entries->getByPayStubIdAndType( $pay_stub2_obj->getId(), array(10,20,30) );
		if ( $pay_stub2_entries->getRecordCount() > 0 ) {
			Debug::Text('Pay Stub2 Entries DO exist: ', __FILE__, __LINE__, __METHOD__,10);
			foreach( $pay_stub2_entries as $pay_stub2_entry_obj ) {
				$pay_stub2_entry_ids[] = $pay_stub2_entry_obj->getPayStubEntryNameId();
			}
		} else {
			Debug::Text('Pay Stub2 Entries does not exist: ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}
		Debug::Arr( $pay_stub1_entry_ids, 'Pay Stub2 Entry IDs: ', __FILE__, __LINE__, __METHOD__,10);


		$pay_stub_entry_ids = array_unique( array_merge($pay_stub1_entry_ids, $pay_stub2_entry_ids) );
		Debug::Arr( $pay_stub_entry_ids, 'Pay Stub Entry Differences: ', __FILE__, __LINE__, __METHOD__,10);
		//var_dump($pay_stub_entry_ids);

		$pself = new PayStubEntryListFactory();
		if ( count($pay_stub_entry_ids) > 0 ) {
			foreach( $pay_stub_entry_ids as $pay_stub_entry_id) {
				Debug::Text('Entry ID: '. $pay_stub_entry_id, __FILE__, __LINE__, __METHOD__,10);
				$pay_stub1_entry_arr = $pself->getSumByPayStubIdAndEntryNameIdAndNotPSAmendment( $pay_stub1_obj->getId(), $pay_stub_entry_id);

				$pay_stub2_entry_arr = $pself->getSumByPayStubIdAndEntryNameIdAndNotPSAmendment( $pay_stub2_obj->getId(), $pay_stub_entry_id);
				Debug::Text('Pay Stub1 Amount: '. $pay_stub1_entry_arr['amount'] .' Pay Stub2 Amount: '. $pay_stub2_entry_arr['amount'], __FILE__, __LINE__, __METHOD__,10);

				if ( $pay_stub1_entry_arr['amount'] != $pay_stub2_entry_arr['amount'] ) {
					$amount_diff = bcsub($pay_stub1_entry_arr['amount'], $pay_stub2_entry_arr['amount'], 2);
					$units_diff = abs( bcsub($pay_stub1_entry_arr['units'], $pay_stub2_entry_arr['units'], 2) );
					Debug::Text('FOUND DIFFERENCE of: Amount: '. $amount_diff .' Units: '. $units_diff, __FILE__, __LINE__, __METHOD__,10);

					//Generate PS Amendment.
					$psaf = new PayStubAmendmentFactory();
					$psaf->setUser( $pay_stub1_obj->getUser() );
					$psaf->setStatus( 'ACTIVE' );
					$psaf->setType( 10 );
					$psaf->setPayStubEntryNameId( $pay_stub_entry_id );

					if ( $units_diff > 0 ) {
						//Re-calculate amount when units are involved, due to rounding issues.
						$unit_rate = Misc::MoneyFormat( bcdiv($amount_diff, $units_diff) );
						$amount_diff = Misc::MoneyFormat( bcmul( $unit_rate, $units_diff ) );
						Debug::Text('bFOUND DIFFERENCE of: Amount: '. $amount_diff .' Units: '. $units_diff .' Unit Rate: '. $unit_rate , __FILE__, __LINE__, __METHOD__,10);

						$psaf->setRate( $unit_rate );
						$psaf->setUnits( $units_diff );
						$psaf->setAmount( $amount_diff );
					} else {
						$psaf->setAmount( $amount_diff );
					}

					$psaf->setDescription( 'Adjustment from Pay Period Ending: '. TTDate::getDate('DATE', $pay_stub2_obj->getEndDate() ) );

					$psaf->setEffectiveDate( TTDate::getBeginDayEpoch( $ps_amendment_date ) );

					if ( $psaf->isValid() ) {
						$psaf->Save();
					}

					unset($amount_diff, $units_diff, $unit_rate);
				} else {
					Debug::Text('No DIFFERENCE!', __FILE__, __LINE__, __METHOD__,10);
				}
			}
		}

		$pslf->CommitTransaction();

		return TRUE;
	}

	function reCalculatePayStubYTD( $pay_stub_id ) {
		//Make sure the entire pay stub object is loaded before calling this.
		if ( $pay_stub_id != '' ) {
			Debug::text('Attempting to recalculate pay stub YTD for pay stub id:'. $pay_stub_id, __FILE__, __LINE__, __METHOD__,10);
			$pslf = new PayStubListFactory();
			$pslf->getById( $pay_stub_id );

			if ( $pslf->getRecordCount() == 1 ) {
				$pay_stub = $pslf->getCurrent();

				$pay_stub->loadPreviousPayStub();

				if ( $pay_stub->loadCurrentPayStubEntries() == TRUE ) {

					$pay_stub->setEnableProcessEntries(TRUE);
					$pay_stub->processEntries();

					if ( $pay_stub->isValid() == TRUE ) {
						Debug::text('Pay Stub is valid, final save.', __FILE__, __LINE__, __METHOD__,10);
						$pay_stub->Save();

						return TRUE;
					}
				} else {
					Debug::text('Failed loading current pay stub entries.', __FILE__, __LINE__, __METHOD__,10);
				}
			}
		}

		return FALSE;
	}

	function reCalculateYTD() {
		//Get all pay stubs NEWER then this one.
		$pslf = new PayStubListFactory();
		$pslf->getByUserIdAndStartDateAndEndDate( $this->getUser() , $this->getTransactionDate(), TTDate::getEndYearEpoch( $this->getTransactionDate() ) );
		$total_pay_stubs = $pslf->getRecordCount();
		if ( $total_pay_stubs > 0 ) {
			$pslf->StartTransaction();

			foreach($pslf as $ps_obj ) {
				$this->reCalculatePayStubYTD( $ps_obj->getId() );
			}

			$pslf->CommitTransaction();
		} else {
			Debug::Text('No Newer Pay Stubs found!', __FILE__, __LINE__, __METHOD__,10);
		}

		return TRUE;
	}


	function preSave() {
		/*
		if ( $this->getEnableProcessEntries() == TRUE ) {
			Debug::Text('Processing PayStub Entries...', __FILE__, __LINE__, __METHOD__,10);

			$this->processEntries();
			//$this->savePayStubEntries();
		} else {
			Debug::Text('NOT Processing PayStub Entries...', __FILE__, __LINE__, __METHOD__,10);
		}
		*/

		return TRUE;
	}

	function Validate() {
		//Make sure we're not submitted two pay stubs for the same pay period per user.
		//Unless the pay period type of Monthly + Advance
		/*
		$pplf = new PayPeriodListFactory();
		$ppslf = new PayPeriodScheduleListFactory();
		$pay_period_type = $ppslf->getById( $pplf->getById( $this->getPayPeriod() )->getCurrent()->getPayPeriodSchedule() )->getCurrent()->getType();
		Debug::Text('Pay Period Type: '. $pay_period_type, __FILE__, __LINE__, __METHOD__,10);
		*/

		if ( $this->getEnableProcessEntries() == TRUE ) {
			$this->ValidateEntries();
		} else {
			Debug::Text('Validating PayStub...', __FILE__, __LINE__, __METHOD__,10);
			//We could re-check these after processEntries are validated,
			//but that might duplicate the error messages?
			if ( $this->isUniquePayStub() == FALSE ) {
				Debug::Text('Unique Pay Stub...', __FILE__, __LINE__, __METHOD__,10);
				$this->Validator->isTrue(		'user',
												FALSE,
												TTi18n::gettext('Invalid unique User and/or Pay Period') );
			}

			if ( $this->getStartDate() == FALSE ) {
					$this->Validator->isDate(		'start_date',
													$this->getStartDate(),
													TTi18n::gettext('Incorrect start date'));
			}
			if ( $this->getEndDate() == FALSE ) {
					$this->Validator->isDate(		'end_date',
													$this->getEndDate(),
													TTi18n::gettext('Incorrect end date'));
			}
			if ( $this->getTransactionDate() == FALSE ) {
					$this->Validator->isDate(		'transaction_date',
													$this->getTransactionDate(),
													TTi18n::gettext('Incorrect transaction date'));
			}

			if ( $this->isValidTransactionDate( $this->getTransactionDate() ) == FALSE ) {
					$this->Validator->isTrue(		'transaction_date',
													FALSE,
													TTi18n::gettext('Transaction date is before pay period end date'));
			}
		}

		return TRUE;
	}

	function ValidateEntries() {
		Debug::Text('Validating PayStub Entries...', __FILE__, __LINE__, __METHOD__,10);

		//Do Pay Stub Entry checks here
		if ( $this->isNew() == FALSE ) {
			//Make sure the pay stub math adds up.
			Debug::Text('Validate: checkEarnings...', __FILE__, __LINE__, __METHOD__,10);
			$this->Validator->isTrue(		'earnings',
											$this->checkNoEarnings(),
											TTi18n::gettext('No Earnings, employee may not have any hours for this pay period, or their wage may not be set') );

			$this->Validator->isTrue(		'earnings',
											$this->checkEarnings(),
											TTi18n::gettext('Earnings don\'t match gross pay') );


			Debug::Text('Validate: checkDeductions...', __FILE__, __LINE__, __METHOD__,10);
			$this->Validator->isTrue(		'deductions',
											$this->checkDeductions(),
											TTi18n::gettext('Deductions don\'t match total deductions') );

			Debug::Text('Validate: checkNetPay...', __FILE__, __LINE__, __METHOD__,10);
			$this->Validator->isTrue(		'net_pay',
											$this->checkNetPay(),
											TTi18n::gettext('Net Pay doesn\'t match earnings or deductions') );
		}

		return $this->Validator->isValid();
	}

	function postSave() {
		$this->removeCache( $this->getId() );

		if ( $this->getEnableProcessEntries() == TRUE ) {
			$this->savePayStubEntries();
		}

		//This needs to be run even if entries aren't being processed,
		//for things like marking the pay stub paid or not.
		$this->handlePayStubAmendmentStatuses();

		if ( $this->getDeleted() == TRUE ) {
			Debug::Text('Deleting Pay Stub, re-calculating YTD ', __FILE__, __LINE__, __METHOD__,10);
			$this->setEnableCalcYTD( TRUE );
		}

		if ( $this->getEnableCalcYTD() == TRUE ) {
			$this->reCalculateYTD();
		}

		return TRUE;
	}

	function handlePayStubAmendmentStatuses() {
		//Mark all PS amendments as 'PAID' if this status is paid.
		//Mark as NEW if the PS is deleted?
		if ( $this->getStatus() == 40 ) {
			$ps_amendment_status_id = 55; //PAID
		} else {
			$ps_amendment_status_id = 52; //INUSE
		}

		//Loop through each entry in current pay stub, if they have
		//a PS amendment ID assigned to them, change the status.
		if ( is_array( $this->tmp_data['current_pay_stub'] ) ) {
			foreach( $this->tmp_data['current_pay_stub'] as $entry_arr ) {
				if ( isset($entry_arr['pay_stub_amendment_id']) AND $entry_arr['pay_stub_amendment_id'] != '' ) {
					Debug::Text('aFound PS Amendments to change status on...', __FILE__, __LINE__, __METHOD__,10);

					$ps_amendment_ids[] = $entry_arr['pay_stub_amendment_id'];
				}
			}

			unset($entry_arr);
		} elseif ( $this->getStatus() != 10 ) {
			//Instead of loading the current pay stub entries, just run a query instead.
			$pself = new PayStubEntryListFactory();
			$pself->getByPayStubId( $this->getId() );

			foreach($pself as $pay_stub_entry_obj) {
				if ( $pay_stub_entry_obj->getPayStubAmendment() != FALSE ) {
					Debug::Text('bFound PS Amendments to change status on...', __FILE__, __LINE__, __METHOD__,10);

					$ps_amendment_ids[] = $pay_stub_entry_obj->getPayStubAmendment();
				}
			}
		}

		if ( isset($ps_amendment_ids) AND is_array($ps_amendment_ids) ) {
			Debug::Text('cFound PS Amendments to change status on...', __FILE__, __LINE__, __METHOD__,10);

			foreach ( $ps_amendment_ids as $ps_amendment_id ) {
				//Set PS amendment status to match Pay stub.
				$psalf = new PayStubAmendmentListFactory();
				$psalf->getById( $ps_amendment_id );
				if ( $psalf->getRecordCount() == 1 ) {
					Debug::Text('Changing Status of PS Amendment: '. $ps_amendment_id , __FILE__, __LINE__, __METHOD__,10);
					$ps_amendment_obj = $psalf->getCurrent();
					$ps_amendment_obj->setStatus( $ps_amendment_status_id );
					$ps_amendment_obj->Save();
					unset($ps_amendment_obj);
				}
				unset($psalf);
			}
			unset($ps_amendment_ids);
		}

		return TRUE;
	}

	/*


		Functions used in adding PayStub entries.


	*/
	function getPayStubEntryAccountLinkObject() {
		if ( is_object($this->pay_stub_entry_account_link_obj) ) {
			return $this->pay_stub_entry_account_link_obj;
		} else {
			$pseallf = new PayStubEntryAccountLinkListFactory();
			$pseallf->getByCompanyID( $this->getUserObject()->getCompany() );
			if ( $pseallf->getRecordCount() > 0 ) {
				$this->pay_stub_entry_account_link_obj = $pseallf->getCurrent();
				return $this->pay_stub_entry_account_link_obj;
			}

			return FALSE;
		}
	}

	function getPayStubEntryAccountsArray() {
		if ( is_array($this->pay_stub_entry_accounts_obj) ) {
			//Debug::text('Returning Cached data...' , __FILE__, __LINE__, __METHOD__,10);
			return $this->pay_stub_entry_accounts_obj;
		} else {
			$psealf = new PayStubEntryAccountListFactory();
			$psealf->getByCompanyId( $this->getUserObject()->getCompany() );
			if ( $psealf->getRecordCount() > 0 ) {
				foreach(  $psealf as $psea_obj ) {
					$this->pay_stub_entry_accounts_obj[$psea_obj->getId()] = array(
						'type_id' => $psea_obj->getType(),
						'accrual_pay_stub_entry_account_id' => $psea_obj->getAccrual()
						);
				}

				//Debug::Arr($this->pay_stub_entry_accounts_obj, ' Pay Stub Entry Accounts ('.count($this->pay_stub_entry_accounts_obj).'): ' , __FILE__, __LINE__, __METHOD__,10);
				return $this->pay_stub_entry_accounts_obj;
			}

			Debug::text('Returning FALSE...' , __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}
	}

	function getPayStubEntryAccountArray( $id ) {
		if ( $id == '' ) {
			return FALSE;
		}

		//Debug::text('ID: '. $id , __FILE__, __LINE__, __METHOD__,10);
		$psea = $this->getPayStubEntryAccountsArray();

		if ( isset($psea[$id]) ) {
			return $psea[$id];
		}

		Debug::text('Returning FALSE...' , __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}

	function getSumByEntriesArrayAndTypeIDAndPayStubAccountID( $ps_entries, $type_ids = NULL, $ps_account_ids = NULL) {
		Debug::text('PS Entries: '. $ps_entries .' Type ID: '. $type_ids .' PS Account ID: '. $ps_account_ids, __FILE__, __LINE__, __METHOD__,10);

		if ( strtolower($ps_entries) == 'current' ) {
			$entries = $this->tmp_data['current_pay_stub'];
		} elseif ( strtolower($ps_entries) == 'previous' ) {
			$entries = $this->tmp_data['previous_pay_stub']['entries'];
		} elseif ( strtolower($ps_entries) == 'previous+ytd_adjustment' ) {
			$entries = $this->tmp_data['previous_pay_stub']['entries'];
			//Include any YTD adjustment PS amendments in the current entries as if they occurred in the previous pay stub.
			//This so we can account for the first pay stub having a YTD adjustment that exceeds a wage base amount, so no amount is calculated.
			foreach( $this->tmp_data['current_pay_stub'] as $current_entry_arr ) {
				if ( isset($current_entry_arr['ytd_adjustment']) AND $current_entry_arr['ytd_adjustment'] === TRUE ) {
					Debug::Text('Found YTD Adjustment in current pay stub when calculating previous pay stub amounts... Amount: '. $current_entry_arr['amount'] , __FILE__, __LINE__, __METHOD__,10);
					//Debug::Arr($current_entry_arr, 'Found YTD Adjustment in current pay stub when calculating previous pay stub amounts...' , __FILE__, __LINE__, __METHOD__,10);
					$entries[] = $current_entry_arr;
				}
			}
			unset($current_entry_arr);
		}
		//Debug::Arr( $entries, 'Sum Entries Array: ', __FILE__, __LINE__, __METHOD__,10);

		if ( !is_array($entries) ) {
			Debug::text('Returning FALSE...' , __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( $type_ids != '' AND !is_array($type_ids) ) {
			$type_ids = array($type_ids);
		}

		if ( $ps_account_ids != '' AND !is_array($ps_account_ids) ) {
			$ps_account_ids = array($ps_account_ids);
		}

		$retarr = array(
				'units' => 0,
				'amount' => 0,
				'ytd_units' => 0,
				'ytd_amount' => 0,
			);

		foreach( $entries as $key => $entry_arr ) {
			if ( $type_ids != '' AND is_array( $type_ids ) ) {
				foreach( $type_ids as $type_id ) {
					if ( isset($entry_arr['pay_stub_entry_type_id']) AND $type_id == $entry_arr['pay_stub_entry_type_id'] AND $entry_arr['pay_stub_entry_type_id'] != 50 ) {
						if ( isset($entry_arr['ytd_adjustment']) AND $entry_arr['ytd_adjustment'] === TRUE ) {
							//If a PS amendment makes a YTD adjustment, we need to treat it as a regular PS amendment
							//affecting the 'amount' instead of the 'ytd_amount', otherwise it will double up YTD amounts.
							//There are two issues at hand, doubling up YTD amounts, and not counting YTD adjustments
							//towards getting YTD amounts on the current pay stub for things like calculating
							//Wage Base/Maximum contributions.
							//Also, we need to make sure that these amounts aren't included in Tax/Deduction calculations
							//for this pay stub. But ARE calculated in this pay stub if they affect accruals.
							$retarr['ytd_amount'] = bcadd( $retarr['ytd_amount'], $entry_arr['amount'] );
							$retarr['ytd_units'] = bcadd( $retarr['ytd_units'], $entry_arr['units'] );
						} else {
							$retarr['amount'] = bcadd( $retarr['amount'], $entry_arr['amount'] );
							$retarr['units'] = bcadd( $retarr['units'], $entry_arr['units'] );
							$retarr['ytd_amount'] = bcadd( $retarr['ytd_amount'], $entry_arr['ytd_amount'] );
							$retarr['ytd_units'] = bcadd( $retarr['ytd_units'], $entry_arr['ytd_units'] );
						}
					} else {
						//Debug::text('Type ID: '. $type_id .' does not match: '. $entry_arr['pay_stub_entry_type_id'] , __FILE__, __LINE__, __METHOD__,10);
					}
				}
			} elseif ( $ps_account_ids != '' AND is_array($ps_account_ids) ) {
				foreach( $ps_account_ids as $ps_account_id ) {
					if ( isset($entry_arr['pay_stub_entry_account_id']) AND $ps_account_id == $entry_arr['pay_stub_entry_account_id']) {
						if ( isset($entry_arr['ytd_adjustment']) AND $entry_arr['ytd_adjustment'] === TRUE AND $entry_arr['pay_stub_entry_type_id'] != 50 ) {
							$retarr['ytd_amount'] = bcadd( $retarr['ytd_amount'], $entry_arr['amount'] );
							$retarr['ytd_units'] = bcadd( $retarr['ytd_units'], $entry_arr['units'] );
						} else {
							$retarr['amount'] = bcadd( $retarr['amount'], $entry_arr['amount'] );
							$retarr['units'] = bcadd( $retarr['units'], $entry_arr['units'] );
							$retarr['ytd_amount'] = bcadd( $retarr['ytd_amount'], $entry_arr['ytd_amount'] );
							$retarr['ytd_units'] = bcadd( $retarr['ytd_units'], $entry_arr['ytd_units'] );
						}
					}
				}
			}
		}

		//Debug::Arr($retarr, 'SumByEntries RetArr: ', __FILE__, __LINE__, __METHOD__,10);
		return $retarr;
	}

	function loadCurrentPayStubEntries() {
		Debug::Text('aLoading current pay stub entries, Pay Stub ID: '. $this->getId(), __FILE__, __LINE__, __METHOD__,10);
		if ( $this->getId() != '' ) {
			//Get pay stub entries
			$pself = new PayStubEntryListFactory();
			$pself->getByPayStubId( $this->getID() );
			Debug::Text('bLoading current pay stub entries, Pay Stub ID: '. $this->getId() .' Record Count: '. $pself->getRecordCount() , __FILE__, __LINE__, __METHOD__,10);

			if ( $pself->getRecordCount() > 0 ) {
				$this->tmp_data['current_pay_stub'] = NULL;

				foreach( $pself as $pse_obj ) {
					//Get PSE account type, group by that.
					$psea_arr = $this->getPayStubEntryAccountArray( $pse_obj->getPayStubEntryNameId() );
					if ( is_array( $psea_arr) ) {
						$type_id = $psea_arr['type_id'];
					} else {
						$type_id = NULL;
					}

					//Skip total entries
					if ( $type_id != 40 ) {
						$pse_arr[] = array(
							'id' => $pse_obj->getId(),
							'pay_stub_entry_type_id' => $type_id,
							'pay_stub_entry_account_id' => $pse_obj->getPayStubEntryNameId(),
							'pay_stub_amendment_id' => $pse_obj->getPayStubAmendment(),
							'rate' => $pse_obj->getRate(),
							'units' => $pse_obj->getUnits(),
							'amount' => $pse_obj->getAmount(),
							//'ytd_units' => $pse_obj->getYTDUnits(),
							//'ytd_amount' => $pse_obj->getYTDAmount(),
							//Don't load YTD values, they need to be recalculated.
							'ytd_units' => NULL,
							'ytd_amount' => NULL,
							'description' => $pse_obj->getDescription(),
							);
					}
					unset($type_id, $psea_obj);
				}

				//Debug::Arr($pse_arr, 'RetArr: ', __FILE__, __LINE__, __METHOD__,10);
				if ( isset( $pse_arr ) ) {
					$retarr['entries'] = $pse_arr;

					$this->tmp_data['current_pay_stub'] = $retarr['entries'];

					Debug::Text('Loading current pay stub entries success!', __FILE__, __LINE__, __METHOD__,10);
					return TRUE;
				}
			}

		}
		Debug::Text('Loading current pay stub entries failed!', __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}

	function loadPreviousPayStub() {
		if ( $this->getUser() == FALSE OR $this->getStartDate() == FALSE ) {
			return FALSE;
		}

		//Grab last pay stub so we can use it for YTD calculations on this pay stub.
		$pslf = new PayStubListFactory();
		$pslf->getLastPayStubByUserIdAndStartDate( $this->getUser(), $this->getStartDate() );
		if ( $pslf->getRecordCount() > 0 ) {
			$ps_obj = $pslf->getCurrent();
			Debug::text('Loading Data from Pay Stub ID: '. $ps_obj->getId() , __FILE__, __LINE__, __METHOD__,10);

			$retarr = array(
							'id' => $ps_obj->getId(),
							'start_date' => $ps_obj->getStartDate(),
							'end_date' => $ps_obj->getEndDate(),
							'transaction_date' => $ps_obj->getTransactionDate(),
							'entries' => NULL,
							);

			//
			//If previous pay stub is in a different year, only carry forward the accrual accounts.
			//
			$new_year = FALSE;
			if ( TTDate::getYear( $this->getTransactionDate() ) != TTDate::getYear( $ps_obj->getTransactionDate() ) ) {
				Debug::text('Pay Stub Years dont match!...' , __FILE__, __LINE__, __METHOD__,10);
				$new_year = TRUE;
			}

			//Get pay stub entries
			$pself = new PayStubEntryListFactory();
			$pself->getByPayStubId( $ps_obj->getID() );
			if ( $pself->getRecordCount() > 0 ) {
				foreach( $pself as $pse_obj ) {
					//Get PSE account type, group by that.
					$psea_arr = $this->getPayStubEntryAccountArray( $pse_obj->getPayStubEntryNameId() );
					if ( is_array( $psea_arr) ) {
						$type_id = $psea_arr['type_id'];
					} else {
						$type_id = NULL;
					}

					//If we're just starting a new year, only carry over
					//accrual balances, reset all YTD entries.
					if ( $new_year == FALSE OR $type_id == 50 ) {
						$pse_arr[] = array(
							'id' => $pse_obj->getId(),
							'pay_stub_entry_type_id' => $type_id,
							'pay_stub_entry_account_id' => $pse_obj->getPayStubEntryNameId(),
							'pay_stub_amendment_id' => $pse_obj->getPayStubAmendment(),
							'rate' => $pse_obj->getRate(),
							'units' => $pse_obj->getUnits(),
							'amount' => $pse_obj->getAmount(),
							'ytd_units' => $pse_obj->getYTDUnits(),
							'ytd_amount' => $pse_obj->getYTDAmount(),
							);
					}
					unset($type_id, $psea_obj);
				}

				if ( isset( $pse_arr ) ) {
					$retarr['entries'] = $pse_arr;

					$this->tmp_data['previous_pay_stub'] = $retarr;

					return TRUE;
				}
			}
		}

		Debug::text('Returning FALSE...' , __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}

	function addEntry( $pay_stub_entry_account_id, $amount, $units = NULL, $rate = NULL, $description = NULL, $ps_amendment_id = NULL, $ytd_amount = NULL, $ytd_units = NULL, $ytd_adjustment = FALSE ) {
		Debug::text('Add Entry: PSE Account ID: '. $pay_stub_entry_account_id .' Amount: '. $amount .' YTD Amount: '. $ytd_amount .' Pay Stub Amendment Id: '. $ps_amendment_id, __FILE__, __LINE__, __METHOD__,10);
		if ( $pay_stub_entry_account_id == '' ) {
			return FALSE;
		}

		//Round amount to 2 decimal places.
		//So any totaling is proper after this point, because it gets rounded to two decimal places
		//in PayStubEntryFactory too.
		$amount = round( $amount, 2 );
		$ytd_amount = round( $ytd_amount, 2 );

		if ( is_numeric( $amount ) ) {
			$psea_arr = $this->getPayStubEntryAccountArray( $pay_stub_entry_account_id );
			if ( is_array( $psea_arr) ) {
				$type_id = $psea_arr['type_id'];
			} else {
				$type_id = NULL;
			}

			$retarr = array(
				'pay_stub_entry_type_id' => $type_id,
				'pay_stub_entry_account_id' => $pay_stub_entry_account_id,
				'pay_stub_amendment_id' => $ps_amendment_id,
				'rate' => $rate,
				'units' => $units,
				'amount' => $amount,
				'ytd_units' => $ytd_units,
				'ytd_amount' => $ytd_amount,
				'description' => $description,
				'ytd_adjustment' => $ytd_adjustment,
				);

			$this->tmp_data['current_pay_stub'][] = $retarr;

			//Check if this pay stub account is linked to an accrual account.
			//Make sure the PSE account does not match the PSE Accrual account,
			//because we don't want to get in to an infinite loop.
			//Also don't touch the accrual account if the amount is 0.
			//This happens mostly when AddUnUsedEntries is called.
			if ( $this->getEnableLinkedAccruals() == TRUE
					AND $amount > 0
					AND $psea_arr['accrual_pay_stub_entry_account_id'] != ''
					AND $psea_arr['accrual_pay_stub_entry_account_id'] != 0
					AND $psea_arr['accrual_pay_stub_entry_account_id'] != $pay_stub_entry_account_id
					AND $ytd_adjustment == FALSE ) {

				Debug::text('Add Entry: PSE Account Links to Accrual Account!: '. $pay_stub_entry_account_id .' Accrual Account ID: '. $psea_arr['accrual_pay_stub_entry_account_id'] .' Amount: '. $amount, __FILE__, __LINE__, __METHOD__,10);

				if ( $type_id == 10 ) {
					$tmp_amount = $amount*-1; //This is an earning... Reduce accrual
				} elseif ( $type_id == 20 ) {
					$tmp_amount = $amount; //This is a employee deduction, add to accrual.
				} else {
					$tmp_amount = 0;
				}
				Debug::text('Amount: '. $tmp_amount , __FILE__, __LINE__, __METHOD__,10);

				return $this->addEntry( $psea_arr['accrual_pay_stub_entry_account_id'], $tmp_amount, NULL, NULL, NULL, NULL, NULL, NULL);
			}

			return TRUE;
		}

		Debug::text('Returning FALSE', __FILE__, __LINE__, __METHOD__,10);

		$this->Validator->isTrue(		'entry',
										FALSE,
										TTi18n::gettext('Invalid Pay Stub entry'));

		return FALSE;
	}

	function processEntries() {
		Debug::Text('Processing PayStub ('. count($this->tmp_data['current_pay_stub']) .') Entries...', __FILE__, __LINE__, __METHOD__,10);
		///Debug::Arr($this->tmp_data['current_pay_stub'], 'Current Entries...', __FILE__, __LINE__, __METHOD__,10);

		$this->deleteEntries( FALSE ); //Delete only total entries
		$this->addUnUsedYTDEntries();
		$this->addEarningSum();
		$this->addDeductionSum();
		$this->addEmployerDeductionSum();
		$this->addNetPay();

		return TRUE;
	}

	function markPayStubEntriesForYTDCalculation( &$pay_stub_arr, $clear_out_ytd = TRUE ) {
		if ( !is_array($pay_stub_arr) ) {
			return FALSE;
		}

		Debug::Text('Marking which entries are to have YTD calculated on!', __FILE__, __LINE__, __METHOD__,10);

		$trace_pay_stub_entry_account_id = array();

		//Loop over the array in reverse
		$pay_stub_arr = array_reverse( $pay_stub_arr, TRUE );
		foreach( $pay_stub_arr as $current_key => $val ) {
			if ( !isset($trace_pay_stub_entry_account_id[$pay_stub_arr[$current_key]['pay_stub_entry_account_id']]) ) {
				$trace_pay_stub_entry_account_id[$pay_stub_arr[$current_key]['pay_stub_entry_account_id']] = 0;
			} else {
				$trace_pay_stub_entry_account_id[$pay_stub_arr[$current_key]['pay_stub_entry_account_id']]++;
			}

			$pay_stub_arr[$current_key]['calc_ytd'] = $trace_pay_stub_entry_account_id[$pay_stub_arr[$current_key]['pay_stub_entry_account_id']];
			//Order here matters in cases for pay stubs with multiple accrual entries.
			//Because if the YTD amount is:
			// -800.00
			//    0.00
			//    0.00
			//We may end up clearing out the only YTD value that is of use.

			//CLEAR_OUT_YTD is used for backwards compat, so old pay stubs that calculated YTD
			//Only duplicate PS entries get zero'd out.
			if ( $clear_out_ytd == TRUE AND $pay_stub_arr[$current_key]['calc_ytd'] > 0 ) {
				//Clear out YTD entries so the sum() function can calculate them properly.
				//This is for backwards compat.
				$pay_stub_arr[$current_key]['ytd_amount'] = 0;
				$pay_stub_arr[$current_key]['ytd_units'] = 0;
			}
		}
		$pay_stub_arr = array_reverse( $pay_stub_arr, TRUE );

		//Debug::Arr($pay_stub_arr, 'Copy Marked Entries ', __FILE__, __LINE__, __METHOD__,10);

		return TRUE;
	}

	function calcPayStubEntriesYTD() {
		if ( !is_array($this->tmp_data['current_pay_stub']) ) {
			return FALSE;
		}

		Debug::Text('Calculating Pay Stub Entry YTD values!', __FILE__, __LINE__, __METHOD__,10);

		$this->markPayStubEntriesForYTDCalculation( $this->tmp_data['previous_pay_stub']['entries'] );
		$this->markPayStubEntriesForYTDCalculation( $this->tmp_data['current_pay_stub'], FALSE ); //Dont clear out YTD values.

		//Debug::Arr($this->tmp_data['current_pay_stub'], 'Before YTD calculation', __FILE__, __LINE__, __METHOD__,10);

		//addUnUsedYTDEntries() should be called before this

		//Go through each pay stub entry, and if there is no entry of the same
		//PSE account id, calc YTD. If there is a duplicate PSE account id,
		//only calculate the YTD on the LAST one.
		foreach( $this->tmp_data['current_pay_stub'] as $key => $entry_arr ) {
			//If YTD is already set, don't recalculate it, because it could be a PS amendment YTD adjustment.
			//Keep in mind this makes it so if a YTD adjustment is set it will show up in the YTD column, and if there
			//is a second PSE account of the same, its YTD will show up too.
			//So this is the ONLY time YTD values should show up for the duplicate PSE accounts on the same PS.
			if ( $entry_arr['calc_ytd'] == 0 ) {
				//Debug::Text('Calculating YTD on PSE account: '. $entry_arr['pay_stub_entry_account_id'], __FILE__, __LINE__, __METHOD__,10);
				$current_pay_stub_sum = $this->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current', NULL, $entry_arr['pay_stub_entry_account_id'] );
				$previous_pay_stub_sum = $this->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'previous', NULL, $entry_arr['pay_stub_entry_account_id'] );

				Debug::Text('Key: '. $key .' Previous YTD Amount: '. $previous_pay_stub_sum['ytd_amount'] .' Current Amount: '. $current_pay_stub_sum['amount'] .' Current YTD Amount: '. $current_pay_stub_sum['ytd_amount'], __FILE__, __LINE__, __METHOD__,10);
				$this->tmp_data['current_pay_stub'][$key]['ytd_amount'] = bcadd( $previous_pay_stub_sum['ytd_amount'], bcadd( $current_pay_stub_sum['amount'], $current_pay_stub_sum['ytd_amount'] ), 2 );
				$this->tmp_data['current_pay_stub'][$key]['ytd_units'] = bcadd( $previous_pay_stub_sum['ytd_units'], bcadd( $current_pay_stub_sum['units'], $current_pay_stub_sum['ytd_units'] ), 4 );
			} elseif ( $this->tmp_data['current_pay_stub'][$key]['ytd_amount'] == '' ) {
				//Debug::Text('Setting YTD on PSE account: '. $entry_arr['pay_stub_entry_account_id'], __FILE__, __LINE__, __METHOD__,10);
				$this->tmp_data['current_pay_stub'][$key]['ytd_amount'] = 0;
				$this->tmp_data['current_pay_stub'][$key]['ytd_units'] = 0;
			}
		}

		//Debug::Arr($this->tmp_data['current_pay_stub'], 'After YTD calculation', __FILE__, __LINE__, __METHOD__,10);

		return TRUE;
	}

	function savePayStubEntries() {
		if ( !is_array($this->tmp_data['current_pay_stub']) ) {
			return FALSE;
		}

		//Cant add entries to a new paystub, since the pay_stub_id isn't set yet.
		if ( $this->isNew() == TRUE ) {
			return FALSE;
		}

		$this->calcPayStubEntriesYTD();

		//Debug::Arr($this->tmp_data['current_pay_stub'], 'Current Pay Stub Entries: ', __FILE__, __LINE__, __METHOD__,10);

		foreach( $this->tmp_data['current_pay_stub'] as $pse_arr ) {
			if ( isset($pse_arr['pay_stub_entry_account_id']) AND isset($pse_arr['amount']) ) {
				Debug::Text('Current Pay Stub ID: '. $this->getId() .' Adding Pay Stub Entry for: '. $pse_arr['pay_stub_entry_account_id'] .' Amount: '. $pse_arr['amount'] .' YTD Amount: '. $pse_arr['ytd_amount'] .' YTD Units: '. $pse_arr['ytd_units'], __FILE__, __LINE__, __METHOD__,10);
				$psef = new PayStubEntryFactory();
				$psef->setPayStub( $this->getId() );
				$psef->setPayStubEntryNameId( $pse_arr['pay_stub_entry_account_id'] );
				$psef->setRate( $pse_arr['rate'] );
				$psef->setUnits( $pse_arr['units'] );
				$psef->setAmount( $pse_arr['amount'] );
				$psef->setYTDAmount( $pse_arr['ytd_amount'] );
				$psef->setYTDUnits( $pse_arr['ytd_units'] );

				$psef->setDescription( $pse_arr['description'] );
				if ( is_numeric( $pse_arr['pay_stub_amendment_id'] ) AND $pse_arr['pay_stub_amendment_id'] > 0 ) {
					$psef->setPayStubAmendment( $pse_arr['pay_stub_amendment_id'] );
				}

				$psef->setEnableCalculateYTD( FALSE );

				if ( $psef->isValid() == FALSE OR $psef->Save() == FALSE ) {
					Debug::Text('Adding Pay Stub Entry failed!', __FILE__, __LINE__, __METHOD__,10);

					$this->Validator->isTrue(		'entry',
													FALSE,
													TTi18n::gettext('Invalid Pay Stub entry'));
					return FALSE;
				}
			}
		}

		return TRUE;
	}

	function deleteEntries( $all_entries = FALSE ) {
		//Delete any entries from the pay stub, so they can be re-created.
		$pself = new PayStubEntryListFactory();

		if ( $all_entries == TRUE ) {
			$pself->getByPayStubIdAndType( $this->getId(), 40 );
		} else {
			$pself->getByPayStubId( $this->getId() );
		}

		foreach( $pself as $pay_stub_entry_obj ) {
			Debug::Text('Deleting Pay Stub Entry: '. $pay_stub_entry_obj->getId(), __FILE__, __LINE__, __METHOD__,10);
			$del_ps_entry_ids[] = $pay_stub_entry_obj->getId();
		}
		if ( isset($del_ps_entry_ids) ) {
			$pself->bulkDelete( $del_ps_entry_ids );
		}
		unset($pay_stub_entry_obj, $del_ps_entry_ids);

		return TRUE;
	}

	function addUnUsedYTDEntries() {
		Debug::Text('Adding Unused Entries ', __FILE__, __LINE__, __METHOD__,10);
		//This has to happen ABOVE the total entries... So Gross pay and stuff
		//takes them in to account when doing YTD totals
		//
		//Find out which prior entries have been made and carry any YTD entries forward with 0 amounts
		if ( isset($this->tmp_data['previous_pay_stub']) AND is_array( $this->tmp_data['previous_pay_stub']['entries']	) ) {
			//Debug::Arr($this->tmp_data['current_pay_stub'], 'Current Pay Stub Entries:', __FILE__, __LINE__, __METHOD__,10);

			foreach( $this->tmp_data['previous_pay_stub']['entries'] as $key => $entry_arr ) {
				//See if current pay stub entries have previous pay stub entries.
				//Skip total entries, as they will be greated after anyways.
				if ( $entry_arr['pay_stub_entry_type_id'] != 40
						AND Misc::inArrayByKeyAndValue( $this->tmp_data['current_pay_stub'], 'pay_stub_entry_account_id', $entry_arr['pay_stub_entry_account_id'] ) == FALSE ) {
					Debug::Text('Adding UnUsed Entry: '. $entry_arr['pay_stub_entry_account_id'], __FILE__, __LINE__, __METHOD__,10);
					$this->addEntry( $entry_arr['pay_stub_entry_account_id'], 0, 0 );
				} else {
					Debug::Text('NOT Adding already existing Entry: '. $entry_arr['pay_stub_entry_account_id'], __FILE__, __LINE__, __METHOD__,10);
				}
			}
		}

		return TRUE;
	}

	function addEarningSum() {
		$sum_arr = $this->getEarningSum();
		Debug::Text('Sum: '. $sum_arr['amount'], __FILE__, __LINE__, __METHOD__,10);
		if ($sum_arr['amount'] > 0) {
			$this->addEntry( $this->getPayStubEntryAccountLinkObject()->getTotalGross(), $sum_arr['amount'], $sum_arr['units'], NULL, NULL, NULL, $sum_arr['ytd_amount'] );
		}
		unset($sum_arr);

		return TRUE;
	}

	function addDeductionSum() {
		$sum_arr = $this->getDeductionSum();
		if ( isset($sum_arr['amount']) ) { //Allow negative amounts for adjustment purposes
			$this->addEntry( $this->getPayStubEntryAccountLinkObject()->getTotalEmployeeDeduction(), $sum_arr['amount'], $sum_arr['units'], NULL, NULL, NULL, $sum_arr['ytd_amount'] );
		}
		unset($sum_arr);

		return TRUE;
	}

	function addEmployerDeductionSum() {
		$sum_arr = $this->getEmployerDeductionSum();
		if ( isset($sum_arr['amount']) ) { //Allow negative amounts for adjustment purposes
			$this->addEntry( $this->getPayStubEntryAccountLinkObject()->getTotalEmployerDeduction(), $sum_arr['amount'], $sum_arr['units'], NULL, NULL, NULL, $sum_arr['ytd_amount'] );
		}
		unset($sum_arr);

		return TRUE;
	}

	function addNetPay() {
		$earning_sum_arr = $this->getEarningSum();
		$deduction_sum_arr = $this->getDeductionSum();

		if ( $earning_sum_arr['amount'] > 0 ) {
			Debug::Text('Earning Sum is greater than 0.', __FILE__, __LINE__, __METHOD__,10);

			$net_pay_amount = bcsub( $earning_sum_arr['amount'], $deduction_sum_arr['amount'] );
			$net_pay_ytd_amount = bcsub( $earning_sum_arr['ytd_amount'], $deduction_sum_arr['ytd_amount'] );

			$this->addEntry( $this->getPayStubEntryAccountLinkObject()->getTotalNetPay(), $net_pay_amount, NULL,  NULL, NULL, NULL, $net_pay_ytd_amount );
		}
		unset($net_pay_amount, $net_pay_ytd_amount, $earning_sum_arr, $deduction_sum_arr );

		Debug::Text('Earning Sum is 0 or less. ', __FILE__, __LINE__, __METHOD__,10);

		return TRUE;
	}

	function getEarningSum() {
		$retarr = $this->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current', 10);
		Debug::Text('Earnings Sum ('. $this->getId() .'): '. $retarr['amount'], __FILE__, __LINE__, __METHOD__,10);

		return $retarr;
	}

	function getDeductionSum() {
		$retarr = $this->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current', 20);
		Debug::Text('Deduction Sum: '. $retarr['amount'], __FILE__, __LINE__, __METHOD__,10);

		return $retarr;
	}

	function getEmployerDeductionSum() {
		$retarr = $this->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current', 30);
		Debug::Text('Employer Deduction Sum: '. $retarr['amount'], __FILE__, __LINE__, __METHOD__,10);

		return $retarr;
	}

	function getGrossPay() {
		if ( (int)$this->getPayStubEntryAccountLinkObject()->getTotalGross() == 0 ) {
			return FALSE;
		}

		$retarr = $this->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current', NULL, $this->getPayStubEntryAccountLinkObject()->getTotalGross() );
		Debug::Text('Gross Pay: '. $retarr['amount'], __FILE__, __LINE__, __METHOD__,10);

		if ( $retarr['amount'] == '' ) {
			$retarr['amount'] = 0;
		}

		return $retarr['amount'];
	}

	function getDeductions() {
		if ( (int)$this->getPayStubEntryAccountLinkObject()->getTotalEmployeeDeduction() == 0 ) {
			return FALSE;
		}

		$retarr = $this->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current', NULL, $this->getPayStubEntryAccountLinkObject()->getTotalEmployeeDeduction() );
		Debug::Text('Deductions: '. $retarr['amount'], __FILE__, __LINE__, __METHOD__,10);

		if ( $retarr['amount'] == '' ) {
			$retarr['amount'] = 0;
		}

		return $retarr['amount'];
	}

	function getNetPay() {
		if ( (int)$this->getPayStubEntryAccountLinkObject()->getTotalNetPay() == 0 ) {
			return FALSE;
		}

		$retarr = $this->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current' , NULL, $this->getPayStubEntryAccountLinkObject()->getTotalNetPay() );
		Debug::Text('Net Pay: '. $retarr['amount'], __FILE__, __LINE__, __METHOD__,10);

		if ( $retarr['amount'] == '' ) {
			$retarr['amount'] = 0;
		}

		return $retarr['amount'];
	}

	function checkNoEarnings() {
		$earnings = $this->getEarningSum();
		if ($earnings == FALSE OR $earnings['amount'] <= 0 ) {
			return FALSE;
		}

		return TRUE;
	}

	//Returns TRUE unless Amount explicitly does not match Gross Pay
	//use checkNoEarnings to see if any earnings exist or not.
	function checkEarnings() {
		$earnings = $this->getEarningSum();
		if ( isset($earnings['amount']) AND $earnings['amount'] != $this->getGrossPay() ) {
			return FALSE;
		}

		return TRUE;
	}

	function checkDeductions() {
		$deductions = $this->getDeductionSum();
		//Don't check for false here, as advance pay stubs may not have any deductions.
		if ( $deductions['amount'] != $this->getDeductions() ) {
			return FALSE;
		}

		return TRUE;
	}

	function checkNetPay() {
		$net_pay = $this->getNetPay();
		//$tmp_net_pay = number_format($this->getGrossPay() - ( $this->getDeductions() + $this->getAdvanceDeduction() ),2, '.', '');
		$tmp_net_pay = bcsub($this->getGrossPay(), $this->getDeductions() );
		Debug::Text('aCheck Net Pay: Net Pay: '. $net_pay .' Tmp Net Pay: '. $tmp_net_pay, __FILE__, __LINE__, __METHOD__,10);

		//Gotta take precision in to account.
		/*
		$epsilon = 0.00001;
		if (abs($net_pay - $tmp_net_pay) < $epsilon) {
			return TRUE;
		}
		*/

		if ($net_pay == $tmp_net_pay) {
			return TRUE;
		}

		Debug::Text('Check Net Pay: Returning false', __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}




























	/*

		Below here are functions for generating PDF pay stubs and exporting pay stub data to other
		formats such as cheques, or EFT file formats.

	*/

	function exportPayStub( $pslf = NULL, $export_type = NULL ) {
		global $current_company;

		if ( !is_object($pslf) AND $this->getId() != '' ) {
			$pslf = new PayStubListFactory();
			$pslf->getById( $this->getId() );
		}

		if ( get_class( $pslf ) !== 'PayStubListFactory' ) {
			return FALSE;
		}

		if ( $export_type == '' ) {
			return FALSE;
		}

		if ( $pslf->getRecordCount() > 0 ) {

			Debug::Text('aExporting...', __FILE__, __LINE__, __METHOD__,10);
			switch (strtolower($export_type)) {
				case 'hsbc':
				case '1464':
				case '105':
				case 'ach':
					//Get file creation number
					$ugdlf = new UserGenericDataListFactory();
					$ugdlf->getByCompanyIdAndScriptAndDefault( $current_company->getId(), 'PayStubFactory', TRUE );
					if ( $ugdlf->getRecordCount() > 0 ) {
						Debug::Text('Found Script Setup Data!', __FILE__, __LINE__, __METHOD__,10);
						$ugd_obj = $ugdlf->getCurrent();
						$setup_data = $ugd_obj->getData();
					} else {
						$ugd_obj = new UserGenericDataFactory();
					}

					Debug::Text('bExporting...', __FILE__, __LINE__, __METHOD__,10);
					//get User Bank account info
					$balf = new BankAccountListFactory();
					$balf->getCompanyAccountByCompanyId( $current_company->getID() );
					if ( $balf->getRecordCount() > 0 ) {
						$company_bank_obj = $balf->getCurrent();
						//Debug::Arr($company_bank_obj,'Company Bank Object', __FILE__, __LINE__, __METHOD__,10);
					}

					if ( isset( $setup_data['file_creation_number'] ) ) {
						$setup_data['file_creation_number']++;
					} else {
						//Start at a high number, in attempt to eliminate conflicts.
						$setup_data['file_creation_number'] = 500;
					}
					Debug::Text('bFile Creation Number: '. $setup_data['file_creation_number'], __FILE__, __LINE__, __METHOD__,10);

					//Increment file creation number in DB
					if ( $ugd_obj->getId() == '' ) {
							$ugd_obj->setID( $ugd_obj->getId() );
					}
					$ugd_obj->setCompany( $current_company->getId() );
					$ugd_obj->setName( 'PayStubFactory' );
					$ugd_obj->setScript( 'PayStubFactory' );
					$ugd_obj->setData( $setup_data );
					$ugd_obj->setDefault( TRUE );
					if ( $ugd_obj->isValid() ) {
							$ugd_obj->Save();
					}

					$eft = new EFT();
					$eft->setFileFormat( $export_type );

					$eft->setOriginatorID( $current_company->getOriginatorID() );
					$eft->setFileCreationNumber( $setup_data['file_creation_number'] );
					$eft->setDataCenter( $current_company->getDataCenterID() );
					$eft->setOriginatorShortName( $current_company->getShortName() );

					$psealf = new PayStubEntryAccountListFactory();
					foreach ($pslf as $pay_stub_obj) {
						Debug::Text('Looping over Pay Stub... ID: '. $pay_stub_obj->getId(), __FILE__, __LINE__, __METHOD__,10);

						//Get pay stub entries.
						$pself = new PayStubEntryListFactory();
						$pself->getByPayStubId( $pay_stub_obj->getId() );

						$prev_type = NULL;
						$description_subscript_counter = 1;
						foreach ($pself as $pay_stub_entry) {
							$description_subscript = NULL;

							//$pay_stub_entry_name_obj = $psenlf->getById( $pay_stub_entry->getPayStubEntryNameId() ) ->getCurrent();
							$pay_stub_entry_name_obj = $psealf->getById( $pay_stub_entry->getPayStubEntryNameId() )->getCurrent();

							if ( $prev_type == 40 OR $pay_stub_entry_name_obj->getType() != 40 ) {
								$type = $pay_stub_entry_name_obj->getType();
							}

							//var_dump( $pay_stub_entry->getDescription() );
							if ( $pay_stub_entry->getDescription() !== NULL
									AND $pay_stub_entry->getDescription() !== FALSE
									AND strlen($pay_stub_entry->getDescription()) > 0) {
								$pay_stub_entry_descriptions[] = array( 'subscript' => $description_subscript_counter,
																		'description' => $pay_stub_entry->getDescription() );

								$description_subscript = $description_subscript_counter;

								$description_subscript_counter++;
							}

							if ( $type != 40 OR ( $type == 40 AND $pay_stub_entry->getAmount() != 0 ) ) {
								$pay_stub_entries[$type][] = array(
															'id' => $pay_stub_entry->getId(),
															'pay_stub_entry_name_id' => $pay_stub_entry->getPayStubEntryNameId(),
															'type' => $pay_stub_entry_name_obj->getType(),
															'name' => $pay_stub_entry_name_obj->getName(),
															'display_name' => $pay_stub_entry_name_obj->getName(),
															'rate' => $pay_stub_entry->getRate(),
															'units' => $pay_stub_entry->getUnits(),
															'ytd_units' => $pay_stub_entry->getYTDUnits(),
															'amount' => $pay_stub_entry->getAmount(),
															'ytd_amount' => $pay_stub_entry->getYTDAmount(),

															'description' => $pay_stub_entry->getDescription(),
															'description_subscript' => $description_subscript,

															'created_date' => $pay_stub_entry->getCreatedDate(),
															'created_by' => $pay_stub_entry->getCreatedBy(),
															'updated_date' => $pay_stub_entry->getUpdatedDate(),
															'updated_by' => $pay_stub_entry->getUpdatedBy(),
															'deleted_date' => $pay_stub_entry->getDeletedDate(),
															'deleted_by' => $pay_stub_entry->getDeletedBy()
															);
							}

							$prev_type = $pay_stub_entry_name_obj->getType();
						}

						if ( isset($pay_stub_entries) ) {
							$pay_stub = array(
												'id' => $pay_stub_obj->getId(),
												'display_id' => str_pad($pay_stub_obj->getId(),12,0, STR_PAD_LEFT),
												'user_id' => $pay_stub_obj->getUser(),
												'pay_period_id' => $pay_stub_obj->getPayPeriod(),
												'start_date' => $pay_stub_obj->getStartDate(),
												'end_date' => $pay_stub_obj->getEndDate(),
												'transaction_date' => $pay_stub_obj->getTransactionDate(),
												'status' => $pay_stub_obj->getStatus(),
												'entries' => $pay_stub_entries,

												'created_date' => $pay_stub_obj->getCreatedDate(),
												'created_by' => $pay_stub_obj->getCreatedBy(),
												'updated_date' => $pay_stub_obj->getUpdatedDate(),
												'updated_by' => $pay_stub_obj->getUpdatedBy(),
												'deleted_date' => $pay_stub_obj->getDeletedDate(),
												'deleted_by' => $pay_stub_obj->getDeletedBy()
											);
							unset($pay_stub_entries);

							//Get User information
							$ulf = new UserListFactory();
							$user_obj = $ulf->getById( $pay_stub_obj->getUser() )->getCurrent();

							//Get company information
							$clf = new CompanyListFactory();
							$company_obj = $clf->getById( $user_obj->getCompany() )->getCurrent();

							//get User Bank account info
							$balf = new BankAccountListFactory();
							$user_bank_obj = $balf->getUserAccountByCompanyIdAndUserId( $user_obj->getCompany(), $user_obj->getId() );
							if ( $user_bank_obj->getRecordCount() > 0 ) {
								$user_bank_obj = $user_bank_obj->getCurrent();
							} else {
								continue;
							}

							$record = new EFT_Record();
							$record->setType('C');

							$amount = $pay_stub['entries'][40][0]['amount'];
							$record->setCPACode(200);

							$record->setAmount( $amount );
							unset($amount);

							$record->setDueDate( TTDate::getBeginDayEpoch($pay_stub_obj->getTransactionDate()) );
							//$record->setDueDate( strtotime("24-Sep-99") );

							$record->setInstitution( $user_bank_obj->getInstitution() );
							$record->setTransit( $user_bank_obj->getTransit() );
							$record->setAccount( $user_bank_obj->getAccount() );
							$record->setName( $user_obj->getFullName() );

							$record->setOriginatorShortName( $company_obj->getShortName() );
							$record->setOriginatorLongName( substr($company_obj->getName(),0,30) );
							$record->setOriginatorReferenceNumber( 'TT'.$pay_stub_obj->getId() );

							if ( isset($company_bank_obj) AND is_object($company_bank_obj) ) {
								$record->setReturnInstitution( $company_bank_obj->getInstitution() );
								$record->setReturnTransit( $company_bank_obj->getTransit() );
								$record->setReturnAccount( $company_bank_obj->getAccount() );
							}

							$eft->setRecord( $record );
						}
					}

					$eft->compile();
					$output = $eft->getCompiledData();
					break;
				case 'cheque_9085':
				case 'cheque_9209p':
				case 'cheque_dlt103':
				case 'cheque_dlt104':
				case 'cheque_cr_standard_form_1':
				case 'cheque_cr_standard_form_2':
					$border = 0;
					$show_background = 0;

					$pdf = new TTPDF();
					$pdf->setMargins(0,0,0,0);
					$pdf->SetAutoPageBreak(FALSE);
					$pdf->SetFont('freeserif','',10);

					$psealf = new PayStubEntryAccountListFactory();

					$i=0;
					foreach ($pslf as $pay_stub_obj) {
						//Get pay stub entries.
						$pself = new PayStubEntryListFactory();
						$pself->getByPayStubId( $pay_stub_obj->getId() );

						$pay_stub_entries = NULL;
						$prev_type = NULL;
						$description_subscript_counter = 1;
						foreach ($pself as $pay_stub_entry) {
							$description_subscript = NULL;

							//$pay_stub_entry_name_obj = $psenlf->getById( $pay_stub_entry->getPayStubEntryNameId() ) ->getCurrent();
							$pay_stub_entry_name_obj = $psealf->getById( $pay_stub_entry->getPayStubEntryNameId() )->getCurrent();

							//Use this to put the total for each type at the end of the array.
							if ( $prev_type == 40 OR $pay_stub_entry_name_obj->getType() != 40 ) {
								$type = $pay_stub_entry_name_obj->getType();
							}
							//Debug::text('Pay Stub Entry Name ID: '. $pay_stub_entry_name_obj->getId() .' Type ID: '. $pay_stub_entry_name_obj->getType() .' Type: '. $type, __FILE__, __LINE__, __METHOD__,10);

							//var_dump( $pay_stub_entry->getDescription() );
							if ( $pay_stub_entry->getDescription() !== NULL
									AND $pay_stub_entry->getDescription() !== FALSE
									AND strlen($pay_stub_entry->getDescription()) > 0) {
								$pay_stub_entry_descriptions[] = array( 'subscript' => $description_subscript_counter,
																		'description' => $pay_stub_entry->getDescription() );

								$description_subscript = $description_subscript_counter;

								$description_subscript_counter++;
							}

							$amount_words = str_pad( ucwords( Numbers_Words::toWords( floor($pay_stub_entry->getAmount()),"en_US") ).' ', 65, "-", STR_PAD_RIGHT );
							//echo "Amount: ". floor($pay_stub_entry->getAmount()) ." - Words: ". $amount_words ."<br>\n";
							//var_dump($amount_words);
							if ( $type != 40 OR ( $type == 40 AND $pay_stub_entry->getAmount() != 0 ) ) {
								$pay_stub_entries[$type][] = array(
															'id' => $pay_stub_entry->getId(),
															'pay_stub_entry_name_id' => $pay_stub_entry->getPayStubEntryNameId(),
															'type' => $pay_stub_entry_name_obj->getType(),
															'name' => $pay_stub_entry_name_obj->getName(),
															'display_name' => $pay_stub_entry_name_obj->getName(),
															'rate' => $pay_stub_entry->getRate(),
															'units' => $pay_stub_entry->getUnits(),
															'ytd_units' => $pay_stub_entry->getYTDUnits(),
															'amount' => $pay_stub_entry->getAmount(),
															'amount_padded' => str_pad($pay_stub_entry->getAmount(),12,'*', STR_PAD_LEFT),
															'amount_words' => $amount_words,
															'amount_cents' => Misc::getAfterDecimal($pay_stub_entry->getAmount()),
															'ytd_amount' => $pay_stub_entry->getYTDAmount(),

															'description' => $pay_stub_entry->getDescription(),
															'description_subscript' => $description_subscript,

															'created_date' => $pay_stub_entry->getCreatedDate(),
															'created_by' => $pay_stub_entry->getCreatedBy(),
															'updated_date' => $pay_stub_entry->getUpdatedDate(),
															'updated_by' => $pay_stub_entry->getUpdatedBy(),
															'deleted_date' => $pay_stub_entry->getDeletedDate(),
															'deleted_by' => $pay_stub_entry->getDeletedBy()
															);
							}
							unset($amount_words);
							//Only for net pay, make a total YTD of Advance plus Net.
							/*
							if ( $type == 40 ) {
								$pay_stub_entries[$type][0]['ytd_net_plus_advance'] =
							}
							*/

							$prev_type = $pay_stub_entry_name_obj->getType();
						}

						//Get User information
						$ulf = new UserListFactory();
						$user_obj = $ulf->getById( $pay_stub_obj->getUser() )->getCurrent();

						//Get company information
						$clf = new CompanyListFactory();
						$company_obj = $clf->getById( $user_obj->getCompany() )->getCurrent();

						if ( $user_obj->getCountry() == 'CA' ) {
							$date_format = 'd/m/Y';
						} else {
							$date_format = 'm/d/Y';
						}
						$pay_stub = array(
											'id' => $pay_stub_obj->getId(),
											'display_id' => str_pad($pay_stub_obj->getId(),15,0, STR_PAD_LEFT),
											'user_id' => $pay_stub_obj->getUser(),
											'pay_period_id' => $pay_stub_obj->getPayPeriod(),
											'start_date' => $pay_stub_obj->getStartDate(),
											'end_date' => $pay_stub_obj->getEndDate(),
											'transaction_date' => $pay_stub_obj->getTransactionDate(),
											'transaction_date_display' => date( $date_format, $pay_stub_obj->getTransactionDate() ),
											'status' => $pay_stub_obj->getStatus(),
											'entries' => $pay_stub_entries,
											'tainted' => $pay_stub_obj->getTainted(),

											'created_date' => $pay_stub_obj->getCreatedDate(),
											'created_by' => $pay_stub_obj->getCreatedBy(),
											'updated_date' => $pay_stub_obj->getUpdatedDate(),
											'updated_by' => $pay_stub_obj->getUpdatedBy(),
											'deleted_date' => $pay_stub_obj->getDeletedDate(),
											'deleted_by' => $pay_stub_obj->getDeletedBy()
										);
						unset($pay_stub_entries);

						Debug::text($i .'. Pay Stub Transaction Date: '. $pay_stub_obj->getTransactionDate(), __FILE__, __LINE__, __METHOD__,10);

						//Get Pay Period information
						$pplf = new PayPeriodListFactory();
						$pay_period_obj = $pplf->getById( $pay_stub_obj->getPayPeriod() )->getCurrent();

						$pp_start_date = $pay_period_obj->getStartDate();
						$pp_end_date = $pay_period_obj->getEndDate();
						$pp_transaction_date = $pay_period_obj->getTransactionDate();

						//Get pay period numbers
						$ppslf = new PayPeriodScheduleListFactory();
						$pay_period_schedule_obj = $ppslf->getById( $pay_period_obj->getPayPeriodSchedule() )->getCurrent();

						$pay_period_data = array(
												'start_date' => TTDate::getDate('DATE', $pp_start_date ),
												'end_date' => TTDate::getDate('DATE', $pp_end_date ),
												'transaction_date' => TTDate::getDate('DATE', $pp_transaction_date ),
												//'pay_period_number' => $pay_period_schedule_obj->getCurrentPayPeriodNumber( $pay_period_obj->getTransactionDate(), $pay_period_obj->getEndDate() ),
												'annual_pay_periods' => $pay_period_schedule_obj->getAnnualPayPeriods()
												);

						$pdf->AddPage();

						switch ( $export_type ) {
							case 'cheque_9085':
								$adjust_x = 0;
								$adjust_y = -5;

								if ( $show_background == 1 ) {
									$pdf->Image(Environment::getBasePath().'interface/images/nebs_cheque_9085.jpg',0,0,210,300);
								}

								$pdf->setXY( Misc::AdjustXY(17, $adjust_x), Misc::AdjustXY(42, $adjust_y) );
								$pdf->Cell(100,5, $pay_stub['entries'][40][0]['amount_words'], $border, 0, 'L');
								$pdf->Cell(15,5, $pay_stub['entries'][40][0]['amount_cents'] .'/100', $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(130, $adjust_x), Misc::AdjustXY(50, $adjust_y) );
								$pdf->Cell(38,5, $pay_stub['transaction_date_display'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(175, $adjust_x),Misc::AdjustXY(50, $adjust_y));
								$pdf->Cell(23,5, ' '. $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount_padded'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(17, $adjust_x), Misc::AdjustXY(55, $adjust_y) );
								$pdf->Cell(100,5, $user_obj->getFullName(), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(17, $adjust_x), Misc::AdjustXY(60, $adjust_y));
								$pdf->Cell(100,5, $user_obj->getAddress1() .' '. $user_obj->getAddress2() ,$border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(17, $adjust_x),  Misc::AdjustXY(65, $adjust_y));
								$pdf->Cell(100,5, $user_obj->getCity() .', '. $user_obj->getProvince() .' '.$user_obj->getPostalCode() ,$border, 0, 'L');


								//Cheque Stub
								$stub_2_offset = 95;

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x), Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x), Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x), Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x), Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x), Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(110+$stub_2_offset, $adjust_y) );
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');

								//Earnings
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: '). $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: '). $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');

								break;
							case 'cheque_9209p':
								$adjust_x = 0;
								$adjust_y = -5;

								if ( $show_background == 1 ) {
									$pdf->Image(Environment::getBasePath().'interface/images/nebs_cheque_9209P.jpg',0,0,210,300);
								}

								$pdf->setXY(Misc::AdjustXY(25, $adjust_x),Misc::AdjustXY(42, $adjust_y));
								$pdf->Cell(100,10, $pay_stub['entries'][40][0]['amount_words'], $border, 0, 'L');
								$pdf->Cell(15,10, $pay_stub['entries'][40][0]['amount_cents'] .'/100', $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(172, $adjust_x),Misc::AdjustXY(25, $adjust_y));
								$pdf->Cell(10,10, TTi18n::gettext('Date:').' ', $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(182, $adjust_x),Misc::AdjustXY(25, $adjust_y));
								$pdf->Cell(25,10, $pay_stub['transaction_date_display'], $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(172, $adjust_x),Misc::AdjustXY(42, $adjust_y));
								$pdf->Cell(35,10, $pay_stub['entries'][40][0]['amount_padded'], $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(25, $adjust_x), Misc::AdjustXY(57, $adjust_y));
								$pdf->Cell(100,5, $user_obj->getFullName(), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(25, $adjust_x), Misc::AdjustXY(62, $adjust_y));
								$pdf->Cell(100,5, $user_obj->getAddress1() .' '. $user_obj->getAddress2() ,$border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(25, $adjust_x), Misc::AdjustXY(67, $adjust_y));
								$pdf->Cell(100,5, $user_obj->getCity() .', '. $user_obj->getProvince() .' '.$user_obj->getPostalCode() ,$border, 0, 'L');


								//Cheque Stub
								$stub_2_offset = 100;

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');

								//Earnings
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: '). $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: '). $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');

								break;
							case 'cheque_dlt103':
								$adjust_x = 0;
								$adjust_y = -5;

								if ( $show_background == 1 ) {
									$pdf->Image(Environment::getBasePath().'interface/images/nebs_cheque_dlt103.jpg',0,0,210,300);
								}

								$pdf->setXY(Misc::AdjustXY(25, $adjust_x),Misc::AdjustXY(54, $adjust_y));
								$pdf->Cell(100,10, $pay_stub['entries'][40][0]['amount_words'], $border, 0, 'L');
								$pdf->Cell(15,10, $pay_stub['entries'][40][0]['amount_cents'] .'/100', $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(172, $adjust_x),Misc::AdjustXY(33, $adjust_y));
								$pdf->Cell(10,10, TTi18n::gettext('Date:').' ', $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(182, $adjust_x),Misc::AdjustXY(33, $adjust_y));
								$pdf->Cell(25,10, $pay_stub['transaction_date_display'], $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(172, $adjust_x),Misc::AdjustXY(46, $adjust_y));
								$pdf->Cell(35,10, $pay_stub['entries'][40][0]['amount_padded'], $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(25, $adjust_x), Misc::AdjustXY(46, $adjust_y));
								$pdf->Cell(100,5, $user_obj->getFullName(), $border, 0, 'L');


								//Cheque Stub
								$stub_2_offset = 100;

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');

								//Earnings
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: '). $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: '). $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');
								break;
							case 'cheque_dlt104':
								$adjust_x = 0;
								$adjust_y = -5;

								if ( $show_background == 1 ) {
									$pdf->Image(Environment::getBasePath().'interface/images/nebs_cheque_dlt104.jpg',0,0,210,300);
								}

								$pdf->setXY(Misc::AdjustXY(25, $adjust_x),Misc::AdjustXY(52, $adjust_y));
								$pdf->Cell(100,10, $pay_stub['entries'][40][0]['amount_words'], $border, 0, 'L');
								$pdf->Cell(15,10, $pay_stub['entries'][40][0]['amount_cents'] .'/100', $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(172, $adjust_x),Misc::AdjustXY(33, $adjust_y));
								$pdf->Cell(10,10, TTi18n::gettext('Date:').' ', $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(182, $adjust_x),Misc::AdjustXY(33, $adjust_y));
								$pdf->Cell(25,10, $pay_stub['transaction_date_display'], $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(172, $adjust_x),Misc::AdjustXY(43, $adjust_y));
								$pdf->Cell(35,10, $pay_stub['entries'][40][0]['amount_padded'], $border, 0, 'C');

								$pdf->setXY(Misc::AdjustXY(25, $adjust_x), Misc::AdjustXY(48, $adjust_y));
								$pdf->Cell(100,5, $user_obj->getFullName(), $border, 0, 'L');


								//Cheque Stub
								$stub_2_offset = 100;

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');

								//Earnings
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: '). $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: '). $pay_stub_obj->getCurrencyObject()->getSymbol() . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');
								break;
							case 'cheque_cr_standard_form_1':
								$adjust_x = 0;
								$adjust_y = -5;

								if ( $show_background == 1 ) {
									$pdf->Image(Environment::getBasePath().'interface/images/nebs_cheque_9085.jpg',0,0,210,300);
								}

								$pdf->setXY( Misc::AdjustXY(20, $adjust_x), Misc::AdjustXY(41, $adjust_y) );
								$pdf->Cell(100,5, $pay_stub['entries'][40][0]['amount_words'] . TTi18n::gettext(' and ') .  $pay_stub['entries'][40][0]['amount_cents'] .'/100 *****', $border, 0, 'J');

								$pdf->setXY(Misc::AdjustXY(100, $adjust_x), Misc::AdjustXY(23, $adjust_y) );
								$pdf->Cell(38,5, $pay_stub['transaction_date_display'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(136, $adjust_x),Misc::AdjustXY(32, $adjust_y));
								$pdf->Cell(24,5, '  $' .$pay_stub['entries'][40][0]['amount_padded'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(20, $adjust_x), Misc::AdjustXY(33, $adjust_y) );
								$pdf->Cell(100,5, $user_obj->getFullName(), $border, 0, 'L');


								//Cheque Stub
								$stub_2_offset = 95;

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x), Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x), Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, $user_obj->getFullName(), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x), Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x), Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Identification #:').' '. $pay_stub['display_id'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x), Misc::AdjustXY(110, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(110+$stub_2_offset, $adjust_y) );
								$pdf->Cell(50,5, TTi18n::gettext('Pay Start Date:').' '. TTDate::getDate('DATE', $pay_stub['start_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(115+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Pay End Date:').' '. TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');
								$pdf->setXY(Misc::AdjustXY(160, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(50,5, TTi18n::gettext('Payment Date:').' '. TTDate::getDate('DATE', $pay_stub['transaction_date'] ), $border, 0, 'L');

								//Earnings
								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: $') . $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(15, $adjust_x),Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->Cell(40,5, TTi18n::gettext('Net Pay: $'). $pay_stub['entries'][40][0]['amount'], $border, 0, 'L');

								//Signature lines

								$pdf->setXY( Misc::AdjustXY(7, $adjust_x), Misc::AdjustXY(250, $adjust_y) );

								$border = 0;
								$pdf->Cell(40,5, TTi18n::gettext('Employee Signature:'), $border, 0, 'L');
								$pdf->Cell(60,5, '_____________________________' , $border, 0, 'L');
								$pdf->Cell(40,5, TTi18n::gettext('Supervisor Signature:'), $border, 0, 'R');
								$pdf->Cell(60,5, '_____________________________' , $border, 0, 'L');

								$pdf->Ln();
								$pdf->Cell(40,5, '', $border, 0, 'R');
								$pdf->Cell(60,5, $user_obj->getFullName() , $border, 0, 'C');

								$pdf->Ln();
								$pdf->Cell(147,5, '', $border, 0, 'R');
								$pdf->Cell(60,5, '_____________________________' , $border, 0, 'C');

								$pdf->Ln();
								$pdf->Cell(140,5, '', $border, 0, 'R');
								$pdf->Cell(60,5, TTi18n::gettext('(print name)'), $border, 0, 'C');

								break;

							case 'cheque_cr_standard_form_2':
								$pdf_created_date = time();
								$adjust_x = 0;
								$adjust_y = -5;

								if ( $show_background == 1 ) {
									$pdf->Image(Environment::getBasePath().'interface/images/nebs_cheque_9085.jpg',0,0,210,300);
								}

								$pdf->setXY( Misc::AdjustXY(20, $adjust_x), Misc::AdjustXY(41, $adjust_y) );
								$pdf->Cell(100,5, $pay_stub['entries'][40][0]['amount_words'] . TTi18n::gettext(' and ') .  $pay_stub['entries'][40][0]['amount_cents'] .'/100 *****', $border, 0, 'J');

								$pdf->setXY(Misc::AdjustXY(100, $adjust_x), Misc::AdjustXY(23, $adjust_y) );
								$pdf->Cell(38,5, $pay_stub['transaction_date_display'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(136, $adjust_x),Misc::AdjustXY(32, $adjust_y));
								$pdf->Cell(24,5, '$  ' .$pay_stub['entries'][40][0]['amount_padded'], $border, 0, 'L');

								$pdf->setXY(Misc::AdjustXY(20, $adjust_x), Misc::AdjustXY(33, $adjust_y) );
								$pdf->Cell(100,5, $user_obj->getFullName(), $border, 0, 'L');

								//Cheque Stub
								$stub_2_offset = 110;

								$pdf->SetFont('','U',14);
								$pdf->setXY(Misc::AdjustXY(65, $adjust_x), Misc::AdjustXY(100, $adjust_y));
								$pdf->Cell(75,5, TTi18n::gettext('Recipient Copy:'), $border, 0, 'C');

								$pdf->SetFont('','',10);
								$pdf->setXY(Misc::AdjustXY(75, $adjust_x), Misc::AdjustXY(110, $adjust_y));
								$pdf->SetFont('','B',10);
								$pdf->Cell(30,5, TTi18n::gettext('Date of Issue:'), $border, 0, 'J');
								$pdf->SetFont('','',10);
								$pdf->Cell(130,5, TTDate::getDate('DATE+TIME', $pdf_created_date ), $border, 0, 'J');

								$pdf->setXY(Misc::AdjustXY(75, $adjust_x), Misc::AdjustXY(110+$stub_2_offset, $adjust_y));
								$pdf->SetFont('','B',10);
								$pdf->Cell(30,5, TTi18n::gettext('Date of Issue:'), $border, 0, 'J');
								$pdf->SetFont('','',10);
								$pdf->Cell(130,5, TTDate::getDate('DATE+TIME', $pdf_created_date ), $border, 0, 'J');

								$pdf->setXY(Misc::AdjustXY(75, $adjust_x), Misc::AdjustXY(120, $adjust_y));
								$pdf->SetFont('','B',10);
								$pdf->Cell(30,5, TTi18n::gettext('Recipient:'), $border, 0, 'J');
								$pdf->SetFont('','',10);
								$pdf->Cell(110,5, $user_obj->getFullName(), $border, 0, 'J');

								$pdf->setXY(Misc::AdjustXY(75, $adjust_x), Misc::AdjustXY(120+$stub_2_offset, $adjust_y));
								$pdf->SetFont('','B',10);
								$pdf->Cell(30,5, TTi18n::gettext('Recipient:'), $border, 0, 'J');
								$pdf->SetFont('','',10);
								$pdf->Cell(130,5, $user_obj->getFullName(), $border, 0, 'J');

								//Earnings
								$pdf->setXY(Misc::AdjustXY(75, $adjust_x),Misc::AdjustXY(130, $adjust_y));
								$pdf->SetFont('','B',10);
								$pdf->Cell(30,5, TTi18n::gettext('Amount:'), $border, 0, 'J');
								$pdf->SetFont('','',10);
								$pdf->Cell(100,5, ' $'. $pay_stub['entries'][40][0]['amount'], $border, 0, 'J');
								$pdf->setXY(Misc::AdjustXY(75, $adjust_x),Misc::AdjustXY(130+$stub_2_offset, $adjust_y));
								$pdf->SetFont('','B',10);
								$pdf->Cell(30,5, TTi18n::gettext('Amount:'), $border, 0, 'J');
								$pdf->SetFont('','',10);
								$pdf->Cell(100,5, ' $'. $pay_stub['entries'][40][0]['amount'], $border, 0, 'J');

								$pdf->setXY(Misc::AdjustXY(75, $adjust_x), Misc::AdjustXY(140, $adjust_y));
								$pdf->SetFont('','B',10);
								$pdf->Cell(30,5, TTi18n::gettext('Regarding:'), $border, 0, 'J');
								$pdf->SetFont('','',10);
								$pdf->Cell(100,5, TTi18n::gettext('Payment from') .' '. TTDate::getDate('DATE', $pay_stub['start_date'] ).' '. TTi18n::gettext('to').' '.TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'J');
								$pdf->setXY(Misc::AdjustXY(75, $adjust_x), Misc::AdjustXY(140+$stub_2_offset, $adjust_y));
								$pdf->SetFont('','B',10);
								$pdf->Cell(30,5, TTi18n::gettext('Regarding:'), $border, 0, 'J');
								$pdf->SetFont('','',10);
								$pdf->Cell(100,5, TTi18n::gettext('Payment from') .' '. TTDate::getDate('DATE', $pay_stub['start_date'] ).' '. TTi18n::gettext('to').' '.TTDate::getDate('DATE', $pay_stub['end_date'] ), $border, 0, 'J');

								$pdf->SetFont('','U',14);
								$pdf->setXY(Misc::AdjustXY(65, $adjust_x), Misc::AdjustXY(210, $adjust_y));
								$pdf->Cell(75,5, $company_obj->getName().' '.TTi18n::gettext('Copy:'), $border, 0, 'C');

								$pdf->setXY( Misc::AdjustXY(30, $adjust_x), Misc::AdjustXY(260, $adjust_y) );

								$column_widths = array(
										'generated_by' => 25,
										'signed_by' => 25,
										'received_by' => 35,
										'date' => 35,
										'sin_ssn' => 35,
										);

								$line_h = 4;
								$cell_h_min = $cell_h_max = $line_h * 4;

								$pdf->SetFont('','',8);
								$pdf->setFillColor(255,255,255);
								$pdf->MultiCell( $column_widths['generated_by'], $line_h, TTi18n::gettext('Generated By'). "\n\n\n " , 1, 'C', 1, 0);
								$pdf->MultiCell( $column_widths['signed_by'], $line_h, TTi18n::gettext('Signed By'). "\n\n\n " , 1, 'C', 1, 0);
								$pdf->MultiCell( $column_widths['received_by'], $line_h, TTi18n::gettext('Received By') . "\n\n\n " , 'T,L,B', 'C', 1, 0);
								$pdf->MultiCell( $column_widths['date'], $line_h, TTi18n::gettext('Date') . "\n\n\n ", 'T,B', 'C', 1, 0);
								$pdf->MultiCell( $column_widths['sin_ssn'], $line_h, TTi18n::gettext('SIN / SSN') . "\n\n\n " , 'T,R,B', 'C', 1, 0);
								$pdf->Ln();
								$pdf->SetFont('','',10);

								break;
						}

						$i++;
					}

					$output = $pdf->Output('','S');

					break;
			}
		}

		if ( isset($output) ) {
			return $output;
		}

		return FALSE;
	}

	function getPayStub( $pslf = NULL, $hide_employer_rows = TRUE ) {
		if ( !is_object($pslf) AND $this->getId() != '' ) {
			$pslf = new PayStubListFactory();
			$pslf->getById( $this->getId() );
		}

		if ( get_class( $pslf ) !== 'PayStubListFactory' ) {
			return FALSE;
		}

		$border = 0;

		if ( $pslf->getRecordCount() > 0 ) {

			$pdf = new TTPDF('P','mm','Letter');
			$pdf->setMargins(0,0);
			//$pdf->SetAutoPageBreak(TRUE, 30);
			$pdf->SetAutoPageBreak(FALSE);
			$pdf->SetFont('freeserif','',10);
			//$pdf->SetFont('FreeSans','',10);

			$i=0;
			foreach ($pslf as $pay_stub_obj) {
				$psealf = new PayStubEntryAccountListFactory();

				Debug::text($i .'. Pay Stub Transaction Date: '. $pay_stub_obj->getTransactionDate(), __FILE__, __LINE__, __METHOD__,10);

				//Get Pay Period information
				$pplf = new PayPeriodListFactory();
				$pay_period_obj = $pplf->getById( $pay_stub_obj->getPayPeriod() )->getCurrent();

				//Use Pay Stub dates, not Pay Period dates.
				$pp_start_date = $pay_stub_obj->getStartDate();
				$pp_end_date = $pay_stub_obj->getEndDate();
				$pp_transaction_date = $pay_stub_obj->getTransactionDate();

				//Get pay period numbers
				$ppslf = new PayPeriodScheduleListFactory();
				$pay_period_schedule_obj = $ppslf->getById( $pay_period_obj->getPayPeriodSchedule() )->getCurrent();

				//Get User information
				$ulf = new UserListFactory();
				$user_obj = $ulf->getById( $pay_stub_obj->getUser() )->getCurrent();

				//Get company information
				$clf = new CompanyListFactory();
				$company_obj = $clf->getById( $user_obj->getCompany() )->getCurrent();

				//Change locale to users own locale.
				TTi18n::setCountry( $user_obj->getCountry() );
				TTi18n::setLanguage( $user_obj->getUserPreferenceObject()->getLanguage() );
				TTi18n::setLocale();

				//
				// Pay Stub Header
				//
				$pdf->AddPage();

				$adjust_x = 20;
				$adjust_y = 10;

				//Logo
				$pdf->Image( $company_obj->getLogoFileName() ,Misc::AdjustXY(0, $adjust_x+0 ),Misc::AdjustXY(1, $adjust_y+0 ), 50, 12, '', '', '', FALSE, 300, '', FALSE, FALSE, 0, TRUE);

				//Company name/address
				$pdf->SetFont('','B',14);
				$pdf->setXY( Misc::AdjustXY(50, $adjust_x), Misc::AdjustXY(0, $adjust_y) );
				$pdf->Cell(75,5,$company_obj->getName(), $border, 0, 'C');

				$pdf->SetFont('','',10);
				$pdf->setXY( Misc::AdjustXY(50, $adjust_x), Misc::AdjustXY(5, $adjust_y) );
				$pdf->Cell(75,5,$company_obj->getAddress1().' '.$company_obj->getAddress2(), $border, 0, 'C');

				$pdf->setXY( Misc::AdjustXY(50, $adjust_x), Misc::AdjustXY(10, $adjust_y) );
				$pdf->Cell(75,5,$company_obj->getCity().', '.$company_obj->getProvince() .' '. strtoupper($company_obj->getPostalCode()), $border, 0, 'C');

				//Pay Period info
				$pdf->SetFont('','',10);
				$pdf->setXY( Misc::AdjustXY(125, $adjust_x), Misc::AdjustXY(0, $adjust_y) );
				$pdf->Cell(30,5,TTi18n::gettext('Pay Start Date:').' ', $border, 0, 'R');
				$pdf->setXY( Misc::AdjustXY(125, $adjust_x), Misc::AdjustXY(5, $adjust_y) );
				$pdf->Cell(30,5,TTi18n::gettext('Pay End Date:').' ', $border, 0, 'R');
				$pdf->setXY( Misc::AdjustXY(125, $adjust_x), Misc::AdjustXY(10, $adjust_y) );
				$pdf->Cell(30,5,TTi18n::gettext('Payment Date:').' ', $border, 0, 'R');

				$pdf->SetFont('','B',10);
				$pdf->setXY( Misc::AdjustXY(155, $adjust_x), Misc::AdjustXY(0, $adjust_y) );
				$pdf->Cell(20,5, TTDate::getDate('DATE', $pp_start_date ) , $border, 0, 'R');
				$pdf->setXY( Misc::AdjustXY(155, $adjust_x), Misc::AdjustXY(5, $adjust_y) );
				$pdf->Cell(20,5, TTDate::getDate('DATE', $pp_end_date ) , $border, 0, 'R');
				$pdf->setXY( Misc::AdjustXY(155, $adjust_x), Misc::AdjustXY(10, $adjust_y) );
				$pdf->Cell(20,5, TTDate::getDate('DATE', $pp_transaction_date ) , $border, 0, 'R');

				//Line
				$pdf->setLineWidth( 1 );
				$pdf->Line( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY(17, $adjust_y), Misc::AdjustXY(185, $adjust_y), Misc::AdjustXY(17, $adjust_y) );

				$pdf->SetFont('','B',14);
				$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY(19, $adjust_y) );
				$pdf->Cell(175, 5, TTi18n::gettext('STATEMENT OF EARNINGS AND DEDUCTIONS'), $border, 0, 'C', 0);

				//Line
				$pdf->setLineWidth( 1 );
				$pdf->Line( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY(27, $adjust_y), Misc::AdjustXY(185, $adjust_y), Misc::AdjustXY(27, $adjust_y) );

				$pdf->setLineWidth( 0.25 );

				//Get pay stub entries.
				$pself = new PayStubEntryListFactory();
				$pself->getByPayStubId( $pay_stub_obj->getId() );
				Debug::text('Pay Stub Entries: '. $pself->getRecordCount()  , __FILE__, __LINE__, __METHOD__,10);

				$prev_type = NULL;
				$description_subscript_counter = 1;
				foreach ($pself as $pay_stub_entry) {

					Debug::text('Pay Stub Entry Account ID: '.$pay_stub_entry->getPayStubEntryNameId()  , __FILE__, __LINE__, __METHOD__,10);
					$description_subscript = NULL;

					$pay_stub_entry_name_obj = $psealf->getById( $pay_stub_entry->getPayStubEntryNameId() )->getCurrent();

					//Use this to put the total for each type at the end of the array.
					if ( $prev_type == 40 OR $pay_stub_entry_name_obj->getType() != 40 ) {
						$type = $pay_stub_entry_name_obj->getType();
					}
					//Debug::text('Pay Stub Entry Name ID: '. $pay_stub_entry_name_obj->getId() .' Type ID: '. $pay_stub_entry_name_obj->getType() .' Type: '. $type, __FILE__, __LINE__, __METHOD__,10);

					if ( $pay_stub_entry->getDescription() !== NULL
							AND $pay_stub_entry->getDescription() !== FALSE
							AND strlen($pay_stub_entry->getDescription()) > 0) {
						$pay_stub_entry_descriptions[] = array( 'subscript' => $description_subscript_counter,
																'description' => $pay_stub_entry->getDescription() );

						$description_subscript = $description_subscript_counter;

						$description_subscript_counter++;
					}

					//If type if 40 (a total) and the amount is 0, skip it.
					//This if the employee has no deductions at all, it won't be displayed
					//on the pay stub.
					if ( $type != 40 OR ( $type == 40 AND $pay_stub_entry->getAmount() != 0 ) ) {
						$pay_stub_entries[$type][] = array(
													'id' => $pay_stub_entry->getId(),
													'pay_stub_entry_name_id' => $pay_stub_entry->getPayStubEntryNameId(),
													'type' => $pay_stub_entry_name_obj->getType(),
													'name' => $pay_stub_entry_name_obj->getName(),
													'display_name' => $pay_stub_entry_name_obj->getName(),
													'rate' => $pay_stub_entry->getRate(),
													'units' => $pay_stub_entry->getUnits(),
													'ytd_units' => $pay_stub_entry->getYTDUnits(),
													'amount' => $pay_stub_entry->getAmount(),
													'ytd_amount' => $pay_stub_entry->getYTDAmount(),

													'description' => $pay_stub_entry->getDescription(),
													'description_subscript' => $description_subscript,

													'created_date' => $pay_stub_entry->getCreatedDate(),
													'created_by' => $pay_stub_entry->getCreatedBy(),
													'updated_date' => $pay_stub_entry->getUpdatedDate(),
													'updated_by' => $pay_stub_entry->getUpdatedBy(),
													'deleted_date' => $pay_stub_entry->getDeletedDate(),
													'deleted_by' => $pay_stub_entry->getDeletedBy()
													);
					}

					$prev_type = $pay_stub_entry_name_obj->getType();
				}

				//There should always be pay stub entries for a pay stub.
				if ( !isset( $pay_stub_entries) ) {
					continue;
				}
				//Debug::Arr($pay_stub_entries, 'Pay Stub Entries...', __FILE__, __LINE__, __METHOD__,10);

				//$pay_period_number = $pay_period_schedule_obj->getCurrentPayPeriodNumber( $pay_period_obj->getTransactionDate(), $pay_period_obj->getEndDate() );

				$block_adjust_y = 30;

				//
				//Earnings
				//
				if ( isset($pay_stub_entries[10]) ) {
					//Earnings Header

					$pdf->SetFont('','B',10);
					$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
					$pdf->Cell(90,5,TTi18n::gettext('Earnings'), $border, 0, 'L');
					$pdf->Cell(17,5,TTi18n::gettext('Rate'), $border, 0, 'R');
					$pdf->Cell(23,5,TTi18n::gettext('Hrs/Units'), $border, 0, 'R');
					$pdf->Cell(20,5,TTi18n::gettext('Amount'), $border, 0, 'R');
					$pdf->Cell(25,5,TTi18n::gettext('YTD Amount'), $border, 0, 'R');

					$block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','',10);
					foreach( $pay_stub_entries[10] as $pay_stub_entry ) {

						if ( $pay_stub_entry['type'] == 10 ) {
							if ( $pay_stub_entry['description_subscript'] != '' ) {
								$subscript = '['.$pay_stub_entry['description_subscript'].']';
							} else {
								$subscript = NULL;
							}

							$pdf->setXY( Misc::AdjustXY(2, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->Cell(88,5, $pay_stub_entry['name'] . $subscript, $border, 0, 'L');
							$pdf->Cell(17,5, TTi18n::formatNumber( $pay_stub_entry['rate'], TRUE ), $border, 0, 'R');
							$pdf->Cell(23,5, TTi18n::formatNumber( $pay_stub_entry['units'], TRUE ), $border, 0, 'R');
							$pdf->Cell(20,5, TTi18n::formatNumber( $pay_stub_entry['amount'] ), $border, 0, 'R');
							$pdf->Cell(25,5, TTi18n::formatNumber( $pay_stub_entry['ytd_amount'] ), $border, 0, 'R');
						} else {
							//Total
							$pdf->SetFont('','B',10);

							$pdf->line(Misc::AdjustXY(110, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y), Misc::AdjustXY(130, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->line(Misc::AdjustXY(131, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y), Misc::AdjustXY(150, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->line(Misc::AdjustXY(151, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y), Misc::AdjustXY(175, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->Cell(90,5, $pay_stub_entry['name'], $border, 0, 'L');
							$pdf->Cell(17,5, '', $border, 0, 'R');
							$pdf->Cell(23,5, TTi18n::formatNumber( $pay_stub_entry['units'], TRUE ), $border, 0, 'R');
							$pdf->Cell(20,5, TTi18n::formatNumber( $pay_stub_entry['amount'] ), $border, 0, 'R');
							$pdf->Cell(25,5, TTi18n::formatNumber( $pay_stub_entry['ytd_amount'] ), $border, 0, 'R');
						}

						$block_adjust_y = $block_adjust_y + 5;
					}
				}

				//
				// Deductions
				//
				if ( isset($pay_stub_entries[20]) ) {
					$max_deductions = count($pay_stub_entries[20]);

					//Deductions Header
					$block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','B',10);
					if ( $max_deductions > 2 ) {
						$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
						$pdf->Cell(40,5,TTi18n::gettext('Deductions'), $border, 0, 'L');
						$pdf->Cell(20,5,TTi18n::gettext('Amount'), $border, 0, 'R');
						$pdf->Cell(25,5,TTi18n::gettext('YTD Amount'), $border, 0, 'R');

						$pdf->setXY( Misc::AdjustXY(90, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
						$pdf->Cell(40,5,TTi18n::gettext('Deductions'), $border, 0, 'L');
					} else {
						$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
						$pdf->Cell(130,5,TTi18n::gettext('Deductions'), $border, 0, 'L');
					}

					$pdf->Cell(20,5,TTi18n::gettext('Amount'), $border, 0, 'R');
					$pdf->Cell(25,5,TTi18n::gettext('YTD Amount'), $border, 0, 'R');

					$block_adjust_y = $tmp_block_adjust_y = $top_block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','',10);
					$x=0;
					$max_block_adjust_y = 0;
					foreach( $pay_stub_entries[20] as $pay_stub_entry ) {
						//Start with the right side.
						if ( $x < floor($max_deductions / 2) ) {
							$tmp_adjust_x = 90;
						} else {
							if ( $tmp_block_adjust_y != 0 ) {
								$block_adjust_y = $tmp_block_adjust_y;
								$tmp_block_adjust_y = 0;
							}
							$tmp_adjust_x = 0;
						}

						if ( $pay_stub_entry['type'] == 20 ) {
							if ( $pay_stub_entry['description_subscript'] != '' ) {
								$subscript = '['.$pay_stub_entry['description_subscript'].']';
							} else {
								$subscript = NULL;
							}

							if ( $max_deductions > 2 ) {
								$pdf->setXY( Misc::AdjustXY(2, $tmp_adjust_x+$adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
								$pdf->Cell(38,5, $pay_stub_entry['name'] . $subscript, $border, 0, 'L');
							} else {
								$pdf->setXY( Misc::AdjustXY(2, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
								$pdf->Cell(128,5, $pay_stub_entry['name'] . $subscript, $border, 0, 'L');
							}
							$pdf->Cell(20,5, TTi18n::formatNumber( $pay_stub_entry['amount'] ), $border, 0, 'R');
							$pdf->Cell(25,5, TTi18n::formatNumber( $pay_stub_entry['ytd_amount'] ), $border, 0, 'R');
						} else {
							$block_adjust_y = $max_block_adjust_y + 0;

							//Total
							$pdf->SetFont('','B',10);

							$pdf->line(Misc::AdjustXY(130, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y), Misc::AdjustXY(150, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->line(Misc::AdjustXY(151, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y), Misc::AdjustXY(175, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );

							$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->Cell(130,5, $pay_stub_entry['name'], $border, 0, 'L');
							$pdf->Cell(20,5, TTi18n::formatNumber( $pay_stub_entry['amount'] ), $border, 0, 'R');
							$pdf->Cell(25,5, TTi18n::formatNumber( $pay_stub_entry['ytd_amount'] ), $border, 0, 'R');
						}

						$block_adjust_y = $block_adjust_y + 5;
						if ( $block_adjust_y > $max_block_adjust_y ) {
							$max_block_adjust_y = $block_adjust_y;
						}

						$x++;
					}

					//Draw line to separate the two columns
					if ( $max_deductions > 2 ) {
						$pdf->Line( Misc::AdjustXY(88, $adjust_x), Misc::AdjustXY( $top_block_adjust_y-5, $adjust_y), Misc::AdjustXY(88, $adjust_x), Misc::AdjustXY( $max_block_adjust_y-5, $adjust_y) );
					}

					unset($x, $max_deductions, $tmp_adjust_x, $max_block_adjust_y, $tmp_block_adjust_y, $top_block_adjust_y);
				}

				if ( isset($pay_stub_entries[40][0]) ) {
					$block_adjust_y = $block_adjust_y + 5;

					//Net Pay entry
					$pdf->SetFont('','B',10);

					$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
					$pdf->Cell(130,5, $pay_stub_entries[40][0]['name'], $border, 0, 'L');
					$pdf->Cell(20,5, TTi18n::formatNumber( $pay_stub_entries[40][0]['amount'] ), $border, 0, 'R');
					$pdf->Cell(25,5, TTi18n::formatNumber( $pay_stub_entries[40][0]['ytd_amount'] ), $border, 0, 'R');

					$block_adjust_y = $block_adjust_y + 5;
				}

				//
				//Employer Contributions
				//
				//echo "Employee Ded: <br>\n";
				if ( isset($pay_stub_entries[30]) AND $hide_employer_rows != TRUE ) {
					$max_deductions = count($pay_stub_entries[30]);
					//echo "Max Employee Ded: ". $max_deductions ."<br>\n";
					//Deductions Header
					$block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','B',10);
					if ( $max_deductions > 2 ) {
						$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
						$pdf->Cell(40,5,TTi18n::gettext('Employer Contributions'), $border, 0, 'L');
						$pdf->Cell(20,5,TTi18n::gettext('Amount'), $border, 0, 'R');
						$pdf->Cell(25,5,TTi18n::gettext('YTD Amount'), $border, 0, 'R');

						$pdf->setXY( Misc::AdjustXY(90, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
						$pdf->Cell(40,5,TTi18n::gettext('Employer Contributions'), $border, 0, 'L');
					} else {
						$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
						$pdf->Cell(130,5,TTi18n::gettext('Employer Contributions'), $border, 0, 'L');
					}

					$pdf->Cell(20,5,TTi18n::gettext('Amount'), $border, 0, 'R');
					$pdf->Cell(25,5,TTi18n::gettext('YTD Amount'), $border, 0, 'R');

					$block_adjust_y = $tmp_block_adjust_y = $top_block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','',10);
					$x=0;
					$max_block_adjust_y = 0;

					foreach( $pay_stub_entries[30] as $pay_stub_entry ) {
						//Start with the right side.
						if ( $x < floor($max_deductions / 2) ) {
							$tmp_adjust_x = 90;
						} else {
							if ( $tmp_block_adjust_y != 0 ) {
								$block_adjust_y = $tmp_block_adjust_y;
								$tmp_block_adjust_y = 0;
							}
							$tmp_adjust_x = 0;
						}

						if ( $pay_stub_entry['type'] == 30 ) {
							if ( $pay_stub_entry['description_subscript'] != '' ) {
								$subscript = '['.$pay_stub_entry['description_subscript'].']';
							} else {
								$subscript = NULL;
							}

							if ( $max_deductions > 2 ) {
								$pdf->setXY( Misc::AdjustXY(2, $tmp_adjust_x+$adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
								$pdf->Cell(38,5, $pay_stub_entry['name'] . $subscript, $border, 0, 'L');
							} else {
								$pdf->setXY( Misc::AdjustXY(2, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
								$pdf->Cell(128,5, $pay_stub_entry['name'] . $subscript, $border, 0, 'L');
							}
							$pdf->Cell(20,5, TTi18n::formatNumber( $pay_stub_entry['amount'] ), $border, 0, 'R');
							$pdf->Cell(25,5, TTi18n::formatNumber( $pay_stub_entry['ytd_amount']), $border, 0, 'R');
						} else {
							$block_adjust_y = $max_block_adjust_y + 0;

							//Total
							$pdf->SetFont('','B',10);

							$pdf->line(Misc::AdjustXY(130, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y), Misc::AdjustXY(150, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->line(Misc::AdjustXY(151, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y), Misc::AdjustXY(175, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );

							$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->Cell(130,5, $pay_stub_entry['name'], $border, 0, 'L');
							$pdf->Cell(20,5, TTi18n::formatNumber( $pay_stub_entry['amount'] ), $border, 0, 'R');
							$pdf->Cell(25,5, TTi18n::formatNumber( $pay_stub_entry['ytd_amount'] ), $border, 0, 'R');
						}

						$block_adjust_y = $block_adjust_y + 5;
						if ( $block_adjust_y > $max_block_adjust_y ) {
							$max_block_adjust_y = $block_adjust_y;
						}

						$x++;
					}

					//Draw line to separate the two columns
					if ( $max_deductions > 2 ) {
						$pdf->Line( Misc::AdjustXY(88, $adjust_x), Misc::AdjustXY( $top_block_adjust_y-5, $adjust_y), Misc::AdjustXY(88, $adjust_x), Misc::AdjustXY( $max_block_adjust_y-5, $adjust_y) );
					}

					unset($x, $max_deductions, $tmp_adjust_x, $max_block_adjust_y, $tmp_block_adjust_y, $top_block_adjust_y);
				}

				//
				//Accruals PS accounts
				//
				if ( isset($pay_stub_entries[50]) ) {
					//Accrual Header
					$block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','B',10);
					$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
					$pdf->Cell(130,5,TTi18n::gettext('Accruals'), $border, 0, 'L');
					$pdf->Cell(20,5,TTi18n::gettext('Amount'), $border, 0, 'R');
					$pdf->Cell(25,5,TTi18n::gettext('Balance'), $border, 0, 'R');

					$block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','',10);
					foreach( $pay_stub_entries[50] as $pay_stub_entry ) {

						if ( $pay_stub_entry['type'] == 50 ) {
							if ( $pay_stub_entry['description_subscript'] != '' ) {
								$subscript = '['.$pay_stub_entry['description_subscript'].']';
							} else {
								$subscript = NULL;
							}

							$pdf->setXY( Misc::AdjustXY(2, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
							$pdf->Cell(128,5, $pay_stub_entry['name'] . $subscript, $border, 0, 'L');
							$pdf->Cell(20,5, TTi18n::formatNumber( $pay_stub_entry['amount'] ), $border, 0, 'R');
							$pdf->Cell(25,5, TTi18n::formatNumber( $pay_stub_entry['ytd_amount'] ), $border, 0, 'R');
						}

						$block_adjust_y = $block_adjust_y + 5;
					}
				}

				//
				//Accrual Policy Balances
				//
				$ablf = new AccrualBalanceListFactory();
				$ablf->getByUserIdAndCompanyIdAndEnablePayStubBalanceDisplay($user_obj->getId(), $user_obj->getCompany(), TRUE );
				if ( $ablf->getRecordCount() > 0 ) {
					//Accrual Header
					$block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','B',10);

					$pdf->setXY( Misc::AdjustXY(40, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );

					$accrual_time_header_start_x = $pdf->getX();
					$accrual_time_header_start_y = $pdf->getY();

					$pdf->Cell(70,5,TTi18n::gettext('Accrual Time Balances as of ').TTDate::getDate('DATE', time() ) , $border, 0, 'L');
					$pdf->Cell(25,5,TTi18n::gettext('Balance (hrs)'), $border, 0, 'R');

					$block_adjust_y = $block_adjust_y + 5;
					$box_height = 5;

					$pdf->SetFont('','',10);
					foreach( $ablf as $ab_obj ) {
						$balance = $ab_obj->getBalance();
						if ( !is_numeric( $balance ) ) {
							$balance = 0;
						}

						$pdf->setXY( Misc::AdjustXY(40, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
						$pdf->Cell(70,5, $ab_obj->getColumn('name'), $border, 0, 'L');
						$pdf->Cell(25,5, TTi18n::formatNumber( TTDate::getHours( $balance ) ), $border, 0, 'R');

						$block_adjust_y = $block_adjust_y + 5;
						$box_height = $box_height + 5;
						unset($balance);
					}
					$pdf->Rect( $accrual_time_header_start_x, $accrual_time_header_start_y, 95, $box_height );

					unset($accrual_time_header_start_x, $accrual_time_header_start_y, $box_height);
				}


				//
				//Descriptions
				//
				if ( isset($pay_stub_entry_descriptions) AND count($pay_stub_entry_descriptions) > 0 ) {

					//Description Header
					$block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','B',10);
					$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y) );
					$pdf->Cell(175,5,TTi18n::gettext('Notes'), $border, 0, 'L');

					$block_adjust_y = $block_adjust_y + 5;

					$pdf->SetFont('','',8);
					$x=0;
					foreach( $pay_stub_entry_descriptions as $pay_stub_entry_description ) {
						if ( $x % 2 == 0 ) {
							$pdf->setXY( Misc::AdjustXY(2, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
						} else {
							$pdf->setXY( Misc::AdjustXY(90, $adjust_x), Misc::AdjustXY( $block_adjust_y, $adjust_y) );
						}

						//$pdf->Cell(173,5, '['.$pay_stub_entry_description['subscript'].'] '.$pay_stub_entry_description['description'], $border, 0, 'L');
						$pdf->Cell(85,5, '['.$pay_stub_entry_description['subscript'].'] '.$pay_stub_entry_description['description'], $border, 0, 'L');

						if ( $x % 2 != 0 ) {
							$block_adjust_y = $block_adjust_y + 5;
						}
						$x++;
					}
				}
				unset($x, $pay_stub_entry_descriptions, $pay_stub_entry_description);

				//
				// Pay Stub Footer
				//

				$block_adjust_y = 215;
				//Line
				$pdf->setLineWidth( 1 );
				$pdf->Line( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y, $adjust_y), Misc::AdjustXY(185, $adjust_y), Misc::AdjustXY($block_adjust_y, $adjust_y) );

				//Non Negotiable
				$pdf->SetFont('','B',14);
				$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y+3, $adjust_y) );
				$pdf->Cell(175, 5, TTi18n::gettext('NON NEGOTIABLE'), $border, 0, 'C', 0);

				//Employee Address
				$pdf->SetFont('','B',12);
				$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y+9, $adjust_y) );
				$pdf->Cell(60, 5, TTi18n::gettext('CONFIDENTIAL'), $border, 0, 'C', 0);
				$pdf->SetFont('','',10);
				$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y+14, $adjust_y) );
				$pdf->Cell(60, 5, $user_obj->getFullName(), $border, 0, 'C', 0);
				$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y+19, $adjust_y) );
				$pdf->Cell(60, 5, $user_obj->getAddress1(), $border, 0, 'C', 0);
				$address2_adjust_y = 0;
				if ( $user_obj->getAddress2() != '' ) {
					$address2_adjust_y = 5;
					$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y+24, $adjust_y) );
					$pdf->Cell(60, 5, $user_obj->getAddress2(), $border, 0, 'C', 0);
				}
				$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y+24+$address2_adjust_y, $adjust_y) );
				$pdf->Cell(60, 5, $user_obj->getCity() .', '. $user_obj->getProvince() .' '. $user_obj->getPostalCode(), $border, 1, 'C', 0);

				//Pay Period - Balance - ID
				$net_pay_amount = 0;
				if ( isset($pay_stub_entries[40][0]) ) {
					$net_pay_amount = $pay_stub_entries[40][0]['amount'];
				}

				if ( isset($pay_stub_entries[65]) AND count($pay_stub_entries[65]) > 0 ) {
					$net_pay_label = TTi18n::gettext('Balance');
				} else {
					$net_pay_label = TTi18n::gettext('Net Pay');
				}

				/*
				if ( $pay_period_schedule_obj->getType() != 5 AND $pay_period_number > 0 AND $pay_period_schedule_obj->getAnnualPayPeriods() > 0 ) {
					$pdf->setXY( Misc::AdjustXY(125, $adjust_x), Misc::AdjustXY($block_adjust_y+10, $adjust_y) );
					$pdf->Cell(50, 5, TTi18n::gettext('Pay Period').' '. $pay_period_number .' '. TTi18n::gettext('of') .' '. $pay_period_schedule_obj->getAnnualPayPeriods(), $border, 1, 'L', 0);
				}
				*/

				$pdf->SetFont('','B',12);
				$pdf->setXY( Misc::AdjustXY(125, $adjust_x), Misc::AdjustXY($block_adjust_y+17, $adjust_y) );
				$pdf->Cell(50, 5, $net_pay_label.': '. $pay_stub_obj->getCurrencyObject()->getSymbol() . $net_pay_amount . ' ' . $pay_stub_obj->getCurrencyObject()->getISOCode(), $border, 1, 'L', 0);

				if ( $pay_stub_obj->getTainted() == TRUE ) {
					$tainted_flag = 'T';
				} else {
					$tainted_flag = '';
				}
				$pdf->SetFont('','',8);
				$pdf->setXY( Misc::AdjustXY(125, $adjust_x), Misc::AdjustXY($block_adjust_y+30, $adjust_y) );
				$pdf->Cell(50, 5, TTi18n::gettext('Identification #:').' '. str_pad($pay_stub_obj->getId(),12,0, STR_PAD_LEFT).$tainted_flag, $border, 1, 'L', 0);
				unset($net_pay_amount, $tainted_flag);

				//Line
				$pdf->setLineWidth( 1 );
				$pdf->Line( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y+35, $adjust_y), Misc::AdjustXY(185, $adjust_y), Misc::AdjustXY($block_adjust_y+35, $adjust_y) );

				$pdf->SetFont('','', 6);
				$pdf->setXY( Misc::AdjustXY(0, $adjust_x), Misc::AdjustXY($block_adjust_y+38, $adjust_y) );
				$pdf->Cell(175, 1, TTi18n::gettext('Pay Stub Generated by').' '. APPLICATION_NAME , $border, 0, 'C', 0);

				unset($pay_period_schedule_obj, $pay_stub_entries, $pay_period_number);

				$i++;
			}

			$output = $pdf->Output('','S');
		}

		TTi18n::setMasterLocale();

		if ( isset($output) ) {
			return $output;
		}

		return FALSE;
	}
}
?>
