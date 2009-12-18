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
 * $Revision: 2637 $
 * $Id: HierarchyControlFactory.class.php 2637 2009-07-07 21:26:01Z ipso $
 * $Date: 2009-07-07 14:26:01 -0700 (Tue, 07 Jul 2009) $
 */

/**
 * @package Module_Hierarchy
 */
class HierarchyControlFactory extends Factory {
	protected $table = 'hierarchy_control';
	protected $pk_sequence_name = 'hierarchy_control_id_seq'; //PK Sequence name

	//Temporaray holding array.
	protected $object_type_ids = NULL;
	function getCompany() {
		return $this->data['company_id'];
	}
	function setCompany($id) {
		$id = trim($id);

		$clf = new CompanyListFactory();

		if ( $this->Validator->isResultSetWithRows(	'company',
													$clf->getByID($id),
													TTi18n::gettext('Invalid Company')
													) ) {

			$this->data['company_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function isUniqueName($name) {
		$ph = array(
					'company_id' => $this->getCompany(),
					'name' => $name,
					);

		$query = 'select id from '. $this->getTable() .' where company_id = ? AND name = ? AND deleted = 0';
		$hierarchy_control_id = $this->db->GetOne($query, $ph);
		Debug::Arr($hierarchy_control_id,'Unique Hierarchy Control ID: '. $hierarchy_control_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $hierarchy_control_id === FALSE ) {
			return TRUE;
		} else {
			if ($hierarchy_control_id == $this->getId() ) {
				return TRUE;
			}
		}

		return FALSE;
	}
	function getName() {
		return $this->data['name'];
	}
	function setName($name) {
		$name = trim($name);

		if (	$this->Validator->isLength(	'name',
											$name,
											TTi18n::gettext('Name is invalid'),
											2,250)
				AND	$this->Validator->isTrue(	'name',
												$this->isUniqueName($name),
												TTi18n::gettext('Name is already in use')
												)
						) {

			$this->data['name'] = $name;

			return TRUE;
		}

		return FALSE;
	}

	function getDescription() {
		return $this->data['description'];
	}
	function setDescription($description) {
		$description = trim($description);

		if (	$description == ''
				OR $this->Validator->isLength(	'description',
											$description,
											TTi18n::gettext('Description is invalid'),
											1,250) ) {

			$this->data['description'] = $description;

			return TRUE;
		}

		return FALSE;
	}

	//Return the temp IDs for validation.
	function getTmpObjectType() {
		if ( isset( $this->object_type_ids ) ) {
			return 	$this->object_type_ids;
		}

		return FALSE;
	}


	//Return IDs from the database,
	function getObjectType() {
		$hotlf = new HierarchyObjectTypeListFactory();
		$hotlf->getByHierarchyControlId( $this->getId() );

		foreach ($hotlf as $object_type) {
			$object_type_list[] = $object_type->getObjectType();
		}

		if ( isset($object_type_list) ) {
			return $object_type_list;
		}

		return FALSE;
	}

	function setObjectType($ids) {
		if ( is_array($ids) AND count($ids) > 0 ) {
			$this->object_type_ids = $ids;

			return TRUE;
		}

		$this->Validator->isTrue(		'object_type',
										FALSE,
										TTi18n::gettext('At least one object type must be selected'));
		return FALSE;
	}

	function setObjectTypeIds($ids) {
		if ( is_array($ids) ) {
			if ( !$this->isNew() ) {
				//If needed, delete mappings first.
				$lf_a = new HierarchyObjectTypeListFactory();
				$lf_a->getByHierarchyControlId( $this->getId() );

				$tmp_ids = array();
				foreach ($lf_a as $obj) {
					$id = $obj->getId();
					Debug::text('Hierarchy Object Type ID: '. $obj->getId() .' ID: '. $id, __FILE__, __LINE__, __METHOD__, 10);

					//Delete users that are not selected.
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
					$f = new HierarchyObjectTypeFactory();
					$f->setHierarchyControl( $this->getId() );
					$f->setObjectType( $id );

					if ($this->Validator->isTrue(		'object_type',
														$f->Validator->isValid(),
														TTi18n::gettext('Object type is already assigned to another hierarchy'))) {
						$f->save();
					}
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	function getUser() {
		$hulf = new HierarchyUserListFactory();
		$hulf->getByHierarchyControlID( $this->getId() );
		foreach ($hulf as $obj) {
			$list[] = $obj->getUser();
		}

		if ( isset($list) ) {
			return $list;
		}

		return FALSE;
	}
	function setUser($ids) {
		Debug::text('Setting User IDs : ', __FILE__, __LINE__, __METHOD__, 10);
		if ( is_array($ids) ) {
			if ( !$this->isNew() ) {
				//If needed, delete mappings first.
				$hulf = new HierarchyUserListFactory();
				$hulf->getByHierarchyControlID( $this->getId() );

				$tmp_ids = array();
				foreach ($hulf as $obj) {
					$id = $obj->getUser();
					Debug::text('HierarchyControl ID: '. $obj->getHierarchyControl() .' ID: '. $id, __FILE__, __LINE__, __METHOD__, 10);

					//Delete users that are not selected.
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

			//Insert new mappings.
			$ulf = new UserListFactory();

			foreach ($ids as $id) {
				if ( isset($ids) AND !in_array($id, $tmp_ids) ) {
					$huf = new HierarchyUserFactory();
					$huf->setHierarchyControl( $this->getId() );
					$huf->setUser( $id );

					$ulf->getById( $id );
					if ( $ulf->getRecordCount() > 0 ) {
						$obj = $ulf->getCurrent();

						if ($this->Validator->isTrue(		'user',
															$huf->Validator->isValid(),
															TTi18n::gettext('Selected subordinate is invalid or already assigned to another hierarchy with the same objects ').' ('. $obj->getFullName() .')' )) {
							$huf->save();
						}
					}
				}
			}


			return TRUE;
		}

		Debug::text('No User IDs to set.', __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
	}

	function Validate() {
		return TRUE;
	}

	function postSave() {
		//Save objectype IDs

		$this->setObjectTypeIds( $this->getTmpObjectType() );

		return TRUE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action, TTi18n::getText('Hierarchy'), NULL, $this->getTable() );
	}
}
?>
