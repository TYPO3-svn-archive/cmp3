<?php




class helper {


	public static function xmlstring2array($string) // from php.net
	{
		$object=simplexml_load_string($string);

		$return = NULL;

		if(is_array($object))
		{
			foreach($object as $key => $value)
				$return[$key] = object2array($value);
		}
		else
		{
			$var = get_object_vars($object);

			if($var)
			{
				foreach($var as $key => $value) {
					#$return[$key] = ($key && !$value) ? NULL : self::xmlstring2array($value);
					$return[$key] = (string)$value;
				}
			}
			else return $object;
		}

		return $return;
	}


	public static function cleanFilename($str)
	{
		return str_replace(array('\\', '::'), '_', $str);
	}
}