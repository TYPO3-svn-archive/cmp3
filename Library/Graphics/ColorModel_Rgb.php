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
 * color model defines RGB colors
 *
 * Based on code of the pear package Image_Color2.
 * author      andrew morton <drewish@katherinehouse.com>
 * license     http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * The code is heavily changed and there's not very much left except the comments which are used when helpful.
 *
 * @property-read const $Type RGB
 * @property integer $Red the red component of the color
 * @property integer $R the same as Red
 * @property integer $Green the green component of the color
 * @property integer $G the same as Green
 * @property integer $Blue the blue component of the color
 * @property integer $B the same as Blue
 *
 * @author Rene Fritz (r.fritz@colorcube.de)
 * @subpackage Graphics
 * @package    CMP3
 */
class ColorModel_rgb implements ColorModel_Interface {

	/**
	 * Type of the color model
	 * @see Color
	 * @var string
	 */
	const TYPE = Color::RGB;

	/**
	 * The color object we are the model for
	 * Needed to get the opacity from
	 * @var Color
	 */
	protected $objColor;

    /**
     * Red component
     * @var integer 0-255
     */
    protected $intRed = 0;

    /**
     * Green component
     * @var integer 0-255
     */
    protected $intGreen = 0;

    /**
     * Blue component
     * @var integer 0-255
     */
    protected $intBlue = 0;




    /**
     * Construct the rgb color
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
			case 'R':
			case 'Red':
				return $this->intRed;
			break;
			case 'G':
			case 'Green':
				return $this->intGreen;
			break;
			case 'B':
			case 'Blue':
				return $this->intBlue;
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
			case 'R':
			case 'Red':
				$this->intRed =   Color::CheckInteger255($mixValue);
			break;
			case 'G':
			case 'Green':
				$this->intGreen = Color::CheckInteger255($mixValue);
			break;
			case 'B':
			case 'Blue':
				$this->intBlue =  Color::CheckInteger255($mixValue);
			break;

			default:
				throw new \Cmp3\UndefinedPropertyException($strName);
			break;
		}
	}


    /**
     * Sets the rgb color from RGB components.
     *
     * @param   integer|array $r 0-255, or array with r,g,b(,o)
     * @param   integer $g 0-255
     * @param   integer $b 0-255
	 * @param   float   $opacity 0.0 - 1.0
     * @return void
     */
    public function SetColor($r, $g=null, $b=null, $opacity=null)
    {
    	if (is_array($r)) {
			$this->Red =   $r[0];
			$this->Green = $r[1];
			$this->Blue =  $r[2];
			if (!is_null($r[3]))
				$this->objColor->Opacity = $r[3];
		} else {
	        $this->Red =   $r;
	        $this->Green = $g;
	        $this->Blue =  $b;
			if (!is_null($opacity))
				$this->objColor->Opacity = $opacity;
		}
    }


    /**
     * Sets the color from RGB components.
     *
     * @param   integer $r 0-255
     * @param   integer $g 0-255
     * @param   integer $b 0-255
     * @return void
     */
    public function SetRgb($r, $g, $b)
    {
    	$args = func_get_args();
    	call_user_func_array(array($this, 'SetColor'), $args);
    }


    /**
     * {@inheritdoc}
     * @return  array RGB array containing three integers
     *          from 0 to 255 for the Red, Green, and Blue color components
     */
    public function GetRgb()
    {
    	if ($this->objColor->hasOpacity()) {
        	return array($this->intRed, $this->intGreen, $this->intBlue, $this->objColor->Opacity);
    	}
        return array($this->intRed, $this->intGreen, $this->intBlue);
    }


    /**
     * {@inheritdoc}
     * @return  array  RGB array containing three integers
     *          from 0 to 255 for the Red, Green, and Blue color components
     * @uses    getRgb() because both functions share the same return format.
     */
    public function GetArray()
    {
        return $this->GetRgb();
    }

    #TODO
	/**
	 * {@inheritdoc}
	 * @return  string In the format 'R, G, B'.
	 */
	public function GetString()
	{
		return sprintf(
            '%d, %d%, %d%',
			$this->intRed,
			$this->intGreen,
			$this->intBlue
		);
	}


    /**
     * Inverts the color
     *
     * @return void
     */
    public function Invert()
    {
        $this->intRed   = 255 - $this->intRed;
        $this->intGreen = 255 - $this->intGreen;
        $this->intBlue  = 255 - $this->intBlue;
    }


    /**
     * Increase or decrease the lightness of the color
     *
     * @param float $value Percent to lighten or darken (minus values) the color
     * @return void
     */
    public function Lightness( $value )
    {
		$value = (integer)(255 * $value / 100);
        $this->intRed   = Color::CheckInteger255( $this->intRed   + $value );
        $this->intGreen = Color::CheckInteger255( $this->intGreen + $value );
        $this->intBlue  = Color::CheckInteger255( $this->intBlue  + $value );
    }


}


