<template>
	<view class="product-detail" :data-theme="theme()" :class="theme() || ''" v-if="!loadding">
		<!--图片-->
		<view class="product-pic">
			<swiper class="swiper" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
				<swiper-item v-for="(item, index) in detail.product.image" :key="index"><image :src="item.file_path" mode=""></image></swiper-item>
			</swiper>
		</view>

		<!--基本信息-->
		<view class="bg-white">
			<view class="price-wrap pr">
				<view class="flex-1 d-s-s d-c">
					<view class="new-price">
						<text class="red f24">所需{{ points_name() }}</text>
						<text class="red fb f50">{{ detail.sku[0].point_num }}</text>
						<template v-if="detail.sku[0].point_money > 0">
							<text class="red f40 gray9">+</text>
							<text>¥</text>
							<text class="num fb f40">{{ detail.sku[0].point_money }}</text>
						</template>
					</view>
					<text class="old-price">¥{{ detail.sku[0].productSku.line_price }}</text>
				</view>
				<view class="share-box">
					<button @click="showShare" open-type="share"><image class="share_img" src="/static/icon/fenxiang.png" mode=""></image></button>
				</view>
				<text class="already-sale">还剩余{{ detail.stock }}件</text>
			</view>
			<view class="product-name">{{ detail.product.product_name }}</view>
			<view class="product-describe">{{ detail.product.selling_point }}</view>
		</view>

		<!--详情内容-->
		<view class="product-content">
			<view class="group-hd border-b-e">
				<view class="left"><text class="min-name">商品介绍</text></view>
			</view>
			<view v-if="detail.product.is_picture == 0" class="content-box" v-html="detail.product.content"></view>
			<view v-if="detail.product.is_picture == 1" class="content-box">
				<view class="ww100" v-for="(item, index) in detail.product.contentImage" :key="index"><image class="ww100" :src="item.file_path" mode="widthFix"></image></view>
			</view>
		</view>

		<!--底部按钮-->
		<view class="btns-wrap">
			<view class="customer-service d-c-c">
				<!-- #ifdef MP-WEIXIN -->
				<view class="icon-box">
					<button class="d-c-c d-c bg-white" open-type="contact" session-from="wxapp">
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
			<button type="primary" class="buy" @click="openPopup('order')">立即抢购</button>
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
import Mpservice from '@/components/mpservice/Mpservice.vue';
import utils from '@/common/utils.js';
import share from '@/components/mp-share.vue';
import AppShare from '@/components/app-share.vue';
export default {
	components: {
		/*选择规格*/
		Spec,
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
			/*积分商品id*/
			point_product_id: null,
			/*产品图片展示*/
			imagList: [],
			/*详情*/
			detail: {
				/*商品规格*/
				product_sku: {},
				/*当前规格对象*/
				show_sku: {
					/*商品价格*/
					product_price: '',
					/*商品规格ID*/
					product_sku_id: 0,
					/*划线价*/
					line_price: '',
					/*库存*/
					stock_num: 0,
					/*图片*/
					sku_image: ''
				},
				show_point_sku: {}
			},
			/*是否确定购买弹窗*/
			isPopup: false,
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
			url: ''
		};
	},
	onReady() {
		uni.setNavigationBarTitle({
			title: this.points_name() + '商品'
		});
	},
	onLoad(e) {
		/*分类id*/
		this.point_product_id = e.point_product_id;
		//#ifdef H5
		this.url = window.location.href;
		//#endif
	},
	mounted() {
		/*获取产品详情*/
		this.getData();
	},
	/*分享*/
	onShareAppMessage() {
		let self = this;
		// 构建页面参数
		let params = self.getShareUrlParams({
			point_product_id: self.point_product_id
		});
		return {
			title: self.detail.product.product_name,
			path: '/pages/plus/points/detail/detail?' + params
		};
	},
	methods: {
		/*获取数据*/
		getData() {
			let self = this;
			let point_product_id = self.point_product_id;
			uni.showLoading({
				title: '加载中'
			});
			self._get(
				'plus.points.product/detail',
				{
					point_product_id: point_product_id,
					url: self.url
				},
				function(res) {
					/*详情内容格式化*/
					res.data.detail.product.content = utils.format_content(res.data.detail.product.content);

					/*初始化商品多规格*/
					if (res.data.detail.product.spec_type == 20) {
						self.initSpecData(res.data.detail.sku, res.data.specData);
					}
					self.detail = res.data.detail;
					// 配置微信分享参数
					//#ifdef H5
					if (self.url != '') {
						let params = {
							point_product_id: self.point_product_id
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
				point_product_id: self.point_product_id
			});
			self.appParams.path = '/pages/plus/points/detail/detail?' + params;
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
	padding: 20rpx 20rpx 0;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.product-detail .price-wrap .old-price {
	padding: 10rpx 0;
	font-size: 30rpx;
	color: #888888;
	text-decoration: line-through;
}

.product-detail .already-sale {
	font-size: 24rpx;
	color: #999999;
}

.product-detail .product-name {
	padding: 10rpx 20rpx 0;
	font-size: 30rpx;
	font-weight: bold;
	color: #333333;
}

.product-detail .product-describe {
	padding: 20rpx;
	line-height: 40rpx;
	font-size: 24rpx;
	color: red;
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
	height: 90rpx;
	right: 0;
	bottom: 0;
	left: 0;
	display: flex;
	background: #ffffff;
}

.btns-wrap .icon-box {
	width: 90rpx;
	height: 90rpx;
	border-right: 1px solid #dddddd;
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
	height: 91rpx;
	line-height: 90rpx;
	margin: 0;
	padding: 0;
	flex: 1;
	border-radius: 0;
	border: 0;
}

.btns-wrap button.add-cart {
	background: $orange-color;
}

.btns-wrap button.buy {
	background: $dominant-color;
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
</style>
