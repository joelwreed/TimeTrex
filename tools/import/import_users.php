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
 * $Revision: 2534 $
 * $Id: import_users.php 2534 2009-05-13 00:02:20Z ipso $
 * $Date: 2009-05-12 17:02:20 -0700 (Tue, 12 May 2009) $
 */
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'includes'. DIRECTORY_SEPARATOR .'global.inc.php');
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'includes'. DIRECTORY_SEPARATOR .'CLI.inc.php');

/*

	Its a good idea to setup Admin -> Company -> New Hire Defaults before importing employee data.
	However if you forget, you can use this query to do it after the fact.

	begin; insert into user_preference (user_id,date_format,time_format,time_unit_format,time_zone,items_per_page,start_week_day,language,enable_email_notification_exception,enable_email_notification_message,enable_email_notification_home) select a.id as user_id,b.date_format,b.time_format,b.time_unit_format,b.time_zone,b.items_per_page,b.start_week_day,b.language,b.enable_email_notification_exception,b.enable_email_notification_message,b.enable_email_notification_home from users as a, user_default as b where a.company_id = b.company_id AND a.company_id = 1 and not exists( select * from user_preference as c where c.user_id = a.id); rollback;

*/


//
//
// Custom functions to parse each individual column
//
//
function parse_status_id( $input, $default_value = NULL, $parse_hint = NULL ) {


	if ( strtolower( $input ) == 'a'
			OR strtolower( $input ) == 'active' ) {
		$retval = 10;
	} elseif ( strtolower( $input ) == 't'
			OR strtolower( $input ) == 'terminated' ) {
		$retval = 20;
	} else {
		$retval = (int)$input;
	}

	return $retval;
}

function parse_title_id( $input, $default_value = NULL, $parse_hint = NULL ) {
	global $title_options, $fuzzy_match;

	if ( !is_numeric( $input ) ) {
		if ( $fuzzy_match == TRUE ) {
			$retval = Misc::findClosestMatch( $input, $title_options, 50 );
			//echo " Fuzzy Title - Search For: ". $input ." Found: ". @$branch_options[$retval];
			return $retval;
		} else {
			return array_search( $input, $title_options );
		}
	}

	return $input;
}

function parse_default_branch_id( $input, $default_value = NULL, $parse_hint = NULL ) {
	global $branch_options, $fuzzy_match;

	if ( !is_numeric( $input ) ) {
		if ( $fuzzy_match == TRUE ) {
			$retval = Misc::findClosestMatch( $input, $branch_options, 50 );
			//echo " Fuzzy Branch - Search For: ". $input ." Found: ". @$branch_options[$retval];
			return $retval;
		} else {
			return array_search( $input, $branch_options );
		}
	}

	return $input;
}

function parse_default_department_id( $input, $default_value = NULL, $parse_hint = NULL ) {
	global $department_options, $fuzzy_match;

	if ( !is_numeric( $input ) ) {
		if ( $fuzzy_match == TRUE ) {
			$retval = Misc::findClosestMatch( $input, $department_options, 50 );
			//echo " Fuzzy Department - Search For: ". $input ." Found: ". @$department_options[$retval];
			return $retval;
		} else {
			return array_search( $input, $department_options );
		}
	}

	return $input;
}

function parse_group_id( $input, $default_value = NULL, $parse_hint = NULL ) {
	global $group_options, $fuzzy_match;

	if ( !is_numeric( $input ) ) {
		if ( $fuzzy_match == TRUE ) {
			$retval = Misc::findClosestMatch( $input, $group_options, 50 );
			echo "\nFuzzy Group - Search For: ". $input ." Found: ". @$group_options[$retval] ."\n";
			return $retval;
		} else {
			return array_search( $input, $group_options );
		}
	}

	return $input;
}

function parse_first_name( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( strpos($input, ',') === FALSE ) {
		return $input;
	}

	$split_full_name = explode(',', $input);
	$first_name = $split_full_name[1];

	if ( strpos( $first_name, ' ') !== FALSE ) {
		$first_name = substr( $first_name, 0, strlen( $first_name )-2);
	}

	return $first_name;
}

function parse_last_name( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( strpos($input, ',') === FALSE ) {
		return $input;
	}

	$split_full_name = explode(',', $input);
	$last_name = $split_full_name[0];

	return $last_name;
}

function parse_home_phone( $input, $default_value = NULL, $parse_hint = NULL ) {
	$input = str_replace( array('/'), '-', $input);

	return $input;
}

function parse_work_phone( $input, $default_value = NULL, $parse_hint = NULL ) {
	$input = str_replace( array('/'), '-', $input);

	return $input;
}

function parse_sex( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( strtolower( $input ) == 'f'
			OR strtolower( $input ) == 'female' ) {
		$retval = 20;
	} else {
		$retval = 10;
	}

	return $retval;
}

function parse_birth_date( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( isset($parse_hint) AND $parse_hint != '' ) {
		TTDate::setDateFormat( $parse_hint );
	}

	return TTDate::getMiddleDayEpoch( TTDate::parseDateTime( $input ) );
}

function parse_hire_date( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( isset($parse_hint) AND $parse_hint != '' ) {
		TTDate::setDateFormat( $parse_hint );
	}

	return TTDate::getMiddleDayEpoch( TTDate::parseDateTime( $input ) );
}

function parse_termination_date( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( isset($parse_hint) AND $parse_hint != '' ) {
		TTDate::setDateFormat( $parse_hint );
	}

	return TTDate::parseDateTime( $input );
}

function parse_wage_effective_date( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( isset($parse_hint) AND $parse_hint != '' ) {
		TTDate::setDateFormat( $parse_hint );
	}

	return TTDate::parseDateTime( $input );
}

function parse_wage_type_id( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( strtolower( $input ) == 'salary' OR strtolower( $input ) == 'salaried' OR strtolower( $input ) == 's' OR strtolower( $input ) == 'annual' ) {
		$retval = 20;
	} elseif ( strtolower( $input ) == 'month' OR strtolower( $input ) == 'monthly') {
		$retval = 15;
	} elseif ( strtolower( $input ) == 'biweekly' OR strtolower( $input ) == 'bi-weekly') {
		$retval = 13;
	} elseif ( strtolower( $input ) == 'week' OR strtolower( $input ) == 'weekly') {
		$retval = 12;
	} else {
		$retval = 10;
	}

	return $retval;
}

function parse_wage_weekly_time( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( isset($parse_hint) AND $parse_hint != '' ) {
		TTDate::setTimeUnitFormat( $parse_hint );
	}

	$retval = TTDate::parseTimeUnit( $input );

	return $retval;
}

function parse_wage( $input, $default_value = NULL, $parse_hint = NULL ) {
	$val = new Validator();
	$retval = $val->stripNonFloat($input);

	return $retval;
}

function parse_bank_institution( $input, $default_value = NULL, $parse_hint = NULL ) {
	$val = new Validator();
	$retval = $val->stripNonNumeric($input);

	return $retval;
}
function parse_bank_transit( $input, $default_value = NULL, $parse_hint = NULL ) {
	$val = new Validator();
	$retval = $val->stripNonNumeric($input);

	return $retval;
}
function parse_bank_account( $input, $default_value = NULL, $parse_hint = NULL ) {
	$val = new Validator();
	$retval = $val->stripNonNumeric($input);

	return $retval;
}

function parse_federal_income_tax_user_value1( $input, $default_value = NULL, $parse_hint = NULL ) {
	if ( strtolower( $input ) == 'm'
			OR strtolower( $input ) == 'married' ) {
		$retval = 20;
	} else {
		$retval = 10;
	}

	return $retval;
}

function parse_province_income_tax_user_value1( $input, $default_value = NULL, $parse_hint = NULL ) {
/*

	10 => 'Single',
	20 => 'Married - Spouse Works',
	30 => 'Married - Spouse does not Work',
	40 => 'Head of Household',

*/
	if ( strtolower( $input ) == 'm2' ) {
		$retval = 30;

	} elseif ( strtolower( $input ) == 'm'
			OR strtolower( $input ) == 'm1'
			OR strtolower( $input ) == 'married' ) {
		$retval = 30;

	} elseif ( strtolower( $input ) == 'sm'
			OR strtolower( $input ) == 'head') {
		$retval = 40;

	} else {
		$retval = 10;
	}

	return $retval;
}

//
//
// Main
//
//
if ( $argc < 3 OR in_array ($argv[1], array('--help', '-help', '-h', '-?') ) ) {
	$help_output = "Usage: import_users.php [OPTIONS] [Column MAP file] [CSV File]\n";
	$help_output .= "\n";
	$help_output .= " *NOTICE* Its a good idea to configure Admin -> Company -> New Hire Defaults BEFORE importing employee information.";
	$help_output .= "\n";
	$help_output .= "  Options:\n";
	$help_output .= "    -u <unique column>	Update already existing users based on unique column, ie: employee_number,user_name,sin,\n";
	$help_output .= "    -cb 				Create branch if it doesn't exist\n";
	$help_output .= "    -cd 				Create department if it doesn't exist\n";
	$help_output .= "    -ct 				Create employee title if it doesn't exist\n";
	$help_output .= "    -cg 				Create employee group if it doesn't exist\n";
	$help_output .= "    -fuzzy 			Use fuzzy matching when searching for branches,departments,titles and groups\n";
	$help_output .= "    -n 				Dry-run, display the first two lines to confirm mapping is correct\n";

	echo $help_output;

} else {
	//FIXME: Use Pears Console_GetArgs package to handle these better.

	//Set timezone to your system timezone, otherwise dates can be off by the difference.
	TTDate::setTimeZone( 'GMT' );

	//Handle command line arguments
	$last_arg = count($argv)-1;

	if ( in_array('-n', $argv) ) {
		$dry_run = TRUE;
	} else {
		$dry_run = FALSE;
	}
	if ( in_array('-u', $argv) ) {
		$update_column = strtolower( trim($argv[array_search('-u', $argv)+1]) );

		if ( !in_array( $update_column, array('employee_number','sin','user_name') ) ) {
			echo "Invalid Unique Column<br>\n";
			exit;
		}
	} else {
		$update_column = FALSE;
	}

	if ( in_array('-cb', $argv) ) {
		$create_branch = TRUE;
	} else {
		$create_branch = FALSE;
	}

	if ( in_array('-cd', $argv) ) {
		$create_department = TRUE;
	} else {
		$create_department = FALSE;
	}

	if ( in_array('-ct', $argv) ) {
		$create_title = TRUE;
	} else {
		$create_title = FALSE;
	}

	if ( in_array('-cg', $argv) ) {
		$create_group = TRUE;
	} else {
		$create_group = FALSE;
	}

	if ( in_array('-fuzzy', $argv) ) {
		$fuzzy_match = TRUE;
	} else {
		$fuzzy_match = FALSE;
	}

	if ( isset($argv[$last_arg-1]) AND $argv[$last_arg-1] != '' ) {
		if ( !file_exists( $argv[$last_arg-1] ) OR !is_readable( $argv[$last_arg-1] ) ) {
			echo "Column MAP File: ". $argv[$last_arg-1] ." does not exists or is not readable!\n";
		} else {
			$column_map_file = $argv[$last_arg-1];
		}
	}

	if ( isset($argv[$last_arg]) AND $argv[$last_arg] != '' ) {
		if ( !file_exists( $argv[$last_arg] ) OR !is_readable( $argv[$last_arg] ) ) {
			echo "Import CSV File: ". $argv[$last_arg] ." does not exists or is not readable!\n";
		} else {
			$import_csv_file = $argv[$last_arg];
		}
	}

	if ( !isset($column_map_file) ) {
		echo "Column Map File not set!<br>\n";
		exit;
	}

	//Import map file, confirm it is correct.
	$import_map_arr = Misc::parseCSV( $column_map_file, TRUE );

	if ( !is_array( $import_map_arr ) ) {
		echo "Parsing column map file failed!\n";
	} else {
		echo "Column Mappings...\n";

		foreach( $import_map_arr as $map_cols ) {
			if ( $map_cols['csv_column'] == '' ) {
				$map_cols['csv_column'] = $map_cols['timetrex_column'];
			}

			if ( ( isset( $map_cols['csv_column'] ) AND isset($map_cols['default_value'])  )
					AND ( $map_cols['csv_column'] != '' OR $map_cols['default_value'] != '' ) ) {
				echo "  TimeTrex Column: ". $map_cols['timetrex_column'] ." => ". $map_cols['csv_column'] ." Default: ". $map_cols['default_value'] ."\n";

				$filtered_import_map[$map_cols['timetrex_column']] = array(
												'timetrex_column' => $map_cols['timetrex_column'],
												'csv_column' => $map_cols['csv_column'],
												'default_value' => $map_cols['default_value'],
												'parse_hint' => $map_cols['parse_hint'],
												);
			} else {
				echo "  TimeTrex Column: ". $map_cols['timetrex_column'] ." => Skipping...\n";
			}
		}
		unset($import_map_arr, $map_cols);
		//var_dump($filtered_import_map);
	}


	if ( $dry_run == TRUE ) {
		//Import first two lines of CSV file to display for testing.
		$import_arr = Misc::parseCSV( $import_csv_file, TRUE, FALSE, ",", 9216, 2 );

		if ( !is_array( $import_arr ) ) {
			echo "Parsing CSV file failed!\n";
		} else {
			echo "Sample Users...\n";

			$i=1;
			foreach( $import_arr as $tmp_import_arr ) {
				$mapped_row = Misc::importApplyColumnMap( $filtered_import_map, $tmp_import_arr );

				echo "  Sample User: $i\n";

				foreach( $mapped_row as $column => $value ) {
					echo "    $column: $value\n";
				}
				$i++;
			}
		}

		unset($import_arr, $mapped_row, $column, $value, $tmp_import_arr, $i);
	}


	//Import all data
	$import_arr = Misc::parseCSV( $import_csv_file, TRUE, FALSE, ",", 9216, 0 );

	if ( !is_array( $import_arr ) ) {
		echo "Parsing CSV file failed!\n";
	} else {
		echo "Importing Users...\n";

		$uf = new UserFactory();
		$uf->StartTransaction();

		$commit_trans = TRUE;

		$i=1;
		$e=0;
		foreach( $import_arr as $tmp_import_arr ) {
			$mapped_row = Misc::importApplyColumnMap( $filtered_import_map, $tmp_import_arr );

			$uf = new UserFactory();

			//Start with user default values.
			$udlf = new UserDefaultListFactory();
			$udlf->getByCompanyId( $mapped_row['company_id'] );
			if ( $udlf->getRecordCount() > 0 ) {
				Debug::Text('Using User Defaults', __FILE__, __LINE__, __METHOD__,10);
				$udf_obj = $udlf->getCurrent();

				$uf->setTitle( $udf_obj->getTitle() );
				$uf->setCity( $udf_obj->getCity() );
				if ( $udf_obj->getProvince() != '' AND $udf_obj->getProvince() != 0 ) {
					$uf->setProvince( $udf_obj->getProvince() );
				}
				if ( $udf_obj->getCountry() != '' ) {
					$uf->setCountry( $udf_obj->getCountry() );
				}
				$uf->setWorkPhone( $udf_obj->getWorkPhone() );
				$uf->setWorkPhoneExt( $udf_obj->getWorkPhoneExt() );
				$uf->setWorkEmail( $udf_obj->getWorkEmail() );
				$uf->setHireDate( $udf_obj->getHireDate() );
				$uf->setDefaultBranch( $udf_obj->getDefaultBranch() );
				$uf->setDefaultDepartment( $udf_obj->getDefaultDepartment() );

				if ( $udf_obj->getPermissionControl() != '' ) {
					$uf->setPermissionControl( $udf_obj->getPermissionControl() );
				}

				if ( $udf_obj->getPayPeriodSchedule() != '' ) {
					$uf->setPayPeriodSchedule( $udf_obj->getPayPeriodSchedule() );
				}

				if ( $udf_obj->getPolicyGroup() != '' ) {
					$uf->setPolicyGroup( $udf_obj->getPolicyGroup() );
				}

				if ( $udf_obj->getCurrency() != '' ) {
					$uf->setCurrency( $udf_obj->getCurrency() );
				}
			}

			if ( $update_column != FALSE AND isset($mapped_row[$update_column]) ) {
				//Try looking up user by the update column.
				echo "  Looking up User By: $update_column (". $mapped_row[$update_column] .") - ". str_pad( $mapped_row['first_name'] ." ". $mapped_row['last_name'] , 20, '.', STR_PAD_RIGHT)."... ";

				$ulf = new UserListFactory();
				$ulf->getSearchByCompanyIdAndArrayCriteria( $mapped_row['company_id'], array( $update_column => $mapped_row[$update_column] ) );
				if ( $ulf->getRecordCount() == 1 ) {
					echo " Found 1 User\n";
					$uf = $ulf->getCurrent();
				} elseif ( $ulf->getRecordCount() == 0 ) {
					echo " User Not Found, Inserting...\n";
				} else {
					echo " More Than One User Found, Skipping!\n";
					continue;
				}
			} elseif ( $update_column != FALSE AND !isset($mapped_row[$update_column]) ) {
				echo " Update Column Not Found In CSV File!\n";
			}

			echo "  Importing User: $i. ". str_pad( Misc::importCallInputParseFunction( 'first_name', $mapped_row['first_name'], $filtered_import_map['first_name']['default_value'], $filtered_import_map['first_name']['parse_hint'] ) ." ". Misc::importCallInputParseFunction( 'last_name', $mapped_row['last_name'], $filtered_import_map['last_name']['default_value'], $filtered_import_map['last_name']['parse_hint'] ) , 30, '.', STR_PAD_RIGHT)."... ";
			if ( isset($mapped_row['company_id']) AND $mapped_row['company_id'] != '' ) {
				//Get all branches
				$blf = new BranchListFactory();
				$blf->getByCompanyId( $mapped_row['company_id'] );
				$branch_options = (array)$blf->getArrayByListFactory( $blf, FALSE, TRUE );
				unset($blf);

				//Get departments
				$dlf = new DepartmentListFactory();
				$dlf->getByCompanyId( $mapped_row['company_id'] );
				$department_options = (array)$dlf->getArrayByListFactory( $dlf, FALSE, TRUE );
				unset($dlf);

				//Get groups
				$uglf = new UserGroupListFactory();
				$uglf->getByCompanyId( $mapped_row['company_id'] );
				$group_options = (array)$uglf->getArrayByListFactory( $uglf, FALSE, TRUE );
				unset($uglf);

				//Get job titles
				$utlf = new UserTitleListFactory();
				$utlf->getByCompanyId( $mapped_row['company_id'] );
				$title_options = (array)$utlf->getArrayByListFactory( $utlf, FALSE, TRUE );
				unset($utlf);

				$uf->setCompany( Misc::importCallInputParseFunction( 'company_id', $mapped_row['company_id'], $filtered_import_map['company_id']['default_value'], $filtered_import_map['company_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['user_name']) AND $mapped_row['user_name'] != '' ) {
				$uf->setUserName($mapped_row['user_name']);
			} elseif ( isset($mapped_row['first_name']) AND $mapped_row['first_name'] != ''
						AND isset($mapped_row['last_name']) AND $mapped_row['last_name'] != '' ) {

				//Get rid of special chars
				$tmp_first_name = $uf->Validator->stripNonAlphaNumeric( Misc::importCallInputParseFunction( 'first_name', $mapped_row['first_name'], $filtered_import_map['first_name']['default_value'], $filtered_import_map['first_name']['parse_hint'] ) );
				$tmp_last_name = $uf->Validator->stripNonAlphaNumeric( Misc::importCallInputParseFunction( 'last_name', $mapped_row['last_name'], $filtered_import_map['last_name']['default_value'], $filtered_import_map['last_name']['parse_hint'] ) );

				$tmp_user_name = strtolower($tmp_first_name.'.'.$tmp_last_name);
				//Make sure user name is unique
				if ( $uf->isUniqueUserName( $tmp_user_name ) == FALSE ) {
					$tmp_user_name = strtolower($tmp_first_name.'.'.$tmp_last_name.rand(1,99) );
				}

				$uf->setUserName( $tmp_user_name );

				unset($tmp_user_name, $tmp_first_name, $tmp_middle_name, $tmp_last_name);
			}

			if ( isset($mapped_row['status_id']) AND $mapped_row['status_id'] != '' ) {
				$uf->setStatus( Misc::importCallInputParseFunction( 'status_id', $mapped_row['status_id'], $filtered_import_map['status_id']['default_value'], $filtered_import_map['status_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['currency_id']) AND $mapped_row['currency_id'] != '' ) {
				$uf->setCurrency( Misc::importCallInputParseFunction( 'currency_id', $mapped_row['currency_id'], $filtered_import_map['currency_id']['default_value'], $filtered_import_map['currency_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['employee_number']) AND $mapped_row['employee_number'] != '' ) {
				$uf->setEmployeeNumber( Misc::importCallInputParseFunction( 'employee_number', $mapped_row['employee_number'], $filtered_import_map['employee_number']['default_value'], $filtered_import_map['employee_number']['parse_hint'] ) );
			} else {
				$ulf = new UserListFactory();
				$ulf->getHighestEmployeeNumberByCompanyId( $mapped_row['company_id'] );
				if ( $ulf->getRecordCount() > 0 ) {
					$next_employee_number = $ulf->getCurrent()->getEmployeeNumber()+1;
					$uf->setEmployeeNumber( $next_employee_number );
				}
				unset($ulf, $next_employee_number);
			}

			if ( isset($mapped_row['pay_period_schedule_id']) AND $mapped_row['pay_period_schedule_id'] != '' ) {
				$uf->setPayPeriodSchedule( Misc::importCallInputParseFunction( 'pay_period_schedule_id', $mapped_row['pay_period_schedule_id'], $filtered_import_map['pay_period_schedule_id']['default_value'], $filtered_import_map['pay_period_schedule_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['policy_group_id']) AND $mapped_row['policy_group_id'] != '' ) {
				$uf->setPolicyGroup( Misc::importCallInputParseFunction( 'policy_group_id', $mapped_row['policy_group_id'], $filtered_import_map['policy_group_id']['default_value'], $filtered_import_map['policy_group_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['default_branch_id']) AND $mapped_row['default_branch_id'] != '' ) {
				if ( $create_branch === TRUE AND !is_numeric($mapped_row['default_branch_id']) ) {
					//Check to see if branch exists or not.
					//if ( array_search( $mapped_row['default_branch_id'], $branch_options ) === FALSE ) {
					if ( parse_default_branch_id( $mapped_row['default_branch_id'] ) === FALSE ) {
						//Create branch
						$bf = new BranchFactory();
						$bf->setCompany( $mapped_row['company_id'] );
						$bf->setStatus( 10 );
						$next_available_manual_id = BranchListFactory::getNextAvailableManualId( $mapped_row['company_id'] );
						$bf->setManualId( $next_available_manual_id );
						$bf->setName( $mapped_row['default_branch_id'] );
						$bf->setCity( 'NA' );
						if ( $bf->isValid() ) {
							echo "[CB: ". $mapped_row['default_branch_id'] ."]";
							$new_branch_id = $bf->Save();
							$branch_options[$new_branch_id] = $mapped_row['default_branch_id'];
						}
						unset($bf, $new_branch_id, $next_available_manual_id );
					}
				}

				$uf->setDefaultBranch( Misc::importCallInputParseFunction( 'default_branch_id', $mapped_row['default_branch_id'], $filtered_import_map['default_branch_id']['default_value'], $filtered_import_map['default_branch_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['default_department_id']) AND $mapped_row['default_department_id'] != '' ) {
				if ( $create_department === TRUE AND !is_numeric($mapped_row['default_department_id']) ) {
					//Check to see if department exists or not.
					if ( array_search( $mapped_row['default_department_id'], $department_options ) === FALSE ) {
						//Create department
						$df = new DepartmentFactory();
						$df->setCompany( $mapped_row['company_id'] );
						$df->setStatus( 10 );
						$df->setName( $mapped_row['default_department_id'] );
						$next_available_manual_id = DepartmentListFactory::getNextAvailableManualId( $mapped_row['company_id'] );
						$df->setManualId( $next_available_manual_id );
						if ( $df->isValid() ) {
							echo "[CD: ". $mapped_row['default_department_id'] ."]";
							$new_department_id = $df->Save();
							$department_options[$new_department_id] = $mapped_row['default_department_id'];
						}
						unset($df, $new_department_id, $next_available_manual_id);
					}
				}

				$uf->setDefaultDepartment( Misc::importCallInputParseFunction( 'default_department_id', $mapped_row['default_department_id'], $filtered_import_map['default_department_id']['default_value'], $filtered_import_map['default_department_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['group_id']) AND $mapped_row['group_id'] != '' ) {
				if ( $create_group === TRUE AND !is_numeric($mapped_row['group_id']) ) {
					//Check to see if branch exists or not.
					if ( array_search( $mapped_row['group_id'], $group_options ) === FALSE ) {
						$ugf = new UserGroupFactory();
						$ugf->setCompany( $mapped_row['company_id'] );
						$ugf->setParent( 0 );
						$ugf->setName( $mapped_row['group_id'] );

						if ( $ugf->isValid() ) {
							echo "[CG: ". $mapped_row['group_id'] ."]";
							$new_group_id = $ugf->Save();
							$group_options[$new_group_id] = $mapped_row['group_id'];
						}
						unset($ugf, $new_group_id);
					}
				}

				$uf->setGroup( Misc::importCallInputParseFunction( 'group_id', $mapped_row['group_id'], $filtered_import_map['group_id']['default_value'], $filtered_import_map['group_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['title_id']) AND $mapped_row['title_id'] != '' ) {
				if ( $create_title === TRUE AND !is_numeric($mapped_row['title_id']) ) {
					//Check to see if title exists or not.
					if ( array_search( $mapped_row['title_id'], $title_options ) === FALSE ) {
						//Create title
						$utf = new UserTitleFactory();
						$utf->setCompany( $mapped_row['company_id'] );
						$utf->setName( $mapped_row['title_id'] );
						if ( $utf->isValid() ) {
							echo "[CT: ". $mapped_row['title_id'] ."]";
							$new_title_id = $utf->Save();
							$title_options[$new_title_id] = $mapped_row['title_id'];
						}
						unset($utf, $new_title_id);
					}
				}

				$uf->setTitle( Misc::importCallInputParseFunction( 'title_id', $mapped_row['title_id'], $filtered_import_map['title_id']['default_value'], $filtered_import_map['title_id']['parse_hint'] ) );
			}

			if ( isset($mapped_row['password']) AND $mapped_row['password'] != '' ) {
				$uf->setPassword( $mapped_row['password'] );
			} else {
				$uf->setPassword( uniqid() );
			}

			if ( isset($mapped_row['first_name']) AND $mapped_row['first_name'] != '' ) {
				$uf->setFirstName( Misc::importCallInputParseFunction( 'first_name', $mapped_row['first_name'], $filtered_import_map['first_name']['default_value'], $filtered_import_map['first_name']['parse_hint'] ) );
			}
			if ( isset($mapped_row['middle_name']) AND $mapped_row['middle_name'] != '' ) {
				$uf->setMiddleName( Misc::importCallInputParseFunction( 'middle_name', $mapped_row['middle_name'], $filtered_import_map['middle_name']['default_value'], $filtered_import_map['middle_name']['parse_hint'] ) );
			}
			if ( isset($mapped_row['last_name']) AND $mapped_row['last_name'] != '' ) {
				$uf->setLastName( Misc::importCallInputParseFunction( 'last_name', $mapped_row['last_name'], $filtered_import_map['last_name']['default_value'], $filtered_import_map['last_name']['parse_hint'] ) );
			}

			if ( isset($mapped_row['sex']) AND $mapped_row['sex'] != '' ) {
				$uf->setSex( Misc::importCallInputParseFunction( 'sex', $mapped_row['sex'], $filtered_import_map['sex']['default_value'], $filtered_import_map['sex']['parse_hint'] ) );
			} else {
				$uf->setSex(10);
			}

			if ( isset($mapped_row['address1']) AND $mapped_row['address1'] != '' ) {
				$uf->setAddress1($mapped_row['address1']);
			}
			if ( isset($mapped_row['address2']) AND $mapped_row['address2'] != '' ) {
				$uf->setAddress2($mapped_row['address2']);
			}

			if ( isset($mapped_row['city']) AND $mapped_row['city'] != '' ) {
				$uf->setCity($mapped_row['city']);
			}
			if ( isset($mapped_row['country']) AND $mapped_row['country'] != '' ) {
				$uf->setCountry($mapped_row['country']);
			}
			if ( isset($mapped_row['province']) AND $mapped_row['province'] != '' ) {
				$uf->setProvince($mapped_row['province']);
			}
			if ( isset($mapped_row['postal_code']) AND $mapped_row['postal_code'] != '' ) {
				$uf->setPostalCode($mapped_row['postal_code']);
			}
			if ( isset($mapped_row['home_phone']) AND $mapped_row['home_phone'] != '' ) {
				$uf->setHomePhone( Misc::importCallInputParseFunction( 'home_phone', $mapped_row['home_phone'], $filtered_import_map['home_phone']['default_value'], $filtered_import_map['home_phone']['parse_hint'] ) );
			}
			if ( isset($mapped_row['mobile_phone']) AND $mapped_row['mobile_phone'] != '' ) {
				$uf->setMobilePhone($mapped_row['mobile_phone']);
			}
			if ( isset($mapped_row['fax_phone']) AND $mapped_row['fax_phone'] != '' ) {
				$uf->setFaxPhone($mapped_row['fax_phone']);
			}

			if ( isset($mapped_row['work_phone']) AND $mapped_row['work_phone'] != '' ) {
				$uf->setWorkPhone( Misc::importCallInputParseFunction( 'work_phone', $mapped_row['work_phone'], $filtered_import_map['work_phone']['default_value'], $filtered_import_map['work_phone']['parse_hint'] ));
			}
			if ( isset($mapped_row['work_phone_ext']) AND $mapped_row['work_phone_ext'] != '' ) {
				$uf->setWorkPhoneExt($mapped_row['work_phone_ext']);
			}

			if ( isset($mapped_row['home_email']) AND $mapped_row['home_email'] != '' ) {
				$uf->setHomeEmail($mapped_row['home_email']);
			}
			if ( isset($mapped_row['work_email']) AND $mapped_row['work_email'] != '' ) {
				$uf->setWorkEmail($mapped_row['work_email']);
			}

			if ( isset($mapped_row['sin']) AND $mapped_row['sin'] != '' ) {
				$uf->setSin($mapped_row['sin']);
			}

			if ( isset($mapped_row['birth_date']) AND $mapped_row['birth_date'] != '' ) {
				$uf->setBirthDate( Misc::importCallInputParseFunction( 'birth_date', $mapped_row['birth_date'], $filtered_import_map['birth_date']['default_value'], $filtered_import_map['birth_date']['parse_hint'] ) );
			}

			if ( isset($mapped_row['hire_date']) AND $mapped_row['hire_date'] != '' ) {
				$uf->setHireDate( Misc::importCallInputParseFunction( 'hire_date', $mapped_row['hire_date'], $filtered_import_map['hire_date']['default_value'], $filtered_import_map['hire_date']['parse_hint'] ) );
			}

			if ( isset($mapped_row['termination_date']) AND $mapped_row['termination_date'] != '' ) {
				$uf->setTerminationDate( Misc::importCallInputParseFunction( 'termination_date', $mapped_row['termination_date'], $filtered_import_map['termination_date']['default_value'], $filtered_import_map['termination_date']['parse_hint'] ) );
			}

			if ( isset($mapped_row['note']) AND $mapped_row['note'] != '' ) {
				$uf->setNote($mapped_row['note']);
			}

			if ( $uf->isValid() == TRUE ) {
				$user_id = $uf->Save(FALSE);
				if ( $user_id === TRUE ) {
					$user_id = $uf->getId();
				}

				if ( $user_id === FALSE ) {
					echo " \t\t\tFailed!\n";
					$commit_trans = FALSE;
					$e++;
				} else {
					echo " \t\t\tSuccess!\n";

					if ( isset($mapped_row['wage_type_id']) AND $mapped_row['wage_type_id'] != ''
							AND isset($mapped_row['wage']) AND $mapped_row['wage'] != ''
							AND isset($mapped_row['wage_effective_date']) AND $mapped_row['wage_effective_date'] != '') {
						echo "    Importing User Wage Information...";

						//Import Salary information
						$wage_effective_date = Misc::importCallInputParseFunction( 'wage_effective_date', $mapped_row['wage_effective_date'], $filtered_import_map['wage_effective_date']['default_value'], $filtered_import_map['wage_effective_date']['parse_hint'] );

						$uwlf = new UserWageListFactory();
						$uwlf->getByUserIdAndStartDateAndEndDate( $user_id, $wage_effective_date, $wage_effective_date );
						if ( $uwlf->getRecordCount() == 1 ) {
							$uwf = $uwlf->getCurrent();
							echo "(U) ";
						} else {
							$uwf = new UserWageFactory();
						}

						$uwf->setUser( $user_id );

						if ( isset($mapped_row['wage_type_id']) AND $mapped_row['wage_type_id'] != '' ) {
							$uwf->setType( Misc::importCallInputParseFunction( 'wage_type_id', $mapped_row['wage_type_id'], $filtered_import_map['wage_type_id']['default_value'], $filtered_import_map['wage_type_id']['parse_hint'] ) );
						}

						if ( isset($mapped_row['wage']) AND $mapped_row['wage'] != '' ) {
							$uwf->setWage( Misc::importCallInputParseFunction( 'wage', $mapped_row['wage'], $filtered_import_map['wage']['default_value'], $mapped_row['wage_type_id'] ) );
						}

						if ( $uwf->getType() == 20 ) {
							if ( isset($mapped_row['wage_weekly_time']) AND $mapped_row['wage_weekly_time'] != '' ) {
								$uwf->setWeeklyTime( Misc::importCallInputParseFunction( 'wage_weekly_time', $mapped_row['wage_weekly_time'], $filtered_import_map['wage_weekly_time']['default_value'], $filtered_import_map['wage_weekly_time']['parse_hint'] ) );
							}
						}

						if ( isset($mapped_row['labor_burden_percent']) AND $mapped_row['labor_burden_percent'] != '' ) {
							$uwf->setLaborBurdenPercent( Misc::importCallInputParseFunction( 'labor_burden_percent', $mapped_row['labor_burden_percent'], $filtered_import_map['labor_burden_percent']['default_value'], $filtered_import_map['labor_burden_percent']['parse_hint'] ) );
						}

						if ( isset($mapped_row['wage_effective_date']) AND $mapped_row['wage_effective_date'] != '' ) {
							$uwf->setEffectiveDate( $wage_effective_date );
							echo "Effective: ". TTDate::getDate('DATE', Misc::importCallInputParseFunction( 'wage_effective_date', $mapped_row['wage_effective_date'], $filtered_import_map['wage_effective_date']['default_value'], $filtered_import_map['wage_effective_date']['parse_hint'] ) );
						} else {
							$uwf->setEffectiveDate( time() );
							echo "Effective: ". TTDate::getDate('DATE', time() );
						}
						unset($wage_effective_date);

						if ( $uwf->isValid() ) {
							$uwf->Save();
							echo " \t\t\tSuccess!\n";
						} else {
							echo " \t\t\tFailed!\n";
							$commit_trans = FALSE;
							$e++;


							$errors = $uwf->Validator->getErrorsArray();
							if ( is_array($errors) ) {
								foreach( $errors as $error_arr ) {
									echo "      ERROR: ". $error_arr[0] ."\n";
								}
							}
						}
					}

					if ( isset($mapped_row['bank_account']) AND $mapped_row['bank_account'] != '' ) {
						echo "    Importing User Bank Information...";

						if ( isset($mapped_row['bank_transit']) ) {
							$bank['transit'] = Misc::importCallInputParseFunction( 'bank_transit', $mapped_row['bank_transit'], $filtered_import_map['bank_transit']['default_value'], $filtered_import_map['bank_transit']['parse_hint'] );
						}
						if ( isset($mapped_row['bank_institution']) ) {
							$bank['institution'] = Misc::importCallInputParseFunction( 'bank_institution', $mapped_row['bank_institution'], $filtered_import_map['bank_institution']['default_value'], $filtered_import_map['bank_institution']['parse_hint'] );
						}
						$bank['account'] = Misc::importCallInputParseFunction( 'bank_account', $mapped_row['bank_account'], $filtered_import_map['bank_account']['default_value'], $filtered_import_map['bank_account']['parse_hint'] );

						//Support a single bank account column and split into Institution/Transit/Account or Routing/Account
						//Based on country.
						if ( strtolower($filtered_import_map['bank_account']['parse_hint']) == 'combined' ) {
							if ( $uf->getCountry() == 'CA' ) {
								echo " (CA) ";
								$bank['institution'] = substr( $bank['account'], 0, 3);
								$bank['transit'] = substr( $bank['account'], 4, 5);
								$bank['account'] = substr( $bank['account'], 7, 100);
							} else {
								echo " (US) ";
								$bank['transit'] = substr( $bank['account'], 0, 9);
								$bank['account'] = substr( $bank['account'], 8, 100);
							}
						}
						echo 'Institution: '. $bank['institution'] .' Transit: '. $bank['transit'] .' Account: '. $bank['account'];

						$balf = new BankAccountListFactory();
						$balf->getUserAccountByCompanyIdAndUserId( $mapped_row['company_id'], $user_id );
						if ( $balf->getRecordCount() == 1 ) {
							$baf = $balf->getCurrent();
							echo "(U) ";
						} else {
							$baf = new BankAccountFactory();
						}

						$baf->setCompany( $mapped_row['company_id'] );
						$baf->setUser( $user_id );

						if ( $bank['institution'] != '' ) {
							$baf->setInstitution( $bank['institution'] );
						}
						$baf->setTransit( $bank['transit'] );
						$baf->setAccount( $bank['account'] );

						if ( $baf->isValid() ) {
							$baf->Save();
							echo " \t\t\tSuccess!\n";
						} else {
							echo " \t\t\tFailed!\n";
							$commit_trans = FALSE;
							$e++;


							$errors = $baf->Validator->getErrorsArray();
							if ( is_array($errors) ) {
								foreach( $errors as $error_arr ) {
									echo "      ERROR: ". $error_arr[0] ."\n";
								}
							}
						}

						unset($bank);
					}

					if ( $update_column == FALSE AND isset($mapped_row['federal_income_tax_deduction_id']) AND $mapped_row['federal_income_tax_deduction_id'] != '' ) {
						echo "    Importing Federal Income Tax Information...";

						$cdlf = new CompanyDeductionListFactory();
						if ( is_numeric( $mapped_row['federal_income_tax_deduction_id'] ) ) {
							$cdlf->getByCompanyIdAndId( $mapped_row['company_id'], $mapped_row['federal_income_tax_deduction_id'] );
						} else {
							$cdlf->getByCompanyIdAndName( $mapped_row['company_id'], $mapped_row['federal_income_tax_deduction_id'] );
						}

						if ( $cdlf->getRecordCount() > 0 ) {
							$cd_obj = $cdlf->getCurrent();

							$udf = new UserDeductionFactory();
							$udf->setUser( $user_id );
							$udf->setCompanyDeduction( $cd_obj->getId() );

							if ( isset($mapped_row['federal_income_tax_user_value1']) AND $mapped_row['federal_income_tax_user_value1'] != '' ) {
								$udf->setUserValue1( Misc::importCallInputParseFunction( 'federal_income_tax_user_value1', $mapped_row['federal_income_tax_user_value1'], $filtered_import_map['federal_income_tax_user_value1']['default_value'], $filtered_import_map['federal_income_tax_user_value1']['parse_hint'] ) );
							}

							if ( isset($mapped_row['federal_income_tax_user_value2']) AND $mapped_row['federal_income_tax_user_value2'] != '' ) {
								$udf->setUserValue2( Misc::importCallInputParseFunction( 'federal_income_tax_user_value2', $mapped_row['federal_income_tax_user_value2'], $filtered_import_map['federal_income_tax_user_value2']['default_value'], $filtered_import_map['federal_income_tax_user_value2']['parse_hint'] ) );
							}

							if ( isset($mapped_row['federal_income_tax_user_value3']) and $mapped_row['federal_income_tax_user_value3'] != '' ) {
								$udf->setuserValue3( Misc::importCallInputParseFunction( 'federal_income_tax_user_value3', $mapped_row['federal_income_tax_user_value3'], $filtered_import_map['federal_income_tax_user_value3']['default_value'], $filtered_import_map['federal_income_tax_user_value3']['parse_hint'] ) );
							}

							if ( isset($mapped_row['federal_income_tax_user_value4']) and $mapped_row['federal_income_tax_user_value4'] != '' ) {
								$udf->setuserValue4( Misc::importCallInputParseFunction( 'federal_income_tax_user_value4', $mapped_row['federal_income_tax_user_value4'], $filtered_import_map['federal_income_tax_user_value4']['default_value'], $filtered_import_map['federal_income_tax_user_value4']['parse_hint'] ) );
							}

							if ( isset($mapped_row['federal_income_tax_user_value5']) AND $mapped_row['federal_income_tax_user_value5'] != '' ) {
								$udf->setUserValue5( Misc::importCallInputParseFunction( 'federal_income_tax_user_value5', $mapped_row['federal_income_tax_user_value5'], $filtered_import_map['federal_income_tax_user_value5']['default_value'], $filtered_import_map['federal_income_tax_user_value5']['parse_hint'] ) );
							}

							if ( $udf->isValid() ) {
								$udf->Save();
								echo " \t\t\t\tSuccess!\n";

							} else {
								echo " \t\t\t\tFailed!\n";
								$commit_trans = FALSE;
								$e++;


								$errors = $udf->Validator->getErrorsArray();
								if ( is_array($errors) ) {
									foreach( $errors as $error_arr ) {
										echo "      ERROR: ". $error_arr[0] ."\n";
									}
								}
							}
						} else {
							echo " \t\t\t\tFailed!\n";
							$commit_trans = FALSE;
							$e++;

							echo "      ERROR: Company Deduction Not Found!\n";
						}

					}

					if ( $update_column == FALSE AND isset($mapped_row['province_income_tax_deduction_id']) AND $mapped_row['province_income_tax_deduction_id'] != ''
							AND isset($mapped_row['province_income_tax_user_value1']) AND $mapped_row['province_income_tax_user_value1'] != '' ) {
						echo "    Importing Provincial/State Income Tax Information...";

						$cdlf = new CompanyDeductionListFactory();
						if ( is_numeric( $mapped_row['province_income_tax_deduction_id'] ) ) {
							$cdlf->getByCompanyIdAndId( $mapped_row['company_id'], $mapped_row['province_income_tax_deduction_id'] );
						} else {
							$cdlf->getByCompanyIdAndName( $mapped_row['company_id'], $mapped_row['province_income_tax_deduction_id'] );
						}

						if ( $cdlf->getRecordCount() > 0 ) {
							$cd_obj = $cdlf->getCurrent();

							$udf = new UserDeductionFactory();
							$udf->setUser( $user_id );
							$udf->setCompanyDeduction( $cd_obj->getId() );

							if ( isset($mapped_row['province_income_tax_user_value1']) AND $mapped_row['province_income_tax_user_value1'] != '' ) {
								$udf->setUserValue1( Misc::importCallInputParseFunction( 'province_income_tax_user_value1', $mapped_row['province_income_tax_user_value1'], $filtered_import_map['province_income_tax_user_value1']['default_value'], $filtered_import_map['province_income_tax_user_value1']['parse_hint'] ) );
							}

							if ( isset($mapped_row['province_income_tax_user_value2']) AND $mapped_row['province_income_tax_user_value2'] != '' ) {
								$udf->setUserValue2( Misc::importCallInputParseFunction( 'province_income_tax_user_value2', $mapped_row['province_income_tax_user_value2'], $filtered_import_map['province_income_tax_user_value2']['default_value'], $filtered_import_map['province_income_tax_user_value2']['parse_hint'] ) );
							}

							if ( isset($mapped_row['province_income_tax_user_value3']) and $mapped_row['province_income_tax_user_value3'] != '' ) {
								$udf->setuserValue3( Misc::importCallInputParseFunction( 'province_income_tax_user_value3', $mapped_row['province_income_tax_user_value3'], $filtered_import_map['province_income_tax_user_value3']['default_value'], $filtered_import_map['province_income_tax_user_value3']['parse_hint'] ) );
							}

							if ( isset($mapped_row['province_income_tax_user_value4']) and $mapped_row['province_income_tax_user_value4'] != '' ) {
								$udf->setuserValue4( Misc::importCallInputParseFunction( 'province_income_tax_user_value4', $mapped_row['province_income_tax_user_value4'], $filtered_import_map['province_income_tax_user_value4']['default_value'], $filtered_import_map['province_income_tax_user_value4']['parse_hint'] ) );
							}

							if ( isset($mapped_row['province_income_tax_user_value5']) AND $mapped_row['province_income_tax_user_value5'] != '' ) {
								$udf->setUserValue5( Misc::importCallInputParseFunction( 'province_income_tax_user_value5', $mapped_row['province_income_tax_user_value5'], $filtered_import_map['province_income_tax_user_value5']['default_value'], $filtered_import_map['province_income_tax_user_value5']['parse_hint'] ) );
							}

							if ( $udf->isValid() ) {
								$udf->Save();
								echo " \t\t\tSuccess!\n";

							} else {
								echo " \t\t\tFailed!\n";
								$commit_trans = FALSE;
								$e++;


								$errors = $udf->Validator->getErrorsArray();
								if ( is_array($errors) ) {
									foreach( $errors as $error_arr ) {
										echo "      ERROR: ". $error_arr[0] ."\n";
									}
								}
							}
						} else {
							echo " \t\t\tFailed!\n";
							$commit_trans = FALSE;
							$e++;

							echo "      ERROR: Company Deduction Not Found!\n";
						}

					}

				}
			} else {
				echo " \t\t\t\t\tFailed!\n";
				$commit_trans = FALSE;
				$e++;

				$errors = $uf->Validator->getErrorsArray();
				if ( is_array($errors) ) {
					foreach( $errors as $error_arr ) {
						echo "    ERROR: ". $error_arr[0] ."\n";
					}
				}
			}

			ob_flush();
			flush();
			$i++;
		}

		if ( $e > 0 ) {
			echo "Total Errors: ". $e ."\n";
		}

		if ( $dry_run == TRUE OR $commit_trans !== TRUE ) {
			echo "Rolling back transaction!\n";
			$uf->FailTransaction();
		}
		//$uf->FailTransaction();
		$uf->CommitTransaction();
	}

}

echo "WARNING: Clear TimeTrex cache after running this.\n";

//Debug::Display();
?>
