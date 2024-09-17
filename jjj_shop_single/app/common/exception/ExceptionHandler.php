<?php

namespace app\common\exception;

use think\exception\Handle;
use think\Response;
use Throwable;
use think\facade\Env;
use think\facade\Log;

/**
 * 重寫Handle的render方法，實現自定義異常訊息
 */
class ExceptionHandler extends Handle
{
    protected $code;
    protected $message;

    /**
     * 輸出異常資訊
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof BaseException) {
            $this->code = $e->code;
            $this->message = $e->message;
        } else {
            $this->isJson = $request->isJson();
            if (Env::get('APP_DEBUG')) {
                return parent::render($request, $e);
            }
            $this->code = 0;
            $this->message = $e->getMessage() ?: '很抱歉，伺服器內部錯誤';
            $this->recordErrorLog($e);
        }
        return json(['msg' => $this->message, 'code' => $this->code]);
    }

    /**
     * 將異常寫入日誌
     */
    private function recordErrorLog(Throwable $e)
    {
        Log::write($e->getMessage(), 'error');
        Log::write($e->getTraceAsString(), 'error');
    }
}
