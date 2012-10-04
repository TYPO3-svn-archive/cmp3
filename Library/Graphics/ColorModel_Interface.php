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
 * An interface for color model classes
 *
 * Based on code of the pear package Image_Color2.
 * author      andrew morton <drewish@katherinehouse.com>
 * license     http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * The code is heavily changed and there's not very much left except the comments which are used when helpful.
 *
 *
 * @author Rene Fritz (r.fritz@colorcube.de)
 * @subpackage Graphics
 * @package    CMP3
 */
interface ColorModel_Interface {

	/**
	 * Construct the color
	 * This will be instanciated from ty_next_Color
	 *
	 * @param Color
	 */
	public function __construct(Color $objColor);


	/**
	 * Initialize the color in HSL with a color in RGB
	 *
     * @param   integer$r 0-255
     * @param   integer $g 0-255
     * @param   integer $b 0-255
	 * @return  void
	 */
	public function SetRgb($r, $g, $b);

    /**
     * Get an RGB array of the color.
     *
     * The 'type' => 'rgb' element must be included as the last element.
     *
     * @return  array A PEAR style RGB array containing three integers
     *          from 0 to 255 for the Red, Green, and Blue color components
     *
     * @see     fromRgb()
     */
    public function GetRgb();


    /**
     * Get an array of values to represent a color in this color model. This
     * should be parseable by fromArray() and should include a type element.
     *
     * {@internal
     * The 'type' => 'foo' where foo is the color model, element must be included
     * as the last element.}}
     *
     * @return  array An array in a color model dependant format with a
     *          type element.
     * @see     fromArray()
     */
    public function GetArray();


    /**
     * Get a string to represent a color in this color model. The string should
     * be parsable by fromString().
     *
     * {@internal
     * Color channels should be separated with a comma and then a space.
     *
     * Integer values (like the Hue in HSL) should be formated as integers.
     * Floating point values that have a range of 0.0 - 1.0 should be converted
     * to a percentage for output.}}
     *
     * <pre>
     * HSL: '135, 100%, 40%'
     * CMYK: '100%, 0%, 75%, 20%'
     * Hex: '#00cc33'
     * </pre>
     *
     * @return  string A string in a color model dependant format.
     * @see     fromString()
     */
    public function GetString();


    /**
     * Increase or decrease the lightness of the color
     *
     * @param float $value Percent to lighten or darken (minus values) the color
     * @return void
     */
    public function Lightness( $value );


    /**
     * Inverts the color
     *
     * @return void
     */
    public function Invert();
}

