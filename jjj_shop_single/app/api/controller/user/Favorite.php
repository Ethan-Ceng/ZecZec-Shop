<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\user\Favorite as FavoriteModel;
/**
 * 我的收藏商品和關注店鋪
 */
class Favorite extends Controller
{
    protected $user;
     /**
     * 構造方法
     */
    public function initialize()
    {   
        parent::initialize();
        $this->user = $this->getUser();
    }

    /**
     * 取消或者收藏
     */
    public function fav($product_id){
        $user_id = $this->user['user_id'];
        $model = FavoriteModel::detail($product_id, $user_id);
        if($model){
            if($model->cancelFav()){
                return $this->renderSuccess('操作成功');
            }
        }else{
            // 新增
            $model = new FavoriteModel();
            if($model->addFav($product_id, $user_id)){
                return $this->renderSuccess('收藏成功');
            }
        }
        return $this->renderSuccess($model->getError()?:'操作失敗');
    }

    /**
     * 我的收藏
     */
    public function list(){
        $favorite_model = new FavoriteModel;
        $list = $favorite_model->getList($this->user['user_id'],$this->postData());
        return $this->renderSuccess('',compact('list'));
    }
}