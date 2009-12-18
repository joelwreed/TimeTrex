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
 * $Revision: 2321 $
 * $Id: FormVariables.class.php 2321 2009-01-03 00:58:17Z ipso $
 * $Date: 2009-01-02 16:58:17 -0800 (Fri, 02 Jan 2009) $
 */

/**
 * @package Core
 */
class FormVariables {
	static function getVariables($form_variables, $form_type = 'BOTH', $filter_input = TRUE, $filter_ignore_name_arr = array('next_page','batch_next_page') ) {
		$form_type = trim(strtoupper($form_type));

		if ( is_array($form_variables) ) {
			foreach($form_variables as $variable_name) {

				$retarr[$variable_name] = NULL;
				switch ($form_type) {
					case 'GET':
						if ( isset($_GET[$variable_name]) ) {
							$retarr[$variable_name] = $_GET[$variable_name];
						}
						break;
					case 'POST':
						if ( isset($_POST[$variable_name]) ) {
							$retarr[$variable_name] = $_POST[$variable_name];
						}
					default:
						if ( isset($_GET[$variable_name]) ) {
							$retarr[$variable_name] = $_GET[$variable_name];
						} elseif ( isset($_POST[$variable_name]) ) {
							$retarr[$variable_name] = $_POST[$variable_name];
						}
				}

				//Ignore next_page, batch_next_page variables as those are encoded URLs passed in, and htmlspecialchars
				//will break them.
				if ( $filter_input == TRUE AND is_string($retarr[$variable_name]) AND $retarr[$variable_name] != ''
						AND ( !is_array($filter_ignore_name_arr) OR ( is_array($filter_ignore_name_arr) AND !in_array( $variable_name, $filter_ignore_name_arr) ) ) ) {
					//Remove "javascript:" from all inputs, and run htmlspecialchars over them to help prevent XSS attacks.
					$retarr[$variable_name] = htmlspecialchars( str_ireplace('javascript:', '', $retarr[$variable_name] ), ENT_QUOTES, 'UTF-8' );
				} elseif ( strtolower($filter_input) == 'recurse' AND is_array($retarr[$variable_name])
								AND ( !is_array($filter_ignore_name_arr) OR ( is_array($filter_ignore_name_arr) AND !in_array( $variable_name, $filter_ignore_name_arr) ) ) ) {
					self::RecurseFilterArray($retarr[$variable_name]);
				}
			}

			if ( isset($retarr) ) {
				return $retarr;
			}
		}

		//Return empty array so extract() doesn't complain.
		return array();
	}

	static function RecurseFilterArray(&$arr) {
		if ( !is_array($arr) ) {
			return FALSE;
		}

		foreach ($arr as $key => $val) {
			if ( is_array($val) ) {
				self::RecurseFilterArray($arr[$key]);
			} else {
				$arr[$key] = htmlspecialchars( str_ireplace('javascript:', '', $val ), ENT_QUOTES, 'UTF-8' );
			}
		}

		return TRUE;
	}
}
?>
