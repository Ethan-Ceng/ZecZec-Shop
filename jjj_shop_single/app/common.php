<?php

use think\facade\Request;
use think\facade\Log;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use think\facade\Config;

// 應用公共檔案
/**
 * 列印除錯函式
 * @param $content
 * @param $is_die
 */
function pre($content, $is_die = true)
{

    header('Content-type: text/html; charset=utf-8');
    echo '<pre>' . print_r($content, true);
    $is_die && die();
}

/**
 * 隱藏敏感字元
 * @param $value
 * @return string
 */
function substr_cut($value)
{
    $strlen = mb_strlen($value, 'utf-8');
    if ($strlen <= 1) return $value;
    $firstStr = mb_substr($value, 0, 1, 'utf-8');
    $lastStr = mb_substr($value, -1, 1, 'utf-8');
    return $strlen == 2 ? $firstStr . str_repeat('*', $strlen - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}

/**
 * 獲取當前系統版本號
 * @return mixed|null
 * @throws Exception
 */
function get_version()
{
    try {
        $file = root_path() . '/version.json';
        $version = json_decode(file_get_contents($file), true);
        return $version['version'];
    } catch (\Exception $e) {
        return '';
    }

}

/**
 * 駝峰命名轉下劃線命名
 * @param $str
 * @return string
 */
function toUnderScore($str)
{
    $dstr = preg_replace_callback('/([A-Z]+)/', function ($matchs) {
        return '_' . strtolower($matchs[0]);
    }, $str);
    return trim(preg_replace('/_{2,}/', '_', $dstr), '_');
}

/**
 * 生成密碼hash值
 * @param $password
 * @return string
 */
function salt_hash($password)
{
    return md5(md5($password) . 'jjjshop_salt_2020');
}

/**
 * curl請求指定url (post)
 * @param $url
 * @param array $data
 * @return mixed
 */
function curlPost($url, $data = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**
 * 多維數組合並
 * @param $array1
 * @param $array2
 * @return array
 */
function array_merge_multiple($array1, $array2)
{
    $merge = $array1 + $array2;
    $data = [];
    foreach ($merge as $key => $val) {
        if (
            isset($array1[$key])
            && is_array($array1[$key])
            && isset($array2[$key])
            && is_array($array2[$key])
        ) {
            $data[$key] = array_merge_multiple($array1[$key], $array2[$key]);
        } else {
            $data[$key] = isset($array2[$key]) ? $array2[$key] : $array1[$key];
        }
    }
    return $data;
}

/**
 * 二維陣列排序
 * @param $arr
 * @param $keys
 * @param bool $desc
 * @return mixed
 */
function array_sort($arr, $keys, $desc = false)
{
    $key_value = $new_array = array();
    foreach ($arr as $k => $v) {
        $key_value[$k] = $v[$keys];
    }
    if ($desc) {
        arsort($key_value);
    } else {
        asort($key_value);
    }
    reset($key_value);
    foreach ($key_value as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/**
 * 資料匯出到excel(csv檔案)
 * @param $fileName
 * @param array $tileArray
 * @param array $dataArray
 */
function export_excel($fileName, $tileArray = [], $dataArray = [])
{
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 0);
    ob_end_clean();
    ob_start();
    header("Content-Type: text/csv");
    header("Content-Disposition:filename=" . $fileName);
    $fp = fopen('php://output', 'w');
    fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));// 轉碼 防止亂碼(比如微信暱稱)
    fputcsv($fp, $tileArray);
    $index = 0;
    foreach ($dataArray as $item) {
        if ($index == 1000) {
            $index = 0;
            ob_flush();
            flush();
        }
        $index++;
        fputcsv($fp, $item);
    }
    ob_flush();
    flush();
    ob_end_clean();
}

/**
 * 寫入日誌
 * @param $value
 * @param string $type
 */
function log_write($value, $channel = '')
{
    $msg = is_string($value) ? $value : var_export($value, true);
    if ($channel != '') {
        Log::channel('task')->write($msg);
    } else {
        Log::channel($channel)->write($msg);
    }
}

/**
 * 獲取當前域名及根路徑
 * @return string
 */
function base_url()
{
    static $baseUrl = '';
    if (empty($baseUrl)) {
        $request = Request::instance();
        //$subDir = str_replace('\\', '/', dirname($request->server('PHP_SELF')));
        $baseUrl = $request->scheme() . '://' . $request->host() . '/';
    }
    return $baseUrl;
}

/**
 * 左側填充0
 * @param $value
 * @param int $padLength
 * @return string
 */
function pad_left($value, $padLength = 2)
{
    return \str_pad($value, $padLength, "0", STR_PAD_LEFT);
}

/**
 * 過濾emoji表情
 * @param $text
 * @return null|string|string[]
 */
function filter_emoji($text)
{
    // 此處的preg_replace用於過濾emoji表情
    // 如需支援emoji表情, 需將mysql的編碼改為utf8mb4
    return preg_replace('/[\xf0-\xf7].{3}/', '', $text);
}


/**
 * 獲取全域性唯一識別符號
 * @param bool $trim
 * @return string
 */
function getGuidV4($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        $charid = com_create_guid();
        return $trim == true ? trim($charid, '{}') : $charid;
    }
    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    return $lbrace .
        substr($charid, 0, 8) . $hyphen .
        substr($charid, 8, 4) . $hyphen .
        substr($charid, 12, 4) . $hyphen .
        substr($charid, 16, 4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
}

function format_time($value)
{
    return date('Y-m-d', $value);
}


/**
 * curl請求指定url (get)
 * @param $url
 * @param array $data
 * @return mixed
 */
function curl($url, $data = [])
{
    // 處理get資料
    if (!empty($data)) {
        $url = $url . '?' . http_build_query($data);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//這個是重點。
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

/**
 * json 轉換true,false,數字轉成vue可直接用的
 */
function jsonRecursive(&$array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            jsonRecursive($array[$key]);
        } else {
            if ($value === 'true') {
                $array[$key] = true;
            } else if ($value === 'false') {
                $array[$key] = false;
            }
        }
    }
}

/**
 * 判斷瀏覽器名稱和版本
 */
function get_client_browser()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return 'robot！';
    }
    if ((false == strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE)) {
        return 'Internet Explorer 11.0';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0')) {
        return 'Internet Explorer 10.0';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0')) {
        return 'Internet Explorer 9.0';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) {
        return 'Internet Explorer 8.0';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) {
        return 'Internet Explorer 7.0';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
        return 'Internet Explorer 6.0';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Edge')) {
        return 'Edge';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
        return 'Firefox';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
        return 'Chrome';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
        return 'Safari';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
        return 'Opera';
    }
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], '360SE')) {
        return '360SE';
    }
    //微信瀏覽器
    if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessage')) {
        return 'MicroMessage';
    }
    return '';
}

//生成驗籤
function signToken($uid, $type)
{
    $key = Config::get('app.salt') . $type;//這裡是自定義的一個隨機字串，應該寫在config檔案中的，解密時也會用，相當    於加密中常用的 鹽  salt
    $token = array(
        "iss" => $key,        //簽發者 可以為空
        "aud" => '',          //面象的使用者，可以為空
        "iat" => time(),      //簽發時間
        "nbf" => time() + 3,    //在什麼時候jwt開始生效  （這裡表示生成100秒後才生效）
        "exp" => time() + 86400 * 30, //token 過期時間
        "data" => [           //記錄的userid的資訊，這裡是自已新增上去的，如果有其它資訊，可以再新增陣列的鍵值對
            'uid' => $uid,
            'type' => $type
        ]
    );
    $jwt = JWT::encode($token, $key, "HS256");  //根據引數生成了 token
    return $jwt;
}

//驗證token
function checkToken($token, $type)
{
    $key = Config::get('app.salt') . $type;
    $status = array("code" => -1);
    try {
        JWT::$leeway = 60;//當前時間減去60，把時間留點餘地
        $decoded = JWT::decode($token, new Key($key, 'HS256')); //HS256方式，這裡要和簽發的時候對應
        $arr = json_decode(json_encode($decoded), 1);
        $res['code'] = 1;
        $res['data'] = $arr['data'];
        return $res;
    } catch (\Firebase\JWT\SignatureInvalidException $e) { //簽名不正確
        $status['msg'] = "簽名不正確";
        return $status;
    } catch (\Firebase\JWT\BeforeValidException $e) { // 簽名在某個時間點之後才能用
        $status['msg'] = "token失效";
        return $status;
    } catch (\Firebase\JWT\ExpiredException $e) { // token過期
        $status['msg'] = "token失效";
        return $status;
    } catch (Exception $e) { //其他錯誤
        $status['msg'] = "未知錯誤";
        return $status;
    }
}

// 發送郵件
function sendEmail($to, $title, $body) {
    include_once (dirname(__FILE__) . '/common/library/mail.class.php');
    include_once (dirname(__FILE__) . '/common/library/smtp.class.php');
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->CharSet = 'UTF-8';  // 设置字符编码为 UTF-8
        $mail->Encoding = 'base64';  // 使用 base64 编码
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.genentech.icu';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'support@genentech.icu';
        $mail->Password   = '3V3MqdfisM';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
    
        // Recipients
        $mail->setFrom('support@genentech.icu', '=?UTF-8?B?'.base64_encode('支持团队').'?=');
        $mail->addAddress($to);
    
        // Content
        $mail->isHTML(true);
        $mail->Subject = '=?UTF-8?B?'.base64_encode($title).'?=';
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);  // 提供纯文本版本
    
        $mail->send();
    
        $errorMsg = '郵件已傳送，請點選郵箱連結完成認證';
    } catch (Exception $e) {
        $errorMsg = "郵件傳送失敗. Mailer Error: {$mail->ErrorInfo}";
    }
    return $errorMsg;
}