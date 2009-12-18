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
 * $Id: BankAccountFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Module_Users
 */
class BankAccountFactory extends Factory {
	protected $table = 'bank_account';
	protected $pk_sequence_name = 'bank_account_id_seq'; //PK Sequence name
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

	function getUser() {
		if ( isset($this->data['user_id']) ) {
			return $this->data['user_id'];
		}

		return FALSE;
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

	function getInstitution() {
		if ( isset($this->data['institution']) ) {
			return $this->data['institution'];
		}

		return FALSE;
	}
	function setInstitution($value) {
		$value = trim($value);

		if (
						$this->Validator->isNumeric(	'institution',
														$value,
														TTi18n::gettext('Invalid institution number, must be digits only'))
				AND
						$this->Validator->isLength(		'institution',
														$value,
														TTi18n::gettext('Invalid institution number length'),
														2,
														3)
			) {

			$this->data['institution'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getTransit() {
		if ( isset($this->data['transit']) ) {
			return $this->data['transit'];
		}

		return FALSE;
	}
	function setTransit($value) {
		$value = trim($value);

		if (
						$this->Validator->isNumeric(	'transit',
														$value,
														TTi18n::gettext('Invalid transit number, must be digits only'))
				AND
						$this->Validator->isLength(		'transit',
														$value,
														TTi18n::gettext('Invalid transit number length'),
														2,
														15)
			) {

			$this->data['transit'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getAccount() {
		if ( isset($this->data['account']) ) {
			return $this->data['account'];
		}

		return FALSE;
	}
	function setAccount($value) {
		$value = trim($value);

		if (
						$this->Validator->isLength(		'account',
														$value,
														TTi18n::gettext('Invalid account number length'),
														3,
														20)
			) {

			$this->data['account'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function Validate() {
		//Make sure this entry is unique.

		return TRUE;
	}

	function preSave() {
		if ( $this->getUser() == FALSE ) {
			Debug::Text('Clearing User value, because this is strictly a company record', __FILE__, __LINE__, __METHOD__,10);
			//$this->setUser( 0 ); //COMPANY record.
		}

		//PGSQL has a NOT NULL constraint on Instituion number prior to schema v1014A.
		if ( $this->getInstitution() == FALSE ) {
			$this->setInstitution( '000' );
		}

		return TRUE;
	}

	function addLog( $log_action ) {
		if ( $this->getUser() == '' ) {
			$log_description = TTi18n::getText('Company');
		} else {
			$log_description = TTi18n::getText('Employee');
		}
		return TTLog::addEntry( $this->getId(), $log_action, TTi18n::getText('Bank Account') .' - '. $log_description, NULL, $this->getTable() );
	}

}
?>
