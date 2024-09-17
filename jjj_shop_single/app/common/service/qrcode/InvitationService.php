<?php

namespace app\common\service\qrcode;

use app\common\model\plus\invitationgift\InvitationGift as InvitationGiftModel;

/**
 * 推廣二維碼
 */
class InvitationService extends Base
{
    private $id;
    private $source;

    /**
     * 構造方法
     */
    public function __construct($id, $source)
    {
        parent::__construct();
        $this->id = $id;
        $this->source = $source;
    }

    /**
     * 獲取小程式碼
     */
    public function getImage()
    {
        $invitation = InvitationGiftModel::detail($this->id);
        // 儲存目錄
        $savePath = $this->getPosterPath($invitation['app_id']);
        // 刪除目錄下的檔案
        if (!$this->is_empty_dir($savePath)) {
            $this->deleteDir(substr($savePath, 0, -1));
        }
        mkdir($savePath, 0755, true);
        if ($this->source == 'wx') {
            // 下載小程式碼
            $this->saveInvitQrcodeToDir($invitation['app_id'], 'pages/user/invite/invite', $savePath, $this->id);
        } else if ($this->source == 'mp' || $this->source == 'h5') {
            $this->saveInvitMpQrcodeToDir('h5/pages/user/invite/invite', $savePath, $this->id, $invitation['app_id']);
        }

        $zipNameUrl = $this->getZipPath($invitation['app_id']);

        $zip = new \ZipArchive();
        if ($zip->open($zipNameUrl, \ZipArchive::OVERWRITE) !== TRUE) {
            //OVERWRITE 引數會覆寫壓縮包的檔案 檔案必須已經存在
            if ($zip->open($zipNameUrl, \ZipArchive::CREATE) !== true) {
                // 檔案不存在則生成一個新的檔案 用CREATE開啟檔案會追加內容至zip
                return '下載失敗，資料夾不存在';
            }
        }

        $this->addFileToZip($savePath, $zip); //呼叫方法，對要打包的根目錄進行操作，並將ZipArchive的物件傳遞給方法

        $zip->close(); //關閉處理的zip檔案

        $zipName = $this->source . '.zip';
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipName);
        readfile($zipNameUrl);
        header('Content-Length: ' . filesize($zipNameUrl));
    }


    /**
     * 二維碼檔案路徑
     */
    private function getPosterPath($app_id)
    {
        // 儲存路徑
        return root_path('public') . 'temp' . '/' . $app_id . '/invite-' . $this->id . '/' . $this->source . '/';
    }

    /**
     * 二維碼檔案路徑
     */
    private function getZipPath($app_id)
    {
        // 儲存路徑
        return root_path('public') . 'temp' . '/' . $app_id . '/invite-' . $this->id . '/' . $this->source . '.zip';
    }

    /**
     * 刪除當前目錄及其目錄下的所有目錄和檔案
     * $path 待刪除的目錄
     * @note  $path路徑結尾不要有斜槓/(例如:正確[$path='./static/image'],錯誤[$path='./static/image/'])
     */
    private function deleteDir($path)
    {
        if (is_dir($path)) {
            //掃描一個目錄內的所有目錄和檔案並返回陣列
            $dirs = scandir($path);
            foreach ($dirs as $dir) {
                //排除目錄中的當前目錄(.)和上一級目錄(..)
                if ($dir != '.' && $dir != '..') {
                    //如果是目錄則遞迴子目錄，繼續操作
                    $sonDir = $path . '/' . $dir;
                    if (is_dir($sonDir)) {
                        //遞迴刪除
                        $this->deleteDir($sonDir);
                        //目錄內的子目錄和檔案刪除後刪除空目錄
                        @rmdir($sonDir);
                    } else {
                        //如果是檔案直接刪除
                        @unlink($sonDir);
                    }
                }
            }
            @rmdir($path);
        }
    }

    /**
     * 打包資料夾
     */
    private function addFileToZip($path, $zip)
    {
        $handler = opendir($path);
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {
                if (is_dir($path . "/" . $filename)) {
                    $this->addFileToZip($path . "/" . $filename, $zip);
                } else { //將檔案加入zip物件
                    $zip->addFile($path . "/" . $filename);
                    $zip->renameName($path . "/" . $filename, $filename);
                }
            }
        }
        @closedir($handler);
    }

    private function is_empty_dir($fp)
    {
        if (!file_exists($fp)) {
            return false;
        }
        $H = @ opendir($fp);
        $i = 0;
        while ($_file = readdir($H)) {
            $i++;
        }
        closedir($H);
        if ($i > 2) {
            return false;
        } else {
            return true;
        }
    }
}