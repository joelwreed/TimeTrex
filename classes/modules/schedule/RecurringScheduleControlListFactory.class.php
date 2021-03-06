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
 * $Id: RecurringScheduleControlListFactory.class.php 3021 2009-11-11 23:33:03Z ipso $
 * $Date: 2009-11-11 15:33:03 -0800 (Wed, 11 Nov 2009) $
 */

/**
 * @package Module_Schedule
 */
class RecurringScheduleControlListFactory extends RecurringScheduleControlFactory implements IteratorAggregate {

	function getAll($limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		$query = '
					select 	*
					from	'. $this->getTable() .'
					WHERE deleted = 0';
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
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByIdAndCompanyId($id, $company_id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		if ( $company_id == '') {
			return FALSE;
		}

		$ph = array(
					'company_id' => $company_id,
					'id' => $id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .' as a
					where	company_id = ?
						AND id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyId($id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'last_name' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$additional_sort_fields = array( 'name', 'description', 'last_name' );

		$rsuf = new RecurringScheduleUserFactory();
		$rstcf = new RecurringScheduleTemplateControlFactory();
		$uf = new UserFactory();

		$ph = array(
					'id' => $id,
					);

		$query = '
					select 	a.*,
							b.name as name,
							b.description as description,
							c.user_id as user_id,
							d.last_name as last_name
					from	'. $this->getTable() .' as a,
							'. $rstcf->getTable() .' as b,
							'. $rsuf->getTable() .' as c,
							'. $uf->getTable() .' as d
					where 	a.recurring_schedule_template_control_id = b.id
						AND a.id = c.recurring_schedule_control_id
						AND c.user_id = d.id
						AND a.company_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0 AND d.deleted=0 )
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict, $additional_sort_fields );

		//Debug::Text('Query: '. $query, __FILE__, __LINE__, __METHOD__,10);

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByUserIDAndStartDateAndEndDate($user_id, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			//$order = array( 'type_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$start_date_stamp = $this->db->BindDate( $start_date );
		$end_date_stamp = $this->db->BindDate( $end_date );

		$rsuf = new RecurringScheduleUserFactory();
		$rstcf = new RecurringScheduleTemplateControlFactory();

		$ph = array(
					'user_id' => $user_id,
					'start_date1' => $start_date_stamp,
					'end_date1' => $end_date_stamp,
					'start_date2' => $start_date_stamp,
					'start_date3' => $start_date_stamp,
					'end_date3' => $end_date_stamp,
					'start_date4' => $start_date_stamp,
					'end_date4' => $end_date_stamp,
					'start_date5' => $start_date_stamp,
					'end_date5' => $end_date_stamp,
					'start_date6' => $start_date_stamp,
					'end_date6' => $end_date_stamp,
					);
/*

					from	'. $this->getTable() .' as a,
							'. $rsuf->getTable() .' as b
					where 	a.id = b.recurring_schedule_control_id

*/
		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a
						LEFT JOIN '. $rstcf->getTable() .' as b ON a.recurring_schedule_template_control_id = b.id
						LEFT JOIN '. $rsuf->getTable() .' as c ON a.id = c.recurring_schedule_control_id
					WHERE c.user_id = ?
						AND
						(
							(a.start_date >= ? AND a.start_date <= ? AND a.end_date IS NULL )
							OR
							(a.start_date <= ? AND a.end_date IS NULL )
							OR
							(a.start_date >= ? AND a.end_date <= ? )
							OR
							(a.start_date >= ? AND a.start_date <= ? )
							OR
							(a.end_date >= ? AND a.end_date <= ? )
							OR
							(a.start_date <= ? AND a.end_date >= ? )
						)
						AND ( a.deleted = 0 AND b.deleted = 0 )
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyIdAndStartDateAndEndDate($company_id, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'a.company_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$start_date_stamp = $this->db->BindDate( $start_date );
		$end_date_stamp = $this->db->BindDate( $end_date );

		//$rsuf = new RecurringScheduleUserFactory();
		$rstcf = new RecurringScheduleTemplateControlFactory();

		$ph = array(
					'company_id' => $company_id,
					'start_date1' => $start_date_stamp,
					'end_date1' => $end_date_stamp,
					'start_date2' => $start_date_stamp,
					'start_date3' => $start_date_stamp,
					'end_date3' => $end_date_stamp,
					'start_date4' => $start_date_stamp,
					'end_date4' => $end_date_stamp,
					'start_date5' => $start_date_stamp,
					'end_date5' => $end_date_stamp,
					'start_date6' => $start_date_stamp,
					'end_date6' => $end_date_stamp,
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
						LEFT JOIN '. $rstcf->getTable() .' as b ON a.recurring_schedule_template_control_id = b.id
					where 	 a.company_id = ?
						AND
						(
							(a.start_date >= ? AND a.start_date <= ? AND a.end_date IS NULL )
							OR
							(a.start_date <= ? AND a.end_date IS NULL )
							OR
							(a.start_date >= ? AND a.end_date <= ? )
							OR
							(a.start_date >= ? AND a.start_date <= ? )
							OR
							(a.end_date >= ? AND a.end_date <= ? )
							OR
							(a.start_date <= ? AND a.end_date >= ? )
						)
						AND ( a.deleted = 0 AND b.deleted = 0 )
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

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
		Debug::Arr($order,'aOrder Data:', __FILE__, __LINE__, __METHOD__,10);

		$additional_order_fields = array('name', 'description', 'last_name', 'template_id');
		if ( $order == NULL ) {
			$order = array( 'last_name' => 'asc', 'd.id' => 'asc', 'a.start_date' => 'desc' );
			$strict = FALSE;
		} else {
			//Always try to order by status first so UNPAID employees go to the bottom.

			if ( isset($order['last_name']) ) {
				$order['d.last_name'] = $order['last_name'];
				unset($order['last_name']);
			}
			if ( isset($order['first_name']) ) {
				$order['d.first_name'] = $order['first_name'];
				unset($order['first_name']);
			}
			if ( isset($order['template_id']) ) {
				$order['b.id'] = $order['template_id'];
				unset($order['template_id']);
			}

			/*
			if ( isset($order['status']) ) {
				$order['status_id'] = $order['status'];
				unset($order['status']);
			}

			if ( isset($order['transaction_date']) ) {
				$order['last_name'] = 'asc';
			} else {
				$order['transaction_date'] = 'desc';
			}
			*/

			$strict = TRUE;
		}
		Debug::Arr($order,'bOrder Data:', __FILE__, __LINE__, __METHOD__,10);
		Debug::Arr($filter_data,'Filter Data:', __FILE__, __LINE__, __METHOD__,10);

		$uf = new UserFactory();
		$rsuf = new RecurringScheduleUserFactory();
		$rstcf = new RecurringScheduleTemplateControlFactory();

		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select 	a.*,
							b.name as name,
							b.description as description,
							c.user_id as user_id,
							d.last_name as last_name
					from 	'. $this->getTable() .' as a
						LEFT JOIN '. $rstcf->getTable() .' as b ON a.recurring_schedule_template_control_id = b.id
						LEFT JOIN '. $rsuf->getTable() .' as c ON a.id = c.recurring_schedule_control_id
						LEFT JOIN '. $uf->getTable() .' as d ON c.user_id = d.id
					where	a.company_id = ?
					';

		if ( isset($filter_data['id']) AND isset($filter_data['id'][0]) AND !in_array(-1, (array)$filter_data['id']) ) {
			$query  .=	' AND a.id in ('. $this->getListSQL($filter_data['id'], $ph) .') ';
		}
		if ( isset($filter_data['permission_children_ids']) AND isset($filter_data['permission_children_ids'][0]) AND !in_array(-1, (array)$filter_data['permission_children_ids']) ) {
			$query  .=	' AND d.id in ('. $this->getListSQL($filter_data['permission_children_ids'], $ph) .') ';
		}
		if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
			$query  .=	' AND d.id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
		}
		if ( isset($filter_data['template_id']) AND isset($filter_data['template_id'][0]) AND !in_array(-1, (array)$filter_data['template_id']) ) {
			$query  .=	' AND b.id in ('. $this->getListSQL($filter_data['template_id'], $ph) .') ';
		}

		if ( isset($filter_data['status_id']) AND isset($filter_data['status_id'][0]) AND !in_array(-1, (array)$filter_data['status_id']) ) {
			$query  .=	' AND d.status_id in ('. $this->getListSQL($filter_data['status_id'], $ph) .') ';
		}
		if ( isset($filter_data['group_id']) AND isset($filter_data['group_id'][0]) AND !in_array(-1, (array)$filter_data['group_id']) ) {
			if ( isset($filter_data['include_subgroups']) AND (bool)$filter_data['include_subgroups'] == TRUE ) {
				$uglf = new UserGroupListFactory();
				$filter_data['group_id'] = $uglf->getByCompanyIdAndGroupIdAndSubGroupsArray( $company_id, $filter_data['group_id'], TRUE);
			}
			$query  .=	' AND d.group_id in ('. $this->getListSQL($filter_data['group_id'], $ph) .') ';
		}
		if ( isset($filter_data['default_branch_id']) AND isset($filter_data['default_branch_id'][0]) AND !in_array(-1, (array)$filter_data['default_branch_id']) ) {
			$query  .=	' AND d.default_branch_id in ('. $this->getListSQL($filter_data['default_branch_id'], $ph) .') ';
		}
		if ( isset($filter_data['default_department_id']) AND isset($filter_data['default_department_id'][0]) AND !in_array(-1, (array)$filter_data['default_department_id']) ) {
			$query  .=	' AND d.default_department_id in ('. $this->getListSQL($filter_data['default_department_id'], $ph) .') ';
		}

		if ( isset($filter_data['title_id']) AND isset($filter_data['title_id'][0]) AND !in_array(-1, (array)$filter_data['title_id']) ) {
			$query  .=	' AND d.title_id in ('. $this->getListSQL($filter_data['title_id'], $ph) .') ';
		}


		if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
			$ph[] = $this->db->BindDate($filter_data['start_date']);
			$query  .=	' AND a.start_date >= ?';
		}
		if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
			$ph[] = $this->db->BindDate($filter_data['end_date']);
			$query  .=	' AND a.start_date <= ?';
		}

		$query .= 	'
						AND (a.deleted = 0 AND b.deleted = 0 AND d.deleted=0)
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict, $additional_order_fields );

		Debug::Text('Query: '. $query, __FILE__, __LINE__, __METHOD__,10);

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

}
?>
