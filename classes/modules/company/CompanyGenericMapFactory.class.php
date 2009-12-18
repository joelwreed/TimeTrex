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
 * $Id: PolicyGroupAccrualPolicyFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Module_Policy
 */
class CompanyGenericMapFactory extends Factory {
	protected $table = 'company_generic_map';
	protected $pk_sequence_name = 'company_generic_map_id_seq'; //PK Sequence name

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'object_type':
				$retval = array(
										//Policy Group mapping
										110 => 'policy_group_over_time_policy',
										120 => 'policy_group_premium_policy',
										130 => 'policy_group_round_interval_policy',
										140 => 'policy_group_accrual_policy',
										150 => 'policy_group_meal_policy',
										155 => 'schedule_policy_meal_policy', //Mapping meal policies to schedule policies.
										160 => 'policy_group_break_policy',
										165 => 'schedule_policy_break_policy', //Mapping break policies to schedule policies.
										170 => 'policy_group_absence_policy',
										180 => 'policy_group_holiday_policy',
										190 => 'policy_group_exception_policy',

/*
										//Station user mapping
										310 => 'station_branch',
										320 => 'station_department',
										330 => 'station_user_group',
										340 => 'station_include_user',
										350 => 'station_exclude_user',

										//Premium Policy mapping
										510 => 'premium_policy_branch',
										520 => 'premium_policy_department',
										530 => 'premium_policy_job',
										540 => 'premium_policy_job_group',
										550 => 'premium_policy_job_item',
										560 => 'premium_policy_job_item_group',
*/
										//Job user mapping
										1010 => 'job_user_branch',
										1020 => 'job_user_department',
										1030 => 'job_user_group',
										1040 => 'job_include_user',
										1050 => 'job_exclude_user',
										//Job task mapping
										1060 => 'job_job_item_group',
										1070 => 'job_include_job_item',
										1080 => 'job_exclude_job_item',


									);
				break;
		}

		return $retval;
	}

	function getCompany() {
		if ( isset($this->data['company_id']) ) {
			return (int)$this->data['company_id'];
		}

		return FALSE;
	}
	function setCompany($id) {
		$id = trim($id);

		$clf = new CompanyListFactory();

		if ( $id == 0
				OR $this->Validator->isResultSetWithRows(	'company',
															$clf->getByID($id),
															TTi18n::gettext('Company is invalid')
															) ) {
			$this->data['company_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getObjectType() {
		if ( isset($this->data['object_type_id']) ) {
			return $this->data['object_type_id'];
		}

		return FALSE;
	}
	function setObjectType($type) {
		$type = trim($type);

		if ( $this->Validator->inArrayKey(	'object_type',
											$type,
											TTi18n::gettext('Object Type is invalid'),
											$this->getOptions('object_type')) ) {

			$this->data['object_type_id'] = $type;

			return FALSE;
		}

		return FALSE;
	}

	function getObjectID() {
		if ( isset($this->data['object_id']) ) {
			return $this->data['object_id'];
		}

		return FALSE;
	}
	function setObjectID($id) {
		$id = trim($id);

		$pglf = new PolicyGroupListFactory();

		if ( $this->Validator->isNumeric(	'object_id',
										$id,
										TTi18n::gettext('Object ID is invalid')
										) ) {
			$this->data['object_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getMapID() {
		if ( isset($this->data['map_id']) ) {
			return $this->data['map_id'];
		}

		return FALSE;
	}
	function setMapID($id) {
		$id = trim($id);

		$pglf = new PolicyGroupListFactory();

		if ( $this->Validator->isNumeric(	'map_id',
										$id,
										TTi18n::gettext('Map ID is invalid')
										) ) {
			$this->data['map_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	static function setMapIDs( $company_id, $object_type_id, $object_id, $ids, $is_new = FALSE ) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( $object_type_id == '') {
			return FALSE;
		}

		if ( $object_id == '') {
			return FALSE;
		}

		if ( $ids == '') {
			return FALSE;
		}

		if ( !is_array($ids) AND is_numeric( $ids ) ) {
			$ids = array($ids);
		}

		Debug::Arr($ids, 'Object Type ID: '. $object_type_id .' Object ID: '. $object_id .' IDs: ', __FILE__, __LINE__, __METHOD__, 10);

		if ( is_array($ids) ) {
			if ( $is_new == FALSE ) {
				//If needed, delete mappings first.
				$cgmlf = new CompanyGenericMapListFactory();
				$cgmlf->getByCompanyIDAndObjectTypeAndObjectID( $company_id, $object_type_id, $object_id );

				$tmp_ids = array();
				foreach ($cgmlf as $obj) {
					$id = $obj->getMapID();
					Debug::text('Object Type ID: '. $object_type_id .' Object ID: '. $obj->getObjectID() .' ID: '. $id, __FILE__, __LINE__, __METHOD__, 10);

					//Delete objects that are not selected.
					if ( !in_array($id, $ids) ) {
						Debug::text('Deleting: '. $id, __FILE__, __LINE__, __METHOD__, 10);
						$obj->Delete();
					} else {
						//Save ID's that need to be updated.
						Debug::text('NOT Deleting : '. $id, __FILE__, __LINE__, __METHOD__, 10);
						$tmp_ids[] = $id;
					}
				}
				unset($id, $obj);
			}

			foreach ($ids as $id) {
				if ( isset($ids) AND !in_array($id, $tmp_ids) ) {
					$cgmf = new CompanyGenericMapFactory();
					$cgmf->setCompany( $company_id );
					$cgmf->setObjectType( $object_type_id );
					$cgmf->setObjectID( $object_id );
					$cgmf->setMapId( $id );
					$cgmf->Save();
				}
			}

			return TRUE;
		}

		Debug::text('No objects to map.', __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
	}

	//This table doesn't have any of these columns, so overload the functions.
	function getDeleted() {
		return FALSE;
	}
	function setDeleted($bool) {
		return FALSE;
	}

	function getCreatedDate() {
		return FALSE;
	}
	function setCreatedDate($epoch = NULL) {
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
}
?>
