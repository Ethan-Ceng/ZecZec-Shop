<?php

namespace app\shop\controller\data;

use app\shop\controller\Controller;
use app\shop\model\plus\giftpackage\GiftPackage;
use app\shop\model\page\Page as PageModel;
use app\shop\model\plus\invitationgift\InvitationGift as InvitationModel;
use app\shop\model\plus\table\Table as TableModel;

/**
 * 超連結控制器
 */
class Link extends Controller
{
    /**
     *獲取資料
     */
    public function index()
    {
        // 禮包購
        $model = new GiftPackage();
        $list = $model->getDatas();
        $packageList = [];
        foreach ($list as $item) {
            $packageList[] = [
                'id' => $item['gift_package_id'],
                'url' => 'pages/plus/giftpackage/giftpackage?package_id=' . $item['gift_package_id'],
                'name' => $item['name'],
                'type' => '營銷'
            ];
        }
        // 邀請有禮
        $list = (new InvitationModel())->getLinkDatas();
        $invitationList = [];
        foreach ($list as $item) {
            $invitationList[] = [
                'id' => $item['invitation_gift_id'],
                'url' => 'pages/user/invite/invite?invitation_gift_id=' . $item['invitation_gift_id'],
                'name' => $item['name'],
                'type' => '營銷'
            ];
        }
        // 萬能表單
        $list = (new TableModel())->getAll();
        $tableList = [];
        foreach ($list as $item) {
            $tableList[] = [
                'id' => $item['table_id'],
                'url' => 'pages/plus/table/table?table_id=' . $item['table_id'],
                'name' => $item['name'],
                'type' => '表單'
            ];
        }
        return $this->renderSuccess('', compact('packageList', 'invitationList', 'tableList'));
    }

    /**
     * 獲取自定義頁面
     */
    public function getPageList()
    {
        $model = new PageModel;
        $list = $model->getLists();
        return $this->renderSuccess('', compact('list'));
    }
}
