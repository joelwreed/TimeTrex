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
 * $Revision: 2838 $
 * $Id: RequestFactory.class.php 2838 2009-09-18 20:57:41Z ipso $
 * $Date: 2009-09-18 13:57:41 -0700 (Fri, 18 Sep 2009) $
 */

/**
 * @package Module_Request
 */
class RequestFactory extends Factory {
	protected $table = 'request';
	protected $pk_sequence_name = 'request_id_seq'; //PK Sequence name

	var $user_date_obj = NULL;


	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'type':
				$retval = array(
										10 => TTi18n::gettext('Missed Punch'),
										20 => TTi18n::gettext('Time Adjustment'),
										30 => TTi18n::gettext('Absence (incl. Vacation)'),
										40 => TTi18n::gettext('Schedule Adjustment'),
										100 => TTi18n::gettext('Other'),
									);
				break;
			case 'status':
				$retval = array(
										10 => TTi18n::gettext('INCOMPLETE'),
										20 => TTi18n::gettext('OPEN'),
										30 => TTi18n::gettext('PENDING AUTHORIZATION'),
										40 => TTi18n::gettext('AUTHORIZATION OPEN'),
										50 => TTi18n::gettext('ACTIVE'),
										55 => TTi18n::gettext('AUTHORIZATION DECLINED'),
										60 => TTi18n::gettext('DISABLED')
									);
				break;

		}

		return $retval;
	}


	function getUserDateObject() {
		if ( is_object($this->user_date_obj) ) {
			return $this->user_date_obj;
		} else {
			$udlf = new UserDateListFactory();
			$this->user_date_obj = $udlf->getById( $this->getUserDateID() )->getCurrent();

			return $this->user_date_obj;
		}
	}

	//Used for authorizationFactory
	function getUserObject() {
		return $this->getUserDateObject()->getUserObject();
	}

	//Used for authorizationFactory
	function getUser() {
		return $this->getUserDateObject()->getUser();
	}

	function setUserDate($user_id, $date) {
		$user_date_id = UserDateFactory::findOrInsertUserDate( $user_id, $date );
		Debug::text(' User Date ID: '. $user_date_id, __FILE__, __LINE__, __METHOD__,10);
		if ( $user_date_id != '' ) {
			$this->setUserDateID( $user_date_id );
			return TRUE;
		}
		Debug::text(' No User Date ID found', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function getUserDateID() {
		if ( isset($this->data['user_date_id']) ) {
			return $this->data['user_date_id'];
		}

		return FALSE;
	}
	function setUserDateID($id = NULL) {
		$id = trim($id);

		$udlf = new UserDateListFactory();

		if (  $this->Validator->isResultSetWithRows(	'user_date',
														$udlf->getByID($id),
														TTi18n::gettext('Invalid User Date ID')
														) ) {
			$this->data['user_date_id'] = $id;

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

			return TRUE;
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

	function getMessage() {
		if ( isset($this->tmp_data['message']) ) {
			return $this->tmp_data['message'];
		}

		return FALSE;
	}
	function setMessage($text) {
		$text = trim($text);

		if 	(	$this->Validator->isLength(		'message',
												$text,
												TTi18n::gettext('Invalid message length'),
												5,
												1024) ) {

			$this->tmp_data['message'] = htmlentities( $text );

			return TRUE;
		}

		return FALSE;
	}

	function Validate() {
		if (	$this->isNew() == TRUE
				AND $this->Validator->hasError('message') == FALSE
				AND $this->getMessage() == FALSE ) {
			$this->Validator->isTRUE(		'message',
											FALSE,
											TTi18n::gettext('Invalid message length') );
		}

		if ( $this->getUserDateID() == FALSE ) {
			$this->Validator->isTRUE(		'user_date',
											FALSE,
											TTi18n::gettext('Invalid User Date ID') );
		}

		return TRUE;
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

		//Remove date_stamp variable so we can generate a proper update SQL query automatically.
		unset($this->data['date_stamp']);

		return TRUE;
	}

	function postSave() {
		//Save message here after we have the request_id.
		//if ( $this->isNew() == TRUE ) {
		if ( $this->getMessage() !== FALSE ) {
			$mf = new MessageFactory();
			$mf->setObjectType( 50 ); //Request
			$mf->setObject( $this->getID() );
			$mf->setParent( 0 );
			$mf->setPriority();
			$mf->setStatus('UNREAD');
			$mf->setBody( $this->getMessage() );
			if ( $mf->isValid() ) {
				return $mf->Save();
			}
		}

		return TRUE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action,  TTi18n::getText('Request - Type').': '. $this->getType(), NULL, $this->getTable() );
	}
}
?>
