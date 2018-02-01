<?php
/**
 * 公共函数库
 * @author boser
 * @time 2017/10/24
 */


/**
 * ajax返回
 * @param $data
 * @return json
 */
function ajaxRtn($data){
    $data['code'] = (isset($data['code']) && $data['code']) ? $data['code'] : 404;
    $data['msg'] = (isset($data['msg']) && $data['msg']) ? $data['msg'] : 'Not Found';
    $data['data'] = (isset($data['data']) && $data['data']) ? $data['data'] : [];
    echo json_encode($data);
}


/**
 * 循环删除目录下的所有文件
 * @param $dirName
 * @return mixed
 */
function delFileUnderDir($dirName) {
    if ($handle = opendir("$dirName")) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir("$dirName/$item")) {
                    delFileUnderDir("$dirName/$item");
                } else {
                    unlink("$dirName/$item");
                }
            }
        }
        closedir($handle);
    }
}

//循环删除目录和文件函数
function delDirAndFile( $dirName ){
    if ( $handle = opendir( "$dirName" ) ) {
        while ( false !== ( $item = readdir( $handle ) ) ) {
            if ( $item != "." && $item != ".." ) {
                if ( is_dir( "$dirName/$item" ) ) {
                    delDirAndFile( "$dirName/$item" );
                } else {
                    if( unlink( "$dirName/$item" ) )
                        echo "成功删除文件： $dirName/$item<br />n";
                }
            }
        }
        closedir( $handle );
        if( rmdir( $dirName ) )
            echo "成功删除目录： $dirName<br />n";
    }
}

/**
 * curl简单封装
 * @param $url
 * @return mixed
 */
function curl($url){
    //此处mora-hu代表用户ID
    $ch = curl_init($url);
    //初始化会话
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //设置请求COOKIE
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    // curl_close($ch);
    return curl_exec($ch);
}


/**
 * curl get请求
 * @param $url
 * @param $charset = 'utf-8'
 * @param bool $is_referer 是否伪造header请求来源 默认为假，否则为header请求来源
 * @param string $agent 是否设置cookie，默认为假，否则为真
 * @return mixed
 */
function _curlGet($url, $charset = 'utf-8', $is_referer = false, $agent = 'pc'){
    //初始化
    $ch = curl_init();
    //设置抓取的url
    curl_setopt($ch, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 设置agent
    $agents = [
        'pc' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.62 Safari/537.36',
        'm' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1'
    ];
    curl_setopt($ch, CURLOPT_USERAGENT, $agents[$agent]);
    $headers[] = 'charset=' . $charset;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);     //设置请求头
    if(false !== $is_referer){
        curl_setopt($ch, CURLOPT_REFERER, $is_referer);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT,5);    //超时时间（s）
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLINFO_HEADER_OUT, true); //TRUE 时追踪句柄的请求字符串，从 PHP 5.1.3 开始可用。这个很关键，就是允许你查看请求header

    //执行命令
    $data = curl_exec($ch);
    // 输出请求头
    $header = curl_getinfo($ch, CURLINFO_HEADER_OUT); //官方文档描述是“发送请求的字符串”，其实就是请求的header。这个就是直接查看请求header，因为上面允许查看
    debug($header);
    //关闭URL请求
    curl_close($ch);
    return $data;
}

/**
 * curl get请求[爬虫版]
 * @param $url
 * @param $charset = 'utf-8'
 * @param bool $is_referer 是否伪造header请求来源 默认为假，否则为header请求来源
 * @param string $agent 是否设置cookie，默认为假，否则为真
 * @return mixed
 */
function _spiderGet($url, $charset = 'utf-8', $is_referer = false, $agent = 'pc'){
    $ch = curl_init();                              //初始化
    curl_setopt($ch, CURLOPT_URL, $url);            //设置抓取的url
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //设置获取的信息以文件流的形式返回，而不是直接输出。

    // Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8
    // Accept-Encoding:gzip, deflate
    // Accept-Language:zh-CN,zh;q=0.9
    // Cache-Control:no-cache
    // Connection:keep-alive
    // Cookie:kujiang_sid=3fr92lgcgla5271h412bri29f2; Hm_lvt_c95caf196d8460c1a86f43d7c1750326=1514973271; Hm_lpvt_c95caf196d8460c1a86f43d7c1750326=1514973273; kujiang_subsite=c24816e0f49ca9605321ebfc73add9d98b7f1eb1%7Em
    // Host:www.kujiang.com
    // Pragma:no-cache
    // Referer:http://www.kujiang.com/book/36883/catalog
    // Upgrade-Insecure-Requests:1
    // User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.62 Safari/537.36
    $agents = [
        'pc' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.62 Safari/537.36',
        'm' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1'
    ];
    // $exts = ['.com', '.cn', '.info', '.net', '.cc', '.top', '.io', '.org'];
    // foreach($exts as $ext){
    //     $pos = strpos($url, $ext);
    //     if(false !== $pos){
    //         $host = substr($url, 0, $pos) . $ext;
    //         $host = str_replace(['https://', 'http://', '//'], ['', '', ''], $host);
    //         break;
    //     }
    // }
    // $headers[] = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
    // $headers[] = 'Accept-Encoding:gzip, deflate';
    // $headers[] = 'Accept-Language:zh-CN,zh;q=0.9';
    $headers[] = 'Cache-Control:no-cache';
    // $headers[] = 'Connection:keep-alive';
    // $headers[] = 'Host:' . $host;
    // $headers[] = 'Set-Cookie:kujiang_sid=u18lr4ruikrogev072ftjo4od0; expires=Thu, 04-Jan-2018 04:07:02 GMT; Max-Age=3600; path=/; domain=.kujiang.com';
    // $headers[] = 'Cookie:kujiang_subsite=c24816e0f49ca9605321ebfc73add9d98b7f1eb1%7Em; kujiang_sid=u18lr4ruikrogev072ftjo4od0; Hm_lvt_c95caf196d8460c1a86f43d7c1750326=1514973271; Hm_lpvt_c95caf196d8460c1a86f43d7c1750326=1515035244';
    // $headers[] = 'Pragma:no-cache';
    debug('referer:' . $is_referer);
    $headers[] = $is_referer ? 'Referer:' . $is_referer : '';
    // $headers[] = 'Upgrade-Insecure-Requests:1';
    $headers[] = 'User-Agent:' . $agents[$agent];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);     //设置请求头
    curl_setopt($ch, CURLOPT_TIMEOUT,5);                //超时时间（s）
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);        //TRUE 时追踪句柄的请求字符串，从 PHP 5.1.3 开始可用。这个很关键，就是允许你查看请求header

    //执行命令
    $data = curl_exec($ch);
    // 输出请求头
    $header = curl_getinfo($ch, CURLINFO_HEADER_OUT);   //官方文档描述是“发送请求的字符串”，其实就是请求的header。这个就是直接查看请求header，因为上面允许查看
    debug($header);
    //关闭URL请求
    curl_close($ch);
    return $data;
}


/**
 * 下载微信生成的二维码
 * @step 结果集写入文件时，只取body键的值
 * @param $url
 * @return array
 */
function _downloadQrFromWechat($url){
    //初始化
    $ch = curl_init();
    //设置抓取的url
    curl_setopt($ch, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    // 从证书中检查SSL加密算法是否存在

    //执行命令
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    //关闭URL请求
    curl_close($ch);
    return array(
        'body' => $data,
        'header' => $info,
    );
}

/**
 * 写文件
 * @param $d
 * @param $file_name
 */
function debug($d, $file_name = 'debug.txt'){
    $old = @file_get_contents($file_name);
    $prefix = str_repeat('-', 10) . str_repeat(' ', 2) . date('y-m-d H:i:s') . str_repeat(' ', 2) . str_repeat('-', 10) . str_repeat(PHP_EOL, 2);
    $sufix = PHP_EOL;
    if(is_array($d)){
        file_put_contents($file_name, $prefix . var_export($d, true) . $sufix);
    }else{
        file_put_contents($file_name, $prefix . $d . $sufix);
    }
    file_put_contents($file_name, $old, FILE_APPEND);
}