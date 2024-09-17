<?php

namespace app\common\service\product\factory;

use app\common\enum\order\OrderSourceEnum;

/**
 * 商品輔助工廠類
 */
class ProductFactory
{
    public static function getFactory($type = OrderSourceEnum::MASTER)
    {
        switch ($type) {
            case OrderSourceEnum::MASTER:
                return new MasterProductService();
                break;
            case OrderSourceEnum::POINTS;
                return new PointsProductService();
                break;
            case OrderSourceEnum::SECKILL:
                return new SeckillProductService();
                break;
            case OrderSourceEnum::BARGAIN:
                return new BargainProductService();
                break;
            case OrderSourceEnum::ASSEMBLE:
                return new AssembleProductService();
                break;
            case OrderSourceEnum::ADVANCE:
                return new AdvanceProductService();
                break;
            case OrderSourceEnum::LOTTERY:
                return new LotteryProductService();
                break;
            case OrderSourceEnum::GIFT:
                return new PackageProductService();
                break;
        }
        return false;
    }
}