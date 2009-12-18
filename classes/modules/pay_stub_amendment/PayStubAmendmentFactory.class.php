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
 * $Revision: 2896 $
 * $Id: PayStubAmendmentFactory.class.php 2896 2009-10-13 20:53:33Z ipso $
 * $Date: 2009-10-13 13:53:33 -0700 (Tue, 13 Oct 2009) $
 */

/**
 * @package Module_Pay_Stub_Amendment
 */
class PayStubAmendmentFactory extends Factory {
	protected $table = 'pay_stub_amendment';
	protected $pk_sequence_name = 'pay_stub_amendment_id_seq'; //PK Sequence name

	var $user_obj = NULL;
	var $pay_stub_entry_account_link_obj = NULL;
	var $pay_stub_entry_name_obj = NULL;
	var $percent_amount_entry_name_obj = NULL;


	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('NEW'),
										20 => TTi18n::gettext('OPEN'),
										30 => TTi18n::gettext('PENDING AUTHORIZATION'),
										40 => TTi18n::gettext('AUTHORIZATION OPEN'),
										50 => TTi18n::gettext('ACTIVE'),
										52 => TTi18n::gettext('IN USE'),
										55 => TTi18n::gettext('PAID'),
										60 => TTi18n::gettext('DISABLED')
									);
				break;
			case 'type':
				$retval = array(
											10 => TTi18n::gettext('Fixed'),
											20 => TTi18n::gettext('Percent')
										);
				break;

		}

		return $retval;
	}


	function getUserObject() {
		if ( is_object($this->user_obj) ) {
			return $this->user_obj;
		} else {
			$ulf = new UserListFactory();
			$this->user_obj = $ulf->getById( $this->getUser() )->getCurrent();

			return $this->user_obj;
		}
	}

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

	function getPayStubEntryNameObject() {
		if ( is_object($this->pay_stub_entry_name_obj) ) {
			return $this->pay_stub_entry_name_obj;
		} else {
			$psealf = new PayStubEntryAccountListFactory();
			$psealf->getByID( $this->getPayStubEntryNameId() );
			if ( $psealf->getRecordCount() > 0 ) {
				$this->pay_stub_entry_name_obj = $psealf->getCurrent();
				return $this->pay_stub_entry_name_obj;
			}

			return FALSE;
		}
	}

	function getPercentAmountEntryNameObject() {
		if ( is_object($this->percent_amount_entry_name_obj) ) {
			return $this->percent_amount_entry_name_obj;
		} else {
			$psealf = new PayStubEntryAccountListFactory();
			$psealf->getByID( $this->getPercentAmountEntryNameId() );
			if ( $psealf->getRecordCount() > 0 ) {
				$this->percent_amount_entry_name_obj = $psealf->getCurrent();
				return $this->percent_amount_entry_name_obj;
			}

			return FALSE;
		}
	}

	function getUser() {
		return $this->data['user_id'];
	}
	function setUser($id) {
		$id = trim($id);

		$ulf = new UserListFactory();

		if ( $this->Validator->isResultSetWithRows(	'user',
															$ulf->getByID($id),
															TTi18n::gettext('Invalid Employee')
															) ) {
			$this->data['user_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPayStubEntryNameId() {
		if ( isset($this->data['pay_stub_entry_name_id']) ) {
			return (int)$this->data['pay_stub_entry_name_id'];
		}

		return FALSE;
	}
	function setPayStubEntryNameId($id) {
		$id = trim($id);

		//$psenlf = new PayStubEntryNameListFactory();
		$psealf = new PayStubEntryAccountListFactory();
		$result = $psealf->getById( $id )->getCurrent();

		if (  $this->Validator->isResultSetWithRows(	'pay_stub_entry_name_id',
														$result,
														TTi18n::gettext('Invalid Type')
														) ) {

			$this->data['pay_stub_entry_name_id'] = $result->getId();

			return TRUE;
		}

		return FALSE;
	}

	function setName($name) {
		$name = trim($name);

		$psenlf = new PayStubEntryNameListFactory();
		$result = $psenlf->getByName($name);

		if (  $this->Validator->isResultSetWithRows(	'name',
														$result,
														TTi18n::gettext('Invalid Entry Name')
														) ) {

			$this->data['pay_stub_entry_name_id'] = $result->getId();

			return TRUE;
		}

		return FALSE;
	}

	function getRecurringPayStubAmendmentId() {
		if ( isset($this->data['recurring_ps_amendment_id']) ) {
			return (int)$this->data['recurring_ps_amendment_id'];
		}

		return FALSE;
	}
	function setRecurringPayStubAmendmentId($id) {
		$id = trim($id);

		$rpsalf = new RecurringPayStubAmendmentListFactory();
		$result = $rpsalf->getById( $id )->getCurrent();

		if (	$id == NULL
				OR
				$this->Validator->isResultSetWithRows(	'recurring_ps_amendment_id',
														$result,
														TTi18n::gettext('Invalid Recurring Pay Stub Amendment ID')
														) ) {

			$this->data['recurring_ps_amendment_id'] = $result->getId();

			return TRUE;
		}

		return FALSE;
	}

	function getEffectiveDate() {
		return $this->data['effective_date'];
	}
	function setEffectiveDate($epoch) {
		$epoch = trim($epoch);

		//Adjust effective date, because we won't want it to be a
		//day boundary and have issues with pay period start/end dates.
		//Although with employees in timezones that differ from the pay period timezones, there can still be issues.
		$epoch = TTDate::getMiddleDayEpoch( $epoch );

		if 	(	$this->Validator->isDate(		'effective_date',
												$epoch,
												TTi18n::gettext('Incorrect effective date')) ) {

			$this->data['effective_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getStatus() {
		if ( isset($this->data['status_id']) ) {
			return $this->data['status_id'];
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

	function getType() {
		if ( isset($this->data['type_id']) ) {
			return $this->data['type_id'];
		}

		return FALSE;
	}
	function setType($type) {
		$type = trim($type);

		$key = Option::getByValue($type, $this->getOptions('type') );
		if ($key !== FALSE) {
			$type = $key;
		}

		if ( $this->Validator->inArrayKey(	'type',
											$type,
											TTi18n::gettext('Incorrect Type'),
											$this->getOptions('type')) ) {

			$this->data['type_id'] = $type;

			return FALSE;
		}

		return FALSE;
	}

	function getRate() {
		if ( isset($this->data['rate']) ) {
			return $this->data['rate'];
		}

		return NULL;
	}
	function setRate($value) {
		$value = trim($value);

		if ($value == 0 OR $value == '') {
			$value = NULL;
		}

		if (	empty($value) OR
				$this->Validator->isFloat(				'rate',
														$value,
														TTi18n::gettext('Invalid Rate')
														) ) {
			Debug::text('Setting Rate to: '. $value, __FILE__, __LINE__, __METHOD__,10);
			//Must round to 2 decimals otherwise discreptancy can occur when generating pay stubs.
			//$this->data['rate'] = Misc::MoneyFormat( $value, FALSE );
			$this->data['rate'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getUnits() {
		if ( isset($this->data['units']) ) {
			return $this->data['units'];
		}

		return NULL;
	}
	function setUnits($value) {
		$value = trim($value);

		if ($value == 0 OR $value == '') {
			$value = NULL;
		}

		if (	empty($value) OR
				$this->Validator->isFloat(				'units',
														$value,
														TTi18n::gettext('Invalid Units')
														) ) {
			//Must round to 2 decimals otherwise discreptancy can occur when generating pay stubs.
			//$this->data['units'] = Misc::MoneyFormat( $value, FALSE );
			$this->data['units'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getPayStubId() {
		//Find which pay period this effective date belongs too
		$pplf = new PayPeriodListFactory();
		$pplf->getByUserIdAndEndDate( $this->getUser(), $this->getEffectiveDate() );
		if ( $pplf->getRecordCount() > 0 ) {
			$pp_obj = $pplf->getCurrent();
			Debug::text('Found Pay Period ID: '. $pp_obj->getId(), __FILE__, __LINE__, __METHOD__,10);

			//Percent PS amendments can't work on advances.
			$pslf = new PayStubListFactory();
			$pslf->getByUserIdAndPayPeriodIdAndAdvance( $this->getUser(), $pp_obj->getId(), FALSE );
			if ( $pslf->getRecordCount() > 0 ) {
				$ps_obj = $pslf->getCurrent();
				Debug::text('Found Pay Stub for this effective date: '. $ps_obj->getId(), __FILE__, __LINE__, __METHOD__,10);

				return $ps_obj->getId();
			}
		}

		return FALSE;
	}

	function getPayStubEntryAmountSum( $pay_stub_obj, $ids ) {
		if ( !is_object($pay_stub_obj) ) {
			return FALSE;
		}

		if ( !is_array($ids) ) {
			return FALSE;
		}

		$pself = new PayStubEntryListFactory();

		//Get Linked accounts so we know which IDs are totals.
		$total_gross_key = array_search( $this->getPayStubEntryAccountLinkObject()->getTotalGross(), $ids);
		if ( $total_gross_key !== FALSE ) {
			$type_ids[] = 10;
			$type_ids[] = 60; //Automatically inlcude Advance Earnings here?
			unset($ids[$total_gross_key]);
		}
		unset($total_gross_key);

		$total_employee_deduction_key = array_search( $this->getPayStubEntryAccountLinkObject()->getTotalEmployeeDeduction(), $ids);
		if ( $total_employee_deduction_key !== FALSE ) {
			$type_ids[] = 20;
			unset($ids[$total_employee_deduction_key]);
		}
		unset($total_employee_deduction_key);

		$total_employer_deduction_key = array_search( $this->getPayStubEntryAccountLinkObject()->getTotalEmployerDeduction(), $ids);
		if ( $total_employer_deduction_key !== FALSE ) {
			$type_ids[] = 30;
			unset($ids[$total_employer_deduction_key]);
		}
		unset($total_employer_deduction_key);

		$type_amount_arr['amount'] = 0;
		if ( isset($type_ids) ) {
			//$type_amount_arr = $pself->getSumByPayStubIdAndType( $pay_stub_id, $type_ids );
			$type_amount_arr = $pay_stub_obj->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current', $type_ids );
		}

		$amount_arr['amount'] = 0;
		if ( count($ids) > 0 ) {
			//Still other IDs left to total.
			//$amount_arr = $pself->getAmountSumByPayStubIdAndEntryNameID( $pay_stub_id, $ids );
			$amount_arr = $pay_stub_obj->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'current', NULL, $ids );
		}

		$retval = bcadd($type_amount_arr['amount'], $amount_arr['amount'] );

		Debug::text('Type Amount: '. $type_amount_arr['amount'] .' Regular Amount: '. $amount_arr['amount'] .' Total: '. $retval, __FILE__, __LINE__, __METHOD__, 10);

		return $retval;
	}

	function getCalculatedAmount( $pay_stub_obj ) {
		if ( !is_object($pay_stub_obj) ) {
			return FALSE;
		}

		if ( $this->getType() == 10 ) {
			//Fixed
			return $this->getAmount();
		} else {
			//Percent
			if ( $this->getPercentAmountEntryNameId() != '' ) {
				$ps_amendment_percent_amount = $this->getPayStubEntryAmountSum( $pay_stub_obj, array($this->getPercentAmountEntryNameId()) );

				$pay_stub_entry_account = $pay_stub_obj->getPayStubEntryAccountArray( $this->getPercentAmountEntryNameId() );
				if ( isset($pay_stub_entry_account['type_id']) AND $pay_stub_entry_account['type_id'] == 50 ) {
					//Get balance amount from previous pay stub so we can include that in our percent calculation.
					$previous_pay_stub_amount_arr = $pay_stub_obj->getSumByEntriesArrayAndTypeIDAndPayStubAccountID( 'previous', NULL, array($this->getPercentAmountEntryNameId()) );

					$ps_amendment_percent_amount = bcadd( $ps_amendment_percent_amount, $previous_pay_stub_amount_arr['ytd_amount']);
					Debug::text('Pay Stub Amendment is a Percent of an Accrual, add previous pay stub accrual balance to amount: '. $previous_pay_stub_amount_arr['ytd_amount'], __FILE__, __LINE__, __METHOD__,10);
				}
				unset($pay_stub_entry_account, $previous_pay_stub_amount_arr);

				Debug::text('Pay Stub Amendment Total Amount: '. $ps_amendment_percent_amount .' Percent Amount: '. $this->getPercentAmount(), __FILE__, __LINE__, __METHOD__,10);
				if ( $ps_amendment_percent_amount != 0 AND $this->getPercentAmount() != 0 ) { //Allow negative values.
					$amount = bcmul($ps_amendment_percent_amount, bcdiv($this->getPercentAmount(), 100) );

					return $amount;
				}
			}
		}

		return FALSE;
	}

	function getAmount() {
		if ( isset($this->data['amount']) ) {
			return $this->data['amount'];
		}

		return NULL;
	}
	function setAmount($value) {
		$value = trim($value);

		Debug::text('Amount: '. $value .' Name: '. $this->getPayStubEntryNameId() , __FILE__, __LINE__, __METHOD__,10);

		if ($value == NULL OR $value == '') {
			return FALSE;
		}

		if (  $this->Validator->isFloat(				'amount',
														$value,
														TTi18n::gettext('Invalid Amount')
														) ) {
			//$this->data['amount'] = number_format( $value, 2, '.', '');
			$this->data['amount'] = Misc::MoneyFormat( $value, FALSE );

			return TRUE;
		}
		return FALSE;
	}

	function getPercentAmount() {
		if ( isset($this->data['percent_amount']) ) {
			return $this->data['percent_amount'];
		}

		return NULL;
	}
	function setPercentAmount($value) {
		$value = trim($value);

		Debug::text('Amount: '. $value .' Name: '. $this->getPayStubEntryNameId() , __FILE__, __LINE__, __METHOD__,10);

		if ($value == NULL OR $value == '') {
			return FALSE;
		}

		if (  $this->Validator->isFloat(				'percent_amount',
														$value,
														TTi18n::gettext('Invalid Percent')
														) ) {
			$this->data['percent_amount'] = round( $value, 2);

			return TRUE;
		}
		return FALSE;
	}

	function getPercentAmountEntryNameId() {
		if ( isset($this->data['percent_amount_entry_name_id']) ) {
			return $this->data['percent_amount_entry_name_id'];
		}

		return FALSE;
	}
	function setPercentAmountEntryNameId($id) {
		$id = trim($id);
/*
		$psenlf = new PayStubEntryNameListFactory();
		$result = $psenlf->getById( $id )->getCurrent();
*/
		$psealf = new PayStubEntryAccountListFactory();
		$result = $psealf->getById( $id )->getCurrent();

		if (  $this->Validator->isResultSetWithRows(	'percent_amount_entry_name',
														$result,
														TTi18n::gettext('Invalid Percent Of')
														) ) {

			$this->data['percent_amount_entry_name_id'] = $id;

			return FALSE;
		}

		return FALSE;
	}

	function getDescription() {
		if ( isset($this->data['description']) ) {
			return $this->data['description'];
		}

		return FALSE;
	}
	function setDescription($text) {
		$text = trim($text);

		if 	(	strlen($text) == 0
				OR
				$this->Validator->isLength(		'description',
												$text,
												TTi18n::gettext('Invalid Description Length'),
												2,
												100) ) {

			$this->data['description'] = htmlentities( $text );

			return TRUE;
		}

		return FALSE;
	}

	function getAuthorized() {
		if ( isset($this->data['authorized']) ) {
			return $this->fromBool( $this->data['authorized'] );
		}

		return FALSE;
	}
	function setAuthorized($bool) {
		$this->data['authorized'] = $this->toBool($bool);

		return true;
	}

	function getYTDAdjustment() {
		if ( isset($this->data['ytd_adjustment']) ) {
			return $this->fromBool( $this->data['ytd_adjustment'] );
		}

		return FALSE;
	}
	function setYTDAdjustment($bool) {
		$this->data['ytd_adjustment'] = $this->toBool($bool);

		return true;
	}

	static function releaseAllAccruals($user_id, $effective_date = NULL) {
		Debug::Text('Release 100% of all accruals!', __FILE__, __LINE__, __METHOD__,10);

		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $effective_date == '' ) {
			$effective_date = TTDate::getTime();
		}
		Debug::Text('Effective Date: '. TTDate::getDate('DATE+TIME', $effective_date), __FILE__, __LINE__, __METHOD__,10);

		$ulf = new UserListFactory();
		$ulf->getById( $user_id );
		if ( $ulf->getRecordCount() > 0 ) {
			$user_obj = $ulf->getCurrent();
		} else {
			return FALSE;
		}

		//Get all PSE acccount accruals
		$psealf = new PayStubEntryAccountListFactory();
		$psealf->getByCompanyIdAndStatusIdAndTypeId( $user_obj->getCompany(), 10, 50);
		if ( $psealf->getRecordCount() > 0 ) {
			$ulf->StartTransaction();
			foreach( $psealf as $psea_obj ) {
				//Get PSE account that affects this accrual.
				$psealf_tmp = new PayStubEntryAccountListFactory();
				$psealf_tmp->getByCompanyIdAndAccrualId( $user_obj->getCompany(), $psea_obj->getId() );
				if ( $psealf_tmp->getRecordCount() > 0 ) {
					$release_account_id = $psealf_tmp->getCurrent()->getId();

					$psaf = new PayStubAmendmentFactory();
					$psaf->setStatus( 50 ); //Active
					$psaf->setType( 20 ) ; //Percent
					$psaf->setUser( $user_obj->getId() );
					$psaf->setPayStubEntryNameId( $release_account_id );
					$psaf->setPercentAmount(100);
					$psaf->setPercentAmountEntryNameId( $psea_obj->getId() );
					$psaf->setEffectiveDate( $effective_date );
					$psaf->setDescription('Release Accrual Balance');

					if ( $psaf->isValid() ) {
						Debug::Text('Release Accrual Is Valid!!: ', __FILE__, __LINE__, __METHOD__,10);
						$psaf->Save();
					}
				} else {
					Debug::Text('No Release Account for this Accrual!!', __FILE__, __LINE__, __METHOD__,10);
				}
			}

			//$ulf->FailTransaction();
			$ulf->CommitTransaction();
		} else {
			Debug::Text('No Accruals to release...', __FILE__, __LINE__, __METHOD__,10);
		}

		return FALSE;
	}

	function preSave() {
		//Authorize all pay stub amendments until we decide they will actually go through an authorization process
		if ( $this->getAuthorized() == FALSE ) {
			$this->setAuthorized(TRUE);
		}

		/*
		//Handle YTD adjustments just like any other amendment.
		if ( $this->getYTDAdjustment() == TRUE
				AND $this->getStatus() != 55
				AND $this->getStatus() != 60) {
			Debug::Text('Calculating Amount...', __FILE__, __LINE__, __METHOD__,10);
			$this->setStatus( 52 );
		}
		*/

		//If amount isn't set, but Rate and units are, calc amount for them.
		if ( ( $this->getAmount() == NULL OR $this->getAmount() == 0 OR $this->getAmount() == '' )
				AND $this->getRate() !== NULL AND $this->getUnits() !== NULL
				AND $this->getRate() != 0 AND $this->getUnits() != 0
				AND $this->getRate() != '' AND $this->getUnits() != ''
				) {
			Debug::Text('Calculating Amount...', __FILE__, __LINE__, __METHOD__,10);
			$this->setAmount( bcmul( $this->getRate(), $this->getUnits() ) );
		}

		return TRUE;
	}

	function Validate() {
		if ( $this->getType() == 10 ) {
			//If rate and units are set, and not amount, calculate the amount for us.
			if ( $this->getRate() !== NULL AND $this->getUnits() !== NULL AND $this->getAmount() == NULL ) {
				$this->preSave();
			}

			//Make sure rate * units = amount
			if ( $this->getAmount() === NULL ) {
				Debug::Text('Amount is NULL...', __FILE__, __LINE__, __METHOD__,10);
				$this->Validator->isTrue(		'amount',
												FALSE,
												TTi18n::gettext('Invalid Amount'));
			}

			//Make sure amount is sane given the rate and units.
			if ( $this->getRate() !== NULL AND $this->getUnits() !== NULL
					AND $this->getRate() != 0 AND $this->getUnits() != 0
					AND $this->getRate() != '' AND $this->getUnits() != ''
					AND ( Misc::MoneyFormat( bcmul( $this->getRate(), $this->getUnits() ), FALSE) ) != Misc::MoneyFormat( $this->getAmount(), FALSE ) ) {
				Debug::text('Validate: Rate: '. $this->getRate() .' Units: '. $this->getUnits() .' Amount: '. $this->getAmount() .' Calc: Rate: '. $this->getRate() .' Units: '. $this->getUnits() .' Total: '. Misc::MoneyFormat( bcmul( $this->getRate(), $this->getUnits() ), FALSE), __FILE__, __LINE__, __METHOD__,10);
				$this->Validator->isTrue(		'amount',
												FALSE,
												TTi18n::gettext('Invalid Amount, calculation is incorrect'));
			}
		} else {

		}

		//FIXME: Make sure effective date isn't in a CLOSED pay period?

		return TRUE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Pay Stub Amendment - User ID').': '. $this->getUser() .' '.  TTi18n::getText('Amount').': '. $this->getAmount(), NULL, $this->getTable() );
	}
}
?>
