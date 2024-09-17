<template>
	<view :data-theme="theme()" :class="theme() || ''">
		<!--列表-->
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50" @scrolltolower="scrolltolowerFunc">
			<view :class="topRefresh ? 'top-refresh open' : 'top-refresh'"><view class="circle" v-for="(circle, n) in 3" :key="n"></view></view>
			<view class="order-list">
				<view class="item" v-for="(item, index) in listData" :key="index">
					<view class="order-head d-b-c">
						<view>
							<text class="state-text theme-bg">{{ item.order_source_text }}</text>
							<text class="f28 flex-1 fb">订单号：{{ item.order_no }}</text>
						</view>
					</view>
					<!--一个商品显示-->
					<view class="one-product d-a-c" @click="gotoDetail(item.order_no)">
						<view>
							<view class="giftlist_item_num">{{item.point*1}}<text class='f20'>个</text></view>
							<view class="giftlist_item_type">{{points_name()}}</view>
						</view>
						<view>
							<view class="giftlist_item_num">{{item.coupon_num}}<text class="f20">张</text></view>
							<view class="giftlist_item_type">优惠券</view>
						</view>
						<view>
							<view class="giftlist_item_num">{{item.product_num}}<text class="f20">件</text></view>
							<view class="giftlist_item_type">商品</view>
						</view>						
						<view class="total-count">
							<view class="left-shadow"></view>
							<view class="price f22">
								¥
								<text class="f40">{{item.pay_price}}</text>
							</view>
							
						</view>
					</view>
				</view>
				<view class="d-c-c p30" v-if="listData.length == 0 && !loading">
					<text class="iconfont icon-wushuju"></text>
					<text class="cont">亲，暂无相关记录哦</text>
				</view>
				<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
			</view>
		</scroll-view>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more.vue';
import { pay } from '@/common/pay.js';
export default {
	components: {
		uniLoadMore
	},
	data() {
		return {
			/*手机高度*/
			phoneHeight: 0,
			/*可滚动视图区域高度*/
			scrollviewHigh: 0,
			/*顶部刷新*/
			topRefresh: false,
			/*数据*/
			listData: [],
			/*是否显示支付类别弹窗*/
			isPayPopup: false,
			/*订单id*/
			order_id: 0,
			/*最后一页码数*/
			last_page: 0,
			/*当前页面*/
			page: 1,
			/*每页条数*/
			list_rows: 10,
			/*有没有等多*/
			no_more: false,
			/*是否正在加载*/
			loading: true,
			/*是否显示核销二维码*/
			isCodeImg: false,
			codeImg: ''
		};
	},
	computed: {
		/*加载中状态*/
		loadingType() {
			if (this.loading) {
				return 1;
			} else {
				if (this.listData.length != 0 && this.no_more) {
					return 2;
				} else {
					return 0;
				}
			}
		}
	},
	onLoad(e) {

	},
	mounted() {
		this.init();
		/*获取订单列表*/
		this.getData();
	},
	methods: {
		/*初始化*/
		init() {
			let self = this;
			uni.getSystemInfo({
				success(res) {
					self.phoneHeight = res.windowHeight;
					self.scrollviewHigh = self.phoneHeight;
				}
			});
		},
		/*可滚动视图区域到底触发*/
		scrolltolowerFunc() {
			let self = this;
			if (self.no_more) {
				return;
			}
			self.page++;
			if (self.page <= self.last_page) {
				self.getData();
			} else {
				self.no_more = true;
			}
		},

		/*获取数据*/
		getData() {
			let self = this;
			self.loading = true;
			self._get(
				'plus.package.order/orderlist',
				{
					page: self.page,
					list_rows: self.list_rows
				},
				function(res) {
					self.loading = false;
					self.listData = self.listData.concat(res.data.list.data);
					self.last_page = res.data.list.last_page;
					if (res.data.list.last_page <= 1) {
						self.no_more = true;
					} else {
						self.no_more = false;
					}
				}
			);
		},

		/*跳转页面*/
		gotoDetail(e) {
			this.gotoPage('/pages/plus/giftpackage/order_detail?order_no=' + e);
		},
	}
};
</script>

<style lang="scss">
	.order-list{
		padding: 30rpx;

	}
.order-list .order-head .state-text {
	padding: 2rpx 4rpx;
	margin-right: 20rpx;
	border-radius: 4rpx;
	// background: $dominant-color;
	color: #ffffff;
}

.order-list .item {
	margin-top: 30rpx;
	padding: 30rpx;
	background: #ffffff;
	border-radius: 15rpx;
}

.order-list .product-list,
.order-list .one-product {
	height: 160rpx;
}

.one-product .pro-info {
	padding: 0 30rpx;
	display: -webkit-box;
	overflow: hidden;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	font-size: 28rpx;
	color: #666666;
}

.order-list .cover,
.order-list .cover image {
	width: 160rpx;
	height: 160rpx;
}

.order-list .total-count {
	padding-left: 20rpx;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: flex-end;
}

.total-count .count {
	padding-top: 10rpx;
	color: #666;
	font-size: 28rpx;
}

.product-list .total-count {
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	background: rgba(255, 255, 255, 0.9);
}

.product-list .total-count .left-shadow {
	position: absolute;
	top: 0;
	bottom: 0;
	left: -24rpx;
	width: 24rpx;
	overflow: hidden;
}

.product-list .total-count .left-shadow::after {
	position: absolute;
	top: 0;
	bottom: 0;
	width: 24rpx;
	right: -12rpx;
	display: block;
	content: '';
	background-image: radial-gradient(rgba(0, 0, 0, 0.2) 10%, rgba(0, 0, 0, 0.1) 40%, rgba(0, 0, 0, 0) 80%);
}

.order-list .order-bts {
	display: flex;
	justify-content: flex-end;
	align-items: center;
}

.order-list .order-bts button {
	margin: 0;
	padding: 0 30rpx;
	height: 60rpx;
	line-height: 60rpx;
	margin-left: 20rpx;
	border-radius: 30rpx;
	font-size: 24rpx;
	border: 1px solid #cccccc;
	white-space: nowrap;
	background: #ffffff;
}

.order-list .order-bts button::after {
	display: none;
}

.order-list .order-bts button.btn-border-red {
	border: 1px solid $dominant-color;
	font-size: 24rpx;
	color: $dominant-color;
}

.order-list .order-bts button.btn-red {
	background: $dominant-color;
	border: 1px solid $dominant-color;
	font-size: 24rpx;
	color: #ffffff;
}

.buy-checkout {
	width: 100%;
}

.buy-checkout .item {
	min-height: 50rpx;
	line-height: 50rpx;
	padding: 20rpx;
	display: flex;
	justify-content: space-between;
	font-size: 28rpx;
}

.buy-checkout .iconfont.icon-weixin {
	color: #04be01;
	font-size: 50rpx;
}

.buy-checkout .iconfont.icon-yue {
	color: #f0de7c;
	font-size: 50rpx;
}

.buy-checkout .item.active .iconfont.icon-xuanze {
	color: #04be01;
}
.giftlist_item_num{
	color: $dominant-color;
	font-size: 28rpx;
	text-align: center;
}
.giftlist_item_type{
	text-align: center;
	color: #000000;
	font-size: 30rpx;
}
.f20{
	font-size: 20rpx;
}
</style>
