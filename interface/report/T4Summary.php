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
 * $Revision: 2906 $
 * $Id: T4Summary.php 2906 2009-10-16 21:41:04Z ipso $
 * $Date: 2009-10-16 14:41:04 -0700 (Fri, 16 Oct 2009) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');
require(Environment::getBasePath() .'/classes/fpdi/fpdi.php');

if ( !$permission->Check('report','enabled')
		OR !$permission->Check('report','view_t4_summary') ) {
	$permission->Redirect( FALSE ); //Redirect
}

$smarty->assign('title', TTi18n::gettext($title = 'T4 Summary Report')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'setup_data',
												'generic_data',
												'filter_data'
												) ) );

URLBuilder::setURL($_SERVER['SCRIPT_NAME'],
											array(
													'filter_data' => $filter_data
//													'sort_column' => $sort_column,
//													'sort_order' => $sort_order,
												) );

$static_columns = array(			'-1000-full_name' => TTi18n::gettext('Full Name'),
									'-1010-title' => TTi18n::gettext('Title'),
									'-1020-province' => TTi18n::gettext('Province'),
									'-1030-country' => TTi18n::gettext('Country'),
									'-1039-group' => TTi18n::gettext('Group'),
									'-1040-default_branch' => TTi18n::gettext('Default Branch'),
									'-1050-default_department' => TTi18n::gettext('Default Department'),
									'-1060-sin' => TTi18n::gettext('SIN')
									);

$non_static_columns = array(		'-1100-income' => TTi18n::gettext('Income (14)'),
									'-1110-income_tax' => TTi18n::gettext('Income Tax (22)'),
									'-1120-employee_cpp' => TTi18n::gettext('Employee CPP (16)'),
									'-1125-ei_earnings' => TTi18n::gettext('EI Insurable Earnings (24)'),
									'-1126-cpp_earnings' => TTi18n::gettext('CPP Pensionable Earnings (26)'),
									'-1130-employee_ei' => TTi18n::gettext('Employee EI (18)'),
									'-1140-union_dues' => TTi18n::gettext('Union Dues (44)'),
									'-1150-employer_cpp' => TTi18n::gettext('Employer CPP'),
									'-1160-employer_ei' => TTi18n::gettext('Employer EI'),
									'-1170-rpp' => TTi18n::gettext('RPP Contributions (20)'),
									'-1180-charity' => TTi18n::gettext('Charity Donations (46)'),
									'-1190-pension_adjustment' => TTi18n::gettext('Pension Adjustment (52)'),
									'-1200-other_box_0' => TTi18n::gettext('Other Box 1'),
									'-1210-other_box_1' => TTi18n::gettext('Other Box 2'),
									'-1220-other_box_2' => TTi18n::gettext('Other Box 3'),
									'-1220-other_box_3' => TTi18n::gettext('Other Box 4'),
									'-1220-other_box_4' => TTi18n::gettext('Other Box 5'),
									'-1220-other_box_5' => TTi18n::gettext('Other Box 6'),
									);

$pseallf = new PayStubEntryAccountLinkListFactory();
$pseallf->getByCompanyId( $current_company->getId() );
if ( $pseallf->getRecordCount() > 0 ) {
	$pseal_obj = $pseallf->getCurrent();
}

$column_ps_entry_name_map = array(
								'income' => @$setup_data['income_psea_ids'], //Gross Pay
								'income_tax' => @$setup_data['tax_psea_ids'],
								'employee_cpp' => @$setup_data['employee_cpp_psea_id'],
								'employee_ei' => @$setup_data['employee_ei_psea_id'],
								'ei_earnings' => @$setup_data['ei_earnings_psea_ids'],
								'cpp_earnings' => @$setup_data['cpp_earnings_psea_ids'],
								'union_dues' => @$setup_data['union_dues_psea_id'],
								'employer_cpp' => @$setup_data['employer_cpp_psea_id'],
								'employer_ei' => @$setup_data['employer_ei_psea_id'],
								'rpp' => @$setup_data['rpp_psea_ids'],
								'charity' => @$setup_data['charity_psea_ids'],
								'pension_adjustment' => @$setup_data['pension_adjustment_psea_ids'],
								'other_box_0' => @$setup_data['other_box'][0]['psea_ids'],
								'other_box_1' => @$setup_data['other_box'][1]['psea_ids'],
								'other_box_2' => @$setup_data['other_box'][2]['psea_ids'],
								'other_box_3' => @$setup_data['other_box'][3]['psea_ids'],
								'other_box_4' => @$setup_data['other_box'][4]['psea_ids'],
								'other_box_5' => @$setup_data['other_box'][5]['psea_ids'],
								);

$columns = Misc::prependArray( $static_columns, $non_static_columns);

$pplf = new PayPeriodListFactory();
$year_options = $pplf->getYearsArrayByCompanyId( $current_company->getId() );

$ugdlf = new UserGenericDataListFactory();
$ugdf = new UserGenericDataFactory();

$filter_data = Misc::preSetArrayValues( $filter_data, array('include_user_ids', 'exclude_user_ids', 'user_status_ids', 'group_ids', 'branch_ids', 'department_ids', 'user_title_ids', 'pay_period_ids', 'column_ids' ), array() );

$action = Misc::findSubmitButton();
Debug::Text('Action: '. $action, __FILE__, __LINE__, __METHOD__,10);
switch ($action) {
	case 'display_t4s':
	case 'display_report':
		//Debug::setVerbosity(11);

		Debug::Text('Submit!: '. $action, __FILE__, __LINE__, __METHOD__,10);
		//Debug::Arr($filter_data, 'aFilter Data', __FILE__, __LINE__, __METHOD__,10);

		//Save report setup data
		$ugdlf->getByCompanyIdAndScriptAndDefault( $current_company->getId(), $_SERVER['SCRIPT_NAME'] );
		if ( $ugdlf->getRecordCount() > 0 ) {
			$ugdf->setID( $ugdlf->getCurrent()->getID() );
		}
		$ugdf->setCompany( $current_company->getId() );
		$ugdf->setScript( $_SERVER['SCRIPT_NAME'] );
		$ugdf->setName( $title );
		$ugdf->setData( $setup_data );
		$ugdf->setDefault( TRUE );
		if ( $ugdf->isValid() ) {
			$ugdf->Save();
		}

		$ulf = new UserListFactory();
		$ulf->getSearchByCompanyIdAndArrayCriteria( $current_company->getId(), $filter_data );
		if ( $ulf->getRecordCount() > 0 ) {
			foreach( $ulf as $u_obj ) {
				$filter_data['user_ids'][] = $u_obj->getId();
			}

			if ( isset($filter_data['year']) AND isset($filter_data['user_ids']) ) {
				//Get all pay period IDs in year.
				if ( isset($filter_data['year']) ) {
					$year_epoch = mktime(0,0,0,1,1,$filter_data['year']);
					Debug::Text(' Year: '. TTDate::getDate('DATE+TIME', $year_epoch) , __FILE__, __LINE__, __METHOD__,10);
				}

				$pself = new PayStubEntryListFactory();
				$pself->getReportByCompanyIdAndUserIdAndTransactionStartDateAndTransactionEndDate($current_company->getId(), $filter_data['user_ids'], TTDate::getBeginYearEpoch($year_epoch), TTDate::getEndYearEpoch($year_epoch) );

				$report_columns = $static_columns;

				foreach( $pself as $pse_obj ) {
					$user_id = $pse_obj->getColumn('user_id');
					$pay_stub_entry_name_id = $pse_obj->getColumn('pay_stub_entry_name_id');

					$raw_rows[$user_id][$pay_stub_entry_name_id] = $pse_obj->getColumn('amount');
				}
				//var_dump($raw_rows);

				if ( isset($raw_rows) ) {
					$ulf = new UserListFactory();

					$utlf = new UserTitleListFactory();
					$title_options = $utlf->getByCompanyIdArray( $current_company->getId() );

					$uglf = new UserGroupListFactory();
					$group_options = $uglf->getArrayByNodes( FastTree::FormatArray( $uglf->getByCompanyIdArray( $current_company->getId() ), 'no_tree_text', TRUE) );

					$blf = new BranchListFactory();
					$branch_options = $blf->getByCompanyIdArray( $current_company->getId() );

					$dlf = new DepartmentListFactory();
					$department_options = $dlf->getByCompanyIdArray( $current_company->getId() );

					$x=0;
					foreach($raw_rows as $user_id => $raw_row) {
						$user_obj = $ulf->getById( $user_id )->getCurrent();

						$tmp_rows[$x]['user_id'] = $user_id;
						$tmp_rows[$x]['full_name'] = $user_obj->getFullName(TRUE);
						//$tmp_rows[$x]['province'] = Option::getByKey($user_obj->getProvince(), $user_obj->getOptions('province') );
						//$tmp_rows[$x]['province'] = $user_obj->getProvince();

						$tmp_rows[$x]['province'] = $user_obj->getProvince();
						$tmp_rows[$x]['country'] = $user_obj->getCountry();

						$tmp_rows[$x]['title'] = Option::getByKey($user_obj->getTitle(), $title_options, NULL );
						$tmp_rows[$x]['group'] = Option::getByKey($user_obj->getGroup(), $group_options );
						$tmp_rows[$x]['default_branch'] =  Option::getByKey($user_obj->getDefaultBranch(), $branch_options, NULL );
						$tmp_rows[$x]['default_department'] = Option::getByKey($user_obj->getDefaultDepartment(), $department_options, NULL );

						$tmp_rows[$x]['sin'] = $user_obj->getSIN();

						foreach($column_ps_entry_name_map as $column_key => $ps_entry_map) {
							$tmp_rows[$x][$column_key] = Misc::MoneyFormat( Misc::sumMultipleColumns( $raw_rows[$user_id], $ps_entry_map), FALSE );
						}

						$x++;
					}
				}
				//var_dump($tmp_rows);

				//Skip grouping if they are displaying T4's
				if ( $action != 'display_t4s' AND isset($filter_data['primary_group_by']) AND $filter_data['primary_group_by'] != '0' ) {
					Debug::Text('Primary Grouping Data By: '. $filter_data['primary_group_by'], __FILE__, __LINE__, __METHOD__,10);

					$ignore_elements = array_keys($static_columns);

					$filter_data['column_ids'] = array_diff( $filter_data['column_ids'], $ignore_elements );

					//Add the group by element back in
					if ( isset($filter_data['secondary_group_by']) AND $filter_data['secondary_group_by'] != 0 ) {
						array_unshift( $filter_data['column_ids'], $filter_data['primary_group_by'], $filter_data['secondary_group_by'] );
					} else {
						array_unshift( $filter_data['column_ids'], $filter_data['primary_group_by'] );
					}

					$tmp_rows = Misc::ArrayGroupBy( $tmp_rows, array(Misc::trimSortPrefix($filter_data['primary_group_by']),Misc::trimSortPrefix($filter_data['secondary_group_by'])), Misc::trimSortPrefix($ignore_elements) );
				}


				if ( isset($tmp_rows) ) {
					foreach($tmp_rows as $row) {
						$rows[] = $row;
					}

					//$rows = Sort::Multisort($rows, $filter_data['primary_sort'], NULL, 'ASC');
					$rows = Sort::Multisort($rows, Misc::trimSortPrefix($filter_data['primary_sort']), Misc::trimSortPrefix($filter_data['secondary_sort']), $filter_data['primary_sort_dir'], $filter_data['secondary_sort_dir']);

					$total_row = Misc::ArrayAssocSum($rows, NULL, 2);

					$last_row = count($rows);
					$rows[$last_row] = $total_row;
					foreach ($static_columns as $static_column_key => $static_column_val) {
						Debug::Text('Clearing Column: '. $static_column_key, __FILE__, __LINE__, __METHOD__,10);
						$rows[$last_row][Misc::trimSortPrefix($static_column_key)] = NULL;
					}
					unset($static_column_key, $static_column_val);
				}

			}
		}

		foreach( $filter_data['column_ids'] as $column_key ) {
			$filter_columns[Misc::trimSortPrefix($column_key)] = $columns[$column_key];
		}

		if ( $action == 'display_t4s' ) {
			Debug::Text('Generating PDF: ', __FILE__, __LINE__, __METHOD__,10);

			$last_row = count($rows)-1;
			$total_row = $last_row+1;

			//Get company information
			$clf = new CompanyListFactory();
			$company_obj = $clf->getById( $current_company->getId() )->getCurrent();

			//Debug::setVerbosity(11);
			$t4 = new T4;
			if ( isset($filter_data['include_t4_back']) AND $filter_data['include_t4_back'] == 1 ) {
				$t4->setShowInstructionPage(TRUE);
			}
			//$t4->setShowBackGround(FALSE);
			$t4->setShowBorder(FALSE);
			//$t4->setXOffset(10);
			//$t4->setYOffset(10);

			$t4->setType( $filter_data['type'] );
			$t4->setYear( $filter_data['year'] );
			$t4->setBusinessNumber( $company_obj->getBusinessNumber() );

			$t4->setCompanyName( $company_obj->getName() );
			$t4->setCompanyAddress1( $company_obj->getAddress1() );
			$t4->setCompanyAddress2( $company_obj->getAddress2() );
			$t4->setCompanyCity( $company_obj->getCity() );
			$t4->setCompanyProvince( $company_obj->getProvince() );
			$t4->setCompanyPostalCode( $company_obj->getPostalCode() );

			$t4sum = new T4Summary();
			$t4sum->setTotalT4s( count($rows)-1 );
			$t4sum->setEmploymentIncome( $rows[$last_row]['income'] );
			$t4sum->setIncomeTax( $rows[$last_row]['income_tax'] );
			$t4sum->setEmployeeCPP( $rows[$last_row]['employee_cpp'] );
			$t4sum->setEmployeeEI($rows[$last_row]['employee_ei'] );
			$t4sum->setEmployerCPP(  $rows[$last_row]['employer_cpp'] );
			$t4sum->setEmployerEI( $rows[$last_row]['employer_ei'] );
			$t4sum->setEmployeeRPP( $rows[$last_row]['rpp'] );
			$t4sum->setPensionAdjustment( $rows[$last_row]['pension_adjustment'] );

			$total_deductions = Misc::MoneyFormat( $rows[$last_row]['employee_cpp'] + $rows[$last_row]['employer_cpp'] + $rows[$last_row]['employee_ei'] + $rows[$last_row]['employer_ei'] + $rows[$last_row]['income_tax'], FALSE );
			$t4sum->setTotalDeductions( $total_deductions );
			$t4->addT4Summary( $t4sum );

			$i=0;
			foreach($rows as $row) {
				if ( $i == $last_row ) {
					continue;
				}

				$ulf = new UserListFactory();
				$user_obj = $ulf->getById( $row['user_id'] )->getCurrent();


				$t4ee = new T4Employee();
				$t4ee->setSin( $row['sin'] );
				$t4ee->setFirstName( $user_obj->getFirstName() );
				$t4ee->setMiddleName( $user_obj->getMiddleName() );
				$t4ee->setLastName( $user_obj->getLastName()  );
				$t4ee->setAddress1( $user_obj->getAddress1() );
				$t4ee->setAddress2( $user_obj->getAddress2() );
				$t4ee->setCity( $user_obj->getCity() );
				$t4ee->setProvince( $user_obj->getProvince() );
				$t4ee->setPostalCode( $user_obj->getPostalCode() );
				//$t4ee->setEmployementCode( );

				//Get User Tax / Deductions by Pay Stub Account.
				$udlf = new UserDeductionListFactory();
				if ( isset($setup_data['employee_cpp_psea_id']) ) {
					$udlf->getByUserIdAndPayStubEntryAccountID( $user_obj->getId(), $setup_data['employee_cpp_psea_id'] );
					if ( $setup_data['employee_cpp_psea_id'] != 0
							AND $udlf->getRecordCount() == 0 ) {
						Debug::Text('CPP Exempt!', __FILE__, __LINE__, __METHOD__,10);
						$t4ee->setExemptCPP( TRUE );
					}
				}

				if ( isset($setup_data['employee_ei_psea_id'] ) ) {
					$udlf->getByUserIdAndPayStubEntryAccountID( $user_obj->getId(), $setup_data['employee_ei_psea_id'] );
					if ( $setup_data['employee_ei_psea_id'] != 0
							AND $udlf->getRecordCount() == 0 ) {
						Debug::Text('EI Exempt!', __FILE__, __LINE__, __METHOD__,10);
						$t4ee->setExemptEI( TRUE );
					}
				}

				$t4ee->setEmploymentIncome( $row['income'] );
				$t4ee->setIncomeTax( $row['income_tax'] );
				$t4ee->setEmployeeCPP( $row['employee_cpp'] );
				$t4ee->setEIEarnings( $row['ei_earnings'] );
				$t4ee->setCPPEarnings( $row['cpp_earnings'] );
				$t4ee->setEmployeeEI( $row['employee_ei'] );
				$t4ee->setUnionDues( $row['union_dues']  );
				$t4ee->setEmployeeRPP( $row['rpp'] );
				$t4ee->setCharityDonations( $row['charity'] );
				$t4ee->setPensionAdjustment( $row['pension_adjustment'] );

				if ( $row['other_box_0'] > 0 AND isset($setup_data['other_box'][0]['box']) AND $setup_data['other_box'][0]['box'] !='') {
					$t4ee->setOtherBox1Code( $setup_data['other_box'][0]['box'] );
					$t4ee->setOtherBox1( $row['other_box_0'] );
				}

				if ( $row['other_box_1'] > 0 AND isset($setup_data['other_box'][1]['box']) AND $setup_data['other_box'][1]['box'] !='') {
					$t4ee->setOtherBox2Code( $setup_data['other_box'][1]['box'] );
					$t4ee->setOtherBox2( $row['other_box_1'] );
				}

				if ( $row['other_box_2'] > 0 AND isset($setup_data['other_box'][2]['box']) AND $setup_data['other_box'][2]['box'] !='') {
					$t4ee->setOtherBox3Code( $setup_data['other_box'][2]['box'] );
					$t4ee->setOtherBox3( $row['other_box_2'] );
				}

				if ( $row['other_box_3'] > 0 AND isset($setup_data['other_box'][3]['box']) AND $setup_data['other_box'][3]['box'] !='') {
					$t4ee->setOtherBox4Code( $setup_data['other_box'][3]['box'] );
					$t4ee->setOtherBox4( $row['other_box_3'] );
				}

				if ( $row['other_box_4'] > 0 AND isset($setup_data['other_box'][4]['box']) AND $setup_data['other_box'][4]['box'] !='') {
					$t4ee->setOtherBox5Code( $setup_data['other_box'][4]['box'] );
					$t4ee->setOtherBox5( $row['other_box_4'] );
				}
				if ( $row['other_box_5'] > 0 AND isset($setup_data['other_box'][5]['box']) AND $setup_data['other_box'][5]['box'] !='') {
					$t4ee->setOtherBox6Code( $setup_data['other_box'][5]['box'] );
					$t4ee->setOtherBox6( $row['other_box_5'] );
				}
				$t4->addT4Employee( $t4ee );

				$i++;
			}

			$t4->compileT4Summary();
			$t4->compileT4();

			$t4->displayPDF();
		} else {
			Debug::Text('NOT Generating PDF: ', __FILE__, __LINE__, __METHOD__,10);
		}

		$smarty->assign_by_ref('generated_time', TTDate::getTime() );
		//$smarty->assign_by_ref('pay_period_options', $pay_period_options );
		$smarty->assign_by_ref('filter_data', $filter_data );
		$smarty->assign_by_ref('columns', $filter_columns );
		$smarty->assign_by_ref('rows', $rows);

		$smarty->display('report/T4SummaryReport.tpl');

		break;
	case 'delete':
	case 'save':
		Debug::Text('Action: '. $action, __FILE__, __LINE__, __METHOD__,10);

		$generic_data['id'] = UserGenericDataFactory::reportFormDataHandler( $action, $filter_data, $generic_data, URLBuilder::getURL(NULL, $_SERVER['SCRIPT_NAME']) );
		unset($generic_data['name']);
	default:
		BreadCrumb::setCrumb($title);

		$ugdlf->getByCompanyIdAndScriptAndDefault( $current_company->getId(), $_SERVER['SCRIPT_NAME'] );
		if ( $ugdlf->getRecordCount() > 0 ) {
			Debug::Text('Found Company Report Setup!', __FILE__, __LINE__, __METHOD__,10);
			$ugd_obj = $ugdlf->getCurrent();
			$setup_data = $ugd_obj->getData();
		}
		unset($ugd_obj);

		if ( $action == 'load' ) {
			Debug::Text('Loading Report!', __FILE__, __LINE__, __METHOD__,10);
			extract( UserGenericDataFactory::getReportFormData( $generic_data['id'] ) );
		} elseif ( $action == '' ) {
			//Check for default saved report first.
			$ugdlf->getByUserIdAndScriptAndDefault( $current_user->getId(), $_SERVER['SCRIPT_NAME'] );
			if ( $ugdlf->getRecordCount() > 0 ) {
				Debug::Text('Found Default Report!', __FILE__, __LINE__, __METHOD__,10);

				$ugd_obj = $ugdlf->getCurrent();
				$filter_data = $ugd_obj->getData();
				$generic_data['id'] = $ugd_obj->getId();
			} else {
				Debug::Text('Default Settings!', __FILE__, __LINE__, __METHOD__,10);
				//Default selections
				//$filter_data['user_ids'] = array_keys( UserListFactory::getByCompanyIdArray( $current_company->getId(), FALSE, FALSE ) );
				$filter_data['user_status_ids'] = array( -1 );
				$filter_data['branch_ids'] = array( -1 );
				$filter_data['department_ids'] = array( -1 );
				$filter_data['user_title_ids'] = array( -1 );
				$filter_data['group_ids'] = array( -1 );

				//$filter_data['year'] = $year_options[$year_keys[1]];

				$filter_data['column_ids'] = array_keys($columns);

				//$filter_data['sort_column'] = 'last_name';
				$filter_data['primary_sort'] = '-1000-full_name';
				$filter_data['secondary_sort'] = '-1020-province';


			}
		}
		$filter_data = Misc::preSetArrayValues( $filter_data, array('include_user_ids', 'exclude_user_ids', 'user_status_ids', 'group_ids', 'branch_ids', 'department_ids', 'user_title_ids', 'pay_period_ids', 'column_ids' ), NULL );

		//Deduction PSEA accounts
		$psealf = new PayStubEntryAccountListFactory();
		$filter_data['pay_stub_entry_account_options'] = $psealf->getByCompanyIdAndStatusIdAndTypeIdArray( $current_company->getId(), 10, array(10,20,30,40,50), TRUE );

		$psealf = new PayStubEntryAccountListFactory();
		$filter_data['deduction_pay_stub_entry_account_options'] = $psealf->getByCompanyIdAndStatusIdAndTypeIdArray( $current_company->getId(), 10, array(20,30), TRUE );

		$ulf = new UserListFactory();
		$all_array_option = array('-1' => TTi18n::gettext('-- All --'));

		//Get include employee list.
		$ulf->getByCompanyId( $current_company->getId() );
		$user_options = $ulf->getArrayByListFactory( $ulf, FALSE, TRUE );

		$filter_data['src_include_user_options'] = Misc::arrayDiffByKey( (array)$filter_data['include_user_ids'], $user_options );
		$filter_data['selected_include_user_options'] = Misc::arrayIntersectByKey( (array)$filter_data['include_user_ids'], $user_options );

		//Get exclude employee list
		$exclude_user_options = Misc::prependArray( $all_array_option, $ulf->getArrayByListFactory( $ulf, FALSE, TRUE ) );
		$filter_data['src_exclude_user_options'] = Misc::arrayDiffByKey( (array)$filter_data['exclude_user_ids'], $user_options );
		$filter_data['selected_exclude_user_options'] = Misc::arrayIntersectByKey( (array)$filter_data['exclude_user_ids'], $user_options );

		//Get employee status list.
		$user_status_options = Misc::prependArray( $all_array_option, $ulf->getOptions('status') );
		$filter_data['src_user_status_options'] = Misc::arrayDiffByKey( (array)$filter_data['user_status_ids'], $user_status_options );
		$filter_data['selected_user_status_options'] = Misc::arrayIntersectByKey( (array)$filter_data['user_status_ids'], $user_status_options );

		//Get Employee Groups
		$uglf = new UserGroupListFactory();
		$group_options = Misc::prependArray( $all_array_option, $uglf->getArrayByNodes( FastTree::FormatArray( $uglf->getByCompanyIdArray( $current_company->getId() ), 'TEXT', TRUE) ) );
		$filter_data['src_group_options'] = Misc::arrayDiffByKey( (array)$filter_data['group_ids'], $group_options );
		$filter_data['selected_group_options'] = Misc::arrayIntersectByKey( (array)$filter_data['group_ids'], $group_options );

		//Get branches
		$blf = new BranchListFactory();
		$blf->getByCompanyId( $current_company->getId() );
		$branch_options = Misc::prependArray( $all_array_option, $blf->getArrayByListFactory( $blf, FALSE, TRUE ) );
		$filter_data['src_branch_options'] = Misc::arrayDiffByKey( (array)$filter_data['branch_ids'], $branch_options );
		$filter_data['selected_branch_options'] = Misc::arrayIntersectByKey( (array)$filter_data['branch_ids'], $branch_options );

		//Get departments
		$dlf = new DepartmentListFactory();
		$dlf->getByCompanyId( $current_company->getId() );
		$department_options = Misc::prependArray( $all_array_option, $dlf->getArrayByListFactory( $dlf, FALSE, TRUE ) );
		$filter_data['src_department_options'] = Misc::arrayDiffByKey( (array)$filter_data['department_ids'], $department_options );
		$filter_data['selected_department_options'] = Misc::arrayIntersectByKey( (array)$filter_data['department_ids'], $department_options );

		//Get employee titles
		$utlf = new UserTitleListFactory();
		$utlf->getByCompanyId( $current_company->getId() );
		$user_title_options = Misc::prependArray( $all_array_option, $utlf->getArrayByListFactory( $utlf, FALSE, TRUE ) );
		$filter_data['src_user_title_options'] = Misc::arrayDiffByKey( (array)$filter_data['user_title_ids'], $user_title_options );
		$filter_data['selected_user_title_options'] = Misc::arrayIntersectByKey( (array)$filter_data['user_title_ids'], $user_title_options );

		//Get column list
		$filter_data['src_column_options'] = Misc::arrayDiffByKey( (array)$filter_data['column_ids'], $columns );
		$filter_data['selected_column_options'] = Misc::arrayIntersectByKey( (array)$filter_data['column_ids'], $columns );

		$filter_data['year_options'] = $year_options;
		$filter_data['type_options'] = array('government' => TTi18n::gettext('Government (Multiple Employees/Page)'), 'employee' => TTi18n::gettext('Employee (One Employee/Page)') );

		//Get primary/secondary order list
		$filter_data['sort_options'] = $columns;
		$filter_data['sort_direction_options'] = Misc::getSortDirectionArray();

		$filter_data['group_by_options'] = Misc::prependArray( array('0' => TTi18n::gettext('No Grouping')), $static_columns );

		$saved_report_options = $ugdlf->getByUserIdAndScriptArray( $current_user->getId(), $_SERVER['SCRIPT_NAME']);
		$generic_data['saved_report_options'] = $saved_report_options;
		$smarty->assign_by_ref('generic_data', $generic_data);

		$smarty->assign_by_ref('filter_data', $filter_data);
		$smarty->assign_by_ref('setup_data', $setup_data);

		$smarty->assign_by_ref('ugdf', $ugdf);

		$smarty->display('report/T4Summary.tpl');

		break;
}
?>