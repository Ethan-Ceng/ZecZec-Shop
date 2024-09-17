<template>
	<view class="gift-package" v-if="!loadding">
		<view class="rule">
			<button class="rule_btn" @click="ReLaunch()">返回首页</button>
			<!-- <button class="rule_btn">活动规则</button> -->
		</view>
		<image v-if="!loadding&&detail.file_path!=''" class="gift-bg" :src="detail.file_path" mode="widthFix"></image>
		<view class="gift-package-main">
			<view class="p-0-30 pack-item" v-if="detail.is_point">
				<view class="integral">
					<view class="title-b">
						{{points_name()}}礼包
					</view>
					<view class="integral_btom  d-c-c">
						<image style="width: 62rpx;height: 72rpx;" src="/static/gift.png" mode="aspectFill"></image>
						<view class="info flex-1">
							<view class="f28" style="color: #FF5649;">
								{{parseFloat(detail.point)}}{{points_name()}}
							</view>
							<view class="f22 gray9">无门槛 全品类</view>
						</view>
						<view class="integral_btn">立即领取</view>
					</view>
				</view>
			</view>
			<view class="t-c" v-if="detail.is_point">
				<text class="add">+</text>
			</view>
			<view class="p-0-30 pack-item" v-if="detail.is_coupon">
				<view class="cuopon-group">
					<view class="title-b">
						优惠券礼包
					</view>
					<view class="body">
						<view class="item" v-for="(item,index) in detail.coupon_list" :key="index">
							<view class="cuopon_item">
								<view>
									<image class="cuopon_img" src="/static/youhuiquan.png" mode="aspectFill"></image>
								</view>
								<view class="d-s-c">
									<view class="d-s-c item_cuopon">
										<view class="price">
											<view class="">
												<template v-if="item.coupon_type.value==10">
													<text class="f22">￥</text><text class="f34">{{parseFloat(item.reduce_price)}}</text>
												</template>
												<template v-else>
													<text class="f34">{{item.discount}}折</text>
												</template>
											</view>
											<view class="f22">优惠券</view>
										</view>
										<view class="des">
											<view class="des_t">{{item.name}}</view>
											<view class="des_b" v-if="item.expire_type==20">有效期至{{item.end_time.text}}
											</view>
											<view class="des_b" v-if="item.expire_type==10">领取后{{item.expire_day}}天内有效
											</view>
										</view>
									</view>
									<view class="cuopon_num">X{{item.coupon_num}}张</view>
								</view>
							</view>

						</view>
					</view>
				</view>
			</view>
			<view class="t-c" v-if="detail.is_product&&detail.is_coupon">
				<text class="add">+</text>
			</view>
			<view class="p-0-30 pack-item" v-if="detail.is_product">
				<view class="cuopon-group">
					<view class="title-b">
						<view>商品礼包<text>({{detail.product_list.length}}选{{detail.product_num}})</text></view>
					</view>
					<view class="body">
						<view class="item mb30 bg-white" v-for="(item,index) in detail.product_list" :key="index"
							@click="choosePro(item.product_id)">
							<view class="d-s-s">
								<view class="mr10">
									<image class="product_img" :src="item.image[0].file_path" mode="aspectFill"></image>
								</view>
								<view class="pro">
									<view class="pro_t text-ellipsis-2">{{item.product_name}}</view>
									<view class="pro_b"><text class="f18">￥</text>{{item.product_price}}</view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="btns">
				<button @click="toPay()">{{detail.money}}元购买</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		pay
	} from '@/common/pay.js';
	import utils from '@/common/utils.js';
	export default {
		data() {
			return {
				/*是否加载完成*/
				loadding: false,
				package_id: 0,
				code: 0,
				/*礼包详情*/
				detail: {
					name: '',
					start_time: {
						text: ''
					},
					end_time: {
						text: ''
					},
					is_point: false,
					point: 0,
					money: '',
					file_path:''
				},
			}
		},
		onLoad(e) {
			/*商品id*/
			let scene = utils.getSceneData(e);
			this.package_id = e.package_id ? e.package_id : scene.pid;
			this.code = e.code ? e.code : scene.cid;
			if (typeof(this.code) == "undefined") {
				this.code = '';
			}
		},
		onShow() {
			/*获取数据*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				
				self._get('plus.package.package/index', {
					package_id: self.package_id,
					code: self.code
				}, function(res) {
					self.detail = res.data.data;
					self.loadding = false;
				});
			},
			/* 返回首页 */
			ReLaunch() {
				this.gotoPage('/pages/index/index', 'reLaunch');
			},
			toPay() {
				this.gotoPage('/pages/order/giftpackage-order?package_id=' + this.package_id + '&code=' + this.code);
			}
		}
	}
</script>

<style lang="scss">
	page {
		background: #FF5649;
	}

	.gift-package {
		position: relative;
		min-height: 100vh;
	}

	.gift-package .gift-bg {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		width: 100%;
		z-index: 0;
	}

	.gift-package .gift-package-main {
		width: 750rpx;
		padding-top: 700rpx;
		padding-bottom: 120rpx;
		position: relative;
		z-index: 1;
		box-sizing: border-box;
		// background: #FF5649;
	}

	.detatime {
		/* padding:10px 30px; */
		font-size: 18rpx;
		color: #FF5649;
		font-weight: 800;
	}

	/* .gift-package-main .datetime {
		margin: 0 auto;
		width: 600rpx;
		height: 60rpx;
		line-height: 60rpx;
		text-align: center;
	} */

	.cuopon-group {
		color: #ffffff;
		text-align: center;
		font-size: 30rpx;
		background-color: #FF6F64;
		border-radius: 20rpx;
		padding: 43rpx 28rpx 47rpx 28rpx;
		position: relative;
		z-index: 98;
	}

	.integral::after {
		content: '';
		position: absolute;
		left: 257rpx;
		top: -20rpx;
		width: 9rpx;
		height: 37rpx;
		background: #FFFFFF;
		box-shadow: 0rpx 8rpx 3rpx 0rpx rgba(6, 0, 1, 0.03);
		border-radius: 4rpx;
		z-index: 99;
	}

	.cuopon-group::after {
		content: '';
		position: absolute;
		left: 257rpx;
		top: -20rpx;
		width: 9rpx;
		height: 37rpx;
		background: #FFFFFF;
		box-shadow: 0rpx 8rpx 3rpx 0rpx rgba(6, 0, 1, 0.03);
		border-radius: 4rpx;
		z-index: 99;
	}

	.integral::before {
		content: '';
		position: absolute;
		right: 257rpx;
		top: -20rpx;
		width: 9rpx;
		height: 37rpx;
		background: #FFFFFF;
		box-shadow: 0rpx 8rpx 3rpx 0rpx rgba(6, 0, 1, 0.03);
		border-radius: 4rpx;
		z-index: 99;
	}

	.cuopon-group::before {
		content: '';
		position: absolute;
		right: 257rpx;
		top: -20rpx;
		width: 9rpx;
		height: 37rpx;
		background: #FFFFFF;
		box-shadow: 0rpx 8rpx 3rpx 0rpx rgba(6, 0, 1, 0.03);
		border-radius: 4rpx;
		z-index: 99;
	}

	.cuopon-group .title {
		font-size: 26rpx;
		color: #FFFFFF;
		text-align: center;
	}

	.cuopon-group .body {
		margin-top: 30rpx;
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

	.cuopon-group .body .item:last-child {
		margin-bottom: 0;
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
		font-size: 14rpx;
		margin-left: 26rpx;
	}

	.cuopon-group .body .item .des {
		z-index: 50;
		padding: 26rpx 0;
		font-size: 24rpx;
		text-align: left;
		margin-left: 50rpx;
	}

	.item .des .des_t {
		font-size: 26rpx;
		color: #000000;
		margin-bottom: 12rpx;
	}

	.item .des .des_c {
		font-size: 18rpx;
		color: #6b6b6b;
		margin-bottom: 8rpx;
	}

	.item .des .des_b {
		font-size: 18rpx;
		color: #6b6b6b;
	}

	.t-c {
		text-align: center;
	}

	.gift-package-main .integral {
		background-color: #FF6F64;
		padding: 43rpx 28rpx 47rpx 28rpx;
		border-radius: 20rpx;
		position: relative;
		z-index: 98;
	}

	.gift-package-main .integral .title {
		font-size: 26rpx;
		color: #FFFFFF;
		text-align: center;
	}

	.gift-package-main .integral_btom {
		height: 193rpx;
		background-color: #ffffff;
		margin-top: 20rpx;
		border-radius: 15rpx;
		padding: 0 27rpx 0 29rpx;
		box-sizing: border-box;
	}

	.integral_btn {
		width: 126rpx;
		height: 44rpx;
		background: linear-gradient(0deg, #FF7220 0%, #FEA712 100%);
		border-radius: 22rpx;
		font-size: 22rpx;
		color: #ffffff;
		text-align: center;
		line-height: 44rpx;
	}

	.gift-package-main .integral .info {
		margin-left: 30rpx;
		width: 300rpx;
		color: #f0510e;
	}

	.gift-package-main .integral image {
		width: 135rpx;
		height: 135rpx;
	}

	.gift-package-main .integral .num {
		font-size: 50rpx;
	}

	.gift-package-main .btns {
		margin-top: 60rpx;
		padding: 0 42rpx 0 45rpx;

	}

	.gift-package-main .btns button {
		height: 92rpx;
		line-height: 92rpx;
		border-radius: 46rpx;
		color: #ffffff;
		font-size: 36rpx;
		background: linear-gradient(0deg, #FF7220 0%, #FEA712 100%);
	}

	.rule {
		overflow: hidden;
		position: fixed;
		right: 0;
		top: 57rpx;
		z-index: 100;
	}

	.rule_btn {
		width: 118rpx;
		height: 44rpx;
		line-height: 44rpx;
		text-align: center;
		background: linear-gradient(0deg, #FF7220 0%, #FEA712 100%);
		box-sizing: border-box;
		border-radius: 22rpx 0 0 22rpx;
		color: #ffffff;
		font-size: 22rpx;
		padding: 0;
	}

	.add {
		font-size: 62rpx;
		font-weight: 800;
		text-align: center;
		color: #FFFFFF;
		position: relative;
		z-index: 98;
		text-shadow: 0px 8rpx 3rpx rgba(6, 0, 1, 0.03);
	}

	.cuopon_num {
		position: absolute;
		right: 0;
	}

	.product_img {
		width: 92rpx;
		height: 92rpx;
		border-radius: 10rpx;
	}

	.pro {
		margin-left: 12rpx;
		text-align: left;
	}

	.pro_t {
		font-size: 22rpx;
		color: #333333;
		line-height: 1.5;
		margin-bottom: 10rpx;
	}

	.pro_c {
		font-size: 20rpx;
		color: #686868;
		margin-bottom: 33rpx;
	}

	.pro_b {
		font-size: 24rpx;
		font-weight: 800;
		color: #FF5649;
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
	.title-b{
		color: #ffffff;
		text-align: center;
		font-size: 36rpx;
		font-weight: bold;
	}
</style>
