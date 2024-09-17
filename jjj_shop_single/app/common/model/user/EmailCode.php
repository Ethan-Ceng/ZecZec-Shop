<?php


namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\model\user\PointsLog as PointsLogModel;
use app\common\model\settings\Setting as SettingModel;
use app\common\model\plus\agent\Referee as RefereeModel;
use app\common\model\plus\invitationgift\Partake;

/**
 * 使用者模型
 */
class EmailCode extends BaseModel
{
    protected $pk = 'id';
    protected $name = 'user_emailcode';
}