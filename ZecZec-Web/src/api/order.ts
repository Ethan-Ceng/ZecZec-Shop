import axios from 'axios';
import type {RouteRecordNormalized} from 'vue-router';
import {UserState} from '@/store/modules/user/types';


/**
 * 個人訂單
 * api/user.order/lists?dataType=payment&page=1&list_rows=10&token=23906f2d05b1ec40dd7f1f0d078ec661&app_id=10001
 */
export function getOrderList(params) {
  return axios({
    url: '/user.order/lists',
    method: 'get',
    params
  })
}

/**
 * 預覽訂單資訊
 * @param params
 */
export function getBuyOrder(params) {
  return axios({
    url: '/order.order/getBuyList',
    method: 'post',
    data: params
  })
}

/**
 * 立馬下單
 * @param params
 */
export function buyOrder(params) {
  return axios({
    url: '/order.order/buyList',
    method: 'post',
    data: params
  })
}

export function getOrderDetail(order_id) {
  return axios({
    url: '/user.order/detail',
    method: 'get',
    params: {order_id}
  })
}

/**
 * 直接餘額支付訂單
 * @param data
 * order_id "931"
 * payType 20
 * pay_source "h5"
 * use_balance: 1
 */
export function payOrder(data) {
  return axios({
    url: '/user.order/pay',
    method: 'post',
    data
  })
}

