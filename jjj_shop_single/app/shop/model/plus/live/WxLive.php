<?php

namespace app\shop\model\plus\live;

use app\common\exception\BaseException;
use app\common\library\easywechat\AppWx;
use app\common\model\plus\live\WxLive as WxLiveModel;
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;

/**
 * 砍價模型
 */
class WxLive extends WxLiveModel
{
    /**
     *列表
     */
    public function getList($params)
    {
        $model = $this;
        if (isset($params['search']) && !empty($params['search'])) {
            $model = $model->where('name|anchor_name', 'like', "%{$params['search']}%");
        }
        return $model->order([
            'is_top' => 'desc',
            'live_status' => 'asc',
            'create_time' => 'desc'
        ])->paginate($params);
    }


    /**
     * 設定直播間置頂狀態
     */
    public function setTop($params)
    {
        return $this->save(['is_top' => (int)$params['is_top']]);
    }

    /**
     * 建立直播間
     */
    public function createRoom($data)
    {
        $roomId = $this->wxCreate($data);
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $data['roomid'] = $roomId;
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 修改直播間
     */
    public function editRoom($data)
    {
        $wxData = [
            'id' => $this['roomid'],
            'startTime' => strtotime($data['start_time']),
            'endTime' => strtotime($data['end_time']),
            'name' => $data['name'],
            'anchorName' => $data['anchor_name'],
            'anchorWechat' => $data['anchor_wechat'],
            'closeGoods' => $data['close_goods'],
            'closeLike' => $data['close_like'],
            'closeComment' => $data['close_comment'],
            'closeReplay' => $data['close_replay'],
            'coverImg' => $this->getMediaID($data['cover_img']),
            'shareImg' => $this->getMediaID($data['share_img']),
            'feedsImg' => $this->getMediaID($data['feeds_img']),
        ];
        $url = "wxaapi/broadcast/room/editroom?access_token=";
        $status = $this->wxHandle($wxData, $url);
        if ($status) {
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            return $this->save($data);
        } else {
            return false;
        }

    }

    /**
     * 刪除直播間
     */
    public function delRoom()
    {
        $wxData = [
            'id' => $this['roomid']
        ];
        $url = "wxaapi/broadcast/room/deleteroom?access_token=";
        $status = $this->wxHandle($wxData, $url);
        if ($status) {
            return $this->save(['is_delete' => 1]);
        } else {
            return false;
        }
    }

    /**
     * 建立微信直播間
     */
    public function wxCreate($data)
    {
        $data = [
            'startTime' => strtotime($data['start_time']),
            'endTime' => strtotime($data['end_time']),
            'name' => $data['name'],
            'anchorName' => $data['anchor_name'],
            'anchorWechat' => $data['anchor_wechat'],
            'closeGoods' => $data['close_goods'],
            'closeLike' => $data['close_like'],
            'closeComment' => $data['close_comment'],
            'closeReplay' => $data['close_replay'],
            'type' => 0,
            'coverImg' => $this->getMediaID($data['cover_img']),
            'shareImg' => $this->getMediaID($data['share_img']),
            'feedsImg' => $this->getMediaID($data['feeds_img']),
        ];
        $app = AppWx::getApp();
        $accessToken = $app->getAccessToken();
        $token = $accessToken->getToken();
        $api = $app->getClient();
        $response = $api->post("wxaapi/broadcast/room/create?access_token={$token}", $data);
        $result = $response->getContent();
        $result = json_decode($result, true);
        if (isset($result['errcode']) && $result['errcode'] == 0 && isset($result['roomId'])) {
            return $result['roomId'];
        } else {
            throw new BaseException(['msg' => '建立直播失敗:' . $response['errmsg']]);
        }
    }

    /**
     * 修改微信直播間
     */
    public function wxHandle($data, $url)
    {
        $app = AppWx::getApp();
        $accessToken = $app->getAccessToken();
        $token = $accessToken->getToken();
        $api = $app->getClient();
        $response = $api->post($url . $token, $data);
        $result = $response->getContent();
        $result = json_decode($result, true);
        if (isset($result['errcode']) && $result['errcode'] == 0) {
            return true;
        } else {
            throw new BaseException(['msg' => '操作失敗:' . $response['errmsg']]);
        }
    }

    /**
     * 獲取微信圖片mediaID
     */
    public function getMediaID($url)
    {
        $path = $this->saveTempImage(self::$app_id, $url);
        $app = AppWx::getApp();
        $accessToken = $app->getAccessToken();
        $token = $accessToken->getToken();
        $api = $app->getClient();
        $options = Form::create(
            [
                'media' => File::fromPath($path),
            ]
        )->toArray();
        $response = $api->post("cgi-bin/media/upload?access_token={$token}&type=image", $options);
        if (isset($response['errcode']) && $response['errcode']) {
            @unlink($path);
            throw new BaseException(['msg' => '獲取微信mediaID失敗:' . $response['errmsg']]);
        } else {
            @unlink($path);
            @unlink($path);
            return $response['media_id'];
        }
    }

    /**
     * 獲取網路圖片到臨時目錄
     */
    protected function saveTempImage($app_id, $url, $mark = 'live')
    {
        $dirPath = root_path('public') . "temp/{$app_id}/{$mark}";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        $savePath = $dirPath . '/' . $mark . '_' . md5($url) . '.png';
        if (file_exists($savePath)) return $savePath;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $img = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($savePath, 'w');
        fwrite($fp, $img);
        fclose($fp);
        return $savePath;
    }
}
