<template>
	<view class="gift-package" :data-theme="theme()" :class="theme() || ''" v-if="!loadding">
		<view class="top-tabbar">
			<block  v-if="hasType('10')"><view :class="tab_type == 0 ? 'tab-item active' : 'tab-item'" @click="tabFunc(0)">快递配送</view></block>
			<block  v-if="hasType('20')"><view :class="tab_type == 1 ? 'tab-item active' : 'tab-item'" @click="tabFunc(1)">上门自提</view></block>
		</view>

		<Myinfo v-if="tab_type == 0" :Address="Address" :exist_address="exist_address"></Myinfo>

		<Storeinfo v-if="tab_type == 1" ref="getShopinfoData" :extract_store="extract_store" :last_extract="last_extract"></Storeinfo>

		<view class="gift-package-main">
			<view class="order_tit">{{ detail.name }}</view>
			<view class="">
				<view class="p-0-30">
					<view class="integral" v-if="detail.is_point">
						<view class="title">{{points_name()}}礼包</view>
						<view class="integral_btom  d-s-c">
							<image src="/static/gift.png" mode="widthFix"></image>
							<view class="info">
								<view class="num">{{ parseFloat(detail.point) }}{{points_name()}}</view>
								<view>无门槛 全品类</view>
							</view>
						</view>
					</view>
				</view>
				<view class="p-0-30" v-if="detail.is_coupon">
					<view class="cuopon-title">优惠券礼包</view>
					<view class="cuopon-group borbox" if="detail.is_coupon">
						<view class="body">
							<view class="item" v-for="(item, index) in detail.coupon_list" :key="index">
								<view class="cuopon_item">
									<view><image class="cuopon_img" src="../../static/youhuiquan2.png" mode=""></image></view>
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
				<view class="p-0-30" v-if="detail.is_product">
					<view class="cuopon-group" if="detail.is_coupon">
						<view class="title f30">
							<view>商品礼包({{ detail.product_list.length }}选{{ detail.product_num }})</view>
						</view>
						<view class="body">
							<view class="item mb30 borbox bg-white" v-for="(item, index) in detail.product_list" :key="index" @click="choosePro(item.product_id)">
								<image v-if="hasId(item.product_id)" class="pro_choose" src="../../static/yes.png" mode=""></image>
								<view v-else class="pro_not"></view>
								<view class="d-s-c">
									<view><image class="product_img" :src="item.image[0].file_path" mode="aspectFill"></image></view>
									<view class="pro">
										<view class="pro_t">{{ item.product_name }}</view>
										<view class="pro_c" v-if="alreadyChioce[item.product_id]">{{ alreadyChioce[item.product_id] }}</view>
										<view class="d-b-c">
											<view class="pro_b theme-price">
												<text class="f18">￥</text>
												{{ item.product_price }}
											</view>
											<view class="speci theme-btn" v-if="item.product_multi_spec" @click="openPopup('order', item.product_multi_spec, item)">选规格</view>
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="btns">
			<view class="order_price theme-price">￥{{ detail.money }}</view>
			<button class="theme-btn" @click="onPay()">立即支付</button>
		</view>
		<!--购物确定-->
		<spec :isPopup="isPopup" :productModel="productModel" @close="closePopup"></spec>
	</view>
</template>

<script>
import { pay } from '@/common/pay.js';
import Myinfo from './giftpackage/my-info';
import Storeinfo from './giftpackage/store-info';
import spec from './giftpackage/spec.vue';
import utils from '@/common/utils.js';
export default {
	components: {
		Myinfo,
		Storeinfo,
		spec
	},
	data() {
		return {
			/*是否加载完成*/
			loadding: false,
			indicatorDots: true,
			autoplay: true,
			interval: 2000,
			duration: 500,
			id: 0,
			tab_type: 0,
			/*礼包详情*/
			detail: {
				name: '',
				point: '',
				money: '',
				coupon_list: []
			},
			store_id: 0,
			// 是否存在收货地址
			exist_address: false,
			/*默认地址*/
			Address: null,
			/* 配送类别 */
			delivery: 0,
			extract_store: {},
			last_extract: {},
			chooseProlist: [],
			/*是否显示支付类别弹窗*/
			isPayPopup: false,
			payType: 10,
			/*子级页面传参*/
			productModel: {},
			/*规格选择弹窗*/
			isPopup: false,
			/*已经选择的规格*/
			alreadyChioce: {},
			code: 0,
			/* 点击的商品规格 */
			chooseSpecData: null,
			deliverySetting:[],
			/*点击的商品详情*/
			chooseDetail: {
				product_sku: {},
				show_sku: {
					product_price: '',
					product_sku_id: 0,
					line_price: '',
					stock_num: 0,
					sku_image: ''
				}
			},
			params: []
		};
	},
	onLoad(e) {
		let self = this;
		let scene = utils.getSceneData(e);
		self.$fire.on('selectStoreId', function(store_id) {
			self.store_id = store_id;
		});
		/*商品id*/
		this.package_id = e.package_id;
		this.code = e.code;
	},
	onShow() {
		this.getData();
	},
	methods: {
		/*获取数据*/
		getData() {
			let self = this;
			uni.showLoading({
				title: '加载中'
			});
			if (self.detail.name == '') {
				self.delivery = 0;
			}
			let params = {
				package_id: self.package_id,
				delivery_type: self.delivery,
				code: self.code
			};
			if (self.delivery == 20) {
				params.store_id = self.store_id;
			}else{
				params.store_id = 0;
			}
			params.pay_source = self.getPlatform();
			self._get(
				'plus.package.package/buy',
				params,
				function(res) {
					self.detail = res.data.data;
					self.deliverySetting = res.data.data.delivery;
					self.extract_store = res.data.data.extract_store;
					self.exist_address = res.data.data.exist_address;
					self.Address = res.data.data.address;
					self.delivery = res.data.data.delivery_type;
					if(self.delivery==10){
						self.tab_type = 0;
					}else{
						self.tab_type = 1;
					}
					uni.hideLoading();
					self.loadding = false;
				},
				function(res) {
					uni.navigateBack({
						delta: 1
					});
				}
			);
		},
		hasType(e) {
			if (this.deliverySetting.indexOf(e) != -1) {
				return true;
			} else {
				return false;
			}
		},
		/*选择配送方式*/
		tabFunc(e) {
			this.tab_type = e;
			if (e == 0) {
				this.delivery = 10;
			} else {
				this.delivery = 20;
			}
			this.getData();
		},
		hasId(id) {
			let self = this;
			let n = self.chooseProlist.indexOf(id);
			if (n == -1) {
				return false;
			} else {
				return true;
			}
		},
		choosePro(id) {
			let self = this;
			let n = self.chooseProlist.indexOf(id);
			if (n == -1) {
				self.chooseProlist.push(id);
			} else {
				self.chooseProlist.splice(n, 1);
			}
		},
		/*购买*/
		payTypeFunc() {
			let self = this;
			uni.showLoading({
				title: '正在提交...'
			});
			let prolist = [];
			let errflag = false;
			self.chooseProlist.forEach((proitem, proindex) => {
				let flag = false;
				self.params.forEach((pramaitem, pramaindex) => {
					if (proitem == pramaitem.product_id) {
						flag = true;
						prolist.push({
							product_id: pramaitem.product_id,
							product_sku_id: pramaitem.product_sku_id
						});
					}
				});
				if (!flag) {
					self.detail.product_list.forEach((item, index) => {
						if (item.product_id == proitem) {
							if (item.spec_type == 20 && item.product_multi_spec.spec_attr.length > 0) {
								uni.showToast({
									title: '请选择商品规格',
									icon: 'none'
								});
								errflag = true;
							} else {
								prolist.push({
									product_id: item.product_id,
									product_sku_id: 0
								});
							}
						}
					});
				}
			});
			if (errflag) {
				return;
			}
			let _linkman = null;
			let _phone = null;
			if (self.$refs != null) {
				if (self.$refs.getShopinfoData != null) {
					_phone = self.$refs.getShopinfoData.phone;
					_linkman = self.$refs.getShopinfoData.linkman;
				}
			}
			let extract = {
				phone: _phone,
				linkman: _linkman,
				store_id: self.store_id
			};
			let pay_source = 'wx';
			// #ifdef  H5
			pay_source = 'mp';
			// #endif
			let params = {
				code: self.code,
				package_id: self.package_id,
				product_ids: JSON.stringify(prolist),
				delivery_type: self.delivery,
				address: JSON.stringify(self.Address),
				extract: JSON.stringify(extract)
			};
			if (self.delivery == 10) {
				params.extract = null;
			}
			if (self.delivery == 20) {
				params.address = null;
			}
			self._post('plus.package.package/buy', params, function(res) {
				self.gotoPage('/pages/order/cashier?order_id=' + res.data.order_id + '&order_type=20');
			});
		},
		onPay() {
			let self = this;
			// 是否选择了商品
			if (self.chooseProlist.length < self.detail.product_num && self.detail.is_product == 1) {
				uni.showToast({
					icon: 'none',
					title: '请选择商品'
				});
				return;
			}
			self.payTypeFunc();
		},
		/*关闭弹窗*/
		closePopup(e, params, cart_total_num) {
			this.isPopup = false;
			let n = this.chooseProlist.indexOf(params.product_id);
			if (n == -1) {
				this.chooseProlist.push(params.product_id);
			}
			if (e && e.spec_attr) {
				this.alreadyChioce[params.product_id] = '';
				let has = '已选：';
				let noone = '';
				e.spec_attr.forEach(item => {
					if (item.spec_items) {
						let h = '';
						for (let i = 0; i < item.spec_items.length; i++) {
							let child = item.spec_items[i];
							if (child.checked) {
								h = child.spec_value + ' / ';
								break;
							}
						}
						if (h != '') {
							has += h;
						} else {
							noone += item.group_name;
						}
					}
				});
				let flag = false;
				this.params.forEach((item, index) => {
					if (item.product_id == params.product_id) {
						flag = true;
						item = params;
					}
				});
				if (!flag) {
					this.params.push(params);
				}
				if (noone != '') {
					this.alreadyChioce[params.product_id] = noone;
				} else {
					has = has.replace(/(\s\/\s)$/gi, '');
					this.alreadyChioce[params.product_id] = has;
				}
			}
			if (cart_total_num) {
				this.cart_total_num = cart_total_num;
			}
		},
		/* 打开弹窗 */
		openPopup(e, spe, detail) {
			let obj = {
				specData: spe,
				detail: detail,
				productSpecArr: spe != null ? new Array(spe.spec_attr.length) : [],
				show_sku: {
					sku_image: '',
					seckill_price: 0,
					product_sku_id: 0,
					line_price: 0,
					seckill_stock: 0,
					seckill_product_sku_id: 0,
					sum: 1
				},
				type: e
			};
			this.productModel = obj;
			this.isPopup = true;
		}
	}
};
</script>

<style lang="scss" scoped>
page {
	background-color: #f2f2f2;
}

/*top-tab*/
.top-tabbar {
	height: 90rpx;
	line-height: 86rpx;
	display: flex;
	justify-content: space-between;
	align-items: center;
	background: #ffffff;
	border-bottom: 1px solid #dddddd;
}

.tab-item {
	flex: 1;
	height: 90rpx;
	display: flex;
	justify-content: center;
	align-items: center;
	font-size: 28rpx;
	color: #666666;
}
/*  */
.gift-package {
	position: relative;
	min-height: 100vh;
	padding-bottom: 100rpx;
}

.gift-package .gift-package-main {
	width: 750rpx;
	background: #ffffff;
	padding: 30rpx;
	box-sizing: border-box;
}

.order_tit {
	padding-left: 30rpx;
	font-size: 28rpx;
	margin-bottom: 73rpx;
}

.gift-package-main .giftpackagr-info {
	margin: 0 auto;
	width: 350rpx;
	height: 43rpx;
	line-height: 43rpx;
	border: 1rpx solid #fa212c;
	margin-top: 18rpx;
	background: rgba(255, 255, 255, 0.8);
	border-radius: 30rpx;
	text-align: center;
}

.gift-package-main .giftpackagr-info .detatime {
	/* padding:10px 30px; */
	font-size: 18rpx;
	color: #fa1f29;
	font-weight: 800;
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
	font-size: 24rpx;
	color: #cacaca;
	text-align: left;
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
	background-color: #ffffff66;
	padding-bottom: 60rpx;
	border-radius: 15rpx;
}

.gift-package-main .integral .title {
	font-size: 24rpx;
	color: #cacaca;
	text-align: left;
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

.btns {
	width: 100%;
	height: 100rpx;
	display: flex;
	justify-content: space-between;
	align-items: center;
	position: fixed;
	bottom: 0;
	background-color: #ffffff;
	border-top: 1rpx solid #eeeeee;
	z-index: 99;
}

.order_price {
	text-align: right;
	color: #fd0000;
	font-size: 30rpx;
	margin-left: 30rpx;
}

.btns button {
	width: 266rpx;
	height: 70rpx;
	background-color: #ee1413;
	color: #ffffff;
	text-align: center;
	line-height: 70rpx;
	border-radius: 35rpx;
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
	flex: 1;
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

.type_activ {
	width: 40rpx;
	height: 40rpx;
	background-color: #04be01;
	border-radius: 50%;
	overflow: hidden;
	text-align: center;
	line-height: 1;
}

.icon-tijiaochenggong {
	color: #ffffff;
	font-size: 22rpx;
}

.speci {
	font-size: 24rpx;
	width: 100rpx;
	height: 44rpx;
	color: #ffffff;
	text-align: center;
	border-radius: 22rpx;
	background-color: #fd0000;
	line-height: 44rpx;
}

.pro_not {
	width: 33rpx;
	height: 33rpx;
	position: absolute;
	right: 10rpx;
	top: 6rpx;
	border: 1rpx solid #cacaca;
	border-radius: 50%;
}
</style>
