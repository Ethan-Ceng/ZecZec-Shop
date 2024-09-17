<template>
	<view class="bargain-detail">
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" v-if="!loadding">
			<!--图片-->
			<view class="product-pic">
				<swiper class="swiper" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
					<swiper-item v-for="(item, index) in detail.product.image" :key="index">
						<image :src="item.file_path" mode=""></image>
					</swiper-item>
				</swiper>
			</view>
			<!--基本信息-->
			<view class="bg-white m20 p30 br12">
				<view class="price-wrap pr">
					<view class="left">
						<view class="new-price">
							<text class="f24 redF6">￥</text>
							<text class="num">{{ detail.bargain_price }}</text>
						</view>
						<text class="old-price">¥{{ detail.line_price }}</text>
					</view>
					<view class="share-box">
						<button @click="showShare" open-type="share">
							<image class="share_img" src="/static/icon/fenxiang.png" mode=""></image>
						</button>
					</view>
				</view>
				<view class="product-name">{{ detail.product.product_name }}</view>
				<!--砍价进行中-->
				<view class="">
					<view class="limited-spike d-b-c">
						<text class="left-name"><text class="already-sale">已有{{ detail.product_sales }}人砍价成功</text></text>
						<view class="right">
							<countdown ref="countdown" :config="countdownConfig" @returnVal="returnValFunc"></countdown>
						</view>
					</view>
				</view>
				<!--邀请好友-->
				<view class="d-b-c f24 gray3 mt20" @click="openRule">
					<text class="f24 gray3">邀请朋友帮忙砍价，超低价购买心仪之物</text>
					<view>
						<text>玩法详情</text><text class="icon iconfont icon-you" style="color: #333333;font-size:25rpx;"></text>
					</view>
				</view>
			</view>
			<!--详情内容-->
			<view class="product-content">
				<view class="group-hd border-b-e">
					<view class="d-s-c">
						<view class="pro_nameline"></view><text class="min-name  f32 fb">商品介绍</text>
					</view>
				</view>
				<view v-if="detail.product.is_picture == 0" class="content-box" v-html="detail.product.content"></view>
				<view v-if="detail.product.is_picture == 1" class="content-box">
					<view class="ww100" v-for="(item, index) in detail.product.contentImage" :key="index"><image class="ww100" :src="item.file_path" mode="widthFix"></image></view>
				</view>
			</view>
		</scroll-view>

		<!--底部按钮-->
		<view class="btns-wrap d-s-c d-stretch">
			<template v-if="!loadding">
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
				<view class="buy-alone flex-1 d-c-c d-c" @click="gotoProducntDetail()">
					<text>￥{{ detail.product.product_price }}</text>
					<button type="primary">单独购买</button>
				</view>
				<view class="make-group flex-1 d-c-c d-c" @click="openPopup('order')">
					<text>￥{{ detail.bargain_price }}</text>
					<button type="primary" class="buy" @click="openPopup('order')">砍价购</button>
				</view>
			</template>
		</view>

		<!--购物确定-->
		<spec :isPopup="isPopup" :productModel="productModel" @close="closePopup"></spec>

		<!--规则-->
		<Rule :isRule="isRule" :setting="setting" @close="closeRule"></Rule>
		<!--底部弹窗-->
		<share :isMpShare="isMpShare" @close="closeBottmpanel"></share>
		<!--app分享-->
		<AppShare :isAppShare="isAppShare" :appParams="appParams" @close="closeAppShare"></AppShare>
		<!--客服-->
		<Mpservice v-if="isMpservice" :isMpservice="isMpservice" @close="closeMpservice"></Mpservice>
	</view>
</template>

<script>
	import countdown from '@/components/countdown/countdown.vue';
	import Mpservice from '@/components/mpservice/Mpservice.vue';
	import Spec from './popup/Spec.vue';
	import Rule from './popup/Rule.vue';
	import utils from '@/common/utils.js';
	import share from '@/components/mp-share.vue';
	import AppShare from '@/components/app-share.vue';
	export default {
		components: {
			Spec,
			Rule,
			countdown,
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
				/*是否知道切换*/
				autoplay: true,
				/*自动切换时间间隔*/
				interval: 2000,
				/*滑动动画时长*/
				duration: 500,
				/*倒计时配置*/
				countdownConfig: {
					/*开始时间*/
					startstamp: 0,
					/*结束时间*/
					endstamp: 0
				},
				/*详情*/
				detail: {
					product_sku: {},
					show_sku: {
						product_price: '',
						product_sku_id: 0,
						line_price: '',
						stock_num: 0,
						sku_image: ''
					},
					show_point_sku: {}
				},
				/*活动设置*/
				setting: {},
				/*是否确定购买弹窗*/
				isPopup: false,
				/*商品属性*/
				specData: null,
				/*子级页面传参*/
				productModel: {},
				/*规格数组*/
				productSpecArr: [],
				/*是否显示规则*/
				isRule: false,
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
			};
		},
		onLoad(e) {
			uni.showLoading({
				title: '加载中'
			});
			this.type_active = 0;
			/*分类id*/
			this.bargain_product_id = e.bargain_product_id;
			//#ifdef H5
			this.url = window.location.href;
			//#endif
		},
		/*分享*/
		onShareAppMessage() {
			let self = this;
			// 构建页面参数
			let params = self.getShareUrlParams({
				bargain_product_id: self.bargain_product_id
			});
			return {
				title: self.detail.product.product_name,
				path: '/pages/plus/bargain/detail/detail?' + params
			};
		},
		mounted() {
			this.init();
			/*获取产品详情*/
			this.getData();
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
				let bargain_product_id = self.bargain_product_id;
				self._get(
					'plus.bargain.product/detail', {
						bargain_product_id: bargain_product_id,
						url: self.url
					},
					function(res) {
						self.countdownConfig.startstamp = res.data.active.start_time;
						self.countdownConfig.endstamp = res.data.active.end_time;
						/*详情内容格式化*/
						res.data.detail.product.content = utils.format_content(res.data.detail.product.content);

						/*初始化商品多规格*/
						if (res.data.detail.product.spec_type == 20) {
							self.initSpecData(res.data.detail.bargainSku, res.data.specData);
						}

						self.setting = res.data.setting;
						self.detail = res.data.detail;
						// 配置微信分享参数
						//#ifdef H5
						if (self.url != '') {
							let params = {
								bargain_product_id: self.bargain_product_id
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

			openPopup(e) {
				let obj = {
					specData: this.specData,
					detail: this.detail,
					productSpecArr: this.specData != null ? new Array(this.specData.spec_attr.length) : [],
					show_sku: {
						sku_image: '',
						bargain_price: 0,
						product_sku_id: 0,
						line_price: 0,
						bargain_stock: 0,
						bargain_product_sku_id: 0,
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

			/*跳转商品详情页面*/
			gotoProducntDetail() {
				let url = '/pages/product/detail/detail?product_id=' + this.detail.product_id;
				this.gotoPage(url);
			},

			/*规则*/
			openRule() {
				this.isRule = true;
			},

			/*关闭规则*/
			closeRule() {
				this.isRule = false;
			},

			/*倒计时返回*/
			returnValFunc() {},
			
			//分享按钮
			showShare() {
				let self = this;
				//#ifdef MP-WEIXIN
					return;
				//#endif
				//#ifndef APP-PLUS
				self.isMpShare = true;
				//#endif
				//#ifdef APP-PLUS
				self.appParams.title = self.detail.product.product_name;
				self.appParams.summary = self.detail.product.product_name;
				// 构建页面参数
				let params = self.getShareUrlParams({
					bargain_product_id: self.bargain_product_id
				});
				self.appParams.path = '/pages/plus/bargain/detail/detail?' + params;
				self.appParams.image = self.detail.product.image[0].file_path;
				self.isAppShare = true;
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
		}
	};
</script>

<style lang="scss">
.bargain-detail {
		padding-bottom: 90rpx;
	}

	.bargain-detail .product-pic,
	.bargain-detail .product-pic .swiper,
	.bargain-detail .product-pic image {
		width: 750rpx;
		height: 750rpx;
	}

	.bargain-detail .price-wrap {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.bargain-detail .price-wrap .left {
		display: flex;
		justify-content: flex-start;
		align-items: flex-end;
	}

	.bargain-detail .price-wrap .new-price {
		color: $dominant-color;
		font-size: 30rpx;
	}

	.bargain-detail .price-wrap .new-price .num {
		padding: 0 4rpx;
		font-size: 40rpx;
	}

	.bargain-detail .price-wrap .old-price {
		margin-left: 10rpx;
		font-size: 24rpx;
		color: #999999;
		text-decoration: line-through;
	}

	.bargain-detail .product-name {
		padding: 20rpx 0;
		font-size: 32rpx;
		font-weight: bold;
		color: #333333;
	}

	.bargain-detail .product-describe {
		padding: 20rpx;
		font-size: 24rpx;
		color: #999999;
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
		background: #ffffff;
		align-items: center;
	}

	.btns-wrap .icon-box {
		width: 90rpx;
		height: 90rpx;
	}

	.btns-wrap .icon-box .iconfont {
		font-size: 40rpx;
		color: #888888;
	}

	.btns-wrap .customer-service button {
		height: 80rpx;
	}

	.btns-wrap button,
	.btns-wrap button:after {
		padding: 0;
		margin: 0;
		height: 30rpx;
		line-height: 30rpx;
		margin: 0;
		padding: 0;
		flex: 1;
		border-radius: 0;
		border: 0;
	}

	.btns-wrap .buy-alone {
		width: 220rpx;
		height: 80rpx;
		border-top-left-radius: 40rpx;
		border-bottom-left-radius: 40rpx;
		margin-left: 10rpx;
	}

	.btns-wrap .make-group {
		width: 220rpx;
		height: 80rpx;
		border-top-right-radius: 40rpx;
		border-bottom-right-radius: 40rpx;
		margin-right: 10rpx;
	}

	.btns-wrap .buy-alone button,
	.btns-wrap .make-group button {
		font-size: 26rpx;
		background: none;
	}

	.btns-wrap .buy-alone,
	.btns-wrap .buy-alone button {
		background: #F6AC0C;
	}

	.btns-wrap .buy-alone text,
	.btns-wrap .make-group text {
		color: #ffffff;
		font-size: 32rpx;
	}

	.btns-wrap .make-group,
	.btns-wrap .make-group button {
		background: #8D60FF;

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

	.limited-spike {
		padding: 0 35rpx;
		height: 60rpx;
		color: #ffffff;
		border-radius: 6rpx;
		background: linear-gradient(0deg, #EBE3FF 0%, #EEE6FF 100%);
		// background: linear-gradient(to bottom, #ff6c65, #e2231a);
	}

	.limited-spike .left-name {
		font-size: 24rpx;
		color: #5C1CFF;
	}

	.limited-spike .right {
		color: #5C1CFF;
		font-size: 24rpx;
	}

	.limited-spike .right::v-deep .box {
		height: 40rpx;
		padding: 4rpx;
		border-radius: 8rpx;
		line-height: 40rpx;
		text-align: center;
		background: #EBE3FF;
		color: #5C1CFF;
	}

	.limited-spike .right::v-deep text {
		padding: 0;
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
		border: 1rpx solid #F6220C;
		border-radius: 30rpx;
		line-height: 60rpx;
		font-size: 26rpx;
		font-family: PingFang SC;
		font-weight: 500;
		color: #F6220C;
		text-align: center;
		padding: 0;
		background-color: #ffffff;
	}

	.pro_nameline {
		width: 4rpx;
		height: 24rpx;
		background-color: #f6220c;
		margin-right: 12rpx;
	}
</style>
