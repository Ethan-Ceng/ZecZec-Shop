<?php

namespace app\shop\controller\file;

use app\JjjController;
use app\common\model\file\UploadImage as UploadImageModel;

class Image extends JjjController
{
    /**
     * 檔案庫列表
     */
    public function list()
    {
        // 檔案列表
        $data = $this->postData();
        $data['app_id'] = 0;
        $list = (new UploadImageModel)->getlist($data);
        return $this->renderSuccess('success', compact('list'));
    }

    /**
     * 相簿分類列表
     */
    public function index()
    {
        // 分組列表
        $list = (new UploadImageModel)->getCategoryList();
        return $this->renderSuccess('success', compact('list'));
    }
}
