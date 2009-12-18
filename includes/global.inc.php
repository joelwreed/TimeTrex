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
 * $Id: global.inc.php 3143 2009-12-02 17:21:41Z ipso $
 * $Date: 2009-12-02 09:21:41 -0800 (Wed, 02 Dec 2009) $
 */
$global_script_start_time = microtime(true);

//BUG in PHP 5.2.2 that causes $HTTP_RAW_POST_DATA not to be set. Work around it.
if ( strpos( phpversion(), '5.2.2') !== FALSE ) {
	$HTTP_RAW_POST_DATA = file_get_contents("php://input");
}

if ( !isset($_SERVER['HTTP_HOST']) ) {
	$_SERVER['HTTP_HOST'] = 'localhost';
}

ob_start(); //Take care of GZIP in Apache

ini_set( 'max_execution_time', 1800 );
//Disable magic quotes at runtime. Require magic_quotes_gpc to be disabled during install.
//Check: http://ca3.php.net/manual/en/security.magicquotes.php#61188 for disabling magic_quotes_gpc
ini_set( 'magic_quotes_runtime', 0 );

define('APPLICATION_VERSION', '3.0.3' );

/*
	Config file inside webroot.
*/
define('CONFIG_FILE', dirname(__FILE__) . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'timetrex.ini.php');

/*
	Config file outside webroot.
*/
//define('CONFIG_FILE', '/etc/timetrex.ini.php');

if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
	define('OPERATING_SYSTEM', 'WIN');
} else {
	define('OPERATING_SYSTEM', 'LINUX');
}

if ( file_exists(CONFIG_FILE) ) {
	$config_vars = parse_ini_file( CONFIG_FILE, TRUE);
} else {
	echo "Config file (". CONFIG_FILE .") does not exist!\n";
	exit;
}

if ( isset($config_vars['debug']['production']) AND $config_vars['debug']['production'] == 1 ) {
	define('PRODUCTION', TRUE);
} else {
	define('PRODUCTION', FALSE);
}

//
//
// **REMOVING OR CHANGING THIS APPLICATION NAME IS IN STRICT VIOLATION OF THE LICENSE AGREEMENT**
//
//
if ( PRODUCTION == TRUE ) {
	define('APPLICATION_NAME', 'TimeTrex');
} else {
	define('APPLICATION_NAME', 'TimeTrex-Debug');
}

if ( isset($config_vars['other']['demo_mode']) AND $config_vars['other']['demo_mode'] == 1 ) {
	define('DEMO_MODE', TRUE);
} else {
	define('DEMO_MODE', FALSE);
}

if ( isset($config_vars['other']['deployment_on_demand']) AND $config_vars['other']['deployment_on_demand'] == 1 ) {
	define('DEPLOYMENT_ON_DEMAND', TRUE);
} else {
	define('DEPLOYMENT_ON_DEMAND', FALSE);
}

if ( isset($config_vars['other']['primary_company_id']) AND $config_vars['other']['primary_company_id'] > 0 ) {
	define('PRIMARY_COMPANY_ID', (int)$config_vars['other']['primary_company_id']);
} else {
	define('PRIMARY_COMPANY_ID', FALSE);
}

//Try to dynamically load required PHP extensions if they aren't already.
//This saves people from having to modify php.ini if possible.
if( (bool)ini_get( 'enable_dl' ) == TRUE AND (bool)ini_get( 'safe_mode' ) == FALSE ) {
	$prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';

	//This quite possibly breaks PEAR's Cache_Lite <= v1.7.2 because
	//it uses strlen() on binary data to write the cache file?
	//http://pear.php.net/bugs/bug.php?id=8361
	/*
	if ( extension_loaded('mbstring') == FALSE ) {
		@dl($prefix . 'mbstring.' . PHP_SHLIB_SUFFIX);
		ini_set('mbstring.func_overload', 7); //Overload all string functions.
	}
	*/

	if ( extension_loaded('gettext') == FALSE ) {
		@dl($prefix . 'gettext.' . PHP_SHLIB_SUFFIX);
	}
	if ( extension_loaded('bcmath') == FALSE ) {
		@dl($prefix . 'bcmath.' . PHP_SHLIB_SUFFIX);
	}
	if ( extension_loaded('soap') == FALSE ) {
		@dl($prefix . 'soap.' . PHP_SHLIB_SUFFIX);
	}
	if ( extension_loaded('mcrypt') == FALSE ) {
		@dl($prefix . 'mcrypt.' . PHP_SHLIB_SUFFIX);
	}
	if ( extension_loaded('calendar') == FALSE ) {
		@dl($prefix . 'calendar.' . PHP_SHLIB_SUFFIX);
	}
	if ( extension_loaded('gd') == FALSE ) {
		@dl($prefix . 'gd.' . PHP_SHLIB_SUFFIX);
	}
	if ( extension_loaded('gd') == FALSE AND extension_loaded('gd2') == FALSE ) {
		@dl($prefix . 'gd2.' . PHP_SHLIB_SUFFIX);
	}

	//Load database extension based on config file.
	if ( isset($config_vars['database']['type']) ) {
		if ( stristr($config_vars['database']['type'], 'postgres') AND extension_loaded('pgsql') == FALSE ) {
			@dl($prefix . 'pgsql.' . PHP_SHLIB_SUFFIX);
		} elseif ( stristr($config_vars['database']['type'], 'mysqlt') AND extension_loaded('mysql') == FALSE ) {
			@dl($prefix . 'mysql.' . PHP_SHLIB_SUFFIX);
		} elseif ( stristr($config_vars['database']['type'], 'mysqli') AND extension_loaded('mysqli') == FALSE ) {
			@dl($prefix . 'mysqli.' . PHP_SHLIB_SUFFIX);
		}
	}
}

//Windows doesn't define LC_MESSAGES, so lets do it manually here.
if ( defined('LC_MESSAGES') == FALSE) {
	define('LC_MESSAGES', 6);
}

//IIS 5 doesn't seem to set REQUEST_URI, so attempt to build one on our own
//This also appears to fix CGI mode.
//Inspired by: http://neosmart.net/blog/2006/100-apache-compliant-request_uri-for-iis-and-windows/
if ( !isset($_SERVER['REQUEST_URI']) ) {
	if ( isset($_SERVER['SCRIPT_NAME']) ) {
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
	} elseif ( isset( $_SERVER['PHP_SELF']) ) {
		$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
	}

	if ( isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != '') {
		$_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
	}
}

/*
function __shutdown() {
	//Include content length header so we can show a downloading progress bar.
	header('Content-Length: '. ob_get_length() );
}
register_shutdown_function('__shutdown');
*/
function __autoload( $name ) {
	$file_name = $name .'.class.php';

	//Use include_once() instead of require_once so the installer
	//doesn't Fatal Error without displaying anything.
	@include_once( $file_name );

	return TRUE;
}

//Function to force browsers to cache certain files.
function forceCacheHeaders( $file_name = NULL, $mtime = NULL, $etag = NULL ) {
	if ( $file_name == '' ) {
		$file_name = $_SERVER['SCRIPT_FILENAME'];
	}

	if ( $mtime == '' ) {
		$file_modified_time = filemtime($file_name);
	} else {
		$file_modified_time = $mtime;
	}

	if ( $etag != '' ) {
		$etag = trim($etag);
	}

	//For some reason even with must-revalidate the browsers won't check ETag every page load.
	//So some pages may get cached for an hour or two regardless of ETag changes.
	Header('Cache-Control: must-revalidate');
	Header('Cache-Control: private', FALSE);
	if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
			AND ( strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $file_modified_time )
			AND ( !isset($_SERVER['HTTP_IF_NONE_MATCH'])
					OR ( isset($_SERVER['HTTP_IF_NONE_MATCH']) AND trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag ) ) ) {
		//Cached page, send 304 code and exit.
		if ( $etag != '' ) {
			Header('ETag: '. $etag);
		}
		Header('Last-Modified: '.gmdate('D, d M Y H:i:s', $file_modified_time).' GMT', TRUE, 304);
		ob_clean();
		exit(); //File is cached, don't continue.
	} else {
		//Not cached page, add headers to assist caching.
		if ( $etag != '' ) {
			Header('ETag: '. $etag);
		}
		Header('Last-Modified: '.gmdate('D, d M Y H:i:s', $file_modified_time).' GMT');
	}

	return TRUE;
}

define('TT_PRODUCT_PROFESSIONAL', 20 );
define('TT_PRODUCT_BUSINESS', 15 );
define('TT_PRODUCT_STANDARD', 10 );
function getTTProductEdition() {
	if ( file_exists(Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'job'. DIRECTORY_SEPARATOR .'JobFactory.class.php') ) {
		return TT_PRODUCT_PROFESSIONAL;
	} elseif ( file_exists(Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'time_clock'. DIRECTORY_SEPARATOR .'TimeClock.class.php') ) {
		return TT_PRODUCT_BUSINESS;
	}

	return TT_PRODUCT_STANDARD;
}
function getTTProductEditionName( ) {
	switch( getTTProductEdition() ) {
		case 15:
			$retval = 'Business';
			break;
		case 20:
			$retval = 'Professional';
			break;
		default:
			$retval = 'Standard';
			break;
	}

	return $retval;
}

//This has to be first, always.
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'classes'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'core'. DIRECTORY_SEPARATOR .'Environment.class.php');

//clearstatcache();
set_include_path(
					Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'core' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'pear' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'api' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'company' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'users' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'punch' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'schedule' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'department' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'help' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'client' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'document' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'hierarchy' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'holiday' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'invoice' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'invoice_policy' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'job' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'job_item' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'message' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'other' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'payperiod'.
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'pay_stub' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'pay_stub_amendment' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'policy' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'product' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'request' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'accrual' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'soap' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'install' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'cron' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'time_clock' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'tax_forms' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'tax_forms' . DIRECTORY_SEPARATOR . 'ca' .
					PATH_SEPARATOR . Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . 'tax_forms' . DIRECTORY_SEPARATOR . 'us' .

					PATH_SEPARATOR . get_include_path() );

//define('FPDF_FONTPATH', Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR .'fpdf'. DIRECTORY_SEPARATOR .'font'. DIRECTORY_SEPARATOR);

require_once(Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'core'. DIRECTORY_SEPARATOR .'Exception.class.php');
require_once(Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'core'. DIRECTORY_SEPARATOR .'Debug.class.php');

if ( isset($_SERVER['REQUEST_URI']) ) {
	Debug::Text('URI: '. $_SERVER['REQUEST_URI'], __FILE__, __LINE__, __METHOD__, 10);
}
Debug::Text('Version: '. APPLICATION_VERSION .' Edition: '. getTTProductEdition() .' Production: '. (int)PRODUCTION .' Demo Mode: '. (int)DEMO_MODE, __FILE__, __LINE__, __METHOD__, 10);

$profiler = new Profiler( true );

if ( function_exists('bcscale') ) {
	bcscale(10);
}

Debug::setEnable( (bool)$config_vars['debug']['enable'] );
Debug::setEnableTidy( FALSE );
Debug::setEnableDisplay( (bool)$config_vars['debug']['enable_display'] );
Debug::setBufferOutput( (bool)$config_vars['debug']['buffer_output'] );
Debug::setEnableLog( (bool)$config_vars['debug']['enable_log'] );
Debug::setVerbosity( (int)$config_vars['debug']['verbosity'] );

if ( Debug::getEnable() == TRUE AND Debug::getEnableDisplay() == TRUE ) {
	ini_set( 'display_errors', 1 );
} else {
	ini_set( 'display_errors', 0 );
}

//Make sure we are using SSL if required.
if ( $config_vars['other']['force_ssl'] == 1 AND !isset( $_SERVER['HTTPS'] ) AND isset( $_SERVER['HTTP_HOST'] ) AND isset( $_SERVER['REQUEST_URI'] ) AND !isset( $disable_https ) AND !isset( $enable_wap ) AND php_sapi_name() != 'cli' ) {
	Redirect::Page( 'https://'. $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
	exit;
}

if ( isset($enable_wap) AND $enable_wap == TRUE ) {
	header( 'Content-type: text/vnd.wap.wml', TRUE );
}

require_once('Cache.inc.php');
require_once('Database.inc.php');
?>
