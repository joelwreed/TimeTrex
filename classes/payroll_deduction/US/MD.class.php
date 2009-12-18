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
 * $Revision: 2449 $
 * $Id: MD.class.php 2449 2009-03-17 20:31:09Z ipso $
 * $Date: 2009-03-17 13:31:09 -0700 (Tue, 17 Mar 2009) $
 */

/**
 * @package PayrollDeduction
 */
class PayrollDeduction_US_MD extends PayrollDeduction_US {

	var $state_options = array(

								1199174400 => array( //2008
													'standard_deduction' => array(
																			10 => array( //Single
																					'minimum' => 1500,
																					'maximum' => 2000,
																					'rate' => 0.15, //percent
																					),
																			20 => array( //Married Filing jointly
																					'minimum' => 2000,
																					'maximum' => 4000,
																					'rate' => 0.15, //percent
																					),
																			30 => array( //Married filing separately
																					'minimum' => 1500,
																					'maximum' => 2000,
																					'rate' => 0.15, //percent
																					),
																			40 => array( //Head of household
																					'minimum' => 2000,
																					'maximum' => 4000,
																					'rate' => 0.15, //percent
																					),
																			),
													'personal_deduction' => array(
																				10 => array( //Single
																							0 => array(100000, 3200),
																							1 => array(125000, 2400),
																							2 => array(150000, 1800),
																							3 => array(175000, 1200),
																							4 => array(200000, 1200),
																							5 => array(250000, 600),
																							6 => array(250000, 600),
																							),
																				20 => array( //Married filing joint
																							0 => array(100000, 3200),
																							1 => array(125000, 3200),
																							2 => array(150000, 3200),
																							3 => array(175000, 2400),
																							4 => array(200000, 1800),
																							5 => array(250000, 1200),
																							6 => array(250000, 600),
																							),
																				30 => array( //Married filing separately
																							0 => array(100000, 3200),
																							1 => array(125000, 2400),
																							2 => array(150000, 1800),
																							3 => array(175000, 1200),
																							4 => array(200000, 1200),
																							5 => array(250000, 600),
																							6 => array(250000, 600),
																							),
																				40 => array( //Head of household
																							0 => array(100000, 3200),
																							1 => array(125000, 3200),
																							2 => array(150000, 3200),
																							3 => array(175000, 2400),
																							4 => array(200000, 1800),
																							5 => array(250000, 1200),
																							6 => array(250000, 600),
																							),
																				),
																			),
								);

	function getStatePayPeriodDeductions() {
		return bcdiv($this->getStateTaxPayable(), $this->getAnnualPayPeriods() );
	}

	function getStateAnnualTaxableIncome() {
		$annual_income = $this->getAnnualTaxableIncome();
		//$federal_tax = $this->getFederalTaxPayable();
		$standard_deduction = $this->getStateStandardDeduction();
		$personal_deduction = $this->getStatePersonalDeduction();

		//Debug::text('Federal Annual Tax: '. $federal_tax, __FILE__, __LINE__, __METHOD__,10);
		Debug::text('Standard Deduction: '. $standard_deduction, __FILE__, __LINE__, __METHOD__,10);
		Debug::text('Personal Deduction: '. $personal_deduction, __FILE__, __LINE__, __METHOD__,10);

		$income = bcsub( bcsub( $annual_income, $standard_deduction ), $personal_deduction);

		Debug::text('State Annual Taxable Income: '. $income, __FILE__, __LINE__, __METHOD__,10);

		return $income;
	}

	function getStateTaxPayable() {
		$annual_income = $this->getStateAnnualTaxableIncome();

		$retval = 0;

		if ( $annual_income > 0 ) {
			$rate = $this->getData()->getStateRate($annual_income);
			$state_constant = $this->getData()->getStateConstant($annual_income);
			$state_rate_income = $this->getData()->getStateRatePreviousIncome($annual_income);

			Debug::text('Rate: '. $rate .' Constant: '. $state_constant .' Rate Income: '. $state_rate_income, __FILE__, __LINE__, __METHOD__,10);
			$retval = bcadd( bcmul( bcsub( $annual_income, $state_rate_income ), $rate ), $state_constant );
		}

		if ( $retval < 0 ) {
			$retval = 0;
		}

		Debug::text('State Annual Tax Payable: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	function getDataByIncome( $income, $arr ) {
		if ( !is_array($arr) ) {
			return FALSE;
		}

		$prev_value = 0;
		$total_rates = count($arr) - 1;
		$i=0;
		foreach( $arr as $key => $values ) {
			if ($this->getAnnualTaxableIncome() > $prev_value AND $this->getAnnualTaxableIncome() <= $values[0]) {
				return $values;
			} elseif ($i == $total_rates) {
				return $values;
			}
			$prev_value = $values[0];
			$i++;
		}

		return FALSE;
	}

	function getStateStandardDeduction() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;

		}

		$deduction_arr = $retarr['standard_deduction'][$this->getStateFilingStatus()];

		$retval = bcmul( $this->getAnnualTaxableIncome(), $deduction_arr['rate'] );

		if ( $retval < $deduction_arr['minimum']) {
			$retval = $deduction_arr['minimum'];
		}

		if ( $retval > $deduction_arr['maximum']) {
			$retval = $deduction_arr['maximum'];
		}

		Debug::text('State Standard Deduction Amount: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	function getStatePersonalDeduction() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;

		}

		$allowance_arr = $this->getDataByIncome( $this->getAnnualTaxableIncome(), $retarr['personal_deduction'][$this->getStateFilingStatus()] );

		$allowance = $allowance_arr[1];

		$retval = bcmul($allowance, $this->getUserValue2() );

		Debug::text('State Personal Deduction Amount: '. $retval .' Allowance: '. $this->getUserValue2(), __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

}
?>
