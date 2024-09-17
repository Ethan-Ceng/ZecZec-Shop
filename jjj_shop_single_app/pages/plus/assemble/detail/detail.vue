<template>
	<view class="product-detail">
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" v-if="!loadding">
			<!--图片-->
			<view class="product-pic">
				<swiper class="swiper" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
					<swiper-item v-for="(item, index) in detail.product.image" :key="index">
						<image :src="item.file_path" mode=""></image>
					</swiper-item>
				</swiper>
			</view>

			<!--限时拼团-->
			<view class="limited-spike d-b-c m-0-20 mt20">
				<text class="left-name">限时拼团</text>
				<view class="right">
					<Countdown ref='countdown' :config="countdownConfig" @returnVal="returnValFunc"></Countdown>
				</view>
			</view>

			<!--基本信息-->
			<view class="bg-white m-0-20 mb20 p30 bottom-radius">
				<view class="price-wrap pr">
					<view class="left">
						<view class="new-price">
							<text class="f24 redF6">￥</text>
							<text class="num">{{ detail.assemble_price }}</text>
						</view>
						<text class="old-price">{{ '￥'+detail.line_price }}</text>
						<!--分享-->
					</view>
					<view class="share-box">
						<button @click="showShare" open-type="share">
							<image class="share_img" src="/static/icon/fenxiang.png" mode=""></image>
						</button>
					</view>
					
				</view>
				<text class="already-sale">已出售{{ detail.product_sales }}件</text>
				<view class="product-name">{{ detail.product.product_name }}</view>
				<view class="product-describe">{{ detail.product.selling_point }}</view>
			</view>

			<!--拼团用户-->
			<Bill :bill="bill" @openMore="openMoreFunc" @gobill="gobillFunc"></Bill>

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
					<text>￥{{ detail.assemble_price }}</text>
					<button type="primary">立即拼团</button>
				</view>
			</template>
		</view>

		<!--购物确定-->
		<spec :isPopup="isPopup" :productModel="productModel" @close="closePopup"></spec>

		<!--底部弹窗-->
		<share :isMpShare="isMpShare" @close="closeBottmpanel"></share>
		<!--app分享-->
		<AppShare :isAppShare="isAppShare" :appParams="appParams" @close="closeAppShare"></AppShare>
		<!--客服-->
		<Mpservice v-if="isMpservice" :isMpservice="isMpservice" @close="closeMpservice"></Mpservice>

		<!--更多用户-->
		<MoreBill v-if="ismore" :bill="bill" :ismore="true" @closeMore="openMoreFunc" @gobill="gobillFunc"></MoreBill>
	</view>
</template>

<script>
import Countdown from '@/components/countdown/countdown.vue';
import Mpservice from '@/components/mpservice/Mpservice.vue';
import MoreBill from './popup/MoreBill.vue';
import Spec from './popup/Spec.vue';
import Bill from './part/Bill.vue';
import utils from '@/common/utils.js';
import share from '@/components/mp-share.vue';
import AppShare from '@/components/app-share.vue';
export default {
	components: {
		Spec,
		Countdown,
		Mpservice,
		Bill,
		MoreBill,
		share,
		AppShare
	},
	data() {
		return {
			from_type: 10,
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
			/*商品id*/
			product_id: null,
			/*产品图片展示*/
			imagList: [],
			/*活动详情*/
			activeDetail: {},
			/*详情*/
			detail: {
				product_sku: {},
				show_sku: {
					assemble_price: '',
					product_sku_id: 0,
					line_price: '',
					stock_num: 0,
					sku_image: '',
					assemble_product_sku_id: 0
				},
				show_point_sku: {}
			},
			/*已购买用户*/
			bill: [],
			/*是否确定购买弹窗*/
			isPopup: false,
			/*商品属性*/
			specData: null,
			/*子级页面传参*/
			productModel: {},
			/*拼团商品ID*/
			assemble_product_id: 0,
			/*倒计时配置*/
			countdownConfig: {
				/*开始时间*/
				startstamp: 0,
				/*结束时间*/
				endstamp: 0
			},
			/*是否打开客服*/
			isMpservice: false,
			/*是否打开更多*/
			ismore: false,
			/*拼团ID*/
			assemble_bill_id: null,
			/*当前用户id*/
			user_id: null,
			/*商品规格*/
			productSku: [],
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

		this.user_id = this.getUserId();

		/*拼团商品ID*/
		this.assemble_product_id = e.assemble_product_id;

		/*拼团组ID*/
		this.assemble_bill_id = e.assemble_bill_id;
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
			assemble_product_id: self.assemble_product_id
		});
		return {
			title: self.detail.product.product_name,
			path: '/pages/plus/assemble/detail/detail?' + params
		};
	},
	onHide() {},
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
			self.loadding = true;
			self._get(
				'plus.assemble.product/detail',
				{
					assemble_product_id: self.assemble_product_id,
					url: self.url
				},
				function(res) {
					self.countdownConfig.startstamp = res.data.active.start_time;
					self.countdownConfig.endstamp = res.data.active.end_time;
					self.activeDetail = res.data.active;
					/*详情内容格式化*/
					res.data.detail.product.content = utils.format_content(res.data.detail.product.content);

					/*初始化商品多规格*/
					if (res.data.detail.product.spec_type == 20) {
						self.initSpecData(res.data.detail.assembleSku, res.data.specData);
					}

					self.detail = res.data.detail;
					self.bill = res.data.bill;
					
					// 配置微信分享参数
					//#ifdef H5
					if (self.url != '') {
						let params = {
							assemble_product_id: self.assemble_product_id
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
			
			/*是否单团，0不，1是*/
			if ((this.activeDetail.is_single == 1&&e == 'order' && this.bill.length == 0)||(this.activeDetail.is_single == 0&&e == 'order')||e!='order') {
				if (e == 'order' && this.assemble_bill_id == null) {
					this.assemble_bill_id = 0;
				}
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
						assemble_bill_id: this.assemble_bill_id,
						sum: 1
					},
					productSku: this.productSku,
					type: e
				};
				this.productModel = obj;
				this.isPopup = true;
			} else {
				this.ismore = true;
			}
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

		/*打开关闭更多*/
		openMoreFunc(e) {
			this.ismore = e;
		},

		/*去拼团*/
		gobillFunc(e) {
			this.ismore = false;
			for (let i = 0; i < e.billUser.length; i++) {
				let user = e.billUser[i];
				if (this.user_id === user.user_id) {
					let url = '/pages/plus/assemble/fight-group-detail/fight-group-detail?assemble_bill_id=' + e.assemble_bill_id;
					this.gotoPage(url);
					return;
				}
			}
			this.assemble_bill_id = e.assemble_bill_id;
			this.openPopup();
		},

		/*倒计时返回值*/
		returnValFunc(e) {},
		
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
				assemble_product_id: self.assemble_product_id
			});
			self.appParams.path = '/pages/plus/assemble/detail/detail?' + params;
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
	},
	destroyed() {}
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
		margin-bottom: 16rpx;
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
		font-size: 26rpx;
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
		width: 90rpx;
		height: 100rpx;
	}

	.btns-wrap .icon-box .iconfont {
		font-size: 40rpx;
		color: #888888;
	}

	.btns-wrap .customer-service button {
		height: 80rpx;
		line-height: 80rpx;
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

	.btns-wrap .buy-alone button,
	.btns-wrap .make-group button {
		font-size: 26rpx;
		background: none;
	}

	.btns-wrap .buy-alone,
	.btns-wrap .buy-alone button {

		width: 220rpx;
		height: 80rpx;
		border-top-left-radius: 40rpx;
		border-bottom-left-radius: 40rpx;
		margin-left: 10rpx;
	}

	.btns-wrap .buy-alone {
		background: linear-gradient(270deg, #E48441 0%, #EAA84E 100%);
	}

	.btns-wrap .buy-alone text,
	.btns-wrap .make-group text {
		color: #ffffff;
		font-size: 32rpx;
	}

	.btns-wrap .make-group,
	.btns-wrap .make-group button {
		width: 220rpx;
		height: 80rpx;
		border-top-right-radius: 40rpx;
		border-bottom-right-radius: 40rpx;
		margin-right: 10rpx;
	}

	.btns-wrap .make-group {
		background: linear-gradient(270deg, #D42716 0%, #DD4746 100%);
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
		background: linear-gradient(-90deg, #FF3C3C 0%, #FF6E35 98%);
		// background: linear-gradient(to bottom, #ff6c65, #e2231a);
	}

	.product-detail .limited-spike .left-name {
		font-size: 32rpx;
		color: #ffffff;
	}

	.product-detail .limited-spike .right::v-deep text {
		color: #FFFFFF;
		font-size: 28rpx;

	}

	.product-detail .limited-spike .right::v-deep .box {
		height: 40rpx;
		padding: 4rpx;
		border-radius: 12rpx;
		line-height: 40rpx;
		text-align: center;
		background: #ffffff;
		color: #FF0001;
		font-size: 28rpx;
		padding: 6rpx 8rpx;
	}

	.product-detail .limited-spike .right::v-deep>text {
		margin-left: 10rpx;

	}

	.already-choice {
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

	.btnname {
		position: absolute;
		bottom: -14px;
		left: 0;
		right: 0;
		font-size: 22rpx;
		color: #333333;
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
