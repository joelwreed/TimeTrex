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
 * $Revision: 2896 $
 * $Id: PayStubAmendmentListFactory.class.php 2896 2009-10-13 20:53:33Z ipso $
 * $Date: 2009-10-13 13:53:33 -0700 (Tue, 13 Oct 2009) $
 */

/**
 * @package Module_Pay_Stub_Amendment
 */
class PayStubAmendmentListFactory extends PayStubAmendmentFactory implements IteratorAggregate {

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

	function getByCompanyId($company_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $company_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			//$order = array( 'a.effective_date' => 'desc', 'a.user_id' => 'asc' );
			$order = array( 'a.effective_date' => 'desc', 'a.status_id' => 'asc', 'b.last_name' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();

		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b
					where	a.user_id = b.id
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

	function getByRecurringPayStubAmendmentId($recurring_ps_amendment_id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {

		if ( $recurring_ps_amendment_id == '') {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'effective_date' => 'desc', 'user_id' => 'asc' );
			$strict_order = FALSE;
		}

		$ph = array(
					'recurring_ps_amendment_id' => $recurring_ps_amendment_id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	recurring_ps_amendment_id = ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}


	function getByUserIdAndRecurringPayStubAmendmentIdAndStartDateAndEndDate($user_id, $recurring_ps_amendment_id, $start_date, $end_date, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $recurring_ps_amendment_id == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		$ph = array(
					'user_id' => $user_id,
					'recurring_ps_amendment_id' => $recurring_ps_amendment_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	user_id = ?
						AND recurring_ps_amendment_id = ?
						AND effective_date >= ?
						AND effective_date <= ?
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

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
			$order = array( 'a.effective_date' => 'desc', 'a.status_id' => 'asc', 'b.last_name' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();

		$ph = array(
					'company_id' => $company_id,
					'user_id' => $user_id,
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b
					where	a.user_id = b.id
						AND b.company_id = ?
						AND a.user_id = ?
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

	function getByCompanyIdAndUserIdAndStartDateAndEndDate($company_id, $user_id, $start_date = NULL, $end_date = NULL, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $company_id == '') {
			return FALSE;
		}

		/*
		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}
		*/

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array( 'a.effective_date' => 'desc', 'a.status_id' => 'asc', 'b.last_name' => 'asc' );
			$strict_order = FALSE;
		}

		$ulf = new UserListFactory();

		$ph = array(
					'company_id' => $company_id,
					//'user_id' => $user_id,
					//'start_date' => $start_date,
					//'end_date' => $end_date,
					);

		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $ulf->getTable() .' as b
					where	a.user_id = b.id
						AND b.company_id = ?
					';

		if ( $user_id != '' AND isset($user_id[0]) AND !in_array(-1, (array)$user_id) ) {
			$query  .=	' AND a.user_id in ('. $this->getListSQL($user_id, $ph) .') ';
		}
		if ( $start_date != ''  ) {
			$ph[] = $start_date;
			$ph[] = $end_date;
			$query  .=	' AND a.effective_date >= ? AND a.effective_date <= ? ';
		}

		$query .= '	AND a.deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order, $strict_order );

		if ($limit == NULL) {
			$this->rs = $this->db->Execute($query, $ph);
		} else {
			$this->rs = $this->db->PageExecute($query, $limit, $page, $ph);
		}

		return $this;
	}

	function getIsModifiedByUserIdAndStartDateAndEndDateAndDate($user_id, $start_date, $end_date, $date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		if ( $date == '') {
			return FALSE;
		}

		$ph = array(
					'user_id' => $user_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'created_date' => $date,
					'updated_date' => $date
					);

		//INCLUDE Deleted rows in this query.
		$query = '
					select 	*
					from	'. $this->getTable() .'
					where
							user_id = ?
						AND effective_date >= ?
						AND effective_date <= ?
						AND
							( created_date >= ? OR updated_date >= ? )
					LIMIT 1
					';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);
		if ( $this->getRecordCount() > 0 ) {
			Debug::text('isModified Query: '. $query, __FILE__, __LINE__, __METHOD__,10);
			Debug::text('PS Amendment rows have been modified: '. $this->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);

			return TRUE;
		}
		Debug::text('PS Amendment rows have NOT been modified', __FILE__, __LINE__, __METHOD__,10);
		return FALSE;
	}

	function getByUserIdAndAuthorizedAndStartDateAndEndDate($user_id, $authorized, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $authorized == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		$ph = array(
					'authorized' => $this->toBool($authorized) ,
					'start_date' => $start_date,
					'end_date' => $end_date,
					);

		//CalculatePayStub uses this to find PS amendments.
		//Because of percent amounts, make sure we order by effective date FIRST,
		//Then FIXED amounts, then percents.


		//Pay period end dates never equal the start start date, so >= and <= are proper.
		
		//06-Oct-06: Start including YTD_adjustment entries for the new pay stub calculation system.
		//						AND ytd_adjustment = 0
		$query = '
					select 	*
					from	'. $this->getTable() .'
					where
						authorized = ?
						AND effective_date >= ?
						AND effective_date <= ?
						AND user_id in ('.$this->getListSQL($user_id, $ph) .')
						AND deleted = 0
					ORDER BY effective_date asc, type_id asc
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		//Debug::text(' Query: '. $query, __FILE__, __LINE__, __METHOD__, 10);

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByUserIdAndAuthorizedAndYTDAdjustmentAndStartDateAndEndDate($user_id, $authorized, $ytd_adjustment, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $authorized == '') {
			return FALSE;
		}

		if ( $ytd_adjustment == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		$ph = array(
					'authorized' => $this->toBool($authorized) ,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'ytd_adjustment' => $this->toBool($ytd_adjustment) ,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where
						authorized = ?
						AND effective_date >= ?
						AND effective_date <= ?
						AND ytd_adjustment = ?
						AND user_id in ('.$this->getListSQL($user_id, $ph) .')
						AND deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		Debug::text(' Query: '. $query, __FILE__, __LINE__, __METHOD__, 10);

		$this->rs = $this->db->Execute($query, $ph);

		return $this;

	}

	function getAmountSumByUserIdAndTypeIdAndAuthorizedAndStartDateAndEndDate($user_id, $type_id, $authorized, $start_date, $end_date, $where = NULL, $order = NULL) {
		$psalf = new PayStubAmendmentListFactory();
		$psalf->getByUserIdAndTypeIdAndAuthorizedAndStartDateAndEndDate($user_id, $type_id, $authorized, $start_date, $end_date, $where , $order );
		if ( $psalf->getRecordCount() > 0 ) {
			$sum = 0;
			Debug::text('Record Count: '. $psalf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);

			foreach($psalf as $psa_obj) {
				$amount = $psa_obj->getCalculatedAmount();
				Debug::text('PS Amendment Amount: '. $amount, __FILE__, __LINE__, __METHOD__,10);
				$sum += $amount;
			}

			return $sum;
		}

		Debug::text('No PS Amendments found...', __FILE__, __LINE__, __METHOD__,10);

		return FALSE;
	}

	function getByUserIdAndTypeIdAndAuthorizedAndStartDateAndEndDate($user_id, $type_id, $authorized, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $type_id == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		$psealf = new PayStubEntryAccountListFactory();

		$ph = array(
					'user_id' => $user_id,
					'type_id' => $type_id,
					'authorized' => $this->toBool($authorized) ,
					'start_date' => $start_date,
					'end_date' => $end_date,
					);

		//select 	sum(amount)
		//						AND a.tax_exempt = \''. $this->toBool($tax_exempt) .'\'
		$query = '
					select 	a.*
					from	'. $this->getTable() .' as a,
							'. $psealf->getTable() .' as b
					where	a.pay_stub_entry_name_id = b.id
						AND	a.user_id = ?
						AND b.type_id = ?
						AND a.authorized = ?
						AND a.effective_date >= ?
						AND a.effective_date <= ?
						AND a.ytd_adjustment = 0
						AND a.deleted = 0';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);
		Debug::text(' Query: '. $query, __FILE__, __LINE__, __METHOD__, 10);

		return $this;
	}

	function getAmountSumByUserIdAndNameIdAndAuthorizedAndStartDateAndEndDate($user_id, $name_id, $authorized, $start_date, $end_date, $where = NULL, $order = NULL) {
		if ( $user_id == '') {
			return FALSE;
		}

		if ( $name_id == '') {
			return FALSE;
		}

		if ( $start_date == '') {
			return FALSE;
		}

		if ( $end_date == '') {
			return FALSE;
		}

		$psealf = new PayStubEntryAccountListFactory();

		$ph = array(
					'user_id' => $user_id,
					'authorized' => $this->toBool($authorized) ,
					'start_date' => $start_date,
					'end_date' => $end_date,
					);

		$query = '
					select 	sum(amount)
					from	'. $this->getTable() .' as a,
							'. $psealf->getTable() .' as b
					where	a.pay_stub_entry_name_id = b.id
						AND	a.user_id = ?
						AND a.authorized = ?
						AND a.effective_date >= ?
						AND a.effective_date <= ?
						AND b.id in ('.$this->getListSQL($name_id, $ph) .')
						AND a.ytd_adjustment = 0
						AND a.deleted = 0
						';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		//$this->rs = $this->db->Execute($query);
		Debug::text(' Query: '. $query, __FILE__, __LINE__, __METHOD__, 10);

		$sum = $this->db->GetOne($query, $ph);

		if ( $sum !== FALSE OR $sum !== NULL) {
			Debug::text('Amount Sum: '. $sum, __FILE__, __LINE__, __METHOD__, 10);
			return $sum;
		}

		Debug::text('Amount Sum is NULL', __FILE__, __LINE__, __METHOD__, 10);
		return FALSE;
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

		$additional_order_fields = array('b.last_name', 'b.first_name');
		if ( $order == NULL ) {
			$order = array( 'a.effective_date' => 'desc', 'a.status_id' => 'asc', 'b.last_name' => 'asc' );
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
			if ( isset($order['type']) ) {
				$order['type_id'] = $order['type'];
				unset($order['type']);
			}
			if ( isset($order['status']) ) {
				$order['status_id'] = $order['status'];
				unset($order['status']);
			}

			if ( isset($order['effective_date']) ) {
				$order['b.last_name'] = 'asc';
			} else {
				$order['a.effective_date'] = 'desc';
			}

			$strict = TRUE;
		}
		//Debug::Arr($order,'bOrder Data:', __FILE__, __LINE__, __METHOD__,10);
		//Debug::Arr($filter_data,'Filter Data:', __FILE__, __LINE__, __METHOD__,10);

		$uf = new UserFactory();
		$psealf = new PayStubEntryAccountListFactory();


		$ph = array(
					'company_id' => $company_id,
					);

		$query = '
					select 	a.*
					from 	'. $this->getTable() .' as a
						LEFT JOIN '. $uf->getTable() .' as b ON a.user_id = b.id
						LEFT JOIN '. $psealf->getTable() .' as c ON a.pay_stub_entry_name_id  = c.id
					where	b.company_id = ?
					';

		if ( isset($filter_data['permission_children_ids']) AND isset($filter_data['permission_children_ids'][0]) AND !in_array(-1, (array)$filter_data['permission_children_ids']) ) {
			$query  .=	' AND b.id in ('. $this->getListSQL($filter_data['permission_children_ids'], $ph) .') ';
		}
		if ( isset($filter_data['user_id']) AND isset($filter_data['user_id'][0]) AND !in_array(-1, (array)$filter_data['user_id']) ) {
			$query  .=	' AND b.id in ('. $this->getListSQL($filter_data['user_id'], $ph) .') ';
		}
		if ( isset($filter_data['status_id']) AND isset($filter_data['status_id'][0]) AND !in_array(-1, (array)$filter_data['status_id']) ) {
			$query  .=	' AND a.status_id in ('. $this->getListSQL($filter_data['status_id'], $ph) .') ';
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
		if ( isset($filter_data['recurring_ps_amendment_id']) AND isset($filter_data['recurring_ps_amendment_id'][0]) AND !in_array(-1, (array)$filter_data['recurring_ps_amendment_id']) ) {
			$query  .=	' AND a.recurring_ps_amendment_id in ('. $this->getListSQL($filter_data['recurring_ps_amendment_id'], $ph) .') ';
		}

		if ( isset($filter_data['start_date']) AND trim($filter_data['start_date']) != '' ) {
			$ph[] = strtolower(trim($filter_data['start_date']));
			$query  .=	' AND a.effective_date >= ?';
		}
		if ( isset($filter_data['end_date']) AND trim($filter_data['end_date']) != '' ) {
			$ph[] = strtolower(trim($filter_data['end_date']));
			$query  .=	' AND a.effective_date <= ?';
		}
		if ( isset($filter_data['effective_date']) AND trim($filter_data['effective_date']) != '' ) {
			$ph[] = strtolower(trim($filter_data['effective_date']));
			$query  .=	' AND a.effective_date = ?';
		}

		$query .= 	'
						AND ( a.deleted = 0 AND b.deleted = 0 AND c.deleted = 0 )
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
