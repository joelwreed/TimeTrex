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
 * $Revision: 676 $
 * $Id: Cache.inc.php 676 2007-03-07 23:47:29Z ipso $
 * $Date: 2007-03-07 15:47:29 -0800 (Wed, 07 Mar 2007) $
 */
require_once( Environment::getBasePath() .'classes'. DIRECTORY_SEPARATOR .'cache_lite'. DIRECTORY_SEPARATOR .'Hashed_Cache_Lite.php');

//If caching is disabled, still do memory caching, otherwise permission checks
//cause the page to take 2+ seconds to load.
if ( $config_vars['cache']['enable'] == FALSE ) {
	$config_vars['cache']['only_memory_cache_enable'] = TRUE;
} else {
	$config_vars['cache']['only_memory_cache_enable'] = FALSE;
}

$cache_options = array(
		'caching' => TRUE,
		'cacheDir' => $config_vars['cache']['dir'] . DIRECTORY_SEPARATOR,
		'lifeTime' => 86400, //604800, //One day, cache should be cleared when the data is modified
		'fileLocking' => TRUE,
		'writeControl' => TRUE,
		'readControl' => TRUE,
		'memoryCaching' => TRUE,
		'onlyMemoryCaching' => $config_vars['cache']['only_memory_cache_enable'],
		'automaticSerialization' => FALSE
);

$cache = new Hashed_Cache_Lite($cache_options);
?>