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
 * $Revision: 2286 $
 * $Id: CA.class.php 2286 2008-12-12 23:12:41Z ipso $
 * $Date: 2008-12-12 15:12:41 -0800 (Fri, 12 Dec 2008) $
 */

/**
 * @package GovernmentForms
 */
include_once( 'US.class.php' );
class GovernmentForms_US_941 extends GovernmentForms_US {
	public $pdf_template = '941.pdf';
	//public $template_offsets = array( 0, -33 ); //x, y

	public $social_security_rate = 0.124; //Line: 5a2, 5b2

	public $medicare_rate = 0.029; //Line: 5c2

	public $line_17_cutoff_amount = 2500; //Line 17

	public function getFilterFunction( $name ) {
		$variable_function_map = array(
										'year' => 'isNumeric',
										'ein' => array( 'stripNonNumeric', 'isNumeric'),
						  );

		if ( isset($variable_function_map[$name]) ) {
			return $variable_function_map[$name];
		}

		return FALSE;
	}

	public function getTemplateSchema( $name = NULL ) {
		$template_schema = array(
								//Initialize page1, replace years on template.
								array(
										'page' => 1,
										'template_page' => 1,
										'value' => 'Form',
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 36,
															'y' => 74,
															'h' => 20,
															'w' => 22,
															'halign' => 'L',
															'fill_color' => array( 255, 255, 255 ),
															),
										'font' => array(
																'size' => 8,
																'type' => '' )
									),

								array(
										'value' => '941 for '. $this->year,
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 57,
															'y' => 66,
															'h' => 28,
															'w' => 99,
															'halign' => 'C',
															'fill_color' => array( 255, 255, 255 ),
															),
										'font' => array(
																'size' => 18,
																'type' => 'B' )
									),

								array(
										'value' => $this->year,
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 527,
															'y' => 107,
															'h' => 8,
															'w' => 20,
															'halign' => 'C',
															'text_color' => array( 255, 255, 255 ),
															'fill_color' => array( 30, 30, 30 ),
															),
										'font' => array(
																'size' => 10,
																'type' => 'B' )
									),

								array(
										'value' => '('. $this->year .')',
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 519,
															'y' => 766,
															'h' => 11,
															'w' => 45,
															'halign' => 'C',
															'fill_color' => array( 255, 255, 255 ),
															),
										'font' => array(
																'size' => 7 )
									),
								//Finish initializing page 1.

								'ein' => array(
												'page' => 1,
												'template_page' => 1,
												'function' => 'drawChars', //custom drawing function.
												'coordinates' => array(
																	   array( 'type' => 'static', //static or relative
																		'x' => 151,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 176,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 213,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 238,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 262,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 287,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 312,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 336,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 361,
																		'y' => 102,
																		'h' => 17,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	  ),
												'font' => array (
																		'size' => 12,
																		'type' => 'B' )
											),

								'name' => array(
												'coordinates' => array(
																		'x' => 134,
																		'y' => 125,
																		'h' => 18,
																		'w' => 246,
																		'halign' => 'L',
																		),
											),

								'trade_name' => array(
												'coordinates' => array(
																		'x' => 113,
																		'y' => 149,
																		'h' => 18,
																		'w' => 267,
																		'halign' => 'L',
																		),
											),

								'address' => array(
												'coordinates' => array(
																		'x' => 78,
																		'y' => 172,
																		'h' => 18,
																		'w' => 302,
																		'halign' => 'L',
																		),
											),

								'city' => array(
												'coordinates' => array(
																		'x' => 78,
																		'y' => 198,
																		'h' => 18,
																		'w' => 182,
																		'halign' => 'L',
																		),
											),
								'state' => array(
												'coordinates' => array(
																		'x' => 268,
																		'y' => 198,
																		'h' => 18,
																		'w' => 35,
																		'halign' => 'C',
																		),
											),
								'zip_code' => array(
												'coordinates' => array(
																		'x' => 310,
																		'y' => 198,
																		'h' => 18,
																		'w' => 70,
																		'halign' => 'C',
																		),
											),


								'quarter' => array(
												'function' => 'drawCheckBox',
												'coordinates' => array(
																		1 => array(
																			'x' => 414,
																			'y' => 136,
																			'h' => 10,
																			'w' => 11,
																			'halign' => 'C',
																			),
																		2 => array(
																			'x' => 414,
																			'y' => 153,
																			'h' => 10,
																			'w' => 11,
																			'halign' => 'C',
																			),
																		3 => array(
																			'x' => 414,
																			'y' => 171,
																			'h' => 10,
																			'w' => 11,
																			'halign' => 'C',
																			),
																		4 => array(
																			'x' => 414,
																			'y' => 188,
																			'h' => 10,
																			'w' => 11,
																			'halign' => 'C',
																			),
																	  ),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )
											),

								'l1' => array(
												'coordinates' => array(
																		'x' => 431,
																		'y' => 257,
																		'h' => 15,
																		'w' => 128,
																		'halign' => 'C',
																	),
											),

								'l2' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 275,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 275,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l3' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 292,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 292,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l4' => array(
												'function' => 'drawCheckbox',
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 314,
																		'h' => 6,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 8,
																		'type' => 'B' )
											),
								'l5a' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 190,
																		'y' => 351,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 269,
																		'y' => 351,
																		'h' => 14,
																		'w' => 20,
																		'halign' => 'C',
																		),
																	),
											),
								'l5b' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 190,
																		'y' => 369,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 269,
																		'y' => 369,
																		'h' => 14,
																		'w' => 20,
																		'halign' => 'C',
																		),
																	),
											),
								'l5c' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 190,
																		'y' => 386,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 269,
																		'y' => 386,
																		'h' => 14,
																		'w' => 20,
																		'halign' => 'C',
																		),
																	),
											),
								'l5a2' => array(
												'function' => array('calcL5A2', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 351,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 351,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l5b2' => array(
												'function' => array('calcL5B2', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 369,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 369,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l5c2' => array(
												'function' => array('calcL5C2', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 386,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 386,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l5d' => array(
												'function' => array('calcL5D', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 409,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 409,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l6' => array(
												'function' => array('calcL6', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 426,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 426,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l7a' => array(
												'function' => array('calcL7A', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 462,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 462,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l7b' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 480,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 480,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l7c' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 497,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 497,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l7d' => array(
												'function' => array('calcL7D', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 520,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 520,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l8' => array(
												'function' => array('calcL8', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 538,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 538,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l9' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 555,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 555,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l10' => array(
												'function' => array('calcL10', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 573,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 573,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l11' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 608,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 608,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l12a' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 631,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 631,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l12b' => array(
												'coordinates' => array(
																	'x' => 254,
																	'y' => 655,
																	'h' => 14,
																	'w' => 76,
																	'halign' => 'C',
																	),
											),

								'l13' => array(
												'function' => array('calcL13', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 678,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 678,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l14' => array(
												'function' => array('calcL14', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 431,
																		'y' => 702,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 533,
																		'y' => 702,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l15' => array(
												'function' => array('calcL15', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 331,
																		'y' => 725,
																		'h' => 14,
																		'w' => 75,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 725,
																		'h' => 14,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	),
											),
								'l15a' => array(
												'function' => array('filterL15A', 'drawCheckbox'),
												'coordinates' => array(
																	array(
																		'x' => 478,
																		'y' => 718,
																		'h' => 6,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 8,
																		'type' => 'B' )
											),
								'l15b' => array(
												'function' => array('filterL15B', 'drawCheckbox'),
												'coordinates' => array(
																	array(
																		'x' => 478,
																		'y' => 729,
																		'h' => 6,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 8,
																		'type' => 'B' )
											),

								//Initialize Page 2
								array(
												'page' => 2,
												'template_page' => 2,
												'value' => $this->name,
												'coordinates' => array(
																		'x' => 36,
																		'y' => 88,
																		'h' => 15,
																		'w' => 350,
																		'halign' => 'L',
																		),
											),
								array(
												'value' => $this->ein,
												'coordinates' => array(
																		'x' => 388,
																		'y' => 88,
																		'h' => 15,
																		'w' => 175,
																		'halign' => 'C',
																		),
											),
								array(
										'value' => '('. $this->year .')',
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 519,
															'y' => 766,
															'h' => 11,
															'w' => 45,
															'halign' => 'C',
															'fill_color' => array( 255, 255, 255 ),
															),
										'font' => array(
																'size' => 7 )
									),
								//Finish initialize Page 2

								'l16' => array(
												'function' => 'drawChars',
												'coordinates' => array(
																		array(
																			'x' => 64,
																			'y' => 144,
																			'h' => 18,
																			'w' => 18,
																			'halign' => 'C',
																			),
																		array(
																			'x' => 85,
																			'y' => 144,
																			'h' => 18,
																			'w' => 18,
																			'halign' => 'C',
																			),
																	  ),
											),
								'l17_month1' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 232,
																		'y' => 223,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 334,
																		'y' => 223,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l17_month2' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 232,
																		'y' => 246,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 334,
																		'y' => 246,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l17_month3' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 232,
																		'y' => 270,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 334,
																		'y' => 270,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								'l17_month_total' => array(
												'function' => array('calcL17MonthTotal', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 232,
																		'y' => 293,
																		'h' => 14,
																		'w' => 99,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 334,
																		'y' => 293,
																		'h' => 14,
																		'w' => 26,
																		'halign' => 'C',
																		),
																	),
											),
								//Put this after Month1,Month2,Month3 are set, as we can automatically determine it for the most part.
								'l17' => array(
												'function' => array( 'filterL17', 'drawCheckbox'),
												'coordinates' => array(
																	'a' => array(
																		'x' => 113,
																		'y' => 169,
																		'h' => 6,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	'b' => array(
																		'x' => 113,
																		'y' => 192,
																		'h' => 6,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	'c' => array(
																		'x' => 113,
																		'y' => 313,
																		'h' => 6,
																		'w' => 10,
																		'halign' => 'C',
																		),

																	),
												'font' => array (
																		'size' => 8,
																		'type' => 'B' )
											),

								//Initialize Page 3
								array(
												'page' => 3,
												'template_page' => 3,
												'value' => substr( $this->year, 2, 2),
												'on_background' => TRUE,
												'coordinates' => array(
																		'x' => 504,
																		'y' => 608,
																		'h' => 0,
																		'w' => 30,
																		'halign' => 'L',
																		'fill_color' => array( 255, 255, 255 ),
																		),
												'font' => array(
																		'size' => 20,
																		'type' => 'B' )
											),
								//Finish initialize Page 3

								array(
												'page' => 3,
												'template_page' => 3,
												'function' => 'drawPage3EIN',
												'coordinates' => array(
																	array(
																		'x' => 54,
																		'y' => 653,
																		'h' => 15,
																		'w' => 30,
																		'halign' => 'C',
																		),
																	array(
																		'x' => 87,
																		'y' => 653,
																		'h' => 15,
																		'w' => 50,
																		'halign' => 'C',
																		)
																	),
												'font' => array(
																		'size' => 10 )
											),

								array(
												'function' => array( 'calcL14', 'drawSplitDecimalFloat'),
												'coordinates' => array(
																	array(
																		'x' => 417,
																		'y' => 642,
																		'h' => 17,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 515,
																		'y' => 642,
																		'h' => 17,
																		'w' => 32,
																		'halign' => 'C',
																		),
																	),
												'font' => array(
																		'size' => 22 )
											),

								array(
												'value' => $this->trade_name,
												'coordinates' => array(
																		'x' => 219,
																		'y' => 677,
																		'h' => 15,
																		'w' => 250,
																		'halign' => 'L',
																		),
												'font' => array(
																		'size' => 10 )
											),
								array(
												'value' => $this->address,
												'coordinates' => array(
																		'x' => 219,
																		'y' => 701,
																		'h' => 15,
																		'w' => 250,
																		'halign' => 'L',
																		),
												'font' => array(
																		'size' => 10 )
											),
								array(
												'value' => $this->city . ', ' . $this->state . ', ' . $this->zip_code,
												'coordinates' => array(
																		'x' => 219,
																		'y' => 724,
																		'h' => 15,
																		'w' => 250,
																		'halign' => 'L',
																		),
												'font' => array(
																		'size' => 10 )
											),
								array(
												'function' => array('drawPage3Quarter', 'drawCheckBox'),
												'coordinates' => array(
																		1 => array(
																			'x' => 56,
																			'y' => 690,
																			'h' => 10,
																			'w' => 11,
																			'halign' => 'C',
																			),
																		2 => array(
																			'x' => 56,
																			'y' => 719,
																			'h' => 10,
																			'w' => 11,
																			'halign' => 'C',
																			),
																		3 => array(
																			'x' => 144,
																			'y' => 690,
																			'h' => 10,
																			'w' => 11,
																			'halign' => 'C',
																			),
																		4 => array(
																			'x' => 144,
																			'y' => 719,
																			'h' => 10,
																			'w' => 11,
																			'halign' => 'C',
																			),
																	  ),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )
											),

							  );

		if ( isset($template_schema[$name]) ) {
			return $name;
		} else {
			return $template_schema;
		}
	}

	function filterL15A( $value, $schema ) {
		if ( $this->l15 > 0 ) {
			return $value;
		}

		return FALSE;
	}
	function filterL15B( $value, $schema ) {
		if ( $this->l15 > 0 ) {
			return $value;
		}

		return FALSE;
	}

	function filterL17( $value, $schema ) {
		if ( $this->l10 < $this->line_17_cutoff_amount ) {
			$value = array('a');
			unset($this->l17_month1, $this->l17_month2, $this->l17_month3, $this->l17_month_total);
		} elseif ( $this->l17_month1 > 0 OR $this->l17_month2 > 0 OR $this->l17_month3 > 0 ) {
			$value = array('b');
		} else {
			$value = array('c');
		}

		return $value;
	}

	function drawPage3Quarter( $value, $schema ) {
		return $this->quarter;
	}
	function drawPage3EIN( $value, $schema ) {
		$value = $this->ein;

		$this->Draw( substr( $value, 0, 2 ), $this->getSchemaSpecificCoordinates( $schema, 0 ) );
		$this->Draw( substr( $value, 2, 7 ), $this->getSchemaSpecificCoordinates( $schema, 1 ) );
		return TRUE;
	}
	
	function calcL5A2( $value, $schema ) {
		$this->l5a2 = $this->l5a * $this->social_security_rate;
		return $this->l5a2;
	}
	function calcL5B2( $value, $schema ) {
		$this->l5b2 = $this->l5b * $this->social_security_rate;
		return $this->l5b2;
	}
	function calcL5C2( $value, $schema ) {
		$this->l5c2 = $this->l5c * $this->medicare_rate;
		return $this->l5c2;
	}
	function calcL5D( $value, $schema ) {
		$this->l5d = $this->l5a2 + $this->l5b2 + $this->l5c2;

		if ( $this->l5d > 0 ) {
			$this->l4 = TRUE;
		} else {
			$this->l4 = FALSE;
		}

		return $this->l5d;
	}
	function calcL6( $value, $schema ) {
		$this->l6 = $this->l3 + $this->l5d;
		return $this->l6;
	}
	function calcL7A( $value, $schema ) {
		/*
		$this->l7a = ( $this->MoneyFormat( $this->l5d, FALSE ) - ( $this->l5a2 + $this->l5b2 + $this->l5c2 ) );

		return $this->l7a;
		*/
		return $value;
	}

	function calcL7D( $value, $schema ) {
		$this->l7d = $this->l7a + $this->l7b + $this->l7c;
		return $this->l7d;
	}
	function calcL8( $value, $schema ) {
		$this->l8 = $this->l6 + $this->l7d;
		return $this->l8;
	}
	function calcL10( $value, $schema ) {
		$this->l10 = $this->l8 - $this->l9;
		return $this->l10;
	}
	function calcL13( $value, $schema ) {
		$this->l13 = $this->l11 + $this->l12a;
		return $this->l13;
	}
	function calcL14( $value, $schema ) {
		if ( $this->l10 > $this->l13 ) {
			$this->l14 = $this->l10 - $this->l13;
			return $this->l14;
		}
	}
	function calcL15( $value, $schema ) {
		if ( $this->l13 > $this->l10 ) {
			$this->l15 = $this->l13 - $this->l10;
			return $this->l15;
		}
	}

	function calcL17MonthTotal( $value, $schema ) {
		$this->l17_month_total = $this->l17_month1 + $this->l17_month2 + $this->l17_month3;
		return $this->l17_month_total;
	}

	function _outputPDF() {
		//Initialize PDF with template.
		$pdf = $this->getPDFObject();

		if ( $this->getShowBackground() == TRUE ) {
			$pdf->setSourceFile( $this->getTemplateDirectory() . DIRECTORY_SEPARATOR . $this->pdf_template );

			$this->template_index[1] = $pdf->ImportPage(1);
			$this->template_index[2] = $pdf->ImportPage(2);
			$this->template_index[3] = $pdf->ImportPage(3);
		}

		if ( $this->year == ''  ) {
			$this->year = $this->getYear();
		}

		//Get location map, start looping over each variable and drawing
		$template_schema = $this->getTemplateSchema();
		if ( is_array( $template_schema) ) {

			$template_page = NULL;

			foreach( $template_schema as $field => $schema ) {
				$this->Draw( $this->$field, $schema );
			}
		}

		return TRUE;
	}
}
?>