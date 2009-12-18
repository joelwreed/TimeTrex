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
 * $Revision: 3091 $
 * $Id: PayPeriodFactory.class.php 3091 2009-11-18 18:00:31Z ipso $
 * $Date: 2009-11-18 10:00:31 -0800 (Wed, 18 Nov 2009) $
 */

/**
 * @package Module_PayPeriod
 */
class PayPeriodFactory extends Factory {
	protected $table = 'pay_period';
	protected $pk_sequence_name = 'pay_period_id_seq'; //PK Sequence name

	var $pay_period_schedule_obj = NULL;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('OPEN'),
										12 => TTi18n::gettext('Locked - Pending Approval'), //Go to this state as soon as date2 is passed
										15 => TTi18n::gettext('Locked - Pending Transaction'), //Go to this as soon as approved, or 48hrs before transaction date.
										20 => TTi18n::gettext('CLOSED'), //Once paid
										30 => TTi18n::gettext('Post Adjustment')
									);
				break;
			case 'columns':
				$retval = array(
										'-1010-type' => TTi18n::gettext('Type'),
										'-1020-status' => TTi18n::gettext('Status'),
										'-1030-pay_period_schedule' => TTi18n::gettext('Pay Period Schedule'),

										'-1040-start_date' => TTi18n::gettext('Start Date'),
										'-1050-end_date' => TTi18n::gettext('End Date'),
										'-1060-transaction_date' => TTi18n::gettext('Transaction Date'),

										'-2000-created_by' => TTi18n::gettext('Created By'),
										'-2010-created_date' => TTi18n::gettext('Created Date'),
										'-2020-updated_by' => TTi18n::gettext('Updated By'),
										'-2030-updated_date' => TTi18n::gettext('Updated Date'),
							);
				break;
			case 'default_display_columns': //Columns that are displayed by default.
				$retval = array(
								'pay_period_schedule',
								'type',
								'status',
								'start_date',
								'end_date',
								'transaction_date'
								);
				break;
			case 'unique_columns': //Columns that are unique, and disabled for mass editing.
				$retval = array(
								);
				break;
			case 'linked_columns': //Columns that are linked together, mainly for Mass Edit, if one changes, they all must.
				$retval = array(
								);
				break;

		}

		return $retval;
	}

	function _getVariableToFunctionMap() {
			$variable_function_map = array(
											'id' => 'ID',
											'company_id' => 'Company',
											'status_id' => 'Status',
											'status' => FALSE,
											'type_id' => FALSE,
											'type' => FALSE,
											'pay_period_schedule_id' => 'PayPeriodSchedule',
											'pay_period_schedule' => FALSE,
											'start_date' => 'StartDate',
											'end_date' => 'EndDate',
											'transaction_date' => 'TransactionDate',
											//'advance_transaction_date' => 'AdvanceTransactionDate',
											//'advance_transaction_date' => 'Primary',
											//'is_primary' => 'PayStubStatus',
											//'tainted' => 'Tainted',
											//'tainted_date' => 'TaintedDate',
											//'tainted_by' => 'TaintedBy',
											'deleted' => 'Deleted',
											);
			return $variable_function_map;
	}

	function getPayPeriodScheduleObject() {
		if ( is_object($this->pay_period_schedule_obj) ) {
			return $this->pay_period_schedule_obj;
		} else {
			$ppslf = new PayPeriodScheduleListFactory();
			//$this->pay_period_schedule_obj = $ppslf->getById( $this->getPayPeriodSchedule() )->getCurrent();
			$ppslf->getById( $this->getPayPeriodSchedule() );
			if ( $ppslf->getRecordCount() > 0 ) {
				$this->pay_period_schedule_obj = $ppslf->getCurrent();
				return $this->pay_period_schedule_obj;
			}

			return FALSE;

		}
	}

	function getCompany() {
		return $this->data['company_id'];
	}
	function setCompany($id) {
		$id = trim($id);

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

	function getPayPeriodSchedule() {
		if ( isset($this->data['pay_period_schedule_id']) ) {
			return $this->data['pay_period_schedule_id'];
		}

		return FALSE;
	}
	function setPayPeriodSchedule($id) {
		$id = trim($id);

		$ppslf = new PayPeriodScheduleListFactory();

		if ( $id == 0
				OR $this->Validator->isResultSetWithRows(	'pay_period_schedule',
															$ppslf->getByID($id),
															TTi18n::gettext('Incorrect Pay Period Schedule')
															) ) {
			$this->data['pay_period_schedule_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function isValidStartDate($epoch) {
		if ( $this->isNew() ) {
			$id = 0;
		} else {
			$id = $this->getId();
		}

		$ph = array(
					'pay_period_schedule_id' => $this->getPayPeriodSchedule(),
					'start_date' => $this->db->BindTimeStamp($epoch),
					'end_date' => $this->db->BindTimeStamp($epoch),
					'id' => $id,
					);

		//Used to have LIMIT 1 at the end, but GetOne() should do that for us.
		$query = 'select id from '. $this->getTable() .'
					where	pay_period_schedule_id = ?
						AND start_date <= ?
						AND end_date >= ?
						AND deleted=0
						AND id != ?
					';
		$id = $this->db->GetOne($query, $ph);
		Debug::Arr($id,'Pay Period ID of conflicting pay period: '. $epoch, __FILE__, __LINE__, __METHOD__,10);

		if ( $id === FALSE ) {
			Debug::Text('aReturning TRUE!', __FILE__, __LINE__, __METHOD__,10);
			return TRUE;
		} else {
			if ($id == $this->getId() ) {
				Debug::Text('bReturning TRUE!', __FILE__, __LINE__, __METHOD__,10);
				return TRUE;
			}
		}

		Debug::Text('Returning FALSE!', __FILE__, __LINE__, __METHOD__,10);
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
			$this->data['start_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getEndDate( $raw = FALSE ) {
		if ( isset($this->data['end_date']) ) {
			if ( $raw === TRUE ) {
				return $this->data['end_date'];
			} else {
				return TTDate::strtotime( $this->data['end_date'] );
			}
		}

		return FALSE;
	}
	function setEndDate($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'end_date',
												$epoch,
												TTi18n::gettext('Incorrect end date')) ) {

			//$this->data['end_date'] = $epoch;
			$this->data['end_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getTransactionDate( $raw = FALSE ) {
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
												TTi18n::gettext('Incorrect transaction date')) ) {

			$this->data['transaction_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getAdvanceEndDate( $raw = FALSE ) {
		if ( isset($this->data['advance_end_date']) ) {
			return TTDate::strtotime($this->data['advance_end_date']);
		}

		return FALSE;
	}
	function setAdvanceEndDate($epoch) {
		$epoch = trim($epoch);

		if 	(	$epoch == FALSE
				OR
				$this->Validator->isDate(		'advance_end_date',
												$epoch,
												TTi18n::gettext('Incorrect advance end date')) ) {

			$this->data['advance_end_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getAdvanceTransactionDate() {
		if ( isset($this->data['advance_transaction_date']) ) {
			return TTDate::strtotime($this->data['advance_transaction_date']);
			/*
			if ( (int)$this->data['advance_transaction_date'] == 0 ) {
				return strtotime( $this->data['advance_transaction_date'] );
			} else {
				return $this->data['advance_transaction_date'];
			}
			*/
		}

		return FALSE;
	}
	function setAdvanceTransactionDate($epoch) {
		$epoch = trim($epoch);

		if 	(	$epoch == FALSE
				OR
				$this->Validator->isDate(		'advance_transaction_date',
												$epoch,
												TTi18n::gettext('Incorrect advance transaction date')) ) {

			$this->data['advance_transaction_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getPrimary() {
		return $this->fromBool( $this->data['is_primary'] );
	}
	function setPrimary($bool) {
		$this->data['is_primary'] = $this->toBool($bool);

		return true;
	}

	function setPayStubStatus($status) {
		Debug::text('setPayStubStatus: '. $status, __FILE__, __LINE__, __METHOD__, 10);

		$this->StartTransaction();

		$pslf = new PayStubListFactory();
		$pslf->getByPayPeriodId( $this->getId() );
		foreach($pslf as $pay_stub) {
			//Only change status of advance pay stubs if we're in the advance part of the pay period.
			//What if the person is too late, set status anyways?
			if ( $pay_stub->getStatus() != $status
					/*
					AND (
							(
							$this->getPayPeriodScheduleObject()->getType() == 40
								AND TTDate::getTime() < $this->getAdvanceTransactionDate()
								AND $pay_stub->getAdvance() == TRUE
							)
						OR
							$pay_stub->getAdvance() == FALSE
						)
					*/
				) {

				Debug::text('Changing Status of Pay Stub ID: '. $pay_stub->getId(), __FILE__, __LINE__, __METHOD__, 10);
				$pay_stub->setStatus($status);
				$pay_stub->save();
			}
		}

		$this->CommitTransaction();

		return TRUE;
	}

	function getTainted() {
		return $this->fromBool( $this->data['tainted'] );
	}
	function setTainted($bool) {
		$this->data['tainted'] = $this->toBool($bool);

		return true;
	}

	function getTaintedDate() {
		if ( isset($this->data['tainted_date']) ) {
			return $this->data['tainted_date'];
		}

		return FALSE;
	}
	function setTaintedDate($epoch = NULL) {
		$epoch = trim($epoch);

		if ($epoch == NULL) {
			$epoch = TTDate::getTime();
		}

		if 	(	$this->Validator->isDate(		'tainted_date',
												$epoch,
												TTi18n::gettext('Incorrect tainted date') ) ) {

			$this->data['tainted_date'] = $epoch;

			return TRUE;
		}

		return FALSE;

	}
	function getTaintedBy() {
		if ( isset($this->data['tainted_by']) ) {
			return $this->data['tainted_by'];
		}

		return FALSE;
	}
	function setTaintedBy($id = NULL) {
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

		if ( $this->Validator->isResultSetWithRows(	'tainted_by',
													$ulf->getByID($id),
													TTi18n::gettext('Incorrect tainted employee')
													) ) {

			$this->data['tainted_by'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getTimeSheetVerifyWindowStartDate() {
		if ( is_object( $this->getPayPeriodScheduleObject() ) ) {
			return (int)$this->getEndDate()-( (int)$this->getPayPeriodScheduleObject()->getTimeSheetVerifyBeforeEndDate()*86400);
		}

		return $this->getEndDate();
	}
	function getTimeSheetVerifyWindowEndDate() {
		if ( is_object( $this->getPayPeriodScheduleObject() ) ) {
			return (int)$this->getTransactionDate()-( (int)$this->getPayPeriodScheduleObject()->getTimeSheetVerifyBeforeTransactionDate()*86400);
		}

		return $this->getTransactionDate();
	}

	function getIsLocked() {
		if ( $this->getStatus() == 10 OR $this->getStatus() == 30 OR $this->isNew() == TRUE ) {
			return FALSE;
		}

		return TRUE;
	}

	function getName($include_schedule_name = FALSE) {
		$schedule_name = NULL;
		if ( $include_schedule_name == TRUE ) {
			$schedule_name = '('. $this->getPayPeriodScheduleObject()->getName() .') ';
		}

		$retval = $schedule_name . TTDate::getDate('DATE', $this->getStartDate() ).' -> '. TTDate::getDate('DATE', $this->getEndDate() );

		return $retval;
	}

	function getEnableImportData() {
		if ( isset($this->import_data) ) {
			return $this->import_data;
		}

		return FALSE;
	}
	function setEnableImportData($bool) {
		$this->import_data = $bool;

		return TRUE;
	}

	function importData() {
		$pps_obj = $this->getPayPeriodScheduleObject();

		if ( is_object( $pps_obj ) ) {
			//Get all users assigned to this pp schedule
			$udlf = new UserDateListFactory();
			$udlf->getByUserIdAndStartDateAndEndDateAndEmptyPayPeriod( $pps_obj->getUser(), $this->getStartDate(), $this->getEndDate() );
			Debug::text(' Pay Period ID: '. $this->getId() .' Pay Period orphaned User Date Rows: '. $udlf->getRecordCount() .' Start Date: '. TTDate::getDate('DATE+TIME', $this->getStartDate() ) .' End Date: '. TTDate::getDate('DATE+TIME', $this->getEndDate() ), __FILE__, __LINE__, __METHOD__,10);
			if ( $udlf->getRecordCount() > 0 ) {
				$udlf->StartTransaction();
				foreach( $udlf as $ud_obj ) {
					$ud_obj->setPayPeriod( $this->getId() );
					if ( $ud_obj->isValid() ) {
						$ud_obj->Save();
					}
				}
				$ud_obj->CommitTransaction();
			}

			return TRUE;
		}

		return FALSE;
	}

	function Validate() {
		//Make sure there aren't conflicting pay periods.
		//Start date checks that...

		//Make sure End Date is after Start Date, and transaction date is the same or after End Date.
		Debug::text('Start Date: '. $this->getStartDate() .' End Date: '. $this->getEndDate(), __FILE__, __LINE__, __METHOD__, 10);
		if ( $this->getEndDate() <= $this->getStartDate() ) {
			$this->Validator->isTrue(		'end_date',
											FALSE,
											TTi18n::gettext('Conflicting end date'));
		}

		if ( $this->getTransactionDate() < $this->getEndDate() ) {
			$this->Validator->isTrue(		'transaction_date',
											FALSE,
											TTi18n::gettext('Conflicting transaction date'));
		}

		//Make sure if this a monthly+advanc pay period, advance dates are set.
        $ppslf = new PayPeriodScheduleListFactory();
		$ppslf->getById( $this->getPayPeriodSchedule() );
		if ( $ppslf->getRecordCount() == 1 AND is_object($ppslf) AND $ppslf->getCurrent()->getType() == 40 ) {
			Debug::text('Pay Period Type IS Monthly + Advance: ', __FILE__, __LINE__, __METHOD__, 10);
			if ( $this->getAdvanceEndDate() === FALSE ) {
				$this->Validator->isTrue(		'advance_end_date',
												FALSE,
												TTi18n::gettext('Advance end date is not set') );
			}

			if ( $this->getAdvanceTransactionDate() === FALSE ) {
				$this->Validator->isTrue(		'advance_transaction_date',
												FALSE,
												TTi18n::gettext('Advance transaction date is not set') );
			}

			//Make sure advance dates are in the proper range.
			if ( $this->getAdvanceEndDate() > $this->getEndDate()
					OR  $this->getAdvanceEndDate() < $this->getStartDate() ) {
						$this->Validator->isDate(		'advance_end_date',
														FALSE,
														TTi18n::gettext('Incorrect advance end date'));
			}

			if ( $this->getAdvanceTransactionDate() > $this->getEndDate()
					OR  $this->getAdvanceTransactionDate() < $this->getAdvanceEndDate() ) {
						$this->Validator->isDate(		'advance_transaction_date',
														FALSE,
														TTi18n::gettext('Incorrect advance transaction date'));
			}

		} elseif ( $ppslf->getRecordCount() == 1 AND is_object($ppslf) ) {
			Debug::text('Pay Period Type is NOT Monthly + Advance... Advance End Date: '. $this->getAdvanceEndDate(), __FILE__, __LINE__, __METHOD__, 10);
			if ( $this->getAdvanceEndDate() != '' ) {
				$this->Validator->isTrue(		'advance_end_date',
												FALSE,
												TTi18n::gettext('Advance end date is set') );
			}

			if ( $this->getAdvanceTransactionDate() != '' ) {
				$this->Validator->isTrue(		'advance_transaction_date',
												FALSE,
												TTi18n::gettext('Advance transaction date is set') );
			}
		} else {
			Debug::text('Pay Period Schedule not found: '. $this->getPayPeriodSchedule(), __FILE__, __LINE__, __METHOD__, 10);
			$this->Validator->isTrue(		'pay_period_schedule',
											FALSE,
											TTi18n::gettext('Incorrect Pay Period Schedule') );
		}

		return TRUE;
	}

	function preSave() {
		$this->StartTransaction();

		if ( $this->getStatus() == 30 ) {
			$this->setTainted(TRUE);
		}

		//Only update these when we are setting the pay period to Post-Adjustment status.
		if ( $this->getStatus() == 30 AND $this->getTainted() == TRUE ) {
			$this->setTaintedBy();
			$this->setTaintedDate();
		}

		return TRUE;
	}

	function postSave() {
		$this->removeCache( $this->getId() );

		if ( $this->getDeleted() == TRUE ) {
			Debug::text('Delete TRUE: ', __FILE__, __LINE__, __METHOD__, 10);
			//Unassign user_date rows from this pay period, no need to delete this data anymore as it can be easily done otherways and users don't realize
			//how much data will actually be deleted.
			$udf = new UserDateFactory();

			$query = 'update '. $udf->getTable() .' set pay_period_id = 0 where pay_period_id = '. $this->getId();
			$this->db->Execute($query);
		} else {
			if ( $this->getStatus() == 20 ) { //Closed
				//Mark pay stubs as PAID once the pay period is closed?
				TTLog::addEntry( $this->getId(), 'Edit',  TTi18n::getText('Setting Pay Period to Closed'), NULL, $this->getTable() );
				$this->setPayStubStatus(40);
			} elseif ( $this->getStatus() == 30 ) {
				TTLog::addEntry( $this->getId(), 'Edit',  TTi18n::getText('Setting Pay Period to Post-Adjustment'), NULL, $this->getTable() );
			}

			if ( $this->getEnableImportData() == TRUE ) {
				$this->importData();
			}
		}

		$this->CommitTransaction();

		return TRUE;
	}

	function setObjectFromArray( $data ) {
		if ( is_array( $data ) ) {
			$variable_function_map = $this->getVariableToFunctionMap();
			foreach( $variable_function_map as $key => $function ) {
				if ( isset($data[$key]) ) {

					$function = 'set'.$function;
					switch( $key ) {
						case 'start_date':
						case 'end_date':
						case 'transaction_date':
							if ( method_exists( $this, $function ) ) {
								$this->$function( TTDate::parseDateTime( $data[$key] ) );
							}
							break;
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
	        $ppsf = new PayPeriodScheduleFactory();

			foreach( $variable_function_map as $variable => $function_stub ) {
				if ( $include_columns == NULL OR ( isset($include_columns[$variable]) AND $include_columns[$variable] == TRUE ) ) {

					$function = 'get'.$function_stub;
					switch( $variable ) {
						case 'status':
							$function = 'get'.$variable;
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = Option::getByKey( $this->$function(), $this->getOptions( $variable ) );
							}
							break;
						case 'type':
							//Make sure type_id is set first.
							if ( isset($data['type_id']) ) {
								$data[$variable] = Option::getByKey( $data['type_id'], $ppsf->getOptions( $variable ) );
							} else {
								$data[$variable] = NULL;
							}
							break;
						case 'type_id':
						case 'pay_period_schedule':
							$data[$variable] = $this->getColumn( $variable );
							break;
						case 'start_date':
						case 'end_date':
						case 'transaction_date':
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = TTDate::getAPIDate( 'DATE+TIME', $this->$function() );
							}
							break;
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

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Pay Period'), NULL, $this->getTable() );
	}
}
?>
