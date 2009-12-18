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
 * $Revision: 2874 $
 * $Id: Validator.class.php 2874 2009-10-06 23:37:59Z ipso $
 * $Date: 2009-10-06 16:37:59 -0700 (Tue, 06 Oct 2009) $
 */

/**
 * @package Core
 */
class Validator {
	private $num_errors = 0; //Number of errors.
	private $errors = array(); //Array of errors.
	private $verbosity = 8;

	//Checks a result set for one or more rows.
	function isResultSetWithRows($label, $rs, $msg = NULL) {
		//Debug::Arr($rs, 'ResultSet: ', __FILE__, __LINE__, __METHOD__, $this->verbosity);

		if ( is_object($rs) ) {
			foreach($rs as $result) {
				return TRUE;
			}
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isNotResultSetWithRows($label, $rs, $msg = NULL) {
		//Debug::Arr($rs, 'ResultSet: ', __FILE__, __LINE__, __METHOD__, $this->verbosity);

		if ( is_object($rs) ) {
			foreach($rs as $result) {
				$this->Error($label, $msg);

				return FALSE;
			}
		}

		return TRUE;
	}

	//Function to simple set an error.
	function isTrue($label, $value, $msg = NULL) {
		if ($value == TRUE) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isFalse($label, $value, $msg = NULL) {
		if ($value == FALSE) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isNull($label, $value, $msg = NULL) {
		//Debug::text('Value: '. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		if ($value == NULL ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isNotNull($label, $value, $msg = NULL) {
		//Debug::text('Value: '. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		if ($value != NULL ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function inArrayValue($label, $value, $msg = NULL, $array) {
		//Debug::text('Value: '. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		if (is_array($array) AND in_array($value, array_values( $array ) ) ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function inArrayKey($label, $key, $msg = NULL, $array) {
		//Debug::text('Key: '. $key, __FILE__, __LINE__, __METHOD__, $this->verbosity);
		//Debug::Arr($array, 'isArrayKey Array:', __FILE__, __LINE__, __METHOD__, $this->verbosity);
		if (is_array($array) AND in_array($key, array_keys( $array ) ) ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isNumeric($label, $value, $msg = NULL) {
		//Debug::Text('Value:'. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		//if ( preg_match('/^[-0-9]+$/',$value) ) {
		if ( is_numeric( $value ) == TRUE ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isFloat($label, $value, $msg = NULL) {
		//Debug::Text('Value:'. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		//if ( preg_match('/^[-0-9\.]+$/',$value) ) {
		if ( preg_match('/^((\.[0-9]+)|([-0-9]+(\.[0-9]*)?))$/',$value) ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isRegEx($label, $value, $msg, $regex) {
		//Debug::text('Value: '. $value .' RegEx: '. $regex, __FILE__, __LINE__, __METHOD__, $this->verbosity);
		if ( preg_match($regex,$value) ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isLength($label, $value, $msg = NULL, $min = 1, $max = 255) {
		$len = strlen($value);

		//Debug::text('Value: '. $value .' Length: '. $len .' Min: '. $min .' Max: '. $max, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		if ($len < $min OR $len > $max) {
			$this->Error($label, $msg);

			return FALSE;
		}

		return TRUE;
	}

	function isPhoneNumber($label, $value, $msg = NULL) {

		//Strip out all non-numeric characters.
		$phone = $this->stripNonNumeric($value);

		//Debug::text('Raw Phone: '. $value .' Phone: '. $phone, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		if ( strlen($phone) >= 6 AND strlen($phone) <= 20
			 AND preg_match('/^[0-9\(\)\-\.\+\ ]{6,20}$/i',$value) ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isPostalCode($label, $value, $msg = NULL, $country = NULL, $province = NULL) {
		//Debug::text('Raw Postal Code: '. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		//Remove any spaces, keep dashes for US extended ZIP.
		$value = str_replace( array(' '), '', $value );

		$province = strtolower( trim($province) );

		switch ( strtolower(trim($country)) ) {
			case 'us':
				//US zip code
				if ( preg_match('/^[0-9]{5}$/i',$value) OR preg_match('/^[0-9]{5}\-[0-9]{4}$/i',$value) ) {

					if ( $province != '' ) {
						$province_postal_code_map = array (
										'ak' => array ('9950099929'),
										'al' => array ('3500036999'),
										'ar' => array ('7160072999', '7550275505'),
										'az' => array ('8500086599'),
										'ca' => array ('9000096199'),
										'co' => array ('8000081699'),
										'ct' => array ('0600006999'),
										'dc' => array ('2000020099', '2020020599'),
										'de' => array ('1970019999'),
										'fl' => array ('3200033999', '3410034999'),
										'ga' => array ('3000031999'),
										'hi' => array ('9670096798', '9680096899'),
										'ia' => array ('5000052999'),
										'id' => array ('8320083899'),
										'il' => array ('6000062999'),
										'in' => array ('4600047999'),
										'ks' => array ('6600067999'),
										'ky' => array ('4000042799', '4527545275'),
										'la' => array ('7000071499', '7174971749'),
										'ma' => array ('0100002799'),
										'md' => array ('2033120331', '2060021999'),
										'me' => array ('0380103801', '0380403804', '0390004999'),
										'mi' => array ('4800049999'),
										'mn' => array ('5500056799'),
										'mo' => array ('6300065899'),
										'ms' => array ('3860039799'),
										'mt' => array ('5900059999'),
										'nc' => array ('2700028999'),
										'nd' => array ('5800058899'),
										'ne' => array ('6800069399'),
										'nh' => array ('0300003803', '0380903899'),
										'nj' => array ('0700008999'),
										'nm' => array ('8700088499'),
										'nv' => array ('8900089899'),
										'ny' => array ('0040000599', '0639006390', '0900014999'),
										'oh' => array ('4300045999'),
										'ok' => array ('7300073199', '7340074999'),
										'or' => array ('9700097999'),
										'pa' => array ('1500019699'),
										'ri' => array ('0280002999', '0637906379'),
										'sc' => array ('2900029999'),
										'sd' => array ('5700057799'),
										'tn' => array ('3700038599', '7239572395'),
										'tx' => array ('7330073399', '7394973949', '7500079999', '8850188599'),
										'ut' => array ('8400084799'),
										'va' => array ('2010520199', '2030120301', '2037020370', '2200024699'),
										'vt' => array ('0500005999'),
										'wa' => array ('9800099499'),
										'wi' => array ('4993649936', '5300054999'),
										'wv' => array ('2470026899'),
										'wy' => array ('8200083199')
									);

						if ( isset($province_postal_code_map[$province]) ) {
							$zip5 = substr($value, 0, 5);
							//Debug::text('Checking ZIP code range, short zip: '. $zip5, __FILE__, __LINE__, __METHOD__, $this->verbosity);
							foreach( $province_postal_code_map[$province] as $postal_code_range ) {
								//Debug::text('Checking ZIP code range: '. $postal_code_range, __FILE__, __LINE__, __METHOD__, $this->verbosity);
								if ( ( $zip5 >= substr($postal_code_range, 0, 5) ) AND ( $zip5 <= substr( $postal_code_range, 5 ) ) ) {
									return TRUE;
								}
							}
						} else {
							//Debug::text('Postal Code does not match province!', __FILE__, __LINE__, __METHOD__, $this->verbosity);
						}

					} else {
						return TRUE;
					}
				}
				break;
			case 'ca':
				//Canada postal code
				if ( preg_match('/^[a-zA-Z]{1}[0-9]{1}[a-zA-Z]{1}[-]?[0-9]{1}[a-zA-Z]{1}[0-9]{1}$/i',$value) ) {
					if ( $province != '' ) {
						//Debug::text('Verifying postal code against province!', __FILE__, __LINE__, __METHOD__, $this->verbosity);
						$province_postal_code_map = array(
												'ab' => array('t'),
												'bc' => array('v'),
												'sk' => array('s'),
												'mb' => array('r'),
												'qc' => array('g', 'h', 'j'),
												'on' => array('k','l','m','n','p'),
												'nl' => array('a'),
												'nb' => array('e'),
												'ns' => array('b'),
												'pe' => array('c'),
												'nt' => array('x'),
												'yt' => array('y'),
												'nu' => array('x')
											);

						//Debug::Arr($province_postal_code_map[$province], 'Valid Postal Codes for Province', __FILE__, __LINE__, __METHOD__, $this->verbosity);
						if ( isset($province_postal_code_map[$province]) AND in_array( substr( strtolower($value), 0, 1), $province_postal_code_map[$province] ) )  {
							return TRUE;
						} else {
							//Debug::text('Postal Code does not match province!', __FILE__, __LINE__, __METHOD__, $this->verbosity);
						}
					} else {
						return TRUE;
					}
				}
				break;
			default:
				//US
				if ( preg_match('/^[0-9]{5}$/i',$value) OR preg_match('/^[0-9]{5}\-[0-9]{4}$/i',$value) ) {
					return TRUE;
				}

				//CA
				if ( preg_match('/^[a-zA-Z]{1}[0-9]{1}[a-zA-Z]{1}[-]?[0-9]{1}[a-zA-Z]{1}[0-9]{1}$/i',$value) ) {
					return TRUE;
				}

				//Other
				if ( preg_match('/^[a-zA-Z0-9]{1,10}$/i',$value) ) {
					return TRUE;
				}

				break;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isEmail($label, $value, $msg = NULL) {
		//Debug::text('Raw Email: '. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		if ( preg_match('/^[\w\.\-\&]+\@[\w\.\-]+\.[a-z]{2,4}$/i',$value) ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isIPAddress($label, $value, $msg = NULL) {
		//Debug::text('Raw IP: '. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		$ip = explode(".", $value);

		if( count($ip) == 4 ) {
			$valid = TRUE;

			foreach($ip as $block) {
				if( !is_numeric($block) OR $block >= 255 OR $block < 0) {
					$valid = FALSE;
				}
			}

			if ( $valid == TRUE ) {
				return TRUE;
			}
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isDate($label, $value, $msg = NULL) {
		if ( $value != '' AND is_numeric($value) ) {
			$date = gmdate("U", $value);
			//Debug::text('Raw Date: '. $value .' Converted Value: '. $date, __FILE__, __LINE__, __METHOD__, $this->verbosity);

			if (  $date == $value ) {
				return TRUE;
			}
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	function isSIN($label, $value, $msg = NULL) {
		//Debug::text('Raw SIN: '. $value, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		$sin = $this->stripNonNumeric($value);

		if ( strlen($sin) == 9 ) {
			return TRUE;
		}

		$this->Error($label, $msg);

		return FALSE;
	}

	/*
	 * String manipulation functions.
	 */

	function stripSpaces($value) {
		return str_replace(' ', '', trim($value));
	}

	function stripNonNumeric($value) {
		$retval = preg_replace('/[^0-9]/','',$value);

		//Debug::Text('Numeric String:'. $retval, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		return $retval;
	}

	function stripNonAlphaNumeric($value) {
		$retval = preg_replace('/[^A-Za-z0-9]/','',$value);

		//Debug::Text('Alpha Numeric String:'. $retval, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		return $retval;
	}

	function stripNonFloat($value) {
		$retval = preg_replace('/[^-0-9\.]/','',$value);

		//Debug::Text('Float String:'. $retval, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		return $retval;

	}

	/*
	 * Class standard functions.
	 */

	function varReplace($string, $var_array) {
		//var_array = arary('var1' => 'blah1', 'var2' => 'blah2');
		if ( is_array($var_array) AND count($var_array) > 0) {
			foreach($var_array as $key => $value) {
				$keys[] = '#'.$key;
				$values[] = $value;
			}
		}

		$retval = str_replace($keys, $values, $string);

		return $retval;
	}

	function getErrorsArray() {
		return $this->errors;
	}

	function getErrors() {
		if ( count($this->errors ) > 0) {
			$output = "<ol>\n";
			foreach ($this->errors as $label) {
				foreach ($label as $key => $msg) {
					$output .= '<li>'.$msg.".</li>";
				}
			}
			$output .= "</ol>\n";
			return $output;
		}

		return FALSE;
	}

	function getTextErrors() {
		if ( count($this->errors ) > 0) {
			$output = '';
			$i=1;
			foreach ($this->errors as $label) {
				foreach ($label as $key => $msg) {
					$output .= $i.'. '.$msg."\n";
				}

				$i++;
			}
			return $output;
		}

		return FALSE;
	}

	final function isValid($label = NULL) {

		if ($this->num_errors == 0) {
			return TRUE;
		} elseif ($label != NULL) {
			Debug::text('Num Errors: '. $this->num_errors, __FILE__, __LINE__, __METHOD__, $this->verbosity);

			//Check to see if a single form variable is valid.
            if ( !isset($this->errors[$label]) ) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		Debug::Arr($this->errors, 'Errors', __FILE__, __LINE__, __METHOD__, $this->verbosity);

		return FALSE;
	}

	function resetErrors() {
		unset($this->errors);
		$this->num_errors = 0;

		return TRUE;
	}

	function hasError( $label ) {
		if ( in_array($label, array_keys($this->errors)) ) {
			return TRUE;
		}

		return FALSE;
	}

	private function Error($label, $msg) {
		Debug::text('Validation Error: Label: '. $label .' Msg: '. $msg, __FILE__, __LINE__, __METHOD__, $this->verbosity);

		//If label is NULL, assume we don't actually want to trigger an error.
		//This is good for just using the check functions for other purposes.
		if ( $label != '') {
			$this->errors[$label][] = $msg;

			$this->num_errors++;

			return TRUE;
		}

		return FALSE;
	}
}
?>
