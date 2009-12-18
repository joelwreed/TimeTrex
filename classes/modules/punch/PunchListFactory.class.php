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
 * $Revision: 2857 $
 * $Id: PunchListFactory.class.php 2857 2009-09-27 09:11:28Z ipso $
 * $Date: 2009-09-27 02:11:28 -0700 (Sun, 27 Sep 2009) $
 */

/**
 * @package Module_Punch
 */
class PunchListFactory extends PunchFactory implements IteratorAggregate {
	//Helper function get to maximum shift time from pay period schedule.
	function getPayPeriodMaximumShiftTime( $user_id, $maximum_shift_time = NULL ) {
		if ( !is_numeric( $maximum_shift_time) ) {
			//Get pay period start/maximum shift time time
			$ppslf = new PayPeriodScheduleListFactory();
			$ppslf->getByUserId( $user_id );
			if ( $ppslf->getRecordCount() == 1 ) {
				$pps_obj = $ppslf->getCurrent();
				$maximum_shift_time = $pps_obj->getMaximumShiftTime();
				//Debug::Text(' aPay Period Schedule Maximum Shift Time: '. $maximum_shift_time, __FILE__, __LINE__, __METHOD__,10);
			} else {
				//Debug::Text(' bPay Period Schedule Not Found! Using 4hrs as default', __FILE__, __LINE__, __METHOD__,10);
				$maximum_shift_time = 3600*16;
			}
		}
		Debug::Text(' cPay Period Schedule Maximum Shift Time: '. $maximum_shift_time, __FILE__, __LINE__, __METHOD__,10);

		return $maximum_shift_time;
	}

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

	function getByPunchControlId($id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'time_stamp' => 'desc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$ph = array(
					'id' => $id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .' as a
					where	punch_control_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict );

		$this->rs = $this->db->Execute($query, $ph);
	}

	function getByCompanyIDAndId($company_id, $id) {
		if ( $company_id == '' ) {
			return FALSE;
		}

		if ( $id == '' ) {
			return FALSE;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();
		$udf = new UserDateFactory();

		$ph = array(
					'company_id' => $company_id,
					);

		//Status sorting MUST be desc first, otherwise transfer punches are completely out of order.
		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c,
							'. $uf->getTable() .' as d
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = d.id
						AND d.company_id = ?
						AND a.id in ('. $this->getListSQL($id, $ph) .')
						AND ( a.deleted = 0 AND b.deleted = 0 AND c.deleted = 0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByPunchControlIdAndStatusId($punch_control_id, $status_id, $where = NULL, $order = NULL) {
		if ( $punch_control_id == '') {
			return FALSE;
		}

		if ( $status_id == '') {
			return FALSE;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'punch_control_id' => $punch_control_id,
					'status_id' => $status_id
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND	b.id = ?
						AND a.status_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateId($user_date_id, $order = NULL) {
		if ( $user_date_id == '') {
			return FALSE;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND	b.user_date_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndTypeId($user_date_id, $type_id, $order = NULL) {
		if ( $user_date_id == '') {
			return FALSE;
		}

		if ( $type_id == '') {
			return FALSE;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'type_id' => $type_id
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND	b.user_date_id = ?
						AND a.type_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.type_id, a.punch_control_id asc
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatusId($user_date_id, $status_id, $order = NULL) {
		if ( $user_date_id == '') {
			return FALSE;
		}

		if ( $status_id == '') {
			return FALSE;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status_id' => $status_id
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND	b.user_date_id = ?
						AND a.status_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndStatusIdAndTypeId($user_date_id, $status_id, $type_id, $order = NULL) {
		if ( $user_date_id == '') {
			return FALSE;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status_id' => $status_id,
					'type_id' => $type_id,
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND	b.user_date_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateIdAndNotPunchId($user_date_id, $punch_id, $order = NULL) {
		if ( $user_date_id == '') {
			return FALSE;
		}

		if ( $punch_id == '') {
			$punch_id = 0;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'punch_id' => $punch_id,
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND	b.user_date_id = ?
						AND a.id != ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getNextPunchByUserIdAndEpoch($user_id, $epoch, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $epoch == '') {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_id' => $user_id,
					'time_stamp' => $this->db->BindTimeStamp($epoch),
					);

		//Status order matters, because if its a.status_id desc, OUT comes first, but if the last
		//punch doesn't have OUT yet, it defaults to IN
		// with a.status_id asc...
		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = ?
						AND a.time_stamp >= ?
						AND ( a.deleted = 0 AND b.deleted = 0 AND c.deleted = 0)
					ORDER BY a.time_stamp asc
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getShiftPunchesByUserIDAndEpoch( $user_id, $epoch, $punch_control_id = 0, $maximum_shift_time = NULL ) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $epoch == '') {
			return FALSE;
		}

		if ( $maximum_shift_time == '' ) {
			$maximum_shift_time = $this->getPayPeriodMaximumShiftTime( $user_id, $maximum_shift_time );
		}

		//Make sure that we get all punches surrounding the EPOCH within the maximum shift time,
		//We also need to take into account punch pairs, for example:
		// Punch Pair: 10-Mar-09 @ 11:30PM -> 11-Mar-09 @ 2:30PM. If the maximum shift time ends at 11:45PM
		// we need to include the out punch as well.
		$start_time_stamp = ($epoch - $maximum_shift_time);
		$end_time_stamp = ($epoch + $maximum_shift_time);

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_id' => $user_id,
					'start_date_stamp' => $this->db->BindDate( $start_time_stamp ),
					'end_date_stamp' => $this->db->BindDate( $end_time_stamp ),
					'start_time_stamp2' => $this->db->BindTimeStamp( $start_time_stamp ),
					'end_time_stamp2' => $this->db->BindTimeStamp( $end_time_stamp ),
					);
/*
	ORIGINAL SLOW query. Moving OR into the sub-query.
 					select a.*
					from '. $this->getTable() .' as a
					where
						(
						a.punch_control_id = ?
						OR
						a.punch_control_id in (
													select 	distinct(x.punch_control_id)
													from 	'. $this->getTable() .' as x,
															'. $pcf->getTable() .' as y,
															'. $udf->getTable() .' as z
													where	x.punch_control_id = y.id
														AND y.user_date_id = z.id
														AND z.user_id = ?
														AND z.date_stamp >= ?
														AND z.date_stamp <= ?
														AND x.time_stamp >= ?
														AND x.time_stamp <= ?
														AND ( x.deleted = 0 AND y.deleted=0 AND z.deleted=0 )

												)
						)
					AND a.deleted = 0
					ORDER BY a.time_stamp asc, a.status_id asc

*/
		$query = '
					select a.*
					from '. $this->getTable() .' as a
					where
						(
						a.punch_control_id in (
													select '. (int)$punch_control_id .'
													UNION ALL
													select 	x.punch_control_id
													from 	'. $this->getTable() .' as x,
															'. $pcf->getTable() .' as y,
															'. $udf->getTable() .' as z
													where	x.punch_control_id = y.id
														AND y.user_date_id = z.id
														AND z.user_id = ?
														AND z.date_stamp >= ?
														AND z.date_stamp <= ?
														AND x.time_stamp >= ?
														AND x.time_stamp <= ?
														AND ( x.deleted = 0 AND y.deleted=0 AND z.deleted=0 )

												)
						)
					AND a.deleted = 0
					ORDER BY a.time_stamp asc, a.status_id asc
					';
		//$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getFirstPunchByUserIDAndEpoch( $user_id, $epoch, $maximum_shift_time = NULL ) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $epoch == '') {
			return FALSE;
		}

		if ( $maximum_shift_time == '' ) {
			$maximum_shift_time = $this->getPayPeriodMaximumShiftTime( $user_id, $maximum_shift_time );
		}

		$user_date_stamp = TTDate::getBeginDayEpoch( $epoch - $maximum_shift_time );

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_id' => $user_id,
					'user_date_stamp' => $this->db->BindDate($user_date_stamp),
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = ?
						AND c.date_stamp = ?
						AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0)
					ORDER BY a.time_stamp asc, a.status_id asc
					LIMIT 1
					';
		//$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPreviousPunchByUserIdAndEpoch($user_id, $epoch, $order = NULL) {
		Debug::Text(' User ID: '. $user_id .' Epoch: '. $epoch, __FILE__, __LINE__, __METHOD__,10);
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $epoch == '') {
			return FALSE;
		}

		$maximum_shift_time = $this->getPayPeriodMaximumShiftTime( $user_id );

		$begin_day_epoch = TTDate::getBeginDayEpoch( $epoch );
		$start_time = $epoch - $maximum_shift_time;

		Debug::Text(' Start Time: '. TTDate::getDate('DATE+TIME', $start_time ), __FILE__, __LINE__, __METHOD__,10);

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_id' => $user_id,
					'start_time' => $this->db->BindTimeStamp($start_time),
					'end_time' => $this->db->BindTimeStamp($epoch),
					);

		//Status order matters, because if its a.status_id desc, OUT comes first, but if the last
		//punch doesn't have OUT yet, it defaults to IN
		// with a.status_id asc...
		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = ?
						AND a.time_stamp >= ?
						AND a.time_stamp <= ?
						AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0)
					ORDER BY a.time_stamp desc, a.status_id asc
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );
		//Debug::Text(' Query: '. $query, __FILE__, __LINE__, __METHOD__,10);
		//echo "Get Previous Punch Query: ". $query ."<br>\n";

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPreviousPunchByUserIdAndEpochAndNotPunchIDAndMaximumShiftTime($user_id, $epoch, $punch_id, $maximum_shift_time = NULL, $order = NULL) {
		Debug::Text(' User ID: '. $user_id .' Epoch: '. $epoch, __FILE__, __LINE__, __METHOD__,10);
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $epoch == '' OR $epoch <= 0 ) {
			return FALSE;
		}

		$maximum_shift_time = $this->getPayPeriodMaximumShiftTime( $user_id, $maximum_shift_time );
		$start_time = $epoch - $maximum_shift_time;

		Debug::Text(' Start Time: '. TTDate::getDate('DATE+TIME', $start_time ), __FILE__, __LINE__, __METHOD__,10);

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_id' => $user_id,
					'start_time' => $this->db->BindTimeStamp($start_time),
					'end_time' => $this->db->BindTimeStamp($epoch),
					'punch_id' => (int)$punch_id,
					);

		//Status order matters, because if its a.status_id desc, OUT comes first, but if the last
		//punch doesn't have OUT yet, it defaults to IN
		// with a.status_id asc...
		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = ?
						AND a.time_stamp >= ?
						AND a.time_stamp <= ?
						AND a.id != ?
						AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0)
					ORDER BY a.time_stamp desc, a.status_id asc
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );
		//Debug::Text(' Query: '. $query, __FILE__, __LINE__, __METHOD__,10);

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPreviousPunchByUserDateIdAndStatusAndTypeAndEpoch($user_date_id, $status_id, $type_id, $epoch, $order = NULL) {
		if ( $user_date_id == '') {
			return FALSE;
		}

		if ( $status_id == '') {
			return FALSE;
		}

		if ( $type_id == '') {
			return FALSE;
		}

		if ( $epoch == '') {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_date_id' => $user_date_id,
					'status_id' => $status_id,
					'type_id' => $type_id,
					'time_stamp' => $this->db->BindTimeStamp($epoch),
					);

		//Status order matters, because if its a.status_id desc, OUT comes first, but if the last
		//punch doesn't have OUT yet, it defaults to IN
		// with a.status_id asc...
		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND b.user_date_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND a.time_stamp <= ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp desc
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPreviousPunchByUserIdAndStatusAndTypeAndEpoch($user_id, $status_id, $type_id, $epoch, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $status_id == '') {
			return FALSE;
		}

		if ( $type_id == '') {
			return FALSE;
		}

		if ( $epoch == '') {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'user_id' => $user_id,
					'status_id' => $status_id,
					'type_id' => $type_id,
					'time_stamp' => $this->db->BindTimeStamp($epoch),
					);

		//Status order matters, because if its a.status_id desc, OUT comes first, but if the last
		//punch doesn't have OUT yet, it defaults to IN
		// with a.status_id asc...
		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = ?
						AND a.status_id = ?
						AND a.type_id = ?
						AND a.time_stamp <= ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp desc
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPreviousPunchByPunchId($punch_id, $order = NULL) {
		if ( $punch_id == '') {
			return FALSE;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'punch_id' => $punch_id,
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND	b.user_date_id = (select z.user_date_id from '. $pcf->getTable() .' as z where z.id = b.punch_control_id)
						AND a.id = ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPreviousPunchByPunchControlId($punch_control_id, $order = NULL) {
		if ( $punch_control_id == '') {
			return FALSE;
		}

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'punch_control_id' => $punch_control_id,
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b
					where	a.punch_control_id = b.id
						AND b.id = ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyIDAndUserIdAndStartDateAndEndDate($company_id, $user_id, $start_date, $end_date) {
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

		$uf = new UserFactory();
		$pcf = new PunchControlFactory();
		$udf = new UserDateFactory();

		$ph = array(
					'company_id' => $company_id,
					'user_id' => $user_id,
					'start_date' => $this->db->BindDate( $start_date ),
					'end_date' => $this->db->BindDate( $end_date )
					);

		//Status sorting MUST be desc first, otherwise transfer punches are completely out of order.
		$query = '
					select 	a.*,
							c.date_stamp as user_date_stamp,
							b.note as note
					from	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c,
							'. $uf->getTable() .' as d
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = d.id
						AND d.company_id = ?
						AND	c.user_id = ?
						AND c.date_stamp >= ?
						AND c.date_stamp <= ?
						AND ( a.deleted = 0 AND b.deleted = 0 AND c.deleted = 0 )
					ORDER BY a.time_stamp asc, a.status_id desc, a.punch_control_id asc
					';

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getPreviousPunchByCompanyId($company_id, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();
		$uf = new UserFactory();

		$ph = array(
					'company_id' => $company_id,
					);

		//Status order matters, because if its a.status_id desc, OUT comes first, but if the last
		//punch doesn't have OUT yet, it defaults to IN
		// with a.status_id asc...
		$query = '
					select 	c.user_id as user_id,

					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c,
							'. $uf->getTable() .' as d
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = d.id
						AND d.company_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0)
					GROUP BY c.user_id
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getLastPunchByUserId($user_id, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();
		$uf = new UserFactory();

		$ph = array(
					'user_id' => $user_id,
					);

		//Status order matters, because if its a.status_id desc, OUT comes first, but if the last
		//punch doesn't have OUT yet, it defaults to IN
		// with a.status_id asc...
		$query = '
					select 	a.*

					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c,
							'. $uf->getTable() .' as d
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id = d.id
						AND d.id = ?
						AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0)
					ORDER BY a.time_stamp desc
					LIMIT 1
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByPayPeriodId($pay_period_id, $order = NULL) {
		if ( $pay_period_id == '') {
			return FALSE;
		}

		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array(
					'pay_period_id' => $pay_period_id,
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c
					where	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.pay_period_id = ?
						AND ( a.deleted = 0 AND b.deleted=0 AND c.deleted=0)
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}
/*
	function getReportByPayPeriodIDListAndUserIdListAndBranchIdAndDepartmentId($pay_period_ids, $user_ids, $branch_id, $department_id, $order = NULL) {
		if ( $user_ids == '') {
			return FALSE;
		}

		if ( $pay_period_ids == '') {
			return FALSE;
		}

		if ( $branch_id == '') {
			return FALSE;
		}

		if ( $department_id == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'c.pay_period_id' => 'asc','c.user_id' => 'asc', 'a.time_stamp' => 'asc', 'a.punch_control_id' => 'asc', 'a.status_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();

		$ph = array();

		$query = '
					select
							a.id as punch_id,
							a.punch_control_id as punch_control_id,
							a.type_id as type_id,
							a.status_id as status_id,
							a.time_stamp as punch_time_stamp,
							a.actual_time_stamp as punch_actual_time_stamp,

							b.user_date_id as user_date_id,
							b.branch_id as branch_id,
							b.department_id as department_id,
							b.job_id as job_id,
							b.job_item_id as job_item_id,
							b.quantity as quantity_id,
							b.bad_quantity as bad_quantity_id,
							b.total_time as total_time,
							b.actual_total_time as actual_total_time,
							b.meal_policy_id as meal_policy_id,
							b.other_id1 as other_id1,
							b.other_id2 as other_id2,
							b.other_id3 as other_id3,
							b.other_id4 as other_id4,
							b.other_id5 as other_id5,
							b.note as note,

							c.user_id as user_id,
							c.pay_period_id as pay_period_id
					from 	'. $this->getTable() .' as a,
							'. $pcf->getTable() .' as b,
							'. $udf->getTable() .' as c
					where 	a.punch_control_id = b.id
						AND b.user_date_id = c.id
						AND c.user_id in ('. $this->getListSQL($user_ids, $ph) .')
					';

		if ( $branch_id != '' AND isset($branch_id[0]) AND !in_array(-1, $branch_id) ) {
			$query .= ' AND b.branch_id in ('. $this->getListSQL($branch_id, $ph) .') ';
		}
		if ( $department_id != '' AND isset($department_id[0]) AND !in_array(-1, $department_id) ) {
			$query .= ' AND b.department_id in ('. $this->getListSQL($department_id, $ph) .') ';
		}
		if ( $pay_period_ids != '' AND isset($pay_period_ids[0]) AND !in_array(-1, (array)$pay_period_ids) ) {
			$query .= ' AND c.pay_period_id in ('. $this->getListSQL($pay_period_ids, $ph) .') ';
		}

		$query .= '
					AND (a.deleted = 0 AND b.deleted =0 AND c.deleted = 0)
				';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}
*/
	function getReportByPayPeriodIDListAndUserIdListAndBranchIdAndDepartmentIdAndJobIdListAndJobItemIdList($pay_period_ids, $user_ids, $branch_id, $department_id, $job_ids, $job_item_ids, $order = NULL) {
		if ( $user_ids == '') {
			return FALSE;
		}

		if ( $pay_period_ids == '') {
			return FALSE;
		}

		if ( $branch_id == '') {
			return FALSE;
		}

		if ( $department_id == '') {
			return FALSE;
		}

		if ( $job_ids == '') {
			return FALSE;
		}

		if ( $job_item_ids == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'c.pay_period_id' => 'asc','c.user_id' => 'asc', 'a.time_stamp' => 'asc', 'a.punch_control_id' => 'asc', 'a.status_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();
		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$jf = new JobFactory();
		}

		$ph = array();

		$query = '
					select
							a.id as punch_id,
							a.punch_control_id as punch_control_id,
							a.type_id as type_id,
							a.status_id as status_id,
							a.time_stamp as punch_time_stamp,
							a.actual_time_stamp as punch_actual_time_stamp,

							b.user_date_id as user_date_id,
							b.branch_id as branch_id,
							b.department_id as department_id,
							b.job_id as job_id,
							b.job_item_id as job_item_id,
							b.quantity as quantity_id,
							b.bad_quantity as bad_quantity_id,
							b.total_time as total_time,
							b.actual_total_time as actual_total_time,
							b.meal_policy_id as meal_policy_id,
							b.other_id1 as other_id1,
							b.other_id2 as other_id2,
							b.other_id3 as other_id3,
							b.other_id4 as other_id4,
							b.other_id5 as other_id5,
							b.note as note,

							c.user_id as user_id,
							c.pay_period_id as pay_period_id
					from 	'. $this->getTable() .' as a
							LEFT JOIN '. $pcf->getTable() .' as b ON a.punch_control_id = b.id
							LEFT JOIN '. $udf->getTable() .' as c ON a.user_date_id = c.id
					';
		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$query .= '	LEFT JOIN '. $jf->getTable() .' as d ON b.job_id = d.id';
		}

		$query .= '
					where c.user_id in ('. $this->getListSQL($user_ids, $ph) .')
					';

		if ( $branch_id != '' AND isset($branch_id[0]) AND !in_array(-1, $branch_id) ) {
			$query .= ' AND b.branch_id in ('. $this->getListSQL($branch_id, $ph) .') ';
		}
		if ( $department_id != '' AND isset($department_id[0]) AND !in_array(-1, $department_id) ) {
			$query .= ' AND b.department_id in ('. $this->getListSQL($department_id, $ph) .') ';
		}
		if ( $pay_period_ids != '' AND isset($pay_period_ids[0]) AND !in_array(-1, (array)$pay_period_ids) ) {
			$query .= ' AND c.pay_period_id in ('. $this->getListSQL($pay_period_ids, $ph) .') ';
		}
		if ( $job_ids != '' AND isset($job_ids[0]) AND !in_array(-1, (array)$job_ids) ) {
			$query .= ' AND b.job_id in ('. $this->getListSQL($job_ids, $ph) .') ';
		}
		if ( $job_item_ids != '' AND isset($job_item_ids[0]) AND !in_array(-1, (array)$job_item_ids) ) {
			$query .= ' AND b.job_item_id in ('. $this->getListSQL($job_item_ids, $ph) .') ';
		}

		$query .= '
					AND (a.deleted = 0 AND b.deleted =0 AND c.deleted = 0)
				';

		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getReportByStartDateAndEndDateAndUserIdListAndBranchIdAndDepartmentIdAndJobIdListAndJobItemIdList($start_date, $end_date, $user_ids, $branch_id, $department_id, $job_ids, $job_item_ids, $order = NULL) {
		if ( $start_date == '') {
			return FALSE;
		}
		if ( $end_date == '') {
			return FALSE;
		}

		if ( $user_ids == '') {
			return FALSE;
		}

		if ( $branch_id == '') {
			return FALSE;
		}

		if ( $department_id == '') {
			return FALSE;
		}

		if ( $job_ids == '') {
			return FALSE;
		}

		if ( $job_item_ids == '') {
			return FALSE;
		}

		if ( $order == NULL ) {
			$order = array( 'c.pay_period_id' => 'asc','c.user_id' => 'asc', 'a.time_stamp' => 'asc', 'a.punch_control_id' => 'asc', 'a.status_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}

		$ulf = new UserListFactory();
		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();
		$uwf = new UserWageFactory();

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$jf = new JobFactory();
		}

		$ph = array(
					'start_date' => $this->db->BindDate($start_date),
					'end_date' => $this->db->BindDate($end_date),
					);

		$query = '
					select
							a.id as punch_id,
							a.punch_control_id as punch_control_id,
							a.type_id as type_id,
							a.status_id as status_id,
							a.time_stamp as punch_time_stamp,
							a.actual_time_stamp as punch_actual_time_stamp,

							b.user_date_id as user_date_id,
							b.branch_id as branch_id,
							b.department_id as department_id,
							b.job_id as job_id,
							b.job_item_id as job_item_id,
							b.quantity as quantity_id,
							b.bad_quantity as bad_quantity_id,
							b.total_time as total_time,
							b.actual_total_time as actual_total_time,
							b.meal_policy_id as meal_policy_id,
							b.other_id1 as other_id1,
							b.other_id2 as other_id2,
							b.other_id3 as other_id3,
							b.other_id4 as other_id4,
							b.other_id5 as other_id5,
							b.note as note,

							c.user_id as user_id,
							c.pay_period_id as pay_period_id,

							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date

					from 	'. $this->getTable() .' as a
							LEFT JOIN '. $pcf->getTable() .' as b ON a.punch_control_id = b.id
							LEFT JOIN '. $udf->getTable() .' as c ON b.user_date_id = c.id
							LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = c.user_id
																			and z.effective_date <= c.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc limit 1)
					';
		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$query .= '	LEFT JOIN '. $jf->getTable() .' as d ON b.job_id = d.id';
		}

		$query .= '
					where c.date_stamp >= ?
						AND c.date_stamp <= ?
					';
		if ( $user_ids != '' AND isset($user_ids[0]) AND !in_array(-1, $user_ids) ) {
			$query .= ' AND c.user_id in ('. $this->getListSQL($user_ids, $ph) .') ';
		}
		if ( $branch_id != '' AND isset($branch_id[0]) AND !in_array(-1, $branch_id) ) {
			$query .= ' AND b.branch_id in ('. $this->getListSQL($branch_id, $ph) .') ';
		}
		if ( $department_id != '' AND isset($department_id[0]) AND !in_array(-1, $department_id) ) {
			$query .= ' AND b.department_id in ('. $this->getListSQL($department_id, $ph) .') ';
		}
		if ( $job_ids != '' AND isset($job_ids[0]) AND !in_array(-1, (array)$job_ids) ) {
			$query .= ' AND b.job_id in ('. $this->getListSQL($job_ids, $ph) .') ';
		}
		if ( $job_item_ids != '' AND isset($job_item_ids[0]) AND !in_array(-1, (array)$job_item_ids) ) {
			$query .= ' AND b.job_item_id in ('. $this->getListSQL($job_item_ids, $ph) .') ';
		}

		$query .= '
					AND (a.deleted = 0 AND b.deleted =0 AND c.deleted = 0)
				';

		$query .= $this->getSortSQL( $order, FALSE );

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

		//$additional_order_fields = array('b.name', 'c.name', 'd.name', 'e.name');
		$additional_order_fields = array('first_name', 'last_name', 'date_stamp','time_stamp','type_id','status_id','branch','department','default_branch','default_department','group','title');
		if ( $order == NULL ) {
			$order = array( 'c.pay_period_id' => 'asc','c.user_id' => 'asc', 'a.time_stamp' => 'asc', 'a.punch_control_id' => 'asc', 'a.status_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = FALSE;
		}
		//Debug::Arr($order,'Order Data:', __FILE__, __LINE__, __METHOD__,10);
		//Debug::Arr($filter_data,'Filter Data:', __FILE__, __LINE__, __METHOD__,10);

		if ( isset($filter_data['exclude_user_ids']) ) {
			$filter_data['exclude_id'] = $filter_data['exclude_user_ids'];
		}
		if ( isset($filter_data['user_id']) ) {
			$filter_data['id'] = $filter_data['user_id'];
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
		if ( isset($filter_data['branch_ids']) ) {
			$filter_data['default_branch_id'] = $filter_data['branch_ids'];
		}
		if ( isset($filter_data['department_ids']) ) {
			$filter_data['default_department_id'] = $filter_data['department_ids'];
		}
		if ( isset($filter_data['punch_branch_ids']) ) {
			$filter_data['punch_branch_id'] = $filter_data['punch_branch_ids'];
		}
		if ( isset($filter_data['punch_department_ids']) ) {
			$filter_data['punch_department_id'] = $filter_data['punch_department_ids'];
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

		$uf = new UserFactory();
		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();
		$uwf = new UserWageFactory();
		$sf = new StationFactory();

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$jf = new JobFactory();
			$jif = new JobItemFactory();
		}

		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select
							a.id as punch_id,
							a.punch_control_id as punch_control_id,
							a.type_id as type_id,
							a.status_id as status_id,
							a.time_stamp as time_stamp,
							a.actual_time_stamp as actual_time_stamp,

							b.user_date_id as user_date_id,
							b.branch_id as branch_id,
							b.department_id as department_id,
							b.job_id as job_id,
							b.job_item_id as job_item_id,
							b.quantity as quantity_id,
							b.bad_quantity as bad_quantity_id,
							b.total_time as total_time,
							b.actual_total_time as actual_total_time,
							b.meal_policy_id as meal_policy_id,
							b.other_id1 as other_id1,
							b.other_id2 as other_id2,
							b.other_id3 as other_id3,
							b.other_id4 as other_id4,
							b.other_id5 as other_id5,
							b.note as note,

							c.user_id as user_id,
							c.date_stamp as date_stamp,
							c.pay_period_id as pay_period_id,

							e.type_id as station_type_id,
							e.station_id as station_station_id,
							e.source as station_source,
							e.description as station_description,

							z.id as user_wage_id,
							z.effective_date as user_wage_effective_date ';

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$query .= ',
						x.name as job_name,
						x.status_id as job_status_id,
						x.manual_id as job_manual_id,
						x.branch_id as job_branch_id,
						x.department_id as job_department_id,
						x.group_id as job_group_id';
		}

		$query .= '
					from 	'. $this->getTable() .' as a
							LEFT JOIN '. $pcf->getTable() .' as b ON a.punch_control_id = b.id
							LEFT JOIN '. $udf->getTable() .' as c ON b.user_date_id = c.id
							LEFT JOIN '. $uf->getTable() .' as d ON c.user_id = d.id
							LEFT JOIN '. $sf->getTable() .' as e ON a.station_id = e.id
							LEFT JOIN '. $uwf->getTable() .' as z ON z.id = (select z.id
																		from '. $uwf->getTable() .' as z
																		where z.user_id = c.user_id
																			and z.effective_date <= c.date_stamp
																			and z.deleted = 0
																			order by z.effective_date desc LiMiT 1)
					';
		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$query .= '	LEFT JOIN '. $jf->getTable() .' as x ON b.job_id = x.id';
			$query .= '	LEFT JOIN '. $jif->getTable() .' as y ON b.job_item_id = y.id';
		}

		$query .= '	WHERE d.company_id = ?';

		if ( isset($filter_data['permission_children_ids']) AND isset($filter_data['permission_children_ids'][0]) AND !in_array(-1, (array)$filter_data['permission_children_ids']) ) {
			$query  .=	' AND d.id in ('. $this->getListSQL($filter_data['permission_children_ids'], $ph) .') ';
		}
		if ( isset($filter_data['id']) AND isset($filter_data['id'][0]) AND !in_array(-1, (array)$filter_data['id']) ) {
			$query  .=	' AND d.id in ('. $this->getListSQL($filter_data['id'], $ph) .') ';
		}
		if ( isset($filter_data['exclude_id']) AND isset($filter_data['exclude_id'][0]) AND !in_array(-1, (array)$filter_data['exclude_id']) ) {
			$query  .=	' AND d.id not in ('. $this->getListSQL($filter_data['exclude_id'], $ph) .') ';
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
		if ( isset($filter_data['punch_branch_id']) AND isset($filter_data['punch_branch_id'][0]) AND !in_array(-1, (array)$filter_data['punch_branch_id']) ) {
			$query  .=	' AND b.branch_id in ('. $this->getListSQL($filter_data['punch_branch_id'], $ph) .') ';
		}
		if ( isset($filter_data['punch_department_id']) AND isset($filter_data['punch_department_id'][0]) AND !in_array(-1, (array)$filter_data['punch_department_id']) ) {
			$query  .=	' AND b.department_id in ('. $this->getListSQL($filter_data['punch_department_id'], $ph) .') ';
		}
		if ( isset($filter_data['punch_status_id']) AND isset($filter_data['punch_status_id'][0]) AND !in_array(-1, (array)$filter_data['punch_status_id']) ) {
			$query  .=	' AND a.status_id in ('. $this->getListSQL($filter_data['punch_status_id'], $ph) .') ';
		}
		if ( isset($filter_data['punch_type_id']) AND isset($filter_data['punch_type_id'][0]) AND !in_array(-1, (array)$filter_data['punch_type_id']) ) {
			$query  .=	' AND a.type_id in ('. $this->getListSQL($filter_data['punch_type_id'], $ph) .') ';
		}
		if ( isset($filter_data['pay_period_ids']) AND isset($filter_data['pay_period_ids'][0]) AND !in_array(-1, (array)$filter_data['pay_period_ids']) ) {
			$query .= 	' AND c.pay_period_id in ('. $this->getListSQL($filter_data['pay_period_ids'], $ph) .') ';
		}


		//Use the job_id in the punch_control table so we can filter by '0' or No Job
		if ( isset($filter_data['include_job_id']) AND isset($filter_data['include_job_id'][0]) AND !in_array(-1, (array)$filter_data['include_job_id']) ) {
			$query  .=	' AND b.job_id in ('. $this->getListSQL($filter_data['include_job_id'], $ph) .') ';
		}
		if ( isset($filter_data['exclude_job_id']) AND isset($filter_data['exclude_job_id'][0]) AND !in_array(-1, (array)$filter_data['exclude_job_id']) ) {
			$query  .=	' AND b.job_id not in ('. $this->getListSQL($filter_data['exclude_job_id'], $ph) .') ';
		}
		if ( isset($filter_data['job_group_id']) AND isset($filter_data['job_group_id'][0]) AND !in_array(-1, (array)$filter_data['job_group_id']) ) {
			if ( isset($filter_data['include_job_subgroups']) AND (bool)$filter_data['include_job_subgroups'] == TRUE ) {
				$uglf = new UserGroupListFactory();
				$filter_data['job_group_id'] = $uglf->getByCompanyIdAndGroupIdAndjob_subgroupsArray( $company_id, $filter_data['job_group_id'], TRUE);
			}
			$query  .=	' AND x.group_id in ('. $this->getListSQL($filter_data['job_group_id'], $ph) .') ';
		}

		if ( isset($filter_data['job_item_id']) AND isset($filter_data['job_item_id'][0]) AND !in_array(-1, (array)$filter_data['job_item_id']) ) {
			$query  .=	' AND b.job_item_id in ('. $this->getListSQL($filter_data['job_item_id'], $ph) .') ';
		}


		if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
			$ph[] = $this->db->BindDate($filter_data['start_date']);
			$query  .=	' AND c.date_stamp >= ?';
		}
		if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
			$ph[] = $this->db->BindDate($filter_data['end_date']);
			$query  .=	' AND c.date_stamp <= ?';
		}

		$query .= 	'
						AND (a.deleted = 0 AND b.deleted = 0 AND c.deleted = 0 AND d.deleted = 0)
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

	function getLastPunchByCompanyIdAndArrayCriteria( $company_id, $filter_data, $limit = NULL, $page = NULL, $where = NULL, $order = NULL ) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( !is_array($order) ) {
			//Use Filter Data ordering if its set.
			if ( isset($filter_data['sort_column']) AND $filter_data['sort_order']) {
				$order = array(Misc::trimSortPrefix($filter_data['sort_column']) => $filter_data['sort_order']);
			}
		}

		//$additional_order_fields = array('b.name', 'c.name', 'd.name', 'e.name');
		$additional_order_fields = array('b.branch_id', 'c.date_stamp', 'd.last_name', 'a.time_stamp', 'a.status_id', 'b.branch_id', 'b.department_id', 'e.type_id');
		if ( $order == NULL ) {
			$order = array( 'b.branch_id' => 'asc', 'd.last_name' => 'asc', 'a.time_stamp' => 'desc', 'a.punch_control_id' => 'asc', 'a.status_id' => 'asc' );
			$strict = FALSE;
		} else {
			$strict = TRUE;
		}
		Debug::Arr($order,'Order Data:', __FILE__, __LINE__, __METHOD__,10);
		Debug::Arr($filter_data,'Filter Data:', __FILE__, __LINE__, __METHOD__,10);

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
		if ( isset($filter_data['branch_ids']) ) {
			$filter_data['default_branch_id'] = $filter_data['branch_ids'];
		}
		if ( isset($filter_data['department_ids']) ) {
			$filter_data['default_department_id'] = $filter_data['department_ids'];
		}
		if ( isset($filter_data['punch_branch_ids']) ) {
			$filter_data['punch_branch_id'] = $filter_data['punch_branch_ids'];
		}
		if ( isset($filter_data['punch_department_ids']) ) {
			$filter_data['punch_department_id'] = $filter_data['punch_department_ids'];
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

		$uf = new UserFactory();
		$udf = new UserDateFactory();
		$pcf = new PunchControlFactory();
		$sf = new StationFactory();

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$jf = new JobFactory();
			$jif = new JobItemFactory();
		}

		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select
							a.id as punch_id,
							a.punch_control_id as punch_control_id,
							a.type_id as type_id,
							a.status_id as status_id,
							a.time_stamp as time_stamp,
							a.actual_time_stamp as actual_time_stamp,

							b.user_date_id as user_date_id,
							b.branch_id as branch_id,
							b.department_id as department_id,
							b.job_id as job_id,
							b.job_item_id as job_item_id,
							b.note as note,

							c.user_id as user_id,

							e.type_id as station_type_id,
							e.station_id as station_station_id,
							e.source as station_source,
							e.description as station_description

					from 	'. $this->getTable() .' as a
							LEFT JOIN '. $pcf->getTable() .' as b ON a.punch_control_id = b.id
							LEFT JOIN '. $udf->getTable() .' as c ON b.user_date_id = c.id
							LEFT JOIN '. $uf->getTable() .' as d ON c.user_id = d.id
							LEFT JOIN '. $sf->getTable() .' as e ON a.station_id = e.id
							LEFT JOIN (
								select tmp2_d.id, max(tmp2_a.time_stamp) as max_punch_time_stamp
									from 	'. $this->getTable() .' as tmp2_a
									LEFT JOIN '. $pcf->getTable() .' as tmp2_b ON tmp2_a.punch_control_id = tmp2_b.id
									LEFT JOIN '. $udf->getTable() .' as tmp2_c ON tmp2_b.user_date_id = tmp2_c.id
									LEFT JOIN '. $uf->getTable() .' as tmp2_d ON tmp2_c.user_id = tmp2_d.id
									WHERE tmp2_d.company_id = ?';

									if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
										$ph[] = $this->db->BindDate($filter_data['start_date']);
										$query  .=	' AND tmp2_c.date_stamp >= ?';
									}
									if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
										$ph[] = $this->db->BindDate($filter_data['end_date']);
										$query  .=	' AND tmp2_c.date_stamp <= ?';
									}

									$query .= '
										AND tmp2_a.time_stamp is not null
										AND ( tmp2_a.deleted = 0 AND tmp2_b.deleted = 0 AND tmp2_c.deleted = 0 )
									group by tmp2_d.id
							) as tmp2 ON c.user_id = tmp2.id AND a.time_stamp = tmp2.max_punch_time_stamp

					';

		if ( getTTProductEdition() == TT_PRODUCT_PROFESSIONAL ) {
			$query .= '	LEFT JOIN '. $jf->getTable() .' as x ON b.job_id = x.id';
			$query .= '	LEFT JOIN '. $jif->getTable() .' as y ON b.job_item_id = y.id';
		}

		$ph[] = $company_id;
		$query .= '	WHERE tmp2.id IS NOT NULL AND d.company_id = ?';

		if ( isset($filter_data['permission_children_ids']) AND isset($filter_data['permission_children_ids'][0]) AND !in_array(-1, (array)$filter_data['permission_children_ids']) ) {
			$query  .=	' AND d.id in ('. $this->getListSQL($filter_data['permission_children_ids'], $ph) .') ';
		}
		if ( isset($filter_data['id']) AND isset($filter_data['id'][0]) AND !in_array(-1, (array)$filter_data['id']) ) {
			$query  .=	' AND d.id in ('. $this->getListSQL($filter_data['id'], $ph) .') ';
		}
		if ( isset($filter_data['exclude_id']) AND isset($filter_data['exclude_id'][0]) AND !in_array(-1, (array)$filter_data['exclude_id']) ) {
			$query  .=	' AND d.id not in ('. $this->getListSQL($filter_data['exclude_id'], $ph) .') ';
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
		if ( isset($filter_data['punch_branch_id']) AND isset($filter_data['punch_branch_id'][0]) AND !in_array(-1, (array)$filter_data['punch_branch_id']) ) {
			$query  .=	' AND b.branch_id in ('. $this->getListSQL($filter_data['punch_branch_id'], $ph) .') ';
		}
		if ( isset($filter_data['punch_department_id']) AND isset($filter_data['punch_department_id'][0]) AND !in_array(-1, (array)$filter_data['punch_department_id']) ) {
			$query  .=	' AND b.department_id in ('. $this->getListSQL($filter_data['punch_department_id'], $ph) .') ';
		}

		//Use the job_id in the punch_control table so we can filter by '0' or No Job
		if ( isset($filter_data['include_job_id']) AND isset($filter_data['include_job_id'][0]) AND !in_array(-1, (array)$filter_data['include_job_id']) ) {
			$query  .=	' AND b.job_id in ('. $this->getListSQL($filter_data['include_job_id'], $ph) .') ';
		}
		if ( isset($filter_data['exclude_job_id']) AND isset($filter_data['exclude_job_id'][0]) AND !in_array(-1, (array)$filter_data['exclude_job_id']) ) {
			$query  .=	' AND b.job_id not in ('. $this->getListSQL($filter_data['exclude_job_id'], $ph) .') ';
		}
		if ( isset($filter_data['job_group_id']) AND isset($filter_data['job_group_id'][0]) AND !in_array(-1, (array)$filter_data['job_group_id']) ) {
			if ( isset($filter_data['include_job_subgroups']) AND (bool)$filter_data['include_job_subgroups'] == TRUE ) {
				$uglf = new UserGroupListFactory();
				$filter_data['job_group_id'] = $uglf->getByCompanyIdAndGroupIdAndjob_subgroupsArray( $company_id, $filter_data['job_group_id'], TRUE);
			}
			$query  .=	' AND x.group_id in ('. $this->getListSQL($filter_data['job_group_id'], $ph) .') ';
		}

		if ( isset($filter_data['job_item_id']) AND isset($filter_data['job_item_id'][0]) AND !in_array(-1, (array)$filter_data['job_item_id']) ) {
			$query  .=	' AND b.job_item_id in ('. $this->getListSQL($filter_data['job_item_id'], $ph) .') ';
		}

		if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
			/*
			$ph[] = $this->db->BindDate($filter_data['start_date']);
			$query  .=	' AND c.date_stamp >= ?';
			*/
			$ph[] = $this->db->BindTimeStamp($filter_data['start_date']);
			$query  .=	' AND a.time_stamp >= ?';
		}
		if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
			$ph[] = $this->db->BindTimeStamp($filter_data['end_date']);
			$query  .=	' AND a.time_stamp <= ?';
		}

		//The Transfer where clause is an attempt to keep transferred punches from appearing twice.
		$query .= 	'
						AND ( a.transfer = 0 OR ( a.transfer = 1 AND a.status_id = 10) )
						AND ( a.deleted = 0 AND b.deleted =0 AND c.deleted = 0 AND d.deleted = 0 )
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

}
?>
