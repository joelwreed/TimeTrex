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
 * $Id: Sort.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Core
 */
class Sort {
	static function multiSort( $data, $col1, $col2 = NULL, $col1_order = 'ASC', $col2_order = 'ASC' ) {
		global $profiler;

		$profiler->startTimer( "multiSort()");
		//Debug::Text('Sorting... Col1: '. $col1 .' Col2: '. $col2 .' Col1 Order: '. $col1_order .' Col2 Order: '. $col2_order, __FILE__, __LINE__, __METHOD__,10);

		foreach ($data as $key => $row) {
			if ( isset($row[$col1]) ) {
				$sort_col1[$key] = $row[$col1];
			} else {
				$sort_col1[$key] = NULL;
			}

			if ( $col2 !== NULL ) {
				if ( isset($row[$col2]) ) {
					$sort_col2[$key] = $row[$col2];
				} else {
					$sort_col2[$key] = NULL;
				}
			}
		}

		if ( strtolower($col1_order) == 'desc' OR $col1_order == -1 ) {
			$col1_order = SORT_DESC;
		} else {
			$col1_order = SORT_ASC;
		}

		if ( strtolower($col2_order) == 'desc' OR $col2_order == -1 ) {
			$col2_order = SORT_DESC;
		} else {
			$col2_order = SORT_ASC;
		}

		if ( isset($sort_col2) ) {
			array_multisort($sort_col1, $col1_order, $sort_col2, $col2_order, $data);
		} else {
			array_multisort($sort_col1, $col1_order, $data);
		}

		$profiler->stopTimer( "multiSort()");
		return $data;
	}
}
?>
