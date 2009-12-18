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
 * $Revision: 2286 $
 * $Id: BC.class.php 2286 2008-12-12 23:12:41Z ipso $
 * $Date: 2008-12-12 15:12:41 -0800 (Fri, 12 Dec 2008) $
 */

/**
 * @package PayrollDeduction
 */
class PayrollDeduction_CA_BC extends PayrollDeduction_CA {
	function getProvincialTaxReduction() {

		$A = $this->getAnnualTaxableIncome();
		$T4 = $this->getProvincialBasicTax();
		$V1 = $this->getProvincialSurtax();
		$Y = 0;
		$S = 0;

		Debug::text('BC Specific - Province: '. $this->getProvince(), __FILE__, __LINE__, __METHOD__,10);
		if ( $this->getDate() >= strtotime('01-Jan-2009') ) {
			//Calculate S after Jan 1st 2009.
			if ( $A <= 17285 ) {
				Debug::text('S: Annual Income less than 17285', __FILE__, __LINE__, __METHOD__,10);
				if ( $T4 > 389 ) {
					$S = 389;
				} else {
					$S = $T4;
				}
			} elseif ( $A > 17285 AND $A <= 29441.25) {
				Debug::text('S: Annual Income less than 29441.25', __FILE__, __LINE__, __METHOD__,10);

				$tmp_S = bcsub( 389, bcmul( bcsub( $A, 17285 ), 0.032 ) );
				Debug::text('Tmp_S: '. $tmp_S, __FILE__, __LINE__, __METHOD__,10);

				if ( $T4 > $tmp_S ) {
					$S = $tmp_S;
				} else {
					$S = $T4;
				}
				unset($tmp_S);
			}
		} elseif ( $this->getDate() >= strtotime('01-Jan-2008') ) {
			//Calculate S after Jan 1st 2008.
			if ( $A <= 16946 ) {
				Debug::text('S: Annual Income less than 16646', __FILE__, __LINE__, __METHOD__,10);
				if ( $T4 > 381 ) {
					$S = 381;
				} else {
					$S = $T4;
				}
			} elseif ( $A > 16946 AND $A <= 28852.25) {
				Debug::text('S: Annual Income less than 28852.25', __FILE__, __LINE__, __METHOD__,10);

				$tmp_S = bcsub( 381, bcmul( bcsub( $A, 16946 ), 0.032 ) );
				Debug::text('Tmp_S: '. $tmp_S, __FILE__, __LINE__, __METHOD__,10);

				if ( $T4 > $tmp_S ) {
					$S = $tmp_S;
				} else {
					$S = $T4;
				}
				unset($tmp_S);
			}
		} elseif ( $this->getDate() >= strtotime('01-Jul-2007') ) {
			//Calculate S after Jul 1st 2007.
			if ( $A <= 16646 ) {
				Debug::text('S: Annual Income less than 16646', __FILE__, __LINE__, __METHOD__,10);
				if ( $T4 > 375 ) {
					$S = 375;
				} else {
					$S = $T4;
				}
			} elseif ( $A > 16646 AND $A <= 28364.75) {
				Debug::text('S: Annual Income less than 27062.67', __FILE__, __LINE__, __METHOD__,10);

				$tmp_S = bcsub( 375, bcmul( bcsub( $A, 16646 ), 0.032 ) );
				Debug::text('Tmp_S: '. $tmp_S, __FILE__, __LINE__, __METHOD__,10);

				if ( $T4 > $tmp_S ) {
					$S = $tmp_S;
				} else {
					$S = $T4;
				}
				unset($tmp_S);
			}
		} elseif ( $this->getDate() >= strtotime('01-Jan-2007') ) {
			//Calculate S after Jan 1st 2007.
			if ( $A <= 16646 ) {
				Debug::text('S: Annual Income less than 16646', __FILE__, __LINE__, __METHOD__,10);
				if ( $T4 > 375 ) {
					$S = 375;
				} else {
					$S = $T4;
				}
			} elseif ( $A > 16646 AND $A <= 27062.67) {
				Debug::text('S: Annual Income less than 27062.67', __FILE__, __LINE__, __METHOD__,10);

				$tmp_S = bcsub( 375, bcmul( bcsub( $A, 16646 ), 0.036 ) );
				Debug::text('Tmp_S: '. $tmp_S, __FILE__, __LINE__, __METHOD__,10);

				if ( $T4 > $tmp_S ) {
					$S = $tmp_S;
				} else {
					$S = $T4;
				}
				unset($tmp_S);
			}
		} elseif ( $this->getDate() >= strtotime('01-Jan-2006') ) {
			//Calculate S after Jan 1st 2006.
			if ( $A <= 16336 ) {
				Debug::text('S: Annual Income less than 16336', __FILE__, __LINE__, __METHOD__,10);
				if ( $T4 > 368 ) {
					$S = 368;
				} else {
					$S = $T4;
				}
			} elseif ( $A > 16336 AND $A <= 26558.22) {
				Debug::text('S: Annual Income less than 26558', __FILE__, __LINE__, __METHOD__,10);

				//$tmp_S = 368 - ( ( $A - 16336 ) * 0.036 );
				$tmp_S = bcsub( 368, bcmul( bcsub( $A, 16336 ), 0.036 ) );
				Debug::text('Tmp_S: '. $tmp_S, __FILE__, __LINE__, __METHOD__,10);

				if ( $T4 > $tmp_S ) {
					$S = $tmp_S;
				} else {
					$S = $T4;
				}
				unset($tmp_S);
			}
		} elseif ( $this->getDate() >= strtotime('01-Jul-2005') ) {
			//Calculate S after July 1st 2005.
			if ( $A <= 16000 ) {
				Debug::text('S: Annual Income less than 16000', __FILE__, __LINE__, __METHOD__,10);
				if ( $T4 > 360 ) {
					$S = 360;
				} else {
					$S = $T4;
				}
			} elseif ( $A > 16000 AND $A <= 26000) {
				Debug::text('S: Annual Income less than 26000', __FILE__, __LINE__, __METHOD__,10);

				//$tmp_S = 360 - ( ( $A - 16000 ) * 0.036 );
				$tmp_S = bcsub( 360, bcmul( bcsub( $A, 16000 ), 0.036 ) );
				Debug::text('Tmp_S: '. $tmp_S, __FILE__, __LINE__, __METHOD__,10);

				if ( $T4 > $tmp_S ) {
					$S = $tmp_S;
				} else {
					$S = $T4;
				}
				unset($tmp_S);
			}
		}

		Debug::text('aS: '. $S, __FILE__, __LINE__, __METHOD__,10);

		if ( $S < 0 ) {
			$S = 0;
		}

		Debug::text('bS: '. $S, __FILE__, __LINE__, __METHOD__,10);

		return $S;
	}
}
?>
