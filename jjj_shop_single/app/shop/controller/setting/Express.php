<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Express as ExpressModel;

/**
 * 物流控制器
 */
class Express extends Controller
{
    /**
     * 物流資料
     */
    public function index()
    {
        $model = new ExpressModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('',compact('list'));
    }

    /**
     * 新增物流公司
     */
    public function add()
    {
        // 新增記錄
        $model = new ExpressModel;
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 物流公司詳情
     */
    public function detail($express_id)
    {
        // 物流公司詳情
        $detail = ExpressModel::detail($express_id);
        return $this->renderSuccess('',compact('detail'));

    }

    /**
     * 修改
     * @param $express_id
     * @return \think\response\Json
     */
    public function edit($express_id)
    {
        if($this->request->isGet()){
            return $this->detail($express_id);
        }
        $model = ExpressModel::detail($express_id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 刪除記錄
     */
    public function delete($express_id)
    {
        $model = ExpressModel::detail($express_id);
        if ($model->remove($express_id)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?:'刪除失敗');
    }

    /**
     *定義物流編碼列表
     */
    public function companyList()
    {
        $data = array(
            array(
                'company_name' => '順豐',
                'company_code' => 'shunfeng',
            ),
            array(
                'company_name' => '中通',
                'company_code' => 'zhongtong',
            ),
            array(
                'company_name' => '澳通華人物流',
                'company_code' => 'cllexpress',
            ),
            array(
                'company_name' => '斑馬物流',
                'company_code' => 'banma',
            ),
            array(
                'company_name' => '信豐物流',
                'company_code' => 'xinfengwuliu',
            ),
            array(
                'company_name' => '蘇寧訂單',
                'company_code' => 'suningorder',
            ),
            array(
                'company_name' => '宜送物流',
                'company_code' => 'yiex',
            ),
            array(
                'company_name' => 'AOL澳通速遞',
                'company_code' => 'aolau',
            ),
            array(
                'company_name' => 'TRAKPAK',
                'company_code' => 'trakpak',
            ),
            array(
                'company_name' => 'GTS快遞',
                'company_code' => 'gts',
            ),
            array(
                'company_name' => '通達興物流',
                'company_code' => 'tongdaxing',
            ),
            array(
                'company_name' => '中國香港(HongKong Post)英文',
                'company_code' => 'hkposten',
            ),
            array(
                'company_name' => '俄羅斯郵政(Russian Post)',
                'company_code' => 'pochta',
            ),
            array(
                'company_name' => '雲達通',
                'company_code' => 'ydglobe',
            ),
            array(
                'company_name' => 'EU-EXPRESS',
                'company_code' => 'euexpress',
            ),
            array(
                'company_name' => '廣州海關',
                'company_code' => 'gzcustoms',
            ),
            array(
                'company_name' => '杭州海關',
                'company_code' => 'hzcustoms',
            ),
            array(
                'company_name' => '南京海關',
                'company_code' => 'njcustoms',
            ),
            array(
                'company_name' => '北京海關',
                'company_code' => 'bjcustoms',
            ),
            array(
                'company_name' => '美西快遞',
                'company_code' => 'meixi',
            ),
            array(
                'company_name' => '順豐-美國件',
                'company_code' => 'shunfengen',
            ),
            array(
                'company_name' => '順豐速運',
                'company_code' => 'shunfeng',
            ),
            array(
                'company_name' => '順豐-繁體',
                'company_code' => 'shunfenghk',
            ),
            array(
                'company_name' => '泰國中通ZTO',
                'company_code' => 'thaizto',
            ),
            array(
                'company_name' => '中通（帶電話）',
                'company_code' => 'zhongtongphone',
            ),
            array(
                'company_name' => '中通國際',
                'company_code' => 'zhongtongguoji',
            ),
            array(
                'company_name' => '中通快運',
                'company_code' => 'zhongtongkuaiyun',
            ),
            array(
                'company_name' => '中通快遞',
                'company_code' => 'zhongtong',
            ),
            array(
                'company_name' => '韻豐物流',
                'company_code' => 'yunfeng56',
            ),
            array(
                'company_name' => '速通物流',
                'company_code' => 'sutongwuliu',
            ),
            array(
                'company_name' => '聯邦快遞',
                'company_code' => 'lianbangkuaidi',
            ),
            array(
                'company_name' => '深圳德創物流',
                'company_code' => 'dechuangwuliu',
            ),
            array(
                'company_name' => 'EMS-英文',
                'company_code' => 'emsen',
            ),
            array(
                'company_name' => '小紅書',
                'company_code' => 'xiaohongshuorder',
            ),
        );
        return $this->renderSuccess('', compact('data'));
    }
}
