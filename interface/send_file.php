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
 * $Revision: 2331 $
 * $Id: send_file.php 2331 2009-01-13 00:16:13Z ipso $
 * $Date: 2009-01-12 16:16:13 -0800 (Mon, 12 Jan 2009) $
 */
require_once('../includes/global.inc.php');

require_once('PEAR.php');
require_once('HTTP/Download.php');

extract	(FormVariables::GetVariables(
										array	(
												'action',
												'object_type',
												'object_id',
												'parent_id',
												) ) );
$object_type = strtolower($object_type);

if ( $object_type != 'primary_company_logo' ) {
	$skip_message_check = TRUE;
	require_once(Environment::getBasePath() .'includes/Interface.inc.php');
}

switch ($object_type) {
	case 'document':
		Debug::Text('Document...', __FILE__, __LINE__, __METHOD__,10);

		$drlf = new DocumentRevisionListFactory();
		$drlf->getByIdAndDocumentId( $object_id, $parent_id );
		Debug::Text('Record Count: '. $drlf->getRecordCount(), __FILE__, __LINE__, __METHOD__,10);
		if ( $drlf->getRecordCount() == 1 ) {
			//echo "File Name: $file_name<br>\n";
			$dr_obj = $drlf->getCurrent();

			$file_name = $dr_obj->getStoragePath().$dr_obj->getLocalFileName();
			Debug::Text('File Name: '. $file_name, __FILE__, __LINE__, __METHOD__,10);
			if ( file_exists($file_name) ) {
				$params['file'] = $file_name;
				$params['ContentType'] = $dr_obj->getMimeType();
				$params['ContentDisposition'] = array( HTTP_DOWNLOAD_ATTACHMENT, $dr_obj->getRemoteFileName() );
				$params['cache'] = FALSE;
			}
		}
		break;
	case 'invoice_config':
		Debug::Text('Invoice Config...', __FILE__, __LINE__, __METHOD__,10);

		$file_name = InvoiceConfigFactory::getLogoFileName( $current_company->getId() );
		Debug::Text('File Name: '. $file_name, __FILE__, __LINE__, __METHOD__,10);
		if ( file_exists($file_name) ) {
			$params['file'] = $file_name;
			$params['cache'] = TRUE;
		}
		break;
	case 'company_logo':
		Debug::Text('Company Logo...', __FILE__, __LINE__, __METHOD__,10);

		$cf = new CompanyFactory();
		$file_name = $cf->getLogoFileName( $current_company->getId() );
		Debug::Text('File Name: '. $file_name, __FILE__, __LINE__, __METHOD__,10);
		if ( $file_name != '' AND file_exists($file_name) ) {
			$params['file'] = $file_name;
			$params['cache'] = TRUE;
		}
		break;
	case 'primary_company_logo':
		Debug::Text('Primary Company Logo...', __FILE__, __LINE__, __METHOD__,10);

		$cf = new CompanyFactory();
		$file_name = $cf->getLogoFileName( PRIMARY_COMPANY_ID );
		Debug::Text('File Name: '. $file_name, __FILE__, __LINE__, __METHOD__,10);
		if ( $file_name != '' AND file_exists($file_name) ) {
			$params['file'] = $file_name;
			$params['cache'] = TRUE;
		}
		break;
	default:
		break;
}

if ( isset($params) ) {
	HTTP_Download::staticSend($params);
} else {
	echo "File does not exist, unable to download!<br>\n";
	Debug::writeToLog();
}
?>