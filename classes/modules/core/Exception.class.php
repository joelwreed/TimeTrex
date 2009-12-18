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
 * $Id: Exception.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */

/**
 * @package Core
 */
class DBError extends Exception {
   function __construct($e) {
      global $db;

      $db->FailTrans();

      //print_r($e);
      //adodb_pr($e);

      //Log database error
      if ( isset($e->message) ) {
         Debug::Text($e->message, __FILE__, __LINE__, __METHOD__,10);
      }

      if ( isset($e->trace) ) {
         $e = strip_tags( adodb_backtrace($e->trace) );
         Debug::Arr( $e, 'Exception...', __FILE__, __LINE__, __METHOD__,10);
      }

      Debug::Arr( Debug::backTrace(), ' BackTrace: ', __FILE__, __LINE__, __METHOD__,10);

      //Dump debug buffer.
      Debug::Display();
      Debug::writeToLog();
      Debug::emailLog();

      Redirect::Page( URLBuilder::getURL( array('exception' => 'DBError'), Environment::getBaseURL().'DownForMaintenance.php') );

      ob_flush();
      ob_clean();

      exit;
   }
}


/**
 * @package Core
 */
class GeneralError extends Exception {
   function __construct($message) {
      global $db;

      //debug_print_backtrace();
      $db->FailTrans();

      echo "======================================================================<br>\n";
      echo "EXCEPTION!<br>\n";
      echo "======================================================================<br>\n";
      echo "<b>Error message: </b>".$message ."<br>\n";
      echo "<b>Error code: </b>".$this->getCode()."<br>\n";
      echo "<b>Script Name: </b>".$this->getFile()."<br>\n";
      echo "<b>Line Number: </b>".$this->getLine()."<br>\n";
      echo "======================================================================<br>\n";
      echo "EXCEPTION!<br>\n";
      echo "======================================================================<br>\n";

      Debug::Arr( Debug::backTrace(), ' BackTrace: ', __FILE__, __LINE__, __METHOD__,10);

      //Dump debug buffer.
      Debug::Display();
      Debug::writeToLog();
      Debug::emailLog();
      ob_flush();
      ob_clean();

      Redirect::Page( URLBuilder::getURL( array('exception' => 'GeneralError'), Environment::getBaseURL().'DownForMaintenance.php') );

      exit;
   }
}
?>
