<?php
/**
 * Created by PhpStorm.
 * User: zhuguangwen
 * Date: 15/7/17
 * Time: 下午6:41
 */

class StringHelper {

    static function startsWith($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    static function endsWith($haystack, $needle) {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    static function convertStringToCamelName($name, $firstUpper){
        $res = "";
        for($i = 0;$i < strlen($name);$i ++){
            if($i == 0){
                $res .= $firstUpper ? strtoupper($name[0]) : strtolower($name[0]);
            }else if($name[$i] == "_"){
                continue;
            }else if($name[$i - 1] == "_"){
                $res .= strtoupper($name[$i]);
            }else{
                $res .= $name[$i];
            }
        }
        return $res;
    }

    static function removeString($str, $subStr, $subStr1){
        return str_replace($subStr1, "", str_replace($subStr, "", $str));
    }

} 