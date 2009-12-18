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
 * $Id: LA.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package PayrollDeduction
 */
class PayrollDeduction_US_LA extends PayrollDeduction_US {

	var $state_options = array(
								1136102400 => array(
													'rate1' => 2.1,
													'rate2' => 1.35,

													'm1' => array(
																	0 => 12500,
																	1 => 12500,
																	2 => 25000
																	),
													'm2' => array(
																	0 => 25000,
																	1 => 25000,
																	2 => 50000
																	),

													'allowance' => 4500,
													'dependant_allowance' => 1000,
													)
								);

	function getStatePayPeriodDeductions() {
		return $this->getStateTaxPayable();
	}

	function getStateAnnualTaxableIncome() {
		$annual_income = $this->getAnnualTaxableIncome();
		$income = $annual_income;

		Debug::text('State Annual Taxable Income: '. $income, __FILE__, __LINE__, __METHOD__,10);

		return $income;
	}

	function getStateAllowanceAmount() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;
		}

		$allowance_arr = $retarr['allowance'];

		$retval = bcmul( $this->getUserValue1(), $allowance_arr );

		Debug::text('State Allowance Amount: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	function getStateDependantAllowanceAmount() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;
		}

		$allowance_arr = $retarr['dependant_allowance'];

		$retval = bcmul( $this->getUserValue2(), $allowance_arr );

		Debug::text('State Dependant Allowance Amount: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	private function getPersonalExemptions() {
		$personal_exemptions = (int)$this->getUserValue1();
		if ( $personal_exemptions > 2 ) {
			$personal_exemptions = 2;
		}

		return $personal_exemptions;
	}

	private function getM1() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;
		}

		$retval = $retarr['m1'][$this->getPersonalExemptions()];

		Debug::text('M1: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	private function getM2() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $retarr == FALSE ) {
			return FALSE;
		}

		$retval = $retarr['m2'][$this->getPersonalExemptions()];

		Debug::text('M2: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	function getStateTaxPayable() {
		$state_options = $this->getDataFromRateArray($this->getDate(), $this->state_options);
		if ( $state_options == FALSE ) {
			return FALSE;
		}

		$rate1 = bcdiv( $state_options['rate1'], 100);
		$rate2 = bcdiv( $state_options['rate2'], 100);

		$pay_period_income = $this->getGrossPayPeriodIncome();

		$a = bcmul( $pay_period_income, bcdiv( $state_options['rate1'], 100) );

		if ( $pay_period_income > bcdiv( $this->getM1(), $this->getAnnualPayPeriods() ) ) {
			$b = bcmul( $rate2, bcsub( $pay_period_income, bcdiv( $this->getM1(), $this->getAnnualPayPeriods() ) ) );
		} else {
			$b = 0;
		}

		if ( $pay_period_income > bcdiv( $this->getM2(), $this->getAnnualPayPeriods() ) ) {
			$c = bcmul( $rate2, bcsub( $pay_period_income, bcdiv( $this->getM2(), $this->getAnnualPayPeriods() ) ) );
		} else {
			$c = 0;
		}

		$personal_exemptions = bcmul($this->getPersonalExemptions(), $state_options['allowance']);
		$dependant_exemptions = bcmul($this->getUserValue2(), $state_options['dependant_allowance']);
		$d = bcmul( $rate1, bcdiv( bcadd( $personal_exemptions, $dependant_exemptions), $this->getAnnualPayPeriods() ) );

		if ( bcadd( $personal_exemptions, $dependant_exemptions) > $this->getM1() ) {
			$e = bcmul( $rate2, bcdiv( bcsub( bcadd( $personal_exemptions, $dependant_exemptions), $this->getM1() ), $this->getAnnualPayPeriods() ) );
		} else {
			$e = 0;
		}

		Debug::text('A: '. $a .' B: '. $b .' C: '. $c .' D: '. $d .' E: '. $e, __FILE__, __LINE__, __METHOD__,10);
		$retval = bcsub( bcadd( bcadd($a, $b), $c),  bcadd($d, $e) );

		if ( $retval <= 0 ) {
			$retval = 0;
		}

		Debug::text('State Annual Tax Payable: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}
}
?>
