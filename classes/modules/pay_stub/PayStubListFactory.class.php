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
 * $Revision: 2740 $
 * $Id: PayStubListFactory.class.php 2740 2009-08-19 20:21:50Z ipso $
 * $Date: 2009-08-19 13:21:50 -0700 (Wed, 19 Aug 2009) $
 */

/**
 * @package Module_Pay_Stub
 */
class PayStubListFactory extends PayStubFactory implements IteratorAggregate {

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

		$this->rs = $this->getCache($id);
		if ( $this->rs === FALSE ) {
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

			$this->saveCache($this->rs,$id);
		}

		return $this;
	}

	function getByIdAndCompanyIdAndIgnoreDeleted($id, $company_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( $id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.transaction_date' => 'desc', 'a.advance' => 'asc', 'b.last_name' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'company_id' => $company_id
					);

		//Include deleted pay stubs, for re-calculating YTD amounts?
		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND b.company_id = ?
						AND a.id in ('. $this->getListSQL($id, $ph) .')
						';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByIdAndUserId($id, $user_id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		if ( $user_id == '') {
			return FALSE;
		}

		$ph = array(
					'id' => $id,
					'user_id' => $user_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	id = ?
						AND user_id = ?
						AND deleted = 0
						';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserId($id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		$ph = array(
					'id' => $id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserIdAndCompanyId($user_id, $company_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $company_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.transaction_date' => 'desc', 'a.advance' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND b.company_id = ?
						AND a.user_id in ('. $this->getListSQL($user_id, $ph) .')
						AND a.deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			//Run query without limit
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByUserIdAndCompanyIdAndPayPeriodId($user_id, $company_id, $pay_period_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $company_id == '') {
			return FALSE;
		}

		if ( $pay_period_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.transaction_date' => 'desc', 'a.advance' => 'asc', 'a.user_id' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND b.company_id = ?
						AND a.user_id in ('. $this->getListSQL($user_id, $ph) .')
						';

		if ( $pay_period_id != '' AND isset($pay_period_id[0]) AND !in_array(-1, (array)$pay_period_id) ) {
			$query .= ' AND a.pay_period_id in ('. $this->getListSQL($pay_period_id, $ph) .') ';
		}

		$query .= '
						AND a.deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			//Run query without limit
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getLastPayStubByUserIdAndStartDate($user_id, $start_date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.start_date' => 'desc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'start_date' => $this->db->BindTimeStamp( $start_date ),
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND a.start_date < ?
						AND a.user_id in ('. $this->getListSQL($user_id, $ph) .')
						AND ( a.deleted = 0 AND c.deleted = 0)
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserIdAndStartDateAndEndDate($user_id, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.transaction_date' => 'asc', 'a.advance' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'start_date' => $this->db->BindTimeStamp( $start_date ),
					'end_date' => $this->db->BindTimeStamp( $end_date ),
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND a.transaction_date >= ?
						AND a.transaction_date <= ?
						AND a.user_id in ('. $this->getListSQL($user_id, $ph) .')
						AND ( a.deleted = 0 AND c.deleted = 0)
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyId($company_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.transaction_date' => 'desc', 'a.advance' => 'asc', 'b.last_name' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'company_id' => $company_id
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND b.company_id = ?
						AND a.deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByCompanyIdAndId($company_id, $id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( $id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.transaction_date' => 'desc', 'a.advance' => 'asc', 'b.last_name' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'company_id' => $company_id
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND b.company_id = ?
						AND a.id in ('. $this->getListSQL($id, $ph) .')
						AND a.deleted = 0
						';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByUserIdAndId($user_id, $id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.transaction_date' => 'desc', 'a.advance' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'user_id' => $user_id
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND b.id = ?
						AND a.id in ('. $this->getListSQL($id, $ph) .')
						AND a.deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getByPayPeriodId($id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		$ph = array(
					'id' => $id
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	pay_period_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCurrencyId($id, $where = NULL, $order = NULL) {
		if ( $id == '') {
			return FALSE;
		}

		$ph = array(
					'id' => $id
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	currency_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, FALSE );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByCompanyIdAndPayPeriodId($company_id, $pay_period_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {

		if ( $company_id == '') {
			return FALSE;
		}

		if ( $pay_period_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL OR !is_array($order) ) {
			$order = array( 'a.transaction_date' => 'desc', 'a.advance' => 'asc', 'b.last_name' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();
		$pplf = new PayPeriodListFactory();

		$ph = array(
					'company_id' => $company_id
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b,
							'. $pplf->getTable() .' as c
					where	a.user_id = b.id
						AND a.pay_period_id = c.id
						AND b.company_id = ?
						AND a.pay_period_id in ('. $this->getListSQL($pay_period_id, $ph) .')
						AND a.deleted = 0';

		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		//$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}
/*
	function getByCompanyIdAndTransactionStartDateAndTransactionEndDate($company_id, $start_date, $end_date, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL OR !is_array($order) ) {
			$order = array( 'a.transaction_date' => 'desc', 'a.advance' => 'asc', 'b.last_name' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b
					where	a.user_id = b.id
						AND b.company_id = '. $company_id .'
						AND a.transaction_date >= '. $this->db->BindTimeStamp( TTDate::getBeginDayEpoch( $start_date ) ) .'
						AND a.transaction_date <= '. $this->db->BindTimeStamp( TTDate::getBeginDayEpoch( $end_date ) ) .'
						AND a.deleted = 0';

		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		$this->rs = $this->db->Execute($query);

		return $this;
	}
*/
	function getByUserIdAndPayPeriodId($user_id, $pay_period_id, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $pay_period_id == '') {
			return FALSE;
		}

		$ph = array(
					'pay_period_id' => $pay_period_id,
					'user_id' => $user_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	pay_period_id = ?
						AND user_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserIdAndPayPeriodIdAndAdvance($user_id, $pay_period_id, $advance, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $pay_period_id == '') {
			return FALSE;
		}

		/*
		//Advance is boolean, don't need this check.
		if ( $advance == '') {
			return FALSE;
		}
		*/

		$ph = array(
					'pay_period_id' => $pay_period_id,
					'user_id' => $user_id,
					'advance' => $this->toBool( $advance ),
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	pay_period_id = ?
						AND user_id = ?
						AND advance = ?
						AND deleted = 0';
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

		$additional_order_fields = array('b.last_name', 'b.first_name');
		if ( $order == NULL ) {
			$order = array( 'a.transaction_date' => 'desc', 'b.last_name' => 'asc' );
			$strict = FALSE;
		} else {
			//Always try to order by status first so UNPAID employees go to the bottom.
			if ( isset($order['last_name']) ) {
				$order['b.last_name'] = $order['last_name'];
				unset($order['last_name']);
			}
			if ( isset($order['first_name']) ) {
				$order['b.first_name'] = $order['first_name'];
				unset($order['first_name']);
			}
			if ( isset($order['status']) ) {
				$order['status_id'] = $order['status'];
				unset($order['status']);
			}

			if ( isset($order['transaction_date']) ) {
				$order['last_name'] = 'asc';
			} else {
				$order['transaction_date'] = 'desc';
			}

			$strict = TRUE;
		}

		if ( isset($filter_data['exclude_user_ids']) ) {
			$filter_data['exclude_id'] = $filter_data['exclude_user_ids'];
		}
		if ( isset($filter_data['include_user_ids']) ) {
			$filter_data['user_id'] = $filter_data['include_user_ids'];
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
		if ( isset($filter_data['pay_period_ids']) ) {
			$filter_data['pay_period_id'] = $filter_data['pay_period_ids'];
		}
		if ( isset($filter_data['currency_ids']) ) {
			$filter_data['currency_id'] = $filter_data['currency_ids'];
		}

		//Debug::Arr($order,'bOrder Data:', __FILE__, __LINE__, __METHOD__,10);
		//Debug::Arr($filter_data,'Filter Data:', __FILE__, __LINE__, __METHOD__,10);

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

		if ( isset($filter_data['id']) AND isset($filter_data['id'][0]) AND !in_array(-1, (array)$filter_data['id']) ) {
			$query  .=	' AND a.id in ('. $this->getListSQL($filter_data['id'], $ph) .') ';
		}
		if ( isset($filter_data['permission_children_ids']) AND isset($filter_data['permission_children_ids'][0]) AND !in_array(-1, (array)$filter_data['permission_children_ids']) ) {
			$query  .=	' AND b.id in ('. $this->getListSQL($filter_data['permission_children_ids'], $ph) .') ';
		}
		if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
			$query  .=	' AND b.id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
		}
		if ( isset($filter_data['exclude_id']) AND isset($filter_data['exclude_id'][0]) AND !in_array(-1, (array)$filter_data['exclude_id']) ) {
			$query  .=	' AND b.id not in ('. $this->getListSQL($filter_data['exclude_id'], $ph) .') ';
		}
		if ( isset($filter_data['status_id']) AND isset($filter_data['status_id'][0]) AND !in_array(-1, (array)$filter_data['status_id']) ) {
			$query  .=	' AND b.status_id in ('. $this->getListSQL($filter_data['status_id'], $ph) .') ';
		}
		if ( isset($filter_data['group_id']) AND isset($filter_data['group_id'][0]) AND !in_array(-1, (array)$filter_data['group_id']) ) {
			if ( isset($filter_data['include_subgroups']) AND (bool)$filter_data['include_subgroups'] == TRUE ) {
				$uglf = new UserGroupListFactory();
				$filter_data['group_id'] = $uglf->getByCompanyIdAndGroupIdAndSubGroupsArray( $company_id, $filter_data['group_id'], TRUE);
			}
			$query  .=	' AND b.group_id in ('. $this->getListSQL($filter_data['group_id'], $ph) .') ';
		}
		if ( isset($filter_data['default_branch_id']) AND isset($filter_data['default_branch_id'][0]) AND !in_array(-1, (array)$filter_data['default_branch_id']) ) {
			$query  .=	' AND b.default_branch_id in ('. $this->getListSQL($filter_data['default_branch_id'], $ph) .') ';
		}
		if ( isset($filter_data['default_department_id']) AND isset($filter_data['default_department_id'][0]) AND !in_array(-1, (array)$filter_data['default_department_id']) ) {
			$query  .=	' AND b.default_department_id in ('. $this->getListSQL($filter_data['default_department_id'], $ph) .') ';
		}
		if ( isset($filter_data['title_id']) AND isset($filter_data['title_id'][0]) AND !in_array(-1, (array)$filter_data['title_id']) ) {
			$query  .=	' AND b.title_id in ('. $this->getListSQL($filter_data['title_id'], $ph) .') ';
		}
		if ( isset($filter_data['currency_id']) AND isset($filter_data['currency_id'][0]) AND !in_array(-1, (array)$filter_data['currency_id']) ) {
			$query  .=	' AND a.currency_id in ('. $this->getListSQL($filter_data['currency_id'], $ph) .') ';
		}
		if ( isset($filter_data['pay_period_id']) AND isset($filter_data['pay_period_id'][0]) AND !in_array(-1, (array)$filter_data['pay_period_id']) ) {
			$query  .=	' AND a.pay_period_id in ('. $this->getListSQL($filter_data['pay_period_id'], $ph) .') ';
		}
		if ( isset($filter_data['pay_stub_status_id']) AND isset($filter_data['pay_stub_status_id'][0]) AND !in_array(-1, (array)$filter_data['pay_stub_status_id']) ) {
			$query  .=	' AND a.status_id in ('. $this->getListSQL($filter_data['pay_stub_status_id'], $ph) .') ';
		}

		if ( isset($filter_data['transaction_start_date']) AND trim($filter_data['transaction_start_date']) != '' ) {
			$ph[] = $this->db->BindTimeStamp( strtolower(trim($filter_data['transaction_start_date'])) );
			$query  .=	' AND a.transaction_date >= ?';
		}
		if ( isset($filter_data['transaction_end_date']) AND trim($filter_data['transaction_end_date']) != '' ) {
			$ph[] = $this->db->BindTimeStamp( strtolower(trim($filter_data['transaction_end_date'])) );
			$query  .=	' AND a.transaction_date <= ?';
		}
		if ( isset($filter_data['transaction_date']) AND trim($filter_data['transaction_date']) != '' ) {
			$ph[] = $this->db->BindTimeStamp( strtolower(trim($filter_data['transaction_date'])) );
			$query  .=	' AND a.transaction_date = ?';
		}

		$query .= 	'
						AND ( a.deleted = 0 AND b.deleted = 0 )
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
