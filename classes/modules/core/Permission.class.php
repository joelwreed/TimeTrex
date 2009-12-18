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
 * $Revision: 2838 $
 * $Id: Permission.class.php 2838 2009-09-18 20:57:41Z ipso $
 * $Date: 2009-09-18 13:57:41 -0700 (Fri, 18 Sep 2009) $
 */

/**
 * @package Core
 */
class Permission {
	function getPermissions( $user_id, $company_id ) {

		$plf = new PermissionListFactory();

		$cache_id = 'permission::all'.$user_id.$company_id;
		$perm_arr = $plf->getCache($cache_id);
		//Debug::Arr($perm_arr, 'Cached Perm Arr:', __FILE__, __LINE__, __METHOD__,9);
		if ( $perm_arr === FALSE ) {
			$plf->getAllPermissionsByCompanyIdAndUserId( $company_id, $user_id );
			if ( $plf->getRecordCount() > 0 ) {
				//Debug::Text('Found Permissions in DB!', __FILE__, __LINE__, __METHOD__,9);
				$perm_arr['_system']['last_updated_date'] = NULL;
				foreach($plf as $p_obj) {
					//Debug::Text('Perm -  Section: '. $p_obj->getSection(), __FILE__, __LINE__, __METHOD__,9);
					if ( $p_obj->getUpdatedDate() > $perm_arr['_system']['last_updated_date'] ) {
						$perm_arr['_system']['last_updated_date'] =  $p_obj->getUpdatedDate();
					}
					$perm_arr[$p_obj->getSection()][$p_obj->getName()] = $p_obj->getValue();
				}

				$plf->saveCache($perm_arr,$cache_id);

				return $perm_arr;
			}
		}

		return $perm_arr;
	}

	function Check($section, $name, $user_id = NULL, $company_id = NULL) {
		//Use Cache_Lite class once we need performance.
		if ( $user_id == NULL OR $user_id == '') {
			global $current_user;
			if ( is_object( $current_user ) ) {
				$user_id = $current_user->getId();
			} else {
				return FALSE;
			}
		}

		if ( $company_id == NULL OR $company_id == '') {
			global $current_company;
			$company_id = $current_company->getId();
		}

		//Debug::Text('Permission Check - Section: '. $section .' Name: '. $name .' User ID: '. $user_id .' Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,9);
		$permission_arr = $this->getPermissions( $user_id, $company_id );

		if ( isset($permission_arr[$section][$name]) ) {
			//Debug::Text('Permission is Set!', __FILE__, __LINE__, __METHOD__,9);
			$result = $permission_arr[$section][$name];
		} else {
			//Debug::Text('Permission is NOT Set!', __FILE__, __LINE__, __METHOD__,9);
			$result = FALSE;
		}

		return $result;
	}

	function Redirect($result) {
		if ( $result !== TRUE ) {
			Redirect::Page( URLBuilder::getURL( NULL, Environment::getBaseURL().'/permission/PermissionDenied.php') );
		}

		return TRUE;
	}

	function PermissionDenied( $result = FALSE, $description = 'Permission Denied' ) {
		if ( $result !== TRUE ) {
			Debug::Text('Permission Denied! Description: '. $description, __FILE__, __LINE__, __METHOD__, 10);
			return APIFactory::returnHandler( FALSE, 'PERMISSION', $description );
		}

		return TRUE;
	}

	function Query($section, $name, $user_id = NULL, $company_id = NULL) {
		Debug::Text('Permission Query!' , __FILE__, __LINE__, __METHOD__,9);
		if ( $user_id == NULL OR $user_id == '') {
			global $current_user;
			if ( is_object( $current_user ) ) {
				$user_id = $current_user->getId();
			} else {
				return FALSE;
			}
		}

		if ( $company_id == NULL OR $company_id == '') {
			global $current_company;
			$company_id = $current_company->getId();
		}

		$plf = new PermissionListFactory();

		return $plf->getBySectionAndNameAndUserIdAndCompanyId($section, $name, $user_id, $company_id)->getCurrent();
	}

	//Checks if the row_object_id is created by the current user
	function isOwner( $object_created_by, $object_assigned_to = NULL, $current_user_id = NULL ) {
		if ( $current_user_id == NULL OR $current_user_id == '') {
			global $current_user;
			if ( is_object( $current_user ) ) {
				$current_user_id = $current_user->getId();
			} else {
				return FALSE;
			}
		}

		if ( ($object_created_by != '' AND $object_created_by == $current_user_id)
				OR ($object_assigned_to != '' AND $object_assigned_to == $current_user_id) ) {
			return TRUE;
		}

		return FALSE;
	}

	//Checks if the row_object_id is in the src_object_list array,
	function isChild( $row_object_id, $src_object_list ) {
		if ( !is_numeric($row_object_id) ) {
			return FALSE;
		}

		if ( !is_array($src_object_list) AND $src_object_list != '' ) {
			$src_object_list = array( $src_object_list );
		}

		if ( is_array($src_object_list) AND in_array( $row_object_id, $src_object_list ) ) {
			return TRUE;
		}

		return FALSE;
	}

	function getLastUpdatedDate( $user_id = NULL, $company_id = NULL ) {
		//Use Cache_Lite class once we need performance.
		if ( $user_id == NULL OR $user_id == '') {
			global $current_user;
			if ( isset($current_user) ) {
				$user_id = $current_user->getId();
			} else {
				return FALSE;
			}
		}

		if ( $company_id == NULL OR $company_id == '') {
			global $current_company;
			$company_id = $current_company->getId();
		}

		//Debug::Text('Permission Check - Section: '. $section .' Name: '. $name .' User ID: '. $user_id .' Company ID: '. $company_id, __FILE__, __LINE__, __METHOD__,9);
		$permission_arr = $this->getPermissions( $user_id, $company_id );

		if ( isset($permission_arr['_system']['last_updated_date']) ) {
			return $permission_arr['_system']['last_updated_date'];
		}

		return FALSE;
	}
}
?>
