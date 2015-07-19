<?php
/**
 * Created by PhpStorm.
 * User: zhuguangwen
 * Date: 15/7/17
 * Time: 下午5:50
 */

include_once("Config.php");
include_once("TypeConst.php");
include_once("StringHelper.php");
include_once("FileHelper.php");
include_once("OCParser.php");
include_once("Param.php");
include_once("Constant.php");

date_default_timezone_set("Asia/Shanghai");

if(!file_exists(Config::output_folder)){
    mkdir(Config::output_folder, 0777, TRUE);
}else{
    exec("rm -rf ".Config::output_folder);
}

$parser = new OCParser();
parse_entity_file(Config::entity_file_name, $parser);
parse_api_file(Config::api_file_name, $parser);
$parser->on_parse_end();

function parse_entity_file($path, OCParser& $parser){
    $content_arr = get_content_arr_filter_line($path, "//");
    $const_int_arr = array();
    $property_arr = array();
    $entity_name = "";

    foreach($content_arr as $index=>$line){
        $line = trim($line);
        if(strlen($line) > 0){
            $vars = explode(" ", $line);
            $var_name = $vars[0];
            $var_type = StringHelper::removeString($vars[1], "(", ")");

            if($var_type == TypeConst::type_const){
                $var_value = $vars[2];
                array_push($const_int_arr, new Constant($var_name, $var_value));

            }else if($var_type == TypeConst::type_entity){
                $entity_name = $var_name;

            }else if($var_type == TypeConst::type_int
                  || $var_type == TypeConst::type_long
                  || $var_type == TypeConst::type_string){
                array_push($property_arr, new Param($var_name, $var_type, FALSE));
            }
            if($index != count($content_arr) - 1){
                continue;
            }
        }

        if($entity_name == ""){
            continue;
        }

        $parser->write_entity_file($entity_name, $property_arr);

        $entity_name = "";
        $property_arr = array();
    }

    $parser->write_const_int_file($const_int_arr);
}

function parse_api_file($path, OCParser& $parser){
    $content_arr = get_content_arr_filter_line($path, "//");
    $api_arr = array();
    $param_arr = array();
    $ret_arr = array();
    $api = "";
    $is_params = TRUE;

    foreach($content_arr as $index=>$line){
        $line = trim($line);
        if(strlen($line) > 0){
            if(strpos($line, "/") > 0){
                $api = $line;

            }else if($line == "params:"){
                $is_params = TRUE;

            }else if($line == "ret:"){
                $is_params = FALSE;

            }else{
                $vars = explode(" ", $line);
                $var_name = $vars[0];
                $var_type = StringHelper::removeString($vars[1], "(", ")");
                $is_list = FALSE;
                if(StringHelper::endsWith($var_type, "List")){
                    $var_type = str_replace("List", "", $var_name);
                    $is_list = TRUE;
                }

                $param = new Param($var_name, $var_type, $is_list);
                if($is_params){
                    array_push($param_arr, $param);
                }else{
                    array_push($ret_arr, $param);
                }
            }
            if($index != count($content_arr) - 1){
                continue;
            }
        }

        if($api == ""){
            continue;
        }

        $parser->write_api_file($api, $param_arr, $ret_arr);

        array_push($api_arr, $api);
        $api = "";
        $param_arr = array();
        $ret_arr = array();
    }

    $parser->write_api_string_file($api_arr);
}

function get_content_arr_filter_line($path, $filter_str){
    $contentArr = array();
    $file = fopen($path, "r") or exit("file not found or can not read : ".$path);
    while(!feof($file)){
        $line = fgets($file);
        if(!StringHelper::startsWith($line, $filter_str)){
            array_push($contentArr, $line);
        }
    }
    return $contentArr;
}