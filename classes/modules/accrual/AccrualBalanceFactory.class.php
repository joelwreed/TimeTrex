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
 * $Id: AccrualBalanceFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Module_Accrual
 */
class AccrualBalanceFactory extends Factory {
	protected $table = 'accrual_balance';
	protected $pk_sequence_name = 'accrual_balance_id_seq'; //PK Sequence name

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

	function getBalance() {
		if ( isset($this->data['balance']) ) {
			return $this->data['balance'];
		}

		return FALSE;
	}
	function setBalance($int) {
		$int = trim($int);

		if  ( empty($int) ){
			$int = 0;
		}

		if 	(	$this->Validator->isNumeric(		'balance',
													$int,
													TTi18n::gettext('Incorrect Balance'))
				) {
			$this->data['balance'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getCreatedBy() {
		return FALSE;
	}
	function setCreatedBy($id = NULL) {
		return FALSE;
	}

	function getUpdatedDate() {
		return FALSE;
	}
	function setUpdatedDate($epoch = NULL) {
		return FALSE;
	}
	function getUpdatedBy() {
		return FALSE;
	}
	function setUpdatedBy($id = NULL) {
		return FALSE;
	}

	function getDeletedDate() {
		return FALSE;
	}
	function setDeletedDate($epoch = NULL) {
		return FALSE;
	}
	function getDeletedBy() {
		return FALSE;
	}
	function setDeletedBy($id = NULL) {
		return FALSE;
	}

	static function calcBalance( $user_id, $accrual_policy_id = NULL ) {
		global $profiler;

		$profiler->startTimer( "AccrualBalanceFactory::calcBalance()");

		$alf = new AccrualListFactory();
		$balance = $alf->getSumByUserIdAndAccrualPolicyId($user_id, $accrual_policy_id);
		Debug::text('Balance for User ID: '. $user_id .' Accrual Policy ID: '. $accrual_policy_id .' Balance: '. $balance, __FILE__, __LINE__, __METHOD__, 10);

		$ablf = new AccrualBalanceListFactory();
		$ablf->getByUserIdAndAccrualPolicyId( $user_id, $accrual_policy_id);
		Debug::text('Found balance records to delete: '. $ablf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
		if ( $ablf->getRecordCount() > 0) {
			foreach($ablf as $ab_obj) {
				$ab_obj->Delete();
			}
		}

		Debug::text('Setting new balance to: '. $balance, __FILE__, __LINE__, __METHOD__, 10);
		$ab = new AccrualBalanceFactory();
		$ab->setUser( $user_id );
		$ab->setAccrualPolicyId( $accrual_policy_id );
		$ab->setBalance( $balance );

		$profiler->stopTimer( "AccrualBalanceFactory::calcBalance()");

		if ( $ab->isValid() ) {
			return $ab->Save();
		}

		Debug::text('Setting new balance failed for User ID: '. $user_id, __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
	}

	function Validate() {
		return TRUE;
	}

	function preSave() {
		return TRUE;
	}

	function postSave() {
		return TRUE;
	}

}
?>
