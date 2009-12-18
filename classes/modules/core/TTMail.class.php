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
 * $Id: TTLog.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Core
 */


/*
 Config options:

 [mail]
 delivery_method = mail/smtp/soap
 smtp_host=mail.domain.com
 smtp_port=25
 smtp_username=test1
 smtp_password=testpass
*/
class TTMail {
	private $mime_obj = NULL;
	private $mail_obj = NULL;

	private $data = NULL;

	function __construct() {

		return TRUE;
	}

	function getMimeObject() {
		if ( $this->mime_obj == NULL ) {
			require_once('Mail/mime.php');
			$this->mime_obj = @new Mail_Mime();
		}

		return $this->mime_obj;
	}
	function getMailObject() {
		if ( $this->mail_obj == NULL ) {
			require_once('Mail.php');

			//Determine if use Mail/SMTP, or SOAP.
			$delivery_method = $this->getDeliveryMethod();

			if ( $delivery_method == 'mail' ) {
				$this->mail_obj = &Mail::factory('mail');

			} elseif ( $delivery_method == 'smtp' ) {
				$smtp_config = $this->getSMTPConfig();
				$this->mail_obj = &Mail::factory('smtp', array (
											'host' => $smtp_config['host'],
											'port' => $smtp_config['port'],
											'auth' => true,
											'username' => $smtp_config['username'],
											'password' => $smtp_config['password']
											)
										);

				Debug::Arr($smtp_config, 'SMTP Config: ', __FILE__, __LINE__, __METHOD__,10);

			}
		}

		return $this->mail_obj;
	}

	function getDeliveryMethod() {
		global $config_vars;

		$possible_values = array( 'mail','soap','smtp' );
		if ( isset( $config_vars['mail']['delivery_method'] ) AND in_array( strtolower( trim($config_vars['mail']['delivery_method']) ), $possible_values ) ) {
			return $config_vars['mail']['delivery_method'];
		}

		if ( DEPLOYMENT_ON_DEMAND == TRUE ) {
			return 'mail';
		}

		return 'soap'; //Default to SOAP as it has a better chance of working than mail/SMTP
	}

	function getSMTPConfig() {
		global $config_vars;

		$retarr = array(
						'host' => NULL,
						'post' => 25,
						'username' => NULL,
						'password' => NULL,
						);

		if ( isset( $config_vars['mail']['smtp_host'] ) ) {
			$retarr['host'] = $config_vars['mail']['smtp_host'];
		}

		if ( isset( $config_vars['mail']['smtp_port'] ) ) {
			$retarr['port'] = $config_vars['mail']['smtp_port'];
		}

		if ( isset( $config_vars['mail']['smtp_username'] ) ) {
			$retarr['username'] = $config_vars['mail']['smtp_username'];
		}
		if ( isset( $config_vars['mail']['smtp_password'] ) ) {
			$retarr['password'] = $config_vars['mail']['smtp_password'];
		}

		return $retarr;
	}

	function getMIMEHeaders() {
		return @$this->getMIMEObject()->headers( $this->getHeaders() );
	}
	function getHeaders() {
		if ( isset( $this->data['headers'] ) ) {
			return $this->data['headers'];
		}

		return FALSE;
	}
	function setHeaders( $headers ) {
		$this->data['headers'] = $headers;

		return TRUE;
	}

	function getTo() {
		if ( isset( $this->data['to'] ) ) {
			return $this->data['to'];
		}

		return FALSE;
	}
	function setTo( $email ) {
		$this->data['to'] = $email;

		return TRUE;
	}

	function getBody() {
		if ( isset( $this->data['body'] ) ) {
			return $this->data['body'];
		}

		return FALSE;
	}
	function setBody( $body ) {
		$this->data['body'] = $body;

		return TRUE;
	}

	function Send() {
		Debug::Text('Attempting to send email To: '. $this->getTo(), __FILE__, __LINE__, __METHOD__,10);

		if ( $this->getTo() == FALSE ) {
			Debug::Text('To Address invalid...', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( $this->getBody() == FALSE ) {
			Debug::Text('Body invalid...', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( PRODUCTION == FALSE ) {
			Debug::Text('Not in production mode, not sending emails...', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( DEMO_MODE == TRUE ) {
			Debug::Text('In DEMO mode, not sending emails...', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

/*
		if ( PRODUCTION == FALSE ) {
			$to = 'root@localhost';
		}

*/
		Debug::Text('Sending Email To: '. $this->getTo() .' Body Size: '. strlen( $this->getBody() ) .' Method: '. $this->getDeliveryMethod(), __FILE__, __LINE__, __METHOD__,10);

		//For some reason the EOL defaults to \r\n, which seems to screw with Amavis
		if ( !defined('MAIL_MIMEPART_CRLF') ) {
			define('MAIL_MIMEPART_CRLF', "\n");
		}

		switch ( $this->getDeliveryMethod() ) {
			case 'smtp':
			case 'mail':
				$send_retval = $this->getMailObject()->send( $this->getTo(), $this->getMIMEHeaders(), $this->getBody() );
				break;
			case 'soap':
				$ttsc = new TimeTrexSoapClient();
				$send_retval = $ttsc->sendEmail( $this->getTo(), $this->getMIMEHeaders(), $this->getBody() );
				break;
		}

		if ( $send_retval == TRUE ) {
			return TRUE;
		}

		Debug::Arr($send_retval, 'Send Email Failed!', __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}
}
?>
