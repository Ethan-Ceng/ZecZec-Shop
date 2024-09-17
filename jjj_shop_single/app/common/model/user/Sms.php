<?php


namespace app\common\model\user;

use app\common\library\sms\Driver as SmsDriver;
use app\common\model\BaseModel;
use app\common\model\settings\Setting as SettingModel;

/**
 * 簡訊模型
 */
class Sms extends BaseModel
{
    protected $pk = 'sms_id';
    protected $name = 'sms';

    /**
     * 簡訊傳送
     * $sence 場景，login：登入 register：註冊 sms：手機號驗證碼登入
     */
    public function send($mobile, $sence = 'login')
    {
        if (empty($mobile)) {
            $this->error = '手機號碼不能為空';
            return false;
        }
        $smsConfig = SettingModel::getItem('sms', self::$app_id);
        $template_code = $smsConfig['engine'][$smsConfig['default']];
        $send_template = '';
        if ($sence == 'login') {
            $send_template = $template_code['template_code'];
            if (empty($send_template)) {
                $this->error = '簡訊登入未開啟';
                return false;
            }
        } else if ($sence == 'register') {
            $send_template = $template_code['template_code'];
            if (empty($send_template)) {
                $this->error = '簡訊登入未開啟';
                return false;
            }
            //判斷是否已經註冊 ->where('reg_source', 'in', ['h5', 'app'])
            $user = (new User)->where('mobile', '=', $mobile)
                ->where('is_delete', '=', 0)
                ->find();
            if ($user) {
                $this->error = '手機號碼已存在';
                return false;
            }
        }
        $code = str_pad(mt_rand(100000, 999999), 6, "0", STR_PAD_BOTH);
        $SmsDriver = new SmsDriver($smsConfig);
        $send_data = [
            'code' => $code
        ];
        //簡訊模板
        $flag = $SmsDriver->sendSms($mobile, $send_template, $send_data);
        if ($flag) {
            $this->save([
                'mobile' => $mobile,
                'code' => $code,
                'app_id' => self::$app_id
            ]);
        }
        return $flag;
    }
}