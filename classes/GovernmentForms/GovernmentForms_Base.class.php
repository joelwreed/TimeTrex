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
class GovernmentForms_Base {

	public $debug = FALSE;
	public $data = NULL; //Form data is stored here in an array.

	public $class_directory = NULL;

	/*
	 * PDF related variables
	 */
	public $template_index = array();
	public $current_template_index = NULL;
	public $page_offsets = array( 0, 0 ); //x, y
	public $template_offsets = array( 0, 0 ); //x, y
	public $show_background = TRUE; //Shows the PDF background
	public $default_font = 'freeserif';


	function setDebug( $bool ) {
		$this->debug = $bool;
	}
	function getDebug() {
		return $this->debug;
	}

	function setClassDirectory( $dir ) {
		$this->class_directory = $dir;
	}
	function getClassDirectory() {
		return $this->class_directory;
	}

	function Output( $type ) {
		switch ( strtolower($type) ) {
			case 'pdf':
				return $this->_outputPDF();
				break;
		}
	}

	/*
	 *
	 * Math functions
	 *
	 */
	function MoneyFormat($value, $pretty = TRUE) {
		if ( $pretty == TRUE ) {
			$thousand_sep = ',';
		} else {
			$thousand_sep = '';
		}

		return number_format( $value, 2, '.', $thousand_sep);
	}

	function getBeforeDecimal($float) {
		$float = $this->MoneyFormat( $float, FALSE );

		$float_array = split('\.', $float);

		if ( isset($float_array[0]) ) {
			return $float_array[0];
		}

		return FALSE;
	}

	function getAfterDecimal($float, $format_number = TRUE ) {
		if ( $format_number == TRUE ) {
			$float = $this->MoneyFormat( $float, FALSE );
		}

		$float_array = split('\.', $float);

		if ( isset($float_array[1]) ) {
			return str_pad($float_array[1],2,'0');
		}

		return FALSE;
	}


	/*
	 *
	 * Date functions
	 *
	 */
	public function getYear($epoch = NULL) {
		if ($epoch == NULL) {
			$epoch = TTDate::getTime();
		}

		return date('Y', $epoch);
	}

	/*
	 *
	 * Validation functions
	 *
	 */
	function isNumeric( $value ) {
		if ( is_numeric( $value ) ) {
			return $value;
		}

		return FALSE;
	}

	/*
	 *
	 * Filter functions
	 *
	 */
	function stripSpaces($value) {
		return str_replace(' ', '', trim($value));
	}

	function stripNonNumeric($value) {
		$retval = preg_replace('/[^0-9]/','',$value);

		return $retval;
	}

	function stripNonAlphaNumeric($value) {
		$retval = preg_replace('/[^A-Za-z0-9]/','',$value);

		return $retval;
	}

	function stripNonFloat($value) {
		$retval = preg_replace('/[^-0-9\.]/','',$value);

		return $retval;
	}

	/*
	 *
	 * PDF helper functions
	 *
	 */
	function setPDFObject( &$obj ) {
		$this->pdf_object = $obj;
		return TRUE;
	}
	function getPDFObject() {
		return $this->pdf_object;
	}

	function setShowBackground( $bool ) {
		$this->show_background = $bool;
		return TRUE;
	}
	function getShowBackground() {
		return $this->show_background;
	}

	function setPageOffsets( $x, $y ) {
		$this->page_offsets = array( $x, $y );
		return TRUE;
	}
	function getPageOffsets( $type = NULL ) {
		switch ( strtolower($type) ) {
			case 'x':
				return $this->page_offsets[0];
				break;
			case 'y':
				return $this->page_offsets[1];
				break;
			default:
				return $this->page_offsets;
				break;
		}
	}
	function setTemplateOffsets( $x, $y ) {
		$this->template_offsets = array( $x, $y );
		return TRUE;
	}
	function getTemplateOffsets( $type = NULL ) {
		switch ( strtolower($type) ) {
			case 'x':
				return $this->template_offsets[0];
				break;
			case 'y':
				return $this->template_offsets[1];
				break;
			default:
				return $this->template_offsets;
				break;
		}
	}

	function getTemplateDirectory() {
		$dir = $this->getClassDirectory() . DIRECTORY_SEPARATOR . 'templates';
		return $dir;
	}

	function getSchemaSpecificCoordinates( $schema, $key, $sub_key1 = NULL ) {
		unset($schema['function']);

		if ( $sub_key1 !== NULL ) {
			if ( isset($schema['coordinates'][$key][$sub_key1]) ) {
				return array( 'coordinates' => $schema['coordinates'][$key][$sub_key1] );
			}
		} else {
			if ( isset($schema['coordinates'][$key]) ) {
				/*
				$tmp = $schema['coordinates'][$key];
				unset($schema['coordinates']);
				$schema['coordinates'] = $tmp;

				return $schema;
				*/
				return array( 'coordinates' => $schema['coordinates'][$key] );
			}
		}

		return FALSE;
	}

	//Draw all digits before the decimal in the first location, and after the decimal in the second location.
	function drawSplitDecimalFloat( $value, $schema) {
		if ( $value > 0 ) {
			$this->Draw( $this->getBeforeDecimal( $value ), $this->getSchemaSpecificCoordinates( $schema, 0 ) );
			$this->Draw( $this->getAfterDecimal( $value ), $this->getSchemaSpecificCoordinates( $schema, 1 ) );
		}

		return TRUE;
	}

	//Draw each char/digit one at a time in different locations.
	function drawChars( $value, $schema ) {

		$value = (string)$value; //convert integer to string.

		$max = strlen($value);
		for($i=0; $i < $max; $i++) {
			$this->Draw( $value[$i], $this->getSchemaSpecificCoordinates( $schema, $i ) );
		}

		return TRUE;
	}

	//Draw an X in each of the specified locations
	function drawSplitDecimalFloatGrid( $value, $schema ) {
		//var_dump($value);
		//var_dump($schema);
		if ( !is_array( $value ) ) {
			$value = (array)$value;
		}

		foreach( $value as $key => $tmp_value ) {
			if ( $tmp_value !== FALSE ) {
				//var_dump($tmp_value, $schema['coordinates'][$key] );

				//$this->Draw( $this->getBeforeDecimal( $value ),  array('coordinates' => $schema['coordinates'][$key][0] ) );
				//var_dump( $this->getSchemaSpecificCoordinates( $schema, $key, 0 ) );
				//$this->Draw( $this->getBeforeDecimal( $value ), $this->getSchemaSpecificCoordinates( $schema, $key, 0 ) );

				$this->drawSplitDecimalFloat( $tmp_value, $this->getSchemaSpecificCoordinates( $schema, $key ) );

				//$this->Draw( $tmp_value, $this->getSchemaSpecificCoordinates( $schema, $key ) );
			}
		}

		return TRUE;
	}

	//Draw an X in each of the specified locations
	function drawCheckBox( $value, $schema ) {
		$char = 'x';

		if ( !is_array( $value ) ) {
			$value = (array)$value;
		}

		foreach( $value as $tmp_value ) {
			//Skip any false values.
			if ( $tmp_value === FALSE ) {
				continue;
			}

			if ( is_string( $tmp_value ) ) {
				$tmp_value = strtolower($tmp_value);
			}

			if ( is_bool($tmp_value) AND $tmp_value == TRUE ) {
				$tmp_value = 0;
			}

			$this->Draw( $char, $this->getSchemaSpecificCoordinates( $schema, $tmp_value ) );
		}

		return TRUE;
	}


	//Generic draw function that works strictly off the coordinate map.
	//It checks for a variable specific function before running though, so we can handle more complex
	//drawing functionality.
	function Draw( $value, $schema ) {
		if ( !is_array($schema) ) {
			return FALSE;
		}

		//If its set, use the static value from the schema.
		if ( isset($schema['value'])) {
			$value = $schema['value'];
			unset($schema['value']);
		}

		//on_background flag forces that item to only be shown if the background is as well.
		if ( isset($schema['on_background']) AND $schema['on_background'] == TRUE AND $this->getShowBackground() == FALSE ) {
			return FALSE;
		}

		//If custom function is defined, pass off to that immediate.
		//Else, try the generic drawing method.
		if ( isset($schema['function'])  ) {
			if ( !is_array($schema['function']) ) {
				$schema['function'] = (array)$schema['function'];
			}
			foreach( $schema['function'] as $function ) {
				if ( method_exists( $this, $function) ) {
					$value = $this->$function($value, &$schema);
				}
			}
			unset($function);

			return $value;
		}

		$pdf = $this->getPDFObject();

		//Make sure we don't load the same template more than once.
		if ( isset($schema['template_page']) AND $schema['template_page'] != $this->current_template_index ) {
			$pdf->AddPage();
			if ( $this->getShowBackground() == TRUE AND isset($this->template_index[$schema['template_page']]) ) {
				$pdf->useTemplate( $this->template_index[$schema['template_page']],$this->getTemplateOffsets('x'), $this->getTemplateOffsets('y') );
			}
			$this->current_template_index = $schema['template_page'];
		}

		if ( isset($schema['font']) ) {
			if ( !isset($schema['font']['font']) ) {
				$schema['font']['font'] = $this->default_font;
			}
			if ( !isset($schema['font']['type']) ) {
				$schema['font']['type'] = '';
			}
			if ( !isset($schema['font']['size']) ) {
				$schema['font']['size'] = 8;
			}

			$pdf->SetFont( $schema['font']['font'], $schema['font']['type'], $schema['font']['size']);
		} else {
			$pdf->SetFont( $this->default_font, '', 8 );
		}

		if ( isset($schema['coordinates']) ) {
			$coordinates = $schema['coordinates'];
			//var_dump( Debug::BackTrace() );

			if ( isset($coordinates['text_color']) AND is_array( $coordinates['text_color'] ) ) {
				$pdf->setTextColor( $coordinates['text_color'][0],$coordinates['text_color'][1],$coordinates['text_color'][2] );
			} else {
				$pdf->setTextColor( 0, 0, 0 ); //Black text.
			}

			if ( isset($coordinates['fill_color']) AND is_array( $coordinates['fill_color'] ) ) {
				$pdf->setFillColor( $coordinates['fill_color'][0],$coordinates['fill_color'][1],$coordinates['fill_color'][2] );
				$coordinates['fill'] = 1;
			} else {
				$pdf->setFillColor( 255, 255, 255 ); //White
				$coordinates['fill'] = 0;
			}

			$pdf->setXY( $coordinates['x']+$this->getPageOffsets('x'), $coordinates['y']+$this->getPageOffsets('y') );

			if ( $this->getDebug() == TRUE ) {
				$pdf->setDrawColor( 255, 0 , 0 );
				$coordinates['border'] = 1;
			} else {
				if ( !isset($coordinates['border']) ) {
					$coordinates['border'] = 0;
				}
			}

			$pdf->Cell( $coordinates['w'],$coordinates['h'], $value , $coordinates['border'], 0, strtoupper($coordinates['halign']), $coordinates['fill'] );
			unset($coordinates);
		}

		return TRUE;
	}

	/*
	 *
	 * Magic functions.
	 *
	 */
	function __set( $name, $value ) {
		$filter_function = $this->getFilterFunction( $name );
		if ( $filter_function != '' ) {
			if ( !is_array( $filter_function ) ) {
				$filter_function = (array)$filter_function;
			}

			foreach( $filter_function as $function ) {
				//Call function
				if ( method_exists( $this, $function ) ) {
					$value = $this->$function( $value );

					if ( $value === FALSE ) {
						return FALSE;
					}
				}
			}
			unset($function);
		}

		$this->data[$name] = $value;

		return TRUE;
	}

	function __get( $name ) {
		if ( isset($this->data[$name]) ) {
			return $this->data[$name];
		}

		return FALSE;
	}

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __unset($name) {
        unset($this->data[$name]);
    }
}
?>