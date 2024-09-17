<?php
declare (strict_types=1);

namespace app;


/**
 * 控制器基礎類
 */
abstract class JjjController extends BaseController
{
    /**
     * 返回封裝後的 API 資料到客戶端
     */
    protected function renderJson($code = 1, $msg = '', $data = [])
    {
        return compact('code', 'msg', 'data');
    }

    /**
     * 返回操作成功json
     */
    protected function renderSuccess($msg = 'success', $data = [])
    {
        return json($this->renderJson(1, $msg, $data));
    }

    /**
     * 返回操作失敗json
     */
    protected function renderError($msg = 'error', $data = [], $code = 0)
    {
        return json($this->renderJson($code, $msg, $data));
    }

    /**
     * 獲取post資料 (陣列)
     */
    protected function postData($key = null)
    {
        return $this->request->param(is_null($key) ? '' : $key . '/a');
    }

    /**
     * 獲取post資料 (陣列)
     */
    protected function getData($key = null)
    {
        return $this->request->get(is_null($key) ? '' : $key);
    }
}
