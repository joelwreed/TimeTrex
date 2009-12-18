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
 * $Id: UserDateTotalListFactory.class.php 2858 2009-09-29 18:12:05Z ipso $
 * $Date: 2009-09-29 11:12:05 -0700 (Tue, 29 Sep 2009) $
 */

/**
 * @package Core
 */
class UserDateTotalListFactory extends UserDateTotalFactory implements IteratorAggregate {

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

	function getByCompanyId($id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		$ph = array(
					'id' => $id,
					);

		$udf = new UserDateFactory();
		$uf = new UserFactory();

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b,
							'. $uf->getTable() .' as c
					where 	a.user_date_id = b.id
						AND b.user_id = c.id
						AND c.company_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0 )
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByUserDateIdAndStatusAndOverride($user_date_id, $status, $override = FALSE) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		/*
		$key = Option::getByValue($status, $this->getOptions('status') );
		if ($key !== FALSE) {
			$status = $key;
		}
		*/

		$ph = array(
					'user_date_id' => $user_date_id,
					'override' => $this->toBool( $override ),
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_date_id = ?
						AND override = ?
						AND status_id in ('. $this->getListSQL($status, $ph) .')
						AND deleted = 0
					';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatusAndOverrideAndMisMatchPunchControlUserDateId($user_date_id, $status, $override = FALSE) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		$pcf = new PunchControlFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'override' => $this->toBool( $override ),
					);

		//Don't check for JUST b.deleted = 0 because of the LEFT JOIN, it might be NULL too.
		//There is a bug where sometimes a user_date_total row is orphaned with no punch_control rows that aren't deleted
		//So make sure this query includes those orphaned rows so they can be deleted.
		//The current fix is to include "OR b.deleted = 1"
		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $pcf->getTable() .' as b ON a.punch_control_id = b.id
					where	a.user_date_id = ?
						AND
							(
								( a.override = ? AND a.status_id in ('. $this->getListSQL($status, $ph) .') )
								OR
								( b.id IS NOT NULL AND a.user_date_id != b.user_date_id)
							)
						AND ( a.deleted = 0 )
					';
//						AND ( a.deleted = 0 AND ( b.deleted IS NULL OR b.deleted = 0 )  )

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateId($user_date_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_date_id = ?
						AND deleted = 0
					';

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByUserDateIdAndType($user_date_id, $type) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_date_id = ?
						AND type_id in ('. $this->getListSQL($type, $ph) .')
						AND deleted = 0
					';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatus($user_date_id, $status, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'a.status_id' => 'desc', 'c.time_stamp' => 'asc', 'a.start_time_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		//Want to be able to see overridden or, just time added on its own?
		//LEFT JOIN '. $pf->getTable() .' as c ON a.punch_control_id = c.punch_control_id AND c.status_id = 10
		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $pcf->getTable() .' as b ON a.punch_control_id = b.id
					LEFT JOIN '. $pf->getTable() .' as c ON a.punch_control_id = c.punch_control_id AND ( c.status_id = 10 OR c.status_id IS NULL )
					where	a.user_date_id = ?
						AND a.status_id in ('. $this->getListSQL($status, $ph) .')
						AND ( a.deleted = 0
								AND ( b.deleted=0 OR b.deleted IS NULL )
								AND ( c.deleted=0 OR c.deleted IS NULL ) )
					';
		$query .= $this->getSortSQL( $order, $strict );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByUserDateIdAndStatusAndType($user_date_id, $status, $type, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'a.status_id' => 'desc', 'a.type_id' => 'asc', 'c.time_stamp' => 'asc', 'a.start_time_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		//Want to be able to see overridden or, just time added on its own?
		//LEFT JOIN '. $pf->getTable() .' as c ON a.punch_control_id = c.punch_control_id AND c.status_id = 10
		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $pcf->getTable() .' as b ON a.punch_control_id = b.id
					LEFT JOIN '. $pf->getTable() .' as c ON a.punch_control_id = c.punch_control_id AND ( c.status_id = 10 OR c.status_id IS NULL )
					where	a.user_date_id = ?
						AND a.status_id in ('. $this->getListSQL($status, $ph) .')
						AND a.type_id in ('. $this->getListSQL($type, $ph) .')
						AND ( a.deleted = 0
								AND ( b.deleted=0 OR b.deleted IS NULL )
								AND ( c.deleted=0 OR c.deleted IS NULL ) )
					';
		$query .= $this->getSortSQL( $order, $strict );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByUserDateIdAndStatusAndTypeAndPunchControlIdAndOverride($user_date_id, $status, $type, $punch_control_id, $override = FALSE, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		if ( $punch_control_id == FALSE ) {
			$punch_control_id = NULL;
		}

		if ( $order == NULL ) {
			//$order = array( 'c.time_stamp' => 'asc', 'a.start_time_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status' => $status,
					'type' => $type,
					'punch_control_id' => (int)$punch_control_id,
					'override' => $this->toBool( $override ),
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					where	a.user_date_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND a.punch_control_id = ?
						AND a.override = ?
						AND a.deleted = 0
					';
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatusAndTypeAndOverride($user_date_id, $status, $type, $override = FALSE, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		if ( $order == NULL ) {
			//$order = array( 'c.time_stamp' => 'asc', 'a.start_time_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status' => $status,
					'type' => $type,
					'override' => $this->toBool( $override ),
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					where	a.user_date_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND a.override = ?
						AND a.deleted = 0
					';
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatusAndTypeAndOverTimePolicyIdAndOverride($user_date_id, $status, $type, $over_time_policy_id, $override = FALSE, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		if ( $order == NULL ) {
			//$order = array( 'c.time_stamp' => 'asc', 'a.start_time_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status' => $status,
					'type' => $type,
					'over_time_policy_id' => $over_time_policy_id,
					'override' => $this->toBool( $override ),
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					where	a.user_date_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND a.over_time_policy_id = ?
						AND a.override = ?
						AND a.deleted = 0
					';
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatusAndTypeAndPremiumPolicyIdAndOverride($user_date_id, $status, $type, $premium_policy_id, $override = FALSE, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		if ( $order == NULL ) {
			//$order = array( 'c.time_stamp' => 'asc', 'a.start_time_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status' => $status,
					'type' => $type,
					'premium_policy_id' => $premium_policy_id,
					'override' => $this->toBool( $override ),
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					where	a.user_date_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND a.premium_policy_id = ?
						AND a.override = ?
						AND a.deleted = 0
					';
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatusAndTypeAndMealPolicyIdAndOverride($user_date_id, $status, $type, $meal_policy_id, $override = FALSE, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		if ( $order == NULL ) {
			//$order = array( 'c.time_stamp' => 'asc', 'a.start_time_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status' => $status,
					'type' => $type,
					'meal_policy_id' => $meal_policy_id,
					'override' => $this->toBool( $override ),
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					where	a.user_date_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND a.meal_policy_id = ?
						AND a.override = ?
						AND a.deleted = 0
					';
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatusAndTypeAndAbsencePolicyIdAndOverride($user_date_id, $status, $type, $absence_policy_id, $override = FALSE, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		if ( $absence_policy_id == FALSE ) {
			$absence_policy_id = NULL;
		}

		if ( $order == NULL ) {
			//$order = array( 'c.time_stamp' => 'asc', 'a.start_time_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status' => $status,
					'type' => $type,
					'absence_policy_id' => $absence_policy_id,
					'override' => $this->toBool( $override ),
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					where	a.user_date_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND a.absence_policy_id = ?
						AND a.override = ?
						AND a.deleted = 0
					';
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByPunchControlId($punch_control_id) {
		if ( $punch_control_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'punch_control_id' => $punch_control_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where punch_control_id = ?
						AND deleted = 0
					';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndPunchControlId($user_date_id, $punch_control_id) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $punch_control_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'user_date_id' => $user_date_id,
					'punch_control_id' => $punch_control_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_date_id = ?
						AND punch_control_id = ?
						AND deleted = 0
					';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getTotalSumByUserDateID( $user_date_id ) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		$apf = new AbsencePolicyFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		//Don't include total time row
		//Include paid absences
		$query = '
					select 	sum(a.total_time)
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $pcf->getTable() .' as b ON a.punch_control_id = b.id
					LEFT JOIN '. $apf->getTable() .' as c ON a.absence_policy_id = c.id
					where 	a.user_date_id = ?
						AND ( a.status_id in (20,30) OR ( a.status_id = 10 AND a.type_id in ( 100, 110 ) ) )
						AND ( c.type_id IS NULL OR c.type_id in ( 10, 12 ) )
						AND ( a.deleted = 0 AND (b.deleted=0 OR b.deleted is NULL) )
				';
		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getWorkedTimeSumByUserDateID( $user_date_id ) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		//Don't include total time row, OR paid absences
		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .'
					where	user_date_id = ?
						AND status_id = 20
						AND deleted = 0
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getOverTimeSumByUserDateID( $user_date_id ) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		//Don't include total time row
		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .'
					where	user_date_id = ?
						AND type_id = 30
						AND deleted = 0
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getPaidAbsenceSumByUserDateID( $user_date_id ) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		$apf = new AbsencePolicyFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		//Include only paid absences.
		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a,
							'. $apf->getTable() .' as b
					where 	a.absence_policy_id = b.id
						AND b.type_id in ( 10, 12 )
						AND a.user_date_id = ?
						AND a.status_id = 30
						AND a.deleted = 0
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getMealPolicySumByUserDateID( $user_date_id ) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a
					where
						a.user_date_id = ?
						AND a.status_id = 40
						AND a.deleted = 0
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getPremiumPolicySumByUserDateIDAndPremiumPolicyID( $user_date_id, $premium_policy_id ) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		if ( $premium_policy_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'user_date_id' => $user_date_id,
					'premium_policy_id' => $premium_policy_id,
					);

		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a
					where
						a.user_date_id = ?
						AND a.premium_policy_id = ?
						AND a.status_id = 10
						AND a.type_id = 40
						AND a.deleted = 0
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getWeekRegularTimeSumByUserIDAndEpochAndStartWeekEpoch( $user_id, $epoch, $week_start_epoch ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $epoch == '' ) {
			return FALSE;
		}

		if ( $week_start_epoch == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$otpf = new OverTimePolicyFactory();

		$ph = array(
					'user_id' => $user_id,
					'week_start_epoch' => $this->db->BindDate( $week_start_epoch ),
					'epoch' =>  $this->db->BindDate( $epoch ),
					);

		//DO NOT Include paid absences. Only count regular time towards weekly overtime.
		//And other weekly overtime polices!
		$query = '
					select 	sum(a.total_time)
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id
					LEFT JOIN '. $otpf->getTable() .' as c ON a.over_time_policy_id = c.id
					where
						b.user_id = ?
						AND b.date_stamp >= ?
						AND b.date_stamp < ?
						AND a.status_id = 10
						AND (
							a.type_id = 20
							OR ( a.type_id = 30 AND c.type_id = 20 )
							)
						AND a.absence_policy_id = 0
						AND a.deleted = 0
				';
		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getPaidAbsenceByUserDateID( $user_date_id ) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		$apf = new AbsencePolicyFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		//Include only paid absences.
		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $apf->getTable() .' as b
					where 	a.absence_policy_id = b.id
						AND b.type_id in ( 10, 12 )
						AND a.user_date_id = ?
						AND a.status_id = 30
						AND a.deleted = 0
				';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyIDAndUserIdAndStatusAndStartDateAndEndDate($company_id, $user_id, $status, $start_date, $end_date) {
		if ( $company_id == '' ) {
			return FALSE;
		}

		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		$key = Option::getByValue($status, $this->getOptions('status') );
		if ($key !== FALSE) {
			$status = $key;
		}

		$uf = new UserFactory();
		$udf = new UserDateFactory();
		$otpf = new OverTimePolicyFactory();

		$ph = array(
					'company_id' => $company_id,
					'user_id' => $user_id,
					'status_id' => $status,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		//Order by a.over_time_policy last so we never leave the ordering up to the database. This can cause
		//the unit tests to fail between databases.
		//AND a.type_id != 40
		$query = '
					select 	a.*,
							b.date_stamp as user_date_stamp
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id
					LEFT JOIN '. $uf->getTable() .' as c ON b.user_id = c.id
					LEFT JOIN '. $otpf->getTable() .' as d ON a.over_time_policy_id = d.id
					where
						c.company_id = ?
						AND	b.user_id = ?
						AND a.status_id = ?
						AND a.type_id not in (40,100,110)
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND ( a.deleted = 0 AND b.deleted = 0 )
					ORDER BY b.date_stamp asc, a.status_id asc, a.type_id asc, d.type_id desc, a.over_time_policy_id desc, a.premium_policy_id, a.total_time, a.id
					';
		//Debug::Text('Query: '. $query, __FILE__, __LINE__, __METHOD__, 10);
		//Debug::Arr($ph, 'PH: ', __FILE__, __LINE__, __METHOD__, 10);

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyIDAndUserIdAndStatusAndTypeAndStartDateAndEndDate($company_id, $user_id, $status, $type, $start_date, $end_date) {
		if ( $company_id == '' ) {
			return FALSE;
		}

		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		if ( $type == '' ) {
			return FALSE;
		}

		$uf = new UserFactory();
		$udf = new UserDateFactory();
		$otpf = new OverTimePolicyFactory();

		$ph = array(
					'company_id' => $company_id,
					'user_id' => $user_id,
					//'status_id' => $status,
					//'type' => $type,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		$query = '
					select 	a.*,
							b.date_stamp as user_date_stamp
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id
					LEFT JOIN '. $uf->getTable() .' as c ON b.user_id = c.id
					LEFT JOIN '. $otpf->getTable() .' as d ON a.over_time_policy_id = d.id
					where
						c.company_id = ?
						AND	b.user_id = ?
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND a.status_id in ('. $this->getListSQL($status, $ph) .')
						AND a.type_id in ('. $this->getListSQL($type, $ph) .')
						AND ( a.deleted = 0 AND b.deleted = 0 )
					ORDER BY b.date_stamp asc, a.status_id asc, a.type_id asc, d.type_id desc, a.total_time asc
					';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPaidTimeByCompanyIDAndUserIdAndStatusAndStartDateAndEndDate($company_id, $user_id, $status, $start_date, $end_date) {
		if ( $company_id == '' ) {
			return FALSE;
		}

		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		if ( $status == '' ) {
			return FALSE;
		}

		/*
		$key = Option::getByValue($status, $this->getOptions('status') );
		if ($key !== FALSE) {
			$status = $key;
		}
		*/

		//FIXME: For some reason if a punch_control gets deleted,
		//the user_date_total rows assigned to that don't get deleted at times.
		//Thus everything is off.
		$uf = new UserFactory();
		$udf = new UserDateFactory();
		$otpf = new OverTimePolicyFactory();
		$apf = new AbsencePolicyFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'company_id' => $company_id,
					'user_id' => $user_id,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		//						AND a.type_id != 40
		$query = '
					select 	a.*,
							b.date_stamp as user_date_stamp
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id
					LEFT JOIN '. $uf->getTable() .' as c ON b.user_id = c.id
					LEFT JOIN '. $otpf->getTable() .' as d ON a.over_time_policy_id = d.id
					LEFT JOIN '. $apf->getTable() .' as e ON a.absence_policy_id = e.id
					LEFT JOIN '. $pcf->getTable() .' as f ON a.punch_control_id = f.id
					where
						c.company_id = ?
						AND	b.user_id = ?
						AND a.type_id not in (10,40,100,110)
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND a.status_id in ('. $this->getListSQL($status, $ph) .')
						AND ( e.type_id is NULL OR e.type_id in ( 10, 12 ) )
						AND ( a.deleted = 0 AND b.deleted = 0 AND (f.deleted=0 OR f.deleted is NULL) )
					ORDER BY b.date_stamp asc, a.status_id asc, a.type_id asc, d.type_id desc
					';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getWorkedTimeSumByUserIDAndStartDateAndEndDate( $user_id, $start_date, $end_date ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					where 	a.user_date_id = b.id
						AND b.user_id = ?
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND a.status_id = 20
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getRegularTimeSumByUserIDAndStartDateAndEndDate( $user_id, $start_date, $end_date ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					where 	a.user_date_id = b.id
						AND b.user_id = ?
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND a.status_id = 10
						AND a.type_id = 20
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getPaidAbsenceTimeSumByUserIDAndStartDateAndEndDate( $user_id, $start_date, $end_date ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();

		//Include only paid absences.
		$udf = new UserDateFactory();
		$apf = new AbsencePolicyFactory();

		$ph = array(
					'user_id' => $user_id,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		//Include only paid absences.
		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b,
							'. $apf->getTable() .' as c
					where 	a.user_date_id = b.id
						AND a.absence_policy_id = c.id
						AND b.user_id = ?
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND a.status_id = 30
						AND c.type_id in ( 10, 12 )
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getDaysWorkedByUserIDAndStartDateAndEndDate( $user_id, $start_date, $end_date ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		//Include only paid absences.
		$query = '
					select 	count(distinct(a.user_date_id))
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					where 	a.user_date_id = b.id
						AND b.user_id = ?
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND a.status_id = 20
						AND a.total_time > 0
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getDaysWorkedByUserIDAndUserDateIDs( $user_id, $user_date_ids ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $user_date_ids == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					);

		//Include only paid absences.
		$query = '
					select 	count(distinct(a.user_date_id))
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					where 	a.user_date_id = b.id
						AND b.user_id = ?
						AND a.user_date_id in ('. $this->getListSQL($user_date_ids, $ph) .')
						AND a.status_id = 20
						AND a.total_time > 0
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	/*

			Pay period sums

	*/
	function getWorkedUsersByPayPeriodId( $pay_period_id ) {
		if ( $pay_period_id == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();

		$ph = array(
					'pay_period_id' => $pay_period_id,
					);

		//Include only paid absences.
		$query = '
					select 	count(distinct(b.user_id))
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					where 	a.user_date_id = b.id
						AND b.pay_period_id = ?
						AND a.status_id = 20
						AND a.total_time > 0
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getWorkedTimeSumByUserIDAndPayPeriodId( $user_id, $pay_period_id ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $pay_period_id == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					'pay_period_id' => $pay_period_id,
					);

		//Include only paid absences.
		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					where 	a.user_date_id = b.id
						AND b.user_id = ?
						AND b.pay_period_id = ?
						AND ( a.status_id = 20 OR ( a.status_id = 10 AND a.type_id in ( 100, 110 ) ) )
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getRegularAndOverTimeSumByUserIDAndPayPeriodId( $user_id, $pay_period_id ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $pay_period_id == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					'pay_period_id' => $pay_period_id,
					);

		//Ignore the type_id = 10 //Total Time
		$query = '
					select 	a.type_id as type_id,
							a.over_time_policy_id as over_time_policy_id,
							sum(total_time) as total_time
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					where 	a.user_date_id = b.id
						AND b.user_id = ?
						AND b.pay_period_id = ?
						AND a.status_id = 10
						AND a.type_id not in ( 10, 40, 100, 110 )
						AND ( a.deleted = 0 AND b.deleted=0 )
					group by a.type_id, a.over_time_policy_id
					order by a.type_id, a.over_time_policy_id
				';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPaidAbsenceTimeSumByUserIDAndPayPeriodId( $user_id, $pay_period_id ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $pay_period_id == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$apf = new AbsencePolicyFactory();

		$ph = array(
					'user_id' => $user_id,
					'pay_period_id' => $pay_period_id,
					);

		//Include only paid absences.
		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b,
							'. $apf->getTable() .' as c
					where 	a.user_date_id = b.id
						AND a.absence_policy_id = c.id
						AND b.user_id = ?
						AND b.pay_period_id = ?
						AND a.status_id = 30
						AND c.type_id in ( 10, 12 )
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getPremiumTimeSumByUserIDAndPayPeriodId( $user_id, $pay_period_id ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $pay_period_id == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$ppf = new PremiumPolicyFactory();

		$ph = array(
					'user_id' => $user_id,
					'pay_period_id' => $pay_period_id,
					);

		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b,
							'. $ppf->getTable() .' as c
					where 	a.user_date_id = b.id
						AND a.premium_policy_id = c.id
						AND b.user_id = ?
						AND b.pay_period_id = ?
						AND a.status_id = 10
						AND a.type_id = 40
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getDockAbsenceTimeSumByUserIDAndPayPeriodId( $user_id, $pay_period_id ) {
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $pay_period_id == '' ) {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$apf = new AbsencePolicyFactory();

		$ph = array(
					'user_id' => $user_id,
					'pay_period_id' => $pay_period_id,
					);

		$query = '
					select 	sum(total_time)
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b,
							'. $apf->getTable() .' as c
					where 	a.user_date_id = b.id
						AND a.absence_policy_id = c.id
						AND b.user_id = ?
						AND b.pay_period_id = ?
						AND a.status_id = 30
						AND c.type_id = 30
						AND ( a.deleted = 0 AND b.deleted=0 )
				';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Total: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getByOverTimePolicyId($over_time_policy_id, $where = NULL, $order = NULL) {
		if ( $over_time_policy_id == '') {
			return FALSE;
		}

		$ph = array(
					'over_time_policy_id' => $over_time_policy_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	over_time_policy_id = ?
						AND deleted = 0
					LIMIT 1
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByMealPolicyId($meal_policy_id, $where = NULL, $order = NULL) {
		if ( $meal_policy_id == '') {
			return FALSE;
		}

		$ph = array(
					'meal_policy_id' => $meal_policy_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	meal_policy_id = ?
						AND deleted = 0
					LIMIT 1
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByBreakPolicyId($break_policy_id, $where = NULL, $order = NULL) {
		if ( $break_policy_id == '') {
			return FALSE;
		}

		$ph = array(
					'break_policy_id' => $break_policy_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	break_policy_id = ?
						AND deleted = 0
					LIMIT 1
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByPremiumTimePolicyId($premium_policy_id, $where = NULL, $order = NULL) {
		if ( $premium_policy_id == '') {
			return FALSE;
		}

		$ph = array(
					'premium_policy_id' => $premium_policy_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	premium_policy_id = ?
						AND deleted = 0
					LIMIT 1
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByAbsencePolicyId($absence_policy_id, $where = NULL, $order = NULL) {
		if ( $absence_policy_id == '') {
			return FALSE;
		}

		$ph = array(
					'absence_policy_id' => $absence_policy_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	absence_policy_id = ?
						AND deleted = 0
					LIMIT 1
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByJobId($job_id, $where = NULL, $order = NULL) {
		if ( $job_id == '') {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$uwf = new UserWageFactory();

		$ph = array(
					'job_id' => $job_id,
					);

		$query = '
					select 	a.*,
							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id
					LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = b.user_id
																			and z.effective_date <= b.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)

					where	a.job_id = ?
						AND a.status_id = 10
						AND ( a.deleted = 0 AND b.deleted = 0)
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getReportByJobId($job_ids) {
		if ( $job_ids == '') {
			return FALSE;
		}

		//$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
		//$order = array( 'z.last_name' => 'asc' );
		/*
		if ( $order == NULL ) {
			$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		$udf = new UserDateFactory();
		$uwf = new UserWageFactory();
		$otpf = new OverTimePolicyFactory();
		$apf = new AbsencePolicyFactory();
		$ppf = new PremiumPolicyFactory();

		$ph = array();

		//Make it so employees with 0 hours still show up!! Very important!
		//Order dock hours first, so it can be deducted from regular time.
		//Used to use this group by line for some reason, changed it to match that of getReportByStartDateAndEndDateAndJobList
		//--group by b.user_id, user_wage_id, user_wage_effective_date, a.job_id, a.job_item_id, a.status_id, a.type_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id
		$query = '
					select
							b.user_id as user_id,
							a.job_id as job_id,
							a.job_item_id as job_item_id,
							a.status_id as status_id,
							a.type_id as type_id,

							a.over_time_policy_id as over_time_policy_id,
							n.id as over_time_policy_wage_id,
							n.effective_date as over_time_policy_wage_effective_date,

							a.absence_policy_id as absence_policy_id,
							p.id as absence_policy_wage_id,
							p.effective_date as absence_policy_wage_effective_date,

							a.premium_policy_id as premium_policy_id,
							r.id as premium_policy_wage_id,
							r.effective_date as premium_policy_wage_effective_date,

							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date,
							sum(total_time) as total_time,
							sum(actual_total_time) as actual_total_time,
							sum(quantity) as quantity,
							sum(bad_quantity) as bad_quantity
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id

					LEFT JOIN '. $otpf->getTable() .' as m ON a.over_time_policy_id = m.id
					LEFT JOIN '. $uwf->getTable() .' as n ON n.id = (select n.id
																		from '. $uwf->getTable() .' as n
																		where n.user_id = b.user_id
																			and n.wage_group_id = m.wage_group_id
																			and n.effective_date <= b.date_stamp
																			and n.deleted = 0
																			order by n.effective_date desc limit 1)

					LEFT JOIN '. $apf->getTable() .' as o ON a.absence_policy_id = o.id
					LEFT JOIN '. $uwf->getTable() .' as p ON p.id = (select p.id
																		from '. $uwf->getTable() .' as p
																		where p.user_id = b.user_id
																			and p.wage_group_id = o.wage_group_id
																			and p.effective_date <= b.date_stamp
																			and p.deleted = 0
																			order by p.effective_date desc limit 1)

					LEFT JOIN '. $ppf->getTable() .' as q ON a.premium_policy_id = q.id
					LEFT JOIN '. $uwf->getTable() .' as r ON r.id = (select r.id
																		from '. $uwf->getTable() .' as r
																		where r.user_id = b.user_id
																			and r.wage_group_id = q.wage_group_id
																			and r.effective_date <= b.date_stamp
																			and r.deleted = 0
																			order by r.effective_date desc limit 1)

					LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = b.user_id
																			and z.effective_date <= b.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)
					where
						a.job_id in ('. $this->getListSQL($job_ids, $ph) .')
						AND a.status_id in (10)
						AND ( a.deleted = 0 AND b.deleted = 0 )
					group by b.user_id,user_wage_id, user_wage_effective_date, over_time_policy_wage_id, over_time_policy_wage_effective_date, absence_policy_wage_id, absence_policy_wage_effective_date, premium_policy_wage_id, premium_policy_wage_effective_date, a.status_id, a.type_id, a.branch_id, a.department_id, a.job_id, a.job_item_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id order by a.job_id asc
				';
		//$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserIdAndPayPeriodIdAndEndDate($user_id, $pay_period_id, $end_date = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $pay_period_id == '') {
			return FALSE;
		}

		if ( $end_date == NULL ) {
			//Get pay period end date.
			$pplf = new PayPeriodListFactory();
			$pplf->getById( $pay_period_id );
			if ( $pplf->getRecordCount() > 0 ) {
				$pp_obj = $pplf->getCurrent();
				$end_date = $pp_obj->getEndDate();
			}
		}

		$udf = new UserDateFactory();
		$uwf = new UserWageFactory();
		$otpf = new OverTimePolicyFactory();
		$apf = new AbsencePolicyFactory();
		$ppf = new PremiumPolicyFactory();

		$ph = array(
					'user_id' => $user_id,
					'pay_period_id' => $pay_period_id,
					'end_date' =>  $this->db->BindDate( $end_date ),
					);

/*
					select 	a.status_id as status_id,
							a.type_id as type_id,
							a.over_time_policy_id as over_time_policy_id,
							a.absence_policy_id as absence_policy_id,
							a.premium_policy_id as premium_policy_id,
							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date,
							sum(total_Time) as total_time
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id
					LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = b.user_id
																			and z.effective_date <= b.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)
					where
						b.user_id = ?
						AND b.pay_period_id = ?
						AND b.date_stamp <= ?
						AND a.status_id in (10,30)
						AND ( a.deleted = 0 AND b.deleted = 0)
					group by user_wage_id, user_wage_effective_date, a.status_id, a.type_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id
					order by a.status_id desc, a.type_id asc, user_wage_effective_date desc
*/
		//Order dock hours first, so it can be deducted from regular time.
		//Order newest wage changes first too. This is VERY important for calculating pro-rate amounts.
		$query = '
					select 	a.status_id as status_id,
							a.type_id as type_id,

							a.over_time_policy_id as over_time_policy_id,
							n.id as over_time_policy_wage_id,
							n.effective_date as over_time_policy_wage_effective_date,

							a.absence_policy_id as absence_policy_id,
							p.id as absence_policy_wage_id,
							p.effective_date as absence_policy_wage_effective_date,

							a.premium_policy_id as premium_policy_id,
							r.id as premium_policy_wage_id,
							r.effective_date as premium_policy_wage_effective_date,

							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date,
							sum(total_Time) as total_time
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id

					LEFT JOIN '. $otpf->getTable() .' as m ON a.over_time_policy_id = m.id
					LEFT JOIN '. $uwf->getTable() .' as n ON n.id = (select n.id
																		from '. $uwf->getTable() .' as n
																		where n.user_id = b.user_id
																			and n.wage_group_id = m.wage_group_id
																			and n.effective_date <= b.date_stamp
																			and n.deleted = 0
																			order by n.effective_date desc limit 1)

					LEFT JOIN '. $apf->getTable() .' as o ON a.absence_policy_id = o.id
					LEFT JOIN '. $uwf->getTable() .' as p ON p.id = (select p.id
																		from '. $uwf->getTable() .' as p
																		where p.user_id = b.user_id
																			and p.wage_group_id = o.wage_group_id
																			and p.effective_date <= b.date_stamp
																			and p.deleted = 0
																			order by p.effective_date desc limit 1)

					LEFT JOIN '. $ppf->getTable() .' as q ON a.premium_policy_id = q.id
					LEFT JOIN '. $uwf->getTable() .' as r ON r.id = (select r.id
																		from '. $uwf->getTable() .' as r
																		where r.user_id = b.user_id
																			and r.wage_group_id = q.wage_group_id
																			and r.effective_date <= b.date_stamp
																			and r.deleted = 0
																			order by r.effective_date desc limit 1)

					LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = b.user_id
																			and z.wage_group_id = 0
																			and z.effective_date <= b.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)
					where
						b.user_id = ?
						AND b.pay_period_id = ?
						AND b.date_stamp <= ?
						AND a.status_id in (10,30)
						AND ( a.deleted = 0 AND b.deleted = 0)
					group by user_wage_id, user_wage_effective_date, over_time_policy_wage_id, over_time_policy_wage_effective_date, absence_policy_wage_id, absence_policy_wage_effective_date, premium_policy_wage_id, premium_policy_wage_effective_date, a.status_id, a.type_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id
					order by a.status_id desc, a.type_id asc, user_wage_effective_date desc, over_time_policy_wage_effective_date desc, absence_policy_wage_effective_date desc, premium_policy_wage_effective_date desc
				';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getReportByPayPeriodIDListAndUserIdList($pay_period_ids, $user_ids, $order = NULL) {
		if ( $user_ids == '') {
			return FALSE;
		}

		if ( $pay_period_ids == '') {
			return FALSE;
		}

		//$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
		$order = array( 'z.last_name' => 'asc' );
		/*
		if ( $order == NULL ) {
			$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();

		$ph = array();

		//Make it so employees with 0 hours still show up!! Very important!
		//Order dock hours first, so it can be deducted from regular time.
		$query = '
				select z.id, tmp.*
				from '. $ulf->getTable() .' as z
				LEFT JOIN

					( select  b.user_id,
							a.status_id as status_id,
							a.type_id as type_id,
							b.pay_period_id as pay_period_id,
							a.over_time_policy_id as over_time_policy_id,
							a.absence_policy_id as absence_policy_id,
							a.premium_policy_id as premium_policy_id,
							sum(total_Time) as total_time,
							sum(actual_total_Time) as actual_total_time
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					where 	a.user_date_id = b.id
						AND b.user_id in ('. $this->getListSQL($user_ids, $ph) .')
					';

		if ( $pay_period_ids != '' AND isset($pay_period_ids[0]) AND !in_array(-1, (array)$pay_period_ids) ) {
			$query .= ' AND b.pay_period_id in ('. $this->getListSQL($pay_period_ids, $ph) .') ';
		}

		$query .= '
						AND a.status_id in (10,20,30)
						AND ( a.deleted = 0 AND b.deleted = 0)
					group by b.user_id,b.pay_period_id,a.status_id, a.type_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id
					) as tmp ON z.id = tmp.user_id
				WHERE z.id in ('. $this->getListSQL($user_ids, $ph) .')
					AND z.deleted = 0
				';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getReportByCompanyIdAndArrayCriteria( $company_id, $filter_data, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		//$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
		$order = array( 'z.last_name' => 'asc' );
		/*
		if ( $order == NULL ) {
			$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		if ( isset($filter_data['user_ids']) ) {
			$filter_data['user_id'] = $filter_data['user_ids'];
		}
		if ( isset($filter_data['job_ids']) ) {
			$filter_data['job_id'] = $filter_data['job_ids'];
		}

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$ppf = new PayPeriodFactory();

		$ph = array();

		//Make it so employees with 0 hours still show up!! Very important!
		//Order dock hours first, so it can be deducted from regular time.
		$query = '
				select z.id, tmp.*
				from '. $ulf->getTable() .' as z
				LEFT JOIN
					( select  b.user_id,
							a.status_id as status_id,
							a.type_id as type_id,
							b.pay_period_id as pay_period_id,
							a.branch_id as branch_id,
							a.department_id as department_id,
							a.job_id as job_id,
							a.job_item_id as job_item_id,
							a.over_time_policy_id as over_time_policy_id,
							a.absence_policy_id as absence_policy_id,
							a.premium_policy_id as premium_policy_id,
							sum(a.total_time) as total_time,
							sum(a.actual_total_Time) as actual_total_time,
							count( distinct(CASE WHEN a.status_id = 20 AND a.type_id = 10 AND a.total_time > 0 THEN a.user_date_id ELSE NULL END) ) as worked_days
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id
					LEFT JOIN '. $ppf->getTable() .' as c ON b.pay_period_id = c.id
					where 1=1
					';

					if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
						$query  .=	' AND b.user_id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
					}

					if ( isset($filter_data['pay_period_ids']) AND isset($filter_data['pay_period_ids'][0]) AND !in_array(-1, (array)$filter_data['pay_period_ids']) ) {
						$query .= 	' AND b.pay_period_id in ('. $this->getListSQL($filter_data['pay_period_ids'], $ph) .') ';
					}
					if ( isset($filter_data['transaction_start_date']) AND trim($filter_data['transaction_start_date']) != '' ) {
						$ph[] = $this->db->BindTimeStamp( strtolower(trim($filter_data['transaction_start_date'])) );
						$query  .=	' AND c.transaction_date >= ?';
					}
					if ( isset($filter_data['transaction_end_date']) AND trim($filter_data['transaction_end_date']) != '' ) {
						$ph[] = $this->db->BindTimeStamp( strtolower(trim($filter_data['transaction_end_date'])) );
						$query  .=	' AND c.transaction_date <= ?';
					}

					if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
						$ph[] = $this->db->BindDate($filter_data['start_date']);
						$query  .=	' AND b.date_stamp >= ?';
					}
					if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
						$ph[] = $this->db->BindDate($filter_data['end_date']);
						$query  .=	' AND b.date_stamp <= ?';
					}

					if ( isset($filter_data['job_id']) AND isset($filter_data['job_id'][0]) AND !in_array(-1, (array)$filter_data['job_id']) ) {
						$query  .=	' AND a.job_id in ('. $this->getListSQL($filter_data['job_id'], $ph) .') ';
					}
					if ( isset($filter_data['job_item_id']) AND isset($filter_data['job_item_id'][0]) AND !in_array(-1, (array)$filter_data['job_item_id']) ) {
						$query  .=	' AND a.job_item_id in ('. $this->getListSQL($filter_data['job_item_id'], $ph) .') ';
					}

		$ph[] = $company_id;
		$query .= '
						AND a.status_id in (10,20,30)
						AND ( a.deleted = 0 AND b.deleted = 0)
					group by b.user_id, b.pay_period_id, a.branch_id, a.department_id, a.job_id, a.job_item_id, a.status_id, a.type_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id
					) as tmp ON z.id = tmp.user_id
				WHERE z.company_id = ? ';

		if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
			$query  .=	' AND z.id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
		}

		$query .= ' AND z.deleted = 0 ';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;

	}

	function getDayReportByPayPeriodIDListAndUserIdList($pay_period_ids, $user_ids, $order = NULL) {
		if ( $user_ids == '') {
			return FALSE;
		}

		if ( $pay_period_ids == '') {
			return FALSE;
		}

		//$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
		$order = array( 'tmp.pay_period_id' => 'asc','z.last_name' => 'asc', 'tmp.date_stamp' => 'asc' );
		/*
		if ( $order == NULL ) {
			$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();

		$ph = array();

		//Make it so employees with 0 hours still show up!! Very important!
		//Order dock hours first, so it can be deducted from regular time.
		$query = '
				select z.id, tmp.*
				from '. $ulf->getTable() .' as z
				LEFT JOIN

					( select
							b.user_id,
							b.pay_period_id as pay_period_id,
							b.date_stamp as date_stamp,
							a.status_id as status_id,
							a.type_id as type_id,
							a.over_time_policy_id as over_time_policy_id,
							a.absence_policy_id as absence_policy_id,
							a.premium_policy_id as premium_policy_id,
							tmp2.min_punch_time_stamp as min_punch_time_stamp,
							tmp2.max_punch_time_stamp as max_punch_time_stamp,
							sum(total_Time) as total_time,
							sum(actual_total_Time) as actual_total_time
					from	'. $this->getTable() .' as a,
							'. $udf->getTable() .' as b
					LEFT JOIN (
						select tmp2_a.id, min(tmp2_c.time_stamp) as min_punch_time_stamp, max(tmp2_c.time_stamp) as max_punch_time_stamp
							from '. $udf->getTable() .' as tmp2_a
							LEFT JOIN '. $pcf->getTable() .' as tmp2_b ON tmp2_a.id = tmp2_b.user_date_id
							LEFT JOIN '. $pf->getTable() .' as tmp2_c ON tmp2_b.id = tmp2_c.punch_control_id
							WHERE tmp2_a.user_id in ('. $this->getListSQL($user_ids, $ph) .') ';

							if ( $pay_period_ids != '' AND isset($pay_period_ids[0]) AND !in_array(-1, (array)$pay_period_ids) ) {
								$query .= ' AND tmp2_a.pay_period_id in ('. $this->getListSQL($pay_period_ids, $ph) .') ';
							}

							$query .= '
								AND tmp2_c.time_stamp is not null
								AND ( tmp2_a.deleted = 0 AND tmp2_b.deleted = 0 AND tmp2_c.deleted = 0 )
							group by tmp2_a.id

					) as tmp2 ON b.id = tmp2.id

					where 	a.user_date_id = b.id
						AND b.user_id in ('. $this->getListSQL($user_ids, $ph) .')
					';

		if ( $pay_period_ids != '' AND isset($pay_period_ids[0]) AND !in_array(-1, (array)$pay_period_ids) ) {
			$query .= ' AND b.pay_period_id in ('. $this->getListSQL($pay_period_ids, $ph) .') ';
		}

		$query .= '
						AND a.status_id in (10,20,30)
						AND ( a.deleted = 0 AND b.deleted = 0)
					group by b.user_id, b.pay_period_id, b.date_stamp, a.status_id, a.type_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id, tmp2.min_punch_time_stamp, tmp2.max_punch_time_stamp
					) as tmp ON z.id = tmp.user_id
				WHERE z.id in ('. $this->getListSQL($user_ids, $ph) .')
					AND z.deleted = 0
				';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getDayReportByCompanyIdAndArrayCriteria( $company_id, $filter_data, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {

		//$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
		$order = array( 'tmp.pay_period_id' => 'asc','z.last_name' => 'asc', 'tmp.date_stamp' => 'asc' );
		/*
		if ( $order == NULL ) {
			$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$uwf = new UserWageFactory();
		$pcf = new PunchControlFactory();
		$pf = new PunchFactory();
		$otpf = new OverTimePolicyFactory();
		$apf = new AbsencePolicyFactory();
		$ppf = new PremiumPolicyFactory();

		$ph = array();

		//Make it so employees with 0 hours still show up!! Very important!
		//Order dock hours first, so it can be deducted from regular time.
		$query = '
				select z.id, tmp.*
				from '. $ulf->getTable() .' as z
				LEFT JOIN

					( select
							b.user_id,
							b.pay_period_id as pay_period_id,
							b.date_stamp as date_stamp,
							a.branch_id as branch_id,
							a.department_id as department_id,
							a.status_id as status_id,
							a.type_id as type_id,

							a.over_time_policy_id as over_time_policy_id,
							n.id as over_time_policy_wage_id,
							n.effective_date as over_time_policy_wage_effective_date,

							a.absence_policy_id as absence_policy_id,
							p.id as absence_policy_wage_id,
							p.effective_date as absence_policy_wage_effective_date,

							a.premium_policy_id as premium_policy_id,
							r.id as premium_policy_wage_id,
							r.effective_date as premium_policy_wage_effective_date,

							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date,
							tmp2.min_punch_time_stamp as min_punch_time_stamp,
							tmp2.max_punch_time_stamp as max_punch_time_stamp,
							sum(total_Time) as total_time,
							sum(actual_total_Time) as actual_total_time
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id

					LEFT JOIN '. $otpf->getTable() .' as m ON a.over_time_policy_id = m.id
					LEFT JOIN '. $uwf->getTable() .' as n ON n.id = (select n.id
																		from '. $uwf->getTable() .' as n
																		where n.user_id = b.user_id
																			and n.wage_group_id = m.wage_group_id
																			and n.effective_date <= b.date_stamp
																			and n.deleted = 0
																			order by n.effective_date desc limit 1)

					LEFT JOIN '. $apf->getTable() .' as o ON a.absence_policy_id = o.id
					LEFT JOIN '. $uwf->getTable() .' as p ON p.id = (select p.id
																		from '. $uwf->getTable() .' as p
																		where p.user_id = b.user_id
																			and p.wage_group_id = o.wage_group_id
																			and p.effective_date <= b.date_stamp
																			and p.deleted = 0
																			order by p.effective_date desc limit 1)

					LEFT JOIN '. $ppf->getTable() .' as q ON a.premium_policy_id = q.id
					LEFT JOIN '. $uwf->getTable() .' as r ON r.id = (select r.id
																		from '. $uwf->getTable() .' as r
																		where r.user_id = b.user_id
																			and r.wage_group_id = q.wage_group_id
																			and r.effective_date <= b.date_stamp
																			and r.deleted = 0
																			order by r.effective_date desc limit 1)

					LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = b.user_id
																			and z.effective_date <= b.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)
					LEFT JOIN (
						select tmp3.id, min(tmp3.min_punch_time_stamp) as min_punch_time_stamp, max(tmp3.max_punch_time_stamp) as max_punch_time_stamp from (
							select tmp2_a.id,
								CASE WHEN tmp2_c.status_id = 10 THEN min(tmp2_c.time_stamp) ELSE NULL END as min_punch_time_stamp,
								CASE WHEN tmp2_c.status_id = 20 THEN max(tmp2_c.time_stamp) ELSE NULL END as max_punch_time_stamp
								from '. $udf->getTable() .' as tmp2_a
								LEFT JOIN '. $pcf->getTable() .' as tmp2_b ON tmp2_a.id = tmp2_b.user_date_id
								LEFT JOIN '. $pf->getTable() .' as tmp2_c ON tmp2_b.id = tmp2_c.punch_control_id
								WHERE 1=1 ';
								if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
									$query  .=	' AND tmp2_a.user_id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
								}

								if ( isset($filter_data['pay_period_ids']) AND isset($filter_data['pay_period_ids'][0]) AND !in_array(-1, (array)$filter_data['pay_period_ids']) ) {
									$query .= 	' AND tmp2_a.pay_period_id in ('. $this->getListSQL($filter_data['pay_period_ids'], $ph) .') ';
								}

								if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
									$ph[] = $this->db->BindDate($filter_data['start_date']);
									$query  .=	' AND tmp2_a.date_stamp >= ?';
								}
								if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
									$ph[] = $this->db->BindDate($filter_data['end_date']);
									$query  .=	' AND tmp2_a.date_stamp <= ?';
								}

								$query .= '
									AND tmp2_c.time_stamp is not null
									AND ( tmp2_a.deleted = 0 AND tmp2_b.deleted = 0 AND tmp2_c.deleted = 0 )
								group by tmp2_a.id, tmp2_c.status_id
							) as tmp3 group by tmp3.id
					) as tmp2 ON b.id = tmp2.id

					where 	1=1 ';

					if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
						$query  .=	' AND b.user_id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
					}

		if ( isset($filter_data['pay_period_ids']) AND isset($filter_data['pay_period_ids'][0]) AND !in_array(-1, (array)$filter_data['pay_period_ids']) ) {
			$query .= 	' AND b.pay_period_id in ('. $this->getListSQL($filter_data['pay_period_ids'], $ph) .') ';
		}

		if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
			$ph[] = $this->db->BindDate($filter_data['start_date']);
			$query  .=	' AND b.date_stamp >= ?';
		}
		if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
			$ph[] = $this->db->BindDate($filter_data['end_date']);
			$query  .=	' AND b.date_stamp <= ?';
		}

		$ph[] = $company_id;
		$query .= '
						AND a.status_id in (10,20,30)
						AND ( a.deleted = 0 AND b.deleted = 0 )
					group by b.user_id, b.pay_period_id, a.branch_id, a.department_id, b.date_stamp, user_wage_id, user_wage_effective_date, over_time_policy_wage_id, over_time_policy_wage_effective_date, absence_policy_wage_id, absence_policy_wage_effective_date, premium_policy_wage_id, premium_policy_wage_effective_date, a.status_id, a.type_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id, tmp2.min_punch_time_stamp, tmp2.max_punch_time_stamp
					) as tmp ON z.id = tmp.user_id
				WHERE z.company_id = ? ';

		if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
			$query  .=	' AND z.id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
		}

		$query .= ' AND z.deleted = 0 ';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getReportByStartDateAndEndDateAndUserIdList($start_date, $end_date, $user_ids, $order = NULL) {
		if ( $user_ids == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		//$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
		$order = array( 'z.last_name' => 'asc' );
		/*
		if ( $order == NULL ) {
			$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$uwf = new UserWageFactory();
		$otpf = new OverTimePolicyFactory();
		$apf = new AbsencePolicyFactory();
		$ppf = new PremiumPolicyFactory();

		$ph = array(
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		//Make it so employees with 0 hours still show up!! Very important!
		//Order dock hours first, so it can be deducted from regular time.
		$query = '
				select z.id, tmp.*
				from '. $ulf->getTable() .' as z
				LEFT JOIN
					( select  b.user_id,
							b.date_stamp as date_stamp,
							a.status_id as status_id,
							a.type_id as type_id,

							a.over_time_policy_id as over_time_policy_id,
							n.id as over_time_policy_wage_id,
							n.effective_date as over_time_policy_wage_effective_date,

							a.absence_policy_id as absence_policy_id,
							p.id as absence_policy_wage_id,
							p.effective_date as absence_policy_wage_effective_date,

							a.premium_policy_id as premium_policy_id,
							r.id as premium_policy_wage_id,
							r.effective_date as premium_policy_wage_effective_date,

							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date,
							sum(total_Time) as total_time,
							sum(actual_total_Time) as actual_total_time
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id

					LEFT JOIN '. $otpf->getTable() .' as m ON a.over_time_policy_id = m.id
					LEFT JOIN '. $uwf->getTable() .' as n ON n.id = (select n.id
																		from '. $uwf->getTable() .' as n
																		where n.user_id = b.user_id
																			and n.wage_group_id = m.wage_group_id
																			and n.effective_date <= b.date_stamp
																			and n.deleted = 0
																			order by n.effective_date desc limit 1)

					LEFT JOIN '. $apf->getTable() .' as o ON a.absence_policy_id = o.id
					LEFT JOIN '. $uwf->getTable() .' as p ON p.id = (select p.id
																		from '. $uwf->getTable() .' as p
																		where p.user_id = b.user_id
																			and p.wage_group_id = o.wage_group_id
																			and p.effective_date <= b.date_stamp
																			and p.deleted = 0
																			order by p.effective_date desc limit 1)

					LEFT JOIN '. $ppf->getTable() .' as q ON a.premium_policy_id = q.id
					LEFT JOIN '. $uwf->getTable() .' as r ON r.id = (select r.id
																		from '. $uwf->getTable() .' as r
																		where r.user_id = b.user_id
																			and r.wage_group_id = q.wage_group_id
																			and r.effective_date <= b.date_stamp
																			and r.deleted = 0
																			order by r.effective_date desc limit 1)

					LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = b.user_id
																			and z.effective_date <= b.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)
					where
						b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND b.user_id in ('. $this->getListSQL($user_ids, $ph) .')
						AND a.status_id in (10,30)
						AND ( a.deleted = 0 AND b.deleted = 0)
					group by b.user_id, b.date_stamp, user_wage_id, user_wage_effective_date, over_time_policy_wage_id, over_time_policy_wage_effective_date, absence_policy_wage_id, absence_policy_wage_effective_date, premium_policy_wage_id, premium_policy_wage_effective_date, a.status_id, a.type_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id
					) as tmp ON z.id = tmp.user_id
				WHERE z.id in ('. $this->getListSQL($user_ids, $ph) .')
					AND z.deleted = 0
				';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getReportByStartDateAndEndDateAndJobList($start_date, $end_date, $job_ids, $order = NULL) {
		if ( $job_ids == '') {
			Debug::Text('No Job Ids: ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( $start_date == '') {
			//Debug::Text('No Start Date: ', __FILE__, __LINE__, __METHOD__,10);
			$start_date = 0;
		}

		if ( $end_date == '') {
			//Debug::Text('No End Date: ', __FILE__, __LINE__, __METHOD__,10);
			$end_date = time();
		}

		//$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
		//$order = array( 'z.last_name' => 'asc' );
		/*
		if ( $order == NULL ) {
			$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$uwf = new UserWageFactory();
		$otpf = new OverTimePolicyFactory();
		$apf = new AbsencePolicyFactory();
		$ppf = new PremiumPolicyFactory();

		$ph = array(
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		$query = '

					select  b.user_id as user_id,
							a.status_id as status_id,
							a.type_id as type_id,
							a.branch_id as branch_id,
							a.department_id as department_id,
							a.job_id as job_id,
							a.job_item_id as job_item_id,

							a.over_time_policy_id as over_time_policy_id,
							n.id as over_time_policy_wage_id,
							n.effective_date as over_time_policy_wage_effective_date,

							a.absence_policy_id as absence_policy_id,
							p.id as absence_policy_wage_id,
							p.effective_date as absence_policy_wage_effective_date,

							a.premium_policy_id as premium_policy_id,
							r.id as premium_policy_wage_id,
							r.effective_date as premium_policy_wage_effective_date,

							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date,
							sum(total_time) as total_time,
							sum(actual_total_time) as actual_total_time,
							sum(quantity) as quantity,
							sum(bad_quantity) as bad_quantity
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id

					LEFT JOIN '. $otpf->getTable() .' as m ON a.over_time_policy_id = m.id
					LEFT JOIN '. $uwf->getTable() .' as n ON n.id = (select n.id
																		from '. $uwf->getTable() .' as n
																		where n.user_id = b.user_id
																			and n.wage_group_id = m.wage_group_id
																			and n.effective_date <= b.date_stamp
																			and n.deleted = 0
																			order by n.effective_date desc limit 1)

					LEFT JOIN '. $apf->getTable() .' as o ON a.absence_policy_id = o.id
					LEFT JOIN '. $uwf->getTable() .' as p ON p.id = (select p.id
																		from '. $uwf->getTable() .' as p
																		where p.user_id = b.user_id
																			and p.wage_group_id = o.wage_group_id
																			and p.effective_date <= b.date_stamp
																			and p.deleted = 0
																			order by p.effective_date desc limit 1)

					LEFT JOIN '. $ppf->getTable() .' as q ON a.premium_policy_id = q.id
					LEFT JOIN '. $uwf->getTable() .' as r ON r.id = (select r.id
																		from '. $uwf->getTable() .' as r
																		where r.user_id = b.user_id
																			and r.wage_group_id = q.wage_group_id
																			and r.effective_date <= b.date_stamp
																			and r.deleted = 0
																			order by r.effective_date desc limit 1)

					LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = b.user_id
																			and z.effective_date <= b.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)
					where 	a.user_date_id = b.id
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND a.job_id in ('. $this->getListSQL($job_ids, $ph) .')
						AND a.status_id in (10,20,30)
						AND ( a.deleted = 0 AND b.deleted = 0)
					group by b.user_id,user_wage_id, user_wage_effective_date, over_time_policy_wage_id, over_time_policy_wage_effective_date, absence_policy_wage_id, absence_policy_wage_effective_date, premium_policy_wage_id, premium_policy_wage_effective_date, a.status_id, a.type_id, a.branch_id, a.department_id, a.job_id, a.job_item_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id
					order by a.job_id asc
				';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getReportByStartDateAndEndDateAndUserIdListAndJobListAndJobItemList($start_date, $end_date, $user_ids, $job_ids, $job_item_ids, $order = NULL) {
		if ( $user_ids == '') {
			Debug::Text('No User Ids: ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( $job_ids == '') {
			Debug::Text('No Job Ids: ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( $job_item_ids == '') {
			Debug::Text('No Job Item Ids: ', __FILE__, __LINE__, __METHOD__,10);
			return FALSE;
		}

		if ( $start_date == '') {
			Debug::Text('No Start Date: ', __FILE__, __LINE__, __METHOD__,10);
			$start_date = 0;
		}

		if ( $end_date == '') {
			Debug::Text('No End Date: ', __FILE__, __LINE__, __METHOD__,10);
			$end_date = time();
		}

		//$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
		//$order = array( 'z.last_name' => 'asc' );
		/*
		if ( $order == NULL ) {
			$order = array( 'b.pay_period_id' => 'asc', 'b.user_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$uwf = new UserWageFactory();
		$otpf = new OverTimePolicyFactory();
		$apf = new AbsencePolicyFactory();
		$ppf = new PremiumPolicyFactory();

		$ph = array(
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		$query = '

					select  b.user_id as user_id,
							a.status_id as status_id,
							a.type_id as type_id,
							a.branch_id as branch_id,
							a.department_id as department_id,
							a.job_id as job_id,
							a.job_item_id as job_item_id,

							a.over_time_policy_id as over_time_policy_id,
							n.id as over_time_policy_wage_id,
							n.effective_date as over_time_policy_wage_effective_date,

							a.absence_policy_id as absence_policy_id,
							p.id as absence_policy_wage_id,
							p.effective_date as absence_policy_wage_effective_date,

							a.premium_policy_id as premium_policy_id,
							r.id as premium_policy_wage_id,
							r.effective_date as premium_policy_wage_effective_date,

							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date,
							sum(total_time) as total_time,
							sum(actual_total_time) as actual_total_time,
							sum(quantity) as quantity,
							sum(bad_quantity) as bad_quantity
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udf->getTable() .' as b ON a.user_date_id = b.id

					LEFT JOIN '. $otpf->getTable() .' as m ON a.over_time_policy_id = m.id
					LEFT JOIN '. $uwf->getTable() .' as n ON n.id = (select n.id
																		from '. $uwf->getTable() .' as n
																		where n.user_id = b.user_id
																			and n.wage_group_id = m.wage_group_id
																			and n.effective_date <= b.date_stamp
																			and n.deleted = 0
																			order by n.effective_date desc limit 1)

					LEFT JOIN '. $apf->getTable() .' as o ON a.absence_policy_id = o.id
					LEFT JOIN '. $uwf->getTable() .' as p ON p.id = (select p.id
																		from '. $uwf->getTable() .' as p
																		where p.user_id = b.user_id
																			and p.wage_group_id = o.wage_group_id
																			and p.effective_date <= b.date_stamp
																			and p.deleted = 0
																			order by p.effective_date desc limit 1)

					LEFT JOIN '. $ppf->getTable() .' as q ON a.premium_policy_id = q.id
					LEFT JOIN '. $uwf->getTable() .' as r ON r.id = (select r.id
																		from '. $uwf->getTable() .' as r
																		where r.user_id = b.user_id
																			and r.wage_group_id = q.wage_group_id
																			and r.effective_date <= b.date_stamp
																			and r.deleted = 0
																			order by r.effective_date desc limit 1)

					LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = b.user_id
																			and z.effective_date <= b.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)
					where 	a.user_date_id = b.id
						AND b.date_stamp >= ?
						AND b.date_stamp <= ?
						AND b.user_id in ('. $this->getListSQL($user_ids, $ph) .')
						AND a.job_id in ('. $this->getListSQL($job_ids, $ph) .')
					';
		//AND a.job_item_id in ('. $this->getListSQL($job_item_ids, $ph) .')

		$filter_query = NULL;
		if ( $job_item_ids != '' AND isset($job_item_ids[0]) AND !in_array(-1, $job_item_ids) ) {
			$query  .=	' AND a.job_item_id in ('. $this->getListSQL($job_item_ids, $ph) .') ';
		}

		$query .= '
						AND a.status_id in (10,20,30)
						AND ( a.deleted = 0 AND b.deleted = 0)
					group by b.user_id,user_wage_id, user_wage_effective_date, over_time_policy_wage_id, over_time_policy_wage_effective_date, absence_policy_wage_id, absence_policy_wage_effective_date, premium_policy_wage_id, premium_policy_wage_effective_date, a.status_id, a.type_id, a.branch_id, a.department_id, a.job_id, a.job_item_id, a.over_time_policy_id, a.absence_policy_id, a.premium_policy_id
				';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getReportHoursByTimePeriodAndUserIdAndCompanyIdAndStartDateAndEndDate($time_period, $user_ids, $company_id, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $time_period == '' ) {
			return FALSE;
		}

		if ( $user_ids == '' ) {
			return FALSE;
		}

		if ( $company_id == '' ) {
			return FALSE;
		}

		if ( $start_date == '' ) {
			return FALSE;
		}

		if ( $end_date == '' ) {
			return FALSE;
		}

		/*
		if ( $order == NULL ) {
			$order = array( 'date_stamp' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		*/

		$uf = new UserFactory();
		$udf = new UserDateFactory();

		$ph = array(
					'company_id' => $company_id,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date ),
					);

		$query = '
					select 	user_id,
							status_id,
							type_id,
							over_time_policy_id,
							absence_policy_id,
							premium_policy_id,
							avg(total_time) as avg,
							min(total_time) as min,
							max(total_time) as max,
							count(*) as date_units
					from (
							select 	b.user_id,
									(EXTRACT('.$time_period.' FROM b.date_stamp) || \'-\' || EXTRACT(month FROM b.date_stamp) || \'-\' || EXTRACT(year FROM b.date_stamp) ) as date,
									a.type_id,
									a.status_id,
									over_time_policy_id,
									absence_policy_id,
									premium_policy_id,
									sum(total_time) as total_time
							from	'. $this->getTable() .' as a,
									'. $udf->getTable() .' as b,
									'. $uf->getTable() .' as c
							where 	a.user_date_id = b.id
								AND b.user_id = c.id
								AND c.company_id = ?
								AND b.date_stamp >= ?
								AND b.date_stamp <= ?
								AND b.user_id in ('. $this->getListSQL($user_ids, $ph) .')
								AND a.total_time > 0
								AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0)
							GROUP BY user_id,(EXTRACT('.$time_period.' FROM b.date_stamp) || \'-\' || EXTRACT(month FROM b.date_stamp) || \'-\' || EXTRACT(year FROM b.date_stamp) ),a.status_id,a.type_id,over_time_policy_id,absence_policy_id,premium_policy_id
						) tmp
					GROUP BY user_id,status_id,type_id,over_time_policy_id,absence_policy_id,premium_policy_id
					';
/*
		$query = '
					select 	user_id,
							status_id,
							type_id,
							over_time_policy_id,
							absence_policy_id,
							premium_policy_id,
							avg(total_time) as avg,
							min(total_time) as min,
							max(total_time) as max,
							count(*) as date_units
					from (

						select 	b.user_id,
								(date_part(\''.$time_period.'\', b.date_stamp) || \'-\' || date_part(\'month\', b.date_stamp) || \'-\' || date_part(\'year\', b.date_stamp) ) as date,
								a.type_id,
								a.status_id,
								over_time_policy_id,
								absence_policy_id,
								premium_policy_id,
								sum(total_time) as total_time
						from	'. $this->getTable() .' as a,
								'. $udf->getTable() .' as b,
								'. $uf->getTable() .' as c
						where 	a.user_date_id = b.id
							AND b.user_id = c.id
							AND c.company_id = ?
							AND b.date_stamp >= ?
							AND b.date_stamp <= ?
							AND b.user_id in ('. $this->getListSQL($user_ids, $ph) .')
							AND a.total_time > 0
							AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0)
							GROUP BY user_id,(date_part(\''. $time_period.'\', b.date_stamp) || \'-\' || date_part(\'month\', b.date_stamp) || \'-\' || date_part(\'year\', b.date_stamp) ),a.status_id,a.type_id,over_time_policy_id,absence_policy_id,premium_policy_id
						) tmp
					GROUP BY user_id,status_id,type_id,over_time_policy_id,absence_policy_id,premium_policy_id
					';
*/
		//$query .= $this->getWhereSQL( $where );
		//$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}
}
?>
