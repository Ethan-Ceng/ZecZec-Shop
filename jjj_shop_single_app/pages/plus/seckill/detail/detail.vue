<!-- 
	暂弃
	已移至/pages/product/detail/detail 
-->
<template>
	<view class="product-detail" :data-theme="theme()" :class="theme() || ''">
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" v-if="!loadding">
			<!--图片-->
			<view class="product-pic">
				<swiper class="swiper" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
					<swiper-item v-for="(item, index) in detail.product.image" :key="index"><image :src="item.file_path" mode=""></image></swiper-item>
				</swiper>
			</view>

			<!--限时秒杀-->
			<view class="limited-spike d-b-c m-0-20 mt20">
				<text class="left-name">限时秒杀</text>
				<view class="right d-s-c"><Countdown ref="countdown" :config="countdownConfig" @returnVal="returnValFunc"></Countdown></view>
			</view>

			<!--基本信息-->
			<view class="bg-white  m-0-20 mb20 p30 bottom-radius">
				<view class="price-wrap mb16 pr">
					<view class="left">
						<view class="new-price">
							<text class="f24 redF6">￥</text>
							<text class="num">{{ detail.seckill_price }}</text>
						</view>
						<text class="old-price">{{ '￥' + detail.line_price }}</text>
					</view>
					<view class="share-box">
						<button @click="showShare" open-type="share"><image class="share_img" src="/static/icon/fenxiang.png" mode=""></image></button>
					</view>
				</view>
				<text class="already-sale">已出售{{ detail.product_sales }}件</text>
				<view class="product-name">{{ detail.product.product_name }}</view>
				<view class="product-describe">{{ detail.product.selling_point }}</view>
			</view>
			<!--详情内容-->
			<view class="product-content">
				<view class="group-hd border-b-e">
					<view class="d-s-c">
						<view class="pro_nameline"></view>
						<text class="min-name  f32 fb">商品介绍</text>
					</view>
				</view>
				<view class="content-box" v-html="detail.product.content"></view>
			</view>
		</scroll-view>

		<!--底部按钮-->
		<view class="btns-wrap">
			<view class="customer-service d-c-c">
				<view class="icon-box d-c-c" @click="gotoPage('/pages/index/index')">
					<button class="d-c-c d-c bg-white">
						<text class="btn_btom pr icon iconfont icon-shouye gray3" style="height: 50rpx;line-height: 60rpx;"></text>
						<text class="f22 gray3" style="height: 50rpx;line-height: 40rpx;">首页</text>
					</button>
				</view>
				<!-- #ifdef MP-WEIXIN -->
				<view class="icon-box">
					<button class="d-c-c d-c bg-white" open-type="contact" session-from="wxapp" show-message-card="true">
						<text class="icon iconfont icon-kefu gray3" style="height: 50rpx;line-height: 60rpx;"></text>
						<text class="f22 gray3" style="height: 50rpx;line-height: 40rpx;">客服</text>
					</button>
				</view>
				<!-- #endif -->
				<!-- #ifndef MP-WEIXIN -->
				<view class="icon-box" @click="openMaservice">
					<button class="d-c-c d-c bg-white">
						<text class="icon iconfont icon-kefu gray3" style="height: 50rpx;line-height: 60rpx;"></text>
						<text class="f22 gray3" style="height: 50rpx;line-height: 40rpx;">客服</text>
					</button>
				</view>
				<!-- #endif -->
			</view>
			<button type="primary" v-if="status == 0" class="buy" @click="openPopup('order')">立即抢购</button>
			<button type="primary" v-if="status == 1" class="btn-gray">未开始</button>
			<button type="primary" v-if="status == 2" class="btn-gray">已结束</button>
			<button type="primary" v-if="detail.stock == 0" class="btn-gray">已抢光</button>
		</view>

		<!--购物确定-->
		<spec :isPopup="isPopup" :productModel="productModel" @close="closePopup"></spec>
		<!--底部弹窗-->
		<share :isMpShare="isMpShare" @close="closeBottmpanel"></share>
		<!--app分享-->
		<AppShare :isAppShare="isAppShare" :appParams="appParams" @close="closeAppShare"></AppShare>
		<!--客服-->
		<Mpservice v-if="isMpservice" :isMpservice="isMpservice" @close="closeMpservice"></Mpservice>
	</view>
</template>

<script>
import Spec from './popup/Spec.vue';
import Countdown from '@/components/countdown/countdown.vue';
import Mpservice from '@/components/mpservice/Mpservice.vue';
import utils from '@/common/utils.js';
import share from '@/components/mp-share.vue';
import AppShare from '@/components/app-share.vue';
export default {
	components: {
		/*选择规格*/
		Spec,
		/*倒计时组件*/
		Countdown,
		/*客服*/
		Mpservice,
		share,
		AppShare
	},
	data() {
		return {
			/*手机高度*/
			phoneHeight: 0,
			/*可滚动视图区域高度*/
			scrollviewHigh: 0,
			/*是否加载完成*/
			loadding: true,
			/*是否显示面板指示点*/
			indicatorDots: true,
			/*是否自动切换*/
			autoplay: true,
			/*自动切换时间间隔*/
			interval: 2000,
			/*滑动动画时长*/
			duration: 500,
			/*秒杀商品id*/
			seckill_product_id: null,
			/*详情*/
			detail: {
				/*商品规格*/
				product_sku: {},
				/*当前规格对象*/
				show_sku: {
					/*秒杀价格*/
					seckill_price: '',
					/*商品规格ID*/
					product_sku_id: 0,
					/*划线价格*/
					line_price: '',
					/*库存数量*/
					stock_num: 0,
					/*商品规格图片*/
					sku_image: '',
					/*秒杀商品规格ID*/
					seckill_product_sku_id: 0
				},
				/*暂不知晓*/
				show_point_sku: {}
			},
			/*是否确定购买弹窗*/
			isPopup: false,
			/*倒计时配置*/
			countdownConfig: {
				/*开始时间*/
				startstamp: 0,
				/*结束时间*/
				endstamp: 0
			},
			/*商品属性*/
			specData: null,
			/*子级页面传参*/
			productModel: {},
			/*商品规格*/
			productSku: [],
			/*是否打开客服*/
			isMpservice: false,
			/*分享*/
			isMpShare: false,
			/*app分享*/
			isAppShare: false,
			appParams: {
				title: '',
				summary: '',
				path: ''
			},
			url: '',
			status: -1
		};
	},
	onLoad(e) {
		/*分类id*/
		this.seckill_product_id = e.seckill_product_id;
		//#ifdef H5
		this.url = window.location.href;
		//#endif
	},
	mounted() {
		this.init();
		/*获取产品详情*/
		this.getData();
	},
	/*分享*/
	onShareAppMessage() {
		let self = this;
		// 构建页面参数
		let params = self.getShareUrlParams({
			seckill_product_id: self.seckill_product_id
		});
		return {
			title: self.detail.product.product_name,
			path: '/pages/plus/seckill/detail/detail?' + params
		};
	},
	methods: {
		/*初始化*/
		init() {
			let _this = this;
			uni.getSystemInfo({
				success(res) {
					_this.phoneHeight = res.windowHeight;
					// 计算组件的高度
					let view = uni.createSelectorQuery().select('.btns-wrap');
					view.boundingClientRect(data => {
						let h = _this.phoneHeight - data.height;
						_this.scrollviewHigh = h;
					}).exec();
				}
			});
		},

		/*获取数据*/
		getData() {
			let self = this;
			uni.showLoading({
				title: '加载中'
			});
			self._get(
				'plus.seckill.product/detail',
				{
					seckill_product_id: self.seckill_product_id,
					url: self.url
				},
				function(res) {
					/*倒计时*/
					self.countdownConfig.endstamp = res.data.active.end_time;
					self.countdownConfig.startstamp = res.data.active.start_time;

					/*详情内容格式化*/
					res.data.detail.product.content = utils.format_content(res.data.detail.product.content);

					/*初始化商品多规格*/
					if (res.data.detail.product.spec_type == 20) {
						self.initSpecData(res.data.detail.seckillSku, res.data.specData);
					}
					self.detail = res.data.detail;
					// 配置微信分享参数
					//#ifdef H5
					if (self.url != '') {
						let params = {
							seckill_product_id: self.seckill_product_id
						};
						self.configWx(res.data.share.signPackage, res.data.share.shareParams, params);
					}
					//#endif
					self.loadding = false;
					uni.hideLoading();
				}
			);
		},

		/*多规格商品*/
		initSpecData(list, data) {
			for (let i = 0; i < list.length; i++) {
				let item = list[i];
				if (item.productSku) {
					let arr = item.productSku.spec_sku_id.split('_').map(Number);
					this.productSku.push(arr);
				}
			}
			for (let i in data.spec_attr) {
				for (let j = 0; j < data.spec_attr[i].spec_items.length; j++) {
					let item = data.spec_attr[i].spec_items[j];
					if (this.hasSpec(item.item_id, i)) {
						item.checked = false;
						item.disabled = false;
					} else {
						data.spec_attr[i].spec_items.splice(j, 1);
						j--;
					}
				}
			}
			this.specData = data;
		},

		/*判断有没有规格*/
		hasSpec(id, _index) {
			let flag = false;
			for (let i = 0; i < this.productSku.length; i++) {
				let arr = this.productSku[i];
				if (arr[_index] == id) {
					flag = true;
					break;
				}
			}
			return flag;
		},

		/*打开窗口*/
		openPopup(e) {
			let obj = {
				specData: this.specData,
				detail: this.detail,
				productSpecArr: this.specData != null ? new Array(this.specData.spec_attr.length) : [],
				show_sku: {
					sku_image: '',
					seckill_price: 0,
					product_sku_id: 0,
					line_price: 0,
					seckill_stock: 0,
					seckill_product_sku_id: 0,
					sum: 1
				},
				productSku: this.productSku,
				type: e
			};
			this.productModel = obj;
			this.isPopup = true;
		},

		/*关闭弹窗*/
		closePopup() {
			this.isPopup = false;
		},

		/*打开客服*/
		openMaservice() {
			this.isMpservice = true;
		},

		/*关闭客服*/
		closeMpservice() {
			this.isMpservice = false;
		},
		//分享按钮
		showShare() {
			let self = this;

			//#ifndef APP-PLUS
			self.isMpShare = true;
			//#endif
			//#ifdef APP-PLUS
			self.appParams.title = self.detail.product.product_name;
			self.appParams.summary = self.detail.product.product_name;
			// 构建页面参数
			let params = self.getShareUrlParams({
				seckill_product_id: self.seckill_product_id
			});
			self.appParams.path = '/pages/plus/seckill/detail/detail?' + params;
			self.appParams.image = self.detail.product.image[0].file_path;
			self.isAppShare = true;
			//#endif
			//#ifdef MP-WEIXIN
			return;
			//#endif
		},
		//关闭分享
		closeBottmpanel(data) {
			this.isMpShare = false;
		},
		//关闭分享
		closeAppShare(data) {
			this.isAppShare = false;
		},
		/*倒计时返回值*/
		returnValFunc(e) {
			this.status = e;
			// 如果原来未开始变成已开始
		}
	}
};
</script>

<style lang="scss">
.product-detail {
	padding-bottom: 90rpx;
}

.product-detail .product-pic,
.product-detail .product-pic .swiper,
.product-detail .product-pic image {
	width: 750rpx;
	height: 750rpx;
}

.product-detail .price-wrap {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.product-detail .price-wrap .left {
	display: flex;
	justify-content: flex-start;
	align-items: flex-end;
}

.product-detail .price-wrap .new-price {
	color: $dominant-color;
	font-size: 30rpx;
	font-weight: bold;
}

.product-detail .price-wrap .new-price .num {
	padding: 0 4rpx;
	font-size: 40rpx;
}

.product-detail .price-wrap .old-price {
	margin-left: 10rpx;
	font-size: 24rpx;
	color: #999999;
	text-decoration: line-through;
}

.product-detail .already-sale {
	font-size: 24rpx;
	color: #999999;
}

.product-detail .product-name {
	padding-top: 26rpx;
	font-size: 32rpx;
	font-weight: bold;
	color: #333333;
}

.product-detail .product-describe {
	padding: 18rpx;
	line-height: 40rpx;
	font-size: 26rpx;
	color: #666666;
	background-color: #f2f2f2;
	border-radius: 12rpx;
	word-break: break-all;
	margin-top: 28rpx;
}

.product-comment,
.product-content {
	margin-top: 20rpx;
	background: #ffffff;
}

.product-content .content-box p image {
	width: 100%;
}

.product-content .content-box {
	font-size: 36rpx;
}

.btns-wrap {
	position: fixed;
	height: 100rpx;
	right: 0;
	bottom: 0;
	left: 0;
	display: flex;
	align-items: center;
	background: #ffffff;
}

.btns-wrap .icon-box {
	width: 100rpx;
	height: 100rpx;
}

.btns-wrap .icon-box .iconfont {
	font-size: 40rpx;
	color: #888888;
}

.btns-wrap .icon-box .iconfont .num {
	position: absolute;
	padding: 0 5rpx;
	top: 10rpx;
	left: 50%;
	height: 30rpx;
	line-height: 30rpx;
	border-radius: 15rpx;
	font-size: 20rpx;
	color: #ffffff;
	background: red;
}

.btns-wrap button,
.btns-wrap button:after {
	height: 80rpx;
	line-height: 80rpx;
	margin: 0;
	padding: 0;
	flex: 1;
	border-radius: 40rpx;
	border: 0;
}

.btns-wrap button.add-cart {
	background: $orange-color;
}

.btns-wrap button.buy {
	background: linear-gradient(270deg, #d42716 0%, #dd4746 100%);
}

.share-box {
	position: absolute;
	width: 60rpx;
	height: 60rpx;
	right: 0;
	bottom: -16rpx;
	display: flex;
	justify-content: center;
	align-items: center;
}

.share-box button {
	padding: 0;
	background: 0;
	line-height: 60rpx;
	border-radius: 0;
}

.share-box .iconfont {
	margin-bottom: 10rpx;
	font-size: 50rpx;
	color: #ffffff;
}

.share_img {
	width: 30rpx;
	height: 30rpx;
	margin: 0 auto;
	margin-bottom: 4rpx;
}

.share_text {
	line-height: 34rpx;
}

.create-img {
	width: 100%;
	padding: 20rpx;
	box-sizing: border-box;
}

.create-img image {
	width: 100%;
}

.create-img button {
	width: 100%;
}

.product-detail .limited-spike {
	padding: 0 35rpx;
	height: 90rpx;
	color: #ffffff;
	border-radius: 12rpx 12rpx 0 0;
	background: linear-gradient(-90deg, #cb2bff 0%, #7727e7 98%);
	// background: linear-gradient(to bottom, #ff6c65, #e2231a);
}

.product-detail .limited-spike .left-name {
	font-size: 32rpx;
	color: #ffffff;
}

.product-detail .limited-spike .right::v-deep text {
	color: #ffffff;
	font-size: 28rpx;
}

.product-detail .limited-spike .right::v-deep .box {
	height: 40rpx;
	padding: 4rpx;
	border-radius: 8rpx;
	line-height: 40rpx;
	text-align: center;
	background: #ffffff;
	color: #ff0001;
}

.product-detail .limited-spike .right::v-deep > text {
	margin-left: 10rpx;
}

.already-choice {
	margin-top: 20rpx;
	padding: 0 30rpx;
	background: #ffffff;
}

.already-choice .center-content {
	line-height: 90rpx;
}

.shop_head_info {
	margin: 20rpx;
	padding: 30rpx;
	box-sizing: border-box;
	background-color: white;
	border-radius: 12rpx;
}

.shop_list_body_item_shop {
	width: 100%;
	height: 120rpx;
	display: flex;
	justify-content: space-between;
}

.shop_list_body_item_shop_logo {
	width: 120rpx;
	height: 120rpx;
}

.shop_list_body_item_shop_logo image {
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.1);
	border-radius: 12rpx;
}

.shop_list_body_item_shop_info {
	box-sizing: border-box;
	margin-left: 20rpx;
	padding-top: 0;
	display: flex;
	justify-content: space-between;
	flex-direction: column;
}

.shop_list_body_item_shop_others {
	box-sizing: border-box;
	display: flex;
	justify-content: space-between;
	flex-direction: column;
	text-align: right;
	padding-top: 0;
}

.shop_list_body_item_shop_others button {
	width: 160rpx;
	height: 60rpx;
	border: 1rpx solid #f6220c;
	border-radius: 30rpx;
	line-height: 60rpx;
	font-size: 26rpx;
	font-family: PingFang SC;
	font-weight: 500;
	color: #f6220c;
	text-align: center;
	padding: 0;
	background-color: #ffffff;
}

.bottom-radius {
	border-bottom-left-radius: 12rpx;
	border-bottom-right-radius: 12rpx;
}

.pro_nameline {
	width: 4rpx;
	height: 24rpx;
	background-color: #f6220c;
	margin-right: 12rpx;
}
</style>
