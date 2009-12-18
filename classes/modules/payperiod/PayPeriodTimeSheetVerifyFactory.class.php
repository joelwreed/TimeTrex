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
 * $Revision: 2682 $
 * $Id: PayPeriodTimeSheetVerifyFactory.class.php 2682 2009-07-23 22:31:20Z ipso $
 * $Date: 2009-07-23 15:31:20 -0700 (Thu, 23 Jul 2009) $
 */

/**
 * @package Module_PayPeriod
 */
class PayPeriodTimeSheetVerifyFactory extends Factory {
	protected $table = 'pay_period_time_sheet_verify';
	protected $pk_sequence_name = 'pay_period_time_sheet_verify_id_seq'; //PK Sequence name

	var $user_obj = NULL;
	var $pay_period_obj = NULL;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('INCOMPLETE'),
										20 => TTi18n::gettext('OPEN'),
										30 => TTi18n::gettext('PENDING AUTHORIZATION'),
										40 => TTi18n::gettext('AUTHORIZATION OPEN'),
										50 => TTi18n::gettext('Verified'),
										55 => TTi18n::gettext('AUTHORIZATION DECLINED'),
										60 => TTi18n::gettext('DISABLED')
									);
				break;
		}

		return $retval;
	}

	function getUserObject() {
		if ( is_object($this->user_obj) ) {
			return $this->user_obj;
		} else {
			$ulf = new UserListFactory();
			$this->user_obj = $ulf->getById( $this->getUser() )->getCurrent();

			return $this->user_obj;
		}
	}

	function getPayPeriodObject() {
		if ( is_object($this->pay_period_obj) ) {
			return $this->pay_period_obj;
		} else {
			$pplf = new PayPeriodListFactory();
			$this->pay_period_obj = $pplf->getById( $this->getPayPeriod() )->getCurrent();

			return $this->pay_period_obj;
		}
	}

	function getPayPeriod() {
		if ( isset($this->data['pay_period_id']) ) {
			return $this->data['pay_period_id'];
		}

		return FALSE;
	}
	function setPayPeriod($id = NULL) {
		$id = trim($id);

		if ( $id == NULL ) {
			$id = $this->findPayPeriod();
		}

		$pplf = new PayPeriodListFactory();

		if (
				$this->Validator->isResultSetWithRows(	'pay_period',
														$pplf->getByID($id),
														TTi18n::gettext('Invalid Pay Period')
														) ) {
			$this->data['pay_period_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getUser() {
		if ( isset($this->data['user_id']) ) {
			return $this->data['user_id'];
		}
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

	function getStatus() {
		if ( isset($this->data['status_id']) ) {
			return $this->data['status_id'];
		}

		return FALSE;
	}
	function setStatus($value) {
		$value = trim($value);

		$key = Option::getByValue($value, $this->getOptions('status') );
		if ($key !== FALSE) {
			$value = $key;
		}

		if ( $this->Validator->inArrayKey(	'status',
											$value,
											TTi18n::gettext('Incorrect Status'),
											$this->getOptions('status')) ) {

			$this->data['status_id'] = $value;

			return FALSE;
		}

		return FALSE;
	}

	function getAuthorized() {
		if ( isset($this->data['authorized']) AND $this->data['authorized'] !== NULL) {
			return $this->fromBool( $this->data['authorized'] );
		}

		return NULL;
	}
	function setAuthorized($bool) {
		$this->data['authorized'] = $this->toBool($bool);

		return true;
	}

	function getAuthorizationLevel() {
		if ( isset($this->data['authorization_level']) ) {
			return $this->data['authorization_level'];
		}

		return FALSE;
	}
	function setAuthorizationLevel($value) {
		$value = (int)trim( $value );

		if ( $value < 0 ) {
			$value = 0;
		}

		if ( $this->Validator->isNumeric(	'authorization_level',
											$value,
											TTi18n::gettext('Incorrect authorization level') ) ) {

			$this->data['authorization_level'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function preSave() {
		//If this is a new request, find the current authorization level to assign to it.
		if ( $this->isNew() == TRUE ) {
			$hlf = new HierarchyListFactory();
			$hierarchy_arr = $hlf->getHierarchyParentByCompanyIdAndUserIdAndObjectTypeID( $this->getUserObject()->getCompany(), $this->getUserObject()->getID(), 50, FALSE);

			$hierarchy_highest_level = 99;
			if ( is_array( $hierarchy_arr ) ) {
				Debug::Arr($hierarchy_arr, ' Hierarchy Array: ', __FILE__, __LINE__, __METHOD__,10);
				$hierarchy_highest_level = end( array_keys( $hierarchy_arr ) ) ;
				Debug::Text(' Setting hierarchy level to: '. $hierarchy_highest_level, __FILE__, __LINE__, __METHOD__,10);
			}
			$this->setAuthorizationLevel( $hierarchy_highest_level );
		}

		if ( $this->getAuthorized() == TRUE ) {
			$this->setAuthorizationLevel( 0 );
		}

		return TRUE;
	}

	function postSave() {
		//If status is pending auth (30) delete all authorization history, because they could
		//be re-verifying.
		if ( $this->getStatus() == 30 ) {
			$alf = new AuthorizationListFactory();
			$alf->getByObjectTypeAndObjectId( 90, $this->getId() );
			if ( $alf->getRecordCount() > 0 ) {
				foreach( $alf as $a_obj ) {
					//Delete the record outright for now, as marking it as deleted causes transaction issues
					//and it never gets committed.
					$a_obj->Delete();
					/*
					$a_obj->setDeleted(TRUE);
					if ( $a_obj->isValid() ) {
						$a_obj->Save();
					}
					*/
				}
			}
		}

		return TRUE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('TimeSheet Verify'), NULL, $this->getTable() );
	}
}
?>
