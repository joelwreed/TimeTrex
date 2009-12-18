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
 * $Id: Misc.class.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */

/**
 * @package Core
 */
class Misc {
	/*
		this method assumes that the form has one or more
		submit buttons and that they are named according
		to this scheme:

		<input type="submit" name="submit:command" value="some value">

		This is useful for identifying which submit button actually
		submitted the form.
	*/
	static function findSubmitButton( $prefix = 'action' ) {
		// search post vars, then get vars.
		$queries = array($_POST, $_GET);
		foreach($queries as $query) {
			foreach($query as $key => $value) {
				//Debug::Text('Key: '. $key .' Value: '. $value, __FILE__, __LINE__, __METHOD__,10);
				$newvar = explode(':', $key, 2);
				//Debug::Text('Explode 0: '. $newvar[0] .' 1: '. $newvar[1], __FILE__, __LINE__, __METHOD__,10);
				 if ( isset($newvar[0]) AND isset($newvar[1]) AND $newvar[0] === $prefix ) {
					$val = $newvar[1];

					// input type=image stupidly appends _x and _y.
					if ( substr($val, strlen($val) - 2) === '_x' ) {
						$val = substr($val, 0, strlen($val) - 2);
					}

					//Debug::Text('Found Button: '. $val, __FILE__, __LINE__, __METHOD__,10);
					return strtolower($val);
				}
			}
		}

		return NULL;
	}

	static function getSortDirectionArray( $text_keys = FALSE ) {
		if ( $text_keys === TRUE ) {
			return array('asc' => 'ASC', 'desc' => 'DESC');
		} else {
			return array(1 => 'ASC', -1 => 'DESC');
		}
	}

	//This function totals arrays where the data wanting to be totaled is deep in a multi-dimentional array.
	//Usually a row array just before its passed to smarty.
	static function ArrayAssocSum($array, $element = NULL, $decimals = NULL) {

		$retarr = array();
		$totals = array();

		foreach($array as $key => $value) {
			if ( isset($element) AND isset($value[$element]) ) {
				foreach($value[$element] as $sum_key => $sum_value ) {
					if ( !isset($totals[$sum_key]) ) {
						$totals[$sum_key] = 0;
					}
					$totals[$sum_key] += $sum_value;
				}
			} else {
				//Debug::text(' Array Element not set: ', __FILE__, __LINE__, __METHOD__,10);
				foreach($value as $sum_key => $sum_value ) {
					if ( !isset($totals[$sum_key]) ) {
						$totals[$sum_key] = 0;
					}
					if ( !is_numeric( $sum_value )) {
						$sum_value = 0;
					}
					$totals[$sum_key] += $sum_value;
					//Debug::text(' Sum: '. $totals[$sum_key] .' Key: '. $sum_key .' This Value: '. $sum_value, __FILE__, __LINE__, __METHOD__,10);
				}
			}
		}

		//format totals
		if ( $decimals !== NULL ) {
			foreach($totals as $retarr_key => $retarr_value) {
				//echo "Key: $retarr_key Value: $retarr_value<br>\n";
				//Debug::text(' Number Formatting: '. $retarr_value , __FILE__, __LINE__, __METHOD__,10);
				$retarr[$retarr_key] = number_format($retarr_value, $decimals, '.','');
				//$retarr[$retarr_key] = round( $retarr_value, $decimals );
			}
		} else {
			return $totals;
		}
		unset($totals);

		return $retarr;
	}

	//This function is similar to a SQL group by clause, only its done on a AssocArray
	//Pass it a row array just before you send it to smarty.
	static function ArrayGroupBy($array, $group_by_elements, $ignore_elements = array() ) {

		if ( !is_array($group_by_elements) ) {
			$group_by_elements = array($group_by_elements);
		}

		if ( isset($ignore_elements) AND is_array($ignore_elements) ) {
			foreach($group_by_elements as $group_by_element) {
				//Remove the group by element from the ignore elements.
				unset($ignore_elements[$group_by_element]);
			}
		}

		$retarr = array();
		if ( is_array($array) ) {
			foreach( $array as $row) {
				$group_by_key_val = NULL;
				foreach($group_by_elements as $group_by_element) {
					if ( isset($row[$group_by_element]) ) {
						$group_by_key_val .= $row[$group_by_element];
					}
				}
				//Debug::Text('Group By Key Val: '. $group_by_key_val, __FILE__, __LINE__, __METHOD__,10);

				if ( !isset($retarr[$group_by_key_val]) ) {
					$retarr[$group_by_key_val] = array();
				}

				foreach( $row as $key => $val) {
					//Debug::text(' Key: '. $key .' Value: '. $val , __FILE__, __LINE__, __METHOD__,10);
					if ( in_array($key, $group_by_elements) ) {
						$retarr[$group_by_key_val][$key] = $val;
					} elseif( !in_array($key, $ignore_elements) ) {
						if ( isset($retarr[$group_by_key_val][$key]) ) {
							$retarr[$group_by_key_val][$key] = Misc::MoneyFormat( bcadd($retarr[$group_by_key_val][$key],$val), FALSE);
							//Debug::text(' Adding Value: '. $val .' For: '. $retarr[$group_by_key_val][$key], __FILE__, __LINE__, __METHOD__,10);
						} else {
							//Debug::text(' Setting Value: '. $val , __FILE__, __LINE__, __METHOD__,10);
							$retarr[$group_by_key_val][$key] = $val;
						}
					}
				}
			}
		}

		return $retarr;
	}

	static function ArrayAvg($arr) {

		if ((!is_array($arr)) OR (!count($arr) > 0)) {
			return FALSE;
		}

		return array_sum($arr) / count($arr);
	}

	static function prependArray($prepend_arr, $arr) {
		if ( !is_array($prepend_arr) AND is_array($arr) ) {
			return $arr;
		} elseif ( is_array($prepend_arr) AND !is_array($arr) ) {
			return $prepend_arr;
		} elseif ( !is_array($prepend_arr) AND !is_array($arr) ) {
			return FALSE;
		}

		$retarr = $prepend_arr;

		foreach($arr as $key => $value) {
			//Don't overwrite entries from the prepend array.
			if ( !isset($retarr[$key]) ) {
				$retarr[$key] = $value;
			}
		}

		return $retarr;
	}

	/*
		When passed an array of input_keys, and an array of output_key => output_values,
		this function will return all the output_key => output_value pairs where
		input_key == output_key
	*/
	static function arrayIntersectByKey( $keys, $options ) {
		if ( is_array($keys) and is_array($options) ) {
			foreach( $keys as $key ) {
				if ( isset($options[$key]) AND $key !== FALSE ) { //Ignore boolean FALSE, so the Root group isn't always selected.
					$retarr[$key] = $options[$key];
				}
			}

			if ( isset($retarr) ) {
				return $retarr;
			}
		}

		//Return NULL because if we return FALSE smarty will enter a
		//"blank" option into select boxes.
		return NULL;
	}

	/*
		When passed an associative array from a ListFactory, ie:
		array( 	0 => array( <...Data ..> ),
				1 => array( <...Data ..> ),
				2 => array( <...Data ..> ),
				... )
		this function will return an associative array of only the key=>value
		pairs that intersect across all rows.

	*/
	static function arrayIntersectByRow( $rows ) {
		if ( !is_array($rows) ) {
			return FALSE;
		}

		if ( count($rows) < 2 ) {
			return FALSE;
		}

		$retval = FALSE;
		if ( isset($rows[0]) ) {
			$retval = call_user_func_array( 'array_intersect_assoc', $rows );
			Debug::Arr($retval, 'Intersected/Common Data', __FILE__, __LINE__, __METHOD__, 10);
		}

		return $retval;
	}
	/*
		Returns all the output_key => output_value pairs where
		the input_keys are not present in output array keys.

	*/
	static function arrayDiffByKey( $keys, $options ) {
		if ( is_array($keys) and is_array($options) ) {
			foreach( $options as $key => $value ) {
				if ( !in_array($key, $keys, TRUE) ) { //Use strict we ignore boolean FALSE, so the Root group isn't always selected.
					$retarr[$key] = $options[$key];
				}
			}

			if ( isset($retarr) ) {
				return $retarr;
			}
		}

		//Return NULL because if we return FALSE smarty will enter a
		//"blank" option into select boxes.
		return NULL;
	}

	static function array_diff_assoc_recursive($array1, $array2) {
		foreach($array1 as $key => $value) {
			if ( is_array($value) ) {
				  if ( !isset($array2[$key]) ) {
					  $difference[$key] = $value;
				  } elseif( !is_array($array2[$key]) ) {
					  $difference[$key] = $value;
				  } else {
					  $new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);
					  if ( $new_diff !== FALSE ) {
							$difference[$key] = $new_diff;
					  }
				  }
			  } elseif ( !isset($array2[$key]) OR $array2[$key] != $value ) {
				  $difference[$key] = $value;
			  }
		}

		if ( !isset($difference) ) {
			return FALSE;
		}

		return $difference;
	}

	static function trimSortPrefix( $value, $trim_arr_value = FALSE ) {
		if ( is_array($value) AND count($value) > 0 ) {
			foreach( $value as $key => $val ) {
				if ( $trim_arr_value == TRUE ) {
					$retval[$key] = preg_replace('/^-[0-9]{3,4}-/i', '', $val);
				} else {
					$retval[preg_replace('/^-[0-9]{3,4}-/i', '', $key)] = $val;
				}
			}
		} else {
			$retval = preg_replace('/^-[0-9]{3,4}-/i', '', $value );
		}

		if ( isset($retval) ) {
			return $retval;
		}

		return $value;
	}

	static function FileDownloadHeader($file_name, $type, $size) {
		if ( $file_name == '' OR $size == '') {
			return FALSE;
		}

		$agent = trim($_SERVER['HTTP_USER_AGENT']);
		if ((preg_match('|MSIE ([0-9.]+)|', $agent, $version)) OR
			(preg_match('|Internet Explorer/([0-9.]+)|', $agent, $version))) {
			//header('Content-Type: application/x-msdownload');
			Header('Content-Type: '. $type);
			if ($version == '5.5') {
				header('Content-Disposition: filename="'.$file_name.'"');
			} else {
				header('Content-Disposition: attachment; filename="'.$file_name.'"');
			}
		} else {
			Header('Content-Type: '. $type);
			Header('Content-disposition: inline; filename='.$file_name);
		}

		Header('Content-Length: '. $size);

		return TRUE;
	}

	static function removeTrailingZeros( $value, $minimum_decimals = 2 ) {
		//Remove trailing zeros after the decimal, leave a minimum of X though.
		if ( strpos( $value, '.') !== FALSE ) {
			$trimmed_value = rtrim( $value, 0);

			$tmp_minimum_decimals = strlen( (int)strrev($trimmed_value) );
			if ( $tmp_minimum_decimals > $minimum_decimals ) {
				$minimum_decimals = $tmp_minimum_decimals;
			}
			return number_format( $value, $minimum_decimals );
		}

		return $value;
	}

	static function MoneyFormat($value, $pretty = TRUE) {

		if ( $pretty == TRUE ) {
			$thousand_sep = ',';
		} else {
			$thousand_sep = '';
		}

		return number_format( $value, 2, '.', $thousand_sep);
	}

	static function TruncateString( $str, $length, $start = 0 ) {
		$retval = trim( substr( $str, $start, $length ) );
		if ( strlen( $str ) > $length ) {
			$retval .= '...';
		}

		return $retval;
	}

	static function HumanBoolean($bool) {
		if ( $bool == TRUE ) {
			return 'Yes';
		} else {
			return 'No';
		}
	}

	static function getBeforeDecimal($float) {
		$float = Misc::MoneyFormat( $float, FALSE );

		$float_array = split('\.', $float);

		if ( isset($float_array[0]) ) {
			return $float_array[0];
		}

		return FALSE;
	}

	static function getAfterDecimal($float, $format_number = TRUE ) {
		if ( $format_number == TRUE ) {
			$float = Misc::MoneyFormat( $float, FALSE );
		}

		$float_array = split('\.', $float);

		if ( isset($float_array[1]) ) {
			return str_pad($float_array[1],2,'0');
		}

		return FALSE;
	}

	static function calculatePercent( $current, $maximum, $precision = 0 ) {
		if ( $maximum == 0 ) {
			return 100;
		}

		$percent = round( ( ( $current / $maximum ) * 100 ), (int)$precision );

		if ( $precision == 0 ) {
			$percent = (int)$percent;
		}

		return $percent;
	}

	//Takes an array with columns, and a 2nd array with column names to sum.
	static function sumMultipleColumns($data, $sum_elements) {
		if (!is_array($data) ) {
			return FALSE;
		}

		if (!is_array($sum_elements) ) {
			return FALSE;
		}

		$retval = 0;

		foreach($sum_elements as $sum_element ) {
			if ( isset($data[$sum_element]) ) {
				$retval = bcadd( $retval, $data[$sum_element]);
				//Debug::Text('Found Element in Source Data: '. $sum_element .' Retval: '. $retval, __FILE__, __LINE__, __METHOD__,10);
			}
		}

		return $retval;
	}

	static function getPointerFromArray( $array, $element, $start = 1 ) {
		//Debug::Arr($array, 'Source Array: ', __FILE__, __LINE__, __METHOD__,10);
		//Debug::Text('Searching for Element: '. $element, __FILE__, __LINE__, __METHOD__,10);
		$keys = array_keys( $array );
		//Debug::Arr($keys, 'Source Array Keys: ', __FILE__, __LINE__, __METHOD__,10);

		//Debug::Text($keys, 'Source Array Keys: ', __FILE__, __LINE__, __METHOD__,10);
		$key = array_search( $element, $keys );

		if ( $key !== FALSE ) {
			$key = $key + $start;
		}

		//Debug::Arr($key, 'Result: ', __FILE__, __LINE__, __METHOD__,10);
		return $key;
	}

	static function AdjustXY( $coord, $adjust_coord) {
		return $coord + $adjust_coord;
	}

	function writeBarCodeFile($file_name, $num, $print_text = TRUE, $height = 60 ) {
		if ( !class_exists('Image_Barcode') ) {
			require_once(Environment::getBasePath().'/classes/Image_Barcode/Barcode.php');
		}

		ob_start();
		Image_Barcode::draw($num, 'code128', 'png', FALSE, $print_text, $height);
		$ob_contents = ob_get_contents();
		ob_end_clean();

		if ( file_put_contents($file_name, $ob_contents) > 0 ) {
			//echo "Writing file successfull<Br>\n";
			return TRUE;
		} else {
			//echo "Error writing file<Br>\n";
			return FALSE;
		}
	}

	static function hex2rgb($hex, $asString = true) {
		// strip off any leading #
		if (0 === strpos($hex, '#')) {
			$hex = substr($hex, 1);
		} else if (0 === strpos($hex, '&H')) {
			$hex = substr($hex, 2);
		}

		// break into hex 3-tuple
		$cutpoint = ceil(strlen($hex) / 2)-1;
		$rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);

		// convert each tuple to decimal
		$rgb[0] = (isset($rgb[0]) ? hexdec($rgb[0]) : 0);
		$rgb[1] = (isset($rgb[1]) ? hexdec($rgb[1]) : 0);
		$rgb[2] = (isset($rgb[2]) ? hexdec($rgb[2]) : 0);

		return ($asString ? "{$rgb[0]} {$rgb[1]} {$rgb[2]}" : $rgb);
	}

	static function Array2CSV( $data, $columns = NULL, $ignore_last_row = TRUE, $include_header = TRUE ) {
		if ( is_array($data) AND count($data) > 0
				AND is_array($columns) AND count($columns) > 0 ) {

			if ( $ignore_last_row === TRUE ) {
				array_pop($data);
			}

			//Header
			if ( $include_header == TRUE ) {
				foreach( $columns as $column_name ) {
					$row_header[] = $column_name;
				}
				$out = '"'.implode('","', $row_header).'"'."\n";
			} else {
				$out = NULL;
			}

			foreach( $data as $rows ) {
				foreach ($columns as $column_key => $column_name ) {
					if ( isset($rows[$column_key]) ) {
						$row_values[] = str_replace("\"", "\"\"", $rows[$column_key]);
					} else {
						//Make sure we insert blank columns to keep proper order of values.
						$row_values[] = NULL;
					}
				}

				$out .= '"'.implode('","', $row_values).'"'."\n";
				unset($row_values);
			}

			return $out;
		}

		return FALSE;
	}

	static function inArrayByKeyAndValue( $arr, $search_key, $search_value ) {
		if ( !is_array($arr) AND $search_key != '' AND $search_value != '') {
			return FALSE;
		}

		//Debug::Text('Search Key: '. $search_key .' Search Value: '. $search_value, __FILE__, __LINE__, __METHOD__,10);
		//Debug::Arr($arr, 'Hay Stack: ', __FILE__, __LINE__, __METHOD__,10);

		foreach( $arr as $arr_key => $arr_value ) {
			if ( isset($arr_value[$search_key]) ) {
				if ( $arr_value[$search_key] == $search_value ) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	//This function is used to quickly preset array key => value pairs so we don't
	//have to have so many isset() checks throughout the code.
	static function preSetArrayValues( $arr, $keys, $preset_value = NULL ) {
		foreach( $keys as $key ) {
			if ( !isset($arr[$key]) ) {
				$arr[$key] = $preset_value;
			}
		}

		return $arr;
	}

	function parseCSV($file, $head = FALSE, $first_column = FALSE, $delim="," , $len = 9216, $max_lines = NULL ) {
		if ( !file_exists($file) ) {
			Debug::text('Files does not exist: '. $file, __FILE__, __LINE__, __METHOD__, 10);
			return FALSE;
		}

		$return = false;
		$handle = fopen($file, "r");
		if ( $head !== FALSE ) {
			if ( $first_column !== FALSE ) {
			   while ( ($header = fgetcsv($handle, $len, $delim) ) !== FALSE) {
				   if ( $header[0] == $first_column ) {
					   //echo "FOUND HEADER!<br>\n";
					   $found_header = TRUE;
					   break;
				   }
			   }

			   if ( $found_header !== TRUE ) {
				   return FALSE;
			   }
			} else {
			   $header = fgetcsv($handle, $len, $delim);
			}
		}

		$i=1;
		while ( ($data = fgetcsv($handle, $len, $delim) ) !== FALSE) {
			if ( $head AND isset($header) ) {
				foreach ($header as $key => $heading) {
					$row[$heading] = ( isset($data[$key]) ) ? $data[$key] : '';
				}
				$return[] = $row;
			} else {
				$return[] = $data;
			}

			if ( $max_lines !== NULL AND $max_lines != '' AND $i == $max_lines ) {
				break;
			}

			$i++;
		}

		fclose($handle);

		return $return;
	}

	function importApplyColumnMap( $column_map, $csv_arr ) {
		if ( !is_array($column_map) ) {
			return FALSE;
		}

		if ( !is_array($csv_arr) ) {
			return FALSE;
		}

		foreach( $column_map as $map_arr ) {
			$timetrex_column = $map_arr['timetrex_column'];
			$csv_column = $map_arr['csv_column'];
			$default_value = $map_arr['default_value'];

			if ( isset($csv_arr[$csv_column]) AND $csv_arr[$csv_column] != '' ) {
				$retarr[$timetrex_column] = trim( $csv_arr[$csv_column] );
				//echo "NOT using default value: ". $default_value ."\n";
			} elseif ( $default_value != '' ) {
				//echo "using Default value! ". $default_value ."\n";
				$retarr[$timetrex_column] = trim( $default_value );
			}
		}

		if ( isset($retarr) ) {
			return $retarr;
		}

		return FALSE;
	}

	function importCallInputParseFunction( $function_name, $input, $default_value = NULL, $parse_hint = NULL ) {
		$full_function_name = 'parse_'.$function_name;

		if ( function_exists( $full_function_name ) ) {
			//echo "      Calling Custom Parse Function for: $function_name\n";
			return call_user_func( $full_function_name, $input, $default_value, $parse_hint );
		}

		return $input;
	}

	static function encrypt( $str, $key = NULL ) {
		if ( $str == '' ) {
			return FALSE;
		}

		if ( $key == NULL OR $key == '' ) {
			global $config_vars;
			$key = $config_vars['other']['salt'];
		}

		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$max_key_size = mcrypt_enc_get_key_size($td);
		mcrypt_generic_init($td, substr($key, 0, $max_key_size), $iv);

		$encrypted_data = base64_encode( mcrypt_generic($td, trim($str) ) );

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $encrypted_data;
	}

	static function decrypt( $str, $key = NULL ) {
		if (  $key == NULL OR $key == '' ) {
			global $config_vars;
			$key = $config_vars['other']['salt'];
		}

		if ( $str == '' ) {
			return FALSE;
		}

		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$max_key_size = mcrypt_enc_get_key_size($td);
		mcrypt_generic_init($td, substr($key, 0, $max_key_size), $iv);

		$unencrypted_data = rtrim( mdecrypt_generic($td, base64_decode( $str ) ) );

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $unencrypted_data;
	}

	static function getJSArray( $values, $name = NULL, $assoc = FALSE) {
		if ( $name != '' AND (bool)$assoc == TRUE ) {
			$retval = 'new Array();';
			if ( is_array($values) AND count($values) > 0 ) {
				foreach( $values as $key => $value ) {
					$retval .= $name.'[\''. $key .'\']=\''. $value .'\';';
				}
			}
		} else {
			$retval = 'new Array("';
			if ( is_array($values) AND count($values) > 0 ) {
				$retval .= implode('","', $values);
			}
			$retval .= '");';
		}

		return $retval;
	}

	static function array_isearch( $str, $array ) {
		foreach ( $array as $key => $value ) {
			if ( strtolower( $value ) == strtolower( $str ) ) {
				return $key;
			}
		}

		return FALSE;
	}

	//Uses the internal array pointer to get array neighnors.
	static function getArrayNeighbors( $arr, $key, $neighbor = 'both' ) {
		$neighbor = strtolower($neighbor);
		//Neighor can be: Prev, Next, Both

		$retarr = array( 'prev' => FALSE, 'next' => FALSE );

		$keys = array_keys($arr);
		$key_indexes = array_flip($keys);

		if ( $neighbor == 'prev' OR $neighbor == 'both' ) {
			if ( isset($keys[$key_indexes[$key]-1]) ) {
				$retarr['prev'] = $keys[$key_indexes[$key]-1];
			}
		}

		if ( $neighbor == 'next' OR $neighbor == 'both' ) {
			if ( isset($keys[$key_indexes[$key]+1]) ) {
				$retarr['next'] = $keys[$key_indexes[$key]+1];
			}
		}
		//next($arr);

		return $retarr;
	}

	static function getHostName( $include_port = TRUE ) {
		global $config_vars;

		$server_port = NULL;
		if ( isset( $_SERVER['SERVER_PORT'] ) ) {
			$server_port = ':'.$_SERVER['SERVER_PORT'];
		}

		if ( DEPLOYMENT_ON_DEMAND == TRUE AND isset($config_vars['other']['hostname']) AND $config_vars['other']['hostname'] != '' ) {
			$server_domain = $config_vars['other']['hostname'];
		} else {
			//Try server hostname/servername first, than fallback on .ini hostname setting.
			if ( isset( $_SERVER['HTTP_HOST'] ) ) { //Use HTTP_HOST instead of SERVER_NAME first so it includes any custom ports.
				$server_domain = $_SERVER['HTTP_HOST'];
			} elseif ( isset( $_SERVER['SERVER_NAME'] ) ) {
				$server_domain = $_SERVER['SERVER_NAME'].$server_port;
			} elseif ( isset( $_SERVER['HOSTNAME'] ) ) {
				$server_domain = $_SERVER['HOSTNAME'].$server_port;
			} elseif ( isset($config_vars['other']['hostname']) AND $config_vars['other']['hostname'] != '' ) {
				$server_domain = $config_vars['other']['hostname'];
			} else {
				$server_domain = 'localhost'.$server_port;
			}
		}

		if ( $include_port == FALSE ) {
			//strip off port, important for sending emails.
			$server_domain = str_replace( $server_port, '', $server_domain );
		}

		return $server_domain;
	}

	//Accepts a search_str and key=>val array that it searches through, to return the array key of the closest match.
	static function findClosestMatch( $search_str, $search_arr, $minimum_percent_match = 0, $return_all_matches = FALSE ) {
		if ( $search_str == '' ) {
			return FALSE;
		}

		if ( !is_array($search_arr) OR count($search_arr) == 0 ) {
			return FALSE;
		}


		foreach( $search_arr as $key => $search_val ) {
			similar_text( $search_str, $search_val, $percent);
			if ( $percent >= $minimum_percent_match ) {
				$matches[$key] = $percent;
			}
		}

		if ( isset($matches) AND count($matches) > 0 ) {
			arsort($matches);

			if ( $return_all_matches == TRUE ) {
				return $matches;
			}

			reset($matches);
			return key($matches);
		}

		return FALSE;
	}

	//Converts a number between 0 and 25 to the corresponding letter.
	static function NumberToLetter( $number ) {
		if ( $number > 25 ) {
			return FALSE;
		}

		return chr($number+65);
	}

	static function issetOr( &$var, $default = NULL ) {
		if ( isset($var) ) {
			return $var;
		}

		return $default;
	}

	static function getFullName($first_name, $middle_name, $last_name, $reverse = FALSE, $include_middle = TRUE) {
		if ( $first_name != '' AND $last_name != '' ) {
			if ( $reverse === TRUE ) {
				$retval = $last_name .', '. $first_name;
				if ( $include_middle == TRUE AND $middle_name != '' ) {
					$retval .= ' '.$middle_name[0].'.'; //Use just the middle initial.
				}
			} else {
				$retval = $first_name .' '. $last_name;
			}

			return $retval;
		}

		return FALSE;
	}

	//Caller ID numbers can come in in all sorts of forms:
	// 2505551234
	// 12505551234
	// +12505551234
	// (250) 555-1234
	//Parse out just the digits, and use only the last 10 digits.
	//Currently this will not support international numbers
	static function parseCallerID( $number ) {
		$validator = new Validator();

		$retval = substr( $validator->stripNonNumeric( $number ), -10, 10 );

		return $retval;
	}

	static function generateCopyName( $name, $strict = FALSE ) {
		$name = str_replace( TTi18n::getText('Copy of'), '', $name );

		if ( $strict === TRUE ) {
			return TTi18n::getText('Copy of').' '. $name;
		} else {
			return TTi18n::getText('Copy of').' '. $name .' ['. rand(1,99) .']';
		}
	}

	static function getFileList( $start_dir, $regex_filter = NULL, $recurse = FALSE ) {
		$files = array();
		if ( is_dir($start_dir) AND is_readable( $start_dir ) ) {
			$fh = opendir($start_dir);
			while ( ($file = readdir($fh)) !== FALSE ) {
				# loop through the files, skipping . and .., and recursing if necessary
				if ( strcmp($file, '.') == 0 OR strcmp($file, '..' ) == 0 ) {
					continue;
				}

				$filepath = $start_dir . DIRECTORY_SEPARATOR . $file;
				if ( is_dir($filepath) AND $recurse == TRUE ) {
					Debug::Text(' Recursing into dir: '. $filepath , __FILE__, __LINE__, __METHOD__, 10);

					$tmp_files = self::getFileList($filepath, $regex_filter, TRUE );
					if ( $tmp_files != FALSE AND is_array($tmp_files) ) {
						$files = array_merge( $files, $tmp_files );
					}
					unset($tmp_files);
				} elseif ( !is_dir( $filepath ) ) {
					if ( $regex_filter == '*' OR preg_match( '/'.$regex_filter.'/i', $file) == 1 ) {
						//Debug::Text(' Match: Dir: '. $start_dir .' File: '. $filepath , __FILE__, __LINE__, __METHOD__, 10);
						if ( is_readable($filepath) ) {
							array_push($files, $filepath);
						} else {
							Debug::Text(' Matching file is not read/writable: '. $filepath , __FILE__, __LINE__, __METHOD__, 10);
						}
					} else {
						//Debug::Text(' NO Match: Dir: '. $start_dir .' File: '. $filepath , __FILE__, __LINE__, __METHOD__, 10);
					}
				}
			}
			closedir($fh);
			sort($files);
		} else {
			# false if the function was called with an invalid non-directory argument
			$files = FALSE;
		}

		//Debug::Arr( $files, 'Matching files: ', __FILE__, __LINE__, __METHOD__, 10);
		return $files;
	}

}
?>