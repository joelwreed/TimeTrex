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
 * $Revision: 3021 $
 * $Id: Install.class.php 3021 2009-11-11 23:33:03Z ipso $
 * $Date: 2009-11-11 15:33:03 -0800 (Wed, 11 Nov 2009) $
 */

/**
 * @package Module_Install
 */
class Install {

	protected $temp_db = NULL;
	var $config_vars = NULL;
	protected $database_driver = NULL;
	protected $is_upgrade = FALSE;
	protected $versions = array(
								'system_version' => APPLICATION_VERSION,
								);


	function __construct() {
		global $config_vars, $cache;

		require_once( Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'install'. DIRECTORY_SEPARATOR .'InstallSchema.class.php');

		$this->config_vars = $config_vars;

		//Disable caching so we don't exceed maximum memory settings.
		$cache->_onlyMemoryCaching = TRUE;

		ini_set('default_socket_timeout', 5);
		ini_set('allow_url_fopen', 1);

		if( (bool)ini_get( 'enable_dl' ) == TRUE AND (bool)ini_get( 'safe_mode' ) == FALSE ) {
			$prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';

			if ( extension_loaded('mysql') == FALSE ) {
				@dl($prefix . 'mysql.' . PHP_SHLIB_SUFFIX);
			}

			if ( extension_loaded('mysqli') == FALSE ) {
				@dl($prefix . 'mysqli.' . PHP_SHLIB_SUFFIX);
			}

			if ( extension_loaded('pgsql') == FALSE ) {
				@dl($prefix . 'pgsql.' . PHP_SHLIB_SUFFIX);
			}
		}

		return TRUE;
	}

	function getDatabaseDriver() {
		return $this->database_driver;
	}

	function setDatabaseDriver( $driver ) {
		if ( $this->getDatabaseType( $driver ) !== 1 ) {
			$this->database_driver = $this->getDatabaseType( $driver );

			return TRUE;
		}

		return FALSE;
	}

	//Read .ini file.
	//Make sure setup_mode is enabled.
	function isInstallMode() {
		if ( isset($this->config_vars['other']['installer_enabled'])
				AND $this->config_vars['other']['installer_enabled'] == 1 ) {
			Debug::text('Install Mode is ON', __FILE__, __LINE__, __METHOD__,9);
			return TRUE;
		}

		Debug::text('Install Mode is OFF', __FILE__, __LINE__, __METHOD__,9);
		return FALSE;
	}

	//Checks if this is the professional version or not
	function getTTProductEdition() {
		return getTTProductEdition();
	}

	function getFullApplicationVersion() {
		$retval = APPLICATION_VERSION;

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$retval .= 'P';
		} elseif ( getTTProductEdition() == TT_PRODUCT_BUSINESS ) {
			$retval .= 'B';
		} else {
			$retval .= 'S';
		}

		return $retval;
	}

	function getLicenseText() {
		$license_file = Environment::getBasePath(). DIRECTORY_SEPARATOR .'LICENSE';

		if ( is_readable($license_file) ) {
			$retval = file_get_contents( $license_file );

			if ( strlen($retval) > 10 ) {
				return $retval;
			}
		}

		return FALSE;
	}

	function setIsUpgrade( $val ) {
		$this->is_upgrade = (bool)$val;
	}
	function getIsUpgrade() {
		return $this->is_upgrade;
	}

	function setDatabaseConnection( $db ) {
		if ( is_object($db) AND ( is_resource($db->_connectionID) OR is_object($db->_connectionID) ) ) {
			$this->temp_db = $db;
			return TRUE;
		}

		return FALSE;
	}
	function getDatabaseConnection() {
		if ( isset($this->temp_db) AND ( is_resource($this->temp_db->_connectionID) OR is_object($this->temp_db->_connectionID)  ) ) {
			return $this->temp_db;
		}

		return FALSE;
	}

	function setNewDatabaseConnection($type, $host, $user, $password, $database_name ) {
		if ( $this->getDatabaseConnection() !== FALSE ) {
			$this->getDatabaseConnection()->Close();
		}

		try {
			$db = ADONewConnection( $type );
			$db->SetFetchMode(ADODB_FETCH_ASSOC);
			$db->Connect( $host, $user, $password, $database_name);
			if (Debug::getVerbosity() == 11) {
				$db->debug=TRUE;
			}

			//MySQLi extension uses an object, not a resource.
			if ( is_resource($db->_connectionID) OR is_object($db->_connectionID) ) {
				$this->setDatabaseConnection( $db );

				//$this->temp_db = $db;

				return TRUE;
			}
		} catch (Exception $e) {
			return FALSE;
		}

		return FALSE;
	}

	function writeConfigFile( $config_vars ) {
		if ( is_writeable( CONFIG_FILE ) ) {
			$contents = file_get_contents( CONFIG_FILE );

			//var_dump($contents);

			if ( isset($config_vars['type']) AND $config_vars['type'] != '' ) {
				$contents = preg_replace('/^type.*=.*/im', 'type = '. trim($config_vars['type']), $contents);
			}
			if ( isset($config_vars['host']) AND $config_vars['host'] != '' ) {
				$contents = preg_replace('/^host.*=.*/im', 'host = '. trim($config_vars['host']), $contents);
			}
			if ( isset($config_vars['database_name']) AND $config_vars['database_name'] != '' ) {
				$contents = preg_replace('/^database_name.*=.*/im', 'database_name = '. trim($config_vars['database_name']), $contents);
			}
			if ( isset($config_vars['user']) AND $config_vars['user'] != '') {
				$contents = preg_replace('/^user.*=.*/im', 'user = '. trim($config_vars['user']), $contents);
			}
			if ( isset($config_vars['password']) AND $config_vars['password'] != '' ) {
				$contents = preg_replace('/^password.*=.*/im', 'password = '. trim($config_vars['password']), $contents);
			}
			if ( isset($config_vars['installer_enabled']) AND $config_vars['installer_enabled'] != '' ) {
				$contents = preg_replace('/^installer_enabled.*=.*/im', 'installer_enabled = '. (bool)$config_vars['installer_enabled'], $contents);
			}
			if ( isset($config_vars['base_url']) AND $config_vars['base_url'] != '' ) {
				$contents = preg_replace('/^base_url.*=.*/im', 'base_url = '. preg_replace('@^(?:http://)?([^/]+)@i', '', $config_vars['base_url']), $contents);
			}
			if ( isset($config_vars['storage_dir']) AND $config_vars['storage_dir'] != '' ) {
				$contents = preg_replace('/^storage.*=.*/im', 'storage = '. $config_vars['storage_dir'], $contents);
			}
			if ( isset($config_vars['log_dir']) AND $config_vars['log_dir'] != '' ) {
				$contents = preg_replace('/^log.*=.*/im', 'log = '. $config_vars['log_dir'], $contents);
			}
			if ( isset($config_vars['cache_dir']) AND $config_vars['cache_dir'] != '' ) {
				$contents = preg_replace('/^dir.*=.*/im', 'dir = '. $config_vars['cache_dir'], $contents);
			}

			//Only change if it isn't already set
			if ( isset($this->config_vars['other']['salt']) AND ( $this->config_vars['other']['salt'] == '' OR $this->config_vars['other']['salt'] == '0' )
					AND isset($config_vars['salt']) AND $config_vars['salt'] != '' ) {
				$contents = preg_replace('/^salt.*=.*/im', 'salt = '. $config_vars['salt'], $contents);
			}

			if ( isset($config_vars['installer_enabled']) AND $config_vars['installer_enabled'] != '' ) {
				$contents = preg_replace('/^installer_enabled.*=.*/im', 'installer_enabled = '. $config_vars['installer_enabled'], $contents);
			}

			if ( isset($config_vars['primary_company_id']) AND $config_vars['primary_company_id'] != '' ) {
				$contents = preg_replace('/^primary_company_id.*=.*/im', 'primary_company_id = '. $config_vars['primary_company_id'], $contents);
			}

			//var_dump($contents);
			Debug::text('Modified Config File!', __FILE__, __LINE__, __METHOD__,9);

			return file_put_contents( CONFIG_FILE, $contents);
		} else {
			Debug::text('Config File Not Writable!', __FILE__, __LINE__, __METHOD__,9);
		}

		return FALSE;
	}


	function setVersions() {
		if ( is_array($this->versions) ) {
			foreach( $this->versions as $name => $value ) {
				$sslf = new SystemSettingListFactory();
				$sslf->getByName( $name );
				if ( $sslf->getRecordCount() == 1 ) {
					$obj = $sslf->getCurrent();
				} else {
					$obj = new SystemSettingListFactory();
				}

				$obj->setName( $name );
				$obj->setValue( $value );
				if ( $obj->isValid() ) {
					if ( $obj->Save() === FALSE ) {
						return FALSE;
					}
				} else {
					return FALSE;
				}
			}
		}

		return TRUE;
	}
	/*

		Database Schema functions

	*/
	function checkDatabaseExists( $database_name ) {
		Debug::text('Database Name: '. $database_name, __FILE__, __LINE__, __METHOD__,9);
		$db_conn = $this->getDatabaseConnection();

		if ( $db_conn == FALSE ) {
			return FALSE;
		}

		$database_arr = $db_conn->MetaDatabases();

		if ( in_array($database_name, $database_arr ) ) {
			Debug::text('Exists - Database Name: '. $database_name, __FILE__, __LINE__, __METHOD__,9);
			return TRUE;
		}

		Debug::text('Does not Exist - Database Name: '. $database_name, __FILE__, __LINE__, __METHOD__,9);
		return FALSE;
	}

	function createDatabase( $database_name ) {
		Debug::text('Database Name: '. $database_name, __FILE__, __LINE__, __METHOD__,9);

		require_once( Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR .'adodb'. DIRECTORY_SEPARATOR .'adodb.inc.php');

		if ( $database_name == '' ) {
			Debug::text('Database Name invalid ', __FILE__, __LINE__, __METHOD__,9);
			return FALSE;
		}

		$db_conn = $this->getDatabaseConnection();
		if ( $db_conn == FALSE ) {
			Debug::text('No Database Connection.', __FILE__, __LINE__, __METHOD__,9);
			return FALSE;
		}
		Debug::text('Attempting to Create Database...', __FILE__, __LINE__, __METHOD__,9);

		$dict = NewDataDictionary( $db_conn );

		$sqlarray = $dict->CreateDatabase( $database_name );
		return $dict->ExecuteSQLArray($sqlarray);
	}

	function checkTableExists( $table_name ) {
		Debug::text('Table Name: '. $table_name, __FILE__, __LINE__, __METHOD__,9);
		$db_conn = $this->getDatabaseConnection();

		if ( $db_conn == FALSE ) {
			return FALSE;
		}

		$table_arr = $db_conn->MetaTables();

		if ( in_array($table_name, $table_arr ) ) {
			Debug::text('Exists - Table Name: '. $table_name, __FILE__, __LINE__, __METHOD__,9);
			return TRUE;
		}

		Debug::text('Does not Exist - Table Name: '. $table_name, __FILE__, __LINE__, __METHOD__,9);
		return FALSE;
	}

	//Get all schema versions
	function getAllSchemaVersions( $group = array('A','B','T') ) {
		if ( !is_array($group) ) {
			$group = array( $group );
		}

		$is_obj = new InstallSchema( $this->getDatabaseDriver(), '', NULL, $this->getIsUpgrade() );

		$schema_files = array();

		$dir = $is_obj->getSQLFileDirectory();
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				list($schema_base_name,$extension) = explode('.', $file);
				$schema_group = substr($schema_base_name, -1,1 );
				Debug::text('Schema: '. $file .' Group: '. $schema_group, __FILE__, __LINE__, __METHOD__,9);

				if ($file != "." AND $file != ".."
						AND substr($file,1,0) != '.'
						AND in_array($schema_group, $group) ) {
					$schema_versions[] = basename($file,'.sql');
				}
			}
			closedir($handle);
		}

		sort($schema_versions);
		Debug::Arr($schema_versions, 'Schema Versions', __FILE__, __LINE__, __METHOD__,9);

		return $schema_versions;
	}


	//Creates DB schema starting at and including start_version, and ending at, including end version.
	//Starting at NULL is first version, ending at NULL is last version.
	function createSchemaRange( $start_version = NULL, $end_version = NULL, $group = array('A','B','T') ) {
		global $cache, $progress_bar;

		$schema_versions = $this->getAllSchemaVersions( $group );

		Debug::Arr($schema_versions, 'Schema Versions: ', __FILE__, __LINE__, __METHOD__,9);

		$total_schema_versions = count($schema_versions);
		if ( is_array($schema_versions) AND $total_schema_versions > 0 ) {
			$this->getDatabaseConnection()->StartTrans();
			$x=0;
			foreach( $schema_versions as $schema_version ) {
				if ( ( $start_version === NULL OR $schema_version >= $start_version )
					AND ( $end_version === NULL OR $schema_version <= $end_version )
						) {

					$create_schema_result = $this->createSchema( $schema_version );

					if ( is_object($progress_bar) ) {
						$progress_bar->setValue( Misc::calculatePercent( $x, $total_schema_versions ) );
						$progress_bar->display();
					}

					if ( $create_schema_result === FALSE ) {
						Debug::text('CreateSchema Failed! On Version: '. $schema_version, __FILE__, __LINE__, __METHOD__,9);
						return FALSE;
					}
				}
				$x++;
			}
			//$this->getDatabaseConnection()->FailTrans();
			$this->getDatabaseConnection()->CompleteTrans();
		}

		$cache->clean(); //Clear all cache.

		return TRUE;
	}

	function createSchema( $version ) {
		if ( $version == '' ) {
			return FALSE;
		}

		$install = FALSE;

		$group = substr( $version,-1,1);
		$version_number = substr( $version,0,(strlen($version)-1));

		Debug::text('Version: '. $version .' Version Number: '. $version_number .' Group: '. $group, __FILE__, __LINE__, __METHOD__,9);

		//Only create schema if current system settings do not exist, or they are
		//older then this current schema version.
		if ( $this->checkTableExists( 'system_setting') == TRUE ) {
			Debug::text('System Setting Table DOES exist...', __FILE__, __LINE__, __METHOD__,9);

			$sslf = new SystemSettingListFactory();
			$sslf->getByName( 'schema_version_group_'. substr( $version,-1,1) );
			if ( $sslf->getRecordCount() > 0 ) {
				$ss_obj = $sslf->getCurrent();
				Debug::text('Found System Setting Entry: '. $ss_obj->getValue(), __FILE__, __LINE__, __METHOD__,9);

				if ( $ss_obj->getValue() < $version_number ) {
					Debug::text('Schema version is older, installing...', __FILE__, __LINE__, __METHOD__,9);
					$install = TRUE;
				} else {
					Debug::text('Schema version is equal, or newer then what we are trying to install...', __FILE__, __LINE__, __METHOD__,9);
					$install = FALSE;
				}
			} else {
				Debug::text('Did not find System Setting Entry...', __FILE__, __LINE__, __METHOD__,9);
				$install = TRUE;
			}
		} else {
			Debug::text('System Setting Table does not exist...', __FILE__, __LINE__, __METHOD__,9);
			$install = TRUE;
		}

		if ( $install == TRUE ) {
			$is_obj = new InstallSchema( $this->getDatabaseDriver(), $version, $this->getDatabaseConnection(), $this->getIsUpgrade() );
			return $is_obj->InstallSchema();
		}

		return TRUE;
	}


	/*

		System Requirements

	*/

	function getPHPVersion() {
		return PHP_VERSION;
	}

	function checkPHPVersion($php_version = NULL) {
		// Return
		// 0 = OK
		// 1 = Invalid
		// 2 = UnSupported

		if ( $php_version == NULL ) {
			$php_version = $this->getPHPVersion();
		}
		Debug::text('Comparing with Version: '. $php_version, __FILE__, __LINE__, __METHOD__,9);

		$min_version = '5.0.0';
		$max_version = '5.3.99';

		$unsupported_versions = array('');

		/*
			Invalid PHP Versions:
				v5.0.4 - Fails to assign object values by ref. In ViewTimeSheet.php $smarty->assign_by_ref( $pp_obj->getId() ) fails.
				v5.2.2 - Fails to populate $HTTP_RAW_POST_DATA http://bugs.php.net/bug.php?id=41293
				 	   - Implemented work around in global.inc.php
		*/
		$invalid_versions = array('5.0.4');


		if ( version_compare( $php_version, $min_version, '<') == 1 ) {
			//Version too low
			$retval = 1;
		} elseif ( version_compare( $php_version, $max_version, '>') == 1 ) {
			//UnSupported
			$retval = 2;
		} else {
			$retval = 0;
		}

		foreach( $unsupported_versions as $unsupported_version ) {
			if ( version_compare( $php_version, $unsupported_version, 'eq') == 1 ) {
				$retval = 2;
				break;
			}
		}

		foreach( $invalid_versions as $invalid_version ) {
			if ( version_compare( $php_version, $invalid_version, 'eq') == 1 ) {
				$retval = 1;
				break;
			}
		}

		//Debug::text('RetVal: '. $retval, __FILE__, __LINE__, __METHOD__,9);
		return $retval;
	}

	function getDatabaseType( $type = NULL ) {
		if ( $type != '' ) {
			$db_type = $type;
		} else {
			//$db_type = $this->config_vars['database']['type'];
			$db_type = $this->getDatabaseDriver();
		}

		if ( stristr($db_type, 'postgres') ) {
			$retval = 'postgresql';
		} elseif ( stristr($db_type, 'mysql') ) {
			$retval = 'mysql';
		} else {
			$retval = 1;
		}

		return $retval;
	}

	function getMemoryLimit() {
		//
		// NULL = unlimited
		// INT = limited to that value

		$raw_limit = ini_get('memory_limit');
		//Debug::text('RAW Limit: '. $raw_limit, __FILE__, __LINE__, __METHOD__,9);
		$limit = (int)rtrim($raw_limit, 'M');
		//Debug::text('Limit: '. $limit, __FILE__, __LINE__, __METHOD__,9);

		if ( $raw_limit == '' ) {
			return NULL;
		}

		return $limit;
	}

	function getPHPConfigFile() {
		return get_cfg_var("cfg_file_path");
	}

	function getConfigFile() {
		return CONFIG_FILE;
	}

	function getPHPIncludePath() {
		return get_cfg_var("include_path");
	}

	function getDatabaseVersion() {
		if ( $this->getDatabaseType() == 'postgresql' ) {
			$version = @pg_version();
			if ( $version == FALSE ) {
				//No connection
				return NULL;
			} else {
				return $version['server'];
			}
		} elseif ( $this->getDatabaseType() == 'mysqlt' OR $this->getDatabaseType() == 'mysqli' ) {
			$version = @get_server_info();
			return $version;
		}

		return FALSE;
	}

	function getDatabaseTypeArray() {
		$retval = array();

		if ( function_exists('pg_connect') ) {
			$retval['postgres8'] = 'PostgreSQL v8+';

			// set edb_redwood_date = 'off' must be set, otherwise enterpriseDB
			// changes all date columns to timestamp columns and breaks TimeTrex.
			$retval['enterprisedb'] = 'EnterpriseDB (DISABLE edb_redwood_date)';
		}
		if ( function_exists('mysqli_real_connect') ) {
			$retval['mysqli'] = 'MySQLi (v5.0.48+ w/InnoDB)';
		}
		if ( function_exists('mysql_connect') ) {
			$retval['mysqlt'] = 'MySQL (Legacy Driver w/InnoDB)';
		}

		return $retval;
	}

	function checkDatabaseType() {
		// Return
		//
		// 0 = OK
		// 1 = Invalid
		// 2 = Unsupported

		$retval = 1;

		if ( function_exists('pg_connect') ) {
			$retval = 0;
		} elseif ( function_exists('mysql_connect') ) {
			$retval = 0;
		} elseif ( function_exists('mysqli_real_connect') ) {
			$retval = 0;
		}

		return $retval;
	}

	function checkDatabaseVersion() {
		$db_version = (string)$this->getDatabaseVersion();

		if ( $this->getDatabaseType() == 'postgresql' ) {
			if ( $db_version == NULL OR version_compare( $db_version, '8.0', '>') == 1 ) {
				return 0;
			}
		} elseif ( $this->getDatabaseType() == 'mysql' ) {
			//Require at least. 4.1.3 and use MySQLi extension?
			if ( version_compare( $db_version, '4.1.3', '>=') == 1 ) {
				return 0;
			}
		}

		return 1;
	}

	function checkDatabaseEngine() {
		//
		// For MySQL only, this checks to make sure InnoDB is enabled!
		//
		Debug::Text('Checking DatabaseEngine...', __FILE__, __LINE__, __METHOD__,10);
		if ($this->getDatabaseType() != 'mysql' ) {
			return TRUE;
		}

		$db_conn = $this->getDatabaseConnection();
		if ( $db_conn == FALSE ) {
			Debug::text('No Database Connection.', __FILE__, __LINE__, __METHOD__,9);
			return FALSE;
		}

		$query = 'show engines';
		$storage_engines = $db_conn->getAll($query);
		Debug::Arr($storage_engines, 'Available Storage Engines:', __FILE__, __LINE__, __METHOD__,9);
		if ( is_array($storage_engines) ) {
			foreach( $storage_engines as $key => $data ) {
				Debug::Text('Engine: '. $data['Engine'] .' Support: '. $data['Support'], __FILE__, __LINE__, __METHOD__,10);
				if ( strtolower($data['Engine']) == 'innodb' AND ( strtolower($data['Support']) == 'yes' OR strtolower($data['Support']) == 'default' )  ) {
					Debug::text('InnoDB is available!', __FILE__, __LINE__, __METHOD__,9);
					return TRUE;
				}
			}
		}

		Debug::text('InnoDB is NOT available!', __FILE__, __LINE__, __METHOD__,9);
		return FALSE;
	}

	function checkPEAR() {
		@include_once('PEAR.php');

		if ( class_exists('PEAR') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARHTML_Progress() {
		include_once('HTML/Progress.php');

		if ( class_exists('HTML_Progress') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARHTML_AJAX() {
		include_once('HTML/AJAX/Server.php');

		if ( class_exists('HTML_AJAX_Server') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARHTTP_Download() {
		include_once('HTTP/Download.php');

		if ( class_exists('HTTP_Download') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARValidate() {
		include_once('Validate.php');

		if ( class_exists('Validate') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARValidate_Finance() {
		include_once('Validate/Finance.php');

		if ( class_exists('Validate_Finance') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARValidate_Finance_CreditCard() {
		include_once('Validate/Finance/CreditCard.php');

		if ( class_exists('Validate_Finance_CreditCard') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARNET_Curl() {
		include_once('Net/Curl.php');

		if ( class_exists('NET_Curl') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARMail() {
		include_once('Mail.php');

		if ( class_exists('Mail') ) {
			return 0;
		}

		return 1;
	}

	function checkPEARMail_Mime() {
		include_once('Mail/mime.php');

		if ( class_exists('Mail_Mime') ) {
			return 0;
		}

		return 1;
	}

	function checkMAIL() {
		if ( function_exists('mail') ) {
			return 0;
		}

		return 1;
	}

	function checkGETTEXT() {
		if ( function_exists('gettext') ) {
			return 0;
		}

		return 1;
	}

	function checkBCMATH() {
		if ( function_exists('bcscale') ) {
			return 0;
		}

		return 1;
	}

	function checkCALENDAR() {
		if ( function_exists('easter_date') ) {
			return 0;
		}

		return 1;
	}

	function checkSOAP() {
		if ( class_exists('SoapServer') ) {
			return 0;
		}

		return 1;
	}

	function checkMCRYPT() {
		if ( function_exists('mcrypt_module_open') ) {
			return 0;
		}

		return 1;
	}

	function checkGD() {
		if ( function_exists('imagefontheight') ) {
			return 0;
		}

		return 1;
	}

	function checkSimpleXML() {
		if ( class_exists('SimpleXMLElement') ) {
			return 0;
		}

		return 1;
	}


	function checkWritableConfigFile() {
		if ( is_writable( CONFIG_FILE ) ) {
			return 0;
		}

		return 1;
	}

	function checkWritableCacheDirectory() {
		if ( isset($this->config_vars['cache']['dir']) AND is_writable($this->config_vars['cache']['dir']) ) {
			return 0;
		}

		return 1;
	}

	function checkCleanCacheDirectory() {
		if ( DEPLOYMENT_ON_DEMAND == FALSE ) {
			$raw_cache_files = scandir( $this->config_vars['cache']['dir'] );

			if ( is_array($raw_cache_files) AND count($raw_cache_files) > 0 ) {
				foreach( $raw_cache_files as $cache_file ) {
					if ( $cache_file != '.' AND $cache_file != '..' AND stristr( $cache_file, '.lock') === FALSE ) {
						return 1;
					}
				}
			}
		}

		return 0;
	}

	function checkWritableStorageDirectory() {
		if ( isset($this->config_vars['path']['storage']) AND is_writable($this->config_vars['path']['storage']) ) {
			return 0;
		}

		return 1;
	}

	function checkWritableLogDirectory() {
		if ( isset($this->config_vars['path']['log']) AND is_writable($this->config_vars['path']['log']) ) {
			return 0;
		}

		return 1;
	}

	function checkPHPSafeMode() {
		if ( ini_get('safe_mode') != '1' ) {
			return 0;
		}

		return 1;
	}

	function checkPHPMemoryLimit() {
		if ( $this->getMemoryLimit() == NULL OR $this->getMemoryLimit() >= 128 ) {
			return 0;
		}

		return 1;
	}

	function checkPHPMagicQuotesGPC() {
		if ( get_magic_quotes_gpc() == 1 ) {
			return 1;
		}

		return 0;
	}

	function getCurrentTimeTrexVersion() {
		//return '1.2.1';
		return APPLICATION_VERSION;
	}

	function getLatestTimeTrexVersion() {
		if ( $this->checkSOAP() == 0 ) {
			$ttsc = new TimeTrexSoapClient();
			return $ttsc->getSoapObject()->getInstallerLatestVersion();
		}

		return FALSE;
	}

	function checkTimeTrexVersion() {
		$current_version = $this->getCurrentTimeTrexVersion();
		$latest_version = $this->getLatestTimeTrexVersion();

		if ( $latest_version == FALSE ) {
			return 1;
		} elseif ( version_compare( $current_version, $latest_version, '>=') == TRUE ) {
			return 0;
		}

		return 2;
	}

	function checkAllRequirements( $post_install_requirements_only = FALSE ) {
		// Return
		//
		// 0 = OK
		// 1 = Invalid
		// 2 = Unsupported

		//Total up each OK, Invalid, and Unsupported requirements
		$retarr = array(
						0 => 0,
						1 => 0,
						2 => 0
						);

		$retarr[$this->checkPHPVersion()]++;
		$retarr[$this->checkDatabaseType()]++;
		$retarr[$this->checkSOAP()]++;
		$retarr[$this->checkBCMATH()]++;
		$retarr[$this->checkCALENDAR()]++;
		$retarr[$this->checkGETTEXT()]++;
		$retarr[$this->checkGD()]++;
		$retarr[$this->checkSimpleXML()]++;
		$retarr[$this->checkMAIL()]++;

		$retarr[$this->checkPEAR()]++;

		//PEAR modules are bundled as of v1.2.0
		if ( $post_install_requirements_only == FALSE ) {
			$retarr[$this->checkWritableConfigFile()]++;
			$retarr[$this->checkWritableCacheDirectory()]++;
			$retarr[$this->checkCleanCacheDirectory()]++;
			$retarr[$this->checkWritableStorageDirectory()]++;
			$retarr[$this->checkWritableLogDirectory()]++;
		}

		$retarr[$this->checkPHPSafeMode()]++;
		$retarr[$this->checkPHPMemoryLimit()]++;
		$retarr[$this->checkPHPMagicQuotesGPC()]++;

		if ( $this->getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$retarr[$this->checkMCRYPT()]++;
		}

		//Debug::Arr($retarr, 'RetArr: ', __FILE__, __LINE__, __METHOD__,9);

		if ( $retarr[1] > 0 ) {
			return 1;
		} elseif ( $retarr[2] > 0 ) {
			return 2;
		} else {
			return 0;
		}
	}

	function getFailedRequirements( $post_install_requirements_only = FALSE ) {

		$fail_all = FALSE;

		$retarr[] = 'Require';

		if ( $fail_all == TRUE OR $this->checkPHPVersion() != 0 ) {
			$retarr[] = 'PHPVersion';
		}

		if ( $fail_all == TRUE OR $this->checkDatabaseType() != 0 ) {
			$retarr[] = 'DatabaseType';
		}


		if ( $fail_all == TRUE OR $this->checkSOAP() != 0 ) {
			$retarr[] = 'SOAP';
		}

		if ( $fail_all == TRUE OR $this->checkBCMATH() != 0 ) {
			$retarr[] = 'BCMATH';
		}

		if ( $fail_all == TRUE OR $this->checkCALENDAR() != 0 ) {
			$retarr[] = 'CALENDAR';
		}

		if ( $fail_all == TRUE OR $this->checkGETTEXT() != 0 ) {
			$retarr[] = 'GETTEXT';
		}

		if ( $fail_all == TRUE OR $this->checkGD() != 0 ) {
			$retarr[] = 'GD';
		}

		if ( $fail_all == TRUE OR $this->checkSimpleXML() != 0 ) {
			$retarr[] = 'SIMPLEXML';
		}

		if ( $fail_all == TRUE OR $this->checkMAIL() != 0 ) {
			$retarr[] = 'MAIL';
		}


		//Bundled PEAR modules require the base PEAR package at least
		if ( $fail_all == TRUE OR $this->checkPEAR() != 0 ) {
			$retarr[] = 'PEAR';
		}

		if ( $post_install_requirements_only == FALSE ) {
			if ( $fail_all == TRUE OR $this->checkWritableConfigFile() != 0 ) {
				$retarr[] = 'WConfigFile';
			}
			if ( $fail_all == TRUE OR $this->checkWritableCacheDirectory() != 0 ) {
				$retarr[] = 'WCacheDir';
			}
			if ( $fail_all == TRUE OR $this->checkCleanCacheDirectory() != 0 ) {
				$retarr[] = 'CleanCacheDir';
			}
			if ( $fail_all == TRUE OR $this->checkWritableStorageDirectory() != 0 ) {
				$retarr[] = 'WStorageDir';
			}
			if ( $fail_all == TRUE OR $this->checkWritableLogDirectory() != 0 ) {
				$retarr[] = 'WLogDir';
			}
		}

		if ( $fail_all == TRUE OR $this->checkPHPSafeMode() != 0 ) {
			$retarr[] = 'PHPSafeMode';
		}
		if ( $fail_all == TRUE OR $this->checkPHPMemoryLimit() != 0 ) {
			$retarr[] = 'PHPMemoryLimit';
		}
		if ( $fail_all == TRUE OR $this->checkPHPMagicQuotesGPC() != 0 ) {
			$retarr[] = 'PHPMagicQuotesGPC';
		}

		if ( $fail_all == TRUE OR $this->getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			if ( $fail_all == TRUE OR $this->checkPEARValidate() != 0 ) {
				$retarr[] = 'PEARVal';
			}

			if ( $fail_all == TRUE OR $this->checkMCRYPT() != 0 ) {
				$retarr[] = 'MCRYPT';
			}
		}

		if ( isset($retarr) ) {
			return $retarr;
		}

		return FALSE;
	}
}
?>
