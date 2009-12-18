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
 * $Revision: 3091 $
 * $Id: CA.class.php 3091 2009-11-18 18:00:31Z ipso $
 * $Date: 2009-11-18 10:00:31 -0800 (Wed, 18 Nov 2009) $
 */

/**
 * @package PayrollDeduction
 */
class PayrollDeduction_US_CA extends PayrollDeduction_US {
/*
														10 => 'Single',
														20 => 'Married - Spouse Works',
														30 => 'Married - Spouse does not Work',
														40 => 'Head of Household',
*/

	var $state_options = array(
								1257058800 => array( //01-Nov-09
													'standard_deduction' => array(
																				//First entry is 0,1 allowance, second is for 2 or more.
																				'10' => array(3692.00, 3692.00),
																				'20' => array(3692.00, 3692.00),
																				'30' => array(3692.00, 7384.00),
																				'40' => array(7384.00, 7384.00),
																				),
													'allowance' => array(
																				'10' => 108.90,
																				'20' => 108.90,
																				'30' => 108.90,
																				'40' => 108.90,
																				),
													),

								1230796800 => array(
													'standard_deduction' => array(
																				//First entry is 0,1 allowance, second is for 2 or more.
																				'10' => array(3692.00, 3692.00),
																				'20' => array(3692.00, 3692.00),
																				'30' => array(3692.00, 7384.00),
																				'40' => array(7384.00, 7384.00),
																				),
													'allowance' => array(
																				'10' => 99.00,
																				'20' => 99.00,
																				'30' => 99.00,
																				'40' => 99.00,
																				),
													),
								1199174400 => array(
													'standard_deduction' => array(
																				//First entry is 0,1 allowance, second is for 2 or more.
																				'10' => array(3516.00, 3516.00),
																				'20' => array(3516.00, 3516.00),
																				'30' => array(3516.00, 7032.00),
																				'40' => array(7032.00, 7032.00),
																				),
													'allowance' => array(
																				'10' => 94.00,
																				'20' => 94.00,
																				'30' => 94.00,
																				'40' => 94.00,
																				),
													),
								1167638400 => array(
													'standard_deduction' => array(
																				//First entry is 0,1 allowance, second is for 2 or more.
																				'10' => array(3410.00, 3410.00),
																				'20' => array(3410.00, 3410.00),
																				'30' => array(3410.00, 6820.00),
																				'40' => array(6820.00, 6820.00),
																				),
													'allowance' => array(
																				'10' => 91.00,
																				'20' => 91.00,
																				'30' => 91.00,
																				'40' => 91.00,
																				),
													),
								1136102400 => array(
													'standard_deduction' => array(
																				//First entry is 0,1 allowance, second is for 2 or more.
																				'10' => array(3254.00, 3254.00),
																				'20' => array(3254.00, 3254.00),
																				'30' => array(3254.00, 6508.00),
																				'40' => array(6508.00, 6508.00),
																				),
													'allowance' => array(
																				'10' => 87.00,
																				'20' => 87.00,
																				'30' => 87.00,
																				'40' => 87.00,
																				),
													)
								);

	function getStatePayPeriodDeductions() {
		return bcdiv($this->getStateTaxPayable(), $this->getAnnualPayPeriods() );
	}

	function getStateAnnualTaxableIncome() {
		return bcsub( $this->getAnnualTaxableIncome(), $this->getStateStandardDeduction() );
	}

	function getStateStandardDeduction() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;

		}

		$deduction_arr = $retarr['standard_deduction'][$this->getStateFilingStatus()];

		if ( $this->getStateAllowance() == 0 OR $this->getStateAllowance() == 1 ) {
			$deduction = $deduction_arr[0];
		} elseif ( $this->getStateAllowance() >= 2 ) {
			$deduction = $deduction_arr[1];
		}
		Debug::text('Standard Deduction: '. $deduction .' Allowances: '. $this->getStateAllowance() .' Filing Status: '. $this->getStateFilingStatus() , __FILE__, __LINE__, __METHOD__,10);

		return $deduction;
	}

	function getStateAllowanceAmount() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;
		}

		$allowance = $retarr['allowance'][$this->getStateFilingStatus()];

		$retval = 0;
		if ( $this->getStateAllowance() == 0 ) {
			$retval = 0;
		} elseif ( $this->getStateAllowance() >= 1 ) {
			$retval = bcmul($allowance, $this->getStateAllowance() );
		}

		Debug::text('State Allowance Amount: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	function getStateTaxPayable() {
		$annual_income = $this->getStateAnnualTaxableIncome();

		$retval = 0;

		if ( $annual_income > 0 ) {
			$rate = $this->getData()->getStateRate($annual_income);
			$prev_income = $this->getData()->getStateRatePreviousIncome($annual_income);
			$state_constant = $this->getData()->getStateConstant($annual_income);

			$retval = bcsub( bcadd( bcmul( bcsub( $annual_income, $prev_income ), $rate ), $state_constant ), $this->getStateAllowanceAmount() );
		}

		Debug::text('State Annual Tax Payable: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		if ( $retval < 0 ) {
			$retval = 0;
		}

		return $retval;
	}

	function getStateEmployerUI() {
		if ( $this->getUIExempt() == TRUE ) {
			return 0;
		}

		$pay_period_income = $this->getGrossPayPeriodIncome();
		$rate = bcdiv( $this->getStateUIRate(), 100 );
		$maximum_contribution = bcmul( $this->getStateUIWageBase(), $rate );
		$ytd_contribution = $this->getYearToDateStateUIContribution();

		Debug::text('Rate: '. $rate .' YTD Contribution: '. $ytd_contribution .' Maximum: '. $maximum_contribution, __FILE__, __LINE__, __METHOD__,10);

		$amount = bcmul( $pay_period_income, $rate );
		$max_amount = bcsub( $maximum_contribution, $ytd_contribution );

		if ( $amount > $max_amount ) {
			$retval = $max_amount;
		} else {
			$retval = $amount;
		}

		return $retval;
	}

}
?>
