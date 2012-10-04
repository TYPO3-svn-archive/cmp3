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


/*
 *
 * Based on code of the pear package Image_Color2.
 * author      andrew morton <drewish@katherinehouse.com>
 * license     http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * The code is heavily changed and there's not very much left except the comments which are used when helpful.
 *
 */


class ColorException extends \Cmp3\Graphics\Exception {}



/**
 * \Cmp3\Graphics\Color is a package for
 *
 * - colors :-)
 * - color manipulation
 * - color conversion between different color models
 * - generation of string representation for css
 *
 *
 * Create an \Cmp3\Graphics\ColorModel from a color model specific string.
 *
 * When a channel's value is followed by a % (with no whitespace
 * separating the two) it should be divided by 100, then treated as a
 * floating point value and forced into the 0.0 to 1.0 range.
 *
 * If applicable, color models should allow the channel
 * components to be separated by spaces and commas (i.e. '50% 23% 0%'
 * should be equivalent to '.5, .23, 0' and '50%, 23%, 0%').}}
 *
 * Color models like CMYK and HSL have some components that are typically
 * expressed as either percentages or floating point values from 0.0 to 1.0.
 * This function tries to be very accomidating, if the value is followed by
 * a percent sign (%) it will be divided by 100 and then treated as a
 * zero to one floating point value.
 *
 * The color's channels can be separated using a comma, a space or both.
 *
 * Examples of string formats used by several implementations of this
 * interface:
 * <pre>
 * Hex: '#abcdef'
 * Named: 'black'
 * CMYK: '100% 0% 75% 20%' or '1, 0, .75, .2'
 * HSL: '135, 100%, 40%' or '135 1.0 0.4'
 * </pre>
 *
 * Here it is in action:
 * <code>
 * <?php
 *
 * \/\/ load a named string and view it as a hex string
 * $red = \Cmp3\Graphics\Color::Create('red');
 * print $red->GetRgbHex() . "\n";     # '#ff0000'
 *
 *  \/\/ load a hex string and view it as an RGB array
 * $blue = \Cmp3\Graphics\Color::Create('#0000ff');
 * var_dump($blue->GetRgb());       # array(0, 0, 255)
 *
 * \/\/ find the average of red and blue (i.e. mix them)...
 * $avg = \Cmp3\Graphics\Color::Blend($red, $blue);
 *
 * \/\/ convert blue from RGB to HSV...
 * $hsv = $blue->convertTo(\Cmp3\Graphics\Color:HSL);
 * \/\/ ...then display it as an HSV string and array
 * print $hsv->GetString() . "\n";  # '240 100% 100%'
 * print_r($hsv->GetArray());       # array(240, 100, 100)
 * ?>
 * </code>
 *
 * When converting from one color model to another, say HSL to CMYK, an
 * intermediate RGB value is used. Each color model has a distinct gamut, or
 * range of expressible color values, and the use of an intermediate value
 * makes the conversions very imprecise. As a result, the output of these
 * conversions should be viewed as an approximation, unfit for any application
 * where color matching is important.
 *
 * @see http://www.faqs.org/faqs/graphics/colorspace-faq/
 *

 * @property integer $Red the red component of the color
 * @property integer $R the same as Red
 * @property integer $Green the green component of the color
 * @property integer $G the same as Green
 * @property integer $Blue the blue component of the color
 * @property integer $B the same as Blue
 * @property float $Opacity 1.0 is opaque, 0.0 is transparent
 * @property integer $Alpha A value between 0 and 255. 0 indicates completely opaque while 255 indicates completely transparent.
 *
 * @author Rene Fritz (r.fritz@colorcube.de)
 * @subpackage Graphics
 * @package    CMP3
 */
class Color {


	/**
	 * Color model. This is assigned by the
	 * constructor. This object implement some methods specific to the color model
	 * @var \Cmp3\Graphics\ColorModel_Interface
	 */
	protected $objModel = null;

	/**
	 * a HTML color name when set before
	 * @var string
	 */
	protected $strName = '';

	/**
	 * a value for opacity (alpha, transparency)
	 * NULL means: not defined so opaque, 1.0, not transparent
	 * 0 = transparent, 1 = opaque
	 * @var float
	 */
	protected $fltOpacity = NULL;

	/**
	 * RGB - red gree blu
	 */
	const RGB = 'rgb';

	/**
	 * HSL - Hue, Saturation, Lightness
	 * This is used in CSS3
	 */
	const HSL = 'hsl';

	/**
	 * CMYK - cyan magenta yellow black
	 */
	const CMYK = 'cmyk';




	protected function __construct()
	{
	}


	/**
	 * Construct a color from a string, array, or an instance of
	 * \Cmp3\Graphics\ColorModel_Interface.
	 *
	 * <code>
	 * \/\/ from a named string
	 * $red = \Cmp3\Graphics\Color::Create('red');
	 * print $red->getHex();    \/\/ '#ff0000'
	 *
	 * \/\/ from a hex string
	 * $blue = \Cmp3\Graphics\Color::Create(('#0000ff');
	 * print $blue->GetCss();   \/\/ '#0000ff'
	 *
	 * \/\/ from an array
	 * $black = \Cmp3\Graphics\Color::Create((array(0,0,0));
	 * print $black->GetCss();  \/\/ '#000000'
	 *
	 * \/\/ HSL color from an array
	 * $hsl = \Cmp3\Graphics\Color::Create((\Cmp3\Graphics\Color::HSL, array(0.0,0.0,0.0));
	 * print $hsl->GetCss();  \/\/ 'hsl(0,0,0)'
	 * </code>
	 *
	 * @todo support for hex rgba: #rrggbbaa
	 *
	 * @param   array|string|\Cmp3\Graphics\ColorModel_Interface $src specifying a color.
	 * @return \Cmp3\Graphics\Color The color object
	 *
	 * Strings will be interpreted as hex if they begin with a #.
	 * CSS3 formats like rgb(...), rgba(), hsl(), hsla() will be detected.
	 * Otherwise they'll be treated as named colors.
	 * @throws  \Cmp3\Graphics\ColorException if the color cannot be loaded.
	 * @uses    _createModelReflectionMethod() If the color is non-RGB the
	 *          function is used to construct an \Cmp3\Graphics\ColorModel_Interface for
	 *          conversion.
	 */
	public static function Create()
	{

		// @see http://www.easyrgb.com/index.php?X=MATH
		// @see http://www.f4.fhtw-berlin.de/~barthel/ImageJ/ColorInspector//HTMLHelp/farbraumJava.htm
		//
		//	lime               /* predefined color name */
		//	rgb(0,255,0)       /* RGB range 0-255   */
		//
		//	#f00               /* #rgb */
		//	#ff0000            /* #rrggbb */
		//	rgb(255,0,0)
		//	rgb(100%, 0%, 0%)
		//
		//	rgb(255,0,0)       /* integer range 0 - 255 */
		//	rgb(300,0,0)       /* clipped to rgb(255,0,0) */
		//	rgb(255,-10,0)     /* clipped to rgb(255,0,0) */
		//	rgb(110%, 0%, 0%)  /* clipped to rgb(100%,0%,0%) */
		//	rgb(255,0,0)       /* integer range 0 - 255 */
		//	rgba(255,0,0,1)    /* the same, with explicit opacity of 1 */
		//	rgb(100%,0%,0%)    /* float range 0.0% - 100.0% */
		//	rgba(100%,0%,0%,1) /* the same, with explicit opacity of 1 */
		//
		//	rgba(0,0,255,0.5)        /* semi-transparent solid blue */
		//	rgba(100%, 50%, 0%, 0.1) /* very transparent solid orange */
		//
		//	hsl(  0, 100%, 50%) /* red */
		//	hsl(120, 100%, 50%) /* green */
		//	hsl(120, 100%, 25%) /* dark green */
		//	hsl(120, 100%, 75%) /* light green */
		//	hsl(120,  75%, 75%) /* pastel green, and so on */
		//
		//	hsl(120, 100%, 50%)     /* green */
		//	hsla(120, 100%, 50%, 1) /* the same, with explicit opacity of 1 */
		//
		//	hsla(240, 100%, 50%, 0.5) /* semi-transparent solid blue */
		//	hsla(30, 100%, 50%, 0.1)  /* very transparent solid orange */
		//
		//
		//  random:rgb(40,10,70|100,60,90) /* random css rgb */
		//  random:rgba(40,10,70,70%|100,60,90,90%) /* random css rgba */
		//  random:hsl(40,10%,70%|100,60%,90%) /* random css hsl */
		//  random:hsla(40,10%,70%,70%|100,60%,90%,90%) /* random css hsla */


		$numArgs = func_num_args();
		$argsArray = func_get_args();



		// the given color is already a color object
		if ($numArgs == 1 AND $argsArray[0] instanceof \Cmp3\Graphics\Color) {
			return $argsArray[0];
		}

		$objColor = new \Cmp3\Graphics\Color;

		if ($numArgs == 0) {
			$objColor->objModel = new \Cmp3\Graphics\ColorModel_Rgb($objColor);
			return $objColor;
		}


		$colorModel = null;

		// the first parameter was a requested color space
		switch (strtolower($argsArray[0])) {
			case self::HSL:
				$colorModel = self::HSL;
				array_shift($argsArray);
				break;

			case self::CMYK:
				$colorModel = self::CMYK;
				array_shift($argsArray);
				break;

			case self::RGB:
				$colorModel = self::RGB;
				array_shift($argsArray);
				break;

			default:
				// maybe we can detect the model from other parameter
				break;
		}

		// we have the color space so we use the other paramter to initialize
		if ($colorModel) {

			$strColorModelClass = '\Cmp3\Graphics\ColorModel_'.ucfirst($colorModel);
			if (!class_exists($strColorModelClass)) {
				throw new \Cmp3\Graphics\ColorException('Non-existing color model requested: ' . $colorModel);
			}

			$objColor->objModel = new $strColorModelClass($objColor);

			if (!$argsArray) {
				return $objColor;
			}

			if (is_array($argsArray[0])) {
				$objColor->objModel->SetColor($argsArray[0]);
				return $objColor;
			}

			if ($argsArray[0] instanceof \Cmp3\Graphics\Color) {
				$objColor->SetRgb($argsArray[0]->GetRgb());
				return $objColor;
			}
		}

		// we have a string, let's detect the color space
		if (is_string($argsArray[0])) {
			$strColor = trim($argsArray[0]);

			// it's hex: #ff00aa
			if ('#' == substr($strColor, 0, 1)) {
				if (!$objColor->objModel) {
					$objColor->objModel = new \Cmp3\Graphics\ColorModel_Rgb($objColor);
				}
				$objColor->SetRgb(self::ParseHex($strColor));

				// it's css rgb: rgb(123, 50%, 34), rgba(...
			} elseif ('rgb' == substr($strColor, 0, 3)) {
				if (!$objColor->objModel) {
					$objColor->objModel = new \Cmp3\Graphics\ColorModel_Rgb($objColor);
				}
				$objColor->SetRgb(self::ParseCss($strColor));

				// it's css hsl, hsla
			} elseif ('hsl' == substr($strColor, 0, 3)) {
				if (!$objColor->objModel) {
					$objColor->objModel = new \Cmp3\Graphics\ColorModel_Hsl($objColor);
				}
				$objColor->objModel->SetColor(self::ParseCss($strColor));

				// it's random css rgb, rgba
				// random:rgb(40,10,70|100,60,90)
			} elseif ('random:rgb' == substr($strColor, 0, 10)) {

				if (!$objColor->objModel) {
					$objColor->objModel = new \Cmp3\Graphics\ColorModel_Rgb($objColor);
				}

				$strColors = substr($strColor, 7);
				$strColors = trim_explode('|', $strColors);
				$strColorFrom = $strColors[0] . ')';
				$strColorTo = 'rgb(' . $strColors[1];
				$strColorFrom = self::ParseCss($strColorFrom);
				$strColorTo = self::ParseCss($strColorTo);

				$objColor->objModel->SetColor(array(
					rand($strColorFrom[0], $strColorTo[0]),
					rand($strColorFrom[1], $strColorTo[1]),
					rand($strColorFrom[2], $strColorTo[2])
					));

				// it's random css hsl, hsla
				// random:hsl(40,10%,70%|100,60%,90%)
			} elseif ('random:hsl' == substr($strColor, 0, 10)) {

				if (!$objColor->objModel) {
					$objColor->objModel = new \Cmp3\Graphics\ColorModel_Hsl($objColor);
				}

				$strColors = substr($strColor, 7);
				$strColors = trim_explode('|', $strColors);
				$strColorFrom = $strColors[0] . ')';
				$strColorTo = 'hsl(' . $strColors[1];
				$strColorFrom = self::ParseCss($strColorFrom);
				$strColorTo = self::ParseCss($strColorTo);

				$objColor->objModel->SetColor(array(
					rand($strColorFrom[0], $strColorTo[0]),
					rand($strColorFrom[1]*100, $strColorTo[1]*100)/100,
					rand($strColorFrom[2]*100, $strColorTo[2]*100)/100
					));

				// we try a name
			} else {
				if ($rgb = \Cmp3\Graphics\ColorNames_rgb::GetFromNameRgb($strColor)) {
					$objColor->objModel = new \Cmp3\Graphics\ColorModel_Rgb($objColor);
					$objColor->SetRgb($rgb);
					$objColor->strName = $strColor;
				}
			}

		// we'll that's not typical but a color model object is here ready to use, so we use it
		} else if ($argsArray[0] instanceof \Cmp3\Graphics\ColorModel_Interface) {
			$objColor->objModel = $argsArray[0];

		} else if (count($argsArray) >= 3) {

			if (!$objColor->objModel) {
				$objColor->objModel = new \Cmp3\Graphics\ColorModel_Rgb($objColor);
			}
			$objColor->objModel->SetColor($argsArray);

		} else {
			throw new \Cmp3\Graphics\ColorException('Invalid color definition.');
		}

		return $objColor;
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
				return $this->objModel->Type;
				break;
			case 'Name':
				return $this->strName;
				break;
			case 'R':
			case 'Red':
				$intColorArray = $this->objModel->GetRgb();
				return $intColorArray[0];
				break;
			case 'G':
			case 'Green':
				$intColorArray = $this->objModel->GetRgb();
				return $intColorArray[1];
				break;
			case 'B':
			case 'Blue':
				$intColorArray = $this->objModel->GetRgb();
				return $intColorArray[2];
				break;
			case 'Opacity':
				if (is_null($this->fltOpacity)) return 1.0;
				return $this->fltOpacity;
				break;
			case 'Alpha':
				return intval(255 - (255.0 * $this->Opacity));
				break;

			default:
				return $this->objModel->__get($strName);
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
			case 'Opacity':
				$this->fltOpacity = self::CheckFloat1($mixValue);
				break;

			default:
				$this->objModel->__set($strName, $mixValue);
				break;
		}
	}


	/**
	 * Check for current color type
	 *
	 * @param    integer   $colorModel  color type
	 * @return   boolean
	 */
	public function isType($colorModel)
	{
		return ($colorModel === $this->objModel->Type);
	}


	/**
	 * Checks if the color has a HTML color name set
	 * @return boolean
	 */
	public function hasName()
	{
		return (boolean)$this->strName;
	}


	/**
	 * Checks if the color is set as 'none'
	 * @return boolean
	 */
	public function isNone()
	{
		return ($this->strName == 'none');
	}


	/**
	 * Checks if color has alpha/opacity
	 *
	 * Alias of hasOpacity()
	 *
	 * @return boolean
	 */
	public function hasAlpha()
	{
		return !is_null($this->fltOpacity);
	}


	/**
	 * Checks if color has alpha/opacity
	 *
	 * Alias of hasAlpha()
	 *
	 * @return boolean
	 */
	public function hasOpacity()
	{
		return !is_null($this->fltOpacity);
	}


	/**
	 * Set the color opaque
	 * When the color is requested in some format the opacity is ommited when setting this.
	 *
	 * @return this
	 */
	public function SetOpaque()
	{
		$this->fltOpacity = NULL;

		return $this;
	}


	/**
	 * Return the color as a RGB array.
	 *
	 * <code>
	 * $color = \Cmp3\Graphics\Color::Create(array(0,128,255));
	 * print_r($color->getRgb()); \/\/ array(0, 128, 255)
	 * </code>
	 *
	 * @return  array
	 */
	public function GetRgb()
	{
		return $this->objModel->GetRgb();
	}


	/**
	 * Return the color as a 32bit integer
	 *
	 * @see sefl::rgba2int()
	 * @return  integer
	 */
	public function GetRgbInt()
	{
		$rgba =  $this->objModel->GetRgb();
		#TODO opacity = a
		return self::rgba2int($rgba[0], $rgba[1], $rgba[2], $a=1);
	}


	/**
	 * Sets the rgb color from RGB components.
	 *
	 * @param   integer|array $r 0-255, 32bit rgba integer, or array with r,g,b(,o)
	 * @param   integer $g 0-255
	 * @param   integer $b 0-255
	 * @param   float   $opacity 0.0 - 1.0
	 * @return void
	 */
	public function SetRgb($r, $g=null, $b=null, $opacity=null)
	{
		$this->strName = null;

		if (is_array($r)) {
			$this->objModel->SetRgb($r[0], $r[1], $r[2]);
			if (!is_null($r[3]))
			$this->Opacity = $r[3];

		} elseif ($g===NULL) {
			$r = self::int2rgba($r);
			$this->objModel->SetRgb($r[0], $r[1], $r[2]);
			if (!is_null($r[3]))
			$this->Opacity = $r[3];

		} else {
			$this->objModel->SetRgb($r, $g, $b);
			if (!is_null($opacity))
			$this->Opacity = $opacity;
		}
	}


	/**
	 * Return the color in a color model dependant, array format. If the color
	 * was specified as an RGB array this will return the same results as
	 * getRgb(). Otherwise, the results depend on the underlying color model.
	 *
	 * <code>
	 * $color = \Cmp3\Graphics\Color::Create(array(0,128,255));
	 * print_r($color->getArray());     \/\/ array(0, 128, 255)
	 * </code>
	 *
	 * @return  array.
	 * @uses    \Cmp3\Graphics\ColorModel_Interface::getArray()
	 */
	public function GetArray()
	{
		return $this->objModel->GetArray();
	}


	/**
	 * {@inheritdoc}
	 *
	 * hsl is in css3 so we still use RGB here (and convert to rgb when needed)
	 *
	 * @return  string A string in the format '#RRGGBB' where each channel is a hex byte.
	 * When the color has opacity following format will be returned: rgba(255,0,0,0.8)
	 */
	public function GetCss()
	{
		if ($this->strName == 'none') {
			return $this->strName;
		}
		#TODO hsl is in css3 so we still use RGB here or what?
		list ($red, $green, $blue) = $this->objModel->GetRgb();
		if ($this->hasOpacity()) {
			return sprintf('rgba(%u, %u, %u, %s)', $red, $green, $blue, number_format ( $this->Opacity , 2 , '.' , '' ));
		}
		return sprintf('#%02x%02x%02x', $red, $green, $blue);
	}


	/**
	 * {@inheritdoc}
	 * @return  string A string in the format '#RRGGBB' where each channel is a hex byte. Opacity will be ignored
	 */
	public function GetRgbHex()
	{
		list ($red, $green, $blue) = $this->objModel->GetRgb();
		return sprintf('#%02x%02x%02x', $red, $green, $blue);
	}


#TODO useless?
#TODO maybe use css format for all color models here?
	/**
	* Return the color as a string. If the color was specified as an RGB array
	* this is exactly the same as calling getHex(). Otherwise, the results
	* depend on the underlying color model.
	*
	* <code>
	* $red = \Cmp3\Graphics\Color::Create(array(255,0,0));
	* print $red->getString();     \/\/ '#ff0000'
	*
	* $hsl = $orange->convertTo('hsl');
	* print $hsl->getString();     \/\/ '38 100% 50%'
	* </code>
	*
	* @return  string
	* @uses    \Cmp3\Graphics\ColorModel_Interface::getString() If the color wasn't originally
	*          RGB.
	* @uses    getHex() If the color was originally specified as RGB.
	*/
	public function GetString()
	{
		return $this->objModel->GetString();
	}


	/**
	 * Alias to GetCss()
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->GetCss();
	}



	/************************************
	 *
	 * Calculations on color objects
	 *
	 ************************************/



	/**
	 * Inverts the color
	 *
	 * @return this
	 */
	public function Invert()
	{
		$this->strName = NULL;

		$this->objModel->Invert();

		return $this;
	}


	/**
	 * Increase or decrease the lightness of the color
	 *
	 * @param float $value Percent to lighten or darken (minus values) the color
	 * @return this
	 */
	public function Lightness( $value )
	{
		$this->strName = NULL;

		$this->objModel->Lightness( $value );

		return $this;
	}


	/**
	 * Increase or decrease the lightness of the color
	 * according to a given gamma value
	 *
	 * A gamma of 1.0 do nothing
	 * A greater gamma lighten the color
	 * A smaller gamma darken the color
	 *
	 * @param float $value
	 * @return this
	 */
	public function Gamma ( $value )
	{
		$this->strName = NULL;
		#TODO move to model
		# in hsl apply gamma to L?
		list ($red, $green, $blue) = $this->objModel->GetRgb();

		(float)$value;
		$red   = self::CheckInteger255(255 * pow(($red   / 255), 1/$value));
		$green = self::CheckInteger255(255 * pow(($green / 255), 1/$value));
		$blue  = self::CheckInteger255(255 * pow(($blue  / 255), 1/$value));

		$this->objModel->SetRgb($red, $green, $blue);

		return $this;
	}


	/**
	 * Increase or decrease the contrast of the color
	 * This means light tones become lighter and dark tones become darker
	 *
	 * @param float $value 1.0 will not change anything. 2.0 will increase the contrast very much for example. Values below 1 decrease the contrast.
	 * @return this
	 */
	public function Contrast ( $value )
	{
		$this->strName = NULL;
		#TODO move to model
		# in hsl applay gamma to L
		list ($red, $green, $blue) = $this->objModel->GetRgb();

		(float)$value;
		$red   = self::CheckInteger255(($red  - 128) * $value + 128);
		$green = self::CheckInteger255(($green- 128) * $value + 128);
		$blue  = self::CheckInteger255(($blue - 128) * $value + 128);

		$this->objModel->SetRgb($red, $green, $blue);

		/*
		 For example, with
		 (p-128)*c + 128
		 c = 1 would leave pixel values unchanged,
		 c > 1 would increase the value of pixels > 128 and decrease pixel values < 128
		 c < 1 would decrease pixel values > 128 and increase pixel values < 128, bringing them all closer to 128
		 c < 0 would give negative contrast

		 If you want a slider value of -255 to correspond to c=0, a slider value of 0 to correspond to c=1, and a slider value of +255 to correspond to c=128, then you might use somehing like
		 c = (slider+255)*259/(255*(259-slider))
		 */

		return $this;
	}


	/**
	 * Increase or decrease the lightness of the color
	 * Change the color to a grey representation
	 *
	 * @return this
	 */
	public function Desaturate ()
	{
		$this->strName = NULL;
		#TODO move to model
		if ($this->Type == self::HSL) {
			$this->objModel->S = 0;
			return $this;
		}


		list ($red, $green, $blue) = $this->objModel->GetRgb();

		$grey = intval(($red+$red+$blue+$green+$green+$green)/6);

		$this->objModel->SetRgb($grey, $grey, $grey);

		/*
		 Do you mean brightness? Perceived brightness? Luminance?

		 * Luminance (standard, objective): (0.2126*R) + (0.7152*G) + (0.0722*B)
		 * Luminance (perceived option 1): (0.299*R + 0.587*G + 0.114*blue)
		 * Luminance (perceived option 2, slower to calculate): sqrt( 0.241*R^2 + 0.691*G^2 + 0.068*B^2 )



		 I think what you are looking for is the RGB -> Luma conversion formula.

		 Photometric/digital ITU-R:

		 Y = 0.2126 R + 0.7152 G + 0.0722 B

		 Digital CCIR601 (gives more weight to the R and B components):

		 Y = 0.299 R + 0.587 G + 0.114 B

		 If you are willing to trade accuracy for perfomance, there are two approximation formulas for this one:

		 Y = 0.33 R + 0.5 G + 0.16 B

		 Y = 0.375 R + 0.5 G + 0.125 B

		 These can be calculated quickly as

		 Y = (R+R+B+G+G+G)/6

		 Y = (R+R+R+B+G+G+G+G)>>3



		 All these equations work kinda well in practice, but if you need to be very precise you have to first convert the color to linear color space (apply inverse image-gamma), do the weight average of the primary colors and - if you want to display the color - take the luminance back into the monitor gamma.

		 The luminance difference between ingnoring gamma and doing proper gamma is up to 20% in the dark grays.

		 */


		return $this;
	}


	/**
	 * TO create a visible font color for a given color
	 *
	 * The predefined colors are white and black and will be returned as CSS hex strings.
	 *
	 * @see http://particletree.com/notebook/calculating-color-contrast-for-legible-text/
	 *
	 * @param string|\Cmp3\Graphics\Color $dark
	 * @param string|\Cmp3\Graphics\Color $light
	 * @return string|\Cmp3\Graphics\Color
	 */
	public function GetContrastColor($dark = '#000000', $light = '#FFFFFF')
	{
		if ($this->Type == self::HSL) {
			// seems to work better than $this->objModel->L > 0.5
			$SL = $this->objModel->S + $this->objModel->L + $this->objModel->L + $this->objModel->L;
			return ($SL > 2) ? $dark : $light;
		}

		list ($red, $green, $blue) = $this->objModel->GetRgb();

		// we calculate a simple luminance value here which is good enough
		$luminance = intval(($red+$red+$blue+$green+$green+$green)/6);

		return ($luminance > 127) ? $dark : $light;
	}



	/*******************************
	 *
	 * Public Static Functions
	 *
	 *******************************/


	/**
	 * Return a copy of this color converted to another color model.
	 * <code>
	 * $blue = \Cmp3\Graphics\Color::Create('#0000ff');
	 * $hsv = $blue->convertTo('hsv');
	 * print $hsv->getString(); \/\/ '240 100% 100%'
	 * </code>

	 * @param   string  Name of a color model. If this variable is foo then a
	 *          class named \Cmp3\Graphics\ColorModel_Foo is required.
	 * @return  \Cmp3\Graphics\Color
	 * @throws  \Cmp3\Graphics\ColorException if the desired color model cannot be found or it
	 *          cannot convert the color.
	 * @uses    _createModelReflectionMethod() The function is used to
	 *          construct an \Cmp3\Graphics\ColorModel_Interface that is passed back to the
	 *          constructor.
	 */
	public function ConvertTo($colorModel)
	{
		if ($colorModel == $this->Type) {
			return $this;
		}

		return \Cmp3\Graphics\Color::Create($colorModel, $this);
	}


	/**
	 * Return the average of the RGB value of two \Cmp3\Graphics\Color objects. If
	 * both objects have an alpha channel it will be averaged too.
	 *
	 * <code>
	 * $red = \Cmp3\Graphics\Color::Create('red');
	 * $blue = \Cmp3\Graphics\Color::Create('blue');
	 * $color = \Cmp3\Graphics\Color::Blend($red, $blue);
	 * </code>
	 *
	 * The $fltBlendFraction parameter should be a floating point number in the range of 0 to 1,
	 * where a value of 0 will result in \Cmp3\Graphics\Color being set to the color represented
	 * by the $left parameter, the value 1 will result in \Cmp3\Graphics\Color being set to
	 * the color represented by the $right parameter, and the value .5 will result in
	 * \Cmp3\Graphics\Color being set to a color that is an equal blend of the colors represented by
	 * the $left and $right parameters.
	 *
	 * @param   \Cmp3\Graphics\Color|string $left
	 * @param   \Cmp3\Graphics\Color|string $right
	 * @param	float $leftBlendFraction
	 * @return  \Cmp3\Graphics\Color
	 */
	public static function Blend(\Cmp3\Graphics\Color $left, \Cmp3\Graphics\Color $right, $rightBlendFraction=0.5)
	{
		if (!$left instanceof \Cmp3\Graphics\Color) {
			$left = \Cmp3\Graphics\Color::Create($left);
		}
		if (!$right instanceof \Cmp3\Graphics\Color) {
			$right = \Cmp3\Graphics\Color::Create($right);
		}
		$lrgb = $left->getRgb();
		$rrgb = $right->getRgb();

		// the color may be RGB or RGBA, either way, they need to be the same
		// length.
		$size = min(count($lrgb), count($rrgb));
		#FIXME implement $fltBlendFraction
		// find the average of each pair of elements

		$rightBlendFraction = max(0, min(1.0, $rightBlendFraction))*100;
		$leftBlendFraction = 100 - $rightBlendFraction;

		$avg = array();
		for ($i = 0; $i < $size; $i++) {
			$avg[] = (integer) (($lrgb[$i]*$leftBlendFraction + $rrgb[$i]*$rightBlendFraction ) / 100);
		}
		return \Cmp3\Graphics\Color::Create(self::RGB, $avg);
	}





	/*******************************
	 *
	 * Tools
	 *
	 *******************************/


	public static function CheckInteger360($mixValue)
	{
		if (is_numeric($mixValue)) {
			return (integer)(max(0, $mixValue) % 360);
		} else {
			// we expect here a percent value: 15%
			return (integer)min(360, max(0, (intval($mixValue)*3.60)));
		}
	}


	public static function CheckInteger255($mixValue)
	{
		if (is_numeric($mixValue)) {
			return (integer)min(255, max(0, $mixValue));
		} else {
			// we expect here a percent value: 15%
			return (integer)min(255, max(0, (intval($mixValue)*2.55)));
		}
	}


	public static function CheckInteger100($mixValue)
	{
		if (is_numeric($mixValue)) {
			return (integer)min(100, max(0, $mixValue));
		} else {
			// we expect here a percent value: 15%
			return (integer)min(100, max(0, (intval($mixValue))));
		}
	}


	public static function CheckFloat1($mixValue)
	{
		if (is_numeric($mixValue)) {
			return (float)min(1.0, max(0.0, $mixValue));
		} else {
			// we expect here a percent value: 15%
			return (float)min(1.0, max(0.0, (intval($mixValue)/100)));
		}
	}





	#TODO
	/**
	 * Convert the hex string into RGB components and return a new instance of
	 * \Cmp3\Graphics\ColorModel_Interface. Allow both #abc and #aabbcc forms.
	 * @param   string $str in the format 'AABBCC', 'ABC', '#ABCDEF', or '#ABC'
	 * @return  array
	 */
	public static function ParseHex($str)
	{
		$color = str_replace('#', '', $str);
		if (strlen($color) == 3) {
			// short #abc form
			return array (
			hexdec($color{0} . $color{0}),
			hexdec($color{1} . $color{1}),
			hexdec($color{2} . $color{2})
			);
		} else {
			// long #aabbcc form
			return array (
			hexdec(substr($color, 0, 2)),
			hexdec(substr($color, 2, 2)),
			hexdec(substr($color, 4, 2))
			);
		}
	}


	/**
	 * parses a given string in CSS3 format: rgb(255,123,66), hsla(123,10%,20%,0.5)
	 *
	 * @param string $str
	 * @return array|false
	 */
	public static function ParseCss($str)
	{
		if (preg_match('#(rgba|rgb|hsla|hsl) *\(([^)]+)\)#', $str, $matches)) {
			switch ($matches[1]) {
				case 'rgb':
				case 'rgba':
					return self::FromStringRGB($matches[2]);
					break;
				case 'hsl':
				case 'hsla':
					return self::FromStringHSL($matches[2]);
					break;
			}
		}
		return false;
	}

	#TODO rename following functions
	/**
	 * Pull the HSL components out of the string and return a color model
	 * object. Hue is a integer degree between 0 and 360 while the Saturation
	 * and Lightness are either percentages or values between 0 and 1.
	 *
	 * @param   string $str In the form '120, 25%, 50%' or '120, .25, .50'
	 * @return  array
	 */
	public static function FromStringHSL($str)
	{
		// split it by commas or spaces
		$a = preg_split('/[, ]/', $str, -1, PREG_SPLIT_NO_EMPTY);

		$h = $a[0];
		$s = substr($a[1], -1) == '%' ? ((float) $a[1]) / 100 : (float) $a[1];
		$l = substr($a[2], -1) == '%' ? ((float) $a[2]) / 100 : (float) $a[2];
		if ($a[3]) {
			$a = substr($a[3], -1) == '%' ? ((float) $a[3]) / 100 : (float) $a[3];
		} else {
			$a = null;
		}

		return array($h, $s, $l, $a);
	}


	/**
	 * This function builds a 32 bit integer from 4 values which must be 0-255 (8 bits)
	 * Example 32 bit integer: 00100000010001000000100000010000
	 *  The first 8 bits define the alpha
	 *  The next 8 bits define the green
	 *  The next 8 bits define the red
	 *
	 * @param integer $r
	 * @param integer $g
	 * @param integer $b
	 * @param integer $a
	 * @return integer
	 */
	public static function rgba2int($r, $g, $b, $a=1)
	{
		#TODO check value of $a - why is 1 default?
		return ($a << 24) + ($b << 16) + ($g << 8) + $r;
	}


	/**
	 * Converts an 32bit integer to a rgba array
	 *
	 * @param integer $int
	 * @return array
	 */
	public static function int2rgba($int)
	{
		$a = ($int >> 24) & 0xFF;
		$b = ($int >> 16) & 0xFF;
		$g = ($int >> 8) & 0xFF;
		$r = $int & 0xFF;
		return array($r, $g, $b, $a);
	}

}



