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
 * $Revision: 2858 $
 * $Id: DemoData.class.php 2858 2009-09-29 18:12:05Z ipso $
 * $Date: 2009-09-29 11:12:05 -0700 (Tue, 29 Sep 2009) $
 */


/**
 * @package Module_Other
 */
class DemoData {

	protected $user_name_postfix = '1';
	protected $user_name_prefix = 'demo';
	protected $admin_user_name_prefix = 'demoadmin';
	protected $password = 'demo';
	protected $max_random_users = 0;

	protected $first_names = array(
									'Sidney',
									'Vi',
									'Lena',
									'Carlee',
									'Mohammad',
									'Pat',
									'Lashell',
									'Denis',
									'Jeffry',
									'Cleo',
									'Nikia',
									'Vallie',
									'Shari',
									'Daniel',
									'Laurena',
									'Elbert',
									'Cortney',
									'Ferne',
									'Willetta',
									'Mitzi',
									'Stacey',
									'Mireya',
									'Reita',
									'Rivka',
									'Tu',
									'Hiram',
									'Giuseppina',
									'Reda',
									'Dion',
									'Izola',
									'Bobbye',
									'Chanelle',
									'Clemmie',
									'Karri',
									'Kylee',
									'Gillian',
									'Octavia',
									'Marielle',
									'Romelia',
									'Stephania',
									'Sherryl',
									'Malka',
									'Kristan',
									'Jolynn',
									'Star',
									'Cinthia',
									'Vern',
									'Junko',
									'Felipa',
									'Alayna',
									'Lorenzo',
									'Agnus',
									'Hyman',
									'Floretta',
									'Rosella',
									'Sabina',
									'Regan',
									'Yu',
									'Muoi',
									'Tomiko',
									'Ada',
									'Lyla',
									'Madelene',
									'Rosaura',
									'Berenice',
									'Georgine',
									'Vada',
									'Ray',
									'Martin',
									'Kathryn',
									'Dolly',
									'Clayton',
									'Arica',
									'Britany',
									'Rolland',
									'Mellissa',
									'Kymberly',
									'Claude',
									'Doyle',
									'Hector',
									'Arlen',
									'Debra',
									'Tami',
									'Catharine',
									'Su',
									'Danica',
									'Shandra',
									'Latrina',
									'Orval',
									'Clifton',
									'Jena',
									'Oliver',
									'Haydee',
									'Julie',
									'Xochitl',
									'Adrian',
									'Winfred',
									'Eldora',
									'Sook',
									'Antonette',
								   );
	protected $last_names = array(
									'Lecompte',
									'Jepko',
									'Godzik',
									'Bereda',
									'Lamers',
									'Errett',
									'Farm',
									'Adamski',
									'Fadri',
									'Gerhart',
									'Lubic',
									'Jost',
									'Manginelli',
									'Farris',
									'Otiz',
									'Huso',
									'Hutchens',
									'Mani',
									'Galland',
									'Laforest',
									'Labatt',
									'Burr',
									'Clemmens',
									'Gode',
									'Kapsner',
									'Harben',
									'Aumend',
									'Lauck',
									'Lassere',
									'Center',
									'Barlow',
									'Hudgens',
									'Fimbres',
									'Northcut',
									'Newstrom',
									'Floerchinger',
									'Goetting',
									'Binienda',
									'Dardagnac',
									'Graper',
									'Cadarette',
									'Castaneda',
									'Grosvenor',
									'Mccurren',
									'Feuerstein',
									'Parizek',
									'Haner',
									'Beyer',
									'Lollis',
									'Osten',
									'Baginski',
									'Fusca',
									'Hardiman',
									'Rechkemmer',
									'Ellerbrock',
									'Macvicar',
									'Golberg',
									'Benassi',
									'Hirons',
									'Lineberry',
									'Flamino',
									'Pickard',
									'Grohmann',
									'Parkers',
									'Hebrard',
									'Glade',
									'Haughney',
									'Levering',
									'Kudo',
									'Hoffschneider',
									'Mussa',
									'Fitzloff',
									'Matelic',
									'Maillard',
									'Carswell',
									'Becera',
									'Gonsior',
									'Qureshi',
									'Armel',
									'Broadnay',
									'Boulch',
									'Flamio',
									'Heaston',
									'Kristen',
									'Chambless',
									'Lamarch',
									'Jedan',
									'Fijal',
									'Jesmer',
									'Capraro',
									'Hemrich',
									'Prudente',
									'Cochren',
									'Karroach',
									'Guillotte',
									'Musinski',
									'Eflin',
									'Palumbo',
									'Legendre',
									'Afton',
								  );

	protected $city_names = array(
								'Richmond',
								'Southampton',
								'Stratford',
								'Wellington',
								'Jasper',
								'Flatrock',
								'Carleton',
								'Belmont',
								'Armstrong',
								);

	function getMaxRandomUsers() {
		if ( isset($this->max_random_users) ) {
			return $this->max_random_users;
		}

		return FALSE;
	}
	function setMaxRandomUsers($val) {
		if ( $val != '' ) {
			$this->max_random_users = $val;

			return TRUE;
		}

		return FALSE;
	}

	function getUserNamePostfix() {
		if ( isset($this->user_name_postfix) ) {
			return $this->user_name_postfix;
		}

		return FALSE;
	}
	function setUserNamePostfix($val) {
		if ( $val != '' ) {
			Debug::Text('UserName Postfix: '. $val, __FILE__, __LINE__, __METHOD__,10);
			$this->user_name_postfix = $val;

			return TRUE;
		}

		return FALSE;
	}

	function getUserNamePrefix() {
		if ( isset($this->user_name_prefix) ) {
			return $this->user_name_prefix;
		}

		return FALSE;
	}
	function setUserNamePrefix($val) {
		if ( $val != '' ) {
			$this->user_name_prefix = $val;

			return TRUE;
		}

		return FALSE;
	}

	function getAdminUserNamePrefix() {
		if ( isset($this->admin_user_name_prefix) ) {
			return $this->admin_user_name_prefix;
		}

		return FALSE;
	}
	function setAdminUserNamePrefix($val) {
		if ( $val != '' ) {
			$this->admin_user_name_prefix = $val;

			return TRUE;
		}

		return FALSE;
	}

	function getPassword() {
		if ( isset($this->password) ) {
			return $this->password;
		}

		return FALSE;
	}
	function setPassword($val) {
		if ( $val != '' ) {
			$this->password = $val;

			return TRUE;
		}

		return FALSE;
	}

	function getRandomArrayValue( $arr ) {
		$rand = array_rand( $arr );
		return $arr[$rand];
	}
	function getRandomFirstName() {
		$rand = array_rand( $this->first_names );
		if ( isset($this->first_names[$rand]) ) {
			return $this->first_names[$rand];
		}
		return FALSE;
	}
	function getRandomLastName() {
		$rand = array_rand( $this->first_names );
		if ( isset($this->first_names[$rand]) ) {
			return $this->first_names[$rand];
		}
		return FALSE;
	}

	function createCompany() {
		$cf = new CompanyFactory();

		$cf->setStatus( 10 ); //Active
		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$cf->setProductEdition( 20 ); //Professional
		} else {
			$cf->setProductEdition( 10 ); //Standard
		}

		$cf->setName( 'ABC Company' );
		$cf->setShortName( 'ABC' );
		$cf->setBusinessNumber( '123456789' );
		//$cf->setOriginatorID( $company_data['originator_id'] );
		//$cf->setDataCenterID($company_data['data_center_id']);
		$cf->setAddress1( '123 Main St' );
		$cf->setAddress2( 'Unit #123' );
		$cf->setCity( 'New York' );
		$cf->setCountry( 'US' );
		$cf->setProvince( 'NY' );
		$cf->setPostalCode( '12345' );
		$cf->setWorkPhone( '555-555-5555' );

		$cf->setEnableAddCurrency(FALSE);
		if ( $cf->isValid() ) {
			$insert_id = $cf->Save();
			Debug::Text('Company ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Company!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createCurrency( $company_id, $type ) {
		$cf = new CurrencyFactory();
		$cf->setCompany( $company_id );
		$cf->setStatus( 10 );
		switch ( $type ) {
			case 10: //USD
				$cf->setName( 'US Dollar' );
				$cf->setISOCode( 'USD' );

				$cf->setConversionRate( '1.000000000' );
				$cf->setAutoUpdate( FALSE );
				$cf->setBase( TRUE );
				$cf->setDefault( TRUE );

				break;
			case 20: //CAD
				$cf->setName( 'Canadian Dollar' );
				$cf->setISOCode( 'CAD' );

				$cf->setConversionRate( '0.850000000' );
				$cf->setAutoUpdate( TRUE );
				$cf->setBase( FALSE );
				$cf->setDefault( FALSE );

				break;
		}

		if ( $cf->isValid() ) {
			$insert_id = $cf->Save();
			Debug::Text('Currency ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Currency!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createBranch( $company_id, $type) {
		$bf = new BranchFactory();
		$bf->setCompany( $company_id );
		$bf->setStatus( 10 );
		switch ( $type ) {
			case 10: //Branch 1
				$bf->setName( 'New York' );
				$bf->setAddress1( '123 Main St' );
				$bf->setAddress2( 'Unit #123' );
				$bf->setCity( 'New York' );
				$bf->setCountry( 'US' );
				$bf->setProvince( 'NY' );

				$bf->setPostalCode( '12345' );
				$bf->setWorkPhone( '555-555-5555' );

				$bf->setManualId( 1 );

				break;
			case 20: //Branch 2
				$bf->setName( 'Seattle' );
				$bf->setAddress1( '789 Main St' );
				$bf->setAddress2( 'Unit #789' );
				$bf->setCity( 'Seattle' );
				$bf->setCountry( 'US' );
				$bf->setProvince( 'WA' );

				$bf->setPostalCode( '98105' );
				$bf->setWorkPhone( '555-555-5555' );

				$bf->setManualId( 2 );
				break;
		}

		if ( $bf->isValid() ) {
			$insert_id = $bf->Save();
			Debug::Text('Branch ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Branch!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createDepartment( $company_id, $type, $branch_ids = NULL) {
		$df = new DepartmentFactory();
		$df->setCompany( $company_id );
		$df->setStatus( 10 );

		switch ( $type ) {
			case 10:
				$df->setName( 'Sales' );
				$df->setManualId( 1 );
				break;
			case 20:
				$df->setName( 'Construction' );
				$df->setManualId( 2 );
				break;

		}

		if ( $df->isValid() ) {
			$insert_id = $df->Save();
			Debug::Text('Department ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Department!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;

	}

	function createStation( $company_id ) {
		$sf = new StationFactory();
		$sf->setCompany( $company_id );

		$sf->setStatus( 20 );
		$sf->setType( 10 );
		$sf->setSource( 'ANY' );
		$sf->setStation( 'ANY' );
		$sf->setDescription( 'All stations' );

		$sf->setGroupSelectionType( 10 );
		$sf->setBranchSelectionType( 10 );
		$sf->setDepartmentSelectionType( 10 );

		if ( $sf->isValid() ) {
			$insert_id = $sf->Save();

			Debug::Text('Station ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);
		}


		$sf = new StationFactory();
		$sf->setCompany( $company_id );

		$sf->setStatus( 20 );
		$sf->setType( 25 );
		$sf->setSource( 'ANY' );
		$sf->setStation( 'ANY' );
		$sf->setDescription( 'All stations' );

		$sf->setGroupSelectionType( 10 );
		$sf->setBranchSelectionType( 10 );
		$sf->setDepartmentSelectionType( 10 );

		if ( $sf->isValid() ) {
			//$insert_id = $sf->Save(FALSE);
			//$sf->setUser( array(-1) );

			$insert_id = $sf->Save();

			Debug::Text('Station ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Station!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;

	}

	function createPayStubAccount( $company_id ) {
		$retval = PayStubEntryAccountFactory::addPresets( $company_id );

		if ( $retval == TRUE ) {
			Debug::Text('Created Pay Stub Accounts!', __FILE__, __LINE__, __METHOD__,10);
			return TRUE;
		}

		Debug::Text('Failed Creating Pay Stub Accounts for Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createPayStubAccountLink( $company_id ) {
		$psealf = new PayStubEntryAccountLinkFactory();
		$psealf->setCompany( $company_id );

		$psealf->setTotalGross( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 40, TTi18n::gettext('Total Gross')) );
		$psealf->setTotalEmployeeDeduction( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 40, TTi18n::gettext('Total Deductions')) );
		$psealf->setTotalEmployerDeduction( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 40, TTi18n::gettext('Employer Total Contributions')) );
		$psealf->setTotalNetPay( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 40, TTi18n::gettext('Net Pay')) );
		$psealf->setRegularTime( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, TTi18n::gettext('Regular Time')) );

		if ( $psealf->isValid() ) {
			$insert_id = $psealf->Save();
			Debug::Text('Pay Stub Account Link ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Pay Stub Account Links!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createCompanyDeduction( $company_id ) {
		$retval = CompanyDeductionFactory::addPresets( $company_id );

		if ( $retval == TRUE ) {
			Debug::Text('Created Company Deductions!', __FILE__, __LINE__, __METHOD__,10);
			return TRUE;
		}

		Debug::Text('Failed Creating Company Deductions for Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createRoundingPolicy( $company_id, $type ) {
		$ripf = new RoundIntervalPolicyFactory();
		$ripf->setCompany( $company_id );

		switch ( $type ) {
			case 10: //In
				$ripf->setName( '5min' );
				$ripf->setPunchType( 40 ); //In
				$ripf->setRoundType( 30 ); //Up
				$ripf->setInterval( (60*5) ); //5mins
				$ripf->setGrace( (60*3) ); //3min
				$ripf->setStrict( FALSE );
				break;
			case 20: //Out
				$ripf->setName( '5min' );
				$ripf->setPunchType( 50 ); //In
				$ripf->setRoundType( 10 ); //Down
				$ripf->setInterval( (60*5) ); //5mins
				$ripf->setGrace( (60*3) ); //3min
				$ripf->setStrict( FALSE );
				break;
			case 30: //Day total
				$ripf->setName( '15min' );
				$ripf->setPunchType( 120 ); //In
				$ripf->setRoundType( 10 ); //Down
				$ripf->setInterval( (60*15) ); //15mins
				$ripf->setGrace( (60*3) ); //3min
				$ripf->setStrict( FALSE );
				break;
			case 40: //Lunch total
				$ripf->setName( '15min' );
				$ripf->setPunchType( 100 ); //In
				$ripf->setRoundType( 10 ); //Down
				$ripf->setInterval( (60*15) ); //15mins
				$ripf->setGrace( (60*3) ); //3min
				$ripf->setStrict( FALSE );
				break;
			case 50: //Break total
				$ripf->setName( '15min' );
				$ripf->setPunchType( 110 ); //In
				$ripf->setRoundType( 10 ); //Down
				$ripf->setInterval( (60*15) ); //15mins
				$ripf->setGrace( (60*3) ); //3min
				$ripf->setStrict( FALSE );
				break;

		}

		if ( $ripf->isValid() ) {
			$insert_id = $ripf->Save();
			Debug::Text('Rounding Policy ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Rounding Policy!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createAccrualPolicy( $company_id, $type ) {
		$apf = new AccrualPolicyFactory();

		$apf->setCompany( $company_id );

		switch ( $type ) {
			case 10: //Bank Time
				$apf->setName( 'Bank Time' );
				$apf->setType( 10 );
				break;
			case 20: //Calendar Based: Vacation/PTO
				$apf->setName( 'Personal Time Off (PTO)/Vacation' );
				$apf->setType( 20 );

				$apf->setApplyFrequency( 10 );

				$apf->setMilestoneRolloverHireDate( TRUE );

				$apf->setMinimumEmployedDays( 30 );
				break;
			case 30: //Calendar Based: Vacation/PTO
				$apf->setName( 'Sick Time' );
				$apf->setType( 20 );

				$apf->setApplyFrequency( 10 );

				$apf->setMilestoneRolloverHireDate( TRUE );

				$apf->setMinimumEmployedDays( 30 );
				break;
		}

		if ( $apf->isValid() ) {
			$insert_id = $apf->Save();
			Debug::Text('Accrual Policy ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			$apmf = new AccrualPolicyMilestoneFactory();
			if ( $type == 20 ) {
				$apmf->setAccrualPolicy( $insert_id );
				$apmf->setLengthOfService( 1 );
				$apmf->setLengthOfServiceUnit( 40 );
				$apmf->setAccrualRate( (3600*8)*5 );
				$apmf->setMaximumTime( (3600*8)*5 );

				if ( $apmf->isValid() ) {
					Debug::Text('Saving Milestone...', __FILE__, __LINE__, __METHOD__,10);
					$apmf->Save();
				}

				$apmf->setAccrualPolicy( $insert_id );
				$apmf->setLengthOfService( 2 );
				$apmf->setLengthOfServiceUnit( 40 );
				$apmf->setAccrualRate( (3600*8)*10 );
				$apmf->setMaximumTime( (3600*8)*10 );

				if ( $apmf->isValid() ) {
					Debug::Text('Saving Milestone...', __FILE__, __LINE__, __METHOD__,10);
					$apmf->Save();
				}

				$apmf->setAccrualPolicy( $insert_id );
				$apmf->setLengthOfService( 3 );
				$apmf->setLengthOfServiceUnit( 40 );
				$apmf->setAccrualRate( (3600*8)*15 );
				$apmf->setMaximumTime( (3600*8)*15 );

				if ( $apmf->isValid() ) {
					Debug::Text('Saving Milestone...', __FILE__, __LINE__, __METHOD__,10);
					$apmf->Save();
				}

			} elseif ( $type == 30 ) {
				$apmf->setAccrualPolicy( $insert_id );
				$apmf->setLengthOfService( 1 );
				$apmf->setLengthOfServiceUnit( 10 );
				$apmf->setAccrualRate( (3600*8)*3 );
				$apmf->setMaximumTime( (3600*8)*3 );

				if ( $apmf->isValid() ) {
					Debug::Text('Saving Milestone...', __FILE__, __LINE__, __METHOD__,10);
					$apmf->Save();
				}

				$apmf->setAccrualPolicy( $insert_id );
				$apmf->setLengthOfService( 1 );
				$apmf->setLengthOfServiceUnit( 40 );
				$apmf->setAccrualRate( (3600*8)*6 );
				$apmf->setMaximumTime( (3600*8)*6 );

				if ( $apmf->isValid() ) {
					Debug::Text('Saving Milestone...', __FILE__, __LINE__, __METHOD__,10);
					$apmf->Save();
				}
			}
			return $insert_id;
		}

		Debug::Text('Failed Creating Accrual Policy!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createOverTimePolicy( $company_id, $type, $accrual_policy_id = NULL ) {
		$otpf = new OverTimePolicyFactory();
		$otpf->setCompany( $company_id );



		switch ( $type ) {
			case 10:
				$otpf->setName( 'OverTime (>8hrs)' );
				$otpf->setType( 10 );
				$otpf->setTriggerTime( (3600*8) );
				$otpf->setRate( '1.5' );
				$otpf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Over Time 1') );

				$otpf->setAccrualPolicyId( 0 );
				$otpf->setAccrualRate( 0 );

				break;
			case 20:
				$otpf->setName( 'Daily (>10hrs)' );
				$otpf->setType( 10 );
				$otpf->setTriggerTime( (3600*10) );
				$otpf->setRate( '1.0' );
				$otpf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Over Time 2') );

				$otpf->setAccrualPolicyId( $accrual_policy_id );
				$otpf->setAccrualRate( '1.0' );
		}

		if ( $otpf->isValid() ) {
			$insert_id = $otpf->Save();
			Debug::Text('Overtime Policy ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Overtime Policy!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createPremiumPolicy( $company_id, $type ) {
		$ppf = new PremiumPolicyFactory();
		$ppf->setCompany( $company_id );

		switch ( $type ) {
			case 10: //Simple weekend premium
				$ppf->setName( 'Weekend' );
				$ppf->setType( 10 );
				$ppf->setPayType( 20 );

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('12:00 AM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setMon( FALSE );
				$ppf->setTue( FALSE );

				$ppf->setWed( FALSE );
				$ppf->setThu( FALSE );
				$ppf->setFri( FALSE );

				$ppf->setSat( TRUE );
				$ppf->setSun( TRUE );

				$ppf->setRate( '1.33' ); //$1.33 per hour
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 1') );

				break;
			case 20: //Simple evening premium
				$ppf->setName( 'Evening' );
				$ppf->setType( 10 );
				$ppf->setPayType( 10 ); //Pay multiplied by factor

				$ppf->setIncludePartialPunch( TRUE );

				$ppf->setStartDate( '' );
				$ppf->setEndDate( '' );

				$ppf->setStartTime( TTDate::parseDateTime('5:00 PM') );
				$ppf->setEndTime( TTDate::parseDateTime('11:59 PM') );

				$ppf->setMon( FALSE );
				$ppf->setTue( FALSE );

				$ppf->setWed( FALSE );
				$ppf->setThu( FALSE );
				$ppf->setFri( TRUE );

				$ppf->setSat( FALSE );
				$ppf->setSun( FALSE );

				$ppf->setWageGroup( $this->user_wage_groups[0] );
				$ppf->setRate( '1.50' );
				$ppf->setPayStubEntryAccountId( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 10, 'Premium 2') );

				break;
		}

		if ( $ppf->isValid() ) {
			$insert_id = $ppf->Save();
			Debug::Text('Premium Policy ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Premium Policy!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createAbsencePolicy( $company_id, $type, $accrual_policy_id = 0) {
		$apf = new AbsencePolicyFactory();
		$apf->setCompany( $company_id );

		switch ( $type ) {
			case 10: //Vacation
				$apf->setName( 'PTO/Vacation' );
				$apf->setType( 10 ); //Paid
				$apf->setAccrualPolicyID( $accrual_policy_id );
				$apf->setPayStubEntryAccountID( CompanyDeductionFactory::getPayStubEntryAccountByCompanyIDAndTypeAndFuzzyName($company_id, 50, 'Vacation Accrual Release') );

				break;
			case 20: //Bank Time
				$apf->setName( 'Bank Time' );
				$apf->setType( 20 ); //Not Paid
				$apf->setAccrualPolicyID( $accrual_policy_id );
				$apf->setPayStubEntryAccountID( 0 );

				break;
			case 30: //Sick Time
				$apf->setName( 'Sick Time' );
				$apf->setType( 20 ); //Not Paid
				$apf->setAccrualPolicyID( $accrual_policy_id );
				$apf->setPayStubEntryAccountID( 0 );

				break;
		}

		if ( $apf->isValid() ) {
			$insert_id = $apf->Save();
			Debug::Text('Absence Policy ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Absence Policy!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createMealPolicy( $company_id ) {
		$mpf = new MealPolicyFactory();

		$mpf->setCompany( $company_id );
		$mpf->setName( 'One Hour Min.' );
		$mpf->setType( 20 );
		$mpf->setTriggerTime( (3600*5) );
		$mpf->setAmount( 3600 );
		$mpf->setStartWindow( (3600*4) );
		$mpf->setWindowLength( (3600*2) );

		if ( $mpf->isValid() ) {
			$insert_id = $mpf->Save();
			Debug::Text('Meal Policy ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Meal Policy!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createSchedulePolicy( $company_id, $meal_policy_id ) {
		$spf = new SchedulePolicyFactory();

		$spf->setCompany( $company_id );
		$spf->setName( 'One Hour Lunch' );
		$spf->setMealPolicyID( $meal_policy_id );
		$spf->setOverTimePolicyID( 0 );
		$spf->setAbsencePolicyID( 0 );
		$spf->setStartStopWindow( 1800 );

		if ( $spf->isValid() ) {
			$insert_id = $spf->Save();
			Debug::Text('Schedule Policy ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Schedule Policy!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createExceptionPolicy( $company_id ) {
		$epcf = new ExceptionPolicyControlFactory();

		$epcf->setCompany( $company_id );
		$epcf->setName( 'Default' );

		if ( $epcf->isValid() ) {
			$epc_id = $epcf->Save();

			Debug::Text('aException Policy Control ID: '. $epc_id , __FILE__, __LINE__, __METHOD__,10);

			if ( $epc_id === TRUE ) {
				$epc_id = $data['id'];
			}

			Debug::Text('bException Policy Control ID: '. $epc_id , __FILE__, __LINE__, __METHOD__,10);

			$data['exceptions'] = array(
									'S1' => array(
												'active' => TRUE,
												'severity_id' => 10,
												),
									'S2' => array(
												'active' => TRUE,
												'severity_id' => 30,
												),
									'S3' => array(
												'active' => TRUE,
												'severity_id' => 10,
												'grace' => 300,
												'watch_window' => 3600,
												),
									'S4' => array(
												'active' => TRUE,
												'severity_id' => 20,
												'grace' => 300,
												'watch_window' => 3600,

												),
									'S5' => array(
												'active' => TRUE,
												'severity_id' => 20,
												'grace' => 300,
												'watch_window' => 3600,

												),
									'S6' => array(
												'active' => TRUE,
												'severity_id' => 10,
												'grace' => 300,
												'watch_window' => 3600,
												),
									'S7' => array(
												'active' => TRUE,
												'severity_id' => 30,
												),
									'S8' => array(
												'active' => TRUE,
												'severity_id' => 10,
												),
									'M1' => array(
												'active' => TRUE,
												'severity_id' => 30,
												),
									'M2' => array(
												'active' => TRUE,
												'severity_id' => 30,
												),
									'L3' => array(
												'active' => TRUE,
												'severity_id' => 30,
												),
									'M3' => array(
												'active' => TRUE,
												'severity_id' => 30,
												),

									);

			if ( count($data['exceptions']) > 0 ) {

				foreach ($data['exceptions'] as $code => $exception_data) {
					Debug::Text('Looping Code: '. $code, __FILE__, __LINE__, __METHOD__,10);

					$epf = new ExceptionPolicyFactory();
					$epf->setExceptionPolicyControl( $epc_id );
					if ( isset($exception_data['active'])  ) {
						$epf->setActive( TRUE );
					} else {
						$epf->setActive( FALSE );
					}
					$epf->setType( $code );
					$epf->setSeverity( $exception_data['severity_id'] );
					if ( isset($exception_data['demerit']) AND $exception_data['demerit'] != '') {
						$epf->setDemerit( $exception_data['demerit'] );
					}
					if ( isset($exception_data['grace']) AND $exception_data['grace'] != '' ) {
						$epf->setGrace( $exception_data['grace'] );
					}
					if ( isset($exception_data['watch_window']) AND $exception_data['watch_window'] != '' ) {
						$epf->setWatchWindow( $exception_data['watch_window'] );
					}
					if ( $epf->isValid() ) {
						$epf->Save();
					}
				}

				Debug::Text('Creating Exception Policy ID: '. $epc_id, __FILE__, __LINE__, __METHOD__,10);
				return $epc_id;
			}
		}

		Debug::Text('Failed Creating Exception Policy!', __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}

	function createPolicyGroup( $company_id, $meal_policy_ids = NULL, $exception_policy_id = NULL, $holiday_policy_ids = NULL, $over_time_policy_ids = NULL, $premium_policy_ids = NULL, $rounding_policy_ids = NULL, $user_ids = NULL  ) {
		$pgf = new PolicyGroupFactory();

		$pgf->StartTransaction();

		$pgf->setCompany( $company_id );
		$pgf->setName( 'Default' );

		if ( $exception_policy_id != '' ) {
			$pgf->setExceptionPolicyControlID( $exception_policy_id );
		}

		if ( $pgf->isValid() ) {
			$insert_id = $pgf->Save(FALSE);

			if ( is_array($meal_policy_ids) ) {
				$pgf->setMealPolicy( $meal_policy_ids );
			} else {
				$pgf->setMealPolicy( array() );
			}

			if ( is_array($over_time_policy_ids) ) {
				$pgf->setOverTimePolicy( $over_time_policy_ids );
			} else {
				$pgf->setOverTimePolicy( array() );
			}

			if ( is_array($premium_policy_ids) ) {
				$pgf->setPremiumPolicy( $premium_policy_ids );
			} else {
				$pgf->setPremiumPolicy( array() );
			}

			if ( is_array($rounding_policy_ids) ) {
				$pgf->setRoundIntervalPolicy( $rounding_policy_ids );
			} else {
				$pgf->setRoundIntervalPolicy( array() );
			}

			if ( is_array($holiday_policy_ids) ) {
				$pgf->setHolidayPolicy( $holiday_policy_ids );
			} else {
				$pgf->setHolidayPolicy( array() );
			}

			if ( is_array($user_ids) ) {
				$pgf->setUser( $user_ids );
			} else {
				$pgf->setUser( array() );
			}

			if ( $pgf->isValid() ) {
				$pgf->Save();
				$pgf->CommitTransaction();

				Debug::Text('Creating Policy Group ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

				return TRUE;
			}
		}

		Debug::Text('Failed Creating Policy Group!', __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}

	function createPayPeriodSchedule( $company_id, $user_ids ) {

		$ppsf = new PayPeriodScheduleFactory();

		$ppsf->setCompany( $company_id );
		$ppsf->setName( 'Bi-Weekly' );
		$ppsf->setDescription( 'Pay every two weeks' );
		$ppsf->setType( 20 );
		$ppsf->setStartWeekDay( 0 );

		//$anchor_date = TTDate::getBeginWeekEpoch( (time()-(86400*42)) ); //Start 6 weeks ago
		$anchor_date = TTDate::getBeginWeekEpoch( (time()-(86400*14)) ); //Start 6 weeks ago

		$ppsf->setAnchorDate( $anchor_date );

		$ppsf->setStartDayOfWeek( TTDate::getDayOfWeek( $anchor_date ) );
		$ppsf->setTransactionDate( 7 );

		$ppsf->setTransactionDateBusinessDay( TRUE );


		/*
		$ppsf->setPrimaryDate( ($anchor_date+(86400*14)) );
		$ppsf->setPrimaryDateLastDayOfMonth( FALSE );
		$ppsf->setPrimaryTransactionDate( ($anchor_date+(86400*21)) );
		$ppsf->setPrimaryTransactionDateLastDayOfMonth( FALSE );
		$ppsf->setPrimaryTransactionDateBusinessDay( TRUE );

		$ppsf->setSecondaryDate( ($anchor_date+(86400*28)) );
		$ppsf->setSecondaryDateLastDayOfMonth( FALSE );
		$ppsf->setSecondaryTransactionDate( ($anchor_date+(86400*35))  );
		$ppsf->setSecondaryTransactionDateLastDayOfMonth( FALSE );
		$ppsf->setSecondaryTransactionDateBusinessDay( TRUE );
		*/

		$ppsf->setDayStartTime( 0 );
		$ppsf->setShiftAssignedDay( 10 ); //Day the shift starts on.
		$ppsf->setNewDayTriggerTime( (4*3600) );
		$ppsf->setMaximumShiftTime( (16*3600) );

		if ( $ppsf->isValid() ) {
			$insert_id = $ppsf->Save(FALSE);
			Debug::Text('Pay Period Schedule ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			//Dont create pay periods twice.
			$ppsf->setEnableInitialPayPeriods(FALSE);
			$ppsf->setUser( $user_ids );
			$ppsf->Save();

			return $insert_id;
		}

		Debug::Text('Failed Creating Pay Period Schedule!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createUserGroup( $company_id, $type, $parent_id = 0) {
		$ugf = new UserGroupFactory();
		$ugf->setCompany( $company_id );

		switch ( $type ) {
			case 10:
				$ugf->setParent( $parent_id );
				$ugf->setName( 'Employee Group A' );

				break;
			case 20:
				$ugf->setParent( $parent_id );
				$ugf->setName( 'Employee Group A2' );

				break;
			case 30:
				$ugf->setParent( $parent_id );
				$ugf->setName( 'Employee Group A3' );

				break;
			case 40:
				$ugf->setParent( $parent_id );
				$ugf->setName( 'Employee Group B' );

				break;
			case 50:
				$ugf->setParent( $parent_id );
				$ugf->setName( 'Employee Group B2' );

				break;
		}

		if ( $ugf->isValid() ) {
			$insert_id = $ugf->Save();
			Debug::Text('Group ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Pay Period Schedule!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createUser( $company_id, $type, $policy_group_id = 0, $default_branch_id = 0, $default_department_id = 0, $default_currency_id = 0, $user_group_id = 0) {
		$uf = new UserFactory();

		$uf->setCompany( $company_id );
		$uf->setStatus( 10 );
		//$uf->setPolicyGroup( 0 );

		if ( $default_currency_id == 0 ) {
			Debug::Text('Get Default Currency...', __FILE__, __LINE__, __METHOD__,10);

			//Get Default.
			$crlf = new CurrencyListFactory();
			$crlf->getByCompanyIdAndDefault( $company_id, TRUE );
			if ( $crlf->getRecordCount() > 0 ) {
				$default_currency_id = $crlf->getCurrent()->getId();
				Debug::Text('Default Currency ID: '. $default_currency_id, __FILE__, __LINE__, __METHOD__,10);
			}
		}

		$hire_date = strtotime(rand(2000,2005).'-'.rand(1,12).'-'.rand(1,28));
		switch ( $type ) {
			case 10: //John Doe
				$uf->setUserName( 'john.doe'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'John' );
				$uf->setLastName( 'Doe' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Springfield St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 11: //Theodora  Simmons
				$uf->setUserName( 'theodora.simmons'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Theodora' );
				$uf->setLastName( 'Simmons' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Springfield St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 12: //Kitty  Nicholas
				$uf->setUserName( 'kitty.nicholas'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Kitty' );
				$uf->setLastName( 'Nicholas' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Ethel St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 13: //Tristen  Braun
				$uf->setUserName( 'tristen.braun'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Tristen' );
				$uf->setLastName( 'Braun' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Ethel St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 14: //Gale  Mench
				$uf->setUserName( 'gale.mench'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Gale' );
				$uf->setLastName( 'Mench' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Gordon St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 15: //Beau  Mayers
				$uf->setUserName( 'beau.mayers'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Beau' );
				$uf->setLastName( 'Mayers' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Gordon St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 16: //Ian  Schofield
				$uf->setUserName( 'ian.schofield'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Ian' );
				$uf->setLastName( 'Schofield' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Sussex St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 17: //Gabe  Hoffhants
				$uf->setUserName( 'gabe.hoffhants'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Gabe' );
				$uf->setLastName( 'Hoffhants' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Sussex St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 18: //Franklin  Mcmichaels
				$uf->setUserName( 'franklin.mcmichaels'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Franklin' );
				$uf->setLastName( 'McMichaels' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Georgia St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 19: //Donald  Whitling
				$uf->setUserName( 'donald.whitling'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Donald' );
				$uf->setLastName( 'Whitling' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Georgia St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 20: //Jane Doe

				$uf->setUserName( 'jane.doe'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '2222' );
				//$uf->setPhonePassword( '2222' );

				$uf->setFirstName( 'Jane' );
				$uf->setLastName( 'Doe' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Ontario St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 21: //Tamera  Erschoff
				$uf->setUserName( 'tamera.erschoff'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Tamera' );
				$uf->setLastName( 'Erschoff' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Ontario St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 22: //Redd  Rifler
				$uf->setUserName( 'redd.rifler'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Redd' );
				$uf->setLastName( 'Rifler' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Main St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 23: //Brent  Pawle
				$uf->setUserName( 'brent.pawle'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Brent' );
				$uf->setLastName( 'Pawle' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Pandosy St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 24: //Heather  Grant
				$uf->setUserName( 'heather.grant'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Heather' );
				$uf->setLastName( 'Grant' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Lakeshore St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 25: //Steph  Mench
				$uf->setUserName( 'steph.mench'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Steph' );
				$uf->setLastName( 'Mench' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Dobbin St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 26: //Kailey  Klockman
				$uf->setUserName( 'kailey.klockman'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Kailey' );
				$uf->setLastName( 'Klockman' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Spall St' );
				//$uf->setAddress2( 'Unit #123' );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 27: //Matt  Marcotte
				$uf->setUserName( 'matt.marcotte'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Matt' );
				$uf->setLastName( 'Marcotte' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Spall St' );
				//$uf->setAddress2( 'Unit #123' );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 28: //Nick  Hanseu
				$uf->setUserName( 'nick.hanseu'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Nick' );
				$uf->setLastName( 'Hanseu' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Gates St' );
				//$uf->setAddress2( 'Unit #123' );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 29: //Rich  Wiggins
				$uf->setUserName( 'rich.wiggins'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '1111' );
				//$uf->setPhonePassword( '1111' );

				$uf->setFirstName( 'Rich' );
				$uf->setLastName( 'Wiggins' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Gates St' );
				//$uf->setAddress2( 'Unit #123' );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 30: //Mike Smith

				$uf->setUserName( 'mike.smith'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '2222' );
				//$uf->setPhonePassword( '2222' );

				$uf->setFirstName( 'Mike' );
				$uf->setLastName( 'Smith' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Main St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 40: //John Hancock

				$uf->setUserName( 'john.hancock'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '2222' );
				//$uf->setPhonePassword( '2222' );

				$uf->setFirstName( 'John' );
				$uf->setLastName( 'Hancock' );
				$uf->setSex( 20 );
				$uf->setAddress1( rand(100,9999). ' Main St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'Seattle' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'WA' );

				$uf->setPostalCode( rand(98000,99499) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 100: //Administrator
				$uf->setUserName( 'demoadmin'. $this->getUserNamePostfix() );
				$uf->setPassword( 'demo' );
				//$uf->setPhoneId( '3333' );
				//$uf->setPhonePassword( '3333' );

				$uf->setFirstName( 'Mr.' );
				$uf->setLastName( 'Administrator' );
				$uf->setSex( 10 );
				$uf->setAddress1( rand(100,9999). ' Main St' );
				$uf->setAddress2( 'Unit #'. rand(10,999) );
				$uf->setCity( 'New York' );

				$uf->setCountry( 'US' );
				$uf->setProvince( 'NY' );

				$uf->setPostalCode( str_pad( rand(400,599), 5, 0, STR_PAD_LEFT) );
				$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkPhoneExt( rand(100,1000) );
				$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
				$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
				$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
				$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
				$uf->setHireDate( $hire_date );
				$uf->setEmployeeNumber( $type );

				$uf->setDefaultBranch( $default_branch_id );
				$uf->setDefaultDepartment( $default_department_id );
				$uf->setCurrency( $default_currency_id );
				$uf->setGroup( $user_group_id );
				break;
			case 999: //Random user
				$first_name = $this->getRandomFirstName();
				$last_name = $this->getRandomLastName();
				if ( $first_name != '' AND $last_name != '' ) {
					$uf->setUserName( $first_name.'.'. $last_name . $this->getUserNamePostfix() );
					$uf->setPassword( 'demo' );

					$uf->setFirstName( $first_name );
					$uf->setLastName( $last_name );
					$uf->setSex( 20 );
					$uf->setAddress1( rand(100,9999). ' '. $this->getRandomLastName() .' St' );
					$uf->setAddress2( 'Unit #'. rand(10,999) );
					$uf->setCity( $this->getRandomArrayValue( $this->city_names ) );

					$uf->setCountry( 'US' );
					$uf->setProvince( 'WA' );

					$uf->setPostalCode( rand(98000,99499) );
					$uf->setWorkPhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
					$uf->setWorkPhoneExt( rand(100,1000) );
					$uf->setHomePhone( rand(403,600).'-'. rand(250,600).'-'. rand(1000,9999) );
					$uf->setWorkEmail( $uf->getUserName().'@abc-company.com' );
					$uf->setSIN( rand(100,999).'-'. rand(100,999).'-'. rand(100,999) );
					$uf->setBirthDate( strtotime(rand(1970,1990).'-'.rand(1,12).'-'.rand(1,28)) );
					$uf->setHireDate( $hire_date );
					$uf->setEmployeeNumber( rand(1000,25000) );

					$uf->setDefaultBranch( $default_branch_id );
					$uf->setDefaultDepartment( $default_department_id );
					$uf->setCurrency( $default_currency_id );
					$uf->setGroup( $user_group_id );
				}
				unset($first_name, $last_name);

				break;
		}

		if ( $uf->isValid() ) {
			$insert_id = $uf->Save();
			Debug::Text('User ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			$this->createUserPreference( $insert_id );
/*
			$preset_flags = array(
								'invoice' => 0,
								'job' => 1,
								'document' => 0,
								);
*/
			if ( $type == 100 ) {
				//$this->createUserPermission( array( $insert_id ), 40, $preset_flags );
				$this->createUserPermission( $insert_id, 40 );
			} else {
				//$this->createUserPermission( array( $insert_id ), 10, $preset_flags );
				$this->createUserPermission( $insert_id, 10 );
			}
			//$this->createUserPermission( array( -1 ), 10, $preset_flags );

			//Default wage group
			$this->createUserWage( $insert_id, '19.50', $hire_date );
			$this->createUserWage( $insert_id, '19.75', strtotime('01-Jun-04') );
			$this->createUserWage( $insert_id, '20.15', strtotime('01-Jan-05') );
			$this->createUserWage( $insert_id, '21.50', strtotime('01-Jan-06') );

			$this->createUserWage( $insert_id, '10.00', strtotime('01-Jan-04'), $this->user_wage_groups[0] );
			$this->createUserWage( $insert_id, '20.00', strtotime('01-Jan-04'), $this->user_wage_groups[1] );

			//Assign Taxes to user
			$this->createUserDeduction( $company_id, $insert_id );

			return $insert_id;
		}

		Debug::Text('Failed Creating User!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createUserPreference( $user_id ) {
		$upf = new UserPreferenceFactory();
		$upf->setUser( $user_id );
		$upf->setDateFormat( 'd-M-y' );
		$upf->setTimeFormat( 'g:i A' );
		$upf->setTimeUnitFormat( 10 );
		$upf->setTimeZone( 'PST8PDT' );
		$upf->setStartWeekDay( 0 );
		$upf->setItemsPerPage( 25 );

		if ( $upf->isValid() ) {
			$insert_id = $upf->Save();
			Debug::Text('User Preference ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating User Preference!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createUserDeduction( $company_id, $user_id ) {
		$fail_transaction = FALSE;

		$cdlf = new CompanyDeductionListFactory();
		$cdlf->getByCompanyId( $company_id  );

		if ( $cdlf->getRecordCount() > 0 ) {
			foreach( $cdlf as $cd_obj ) {
				Debug::Text('Creating User Deduction: User Id:'. $user_id .' Company Deduction: '. $cd_obj->getId(), __FILE__, __LINE__, __METHOD__,10);
				$udf = new UserDeductionFactory();
				$udf->setUser( $user_id );
				$udf->setCompanyDeduction( $cd_obj->getId() );
				if ( $udf->isValid() ) {
					if ( $udf->Save() === FALSE ) {
						Debug::Text('User Deductions... Save Failed!', __FILE__, __LINE__, __METHOD__,10);
						$fail_transaction = TRUE;
					}
				} else {
					Debug::Text('User Deductions... isValid Failed!', __FILE__, __LINE__, __METHOD__,10);
					$fail_transaction = TRUE;
				}
			}

			if ( $fail_transaction == FALSE ) {
				Debug::Text('User Deductions Created!', __FILE__, __LINE__, __METHOD__,10);
				return TRUE;
			}
		} else {
			Debug::Text('No Company Deductions Found!', __FILE__, __LINE__, __METHOD__,10);
		}


		Debug::Text('Failed Creating User Deductions!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createUserWageGroups( $company_id ) {
		$wgf = new WageGroupFactory();
		$wgf->setCompany( $company_id );
		$wgf->setName('Alternate Wage #1');

		if ( $wgf->isValid() ) {
			$this->user_wage_groups[0] = $wgf->Save();
			Debug::Text('aUser Wage Group ID: '. $this->user_wage_groups[0], __FILE__, __LINE__, __METHOD__,10);
		}

		$wgf = new WageGroupFactory();
		$wgf->setCompany( $company_id );
		$wgf->setName('Alternate Wage #2');

		if ( $wgf->isValid() ) {
			$this->user_wage_groups[1] = $wgf->Save();

			Debug::Text('bUser Wage Group ID: '. $this->user_wage_groups[1], __FILE__, __LINE__, __METHOD__,10);
		}

		return TRUE;
	}
	function createUserWage( $user_id, $rate, $effective_date, $wage_group_id = 0 ) {
		$uwf = new UserWageFactory();

		$uwf->setUser($user_id);
		$uwf->setWageGroup( $wage_group_id );
		$uwf->setType( 10 );
		$uwf->setWage(  $rate );
		//$uwf->setWeeklyTime( TTDate::parseTimeUnit( $wage_data['weekly_time'] ) );
		$uwf->setEffectiveDate( $effective_date );

		if ( $uwf->isValid() ) {
			$insert_id = $uwf->Save();
			Debug::Text('User Wage ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating User Wage!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createPermissionGroups( $company_id ) {
		Debug::text('Adding Preset Permission Groups: '. $company_id, __FILE__, __LINE__, __METHOD__,9);

		$pf = new PermissionFactory();
		$pf->StartTransaction();

		if ( getTTProductEdition() == 20 ) {
			$preset_flags = array(
								'invoice' => 0,
								'job' => 1,
								'document' => 0,
								);
/*
			$preset_flags = array(
								'job' => 1,
								'invoice' => 1,
								'document' => 1,
								);
*/
		} else {
			$preset_flags = array();
		}

		$preset_options = $pf->getOptions('preset');
		foreach( $preset_options as $preset_id => $preset_name ) {
			$pcf = new PermissionControlFactory();
			$pcf->setCompany( $company_id );
			$pcf->setName( $preset_name );
			$pcf->setDescription( '' );
			if ( $pcf->isValid() ) {
				$pcf_id = $pcf->Save(FALSE);

				$this->permission_presets[$preset_id] = $pcf_id;

				$pf->applyPreset($pcf_id, $preset_id, $preset_flags );
			}
		}
		//$pf->FailTransaction();
		$pf->CommitTransaction();

	}

	function createUserPermission( $user_id, $preset_id ) {
		if ( isset($this->permission_presets[$preset_id] ) ) {
			$pclf = new PermissionControlListFactory();
			$pclf->getById( $this->permission_presets[$preset_id] );
			if ( $pclf->getRecordCount() > 0 ) {
				$pc_obj = $pclf->getCurrent();

				$puf = new PermissionUserFactory();
				$puf->setPermissionControl( $pc_obj->getId() );
				$puf->setUser( $user_id );
				if ( $puf->isValid() ) {
					Debug::Text('Assigning User ID: '. $user_id .' To Permission Control: '. $this->permission_presets[$preset_id] .' Preset: '. $preset_id, __FILE__, __LINE__, __METHOD__,10);

					$puf->Save();

					return TRUE;
				}
			}
		}

		Debug::Text('Failed Assigning User to Permission Control! User ID: '. $user_id .' Preset: '. $preset_id, __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createAuthorizationHierarchyControl( $company_id, $child_user_ids ) {
		$hcf = new HierarchyControlFactory();

		$hcf->setCompany( $company_id );
		$hcf->setObjectType( array(50) );

		$hcf->setName('Request');
		$hcf->setDescription('Request Hierarchy');

		if ( $hcf->isValid() ) {
			$insert_id = $hcf->Save( FALSE );
			Debug::Text('Hierarchy Control ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			$hcf->setUser( $child_user_ids );

			return $insert_id;
		}

		Debug::Text('Failed Creating Hierarchy Control!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createAuthorizationHierarchyLevel( $company_id, $hierarchy_id, $root_user_id, $level ) {
		if ( $hierarchy_id != '' ) {
			//Add level
			$hlf = new HierarchyLevelFactory();
			$hlf->setHierarchyControl( $hierarchy_id );
			$hlf->setLevel( $level );
			$hlf->setUser( $root_user_id );

			if ( $hlf->isValid() ) {
				$insert_id = $hlf->Save();
				Debug::Text('Hierarchy Level ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);
			}
		}

		//Debug::Text('Failed Creating Hierarchy!', __FILE__, __LINE__, __METHOD__,10);

		return TRUE;
	}

	function createRequest( $type, $user_id, $date_stamp ) {
		$date_stamp = strtotime($date_stamp); //Make sure date_stamp is always an integer.

		$rf = new RequestFactory();
		$rf->setUserDate( $user_id, $date_stamp );

		switch( $type ) {
			case 10:
				$rf->setType( 30 ); //Vacation Request
				$rf->setStatus( 30 );
				$rf->setMessage( 'I would like to request 1 week vacation starting this friday.' );
				$rf->setCreatedBy( $user_id );

				break;
			case 20:
				$rf->setType( 40 ); //Schedule Request
				$rf->setStatus( 30 );
				$rf->setMessage( 'I would like to leave at 1pm this friday.' );
				$rf->setCreatedBy( $user_id );

				break;
			case 30:
				$rf->setType( 10 ); //Schedule Request
				$rf->setStatus( 30 );
				$rf->setMessage( 'Sorry, I forgot to punch out today. I left at 5:00PM' );
				$rf->setCreatedBy( $user_id );

				break;
		}

		if ( $rf->isValid() ) {
			$insert_id = $rf->Save();
			Debug::Text('Request ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Request!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;

	}

	function createTaskGroup( $company_id, $type, $parent_id = 0 ) {
		$jigf = new JobItemGroupFactory();
		$jigf->setCompany( $company_id );

		switch ( $type ) {
			case 10:
				$jigf->setParent( $parent_id );
				$jigf->setName( 'Construction' );
				break;
			case 20:
				$jigf->setParent( $parent_id );
				$jigf->setName( 'Inside' );
				break;
			case 30:
				$jigf->setParent( $parent_id );
				$jigf->setName( 'Outside' );
				break;
			case 40:
				$jigf->setParent( $parent_id );
				$jigf->setName( 'Projects' );
				break;
			case 50:
				$jigf->setParent( $parent_id );
				$jigf->setName( 'Accounting' );
				break;
			case 60:
				$jigf->setParent( $parent_id );
				$jigf->setName( 'Estimating' );
				break;

		}

		if ( $jigf->isValid() ) {
			$insert_id = $jigf->Save();
			Debug::Text('Job Group ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Job Group!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;

	}

	function createTask( $company_id, $type, $group_id, $product_id = NULL ) {
		$jif = new JobItemFactory();
		$jif->setCompany( $company_id );
		//$jif->setProduct( $data['product_id'] );
		$jif->setStatus( 10 );
		$jif->setType( 10 );
		//$jif->setGroup( $data['group_id'] );

		switch ( $type ) {
			case 10: //Framing
				$jif->setManualID( 1 );
				$jif->setName( 'Framing' );
				$jif->setDescription( 'Framing' );

				$jif->setEstimateTime( (3600*500) );
				$jif->setEstimateQuantity( 0 );
				$jif->setEstimateBadQuantity( 0 );
				$jif->setBadQuantityRate( 0 );
				$jif->setBillableRate( '80.00' );
				$jif->setMinimumTime( 3600 );
				$jif->setGroup( $group_id );

				break;
			case 20: //Sanding
				$jif->setManualID( 2 );
				$jif->setName( 'Sanding' );
				$jif->setDescription( 'Sanding' );

				$jif->setEstimateTime( (3600*300) );
				$jif->setEstimateQuantity( 0 );
				$jif->setEstimateBadQuantity( 0 );
				$jif->setBadQuantityRate( 0 );
				$jif->setBillableRate( '15.25' );
				$jif->setMinimumTime( (3600*2) );
				$jif->setGroup( $group_id );

				break;
			case 30: //Painting
				$jif->setManualID( 3 );
				$jif->setName( 'Painting' );
				$jif->setDescription( 'Painting' );

				$jif->setEstimateTime( (3600*400) );
				$jif->setEstimateQuantity( 0 );
				$jif->setEstimateBadQuantity( 0 );
				$jif->setBadQuantityRate( 0 );
				$jif->setBillableRate( '25.50' );
				$jif->setMinimumTime( (3600*1) );
				$jif->setGroup( $group_id );

				break;
			case 40: //Landscaping
				$jif->setManualID( 4 );
				$jif->setName( 'Land Scaping' );
				$jif->setDescription( 'Land Scaping' );

				$jif->setEstimateTime( (3600*600) );
				$jif->setEstimateQuantity( 0 );
				$jif->setEstimateBadQuantity( 0 );
				$jif->setBadQuantityRate( 0 );
				$jif->setBillableRate( '33' );
				$jif->setMinimumTime( (3600*1) );
				$jif->setGroup( $group_id );

				break;
			case 50:
				$jif->setManualID( 5 );
				$jif->setName( 'Data Entry' );
				$jif->setDescription( '' );

				$jif->setEstimateTime( (3600*600) );
				$jif->setEstimateQuantity( 0 );
				$jif->setEstimateBadQuantity( 0 );
				$jif->setBadQuantityRate( 0 );
				$jif->setBillableRate( '15' );
				$jif->setMinimumTime( (3600*1) );
				$jif->setGroup( $group_id );

				break;
			case 60:
				$jif->setManualID( 6 );
				$jif->setName( 'Accounting' );
				$jif->setDescription( '' );

				$jif->setEstimateTime( (3600*600) );
				$jif->setEstimateQuantity( 0 );
				$jif->setEstimateBadQuantity( 0 );
				$jif->setBadQuantityRate( 0 );
				$jif->setBillableRate( '45' );
				$jif->setMinimumTime( (3600*1) );
				$jif->setGroup( $group_id );

				break;
			case 70:
				$jif->setManualID( 7 );
				$jif->setName( 'Appraisals' );
				$jif->setDescription( '' );

				$jif->setEstimateTime( (3600*600) );
				$jif->setEstimateQuantity( 0 );
				$jif->setEstimateBadQuantity( 0 );
				$jif->setBadQuantityRate( 0 );
				$jif->setBillableRate( '50' );
				$jif->setMinimumTime( (3600*1) );
				$jif->setGroup( $group_id );

				break;

		}

		if ( $jif->isValid() ) {
			$insert_id = $jif->Save();
			Debug::Text('Task ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Task!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createJobGroup( $company_id, $type, $parent_id = 0 ) {
		$jgf = new JobGroupFactory();
		$jgf->setCompany( $company_id );

		switch ( $type ) {
			case 10:
				$jgf->setParent( $parent_id );
				$jgf->setName( 'Houses' );
				break;
			case 20:
				$jgf->setParent( $parent_id );
				$jgf->setName( 'Duplexes' );
				break;
			case 30:
				$jgf->setParent( $parent_id );
				$jgf->setName( 'Townhomes' );
				break;
			case 40:
				$jgf->setParent( $parent_id );
				$jgf->setName( 'Projects' );
				break;
			case 50:
				$jgf->setParent( $parent_id );
				$jgf->setName( 'Internal' );
				break;
			case 60:
				$jgf->setParent( $parent_id );
				$jgf->setName( 'External' );
				break;

		}

		if ( $jgf->isValid() ) {
			$insert_id = $jgf->Save();
			Debug::Text('Job Group ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Job Group!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;

	}

	function createJob( $company_id, $type, $item_id, $job_group_id = 0, $branch_id = 0, $department_id = 0, $client_id = NULL ) {
		$jf = new JobFactory();

		$jf->setCompany( $company_id );
		//$jf->setClient( $data['client_id'] );
		$jf->setStatus( 10 );
		//$jf->setGroup( $data['group_id'] );
		//$jf->setBranch( $data['branch_id'] );
		//$jf->setDepartment( $data['department_id'] );

		$jf->setDefaultItem( $item_id  );

		switch ( $type ) {
			case 10:
				$jf->setManualID( 10 );
				$jf->setName( 'House 1' );
				$jf->setDescription( rand(100,9999). ' Main St' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*500) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '20.00' );
				$jf->setMinimumTime( (3600*30) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );

				//$jf->setNote( $data['note'] );

				break;
			case 11:
				$jf->setManualID( 11 );
				$jf->setName( 'House 2' );
				$jf->setDescription( rand(100,9999). ' Springfield Rd' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 12:
				$jf->setManualID( 12 );
				$jf->setName( 'House 3' );
				$jf->setDescription( rand(100,9999). ' Spall Ave' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 13:
				$jf->setManualID( 13 );
				$jf->setName( 'House 4' );
				$jf->setDescription( rand(100,9999). ' Dobbin St' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 14:
				$jf->setManualID( 14 );
				$jf->setName( 'House 5' );
				$jf->setDescription( rand(100,9999). ' Sussex Court' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 15:
				$jf->setManualID( 15 );
				$jf->setName( 'House 6' );
				$jf->setDescription( rand(100,9999). ' Georgia St' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 16:
				$jf->setManualID( 16 );
				$jf->setName( 'House 7' );
				$jf->setDescription( rand(100,9999). ' Gates Rd' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 17:
				$jf->setManualID( 17 );
				$jf->setName( 'House 8' );
				$jf->setDescription( rand(100,9999). ' Lakeshore Rd' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 18:
				$jf->setManualID( 18 );
				$jf->setName( 'House 9' );
				$jf->setDescription( rand(100,9999). ' Main St' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 19:
				$jf->setManualID( 19 );
				$jf->setName( 'House 10' );
				$jf->setDescription( rand(100,9999). ' Ontario St' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*750) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '45.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				break;
			case 20:
				$jf->setManualID( 20 );
				$jf->setName( 'Project A' );
				$jf->setDescription( '' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*760) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '55.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				//$jf->setNote( $data['note'] );

				break;
			case 21:
				$jf->setManualID( 21 );
				$jf->setName( 'Project B' );
				$jf->setDescription( '' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*760) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '55.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				//$jf->setNote( $data['note'] );

				break;
			case 22:
				$jf->setManualID( 22 );
				$jf->setName( 'Project C' );
				$jf->setDescription( '' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*760) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '55.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				//$jf->setNote( $data['note'] );

				break;
			case 23:
				$jf->setManualID( 23 );
				$jf->setName( 'Project D' );
				$jf->setDescription( '' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*760) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '55.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				//$jf->setNote( $data['note'] );

				break;
			case 24:
				$jf->setManualID( 24 );
				$jf->setName( 'Project E' );
				$jf->setDescription( '' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*760) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '55.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				//$jf->setNote( $data['note'] );

				break;
			case 25:
				$jf->setManualID( 25 );
				$jf->setName( 'Project F' );
				$jf->setDescription( '' );

				$jf->setStartDate( time()-(86400*14) );
				$jf->setEndDate( time()+(86400*7) );

				$jf->setEstimateTime( (3600*760) );
				$jf->setEstimateQuantity( 0 );
				$jf->setEstimateBadQuantity( 0 );
				$jf->setBadQuantityRate( 0 );
				$jf->setBillableRate( '55.00' );
				$jf->setMinimumTime( (3600*100) );
				$jf->setGroup( $job_group_id );
				$jf->setBranch( $branch_id );
				$jf->setDepartment( $department_id );
				//$jf->setNote( $data['note'] );

				break;

		}

		if ( $jf->isValid() ) {
			$insert_id = $jf->Save();
			Debug::Text('Job ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Job!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;

	}

	function createRecurringSchedule( $company_id, $template_id, $start_date, $end_date, $user_ids ) {
		$rscf = new RecurringScheduleControlFactory();
		$rscf->setCompany( $company_id );
		$rscf->setRecurringScheduleTemplateControl( $template_id );
		$rscf->setStartWeek( 1 );
		$rscf->setStartDate( $start_date );
		$rscf->setEndDate( $end_date );
		$rscf->setAutoFill( FALSE );

		if ( $rscf->isValid() ) {
			$rscf->Save(FALSE);

			if ( isset($user_ids) ) {
				$rscf->setUser( $user_ids );
			}

			if ( $rscf->isValid() ) {
				$rscf->Save();
				Debug::Text('Saving Recurring Schedule...', __FILE__, __LINE__, __METHOD__,10);

				return TRUE;
			}
		}

		return FALSE;
	}

	function createRecurringScheduleTemplate( $company_id, $type, $schedule_policy_id = NULL ) {
		$rstcf = new RecurringScheduleTemplateControlFactory();
		$rstcf->setCompany( $company_id );

		switch ( $type ) {
			case 10: //Morning Shift
				$rstcf->setName( 'Morning Shift' );
				$rstcf->setDescription( '6:00AM - 3:00PM' );

				if ( $rstcf->isValid() ) {
					$rstc_id = $rstcf->Save();
					Debug::Text('bRecurring Schedule Template Control ID: '. $rstc_id , __FILE__, __LINE__, __METHOD__,10);

					//Week 1
					$rstf = new RecurringScheduleTemplateFactory();
					$rstf->setRecurringScheduleTemplateControl( $rstc_id );
					$rstf->setWeek( 1 );
					$rstf->setSun( FALSE );
					$rstf->setMon( TRUE );
					$rstf->setTue( TRUE );
					$rstf->setWed( TRUE );
					$rstf->setThu( TRUE );
					$rstf->setFri( TRUE );
					$rstf->setSat( FALSE );

					$rstf->setStartTime( strtotime('06:00 AM') );
					$rstf->setEndTime( strtotime('03:00 PM') );

					if ( $schedule_policy_id > 0 ) {
						$rstf->setSchedulePolicyID( $schedule_policy_id );
					}
					$rstf->setBranch( '-1' ); //Default
					$rstf->setDepartment( '-1' ); //Default

					if ( $rstf->isValid() ) {
						Debug::Text('Saving Recurring Schedule Week...', __FILE__, __LINE__, __METHOD__,10);
						$rstf->Save();
					}

					return $rstc_id;
				}

				break;
			case 20: //Afternoon Shift
				$rstcf->setName( 'Afternoon Shift' );
				$rstcf->setDescription( '10:00AM - 7:00PM' );

				if ( $rstcf->isValid() ) {
					$rstc_id = $rstcf->Save();
					Debug::Text('bRecurring Schedule Template Control ID: '. $rstc_id , __FILE__, __LINE__, __METHOD__,10);

					//Week 1
					$rstf = new RecurringScheduleTemplateFactory();
					$rstf->setRecurringScheduleTemplateControl( $rstc_id );
					$rstf->setWeek( 1 );
					$rstf->setSun( FALSE );
					$rstf->setMon( TRUE );
					$rstf->setTue( TRUE );
					$rstf->setWed( TRUE );
					$rstf->setThu( TRUE );
					$rstf->setFri( TRUE );
					$rstf->setSat( FALSE );

					$rstf->setStartTime( strtotime('10:00 AM') );
					$rstf->setEndTime( strtotime('07:00 PM') );

					if ( $schedule_policy_id > 0 ) {
						$rstf->setSchedulePolicyID( $schedule_policy_id );
					}
					$rstf->setBranch( '-1' ); //Default
					$rstf->setDepartment( '-1' ); //Default

					if ( $rstf->isValid() ) {
						Debug::Text('Saving Recurring Schedule Week...', __FILE__, __LINE__, __METHOD__,10);
						$rstf->Save();
					}

					return $rstc_id;
				}

				break;
			case 30: //Evening Shift
				$rstcf->setName( 'Evening Shift' );
				$rstcf->setDescription( '2:00PM - 11:00PM' );

				if ( $rstcf->isValid() ) {
					$rstc_id = $rstcf->Save();
					Debug::Text('bRecurring Schedule Template Control ID: '. $rstc_id , __FILE__, __LINE__, __METHOD__,10);

					//Week 1
					$rstf = new RecurringScheduleTemplateFactory();
					$rstf->setRecurringScheduleTemplateControl( $rstc_id );
					$rstf->setWeek( 1 );
					$rstf->setSun( FALSE );
					$rstf->setMon( TRUE );
					$rstf->setTue( TRUE );
					$rstf->setWed( TRUE );
					$rstf->setThu( TRUE );
					$rstf->setFri( TRUE );
					$rstf->setSat( FALSE );

					$rstf->setStartTime( strtotime('02:00 PM') );
					$rstf->setEndTime( strtotime('11:00 PM') );

					if ( $schedule_policy_id > 0 ) {
						$rstf->setSchedulePolicyID( $schedule_policy_id );
					}
					$rstf->setBranch( '-1' ); //Default
					$rstf->setDepartment( '-1' ); //Default

					if ( $rstf->isValid() ) {
						Debug::Text('Saving Recurring Schedule Week...', __FILE__, __LINE__, __METHOD__,10);
						$rstf->Save();
					}

					return $rstc_id;
				}

				break;
			case 40: //Split Shift
				$rstcf->setName( 'Split Shift' );
				$rstcf->setDescription( '8:00AM-12:00PM, 5:00PM-9:00PM ' );

				if ( $rstcf->isValid() ) {
					$rstc_id = $rstcf->Save();
					Debug::Text('bRecurring Schedule Template Control ID: '. $rstc_id , __FILE__, __LINE__, __METHOD__,10);

					//Week 1
					$rstf = new RecurringScheduleTemplateFactory();
					$rstf->setRecurringScheduleTemplateControl( $rstc_id );
					$rstf->setWeek( 1 );
					$rstf->setSun( FALSE );
					$rstf->setMon( TRUE );
					$rstf->setTue( TRUE );
					$rstf->setWed( TRUE );
					$rstf->setThu( TRUE );
					$rstf->setFri( TRUE );
					$rstf->setSat( FALSE );

					$rstf->setStartTime( strtotime('08:00 AM') );
					$rstf->setEndTime( strtotime('12:00 PM') );

					if ( $schedule_policy_id > 0 ) {
						$rstf->setSchedulePolicyID( $schedule_policy_id );
					}
					$rstf->setBranch( '-1' ); //Default
					$rstf->setDepartment( '-1' ); //Default

					if ( $rstf->isValid() ) {
						Debug::Text('Saving Recurring Schedule Week...', __FILE__, __LINE__, __METHOD__,10);
						$rstf->Save();
					}
					//Week 1
					$rstf = new RecurringScheduleTemplateFactory();
					$rstf->setRecurringScheduleTemplateControl( $rstc_id );
					$rstf->setWeek( 1 );
					$rstf->setSun( FALSE );
					$rstf->setMon( TRUE );
					$rstf->setTue( TRUE );
					$rstf->setWed( TRUE );
					$rstf->setThu( TRUE );
					$rstf->setFri( TRUE );
					$rstf->setSat( FALSE );

					$rstf->setStartTime( strtotime('05:00 PM') );
					$rstf->setEndTime( strtotime('9:00 PM') );

					if ( $schedule_policy_id > 0 ) {
						$rstf->setSchedulePolicyID( $schedule_policy_id );
					}
					$rstf->setBranch( '-1' ); //Default
					$rstf->setDepartment( '-1' ); //Default

					if ( $rstf->isValid() ) {
						Debug::Text('Saving Recurring Schedule Week...', __FILE__, __LINE__, __METHOD__,10);
						$rstf->Save();
					}

					return $rstc_id;
				}

				break;
			case 50: //Full Rotation
				$rstcf->setName( 'Full Rotation' );
				$rstcf->setDescription( '1wk-Morning, 1wk-Afternoon, 1wk-Evening' );

				if ( $rstcf->isValid() ) {
					$rstc_id = $rstcf->Save();
					Debug::Text('bRecurring Schedule Template Control ID: '. $rstc_id , __FILE__, __LINE__, __METHOD__,10);

					//Week 1
					$rstf = new RecurringScheduleTemplateFactory();
					$rstf->setRecurringScheduleTemplateControl( $rstc_id );
					$rstf->setWeek( 1 );
					$rstf->setSun( FALSE );
					$rstf->setMon( TRUE );
					$rstf->setTue( TRUE );
					$rstf->setWed( TRUE );
					$rstf->setThu( TRUE );
					$rstf->setFri( TRUE );
					$rstf->setSat( FALSE );

					$rstf->setStartTime( strtotime('06:00 AM') );
					$rstf->setEndTime( strtotime('03:00 PM') );

					if ( $schedule_policy_id > 0 ) {
						$rstf->setSchedulePolicyID( $schedule_policy_id );
					}
					$rstf->setBranch( '-1' ); //Default
					$rstf->setDepartment( '-1' ); //Default

					if ( $rstf->isValid() ) {
						Debug::Text('Saving Recurring Schedule Week...', __FILE__, __LINE__, __METHOD__,10);
						$rstf->Save();
					}

					//Week 2
					$rstf = new RecurringScheduleTemplateFactory();
					$rstf->setRecurringScheduleTemplateControl( $rstc_id );
					$rstf->setWeek( 2 );
					$rstf->setSun( FALSE );
					$rstf->setMon( TRUE );
					$rstf->setTue( TRUE );
					$rstf->setWed( TRUE );
					$rstf->setThu( TRUE );
					$rstf->setFri( TRUE );
					$rstf->setSat( FALSE );

					$rstf->setStartTime( strtotime('10:00 AM') );
					$rstf->setEndTime( strtotime('07:00 PM') );

					if ( $schedule_policy_id > 0 ) {
						$rstf->setSchedulePolicyID( $schedule_policy_id );
					}
					$rstf->setBranch( '-1' ); //Default
					$rstf->setDepartment( '-1' ); //Default

					if ( $rstf->isValid() ) {
						Debug::Text('Saving Recurring Schedule Week...', __FILE__, __LINE__, __METHOD__,10);
						$rstf->Save();
					}
					//Week 3
					$rstf = new RecurringScheduleTemplateFactory();
					$rstf->setRecurringScheduleTemplateControl( $rstc_id );
					$rstf->setWeek( 3 );
					$rstf->setSun( FALSE );
					$rstf->setMon( TRUE );
					$rstf->setTue( TRUE );
					$rstf->setWed( TRUE );
					$rstf->setThu( TRUE );
					$rstf->setFri( TRUE );
					$rstf->setSat( FALSE );

					$rstf->setStartTime( strtotime('02:00 PM') );
					$rstf->setEndTime( strtotime('11:00 PM') );

					if ( $schedule_policy_id > 0 ) {
						$rstf->setSchedulePolicyID( $schedule_policy_id );
					}
					$rstf->setBranch( '-1' ); //Default
					$rstf->setDepartment( '-1' ); //Default

					if ( $rstf->isValid() ) {
						Debug::Text('Saving Recurring Schedule Week...', __FILE__, __LINE__, __METHOD__,10);
						$rstf->Save();
					}

					return $rstc_id;
				}

				break;

		}

		Debug::Text('ERROR Saving schedule template!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function createSchedule( $user_id, $date_stamp, $data = NULL ) {
		$sf = new ScheduleFactory();
		$sf->setUserDateId( UserDateFactory::findOrInsertUserDate( $user_id, $date_stamp) );

		if ( isset($data['status_id']) ) {
			$sf->setStatus( $data['status_id'] );
		} else {
			$sf->setStatus( 10 );
		}

		if ( isset($data['schedule_policy_id']) ) {
			$sf->setSchedulePolicyID( $data['schedule_policy_id'] );
		}

		if ( isset($data['absence_policy_id']) ) {
			$sf->setAbsencePolicyID( $data['absence_policy_id'] );
		}
		if ( isset($data['branch_id']) ) {
			$sf->setBranch( $data['branch_id'] );
		}
		if ( isset($data['department_id']) ) {
			$sf->setDepartment( $data['department_id'] );
		}

		if ( isset($data['job_id']) ) {
			$sf->setJob( $data['job_id'] );
		}

		if ( isset($data['job_item_id'] ) ) {
			$sf->setJobItem( $data['job_item_id'] );
		}

		if ( $data['start_time'] != '') {
			$start_time = strtotime( $data['start_time'], $date_stamp ) ;
		}
		if ( $data['end_time'] != '') {
			Debug::Text('End Time: '. $data['end_time'] .' Date Stamp: '. $date_stamp , __FILE__, __LINE__, __METHOD__,10);
			$end_time = strtotime( $data['end_time'], $date_stamp ) ;
			Debug::Text('bEnd Time: '. $data['end_time'] .' - '. TTDate::getDate('DATE+TIME',$data['end_time']) , __FILE__, __LINE__, __METHOD__,10);
		}

		$sf->setStartTime( $start_time );
		$sf->setEndTime( $end_time );

		if ( $sf->isValid() ) {
			$sf->setEnableReCalculateDay(FALSE);
			$insert_id = $sf->Save();
			Debug::Text('Schedule ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Schedule!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function deletePunch( $id ) {

		$plf = new PunchListFactory();
		$plf->getById( $id );
		if ( $plf->getRecordCount() > 0 ) {
			Debug::Text('Deleting Punch ID: '. $id, __FILE__, __LINE__, __METHOD__,10);
			foreach($plf as $p_obj) {
				$p_obj->setUser( $p_obj->getPunchControlObject()->getUserDateObject()->getUser() );
				$p_obj->setDeleted(TRUE);
				$p_obj->setEnableCalcTotalTime( TRUE );
				$p_obj->setEnableCalcSystemTotalTime( TRUE );
				$p_obj->setEnableCalcWeeklySystemTotalTime( TRUE );
				$p_obj->setEnableCalcUserDateTotal( TRUE );
				$p_obj->setEnableCalcException( TRUE );
				$p_obj->Save();
			}
			Debug::Text('Deleting Punch ID: '. $id .' Done...', __FILE__, __LINE__, __METHOD__,10);

			return TRUE;
		}

		return FALSE;
	}

	function editPunch( $id, $data = NULL ) {
		if ( $id == '' ) {
			return FALSE;
		}

		//Edit out punch so its on the next day.
		$plf = new PunchListFactory();
		$plf->getById( $id );
		if ( $plf->getRecordCount() == 1 ) {
			//var_dump($data);
			$p_obj = $plf->getCurrent();

			//$p_obj->setUser( $this->user_id );

			if ( isset($data['type_id']) ) {
				$p_obj->setType( $data['type_id'] );
			}

			if ( isset($data['status_id']) ) {
				$p_obj->setStatus( $data['status_id'] );
			}

			if ( isset($data['time_stamp']) ) {
				$p_obj->setTimeStamp( $data['time_stamp'] );
			}

			if ( $p_obj->isValid() == TRUE )  {
				$p_obj->Save( FALSE );

				$p_obj->getPunchControlObject()->setPunchObject( $p_obj );
				$p_obj->getPunchControlObject()->setEnableCalcUserDateID( TRUE );
				$p_obj->getPunchControlObject()->setEnableCalcSystemTotalTime( TRUE );
				$p_obj->getPunchControlObject()->setEnableCalcWeeklySystemTotalTime( TRUE );
				$p_obj->getPunchControlObject()->setEnableCalcException( TRUE );
				$p_obj->getPunchControlObject()->setEnablePreMatureException( TRUE );
				$p_obj->getPunchControlObject()->setEnableCalcUserDateTotal( TRUE );
				$p_obj->getPunchControlObject()->setEnableCalcTotalTime( TRUE );

				if ( $p_obj->getPunchControlObject()->isValid() == TRUE ) {
					$p_obj->getPunchControlObject()->Save();

					return TRUE;
				}
			}
		}

		return FALSE;
	}

	function createPunchPair( $user_id, $in_time_stamp, $out_time_stamp, $data = NULL, $calc_total_time = TRUE ) {
		$fail_transaction = FALSE;

		Debug::Text('Punch Full In Time Stamp: ('.$in_time_stamp.') '. TTDate::getDate('DATE+TIME', $in_time_stamp) .' Out: ('.$out_time_stamp.') '. TTDate::getDate('DATE+TIME', $out_time_stamp), __FILE__, __LINE__, __METHOD__,10);

		$pf = new PunchFactory();
		$pf->StartTransaction();

		//Out Punch
		//Save out punch first so the $pf object is for the In Punch if there is one.
		if ( $out_time_stamp !== NULL ) {
			$pf_in = new PunchFactory();
			$pf_in->setTransfer( FALSE );
			$pf_in->setUser( $user_id );
			$pf_in->setType( $data['out_type_id'] );
			$pf_in->setStatus( 20 );
			$pf_in->setTimeStamp( $out_time_stamp );

			if ( $pf_in->isNew() ) {
				$pf_in->setActualTimeStamp( $out_time_stamp );
				$pf_in->setOriginalTimeStamp( $pf_in->getTimeStamp() );
			}

			$pf_in->setPunchControlID( $pf_in->findPunchControlID() );
			if ( $pf_in->isValid() ) {
				if ( $pf_in->Save( FALSE ) === FALSE ) {
					Debug::Text(' aFail Transaction: ', __FILE__, __LINE__, __METHOD__,10);
					$fail_transaction = TRUE;
				}
			}
		}

		if ( $in_time_stamp !== NULL ) {
			//In Punch
			$pf_out = new PunchFactory();
			$pf_out->setTransfer( FALSE );
			$pf_out->setUser( $user_id );
			$pf_out->setType( $data['in_type_id'] );
			$pf_out->setStatus( 10 );
			$pf_out->setTimeStamp( $in_time_stamp );

			if ( $pf_out->isNew() ) {
				$pf_out->setActualTimeStamp( $in_time_stamp );
				$pf_out->setOriginalTimeStamp( $pf_out->getTimeStamp() );
			}

			if ( isset($pf_in) AND $pf_in->getPunchControlID() != FALSE ) { //Get Punch Control ID from above Out punch.
				$pf_out->setPunchControlID( $pf_in->getPunchControlID() );
			} else {
				$pf_out->setPunchControlID( $pf_out->findPunchControlID() );
			}

			if ( $pf_out->isValid() ) {
				if ( $pf_out->Save( FALSE ) === FALSE ) {
					Debug::Text(' aFail Transaction: ', __FILE__, __LINE__, __METHOD__,10);
					$fail_transaction = TRUE;
				}
			}
		}

		if ( $fail_transaction == FALSE ) {
			if ( isset($pf_in) AND is_object($pf_in) ) {
				Debug::Text(' Using In Punch Object... TimeStamp: '. $pf_in->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);
				$pf = $pf_in;
			} elseif ( isset($pf_out) AND is_object( $pf_out ) ) {
				Debug::Text(' Using Out Punch Object... TimeStamp: '. $pf_out->getTimeStamp(), __FILE__, __LINE__, __METHOD__,10);
				$pf = $pf_out;
			}

			$pcf = new PunchControlFactory();
			$pcf->setId( $pf->getPunchControlID() );
			$pcf->setPunchObject( $pf );
			$pcf->setBranch( $data['branch_id'] );
			$pcf->setDepartment( $data['department_id'] );
			if ( isset($data['job_id']) ) {
				$pcf->setJob( $data['job_id'] );
			}
			if ( isset($data['job_item_id']) ) {
				$pcf->setJobItem( $data['job_item_id'] );
			}
			if ( isset($data['quantity']) ) {
				$pcf->setQuantity( $data['quantity'] );
			}
			if ( isset($data['bad_quantity']) ) {
				$pcf->setBadQuantity( $data['bad_quantity'] );
			}

			$pcf->setEnableCalcUserDateID( TRUE );
			$pcf->setEnableCalcTotalTime( $calc_total_time );
			$pcf->setEnableCalcSystemTotalTime( $calc_total_time );
			$pcf->setEnableCalcWeeklySystemTotalTime( $calc_total_time );
			$pcf->setEnableCalcUserDateTotal( $calc_total_time );
			$pcf->setEnableCalcException( $calc_total_time );

			if ( $pcf->isValid() == TRUE ) {
				$punch_control_id = $pcf->Save(TRUE, TRUE); //Force lookup

				if ( $fail_transaction == FALSE ) {
					Debug::Text('Punch Control ID: '. $punch_control_id, __FILE__, __LINE__, __METHOD__,10);
					$pf->CommitTransaction();

					return TRUE;
				}
			}
		}

		Debug::Text('Failed Creating Punch!', __FILE__, __LINE__, __METHOD__,10);
		$pf->FailTransaction();

		return FALSE;
	}

	function createAccrualBalance( $user_id, $accrual_policy_id, $type = 30) {
		$af = new AccrualFactory();

		$af->setUser( $user_id );
		$af->setType( $type ); //Awarded
		$af->setAccrualPolicyID( $accrual_policy_id );
		$af->setAmount( rand((3600*8),(3600*24))  );
		$af->setTimeStamp( time()-(86400*3) );
		$af->setEnableCalcBalance( TRUE );

		if ( $af->isValid() ) {
			$insert_id = $af->Save();
			Debug::Text('Accrual ID: '. $insert_id, __FILE__, __LINE__, __METHOD__,10);

			return $insert_id;
		}

		Debug::Text('Failed Creating Accrual Balance!', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;

	}

	function generateData() {
		global $current_company, $current_user;

		TTDate::setTimeZone('PST8PDT');

		$current_epoch = time();

		$cf = new CompanyFactory();
		$cf->StartTransaction();

		$company_id = $this->createCompany();

		$clf = new CompanyListFactory();
		$clf->getById( $company_id );
		$current_company = $clf->getCurrent();

		if ( $company_id !== FALSE ) {
			Debug::Text('Company Created Successfully!', __FILE__, __LINE__, __METHOD__,10);

			$this->createPermissionGroups( $company_id );

			//Create currency
			$currency_ids[] = $this->createCurrency( $company_id, 10 ); //USD
			$currency_ids[] = $this->createCurrency( $company_id, 20 ); //CAD

			//Create branch
			$branch_ids[] = $this->createBranch( $company_id, 10 ); //NY
			$branch_ids[] = $this->createBranch( $company_id, 20 ); //WA

			//Create departments
			$department_ids[] = $this->createDepartment( $company_id, 10 );
			$department_ids[] = $this->createDepartment( $company_id, 20 );

			//Create stations
			$station_id = $this->createStation( $company_id );

			//Create pay stub accounts.
			$this->createPayStubAccount( $company_id );

			//Link pay stub accounts.
			$this->createPayStubAccountLink( $company_id );

			//Company Deductions
			$this->createCompanyDeduction( $company_id );

			//Wage Groups
			$wage_group_ids[] = $this->createUserWageGroups( $company_id );

			//User Groups
			$user_group_ids[] = $this->createUserGroup( $company_id, 10, 0 );
			$user_group_ids[] = $this->createUserGroup( $company_id, 20, $user_group_ids[0] );
			$user_group_ids[] = $this->createUserGroup( $company_id, 30, $user_group_ids[0] );
			$user_group_ids[] = $this->createUserGroup( $company_id, 40, 0 );
			$user_group_ids[] = $this->createUserGroup( $company_id, 50, $user_group_ids[3] );

			//Users
			$user_ids[] = $this->createUser( $company_id, 10, 0, $branch_ids[0], $department_ids[0], $currency_ids[0], $user_group_ids[0] );
			$user_ids[] = $this->createUser( $company_id, 11, 0, $branch_ids[0], $department_ids[1], $currency_ids[0], $user_group_ids[0] );
			$user_ids[] = $this->createUser( $company_id, 12, 0, $branch_ids[0], $department_ids[1], $currency_ids[0], $user_group_ids[0] );
			$user_ids[] = $this->createUser( $company_id, 13, 0, $branch_ids[0], $department_ids[1], $currency_ids[0], $user_group_ids[0] );
			$user_ids[] = $this->createUser( $company_id, 14, 0, $branch_ids[0], $department_ids[1], $currency_ids[0], $user_group_ids[1] );
			$user_ids[] = $this->createUser( $company_id, 15, 0, $branch_ids[0], $department_ids[0], $currency_ids[0], $user_group_ids[1] );
			$user_ids[] = $this->createUser( $company_id, 16, 0, $branch_ids[0], $department_ids[1], $currency_ids[0], $user_group_ids[1] );
			$user_ids[] = $this->createUser( $company_id, 17, 0, $branch_ids[0], $department_ids[1], $currency_ids[0], $user_group_ids[1] );
			$user_ids[] = $this->createUser( $company_id, 18, 0, $branch_ids[0], $department_ids[0], $currency_ids[0], $user_group_ids[2] );
			$user_ids[] = $this->createUser( $company_id, 19, 0, $branch_ids[0], $department_ids[1], $currency_ids[0], $user_group_ids[2] );
			$user_ids[] = $this->createUser( $company_id, 20, 0, $branch_ids[0], $department_ids[1], $currency_ids[0], $user_group_ids[2] );
			$user_ids[] = $this->createUser( $company_id, 21, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[3] );
			$user_ids[] = $this->createUser( $company_id, 22, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[3] );
			$user_ids[] = $this->createUser( $company_id, 23, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[3] );
			$user_ids[] = $this->createUser( $company_id, 24, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[3] );
			$user_ids[] = $this->createUser( $company_id, 25, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[4] );
			$user_ids[] = $this->createUser( $company_id, 26, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[4] );
			$user_ids[] = $this->createUser( $company_id, 27, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[4] );
			$user_ids[] = $this->createUser( $company_id, 28, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[4] );
			$user_ids[] = $this->createUser( $company_id, 29, 0, $branch_ids[1], $department_ids[1], $currency_ids[0], $user_group_ids[4] );
			$user_ids[] = $this->createUser( $company_id, 30, 0, $branch_ids[1], $department_ids[0], $currency_ids[0], $user_group_ids[4] );
			$user_ids[] = $this->createUser( $company_id, 40, 0, $branch_ids[1], $department_ids[0], $currency_ids[0], $user_group_ids[4] );
			$current_user_id = $user_ids[] = $this->createUser( $company_id, 100, 0, $branch_ids[0], $department_ids[0], $currency_ids[0], $user_group_ids[1] );

			//Create random users.
			for( $i=0; $i <= $this->getMaxRandomUsers(); $i++ ) {
				$tmp_user_id = $this->createUser( $company_id, 999, 0, $branch_ids[($i%2)], $department_ids[($i%2)], $currency_ids[0], $user_group_ids[($i%5)] );
				if ( $tmp_user_id != FALSE ) {
					$user_ids[] = $tmp_user_id;
				}
			}

			Debug::Arr($user_ids, 'All User IDs:', __FILE__, __LINE__, __METHOD__,10);

			$ulf = new UserListFactory();
			$ulf->getById( $current_user_id );
			$current_user = $ulf->getCurrent();
			unset($current_user_id);

			//Create policies
			$policy_ids['round'][] = $this->createRoundingPolicy( $company_id, 10 ); //In
			$policy_ids['round'][] = $this->createRoundingPolicy( $company_id, 20 ); //Out

			$policy_ids['accrual'][] = $this->createAccrualPolicy( $company_id, 10 ); //Bank Time
			$policy_ids['accrual'][] = $this->createAccrualPolicy( $company_id, 20 ); //Vacaction
			$policy_ids['accrual'][] = $this->createAccrualPolicy( $company_id, 30 ); //Sick

			$policy_ids['overtime'][] = $this->createOverTimePolicy( $company_id, 10 );
			$policy_ids['overtime'][] = $this->createOverTimePolicy( $company_id, 20, $policy_ids['accrual'][0] );

			$policy_ids['premium'][] = $this->createPremiumPolicy( $company_id, 10 );

			$policy_ids['absence'][] = $this->createAbsencePolicy( $company_id, 10, $policy_ids['accrual'][1] );
			$policy_ids['absence'][] = $this->createAbsencePolicy( $company_id, 20, $policy_ids['accrual'][0] );
			$policy_ids['absence'][] = $this->createAbsencePolicy( $company_id, 30, $policy_ids['accrual'][2] );

			$policy_ids['meal_1'] = $this->createMealPolicy( $company_id );

			$policy_ids['schedule_1'] = $this->createSchedulePolicy( $company_id, $policy_ids['meal_1'] );

			$policy_ids['exception_1'] = $this->createExceptionPolicy( $company_id );

			$hierarchy_user_ids = $user_ids;
			$root_user_id = array_pop( $hierarchy_user_ids );
			unset($hierarchy_user_ids[0], $hierarchy_user_ids[1] );

			//Create authorization hierarchy
			$hierarchy_control_id = $this->createAuthorizationHierarchyControl( $company_id, $hierarchy_user_ids );

			if ( $root_user_id == FALSE ) {
				Debug::Text('Administrator wasn\'t created! Duplicate username perhaps? Are we appending a random number?', __FILE__, __LINE__, __METHOD__,10);
			}
			//Admin user at the top
			$this->createAuthorizationHierarchyLevel( $company_id, $hierarchy_control_id, $root_user_id, 1);
			$this->createAuthorizationHierarchyLevel( $company_id, $hierarchy_control_id, $user_ids[0], 2 );
			$this->createAuthorizationHierarchyLevel( $company_id, $hierarchy_control_id, $user_ids[1], 3 );
			unset($hierarchy_user_ids, $root_user_id);

			//Pay Period Schedule
			$this->createPayPeriodSchedule( $company_id, $user_ids );

			//Create Policy Group
			$this->createPolicyGroup( 	$company_id,
										$policy_ids['meal_1'],
										$policy_ids['exception_1'],
										NULL,
										$policy_ids['overtime'],
										$policy_ids['premium'],
										$policy_ids['round'],
										$user_ids );

			if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
				//Task Groups
				$task_group_ids[] = $this->createTaskGroup( $company_id, 10, 0 );
				$task_group_ids[] = $this->createTaskGroup( $company_id, 20, $task_group_ids[0] );
				$task_group_ids[] = $this->createTaskGroup( $company_id, 30, $task_group_ids[0] );
				$task_group_ids[] = $this->createTaskGroup( $company_id, 40, 0 );
				$task_group_ids[] = $this->createTaskGroup( $company_id, 50, $task_group_ids[3] );
				$task_group_ids[] = $this->createTaskGroup( $company_id, 60, $task_group_ids[3] );

				//Job Tasks
				$default_task_id = $this->createTask( $company_id, 10, $task_group_ids[2] );
				$task_ids[] = $this->createTask( $company_id, 20, $task_group_ids[1] );
				$task_ids[] = $this->createTask( $company_id, 30, $task_group_ids[1] );
				$task_ids[] = $this->createTask( $company_id, 40, $task_group_ids[2] );

				$task_ids[] = $this->createTask( $company_id, 50, $task_group_ids[4] );
				$task_ids[] = $this->createTask( $company_id, 60, $task_group_ids[4] );
				$task_ids[] = $this->createTask( $company_id, 70, $task_group_ids[5] );

				//Job Groups
				$job_group_ids[] = $this->createJobGroup( $company_id, 10, 0 );
				$job_group_ids[] = $this->createJobGroup( $company_id, 20, $job_group_ids[0] );
				$job_group_ids[] = $this->createJobGroup( $company_id, 30, $job_group_ids[0] );
				$job_group_ids[] = $this->createJobGroup( $company_id, 40, 0 );
				$job_group_ids[] = $this->createJobGroup( $company_id, 50, $job_group_ids[3] );
				$job_group_ids[] = $this->createJobGroup( $company_id, 60, $job_group_ids[3] );

				//Jobs
				$job_ids[] = $this->createJob( $company_id, 10, $default_task_id, $job_group_ids[1], $branch_ids[0], $department_ids[0] );
				$job_ids[] = $this->createJob( $company_id, 11, $default_task_id, $job_group_ids[1], $branch_ids[0], $department_ids[0] );
				$job_ids[] = $this->createJob( $company_id, 12, $default_task_id, $job_group_ids[1], $branch_ids[0], $department_ids[0] );
				$job_ids[] = $this->createJob( $company_id, 13, $default_task_id, $job_group_ids[1], $branch_ids[0], $department_ids[0] );
				$job_ids[] = $this->createJob( $company_id, 14, $default_task_id, $job_group_ids[1], $branch_ids[0], $department_ids[0] );
				$job_ids[] = $this->createJob( $company_id, 15, $default_task_id, $job_group_ids[2], $branch_ids[1], $department_ids[1] );
				$job_ids[] = $this->createJob( $company_id, 16, $default_task_id, $job_group_ids[2], $branch_ids[1], $department_ids[1] );
				$job_ids[] = $this->createJob( $company_id, 17, $default_task_id, $job_group_ids[2], $branch_ids[1], $department_ids[1] );
				$job_ids[] = $this->createJob( $company_id, 18, $default_task_id, $job_group_ids[2], $branch_ids[1], $department_ids[1] );
				$job_ids[] = $this->createJob( $company_id, 19, $default_task_id, $job_group_ids[2], $branch_ids[1], $department_ids[1] );

				$job_ids[] = $this->createJob( $company_id, 20, $default_task_id, $job_group_ids[4], $branch_ids[0], $department_ids[0] );
				$job_ids[] = $this->createJob( $company_id, 21, $default_task_id, $job_group_ids[4], $branch_ids[0], $department_ids[0] );
				$job_ids[] = $this->createJob( $company_id, 22, $default_task_id, $job_group_ids[4], $branch_ids[0], $department_ids[0] );
				$job_ids[] = $this->createJob( $company_id, 23, $default_task_id, $job_group_ids[5], $branch_ids[1], $department_ids[1] );
				$job_ids[] = $this->createJob( $company_id, 24, $default_task_id, $job_group_ids[5], $branch_ids[1], $department_ids[1] );
				$job_ids[] = $this->createJob( $company_id, 25, $default_task_id, $job_group_ids[5], $branch_ids[1], $department_ids[1] );

			} else {
				$task_ids[] = 0;
				$job_ids[] = 0;
			}

			//Create Accrual balances
			foreach( $user_ids as $user_id ) {
				foreach( $policy_ids['accrual'] as $accrual_policy_id ) {
					$this->createAccrualBalance( $user_id, $accrual_policy_id );
				}
				unset($accrual_policy_id);
			}

			//Create recurring schedule templates
			$recurring_schedule_ids[] = $this->createRecurringScheduleTemplate( $company_id, 10, $policy_ids['schedule_1'] ); //Morning shift
			$recurring_schedule_ids[] = $this->createRecurringScheduleTemplate( $company_id, 20, $policy_ids['schedule_1'] ); //Afternoon shift
			$recurring_schedule_ids[] = $this->createRecurringScheduleTemplate( $company_id, 30, $policy_ids['schedule_1'] ); //Evening shift
			$recurring_schedule_ids[] = $this->createRecurringScheduleTemplate( $company_id, 40 ); //Split Shift
			$recurring_schedule_ids[] = $this->createRecurringScheduleTemplate( $company_id, 50, $policy_ids['schedule_1'] ); //Full rotation

			$recurring_schedule_start_date = TTDate::getBeginWeekEpoch($current_epoch+(86400*7.5));
			$this->createRecurringSchedule( $company_id, $recurring_schedule_ids[0], $recurring_schedule_start_date, '', array( $user_ids[0],$user_ids[1],$user_ids[2],$user_ids[3], $user_ids[4] ) );
			$this->createRecurringSchedule( $company_id, $recurring_schedule_ids[1], $recurring_schedule_start_date, '', array( $user_ids[5],$user_ids[6],$user_ids[7],$user_ids[8], $user_ids[9] ) );
			$this->createRecurringSchedule( $company_id, $recurring_schedule_ids[2], $recurring_schedule_start_date, '', array( $user_ids[10],$user_ids[11],$user_ids[12],$user_ids[13], $user_ids[14] ) );

			//Create schedule for each employee.
			foreach( $user_ids as $user_id ) {
				//Create schedule starting 6 weeks ago, up to the end of the week.
				$schedule_options_arr = array(
											'status_id' => 10,
											'start_time' => '08:00AM',
											'end_time' => '05:00PM',
											'schedule_policy_id' => $policy_ids['schedule_1'],
											);

				//$schedule_date = ($current_epoch-(86400*42));
				$schedule_date = ($current_epoch-(86400*14));
				$schedule_end_date = TTDate::getEndWeekEpoch( $current_epoch );
				//$schedule_date = ($current_epoch-(86400*14));
				//$schedule_end_date = ($current_epoch+(86400*28));
				while ( $schedule_date <= $schedule_end_date ) {
					//Random departments/branches
					$schedule_options_arr['branch_id'] = $branch_ids[rand(0,count($branch_ids)-1)];
					$schedule_options_arr['department_id'] = $department_ids[rand(0,count($department_ids)-1)];

					//Skip weekends.
					if ( date('w',$schedule_date) != 0 AND date('w',$schedule_date) != 6 ) {
						$this->createSchedule( $user_id, $schedule_date, $schedule_options_arr);
					}
					$schedule_date+=86400;
				}
				//break;

				unset($schedule_options_arr, $schedule_date, $schedule_end_date, $user_id);
			}


			//Punch users in/out randomly.
			foreach( $user_ids as $user_id ) {
				//Pick random jobs/tasks that are used for the entire date range.
				//So one employee isn't punching into 15 jobs.
				$user_random_job_ids = array_rand($job_ids, 2 );
				$user_random_task_ids = array_rand($job_ids, 3 );

				//Create punches starting 6 weeks ago, up to the end of the week.
				//$start_date = $punch_date = ($current_epoch-(86400*42));
				$start_date = $punch_date = ($current_epoch-(86400*14));
				$end_date = TTDate::getEndWeekEpoch( $current_epoch );
				//$start_date = $punch_date = ($current_epoch-(86400*14));
				//$end_date = ($current_epoch+(86400*28));
				$i=0;
				while ( $punch_date <= $end_date ) {
					$date_stamp = TTDate::getDate('DATE', $punch_date );
					//$punch_full_time_stamp = strtotime($pc_data['date_stamp'].' '.$pc_data['time_stamp']);
					$exception_cutoff_date = $current_epoch-(86400*14);

					if ( date('w',$punch_date) != 0 AND date('w',$punch_date) != 6 ) {
						if ( $punch_date >= $exception_cutoff_date
								AND $i % 4 == 0 ) {
							$first_punch_in = rand(7,8).':'.str_pad( rand(0,30), 2, '0', STR_PAD_LEFT) .'AM';
							$last_punch_out = strtotime($date_stamp.' '. rand(4,5).':'.str_pad( rand(0,30), 2, '0', STR_PAD_LEFT) .'PM' );

							if ( $punch_date >= $exception_cutoff_date
									AND rand(0,20) == 0 ) {
								//Create request
								$this->createRequest( 20, $user_id, $date_stamp );
							}
							if ( $punch_date >= $exception_cutoff_date
									AND rand(0,16) == 0 ) {
								//Create request
								$this->createRequest( 20, $user_id, $date_stamp );
							}

						} else {
							$first_punch_in = '08:00AM';
							if ( $punch_date >= $exception_cutoff_date
									AND $i % 10 == 0 ) {
								//Don't punch out to generate exception.
								$last_punch_out = NULL;

								//Forgot to punch out request
								$this->createRequest( 30, $user_id, $date_stamp );
							} else {
								$last_punch_out = strtotime($date_stamp.' 5:00PM');
							}
						}

						//Weekdays
						$this->createPunchPair( 	$user_id,
													strtotime($date_stamp.' '. $first_punch_in),
													strtotime($date_stamp.' 11:00AM'),
													array(
															'in_type_id' => 10,
															'out_type_id' => 10,
															'branch_id' => $branch_ids[rand(0,count($branch_ids)-1)],
															'department_id' => $department_ids[rand(0,count($department_ids)-1)],
															'job_id' => $job_ids[array_rand($user_random_job_ids)],
															'job_item_id' => $task_ids[array_rand($user_random_task_ids)],
															//'job_item_id' => $task_ids[rand(0,count($task_ids)-1)],
														),
													TRUE
												);
						$this->createPunchPair( 	$user_id,
													strtotime($date_stamp.' 11:00AM'),
													strtotime($date_stamp.' 1:00PM'),
													array(
															'in_type_id' => 10,
															'out_type_id' => 20,
															'branch_id' => $branch_ids[rand(0,count($branch_ids)-1)],
															'department_id' => $department_ids[rand(0,count($department_ids)-1)],
															'job_id' => $job_ids[array_rand($user_random_job_ids)],
															'job_item_id' => $task_ids[array_rand($user_random_task_ids)],
														),
													TRUE
												);
						//Calc total time on last punch pair only.
						$this->createPunchPair( 	$user_id,
													strtotime($date_stamp.' 2:00PM'),
													$last_punch_out,
													array(
															'in_type_id' => 20,
															'out_type_id' => 10,
															'branch_id' => $branch_ids[rand(0,count($branch_ids)-1)],
															'department_id' => $department_ids[rand(0,count($department_ids)-1)],
															'job_id' => $job_ids[array_rand($user_random_job_ids)],
															'job_item_id' => $task_ids[array_rand($user_random_task_ids)],

														),
													TRUE
												);
					} elseif ( $punch_date > $exception_cutoff_date
								AND date('w',$punch_date) == 6 AND $i % 10 == 0) {
						//Sat.
						$this->createPunchPair( 	$user_id,
													strtotime($date_stamp.' 10:00AM'),
													strtotime($date_stamp.' 2:30PM'),
													array(
															'in_type_id' => 10,
															'out_type_id' => 10,
															'branch_id' => $branch_ids[rand(0,count($branch_ids)-1)],
															'department_id' => $department_ids[rand(0,count($department_ids)-1)],
															'job_id' => $job_ids[array_rand($user_random_job_ids)],
															'job_item_id' => $task_ids[array_rand($user_random_task_ids)],
														),
													TRUE
												);

					}

					//Recalculate entire day. Performance optimization.
					//UserDateTotalFactory::reCalculateRange( $user_id, $start_date, $end_date );

					$punch_date+=86400;
					$i++;
				}
				unset($punch_options_arr, $punch_date, $user_id);
			}


			//Generate pay stubs for each pay period
			$pplf = new PayPeriodListFactory();
			$pplf->getByCompanyId( $company_id );
			if ( $pplf->getRecordCount() > 0 ) {
				foreach( $pplf as $pp_obj ) {
					foreach( $user_ids as $user_id ) {
						$cps = new CalculatePayStub();
						$cps->setUser( $user_id );
						$cps->setPayPeriod( $pp_obj->getId() );
						$cps->calculate();
					}
				}
			}
			unset($pplf, $pp_obj, $user_id);

		}

		//$cf->FailTransaction();
		$cf->CommitTransaction();

		return FALSE;
	}
}
?>
