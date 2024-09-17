<?php

namespace app\shop\service;

use think\Config;

/**
 * 商家後臺選單業務
 */
class MenusService
{
    // 存放例項
    static public $instance;

    // 商家後臺許可權
    private $auth;

    /**
     * 公有化獲取例項方法
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof MenusService)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 私有化構造方法
     */
    private function __construct()
    {
        $this->auth = AuthService::getInstance();
    }

    /**
     * 私有化克隆方法
     */
    private function __clone()
    {
    }

    /**
     * 一級選單
     */
    private function first(&$menus, $routeUri, $group)
    {
        foreach ($menus as $key => &$first) :
            // 一級選單索引url
            $indexData = $this->getMenusIndexUrls($first, 1);
            // 許可權驗證
            $first['index'] = $this->getAuthUrl($indexData);
            if ($first['index'] === false) {
                unset($menus[$key]);
                continue;
            }
            // 選單聚焦
            $first['active'] = $key === $group;
            // 遍歷：二級選單
            if (isset($first['submenu'])) {
                $this->second($first['submenu'], $routeUri);
            }
        endforeach;
    }

    /**
     * 二級選單
     */
    public function second(&$menus, $routeUri)
    {
        foreach ($menus as $key => &$second) :
            // 二級選單索引url
            $indexData = $this->getMenusIndexUrls($second, 2);
            // 許可權驗證
            $second['index'] = $this->getAuthUrl($indexData);
            if ($second['index'] === false) {
                unset($menus[$key]);
                continue;
            }
            // 二級選單所有uri
            $secondUris = [];
            // 遍歷：三級選單
            if (isset($second['submenu'])) {
                $this->third($second['submenu'], $routeUri, $secondUris);
            } else {
                if (isset($second['uris']))
                    $secondUris = array_merge($secondUris, $second['uris']);
                else
                    $secondUris[] = $second['index'];
            }
            // 二級選單：active
            !isset($second['active']) && $second['active'] = in_array($routeUri, $secondUris);
        endforeach;
        // 刪除空陣列
        $menus = array_filter($menus);
    }

    /**
     * 三級選單
     */
    private function third(&$menus, $routeUri, &$secondUris)
    {
        foreach ($menus as $key => &$third):
            // 三級選單索引url
            $indexData = $this->getMenusIndexUrls($third, 3);
            // 許可權驗證
            $third['index'] = $this->getAuthUrl($indexData);
            if ($third['index'] === false) {
                unset($menus[$key]);
                continue;
            }
            // 三級選單所有uri
            $thirdUris = [];
            if (isset($third['uris'])) {
                $secondUris = array_merge($secondUris, $third['uris']);
                $thirdUris = array_merge($thirdUris, $third['uris']);
            } else {
                $secondUris[] = $third['index'];
                $thirdUris[] = $third['index'];
            }
            $third['active'] = in_array($routeUri, $thirdUris);
        endforeach;
    }

    /**
     * 獲取指定選單下的所有索引url
     */
    private function getMenusIndexUrls($menus, $level = 1)
    {
        // 判斷是否存在url
        if (!isset($menus['index']) && !isset($menus['submenu'])) {
            return [];
        }
        $data = [];
        if (isset($menus['index']) && !empty($menus['index'])) {
            $data[] = $menus['index'];
        }
        if (isset($menus['submenu']) && !empty($menus['submenu'])) {
            foreach ($menus['submenu'] as $submenu) {
                $submenuIndex = $this->getMenusIndexUrls($submenu, ++$level);
                !is_null($submenuIndex) && $data = array_merge($data, $submenuIndex);
            }
        }
        return array_unique($data);
    }

    /**
     * 取出透過許可權驗證urk作為index
     */
    private function getAuthUrl($urls)
    {
        // 取出透過許可權驗證urk作為index
        foreach ($urls as $url) {
            if ($this->auth->checkPrivilege($url)) return $url;
        }
        return false;
    }

}