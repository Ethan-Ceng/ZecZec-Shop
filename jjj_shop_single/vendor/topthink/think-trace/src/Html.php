<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);
namespace think\trace;

use think\App;
use think\Response;

/**
 * 頁面Trace除錯
 */
class Html
{
    protected $config = [
        'file' => '',
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
     * @param  App      $app 應用例項
     * @param  Response $response Response物件
     * @param  array    $log 日誌資訊
     * @return bool|string
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

        // 頁面Trace資訊
        if ($request->host()) {
            $uri = $request->protocol() . ' ' . $request->method() . ' : ' . $request->url(true);
        } else {
            $uri = 'cmd:' . implode(' ', $_SERVER['argv']);
        }

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
        // 呼叫Trace頁面模板
        ob_start();
        include $this->config['file'] ?: __DIR__ . '/tpl/page_trace.tpl';
        return ob_get_clean();
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
