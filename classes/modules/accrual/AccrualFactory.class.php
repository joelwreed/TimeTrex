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
 * $Revision: 2095 $
 * $Id: AccrualFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Module_Accrual
 */
class AccrualFactory extends Factory {
	protected $table = 'accrual';
	protected $pk_sequence_name = 'accrual_id_seq'; //PK Sequence name



	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'type':
				$retval = array(
										10 => TTi18n::gettext('Banked'),
										20 => TTi18n::gettext('Used'),
										30 => TTi18n::gettext('Awarded'),
										40 => TTi18n::gettext('Un-Awarded'),
										50 => TTi18n::gettext('Gift'),
										55 => TTi18n::gettext('Paid Out'),
										60 => TTi18n::gettext('Rollover'),
										70 => TTi18n::gettext('Initial Balance'),
										75 => TTi18n::gettext('Accrual Policy'),
										80 => TTi18n::gettext('Other')
									);
				break;

		}

		return $retval;
	}


	function getUser() {
		if ( isset($this->data['user_id']) ) {
			return $this->data['user_id'];
		}
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

	function getAccrualPolicyID() {
		if ( isset($this->data['accrual_policy_id']) ) {
			return $this->data['accrual_policy_id'];
		}

		return FALSE;
	}
	function setAccrualPolicyID($id) {
		$id = trim($id);

		if ( $id == '' OR empty($id) ) {
			$id = NULL;
		}

		$aplf = new AccrualPolicyListFactory();

		if ( $id == NULL
				OR
				$this->Validator->isResultSetWithRows(	'accrual_policy',
													$aplf->getByID($id),
													TTi18n::gettext('Accrual Policy is invalid')
													) ) {

			$this->data['accrual_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getType() {
		if ( isset($this->data['type_id']) ) {
			return $this->data['type_id'];
		}

		return FALSE;
	}
	function setType($value) {
		$value = trim($value);

		$key = Option::getByValue($value, $this->getOptions('type') );
		if ($key !== FALSE) {
			$value = $key;
		}

		if ( $this->Validator->inArrayKey(	'type',
											$value,
											TTi18n::gettext('Incorrect Type'),
											$this->getOptions('type')) ) {

			$this->data['type_id'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getUserDateTotalID() {
		if ( isset($this->data['user_date_total_id']) ) {
			return $this->data['user_date_total_id'];
		}
	}
	function setUserDateTotalID($id) {
		$id = trim($id);

		$udtlf = new UserDateTotalListFactory();

		if ( $id == 0
				OR
				$this->Validator->isResultSetWithRows(	'user_date_total',
															$udtlf->getByID($id),
															TTi18n::gettext('User Date Total ID is invalid')
															) ) {
			$this->data['user_date_total_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getTimeStamp( $raw = FALSE ) {
		if ( isset($this->data['time_stamp']) ) {
			if ( $raw === TRUE ) {
				return $this->data['time_stamp'];
			} else {
				return TTDate::strtotime( $this->data['time_stamp'] );
			}
		}

		return FALSE;
	}
	function setTimeStamp($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'times_tamp',
												$epoch,
												TTi18n::gettext('Incorrect time stamp'))

			) {

			$this->data['time_stamp'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function isValidAmount($amount) {
		Debug::text('Type: '. $this->getType() .' Amount: '. $amount , __FILE__, __LINE__, __METHOD__, 10);
		//Based on type, set Amount() pos/neg
		switch ( $this->getType() ) {
			case 10: // Banked
			case 30: // Awarded
			case 50: // Gifted
				if ( $amount >= 0 ) {
					return TRUE;
				}
				break;
			case 20: // Used
			case 55: // Paid Out
			case 40: // Un Awarded
				if ( $amount <= 0 ) {
					return TRUE;
				}
				break;
			default:
				return TRUE;
				break;
		}

		return FALSE;

	}

	function getAmount() {
		if ( isset($this->data['amount']) ) {
			return $this->data['amount'];
		}

		return FALSE;
	}
	function setAmount($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'amount',
													$int,
													TTi18n::gettext('Incorrect Amount'))
				AND
				$this->Validator->isTrue(		'amount',
													$this->isValidAmount($int),
													TTi18n::gettext('Amount does not match type'))
				) {
			$this->data['amount'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getEnableCalcBalance() {
		if ( isset($this->calc_balance) ) {
			return $this->calc_balance;
		}

		return FALSE;
	}
	function setEnableCalcBalance($bool) {
		$this->calc_balance = $bool;

		return TRUE;
	}

	function Validate() {
		if ( $this->getAccrualPolicyID() == FALSE OR $this->getAccrualPolicyID() == 0 ) {
			$this->Validator->isTrue(		'accrual_policy',
											FALSE,
											TTi18n::gettext('Accrual Policy is invalid'));

		}

		return TRUE;
	}

	function preSave() {
		if ( $this->getTimeStamp() == FALSE ) {
			$this->setTimeStamp( TTDate::getTime() );
		}

		//Delete duplicates before saving.
		//Or orphaned entries on Sum'ing?
		//Would have to do it on view as well though.
		if ( $this->getUserDateTotalID() !== FALSE AND $this->getUserDateTotalID() !== 0 ) {
			$alf = new AccrualListFactory();
			$alf->getByUserIdAndAccrualPolicyIDAndUserDateTotalID( $this->getUser(), $this->getAccrualPolicyID(), $this->getUserDateTotalID() );
			Debug::text('Found Duplicate Records: '. (int)$alf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
			if ( $alf->getRecordCount() > 0 ) {
				foreach($alf as $a_obj ) {
					$a_obj->Delete();
				}
			}
		}

		return TRUE;
	}

	function postSave() {
		//Calculate balance
		if ( $this->getEnableCalcBalance() == TRUE ) {
			Debug::text('Calculating Balance is enabled! ', __FILE__, __LINE__, __METHOD__, 10);
			AccrualBalanceFactory::calcBalance( $this->getUser(), $this->getAccrualPolicyID() );
		}

		return TRUE;
	}

	static function deleteOrphans($user_id) {
		Debug::text('Attempting to delete Orphaned Records for User ID: '. $user_id, __FILE__, __LINE__, __METHOD__, 10);
		//Remove orphaned entries
		$alf = new AccrualListFactory();
		$alf->getOrphansByUserId( $user_id );
		Debug::text('Found Orphaned Records: '. $alf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
		if ( $alf->getRecordCount() > 0 ) {
			foreach( $alf as $a_obj ) {
				Debug::text('Orphan Record ID: '. $a_obj->getID(), __FILE__, __LINE__, __METHOD__, 10);
				$accrual_policy_ids[] = $a_obj->getAccrualPolicyId();
				$a_obj->Delete();
			}

			//ReCalc balances
			if ( isset($accrual_policy_ids) ) {
				foreach($accrual_policy_ids as $accrual_policy_id) {
					AccrualBalanceFactory::calcBalance( $user_id, $accrual_policy_id );
				}
			}

		}

		return TRUE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action, TTi18n::getText('Accrual'), NULL, $this->getTable() );
	}

}
?>
