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
 * $Id: PayStubCalculationTest.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */
require_once('PHPUnit/Framework/TestCase.php');

class PayStubCalculationTest extends PHPUnit_Framework_TestCase {

	protected $company_id = NULL;
	protected $user_id = NULL;
	protected $pay_period_schedule_id = NULL;
	protected $pay_period_objs = NULL;
	protected $pay_stub_account_link_arr = NULL;

    public function __construct() {
        global $db, $cache, $profiler;

        require_once('../includes/global.inc.php');

        $profiler = new Profiler( true );
        Debug::setBufferOutput(FALSE);
        Debug::setEnable(TRUE);

        if ( PRODUCTION != FALSE ) {
            echo "DO NOT RUN ON A PRODUCTION SERVER<br>\n";
            exit;
        }
    }

    public function setUp() {
		global $dd;
        Debug::text('Running setUp(): ', __FILE__, __LINE__, __METHOD__,10);

		$dd = new DemoData();
		$dd->setUserNamePostFix( rand(1000,99999) );
		$this->company_id = $dd->createCompany();
		Debug::text('Company ID: '. $this->company_id, __FILE__, __LINE__, __METHOD__,10);

		$dd->createCurrency( $this->company_id, 10 );

		$dd->createPermissionGroups( $this->company_id );

		$dd->createPayStubAccount( $this->company_id );
		$this->createPayStubAccounts();
		$this->createPayStubAccrualAccount();
		$dd->createPayStubAccountLink( $this->company_id );
		$this->getPayStubAccountLinkArray();

		//Company Deductions
		$dd->createCompanyDeduction( $this->company_id );
		$this->createCompanyDeductions();

		$dd->createUserWageGroups( $this->company_id );

		$this->user_id = $dd->createUser( $this->company_id, 100 );

		$this->createPayPeriodSchedule();
		$this->createPayPeriods();
		$this->getAllPayPeriods();

		//Create policies
		$policy_ids['overtime'][] = $dd->createOverTimePolicy( $this->company_id, 10 );
		//$policy_ids['overtime'][] = $dd->createOverTimePolicy( $this->company_id, 20, $policy_ids['accrual'][0] );
		$policy_ids['overtime'][] = $dd->createOverTimePolicy( $this->company_id, 20 );

		$policy_ids['premium'][] = $dd->createPremiumPolicy( $this->company_id, 10 );
		$policy_ids['premium'][] = $dd->createPremiumPolicy( $this->company_id, 20 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									$policy_ids['overtime'],
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );


		$this->createPunchData();

		$this->addPayStubAmendments();

		$this->createPayStub();

        return TRUE;
    }

    public function tearDown() {
        Debug::text('Running tearDown(): ', __FILE__, __LINE__, __METHOD__,10);

		//$this->deleteAllSchedules();

        return TRUE;
    }

	function getPayStubAccountLinkArray() {
		$this->pay_stub_account_link_arr = array(
			'total_gross' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName( $this->company_id, 40, 'Total Gross'),
			'total_deductions' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName( $this->company_id, 40, 'Total Deductions'),
			'employer_contribution' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 40, 'Employer Total Contributions'),
			'net_pay' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 40, 'Net Pay'),
			'regular_time' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Regular Time'),
			);

		return TRUE;
	}

	function createPayStubAccounts() {
		Debug::text('Saving.... Employee Deduction - Other', __FILE__, __LINE__, __METHOD__, 10);
		$pseaf = new PayStubEntryAccountFactory();
		$pseaf->setCompany( $this->company_id );
		$pseaf->setStatus(10);
		$pseaf->setType(20);
		$pseaf->setName('Other');
		$pseaf->setOrder(290);

		if ( $pseaf->isValid() ) {
			$pseaf->Save();
		}

		Debug::text('Saving.... Employee Deduction - Other2', __FILE__, __LINE__, __METHOD__, 10);
		$pseaf = new PayStubEntryAccountFactory();
		$pseaf->setCompany( $this->company_id );
		$pseaf->setStatus(10);
		$pseaf->setType(20);
		$pseaf->setName('Other2');
		$pseaf->setOrder(291);

		if ( $pseaf->isValid() ) {
			$pseaf->Save();
		}

		Debug::text('Saving.... Employee Deduction - Advanced Percent 1', __FILE__, __LINE__, __METHOD__, 10);
		$pseaf = new PayStubEntryAccountFactory();
		$pseaf->setCompany( $this->company_id );
		$pseaf->setStatus(10);
		$pseaf->setType(20);
		$pseaf->setName('Advanced Percent 1');
		$pseaf->setOrder(291);

		if ( $pseaf->isValid() ) {
			$pseaf->Save();
		}
		Debug::text('Saving.... Employee Deduction - Advanced Percent 2', __FILE__, __LINE__, __METHOD__, 10);
		$pseaf = new PayStubEntryAccountFactory();
		$pseaf->setCompany( $this->company_id );
		$pseaf->setStatus(10);
		$pseaf->setType(20);
		$pseaf->setName('Advanced Percent 2');
		$pseaf->setOrder(291);

		if ( $pseaf->isValid() ) {
			$pseaf->Save();
		}

		Debug::text('Saving.... Employee Deduction - EI', __FILE__, __LINE__, __METHOD__, 10);
		$pseaf = new PayStubEntryAccountFactory();
		$pseaf->setCompany( $this->company_id );
		$pseaf->setStatus(10);
		$pseaf->setType(20);
		$pseaf->setName('EI');
		$pseaf->setOrder(292);

		if ( $pseaf->isValid() ) {
			$pseaf->Save();
		}

		Debug::text('Saving.... Employee Deduction - CPP', __FILE__, __LINE__, __METHOD__, 10);
		$pseaf = new PayStubEntryAccountFactory();
		$pseaf->setCompany( $this->company_id );
		$pseaf->setStatus(10);
		$pseaf->setType(20);
		$pseaf->setName('CPP');
		$pseaf->setOrder(293);

		if ( $pseaf->isValid() ) {
			$pseaf->Save();
		}

		//Link Account EI and CPP accounts
		$pseallf = new PayStubEntryAccountLinkListFactory();
		$pseallf->getByCompanyId( $this->company_id );
		if ( $pseallf->getRecordCount() > 0 ) {
			$pseal_obj = $pseallf->getCurrent();
			$pseal_obj->setEmployeeEI( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'EI') );
			$pseal_obj->setEmployeeCPP( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'CPP') );
			$pseal_obj->Save();
		}


		return TRUE;
	}

	function createCompanyDeductions() {

		//Test Wage Base amount
		$cdf = new CompanyDeductionFactory();
		$cdf->setCompany( $this->company_id );
		$cdf->setStatus( 10 ); //Enabled
		$cdf->setType( 10 ); //Tax
		$cdf->setName( 'Union Dues' );
		$cdf->setCalculation( 15 );
		$cdf->setCalculationOrder( 90 );
		$cdf->setPayStubEntryAccount( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Union Dues') );
		$cdf->setUserValue1( 1 ); //10%
		$cdf->setUserValue2( 3000 );

		if ( $cdf->isValid() ) {
			$cdf->Save(FALSE);

			$cdf->setIncludePayStubEntryAccount( array( $this->pay_stub_account_link_arr['total_gross'] ) );

			if ( $cdf->isValid() ) {
				$cdf->Save();
			}
		}

		//Test Wage Exempt Amount
		$cdf = new CompanyDeductionFactory();
		$cdf->setCompany( $this->company_id );
		$cdf->setStatus( 10 ); //Enabled
		$cdf->setType( 10 ); //Tax
		$cdf->setName( 'Union Dues2' );
		$cdf->setCalculation( 15 );
		$cdf->setCalculationOrder( 90 );
		$cdf->setPayStubEntryAccount( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Other') );
		$cdf->setUserValue1( 10 ); //10%
		//$cdf->setUserValue2( 0 );
		$cdf->setUserValue3( 78000 ); //Annual

		if ( $cdf->isValid() ) {
			$cdf->Save(FALSE);

			$cdf->setIncludePayStubEntryAccount( array( $this->pay_stub_account_link_arr['total_gross'] ) );

			if ( $cdf->isValid() ) {
				$cdf->Save();
			}
		}

		//Test Advanced Percent Calculation maximum amount.
		$cdf = new CompanyDeductionFactory();
		$cdf->setCompany( $this->company_id );
		$cdf->setStatus( 10 ); //Enabled
		$cdf->setType( 10 ); //Tax
		$cdf->setName( 'Test Advanced Percent 1' );
		$cdf->setCalculation( 15 );
		$cdf->setCalculationOrder( 90 );
		$cdf->setPayStubEntryAccount( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Advanced Percent 1') );
		$cdf->setUserValue1( 1 ); //1%
		$cdf->setUserValue2( 2000 ); //Wage Base

		if ( $cdf->isValid() ) {
			$cdf->Save(FALSE);

			$cdf->setIncludePayStubEntryAccount( array( $this->pay_stub_account_link_arr['regular_time'] ) );

			if ( $cdf->isValid() ) {
				$cdf->Save();
			}
		}
		//Test Advanced Percent Calculation maximum amount.
		$cdf = new CompanyDeductionFactory();
		$cdf->setCompany( $this->company_id );
		$cdf->setStatus( 10 ); //Enabled
		$cdf->setType( 10 ); //Tax
		$cdf->setName( 'Test Advanced Percent 2' );
		$cdf->setCalculation( 15 );
		$cdf->setCalculationOrder( 90 );
		$cdf->setPayStubEntryAccount( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Advanced Percent 2') );
		$cdf->setUserValue1( 1 ); //1%
		$cdf->setUserValue2( 2500 ); //Wage Base

		if ( $cdf->isValid() ) {
			$cdf->Save(FALSE);

			$cdf->setIncludePayStubEntryAccount( array( $this->pay_stub_account_link_arr['regular_time'] ) );

			if ( $cdf->isValid() ) {
				$cdf->Save();
			}
		}

		$cdf = new CompanyDeductionFactory();
		$cdf->setCompany( $this->company_id );
		$cdf->setStatus( 10 ); //Enabled
		$cdf->setType( 10 ); //Tax
		$cdf->setName( 'EI - Employee' );
		$cdf->setCalculation( 91 ); //EI Formula
		$cdf->setCalculationOrder( 90 );
		$cdf->setPayStubEntryAccount( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'EI') );

		if ( $cdf->isValid() ) {
			$cdf->Save(FALSE);

			$cdf->setIncludePayStubEntryAccount( array( $this->pay_stub_account_link_arr['total_gross'] ) );

			if ( $cdf->isValid() ) {
				$cdf->Save();
			}
		}

		$cdf = new CompanyDeductionFactory();
		$cdf->setCompany( $this->company_id );
		$cdf->setStatus( 10 ); //Enabled
		$cdf->setType( 10 ); //Tax
		$cdf->setName( 'CPP - Employee' );
		$cdf->setCalculation( 90 ); //CPP Formula
		$cdf->setCalculationOrder( 91 );
		$cdf->setPayStubEntryAccount( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'CPP') );

		if ( $cdf->isValid() ) {
			$cdf->Save(FALSE);

			$cdf->setIncludePayStubEntryAccount( array( $this->pay_stub_account_link_arr['total_gross'] ) );

			if ( $cdf->isValid() ) {
				$cdf->Save();
			}
		}

		return TRUE;
	}

	function createPayPeriodSchedule() {
		$ppsf = new PayPeriodScheduleFactory();

		$ppsf->setCompany( $this->company_id );
		//$ppsf->setName( 'Bi-Weekly'.rand(1000,9999) );
		$ppsf->setName( 'Bi-Weekly' );
		$ppsf->setDescription( 'Pay every two weeks' );
		$ppsf->setType( 20 );
		$ppsf->setStartWeekDay( 0 );


		$anchor_date = TTDate::getBeginWeekEpoch( TTDate::getBeginYearEpoch() ); //Start 6 weeks ago

		$ppsf->setAnchorDate( $anchor_date );

		$ppsf->setStartDayOfWeek( TTDate::getDayOfWeek( $anchor_date ) );
		$ppsf->setTransactionDate( 7 );

		$ppsf->setTransactionDateBusinessDay( TRUE );

/*
		$ppsf->setPrimaryDate( ($anchor_date+(86400*14)) );
		$ppsf->setPrimaryDateLastDayOfMonth( FALSE );
		$ppsf->setPrimaryTransactionDate( ($anchor_date+(86400*21)) );
		$ppsf->setPrimaryTransactionDateLastDayOfMonth( FALSE );
		$ppsf->setPrimaryTransactionDateBusinessDay( FALSE );

		$ppsf->setSecondaryDate( ($anchor_date+(86400*28)) );
		$ppsf->setSecondaryDateLastDayOfMonth( FALSE );
		$ppsf->setSecondaryTransactionDate( ($anchor_date+(86400*35))  );
		$ppsf->setSecondaryTransactionDateLastDayOfMonth( FALSE );
		$ppsf->setSecondaryTransactionDateBusinessDay( FALSE );
*/
		$ppsf->setDayStartTime( 0 );
		$ppsf->setNewDayTriggerTime( (4*3600) );
		$ppsf->setMaximumShiftTime( (16*3600) );

		$ppsf->setEnableInitialPayPeriods( FALSE );
		if ( $ppsf->isValid() ) {
			$insert_id = $ppsf->Save(FALSE);
			Debug::Text('Pay Period Schedule ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			$ppsf->setUser( array($this->user_id) );
			$ppsf->Save();

			$this->pay_period_schedule_id = $insert_id;

			return $insert_id;
		}

		Debug::Text('Failed Creating Pay Period Schedule!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;

	}

	function createPayPeriods() {
		$max_pay_periods = 29;

		$ppslf = new PayPeriodScheduleListFactory();
		$ppslf->getById( $this->pay_period_schedule_id );
		if ( $ppslf->getRecordCount() > 0 ) {
			$pps_obj = $ppslf->getCurrent();


			for ( $i = 0; $i < $max_pay_periods; $i++ ) {
				if ( $i == 0 ) {
					$end_date = TTDate::getBeginYearEpoch( strtotime('01-Jan-06') );
				} else {
					$end_date = $end_date + ( (86400*14) );
				}

				Debug::Text('I: '. $i .' End Date: '. TTDate::getDate('DATE+TIME', $end_date) , __FILE__, __LINE__, __METHOD__,10);


				$pps_obj->createNextPayPeriod( $end_date , (86400*360) );
			}

		}

		return TRUE;
	}

	function getAllPayPeriods() {
		$pplf = new PayPeriodListFactory();
		//$pplf->getByCompanyId( $this->company_id );
		$pplf->getByPayPeriodScheduleId( $this->pay_period_schedule_id );
		if ( $pplf->getRecordCount() > 0 ) {
			foreach( $pplf as $pp_obj ) {
				Debug::text('Pay Period... Start: '. TTDate::getDate('DATE+TIME', $pp_obj->getStartDate() ) .' End: '. TTDate::getDate('DATE+TIME', $pp_obj->getEndDate() ), __FILE__, __LINE__, __METHOD__, 10);

				$this->pay_period_objs[] = $pp_obj;
			}
		}

		$this->pay_period_objs = array_reverse( $this->pay_period_objs );

		return TRUE;
	}

	function createPayStubAccrualAccount() {
		Debug::text('Saving.... Vacation Accrual', __FILE__, __LINE__, __METHOD__, 10);
		$pseaf = new PayStubEntryAccountFactory();
		$pseaf->setCompany( $this->company_id );
		$pseaf->setStatus(10);
		$pseaf->setType(50);
		$pseaf->setName('Vacation Accrual');
		$pseaf->setOrder(400);

		if ( $pseaf->isValid() ) {
			$vacation_accrual_id = $pseaf->Save();

			Debug::text('Saving.... Earnings - Vacation Accrual Release', __FILE__, __LINE__, __METHOD__, 10);
			$pseaf = new PayStubEntryAccountFactory();
			$pseaf->setCompany( $this->company_id );
			$pseaf->setStatus(10);
			$pseaf->setType(10);
			$pseaf->setName('Vacation Accrual Release');
			$pseaf->setOrder(180);
			$pseaf->setAccrual($vacation_accrual_id);

			if ( $pseaf->isValid() ) {
				$pseaf->Save();
			}

			//unset($vaction_accrual_id);

			//Don't need this because we are doing it manually.
			Debug::text('Saving.... Vacation Accrual Deduction', __FILE__, __LINE__, __METHOD__, 10);
			$cdf = new CompanyDeductionFactory();
			$cdf->setCompany( $this->company_id );
			$cdf->setStatus( 10 ); //Enabled
			$cdf->setType( 20 ); //Deduction
			$cdf->setName( 'Vacation Accrual' );
			$cdf->setCalculation( 10 );
			$cdf->setCalculationOrder( 50 );
			$cdf->setPayStubEntryAccount( $vacation_accrual_id );
			$cdf->setUserValue1( 4 );

			if ( $cdf->isValid() ) {
				Debug::text('bSaving.... Vacation Accrual Deduction', __FILE__, __LINE__, __METHOD__, 10);
				$cdf->Save(FALSE);

				$cdf->setIncludePayStubEntryAccount( array( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 40, 'Total Gross') ) );

				if ( $cdf->isValid() ) {
					$cdf->Save();
				}
			}


		}

		return TRUE;
	}


	function getPayStubEntryArray( $pay_stub_id ) {
		//Check Pay Stub to make sure it was created correctly.
		$pself = new PayStubEntryListFactory();
		$pself->getByPayStubId( $pay_stub_id ) ;
		if ( $pself->getRecordCount() > 0 ) {
			foreach( $pself as $pse_obj ) {
				$ps_entry_arr[$pse_obj->getPayStubEntryNameId()][] = array(
					'rate' => $pse_obj->getRate(),
					'units' => $pse_obj->getUnits(),
					'amount' => $pse_obj->getAmount(),
					'ytd_amount' => $pse_obj->getYTDAmount(),
					);
			}
		}

		if ( isset( $ps_entry_arr ) ) {
			return $ps_entry_arr;
		}

		return FALSE;
	}

	function createPunchData() {
		global $dd;

		$punch_date = $this->pay_period_objs[0]->getStartDate();
		$end_punch_date = $this->pay_period_objs[0]->getEndDate();
		$i=0;
		while ( $punch_date <= $end_punch_date ) {
			$date_stamp = TTDate::getDate('DATE', $punch_date );

			//$punch_full_time_stamp = strtotime($pc_data['date_stamp'].' '.$pc_data['time_stamp']);
			$dd->createPunchPair( 	$this->user_id,
										strtotime($date_stamp.' 08:00AM'),
										strtotime($date_stamp.' 11:00AM'),
										array(
												'in_type_id' => 10,
												'out_type_id' => 10,
												'branch_id' => 0,
												'department_id' => 0,
												'job_id' => 0,
												'job_item_id' => 0,
											)
									);
			$dd->createPunchPair( 	$this->user_id,
										strtotime($date_stamp.' 11:00AM'),
										strtotime($date_stamp.' 1:00PM'),
										array(
												'in_type_id' => 10,
												'out_type_id' => 20,
												'branch_id' => 0,
												'department_id' => 0,
												'job_id' => 0,
												'job_item_id' => 0,
											)
									);

			$dd->createPunchPair( 	$this->user_id,
										strtotime($date_stamp.' 2:00PM'),
										strtotime($date_stamp.' 6:00PM'),
										array(
												'in_type_id' => 20,
												'out_type_id' => 10,
												'branch_id' => 0,
												'department_id' => 0,
												'job_id' => 0,
												'job_item_id' => 0,
											)
									);

			$punch_date+=86400;
			$i++;
		}
		unset($punch_options_arr, $punch_date, $user_id);

	}

	function addPayStubAmendments() {
		//Regular FIXED PS amendment
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Bonus') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 10 );
		$psaf->setRate( 10 );
		$psaf->setUnits( 10 );

		$psaf->setDescription('Test Fixed PS Amendment');

		$psaf->setEffectiveDate( $this->pay_period_objs[0]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		//Regular percent PS amendment
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Other') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 20 );
		$psaf->setPercentAmount( 10 ); //10%
		$psaf->setPercentAmountEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Regular Time') );

		$psaf->setDescription('Test Percent PS Amendment');

		$psaf->setEffectiveDate( $this->pay_period_objs[0]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}


		//Vacation Accrual Release percent PS amendment
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Vacation Accrual Release') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 20 );
		$psaf->setPercentAmount( 50 ); //50% - Leave some balance to check against.
		$psaf->setPercentAmountEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 50, 'Vacation Accrual') );

		$psaf->setDescription('Test Vacation Release Percent PS Amendment');

		$psaf->setEffectiveDate( $this->pay_period_objs[0]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		//YTD Adjustment FIXED PS amendment
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Premium 2') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 10 );
		$psaf->setAmount( 1.99 );
		$psaf->setYTDAdjustment(TRUE);

		$psaf->setDescription('Test YTD PS Amendment');

		$psaf->setEffectiveDate( $this->pay_period_objs[0]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		//YTD Adjustment FIXED PS amendment
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Other') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 10 );
		//$psaf->setAmount( 0.09 );
		$psaf->setAmount( 1000 ); //Increase this so Union Dues are closer to the maximum earnings and are calculated to be less.
		$psaf->setYTDAdjustment(TRUE);

		$psaf->setDescription('Test YTD (2) PS Amendment');

		$psaf->setEffectiveDate( $this->pay_period_objs[0]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		//YTD Adjustment FIXED PS amendment for testing Maximum EI contribution
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'EI') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 10 );
		$psaf->setAmount( 700.00 );
		$psaf->setYTDAdjustment(TRUE);

		$psaf->setDescription('Test EI YTD PS Amendment');

		$psaf->setEffectiveDate( $this->pay_period_objs[0]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		//YTD Adjustment FIXED PS amendment for testing Maximum CPP contribution
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'CPP') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 10 );
		$psaf->setAmount( 1900.00 );
		$psaf->setYTDAdjustment(TRUE);

		$psaf->setDescription('Test CPP YTD PS Amendment');

		$psaf->setEffectiveDate( $this->pay_period_objs[0]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		//YTD Adjustment FIXED PS amendment for testing Vacation Accrual totaling issues.
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 50, 'Vacation Accrual') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 10 );
		$psaf->setAmount( 99.01 );
		$psaf->setYTDAdjustment(TRUE);

		$psaf->setDescription('Test Vacation Accrual YTD PS Amendment');

		$psaf->setEffectiveDate( $this->pay_period_objs[0]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		//
		// Add EARNING PS amendments for a pay period that has no Punch hours.
		// Include a regular time adjustment so we can test Wage Base amounts for some tax/deductions.

		//Regular FIXED PS amendment as regular time.
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Regular Time') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 10 );
		$psaf->setRate( 33.33 );
		$psaf->setUnits( 3 );

		$psaf->setDescription('Test Fixed PS Amendment (1)');

		$psaf->setEffectiveDate( $this->pay_period_objs[1]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		//Regular FIXED PS amendment as Bonus
		$psaf = new PayStubAmendmentFactory();
		$psaf->setUser( $this->user_id );
		$psaf->setPayStubEntryNameId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Bonus') );
		$psaf->setStatus( 50 ); //Active

		$psaf->setType( 10 );
		$psaf->setRate( 10 );
		$psaf->setUnits( 30 );

		$psaf->setDescription('Test Fixed PS Amendment (2)');

		$psaf->setEffectiveDate( $this->pay_period_objs[1]->getEndDate() );

		$psaf->setAuthorized(TRUE);
		if ( $psaf->isValid() ) {
			$psaf->Save();
		}

		return TRUE;
	}

	function createPayStub() {

		$cps = new CalculatePayStub();
		$cps->setUser( $this->user_id );
		$cps->setPayPeriod( $this->pay_period_objs[0]->getId() );
		$cps->calculate();

		//Pay stub for 2nd pay period
		$cps = new CalculatePayStub();
		$cps->setUser( $this->user_id );
		$cps->setPayPeriod( $this->pay_period_objs[1]->getId() );
		$cps->calculate();

		return TRUE;
	}

	function getPayStub( $pay_period_id = FALSE ) {
		if ( $pay_period_id == FALSE ) {
			$pay_period_id = $this->pay_period_objs[0]->getId();
		}

		$pslf = new PayStubListFactory();
		$pslf->getByUserIdAndPayPeriodId( $this->user_id, $pay_period_id );
		if ( $pslf->getRecordCount() > 0 ) {
			return $pslf->getCurrent()->getId();
		}

		return FALSE;
	}

	function testMain() {
		$pse_accounts = array(
							'regular_time' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Regular Time'),
							'over_time_1' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Over Time 1'),
							'premium_1' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Premium 1'),
							'premium_2' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Premium 2'),
							'bonus' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Bonus'),
							'other' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Other'),
							'vacation_accrual_release' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Vacation Accrual Release'),
							'federal_income_tax' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Federal Income Tax'),
							'state_income_tax' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'State Income Tax'),
							'state_disability' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'State Disability Ins.'),
							'medicare' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Medicare'),
							'union_dues' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Union Dues'),
							'advanced_percent_1' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Advanced Percent 1'),
							'advanced_percent_2' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Advanced Percent 2'),
							'deduction_other' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Other'),
							'ei' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'ei'),
							'cpp' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'cpp'),
							'employer_medicare' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 30, 'Medicare'),
							'employer_fica' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 30, 'Social Security (FICA)'),
							'vacation_accrual' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 50, 'Vacation Accrual'),

							);

		$pay_stub_id = $this->getPayStub();

		$pse_arr = $this->getPayStubEntryArray( $pay_stub_id );
		//var_dump($pse_arr);

		$this->assertEquals( $pse_arr[$pse_accounts['regular_time']][0]['amount'], '2408.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['regular_time']][0]['ytd_amount'], '2408.00' );

		$this->assertEquals( $pse_arr[$pse_accounts['over_time_1']][0]['amount'], '451.50' );
		$this->assertEquals( $pse_arr[$pse_accounts['over_time_1']][0]['ytd_amount'], '451.50' );

		$this->assertEquals( $pse_arr[$pse_accounts['premium_1']][0]['amount'], '47.88' );
		$this->assertEquals( $pse_arr[$pse_accounts['premium_1']][0]['ytd_amount'], '47.88' );

		$this->assertEquals( $pse_arr[$pse_accounts['bonus']][0]['rate'], '10.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['bonus']][0]['units'], '10.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['bonus']][0]['amount'], '100.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['bonus']][0]['ytd_amount'], '100.00' );

		//YTD adjustment
		$this->assertEquals( $pse_arr[$pse_accounts['other']][0]['amount'], '240.80' );
		$this->assertEquals( $pse_arr[$pse_accounts['other']][0]['ytd_amount'], '0.00' );
		//Fixed amount PS amendment
		$this->assertEquals( $pse_arr[$pse_accounts['other']][1]['amount'], '1000.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['other']][1]['ytd_amount'], '1240.80' );

		$this->assertEquals( $pse_arr[$pse_accounts['premium_2']][0]['amount'], '10.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['premium_2']][0]['ytd_amount'], '0.00' );

		$this->assertEquals( $pse_arr[$pse_accounts['premium_2']][1]['amount'], '1.99' );
		$this->assertEquals( $pse_arr[$pse_accounts['premium_2']][1]['ytd_amount'], '11.99' );

		//Vacation accrual release
		//$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual_release']][0]['amount'], '64.97' );
		//$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual_release']][0]['ytd_amount'], '64.97' );

		$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual_release']][0]['amount'], '114.67' );
		$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual_release']][0]['ytd_amount'], '114.67' );

		//Vacation accrual deduction
		//$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][0]['amount'], '129.93' );
		//$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][0]['ytd_amount'], '0.00' );

		$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][0]['amount'], '99.01' );
		$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][0]['ytd_amount'], '0.00' );

		$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][1]['amount'], '130.33' );
		$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][1]['ytd_amount'], '0.00' );

		//$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][2]['amount'], '-64.97' );
		//$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][2]['ytd_amount'], '163.97' );

		$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][2]['amount'], '-114.67' );
		$this->assertEquals( $pse_arr[$pse_accounts['vacation_accrual']][2]['ytd_amount'], '114.67' );

		//Union Dues - Should be 19.98 due to getting close to hitting Wage Base, because a YTD adjustment for Total Gross exists for around 1001.99.
		$this->assertEquals( $pse_arr[$pse_accounts['union_dues']][0]['amount'], '19.98' );
		$this->assertEquals( $pse_arr[$pse_accounts['union_dues']][0]['ytd_amount'], '19.98' );

		//Advanced Percent
		$this->assertEquals( $pse_arr[$pse_accounts['advanced_percent_1']][0]['amount'], '20.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['advanced_percent_1']][0]['ytd_amount'], '20.00' ); //Exceeds Wage Base

		$this->assertEquals( $pse_arr[$pse_accounts['advanced_percent_2']][0]['amount'], '24.08' );
		$this->assertEquals( $pse_arr[$pse_accounts['advanced_percent_2']][0]['ytd_amount'], '24.08' ); //Not close to Wage Base.

		$this->assertEquals( $pse_arr[$pse_accounts['deduction_other']][0]['amount'], '37.29' );
		$this->assertEquals( $pse_arr[$pse_accounts['deduction_other']][0]['ytd_amount'], '37.29' );

		//EI
		$this->assertEquals( $pse_arr[$pse_accounts['ei']][0]['amount'], '700.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['ei']][0]['ytd_amount'], '0.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['ei']][1]['amount'], '29.30' ); //HAS TO BE 29.30, as it reached maximum contribution.
		$this->assertEquals( $pse_arr[$pse_accounts['ei']][1]['ytd_amount'], '729.30' );

		//CPP
		$this->assertEquals( $pse_arr[$pse_accounts['cpp']][0]['amount'], '1900.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['cpp']][0]['ytd_amount'], '0.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['cpp']][1]['amount'], '10.70' );
		$this->assertEquals( $pse_arr[$pse_accounts['cpp']][1]['ytd_amount'], '1910.70' );

		if ( $pse_arr[$pse_accounts['federal_income_tax']][0]['amount'] >= 500
				AND $pse_arr[$pse_accounts['federal_income_tax']][0]['amount'] <= 700
				AND $pse_arr[$pse_accounts['federal_income_tax']][0]['amount'] == $pse_arr[$pse_accounts['federal_income_tax']][0]['amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'Federal Income Tax not within range!' );
		}

		if ( $pse_arr[$pse_accounts['state_income_tax']][0]['amount'] >= 100
				AND $pse_arr[$pse_accounts['state_income_tax']][0]['amount'] <= 300
				AND $pse_arr[$pse_accounts['state_income_tax']][0]['amount'] == $pse_arr[$pse_accounts['state_income_tax']][0]['amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'State Income Tax not within range!' );
		}

		if ( $pse_arr[$pse_accounts['medicare']][0]['amount'] >= 10
				AND $pse_arr[$pse_accounts['medicare']][0]['amount'] <= 100
				AND $pse_arr[$pse_accounts['medicare']][0]['amount'] == $pse_arr[$pse_accounts['medicare']][0]['amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'Medicare not within range!' );
		}

		if ( $pse_arr[$pse_accounts['state_disability']][0]['amount'] >= 2
				AND $pse_arr[$pse_accounts['state_disability']][0]['amount'] <= 50
				AND $pse_arr[$pse_accounts['state_disability']][0]['amount'] == $pse_arr[$pse_accounts['state_disability']][0]['amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'State Disability not within range!' );
		}

		if ( $pse_arr[$pse_accounts['employer_medicare']][0]['amount'] >= 10
				AND $pse_arr[$pse_accounts['employer_medicare']][0]['amount'] <= 100
				AND $pse_arr[$pse_accounts['employer_medicare']][0]['amount'] == $pse_arr[$pse_accounts['employer_medicare']][0]['amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'Employer Medicare not within range!' );
		}

		if ( $pse_arr[$pse_accounts['employer_fica']][0]['amount'] >= 100
				AND $pse_arr[$pse_accounts['employer_fica']][0]['amount'] <= 250
				AND $pse_arr[$pse_accounts['employer_fica']][0]['amount'] == $pse_arr[$pse_accounts['employer_fica']][0]['amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'Employer FICA not within range!' );
		}


		if ( $pse_arr[$this->pay_stub_account_link_arr['total_gross']][0]['amount'] >= 3250
				AND $pse_arr[$this->pay_stub_account_link_arr['total_gross']][0]['amount'] <= 3400
				AND ( $pse_arr[$this->pay_stub_account_link_arr['total_gross']][0]['amount']+(1000+1.99) ) == $pse_arr[$this->pay_stub_account_link_arr['total_gross']][0]['ytd_amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'Total Gross not within range!' );
		}

		if ( $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['amount'] >= 1100
				AND $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['amount'] <= 1400
				AND ( bcadd($pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['amount'],2600)) == $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['ytd_amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'Total Deductions not within range! Amount: '. $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['amount'] .' YTD Amount: '. $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['ytd_amount'] );
		}

		if ( $pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['amount'] >= 1900
				AND $pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['amount'] <= 2300
				AND bcsub($pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['amount'], 1598.01) == $pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['ytd_amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'NET PAY not within range!');
		}

		return TRUE;
	}

	function testNoHoursPayStub() {
		$pse_accounts = array(
							'regular_time' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Regular Time'),
							'over_time_1' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Over Time 1'),
							'premium_1' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Premium 1'),
							'premium_2' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Premium 2'),
							'bonus' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Bonus'),
							'other' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Other'),
							'vacation_accrual_release' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 10, 'Vacation Accrual Release'),
							'federal_income_tax' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Federal Income Tax'),
							'state_income_tax' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'State Income Tax'),
							'state_disability' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'State Disability Ins.'),
							'medicare' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Medicare'),
							'union_dues' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Union Dues'),
							'advanced_percent_1' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Advanced Percent 1'),
							'advanced_percent_2' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Advanced Percent 2'),
							'deduction_other' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'Other'),
							'ei' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'ei'),
							'cpp' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 20, 'cpp'),
							'employer_medicare' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 30, 'Medicare'),
							'employer_fica' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 30, 'Social Security (FICA)'),
							'vacation_accrual' => CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($this->company_id, 50, 'Vacation Accrual'),
							);

		$pay_stub_id = $this->getPayStub( $this->pay_period_objs[1]->getId() );

		$pse_arr = $this->getPayStubEntryArray( $pay_stub_id );
		//var_dump($pse_arr);


		$this->assertEquals( $pse_arr[$pse_accounts['regular_time']][0]['rate'], '33.33' );
		$this->assertEquals( $pse_arr[$pse_accounts['regular_time']][0]['units'], '3.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['regular_time']][0]['amount'], '99.99' );
		$this->assertEquals( $pse_arr[$pse_accounts['regular_time']][0]['ytd_amount'], '2507.99' );

		$this->assertEquals( $pse_arr[$pse_accounts['bonus']][0]['rate'], '10.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['bonus']][0]['units'], '30.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['bonus']][0]['amount'], '300.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['bonus']][0]['ytd_amount'], '400.00' );

		$this->assertEquals( $pse_arr[$pse_accounts['union_dues']][0]['amount'], '0.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['union_dues']][0]['ytd_amount'], '19.98' );

		$this->assertEquals( $pse_arr[$this->pay_stub_account_link_arr['total_gross']][0]['amount'], '399.99' );

		//Check deductions.
		$this->assertEquals( $pse_arr[$pse_accounts['advanced_percent_1']][0]['amount'], '0.00' ); //Already Exceeded Wage Base, this should be 0!!
		$this->assertEquals( $pse_arr[$pse_accounts['advanced_percent_1']][0]['ytd_amount'], '20.00' );
		$this->assertEquals( $pse_arr[$pse_accounts['advanced_percent_2']][0]['amount'], '0.92' ); //Nearing Wage Base, this should be less than 1!!
		$this->assertEquals( $pse_arr[$pse_accounts['advanced_percent_2']][0]['ytd_amount'], '25.00' );


		if ( $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['amount'] >= 45
				AND $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['amount'] <= 65
				AND ( bcadd($pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['amount'], 3881.92)) == $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['ytd_amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'Total Deductions not within range! Total Deductions: '. $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['amount'] .' YTD Amount: '. $pse_arr[$this->pay_stub_account_link_arr['total_deductions']][0]['ytd_amount'] );
		}

		if ( $pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['amount'] >= 335
				AND $pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['amount'] <= 355
				AND bcadd($pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['amount'],492.92) == $pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['ytd_amount'] ) {
			$this->assertTrue( TRUE );
		} else {
			$this->assertTrue( FALSE, 'NET PAY not within range! Net Pay: '. $pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['amount'] .' YTD Amount: '. $pse_arr[$this->pay_stub_account_link_arr['net_pay']][0]['ytd_amount']);
		}

		return TRUE;
	}
}
?>