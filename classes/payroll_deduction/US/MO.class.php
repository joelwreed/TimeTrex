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
 * $Revision: 2326 $
 * $Id: MO.class.php 2326 2009-01-07 17:02:29Z ipso $
 * $Date: 2009-01-07 09:02:29 -0800 (Wed, 07 Jan 2009) $
 */

/**
 * @package PayrollDeduction
 */
class PayrollDeduction_US_MO extends PayrollDeduction_US {
/*
														10 => 'Single',
														20 => 'Married - Spouse Works',
														30 => 'Married - Spouse does not Work',
														40 => 'Head of Household',
*/

	var $state_options = array(
								1230796800 => array(
													'standard_deduction' => array(
																				'10' => 5700.00,
																				'20' => 5700.00,
																				'30' => 11400.00,
																				'40' => 8350.00,
																				),
													'allowance' => array(
																				'10' => array( 2100.00, 1200.00, 1200.00 ),
																				'20' => array( 2100.00, 1200.00, 1200.00 ),
																				'30' => array( 2100.00, 2100.00, 1200.00 ),
																				'40' => array( 3500.00, 1200.00, 1200.00 ),
																				),
													'federal_tax_maximum' => array(
																				'10' => 5000.00,
																				'20' => 5000.00,
																				'30' => 10000.00,
																				'40' => 5000.00
																				)
													),
								1167638400 => array(
													'standard_deduction' => array(
																				'10' => 5350.00,
																				'20' => 5350.00,
																				'30' => 10700.00,
																				'40' => 7850.00,
																				),
													'allowance' => array(
																				'10' => array( 1200.00, 1200.00, 1200.00 ),
																				'20' => array( 1200.00, 1200.00, 1200.00 ),
																				'30' => array( 1200.00, 1200.00, 1200.00 ),
																				'40' => array( 3500.00, 1200.00, 1200.00 ),
																				),
													'federal_tax_maximum' => array(
																				'10' => 5000.00,
																				'20' => 5000.00,
																				'30' => 10000.00,
																				'40' => 5000.00
																				)
													),
								1136102400 => array(
													'standard_deduction' => array(
																				'10' => 5150.00,
																				'20' => 5150.00,
																				'30' => 10300.00,
																				'40' => 7550.00,
																				),
													'allowance' => array(
																				'10' => array( 1200.00, 1200.00, 1200.00 ),
																				'20' => array( 1200.00, 1200.00, 1200.00 ),
																				'30' => array( 1200.00, 1200.00, 1200.00 ),
																				'40' => array( 3500.00, 1200.00, 1200.00 ),
																				),
													'federal_tax_maximum' => array(
																				'10' => 5000.00,
																				'20' => 5000.00,
																				'30' => 10000.00,
																				'40' => 5000.00
																				)

													)
								);

	function getStatePayPeriodDeductions() {
		return $this->RoundNearestDollar( bcdiv($this->getStateTaxPayable(), $this->getAnnualPayPeriods() ) );
	}

	function getStateAnnualTaxableIncome() {
		$annual_income = $this->getAnnualTaxableIncome();
		$federal_tax = $this->getFederalTaxPayable();
		$state_deductions = $this->getStateStandardDeduction();
		$state_allowance = $this->getStateAllowanceAmount();

		if ( $federal_tax > $this->getStateFederalTaxMaximum() ) {
			$federal_tax = $this->getStateFederalTaxMaximum();
		}

		$income = bcsub( bcsub( bcsub($annual_income, $federal_tax), $state_deductions), $state_allowance );

		Debug::text('State Annual Taxable Income: '. $income, __FILE__, __LINE__, __METHOD__,10);

		return $income;
	}

	function getStateFederalTaxMaximum() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;
		}

		$maximum = $retarr['federal_tax_maximum'][$this->getStateFilingStatus()];

		Debug::text('Maximum State allowed Federal Tax: '. $maximum, __FILE__, __LINE__, __METHOD__,10);

		return $maximum;
	}

	function getStateStandardDeduction() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;

		}

		$deduction = $retarr['standard_deduction'][$this->getStateFilingStatus()];

		Debug::text('Standard Deduction: '. $deduction, __FILE__, __LINE__, __METHOD__,10);

		return $deduction;
	}

	function getStateAllowanceAmount() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;

		}

		$allowance_arr = $retarr['allowance'][$this->getStateFilingStatus()];

		if ( $this->getStateAllowance() == 0 ) {
			$retval = 0;
		} elseif ( $this->getStateAllowance() == 1 ) {
			$retval = $allowance_arr[0];
		} elseif ( $this->getStateAllowance() == 2 ) {
			$retval = $allowance_arr[1];
		} else {
			$retval = bcadd($allowance_arr[0], bcmul( bcsub( $this->getStateAllowance(),1 ), $allowance_arr[2] ) );
		}

		Debug::text('State Allowance Amount: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	function getStateTaxPayable() {
		$annual_income = $this->getStateAnnualTaxableIncome();

		$retval = 0;

		if ( $annual_income > 0 ) {
			$rate = $this->getData()->getStateRate($annual_income);
			$state_constant = $this->getData()->getStateConstant($annual_income);
			$state_rate_income = $this->getData()->getStateRateIncome($annual_income);

			$retval = bcadd( bcmul( bcsub( $annual_income, $state_rate_income ), $rate ), $state_constant );
		}

		if ( $retval < 0 ) {
			$retval = 0;
		}

		Debug::text('State Annual Tax Payable: '. $retval, __FILE__, __LINE__, __METHOD__,10);

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
