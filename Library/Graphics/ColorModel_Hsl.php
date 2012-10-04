<?php
/**
 * Cross Media Publishing - CMP3
 * www.cross-media.net
 *
 * LICENSE
 *
 * This source file is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * @subpackage Graphics
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Rene Fritz (r.fritz@colorcube.de)
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Graphics;



/**
 * HSL (also called HSI or HLS) color model defines colors in terms of Hue,
 * Saturation, and Lightness (also Luminance, Luminosity or Intensity).
 *
 * Based on code of the pear package Image_Color2.
 * author      andrew morton <drewish@katherinehouse.com>
 * license     http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * The code is heavily changed and there's not very much left except the comments which are used when helpful.
 *
 * @property-read const $Type HSL
 * @property integer $Hue component of the color
 * @property integer $H the same as Hue
 * @property integer $Saturation component of the color
 * @property integer $S the same as Saturation
 * @property integer $Luminosity component of the color
 * @property integer $L the same as Luminosity
 *
 * @author Rene Fritz (r.fritz@colorcube.de)
 * @subpackage Graphics
 * @package    CMP3
 */
class ColorModel_Hsl implements ColorModel_Interface {

	/**
	 * Type of the color model
	 * @see Color
	 * @var string
	 */
	const TYPE = Color::HSL;

	/**
	 * The color object we are the model for
	 * Needed to get the opacity from
	 * @var Color
	 */
	protected $objColor;

	/**
	 * Hue component
	 * @var integer 0-360
	 */
	protected $_h;

	/**
	 * Saturation component
	 * @var float 0.0-1.0
	 */
	protected $_s;

	/**
	 * Lightness component
	 * @var float 0.0-1.0
	 */
	protected $_l;


	/**
	 * Construct the color
	 * This will be instanciated from ty_next_Color
	 *
	 * @param Color
	 */
	public function __construct(Color $objColor)
	{
		$this->objColor = $objColor;
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * Elements which are not yet set will return NULL.
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get($strName)
	{
		switch ($strName) {
			case 'Type':
				return self::TYPE;
				break;
			case 'H':
			case 'Hue':
				return $this->_h;
			break;
			case 'S':
			case 'Saturation':
				return $this->_s;
			break;
			case 'L':
			case 'Lightness':
				return $this->_l;
			break;

			default:
				throw new \Cmp3\UndefinedPropertyException($strName);
			break;
		}
	}


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName to be $mixValue
	 *
	 * @param string $strName Name of the property to set
	 * @param string $mixValue New value of the property
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {
			case 'H':
			case 'Hue':
				$this->_h = Color::CheckInteger360($mixValue);
			break;
			case 'S':
			case 'Saturation':
				$this->_s = Color::CheckFloat1($mixValue);
			break;
			case 'L':
			case 'Lightness':
				$this->_l = Color::CheckFloat1($mixValue);
			break;

			default:
				throw new \Cmp3\UndefinedPropertyException($strName);
			break;
		}
	}


	/**
	 * Sets the color components from a integer for Hue, and two floats for Saturation and
	 * Lightness.
	 * @param   integer $h 0 - 360, or an array with h,s,l(,o)
	 * @param   float   $s 0.0 - 1.0
	 * @param   float   $l 0.0 - 1.0
	 * @param   float   $opacity 0.0 - 1.0
     * @return void
	 */
	public function SetColor($h, $s=null, $l=null, $opacity=null)
	{
		if (is_array($h)) {
			$this->H = $h[0];
			$this->S = $h[1];
			$this->L = $h[2];
			if (!is_null($h[3]))
				$this->objColor->Opacity = $h[3];

		} else {
			$this->H = $h;
			$this->S = $s;
			$this->L = $l;
			if (!is_null($opacity))
				$this->objColor->Opacity = $opacity;
		}
	}


	/**
	 * Initialize the color in HSL with a color in RGB
	 *
     * @param   integer$r 0-255
     * @param   integer $g 0-255
     * @param   integer $b 0-255
	 * @return  void
	 */
	public function SetRgb($r, $g, $b)
	{

		$r = $r / 255;
		$g = $g / 255;
		$b = $b / 255;

		$min = min($r, $g, $b);
		$max = max($r, $g, $b);

		switch ($max) {
			case 0: // it's black like my soul.
				// value = 0, hue and saturation are undefined
				$h = $s = $l = 0;
				break;

			case $min: // grey
				// saturation = 0, hue is undefined
				$h = $s = 0;
				$l = $max;
				break;

			default: // normal color... color
				$delta = $max - $min;

				// hue...
				if( $r == $max ) {
					// between yellow & magenta
					$h = 0 + ( $g - $b ) / $delta;
				} else if( $g == $max ) {
					// between cyan & yellow
					$h = 2 + ( $b - $r ) / $delta;
				} else {
					// between magenta & cyan
					$h = 4 + ( $r - $g ) / $delta;
				}
				// ...convert hue to degrees
				$h *= 60;
				if($h < 0 ) {
					$h += 360;
				}
				// saturation
				$s = $delta;
				// lightness
				$l = ($max + $min) / 2;
		}

		$this->SetColor($h, $s, $l);
	}


	/**
	 * {@inheritdoc}
	 * @return  arrayRGB array containing three integers
	 *          from 0 to 255 for the Red, Green, and Blue color components
	 *
	 */
	public function GetRgb()
	{
		// copy the members to locals for ease of reading
		$h = $this->_h / 360;
		$s = $this->_s;
		$l = $this->_l;

		if ($s == 0.0) {
			// saturation is 0 so it's a grey
			$r = $g = $b = $l;
		} else {
			if ($l < 0.5) {
				$temp2 = $l * (1.0 + $s);
			} else {
				$temp2 = ($l + $s) - ($l * $s);
			}
			$temp1 = (2.0 * $l) - $temp2;
			$r = self::rgbFromHue($temp1, $temp2, $h + (1 / 3));
			$g = self::rgbFromHue($temp1, $temp2, $h);
			$b = self::rgbFromHue($temp1, $temp2, $h - (1 / 3));
		}

		// add .5 and cast to an int to round the values. calling round()
		// returns a float so you'd have to cast it back anyway. this avoids
		// the overhead of a function call.
		$rgbArray = array(
			(integer) ($r * 255 + 0.5),
			(integer) ($g * 255 + 0.5),
			(integer) ($b * 255 + 0.5)
            );
	    if ($this->objColor->hasOpacity()) {
        	$rgbArray[] = $this->objColor->Opacity;
    	}

    	return $rgbArray;
	}


	/**
	 * {@inheritdoc}
	 * @return  array
	 */
	public function GetArray()
	{
		$hslArray = array(
			$this->_h,
			$this->_s,
			$this->_l
		);

	    if ($this->objColor->hasOpacity()) {
        	$hslArray[] = $this->objColor->Opacity;
    	}
    	return $hslArray;
	}

#TODO
	/**
	 * {@inheritdoc}
	 * @return  string In the format 'H, S%, L%'.
	 */
	public function GetString()
	{
		return sprintf(
            '%d, %d%%, %d%%',
			$this->_h,
			round($this->_s * 100),
			round($this->_l * 100)
		);
	}


    /**
     * Inverts the color
     *
     * @return void
     */
    public function Invert()
    {
        $this->_h = ($this->_h + 180 ) % 360;
        $this->_s = 1.0 - $this->_s;
        $this->_l = 1.0 - $this->_l;
    }


    /**
     * Increase or decrease the lightness of the color
     *
     * @param float $value Percent to lighten or darken (minus values) the color
     * @return void
     */
    public function Lightness( $value )
    {
		if (is_float($value)) {
        	$this->Lightness = $this->_l + $value;
		} else {
        	$this->Lightness = $this->_l + (intval($value)/100);

		}
    }


	/**
	 * This is a private function to convert the hue information into an RGB
	 * component.
	 * @param   float
	 * @param   float
	 * @param   float
	 * @return  float
	 */
	private static function rgbFromHue( $v1, $v2, $vH )
	{
		if ( $vH < 0 ) {
			$vH += 1;
		}
		if ( $vH > 1 ) {
			$vH -= 1;
		}

		if ( 6 * $vH < 1 ) {
			return $v1 + ( $v2 - $v1 ) * 6 * $vH;
		} else if ( 2 * $vH < 1 ) {
			return $v2;
		} else if ( 3 * $vH < 2 ) {
			return $v1 + ( $v2 - $v1 ) * ( ( 2 / 3 ) - $vH ) * 6;
		} else {
			return $v1;
		}
	}

}


