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
 * $Revision: 2881 $
 * $Id: ExceptionFactory.class.php 2881 2009-10-07 22:31:12Z ipso $
 * $Date: 2009-10-07 15:31:12 -0700 (Wed, 07 Oct 2009) $
 */

/**
 * @package Core
 */
class ExceptionFactory extends Factory {
	protected $table = 'exception';
	protected $pk_sequence_name = 'exception_id_seq'; //PK Sequence name

	protected $user_date_obj = NULL;
	protected $exception_policy_obj = NULL;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {
			case 'type':
				//Exception life-cycle
				//
				// - Exception occurs, such as missed out punch, in late.
				//   - If the exception is pre-mature, we wait 16-24hrs for it to become a full-blown exception
				// - If the exception requires authorization, it sits in a pending state waiting for supervsior intervention.
				// - Supervisor authorizes the exception, or makes a correction, leaves a note or something.
				//	 - Exception no longer appears on timesheet/exception list.
				$retval = array(
										5  => TTi18n::gettext('Pre-Mature'),
										30 => TTi18n::gettext('PENDING AUTHORIZATION'),
										40 => TTi18n::gettext('AUTHORIZATION OPEN'),
										50 => TTi18n::gettext('ACTIVE'),
										55 => TTi18n::gettext('AUTHORIZATION DECLINED'),
										60 => TTi18n::gettext('DISABLED'),
										70 => TTi18n::gettext('Corrected')
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

	function getExceptionPolicyObject() {
		if ( is_object($this->exception_policy_obj) ) {
			return $this->exception_policy_obj;
		} else {
			$eplf = new ExceptionPolicyListFactory();
			$this->exception_policy_obj = $eplf->getById( $this->getExceptionPolicyID() )->getCurrent();

			return $this->exception_policy_obj;
		}
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

	function getExceptionPolicyID() {
		if ( isset($this->data['exception_policy_id']) ) {
			return $this->data['exception_policy_id'];
		}

		return FALSE;
	}
	function setExceptionPolicyID($id) {
		$id = trim($id);

		if ( $id == '' OR empty($id) ) {
			$id = NULL;
		}

		$eplf = new ExceptionPolicyListFactory();

		if (	$id == NULL
				OR
				$this->Validator->isResultSetWithRows(	'exception_policy',
														$eplf->getByID($id),
														TTi18n::gettext('Invalid Exception Policy ID')
														) ) {
			$this->data['exception_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPunchControlID() {
		if ( isset($this->data['punch_control_id']) ) {
			return $this->data['punch_control_id'];
		}

		return FALSE;
	}
	function setPunchControlID($id) {
		$id = trim($id);

		if ( $id == '' OR empty($id) ) {
			$id = NULL;
		}

		$pclf = new PunchControlListFactory();

		if (
				$id == NULL
				OR
				$this->Validator->isResultSetWithRows(	'punch_control',
														$pclf->getByID($id),
														TTi18n::gettext('Invalid Punch Control ID')
														) ) {
			$this->data['punch_control_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getPunchID() {
		if ( isset($this->data['punch_id']) ) {
			return $this->data['punch_id'];
		}

		return FALSE;
	}
	function setPunchID($id) {
		$id = trim($id);

		if ( $id == '' OR empty($id) ) {
			$id = NULL;
		}

		$plf = new PunchListFactory();

		if (	$id == NULL
				OR
				$this->Validator->isResultSetWithRows(	'punch',
														$plf->getByID($id),
														TTi18n::gettext('Invalid Punch ID')
														) ) {
			$this->data['punch_id'] = $id;

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

			return FALSE;
		}

		return FALSE;
	}

	function getEnableDemerits() {
		if ( isset($this->data['enable_demerit']) ) {
			return $this->data['enable_demerit'];
		}

		return FALSE;
	}
	function setEnableDemerits($bool) {
		$this->data['enable_demerit'] = $bool;

		return TRUE;
	}

	function getColor() {
		$retval = FALSE;

		if (  $this->getType() == 5 ) {
			$retval = "gray";
		} else {
			if ( $this->getColumn('severity_id') != '' ) {
				switch ( $this->getColumn('severity_id') ) {
					case 10:
						$retval = 'black';
						break;
					case 20:
						$retval = 'blue';
						break;
					case 30:
						$retval = 'red';
						break;
				}
			}
		}

		return $retval;
	}

	function getEmailExceptionAddresses( $u_obj = NULL, $ep_obj = NULL ) {
		Debug::text(' Attempting to Email Notification...', __FILE__, __LINE__, __METHOD__,10);

		//Make sure type is not pre-mature.
		if ( $this->getType() > 5 ) {
			if ( !is_object($ep_obj) ) {
				$ep_obj = $this->getExceptionPolicyObject();
			}

			//Make sure exception policy email notifications are enabled.
			if ( $ep_obj->getEmailNotification() > 0 ) {
				if ( !is_object($u_obj) ) {
					$u_obj = $this->getUserDateObject()->getUserObject();
				}

				$up_obj = $this->getUserDateObject()->getUserObject()->getUserPreferenceObject();

				//Make sure user email notifications are enabled.
				if ( ( $ep_obj->getEmailNotification() == 10 OR $ep_obj->getEmailNotification() == 100 ) AND $up_obj->getEnableEmailNotificationException() == TRUE ) {
					Debug::Text(' Emailing exception to user!', __FILE__, __LINE__, __METHOD__,10);
					if ( $u_obj->getWorkEmail() != '' ) {
						$retarr[] = $u_obj->getWorkEmail();
					}
					if ( $up_obj->getEnableEmailNotificationHome() == TRUE AND $u_obj->getHomeEmail() != '' ) {
						$retarr[] = $u_obj->getHomeEmail();
					}
				} else {
					Debug::Text(' Skipping email to user.', __FILE__, __LINE__, __METHOD__,10);
				}

				//Make sure supervisor email notifcations are enabled
				if ( $ep_obj->getEmailNotification() == 20 OR $ep_obj->getEmailNotification() == 100 ) {
					//Find supervisor
					$hlf = new HierarchyListFactory();
					$parent_user_id = $hlf->getHierarchyParentByCompanyIdAndUserIdAndObjectTypeID( $u_obj->getCompany(), $u_obj->getId(), 80 );
					if ( $parent_user_id != FALSE ) {
						$ulf = new UserListFactory();
						$ulf->getById( $parent_user_id );
						if ( $ulf->getRecordCount() > 0 ) {
							$parent_user_obj = $ulf->getCurrent();

							if ( is_object( $parent_user_obj->getUserPreferenceObject() ) AND $parent_user_obj->getUserPreferenceObject()->getEnableEmailNotificationException() == TRUE ) {
								Debug::Text(' Emailing exception to supervisor!', __FILE__, __LINE__, __METHOD__,10);
								if ( $parent_user_obj->getWorkEmail() != '' ) {
									$retarr[] = $parent_user_obj->getWorkEmail();
								}

								if ( $up_obj->getEnableEmailNotificationHome() == TRUE AND $parent_user_obj->getHomeEmail() != '' ) {
									$retarr[] = $parent_user_obj->getHomeEmail();
								}
							} else {
								Debug::Text(' Skipping email to supervisor.', __FILE__, __LINE__, __METHOD__,10);
							}
						}
					} else {
						Debug::Text(' No Hierarchy Parent Found, skipping email to supervisor.', __FILE__, __LINE__, __METHOD__,10);
					}
				}

				if ( isset($retarr) AND is_array($retarr) ) {
					return $retarr;
				} else {
					Debug::text(' No user objects to email too...', __FILE__, __LINE__, __METHOD__,10);
				}
			} else {
				Debug::text(' Exception Policy Email Exceptions are disabled, skipping email...', __FILE__, __LINE__, __METHOD__,10);
			}
		} else {
			Debug::text(' Pre-Mature exception, or not in production mode, skipping email...', __FILE__, __LINE__, __METHOD__,10);
		}

		return FALSE;
	}


	/*

		What do we pass the emailException function?
			To address, CC address (home email) and Bcc (supervisor) address?

	*/
	function emailException( $u_obj, $user_date_obj, $ep_obj = NULL ) {

		if ( !is_object( $u_obj ) ) {
			return FALSE;
		}

		if ( !is_object( $user_date_obj ) ) {
			return FALSE;
		}

		if ( !is_object($ep_obj) ) {
			$ep_obj = $this->getExceptionPolicyObject();
		}

		//Only email on active exceptions.
		if ( $this->getType() != 50 ) {
			return FALSE;
		}

		$email_to_arr = $this->getEmailExceptionAddresses( $u_obj, $ep_obj );
		if ( $email_to_arr == FALSE ) {
			return FALSE;
		}

		$from = 'DoNotReply@'.Misc::getHostName( FALSE );

		$to = array_shift( $email_to_arr );
		Debug::Text('To: '. $to, __FILE__, __LINE__, __METHOD__,10);
		if ( is_array($email_to_arr) AND count($email_to_arr) > 0 ) {
			$bcc = implode(',', $email_to_arr);
		} else {
			$bcc = NULL;
		}

		$exception_email_subject = ' #exception_name# (#exception_code#) '. TTi18n::gettext('exception for') .' #employee_first_name# #employee_last_name# '. TTi18n::gettext('on') .' #date#';
		$exception_email_body  = TTi18n::gettext('Employee:').' #employee_first_name# #employee_last_name#'."\n";
		$exception_email_body .= TTi18n::gettext('Date:').' #date#'."\n";
		$exception_email_body .= TTi18n::gettext('Exception:').' #exception_name# (#exception_code#)'."\n";
		$exception_email_body .= TTi18n::gettext('Severity:').' #exception_severity#'."\n";
		$exception_email_body .= TTi18n::gettext('Link:').' <a href="http://'. Misc::getHostName().Environment::getBaseURL().'">'.APPLICATION_NAME.' '. TTi18n::gettext('Login') .'</a>';

		//Define subject/body variables here.
		$search_arr = array(
							'#employee_first_name#',
							'#employee_last_name#',
							'#exception_code#',
							'#exception_name#',
							'#exception_severity#',
							'#date#',
							'#link#',
							);

		$replace_arr = array(
							$u_obj->getFirstName(),
							$u_obj->getLastName(),
							$ep_obj->getType(),
							Option::getByKey( $ep_obj->getType(), $ep_obj->getOptions('type') ),
							Option::getByKey( $ep_obj->getSeverity(), $ep_obj->getOptions('severity') ),
							TTDate::getDate('DATE', $user_date_obj->getDateStamp() ),
							NULL,
							);

		//For some reason the EOL defaults to \r\n, which seems to screw with Amavis
		if ( !defined('MAIL_MIMEPART_CRLF') ) {
			define('MAIL_MIMEPART_CRLF', "\n");
		}

		$subject = str_replace( $search_arr, $replace_arr, $exception_email_subject );
		Debug::Text('Subject: '. $subject, __FILE__, __LINE__, __METHOD__,10);

		$headers = array(
							'From'    => $from,
							'Subject' => $subject,
							'Bcc'	  => $bcc,
							'Reply-To' => $to,
							'Return-Path' => $to,
							'Errors-To' => $to,
						 );
		Debug::Arr($headers, 'Headers: ', __FILE__, __LINE__, __METHOD__,10);

		$body = '<pre>'.str_replace( $search_arr, $replace_arr, $exception_email_body ).'</pre>';
		Debug::Text('Body: '. $body, __FILE__, __LINE__, __METHOD__,10);

		$mail = new TTMail();
		$mail->setTo( $to );
		$mail->setHeaders( $headers );

		@$mail->getMIMEObject()->setHTMLBody($body);

		$mail->setBody( $mail->getMIMEObject()->get() );
		$retval = $mail->Send();

		if ( $retval == TRUE ) {
			TTLog::addEntry( $this->getId(), 500,  TTi18n::getText('Email Exception to').': '. $to .' Bcc: '. $headers['Bcc'], NULL, $this->getTable() );
			return TRUE;
		}

		return TRUE;
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
