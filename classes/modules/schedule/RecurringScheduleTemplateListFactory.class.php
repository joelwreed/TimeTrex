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
 * $Revision: 2681 $
 * $Id: RecurringScheduleTemplateListFactory.class.php 2681 2009-07-23 22:30:39Z ipso $
 * $Date: 2009-07-23 15:30:39 -0700 (Thu, 23 Jul 2009) $
 */

/**
 * @package Module_Schedule
 */
class RecurringScheduleTemplateListFactory extends RecurringScheduleTemplateFactory implements IteratorAggregate {

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

	function getByRecurringScheduleTemplateControlId($id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		$ph = array(
					'id' => $id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	recurring_schedule_template_control_id = ?
						AND deleted = 0
					ORDER BY week asc';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

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


		$additional_order_fields = array('name', 'description', 'last_name');
		if ( $order == NULL ) {
			$order = array( 'c.start_date' => 'asc', 'd.user_id' => 'asc', 'a.week' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		if ( isset($filter_data['exclude_user_ids']) ) {
			$filter_data['exclude_id'] = $filter_data['exclude_user_ids'];
		}
		if ( isset($filter_data['include_user_ids']) ) {
			$filter_data['id'] = $filter_data['include_user_ids'];
		}
		if ( isset($filter_data['user_status_ids']) ) {
			$filter_data['status_id'] = $filter_data['user_status_ids'];
		}
		if ( isset($filter_data['user_title_ids']) ) {
			$filter_data['title_id'] = $filter_data['user_title_ids'];
		}
		if ( isset($filter_data['group_ids']) ) {
			$filter_data['group_id'] = $filter_data['group_ids'];
		}
		if ( isset($filter_data['default_branch_ids']) ) {
			$filter_data['default_branch_id'] = $filter_data['default_branch_ids'];
		}
		if ( isset($filter_data['default_department_ids']) ) {
			$filter_data['default_department_id'] = $filter_data['default_department_ids'];
		}
		if ( isset($filter_data['schedule_branch_ids']) ) {
			$filter_data['schedule_branch_id'] = $filter_data['schedule_branch_ids'];
		}
		if ( isset($filter_data['schedule_department_ids']) ) {
			$filter_data['schedule_department_id'] = $filter_data['schedule_department_ids'];
		}

		if ( isset($filter_data['exclude_job_ids']) ) {
			$filter_data['exclude_id'] = $filter_data['exclude_job_ids'];
		}
		if ( isset($filter_data['include_job_ids']) ) {
			$filter_data['include_job_id'] = $filter_data['include_job_ids'];
		}
		if ( isset($filter_data['job_group_ids']) ) {
			$filter_data['job_group_id'] = $filter_data['job_group_ids'];
		}
		if ( isset($filter_data['job_item_ids']) ) {
			$filter_data['job_item_id'] = $filter_data['job_item_ids'];
		}

		Debug::Arr($order,'bOrder Data:', __FILE__, __LINE__, __METHOD__,10);
		Debug::Arr($filter_data,'Filter Data:', __FILE__, __LINE__, __METHOD__,10);

		$uf = new UserFactory();
		$rscf = new RecurringScheduleControlFactory();
		$rsuf = new RecurringScheduleUserFactory();
		$rstcf = new RecurringScheduleTemplateControlFactory();

		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select 	a.*,
							d.user_id as user_id,
							e.first_name as first_name,
							e.last_name as last_name,
							e.created_by as user_created_by,
							CASE WHEN a.branch_id = -1 THEN e.default_branch_id ELSE a.branch_id END as schedule_branch_id,
							CASE WHEN a.department_id = -1 THEN e.default_department_id ELSE a.department_id END as schedule_department_id,
							c.start_date as recurring_schedule_control_start_date,
							c.end_date as recurring_schedule_control_end_date,
							c.start_week as recurring_schedule_control_start_week,
							y.max_week as max_week,
							( (((a.week-1)+y.max_week-(c.start_week-1))%y.max_week) + 1) as remapped_week,
							e.hire_date as hire_date,
							e.termination_date as termination_date
					from 	'. $this->getTable() .' as a
						LEFT JOIN ( select z.recurring_schedule_template_control_id, max(z.week) as max_week from recurring_schedule_template as z where deleted = 0 group by z.recurring_schedule_template_control_id ) as y ON a.recurring_schedule_template_control_id = y.recurring_schedule_template_control_id
						LEFT JOIN '. $rstcf->getTable() .' as b ON a.recurring_schedule_template_control_id = b.id
						LEFT JOIN '. $rscf->getTable() .' as c ON a.recurring_schedule_template_control_id = c.recurring_schedule_template_control_id
						LEFT JOIN '. $rsuf->getTable() .' as d ON c.id = d.recurring_schedule_control_id
						LEFT JOIN '. $uf->getTable() .' as e ON d.user_id = e.id
					where	b.company_id = ?
					';

		if ( isset($filter_data['id']) AND isset($filter_data['id'][0]) AND !in_array(-1, (array)$filter_data['id']) ) {
			$query  .=	' AND e.id in ('. $this->getListSQL($filter_data['id'], $ph) .') ';
		}
		if ( isset($filter_data['permission_children_ids']) AND isset($filter_data['permission_children_ids'][0]) AND !in_array(-1, (array)$filter_data['permission_children_ids']) ) {
			$query  .=	' AND e.id in ('. $this->getListSQL($filter_data['permission_children_ids'], $ph) .') ';
		}
		if ( isset($filter_data['status_id']) AND isset($filter_data['status_id'][0]) AND !in_array(-1, (array)$filter_data['status_id']) ) {
			$query  .=	' AND e.status_id in ('. $this->getListSQL($filter_data['status_id'], $ph) .') ';
		}
		if ( isset($filter_data['group_id']) AND isset($filter_data['group_id'][0]) AND !in_array(-1, (array)$filter_data['group_id']) ) {
			if ( isset($filter_data['include_subgroups']) AND (bool)$filter_data['include_subgroups'] == TRUE ) {
				$uglf = new UserGroupListFactory();
				$filter_data['group_id'] = $uglf->getByCompanyIdAndGroupIdAndSubGroupsArray( $company_id, $filter_data['group_id'], TRUE);
			}
			$query  .=	' AND e.group_id in ('. $this->getListSQL($filter_data['group_id'], $ph) .') ';
		}
		if ( isset($filter_data['default_branch_id']) AND isset($filter_data['default_branch_id'][0]) AND !in_array(-1, (array)$filter_data['default_branch_id']) ) {
			$query  .=	' AND e.default_branch_id in ('. $this->getListSQL($filter_data['default_branch_id'], $ph) .') ';
		}
		if ( isset($filter_data['default_department_id']) AND isset($filter_data['default_department_id'][0]) AND !in_array(-1, (array)$filter_data['default_department_id']) ) {
			$query  .=	' AND e.default_department_id in ('. $this->getListSQL($filter_data['default_department_id'], $ph) .') ';
		}

		if ( isset($filter_data['schedule_branch_id']) AND isset($filter_data['schedule_branch_id'][0]) AND !in_array(-1, (array)$filter_data['schedule_branch_id']) ) {
			$query  .=	' AND ( a.branch_id in ('. $this->getListSQL($filter_data['schedule_branch_id'], $ph) .') OR ( a.branch_id = -1 AND e.default_branch_id in ('. $this->getListSQL($filter_data['schedule_branch_id'], $ph) .') ) )';
		}
		if ( isset($filter_data['schedule_department_id']) AND isset($filter_data['schedule_department_id'][0]) AND !in_array(-1, (array)$filter_data['schedule_department_id']) ) {
			$query  .=	' AND ( a.department_id in ('. $this->getListSQL($filter_data['schedule_department_id'], $ph) .') OR ( a.department_id = -1 AND e.default_department_id in ('. $this->getListSQL($filter_data['schedule_department_id'], $ph) .') ) )';
		}

		if ( isset($filter_data['title_id']) AND isset($filter_data['title_id'][0]) AND !in_array(-1, (array)$filter_data['title_id']) ) {
			$query  .=	' AND e.title_id in ('. $this->getListSQL($filter_data['title_id'], $ph) .') ';
		}

		if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != ''
				AND isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '') {
			$start_date_stamp = $this->db->BindDate( $filter_data['start_date'] );
			$end_date_stamp = $this->db->BindDate( $filter_data['end_date'] );

			$ph[] = $start_date_stamp;
			$ph[] = $end_date_stamp;
			$ph[] = $start_date_stamp;
			$ph[] = $start_date_stamp;
			$ph[] = $end_date_stamp;
			$ph[] = $start_date_stamp;
			$ph[] = $end_date_stamp;
			$ph[] = $start_date_stamp;
			$ph[] = $end_date_stamp;
			$ph[] = $start_date_stamp;
			$ph[] = $end_date_stamp;
			$ph[] = $start_date_stamp;
			$ph[] = $end_date_stamp;

			$ph[] = $filter_data['end_date'] ;
			$ph[] = $filter_data['start_date'];

			$query  .=	' AND (
								(c.start_date >= ? AND c.start_date <= ? AND c.end_date IS NULL )
								OR
								(c.start_date <= ? AND c.end_date IS NULL )
								OR
								(c.start_date <= ? AND c.end_date >= ? )
								OR
								(c.start_date >= ? AND c.end_date <= ? )
								OR
								(c.start_date >= ? AND c.start_date <= ? )
								OR
								(c.end_date >= ? AND c.end_date <= ? )
								OR
								(c.start_date <= ? AND c.end_date >= ? )
							)
							AND
							(
								( e.hire_date is NULL OR e.hire_date <= ? )
								AND
								( e.termination_date is NULL OR e.termination_date >= ? )
							)
						';
		}

		$query .= 	'
						AND ( a.deleted = 0 AND b.deleted = 0 AND c.deleted = 0 AND e.deleted = 0 )
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
