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
 * $Id: PermissionControlFactory.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */

/**
 * @package Core
 */
class PermissionControlFactory extends Factory {
	protected $table = 'permission_control';
	protected $pk_sequence_name = 'permission_control_id_seq'; //PK Sequence name

	protected $company_obj = NULL;
	protected $tmp_previous_user_ids = array();

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'columns':
				$retval = array(
										'-1000-name' => TTi18n::gettext('Name'),
										'-1010-description' => TTi18n::gettext('Description'),

										'-2000-created_by' => TTi18n::gettext('Created By'),
										'-2010-created_date' => TTi18n::gettext('Created Date'),
										'-2020-updated_by' => TTi18n::gettext('Updated By'),
										'-2030-updated_date' => TTi18n::gettext('Updated Date'),
							);
				break;
			case 'default_display_columns': //Columns that are displayed by default.
				$retval = array(
								'name',
								'description',
								'created_by',
								'created_date',
								'updated_by',
								'updated_date',
								);
				break;
			case 'unique_columns': //Columns that are unique, and disabled for mass editing.
				$retval = array(
								'name',
								);
				break;
		}

		return $retval;
	}

	function _getVariableToFunctionMap() {
		$variable_function_map = array(
										'id' => 'ID',
										'company_id' => 'Company',
										'name' => 'Name',
										'description' => 'Description',
										'deleted' => 'Deleted',
										);
		return $variable_function_map;
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

	function isUniqueName($name) {
		$ph = array(
					'company_id' => $this->getCompany(),
					'name' => $name,
					);

		$query = 'select id from '. $this->getTable() .' where company_id = ? AND name = ? AND deleted=0';
		$permission_control_id = $this->db->GetOne($query, $ph);
		Debug::Arr($permission_control_id,'Unique Permission Control ID: '. $permission_control_id, __FILE__, __LINE__, __METHOD__,10);

		if ( $permission_control_id === FALSE ) {
			return TRUE;
		} else {
			if ($permission_control_id == $this->getId() ) {
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
											2,50)
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
											1,255) ) {

			$this->data['description'] = $description;

			return TRUE;
		}

		return FALSE;
	}

	function getUser() {
		$pulf = new PermissionUserListFactory();
		$pulf->getByPermissionControlId( $this->getId() );
		foreach ($pulf as $obj) {
			$list[] = $obj->getUser();
		}

		if ( isset($list) ) {
			return $list;
		}

		return FALSE;
	}
	function setUser($ids) {
		Debug::text('Setting User IDs : ', __FILE__, __LINE__, __METHOD__, 10);
		if (is_array($ids) and count($ids) > 0) {
			//Remove any of the selected employees from other permission control objects first.
			//So there we can switch employees from one group to another in a single action.
			$pulf = new PermissionUserListFactory();
			//$pculf->getByCompanyIdAndUserId( $this->getCompany(), $ids );
			$pulf->getByCompanyIdAndUserIdAndNotPermissionControlId( $this->getCompany(), $ids, (int)$this->getId() );
			if ( $pulf->getRecordCount() > 0 ) {
				Debug::text('Found User IDs assigned to another Permission Group, unassigning them!', __FILE__, __LINE__, __METHOD__, 10);
				foreach( $pulf as $pu_obj ) {
					$pu_obj->Delete();
				}
			}
			unset($pulf, $pu_obj);

			$tmp_ids = array();

			$pf = new PermissionFactory();
			if ( !$this->isNew() ) {
				//If needed, delete mappings first.
				$pulf = new PermissionUserListFactory();
				$pulf->getByPermissionControlId( $this->getId() );

				$tmp_ids = array();
				foreach ($pulf as $obj) {
					$id = $obj->getUser();
					Debug::text('Permission Control ID: '. $obj->getPermissionControl() .' ID: '. $id, __FILE__, __LINE__, __METHOD__, 10);

					//Delete users that are not selected.
					if ( !in_array($id, $ids) ) {
						Debug::text('Deleting: '. $id, __FILE__, __LINE__, __METHOD__, 10);
						$this->tmp_previous_user_ids[] = $id;
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
					//Remove users from any other permission control object
					//first, otherwise there is a gab where an employee has
					//no permissions, this is especially bad for administrators
					//who are currently logged in.
					$puf = new PermissionUserFactory();
					$puf->setPermissionControl( $this->getId() );
					$puf->setUser( $id );

					$obj = $ulf->getById( $id )->getCurrent();

					if ($this->Validator->isTrue(		'user',
														$puf->Validator->isValid(),
														TTi18n::gettext('Selected employee is invalid, or already assigned to another permission group').' ('. $obj->getFullName() .')' )) {
						$puf->save();
					}
				}
			}

			return TRUE;
		}

		Debug::text('No User IDs to set.', __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
	}

	function getPermission() {
		$plf = new PermissionListFactory();
		$plf->getByCompanyIdAndPermissionControlId( $this->getCompany(), $this->getId() );
		if ( $plf->getRecordCount() > 0 ) {
			Debug::Text('Found Permissions: '. $plf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);
			foreach($plf as $p_obj) {
				$current_permissions[$p_obj->getSection()][$p_obj->getName()] = $p_obj->getValue();
			}

			return $current_permissions;
		}

		return FALSE;
	}
	function setPermission( $permission_arr, $old_permission_arr = array() ) {
		if ( $this->getId() == FALSE ) {
			return FALSE;
		}

		$pf = new PermissionFactory();

		//Don't Delete all previous permissions, do that in the Permission class.
		if ( isset($permission_arr) AND is_array($permission_arr) AND count($permission_arr) > 0 ) {
			foreach ($permission_arr as $section => $permissions) {
				Debug::Text('  Section: '. $section, __FILE__, __LINE__, __METHOD__,10);

				foreach ($permissions as $name => $value) {
					Debug::Text('     Name: '. $name .' - Value: '. $value, __FILE__, __LINE__, __METHOD__,10);
					if ( 	(
							!isset($old_permission_arr[$section][$name])
								OR (isset($old_permission_arr[$section][$name]) AND $value != $old_permission_arr[$section][$name] )
							)
							AND $pf->isIgnore( $section, $name, $this->getCompanyObject()->getProductEdition() ) == FALSE
							) {

						if ( $value == 0 OR $value == 1 ) {
							Debug::Text('    Modifying/Adding Permission: '. $name .' - Value: '. $value, __FILE__, __LINE__, __METHOD__,10);
							$tmp_pf = new PermissionFactory();
							$tmp_pf->setPermissionControl( $this->getId() );
							$tmp_pf->setSection( $section );
							$tmp_pf->setName( $name );
							$tmp_pf->setValue( (int)$value );

							if ( $tmp_pf->isValid() ) {
								$tmp_pf->save();
							}
						}
					} else {
						Debug::Text('     Permission didnt change... Skipping', __FILE__, __LINE__, __METHOD__,10);
					}
				}
			}
		}

		return TRUE;
	}

	function postSave() {
		$pf = new PermissionFactory();

		$clear_cache_user_ids = array_merge( (array)$this->getUser(), (array)$this->tmp_previous_user_ids);
		foreach( $clear_cache_user_ids as $user_id ) {
			$pf->clearCache( $user_id, $this->getCompany() );
		}
	}

	//Support setting created_by,updated_by especially for importing data.
	//Make sure data is set based on the getVariableToFunctionMap order.
	function setObjectFromArray( $data ) {
		if ( is_array( $data ) ) {
			$variable_function_map = $this->getVariableToFunctionMap();
			foreach( $variable_function_map as $key => $function ) {
				if ( isset($data[$key]) ) {

					$function = 'set'.$function;
					switch( $key ) {
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
			foreach( $variable_function_map as $variable => $function_stub ) {
				if ( $include_columns == NULL OR ( isset($include_columns[$variable]) AND $include_columns[$variable] == TRUE ) ) {

					$function = 'get'.$function_stub;
					switch( $variable ) {
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
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Permission Group: '). $this->getName(), NULL, $this->getTable() );
	}
}
?>
