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
 * $Revision: 2860 $
 * $Id: RecurringPayStubAmendmentFactory.class.php 2860 2009-09-30 00:02:16Z ipso $
 * $Date: 2009-09-29 17:02:16 -0700 (Tue, 29 Sep 2009) $
 */

/**
 * @package Module_Pay_Stub_Amendment
 */
class RecurringPayStubAmendmentFactory extends Factory {
	protected $table = 'recurring_ps_amendment';
	protected $pk_sequence_name = 'recurring_ps_amendment_id_seq'; //PK Sequence name
/*
*/


	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('INCOMPLETE'),
										20 => TTi18n::gettext('OPEN'),
										30 => TTi18n::gettext('PENDING AUTHORIZATION'),
										40 => TTi18n::gettext('AUTHORIZATION OPEN'),
										50 => TTi18n::gettext('ACTIVE'),
										55 => TTi18n::gettext('AUTHORIZATION DECLINED'),
										60 => TTi18n::gettext('DISABLED')
									);
				break;
			case 'frequency':
				$retval = array(
										10 => TTi18n::gettext('each Pay Period'),
										30 => TTi18n::gettext('Weekly'),
										40 => TTi18n::gettext('Monthly'),
										70 => TTi18n::gettext('Yearly'),

										//20 => TTi18n::gettext('every 2nd Pay Period'),
										//30 => TTi18n::gettext('twice per Pay Period'),
										//50 => TTi18n::gettext('every 2nd Month'),
										//52 => TTi18n::gettext('twice per Month'),
										//60 => TTi18n::gettext('Bi-Weekly'),
										//80 => TTi18n::gettext('Bi-Annually')
									);
				break;
			case 'percent_amount':
				$retval = array(
										10 => TTi18n::gettext('Gross Wage')
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


	function getCompany() {
		if ( isset($this->data['company_id']) ) {
			return $this->data['company_id'];
		}

		return FALSE;
	}
	function setCompany($id) {
		$id = trim($id);

		Debug::Text('Company ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$clf = new CompanyListFactory();

		if ( $this->Validator->isResultSetWithRows(	'company',
													$clf->getByID($id),
													TTi18n::gettext('Company is invalid')
													) ) {

			$this->data['company_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getStartDate() {
		return $this->data['start_date'];
	}
	function setStartDate($epoch) {
		$epoch = trim($epoch);

		//Add 12 hours to effective date, because we won't want it to be a
		//day boundary and have issues with pay period end date.
		//$epoch = TTDate::getBeginDayEpoch( $epoch ) + (43200-1);

		if 	(	$this->Validator->isDate(		'start_date',
												$epoch,
												TTi18n::gettext('Incorrect start date')) ) {

			$this->data['start_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getEndDate() {
		if ( isset($this->data['end_date']) ) {
			return $this->data['end_date'];
		}

		return FALSE;
	}
	function setEndDate($epoch) {
		$epoch = trim($epoch);

		//Add 12 hours to effective date, because we won't want it to be a
		//day boundary and have issues with pay period end date.
		if ( $epoch != '' ) {
			$epoch = TTDate::getBeginDayEpoch( $epoch ) + (43200-1);
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'end_date',
												$epoch,
												TTi18n::gettext('Incorrect end date')) ) {

			$this->data['end_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getFrequency() {
		if ( isset($this->data['frequency_id']) ) {
			return $this->data['frequency_id'];
		}

		return FALSE;
	}
	function setFrequency($status) {
		$status = trim($status);

		$key = Option::getByValue($status, $this->getOptions('frequency') );
		if ($key !== FALSE) {
			$status = $key;
		}

		if ( $this->Validator->inArrayKey(	'frequency',
											$status,
											TTi18n::gettext('Incorrect Frequency'),
											$this->getOptions('frequency')) ) {

			$this->data['frequency_id'] = $status;

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
	function setName($text) {
		$text = trim($text);

		if 	(	strlen($text) == 0
				OR
				$this->Validator->isLength(		'name',
												$text,
												TTi18n::gettext('Invalid Name Length'),
												2,
												100) ) {

			$this->data['name'] = htmlentities( $text );

			return TRUE;
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

	function getUser() {
		$rpsaulf = new RecurringPayStubAmendmentUserListFactory();
		$rpsaulf->getByRecurringPayStubAmendment( $this->getId() );
		foreach ($rpsaulf as $ps_amendment_user) {
			$user_list[] = $ps_amendment_user->getUser();
		}

		if ( isset($user_list) ) {
			return $user_list;
		}

		return FALSE;
	}
	function setUser($ids) {
		if (is_array($ids) ) {
			if ( in_array(0, $ids ) ) {
				Debug::text('All Users is selected: ', __FILE__, __LINE__, __METHOD__, 10);
				$ids = array(0);
			}

			//If needed, delete mappings first.
			$rpsaulf = new RecurringPayStubAmendmentUserListFactory();
			$rpsaulf->getByRecurringPayStubAmendment( $this->getId() );

			$user_ids = array();
			foreach ($rpsaulf as $ps_amendment_user) {
				$user_id = $ps_amendment_user->getUser();
				Debug::text('Recurring PS Amendment ID: '. $ps_amendment_user->getRecurringPayStubAmendment() .' User: '. $user_id, __FILE__, __LINE__, __METHOD__, 10);

				//Delete users that are not selected.
				if ( !in_array($user_id, $ids) ) {
					Debug::text('Deleting PS Amendment User: '. $user_id, __FILE__, __LINE__, __METHOD__, 10);
					$ps_amendment_user->Delete();
				} else {
					//Save branch ID's that need to be updated.
					Debug::text('NOT Deleting PS Amendment User: '. $user_id, __FILE__, __LINE__, __METHOD__, 10);
					$user_ids[] = $user_id;
				}
			}

			//Insert new mappings.
			$rpsauf = new RecurringPayStubAmendmentUserFactory();
			foreach ($ids as $id) {
				if ( !in_array($id, $user_ids) ) {
					$rpsauf->setRecurringPayStubAmendment( $this->getId() );
					$rpsauf->setUser( $id );

					if ($this->Validator->isTrue(		'user',
														$rpsauf->Validator->isValid(),
														TTi18n::gettext('Invalid User'))) {
						$rpsauf->save();
					}
				}
			}

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
/*
		$psenlf = new PayStubEntryNameListFactory();
		$result = $psenlf->getById( $id )->getCurrent();
*/
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

	function setPayStubEntryName($name) {
		$name = trim($name);

		$psenlf = new PayStubEntryNameListFactory();
		$result = $psenlf->getByName($name);

		if (  $this->Validator->isResultSetWithRows(	'ps_entry_name',
														$result,
														TTi18n::gettext('Invalid Entry Name')
														) ) {

			$this->data['pay_stub_entry_name_id'] = $result->getId();

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

		/*
		if ($value == NULL OR $value == '') {
			return FALSE;
		}
		*/

		if (	empty($value) OR
				$this->Validator->isFloat(				'rate',
														$value,
														TTi18n::gettext('Invalid Rate')
														) ) {
			Debug::text('Setting Rate to: '. $value, __FILE__, __LINE__, __METHOD__,10);
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

		/*
		if ($value == NULL OR $value == '') {
			return FALSE;
		}
		*/
		if (	empty($value) OR
				$this->Validator->isFloat(				'units',
														$value,
														TTi18n::gettext('Invalid Units')
														) ) {
			$this->data['units'] = $value;

			return TRUE;
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
			$this->data['amount'] = round( $value, 2);

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
			//$this->data['amount'] = number_format( $value, 2, '.', '');
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
/*
	function getPercentAmountEntryNameId() {
		if ( isset($this->data['percent_amount_entry_name_id']) ) {
			return $this->data['percent_amount_entry_name_id'];
		}

		return FALSE;
	}
	function setPercentAmountEntryNameId($status) {
		$status = trim($status);

		$key = Option::getByValue($status, $this->getOptions('percent_amount') );
		if ($key !== FALSE) {
			$status = $key;
		}

		if ( $this->Validator->inArrayKey(	'percent_amount_entry_name',
											$status,
											TTi18n::gettext('Invalid Percent Of'),
											$this->getOptions('percent_amount')) ) {

			$this->data['percent_amount_entry_name_id'] = $status;

			return FALSE;
		}

		return FALSE;
	}
*/
	function getPayStubAmendmentDescription() {
		return $this->data['ps_amendment_description'];
	}
	function setPayStubAmendmentDescription($text) {
		$text = trim($text);

		if 	(	strlen($text) == 0
				OR
				$this->Validator->isLength(		'ps_amendment_description',
												$text,
												TTi18n::gettext('Invalid Pay Stub Amendment Description Length'),
												2,
												100) ) {

			$this->data['ps_amendment_description'] = htmlentities( $text );

			return TRUE;
		}

		return FALSE;
	}

	function checkTimeFrame( $epoch = NULL ) {
		if ( $epoch == NULL ) {
			$epoch = TTDate::getTime();
		}

		//Due to Cron running late, we want to still be able to insert
		//Recurring PS amendments up to two days after the end date.
		if ( ( $this->getEndDate() == '' AND $epoch >= $this->getStartDate() )
				OR ( $this->getEndDate() != ''
					AND ( $epoch >= $this->getStartDate() AND $epoch <= ($this->getEndDate()+(86400*2)) ) ) ) {
			Debug::text('IN TimeFrame: '. TTDate::getDATE('DATE+TIME', $epoch), __FILE__, __LINE__, __METHOD__,10);
			return TRUE;
		}

		Debug::text('Not in TimeFrame: '. TTDate::getDATE('DATE+TIME', $epoch), __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}

	//function createRecurringPayStubAmendments() {
	function createPayStubAmendments($epoch = NULL) {
		//Get all recurring pay stub amendments and generate single pay stub amendments if appropriate.

		if ( $epoch == '' ) {
			$epoch = TTDate::getTime();
		}

		$ulf = new UserListFactory();

		Debug::text('Recurring PS Amendment ID: '. $this->getId() .' Frequency: '. $this->getFrequency(), __FILE__, __LINE__, __METHOD__,10);

		$this->StartTransaction();

		$tmp_user_ids = $this->getUser();
		if ( $tmp_user_ids[0] == -1) {
			$ulf->getByCompanyIdAndStatus( $this->getCompany(), 10 );
			foreach($ulf as $user_obj) {
				$user_ids[] = $user_obj->getId();
			}
			unset($user_obj);
		} else {
			$user_ids = $this->getUser();
		}
		unset($tmp_user_ids);
		Debug::text('Total User IDs: '. count($user_ids), __FILE__, __LINE__, __METHOD__,10);

		if ( is_array($user_ids) AND count($user_ids) > 0 ) {

			//Make the PS amendment duplicate check start/end date separate
			//Make the PS amendment effective date separate.
			switch( $this->getFrequency() ) {
				case 10:
					//Get all open pay periods
					$pplf = new PayPeriodListFactory();
					//FIXME: Get all non-closed pay periods AFTER the start date.
					$pplf->getByUserIdListAndNotStatusAndStartDateAndEndDate($user_ids, 20, $this->getStartDate(), $this->getEndDate() ); //All non-closed pay periods
					Debug::text('Found Open Pay Periods: '. $pplf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);
					foreach($pplf as $pay_period_obj) {
						Debug::text('Working on Pay Period: '. $pay_period_obj->getId(), __FILE__, __LINE__, __METHOD__,10);

						//If near the end of a pay period, or a pay period is already ended, add PS amendment if
						//it does not already exist.
						if ( $epoch >= $pay_period_obj->getEndDate()
								AND $this->checkTimeFrame($epoch) ) {
							Debug::text('After end of pay period.', __FILE__, __LINE__, __METHOD__,10);

							$psalf = new PayStubAmendmentListFactory();

							//Loop through each user of this Pay Period Schedule adding PS amendments if they don't already exist.
							$pay_period_schedule_users = $pay_period_obj->getPayPeriodScheduleObject()->getUser();
							Debug::text(' Pay Period Schedule Users: '. count($pay_period_schedule_users), __FILE__, __LINE__, __METHOD__,10);

							foreach( $pay_period_schedule_users as $user_id ) {
								//Make sure schedule user is in the PS amendment user list and user is active.
								Debug::text(' Pay Period Schedule User: '. $user_id, __FILE__, __LINE__, __METHOD__,10);
								//Debug::Arr($user_ids, ' Recurring PS Amendment Selected Users: ', __FILE__, __LINE__, __METHOD__,10);

								if ( $ulf->getById( $user_id )->getCurrent()->getStatus() == 10
										AND in_array( $user_id, $user_ids ) ) {

									//Check to see if the amendment was added already.
									if ( $psalf->getByUserIdAndRecurringPayStubAmendmentIdAndStartDateAndEndDate( $user_id, $this->getId(), $pay_period_obj->getStartDate(), $pay_period_obj->getEndDate() )->getRecordCount() == 0 ) {
										//No amendment, good to insert one
										Debug::text('Inserting Recurring PS Amendment for User: '. $user_id, __FILE__, __LINE__, __METHOD__,10);

										$psaf = new PayStubAmendmentFactory();
										$psaf->setUser( $user_id );
										$psaf->setStatus( 50 );

										$psaf->setType( $this->getType() );

										$psaf->setRecurringPayStubAmendmentId( $this->getId() );
										$psaf->setPayStubEntryNameId( $this->getPayStubEntryNameId() );

										if ( $this->getType() == 10 ) {
											$psaf->setRate( $this->getRate() );
											$psaf->setUnits( $this->getUnits() );
											$psaf->setAmount( $this->getAmount() );
										} else {
											$psaf->setPercentAmount( $this->getPercentAmount() );
											$psaf->setPercentAmountEntryNameID( $this->getPercentAmountEntryNameId() );
										}

										$psaf->setDescription( $this->getPayStubAmendmentDescription() );

										$psaf->setEffectiveDate( TTDate::getBeginDayEpoch( $pay_period_obj->getEndDate() ) );

										if ( $psaf->isValid() ) {
											$psaf->Save();
										}
									} else {
										//Amendment already inserted!
										Debug::text('Recurring PS Amendment already inserted for User: '. $user_id, __FILE__, __LINE__, __METHOD__,10);
									}
								} else {
									Debug::text('Skipping User because they are INACTIVE or are not on the Recurring PS Amendment User List - ID: '. $user_id, __FILE__, __LINE__, __METHOD__,10);
									//continue;

								}

							}

						} else {
							Debug::text('Not in TimeFrame, not inserting amendments: Epoch: '. $epoch .' Pay Period End Date: '. $pay_period_obj->getEndDate(), __FILE__, __LINE__, __METHOD__,10);
						}
					}
					break;
				case 30: //Weekly
				case 40: //Monthly
				case 70: //Annually
					switch ( $this->getFrequency() ) {
						case 30:
							$trigger_date = TTDate::getDateOfNextDayOfWeek( TTDate::getBeginWeekEpoch($epoch), $this->getStartDate() );

							$start_date = TTDate::getBeginWeekEpoch($epoch);
							$end_date = TTDate::getEndWeekEpoch($epoch);
							break;
						case 40:
							$trigger_date = TTDate::getDateOfNextDayOfMonth( TTDate::getBeginMonthEpoch($epoch), $this->getStartDate() );
							//$monthly_date = TTDate::getDateOfNextDayOfMonth( TTDate::getBeginMonthEpoch($epoch), $this->getStartDate() );

							$start_date = TTDate::getBeginMonthEpoch($epoch);
							$end_date = TTDate::getEndMonthEpoch($epoch);
							break;
						case 70:
							$trigger_date = TTDate::getDateOfNextYear( $this->getStartDate() );

							$start_date = TTDate::getBeginYearEpoch($epoch);
							$end_date = TTDate::getEndYearEpoch($epoch);
							break;
					}
					Debug::text('Trigger Date: '. TTDate::getDate('DATE', $trigger_date), __FILE__, __LINE__, __METHOD__,10);

					if ( $epoch >= $trigger_date
							AND $this->checkTimeFrame($epoch) ) {
							Debug::text('After end of pay period.', __FILE__, __LINE__, __METHOD__,10);

						foreach( $user_ids as $user_id ) {
							//Make sure schedule user is in the PS amendment user list and user is active.
							if ( $ulf->getById( $user_id )->getCurrent()->getStatus() != 10
									AND !in_array( $user_id, $user_ids ) ) {
								Debug::text('Skipping User because they are INACTIVE or are not on the Recurring PS Amendment User List - ID: '. $user_id, __FILE__, __LINE__, __METHOD__,10);
								continue;
							}

							$psalf = new PayStubAmendmentListFactory();
							if ( $psalf->getByUserIdAndRecurringPayStubAmendmentIdAndStartDateAndEndDate( $user_id, $this->getId(), $start_date, $end_date )->getRecordCount() == 0 ) {
								//No amendment, good to insert one
								Debug::text('Inserting Recurring PS Amendment for User: '. $user_id, __FILE__, __LINE__, __METHOD__,10);

								$psaf = new PayStubAmendmentFactory();
								$psaf->setUser( $user_id );
								$psaf->setStatus( 50 );

								$psaf->setType( $this->getType() );

								$psaf->setRecurringPayStubAmendmentId( $this->getId() );
								$psaf->setPayStubEntryNameId( $this->getPayStubEntryNameId() );

								if ( $this->getType() == 10 ) {
									$psaf->setRate( $this->getRate() );
									$psaf->setUnits( $this->getUnits() );
									$psaf->setAmount( $this->getAmount() );
								} else {
									$psaf->setPercentAmount( $this->getPercentAmount() );
									$psaf->setPercentAmountEntryNameID( $this->getPercentAmountEntryNameId() );
								}

								$psaf->setDescription( $this->getDescription() );

								$psaf->setEffectiveDate( TTDate::getBeginDayEpoch( $trigger_date ) );

								if ( $psaf->isValid() ) {
									$psaf->Save();
								}
							} else {
								//Amendment already inserted!
								Debug::text('Recurring PS Amendment already inserted for User: '. $user_id, __FILE__, __LINE__, __METHOD__,10);
							}
						}
					}

					break;
			}
		}

		//$this->FailTransaction();
		$this->CommitTransaction();

		return TRUE;
	}

	function Validate() {
		//If amount is set, make sure percent is cleared.
		if ( $this->getAmount() != '' AND $this->getPercentAmount() != '' ) {
			$this->Validator->isTrue(		'amount',
											FALSE,
											TTi18n::gettext('Fixed Amount and Percent cannot both be entered'));
		}

		if ( $this->getType() == 10 ) {
			//If rate and units are set, and not amount, calculate the amount for us.
			if ( $this->getRate() !== NULL AND $this->getUnits() !== NULL AND $this->getAmount() == NULL ) {
				$this->preSave();
			}

			//Make sure amount is sane given the rate and units.
			if ( $this->getRate() !== NULL AND $this->getUnits() !== NULL
					AND $this->getRate() != 0 AND $this->getUnits() != 0
					AND $this->getRate() != '' AND $this->getUnits() != ''
					AND ( round( $this->getRate() * $this->getUnits(),2 ) ) != round( $this->getAmount(), 2) ) {
				Debug::text('Validate: Rate: '. $this->getRate() .' Units: '. $this->getUnits() .' Amount: '. $this->getAmount() .' Calc: Rate: '. $this->getRate() .' Units: '. $this->getUnits() .' Total: '. ( $this->getRate() * $this->getUnits() ), __FILE__, __LINE__, __METHOD__,10);
				$this->Validator->isTrue(		'amount',
												FALSE,
												TTi18n::gettext('Invalid Amount, calculation is incorrect'));
			}
		}

		//Make sure rate * units = amount
		if ( $this->getPercentAmount() == NULL AND $this->getAmount() === NULL ) {
			$this->Validator->isTrue(		'amount',
											FALSE,
											TTi18n::gettext('Invalid Amount'));
		}

		return TRUE;
	}

	function preSave() {
		/*
		if ( $this->getAmount() != '' ) {
			$this->setPercentAmount(0);
		}

		if ( $this->getPercentAmount() != '' ) {
			$this->setAmount(0);
		}
		*/

		if ( $this->getFrequency() == 40 ) {
			if ( TTDate::getDayOfMonth( $this->getStartDate() ) > 28 ) {
				Debug::text(' Start Date is After the 28th, making the 28th: ', __FILE__, __LINE__, __METHOD__,10);
				$this->setStartDate( TTDate::getDateOfNextDayOfMonth( $this->getStartDate(), strtotime('28-Feb-05') ) );
			}
		}

		if ( $this->getType() == 10 ) {
			//If amount isn't set, but Rate and units are, calc amount for them.
			if ( ( $this->getAmount() == NULL OR $this->getAmount() == 0 OR $this->getAmount() == '' )
					AND $this->getRate() !== NULL AND $this->getUnits() !== NULL
					AND $this->getRate() != 0 AND $this->getUnits() != 0
					AND $this->getRate() != '' AND $this->getUnits() != ''
					) {
				$this->setAmount( $this->getRate() * $this->getUnits() );
			}
		}


		if ( $this->isNew() == TRUE ) {
			$this->first_insert = TRUE;
		}

		return TRUE;
	}

	function postSave() {
		if ( isset($this->first_insert) AND $this->first_insert == TRUE ) {
			Debug::text('First Insert... Creating PS amendments', __FILE__, __LINE__, __METHOD__,10);
			//Immediately generate PS amendments
			$this->createPayStubAmendments();
		}
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Recurring Pay Stub Amendment'), NULL, $this->getTable() );
	}
}
?>
