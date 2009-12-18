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
 * $Revision: 3021 $
 * $Id: DepartmentFactory.class.php 3021 2009-11-11 23:33:03Z ipso $
 * $Date: 2009-11-11 15:33:03 -0800 (Wed, 11 Nov 2009) $
 */

/**
 * @package Module_Department
 */
class DepartmentFactory extends Factory {
	protected $table = 'department';
	protected $pk_sequence_name = 'department_id_seq'; //PK Sequence name


	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('ENABLED'),
										20 => TTi18n::gettext('DISABLED')
									);
				break;
			case 'columns':
				$retval = array(
										'-1010-status' => TTi18n::gettext('Status'),
										'-1020-manual_id' => TTi18n::gettext('Code'),
										'-1030-name' => TTi18n::gettext('Name'),

										'-2000-created_by' => TTi18n::gettext('Created By'),
										'-2010-created_date' => TTi18n::gettext('Created Date'),
										'-2020-updated_by' => TTi18n::gettext('Updated By'),
										'-2030-updated_date' => TTi18n::gettext('Updated Date'),
							);
				break;
			case 'default_display_columns': //Columns that are displayed by default.
				$retval = array(
								'manual_id',
								'status',
								'name',
								'created_by',
								'created_date',
								'updated_by',
								'updated_date',
								);
				break;
			case 'unique_columns': //Columns that are unique, and disabled for mass editing.
				$retval = array(
								'name',
								'manual_id'
								);

		}

		return $retval;
	}

	function _getVariableToFunctionMap() {
		$variable_function_map = array(
										'id' => 'ID',
										'company_id' => 'Company',
										'status_id' => 'Status',
										'status' => FALSE,
										'manual_id' => 'ManualID',
										'name' => 'Name',
										'deleted' => 'Deleted',
										);
		return $variable_function_map;
	}

	function getCompany() {
		return $this->data['company_id'];
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

	function getStatus() {
		//Have to return the KEY because it should always be a drop down box.
		//return Option::getByKey($this->data['status_id'], $this->getOptions('status') );
		return $this->data['status_id'];
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

	function isUniqueManualID($id) {
		if ( $this->getCompany() == FALSE ) {
			return FALSE;
		}

		$ph = array(
					'manual_id' => $id,
					'company_id' =>  $this->getCompany(),
					);

		$query = 'select id from '. $this->getTable() .' where manual_id = ? AND company_id = ? AND deleted=0';
		$id = $this->db->GetOne($query, $ph);
		Debug::Arr($id,'Unique Department: '. $id, __FILE__, __LINE__, __METHOD__,10);

		if ( $id === FALSE ) {
			return TRUE;
		} else {
			if ($id == $this->getId() ) {
				return TRUE;
			}
		}

		return FALSE;
	}
	static function getNextAvailableManualId( $company_id = NULL ) {
		global $current_company;

		if ( $company_id == '' ANd is_object($current_company) ) {
			$company_id = $current_company->getId();
		} elseif ( $company_id == '' AND isset($this) AND is_object($this) ) {
			$company_id = $this->getCompany();
		}

		$dlf = new DepartmentListFactory();
		$dlf->getHighestManualIDByCompanyId( $company_id );
		if ( $dlf->getRecordCount() > 0 ) {
			$next_available_manual_id = $dlf->getCurrent()->getManualId()+1;
		} else {
			$next_available_manual_id = 1;
		}

		return $next_available_manual_id;
	}

	function getManualID() {
		if ( isset($this->data['manual_id']) ) {
			return $this->data['manual_id'];
		}

		return FALSE;
	}
	function setManualID($value) {
		$value = trim($value);

		if (	$this->Validator->isNumeric(	'manual_id',
												$value,
												TTi18n::gettext('Code is invalid'))
				AND
					$this->Validator->isTrue(		'manual_id',
													$this->isUniqueManualID($value),
													TTi18n::gettext('Code is already in use, please enter a different one'))
												) {

			$this->data['manual_id'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function isUniqueName($name) {
		if ( $this->getCompany() == FALSE ) {
			return FALSE;
		}

		$name = trim($name);
		if ( $name == '' ) {
			return FALSE;
		}

		$ph = array(
					'company_id' => $this->getCompany(),
					'name' => $name,
					);

		$query = 'select id from '. $this->table .'
					where company_id = ?
						AND name = ?
						AND deleted = 0';
		$name_id = $this->db->GetOne($query, $ph);
		//Debug::Arr($name_id,'Unique Name: '. $name, __FILE__, __LINE__, __METHOD__,10);

		if ( $name_id === FALSE ) {
			return TRUE;
		} else {
			if ($name_id == $this->getId() ) {
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

		if 	(	$this->Validator->isLength(		'name',
												$name,
												TTi18n::gettext('Department name is too short or too long'),
												2,
												100)
					AND
						$this->Validator->isTrue(		'name',
														$this->isUniqueName($name),
														TTi18n::gettext('Department already exists'))

												) {

			$this->data['name'] = $name;

			return TRUE;
		}

		return FALSE;
	}

	function getBranch() {
		$dblf = new DepartmentBranchListFactory();
		$dblf->getByDepartmentId( $this->getId() );
		foreach ($dblf as $department_branch) {
			$branch_list[] = $department_branch->getBranch();
		}

		if ( isset($branch_list) ) {
			return $branch_list;
		}

		return FALSE;
	}
	function setBranch($ids) {
		if (is_array($ids) and count($ids) > 0) {
			//If needed, delete mappings first.
			$dblf = new DepartmentBranchListFactory();
			$dblf->getByDepartmentId( $this->getId() );

			$branch_ids = array();
			foreach ($dblf as $department_branch) {
				$branch_id = $department_branch->getBranch();
				Debug::text('Department ID: '. $department_branch->getDepartment() .' Branch: '. $branch_id, __FILE__, __LINE__, __METHOD__, 10);

				//Delete branches that are not selected.
				if ( !in_array($branch_id, $ids) ) {
					Debug::text('Deleting DepartmentBranch: '. $branch_id, __FILE__, __LINE__, __METHOD__, 10);
					$department_branch->Delete();
				} else {
					//Save branch ID's that need to be updated.
					Debug::text('NOT Deleting DepartmentBranch: '. $branch_id, __FILE__, __LINE__, __METHOD__, 10);
					$branch_ids[] = $branch_id;
				}
			}

			//Insert new mappings.
			$dbf = new DepartmentBranchFactory();
			foreach ($ids as $id) {
				if ( !in_array($id, $branch_ids) ) {
					$dbf->setDepartment( $this->getId() );
					$dbf->setBranch( $id );

					if ($this->Validator->isTrue(		'branch',
														$dbf->Validator->isValid(),
														TTi18n::gettext('Branch selection is invalid'))) {
						$dbf->save();
					}
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	function preSave() {
		if ( $this->getStatus() == FALSE ) {
			$this->setStatus(10);
		}

		if ( $this->getManualID() == FALSE ) {
			$this->setManualID( DepartmentListFactory::getNextAvailableManualId( $this->getCompany() ) );
		}

		return TRUE;
	}

	function postSave() {
		$this->removeCache( $this->getId() );

		if ( $this->getDeleted() == TRUE ) {
			Debug::Text('UnAssign Hours from Department: '. $this->getId(), __FILE__, __LINE__, __METHOD__,10);
			//Unassign hours from this department.
			$pcf = new PunchControlFactory();
			$udtf = new UserDateTotalFactory();
			$uf = new UserFactory();
			$sf = new StationFactory();
			$sf_b = new ScheduleFactory();
			$udf = new UserDefaultFactory();
			$rstf = new RecurringScheduleTemplateFactory();

			$query = 'update '. $pcf->getTable() .' set department_id = 0 where department_id = '. $this->getId();
			$this->db->Execute($query);

			$query = 'update '. $udtf->getTable() .' set department_id = 0 where department_id = '. $this->getId();
			$this->db->Execute($query);

			$query = 'update '. $sf_b->getTable() .' set department_id = 0 where department_id = '. $this->getId();
			$this->db->Execute($query);

			$query = 'update '. $uf->getTable() .' set default_department_id = 0 where company_id = '. $this->getCompany() .' AND default_department_id = '. $this->getId();
			$this->db->Execute($query);

			$query = 'update '. $udf->getTable() .' set default_department_id = 0 where company_id = '. $this->getCompany() .' AND default_department_id = '. $this->getId();
			$this->db->Execute($query);

			$query = 'update '. $sf->getTable() .' set department_id = 0 where company_id = '. $this->getCompany() .' AND department_id = '. $this->getId();
			$this->db->Execute($query);

			$query = 'update '. $rstf->getTable() .' set department_id = 0 where department_id = '. $this->getId();
			$this->db->Execute($query);
		}

		return TRUE;
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
						case 'status':
							$function = 'get'.$variable;
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = Option::getByKey( $this->$function(), $this->getOptions( $variable ) );
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
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Department'), NULL, $this->getTable() );
	}
}
?>
