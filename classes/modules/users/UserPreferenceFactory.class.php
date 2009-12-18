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
 * $Revision: 2862 $
 * $Id: UserPreferenceFactory.class.php 2862 2009-09-30 19:35:20Z ipso $
 * $Date: 2009-09-30 12:35:20 -0700 (Wed, 30 Sep 2009) $
 */

/**
 * @package Module_Users
 */
class UserPreferenceFactory extends Factory {
	protected $table = 'user_preference';
	protected $pk_sequence_name = 'user_preference_id_seq'; //PK Sequence name

	var $user_obj = NULL;

	function _getFactoryOptions( $name ) {

		$retval = NULL;
		switch( $name ) {

			// I18n: No need to use gettext because these options only appear for english.
			case 'date_format':
				$retval = array(
											'd-M-y'		=> '25-Feb-01 (dd-mmm-yy)',
											'd-M-Y'		=> '25-Feb-2001 (dd-mmm-yyyy)',
//PHP 5.1.2 fails to parse these with strtotime it looks like
//											'd/M/y' 	=> '25/Feb/01 (dd/mmm/yy)',
//											'd/M/Y' 	=> '25/Feb/2001 (dd/mmm/yyyy)',
											'dMY' 		=> '25Feb2001 (ddmmmyyyy)',
											'd/m/Y' 	=> '25/02/2001 (dd/mm/yyyy)',
											'd/m/y' 	=> '25/02/01 (dd/mm/yy)',
											'd-m-y' 	=> '25-02-01 (dd-mm-yy)',
											'd-m-Y'		=> '25-02-2001 (dd-mm-yyyy)',
											'm/d/y' 	=> '02/25/01 (mm/dd/yy)',
											'm/d/Y' 	=> '02/25/2001 (mm/dd/yyyy)',
											'm-d-y'		=> '02-25-01 (mm-dd-yy)',
											'm-d-Y'		=> '02-25-2001 (mm-dd-yyyy)',
											'Y-m-d' 	=> '2001-02-25 (yyyy-mm-dd)',
											'M-d-y' 	=> 'Feb-25-01 (mmm-dd-yy)',
											'M-d-Y' 	=> 'Feb-25-2001 (mmm-dd-yyyy)',
											'l, F d Y'	=> 'Sunday, February 25 2001',
											'D, F d Y'	=> 'Sun, February 25 2001',
											'D, M d Y'	=> 'Sun, Feb 25 2001',
											'D, d-M-Y'	=> 'Sun, 25-Feb-2001',
											'D, dMY'	=> 'Sun, 25Feb2001'
									);
				break;

			// I18n: We use fewer calendar options for non-en langs, as otherwise strtotime chokes.
			case 'other_date_format':
				$retval = array(
											'd/m/Y' 	=> '25/02/2001 (dd/mm/yyyy)',
											'd/m/y' 	=> '25/02/01 (dd/mm/yy)',
											'd-m-y' 	=> '25-02-01 (dd-mm-yy)',
											'd-m-Y'		=> '25-02-2001 (dd-mm-yyyy)',
											'm/d/y' 	=> '02/25/01 (mm/dd/yy)',
											'm/d/Y' 	=> '02/25/2001 (mm/dd/yyyy)',
											'm-d-y'		=> '02-25-01 (mm-dd-yy)',
											'm-d-Y'		=> '02-25-2001 (mm-dd-yyyy)',
											'Y-m-d' 	=> '2001-02-25 (yyyy-mm-dd)',
									);
				break;
			case 'js_date_format':
				$retval = array(
											//http://www.dynarch.com/demos/jscalendar/doc/html/reference.html#node_sec_5
											'd-M-y'		=> '%d-%b-%y',
											'd-M-Y'		=> '%d-%b-%Y',
											'dMY' 		=> '%d%b%Y',
											'd/m/Y' 	=> '%d/%m/%Y',
											'd/m/y' 	=> '%d/%m/%y',
											'd-m-y' 	=> '%d-%m-%y',
											'd-m-Y'		=> '%d-%m-%Y',
											'm/d/y' 	=> '%m/%d/%y',
											'm/d/Y' 	=> '%m/%d/%Y',
											'm-d-y'		=> '%m-%d-%y',
											'm-d-Y'		=> '%m-%d-%Y',
											'Y-m-d' 	=> '%Y-%m-%d',
											'M-d-y' 	=> '%b-%d-%y',
											'M-d-Y' 	=> '%b-%d-%Y',
											'l, F d Y'	=> '%A, %B %d %Y',
											'D, F d Y'	=> '%a, %B %d %Y',
											'D, M d Y'	=> '%a, %b %d %Y',
											'D, d-M-Y' 	=> '%a, %d-%b-%Y',
											'D, dMY' 	=> '%a, %d%b%Y'
									);
				break;
			case 'flex_date_format':
				$retval = array(
											'd-M-y'		=> 'DD-MMM-YY',
											'd-M-Y'		=> 'DD-MMM-YYYY',
											'dMY'		=> 'DDMMMYYYY',
											'd/m/Y'		=> 'DD/MM/YYYY',
											'd/m/y'		=> 'DD/MM/YY',
											'd-m-y'		=> 'DD-MM-YY',
											'd-m-Y'		=> 'DD-MM-YYYY',
											'm/d/y'		=> 'MM/DD/YY',
											'm/d/Y'		=> 'MM/DD/YYYY',
											'm-d-y'		=> 'MM-DD-YY',
											'm-d-Y'     => 'MM-DD-YYYY',
											'Y-m-d'		=> 'YYYY-MM-DD',
											'M-d-y'		=> 'MMM-DD-yy',
											'M-d-Y' 	=> 'MMM-DD-YYYY',
											'l, F d Y'  => 'EEEE,MMMM DD YYYY',
											'D, F d Y'  => 'EEE,MMMM DD YYYY',
											'D, M d Y'  => 'EEE,MMM DD YYYY',
											'D, d-M-Y'  => 'EEE,DD-MMM-YYYY',
											'D, dMY'	=> 'EEE,DDMMMYYYY'
									);
				break;
			case 'time_format':
				$retval = array(
											//'g:i:s A' 	=> TTi18n::gettext('8:09:11 PM'),
											'g:i A' 	=> TTi18n::gettext('8:09 PM'),
											//'g:i:s a' 	=> TTi18n::gettext('8:09:11 pm'),
											'g:i a' 	=> TTi18n::gettext('8:09 pm'),
											//'G:i:s' 	=> TTi18n::gettext('20:09:11'),
											'G:i' 		=> TTi18n::gettext('20:09'),
											'g:i A T' => TTi18n::gettext('8:09 PM GMT'),
											'G:i T' => TTi18n::gettext('20:09 GMT')
									);
				break;
			case 'js_time_format':
				$retval = array(
											'g:i A' 	=> '%l:%M %p', //  8:09 PM
											'g:i a' 	=> '%l:%M %P', //8:09 pm
											'G:i' 		=> '%k:%M', //20:09
											'g:i A T' => '%l:%M %p', //8:09 PM GMT
											'G:i T' => '%k:%M' //20:09 PM GMT
									);
				break;
			case 'flex_time_format':
				$retval = array(
											'g:i A' 	=> 'L:NN A', //  8:09 PM
											'g:i a' 	=> 'L:NN a', //8:09 pm
											'G:i' 		=> 'JJ:NN', //20:09
											'g:i A T' => 'L:NN A T', //8:09 PM GMT
											'G:i T' => 'JJ:NN T' //20:09 PM GMT
									);
				break;
			case 'time_unit_format':
				$retval = array(
											10 	=> TTi18n::gettext('hh:mm (2:15)'),
											12 	=> TTi18n::gettext('hh:mm:ss (2:15:59)'),
											20 	=> TTi18n::gettext('Hours (2.25)'),
											22 	=> TTi18n::gettext('Hours (2.141)'),
											30 	=> TTi18n::gettext('Minutes (135)')
									);
				break;

			// I18n: These timezones probably should be translated, but doing so would add ~550
			//       lines to the translator's workload for each lang.  And these are hard to translate.
			//		 Probably better to use an already translated timezone class, if one exists.
			//
			//Commented out timezones do not work in PostgreSQL 8.2, as they hardcode timezone data into versions.
			case 'time_zone':
				$retval = array(

											'Africa/Abidjan' => 'Africa/Abidjan',
											'Africa/Accra' => 'Africa/Accra',
											'Africa/Addis_Ababa' => 'Africa/Addis_Ababa',
											'Africa/Algiers' => 'Africa/Algiers',
											'Africa/Asmera' => 'Africa/Asmera',
											'Africa/Bamako' => 'Africa/Bamako',
											'Africa/Bangui' => 'Africa/Bangui',
											'Africa/Banjul' => 'Africa/Banjul',
											'Africa/Bissau' => 'Africa/Bissau',
											//'Africa/Blantyre' => 'Africa/Blantyre',
											'Africa/Brazzaville' => 'Africa/Brazzaville',
											//'Africa/Bujumbura' => 'Africa/Bujumbura',
											'Africa/Cairo' => 'Africa/Cairo',
											'Africa/Casablanca' => 'Africa/Casablanca',
											'Africa/Ceuta' => 'Africa/Ceuta',
											'Africa/Conakry' => 'Africa/Conakry',
											'Africa/Dakar' => 'Africa/Dakar',
											'Africa/Dar_es_Salaam' => 'Africa/Dar_es_Salaam',
											'Africa/Djibouti' => 'Africa/Djibouti',
											'Africa/Douala' => 'Africa/Douala',
											'Africa/El_Aaiun' => 'Africa/El_Aaiun',
											'Africa/Freetown' => 'Africa/Freetown',
											//'Africa/Gaborone' => 'Africa/Gaborone',
											//'Africa/Harare' => 'Africa/Harare',
											'Africa/Johannesburg' => 'Africa/Johannesburg',
											'Africa/Kampala' => 'Africa/Kampala',
											'Africa/Khartoum' => 'Africa/Khartoum',
											//'Africa/Kigali' => 'Africa/Kigali',
											'Africa/Kinshasa' => 'Africa/Kinshasa',
											'Africa/Lagos' => 'Africa/Lagos',
											'Africa/Libreville' => 'Africa/Libreville',
											'Africa/Lome' => 'Africa/Lome',
											'Africa/Luanda' => 'Africa/Luanda',
											//'Africa/Lubumbashi' => 'Africa/Lubumbashi',
											//'Africa/Lusaka' => 'Africa/Lusaka',
											'Africa/Malabo' => 'Africa/Malabo',
											//'Africa/Maputo' => 'Africa/Maputo',
											'Africa/Maseru' => 'Africa/Maseru',
											'Africa/Mbabane' => 'Africa/Mbabane',
											'Africa/Mogadishu' => 'Africa/Mogadishu',
											'Africa/Monrovia' => 'Africa/Monrovia',
											'Africa/Nairobi' => 'Africa/Nairobi',
											'Africa/Ndjamena' => 'Africa/Ndjamena',
											'Africa/Niamey' => 'Africa/Niamey',
											'Africa/Nouakchott' => 'Africa/Nouakchott',
											'Africa/Ouagadougou' => 'Africa/Ouagadougou',
											'Africa/Porto-Novo' => 'Africa/Porto-Novo',
											'Africa/Sao_Tome' => 'Africa/Sao_Tome',
											'Africa/Timbuktu' => 'Africa/Timbuktu',
											'Africa/Tripoli' => 'Africa/Tripoli',
											'Africa/Tunis' => 'Africa/Tunis',
											'Africa/Windhoek' => 'Africa/Windhoek',
											//'America/Adak' => 'America/Adak',
											'America/Anchorage' => 'America/Anchorage',
											'America/Anguilla' => 'America/Anguilla',
											'America/Antigua' => 'America/Antigua',
											'America/Araguaina' => 'America/Araguaina',
											'America/Aruba' => 'America/Aruba',
											'America/Asuncion' => 'America/Asuncion',
											//'America/Atka' => 'America/Atka',
											'America/Barbados' => 'America/Barbados',
											'America/Belem' => 'America/Belem',
											'America/Belize' => 'America/Belize',
											'America/Boa_Vista' => 'America/Boa_Vista',
											'America/Bogota' => 'America/Bogota',
											'America/Boise' => 'America/Boise',
											'America/Buenos_Aires' => 'America/Buenos_Aires',
											'America/Cambridge_Bay' => 'America/Cambridge_Bay',
											'America/Cancun' => 'America/Cancun',
											'America/Caracas' => 'America/Caracas',
											'America/Catamarca' => 'America/Catamarca',
											'America/Cayenne' => 'America/Cayenne',
											'America/Cayman' => 'America/Cayman',
											'America/Chicago' => 'America/Chicago',
											'America/Chihuahua' => 'America/Chihuahua',
											'America/Cordoba' => 'America/Cordoba',
											'America/Costa_Rica' => 'America/Costa_Rica',
											'America/Cuiaba' => 'America/Cuiaba',
											'America/Curacao' => 'America/Curacao',
											'America/Danmarkshavn' => 'America/Danmarkshavn',
											'America/Dawson' => 'America/Dawson',
											'America/Dawson_Creek' => 'America/Dawson_Creek',
											'America/Denver' => 'America/Denver',
											'America/Detroit' => 'America/Detroit',
											'America/Dominica' => 'America/Dominica',
											'America/Edmonton' => 'America/Edmonton',
											'America/Eirunepe' => 'America/Eirunepe',
											'America/El_Salvador' => 'America/El_Salvador',
											'America/Ensenada' => 'America/Ensenada',
											'America/Fort_Wayne' => 'America/Fort_Wayne',
											'America/Fortaleza' => 'America/Fortaleza',
											'America/Glace_Bay' => 'America/Glace_Bay',
											'America/Godthab' => 'America/Godthab',
											'America/Goose_Bay' => 'America/Goose_Bay',
											'America/Grand_Turk' => 'America/Grand_Turk',
											'America/Grenada' => 'America/Grenada',
											'America/Guadeloupe' => 'America/Guadeloupe',
											'America/Guatemala' => 'America/Guatemala',
											//'America/Guayaquil' => 'America/Guayaquil',
											'America/Guyana' => 'America/Guyana',
											'America/Halifax' => 'America/Halifax',
											'America/Havana' => 'America/Havana',
											'America/Hermosillo' => 'America/Hermosillo',
											'America/Indiana/Indianapolis' => 'America/Indiana/Indianapolis',
											'America/Indiana/Knox' => 'America/Indiana/Knox',
											'America/Indiana/Marengo' => 'America/Indiana/Marengo',
											'America/Indiana/Vevay' => 'America/Indiana/Vevay',
											'America/Indianapolis' => 'America/Indianapolis',
											'America/Inuvik' => 'America/Inuvik',
											'America/Iqaluit' => 'America/Iqaluit',
											'America/Jamaica' => 'America/Jamaica',
											'America/Jujuy' => 'America/Jujuy',
											'America/Juneau' => 'America/Juneau',
											'America/Kentucky/Louisville' => 'America/Kentucky/Louisville',
											'America/Kentucky/Monticello' => 'America/Kentucky/Monticello',
											'America/Knox_IN' => 'America/Knox_IN',
											'America/La_Paz' => 'America/La_Paz',
											'America/Lima' => 'America/Lima',
											'America/Los_Angeles' => 'America/Los_Angeles',
											'America/Louisville' => 'America/Louisville',
											'America/Maceio' => 'America/Maceio',
											'America/Managua' => 'America/Managua',
											'America/Manaus' => 'America/Manaus',
											'America/Martinique' => 'America/Martinique',
											'America/Mazatlan' => 'America/Mazatlan',
											'America/Mendoza' => 'America/Mendoza',
											'America/Menominee' => 'America/Menominee',
											'America/Merida' => 'America/Merida',
											'America/Mexico_City' => 'America/Mexico_City',
											'America/Miquelon' => 'America/Miquelon',
											'America/Monterrey' => 'America/Monterrey',
											'America/Montevideo' => 'America/Montevideo',
											'America/Montreal' => 'America/Montreal',
											'America/Montserrat' => 'America/Montserrat',
											'America/Nassau' => 'America/Nassau',
											'America/New_York' => 'America/New_York',
											'America/Nipigon' => 'America/Nipigon',
											'America/Nome' => 'America/Nome',
											'America/Noronha' => 'America/Noronha',
											'America/North_Dakota/Center' => 'America/North_Dakota/Center',
											'America/Panama' => 'America/Panama',
											'America/Pangnirtung' => 'America/Pangnirtung',
											//'America/Paramaribo' => 'America/Paramaribo',
											'America/Phoenix' => 'America/Phoenix',
											'America/Port-au-Prince' => 'America/Port-au-Prince',
											'America/Port_of_Spain' => 'America/Port_of_Spain',
											'America/Porto_Acre' => 'America/Porto_Acre',
											'America/Porto_Velho' => 'America/Porto_Velho',
											'America/Puerto_Rico' => 'America/Puerto_Rico',
											'America/Rainy_River' => 'America/Rainy_River',
											'America/Rankin_Inlet' => 'America/Rankin_Inlet',
											'America/Recife' => 'America/Recife',
											'America/Regina' => 'America/Regina',
											'America/Rio_Branco' => 'America/Rio_Branco',
											'America/Rosario' => 'America/Rosario',
											'America/Santiago' => 'America/Santiago',
											'America/Santo_Domingo' => 'America/Santo_Domingo',
											'America/Sao_Paulo' => 'America/Sao_Paulo',
											'America/Scoresbysund' => 'America/Scoresbysund',
											'America/Shiprock' => 'America/Shiprock',
											'America/St_Johns' => 'America/St_Johns',
											'America/St_Kitts' => 'America/St_Kitts',
											'America/St_Lucia' => 'America/St_Lucia',
											'America/St_Thomas' => 'America/St_Thomas',
											'America/St_Vincent' => 'America/St_Vincent',
											'America/Swift_Current' => 'America/Swift_Current',
											'America/Tegucigalpa' => 'America/Tegucigalpa',
											'America/Thule' => 'America/Thule',
											'America/Thunder_Bay' => 'America/Thunder_Bay',
											'America/Tijuana' => 'America/Tijuana',
											'America/Tortola' => 'America/Tortola',
											'America/Vancouver' => 'America/Vancouver',
											'America/Virgin' => 'America/Virgin',
											'America/Whitehorse' => 'America/Whitehorse',
											'America/Winnipeg' => 'America/Winnipeg',
											'America/Yakutat' => 'America/Yakutat',
											'America/Yellowknife' => 'America/Yellowknife',
											//'Antarctica/Casey' => 'Antarctica/Casey',
											'Antarctica/Davis' => 'Antarctica/Davis',
											'Antarctica/DumontDUrville' => 'Antarctica/DumontDUrville',
											'Antarctica/Mawson' => 'Antarctica/Mawson',
											'Antarctica/McMurdo' => 'Antarctica/McMurdo',
											'Antarctica/Palmer' => 'Antarctica/Palmer',
											'Antarctica/South_Pole' => 'Antarctica/South_Pole',
											//'Antarctica/Syowa' => 'Antarctica/Syowa',
											//'Antarctica/Vostok' => 'Antarctica/Vostok',
											'Arctic/Longyearbyen' => 'Arctic/Longyearbyen',
											'Asia/Aden' => 'Asia/Aden',
											'Asia/Almaty' => 'Asia/Almaty',
											'Asia/Amman' => 'Asia/Amman',
											'Asia/Anadyr' => 'Asia/Anadyr',
											//'Asia/Aqtau' => 'Asia/Aqtau',
											//'Asia/Aqtobe' => 'Asia/Aqtobe',
											'Asia/Ashgabat' => 'Asia/Ashgabat',
											'Asia/Ashkhabad' => 'Asia/Ashkhabad',
											'Asia/Baghdad' => 'Asia/Baghdad',
											'Asia/Bahrain' => 'Asia/Bahrain',
											'Asia/Baku' => 'Asia/Baku',
											'Asia/Bangkok' => 'Asia/Bangkok',
											'Asia/Beirut' => 'Asia/Beirut',
											'Asia/Bishkek' => 'Asia/Bishkek',
											'Asia/Brunei' => 'Asia/Brunei',
											'Asia/Calcutta' => 'Asia/Calcutta',
											//'Asia/Choibalsan' => 'Asia/Choibalsan',
											'Asia/Chongqing' => 'Asia/Chongqing',
											'Asia/Chungking' => 'Asia/Chungking',
											'Asia/Colombo' => 'Asia/Colombo',
											'Asia/Dacca' => 'Asia/Dacca',
											'Asia/Damascus' => 'Asia/Damascus',
											'Asia/Dhaka' => 'Asia/Dhaka',
											//'Asia/Dili' => 'Asia/Dili',
											//'Asia/Dubai' => 'Asia/Dubai',
											'Asia/Dushanbe' => 'Asia/Dushanbe',
											'Asia/Gaza' => 'Asia/Gaza',
											'Asia/Harbin' => 'Asia/Harbin',
											'Asia/Hong_Kong' => 'Asia/Hong_Kong',
											//'Asia/Hovd' => 'Asia/Hovd',
											'Asia/Irkutsk' => 'Asia/Irkutsk',
											'Asia/Istanbul' => 'Asia/Istanbul',
											//'Asia/Jakarta' => 'Asia/Jakarta',
											//'Asia/Jayapura' => 'Asia/Jayapura',
											//'Asia/Jerusalem' => 'Asia/Jerusalem', //Offset 10800
											'Asia/Kabul' => 'Asia/Kabul',
											'Asia/Kamchatka' => 'Asia/Kamchatka',
											'Asia/Karachi' => 'Asia/Karachi',
											'Asia/Kashgar' => 'Asia/Kashgar',
											'Asia/Katmandu' => 'Asia/Katmandu',
											'Asia/Krasnoyarsk' => 'Asia/Krasnoyarsk',
											'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur',
											'Asia/Kuching' => 'Asia/Kuching',
											'Asia/Kuwait' => 'Asia/Kuwait',
											'Asia/Macao' => 'Asia/Macao',
											'Asia/Magadan' => 'Asia/Magadan',
											'Asia/Manila' => 'Asia/Manila',
											//'Asia/Muscat' => 'Asia/Muscat',
											'Asia/Nicosia' => 'Asia/Nicosia',
											'Asia/Novosibirsk' => 'Asia/Novosibirsk',
											'Asia/Omsk' => 'Asia/Omsk',
											'Asia/Phnom_Penh' => 'Asia/Phnom_Penh',
											//'Asia/Pontianak' => 'Asia/Pontianak',
											'Asia/Pyongyang' => 'Asia/Pyongyang',
											'Asia/Qatar' => 'Asia/Qatar',
											'Asia/Rangoon' => 'Asia/Rangoon',
											'Asia/Riyadh' => 'Asia/Riyadh',
											//'Asia/Riyadh87' => 'Asia/Riyadh87',
											//'Asia/Riyadh88' => 'Asia/Riyadh88',
											//'Asia/Riyadh89' => 'Asia/Riyadh89',
											'Asia/Saigon' => 'Asia/Saigon',
											//'Asia/Sakhalin' => 'Asia/Sakhalin',
											'Asia/Samarkand' => 'Asia/Samarkand',
											'Asia/Seoul' => 'Asia/Seoul',
											'Asia/Shanghai' => 'Asia/Shanghai',
											//'Asia/Singapore' => 'Asia/Singapore',
											'Asia/Taipei' => 'Asia/Taipei',
											'Asia/Tashkent' => 'Asia/Tashkent',
											'Asia/Tbilisi' => 'Asia/Tbilisi',
											//'Asia/Tehran' => 'Asia/Tehran',
											//'Asia/Tel_Aviv' => 'Asia/Tel_Aviv',
											'Asia/Thimbu' => 'Asia/Thimbu',
											'Asia/Thimphu' => 'Asia/Thimphu',
											'Asia/Tokyo' => 'Asia/Tokyo',
											//'Asia/Ujung_Pandang' => 'Asia/Ujung_Pandang',
											'Asia/Ulaanbaatar' => 'Asia/Ulaanbaatar',
											'Asia/Ulan_Bator' => 'Asia/Ulan_Bator',
											'Asia/Urumqi' => 'Asia/Urumqi',
											'Asia/Vientiane' => 'Asia/Vientiane',
											'Asia/Vladivostok' => 'Asia/Vladivostok',
											'Asia/Yakutsk' => 'Asia/Yakutsk',
											'Asia/Yekaterinburg' => 'Asia/Yekaterinburg',
											'Asia/Yerevan' => 'Asia/Yerevan',
											'Atlantic/Azores' => 'Atlantic/Azores',
											'Atlantic/Bermuda' => 'Atlantic/Bermuda',
											//'Atlantic/Canary' => 'Atlantic/Canary',
											//'Atlantic/Cape_Verde' => 'Atlantic/Cape_Verde',
											//'Atlantic/Faeroe' => 'Atlantic/Faeroe',
											'Atlantic/Jan_Mayen' => 'Atlantic/Jan_Mayen',
											//'Atlantic/Madeira' => 'Atlantic/Madeira',
											'Atlantic/Reykjavik' => 'Atlantic/Reykjavik',
											//'Atlantic/South_Georgia' => 'Atlantic/South_Georgia',
											'Atlantic/St_Helena' => 'Atlantic/St_Helena',
											'Atlantic/Stanley' => 'Atlantic/Stanley',
											'Australia/ACT' => 'Australia/ACT',
											'Australia/Adelaide' => 'Australia/Adelaide',
											'Australia/Brisbane' => 'Australia/Brisbane',
											'Australia/Broken_Hill' => 'Australia/Broken_Hill',
											'Australia/Canberra' => 'Australia/Canberra',
											'Australia/Darwin' => 'Australia/Darwin',
											'Australia/Hobart' => 'Australia/Hobart',
											'Australia/LHI' => 'Australia/LHI',
											'Australia/Lindeman' => 'Australia/Lindeman',
											'Australia/Lord_Howe' => 'Australia/Lord_Howe',
											'Australia/Melbourne' => 'Australia/Melbourne',
											'Australia/NSW' => 'Australia/NSW',
											'Australia/North' => 'Australia/North',
											//'Australia/Perth' => 'Australia/Perth',
											'Australia/Queensland' => 'Australia/Queensland',
											'Australia/South' => 'Australia/South',
											'Australia/Sydney' => 'Australia/Sydney',
											'Australia/Tasmania' => 'Australia/Tasmania',
											'Australia/Victoria' => 'Australia/Victoria',
											//'Australia/West' => 'Australia/West',
											'Australia/Yancowinna' => 'Australia/Yancowinna',
											'Brazil/Acre' => 'Brazil/Acre',
											'Brazil/DeNoronha' => 'Brazil/DeNoronha',
											'Brazil/East' => 'Brazil/East',
											'Brazil/West' => 'Brazil/West',
											'Canada/Atlantic' => 'Canada/Atlantic',
											'Canada/Central' => 'Canada/Central',
											'Canada/East-Saskatchewan' => 'Canada/East-Saskatchewan',
											'Canada/Eastern' => 'Canada/Eastern',
											'Canada/Mountain' => 'Canada/Mountain',
											'Canada/Newfoundland' => 'Canada/Newfoundland',
											'Canada/Pacific' => 'Canada/Pacific',
											'Canada/Saskatchewan' => 'Canada/Saskatchewan',
											'Canada/Yukon' => 'Canada/Yukon',
											'Chile/Continental' => 'Chile/Continental',
											'Chile/EasterIsland' => 'Chile/EasterIsland',
											'Cuba' => 'Cuba',
											'Egypt' => 'Egypt',
											'Eire' => 'Eire',
											//'Etc/GMT0' => 'Etc/GMT0',
											//'Etc/Greenwich' => 'Etc/Greenwich',
											//'Etc/UCT' => 'Etc/UCT',
											//'Etc/UTC' => 'Etc/UTC',
											//'Etc/Universal' => 'Etc/Universal',
											//'Etc/Zulu' => 'Etc/Zulu',
											'Europe/Amsterdam' => 'Europe/Amsterdam',
											'Europe/Andorra' => 'Europe/Andorra',
											'Europe/Athens' => 'Europe/Athens',
											'Europe/Belfast' => 'Europe/Belfast',
											'Europe/Belgrade' => 'Europe/Belgrade',
											'Europe/Berlin' => 'Europe/Berlin',
											'Europe/Bratislava' => 'Europe/Bratislava',
											'Europe/Brussels' => 'Europe/Brussels',
											'Europe/Bucharest' => 'Europe/Bucharest',
											'Europe/Budapest' => 'Europe/Budapest',
											'Europe/Chisinau' => 'Europe/Chisinau',
											'Europe/Copenhagen' => 'Europe/Copenhagen',
											'Europe/Dublin' => 'Europe/Dublin',
											'Europe/Gibraltar' => 'Europe/Gibraltar',
											'Europe/Helsinki' => 'Europe/Helsinki',
											'Europe/Istanbul' => 'Europe/Istanbul',
											'Europe/Kaliningrad' => 'Europe/Kaliningrad',
											'Europe/Kiev' => 'Europe/Kiev',
											//'Europe/Lisbon' => 'Europe/Lisbon',
											'Europe/Ljubljana' => 'Europe/Ljubljana',
											'Europe/London' => 'Europe/London',
											'Europe/Luxembourg' => 'Europe/Luxembourg',
											'Europe/Madrid' => 'Europe/Madrid',
											'Europe/Malta' => 'Europe/Malta',
											'Europe/Minsk' => 'Europe/Minsk',
											'Europe/Monaco' => 'Europe/Monaco',
											'Europe/Moscow' => 'Europe/Moscow',
											'Europe/Nicosia' => 'Europe/Nicosia',
											'Europe/Oslo' => 'Europe/Oslo',
											'Europe/Paris' => 'Europe/Paris',
											'Europe/Prague' => 'Europe/Prague',
											'Europe/Riga' => 'Europe/Riga',
											'Europe/Rome' => 'Europe/Rome',
											//'Europe/Samara' => 'Europe/Samara',
											'Europe/San_Marino' => 'Europe/San_Marino',
											'Europe/Sarajevo' => 'Europe/Sarajevo',
											'Europe/Simferopol' => 'Europe/Simferopol',
											'Europe/Skopje' => 'Europe/Skopje',
											'Europe/Sofia' => 'Europe/Sofia',
											'Europe/Stockholm' => 'Europe/Stockholm',
											'Europe/Tallinn' => 'Europe/Tallinn',
											'Europe/Tirane' => 'Europe/Tirane',
											'Europe/Tiraspol' => 'Europe/Tiraspol',
											'Europe/Uzhgorod' => 'Europe/Uzhgorod',
											'Europe/Vaduz' => 'Europe/Vaduz',
											'Europe/Vatican' => 'Europe/Vatican',
											'Europe/Vienna' => 'Europe/Vienna',
											'Europe/Vilnius' => 'Europe/Vilnius',
											'Europe/Warsaw' => 'Europe/Warsaw',
											'Europe/Zagreb' => 'Europe/Zagreb',
											'Europe/Zaporozhye' => 'Europe/Zaporozhye',
											'Europe/Zurich' => 'Europe/Zurich',
											'GB' => 'GB',
											'GB-Eire' => 'GB-Eire',
											'Greenwich' => 'Greenwich',
											'Hongkong' => 'Hongkong',
											'Iceland' => 'Iceland',
											'-1000-Asia/Calcutta' => 'India', //GMT+5:30, same as Asia Calcutta
											'Indian/Antananarivo' => 'Indian/Antananarivo',
											'Indian/Chagos' => 'Indian/Chagos',
											'Indian/Christmas' => 'Indian/Christmas',
											'Indian/Cocos' => 'Indian/Cocos',
											'Indian/Comoro' => 'Indian/Comoro',
											'Indian/Kerguelen' => 'Indian/Kerguelen',
											'Indian/Mahe' => 'Indian/Mahe',
											'Indian/Maldives' => 'Indian/Maldives',
											'Indian/Mauritius' => 'Indian/Mauritius',
											'Indian/Mayotte' => 'Indian/Mayotte',
											'Indian/Reunion' => 'Indian/Reunion',
											//'Iran' => 'Iran',
											//'Israel' => 'Israel', //Fails in PostgreSQL 8.2
											'Jamaica' => 'Jamaica',
											'Japan' => 'Japan',
											'Kwajalein' => 'Kwajalein',
											'Libya' => 'Libya',
											'Mexico/BajaNorte' => 'Mexico/BajaNorte',
											'Mexico/BajaSur' => 'Mexico/BajaSur',
											'Mexico/General' => 'Mexico/General',
											//'Mideast/Riyadh87' => 'Mideast/Riyadh87',
											//'Mideast/Riyadh88' => 'Mideast/Riyadh88',
											//'Mideast/Riyadh89' => 'Mideast/Riyadh89',
											'NZ' => 'NZ',
											'NZ-CHAT' => 'NZ-CHAT',
											'Navajo' => 'Navajo',
											//'Pacific/Apia' => 'Pacific/Apia',
											'Pacific/Auckland' => 'Pacific/Auckland',
											'Pacific/Chatham' => 'Pacific/Chatham',
											'Pacific/Easter' => 'Pacific/Easter',
											'Pacific/Efate' => 'Pacific/Efate',
											'Pacific/Enderbury' => 'Pacific/Enderbury',
											'Pacific/Fakaofo' => 'Pacific/Fakaofo',
											'Pacific/Fiji' => 'Pacific/Fiji',
											'Pacific/Funafuti' => 'Pacific/Funafuti',
											'Pacific/Galapagos' => 'Pacific/Galapagos',
											'Pacific/Gambier' => 'Pacific/Gambier',
											//'Pacific/Guadalcanal' => 'Pacific/Guadalcanal',
											//'Pacific/Guam' => 'Pacific/Guam',
											'Pacific/Honolulu' => 'Pacific/Honolulu',
											'Pacific/Johnston' => 'Pacific/Johnston',
											'Pacific/Kiritimati' => 'Pacific/Kiritimati',
											'Pacific/Kosrae' => 'Pacific/Kosrae',
											'Pacific/Kwajalein' => 'Pacific/Kwajalein',
											'Pacific/Majuro' => 'Pacific/Majuro',
											'Pacific/Marquesas' => 'Pacific/Marquesas',
											//'Pacific/Midway' => 'Pacific/Midway',
											//'Pacific/Nauru' => 'Pacific/Nauru',
											'Pacific/Niue' => 'Pacific/Niue',
											'Pacific/Norfolk' => 'Pacific/Norfolk',
											//'Pacific/Noumea' => 'Pacific/Noumea',
											//'Pacific/Pago_Pago' => 'Pacific/Pago_Pago',
											'Pacific/Palau' => 'Pacific/Palau',
											'Pacific/Pitcairn' => 'Pacific/Pitcairn',
											'Pacific/Ponape' => 'Pacific/Ponape',
											'Pacific/Port_Moresby' => 'Pacific/Port_Moresby',
											'Pacific/Rarotonga' => 'Pacific/Rarotonga',
											//'Pacific/Saipan' => 'Pacific/Saipan',
											//'Pacific/Samoa' => 'Pacific/Samoa',
											'Pacific/Tahiti' => 'Pacific/Tahiti',
											'Pacific/Tarawa' => 'Pacific/Tarawa',
											'Pacific/Tongatapu' => 'Pacific/Tongatapu',
											'Pacific/Truk' => 'Pacific/Truk',
											'Pacific/Wake' => 'Pacific/Wake',
											'Pacific/Wallis' => 'Pacific/Wallis',
											'Pacific/Yap' => 'Pacific/Yap',
											'Poland' => 'Poland',
											//'Portugal' => 'Portugal',
											'ROK' => 'ROK',
											//'SST' => 'SST',
											//'Singapore' => 'Singapore',
											//'SystemV/AST4' => 'SystemV/AST4',
											//'SystemV/AST4ADT' => 'SystemV/AST4ADT',
											//'SystemV/CST6' => 'SystemV/CST6',
											//'SystemV/CST6CDT' => 'SystemV/CST6CDT',
											//'SystemV/EST5' => 'SystemV/EST5',
											//'SystemV/EST5EDT' => 'SystemV/EST5EDT',
											//'SystemV/HST10' => 'SystemV/HST10',
											//'SystemV/MST7' => 'SystemV/MST7',
											//'SystemV/MST7MDT' => 'SystemV/MST7MDT',
											//'SystemV/PST8' => 'SystemV/PST8',
											//'SystemV/PST8PDT' => 'SystemV/PST8PDT',
											//'SystemV/YST9' => 'SystemV/YST9',
											//'SystemV/YST9YDT' => 'SystemV/YST9YDT',
											'Turkey' => 'Turkey',
											'US/Alaska' => 'US/Alaska',
											//'US/Aleutian' => 'US/Aleutian',
											'US/Arizona' => 'US/Arizona',
											'US/Central' => 'US/Central',
											'US/East-Indiana' => 'US/East-Indiana',
											'US/Eastern' => 'US/Eastern',
											'US/Hawaii' => 'US/Hawaii',
											'US/Indiana-Starke' => 'US/Indiana-Starke',
											'US/Michigan' => 'US/Michigan',
											'US/Mountain' => 'US/Mountain',
											'US/Pacific' => 'US/Pacific',
											//'US/Pacific-New' => 'US/Pacific-New',
											//'US/Samoa' => 'US/Samoa',
											'Universal' => 'Universal',
											'W-SU' => 'W-SU',
											//'WET' => 'WET',
											'Zulu' => 'Zulu',

											'AST4ADT' => 'AST4ADT',
											'CST6CDT' => 'CST6CDT',
											'EST5EDT' => 'EST5EDT',
											'MST7MDT' => 'MST7MDT',
											'PST8PDT' => 'PST8PDT',
											'YST9YDT' => 'YST9YDT',

											'ACT' => 'ACT',
											'AET' => 'AET',
											'AGT' => 'AGT',
											'ART' => 'ART',
											'AST' => 'AST',
											//'BDT' => 'BDT',
											'BET' => 'BET',
											'CAT' => 'CAT',
											'CET' => 'CET',
											'CNT' => 'CNT',
											'CST' => 'CST',
											'CTT' => 'CTT',
											'EAT' => 'EAT',
											//'ECT' => 'ECT',
											'EET' => 'EET',
											'EST' => 'EST',
											'GMT' => 'GMT',
											'HST' => 'HST',
											'IET' => 'IET',
											//'IST' => 'IST', //10800 offset
											'JST' => 'JST',
											'MET' => 'MET',
											'MIT' => 'MIT',
											'MST' => 'MST',
											'NET' => 'NET',
											'NST' => 'NST',
											'PLT' => 'PLT',
											'PNT' => 'PNT',
											'PRC' => 'PRC',
											'PRT' => 'PRT',
											'PST' => 'PST',
											'UCT' => 'UCT',
											'UTC' => 'UTC',
											'VST' => 'VST',

											//POSIX standard states to invert the signs, so do this here for our users.
											'Etc/GMT' => 'GMT',
											'Etc/GMT-0' => 'GMT+0',
											'Etc/GMT-1' => 'GMT+1',
											'Etc/GMT-2' => 'GMT+2',
											'Etc/GMT-3' => 'GMT+3',
											'Etc/GMT-4' => 'GMT+4',
											'Etc/GMT-5' => 'GMT+5',
											'Etc/GMT-6' => 'GMT+6',
											'Etc/GMT-7' => 'GMT+7',
											'Etc/GMT-8' => 'GMT+8',
											'Etc/GMT-9' => 'GMT+9',
											'Etc/GMT-10' => 'GMT+10',
											'Etc/GMT-11' => 'GMT+11',
											'Etc/GMT-12' => 'GMT+12',
											'Etc/GMT+0' => 'GMT-0',
											'Etc/GMT+1' => 'GMT-1',
											'Etc/GMT+2' => 'GMT-2',
											'Etc/GMT+3' => 'GMT-3',
											'Etc/GMT+4' => 'GMT-4',
											'Etc/GMT+5' => 'GMT-5',
											'Etc/GMT+6' => 'GMT-6',
											'Etc/GMT+7' => 'GMT-7',
											'Etc/GMT+8' => 'GMT-8',
											'Etc/GMT+9' => 'GMT-9',
											'Etc/GMT+10' => 'GMT-10',
											'Etc/GMT+11' => 'GMT-11',
											'Etc/GMT+12' => 'GMT-12',
											'Etc/GMT+13' => 'GMT-13',
											'Etc/GMT+14' => 'GMT-14',
									);
				break;

			case 'date_format':
				$retval = array(
											'd-M-y'		=> TTi18n::gettext('25-Feb-01 (dd-mmm-yy)'),
											'd-M-Y'		=> TTi18n::gettext('25-Feb-2001 (dd-mmm-yyyy)'),
//PHP 5.1.2 fails to parse these with strtotime it looks like
//											'd/M/y' 	=> '25/Feb/01 (dd/mmm/yy)',
//											'd/M/Y' 	=> '25/Feb/2001 (dd/mmm/yyyy)',
											'dMY' 		=> TTi18n::gettext('25Feb2001 (ddmmmyyyy)'),
											'd/m/Y' 	=> '25/02/2001 (dd/mm/yyyy)',
											'd/m/y' 	=> '25/02/01 (dd/mm/yy)',
											'd-m-y' 	=> '25-02-01 (dd-mm-yy)',
											'd-m-Y'		=> '25-02-2001 (dd-mm-yyyy)',
											'm/d/y' 	=> '02/25/01 (mm/dd/yy)',
											'm/d/Y' 	=> '02/25/2001 (mm/dd/yyyy)',
											'm-d-y'		=> '02-25-01 (mm-dd-yy)',
											'm-d-Y'		=> '02-25-2001 (mm-dd-yyyy)',
											'Y-m-d' 	=> '2001-02-25 (yyyy-mm-dd)',
											'M-d-y' 	=> TTi18n::gettext('Feb-25-01 (mmm-dd-yy)'),
											'M-d-Y' 	=> TTi18n::gettext('Feb-25-2001 (mmm-dd-yyyy)'),
											'l, F d Y' 	=> TTi18n::gettext('Sunday, February 25 2001'),
											'D, F d Y' 	=> TTi18n::gettext('Sun, February 25 2001'),
											'D, M d Y' 	=> TTi18n::gettext('Sun, Feb 25 2001'),
											'D, d-M-Y' 	=> TTi18n::gettext('Sun, 25-Feb-2001'),
											'D, dMY' 	=> TTi18n::gettext('Sun, 25Feb2001')
									);
				break;

			case 'timesheet_view':
				$retval = array(
											10 	=> TTi18n::gettext('Calendar'),
											20 	=> TTi18n::gettext('List')
									);
				break;

			case 'start_week_day':
				$retval = array(
											0 	=> TTi18n::gettext('Sunday'),
											1 	=> TTi18n::gettext('Monday'),
											2 	=> TTi18n::gettext('Tuesday'),
											3 	=> TTi18n::gettext('Wednesday'),
											4 	=> TTi18n::gettext('Thursday'),
											5 	=> TTi18n::gettext('Friday'),
											6 	=> TTi18n::gettext('Saturday'),
									);
				break;
			case 'columns':
				$retval = array(
										'-1000-first_name' => TTi18n::gettext('First Name'),
										'-1010-last_name' => TTi18n::gettext('Last Name'),
										'-1020-language_display' => TTi18n::gettext('Language'),
										'-1030-date_format' => TTi18n::gettext('Date Format'),
										'-1040-time_format' => TTi18n::gettext('Time Format'),
										'-1050-time_zone' => TTi18n::gettext('TimeZone'),
										'-1060-time_unit_format' => TTi18n::gettext('Time Unit Format'),
										'-1070-items_per_page' => TTi18n::gettext('Items Per Page'),
										'-1080-timesheet_view' => TTi18n::gettext('TimeSheet View'),
										'-1090-start_week_day' => TTi18n::gettext('Start Weekday'),
										//'-1100-enable_email_notification_exception' => TTi18n::gettext('Email Notification Exception'),
										//'-1110-enable_email_notification_message' => TTi18n::gettext('Email Notification Message'),
										//'-1120-enable_email_notification_home' => TTi18n::gettext('Email Notification Home'),

										'-2000-created_by' => TTi18n::gettext('Created By'),
										'-2010-created_date' => TTi18n::gettext('Created Date'),
										'-2020-updated_by' => TTi18n::gettext('Updated By'),
										'-2030-updated_date' => TTi18n::gettext('Updated Date'),
							);
				break;
			case 'default_display_columns': //Columns that are displayed by default.
				$retval = array(
								'first_name',
								'last_name',
								'date_format',
								'time_format',
								'time_unit_format',
								'time_zone',
								'created_by',
								'created_date',
								'updated_by',
								'updated_date',
								);
				break;
		}

		return $retval;
	}

	function _getVariableToFunctionMap() {
		$variable_function_map = array(
										'id' => 'ID',
										'user_id' => 'User',
										'language' => 'Language',
										'date_format' => 'DateFormat',
										'time_format' => 'TimeFormat',
										'time_zone' => 'TimeZone',
										'time_unit_format' => 'TimeUnitFormat',
										'items_per_page' => 'ItemsPerPage',
										'timesheet_view' => 'TimeSheetView',
										'start_week_day' => 'StartWeekDay',
										'enable_email_notification_exception' => 'EnableEmailNotificationException',
										'enable_email_notification_message' => 'EnableEmailNotificationMessage',
										'enable_email_notification_home' => 'EnableEmailNotificationHome',
										'deleted' => 'Deleted',
										);
		return $variable_function_map;
	}

	function getUserObject() {
		if ( is_object($this->user_obj) ) {
			return $this->user_obj;
		} else {
			$ulf = new UserListFactory();
			$this->user_obj = $ulf->getById( $this->getUser() )->getCurrent();

			return $this->user_obj;
		}
	}

	function getUser() {
		if ( isset($this->data['user_id']) ) {
			return $this->data['user_id'];
		}

		return FALSE;
	}
	function setUser($id) {
		$id = trim($id);

		$ulf = new UserListFactory();

		if ( $id == 0
				OR $this->Validator->isResultSetWithRows(	'user',
															$ulf->getByID($id),
															TTi18n::gettext('Invalid User')
															) ) {
			$this->data['user_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getLanguage() {
		if ( isset($this->data['language']) ) {
			return $this->data['language'];
		}

		return FALSE;
	}
	function setLanguage($value) {
		$value = trim($value);

		$language_options = TTi18n::getLanguageArray();

		$key = Option::getByValue($value, $language_options );
		if ($key !== FALSE) {
			$value = $key;
		}

		if ( $this->Validator->inArrayKey(	'language',
											$value,
											TTi18n::gettext('Incorrect language'),
											$language_options ) ) {

			$this->data['language'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getDateFormatExample() {
		$options = $this->getOptions('date_format');

		if ( isset($options[$this->getDateFormat()]) ) {
			//Split at the space
			$split_str = explode(' ', $options[$this->getDateFormat()] );
			if ( isset( $split_str[0] ) ) {
				if ( strlen( $split_str[0] ) <= 8 ) {
					return TTi18n::gettext($options[$this->getDateFormat()]);
				} else {
					return TTi18n::gettext($split_str[0]);
				}
			}
			return TTi18n::gettext($options[$this->getDateFormat()]);
		}

		return FALSE;
	}

	function getJSDateFormat() {
		$js_date_format = Option::getByKey($this->getDateFormat(), $this->getOptions('js_date_format') );
		if ( $js_date_format != '' ) {
			Debug::text('Javascript Date Format: '. $js_date_format, __FILE__, __LINE__, __METHOD__, 10);
			return $js_date_format;
		}

		return '%d-%M-%y';
	}
	function getDateFormat() {
		if ( isset($this->data['date_format']) ) {
			return $this->data['date_format'];
		}

		return FALSE;
	}
	function setDateFormat($date_format) {
		$date_format = trim($date_format);

		$key = Option::getByValue($date_format, $this->getOptions('date_format') );
		if ($key !== FALSE) {
			$date_format = $key;
		}

		if ( $this->Validator->inArrayKey(	'date_format',
											$date_format,
											TTi18n::gettext('Incorrect date format'),
											$this->getOptions('date_format')) ) {

			$this->data['date_format'] = $date_format;

			return TRUE;
		}

		return FALSE;
	}

	function getTimeFormatExample() {
		$options = $this->getOptions('time_format');

		if ( isset($options[$this->getTimeFormat()]) ) {
			return $options[$this->getTimeFormat()];
		}

		return FALSE;
	}
	function getJSTimeFormat() {
		$js_time_format = Option::getByKey($this->getTimeFormat(), $this->getOptions('js_time_format') );
		if ( $js_time_format != '' ) {
			Debug::text('Javascript Time Format: '. $js_time_format, __FILE__, __LINE__, __METHOD__, 10);
			return $js_time_format;
		}

		return '%l:%M %p';
	}
	function getTimeFormat() {
		if ( isset($this->data['time_format']) ) {
			return $this->data['time_format'];
		}

		return FALSE;
	}
	function setTimeFormat($time_format) {
		$time_format = trim($time_format);

		$key = Option::getByValue($time_format, $this->getOptions('time_format') );
		if ($key !== FALSE) {
			$time_format = $key;
		}

		if ( $this->Validator->inArrayKey(	'time_format',
											$time_format,
											TTi18n::gettext('Incorrect time format'),
											$this->getOptions('time_format')) ) {

			$this->data['time_format'] = $time_format;

			return TRUE;
		}

		return FALSE;
	}

	function getTimeZone() {
		if ( isset($this->data['time_zone']) ) {
			return $this->data['time_zone'];
		}

		return FALSE;
	}
	function setTimeZone($time_zone) {
		$time_zone = trim($time_zone);

		$key = Option::getByValue($time_zone, $this->getOptions('time_zone') );
		if ($key !== FALSE) {
			$time_zone = $key;
		}

		if ( $this->Validator->inArrayKey(	'time_zone',
											$time_zone,
											TTi18n::gettext('Incorrect time zone'),
											$this->getOptions('time_zone')) ) {

			$this->data['time_zone'] = Misc::trimSortPrefix( $time_zone );

			return TRUE;
		}

		return FALSE;
	}

	function getTimeUnitFormatExample() {
		$options = $this->getOptions('time_unit_format');

		return $options[$this->getTimeUnitFormat()];
	}
	function getTimeUnitFormat() {
		if ( isset($this->data['time_unit_format']) ) {
			return $this->data['time_unit_format'];
		}

		return FALSE;
	}
	function setTimeUnitFormat($time_unit_format) {
		$time_unit_format = trim($time_unit_format);

		$key = Option::getByValue($time_unit_format, $this->getOptions('time_unit_format') );
		if ($key !== FALSE) {
			$time_unit_format = $key;
		}

		if ( $this->Validator->inArrayKey(	'time_unit_format',
											$time_unit_format,
											TTi18n::gettext('Incorrect time units'),
											$this->getOptions('time_unit_format')) ) {

			$this->data['time_unit_format'] = $time_unit_format;

			return TRUE;
		}

		return FALSE;
	}

	function getItemsPerPage() {
		if ( isset($this->data['items_per_page']) ) {
			return $this->data['items_per_page'];
		}

		return FALSE;
	}
	function setItemsPerPage($items_per_page) {
		$items_per_page = trim($items_per_page);

		if 	($items_per_page != '' AND $items_per_page >= 5 AND $items_per_page <= 2000) {

			$this->data['items_per_page'] = $items_per_page;

			return TRUE;
		} else {

			$this->Validator->isTrue(		'items_per_page',
											FALSE,
											TTi18n::gettext('Items per page must be between 5 and 2000'));
		}

		return FALSE;
	}

	//A quick function to change just the timezone, without having to change
	//date formats and such in the process.
	function setTimeZonePreferences() {
		return TTDate::setTimeZone( $this->getTimeZone() );
	}

	function setDateTimePreferences() {
		//TTDate::setTimeZone( $this->getTimeZone() );
		if ( $this->setTimeZonePreferences() == FALSE ) {
			//In case setting the time zone failed, most likely due to MySQL timezone issues.
			return FALSE;
		}

		TTDate::setDateFormat( $this->getDateFormat() );
		TTDate::setTimeFormat( $this->getTimeFormat() );
		TTDate::setTimeUnitFormat(  $this->getTimeUnitFormat() );

		return TRUE;
	}

	function getTimeSheetView() {
		if ( isset($this->data['timesheet_view']) ) {
			return $this->data['timesheet_view'];
		}

		return FALSE;
	}
	function setTimeSheetView($value) {
		$value = trim($value);

		$key = Option::getByValue($value, $this->getOptions('timesheet_view') );
		if ($key !== FALSE) {
			$value = $key;
		}

		if ( $this->Validator->inArrayKey(	'timesheet_view',
											$value,
											TTi18n::gettext('Incorrect default TimeSheet view'),
											$this->getOptions('timesheet_view')) ) {

			$this->data['timesheet_view'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getStartWeekDay() {
		if ( isset($this->data['start_week_day']) ) {
			return $this->data['start_week_day'];
		}

		return FALSE;
	}
	function setStartWeekDay($value) {
		$value = trim($value);

		$key = Option::getByValue($value, $this->getOptions('start_week_day') );
		if ($key !== FALSE) {
			$value = $key;
		}

		if ( $this->Validator->inArrayKey(	'start_week_day',
											$value,
											TTi18n::gettext('Incorrect day to start a week on'),
											$this->getOptions('start_week_day')) ) {

			$this->data['start_week_day'] = $value;

			return TRUE;
		}

		return FALSE;
	}

	function getEnableEmailNotificationException() {
		return $this->fromBool( $this->data['enable_email_notification_exception'] );
	}
	function setEnableEmailNotificationException($bool) {
		$this->data['enable_email_notification_exception'] = $this->toBool($bool);

		return TRUE;
	}
	function getEnableEmailNotificationMessage() {
		return $this->fromBool( $this->data['enable_email_notification_message'] );
	}
	function setEnableEmailNotificationMessage($bool) {
		$this->data['enable_email_notification_message'] = $this->toBool($bool);

		return TRUE;
	}
	function getEnableEmailNotificationHome() {
		return $this->fromBool( $this->data['enable_email_notification_home'] );
	}
	function setEnableEmailNotificationHome($bool) {
		$this->data['enable_email_notification_home'] = $this->toBool($bool);

		return TRUE;
	}

	function Validate() {
		if ( $this->getUser() == '' ) {
			$this->Validator->isTRUE(	'user',
										FALSE,
										TTi18n::gettext('Invalid User') );

		}

		if ( $this->getDateFormat() == '' ) {
			$this->Validator->isTRUE(	'date_format',
										FALSE,
										TTi18n::gettext('Incorrect date format') );

		}

		return TRUE;
	}

	function isPreferencesComplete() {
		if ( $this->getItemsPerPage() == ''
				OR $this->getTimeZone() == '' ) {
			Debug::text('User Preferences is NOT Complete: ', __FILE__, __LINE__, __METHOD__, 10);
			return FALSE;
		}

		Debug::text('User Preferences IS Complete: ', __FILE__, __LINE__, __METHOD__, 10);
		return TRUE;
	}

	function preSave() {
		//Check the locale, if its not english, we need to make sure the selected dateformat is correct for the language, or else force it.
		if ( $this->getLanguage() != 'en' ) {
			if ( Option::getByValue( $this->getDateFormat(), $this->getOptions('other_date_format') ) == FALSE ) {
				//Force a change of date format
				$this->setDateFormat('d/m/Y');
				Debug::text('Language changed and date format doesnt match any longer, forcing it to: d/m/Y', __FILE__, __LINE__, __METHOD__, 10);
			} else {
				Debug::text('Date format doesnt need fixing...', __FILE__, __LINE__, __METHOD__, 10);
			}
		}

		return TRUE;
	}

	function postSave() {
		$this->removeCache( $this->getUser() );

		return TRUE;
	}

	//Support setting created_by,updated_by especially for importing data.
	//Make sure data is set based on the getVariableToFunctionMap order.
	function setObjectFromArray( $data ) {
		if ( is_array( $data ) ) {
			$variable_function_map = $this->getVariableToFunctionMap();
			foreach( $variable_function_map as $key => $function ) {
				if ( isset($data[$key]) ) {

					$function = 'set'.$function;
					switch( $key ) {
						default:
							if ( method_exists( $this, $function ) ) {
								$this->$function( $data[$key] );
							}
							break;
					}
				}
			}

			$this->setCreatedAndUpdatedColumns( $data );

			return TRUE;
		}

		return FALSE;
	}


	function getObjectAsArray( $include_columns = NULL ) {
		$variable_function_map = $this->getVariableToFunctionMap();
		if ( is_array( $variable_function_map ) ) {
			foreach( $variable_function_map as $variable => $function_stub ) {
				if ( $include_columns == NULL OR ( isset($include_columns[$variable]) AND $include_columns[$variable] == TRUE ) ) {

					$function = 'get'.$function_stub;
					switch( $variable ) {
						case 'language':
						case 'date_format':
						case 'time_format':
						case 'time_zone':
						case 'time_unit_format':
						case 'timesheet_view':
						case 'start_week_day':
							if ( method_exists( $this, $function ) ) {
								$data[$variable.'_display'] = Option::getByKey( $this->$function(), $this->getOptions( $variable ) );
							}
						default:
							if ( method_exists( $this, $function ) ) {
								$data[$variable] = $this->$function();
							}
							break;
					}
				}
			}
			$this->getCreatedAndUpdatedColumns( &$data, $include_columns );
		}

		return $data;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action, TTi18n::getText('Employee Preferences') , NULL, $this->getTable() );
	}
}
?>