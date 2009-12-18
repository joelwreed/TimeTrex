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
 * $Id: Factory.class.php 3021 2009-11-11 23:33:03Z ipso $
 * $Date: 2009-11-11 15:33:03 -0800 (Wed, 11 Nov 2009) $
 */

/**
 * @package Core
 */
abstract class Factory {
	public $data = array();
	protected $next_insert_id = NULL;

	function __construct() {
		global $db, $cache;

		$this->db = $db;
		$this->cache = $cache;
		$this->Validator = new Validator();

		//Callback to the child constructor method.
		if (method_exists($this,'childConstruct') ) {
			$this->childConstruct();
		}

		return TRUE;
	}


	/*
	 * Cache functions
	 */
	function getCache($cache_id) {
		if ( is_object($this->cache) AND is_string( $this->cache->get($cache_id, $this->getTable(TRUE) ) ) ) {
			return unserialize( $this->cache->get($cache_id, $this->getTable(TRUE) ) );
		}

		return FALSE;
	}
	function saveCache($data, $cache_id) {
		if ( is_object($this->cache) ) {
			return $this->cache->save(serialize($data), $cache_id, $this->getTable(TRUE) );
		}
		return FALSE;
	}
	function removeCache($cache_id = NULL, $group_id = NULL ) {
		Debug::text('Attempting to remove cache: '. $cache_id , __FILE__, __LINE__, __METHOD__,10);
		if ( is_object($this->cache) ) {
			if ( $group_id == '' ) {
				$group_id = $this->getTable(TRUE);
			}
			if ( $cache_id != '' ) {
				Debug::text('Removing cache: '. $cache_id .' Group Id: '. $group_id, __FILE__, __LINE__, __METHOD__,10);
				return $this->cache->remove($cache_id, $group_id );
			} elseif ( $group_id != '' ) {
				Debug::text('Removing cache group: '. $group_id , __FILE__, __LINE__, __METHOD__,10);
				return $this->cache->clean( $group_id );
			}
		}

		return FALSE;
	}
	function setCacheLifeTime( $secs ) {
		if ( is_object($this->cache) ) {
			return $this->cache->setLifeTime( $secs );
		}

		return FALSE;
	}


	function getTable($strip_quotes = FALSE) {

		if ( isset($this->table) ) {
			if ( $strip_quotes == TRUE ) {
				return str_replace('"','', $this->table );
			} else {
				return $this->table;
			}
		}

		return FALSE;
	}

	//Generic function get any data from the data array.
	//Used mainly for the reports that return grouped queries and such.
	function getColumn( $column ) {

		if ( isset($this->data[$column]) ) {
			return $this->data[$column];
		}

		return FALSE;
	}

	function toBool($value) {
		$value = strtolower(trim($value));

		if ($value === TRUE OR $value == 1 OR $value == 't') {
			//return 't';
			return 1;
		} else {
			//return 'f';
			return 0;
		}
	}

	function fromBool($value) {
		$value = strtolower(trim($value));

		//if ($value == 't') {
		if ($value == 1 OR $value == 't' ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//Determines if the data is new data, or updated data.
	function isNew( $force_lookup = FALSE ) {
		//Debug::Arr( $this->getId() ,'getId: ', __FILE__, __LINE__, __METHOD__,10);
		if ( $this->getId() === FALSE ) {
			//New Data
			return TRUE;
		} elseif ( $force_lookup == TRUE ) {
			//See if we can find the ID to determine if the record needs to be inserted or update.
			$ph = array( 'id' => $this->getID() );
			$query = 'select id from '. $this->getTable() .' where id = ?';
			$retval = $this->db->GetOne($query, $ph);
			if ( $retval === FALSE ) {
				return TRUE;
			}
		}

		//Not new data
		return FALSE;
	}

	//Determines if we were called by a save function or not.
	//This is useful for determining if we are just validating or actually saving data. Problem is its too late to throw any new validation errors.
	function isSave() {
		$stack = debug_backtrace();

		if ( is_array($stack) ) {
			//Loop through and if we find a Save function call return TRUE.
			//Not sure if this will work in some more complex cases though.
			foreach( $stack as $data ) {
				if ( $data['function'] == 'Save' ) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	//Returns the calling function name
	function getCallerFunction() {
		$stack = debug_backtrace();
		if ( isset($stack[1]) ) {
			return $statc[1]['function'];
		}

		return FALSE;
	}

	function getLabelId() {
		//Gets the ID used in validator labels. If no ID, uses "-1";
		if ( $this->getId() == FALSE ) {
			return '-1';
		}

		return $this->getId();
	}

	function getId() {
		if ( isset($this->data['id']) AND $this->data['id'] != NULL) {
			return $this->data['id'];
		}

		return FALSE;
	}
	function setId($id) {
		/*
		if ($id != NULL) {
			//$this->data['id'] = (int)$id;
			$this->data['id'] = $id; //Allow ID to be set as FALSE. Essentially making a new entry.
		}
		*/
		$this->data['id'] = $id; //Allow ID to be set as FALSE. Essentially making a new entry.

		return true;
	}

	function getDeleted() {
		if ( isset($this->data['deleted']) ) {
			return $this->fromBool( $this->data['deleted'] );
		}

		return FALSE;
	}
	function setDeleted($bool) {
		$value = $this->toBool($bool);

		//Handle Postgres's boolean values.
		if ($value === TRUE) {
			//Only set this one we're deleting
			$this->setDeletedDate();
			$this->setDeletedBy();
		}

		$this->data['deleted'] = $this->toBool($value);

		return true;
	}

	function getCreatedDate() {
		if ( isset($this->data['created_date']) ) {
			return (int)$this->data['created_date'];
		}

		return FALSE;
	}
	function setCreatedDate($epoch = NULL) {
		$epoch = trim($epoch);

		if ( $epoch == NULL OR $epoch == '' OR $epoch == 0 ) {
			$epoch = TTDate::getTime();
		}

		if 	(	$this->Validator->isDate(		'created_date',
												$epoch,
												TTi18n::gettext('Incorrect Date')) ) {

			$this->data['created_date'] = $epoch;

			return TRUE;
		}

		return FALSE;

	}
	function getCreatedBy() {
		if ( isset($this->data['created_by']) ) {
			return (int)$this->data['created_by'];
		}

		return FALSE;
	}
	function setCreatedBy($id = NULL) {
		$id = trim($id);

		if ( empty($id) ) {
			global $current_user;

			if ( is_object($current_user) ) {
				$id = $current_user->getID();
			} else {
				return FALSE;
			}
		}

		$ulf = new UserListFactory();

		if ( $this->Validator->isResultSetWithRows(	'created_by',
													$ulf->getByID($id),
													TTi18n::gettext('Incorrect User')
													) ) {

			$this->data['created_by'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getUpdatedDate() {
		if ( isset($this->data['updated_date']) ) {
			return (int)$this->data['updated_date'];
		}

		return FALSE;
	}
	function setUpdatedDate($epoch = NULL) {
		$epoch = trim($epoch);

		if ( $epoch == NULL OR $epoch == '' OR $epoch == 0 ) {
			$epoch = TTDate::getTime();
		}

		if 	(	$this->Validator->isDate(		'updated_date',
												$epoch,
												TTi18n::gettext('Incorrect Date')) ) {

			$this->data['updated_date'] = $epoch;

			//return TRUE;
			//Return the value so we can use it in getUpdateSQL
			return $epoch;
		}

		return FALSE;

	}
	function getUpdatedBy() {
		if ( isset($this->data['updated_by']) ) {
			return (int)$this->data['updated_by'];
		}

		return FALSE;
	}
	function setUpdatedBy($id = NULL) {
		$id = trim($id);

		if ( empty($id) ) {
			global $current_user;

			if ( is_object($current_user) ) {
				$id = $current_user->getID();
			} else {
				return FALSE;
			}
		}

		$ulf = new UserListFactory();

		if ( $this->Validator->isResultSetWithRows(	'updated_by',
													$ulf->getByID($id),
													TTi18n::gettext('Incorrect User')
													) ) {
			$this->data['updated_by'] = $id;

			//return TRUE;
			return $id;
		}

		return FALSE;
	}


	function getDeletedDate() {
		if ( isset($this->data['deleted_date']) ) {
			return $this->data['deleted_date'];
		}

		return FALSE;
	}
	function setDeletedDate($epoch = NULL) {
		$epoch = trim($epoch);

		if ( $epoch == NULL OR $epoch == '' OR $epoch == 0 ) {
			$epoch = TTDate::getTime();
		}

		if 	(	$this->Validator->isDate(		'deleted_date',
												$epoch,
												TTi18n::gettext('Incorrect Date')) ) {

			$this->data['deleted_date'] = $epoch;

			return TRUE;
		}

		return FALSE;

	}
	function getDeletedBy() {
		if ( isset($this->data['deleted_by']) ) {
			return $this->data['deleted_by'];
		}

		return FALSE;
	}
	function setDeletedBy($id = NULL) {
		$id = trim($id);

		if ( empty($id) ) {
			global $current_user;

			if ( is_object($current_user) ) {
				$id = $current_user->getID();
			} else {
				return FALSE;
			}
		}

		$ulf = new UserListFactory();

		if ( $this->Validator->isResultSetWithRows(	'updated_by',
													$ulf->getByID($id),
													TTi18n::gettext('Incorrect User')
													) ) {

			$this->data['deleted_by'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function setCreatedAndUpdatedColumns( $data ) {
		//Update array in-place.
		if ( isset($data['created_by']) AND is_numeric($data['created_by']) AND $data['created_by'] > 0 ) {
			$this->setCreatedBy( $data['created_by'] );
		}
		if ( isset($data['created_by_id']) AND is_numeric($data['created_by_id']) AND $data['created_by_id'] > 0 ) {
			$this->setCreatedBy( $data['created_by_id'] );
		}
		if ( isset($data['created_date']) AND $data['created_date'] != FALSE ) {
			$this->setCreatedDate( TTDate::parseDateTime( $data['created_date'] ) );
		}

		if ( isset($data['updated_by']) AND is_numeric($data['updated_by']) AND $data['updated_by'] > 0 ) {
			$this->setUpdatedBy( $data['updated_by'] );
		}
		if ( isset($data['updated_by_id']) AND is_numeric($data['updated_by_id']) AND $data['updated_by_id'] > 0 ) {
			$this->setUpdatedBy( $data['updated_by_id'] );
		}
		if ( isset($data['updated_date']) AND $data['created_date'] != FALSE ) {
			$this->setUpdatedDate( TTDate::parseDateTime( $data['updated_date'] ) );
		}

		return TRUE;
	}
	function getCreatedAndUpdatedColumns( &$data, $include_columns = NULL ) {
		//Update array in-place.
		if ( $include_columns == NULL OR ( isset($include_columns['created_by_id']) AND $include_columns['created_by_id'] == TRUE) ) {
			$data['created_by_id'] = $this->getCreatedBy();
		}
		if ( $include_columns == NULL OR ( isset($include_columns['created_by']) AND $include_columns['created_by'] == TRUE) ) {
			$data['created_by'] = Misc::getFullName( $this->getColumn('created_by_first_name'), $this->getColumn('created_by_middle_name'), $this->getColumn('created_by_last_name') );
		}
		if ( $include_columns == NULL OR ( isset($include_columns['created_date']) AND $include_columns['created_date'] == TRUE) ) {
			$data['created_date'] = TTDate::getAPIDate( 'DATE+TIME', $this->getCreatedDate() );
		}
		if ( $include_columns == NULL OR ( isset($include_columns['updated_by_id']) AND $include_columns['updated_by_id'] == TRUE) ) {
			$data['updated_by_id'] = $this->getUpdatedBy();
		}
		if ( $include_columns == NULL OR ( isset($include_columns['updated_by']) AND $include_columns['updated_by'] == TRUE) ) {
			$data['updated_by'] = Misc::getFullName( $this->getColumn('updated_by_first_name'), $this->getColumn('updated_by_middle_name'), $this->getColumn('updated_by_last_name') );
		}
		if ( $include_columns == NULL OR ( isset($include_columns['updated_date']) AND $include_columns['updated_date'] == TRUE) ) {
			$data['updated_date'] = TTDate::getAPIDate( 'DATE+TIME', $this->getUpdatedDate() );
		}

		return TRUE;
	}

	function getPermissionColumns( &$data, $object_id, $created_by_id, $permission_children_ids ) {
		$permission = new Permission();

		$data['is_owner'] = $permission->isOwner( $created_by_id, $object_id );

		if ( is_array($permission_children_ids) ) {
			$data['is_child'] = $permission->isChild( $object_id, $permission_children_ids );
		} else {
			$data['is_child'] = FALSE;
		}

		return TRUE;
	}

	function getOptions($name, $parent = NULL) {
		if ( $parent == NULL OR $parent == '') {
			return $this->_getFactoryOptions( $name );
		} else {
			$retval = $this->_getFactoryOptions( $name );
			if ( isset($retval[$parent]) ){
				return $retval[$parent];
			}
		}

		return FALSE;
	}
	protected function _getFactoryOptions( $name ) {
		return FALSE;
	}

	function getVariableToFunctionMap( $data = NULL ) {
		return $this->_getVariableToFunctionMap( $data );
	}
	protected function _getVariableToFunctionMap( $data ) {
		return FALSE;
	}

	function getRecordCount() {
		if ( isset($this->rs) ) {
			return $this->rs->RecordCount();
		}

		return FALSE;
	}

	function getCurrentRow( $offset = 1 ) {
		if ( isset($this->rs) AND isset($this->rs->_currentRow) ) {
			return $this->rs->_currentRow+(int)$offset;
		}

		return FALSE;
	}

	private function getRecordSetColumnList($rs) {
		if (is_object($rs)) {
			for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++) {
				$field = $rs->FetchField($i);
				$fields[] = $field->name;
			}

			return $fields;
		}

		return FALSE;
	}

	protected function getListSQL($array, &$ph = NULL) {
		if ( $ph === NULL ) {
			if ( is_array( $array ) AND count($array) > 0) {
				return '\''.implode('\',\'',$array).'\'';
			} elseif ( is_array($array) ) {
				//Return NULL, because this is an empty array.
				return 'NULL';
			} elseif ( $array == '' ) {
				return 'NULL';
			}

			//Just a single ID, return it.
			return $array;
		} else {
			//Debug::Arr($ph, 'Place Holder BEFORE:', __FILE__, __LINE__, __METHOD__,10);

			//Append $array values to end of $ph, return
			//one "?," for each element in $array.

			$array_count = count($array);
			if ( is_array( $array ) AND $array_count > 0) {
				foreach( $array as $key => $val ) {
					$ph_arr[] = '?';

					//Make sure we filter out any FALSE or NULL values from going into a SQL list.
					//Replace them with "-1"'s so we keep the same number of place holders.
					//This should hopefully prevent SQL errors if a FALSE creeps into the SQL list array.
					if ( !is_null($val) AND ( is_numeric( $val ) OR is_string( $val ) ) ) {
						$ph[] = $val;
					} else {
						$ph[] = '-1';
					}
				}

				if ( isset($ph_arr) ) {
					$retval = implode(',',$ph_arr);
				}
			} elseif ( is_array($array) ) {
				//Return NULL, because this is an empty array.
				//This may have to return -1 instead of NULL
				//$ph[] = 'NULL';
				$ph[] = -1;
				$retval = '?';
			} elseif ( $array == '' ) {
				//$ph[] = 'NULL';
				$ph[] = -1;
				$retval = '?';
			} else {
				$ph[] = $array;
				$retval = '?';
			}

			//Debug::Arr($ph, 'Place Holder AFTER:', __FILE__, __LINE__, __METHOD__,10);

			//Just a single ID, return it.
			return $retval;
		}
	}

	//This function takes plain input from the user and creates a SQL statement for filtering
	//based on a date range.
	// Supported Syntax:
	//					>=01-Jan-09
	//					<=01-Jan-09
	//					<01-Jan-09
	//					>01-Jan-09
	//					>01-Jan-09 & <10-Jan-09
	//
	function getDateRangeSQL( $str, $column, $use_epoch = TRUE ) {

		if ( $str == '' ) {
			return FALSE;
		}

		if ( $column == '' ) {
			return FALSE;
		}

		$operators = array(
						   '>',
						   '<',
						   '>=',
						   '<=',
						   '=',
						   );

		$operations = FALSE;

		//Parse input, separate any subqueries first.
		$split_str = explode( '&', $str, 2 ); //Limit sub-queries
		if ( is_array($split_str) ) {
			foreach( $split_str as $tmp_str ) {
				$tmp_str = trim($tmp_str);

				$date = (int)TTDate::parseDateTime( str_replace( $operators, '', $tmp_str ) );
				//Debug::text(' Parsed Date: '. $tmp_str .' To: '. TTDate::getDate('DATE+TIME', $date) .' ('. $date .')', __FILE__, __LINE__, __METHOD__,10);

				if ( $date != 0 ) {
					preg_match('/^>=|>|<=|</i', $tmp_str, $operator );

					//Debug::Arr($operator, ' Operator: ', __FILE__, __LINE__, __METHOD__,10);
					if ( isset($operator[0]) AND in_array( $operator[0], $operators) ) {
						if ( $operator[0] == '<=' ) {
							$date = TTDate::getEndDayEpoch( $date );
						} elseif ( $operator[0] == '>' ) {
							$date = TTDate::getEndDayEpoch( $date );
						}

						$operations[] = $column .' '. $operator[0] .' '. $date;
					} else {
						//Debug::text(' No operator specified... Using a 24hr period', __FILE__, __LINE__, __METHOD__,10);
						$operations[] = $column .' >= '. TTDate::getBeginDayEpoch( $date );
						$operations[] = $column .' <= '. TTDate::getEndDayEpoch( $date );
					}
				}
			}
		}

		//Debug::Arr($operations, ' Operations: ', __FILE__, __LINE__, __METHOD__,10);
		if ( is_array($operations) ) {
			$retval = ' ( '. implode(' AND ', $operations ) .' )';
			Debug::text(' Query parts: '. $retval, __FILE__, __LINE__, __METHOD__,10);

			return $retval;
		}

		return FALSE;
	}

	//Parses out the exact column name, without any aliases, or = signs in it.
	private function parseColumnName($column) {
		$column = trim($column);

		if ( strstr($column, '=') ) {
			$tmp_column = explode('=', $column);
			$retval = trim($tmp_column[0]);
			unset($tmp_column);
		} else {
			$retval = $column;
		}

		if ( strstr($retval, '.') ) {
			$tmp_column = explode('.', $retval);
			$retval = $tmp_column[1];
			unset($tmp_column);
		}
		//Debug::Text('Column: '. $column .' RetVal: '. $retval, __FILE__, __LINE__, __METHOD__,10);

		return $retval;
	}

	protected function getWhereSQL($array, $append_where = FALSE) {
		//Make this a multi-dimensional array, the first entry
		//is the WHERE clauses with '?' for placeholders, the second is
		//the array to replace the placeholders with.
		if (is_array($array) ) {
			$rs = $this->getEmptyRecordSet();
			$fields = $this->getRecordSetColumnList($rs);

			foreach ($array as $orig_column => $expression) {
				$orig_column = trim($orig_column);
				$column = $this->parseColumnName( $orig_column );

				$expression = trim($expression);

				if ( in_array($column, $fields) ) {
					$sql_chunks[] = $orig_column.' '.$expression;
				}
			}

			if ( isset($sql_chunks) ) {
				$sql = implode(",", $sql_chunks);

				if ($append_where == TRUE) {
					return ' where '.$sql;
				} else {
					return ' AND '.$sql;
				}
			}
		}

		return FALSE;
	}

	protected function getColumnsFromAliases( $columns, $aliases ) {
		// Columns is the original column array.
		//
		// Aliases is an array of search => replace key/value pairs.
		//
		// This is used so the frontend can sort by the column name (ie: type) and it can be converted to type_id for the SQL query.
		if ( is_array($columns) AND is_array( $aliases ) ) {
			$columns = $this->convertFlexArray( $columns );

			//Debug::Arr($columns, 'Columns before: ', __FILE__, __LINE__, __METHOD__,10);

			foreach( $columns as $column => $sort_order ) {
				if ( isset($aliases[$column]) AND !isset($columns[$aliases[$column]]) ) {
					$retarr[$aliases[$column]] = $sort_order;
				} else {
					$retarr[$column] = $sort_order;
				}

			}
			//Debug::Arr($retarr, 'Columns after: ', __FILE__, __LINE__, __METHOD__,10);

			if ( isset($retarr) ) {
				return $retarr;
			}
		}

		return $columns;
	}

	protected function convertFlexArray( $array ) {
		//Flex doesn't appear to be consistent on the order the fields are placed into an assoc array, so
		//handle this type of array too:
		// array(
		//		0 => array('first_name' => 'asc')
		//		1 => array('last_name' => 'desc')
		//		)

		if ( isset($array[0]) AND is_array($array[0]) ) {
			Debug::text('Found Flex Sort Array, converting to proper format...', __FILE__, __LINE__, __METHOD__,10);

			//Debug::Arr($array, 'Before conversion...', __FILE__, __LINE__, __METHOD__,10);

			$new_arr = array();
			foreach( $array as $tmp_order => $tmp_arr ) {
				if ( is_array($tmp_arr) ) {
					foreach( $tmp_arr as $tmp_column => $tmp_order ) {
						$new_arr[$tmp_column] = $tmp_order;
					}
				}
			}
			$array = $new_arr;
			unset($tmp_key, $tmp_arr, $tmp_order, $tmp_column, $new_arr);
			//Debug::Arr($array, 'Converted format...', __FILE__, __LINE__, __METHOD__,10);
		}

		return $array;
	}

	protected function getSortSQL($array, $strict = TRUE, $additional_fields = NULL) {
		if ( is_array($array) ) {
			$array = $this->convertFlexArray( $array );

			$alt_order_options = array( 1 => 'asc', -1 => 'desc');
			$order_options = array('asc', 'desc');

			$rs = $this->getEmptyRecordSet();
			$fields = $this->getRecordSetColumnList($rs);

			//Merge additional fields
			if ( is_array($additional_fields) ) {
				$fields = array_merge( $fields, $additional_fields);
			}
			//Debug::Arr($fields, 'Column List:', __FILE__, __LINE__, __METHOD__,10);

			foreach ( $array as $orig_column => $order ) {
				$orig_column = trim($orig_column);

				$column = $this->parseColumnName( $orig_column );
				$order = trim($order);
				//Handle both order types.
				if ( is_numeric($order) ) {
					if ( isset($alt_order_options[$order]) ) {
						$order = $alt_order_options[$order];
					}
				}

				if ( $strict == FALSE
						OR ( 	(
									in_array($column, $fields)
									OR
									in_array($orig_column, $fields)
								)
								AND in_array( strtolower($order), $order_options)
							)
						) {
					//Make sure ';' does not appear in the resulting order string, to help prevent attacks in non-strict mode.
					if ( $strict == TRUE OR ( $strict == FALSE AND strpos( $orig_column, ';') === FALSE AND strpos( $order, ';') === FALSE ) ) {
						$sql_chunks[] = $orig_column.' '.$order;
					} else {
						Debug::text('ERROR: Found ";" in SQL order string: '. $orig_column .' Order: '. $order, __FILE__, __LINE__, __METHOD__,10);
					}
				} else {
					Debug::text('Invalid Sort Column/Order: '. $column .' Order: '. $order, __FILE__, __LINE__, __METHOD__,10);
				}
			}

			if ( isset($sql_chunks) ) {
				$sql = implode(',', $sql_chunks);

				return ' order by '. $this->db->escape( $sql );
			}
		}

		return FALSE;
	}

	public function getColumnList() {
		if ( is_array($this->data) AND count($this->data) > 0) {
			$column_list = array_keys($this->data);

			if ( $this->setUpdatedDate() !== FALSE ) {
				$column_list[] = 'updated_date';
			}
			if ( $this->setUpdatedBy() !== FALSE ) {
				$column_list[] = 'updated_by';
			}

			$column_list = array_unique($column_list);

			//Debug::Arr($this->data,'aColumn List', __FILE__, __LINE__, __METHOD__,10);
			//Debug::Arr($column_list,'bColumn List', __FILE__, __LINE__, __METHOD__,10);

			return $column_list;
		}

		return FALSE;
	}

	public function getEmptyRecordSet($id = NULL) {
		global $profiler;
		$profiler->startTimer( 'getEmptyRecordSet()' );

		if ($id == NULL) {
			$id = -1;
		}

		$column_list = $this->getColumnList();
		if ( is_array($column_list) ) {
			//Implode columns.
			$column_str = implode(',', $column_list);
		} else {
			$column_str = '*'; //Get empty RS with all columns.
		}

		try {
/*
			//Cache_Lite fails when caching this query.
			$query = 'select '. $column_str .' from '. $this->table .' where id = '. (int)$id;
			//Debug::text('Query: '. $query , __FILE__, __LINE__, __METHOD__,9);

			if ( $id == -1 ) {
				$cache_id = md5($this->getTable().$column_str.$id);

				$rs = $this->getCache($cache_id);
				if ( $rs === FALSE ) {
					$rs = $this->db->Execute($query);

					$this->saveCache($rs, $cache_id);
				}
			} else {
				$rs = $this->db->Execute($query);
			}
			Debug::Arr($rs, 'getEmptyRecordSet() RS:' , __FILE__, __LINE__, __METHOD__,9);
*/
			$query = 'select '. $column_str .' from '. $this->table .' where id = '. (int)$id;
			//Debug::text('Query: '. $query , __FILE__, __LINE__, __METHOD__,9);

			if ( $id == -1 ) {
				$rs = $this->db->CacheExecute(86400, $query);
			} else {
				$rs = $this->db->Execute($query);
			}
		} catch (Exception $e) {
			throw new DBError($e);
		}

		$profiler->stopTimer( 'getEmptyRecordSet()' );
		return $rs;
	}

	private function getUpdateQuery($data = NULL) {
		//Debug::text('Update' , __FILE__, __LINE__, __METHOD__,9);

		//
		// If the table has timestamp columns without timezone set
		// this function will think the data has changed, and update it.
		// PayStubFactory() had this issue.
		//

		//Debug::arr($this->data,'Data Arr', __FILE__, __LINE__, __METHOD__,10);

		//Add new columns to record set.
		//Check to make sure the columns exist in the table first though
		//Classes like station don't have updated_date, so we need to take that in to account.
		try {
			$rs = $this->getEmptyRecordSet( $this->getId() );
			//Debug::arr($rs->fields,'Data Arr Fields', __FILE__, __LINE__, __METHOD__,10);
		} catch (Exception $e) {
			throw new DBError($e);
		}
		if (!$rs) {
			Debug::text('No Record Found! Insert instead?' , __FILE__, __LINE__, __METHOD__,9);
			//Throw exception?
		}

		//If no columns changed, this will be FALSE.
		$query = $this->db->GetUpdateSQL($rs, $this->data);

		//No updates are fine. We still want to run postsave() etc...
		if ($query === FALSE) {
			$query = TRUE;
		} else {
			Debug::text('Data changed, set updated date: ', __FILE__, __LINE__, __METHOD__, 9);

			//Always run update query even if only updated_date was changed. Otherwise
			//we need to run two getEmptyRecordSet() calls, and two getUpdateSQL calls.
			/*
			if ( $this->setUpdatedDate() !== FALSE ) {
				$this->data['updated_date'] = NULL;
			}
			if ( $this->setUpdatedBy() !== FALSE ) {
				$this->data['updated_by'] = NULL;
			}

			//Create new record set with new columns
			//**This causes another query to be executed, because it needs to re-check the updated_date columns.
			//
			try {
				$rs = $this->getEmptyRecordSet( $this->getId() );
			} catch (Exception $e) {
				throw new DBError($e);
			}

			//Update new columns so ADODB knows they changed.
			$this->setUpdatedDate();
			$this->setUpdatedBy();

			//Debug::arr($this->data,'bData Arr', __FILE__, __LINE__, __METHOD__,10);

			$query = $this->db->GetUpdateSQL($rs, $this->data);
			*/
		}

		//Debug::text('Update Query: '. $query, __FILE__, __LINE__, __METHOD__, 9);

		return $query;
	}

	private function getInsertQuery($data = NULL) {
		Debug::text('Insert' , __FILE__, __LINE__, __METHOD__,9);

		//Debug::arr($this->data,'Data Arr', __FILE__, __LINE__, __METHOD__, 10);

		try {
			$rs = $this->getEmptyRecordSet();
		} catch (Exception $e) {
			throw new DBError($e);
		}

		//Use table name instead of recordset, especially when using CacheLite for caching empty recordsets.
		//$query = $this->db->GetInsertSQL($rs, $this->data);
		$query = $this->db->GetInsertSQL($this->getTable(), $this->data);

		//Debug::text('Insert Query: '. $query, __FILE__, __LINE__, __METHOD__, 9);

		return $query;
	}

	function StartTransaction() {
		Debug::text('StartTransaction(): Transaction Count: '. $this->db->transCnt .' Trans Off: '. $this->db->transOff, __FILE__, __LINE__, __METHOD__, 9);
		return $this->db->StartTrans();
	}

	function FailTransaction() {
		Debug::text('FailTransaction(): Transaction Count: '. $this->db->transCnt .' Trans Off: '. $this->db->transOff, __FILE__, __LINE__, __METHOD__, 9);
		return $this->db->FailTrans();
	}

	function CommitTransaction() {
		Debug::text('CommitTransaction(): Transaction Count: '. $this->db->transCnt .' Trans Off: '. $this->db->transOff, __FILE__, __LINE__, __METHOD__, 9);
		return $this->db->CompleteTrans();
	}

	//Call class specific validation function just before saving.
	function isValid() {
		if ( method_exists($this,'Validate') ) {
			Debug::text('Calling Validate()' , __FILE__, __LINE__, __METHOD__,10);
			$this->Validate();
		}

        return $this->Validator->isValid();
	}

	function getNextInsertId() {
		return $this->db->GenID( $this->pk_sequence_name );
	}

	//Determines to insert or update, and does it.
	//Have this handle created, createdby, updated, updatedby.
	function Save($reset_data = TRUE, $force_lookup = FALSE) {
		$this->StartTransaction();

		//Run Pre-Save function
		//This is called before validate so it can do extra calculations,etc before validation.
		//Should this AND validate() NOT be called when delete flag is set?
		if (method_exists($this,'preSave') ) {
			Debug::text('Calling preSave()' , __FILE__, __LINE__, __METHOD__,10);
			if ( $this->preSave() === FALSE ) {
				throw new GeneralError('preSave() failed.');
			}
		}

		//Don't validate when deleting
		if ( $this->getDeleted() == FALSE AND $this->isValid() === FALSE ) {
			throw new GeneralError('Invalid Data, not saving.');
		}

		//Should we insert, or update?
		if ( $this->isNew( $force_lookup ) ) {
			//Insert
			$time = TTDate::getTime();

			$this->setCreatedDate($time);
			$this->setCreatedBy();

			//Set updated date at the same time, so we can easily select last
			//updated, or last created records.
			$this->setUpdatedDate($time);
			$this->setUpdatedBy();

			unset($time);

			$insert_id = $this->getID();
			if ( $insert_id == FALSE ) {
				//Append insert ID to data array.
				$insert_id = $this->getNextInsertId();

				Debug::text('Insert ID: '. $insert_id , __FILE__, __LINE__, __METHOD__, 9);
				$this->setId($insert_id);
			}

			try {
				//$query = $this->getInsertQuery($this->data);
				$query = $this->getInsertQuery();
			} catch (Exception $e) {
				throw new DBError($e);
			}

			$retval = (int)$insert_id;
			$log_action = 'Add';
		} else {
			Debug::text(' Updating...' , __FILE__, __LINE__, __METHOD__,10);

			//Update
			$query = $this->getUpdateQuery(); //Don't pass data, too slow

			//Debug::Arr($this->data, 'Save(): Query: ', __FILE__, __LINE__, __METHOD__,10);
			$retval = TRUE;

			if ( $this->getDeleted() === TRUE ) {
				$log_action = 'Delete';
			} else {
				$log_action = 'Edit';
			}
		}

		//Debug::text('Save(): Query: '. $query , __FILE__, __LINE__, __METHOD__,10);
		//Debug::Arr($query, 'Save(): Query: ', __FILE__, __LINE__, __METHOD__,10);

		if ( $query != '' OR $query === TRUE ) {

			if ( is_string($query) AND $query != '' ) {
				try {
					$this->db->Execute($query);
				} catch (Exception $e) {
					//Comment this out to see some errors on MySQL.
					//throw new DBError($e);
				}
			}

			if ( method_exists($this,'addLog') ) {
				if ( $this->addLog( $log_action ) === FALSE ) {
					throw new GeneralError('Save(): Adding log entry failed.');
				}
			}

			//Run postSave function.
			if ( method_exists($this,'postSave') ) {
				Debug::text('Calling postSave()' , __FILE__, __LINE__, __METHOD__,10);
				if ( $this->postSave() === FALSE ) {
					throw new GeneralError('postSave() failed.');
				}
			}

			//Clear the data.
			if ($reset_data == TRUE) {
				$this->clearData();
			}
			//IF YOUR NOT RESETTING THE DATA, BE SURE TO CLEAR THE OBJECT MANUALLY
			//IF ITS IN A LOOP!! VERY IMPORTANT!

			$this->CommitTransaction();

			//Debug::Arr($retval, 'Save Retval: ', __FILE__, __LINE__, __METHOD__,10);

			return $retval;
		}

		Debug::text('Save(): returning FALSE! Very BAD!' , __FILE__, __LINE__, __METHOD__,10);

		throw new GeneralError('Save(): failed.');

		return FALSE; //This should return false here?
	}

	function Delete() {
		Debug::text('Delete: '. $this->getId(), __FILE__, __LINE__, __METHOD__, 9);

		if ( $this->getId() !== FALSE ) {
			$ph = array(
						'id' => $this->getId(),
						);

			$query = 'DELETE FROM '. $this->getTable() .' WHERE id = ?';

			try {
				$this->db->Execute($query, $ph);
			} catch (Exception $e) {
				throw new DBError($e);
			}

			return TRUE;
		}

		return FALSE;
	}

	function getIDSByListFactory( $lf ) {
		if ( !is_object($lf) ) {
			return FALSE;
		}

		foreach( $lf as $lf_obj ) {
			$retarr[] = $lf_obj->getID();
		}

		if ( isset($retarr) ) {
			return $retarr;
		}

		return FALSE;
	}

	function bulkDelete( $ids ) {
		//Debug::text('Delete: '. $this->getId(), __FILE__, __LINE__, __METHOD__, 9);

		//Make SURE you get the right table when calling this.
		if ( is_array($ids) AND count($ids) > 0 ) {
			$ph = array();

			$query = 'DELETE FROM '. $this->getTable() .' WHERE id in ('. $this->getListSQL( $ids, $ph ) .')';
			Debug::text('Bulk Delete Query: '. $query, __FILE__, __LINE__, __METHOD__, 9);

			try {
				$this->db->Execute($query, $ph);
			} catch (Exception $e) {
				throw new DBError($e);
			}

			return TRUE;
		}

		return FALSE;
	}

	function clearData() {
		$this->data = array();
		$this->tmp_data = array();
		$this->next_insert_id = NULL;

		return TRUE;
	}

	final function getIterator() {
		return new FactoryListIterator($this);
	}

	//Grabs the current object
	final function getCurrent() {
		return $this->getIterator()->current();
	}
}
?>
