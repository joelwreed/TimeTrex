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
 * $Revision: 2058 $
 * $Id: ViewMessage.php 2058 2008-08-21 19:16:38Z ipso $
 * $Date: 2008-08-21 12:16:38 -0700 (Thu, 21 Aug 2008) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

//Debug::setVerbosity(11);

if ( !$permission->Check('message','enabled')
		OR !( $permission->Check('message','view') OR $permission->Check('message','view_own') ) ) {

	$permission->Redirect( FALSE ); //Redirect
}

$smarty->assign('title', TTi18n::gettext($title = 'View Message') ); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'page',
												'sort_column',
												'sort_order',
												'object_type_id',
												'object_id',
												'parent_id',
												'id',
												'message_data',
												'ack_message_id',
												) ) );
$mf = new MessageFactory();

$action = Misc::findSubmitButton();
switch ($action) {
	case 'acknowledge_message':
		$mf->setId( $ack_message_id );
		$mf->setAckDate( TTDate::getTime() );
		$mf->setAckBy( $current_user->getId() );
		if ( $mf->isValid() ) {
			$mf->Save();

			Redirect::Page( URLBuilder::getURL( 	array('object_type_id' => $object_type_id, 'object_id' => $object_id, 'id' => $parent_id), 'ViewMessage.php') );
		}
		break;
	case 'submit_message':
		//Debug::setVerbosity(11);
		if ( !$permission->Check('message','enabled')
			OR !( $permission->Check('message','add') ) ) {

			$permission->Redirect( FALSE ); //Redirect

		}

		if ( isset($object_type_id) AND isset($object_id) ) {
			if ( !isset($parent_id) ) {
				$parent_id = 0;
			}

			$mf->setObjectType( $object_type_id );
			$mf->setObject( $object_id );
			$mf->setParent( $parent_id );
			$mf->setPriority();
			$mf->setStatus('UNREAD');
			$mf->setSubject( $message_data['subject'] );
			$mf->setBody( $message_data['body'] );

			if ( $mf->isValid() ) {
				$mf->Save();

				Redirect::Page( URLBuilder::getURL( 	array('object_type_id' => $object_type_id,'object_id' => $object_id, 'id' => $parent_id), 'ViewMessage.php') );

				break;
			}
		}
	default:
		if ( isset($object_type_id) AND isset($object_id) ) {
			BreadCrumb::setCrumb($title);

			$mlf = new MessageListFactory();

			$mlf->getByObjectTypeAndObjectAndId($object_type_id, $object_id, $id);

			$ack_message_id = NULL;
			$require_ack = FALSE;
			$i=0;
			foreach ($mlf as $message) {
				//Get user info
				$ulf = new UserListFactory();

				if ( $message->getRequireAck() == TRUE
						AND $message->isAck() == FALSE
						AND $ack_message_id == NULL
						AND $message->getCreatedBy() != $current_user->getId() ) {
					$require_ack = TRUE;
					$ack_message_id = $message->getId();
				}

				if ( $message->getAckBy() != '' ) {
					$ack_by_full_name = $ulf->getById( $message->getAckBy() )->getCurrent()->getFullName();
				} else {
					$ack_by_full_name = NULL;
				}

				$messages[] = array(
									'id' => $message->getId(),
									'parent_id' => $message->getParent(),
									'object_type' => $message->getObjectType(),
									'object_id' => $message->getObject(),
									'priority' => $message->getPriority(),
									'status' => $message->getStatus(),
									'require_ack' => $message->getRequireAck(),
									'ack_date' => $message->getAckDate(),
									'ack_by' => $message->getAckBy(),
									'ack_by_full_name' => $ack_by_full_name,
									'is_ack' => $message->isAck(),
									'subject' => $message->getSubject(),
									'body' => $message->getBody(),
									'created_date' => $message->getCreatedDate(),
									'created_by' => $message->getCreatedBy(),
									'created_by_full_name' => $ulf->getById( $message->getCreatedBy() )->getCurrent()->getFullName(),
									'updated_date' => $message->getUpdatedDate(),
									'updated_by' => $message->getUpdatedBy(),
									'deleted_date' => $message->getDeletedDate(),
									'deleted_by' => $message->getDeletedBy()
								);

				//Mark own messages as read.
				if ( $message->getCreatedBy() != $current_user->getId() ) {
					$mlf_b = new MessageListFactory();
					$message_obj = $mlf_b->getById( $message->getId() )->getCurrent();
					if ( $message_obj->getStatus() == 10 ) {
						$message_obj->setStatus(20);
						$message_obj->Save();
					}
				}

				if ( $i == 0 ) {
					$parent_id = $message->getId();
					$default_subject = 'Re: '.$message->getSubject();
				}

				$i++;
			}

			//Get object data
			$object_name_options = $mlf->getOptions('object_name');
			$smarty->assign_by_ref('object_name', $object_name_options[$object_type_id]);

			$smarty->assign_by_ref('messages', $messages);
			$smarty->assign_by_ref('message_data', $message_data);

			$smarty->assign_by_ref('default_subject', $default_subject);

			$smarty->assign_by_ref('total_messages', $i);

			$smarty->assign_by_ref('require_ack', $require_ack);
			$smarty->assign_by_ref('ack_message_id', $ack_message_id);
			$smarty->assign_by_ref('current_date', TTDate::getTime() );

			$smarty->assign_by_ref('id', $id);
			$smarty->assign_by_ref('object_type_id', $object_type_id);
			$smarty->assign_by_ref('object_id', $object_id);
		}

		break;
}

$smarty->assign_by_ref('mf', $mf);

$smarty->display('message/ViewMessage.tpl');
?>