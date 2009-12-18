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
 * $Revision: 2876 $
 * $Id: HolidayListFactory.class.php 2876 2009-10-07 20:27:23Z ipso $
 * $Date: 2009-10-07 13:27:23 -0700 (Wed, 07 Oct 2009) $
 */

/**
 * @package Module_Holiday
 */
class HolidayListFactory extends HolidayFactory implements IteratorAggregate {

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

	function getByIdAndHolidayPolicyID($id, $holiday_policy_id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		$ph = array(
					'id' => $id,
					'holiday_policy_id' => $holiday_policy_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	id = ?
						AND holiday_policy_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByHolidayPolicyId($id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'date_stamp' => 'desc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$ph = array();

		$query = '
					select 	*
					from	'. $this->getTable() .' as a
					where	holiday_policy_id in ('. $this->getListSQL($id, $ph) .')
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);
	}

	function getByCompanyIdAndHolidayPolicyId($company_id, $id, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( $id == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'date_stamp' => 'desc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$hpf = new HolidayPolicyFactory();

		$ph = array( 'company_id' => $company_id );

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
						LEFT JOIN '. $hpf->getTable() .' as b ON a.holiday_policy_id = b.id
					where	b.company_id = ?
						AND a.holiday_policy_id in ('. $this->getListSQL($id, $ph) .')
						AND ( a.deleted = 0 AND b.deleted = 0) ';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);
	}

	function getByPolicyGroupUserId($user_id, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			//$order = array( 'c.type_id' => 'asc', 'c.trigger_time' => 'desc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pgf = new PolicyGroupFactory();
		$pguf = new PolicyGroupUserFactory();
		$hpf = new HolidayPolicyFactory();

		$ph = array(
					'user_id' => $user_id,
					);

		$query = '
					select 	d.*
					from 	'. $pguf->getTable() .' as a,
							'. $pgf->getTable() .' as b,
							'. $hpf->getTable() .' as c,
							'. $this->getTable() .' as d
					where 	a.policy_group_id = b.id
						AND b.holiday_policy_id = c.id
						AND c.id = d.holiday_policy_id
						AND a.user_id = ?
						AND ( c.deleted = 0 AND d.deleted = 0 AND b.deleted = 0)
						';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByPolicyGroupUserIdAndDate($user_id, $date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $date == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			//$order = array( 'c.type_id' => 'asc', 'c.trigger_time' => 'desc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pgf = new PolicyGroupFactory();
		$pguf = new PolicyGroupUserFactory();
		$hpf = new HolidayPolicyFactory();
		$cgmf = new CompanyGenericMapFactory();


		$ph = array(
					'user_id' => $user_id,
					'date' => $this->db->BindDate( $date ),
					);

		$query = '
					select 	d.*
					from 	'. $pguf->getTable() .' as a,
							'. $pgf->getTable() .' as b,
							'. $hpf->getTable() .' as c,
							'. $cgmf->getTable() .' as z,
							'. $this->getTable() .' as d
					where 	a.policy_group_id = b.id
						AND ( b.id = z.object_id AND z.company_id = b.company_id AND z.object_type_id = 180)
						AND z.map_id = d.holiday_policy_id
						AND d.holiday_policy_id = c.id
						AND a.user_id = ?
						AND d.date_stamp = ?
						AND ( c.deleted = 0 AND d.deleted = 0 AND b.deleted = 0 )
						';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByPolicyGroupUserIdAndStartDateAndEndDate($user_id, $start_date, $end_date, $where = NULL, $order = NULL) {
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
			$order = array( 'd.date_stamp' => 'desc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pgf = new PolicyGroupFactory();
		$pguf = new PolicyGroupUserFactory();
		$hpf = new HolidayPolicyFactory();
		$cgmf = new CompanyGenericMapFactory();


		$ph = array(
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		//Query was: distinct(d.*) but MySQL doesnt like that.
		$query = '
					select 	distinct d.*
					from 	'. $pguf->getTable() .' as a,
							'. $pgf->getTable() .' as b,
							'. $hpf->getTable() .' as c,
							'. $cgmf->getTable() .' as z,
							'. $this->getTable() .' as d
					where 	a.policy_group_id = b.id
						AND ( b.id = z.object_id AND z.company_id = b.company_id AND z.object_type_id = 180)
						AND z.map_id = d.holiday_policy_id
						AND d.holiday_policy_id = c.id
						AND d.date_stamp >= ?
						AND d.date_stamp <= ?
						AND a.user_id in ('. $this->getListSQL($user_id, $ph) .')
						AND ( c.deleted = 0 AND d.deleted=0 )
						';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyIdAndStartDateAndEndDate($company_id, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'd.date_stamp' => 'desc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pgf = new PolicyGroupFactory();
		$pguf = new PolicyGroupUserFactory();
		$hpf = new HolidayPolicyFactory();
		$cgmf = new CompanyGenericMapFactory();


		$ph = array(
					'company_id' => $company_id,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		//Query was: distinct(d.*) but MySQL doesnt like that.
		$query = '
					select 	distinct d.*
					from 	'. $pguf->getTable() .' as a,
							'. $pgf->getTable() .' as b,
							'. $hpf->getTable() .' as c,
							'. $cgmf->getTable() .' as z,
							'. $this->getTable() .' as d
					where 	a.policy_group_id = b.id
						AND ( b.id = z.object_id AND z.company_id = b.company_id AND z.object_type_id = 180)
						AND z.map_id = d.holiday_policy_id
						AND d.holiday_policy_id = c.id
						AND b.company_id = ?
						AND d.date_stamp >= ?
						AND d.date_stamp <= ?
						AND ( c.deleted = 0 AND d.deleted=0 )
						';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getArrayByPolicyGroupUserId($user_id, $start_date, $end_date) {
		$hlf = new HolidayListFactory();
		$hlf->getByPolicyGroupUserIdAndStartDateAndEndDate( $user_id, $start_date, $end_date);

		if ( $hlf->getRecordCount() > 0 ) {
			foreach($hlf as $h_obj) {
				$list[$h_obj->getDateStamp()] = $h_obj->getName();
			}

			return $list;
		}

		return FALSE;
	}

/*
	function getByCompanyIdArray($company_id, $include_blank = TRUE) {

		$aplf = new AbsencePolicyListFactory();
		$aplf->getByCompanyId($company_id);

		if ( $include_blank == TRUE ) {
			$list[0] = '--';
		}

		foreach ($aplf as $ap_obj) {
			$list[$ap_obj->getID()] = $ap_obj->getName();
		}

		if ( isset($list) ) {
			return $list;
		}

		return FALSE;
	}
*/
}
?>
