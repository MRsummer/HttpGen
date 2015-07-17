<?php
/**
 * Created by PhpStorm.
 * User: zhuguangwen
 * Date: 15/7/17
 * Time: 下午8:15
 */

include_once("Param.php");
include_once("Constant.php");

class OCParser {

    /**
     * 服务器定义的int类型变量写入
     * @param array& $const_arr int constant 数组
     */
    public function write_const_int_file(array& $const_arr){
        $path = Config::output_folder.Config::output_const_int_file_name;
        $this->write_common_file_header($path);
        FileHelper::appendLine($path, "//const int");
        foreach($const_arr as $constant){
            $code = "static const NSInteger ".$constant->name." = ".$constant->value.";";
            FileHelper::appendLine($path, $code);
        }
    }

    /**
     * api地址定义写入
     * @param array& $api_arr string constant 数组
     */
    public function write_api_string_file(array& $api_arr){
        $path = Config::output_folder.Config::output_const_api_string_file_name;
        $this->write_common_file_header($path);
        FileHelper::appendLine($path, "//api");
        foreach($api_arr as $index=>$api_str){
            $api_name = $this->get_api_camel_name($api_str);
            $code = "static NSString const *".$api_name." = @\"".$api_str."\";";
            FileHelper::appendLine($path, $code);

            if($index != count($api_arr) - 1){
                $this_base_name = $this->get_api_base_name($api_str);
                $next_base_name = $this->get_api_base_name($api_arr[$index+1]);
                if($this_base_name != $next_base_name){
                    FileHelper::appendLine($path, "");
                }
            }
        }

        //各个api.h文件中的 @end
        $this->write_header_end($api_arr);
    }

    /**
     * 实体类写入
     * @param $entity_name 实体名称
     * @param $property_code_arr 实体成员的数组
     */
    public function write_entity_file($entity_name, $property_code_arr){
        $this->write_entity_header_file(Config::output_folder.$entity_name.".h", $entity_name, $property_code_arr);
        $this->write_entity_implementation_file(Config::output_folder.$entity_name.".m", $entity_name);
    }

    /**
     * api实现文件的写入
     * @param $api 接口地址
     * @param $param_arr 参数列表
     * @param $ret_arr 返回值列表
     */
    public function write_api_file($api, $param_arr, $ret_arr){
        $file_name = StringHelper::convertStringToCamelName($this->get_api_base_name($api), TRUE);
        $path = Config::output_folder.$file_name;
        $this->write_api_header_file($path.".h", $file_name, $api, $param_arr, $ret_arr);
        $this->write_api_implementation_file($path.".m", $api, $param_arr, $ret_arr);
    }

    private function write_api_header_file($path, $file_name, $api, $param_arr, $ret_arr){
        if(!file_exists($path)){
            $this->write_common_file_header($path);
            FileHelper::appendLine($path, "");
            FileHelper::appendLine($path, "@interface ".$file_name);
        }

        //+gameStartWithGameId:(NSInteger)game_id success:(void (^)(NSInteger))apisuccess failure:(void (^)(NSString *))apifailure;
        $function_name = "+".StringHelper::convertStringToCamelName($this->get_api_second_name($api), FALSE);
        if(count($param_arr) > 0){
            $function_name .= "With";
            foreach($param_arr as $param){
                $function_name .= StringHelper::convertStringToCamelName($param->name, TRUE);
                $function_name .= ":(".$this->get_oc_type_string($param->type).")".$param->name." ";
            }
        }

        //TODO: 发现不认识的类型需要 import
        $function_name .= "Success:(void (^)(";
        if(count($ret_arr) > 0){
            foreach($ret_arr as $index=>$param){
                $function_name .= $this->get_oc_type_string($param->type);
                if($index != count($ret_arr) - 1){
                    $function_name .= ", ";
                }
            }
        }else{
            $function_name .= "void";
        }
        $function_name .= "))apisuccess Failure:(void (^)(NSString *))apifailure;";

        FileHelper::appendLine($path, $function_name);
    }

    private function write_api_implementation_file($path, $api, $param_arr, $ret_arr){

    }

    private function write_header_end(array& $api_arr){
        $file_name_arr = array();
        foreach($api_arr as $api){
            $file_name = StringHelper::convertStringToCamelName($this->get_api_base_name($api), TRUE);
            if(! array_key_exists($file_name, $file_name_arr)){
                $file_name_arr[$file_name] = 1;
                FileHelper::appendLine(Config::output_folder.$file_name.".h", "@end");
            }
        }
    }

    private function get_oc_type_string($type){
        if($type == TypeConst::type_int || $type == TypeConst::type_long){
            return "NSInteger";
        }else if($type == TypeConst::type_string){
            return "NSString *";
        }else{
            return $type." *";
        }
    }

    private function get_api_base_name($api){
        $arr = explode("/", $api);
        return $arr[0];
    }

    private function get_api_second_name($api){
        $arr = explode("/", $api);
        return $arr[1];
    }

    private function get_api_camel_name($api){
        $api = str_replace("/", "_", $api);
        return "k".StringHelper::convertStringToCamelName($api, TRUE);
    }

    private function write_entity_header_file($path, $entity_name, $property_array){
        $this->write_common_file_header($path);
        FileHelper::appendLine($path, "@interface ".$entity_name);
        foreach($property_array as $param){
            FileHelper::appendLine($path, $this->get_property_code($param->name, $param->type));
        }
        FileHelper::appendLine($path, "@end");
    }

    private function get_property_code($name, $type){
        if($type == TypeConst::type_int || $type == TypeConst::type_long){
            return "@property (nonatomic, assign) NSInteger ".$name.";";
        }else if($type == TypeConst::type_string){
            return "@property (nonatomic, strong) NSString *".$name.";";
        }
        return "";
    }

    private function write_entity_implementation_file($path, $entity_name){
        $this->write_common_file_header($path);
        FileHelper::appendLine($path, "#import \"".$entity_name.".h\"");
        FileHelper::appendLine($path, "@implementation ".$entity_name);
        FileHelper::appendLine($path, "@end");
    }

    private function write_common_file_header($path){
        FileHelper::clearFile($path);
        FileHelper::appendLine($path, Config::file_prefix);
        FileHelper::appendLine($path, "//Created automatically on ".date("y-m-d H:i:s"));
        FileHelper::appendLine($path, "");
        FileHelper::appendLine($path, "#import <Fundation.h>");
    }

} 