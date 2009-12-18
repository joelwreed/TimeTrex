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
 * $Id: PunchControlListFactory.class.php 2858 2009-09-29 18:12:05Z ipso $
 * $Date: 2009-09-29 11:12:05 -0700 (Tue, 29 Sep 2009) $
 */

/**
 * @package Module_Punch
 */
class PunchControlListFactory extends PunchControlFactory implements IteratorAggregate {

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
					'id' => (int)$id,
					);

		$this->rs = $this->getCache($id);
		if ( $this->rs === FALSE ) {

			$query = '
						select 	*
						from	'. $this->getTable() .'
						where	id = ?
							AND deleted = 0';
			$query .= $this->getWhereSQL( $where );
			$query .= $this->getSortSQL( $order );

			$this->rs = $this->db->Execute($query, $ph);

			$this->saveCache($this->rs,$id);
		}

		return $this;
	}

	function getByPunchId($punch_id, $order = NULL) {
		if ( $punch_id == '' ) {
			return FALSE;
		}

		$pf = new PunchFactory();

		$ph = array(
					'punch_id' => (int)$punch_id,
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $pf->getTable() .' as b
					where 	a.id = b.punch_control_id
						AND b.id = ?
						AND ( a.deleted = 0 AND b.deleted=0 )
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserDateId($user_date_id, $order = NULL) {
		if ( $user_date_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'user_date_id' => $user_date_id,
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					where
						a.user_date_id = ?
						AND ( a.deleted = 0 )
					';
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	//This function grabs all the punches on the given day
	//and determines where the epoch will fit in.
	function getInCompletePunchControlIdByUserIdAndEpoch( $user_id, $epoch, $status_id ) {
		Debug::text(' Epoch: '. TTDate::getDate('DATE+TIME', $epoch), __FILE__, __LINE__, __METHOD__,10);
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $epoch == '' ) {
			return FALSE;
		}

		$plf = new PunchListFactory();
		$plf->getShiftPunchesByUserIDAndEpoch( $user_id, $epoch );
		if ( $plf->getRecordCount() > 0 ) {
			//Check for gaps.
			$prev_time_stamp = 0;
			foreach( $plf as $p_obj) {
				if ( $p_obj->getStatus() == 10 ) {
					$punch_arr[$p_obj->getPunchControlId()]['in'] = $p_obj->getTimeStamp();
				} else {
					$punch_arr[$p_obj->getPunchControlId()]['out'] = $p_obj->getTimeStamp();
				}

				if ( $prev_time_stamp != 0 ) {
					$prev_punch_arr[$p_obj->getTimeStamp()] = $prev_time_stamp;
				}

				$prev_time_stamp = $p_obj->getTimeStamp();
			}
			unset($prev_time_stamp);

			if ( isset($prev_punch_arr) ) {
				$next_punch_arr = array_flip($prev_punch_arr);
			}

			//Debug::Arr( $punch_arr, ' Punch Array: ', __FILE__, __LINE__, __METHOD__,10);
			//Debug::Arr( $next_punch_arr, ' Next Punch Array: ', __FILE__, __LINE__, __METHOD__,10);

			if ( isset($punch_arr) ) {
				$i=0;
				foreach($punch_arr as $punch_control_id => $data ) {
					$found_gap = FALSE;
					Debug::text(' Iteration: '. $i, __FILE__, __LINE__, __METHOD__,10);

					//Skip complete punch control rows.
					if ( isset($data['in']) AND isset($data['out']) ) {
						Debug::text(' Punch Control ID is Complete: '. $punch_control_id, __FILE__, __LINE__, __METHOD__,10);
					} else {
						if ( $status_id == 10 AND !isset($data['in']) ) {
							Debug::text(' aFound Valid Gap...', __FILE__, __LINE__, __METHOD__,10);
							$found_gap = TRUE;
						} elseif ( $status_id == 20 AND !isset($data['out']) ) {
							Debug::text(' bFound Valid Gap...', __FILE__, __LINE__, __METHOD__,10);
							$found_gap = TRUE;
						} else {
							Debug::text(' No Valid Gap Found...', __FILE__, __LINE__, __METHOD__,10);
						}
					}

					if ( $found_gap == TRUE ) {
						if ( $status_id == 10 ) { //In Gap
							Debug::text(' In Gap...', __FILE__, __LINE__, __METHOD__,10);
							if ( isset($prev_punch_arr[$data['out']]) ) {
								Debug::text(' Punch Before In Gap... Range Start: '. TTDate::getDate('DATE+TIME', $prev_punch_arr[$data['out']]) .' End: '. TTDate::getDate('DATE+TIME', $data['out']), __FILE__, __LINE__, __METHOD__,10);
								if ( $prev_punch_arr[$data['out']] == $data['out'] OR TTDate::isTimeOverLap($epoch, $epoch, $prev_punch_arr[$data['out']], $data['out'] ) ) {
									Debug::text(' Epoch OverLaps, THIS IS GOOD!', __FILE__, __LINE__, __METHOD__,10);
									Debug::text(' aReturning Punch Control ID: '. $punch_control_id, __FILE__, __LINE__, __METHOD__,10);
									$retval = $punch_control_id;
									break; //Without this adding mass punches fails in some basic circumstances because it loops and attaches to a later punch control
								} else {
									Debug::text(' Epoch does not OverLaps, Cant attached to this punch_control!', __FILE__, __LINE__, __METHOD__,10);
								}

							} else {
								//No Punch After
								Debug::text(' NO Punch Before In Gap...', __FILE__, __LINE__, __METHOD__,10);
								$retval = $punch_control_id;
								break;
							}
						} else { //Out Gap
							Debug::text(' Out Gap...', __FILE__, __LINE__, __METHOD__,10);
							//Start: $data['in']
							//End: $data['in']
							if ( isset($next_punch_arr[$data['in']]) ) {
								Debug::text(' Punch After Out Gap... Range Start: '. TTDate::getDate('DATE+TIME', $data['in']) .' End: '. TTDate::getDate('DATE+TIME', $next_punch_arr[$data['in']]), __FILE__, __LINE__, __METHOD__,10);
								if ( $data['in'] == $next_punch_arr[$data['in']] OR TTDate::isTimeOverLap($epoch, $epoch, $data['in'], $next_punch_arr[$data['in']] ) ) {
									Debug::text(' Epoch OverLaps, THIS IS GOOD!', __FILE__, __LINE__, __METHOD__,10);
									Debug::text(' bReturning Punch Control ID: '. $punch_control_id, __FILE__, __LINE__, __METHOD__,10);
									$retval = $punch_control_id;
									break; //Without this adding mass punches fails in some basic circumstances because it loops and attaches to a later punch control
								} else {
									Debug::text(' Epoch does not OverLaps, Cant attached to this punch_control!', __FILE__, __LINE__, __METHOD__,10);
								}

							} else {
								//No Punch After
								Debug::text(' NO Punch After Out Gap...', __FILE__, __LINE__, __METHOD__,10);
								$retval = $punch_control_id;
								break;
							}
						}
					}
					$i++;
				}
			}
		}

		if ( isset($retval) ) {
			Debug::text(' Returning Punch Control ID: '. $retval, __FILE__, __LINE__, __METHOD__,10);
			return $retval;
		}

		Debug::text(' Returning FALSE No Valid Gaps Found...', __FILE__, __LINE__, __METHOD__,10);
		//FALSE means no gaps in punch control rows found.
		return FALSE;
	}

}
?>