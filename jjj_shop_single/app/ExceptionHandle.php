<?php
namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 應用異常處理類
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要記錄資訊（日誌）的異常類列表
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 記錄異常資訊（包括日誌或者其它方式記錄）
     */
    public function report(Throwable $exception): void
    {
        // 使用內建的方式記錄異常日誌
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     */
    public function render($request, Throwable $e): Response
    {
        // 新增自定義異常處理機制

        // 其他錯誤交給系統處理
        return parent::render($request, $e);
    }
}
