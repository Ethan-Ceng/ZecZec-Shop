<template>
	<view class="order-datail pb100" v-if="!loadding">
		<!--购物列表-->
		<view class="shops group bg-white">
			<view class="group-hd border-b-e">
				<view class="left"><text class="min-name">商品</text></view>
			</view>
			<view class="list">
				<view class="gift-package-main">
					<view class="p-0-30">
						<view class="integral" v-if="detail.point != 0">
							<view class="title">{{ points_name() }}礼包</view>
							<view class="integral_btom  d-s-c">
								<image src="/static/gift.png" mode="widthFix"></image>
								<view class="info">
									<view class="num">{{ parseFloat(detail.point) }}{{ points_name() }}</view>
									<view>无门槛 全品类</view>
								</view>
							</view>
						</view>
					</view>
					<view class="p-0-30" v-if="detail.coupon_ids != ''">
						<view class="cuopon-title">优惠券礼包</view>
						<view class="cuopon-group borbox" v-if="detail.coupon_list != ''">
							<view class="body">
								<view class="item" v-for="(item, index) in detail.coupon_list" :key="index">
									<view class="cuopon_item">
										<view><image class="cuopon_img" src="../../../static/youhuiquan2.png" mode=""></image></view>
										<view class="d-s-c">
											<view class="d-s-c item_cuopon">
												<view class="price">
													<view class="">
														<text class="f22">￥</text>
														<text class="f34">{{ parseFloat(item.reduce_price) }}</text>
													</view>
													<view class="f22">优惠券</view>
												</view>
												<view class="des">
													<view class="des_t">{{ item.name }}</view>
													<view class="des_b" v-if="item.expire_type == 20">有效期至{{ item.end_time.text }}</view>
													<view class="des_b" v-if="item.expire_type == 10">领取后{{ item.expire_day }}天内有效</view>
												</view>
											</view>
										</view>
										<view class="cuopon_num">
											<text class="f12">x</text>
											{{ item.coupon_num }}
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
					<view class="p-0-30" v-if="detail.product_list && detail.product_list.length > 0">
						<view class="cuopon-group">
							<view class="title f30"><view>商品礼包</view></view>
							<view class="body">
								<view class="item mb30 borbox bg-white" v-for="(item, index) in detail.product_list" :key="index" @click="choosePro(item.product_id)">
									<view class="d-s-c">
										<view><image class="product_img" :src="item.image[0].file_path" mode="aspectFill"></image></view>
										<view class="pro">
											<view class="pro_t">{{ item.product_name }}</view>
											<view class="pro_c">{{ item.product_sku.product_attr }}</view>
											<view class="pro_b">
												<text class="f18">￥</text>
												{{ item.product_price }}
											</view>
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>

		<!--订单信息-->
		<view class="group bg-white f28">
			<view class="p-20-0">
				<text class="gray9">订单编号：</text>
				<text>{{ detail.order_no }}</text>
			</view>
			<view class="p-20-0">
				<text class="gray9">下单时间：</text>
				<text>{{ detail.create_time }}</text>
			</view>
			<view class="p-20-0">
				<text class="gray9">支付方式：</text>
				<text>{{ detail.pay_type.text }}</text>
			</view>
			<view class="p-20-0">
				<text class="gray9">配送方式：</text>
				<text v-if="detail.delivery_type == 10">快递配送</text>
				<text v-if="detail.delivery_type == 20">自提上门</text>
			</view>
		</view>

		<view class="group bg-white f28">
			<view class="p-20-0 d-b-c">
				<text class="gray9">订单金额</text>
				<text>¥ {{ detail.order_price }}</text>
			</view>
		</view>
	</view>
</template>

<script>
import Popup from '@/components/uni-popup.vue';
import { pay } from '@/common/pay.js';
export default {
	data() {
		return {
			/*是否加载完成*/
			loadding: true,
			indicatorDots: true,
			autoplay: true,
			interval: 2000,
			duration: 500,
			/*是否显示支付类别弹窗*/
			isPayPopup: false,
			/*订单id*/
			order_id: 0,
			/*订单详情*/
			detail: {
				point: '',
				product_list: [],
				pay_price: '',
				coupon_list: [],
				order_status: [],
				address: {
					region: []
				},
				product: [],
				pay_type: [],
				delivery_type: [],
				pay_status: []
			},
			extractStore: {},
			/*是否显示拼团*/
			is_fightgroup: false
		};
	},
	components: {
		Popup
	},
	onLoad(e) {
		this.order_no = e.order_no;
	},
	mounted() {
		uni.showLoading({
			title: '加载中'
		});
		/*获取订单详情*/
		this.getData();
	},
	methods: {
		/*获取数据*/
		getData() {
			let self = this;
			let order_no = self.order_no;
			self._get(
				'plus.package.Order/orderDetail',
				{
					order_no: order_no
				},
				function(res) {
					self.detail = res.data.detail;
					self.extractStore = res.data.detail.extract;
					self.loadding = false;
					console.log(self.detail);
					uni.hideLoading();
				}
			);
		},
		/*显示支付方式*/
		hidePopupFunc() {
			this.isPayPopup = false;
		},
		/*查看物流*/
		gotoExpress(order_id) {
			this.gotoPage('/pages/order/express/express?order_id=' + order_id);
		}
	}
};
</script>

<style scoped>
.order-express {
	background: #ffffff;
}

.order-express .icon-box .iconfont {
	font-size: 50rpx;
}

.order-datail {
	padding-bottom: 90 rpx;
}
.order-datail .fight-users {
	margin: 0 auto;
}
.order-datail .fight-users .user-box {
	width: 80rpx;
	height: 80rpx;
	margin: 10rpx;
	border-radius: 50%;
	border: 1px dashed #cccccc;
}
.order-datail .fight-users {
	padding: 30rpx;
}
.order-datail .fight-users .user-box image {
	width: 80rpx;
	height: 80rpx;
	border-radius: 50%;
}
.order-datail .fight-users .user-box .leader {
	position: absolute;
	top: -20rpx;
	left: 50%;
	margin-left: -30rpx;
	width: 60rpx;
	height: 30rpx;
	line-height: 30rpx;
	text-align: center;
	color: #ffffff;
	border-radius: 30rpx;
	border: 1px solid #ffffff;
	background: red;
}
.order-datail .fight-users .user-box.user-who {
	font-size: 50rpx;
	color: #999999;
}

.state-cont .countdown-datetime {
	margin-top: 16rpx;
}
.state-cont .countdown-datetime text {
	padding: 4rpx 8rpx;
	border-radius: 4rpx;
	background: rgba(0, 0, 0, 0.4);
}
.gift-package-main .integral {
	background-color: #ffffff66;
	padding-bottom: 60rpx;
	border-radius: 15rpx;
}
.gift-package-main .integral .title {
	color: #919191;
	font-size: 35rpx;
	margin-bottom: 20rpx;
}
.gift-package-main .integral_btom {
	border: 1rpx solid #cacaca;
	height: 193rpx;
	background-color: #ffffff;
	margin-top: 20rpx;
	border-radius: 15rpx;
}

.gift-package-main .integral .info {
	margin-left: 30rpx;
	width: 300rpx;
	color: #f0510e;
}

.gift-package-main .integral image {
	width: 135rpx;
	height: 135rpx;
	margin-left: 40rpx;
}

.gift-package-main .integral .num {
	font-size: 50rpx;
}

.gift-package-main .btns {
	margin-top: 92rpx;
	margin-bottom: 120rpx;
	float: right;
}
.gift-package-main .order_price {
	text-align: right;
	color: #fd0000;
	font-size: 30rpx;
	margin-bottom: 68rpx;
}
.gift-package-main .btns button {
	width: 266rpx;
	height: 54rpx;
	background-color: #ee1413;
	color: #ffffff;
	text-align: center;
	line-height: 54rpx;
	border-radius: 15rpx;
}
.rule {
	overflow: hidden;
	position: absolute;
	right: 0;
	top: 480rpx;
	z-index: 100;
}
.rule_btn {
	margin-top: 27rpx;
	width: 183rpx;
	height: 62rpx;
	line-height: 62rpx;
	text-align: center;
	background-color: #e83514;
	border: 1rpx solid #ffffff;
	border-top-left-radius: 32rpx;
	border-bottom-left-radius: 32rpx;
	color: #ffffff;
	font-size: 26rpx;
}
.add {
	font-size: 73rpx;
	font-weight: 900;
	text-align: center;
	color: #ffffff;
}
.cuopon_num {
	display: inline-block;
	width: 40rpx;
	height: 40rpx;
	border: 1rpx solid #939393;
	margin-left: 150rpx;
	text-align: center;
	line-height: 40rpx;
	font-size: 18rpx;
	border-radius: 10rpx;
	position: absolute;
	top: 40px;
	right: 15px;
	color: #000000;
}
.product_img {
	width: 140rpx;
	height: 140rpx;
}
.pro {
	margin-left: 12rpx;
	text-align: left;
}
.pro_t {
	font-size: 24rpx;
	color: #000000;
	margin-bottom: 15rpx;
}
.pro_c {
	font-size: 20rpx;
	color: #686868;
}
.pro_b {
	font-size: 28rpx;
	color: #fd0000;
	margin-left: 252rpx;
}
.f18 {
	font-size: 18rpx;
}
.pro_choose {
	width: 33rpx;
	height: 33rpx;
	position: absolute;
	right: 10rpx;
	top: 6rpx;
}
.borbox {
	border: 1rpx solid #cacaca;
}
.cuopon-group {
	color: #ffffff;
	text-align: center;
	font-size: 30rpx;
	background-color: #ffffff30;
	border-radius: 15rpx;
	padding: 35rpx 0;
}
.cuopon-title {
	color: #919191;
	font-size: 35rpx;
	margin-bottom: 20rpx;
}
.cuopon-group .title {
	color: #919191;
	font-size: 35rpx;
	margin-bottom: 20rpx;
}
.cuopon-group .body {
}
.cuopon_item {
	position: relative;
}
.item_cuopon {
	z-index: 50;
}
.cuopon-group .body .item {
	padding: 20rpx;
	display: flex;
	flex-direction: column;
	border-radius: 20rpx;
	position: relative;
}
.cuopon_img {
	width: 493rpx;
	height: 123rpx;
	position: absolute;
	top: 0;
	left: 0;
}
.cuopon-group .body .item .price {
	z-index: 50;
	font-size: 18rpx;
	margin-left: 26rpx;
}

.cuopon-group .body .item .des {
	color: #000000;
	z-index: 50;
	padding: 26rpx 0;
	font-size: 24rpx;
	text-align: left;
	margin-left: 50rpx;
}
</style>
