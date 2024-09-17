<?php
declare (strict_types=1);

namespace app;

use think\App;
use think\Validate;

/**
 * 控制器基礎類
 */
abstract class BaseController
{
    /**
     * Request例項
     */
    protected $request;

    /**
     * 應用例項
     */
    protected $app;

    /**
     * 是否批次驗證
     */
    protected $batchValidate = false;

    /**
     * 控制器中介軟體
     */
    protected $middleware = [];

    /**
     * 構造方法
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 驗證資料
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支援場景
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批次驗證
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

}
