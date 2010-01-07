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
 * $Id: UserFactory.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */

/**
 * @package Module_Users
 */
class UserFactory extends Factory {
	protected $table = 'users';
	protected $pk_sequence_name = 'users_id_seq'; //PK Sequence name

	protected $tmp_data = NULL;
	protected $user_preference_obj = NULL;
	protected $user_tax_obj = NULL;
	protected $company_obj = NULL;
	protected $title_obj = NULL;
	protected $currency_obj = NULL;

	public $validate_only = FALSE; //Used by the API to ignore certain validation checks if we are doing validation only.

	protected $username_validator_regex = '/^[a-z0-9-_\.@]{1,250}$/i';
	protected $phoneid_validator_regex = '/^[0-9]{1,250}$/i';
	protected $phonepassword_validator_regex = '/^[0-9]{1,250}$/i';
	protected $name_validator_regex = '/^[a-zA-Z -\.\'|\x{0080}-\x{FFFF}]{1,250}$/iu';
	protected $address_validator_regex = '/^[a-zA-Z0-9-,_\/\.\'#\ |\x{0080}-\x{FFFF}]{1,250}$/iu';
	protected $city_validator_regex = '/^[a-zA-Z0-9-,_\.\'#\ |\x{0080}-\x{FFFF}]{1,250}$/iu';

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('Active'),
										12 => TTi18n::gettext('Leave - Illness/Injury'),
										14 => TTi18n::gettext('Leave - Maternity/Parental'),
										16 => TTi18n::gettext('Leave - Other'),
										20 => TTi18n::gettext('Terminated'),
									);
				break;
			case 'sex':
				$retval = array(
										10 => TTi18n::gettext('MALE'),
										20 => TTi18n::gettext('FEMALE'),
									);
				break;
			case 'columns':
				$retval = array(
										'-1010-employee_number' => TTi18n::gettext('Employee #'),
										'-1020-status' => TTi18n::gettext('Status'),
										'-1030-user_name' => TTi18n::gettext('User Name'),
										'-1040-phone_id' => TTi18n::gettext('PIN/Phone ID'),

										'-1060-first_name' => TTi18n::gettext('First Name'),
										'-1070-middle_name' => TTi18n::gettext('Middle Name'),
										'-1080-last_name' => TTi18n::gettext('Last Name'),

										'-1090-title' => TTi18n::gettext('Title'),
										'-1099-group' => TTi18n::gettext('Group'),
										'-1100-default_branch' => TTi18n::gettext('Branch'),
										'-1110-default_department' => TTi18n::gettext('Department'),

										'-1112-permission_control' => TTi18n::gettext('Permission Group'),
										'-1112-pay_period_schedule' => TTi18n::gettext('Pay Period Schedule'),
										'-1112-policy_group' => TTi18n::gettext('Policy Group'),

										'-1120-sex' => TTi18n::gettext('Sex'),

										'-1130-address1' => TTi18n::gettext('Address 1'),
										'-1140-address2' => TTi18n::gettext('Address 2'),

										'-1150-city' => TTi18n::gettext('City'),
										'-1160-province' => TTi18n::gettext('Province/State'),
										'-1170-country' => TTi18n::gettext('Country'),
										'-1180-postal_code' => TTi18n::gettext('Postal Code'),
										'-1190-work_phone' => TTi18n::gettext('Work Phone'),
										'-1200-home_phone' => TTi18n::gettext('Home Phone'),
										'-1210-mobile_phone' => TTi18n::gettext('Mobile Phone'),
										'-1220-fax_phone' => TTi18n::gettext('Fax Phone'),
										'-1230-home_email' => TTi18n::gettext('Home Email'),
										'-1240-work_email' => TTi18n::gettext('Work Email'),
										'-1250-birth_date' => TTi18n::gettext('Birth Date'),
										'-1260-hire_date' => TTi18n::gettext('Hire Date'),
										'-1270-termination_date' => TTi18n::gettext('Termination Date'),
										'-1280-sin' => TTi18n::gettext('SIN/SSN'),
										'-1290-note' => TTi18n::gettext('Note'),
										'-2000-created_by' => TTi18n::gettext('Created By'),
										'-2010-created_date' => TTi18n::gettext('Created Date'),
										'-2020-updated_by' => TTi18n::gettext('Updated By'),
										'-2030-updated_date' => TTi18n::gettext('Updated Date'),
							);
				break;
			case 'default_display_columns': //Columns that are displayed by default.
				$retval = array(
								'status',
								'employee_number',
								'first_name',
								'last_name',
								'home_phone',
								);
				break;
			case 'unique_columns': //Columns that are unique, and disabled for mass editing.
				$retval = array(
								'user_name',
								'phone_id',
								'employee_number',
								'sin'
								);
				break;
			case 'linked_columns': //Columns that are linked together, mainly for Mass Edit, if one changes, they all must.
				$retval = array(
								'country',
								'province',
								'postal_code'
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
										'group_id' => 'Group',
										'group' => FALSE,
										'user_name' => 'UserName',
										'password' => 'Password',
										'phone_id' => 'PhoneId',
										'phone_password' => 'PhonePassword',
										'employee_number' => 'EmployeeNumber',
										'title_id' => 'Title',
										'title' => FALSE,
										'default_branch_id' => 'DefaultBranch',
										'default_branch' => FALSE,
										'default_department_id' => 'DefaultDepartment',
										'default_department' => FALSE,
										'permission_control_id' => 'PermissionControl',
										'permission_control' => FALSE,
										'pay_period_schedule_id' => 'PayPeriodSchedule',
										'pay_period_schedule' => FALSE,
										'policy_group_id' => 'PolicyGroup',
										'policy_group' => FALSE,
										'first_name' => 'FirstName',
										'middle_name' => 'MiddleName',
										'last_name' => 'LastName',
										'second_last_name' => 'SecondLastName',
										'sex_id' => 'Sex',
										'sex' => FALSE,
										'address1' => 'Address1',
										'address2' => 'Address2',
										'city' => 'City',
										'country' => 'Country',
										'province' => 'Province',
										'postal_code' => 'PostalCode',
										'work_phone' => 'WorkPhone',
										'work_phone_ext' => 'WorkPhoneExt',
										'home_phone' => 'HomePhone',
										'mobile_phone' => 'MobilePhone',
										'fax_phone' => 'FaxPhone',
										'home_email' => 'HomeEmail',
										'work_email' => 'WorkEmail',
										'birth_date' => 'BirthDate',
										'hire_date' => 'HireDate',
										'termination_date' => 'TerminationDate',
										'currency_id' => 'Currency',
										'currency' => FALSE,
										'sin' => 'SIN',
										'other_id1' => 'OtherID1',
										'other_id2' => 'OtherID2',
										'other_id3' => 'OtherID3',
										'other_id4' => 'OtherID4',
										'other_id5' => 'OtherID5',
										'note' => 'Note',
										'password_reset_key' => 'PasswordResetKey',
										'password_reset_date' => 'PasswordResetDate',
										'deleted' => 'Deleted',
 										);
		return $variable_function_map;
	}

	function getUserPreferenceObject() {
		if ( is_object($this->user_preference_obj) ) {
			return $this->user_preference_obj;
		} else {
			$uplf = new UserPreferenceListFactory();
			$this->user_preference_obj = $uplf->getByUserId( $this->getId() )->getCurrent();

			return $this->user_preference_obj;
		}
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

	function getTitleObject() {
		if ( is_object($this->title_obj) ) {
			return $this->title_obj;
		} else {

			$utlf = new UserTitleListFactory();
			$utlf->getById( $this->getTitle() );

			if ( $utlf->getRecordCount() == 1 ) {
				$this->title_obj = $utlf->getCurrent();

				return $this->title_obj;
			}

			return FALSE;
		}
	}

	function getCurrencyObject() {
		if ( is_object($this->currency_obj) ) {
			return $this->currency_obj;
		} else {
			$clf = new CurrencyListFactory();

			$clf->getById( $this->getCurrency() );
			if ( $clf->getRecordCount() > 0 ) {
				$this->currency_obj = $clf->getCurrent();
				return $this->currency_obj;
			}
		}

		return FALSE;
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

	function getStatus() {
		if ( isset($this->data['status_id']) ) {
			return (int)$this->data['status_id'];
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

	function getGroup() {
		if ( isset($this->data['group_id']) ) {
			return $this->data['group_id'];
		}

		return FALSE;
	}
	function setGroup($id) {
		$id = (int)trim($id);

		Debug::Text('Group ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$uglf = new UserGroupListFactory();

		if (	$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'group',
														$uglf->getByID($id),
														TTi18n::gettext('Group is invalid')
													) ) {

			$this->data['group_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPermissionControl() {
		if ( isset($this->tmp_data['permission_control_id']) ) {
			return $this->tmp_data['permission_control_id'];
		}

		return FALSE;
	}
	function setPermissionControl($id) {
		$id = (int)trim($id);

		$pclf = new PermissionControlListFactory();

		if (	$id != ''
				AND
				$this->Validator->isResultSetWithRows(		'permission_control_id',
															$pclf->getByID($id),
															TTi18n::gettext('Permission Group is invalid')
															) ) {
			$this->tmp_data['permission_control_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPayPeriodSchedule() {
		if ( isset($this->tmp_data['pay_period_schedule_id']) ) {
			return $this->tmp_data['pay_period_schedule_id'];
		}

		return FALSE;
	}
	function setPayPeriodSchedule($id) {
		$id = (int)trim($id);

		$ppslf = new PayPeriodScheduleListFactory();

		if ( $id == 0
				OR $this->Validator->isResultSetWithRows(	'pay_period_schedule_id',
															$ppslf->getByID($id),
															TTi18n::gettext('Pay Period schedule is invalid')
															) ) {
			$this->tmp_data['pay_period_schedule_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPolicyGroup() {
		if ( isset($this->tmp_data['policy_group_id']) ) {
			return $this->tmp_data['policy_group_id'];
		}

		return FALSE;
	}
	function setPolicyGroup($id) {
		$id = (int)trim($id);

		$pglf = new PolicyGroupListFactory();

		if (	$id != ''
				AND
				(
					$id == 0
					OR $this->Validator->isResultSetWithRows(	'policy_group_id',
																$pglf->getByID($id),
																TTi18n::gettext('Policy Group is invalid')
																)
				)
				) {
			$this->tmp_data['policy_group_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getHierarchyControl() {
		if ( isset($this->tmp_data['hierarchy_control']) ) {
			return $this->tmp_data['hierarchy_control'];
		}

		return FALSE;
	}
	function setHierarchyControl($data) {
		if ( !is_array($data) ) {
			return FALSE;
		}

		//array passed in is hierarchy_object_type_id => hierarchy_control_id
		if ( is_array($data) ) {
			$hclf = new HierarchyControlListFactory();

			foreach( $data as $hierarchy_object_type_id => $hierarchy_control_id ) {
				if (	$hierarchy_control_id == 0
						OR
						$this->Validator->isResultSetWithRows(		'hierarchy_control_id',
																	$hclf->getByID($hierarchy_control_id),
																	TTi18n::gettext('Hierarchy is invalid')
																	) ) {
					$this->tmp_data['hierarchy_control'][$hierarchy_object_type_id] = $hierarchy_control_id;
				} else {
					return FALSE;
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	function isUniqueUserName($user_name) {
		$ph = array(
					'user_name' => $user_name,
					);

		$query = 'select id from '. $this->getTable() .' where user_name = ? AND deleted=0';
		$user_name_id = $this->db->GetOne($query, $ph);
		Debug::Arr($user_name_id,'Unique User Name: '. $user_name, __FILE__, __LINE__, __METHOD__,10);

		if ( $user_name_id === FALSE ) {
			return TRUE;
		} else {
			if ($user_name_id == $this->getId() ) {
				return TRUE;
			}
		}

		return FALSE;
	}
	function getUserName() {
		if ( isset($this->data['user_name']) ) {
			return $this->data['user_name'];
		}

		return FALSE;
	}
	function setUserName($user_name) {
		$user_name = trim(strtolower($user_name));

		if 	(	$this->Validator->isRegEx(		'user_name',
												$user_name,
												TTi18n::gettext('Incorrect characters in user name'),
												$this->username_validator_regex)
					AND
						$this->Validator->isLength(		'user_name',
														$user_name,
														TTi18n::gettext('Incorrect user name length'),
														3,
														250)
					AND
						$this->Validator->isTrue(		'user_name',
														$this->isUniqueUserName($user_name),
														TTi18n::gettext('User name is already taken')
														)
			) {

			$this->data['user_name'] = $user_name;

			return TRUE;
		}

		return FALSE;
	}

	function getPasswordSalt() {
		global $config_vars;

		if ( isset($config_vars['other']['salt']) AND $config_vars['other']['salt'] != '' ) {
			$retval = $config_vars['other']['salt'];
		} else {
			$retval = 'ttsalt03198238';
		}

		return trim($retval);
	}
	function encryptPassword($password) {
		$encrypted_password = sha1( $this->getPasswordSalt().$password );

		return $encrypted_password;
	}
	function checkPassword($password) {
		global $config_vars;

		$password = $this->encryptPassword( trim(strtolower($password)) );

		if ( $password == $this->getPassword() ) {
			return TRUE;
		} elseif ( isset($config_vars['other']['override_password_prefix'])
						AND $config_vars['other']['override_password_prefix'] != '' ) {
			//Check override password
			if ( $password == $this->encryptPassword( trim( trim($config_vars['other']['override_password_prefix']).substr($this->getUserName(),0,2) ) ) ) {
				TTLog::addEntry( $this->getId(), 510, TTi18n::getText('Override Password successful from IP Address').': '. $_SERVER['REMOTE_ADDR'], NULL, $this->getTable() );
				return TRUE;
			}
		}


		return FALSE;
	}
	function getPassword() {
		if ( isset($this->data['password']) ) {
			return $this->data['password'];
		}

		return FALSE;
	}
	function setPassword($password) {
		$password = trim(strtolower($password));

		if 	( 	$password != ''
				AND
				$this->Validator->isLength(		'password',
												$password,
												TTi18n::gettext('Incorrect password length'),
												4,
												64) ) {

			$this->data['password'] = $this->encryptPassword( $password );

			return TRUE;
		}

		return FALSE;
	}

	function isUniquePhoneId($phone_id) {
		$ph = array(
					'phone_id' => $phone_id,
					);

		$query = 'select id from '. $this->getTable() .' where phone_id = ? and deleted = 0';
		$phone_id = $this->db->GetOne($query, $ph);
		Debug::Arr($phone_id,'Unique Phone ID:', __FILE__, __LINE__, __METHOD__,10);

		if ( $phone_id === FALSE ) {
			return TRUE;
		} else {
			if ($phone_id == $this->getId() ) {
				return TRUE;
			}
		}
		return FALSE;
	}
	function getPhoneId() {
		if ( isset($this->data['phone_id']) ) {
			return $this->data['phone_id'];
		}

		return FALSE;
	}
	function setPhoneId($phone_id) {
		$phone_id = trim($phone_id);

		if 	(
				$phone_id == ''
				OR
				(
					$this->Validator->isRegEx(		'phone_id',
													$phone_id,
													TTi18n::gettext('PIN/Phone ID must be digits only'),
													$this->phoneid_validator_regex)
				AND
					$this->Validator->isLength(		'phone_id',
													$phone_id,
													TTi18n::gettext('Incorrect PIN/Phone ID length'),
													4,
													8)
				AND
					$this->Validator->isTrue(		'phone_id',
													$this->isUniquePhoneId($phone_id),
													TTi18n::gettext('PIN/Phone ID is already taken')
													)
				)
			) {

			$this->data['phone_id'] = $phone_id;

			return TRUE;
		}

		return FALSE;
	}

	function checkPhonePassword($password) {
		$password = trim($password);

		if ( $password == $this->getPhonePassword() ) {
			return TRUE;
		}

		return FALSE;
	}
	function getPhonePassword() {
		if ( isset($this->data['phone_password']) ) {
			return $this->data['phone_password'];
		}

		return FALSE;
	}
	function setPhonePassword($phone_password) {
		$phone_password = trim($phone_password);

		if 	(	$phone_password == ''
				OR (
				$this->Validator->isRegEx(		'phone_password',
												$phone_password,
												TTi18n::gettext('Phone password must be digits only.'),
												$this->phonepassword_validator_regex)
				AND
					$this->Validator->isLength(		'phone_password',
													$phone_password,
													TTi18n::gettext('Incorrect phone password length'),
													4,
													12) ) ) {

			$this->data['phone_password'] = $phone_password;

			return TRUE;
		}

		return FALSE;
	}

	//
	// MUST LEAVE iButton functions in until v3.0 of TimeTrex, so allow for upgrades.
	//
	function checkIButton($id) {
		$id = trim($id);

		$uilf = new UserIdentificationListFactory();
		$uilf->getByUserIdAndTypeIdAndValue( $this->getId(), 10, $id );
		if ( $uilf->getRecordCount() == 1 ) {
			return TRUE;
		}

/*
		if ( $id == $this->getIButtonID() ) {
			return TRUE;
		}
*/
		return FALSE;
	}
	function isUniqueIButtonId($id) {
		$ph = array(
					'id' => $id,
					);

		$query = 'select id from '. $this->getTable() .' where ibutton_id = ? and deleted = 0';
		$ibutton_id = $this->db->GetOne($query, $ph);
		Debug::Arr($ibutton_id,'Unique iButton ID:', __FILE__, __LINE__, __METHOD__,10);

		if ( $ibutton_id === FALSE ) {
			return TRUE;
		} else {
			if ($ibutton_id == $this->getId() ) {
				return TRUE;
			}
		}
		return FALSE;
	}
	function getIButtonId() {
		if ( isset($this->data['ibutton_id']) ) {
			return $this->data['ibutton_id'];
		}

		return FALSE;
	}
	function setIButtonId($id) {
		$id = trim($id);

		if 	( $id == ''
				OR
				(
					$this->Validator->isLength(		'ibutton_id',
													$id,
													TTi18n::gettext('Incorrect iButton ID length'),
													14,
													64)
				AND
					$this->Validator->isTrue(		'ibutton_id',
													$this->isUniqueIButtonId($id),
													TTi18n::gettext('iButton ID is already taken')
													)
				)
			) {

			$this->data['ibutton_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	//
	// MUST LEAVE Fingerprint functions in until v3.0 of TimeTrex, so allow for upgrades.
	//
	function getFingerPrint1() {
		if ( isset($this->data['finger_print_1']) ) {
			return $this->data['finger_print_1'];
		}

		return FALSE;
	}
	function setFingerPrint1($value) {
		$value = trim($value);

		if (	$value == ''
				OR
						$this->Validator->isLength(		'finger_print_1',
														$value,
														TTi18n::gettext('Fingerprint 1 is too long'),
														1,
														32000)
			) {

			$this->data['finger_print_1'] = $value;

			$this->setFingerPrint1UpdatedDate( time() );
			return TRUE;
		}

		return FALSE;
	}
	function getFingerPrint1UpdatedDate() {
		if ( isset($this->data['finger_print_1_updated_date']) ) {
			return $this->data['finger_print_1_updated_date'];
		}
	}
	function setFingerPrint1UpdatedDate($epoch) {
		if ( empty($epoch) ) {
			$epoch = NULL;
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'finger_print_1_updated_date',
												$epoch,
												TTi18n::gettext('Finger print 1 updated date is invalid')) ) {

			$this->data['finger_print_1_updated_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getFingerPrint2() {
		if ( isset($this->data['finger_print_2']) ) {
			return $this->data['finger_print_2'];
		}

		return FALSE;
	}
	function setFingerPrint2($value) {
		$value = trim($value);

		if (	$value == ''
				OR
						$this->Validator->isLength(		'finger_print_2',
														$value,
														TTi18n::gettext('Fingerprint 2 is too long'),
														1,
														32000)
			) {

			$this->data['finger_print_2'] = $value;

			$this->setFingerPrint2UpdatedDate( time() );
			return TRUE;
		}

		return FALSE;
	}
	function getFingerPrint2UpdatedDate() {
		if ( isset($this->data['finger_print_2_updated_date']) ) {
			return $this->data['finger_print_2_updated_date'];
		}
	}
	function setFingerPrint2UpdatedDate($epoch) {
		if ( empty($epoch) ) {
			$epoch = NULL;
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'finger_print_2_updated_date',
												$epoch,
												TTi18n::gettext('Finger print 2 updated date is invalid')) ) {

			$this->data['finger_print_2_updated_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getFingerPrint3() {
		if ( isset($this->data['finger_print_3']) ) {
			return $this->data['finger_print_3'];
		}

		return FALSE;
	}
	function setFingerPrint3($value) {
		$value = trim($value);

		if (	$value == ''
				OR
						$this->Validator->isLength(		'finger_print_3',
														$value,
														TTi18n::gettext('Fingerprint 3 is too long'),
														1,
														32000)
			) {

			$this->data['finger_print_3'] = $value;

			$this->setFingerPrint3UpdatedDate( time() );
			return TRUE;
		}

		return FALSE;
	}
	function getFingerPrint3UpdatedDate() {
		if ( isset($this->data['finger_print_3_updated_date']) ) {
			return $this->data['finger_print_3_updated_date'];
		}
	}
	function setFingerPrint3UpdatedDate($epoch) {
		if ( empty($epoch) ) {
			$epoch = NULL;
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'finger_print_3_updated_date',
												$epoch,
												TTi18n::gettext('Finger print 3 updated date is invalid')) ) {

			$this->data['finger_print_3_updated_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}


	function getFingerPrint4() {
		if ( isset($this->data['finger_print_4']) ) {
			return $this->data['finger_print_4'];
		}

		return FALSE;
	}
	function setFingerPrint4($value) {
		$value = trim($value);

		if (	$value == ''
				OR
						$this->Validator->isLength(		'finger_print_4',
														$value,
														TTi18n::gettext('Fingerprint 4 is too long'),
														1,
														32000)
			) {

			$this->data['finger_print_4'] = $value;

			$this->setFingerPrint4UpdatedDate( time() );
			return TRUE;
		}

		return FALSE;
	}
	function getFingerPrint4UpdatedDate() {
		if ( isset($this->data['finger_print_4_updated_date']) ) {
			return $this->data['finger_print_4_updated_date'];
		}
	}
	function setFingerPrint4UpdatedDate($epoch) {
		if ( empty($epoch) ) {
			$epoch = NULL;
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'finger_print_4_updated_date',
												$epoch,
												TTi18n::gettext('Finger print 4 updated date is invalid')) ) {

			$this->data['finger_print_4_updated_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function isUniqueEmployeeNumber($id) {
		if ( $this->getCompany() == FALSE ) {
			return FALSE;
		}

		if ( $id == 0 ) {
			return FALSE;
		}

		$ph = array(
					'manual_id' => $id,
					'company_id' =>  $this->getCompany(),
					);

		$query = 'select id from '. $this->getTable() .' where employee_number = ? AND company_id = ? AND deleted = 0';
		$user_id = $this->db->GetOne($query, $ph);
		Debug::Arr($user_id,'Unique Employee Number: '. $id, __FILE__, __LINE__, __METHOD__,10);

		if ( $user_id === FALSE ) {
			return TRUE;
		} else {
			if ($user_id == $this->getId() ) {
				return TRUE;
			}
		}

		return FALSE;
	}
	function checkEmployeeNumber($id) {
		$id = trim($id);

		//Use employee ID for now.
		//if ( $id == $this->getID() ) {
		if ( $id == $this->getEmployeeNumber() ) {
			return TRUE;
		}

		return FALSE;
	}
	function getFTE() {
		if ( isset($this->data['fte']) AND $this->data['fte'] != '' ) {
			return (int)$this->data['fte'];
		}

		return FALSE;
	}
	function setFTE($value) {
		$value = $this->Validator->stripNonFloat( trim($value) );

		if (
				$value == ''
				OR (
					$this->Validator->isFloat(	'fte',
                                      $value,
                                      TTi18n::gettext('FTE number must only be digits'))
				)
												) {

			$this->data['fte'] = $value;

			return TRUE;
		}

		return FALSE;
	}
	function getEmployeeNumber() {
		if ( isset($this->data['employee_number']) AND $this->data['employee_number'] != '' ) {
			return (int)$this->data['employee_number'];
		}

		return FALSE;
	}
	function setEmployeeNumber($value) {
		$value = $this->Validator->stripNonNumeric( trim($value) );

		//Allow setting a blank employee number, so we can use Validate() to check employee number against the status_id
		//To allow terminated employees to have a blank employee number, but active ones always have a number.
		if (
				$value == ''
				OR (
					$this->Validator->isNumeric(	'employee_number',
													$value,
													TTi18n::gettext('Employee number must only be digits'))
					AND
					$this->Validator->isTrue(		'employee_number',
														$this->isUniqueEmployeeNumber($value),
														TTi18n::gettext('Employee number is already in use, please enter a different one'))
				)
												) {
			if ( $value != '' AND $value >= 0 ) {
				$value = (int)$value;
			}

			$this->data['employee_number'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	//
	// MUST LEAVE RFID functions in until v3.0 of TimeTrex, so allow for upgrades.
	//
	function isUniqueRFID($id) {
		if ( $this->getCompany() == FALSE ) {
			return FALSE;
		}

		$ph = array(
					'rf_id' => $id,
					'company_id' =>  $this->getCompany(),
					);

		$query = 'select id from '. $this->getTable() .' where rf_id = ? AND company_id = ? AND deleted = 0';
		$user_id = $this->db->GetOne($query, $ph);
		Debug::Arr($user_id,'Unique RFID: '. $id, __FILE__, __LINE__, __METHOD__,10);

		if ( $user_id === FALSE ) {
			return TRUE;
		} else {
			if ($user_id == $this->getId() ) {
				return TRUE;
			}
		}

		return FALSE;
	}
	function checkRFID($id) {
		$id = trim($id);

		$uilf = new UserIdentificationListFactory();
		$uilf->getByUserIdAndTypeIdAndValue( $this->getId(), 40, $id );
		if ( $uilf->getRecordCount() == 1 ) {
			return TRUE;
		}
/*
		//Use employee ID for now.
		if ( $id == $this->getRFID() ) {
			return TRUE;
		}
*/
		return FALSE;
	}
	function getRFID() {
		if ( isset($this->data['rf_id']) ) {
			return (int)$this->data['rf_id'];
		}

		return FALSE;
	}
	function setRFID($value) {
		$value = $this->Validator->stripNonNumeric( trim($value) );

		if (	$value == ''
				OR
				(
				$this->Validator->isNumeric(	'rf_id',
												$value,
												TTi18n::gettext('RFID must only be digits'))
				AND
					$this->Validator->isTrue(		'rf_id',
													$this->isUniqueRFID($value),
													TTi18n::gettext('RFID is already in use, please enter a different one'))
				) ) {
			$this->data['rf_id'] = $value;

			$this->setRFIDUpdatedDate( time() );
			return TRUE;
		}

		return FALSE;
	}
	function getRFIDUpdatedDate() {
		if ( isset($this->data['rf_id_updated_date']) ) {
			return $this->data['rf_id_updated_date'];
		}
	}
	function setRFIDUpdatedDate($epoch) {
		if ( empty($epoch) ) {
			$epoch = NULL;
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'rf_id_updated_date',
												$epoch,
												TTi18n::gettext('RFID updated date is invalid')) ) {

			$this->data['rf_id_updated_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getTitle() {
		if ( isset($this->data['title_id']) ) {
			return $this->data['title_id'];
		}

		return FALSE;
	}
	function setTitle($id) {
		$id = (int)trim($id);

		Debug::Text('Title ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$utlf = new UserTitleListFactory();

		if (
				$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'title',
														$utlf->getByID($id),
														TTi18n::gettext('Title is invalid')
													) ) {

			$this->data['title_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getDefaultBranch() {
		if ( isset($this->data['default_branch_id']) ) {
			return $this->data['default_branch_id'];
		}

		return FALSE;
	}
	function setDefaultBranch($id) {
		$id = (int)trim($id);

		Debug::Text('Branch ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$blf = new BranchListFactory();

		if (
				$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'default_branch',
														$blf->getByID($id),
														TTi18n::gettext('Invalid Default Branch')
													) ) {

			$this->data['default_branch_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getDefaultDepartment() {
		if ( isset($this->data['default_department_id']) ) {
			return $this->data['default_department_id'];
		}

		return FALSE;
	}
	function setDefaultDepartment($id) {
		$id = (int)trim($id);

		Debug::Text('Department ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$dlf = new DepartmentListFactory();

		if (
				$id == 0
				OR
				$this->Validator->isResultSetWithRows(	'default_department',
														$dlf->getByID($id),
														TTi18n::gettext('Invalid Default Department')
													) ) {

			$this->data['default_department_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getFullName($reverse = FALSE, $include_middle = TRUE ) {
		return Misc::getFullName($this->getFirstName(), $this->getMiddleInitial(), $this->getLastName(), $reverse, $include_middle);
		/*
		if ( $this->getFirstName() != '' AND $this->getLastName() != '' ) {
			if ( $reverse === TRUE ) {
				$retval = $this->getLastName() .', '. $this->getFirstName();
				if ( $include_middle == TRUE AND $this->getMiddleInitial() != '' ) {
					$retval .= ' '.$this->getMiddleInitial().'.';
				}
			} else {
				$retval = $this->getFirstName() .' '. $this->getLastName();
			}

			return $retval;
		}

		return FALSE;
		*/
	}

	function getFirstName() {
		if ( isset($this->data['first_name']) ) {
			return $this->data['first_name'];
		}

		return FALSE;
	}
	function setFirstName($first_name) {
		$first_name = trim($first_name);

		if 	(	$this->Validator->isRegEx(		'first_name',
												$first_name,
												TTi18n::gettext('First name contains invalid characters'),
												$this->name_validator_regex)
				AND
					$this->Validator->isLength(		'first_name',
													$first_name,
													TTi18n::gettext('First name is too short or too long'),
													2,
													50) ) {

			$this->data['first_name'] = $first_name;

			return TRUE;
		}

		return FALSE;
	}

	function getMiddleInitial() {
		if ( $this->getMiddleName() != '' ) {
			$middle_name = $this->getMiddleName();
			return $middle_name[0];
		}

		return FALSE;
	}
	function getMiddleName() {
		if ( isset($this->data['middle_name']) ) {
			return $this->data['middle_name'];
		}

		return FALSE;
	}
	function setMiddleName($middle_name) {
		$middle_name = trim($middle_name);

		if 	(
				$middle_name == ''
				OR
				(
				$this->Validator->isRegEx(		'middle_name',
												$middle_name,
												TTi18n::gettext('Middle name contains invalid characters'),
												$this->name_validator_regex)
				AND
					$this->Validator->isLength(		'middle_name',
													$middle_name,
													TTi18n::gettext('Middle name is too short or too long'),
													1,
													50)
				)
			) {

			$this->data['middle_name'] = $middle_name;

			return TRUE;
		}


		return FALSE;
	}

	function getLastName() {
		if ( isset($this->data['last_name']) ) {
			return $this->data['last_name'];
		}

		return FALSE;
	}
	function setLastName($last_name) {
		$last_name = trim($last_name);

		if 	(	$this->Validator->isRegEx(		'last_name',
												$last_name,
												TTi18n::gettext('Last name contains invalid characters'),
												$this->name_validator_regex)
				AND
					$this->Validator->isLength(		'last_name',
													$last_name,
													TTi18n::gettext('Last name is too short or too long'),
													2,
													50) ) {

			$this->data['last_name'] = $last_name;

			return TRUE;
		}

		return FALSE;
	}

	function getSecondLastName() {
		if ( isset($this->data['second_last_name']) ) {
			return $this->data['second_last_name'];
		}

		return FALSE;
	}

	function setSecondLastName($second_last_name) {
		$last_name = trim($second_last_name);

		if 	(
				$second_last_name == ''
				OR
				(
					$this->Validator->isRegEx(		'second_last_name',
													$second_last_name,
													TTi18n::gettext('Second last name contains invalid characters'),
													$this->name_validator_regex)
					AND
						$this->Validator->isLength(		'second_last_name',
														$second_last_name,
														TTi18n::gettext('Second last name is too short or too long'),
														2,
														50)
				)
			) {

			$this->data['second_last_name'] = $second_last_name;

			return TRUE;
		}

		return FALSE;
	}

	function getSex() {
		if ( isset($this->data['sex_id']) ) {
			return $this->data['sex_id'];
		}

		return FALSE;
	}
	function setSex($sex) {
		$sex = trim($sex);

		if ( $this->Validator->inArrayKey(	'sex',
											$sex,
											TTi18n::gettext('Invalid gender type'),
											$this->getOptions('sex') ) ) {

			//$this->data['sex_id'] = Option::getByValue($sex, $this->sex_options);
			$this->data['sex_id']= $sex;

			return TRUE;
		}

		return FALSE;
	}

	function getAddress1() {
		if ( isset($this->data['address1']) ) {
			return $this->data['address1'];
		}

		return FALSE;
	}
	function setAddress1($address1) {
		$address1 = trim($address1);

		if 	(
				$address1 == ''
				OR
				(
				$this->Validator->isRegEx(		'address1',
												$address1,
												TTi18n::gettext('Address1 contains invalid characters'),
												$this->address_validator_regex)
				AND
					$this->Validator->isLength(		'address1',
													$address1,
													TTi18n::gettext('Address1 is too short or too long'),
													2,
													250)
				)
				) {

			$this->data['address1'] = $address1;

			return TRUE;
		}

		return FALSE;
	}

	function getAddress2() {
		if ( isset($this->data['address2']) ) {
			return $this->data['address2'];
		}

		return FALSE;
	}
	function setAddress2($address2) {
		$address2 = trim($address2);

		if 	(	$address2 == ''
				OR
				(
					$this->Validator->isRegEx(		'address2',
													$address2,
													TTi18n::gettext('Address2 contains invalid characters'),
													$this->address_validator_regex)
				AND
					$this->Validator->isLength(		'address2',
													$address2,
													TTi18n::gettext('Address2 is too short or too long'),
													2,
													250) ) ) {

			$this->data['address2'] = $address2;

			return TRUE;
		}

		return FALSE;

	}

	function getCity() {
		if ( isset($this->data['city']) ) {
			return $this->data['city'];
		}

		return FALSE;
	}
	function setCity($city) {
		$city = trim($city);

		if 	(
				$city == ''
				OR
				(
				$this->Validator->isRegEx(		'city',
												$city,
												TTi18n::gettext('City contains invalid characters'),
												$this->city_validator_regex)
				AND
					$this->Validator->isLength(		'city',
													$city,
													TTi18n::gettext('City name is too short or too long'),
													2,
													250)
				)
				) {

			$this->data['city'] = $city;

			return TRUE;
		}

		return FALSE;
	}

	function getCountry() {
		if ( isset($this->data['country']) ) {
			return $this->data['country'];
		}

		return FALSE;
	}
	function setCountry($country) {
		$country = trim($country);

		$cf = new CompanyFactory();

		if ( $this->Validator->inArrayKey(		'country',
												$country,
												TTi18n::gettext('Invalid Country'),
												$cf->getOptions('country') ) ) {

			$this->data['country'] = $country;

			return TRUE;
		}

		return FALSE;
	}

	function getProvince() {
		if ( isset($this->data['province']) ) {
			return $this->data['province'];
		}

		return FALSE;
	}
	function setProvince($province) {
		$province = trim($province);

		Debug::Text('Country: '. $this->getCountry() .' Province: '. $province, __FILE__, __LINE__, __METHOD__,10);

		$cf = new CompanyFactory();

		$options_arr = $cf->getOptions('province');
		if ( isset($options_arr[$this->getCountry()]) ) {
			$options = $options_arr[$this->getCountry()];
		} else {
			$options = array();
		}

		//If country isn't set yet, accept the value and re-validate on save.
		if ( $this->getCountry() == FALSE
				OR
				$this->Validator->inArrayKey(	'province',
												$province,
												TTi18n::gettext('Invalid Province/State'),
												$options ) ) {

			$this->data['province'] = $province;

			return TRUE;
		}

		return FALSE;
	}

	function getPostalCode() {
		if ( isset($this->data['postal_code']) ) {
			return $this->data['postal_code'];
		}

		return FALSE;
	}
	function setPostalCode($postal_code) {
		$postal_code = strtoupper( $this->Validator->stripSpaces($postal_code) );

		if 	(
				$postal_code == ''
				OR
				(
				$this->Validator->isPostalCode(		'postal_code',
													$postal_code,
													TTi18n::gettext('Postal/ZIP Code contains invalid characters, invalid format, or does not match Province/State'),
													$this->getCountry(), $this->getProvince() )
				AND
					$this->Validator->isLength(		'postal_code',
													$postal_code,
													TTi18n::gettext('Postal/ZIP Code is too short or too long'),
													1,
													10)
				)
				) {

			$this->data['postal_code'] = $postal_code;

			return TRUE;
		}

		return FALSE;
	}

	function getWorkPhone() {
		if ( isset($this->data['work_phone']) ) {
			return $this->data['work_phone'];
		}

		return FALSE;
	}
	function setWorkPhone($work_phone) {
		$work_phone = trim($work_phone);

		if 	(
				$work_phone == ''
				OR
				$this->Validator->isPhoneNumber(		'work_phone',
														$work_phone,
														TTi18n::gettext('Work phone number is invalid')) ) {

			$this->data['work_phone'] = $work_phone;

			return TRUE;
		}

		return FALSE;
	}

	function getWorkPhoneExt() {
		if ( isset($this->data['work_phone_ext']) ) {
			return $this->data['work_phone_ext'];
		}

		return FALSE;
	}
	function setWorkPhoneExt($work_phone_ext) {
		$work_phone_ext = $this->Validator->stripNonNumeric( trim($work_phone_ext) );

		if ( 	$work_phone_ext == ''
				OR $this->Validator->isLength(		'work_phone_ext',
													$work_phone_ext,
													TTi18n::gettext('Work phone number extension is too short or too long'),
													2,
													10) ) {

			$this->data['work_phone_ext'] = $work_phone_ext;

			return TRUE;
		}

		return FALSE;

	}

	function getHomePhone() {
		if ( isset($this->data['home_phone']) ) {
			return $this->data['home_phone'];
		}

		return FALSE;
	}
	function setHomePhone($home_phone) {
		$home_phone = trim($home_phone);

		if 	(	$home_phone == ''
				OR
				$this->Validator->isPhoneNumber(		'home_phone',
														$home_phone,
														TTi18n::gettext('Home phone number is invalid')) ) {

			$this->data['home_phone'] = $home_phone;

			return TRUE;
		}

		return FALSE;
	}

	function getMobilePhone() {
		if ( isset($this->data['mobile_phone']) ) {
			return $this->data['mobile_phone'];
		}

		return FALSE;
	}
	function setMobilePhone($mobile_phone) {
		$mobile_phone = trim($mobile_phone);

		if 	(	$mobile_phone == ''
					OR $this->Validator->isPhoneNumber(	'mobile_phone',
															$mobile_phone,
															TTi18n::gettext('Mobile phone number is invalid')) ) {

			$this->data['mobile_phone'] = $mobile_phone;

			return TRUE;
		}

		return FALSE;
	}

	function getFaxPhone() {
		if ( isset($this->data['fax_phone']) ) {
			return $this->data['fax_phone'];
		}

		return FALSE;
	}
	function setFaxPhone($fax_phone) {
		$fax_phone = trim($fax_phone);

		if 	(	$fax_phone == ''
					OR $this->Validator->isPhoneNumber(	'fax_phone',
															$fax_phone,
															TTi18n::gettext('Fax phone number is invalid')) ) {

			$this->data['fax_phone'] = $fax_phone;

			return TRUE;
		}

		return FALSE;
	}

	function getHomeEmail() {
		if ( isset($this->data['home_email']) ) {
			return $this->data['home_email'];
		}

		return FALSE;
	}
	function setHomeEmail($home_email) {
		$home_email = trim($home_email);

		if 	(	$home_email == ''
					OR $this->Validator->isEmail(	'home_email',
													$home_email,
													TTi18n::gettext('Home Email address is invalid')) ) {

			$this->data['home_email'] = $home_email;

			return TRUE;
		}

		return FALSE;
	}

	function getWorkEmail() {
		if ( isset($this->data['work_email']) ) {
			return $this->data['work_email'];
		}

		return FALSE;
	}
	function setWorkEmail($work_email) {
		$work_email = trim($work_email);

		if 	(	$work_email == ''
					OR	$this->Validator->isEmail(	'work_email',
													$work_email,
													TTi18n::gettext('Work Email address is invalid')) ) {

			$this->data['work_email'] = $work_email;

			return TRUE;
		}

		return FALSE;
	}

	function getAge() {
		return round( TTDate::getYearDifference( $this->getBirthDate(), TTDate::getTime() ),1 );
	}

	function getBirthDate() {
		if ( isset($this->data['birth_date']) ) {
			return $this->data['birth_date'];
		}

		return FALSE;
	}
	function setBirthDate($epoch) {
		$epoch = trim($epoch);

		if 	(	$this->Validator->isDate(		'birth_date',
												$epoch,
												TTi18n::gettext('Birth date is invalid')) ) {

			$this->data['birth_date'] = TTDate::getMiddleDayEpoch( $epoch );

			return TRUE;
		}

		return FALSE;
	}

	function getHireDate() {
		if ( isset($this->data['hire_date']) ) {
			return $this->data['hire_date'];
		}

		return FALSE;
	}
	function setHireDate($epoch) {
		if ( empty($epoch) ) {
			$epoch = NULL;
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'hire_date',
												$epoch,
												TTi18n::gettext('Hire date is invalid')) ) {

			$this->data['hire_date'] = TTDate::getMiddleDayEpoch( $epoch );

			return TRUE;
		}

		return FALSE;
	}

	function getTerminationDate() {
		if ( isset($this->data['termination_date']) ) {
			return $this->data['termination_date'];
		}

		return FALSE;
	}
	function setTerminationDate($epoch) {
		if ( empty($epoch) ) {
			$epoch = NULL;
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'termination_date',
												$epoch,
												TTi18n::gettext('Termination date is invalid')) ) {

			$this->data['termination_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function getCurrency() {
		if ( isset($this->data['currency_id']) ) {
			return $this->data['currency_id'];
		}

		return FALSE;
	}
	function setCurrency($id) {
		$id = trim($id);

		Debug::Text('Currency ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
		$culf = new CurrencyListFactory();

		if (
				$this->Validator->isResultSetWithRows(	'currency_id',
														$culf->getByID($id),
														TTi18n::gettext('Invalid Currency')
													) ) {

			$this->data['currency_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getSecureSIN() {
		if ( $this->getSIN() != '' ) {
			//Grab the first 1, and last 3 digits.
			$first_four = substr( $this->getSIN(), 0, 1 );
			$last_four = substr( $this->getSIN(), -3 );

			$total = strlen($this->getSIN())-4;

			$retval = $first_four.str_repeat('X', $total).$last_four;

			return $retval;
		}

		return FALSE;
	}
	function getSIN() {
		if ( isset($this->data['sin']) ) {
			return $this->data['sin'];
		}

		return FALSE;
	}
	function setSIN($sin) {
		//If *'s are in the SIN number, skip setting it
		//This allows them to change other data without seeing the CC number.
		if ( stripos( $sin, 'X') !== FALSE  ) {
			return FALSE;
		}

		$sin = $this->Validator->stripNonNumeric( trim($sin) );

		if 	(
				$sin == ''
				OR
				$this->Validator->isLength(		'sin',
												$sin,
												TTi18n::gettext('SIN is invalid'),
												6,
												20)
				) {

			$this->data['sin'] = $sin;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID1() {
		if ( isset($this->data['other_id1']) ) {
			return $this->data['other_id1'];
		}

		return FALSE;
	}
	function setOtherID1($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id1',
											$value,
											TTi18n::gettext('Other ID 1 is invalid'),
											1,255) ) {

			$this->data['other_id1'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID2() {
		if ( isset($this->data['other_id2']) ) {
			return $this->data['other_id2'];
		}

		return FALSE;
	}
	function setOtherID2($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id2',
											$value,
											TTi18n::gettext('Other ID 2 is invalid'),
											1,255) ) {

			$this->data['other_id2'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID3() {
		if ( isset($this->data['other_id3']) ) {
			return $this->data['other_id3'];
		}

		return FALSE;
	}
	function setOtherID3($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id3',
											$value,
											TTi18n::gettext('Other ID 3 is invalid'),
											1,255) ) {

			$this->data['other_id3'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID4() {
		if ( isset($this->data['other_id4']) ) {
			return $this->data['other_id4'];
		}

		return FALSE;
	}
	function setOtherID4($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id4',
											$value,
											TTi18n::gettext('Other ID 4 is invalid'),
											1,255) ) {

			$this->data['other_id4'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getOtherID5() {
		if ( isset($this->data['other_id5']) ) {
			return $this->data['other_id5'];
		}

		return FALSE;
	}
	function setOtherID5($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'other_id5',
											$value,
											TTi18n::gettext('Other ID 5 is invalid'),
											1,255) ) {

			$this->data['other_id5'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getNote() {
		if ( isset($this->data['note']) ) {
			return $this->data['note'];
		}

		return FALSE;
	}
	function setNote($value) {
		$value = trim($value);

		if (	$value == ''
				OR
						$this->Validator->isLength(		'note',
														$value,
														TTi18n::gettext('Note is too long'),
														1,
														2048)
			) {

			$this->data['note'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function checkPasswordResetKey($key) {
		if ( $this->getPasswordResetDate() != ''
				AND $this->getPasswordResetDate() > (time() - 86400)
				AND $this->getPasswordResetKey() == $key ) {

			return TRUE;
		}

		return FALSE;
	}

	function sendPasswordResetEmail() {
		global $config_vars;

		if ( $this->getHomeEmail() != FALSE
				OR $this->getWorkEmail() != FALSE ) {

			if ( $this->getWorkEmail() != FALSE ) {
				$primary_email = $this->getWorkEmail();
				if ( $this->getHomeEmail() != FALSE ) {
					$secondary_email = $this->getHomeEmail();
				} else {
					$secondary_email = NULL;
				}
			} else {
				$primary_email = $this->getHomeEmail();
				$secondary_email = NULL;
			}

			$this->setPasswordResetKey( md5( uniqid() ) );
			$this->setPasswordResetDate( time() );
			$this->Save(FALSE);

			if ( $config_vars['other']['force_ssl'] == 1 ) {
				$protocol = 'https';
			} else {
				$protocol = 'http';
			}

			$subject = 'Password Reset requested at '. TTDate::getDate('DATE+TIME', time() ).' from '. $_SERVER['REMOTE_ADDR'];

			$body = '
			<html><body>
			If you did not request your password to be reset, you may ignore this email.
			<br>
			<br>
			If you did request the password for '. $this->getUserName() .' to be reset,
			please click <a href="'.$protocol .'://'.Misc::getHostName().Environment::getBaseURL() .'ForgotPassword.php?action:password_reset=null&key='. $this->getPasswordResetKey().'">here</a>
			</body></html>
			';

			//Debug::Text('Emailing Report to: '. $this->getUserName() .' Email: '. $primary_email , __FILE__, __LINE__, __METHOD__,10);
			//Debug::Arr($body, 'Email Report', __FILE__, __LINE__, __METHOD__,10);
			//echo "<pre>$body</pre><br>\n";
			//$retval = -->liam<--($primary_email, $subject, $body, "MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\nFrom: \"TimeTrex - Password Reset\"<DoNotReply@".Misc::getHostName().">\nCc: ". $secondary_email ."\n");
			//Debug::Text('Mail() result: '. (int)$retval, __FILE__, __LINE__, __METHOD__,10);

			TTLog::addEntry( $this->getId(), 500, TTi18n::getText('Employee Password Reset By').': '. $_SERVER['REMOTE_ADDR'] .' '. TTi18n::getText('Key').': '. $this->getPasswordResetKey(), NULL, $this->getTable() );

			$headers = array(
								'From'    => '"TimeTrex - Password Reset"<DoNotReply@'.Misc::getHostName( FALSE ).">",
								'Subject' => $subject,
								'Cc'	  => $secondary_email,
								'MIME-Version' => '1.0',
								'Content-type' => 'text/html; charset=iso-8859-1'
							 );

			$mail = new TTMail();
			$mail->setTo( $primary_email );
			$mail->setHeaders( $headers );
			$mail->setBody( $body );
			$retval = $mail->Send();

			return $retval;
		}

		return FALSE;
	}

	function getPasswordResetKey() {
		if ( isset($this->data['password_reset_key']) ) {
			return $this->data['password_reset_key'];
		}

		return FALSE;
	}
	function setPasswordResetKey($value) {
		$value = trim($value);

		if (	$value == ''
				OR
				$this->Validator->isLength(	'password_reset_key',
											$value,
											TTi18n::gettext('Password reset key is invalid'),
											1,255) ) {

			$this->data['password_reset_key'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getPasswordResetDate() {
		if ( isset($this->data['password_reset_date']) ) {
			return $this->data['password_reset_date'];
		}
	}
	function setPasswordResetDate($epoch) {
		if ( empty($epoch) ) {
			$epoch = NULL;
		}

		if 	(	$epoch == ''
				OR
				$this->Validator->isDate(		'password_reset_date',
												$epoch,
												TTi18n::gettext('Password reset date is invalid')) ) {

			$this->data['password_reset_date'] = $epoch;

			return TRUE;
		}

		return FALSE;
	}

	function isInformationComplete() {
		//Make sure the users information is all complete.
		//No longer check for SIN, as employees can't change it anyways.
		//Don't check for postal code, as some countries don't have that.
		if ( $this->getAddress1() == ''
				OR $this->getCity() == ''
				OR $this->getHomePhone() == '' ) {
			Debug::text('User Information is NOT Complete: ', __FILE__, __LINE__, __METHOD__, 10);
			return FALSE;
		}

		Debug::text('User Information is Complete: ', __FILE__, __LINE__, __METHOD__, 10);
		return TRUE;
	}

	function Validate() {
		//Re-validate the province just in case the country was set AFTER the province.
		$this->setProvince( $this->getProvince() );

		if ( $this->getCompany() == FALSE ) {
			$this->Validator->isTrue(		'company',
											FALSE,
											TTi18n::gettext('Company is invalid'));
		}

		//Need to require password on new employees as the database column is NOT NULL.
		//However when mass editing, no IDs are set so this always fails during the only validation phase.
		if ( $this->validate_only == FALSE AND $this->isNew() == TRUE AND ( $this->getPassword() == FALSE OR $this->getPassword() == '' ) ) {
			$this->Validator->isTrue(		'password',
											FALSE,
											TTi18n::gettext('Please specify a password'));
		}

		if ( $this->getEmployeeNumber() == FALSE AND $this->getStatus() == 10 ) {
			$this->Validator->isTrue(		'employee_number',
											FALSE,
											TTi18n::gettext('Employee number must be specified for ACTIVE employees') );
		}

																																												if ( $this->isNew() == TRUE ) { $obj_class = "\124\124\114\x69\x63\x65\x6e\x73\x65"; $obj_function = "\166\x61\154\x69\144\x61\164\145\114\x69\x63\145\x6e\x73\x65"; $obj_error_msg_function = "\x67\x65\x74\x46\x75\154\154\105\162\x72\x6f\x72\115\x65\x73\163\141\x67\x65"; @$obj = new $obj_class; $retval = $obj->{$obj_function}(); if ( $retval !== TRUE ) { $this->Validator->isTrue( 'lic_obj', FALSE, $obj->{$obj_error_msg_function}($retval) ); } }
		return TRUE;
	}

	function preSave() {
		if ( $this->getDefaultBranch() == FALSE ) {
			$this->setDefaultBranch(0);
		}
		if ( $this->getDefaultDepartment() == FALSE ) {
			$this->setDefaultDepartment(0);
		}

		if ( $this->getStatus() == FALSE ) {
			$this->setStatus( 10 ); //Active
		}

		//Remember if this is a new user for postSave()
		if ( $this->isNew() ) {
			$this->is_new = TRUE;
		}

		return TRUE;
	}

	function postSave() {
		$this->removeCache( $this->getId() );

		if ( $this->getDeleted() == FALSE AND $this->getPermissionControl() !== FALSE ) {
			Debug::text('Permission Group is set...', __FILE__, __LINE__, __METHOD__, 10);

			$pclf = new PermissionControlListFactory();
			$pclf->getByCompanyIdAndUserID( $this->getCompany(), $this->getId() );
			if ( $pclf->getRecordCount() > 0 ) {
				Debug::text('Already assigned to a Permission Group...', __FILE__, __LINE__, __METHOD__, 10);

				$pc_obj = $pclf->getCurrent();

				if ( $pc_obj->getId() == $this->getPermissionControl() ) {
					$add_permission_control = FALSE;
				} else {
					Debug::text('Permission Group has changed...', __FILE__, __LINE__, __METHOD__, 10);

					//Remove user from current schedule.
					$pulf = new PermissionUserListFactory();
					$pulf->getByPermissionControlIdAndUserID( $pc_obj->getId(), $this->getId() );
					Debug::text('Record Count: '. $pulf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
					if ( $pulf->getRecordCount() > 0 ) {
						foreach( $pulf as $pu_obj ) {
							Debug::text('Deleteing from Permission Group: '. $pu_obj->getPermissionControl(), __FILE__, __LINE__, __METHOD__, 10);
							$pu_obj->Delete();
						}
					}

					$add_permission_control = TRUE;
				}
			} else {
				Debug::text('NOT Already assigned to a Permission Group...', __FILE__, __LINE__, __METHOD__, 10);
				$add_permission_control = TRUE;
			}

			if ( $this->getPermissionControl() !== FALSE AND $add_permission_control == TRUE ) {
				Debug::text('Adding user to Permission Group...', __FILE__, __LINE__, __METHOD__, 10);

				//Add to new permission group
				$puf = new PermissionUserFactory();
				$puf->setPermissionControl( $this->getPermissionControl() );
				$puf->setUser( $this->getID() );

				if ( $puf->isValid() ) {
					$puf->Save();

					//Clear permission class for this employee.
					$pf = new PermissionFactory();
					$pf->clearCache( $this->getID(), $this->getCompany() );
				}
			}
			unset($add_permission_control);
		}

		if ( $this->getDeleted() == FALSE AND $this->getPayPeriodSchedule() !== FALSE ) {
			Debug::text('Pay Period Schedule is set...', __FILE__, __LINE__, __METHOD__, 10);

			$ppslf = new PayPeriodScheduleListFactory();
			$ppslf->getByUserId( $this->getId() );
			if ( $ppslf->getRecordCount() > 0 ) {
				$pps_obj = $ppslf->getCurrent();

				if ( $this->getPayPeriodSchedule() == $pps_obj->getId() ) {
					Debug::text('Already assigned to this Pay Period Schedule...', __FILE__, __LINE__, __METHOD__, 10);
					$add_pay_period_schedule = FALSE;
				} else {
					Debug::text('Changing Pay Period Schedule...', __FILE__, __LINE__, __METHOD__, 10);

					//Remove user from current schedule.
					$ppsulf = new PayPeriodScheduleUserListFactory();
					$ppsulf->getByPayPeriodScheduleIdAndUserID( $pps_obj->getId(), $this->getId() );
					Debug::text('Record Count: '. $ppsulf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
					if ( $ppsulf->getRecordCount() > 0 ) {
						foreach( $ppsulf as $ppsu_obj ) {
							Debug::text('Deleteing from Pay Period Schedule: '. $ppsu_obj->getPayPeriodSchedule(), __FILE__, __LINE__, __METHOD__, 10);
							$ppsu_obj->Delete();
						}
					}
					$add_pay_period_schedule = TRUE;
				}
			} else {
				Debug::text('Not assigned to ANY Pay Period Schedule...', __FILE__, __LINE__, __METHOD__, 10);
				$add_pay_period_schedule = TRUE;
			}

			if ( $this->getPayPeriodSchedule() !== FALSE AND $add_pay_period_schedule == TRUE ) {
				//Add to new pay period schedule
				$ppsuf = new PayPeriodScheduleUserFactory();
				$ppsuf->setPayPeriodSchedule( $this->getPayPeriodSchedule() );
				$ppsuf->setUser( $this->getID() );

				if ( $ppsuf->isValid() ) {
					$ppsuf->Save();
				}
			}
			unset($add_pay_period_schedule);
		}

		if ( $this->getDeleted() == FALSE AND $this->getPolicyGroup() !== FALSE ) {
			Debug::text('Policy Group is set...', __FILE__, __LINE__, __METHOD__, 10);

			$pglf = new PolicyGroupListFactory();
			$pglf->getByUserIds( $this->getId() );
			if ( $pglf->getRecordCount() > 0 ) {
				$pg_obj = $pglf->getCurrent();

				if ( $this->getPolicyGroup() == $pg_obj->getId() ) {
					Debug::text('Already assigned to this Policy Group...', __FILE__, __LINE__, __METHOD__, 10);
					$add_policy_group = FALSE;
				} else {
					Debug::text('Changing Policy Group...', __FILE__, __LINE__, __METHOD__, 10);

					//Remove user from current schedule.
					$pgulf = new PolicyGroupUserListFactory();
					$pgulf->getByPolicyGroupIdAndUserId( $pg_obj->getId(), $this->getId() );
					Debug::text('Record Count: '. $pgulf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
					if ( $pgulf->getRecordCount() > 0 ) {
						foreach( $pgulf as $pgu_obj ) {
							Debug::text('Deleteing from Policy Group: '. $pgu_obj->getPolicyGroup(), __FILE__, __LINE__, __METHOD__, 10);
							$pgu_obj->Delete();
						}
					}
					$add_policy_group = TRUE;
				}
			} else {
				Debug::text('Not assigned to ANY Policy Group...', __FILE__, __LINE__, __METHOD__, 10);
				$add_policy_group = TRUE;
			}

			if ( $this->getPolicyGroup() !== FALSE AND $add_policy_group == TRUE ) {
				//Add to new policy group
				$pguf = new PolicyGroupUserFactory();
				$pguf->setPolicyGroup( $this->getPolicyGroup() );
				$pguf->setUser( $this->getID() );

				if ( $pguf->isValid() ) {
					$pguf->Save();
				}
			}
			unset($add_policy_group);
		}

		if ( $this->getDeleted() == FALSE AND $this->getHierarchyControl() !== FALSE ) {
			Debug::text('Hierarchies are set...', __FILE__, __LINE__, __METHOD__, 10);

			$hierarchy_control_data = array_unique( array_values( (array)$this->getHierarchyControl() ) );
			//Debug::Arr($hierarchy_control_data, 'Setting hierarchy control data...', __FILE__, __LINE__, __METHOD__, 10);

			if ( is_array( $hierarchy_control_data ) ) {
				$hclf = new HierarchyControlListFactory();
				$hclf->getObjectTypeAppendedListByCompanyIDAndUserID( $this->getCompany(), $this->getID() );
				$existing_hierarchy_control_data = array_unique( array_values( (array)$hclf->getArrayByListFactory( $hclf, FALSE, TRUE, FALSE ) ) );
				//Debug::Arr($existing_hierarchy_control_data, 'Existing hierarchy control data...', __FILE__, __LINE__, __METHOD__, 10);

				$hierarchy_control_delete_diff = array_diff( $existing_hierarchy_control_data, $hierarchy_control_data );
				//Debug::Arr($hierarchy_control_delete_diff, 'Hierarchy control delete diff: ', __FILE__, __LINE__, __METHOD__, 10);

				//Remove user from existing hierarchy control
				if ( is_array($hierarchy_control_delete_diff) ) {
					foreach( $hierarchy_control_delete_diff as $hierarchy_control_id ) {
						if ( $hierarchy_control_id != 0 ) {
							$hulf = new HierarchyUserListFactory();
							$hulf->getByHierarchyControlAndUserID( $hierarchy_control_id, $this->getID() );
							if ( $hulf->getRecordCount() > 0 ) {
								Debug::text('Deleting user from hierarchy control ID: '. $hierarchy_control_id, __FILE__, __LINE__, __METHOD__, 10);
								$hulf->getCurrent()->Delete();
							}
						}
					}
				}
				unset($hierarchy_control_delete_diff, $hulf, $hclf, $hierarchy_control_id);

				$hierarchy_control_add_diff = array_diff( $hierarchy_control_data, $existing_hierarchy_control_data  );
				//Debug::Arr($hierarchy_control_add_diff, 'Hierarchy control add diff: ', __FILE__, __LINE__, __METHOD__, 10);

				if ( is_array($hierarchy_control_add_diff) ) {
					foreach( $hierarchy_control_add_diff as $hierarchy_control_id ) {
						Debug::text('Hierarchy data changed...', __FILE__, __LINE__, __METHOD__, 10);
						if ( $hierarchy_control_id != 0 ) {
							$huf = new HierarchyUserFactory();
							$huf->setHierarchyControl( $hierarchy_control_id );
							$huf->setUser( $this->getId() );
							if ( $huf->isValid() ) {
								Debug::text('Adding user to hierarchy control ID: '. $hierarchy_control_id, __FILE__, __LINE__, __METHOD__, 10);
								$huf->Save();
							}
						}
					}
				}
				unset($hierarchy_control_add, $huf, $hierarchy_control_id);
			}
		}

		if ( isset($this->is_new) AND $this->is_new == TRUE ) {
			$udlf = new UserDefaultListFactory();
			$udlf->getByCompanyId( $this->getCompany() );
			if ( $udlf->getRecordCount() > 0 ) {
				Debug::Text('Using User Defaults', __FILE__, __LINE__, __METHOD__,10);
				$udf_obj = $udlf->getCurrent();

				Debug::text('Inserting Default Deductions...', __FILE__, __LINE__, __METHOD__, 10);

				$company_deduction_ids = $udf_obj->getCompanyDeduction();
				if ( is_array($company_deduction_ids) AND count($company_deduction_ids) > 0 ) {
					foreach( $company_deduction_ids as $company_deduction_id ) {
						$udf = new UserDeductionFactory();
						$udf->setUser( $this->getId() );
						$udf->setCompanyDeduction( $company_deduction_id );
						if ( $udf->isValid() ) {
							$udf->Save();
						}
					}
				}
				unset($company_deduction_ids, $company_deduction_id, $udf);

				Debug::text('Inserting Default Prefs...', __FILE__, __LINE__, __METHOD__, 10);
				$upf = new UserPreferenceFactory();
				$upf->setUser( $this->getId() );
				$upf->setLanguage( $udf_obj->getLanguage() );
				$upf->setDateFormat( $udf_obj->getDateFormat() );
				$upf->setTimeFormat( $udf_obj->getTimeFormat() );
				$upf->setTimeUnitFormat( $udf_obj->getTimeUnitFormat() );
				$upf->setTimeZone( $udf_obj->getTimeZone() );
				$upf->setItemsPerPage( $udf_obj->getItemsPerPage() );
				$upf->setStartWeekDay( $udf_obj->getStartWeekDay() );
				$upf->setEnableEmailNotificationException( $udf_obj->getEnableEmailNotificationException() );
				$upf->setEnableEmailNotificationMessage( $udf_obj->getEnableEmailNotificationMessage() );
				$upf->setEnableEmailNotificationHome( $udf_obj->getEnableEmailNotificationHome() );

				if ( $upf->isValid() ) {
					$upf->Save();
				}
			}

		}

		if ( $this->getDeleted() == TRUE ) {
			//Remove them from the authorization hierarchy, policy group, and pay period schedule.
			//Delete any accruals for them as well.

			//Pay Period Schedule
			$ppslf = new PayPeriodScheduleListFactory();
			$ppslf->getByUserId( $this->getId() );
			if ( $ppslf->getRecordCount() > 0 ) {
				$pps_obj = $ppslf->getCurrent();

				//Remove user from current schedule.
				$ppsulf = new PayPeriodScheduleUserListFactory();
				$ppsulf->getByPayPeriodScheduleIdAndUserID( $pps_obj->getId(), $this->getId() );
				Debug::text('Record Count: '. $ppsulf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
				if ( $ppsulf->getRecordCount() > 0 ) {
					foreach( $ppsulf as $ppsu_obj ) {
						Debug::text('Deleteing from Pay Period Schedule: '. $ppsu_obj->getPayPeriodSchedule(), __FILE__, __LINE__, __METHOD__, 10);
						$ppsu_obj->Delete();
					}
				}
			}

			//Policy Group
			$pglf = new PolicyGroupListFactory();
			$pglf->getByUserIds( $this->getId() );
			if ( $pglf->getRecordCount() > 0 ) {
				$pg_obj = $pglf->getCurrent();

				$pgulf = new PolicyGroupUserListFactory();
				$pgulf->getByPolicyGroupIdAndUserId( $pg_obj->getId(), $this->getId() );
				Debug::text('Record Count: '. $pgulf->getRecordCount(), __FILE__, __LINE__, __METHOD__, 10);
				if ( $pgulf->getRecordCount() > 0 ) {
					foreach( $pgulf as $pgu_obj ) {
						Debug::text('Deleteing from Policy Group: '. $pgu_obj->getPolicyGroup(), __FILE__, __LINE__, __METHOD__, 10);
						$pgu_obj->Delete();
					}
				}
			}

			//Hierarchy
			$hclf = new HierarchyControlListFactory();
			$hclf->getByCompanyId( $this->getCompany() );
			if ( $hclf->getRecordCount() > 0 ) {
				foreach( $hclf as $hc_obj ) {
					$hf = new HierarchyListFactory();
					$hf->setUser( $this->getID() );
					$hf->setHierarchyControl( $hc_obj->getId() );
					$hf->Delete();
				}
				$hf->removeCache( NULL, $hf->getTable(TRUE) ); //On delete we have to delete the entire group.
				unset($hf);
			}

			//Accrual balances
			$alf = new AccrualListFactory();
			$alf->getByUserIdAndCompanyId( $this->getId(), $this->getCompany() );
			if ( $alf->getRecordCount()> 0 ) {
				foreach( $alf as $a_obj ) {
					$a_obj->setDeleted(TRUE);
					if ( $a_obj->isValid() ) {
						$a_obj->Save();
					}
				}
			}
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
						case 'hire_date':
						case 'birth_date':
						case 'termination_date':
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


	function getObjectAsArray( $include_columns = NULL, $permission_children_ids = FALSE ) {
		/*
		 $include_columns = array(
								'id' => TRUE,
								'company_id' => TRUE,
								...
								)

		*/

		$variable_function_map = $this->getVariableToFunctionMap();
		if ( is_array( $variable_function_map ) ) {
			foreach( $variable_function_map as $variable => $function_stub ) {
				if ( $include_columns == NULL OR ( isset($include_columns[$variable]) AND $include_columns[$variable] == TRUE ) ) {

					$function = 'get'.$function_stub;
					switch( $variable ) {
						case 'status':
						case 'sex':
							$function = 'get'.$variable;
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = Option::getByKey( $this->$function(), $this->getOptions( $variable ) );
							}
							break;
						case 'title':
						case 'group':
						case 'currency':
						case 'default_branch':
						case 'default_department':
						case 'permission_control_id':
						case 'permission_control':
						case 'pay_period_schedule_id':
						case 'pay_period_schedule':
						case 'policy_group_id':
						case 'policy_group':
							$data[$variable] = $this->getColumn( $variable );
						case 'password': //Don't return password
							break;
						case 'hire_date':
						case 'birth_date':
						case 'termination_date':
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = TTDate::getAPIDate( 'DATE', $this->$function() );
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
			$this->getPermissionColumns( &$data, $this->getID(), $this->getCreatedBy(), $permission_children_ids );
			$this->getCreatedAndUpdatedColumns( &$data, $include_columns );
		}

		return $data;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action, TTi18n::getText('Employee').': '. $this->getFullName( FALSE, TRUE ) , NULL, $this->getTable() );
	}
}
?>
