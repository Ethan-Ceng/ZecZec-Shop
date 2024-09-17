<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);
namespace think\trace;

use think\App;
use think\Response;

/**
 * 瀏覽器除錯輸出
 */
class Console
{
    protected $config = [
        'tabs' => ['base' => '基本', 'file' => '檔案', 'info' => '流程', 'notice|error' => '錯誤', 'sql' => 'SQL', 'debug|log' => '除錯'],
    ];

    // 例項化並傳入引數
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 除錯輸出介面
     * @access public
     * @param  Response  $response Response物件
     * @param  array     $log 日誌資訊
     * @return string|bool
     */
    public function output(App $app, Response $response, array $log = [])
    {
        $request     = $app->request;
        $contentType = $response->getHeader('Content-Type');

        if ($request->isJson() || $request->isAjax()) {
            return false;
        } elseif (!empty($contentType) && strpos($contentType, 'html') === false) {
            return false;
        } elseif ($response->getCode() == 204) {
            return false;
        }

        // 獲取基本資訊
        $runtime = number_format(microtime(true) - $app->getBeginTime(), 10, '.', '');
        $reqs    = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $mem     = number_format((memory_get_usage() - $app->getBeginMem()) / 1024, 2);

        if ($request->host()) {
            $uri = $request->protocol() . ' ' . $request->method() . ' : ' . $request->url(true);
        } else {
            $uri = 'cmd:' . implode(' ', $_SERVER['argv']);
        }

        // 頁面Trace資訊
        $base = [
            '請求資訊' => date('Y-m-d H:i:s', $request->time() ?: time()) . ' ' . $uri,
            '執行時間' => number_format((float) $runtime, 6) . 's [ 吞吐率：' . $reqs . 'req/s ] 記憶體消耗：' . $mem . 'kb 檔案載入：' . count(get_included_files()),
            '查詢資訊' => $app->db->getQueryTimes() . ' queries',
            '快取資訊' => $app->cache->getReadTimes() . ' reads,' . $app->cache->getWriteTimes() . ' writes',
        ];

        if (isset($app->session)) {
            $base['會話資訊'] = 'SESSION_ID=' . $app->session->getId();
        }

        $info = $this->getFileInfo();

        // 頁面Trace資訊
        $trace = [];
        foreach ($this->config['tabs'] as $name => $title) {
            $name = strtolower($name);
            switch ($name) {
                case 'base': // 基本資訊
                    $trace[$title] = $base;
                    break;
                case 'file': // 檔案資訊
                    $trace[$title] = $info;
                    break;
                default: // 除錯資訊
                    if (strpos($name, '|')) {
                        // 多組資訊
                        $names  = explode('|', $name);
                        $result = [];
                        foreach ($names as $item) {
                            $result = array_merge($result, $log[$item] ?? []);
                        }
                        $trace[$title] = $result;
                    } else {
                        $trace[$title] = $log[$name] ?? '';
                    }
            }
        }

        //輸出到控制檯
        $lines = '';
        foreach ($trace as $type => $msg) {
            $lines .= $this->console($type, empty($msg) ? [] : $msg);
        }
        $js = <<<JS

<script type='text/javascript'>
{$lines}
</script>
JS;
        return $js;
    }

    protected function console(string $type, $msg)
    {
        $type       = strtolower($type);
        $trace_tabs = array_values($this->config['tabs']);
        $line       = [];
        $line[]     = ($type == $trace_tabs[0] || '除錯' == $type || '錯誤' == $type)
        ? "console.group('{$type}');"
        : "console.groupCollapsed('{$type}');";

        foreach ((array) $msg as $key => $m) {
            switch ($type) {
                case '除錯':
                    $var_type = gettype($m);
                    if (in_array($var_type, ['array', 'string'])) {
                        $line[] = "console.log(" . json_encode($m) . ");";
                    } else {
                        $line[] = "console.log(" . json_encode(var_export($m, true)) . ");";
                    }
                    break;
                case '錯誤':
                    $msg    = str_replace("\n", '\n', addslashes(is_scalar($m) ? $m : json_encode($m)));
                    $style  = 'color:#F4006B;font-size:14px;';
                    $line[] = "console.error(\"%c{$msg}\", \"{$style}\");";
                    break;
                case 'sql':
                    $msg    = str_replace("\n", '\n', addslashes($m));
                    $style  = "color:#009bb4;";
                    $line[] = "console.log(\"%c{$msg}\", \"{$style}\");";
                    break;
                default:
                    $m      = is_string($key) ? $key . ' ' . $m : $key + 1 . ' ' . $m;
                    $msg    = json_encode($m);
                    $line[] = "console.log({$msg});";
                    break;
            }
        }
        $line[] = "console.groupEnd();";
        return implode(PHP_EOL, $line);
    }

    /**
     * 獲取檔案載入資訊
     * @access protected
     * @return integer|array
     */
    protected function getFileInfo()
    {
        $files = get_included_files();
        $info  = [];

        foreach ($files as $key => $file) {
            $info[] = $file . ' ( ' . number_format(filesize($file) / 1024, 2) . ' KB )';
        }

        return $info;
    }
}
