import axios from 'axios';
import type {RouteRecordNormalized} from 'vue-router';
import {UserState} from '@/store/modules/user/types';


/**
 * 商品分類
 */
export function getCategory() {
  return axios.get('/product.category/index',);
}

export function getProductList(params) {
  return axios({
    url: '/product.product/lists',
    method: 'get',
    params
  })
}

/**
 * 產品收藏
 * @param data
 */
export function getFavoriteList(data) {
  return axios({
    url: '/user.Favorite/list',
    method: 'post',
    data
  })
}

export function likeProduct(product_id) {
  return axios({
    url: '/user.favorite/fav',
    method: 'post',
    data: {product_id}
  })
}




// ?product_id=150
export function getProductDetail(product_id) {
  return axios({
    url: '/product.product/detail',
    method: 'get',
    params: {product_id}
  })
}

export function getProductComment(params) {
  return axios({
    url: '/product.comment/lists',
    method: 'get',
    params
  })
}



export function getProductFaq(product_id) {
  return axios({
    url: '/product.Faq/lists',
    method: 'get',
    params: {product_id}
  })
}

