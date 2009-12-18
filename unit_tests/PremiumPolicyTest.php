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
 * $Revision: 676 $
 * $Id: PayStubCalculationTest.php 676 2007-03-07 23:47:29Z ipso $
 * $Date: 2007-03-07 15:47:29 -0800 (Wed, 07 Mar 2007) $
 */
require_once('PHPUnit/Framework/TestCase.php');

class PremiumPolicyTest extends PHPUnit_Framework_TestCase {

	protected $company_id = NULL;
	protected $user_id = NULL;
	protected $pay_period_schedule_id = NULL;
	protected $pay_period_objs = NULL;
	protected $pay_stub_account_link_arr = NULL;
	protected $branch_ids = NULL;
	protected $department_ids = NULL;

    public function __construct() {
        global $db, $cache, $profiler;

        require_once('../includes/global.inc.php');

        $profiler = new Profiler( true );
        Debug::setBufferOutput(FALSE);
        Debug::setEnable(TRUE);
		//Debug::setVerbosity(11);

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

		$dd->createPermissionGroups( $this->company_id );

		$dd->createCurrency( $this->company_id, 10 );

		$dd->createPayStubAccount( $this->company_id );
		$this->createPayStubAccounts();
		//$this->createPayStubAccrualAccount();
		$dd->createPayStubAccountLink( $this->company_id );
		$this->getPayStubAccountLinkArray();

		$dd->createUserWageGroups( $this->company_id );

		$this->user_id = $dd->createUser( $this->company_id, 100 );

		$this->createPayPeriodSchedule();
		$this->createPayPeriods();
		$this->getAllPayPeriods();

		$this->branch_ids[] = $dd->createBranch( $this->company_id, 10 );
		$this->branch_ids[] = $dd->createBranch( $this->company_id, 20 );

		$this->department_ids[] = $dd->createDepartment( $this->company_id, 10 );
		$this->department_ids[] = $dd->createDepartment( $this->company_id, 20 );

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


		$ppsf->setDayStartTime( 0 );
		$ppsf->setNewDayTriggerTime( (4*3600) );
		$ppsf->setMaximumShiftTime( (16*3600) );
		$ppsf->setShiftAssignedDay( 10 );
		//$ppsf->setContinuousTime( (4*3600) );

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
					//$end_date = TTDate::getBeginYearEpoch( strtotime('01-Jan-07') );
					$end_date = TTDate::getBeginYearEpoch( time() );
				} else {
					$end_date = $end_date + ( (86400*14) );
				}

				Debug::Text('I: '. $i .' End Date: '. TTDate::getDate('DATE+TIME', $end_date) , __FILE__, __LINE__, __METHOD__,10);

				$pps_obj->createNextPayPeriod( $end_date , (86400*3600) );
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

	function getUserDateTotalArray( $start_date, $end_date ) {
		$udtlf = new UserDateTotalListFactory();

		$date_totals = array();

		//Get only system totals.
		//$udtlf->getByCompanyIDAndUserIdAndStatusAndStartDateAndEndDate( $this->company_id, $this->user_id, 10, $start_date, $end_date);
		$udtlf->getByCompanyIDAndUserIdAndStatusAndTypeAndStartDateAndEndDate( $this->company_id, $this->user_id, 10, array(10,20,40), $start_date, $end_date);
		if ( $udtlf->getRecordCount() > 0 ) {
			foreach($udtlf as $udt_obj) {
				$user_date_stamp = TTDate::strtotime( $udt_obj->getColumn('user_date_stamp') );

				$type_and_policy_id = $udt_obj->getType().(int)$udt_obj->getOverTimePolicyID();

				$date_totals[$user_date_stamp][] = array(
												'date_stamp' => $udt_obj->getColumn('user_date_stamp'),
												'id' => $udt_obj->getId(),
												'user_date_id' => $udt_obj->getUserDateId(),
												'status_id' => $udt_obj->getStatus(),
												'type_id' => $udt_obj->getType(),
												'over_time_policy_id' => $udt_obj->getOverTimePolicyID(),
												'premium_policy_id' => $udt_obj->getPremiumPolicyID(),
												'type_and_policy_id' => $type_and_policy_id,
												'branch_id' => (int)$udt_obj->getBranch(),
												'department_id' => $udt_obj->getDepartment(),
												'total_time' => $udt_obj->getTotalTime(),
												'name' => $udt_obj->getName(),
												//Override only shows for SYSTEM override columns...
												//Need to check Worked overrides too.
												'tmp_override' => $udt_obj->getOverride()
												);

			}
		}

		return $date_totals;
	}

	function createPremiumPolicy( $company_id, $type, $accrual_policy_id = NULL ) {
		$ppf = new PremiumPolicyFactory();
		$ppf->setCompany( $company_id );

		switch ( $type ) {
			case 90: //Basic Min/Max only.
				$ppf->setName( 'Min/Max Only' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( NULL );
				$ppf->setEndDate( NULL );

				$ppf->setStartTime( NULL );
				$ppf->setEndTime( NULL );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( 0 );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 3600 );
				$ppf->setMaximumTime( 7200 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );
				break;
			case 100:
				$ppf->setName( 'Start/End Date Only' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( $this->pay_period_objs[0]->getStartDate()+86400 );
				$ppf->setEndDate( $this->pay_period_objs[0]->getStartDate()+(86400*3) ); //2nd & 3rd days.

				$ppf->setStartTime( TTDate::parseDateTime('12:00 AM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( 0 );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );
				break;
			case 110:
				$ppf->setName( 'Start/End Date+Effective Days' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( $this->pay_period_objs[0]->getStartDate()+86400 );
				$ppf->setEndDate( $this->pay_period_objs[0]->getStartDate()+(86400*3) ); //2nd & 3rd days.

				$ppf->setStartTime( TTDate::parseDateTime('12:00 AM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( 0 );

				if ( TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*2)) == 1
							OR TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*3)) == 1 ) {
					$ppf->setMon( TRUE );
				} else {
					$ppf->setMon( FALSE );
				}
				if ( TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*2)) == 2
						OR TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*3)) == 2) {
					$ppf->setTue( TRUE );
				} else {
					$ppf->setTue( FALSE );
				}
				if ( TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*2)) == 3
						OR TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*3)) == 3) {
					$ppf->setWed( TRUE );
				} else {
					$ppf->setWed( FALSE );
				}
				if ( TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*2)) == 4
						OR TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*3)) == 4) {
					$ppf->setThu( TRUE );
				} else {
					$ppf->setThu( FALSE );
				}
				if ( TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*2)) == 5
						OR TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*3)) == 5 ) {
					$ppf->setFri( TRUE );
				} else {
					$ppf->setFri( FALSE );
				}
				if ( TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*2)) == 6
						OR TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*3)) == 6) {
					$ppf->setSat( TRUE );
				} else {
					$ppf->setSat( FALSE );
				}
				if ( TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*2)) == 0
						OR TTDate::getDayOfWeek($this->pay_period_objs[0]->getStartDate()+(86400*3)) == 0) {
					$ppf->setSun( TRUE );
				} else {
					$ppf->setSun( FALSE );
				}

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 120:
				$ppf->setName( 'Time Based/Evening Shift w/Partial' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('7:00 PM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( 0 );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 122:
				$ppf->setName( 'Time Based/Evening Shift w/Partial+Span Midnight' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('6:00 PM') );
				$ppf->setEndTime( TTDate::parseDateTime('3:00 AM') );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( 0 );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 123:
				$ppf->setName( 'Time Based/Weekend Day Shift w/Partial' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('7:00 AM') );
				$ppf->setEndTime( TTDate::parseDateTime('7:00 PM') );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( 0 );

				$ppf->setMon( FALSE );
				$ppf->setTue( FALSE );
				$ppf->setWed( FALSE );
				$ppf->setThu( FALSE);
				$ppf->setFri( FALSE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 130:
				$ppf->setName( 'Time Based/Evening Shift w/o Partial' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('7:00 PM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( 0 );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( FALSE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 132:
				$ppf->setName( 'Time Based/Evening Shift w/o Partial+Span Midnight' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('6:00 PM') );
				$ppf->setEndTime( TTDate::parseDateTime('3:00 AM') );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( 0 );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( FALSE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 140:
				$ppf->setName( 'Daily Hour Based' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('12:00 AM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setDailyTriggerTime( (3600*5) );
				$ppf->setWeeklyTriggerTime( 0 );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 150:
				$ppf->setName( 'Weekly Hour Based' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('12:00 AM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setDailyTriggerTime( 0 );
				$ppf->setWeeklyTriggerTime( (3600*9) );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 160:
				$ppf->setName( 'Daily+Weekly Hour Based' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('12:00 AM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setDailyTriggerTime( (3600*3) );
				$ppf->setWeeklyTriggerTime( (3600*9) );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 170:
				$ppf->setName( 'Time+Daily+Weekly Hour Based' );
				$ppf->setType( 10 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('7:00 PM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setDailyTriggerTime( (3600*5) );
				$ppf->setWeeklyTriggerTime( (3600*9) );

				$ppf->setMon( TRUE );
				$ppf->setTue( TRUE );
				$ppf->setWed( TRUE );
				$ppf->setThu( TRUE );
				$ppf->setFri( TRUE );
				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setExcludeDefaultBranch( FALSE );
				//$ppf->setExcludeDefaultDepartment( FALSE );
				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 200:
				$ppf->setName( 'Branch Differential' );
				$ppf->setType( 20 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				$ppf->setExcludeDefaultBranch( FALSE );
				$ppf->setExcludeDefaultDepartment( FALSE );

				$ppf->setBranchSelectionType( 20 );

				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;
			case 210:
				$ppf->setName( 'Branch/Department Differential' );
				$ppf->setType( 20 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setIncludePartialPunch( TRUE );
				//$ppf->setMaximumNoBreakTime( $data['maximum_no_break_time'] );
				//$ppf->setMinimumBreakTime( $data['minimum_break_time'] );

				$ppf->setMinimumTime( 0 );
				$ppf->setMaximumTime( 0 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				$ppf->setExcludeDefaultBranch( FALSE );
				$ppf->setExcludeDefaultDepartment( FALSE );

				$ppf->setBranchSelectionType( 20 );
				$ppf->setDepartmentSelectionType( 20 );

				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );

				break;

			case 300:
				$ppf->setName( 'Meal Break' );
				$ppf->setType( 30 );

				$ppf->setPayType( 10 ); //Pay Multiplied by factor

				$ppf->setIncludePartialPunch( TRUE );

				$ppf->setDailyTriggerTime( (3600*5) );
				$ppf->setMaximumNoBreakTime( (3600*5) );
				$ppf->setMinimumBreakTime(  1800 );

				$ppf->setMinimumTime( 1800 );
				$ppf->setMaximumTime( 1800 );
				$ppf->setIncludeMealPolicy( TRUE );

				$ppf->setRate( 1.0 );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				//$ppf->setJobGroupSelectionType( 10 );
				//$ppf->setJobSelectionType( 10 );
				//$ppf->setJobItemGroupSelectionType( 10 );
				//$ppf->setJobItemSelectionType( 10 );
				break;
		}

		if ( $ppf->isValid() ) {
			$insert_id = $ppf->Save(FALSE);
			Debug::Text('Premium Policy ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			switch ( $type ) {
				case 200:
					Debug::Text('Post Save Data...', __FILE__, __LINE__, __METHOD__,10);
					$ppf->setBranch( array($this->branch_ids[0]) );
					break;
				case 210:
					Debug::Text('Post Save Data...', __FILE__, __LINE__, __METHOD__,10);
					$ppf->setBranch( array($this->branch_ids[0]) );
					$ppf->setDepartment( array($this->department_ids[0]) );
					break;
			}

			Debug::Text('Post Save...', __FILE__, __LINE__, __METHOD__,10);
			$ppf->Save();

			return $insert_id;
		}

		Debug::Text('Failed Creating Premium Policy!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	/*
	 Tests:
		No Premium
		Min/Max time.
		Day Based
		Day Based+Effective Days
		Time Based w/No Partial punches
		Time Based w/Partial punches
		Daily Hour Based
		Weekly Hour Based
		Daily+Weekly Hour Based
		Time+Hour Based Premium
		Shift Differential Branch
		Shift Differential Department
		Shift Differential Branch+Department
		Shift Differential Job
		Shift Differential Task
		Shift Differential Job+Task
		Meal Break
		Advanced Time+Hour+Branch+Department+Job
	*/

	function testNoPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate();
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 2 );

		return TRUE;
	}

	function testMinMaxPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 90 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 8:30AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (0.5*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (0.5*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (1*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testMinMaxPremiumPolicyB() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 90 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 9:30AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (1.5*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (1.5*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (1.5*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testMinMaxPremiumPolicyC() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 90 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 10:00AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (2*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (2*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (2*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testMinMaxPremiumPolicyD() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 90 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 11:00AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (3*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (3*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (2*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testMinMaxPremiumPolicyE() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 90 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 8:15AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:30AM'),
								strtotime($date_stamp.' 8:45AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (0.5*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (0.5*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], 900 );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], 2700 );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 4 );

		return TRUE;
	}

	function testMinMaxPremiumPolicyF() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 90 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 8:30AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 9:00AM'),
								strtotime($date_stamp.' 11:30AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (3*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (3*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], 1800 );

		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], 5400 );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 4 );

		return TRUE;
	}

	function testDatePremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testEffectiveDatePremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		//$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//
		// Day1
		//
		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['premium_policy_id'], $policy_ids['premium'][0] );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		//
		// Day2
		//
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*2);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 4 );

		return TRUE;
	}

	function testTimeBasedPartialPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 120 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 3:00PM'),
								strtotime($date_stamp.' 10:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (3*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (7*3600) );
		//Premium Time3
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (7*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 5 );

		return TRUE;
	}

	function testTimeBasedPartialPremiumPolicyB() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 122 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 7:00PM'),
								strtotime($date_stamp2.' 2:00AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testTimeBasedPartialPremiumPolicyC() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 122 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 2:00PM'),
								strtotime($date_stamp.' 5:30PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 20,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 6:30PM'),
								strtotime($date_stamp2.' 1:30AM'),
								array(
											'in_type_id' => 20,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (10.5*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (10.5*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testTimeBasedPartialPremiumPolicyD() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 122 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 1:00AM'),
								strtotime($date_stamp.' 4:00AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 5:00PM'),
								strtotime($date_stamp.' 8:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (6*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (6*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (2*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (2*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 4 );

		return TRUE;
	}

	function testTimeBasedPartialPremiumPolicyE() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 122 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		//Test punching in before the premium start time, and out after the premium end time.
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 5:00PM'),
								strtotime($date_stamp2.' 4:00AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (11*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (11*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (9*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testTimeBasedPartialPremiumPolicyF() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 123 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*5);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		//Test punching in before the premium start time, and out after the premium end time.
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 6:00PM'), //Friday evening
								strtotime($date_stamp2.' 9:00AM'), //Saturday morning.
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (15*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (15*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (2*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testTimeBasedPartialPremiumPolicyG() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 123 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*7);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		//Test punching in before the premium start time, and out after the premium end time.
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 6:00PM'), //Sunday evening
								strtotime($date_stamp2.' 9:00AM'), //Monday morning.
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (15*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (15*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (1*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testTimeBasedNoPartialPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 130 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 3:00PM'),
								strtotime($date_stamp.' 10:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 4 );

		return TRUE;
	}

	function testTimeBasedNoPartialPremiumPolicyB() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 132 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 1:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 20,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 2:00PM'),
								strtotime($date_stamp.' 7:00PM'),
								array(
											'in_type_id' => 20,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (10*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (10*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 2 );

		return TRUE;
	}

	function testTimeBasedNoPartialPremiumPolicyC() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 132 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 2:30PM'),
								strtotime($date_stamp.' 5:30PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 20,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 7:00PM'),
								strtotime($date_stamp.' 10:00PM'),
								array(
											'in_type_id' => 20,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (6*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (6*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 2 );

		return TRUE;
	}

	//Put a 5hr gap between the two punch pairs to signify a new shift starting, so premium does kick in.
	function testTimeBasedNoPartialPremiumPolicyD() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 132 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 1:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 7:00PM'),
								strtotime($date_stamp.' 10:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (8*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (8*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (3*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		return TRUE;
	}

	function testTimeBasedNoPartialPremiumPolicyE() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 132 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 1:00AM'),
								strtotime($date_stamp.' 5:00AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 20,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 7:00AM'),
								strtotime($date_stamp.' 11:00AM'),
								array(
											'in_type_id' => 20,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (8*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (8*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (4*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (4*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 4 );

		return TRUE;
	}

	function testTimeBasedNoPartialPremiumPolicyF() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 132 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );
		$date_stamp2 = TTDate::getDate('DATE', $date_epoch+86400 );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 11:00PM'),
								strtotime($date_stamp2.' 3:00AM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 20,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp2.' 5:00AM'),
								strtotime($date_stamp2.' 9:00AM'),
								array(
											'in_type_id' => 20,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (8*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (8*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (4*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (4*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 4 );

		return TRUE;
	}


	function testDailyHourPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 140 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (2*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (7*3600) );
		//Premium Time3
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 5 );

		return TRUE;
	}

	function testWeeklyHourPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 150 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//
		// Day1
		//
		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		//
		// Day2
		//
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*2);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (5*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (7*3600) );
		//Premium Time3
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 5 );

		return TRUE;
	}

	function testDailyWeeklyHourPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 160 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//
		// Day1
		//
		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		//
		// Day2
		//
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*2);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (4*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (7*3600) );
		//Premium Time3
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 5 );

		return TRUE;
	}

	function testTimeDailyWeeklyHourPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 170 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//
		// Day1
		//
		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (7*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 3 );

		//
		// Day2
		//
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*2);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 3:00PM'),
								strtotime($date_stamp.' 10:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (2*3600) );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (7*3600) );
		//Premium Time3
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (7*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 5 );

		return TRUE;
	}

	function testBranchDifferentialPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 200 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		//
		// Punch Pair 1
		//
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 12:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		//
		// Punch Pair 2
		//
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 1:00PM'),
								strtotime($date_stamp.' 4:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => $this->branch_ids[0],
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (3*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (4*3600) );
		//Premium Time 1
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (3*3600) );
		//Premium Time 2
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (3*3600) );
		//Premium Time 3
		$this->assertEquals( $udt_arr[$date_epoch][5]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][5]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][5]['total_time'], (4*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 6 );

		return TRUE;
	}

	function testBranchDepartmentDifferentialPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 210 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+86400;
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		//
		// Punch Pair 1
		//
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 12:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		//
		// Punch Pair 2
		//
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 1:00PM'),
								strtotime($date_stamp.' 4:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => $this->branch_ids[0],
											'department_id' => $this->department_ids[0],
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (3*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (4*3600) );
		//Premium Time 1
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (3*3600) );
		//Premium Time 2
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (3*3600) );
		//Premium Time 3
		$this->assertEquals( $udt_arr[$date_epoch][5]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][5]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][5]['total_time'], (4*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 6 );

		return TRUE;
	}

	function testMealPremiumPolicyA() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 300 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 3:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );
		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], 1800 );
		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (7*3600) );
		//Premium Time3
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (7*3600) );

		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 5 );

		return TRUE;
	}

	function testMealPremiumPolicyB() {
		global $dd;

		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 100 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 110 );
		$policy_ids['premium'][] = $this->createPremiumPolicy( $this->company_id, 300 );

		//Create Policy Group
		$dd->createPolicyGroup( 	$this->company_id,
									NULL,
									NULL,
									NULL,
									NULL,
									$policy_ids['premium'],
									NULL,
									array($this->user_id) );

		//$date_epoch = TTDate::getBeginWeekEpoch( time() );
		$date_epoch = $this->pay_period_objs[0]->getStartDate()+(86400*3);
		$date_stamp = TTDate::getDate('DATE', $date_epoch );

		//
		// Punch Pair 1
		//
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 8:00AM'),
								strtotime($date_stamp.' 12:00PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		//
		// Punch Pair 2
		//
		$dd->createPunchPair( 	$this->user_id,
								strtotime($date_stamp.' 12:45PM'),
								strtotime($date_stamp.' 3:45PM'),
								array(
											'in_type_id' => 10,
											'out_type_id' => 10,
											'branch_id' => 0,
											'department_id' => 0,
											'job_id' => 0,
											'job_item_id' => 0,
										),
								TRUE
								);

		$udt_arr = $this->getUserDateTotalArray( $date_epoch, $date_epoch );
		//print_r($udt_arr);

		//Total Time
		$this->assertEquals( $udt_arr[$date_epoch][0]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['type_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][0]['total_time'], (7*3600) );
		//Regular Time
		$this->assertEquals( $udt_arr[$date_epoch][1]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['type_id'], 20 );
		$this->assertEquals( $udt_arr[$date_epoch][1]['total_time'], (7*3600) );

		//Premium Time1
		$this->assertEquals( $udt_arr[$date_epoch][2]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][2]['total_time'], (3*3600) );
		//Premium Time3
		$this->assertEquals( $udt_arr[$date_epoch][3]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][3]['total_time'], (3*3600) );

		//Premium Time2
		$this->assertEquals( $udt_arr[$date_epoch][4]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][4]['total_time'], (4*3600) );
		//Premium Time4
		$this->assertEquals( $udt_arr[$date_epoch][5]['status_id'], 10 );
		$this->assertEquals( $udt_arr[$date_epoch][5]['type_id'], 40 );
		$this->assertEquals( $udt_arr[$date_epoch][5]['total_time'], (4*3600) );
		//Make sure no other hours
		$this->assertEquals( count($udt_arr[$date_epoch]), 6 );

		return TRUE;
	}
}
?>