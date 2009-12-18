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
class GovernmentForms_US_940 extends GovernmentForms_US {
	public $pdf_template = '940.pdf';

	public $payment_cutoff_amount = 7000; //Line5

	public $futa_tax_before_adjustment_rate = 0.008; //Line8

	public $futa_tax_rate = 0.054; //Line9

	public $line_16_cutoff_amount = 500; //Line16

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
										'value' => '940 for '. $this->year,
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 53,
															'y' => 28,
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
															'x' => 459,
															'y' => 151,
															'h' => 8,
															'w' => 20,
															'halign' => 'C',
															'fill_color' => array( 245, 245, 245 ),
															),
										'font' => array(
																'size' => 9 )
									),
								array(
										'value' => '('. $this->year .')',
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 554,
															'y' => 751,
															'h' => 11,
															'w' => 25,
															'halign' => 'C',
															'fill_color' => array( 255, 255, 255 ),
															),
										'font' => array(
																'size' => 7 )
									),
								array(
										'value' => $this->year,
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 292,
															'y' => 302.5,
															'h' => 6,
															'w' => 25,
															'halign' => 'C',
															'text_color' => array( 255, 255, 255 ),
															'fill_color' => array( 30, 30, 30 ),
															),
										'font' => array(
																'size' => 9,
																'type' => 'B')
									),
								array(
										'value' => $this->year,
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 351,
															'y' => 579.5,
															'h' => 6,
															'w' => 25,
															'halign' => 'C',
															'text_color' => array( 255, 255, 255 ),
															'fill_color' => array( 30, 30, 30 ),
															),
										'font' => array(
																'size' => 9,
																'type' => 'B')
									),
								array(
										'value' => $this->year,
										'on_background' => TRUE,
										'coordinates' => array(
															'x' => 227,
															'y' => 673,
															'h' => 10,
															'w' => 22,
															'halign' => 'C',
															'fill_color' => array( 245, 245, 245 ),
															),
										'font' => array(
																'size' => 9 )
									),
								//Finish initializing page 1.

								'ein' => array(
												'page' => 1,
												'template_page' => 1,
												'function' => 'drawChars', //custom drawing function.
												'coordinates' => array(
																	   array( 'type' => 'static', //static or relative
																		'x' => 155,
																		'y' => 68,
																		'h' => 18,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 180,
																		'y' => 68,
																		'h' => 18,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 218,
																		'y' => 68,
																		'h' => 18,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 243,
																		'y' => 68,
																		'h' => 18,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 268,
																		'y' => 68,
																		'h' => 18,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 294,
																		'y' => 68,
																		'h' => 18,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 320,
																		'y' => 68,
																		'h' => 18,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 345,
																		'y' => 68,
																		'h' => 18,
																		'w' => 19,
																		'halign' => 'C',
																		),
																	   array(
																		'x' => 370,
																		'y' => 68,
																		'h' => 18,
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
																		'x' => 136,
																		'y' => 92,
																		'h' => 18,
																		'w' => 252,
																		'halign' => 'L',
																		),
											),
								'trade_name' => array(
												'coordinates' => array(
																		'x' => 115,
																		'y' => 116,
																		'h' => 18,
																		'w' => 273,
																		'halign' => 'L',
																		),
											),
								'address' => array(
												'coordinates' => array(
																		'x' => 79,
																		'y' => 140,
																		'h' => 18,
																		'w' => 310,
																		'halign' => 'L',
																		),
											),
								'city' => array(
												'coordinates' => array(
																		'x' => 79,
																		'y' => 165,
																		'h' => 18,
																		'w' => 186,
																		'halign' => 'L',
																		),
											),
								'state' => array(
												'coordinates' => array(
																		'x' => 274,
																		'y' => 165,
																		'h' => 18,
																		'w' => 36,
																		'halign' => 'C',
																		),
											),
								'zip_code' => array(
												'coordinates' => array(
																		'x' => 317,
																		'y' => 165,
																		'h' => 18,
																		'w' => 72,
																		'halign' => 'C',
																		),
											),

								'return_type' => array(
												'function' => 'drawCheckBox',
												'coordinates' => array(
																		'a' => array(
																			'x' => 424,
																			'y' => 102,
																			'h' => 11,
																			'w' => 12,
																			'halign' => 'C',
																			),
																		'b' => array(
																			'x' => 424,
																			'y' => 120,
																			'h' => 11,
																			'w' => 12,
																			'halign' => 'C',
																			),
																		'c' => array(
																			'x' => 424,
																			'y' => 138,
																			'h' => 11,
																			'w' => 12,
																			'halign' => 'C',
																			),
																		'd' => array(
																			'x' => 424,
																			'y' => 156,
																			'h' => 11,
																			'w' => 12,
																			'halign' => 'C',
																			),
																	  ),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )
											),

								'l1a' => array(
												'function' => 'drawChars',
												'coordinates' => array(
																		array(
																			'x' => 309,
																			'y' => 234,
																			'h' => 18,
																			'w' => 22,
																			'halign' => 'C',
																			),
																		array(
																			'x' => 338,
																			'y' => 234,
																			'h' => 18,
																			'w' => 23,
																			'halign' => 'C',
																			),
																	  ),
											),
								'l1b' => array(
												'function' => 'drawCheckbox',
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 265,
																		'h' => 8,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )

											),
								'l3' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 320,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 320,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l4' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 310,
																		'y' => 344,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 344,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l4a' => array(
												'function' => 'drawCheckbox',
												'coordinates' => array(
																	array(
																		'x' => 158.5,
																		'y' => 368.5,
																		'h' => 8,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )

											),
								'l4b' => array(
												'function' => 'drawCheckbox',
												'coordinates' => array(
																	array(
																		'x' => 158.5,
																		'y' => 381,
																		'h' => 8,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )

											),
								'l4c' => array(
												'function' => 'drawCheckbox',
												'coordinates' => array(
																	array(
																		'x' => 288,
																		'y' => 369,
																		'h' => 8,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )

											),
								'l4d' => array(
												'function' => 'drawCheckbox',
												'coordinates' => array(
																	array(
																		'x' => 288,
																		'y' => 381,
																		'h' => 8,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )

											),
								'l4e' => array(
												'function' => 'drawCheckbox',
												'coordinates' => array(
																	array(
																		'x' => 396.5,
																		'y' => 369,
																		'h' => 8,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )

											),

								'l5' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 310,
																		'y' => 398,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 410,
																		'y' => 398,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l6' => array(
												'function' => array( 'calcL6', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 422,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 422,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l7' => array(
												'function' => array( 'calcL7', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 447,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 447,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l8' => array(
												'function' => array( 'calcL8', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 469,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 469,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l9' => array(
												'function' => array( 'calcL9', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 505,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 505,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l10' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 535,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 535,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l12' => array(
												'function' => array( 'calcL12', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 598,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 598,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l13' => array(
												'function' => 'drawSplitDecimalFloat',
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 621,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 621,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l14' => array(
												'function' => array( 'calcL14', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 664,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 664,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l15' => array(
												'function' => array( 'calcL15', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 454,
																		'y' => 687,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 554,
																		'y' => 687,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l15a' => array(
												'function' => array( 'filterL15', 'drawCheckbox'),
												'coordinates' => array(
																	array(
																		'x' => 490.5,
																		'y' => 706,
																		'h' => 8,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )
											),
								'l15b' => array(
												'function' => array( 'filterL15', 'drawCheckbox'),
												'coordinates' => array(
																	array(
																		'x' => 490.5,
																		'y' => 718,
																		'h' => 8,
																		'w' => 10,
																		'halign' => 'C',
																		),
																	),
												'font' => array (
																		'size' => 10,
																		'type' => 'B' )

											),

								//Initialize Page 2
								array(
												'page' => 2,
												'template_page' => 2,
												'value' => $this->name,
												'coordinates' => array(
																		'x' => 37,
																		'y' => 65,
																		'h' => 15,
																		'w' => 355,
																		'halign' => 'L',
																		),
											),
								array(
												'value' => $this->ein,
												'coordinates' => array(
																		'x' => 400,
																		'y' => 65,
																		'h' => 15,
																		'w' => 175,
																		'halign' => 'C',
																		),
											),

								array(
												'value' => '('. $this->year .')',
												'on_background' => TRUE,
												'coordinates' => array(
																		'x' => 554,
																		'y' => 697,
																		'h' => 11,
																		'w' => 25,
																		'halign' => 'C',
																		'fill_color' => array( 255, 255, 255 ),
																		),
												'font' => array(
																		'size' => 7 )
											),
								//Finish initialize Page 2

								'l16a' => array(
												'page' => 2,
												'template_page' => 2,
												'function' => array( 'filterL16', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 346,
																		'y' => 127,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 446,
																		'y' => 127,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l16b' => array(
												'function' => array( 'filterL16', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 346,
																		'y' => 152,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 446,
																		'y' => 152,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l16c' => array(
												'function' => array( 'filterL16', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 346,
																		'y' => 177,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 446,
																		'y' => 177,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l16d' => array(
												'function' => array( 'filterL16', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 346,
																		'y' => 200,
																		'h' => 18,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 446,
																		'y' => 200,
																		'h' => 18,
																		'w' => 23,
																		'halign' => 'C',
																		),
																	),
											),
								'l17' => array(
												'function' => array( 'calcL17', 'drawSplitDecimalFloat' ),
												'coordinates' => array(
																	array(
																		'x' => 346,
																		'y' => 224.5,
																		'h' => 17,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 446,
																		'y' => 224.5,
																		'h' => 17,
																		'w' => 22,
																		'halign' => 'C',
																		),
																	),
											),

								//Initialize Page 3
								array(
												'page' => 3,
												'template_page' => 3,
												'value' => substr( $this->year, 2, 2),
												'on_background' => TRUE,
												'coordinates' => array(
																		'x' => 525,
																		'y' => 588,
																		'h' => 0,
																		'w' => 30,
																		'halign' => 'L',
																		'fill_color' => array( 255, 255, 255 ),
																		),
												'font' => array(
																		'size' => 21,
																		'type' => 'B' )
											),
								array(
												'value' => $this->year,
												'on_background' => TRUE,
												'coordinates' => array(
																		'x' => 253,
																		'y' => 146,
																		'h' => 11,
																		'w' => 22,
																		'halign' => 'C',
																		'fill_color' => array( 255, 255, 255 ),
																		),
												'font' => array(
																		'size' => 10 )
											),
								array(
												'value' => $this->year,
												'on_background' => TRUE,
												'coordinates' => array(
																		'x' => 341,
																		'y' => 195,
																		'h' => 11,
																		'w' => 22,
																		'halign' => 'C',
																		'fill_color' => array( 255, 255, 255 ),
																		),
												'font' => array(
																		'size' => 10 )
											),
								array(
												'value' => $this->year,
												'on_background' => TRUE,
												'coordinates' => array(
																		'x' => 373,
																		'y' => 240,
																		'h' => 11,
																		'w' => 22,
																		'halign' => 'C',
																		'fill_color' => array( 255, 255, 255 ),
																		),
												'font' => array(
																		'size' => 10 )
											),
								array(
												'value' => $this->year,
												'on_background' => TRUE,
												'coordinates' => array(
																		'x' => 525,
																		'y' => 250,
																		'h' => 11,
																		'w' => 22,
																		'halign' => 'C',
																		'fill_color' => array( 255, 255, 255 ),
																		),
												'font' => array(
																		'size' => 10 )
											),
								//Finish initialize Page 3

								array(
												'page' => 3,
												'template_page' => 3,
												'function' => 'drawPage3EIN',
												'coordinates' => array(
																	array(
																		'x' => 56,
																		'y' => 635,
																		'h' => 15,
																		'w' => 30,
																		'halign' => 'C',
																		),
																	array(
																		'x' => 95,
																		'y' => 635,
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
																		'x' => 435,
																		'y' => 623,
																		'h' => 17,
																		'w' => 95,
																		'halign' => 'R',
																		),
																	array(
																		'x' => 535,
																		'y' => 623,
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
																		'x' => 229,
																		'y' => 661,
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
																		'x' => 229,
																		'y' => 684,
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
																		'x' => 229,
																		'y' => 708,
																		'h' => 15,
																		'w' => 250,
																		'halign' => 'L',
																		),
												'font' => array(
																		'size' => 10 )
											),

							  );

		if ( isset($template_schema[$name]) ) {
			return $name;
		} else {
			return $template_schema;
		}
	}

	function filterL15( $value ) {
		if ( $this->l15 > 0 ) {
			return $value;
		}

		return FALSE;
	}

	function filterL16( $value ) {
		if ( $this->l12 > $this->line_16_cutoff_amount ) {
			return $value;
		}

		return FALSE;
	}

	function drawPage3EIN( $value, $schema ) {
		$value = $this->ein;

		$this->Draw( substr( $value, 0, 2 ), $this->getSchemaSpecificCoordinates( $schema, 0 ) );
		$this->Draw( substr( $value, 2, 7 ), $this->getSchemaSpecificCoordinates( $schema, 1 ) );
		return TRUE;
	}

	function calcL6( $value, $schema ) {
		//Subtotal: Line 4 + Line 5
		$this->l6 = $this->l4 + $this->l5;
		return $this->l6;
	}
	function calcL7( $value, $schema ) {
		//Total Taxable FUTA wages: Line 3 - Line 6
		$this->l7 = $this->l3 - $this->l6;
		return $this->l7;
	}
	function calcL8( $value, $schema ) {
		//FUTA tax before adjustments
		$this->l8 = $this->l7 * $this->futa_tax_before_adjustment_rate;
		return $this->l8;
	}
	function calcL9( $value, $schema ) {
		//Taxable FUTA wages

		//Either fill out line 9, or line 10. So if line 10 is filled out, ignore this.
		if ( $this->l10 == '' ) {
			$this->l9 = $this->l7 * $this->futa_tax_rate;
			return $this->l9;
		}

		return FALSE;
	}
	function calcL12( $value, $schema ) {
		//Total FUTA tax after adjustments
		$this->l12 = $this->l8 + $this->l9 + $this->l10;
		return $this->l12;
	}
	function calcL14( $value, $schema ) {
		//Balance Due
		if ( $this->l12 > $this->l13 ) {
			$this->l14 = $this->l12 - $this->l13;
			return $this->l14;
		}

		return FALSE;
	}
	function calcL15( $value, $schema ) {
		//Balance Due

		if ( $this->l13 > $this->l12 ) {
			$this->l15 = $this->l13 - $this->l12;
			return $this->l15;
		}

		return FALSE;
	}
	function calcL17( $value, $schema ) {
		//Total tax liability for the year
		if ( $this->l12 > $this->line_16_cutoff_amount ) {
			$this->l17 = $this->l16a + $this->l16b + $this->l16c + $this->l16d;

			if ( $this->MoneyFormat( $this->l17 ) != $this->MoneyFormat( $this->l12 ) ) {
				$schema['coordinates'][0]['fill_color'] = array( 255, 0, 0 );
				$schema['coordinates'][1]['fill_color'] = array( 255, 0, 0 );
			}
			return $this->l17;
		}

		return FALSE;
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