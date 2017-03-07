<?php
/**
 * @time:	2017-03-07
 * @author:	boser
 */

/**
 * 变量调试
 * @param $x
 */
function x($x){
    echo '<pre>';
    print_r($x);
    echo '</pre>';
}

/**
 * 文件调试
 * @param $f
 */
function _f($f){
    file_put_contents('debug.md', "\n", FILE_APPEND);
    is_array($f) ? file_put_contents('debug.md', var_export($f, true), FILE_APPEND) : file_put_contents('debug.md', $f, FILE_APPEND);
    file_put_contents('debug.md', "\n", FILE_APPEND);
}


