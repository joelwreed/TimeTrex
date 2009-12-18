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
 * $Id: OtherFieldFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Core
 */
class OtherFieldFactory extends Factory {
	protected $table = 'other_field';
	protected $pk_sequence_name = 'other_field_id_seq'; //PK Sequence name

	protected $company_obj = NULL;


	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'type':
				$retval = array(
											10  => TTi18n::gettext('Employee'),
											15  => TTi18n::gettext('Punch'),
											20  => TTi18n::gettext('Job'),
											30  => TTi18n::gettext('Task'),
											50  => TTi18n::gettext('Client'),
											55  => TTi18n::gettext('Client Contact'),
											//57  => TTi18n::gettext('Client Payment'),
											60  => TTi18n::gettext('Product'),
											70  => TTi18n::gettext('Invoice'),
											80  => TTi18n::gettext('Document'),
									);
				break;

		}

		return $retval;
	}


	function getCompanyObject() {
		if ( is_object($this->company_obj) ) {
			return $this->company_obj;
		} else {
			$clf = new CompanyListFactory();
			$this->company_obj = $clf->getById( $this->getCompany() )->getCurrent();

			return $this->company_obj;
		}
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

	function isUniqueType($type) {
		$ph = array(
					'company_id' => $this->getCompany(),
					'type_id' => $type,
					);

		$query = 'select id from '. $this->getTable() .'
					where company_id = ?
						AND type_id = ?
						AND deleted = 0';
		$type_id = $this->db->GetOne($query, $ph);
		Debug::Arr($type_id,'Unique Type: '. $type, __FILE__, __LINE__, __METHOD__,10);

		if ( $type_id === FALSE ) {
			return TRUE;
		} else {
			if ($type_id == $this->getId() ) {
				return TRUE;
			}
		}

		return FALSE;
	}

	function getType() {
		return $this->data['type_id'];
	}
	function setType($type) {
		$type = trim($type);

		//$jif = new JobItemFactory();
		$key = Option::getByValue($type, $this->getOptions('type') );
		if ($key !== FALSE) {
			$type = $key;
		}

		if ( $this->Validator->inArrayKey(	'type',
											$type,
											TTi18n::gettext('Incorrect Type'),
											$this->getOptions('type') )
					AND
						$this->Validator->isTrue(		'type',
														$this->isUniqueType($type),
														TTi18n::gettext('Type already exists'))

											) {

			$this->data['type_id'] = $type;

			return FALSE;
		}

		return FALSE;
	}

	function getOtherID1() {
		return $this->data['other_id1'];
	}
	function setOtherID1($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id1',
											$value,
											TTi18n::gettext('Other ID1 is invalid'),
											1,255) ) {

			$this->data['other_id1'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID2() {
		return $this->data['other_id2'];
	}
	function setOtherID2($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id2',
											$value,
											TTi18n::gettext('Other ID2 is invalid'),
											1,255) ) {

			$this->data['other_id2'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID3() {
		return $this->data['other_id3'];
	}
	function setOtherID3($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id3',
											$value,
											TTi18n::gettext('Other ID3 is invalid'),
											1,255) ) {

			$this->data['other_id3'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID4() {
		return $this->data['other_id4'];
	}
	function setOtherID4($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id4',
											$value,
											TTi18n::gettext('Other ID4 is invalid'),
											1,255) ) {

			$this->data['other_id4'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID5() {
		return $this->data['other_id5'];
	}
	function setOtherID5($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id5',
											$value,
											TTi18n::gettext('Other ID5 is invalid'),
											1,255) ) {

			$this->data['other_id5'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID6() {
		return $this->data['other_id6'];
	}
	function setOtherID6($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id6',
											$value,
											TTi18n::gettext('Other ID6 is invalid'),
											1,255) ) {

			$this->data['other_id6'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID7() {
		return $this->data['other_id7'];
	}
	function setOtherID7($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id7',
											$value,
											TTi18n::gettext('Other ID7 is invalid'),
											1,255) ) {

			$this->data['other_id7'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID8() {
		return $this->data['other_id8'];
	}
	function setOtherID8($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id8',
											$value,
											TTi18n::gettext('Other ID8 is invalid'),
											1,255) ) {

			$this->data['other_id8'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID9() {
		return $this->data['other_id9'];
	}
	function setOtherID9($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id9',
											$value,
											TTi18n::gettext('Other ID9 is invalid'),
											1,255) ) {

			$this->data['other_id9'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID10() {
		return $this->data['other_id10'];
	}
	function setOtherID10($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id10',
											$value,
											TTi18n::gettext('Other ID10 is invalid'),
											1,255) ) {

			$this->data['other_id10'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Other Fields'), NULL, $this->getTable() );
	}
}
?>
