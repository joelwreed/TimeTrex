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
 * $Id: HierarchyObjectTypeListFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Module_Hierarchy
 */
class HierarchyLevelListFactory extends HierarchyLevelFactory implements IteratorAggregate {

	function getAll($limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		$query = '
					select 	*
					from	'. $this->getTable() .'
					where 	deleted = 0
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
		if ( $id == '' ) {
			return FALSE;
		}

		$ph = array(
					'id' => $id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	id = ?
						AND deleted = 0
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByHierarchyControlId($id, $where = NULL, $order = NULL) {
		if ( $id == '' ) {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array('level' => 'asc', 'user_id' => 'asc');
			$strict_order = FALSE;
		}

		$ph = array(
					'id' => $id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	hierarchy_control_id = ?
						AND deleted = 0
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getByHierarchyControlIdAndUserId($id, $user_id, $where = NULL, $order = NULL) {
		if ( $id == '' ) {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			$order = array('level' => 'asc', 'user_id' => 'asc');
			$strict_order = FALSE;
		}

		$ph = array(
					'id' => $id,
					'user_id' => $user_id
					);

		$query = '
					select 	*
					from	'. $this->getTable() .'
					where	hierarchy_control_id = ?
						AND user_id = ?
						AND deleted = 0
				';
		$query .= $this->getWhereSQL( $where );
		$query .= $this->getSortSQL( $order );

		$this->rs = $this->db->Execute($query, $ph);

		return $this;
	}

	function getLevelsByHierarchyControlIdAndUserId( $id, $user_id ) {
		if ( $id == '' ) {
			return FALSE;
		}

		if ( $user_id == '' ) {
			return FALSE;
		}

		$ph = array(
					'id' => $id,
					'idb' => $id,
					'user_id' => $user_id,
					);

		$query = '
					select 	distinct(level)
					from	'. $this->getTable() .'
					where	hierarchy_control_id = ?
						AND level >= (
										select 	level
										from	'. $this->getTable() .'
										where	hierarchy_control_id = ?
											AND user_id = ?
											AND deleted = 0
										LIMIT 1
									 )
						AND deleted = 0
					ORDER BY level ASC
				';

		$retarr = $this->db->GetCol($query, $ph);

		return $retarr;

	}

	function getLevelsByUserIdAndObjectTypeID( $user_id, $object_type_id = 50 ) { //Requests
		if ( $user_id == '' ) {
			return FALSE;
		}

		if ( $object_type_id == '' ) {
			return FALSE;
		}

		$uf = new UserFactory();
		$hotf = new HierarchyObjectTypeFactory();
		$hcf = new HierarchyControlFactory();

		$ph = array(
					'user_id' => $user_id,
					);

		$query = '
				select 	distinct (x.level) as level
				from	'. $this->getTable() .' as x,
						'. $hcf->getTable() .' as y,
					(
								select 	a.hierarchy_control_id,a.level
								from	'. $this->getTable() .' as a
									LEFT JOIN '. $hotf->getTable() .' as b ON a.hierarchy_control_id = b.hierarchy_control_id
								where a.user_id = ?
									AND b.object_type_id in ('. $this->getListSQL($object_type_id, $ph) .')
									AND a.deleted = 0
					) as z
				where
					x.hierarchy_control_id = y.id
					AND x.hierarchy_control_id = z.hierarchy_control_id
					AND x.level >= z.level
					AND ( x.deleted = 0 AND y.deleted = 0 )
				ORDER BY x.level asc
				';

		$rs = $this->db->Execute($query, $ph);
		//Debug::Text(' Rows: '. $rs->RecordCount(), __FILE__, __LINE__, __METHOD__,10);

		if ( $rs->RecordCount() > 0 ) {
			//The retarr key is the value that will be displayed to the user when switching levels on the authorization page,
			//so we need to start that from 1 and increasing sequentially, regardless of what the actual hierarchy level is.
			$i=1;
			foreach( $rs as $row ) {
				$retarr[$i] = $row['level'];
				$i++;
			}

			return $retarr;
		}

		return FALSE;
	}

/*
	function getByCompanyIdAndObjectTypeId($id, $object_type_id, $where = NULL, $order = NULL) {
		if ( $id == '' ) {
			return FALSE;
		}

		if ( $object_type_id == '' ) {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			//$order = array('b.last_name' => 'asc');
			$strict_order = FALSE;
		}

		$cache_id = $id.$object_type_id;

		$hcf = new HierarchyControlFactory();
		$hotf = new HierarchyObjectTypeFactory();

		$this->rs = $this->getCache($cache_id);
		if ( $this->rs === FALSE ) {
			$ph = array(
						'id' => $id,
						'object_type_id' => $object_type_id,
						);

			$query = '
						select 	*
						from	'. $this->getTable() .' as a,
								'. $hcf->getTable() .' as b,
								'. $hotf->getTable() .' as c

						where	a.hierarchy_control_id = b.id
							AND a.hierarchy_control_id = c.hierarchy_control_id
							AND b.company_id = ?
							AND c.object_type_id = ?
							AND b.deleted = 0
					';
			$query .= $this->getWhereSQL( $where );
			$query .= $this->getSortSQL( $order, $strict_order );

			$this->rs = $this->db->Execute($query, $ph);

			$this->saveCache($this->rs,$cache_id);
		}

		return $this;
	}

	function getByCompanyId($id, $limit = NULL, $page = NULL, $where = NULL, $order = NULL) {
		if ( $id == '' ) {
			return FALSE;
		}

		$strict_order = TRUE;
		if ( $order == NULL ) {
			//$order = array('b.last_name' => 'asc');
			$strict_order = FALSE;
		}

		$hcf = new HierarchyControlFactory();

		$ph = array(
					'id' => $id,
					);

		$query = '
					select 	*
					from	'. $this->getTable() .' as a,
							'. $hcf->getTable() .' as b

					where	a.hierarchy_control_id = b.id
						AND b.company_id = ?
						AND b.deleted = 0
				';
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


	function getByCompanyIdArray($id) {

		$hotlf = new HierarchyObjectTypeListFactory();
		$hotlf->getByCompanyId( $id ) ;

		$object_type = array();
		foreach ($hotlf as $object_type) {
			$object_types[] = $object_type->getObjectType();
		}

		return $object_types;
	}
*/
}
?>
