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
 * $Id: Debug.class.php 3021 2009-11-11 23:33:03Z ipso $
 * $Date: 2009-11-11 15:33:03 -0800 (Wed, 11 Nov 2009) $
 */

/**
 * @package Core
 */
class Debug {
	static protected $enable = FALSE; 			//Enable/Disable debug printing.
	static protected $verbosity = 5; 			//Display debug info with a verbosity level equal or lesser then this.
	static protected $buffer_output = TRUE; 	//Enable/Disable output buffering.
	static protected $debug_buffer = NULL; 		//Output buffer.
	static protected $enable_tidy = FALSE; 		//Enable/Disable tidying of output
	static protected $enable_display = FALSE;	//Enable/Disable displaying of debug output
	static protected $enable_log = FALSE; 		//Enable/Disable logging of debug output
	static protected $max_buffer_size = 1000;	//Max buffer size in lines.

	static protected $buffer_size = 0; 			//Current buffer size in lines.

	static $tidy_obj = NULL;

	function setEnable($bool) {
		self::$enable = $bool;
	}

	function getEnable() {
		return self::$enable;
	}

	function setBufferOutput($bool) {
		self::$buffer_output = $bool;
	}

	function setVerbosity($level) {
		global $db;

		self::$verbosity = $level;

		if (is_object($db) AND $level == 11) {
			$db->debug=TRUE;
		}
	}
	function getVerbosity() {
		return self::$verbosity;
	}

	function setEnableTidy($bool) {
		self::$enable_tidy = $bool;
	}

	function getEnableTidy() {
		return self::$enable_tidy;
	}

	function setEnableDisplay($bool) {
		self::$enable_display = $bool;
	}

	function getEnableDisplay() {
		return self::$enable_display;
	}

	function setEnableLog($bool) {
		self::$enable_log = $bool;
	}

	function getEnableLog() {
		return self::$enable_log;
	}

	static function Text($text = NULL, $file = __FILE__, $line = __LINE__, $method = __METHOD__, $verbosity = 9) {
		if ( $verbosity > self::getVerbosity() OR self::$enable == FALSE ) {
			return FALSE;
		}

		if ( empty($method) ) {
			$method = "[Function]";
		}

		$text = 'DEBUG ['. $line .']:'. "\t" .'<b>'. $method .'()</b>: '. $text ."<br>\n";

		if ( self::$buffer_output == TRUE ) {
			self::$debug_buffer[] = array($verbosity, $text);
			self::$buffer_size++;
		} else {
			if ( self::$enable_display == TRUE ) {
				echo $text;
			} elseif ( OPERATING_SYSTEM != 'WIN' AND self::$enable_log == TRUE ) {
				syslog(LOG_WARNING, $text );
			}
		}

		return true;
	}

	static function backTrace() {
		ob_start();
		debug_print_backtrace();
		$ob_contents = ob_get_contents();
		ob_end_clean();

		return $ob_contents;
	}

	static function varDump( $array ) {
		ob_start();
		//var_dump($array); //Xdebug may interfere with this and cause it to not display all the data...
		print_r($array);
		$ob_contents = ob_get_contents();
		ob_end_clean();

		return $ob_contents;
	}

	static function Arr($array, $text = NULL, $file = __FILE__, $line = __LINE__, $method = __METHOD__, $verbosity = 9) {
		if ( $verbosity > self::getVerbosity() OR self::$enable == FALSE ) {
			return FALSE;
		}

		if ( empty($method) ) {
			$method = "[Function]";
		}

		$output = 'DEBUG ['. $line .'] Array: <b>'. $method .'()</b>: '. $text ."\n";
		$output .= "<pre>\n". self::varDump($array) ."</pre><br>\n";

		if (self::$buffer_output == TRUE) {
			self::$debug_buffer[] = array($verbosity, $output);
			self::$buffer_size = self::$buffer_size + count($array);
		} else {
			if ( self::$enable_display == TRUE ) {
				echo $output;
			} elseif ( OPERATING_SYSTEM != 'WIN' AND self::$enable_log == TRUE ) {
				syslog(LOG_WARNING, $text );
			}
		}

		return TRUE;
	}

	static function getOutput() {
		$output = NULL;
		if ( count(self::$debug_buffer) > 0 ) {
			foreach (self::$debug_buffer as $arr) {
				$verbosity = $arr[0];
				$text = $arr[1];

				if ($verbosity <= self::getVerbosity() ) {
					$output .= $text;
				}
			}

			return $output;
		}

		return FALSE;
	}

	static function emailLog() {
		if ( PRODUCTION === TRUE ) {
			$output = self::getOutput();

			if (strlen($output) > 0) {
				$server_domain = Misc::getHostName();

				mail('root@'.$server_domain,'TimeTrex - Error!', $output, "From: timetrex@".$server_domain."\n");
			}
		}
		return TRUE;
	}

	static function writeToLog() {
		if (self::$enable_log == TRUE AND self::$buffer_output == TRUE) {
			global $config_vars;

			$date_format = 'D M j G:i:s T Y';
			$file_name = $config_vars['path']['log'] . DIRECTORY_SEPARATOR .'timetrex.log';

			$eol = "\n";
			if ( is_writable( $config_vars['path']['log'] ) ) {
				$output = '---------------[ '. Date('r') .' (PID: '.getmypid().') ]---------------'.$eol;
				if ( is_array(self::$debug_buffer) ) {
					foreach (self::$debug_buffer as $arr) {

						$verbosity = $arr[0];
						$text = $arr[1];

						if ($verbosity <= self::getVerbosity() ) {
							$output .= $text;
						}
					}
				}
				$output .= '---------------[ '. Date('r') .' (PID: '.getmypid().') ]---------------'.$eol;

				$fp = @fopen( $file_name,'a' );
				@fwrite($fp, $output);
				@fclose($fp);
				unset($output);
			}
		}

		return FALSE;
	}

	static function Display() {
		//if (self::$enable == TRUE AND self::$buffer_output == TRUE) {
		if (self::$enable_display == TRUE AND self::$buffer_output == TRUE) {

			$output = self::getOutput();

			if ( function_exists('memory_get_usage') ) {
				$memory_usage = memory_get_usage();
			} else {
				$memory_usage = "N/A";
			}

			if (strlen($output) > 0) {
				echo "<br>\n<b>Debug Buffer</b><br>\n";
				echo "============================================================================<br>\n";
				echo "Memory Usage: ". $memory_usage ." Buffer Size: ". self::$buffer_size."<br>\n";
				echo "----------------------------------------------------------------------------<br>\n";
				echo $output;
				echo "============================================================================<br>\n";
			}

		}
	}

	static function Tidy() {
		if (self::$enable_tidy == TRUE ) {

			$tidy_config = Environment::getBasePath() .'/includes/tidy.conf';

			self::$tidy_obj = tidy_parse_string( ob_get_contents(), $tidy_config );

			//erase the output buffer
			ob_clean();

			//tidy_clean_repair();
			self::$tidy_obj->cleanRepair();

			echo self::$tidy_obj;

		}
		return TRUE;
    }

	static function DisplayTidyErrors() {
		if ( self::$enable_tidy == TRUE
				AND ( tidy_error_count(self::$tidy_obj) > 0 OR tidy_warning_count(self::$tidy_obj) > 0 ) ) {
			echo "<br>\n<b>Tidy Output</b><br><pre>\n";
			echo "============================================================================<br>\n";
			echo htmlentities( self::$tidy_obj->errorBuffer );
			echo "============================================================================<br></pre>\n";
		}
	}

	static function clearBuffer() {
		self::$debug_buffer = NULL;
		return TRUE;
	}
}
?>