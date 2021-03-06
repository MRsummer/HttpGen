<?php
/**
 * Created by PhpStorm.
 * User: zhuguangwen
 * Date: 15/7/17
 * Time: 下午5:54
 */

class Config{

    //input file names
    const entity_file_name = "apiconfig/entity.txt";
    const api_file_name = "apiconfig/api.txt";

    //class config
    const entity_parent_class = "WPModel";
    const api_use_http_class = "WPHttpClient";

    //output file names
    const output_folder = "network/";
    const output_api_const_file_name = "ApiConfig.h";//常量定义文件
    const output_entity_header_file = "HttpEntity.h";//实体 头文件
    const output_entity_implementation_file = "HttpEntity.m";//实体 实现文件
    const output_api_header_file = "HttpApi.h";//api头文件
    const output_api_implementation_file = "HttpApi.m";//api 实现文件

    //const string for generate code
    const file_prefix = "//this file is automatically generated by program, you should not change this file";
    const api_m_template =
'{apiFuncName}{
    NSMutableDictionary* params = [[NSMutableDictionary alloc] init];
    {setParams}
    [[WPHTTPClient sharedClient] POSTRequestWithWebAPI:{api} parameters:params success:^(id result) {
        {onSuccessCode}
    } failure:^(NSString* errmsg) {
        apifailure(errmsg);
    }];
}';
}