<?php
/**
 * Created by PhpStorm.
 * User: zhuguangwen
 * Date: 15/7/17
 * Time: 下午7:32
 */

class FileHelper {

    static function clearFile($file){
        file_put_contents($file, "");
    }

    static function appendLine($file, $content){
        file_put_contents($file, $content."\n", FILE_APPEND);
    }

} 