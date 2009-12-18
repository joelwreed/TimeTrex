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
 * $Id: AccrualListFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Module_Accrual
 */
class AccrualListFactory extends AccrualFactory implements IteratorAggregate {

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
					'id' => $id,
					'company_id' => $company_id
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	id = ?
						AND company_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserIdAndCompanyId($user_id, $company_id, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $company_id == '') {
			return FALSE;
		}

		$ph = array(
					'user_id' => $user_id,
					'company_id' => $company_id
					);

		$uf = new UserFactory();

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
						LEFT JOIN '. $uf->getTable() .' as b ON a.user_id = b.id
					where	a.user_id = ?
						AND b.company_id = ?
						AND a.deleted = 0';

		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyIdAndUserIdAndAccrualPolicyID($company_id, $user_id, $accrual_policy_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( $user_id == '') {
			return FALSE;
		}

		if ( $accrual_policy_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array('d.date_stamp' => 'desc', 'a.time_stamp' => 'desc');
			$strict_order = FALSE;
		}

		$uf = new UserFactory();
		$udtf = new UserDateTotalFactory();
		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					'company_id' => $company_id,
					'accrual_policy_id' => $accrual_policy_id,
					);

		$query = '
					select 	a.*,
							d.date_stamp as date_stamp
					from	'. $this->getTable() .' as a
							LEFT JOIN '. $uf->getTable() .' as b ON a.user_id = b.id
							LEFT JOIN '. $udtf->getTable() .' as c ON a.user_date_total_id = c.id
							LEFT JOIN '. $udf->getTable() .' as d ON c.user_date_id = d.id
					where
						a.user_id = ?
						AND b.company_id = ?
						AND a.accrual_policy_id = ?
						AND ( a.user_date_total_id IS NULL OR ( a.user_date_total_id IS NOT NULL AND c.deleted = 0 AND d.deleted = 0) )
						AND ( a.deleted = 0 AND b.deleted = 0 )';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyIdAndUserIdAndAccrualPolicyIDAndTimeStampAndAmount($company_id, $user_id, $accrual_policy_id, $time_stamp, $amount, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( $user_id == '') {
			return FALSE;
		}

		if ( $accrual_policy_id == '') {
			return FALSE;
		}

		if ( $time_stamp == '') {
			return FALSE;
		}

		if ( $amount == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array('d.date_stamp' => 'desc', 'a.time_stamp' => 'desc');
			$strict_order = FALSE;
		}

		$uf = new UserFactory();
		$udtf = new UserDateTotalFactory();
		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					'company_id' => $company_id,
					'accrual_policy_id' => $accrual_policy_id,
					'time_stamp' => $this->db->BindTimeStamp( $time_stamp ),
					'amount' => $amount,
					);

		$query = '
					select 	a.*,
							d.date_stamp as date_stamp
					from	'. $this->getTable() .' as a
							LEFT JOIN '. $uf->getTable() .' as b ON a.user_id = b.id
							LEFT JOIN '. $udtf->getTable() .' as c ON a.user_date_total_id = c.id
							LEFT JOIN '. $udf->getTable() .' as d ON c.user_date_id = d.id
					where
						a.user_id = ?
						AND b.company_id = ?
						AND a.accrual_policy_id = ?
						AND a.time_stamp = ?
						AND a.amount = ?
						AND a.deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserIdAndAccrualPolicyIDAndUserDateTotalID($user_id, $accrual_policy_id, $user_date_total_id, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $accrual_policy_id == '') {
			return FALSE;
		}

		if ( $user_date_total_id == '') {
			return FALSE;
		}

		$ph = array(
					'user_id' => $user_id,
					'accrual_policy_id' => $accrual_policy_id,
					'user_date_total_id' => $user_date_total_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_id = ?
						AND accrual_policy_id = ?
						AND user_date_total_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}


	function getByUserIdAndUserDateTotalID($user_id, $user_date_total_id, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $user_date_total_id == '') {
			return FALSE;
		}

		$ph = array(
					'user_id' => $user_id,
					'user_date_total_id' => $user_date_total_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_id = ?
						AND user_date_total_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getOrphansByUserId($user_id, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		$apf = new AccrualPolicyFactory();
		$udtf = new UserDateTotalFactory();
		$udf = new UserDateFactory();

		$ph = array(
					'user_id' => $user_id,
					);

		//Make sure we check if user_date rows are deleted where user_date_total rows are not.
		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udtf->getTable() .' as b ON a.user_date_total_id = b.id
					LEFT JOIN '. $udf->getTable() .' as c ON b.user_date_id = c.id
					LEFT JOIN '. $apf->getTable() .' as d ON a.accrual_policy_id = d.id
					where	a.user_id = ?
						AND (
								( b.id is NULL OR b.deleted = 1 )
								OR
								( b.deleted = 0 AND ( c.id is NULL OR c.deleted = 1) )
							)
						AND ( a.type_id = 10 OR a.type_id = 20 OR ( a.type_id = 75 AND d.type_id = 30 ) )
						AND a.deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getSumByUserIdAndAccrualPolicyId($user_id, $accrual_policy_id) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $accrual_policy_id == '') {
			return FALSE;
		}

		$udtf = new UserDateTotalFactory();

		$ph = array(
					'user_id' => $user_id,
					'accrual_policy_id' => $accrual_policy_id,
					);

		$query = '
					select 	sum(amount) as amount
					from	'. $this->getTable() .' as a
					LEFT JOIN '. $udtf->getTable() .' as b ON a.user_date_total_id = b.id
					where	a.user_id = ?
						AND a.accrual_policy_id = ?
						AND ( (a.user_date_total_id is NOT NULL AND b.id is NOT NULL)
								OR a.user_date_total_id IS NULL AND b.id is NULL )
						AND a.deleted = 0';

		$total = $this->db->GetOne($query, $ph);

		if ($total === FALSE ) {
			$total = 0;
		}
		Debug::text('Balance: '. $total, __FILE__, __LINE__, __METHOD__, 10);

		return $total;
	}

	function getByAccrualPolicyId($accrual_policy_id, $where = NULL, $order = NULL) {
		if ( $accrual_policy_id == '') {
			return FALSE;
		}

		$ph = array(
					'accrual_policy_id' => $accrual_policy_id,
					);

		$uf = new UserFactory();

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a
							LEFT JOIN '. $uf->getTable() .' as b ON a.user_id = b.id
					where	a.accrual_policy_id = ?
						AND a.deleted = 0
						AND b.deleted = 0
					LIMIT 1
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

}
?>
