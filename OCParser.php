<?php
/**
 * Created by PhpStorm.
 * User: zhuguangwen
 * Date: 15/7/17
 * Time: 下午8:15
 */

include_once("Param.php");
include_once("Constant.php");
include_once("Config.php");
include_once("FileHelper.php");
include_once("StringHelper.php");
include_once("TypeConst.php");

class OCParser {

    /**
     * 服务器定义的int类型变量写入
     * @param array& $const_arr int constant 数组
     */
    public function write_const_int_file(array& $const_arr){
        $path = Config::output_folder.Config::output_api_const_file_name;
        if(! file_exists($path)){
            $this->write_common_file_header($path);
        }

        FileHelper::appendLine($path, "");
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
        $path = Config::output_folder.Config::output_api_const_file_name;
        if(! file_exists($path)){
            $this->write_common_file_header($path);
        }

        FileHelper::appendLine($path, "");
        foreach($api_arr as $index=>$api_str){
            $api_name = $this->get_api_camel_name($api_str);
            $code = "static NSString const *".$api_name." = @\"".$api_str."\";";
            FileHelper::appendLine($path, $code);

            //空行
            if($index != count($api_arr) - 1){
                $this_base_name = $this->get_api_base_name($api_str);
                $next_base_name = $this->get_api_base_name($api_arr[$index+1]);
                if($this_base_name != $next_base_name){
                    FileHelper::appendLine($path, "");
                }
            }
        }
    }

    /**
     * 实体类写入
     * @param $entity_name 实体名称
     * @param $property_code_arr 实体成员的数组
     */
    public function write_entity_file($entity_name, $property_code_arr){
        $this->write_entity_header_file(Config::output_folder.Config::output_entity_header_file, $entity_name, $property_code_arr);
        $this->write_entity_implementation_file(Config::output_folder.Config::output_entity_implementation_file, $entity_name);
    }

    /**
     * api实现文件的写入
     * @param $api 接口地址
     * @param $param_arr 参数列表
     * @param $ret_arr 返回值列表
     */
    public function write_api_file($api, $param_arr, $ret_arr){
        $this->write_api_header_file(Config::output_folder.Config::output_api_header_file, $api, $param_arr, $ret_arr);
        $this->write_api_implementation_file(Config::output_folder.Config::output_api_implementation_file, $api, $param_arr, $ret_arr);
    }

    /**
     * 解析结束
     */
    public function on_parse_end(){
        FileHelper::appendLine(Config::output_folder.Config::output_api_header_file, "@end");
        FileHelper::appendLine(Config::output_folder.Config::output_api_implementation_file, "");
        FileHelper::appendLine(Config::output_folder.Config::output_api_implementation_file, "@end");
    }

    private function write_entity_header_file($path, $entity_name, $property_array){
        if(! file_exists($path)){
            $this->write_common_file_header($path);
            FileHelper::appendLine($path, "#import \"".Config::entity_parent_class.".h\"");
        }
        FileHelper::appendLine($path, "");
        FileHelper::appendLine($path, "@interface ".$entity_name." : ".Config::entity_parent_class);
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
        if(! file_exists($path)){
            $this->write_common_file_header($path);
            FileHelper::appendLine($path, "#import \"".Config::output_entity_header_file.".h\"");
        }
        FileHelper::appendLine($path, "");
        FileHelper::appendLine($path, "@implementation ".$entity_name);
        FileHelper::appendLine($path, "@end");
    }

    private function write_api_header_file($path, $api, $param_arr, $ret_arr){
        if(!file_exists($path)){
            $this->write_common_file_header($path);
            $api_class_name = $this->class_name_from_header_file_name(Config::output_entity_header_file);
            FileHelper::appendLine($path, "#import \"".Config::api_use_http_class.".h\"");
            FileHelper::appendLine($path, "#import \"".$api_class_name."\"");
            FileHelper::appendLine($path, "");
            FileHelper::appendLine($path, "@interface ".$api_class_name." : NSObject");
        }

        $function_name = $this->get_api_func_name($api, $param_arr, $ret_arr).";";
        FileHelper::appendLine($path, $function_name);
    }

    private function get_api_func_name($api, array& $param_arr, array& $ret_arr){
        //+gameStartWithGameId:(NSInteger)game_id success:(void (^)(NSInteger))apisuccess failure:(void (^)(NSString *))apifailure;

        $function_name = "+".StringHelper::convertStringToCamelName($this->get_api_second_name($api), FALSE);
        if(count($param_arr) > 0){
            $function_name .= "With";
            foreach($param_arr as $param){
                $function_name .= StringHelper::convertStringToCamelName($param->name, TRUE);
                $function_name .= ":(".$this->get_oc_type_string($param->type).")".$param->name." ";
            }
        }

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
        $function_name .= "))apisuccess Failure:(void (^)(NSString *))apifailure";

        return $function_name;
    }

    private function write_api_implementation_file($path, $api, $param_arr, $ret_arr){
        if(!file_exists($path)){
            $this->write_common_file_header($path);
            $api_class_name = $this->class_name_from_header_file_name(Config::output_entity_header_file);
            FileHelper::appendLine($path, "#import \"".$api_class_name.".h\"");
            FileHelper::appendLine($path, "");
            FileHelper::appendLine($path, "@implementation ".$api_class_name);
        }

        $template = Config::api_m_template;

        $api_func_name = $this->get_api_func_name($api, $param_arr, $ret_arr);
        $template = str_replace("{apiFuncName}", $api_func_name, $template);

        $set_params = "";
        if(count($param_arr) > 0){
            foreach($param_arr as $param){
                if($param->type == TypeConst::type_int || $param->type == TypeConst::type_long){
                    $set_params .= "NSNumber *".$param->name."Number = [[NSNumber alloc] initWithInteger:".$param->name."];\n    ";
                    $set_params .= "[params setValue:".$param->name."Number forKey:@\"".$param->name."\"];\n    ";
                }else if($param->type == TypeConst::type_string){
                    $set_params .= "[params setVlaue:".$param->name." forKey:\"@".$param->name."\"];\n    ";
                }
            }
        }
        $template = str_replace("{setParams}", $set_params, $template);

        $on_success_code = "";
        if(count($ret_arr) > 0){
            $on_success_code = "apisuccess(";
            foreach($ret_arr as $index=>$param){
                if($param->type == TypeConst::type_long || $param->type == TypeConst::type_int){
                    $on_success_code .= "[result[@\"".$param->name."\"] integerValue]";
                }else if($param->type == TypeConst::type_string){
                    $on_success_code .= "result[@\"".$param->name."\"]";
                }else{
                    //[GameInfo modelListFromDictionaryList:infoArr]
                    $list_flag = $param->is_list ? "List" : "";
                    $on_success_code .= "[".$param->Type." model".$list_flag."FromDictionary".$list_flag.": result[@\"".$param->name."\"]]";
                }
                if($index != count($ret_arr) - 1){
                    $on_success_code .= ", ";
                }
            }
            $on_success_code .= ");";
        }else{
            $on_success_code = "apisuccess();";
        }
        $template = str_replace("{onSuccessCode}", $on_success_code, $template);

        FileHelper::appendLine($path, "");
        FileHelper::appendLine($path, $template);
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

    private function class_name_from_header_file_name($header_file_name){
        return str_replace(".h", "", $header_file_name);
    }

    private function write_common_file_header($path){
        FileHelper::clearFile($path);
        FileHelper::appendLine($path, Config::file_prefix);
        FileHelper::appendLine($path, "//Created automatically on ".date("y-m-d H:i:s"));
        FileHelper::appendLine($path, "");
        FileHelper::appendLine($path, "#import <Fundation.h>");
    }

} 