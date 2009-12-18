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
 * $Revision: 3060 $
 * $Id: index.php 3060 2009-11-13 16:08:37Z ipso $
 * $Date: 2009-11-13 08:08:37 -0800 (Fri, 13 Nov 2009) $
 */
require_once('../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');


/* We assign $title var before translation to save breadcrumb in db as english.
 * Yet string in gettext() is still found by xgettext extraction utility.
 * This construction lets us do both without duplicating the string literal.
 */
$smarty->assign('title', TTi18n::gettext($title = 'Home'));
BreadCrumb::setCrumb($title);


/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'id',
												'pref_data'
												) ) );

//Grab total number of exceptions for each severity level
$exceptions = array();

$elf = new ExceptionListFactory();
$elf->getFlaggedExceptionsByUserIdAndPayPeriodStatus( $current_user->getId(), 10 );
if ( $elf->getRecordCount() > 0 ) {
	foreach($elf as $e_obj) {
		if ( isset($exceptions[$e_obj->getColumn('severity_id')]) ) {
			$exceptions[$e_obj->getColumn('severity_id')]++;
		} else {
			$exceptions[$e_obj->getColumn('severity_id')] = 1;
		}
	}
}
unset($elf, $e_obj);
$smarty->assign_by_ref('exceptions', $exceptions);


//Grab list of recent requests
$rlf = new RequestListFactory();
$rlf->getByUserIDAndCompanyId( $current_user->getId(), $current_company->getId(), 5, 1 );
if ($rlf->getRecordCount() > 0 ) {
	$status_options = $rlf->getOptions('status');
	$type_options = $rlf->getOptions('type');

	foreach ($rlf as $r_obj) {
		$requests[] = array(
							'id' => $r_obj->getId(),
							'user_date_id' => $r_obj->getUserDateID(),
							'date_stamp' => TTDate::strtotime($r_obj->getColumn('date_stamp')),
							'status_id' => $r_obj->getStatus(),
							'status' => Misc::TruncateString( $status_options[$r_obj->getStatus()], 15 ),
							'type_id' => $r_obj->getType(),
							'type' => $type_options[$r_obj->getType()],
							'created_date' => $r_obj->getCreatedDate(),
							'deleted' => $r_obj->getDeleted()
						);
	}
}
$smarty->assign_by_ref('requests', $requests);


//Grab list of unread messages
$mlf = new MessageListFactory();
$mlf->getByUserIdAndFolder( $current_user->getId(), 10, 5, 1);
if ( $mlf->getRecordCount() > 0 ) {
	$object_name_options = $mlf->getOptions('object_name');

	$require_ack = FALSE;
	foreach ($mlf as $message) {
		//Get user info
		$ulf = new UserListFactory();

		if ( $message->isAck() == FALSE ) {
			$require_ack = TRUE;
		}

		if ( $message->getRequireAck() == TRUE ) {
			$show_ack_column = TRUE;
		}

		$messages[] = array(
							'id' => $message->getId(),
							'parent_id' => $message->getParent(),
							'object_type_id' => $message->getObjectType(),
							'object_type' => $object_name_options[$message->getObjectType()],
							'object_id' => $message->getObject(),
							'priority' => $message->getPriority(),
							'status_id' => $message->getStatus(),
							'require_ack' => $message->getRequireAck(),
							'ack_date' => $message->getAckDate(),
							'subject' => Misc::TruncateString( $message->getSubject(), 20 ),
							'body' => $message->getBody(),
							'created_date' => $message->getCreatedDate(),
							'created_by' => $message->getCreatedBy(),
							'created_by_full_name' => $ulf->getById( $message->getCreatedBy() )->getIterator()->current()->getFullName(),
							'updated_date' => $message->getUpdatedDate(),
							'updated_by' => $message->getUpdatedBy(),
							'deleted_date' => $message->getDeletedDate(),
							'deleted_by' => $message->getDeletedBy()
						);

	}
}
$smarty->assign_by_ref('messages', $messages);

//Grab requests pending authorization if they are a supervisor.
if ( $permission->Check('authorization','enabled')
		AND $permission->Check('authorization','view')
		AND $permission->Check('request','authorize') ) {

	$ulf = new UserListFactory();
	$hlf = new HierarchyListFactory();
	$hllf = new HierarchyLevelListFactory();

	$request_levels = $hllf->getLevelsByUserIdAndObjectTypeID( $current_user->getId(), 50 );
	//Debug::Arr( $request_levels, 'Request Levels', __FILE__, __LINE__, __METHOD__,10);

	$request_hierarchy_user_ids = $hlf->getByUserIdAndObjectTypeIDAndLevel( $current_user->getId(), 50, 1 );
	//Debug::Arr( $request_hierarchy_user_ids, 'Request Hierarchy Ids', __FILE__, __LINE__, __METHOD__,10);

	if ( is_array($request_hierarchy_user_ids)
			AND isset($request_hierarchy_user_ids['child_level'])
			AND isset($request_hierarchy_user_ids['parent_level'])
			AND isset($request_hierarchy_user_ids['current_level']) ) {

		$rlf = new RequestListFactory();
		//$rlf->getByUserIdListAndStatusAndNotAuthorized($request_hierarchy_user_ids['child_level'], 30, $request_hierarchy_user_ids['parent_level'], $request_hierarchy_user_ids['current_level'] );
		$rlf->getByUserIdListAndStatusAndLevelAndMaxLevelAndNotAuthorized( $request_hierarchy_user_ids['child_level'], 30, 1, (int)end($request_levels)  );


		$status_options = $rlf->getOptions('status');
		$type_options = $rlf->getOptions('type');

		foreach( $rlf as $r_obj) {
			//Grab authorizations for this object.
			$pending_requests[] = array(
									'id' => $r_obj->getId(),
									'user_date_id' => $r_obj->getId(),
									'user_id' => $r_obj->getUserDateObject()->getUser(),
									'user_full_name' => $r_obj->getUserDateObject()->getUserObject()->getFullName(),
									'date_stamp' => $r_obj->getUserDateObject()->getDateStamp(),
									'type_id' => $r_obj->getType(),
									'type' => $type_options[$r_obj->getType()],
									'status_id' => $r_obj->getStatus(),
									'status' => $status_options[$r_obj->getStatus()]
												);
		}
	}
	$smarty->assign_by_ref('pending_requests', $pending_requests);
	unset($pending_requests, $request_hierarchy_id, $request_user_id, $request_node_data, $request_current_level_user_ids, $request_parent_level_user_ids, $request_child_level_user_ids );
}

$smarty->display('index.tpl');
?>