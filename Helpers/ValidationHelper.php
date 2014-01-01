<?php

class ValidationHelper {

  // General methods
  
	public static function isLooseBoolean($value){
		return ($value === 1 OR $value === 0 OR is_null($value));
	}
    public static function isNumeric($value) {
        return is_numeric($value);
    }

    public static function isEscaped($value){
		return $value == htmlspecialchars($value);
    }
    
    public static function isAlphaNumeric($value) {
        return ctype_alnum($value);
    }

    public static function isNotEmpty($value) {
        if (trim($value) == '') {
            return false;
        }
        return true;
    }

    public static function isEmpty($value) {
        if (trim($value) == '') {
            return true;
        }
        return false;
    }

    public static function isDatetime($value) {
        $time = strtotime($value);
        $res = false;
        if (date("Y-m-d H:i:s", $time) == $value) {
            $res = true;
        }
        return $res;
    }

    public static function notNull($value) {
        return !is_null($value);
    }

    public static function isArray($value) {
        return is_array($value);
    }

    public static function isObject($value){
        return is_object($value);
    }

    public static function isValidModel($value){
        return $value->isValid();
    }

}

?>