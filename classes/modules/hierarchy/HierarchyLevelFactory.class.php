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
 * $Id: HierarchyObjectTypeFactory.class.php 2095 2008-09-01 07:04:25Z ipso $
 * $Date: 2008-09-01 00:04:25 -0700 (Mon, 01 Sep 2008) $
 */

/**
 * @package Module_Hierarchy
 */
class HierarchyLevelFactory extends Factory {
	protected $table = 'hierarchy_level';
	protected $pk_sequence_name = 'hierarchy_level_id_seq'; //PK Sequence name

	var $hierarchy_control_obj = NULL;

	function _getFactoryOptions( $name ) {
		$retval = NULL;
		return $retval;
	}

	function getHierarchyControlObject() {
		if ( is_object($this->hierarchy_control_obj) ) {
			return $this->hierarchy_control_obj;
		} else {
			$hclf = new HierarchyControlListFactory();
			$this->hierarchy_control_obj = $hclf->getById( $this->getHierarchyControl() )->getCurrent();

			return $this->hierarchy_control_obj;
		}
	}

	function getHierarchyControl() {
		if ( isset($this->data['hierarchy_control_id']) ) {
			return $this->data['hierarchy_control_id'];
		}

		return FALSE;
	}
	function setHierarchyControl($id) {
		$id = trim($id);

		$hclf = new HierarchyControlListFactory();

		if ( $this->Validator->isResultSetWithRows(	'hierarchy_control_id',
															$hclf->getByID($id),
															TTi18n::gettext('Invalid Hierarchy Control')
															) ) {
			$this->data['hierarchy_control_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getLevel() {
		if ( isset($this->data['level']) ) {
			return (int)$this->data['level'];
		}

		return FALSE;
	}
	function setLevel($int) {
		$int = trim($int);

		if ( $int <= 0 ) {
			$int = 1; //1 is the lowest level
		}

		if 	(	$int > 0
				AND
				$this->Validator->isNumeric(		'level',
													$int,
													TTi18n::gettext('Level is invalid')) ) {
			$this->data['level'] = $int;

			return TRUE;
		}

		return FALSE;
	}

	function getUser() {
		if ( isset($this->data['user_id']) ) {
			return $this->data['user_id'];
		}

		return FALSE;
	}
	function setUser($id) {
		$id = trim($id);

		$ulf = new UserListFactory();
		$hulf = new HierarchyUserListFactory();

		if ( $this->getHierarchyControl() == FALSE ) {
			return FALSE;
		}

		//Get user object so we can get the users full name to display as an error message.
		$ulf->getById( $id );

		if ( $id == 0
				OR
				(
				$ulf->getRecordCount() > 0
				AND
				$this->Validator->isResultSetWithRows(	'user',
															$ulf->getByID($id),
															TTi18n::gettext('Invalid Employee')
															)
				AND
				$this->Validator->isNotResultSetWithRows(	'user',
															$hulf->getByHierarchyControlAndUserId( $this->getHierarchyControl(), $id ),
															TTi18n::gettext( $ulf->getCurrent()->getFullName() .' is assigned as both a superior and subordinate')
															)
				)
				) {
			$this->data['user_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	//Remaps raw hierarchy_levels so they always start from 1, and have no gaps in them.
	static function ReMapHierarchyLevels( $hierarchy_level_data ) {
		if ( !is_array($hierarchy_level_data) ) {
			return FALSE;
		}

		$remapped_hierarchy_levels = FALSE;

		foreach( $hierarchy_level_data as $hierarchy_level_id => $hierarchy_level ) {
			$tmp_hierarchy_levels[] = $hierarchy_level['level'];
		}
		sort($tmp_hierarchy_levels);

		$level = 0;
		$prev_level = FALSE;
		foreach( $tmp_hierarchy_levels as $hierarchy_level ) {
			if ( $prev_level != $hierarchy_level ) {
				$level++;
			}

			$remapped_hierarchy_levels[$hierarchy_level] = $level;

			$prev_level = $hierarchy_level;
		}

		return $remapped_hierarchy_levels;
	}

	function postSave() {
		return TRUE;
	}

	function addLog( $log_action ) {
		return TTLog::addEntry( $this->getId(), $log_action, TTi18n::getText('Hierarchy Level'), NULL, $this->getTable() );
	}
}
?>
