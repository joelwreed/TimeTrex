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
 * $Revision: 2095 $
 * $Id: LogListFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Core
 */
class LogListFactory extends LogFactory implements IteratorAggregate {

	function getAll($limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		$query = '
					select 	*
					from	'. $this->getTable() .'
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		if ($limit == NULL) {
			//Run query without limit
			$this->rs = $this->db->SelectLimit($query);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page);
		}

		return $this;
	}

	function getById($id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		$ph = array(
					'id' => $id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	id = ?
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getLastEntryByUserIdAndActionAndTable($user_id, $action, $table_name) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $action == '') {
			return FALSE;
		}

		if ( $table_name == '') {
			return FALSE;
		}

		$action_key = Option::getByValue($action, $this->getOptions('action') );
		if ($action_key !== FALSE) {
			$action = $action_key;
		}

		$ph = array(
					'user_id' => $user_id,
					'table_name' => $table_name,
					'action_id' => $action,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_id = ?
						AND table_name = ?
						AND action_id = ?
					ORDER BY date desc
					LIMIT 1
					';
		//$query .= $this->getWhereSQL( $where );
		//$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getSearchByCompanyIdAndArrayCriteria( $company_id, $filter_data, $limit = NULL, $page = NULL, $where = NULL, $order = NULL ) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( !is_array($order) ) {
			//Use Filter Data ordering if its set.
			if ( isset($filter_data['sort_column']) AND $filter_data['sort_order']) {
				$order = array(Misc::trimSortPrefix($filter_data['sort_column']) => $filter_data['sort_order']);
			}
		}

		$additional_order_fields = array('b.last_name');
		if ( $order == NULL ) {
			$order = array( 'date' => 'desc');
			$strict = FALSE;
		} else {
			//Do order by column conversions, because if we include these columns in the SQL
			//query, they contaminate the data array.
			$strict = TRUE;
		}
		//Debug::Arr($order,'Order Data:', __FILE__, __LINE__, __METHOD__,10);
		//Debug::Arr($filter_data,'Filter Data:', __FILE__, __LINE__, __METHOD__,10);


		if ( isset($filter_data['user_ids']) ) {
			$filter_data['user_id'] = $filter_data['user_ids'];
		}
		if ( isset($filter_data['log_action_ids']) ) {
			$filter_data['log_action_id'] = $filter_data['log_action_ids'];
		}
		if ( isset($filter_data['log_table_name_ids']) ) {
			$filter_data['log_table_name_id'] = $filter_data['log_table_name_ids'];
		}

		$uf = new UserFactory();

		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a
						LEFT JOIN '. $uf->getTable() .' as b ON a.user_id = b.id
					where	b.company_id = ?
					';

		if ( isset($filter_data['permission_children_ids']) AND isset($filter_data['permission_children_ids'][0]) AND !in_array(-1, (array)$filter_data['permission_children_ids']) ) {
			$query  .=	' AND a.user_id in ('. $this->getListSQL($filter_data['permission_children_ids'], $ph) .') ';
		}
		if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
			$query  .=	' AND a.user_id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
		}
		if ( isset($filter_data['log_action_id']) AND isset($filter_data['log_action_id'][0]) AND !in_array(-1, (array)$filter_data['log_action_id']) ) {
			$query  .=	' AND a.action_id in ('. $this->getListSQL($filter_data['log_action_id'], $ph) .') ';
		}
		if ( isset($filter_data['log_table_name_id']) AND isset($filter_data['log_table_name_id'][0]) AND !in_array(-1, (array)$filter_data['log_table_name_id']) ) {
			$query  .=	' AND a.table_name in ('. $this->getListSQL($filter_data['log_table_name_id'], $ph) .') ';
		}
		if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
			$ph[] = $filter_data['start_date'];
			$query  .=	' AND a.date >= ?';
		}
		if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
			$ph[] = $filter_data['end_date'];
			$query  .=	' AND a.date <= ?';
		}

		$query .= 	'
						AND ( b.deleted = 0 )
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict, $additional_order_fields );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

/*
	function getByCompanyId($id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		$query = '
					select 	*,
							CASE WHEN ( updated_date is NULL) THEN TRUE ELSE FALSE END as updated_date_null
					from	'. $this->getTable() .'
					where	company_id = '. $id .'
						AND deleted = 0
					';
		$query .= $this->getWhereSQL( $where );
		//Because of the null updated date, we have to manually sort.
		if ( $order == NULL ) {
			//$order = array( 'type_id' => 'asc', 'status_id' => 'asc', 'updated_date_null' => 'asc', 'updated_date' => 'desc', 'created_date' => 'desc' );
			$query .= 'ORDER BY type_id asc, status_id asc, updated_date_null asc, updated_date desc, created_date desc';
		} else {
			$query .= $this->getSortSQL( $order );
		}

		if ($limit == NULL) {
			//Run query without limit
			$this->rs = $this->db->SelectLimit($query);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page);
		}

		return $this;
	}

	function getByIdAndCompanyId($id, $company_id, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		if ( $company_id == '') {
			return FALSE;
		}

		$query = '
					select 	*
					from 	'. $this->getTable() .'
					where	company_id = '. $company_id .'
						AND	id = '. $id .'
						AND deleted = 0';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query);

		return $this;
	}
*/
}
?>
