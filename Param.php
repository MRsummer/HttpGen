<?php
/**
 * Created by PhpStorm.
 * User: zhuguangwen
 * Date: 15/7/17
 * Time: 下午10:10
 */

class Param {

    var $name;
    var $type;
    var $is_list;

    function __construct($name, $type, $is_list){
        $this->name = $name;
        $this->type = $type;
        $this->is_list = $is_list;
    }

} 