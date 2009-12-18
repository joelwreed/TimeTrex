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
 * $Id: Data.class.php 2449 2009-03-17 20:31:09Z ipso $
 * $Date: 2009-03-17 13:31:09 -0700 (Tue, 17 Mar 2009) $
 */

/*
Site with good Tax news on updates: http://www.sageabra.com/hr-news/
 - http://www.sageabra.com/hr-news/payroll_news.aspx?item={fe86cf98-8789-3d13-aabc-0dff4bf8cc13}
DONE-	Federal          		- Google: Notice 1036 http://www.irs.gov/pub/irs-pdf/n1036.pdf

**************************8- Kansas/Maryland/Puerto Rico tax table updates from May.

NOCHANGE-	'AL' => 'Alabama' 		- http://www.ador.state.al.us/Withholding/index.html
NOCHANGE-	'AZ' => 'Arizona',		- http://www.azdor.gov/Withholding/menu.htm
NOCHANGE-	'AR' => 'Arkansas'		- http://www.arkansas.gov/dfa/income_tax/tax_wh_forms.html
DONE-	'CA' => 'California' 	- http://www.edd.ca.gov/taxrep/taxrte9x.htm#Withhold
DONE- (no change)	'CO' => 'Colorado',		- http://www.revenue.state.co.us/taxstatutesregs/withholdingindex/WHtablesgeninfo.html
DONE-	'CT' => 'Connecticut'	- http://www.ct.gov/drs/cwp/view.asp?a=1479&q=269984
NOCHANGE-	'DE' => 'Delaware',		- http://www.state.de.us/revenue/services/Business_Tax/WH_FAQS.shtml
DONE-	'DC' => 'D.C.', 		- http://otr.cfo.dc.gov/otr/cwp/view.asp?a=1330&Q=593946
NOCHANGE-	'GA' => 'Georgia',		- http://www.dot.state.ga.us/topps/admin/7153-6.htm
NOCHANGE-	'HI' => 'Hawaii',		- http://www.state.hi.us/tax/a1_b1_5whhold.htm
DONE-	'ID' => 'Idaho',		- http://tax.idaho.gov/publications.htm
NOCHANGE-	'IL' => 'Illinois',		- http://www.revenue.state.il.us/Businesses/TaxInformation/Payroll/index.htm
NOCHANGE-	'IN' => 'Indiana',		- http://www.in.gov/dor/taxforms/s-wforms.html
NOCHANGE-	'IA' => 'Iowa',			- http://www.state.ia.us/tax/forms/withhold.html
NOCHANGE-	'KS' => 'Kansas',		- http://www.ksrevenue.org/forms-btwh.htm
DONE-	'KY' => 'Kentucky', 	- http://revenue.ky.gov/business/whtax.htm
NOCHANGE-until July	'LA' => 'Louisiana',	- http://www.revenue.louisiana.gov/sections/publications/tm.asp
DONE-	'ME' => 'Maine',		- http://www.state.me.us/revenue/forms/with/2009.htm -- Check each year.
DONE-	'MD' => 'Maryland',		- http://www.oregon.gov/DOR/BUS/withholding.shtml - http://business.marylandtaxes.com/taxinfo/withholding/default.asp
NOCHANGE-	'MA' => 'Massachusetts' - http://www.mass.gov/?pageID=dorterminal&L=3&L0=Home&L1=Businesses&L2=Current+Tax+Year+Information&sid=Ador&b=terminalcontent&f=dor_business_buspubs&csid=Ador
DONE-	'MI' => 'Michigan',		- http://www.michigan.gov/documents/treasury/446_2007_176836_7.pdf
DONE-	'MN' => 'Minnesota',	- http://www.taxes.state.mn.us/taxes/withholding/index.shtml
NOCHANGE-	'MS' => 'Mississippi',	- http://www.mstc.state.ms.us/taxareas/withhold/main.htm
NOCHANGE-	'MO' => 'Missouri',		- http://www.dor.mo.gov/tax/business/withhold/
NOCHANGE-	'MT' => 'Montana',		- http://mt.gov/revenue/formsandresources/forms.asp
NOCHANGE-	'NE' => 'Nebraska',		- http://www.revenue.state.ne.us/tax/current/current.htm#inc
DONE-	'NM' => 'New Mexico', 	- http://www.tax.state.nm.us/trd_pubs.htm
NOCHANGE-	'NJ' => 'New Jersey',	- http://www.state.nj.us/treasury/taxation/index.html?njit30.htm~mainFrame
NOCHANGE-	'NY' => 'New York',		- http://www.tax.state.ny.us/forms/withholding_cur_forms.htm
DONE- (just knocked off the last bracket of each status)	'NC' => 'North Carolina'- http://www.dornc.com/taxes/wh_tax/index.html
DONE-	'ND' => 'North Dakota', - http://www.nd.gov/tax/indwithhold/pubs/guide/index.html
DONE-	'OH' => 'Ohio',			- http://tax.ohio.gov/divisions/employer_withholding/index.stm
DONE-	'OK' => 'Oklahoma',		- http://www.tax.ok.gov/btforms.html
DONE-	'OR' => 'Oregon',		- http://www.oregon.gov/DOR/BUS/withholding.shtml
NOCHANGE-	'PA' => 'Pennsylvania', - http://www.revenue.state.pa.us/revenue/cwp/view.asp?A=2&Q=205810
DONE-	'RI' => 'Rhode Island', - http://www.tax.state.ri.us/
NOCHANGE-	'SC' => 'South Carolina'- http://www.sctax.org/Publications/default.htm
DONE-	'UT' => 'Utah',			- http://www.tax.utah.gov/forms/ (PUB-14)
DONE-	'VT' => 'Vermont',		- http://www.state.vt.us/tax/businesswithholding.shtml
NOCHANGE- 'VA' => 'Virginia',		- http://www.tax.virginia.gov/site.cfm?alias=WithholdingTax
NOCHANGE-	'WI' => 'Wisconsin',	- http://www.revenue.wi.gov/ise/with/index.html

	'AK' => 'Alaska',		- NO STATE TAXES
	'FL' => 'Florida',		- NO STATE TAXES
	'NV' => 'Nevada',		- NO STATE TAXES
	'NH' => 'New Hampshire' - NO STATE TAXES
	'SD' => 'South Dakota',	- NO STATE TAXES
	'TN' => 'Tennessee',	- NO STATE TAXES
	'TX' => 'Texas',		- NO STATE TAXES
	'WA' => 'Washington',	- NO STATE TAXES
	'WV' => 'West Virginia'	- NO STATE TAXES
	'WY' => 'Wyoming'		- NO STATE TAXES

*/

/**
 * @package PayrollDeduction
 */
class PayrollDeduction_US_Data extends PayrollDeduction_Base {
	var $db = NULL;
	var $income_tax_rates = array();
	var $table = 'income_tax_rate_us';
	var $country_primary_currency = 'USD';

	var $federal_allowance = array(
									1230796800 => 3650.00, //01-Jan-09
									1199174400 => 3500.00, //01-Jan-08
									1167638400 => 3400.00, //01-Jan-07
									1136102400 => 3300.00  //01-Jan-06
								);

	//http://www.ssa.gov/pressoffice/factsheets/colafacts2007.htm
	var $social_security_options = array(
									1230796800 => array( //2009a
														'maximum_earnings' => 106800,
														'rate' => 6.2,
														'maximum_contribution' => 6621.60
													),
									1199174400 => array( //2008a
														'maximum_earnings' => 102000,
														'rate' => 6.2,
														'maximum_contribution' => 6324.00
													),
									1167638400 => array( //2007a
														'maximum_earnings' => 97500,
														'rate' => 6.2,
														'maximum_contribution' => 6045.00
													),
									1136102400 => array( //2006a
														'maximum_earnings' => 94200,
														'rate' => 6.2,
														'maximum_contribution' => 5840.40
													)
								);

	var $federal_ui_options = array(
									1136102400 => array( //2006a
														'maximum_earnings' => 7000,
														'rate' => 6.2,
														'minimum_rate' => 0.8,
													)
								);

	var $medicare_options = array(
									1136102400 => 1.45 //2006
								);

	/*
		10 => 'Single or HOH',
		20 => 'Married Without Spouse Filing',
		30 => 'Married With Spouse Filing',

		Calculation type is:
			10 = Percent
			20 = Amount
			30 = Amount less Percent of wages in excess.

		Wage Base is the maximum wage that is eligible for EIC.

	*/
	var $eic_options = array(
								1238569200 => array( //01-Apr-09
													10 => array(
																array( 'income' => 8950,  'calculation_type' => 10, 'percent' => 20.40 ),
																array( 'income' => 16420, 'calculation_type' => 20, 'amount' => 1826 ),
																array( 'income' => 16420, 'calculation_type' => 30, 'amount' => 1826, 'percent' => 9.588  ),
																),
													20 => array(
																array( 'income' => 8950,  'calculation_type' => 10, 'percent' => 20.40 ),
																array( 'income' => 21420, 'calculation_type' => 20, 'amount' => 1826 ),
																array( 'income' => 21420, 'calculation_type' => 30, 'amount' => 1826, 'percent' => 9.588  ),
																),
													30 => array(
																array( 'income' => 4475, 'calculation_type' => 10, 'percent' => 20.40 ),
																array( 'income' => 10710, 'calculation_type' => 20, 'amount' => 913 ),
																array( 'income' => 10710, 'calculation_type' => 30, 'amount' => 913, 'percent' => 9.588  ),
																),
													),
								1199174400 => array( //01-Jan-08
													10 => array(
																array( 'income' => 8580,  'calculation_type' => 10, 'percent' => 20.40 ),
																array( 'income' => 15740, 'calculation_type' => 20, 'amount' => 1750 ),
																array( 'income' => 15740, 'calculation_type' => 30, 'amount' => 1750, 'percent' => 9.588  ),
																),
													20 => array(
																array( 'income' => 8580,  'calculation_type' => 10, 'percent' => 20.40 ),
																array( 'income' => 18740, 'calculation_type' => 20, 'amount' => 1750 ),
																array( 'income' => 18740, 'calculation_type' => 30, 'amount' => 1750, 'percent' => 9.588  ),
																),
													30 => array(
																array( 'income' => 4290, 'calculation_type' => 10, 'percent' => 20.40 ),
																array( 'income' => 9370, 'calculation_type' => 20, 'amount' => 875 ),
																array( 'income' => 9370, 'calculation_type' => 30, 'amount' => 875, 'percent' => 9.588  ),
																),
													),
							);

	function __construct() {
		global $db;

		$this->db = $db;

		return TRUE;
	}

	function getData() {
		global $cache;

		$country = $this->getCountry();
		$state = $this->getProvince();
		$district = $this->getDistrict();

		$epoch = $this->getDate();
		$federal_status = $this->getFederalFilingStatus();
		if ( $federal_status == '' ) {
			$federal_status = 10;
		}
		$state_status = $this->getStateFilingStatus();
		if ( $state_status == '' ) {
			$state_status = 10;
		}
		$district_status = $this->getDistrictFilingStatus();

		if ($epoch == NULL OR $epoch == ''){
			//$year = date('Y');
			$epoch = TTDate::getTime();
		}

		Debug::text('bUsing ('. $state .'/'. $district .') values from: '. TTDate::getDate('DATE+TIME', $epoch) , __FILE__, __LINE__, __METHOD__,10);

		$cache_id = $country.$state.$district.$epoch.$federal_status.$state_status.$district_status;

		if ( is_string( $cache->get($cache_id, $this->table ) ) ) {
			$this->income_tax_rates = unserialize( $cache->get($cache_id, $this->table ) );
		} else {
			$this->income_tax_rates = FALSE;
		}


		if ( $this->income_tax_rates === FALSE ) {
			//There were issues with this query when provincial taxes were updated but not federal
			//We need to basically make a union query that queries the latest federal taxes separate
			//from the provincial
			$query = 'select country,state,district,status,income,rate,constant,effective_date
						from '. $this->table .'
						where
								(
								effective_date = ( 	select effective_date
													from '. $this->table .'
													where effective_date <= '. $epoch .'
														AND country = '. $this->db->qstr($country).'
														AND state is NULL
														AND ( status = 0
															OR status = '. $federal_status .' )
													ORDER BY effective_date DESC
													LIMIT 1) )
							AND
							( ( country = '. $this->db->qstr($country).'
									and state is NULL
									and ( status = 0 OR status = '. $federal_status .') ) )
							OR
								(
								effective_date = ( 	select effective_date
													from '. $this->table .'
													where effective_date <= '. $epoch .'
														AND country = '. $this->db->qstr($country).'
														AND state = '. $this->db->qstr($state) .'
														AND ( status = 0
															OR status = '. $state_status .' )
													ORDER BY effective_date DESC
													LIMIT 1) )
							AND
							( country = '. $this->db->qstr($country).'
									and state = '. $this->db->qstr($state) .'
									and district is NULL
									and ( status = 0 OR status = '. $state_status .') )
							OR
								(
								effective_date = ( 	select effective_date
													from '. $this->table .'
													where effective_date <= '. $epoch .'
														AND country = '. $this->db->qstr($country).'
														AND state = '. $this->db->qstr($state) .'
														AND district = '. $this->db->qstr($district) .'
														AND ( status = 0
															OR status = '. $district_status .' )
													ORDER BY effective_date DESC
													LIMIT 1) )
							AND
							( country = '. $this->db->qstr($country).'
									and state = '. $this->db->qstr($state) .'
									and district = '. $this->db->qstr($district) .'
									and ( status = 0 OR status = '. $district_status .') )
						ORDER BY state desc, district desc, income asc, rate asc';

			//Debug::text('Query: '. $query , __FILE__, __LINE__, __METHOD__,10);
			try {
				$rs = $this->db->Execute($query);
			} catch (Exception $e) {
				throw new DBError($e);
			}

			$rows = $rs->GetRows();

			$prev_income = 0;
			$prev_rate = 0;
			$prev_constant = 0;
			foreach($rows as $key => $arr) {
				if ( $arr['district'] != NULL ) {
					$type = 'district';
				} elseif ( $arr['state'] != NULL ) {
					$type = 'state';
				} else {
					$type = 'federal';
				}

				$this->income_tax_rates[$type][] = array(	'prev_income' => trim($prev_income),
															'income' => trim($arr['income']),
															'prev_rate' => ( bcdiv( trim($prev_rate), 100 ) ),
															'rate' => ( bcdiv( trim($arr['rate']), 100 ) ),
															'prev_constant' => trim($prev_constant),
															'constant' => trim($arr['constant']) );

				$prev_income = $arr['income'];
				$prev_rate = $arr['rate'];
				$prev_constant = $arr['constant'];
			}

			if ( isset($this->income_tax_rates) ) {
				foreach( $this->income_tax_rates as $type => $brackets ) {
					$i=0;
					$total_brackets = count($brackets)-1;
					foreach( $brackets as $key => $bracket_data ) {
						if ( $i == 0 ) {
							$first = TRUE;
						} else {
							$first = FALSE;
						}

						if ( $i == $total_brackets ) {
							$last = TRUE;
						} else {
							$last = FALSE;
						}

						$this->income_tax_rates[$type][$key]['first'] = $first;
						$this->income_tax_rates[$type][$key]['last'] = $last;

						$i++;
					}
				}
			}

			if ( isset($arr) ) {
				Debug::text('bUsing values from: '. TTDate::getDate('DATE+TIME', $arr['effective_date']) , __FILE__, __LINE__, __METHOD__,10);
			}

			//var_dump($this->income_tax_rates);
			$cache->save(serialize($this->income_tax_rates), $cache_id, $this->table );
		}

		return $this;
	}

	function getRateArray($income, $type) {
		Debug::text('Calculating '. $type .' Taxes on: $'. $income, __FILE__, __LINE__, __METHOD__,10);

		if ( isset($this->income_tax_rates[$type]) ) {
			$rates = $this->income_tax_rates[$type];
		} else {
			Debug::text('aNO INCOME TAX RATES FOUND!!!!!! '. $type .' Taxes on: $'. $income, __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( count($rates) == 0 ) {
			Debug::text('bNO INCOME TAX RATES FOUND!!!!!! '. $type .' Taxes on: $'. $income, __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		$prev_value = 0;
		$total_rates = count($rates) - 1;
		$i=0;
		foreach ($rates as $key => $values) {
			$value = $values['income'];
			$rate = $values['rate'];
			$constant = $values['constant'];

			//Debug::text('Key: '. $key .' Value: '. $value .' Rate: '. $rate .' Constant: '. $constant .' Previous Value: '. $prev_value , __FILE__, __LINE__, __METHOD__,10);

			if ($income > $prev_value AND $income <= $value) {
				//Debug::text('Found Key: '. $key, __FILE__, __LINE__, __METHOD__,10);

				return $this->income_tax_rates[$type][$key];
			} elseif ($i == $total_rates) {
				//Debug::text('Found Last Key: '. $key, __FILE__, __LINE__, __METHOD__,10);
				return $this->income_tax_rates[$type][$key];
			}

			$prev_value = $value;
			$i++;
		}

		return FALSE;
	}

	function getEICRateArray( $income, $type ) {
		Debug::text('Calculating '. $type .' Taxes on: $'. $income, __FILE__, __LINE__, __METHOD__,10);

		$eic_options = $this->getDataFromRateArray( $this->getDate(), $this->eic_options);
		if ( $eic_options == FALSE ) {
			Debug::text('aNO INCOME TAX RATES FOUND!!!!!! '. $type .' Taxes on: $'. $income, __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( isset($eic_options[$type]) ) {
			$rates = $eic_options[$type];
		} else {
			Debug::text('bNO INCOME TAX RATES FOUND!!!!!! '. $type .' Taxes on: $'. $income, __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( count($rates) == 0 ) {
			Debug::text('cNO INCOME TAX RATES FOUND!!!!!! '. $type .' Taxes on: $'. $income, __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		$prev_value = 0;
		$total_rates = count($rates) - 1;
		$i=0;
		foreach ($rates as $key => $values) {
			$value = $values['income'];

			//Debug::text('Key: '. $key .' Income: '. $value , __FILE__, __LINE__, __METHOD__,10);

			if ($income > $prev_value AND $income <= $value) {
				//Debug::text('Found Key: '. $key, __FILE__, __LINE__, __METHOD__,10);
				return $eic_options[$type][$key];
			} elseif ($i == $total_rates) {
				//Debug::text('Found Last Key: '. $key, __FILE__, __LINE__, __METHOD__,10);
				return $eic_options[$type][$key];
			}

			$prev_value = $value;
			$i++;
		}

		return FALSE;

	}

	function getFederalRate($income) {
		$arr = $this->getRateArray($income, 'federal');
		Debug::text('Federal Rate: '. $arr['rate'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['rate'];
	}

	function getFederalPreviousRate($income) {
		$arr = $this->getRateArray($income, 'federal');
		Debug::text('Federal Previous Rate: '. $arr['prev_rate'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['prev_rate'];
	}

	function getFederalRatePreviousIncome($income) {
		$arr = $this->getRateArray($income, 'federal');
		Debug::text('Federal Rate Previous Income: '. $arr['prev_income'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['prev_income'];
	}

	function getFederalRateIncome($income) {
		$arr = $this->getRateArray($income, 'federal');
		Debug::text('Federal Rate Income: '. $arr['income'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['income'];
	}

	function getFederalConstant($income) {
		$arr = $this->getRateArray($income, 'federal');
		Debug::text('Federal Constant: '. $arr['constant'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['constant'];
	}

	function getFederalAllowanceAmount($date) {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->federal_allowance);
		if ( $retarr != FALSE ) {
			return $retarr;
		}

		return FALSE;
	}


	function getStateRate($income) {
		$arr = $this->getRateArray($income, 'state');
		Debug::text('State Rate: '. $arr['rate'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['rate'];
	}

	function getStatePreviousRate($income) {
		$arr = $this->getRateArray($income, 'state');
		Debug::text('State Previous Rate: '. $arr['prev_rate'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['prev_rate'];
	}

	function getStateRatePreviousIncome($income) {
		$arr = $this->getRateArray($income, 'state');
		Debug::text('State Rate Previous Income: '. $arr['prev_income'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['prev_income'];
	}

	function getStateRateIncome($income) {
		$arr = $this->getRateArray($income, 'state');
		Debug::text('State Rate Income: '. $arr['income'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['income'];
	}

	function getStateConstant($income) {
		$arr = $this->getRateArray($income, 'state');
		Debug::text('State Constant: '. $arr['constant'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['constant'];
	}

	function getStatePreviousConstant($income) {
		$arr = $this->getRateArray($income, 'state');
		Debug::text('State Previous Constant: '. $arr['prev_constant'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['prev_constant'];
	}

	function getDistrictRate($income) {
		$arr = $this->getRateArray($income, 'district');
		Debug::text('District Rate: '. $arr['rate'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['rate'];
	}

	function getDistrictRatePreviousIncome($income) {
		$arr = $this->getRateArray($income, 'district');
		Debug::text('District Rate Previous Income: '. $arr['prev_income'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['prev_income'];
	}

	function getDistrictRateIncome($income) {
		$arr = $this->getRateArray($income, 'district');
		Debug::text('District Rate Income: '. $arr['income'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['income'];
	}

	function getDistrictConstant($income) {
		$arr = $this->getRateArray($income, 'district');
		Debug::text('District Constant: '. $arr['constant'], __FILE__, __LINE__, __METHOD__,10);
		return $arr['constant'];
	}

	//Social Security
	function getSocialSecurityMaximumContribution() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->social_security_options);
		if ( $retarr != FALSE ) {
			return $retarr['maximum_contribution'];
		}

		return FALSE;
	}

	function getSocialSecurityRate() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->social_security_options);
		if ( $retarr != FALSE ) {
			return $retarr['rate'];
		}

		return FALSE;
	}


	//Medicare
	function getMedicareRate() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->medicare_options);
		if ( $retarr != FALSE ) {
			return $retarr;
		}

		return FALSE;
	}


	//Federal UI
	function getFederalUIRate() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->federal_ui_options);
		if ( $retarr != FALSE ) {
			if ( $this->getStateUIRate() > bcsub( $retarr['rate'], $this->getFederalUIMinimumRate() ) ) {
				$retval = $this->getFederalUIMinimumRate();
			} else {
				$retval = $retarr['rate'] - $this->getStateUIRate();
			}

			return $retval;
		}

		return FALSE;
	}

	function getFederalUIMinimumRate() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->federal_ui_options);
		if ( $retarr != FALSE ) {
			return $retarr['minimum_rate'];
		}

		return FALSE;
	}

	function getFederalUIMaximumEarnings() {
		$retarr = $this->getDataFromRateArray($this->getDate(), $this->federal_ui_options);
		if ( $retarr != FALSE ) {
			return $retarr['maximum_earnings'];
		}

		return FALSE;
	}

	function getFederalUIMaximumContribution() {
		$retval = bcmul( $this->getFederalUIMaximumEarnings() ,bcdiv( $this->getFederalUIRate(), 100 ) );
		if ( $retval != FALSE ) {
			return $retval;
		}

		return FALSE;
	}
}
?>
