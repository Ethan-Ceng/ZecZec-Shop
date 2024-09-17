<?php

namespace app\common\model\plus\live;

use app\common\exception\BaseException;
use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\wx\LiveRoom as WxLiveRoom;
use app\common\model\BaseModel;

/**
 * 微信直播模型
 */
class WxLive extends BaseModel
{
    protected $name = 'app_wx_live';
    protected $pk = 'live_id';
    //附加欄位
    protected $append = ['start_time_text', 'end_time_text'];

    /**
     * 有效期-開始時間
     */
    public function getStartTimeTextAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['start_time']);
    }

    /**
     * 有效期-開始時間
     * @param $value
     * @return array
     */
    public function getEndTimeTextAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['end_time']);
    }

    /**
     * 詳情
     */
    public static function detail($live_id)
    {
        return (new static())->find($live_id);
    }

    /**
     * 同步直播間
     */
    public function syn($app_id = null)
    {
        // 小程式配置資訊
        $app = AppWx::getApp($app_id);
        // 請求api資料
        $live_room = new WxLiveRoom($app);
        $response = $live_room->syn();
        $isEmpty = false;
        if ($response === false) {
            if ($live_room->getError() == 'empty') {
                $isEmpty = true;
            } else {
                throw new BaseException(['msg' => '獲取直播房間列表請求失敗：' . $live_room->getError()]);
            }
        }
        // 格式化返回的列表資料
        $roomList = [];
        if (!$isEmpty) {
            foreach ($response['room_info'] as $item) {
                $roomList[$item['roomid']] = $item;
            }
        }

        $roomIds = array_column($roomList, 'roomid');
        // 本地歷史資料
        $hasRoomIds = $this->getRoomIds();
        // 新增資料庫沒有的
        $this->addRoom($hasRoomIds, $roomIds, $roomList, $app_id);
        // 刪除微信小程式已刪除的房間
        $this->deleteRoom($hasRoomIds, $roomIds);
        // 更新本地直播間
        $this->updateRoom($hasRoomIds, $roomIds, $roomList, $app_id);
        return true;
    }

    /**
     * 獲取本地直播間
     */
    private function getRoomIds()
    {
        return $this->where('is_delete', '=', 0)->column('roomid', 'live_id');
    }

    /**
     * 同步新增直播間
     */
    private function addRoom($hasRoomIds, $roomIds, $roomList, $app_id = "")
    {
        // 需要新增的直播間ID
        $ids = array_values(array_diff($roomIds, $hasRoomIds));
        if (empty($ids)) return true;

        // 整理新增資料
        $saveData = [];
        foreach ($ids as $roomId) {
            $item = $roomList[$roomId];
            $saveData[] = [
                'roomid' => $roomId,
                'name' => $item['name'],
                'cover_img' => $item['cover_img'],
                'share_img' => $item['share_img'],
                'anchor_name' => $item['anchor_name'],
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time'],
                'live_status' => $item['live_status'],
                'close_goods' => $item['close_goods'],
                'close_like' => $item['close_like'],
                'close_comment' => $item['close_comment'],
                'close_replay' => $item['close_replay'],
                'app_id' => $app_id ? $app_id : self::$app_id,
            ];
        }
        // 批次新增直播間
        return $this->saveAll($saveData);
    }

    /**
     * 同步刪除直播間
     */
    private function deleteRoom($hasRoomIds, $roomIds)
    {
        // 需要刪除的直播間ID
        $ids = array_values(array_diff($hasRoomIds, $roomIds));
        if (empty($ids)) return true;
        // 批次刪除直播間
        return self::where('roomid', 'in', $ids)->delete();
    }

    /**
     * 修改本地直播間
     */
    private function updateRoom($hasRoomIds, $roomIds, $roomList, $app_id = "")
    {
        // 需要新增的直播間ID
        $ids = array_values(array_intersect($roomIds, $hasRoomIds));
        if (empty($ids)) return true;

        // 整理新增資料
        $saveData = [];
        foreach ($ids as $roomId) {
            $item = $roomList[$roomId];
            $saveData[] = [
                'data' => [
                    'roomid' => $roomId,
                    'name' => $item['name'],
                    'cover_img' => $item['cover_img'],
                    'share_img' => $item['share_img'],
                    'feeds_img' => $item['feeds_img'],
                    'anchor_name' => $item['anchor_name'],
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'live_status' => $item['live_status'],
                    'close_goods' => $item['close_goods'],
                    'close_like' => $item['close_like'],
                    'close_comment' => $item['close_comment'],
                    'close_replay' => $item['close_replay'],
                ],
                'where' => [
                    'live_id' => array_search($roomId, $hasRoomIds),
                ],
            ];
        }
        // 批次更新直播間
        return $this->updateAll($saveData);
    }

}
