<template>
	<view class="wrap" :data-theme='theme()' :class="theme() || ''" v-if="!loading">
		<!--tab-->
		<view class="top-tabbar" v-if="OrderData.delivery != 30">
			<block v-if="hasType(10)">
				<view :class="tab_type == 0 ? 'tab-item active' : 'tab-item'" @click="tabFunc(0)">快递配送</view>
			</block>
			<block v-if="hasType(20)">
				<view :class="tab_type == 1 ? 'tab-item active' : 'tab-item'" @click="tabFunc(1)">上门自提</view>
			</block>
		</view>
		<view class="p-0-23">
			<Myinfo :dis="options.order_type == 'retainage'" v-if="tab_type == 0 && OrderData.delivery != 30"
				:Address="Address" :exist_address="exist_address">
			</Myinfo>

			<Storeinfo v-if="tab_type == 1 && OrderData.delivery != 30" ref="getShopinfoData"
				:extract_store="extract_store" :last_extract="last_extract"></Storeinfo>

			<!--购买的产品-->
			<view class="vender br6">
				<view class="list">
					<view class="d-b-c f24 fb mb30 top-title">
						<view>商品信息</view>
						<view>共{{OrderData.order_total_num }}件</view>
					</view>
					<view class="item" v-for="(item, index) in ProductData" :key="index">
						<view class="d-f">
							<view class="cover">
								<image :src="item.product_image" mode="aspectFit"></image>
							</view>
							<view class="info">
								<view class="d-b-s">
									<view class="flex-1">
										<view class="title f32 gray3">{{ item.product_name }}</view>
										<view class="theme-price mt10 f18">
											¥<text class="f26">{{ item.product_price }}</text>
										</view>
										<view class="describe mt10 f24" v-if="options.order_type=='deposit'">
											{{ item.advance_sku.product_attr }}
										</view>
										<view class="describe mt10 f24" v-else-if="options.order_type=='retainage'">
											{{ item.product_attr }}
										</view>
										<view class="describe mt10 f24" v-else>{{ item.product_sku.product_attr }}
										</view>
									</view>
									<view>
										<view class=" count_choose">
											<view class="num-wrap">
												<view class="f22 tr">×{{ item.total_num }}</view>
											</view>
										</view>
									</view>
								</view>
							</view>
						</view>
						<view class="mt10 tr f28" v-if="item.is_user_grade==true">
							<text class="f26">会员折扣价：</text>
							<text class="theme-price f32">￥{{item.grade_product_price}}</text>
						</view>
						<view class="mt10 tr f28" v-if="item.product_reduce_money > 0">
							<text class="f26">立减：</text>
							<text class="theme-price f32">￥{{item.product_reduce_money}}</text>
						</view>
					</view>
					<view class="item" v-for="(item, index) in giftData" :key="'gift_'+index">
						<view class="d-f">
							<view class="cover">
								<image :src="item.product_image" mode="aspectFit"></image>
							</view>
							<view class="info d-b-s">
								<view class="flex-1 d-b-s d-c hh100">
									<view>
										<view class="f28 gray3 text-ellipsis mb6"><text
												class="gift-tips">赠品</text>{{ item.product_name }}</view>
									</view>
									<view class="describe mt10 f24" v-if="item.product_sku.product_attr">
										{{ item.product_sku.product_attr }}
									</view>
									<view class="f24 text-d-line gray9">¥{{ item.line_price }}</view>
								</view>
								<view class="f24 tr gray9">×{{ item.total_num }}</view>
							</view>
						</view>
					</view>
				</view>
			</view>

			<!--其它费用-->
			<view class="buy-checkout br6">
				<template v-if="options.order_type=='deposit'">
					<view class="item">
						<text class="key f26">定金：</text>
						<view class="f24">￥{{ OrderData.order_total_front_price }}</view>
					</view>
					<view class="item">
						<text class="key f26">尾款：</text>
						<view class="f24">￥{{ OrderData.order_total_pay_price  }}</view>
					</view>
					<view class="item f-d-c">
						<view class="ww100 d-e-c redF11">
							(单件商品尾款已减{{OrderData.product_list[0].advance_sku.advance_price}}元)
						</view>
						<view class="ww100 d-e-c gray9">
							{{OrderData.product_list[0].advance.active_time[0]}}-{{OrderData.product_list[0].advance.active_time[1]}}支付尾款
						</view>
					</view>
				</template>
				<template v-else>
					<view class="item">
						<text class="key f26">商品金额：</text>
						<view class="f24">￥{{ OrderData.order_total_price }}</view>
					</view>
					<view class="item">
						<text class="key">配送费用：</text>
						<view>
							<text class="f24">￥{{ OrderData.express_price }}</text>
						</view>
					</view>
					<view class="item" v-if="OrderData.is_coupon">
						<text class="key">优惠券：</text>
						<block v-if="coupon_num > 0">
							<view class="f24 theme-price" v-if="OrderData.coupon_id > 0"
								@click="onTogglePopupCoupon(coupon_list)">-￥{{ OrderData.coupon_money }}</view>
							<view v-else class="hascoupon" @click="onTogglePopupCoupon(coupon_list)">
								{{ coupon_num }}张可用<text class="icon iconfont icon-you"></text>
							</view>
						</block>
						<text v-else class="f24 gray9">无优惠券可用</text>
					</view>
					<view class="item" v-if="OrderData.product_reduce_money > 0">
						<text class="key">商品立减：</text>
						<view>
							<text class="theme-price f24">-￥{{ OrderData.product_reduce_money }}</text>
						</view>
					</view>
					<view class="item" v-if="OrderData.reduce">
						<text class="key">满减({{ OrderData.reduce.active_name }})：</text>
						<view>
							<text class="theme-price f24">-￥{{ OrderData.reduce.reduced_price }}</text>
						</view>
					</view>
					<view class="item" v-if="OrderData.reduce_money">
						<text class="key">尾款抵扣：</text>
						<view>
							<text class="theme-price f24">-￥{{ OrderData.reduce_money }}</text>
						</view>
					</view>
					<view class="item"
						v-if="OrderData.is_allow_points && OrderData.force_points == false && OrderData.points_money > 0">
						<text class="key">可用{{points_name()}}抵扣：</text>
						<view class="">
							<text class="price">-￥{{ OrderData.points_money }}</text>
							<switch :checked="is_use_points" style="transform: scale(0.7); margin-right: -10rpx;"
								@change="onShowPoints" />
						</view>
					</view>
				</template>

				<view class="item">
					<view class="border-t-d9 d-e-c p-30-0 ww100">
						<text class="key f24">小计：</text>
						<view class="f38 fb" v-if="options.order_type=='deposit'"><text
								class="f24">￥</text>{{toDecimal2(OrderData.order_total_front_price)}}</view>
						<view class="f38 fb" v-else><text class="f24">￥</text>{{toDecimal2(OrderData.order_pay_price)}}
						</view>
					</view>
				</view>
			</view>
			<!--买家留言-->
			<view class="buyer-message uni-textarea" v-if="options.order_type != 'deposit'"><textarea class="textarea"
					v-model="remark" placeholder-style="color:#cccccc;" placeholder="选项:买家留言" /></view>
		</view>
		<view class="buy-checkout" v-if="confirm && confirm != null">
			<view class="item" v-for="(item, index) in confirm" :key="index">
				<view style="white-space: nowrap">
					<span v-if="item.status" style="color: red">* </span>
					<span v-else style="margin-left: 18rpx"></span>
					<span>{{ item.title }}</span>
				</view>
				<!-- text -->
				<view v-if="item.label == 'text'" class="confirm">
					<input class="tr f24 gray3" placeholder-class="f24 gray3" type="text"
						:placeholder="'请填写' + item.title" v-model="item.value" />
				</view>
				<!-- number -->
				<view v-if="item.label == 'number'" class="confirm">
					<input class="tr f24 gray3" placeholder-class="f24 gray3" type="number"
						:placeholder="'请填写' + item.title" v-model="item.value" />
				</view>
				<!-- email -->
				<view v-if="item.label == 'email'" class="confirm">
					<input class="tr f24 gray3" placeholder-class="f24 gray3" type="text"
						:placeholder="'请填写' + item.title" v-model="item.value" />
				</view>
				<!-- data -->
				<view v-if="item.label == 'data'" class="uni-list">
					<view class="uni-list-cell">
						<view class="uni-list-cell-db">
							<picker mode="date" :value="item.value" @change="bindDateChange($event, index)">
								<view v-if="item.value == ''" class="f24 gray3 ">{{ date + item.title  }}<text
										class="iconfont icon-jiantou"></text></view>
								<view v-else class="uni-input f24">{{ item.value }}</view>
							</picker>
						</view>
					</view>
				</view>
				<!-- time -->
				<view v-if="item.label == 'time'">
					<view>
						<view>
							<picker mode="time" :value="item.value" start="00:00" end="23:59"
								@change="bindTimeChange($event, index)">
								<view v-if="item.value == ''" class="f24 gray3">{{ time + item.title }}<text
										class="iconfont icon-jiantou"></text></view>
								<view>{{ item.value }}</view>
							</picker>
						</view>
					</view>
				</view>
				<!-- id -->
				<view v-if="item.label == 'id'" class="confirm">
					<input class="tr f24 gray3" type="idcard" placeholder-class="f24 gray3"
						:placeholder="'请填写' + item.title" v-model="item.value" />
				</view>
				<!-- phone -->
				<view v-if="item.label == 'phone'" class="confirm">
					<input class="tr f24 gray3" type="tel" placeholder-class="f24 gray3"
						:placeholder="'请填写' + item.title" v-model="item.value" />
				</view>
			</view>
		</view>

		<!--底部支付-->
		<view class="foot-pay-btns">
			<template v-if="options.order_type=='deposit'">
				<view>
					应付
					<text class="fb theme-price">¥</text>
					<text class="num theme-price fb f38">{{ OrderData.order_total_front_price }}</text>
				</view>
			</template>
			<template v-else>
				<view v-if="!OrderData.force_points">
					应付
					<text class="fb theme-price">¥</text>
					<text class="num theme-price fb f38">{{ OrderData.order_pay_price }}</text>
				</view>
				<view class="price" v-if="OrderData.force_points">
					<text class="gray9">所需{{points_name()}}</text>
					<text class="num theme-price fb">{{ OrderData.points_num }}</text>
					<template v-if="OrderData.order_pay_price > 0">
						<text class="theme-price">+</text>
						<text class="theme-price">¥</text>
						<text class="num fb theme-price">{{ OrderData.order_pay_price }}</text>
					</template>
				</view>
			</template>
			<!-- #ifdef  H5 -->
			<template v-if="isWeixin() && mpState == 1&&temlIds!=''">
				<wx-open-subscribe :template="temlIds" id="subscribe-btn" @success="subscribeSuccess"
					@error="subscribeFail" :style="{
					width: btnAtrrpx.width+'px',
				}">
					<div v-is="'script'" type="text/wxtag-template" slot="style">
						<div v-is="'style'">
							.subscribe-btn.theme0{
							background: #ff5704;
							}
							.subscribe-btn.theme1{
							background: #19ad57;
							}
							.subscribe-btn.theme2{
							background: #ffcc00;
							}
							.subscribe-btn.theme3{
							background: #33a7ff;
							}
							.subscribe-btn.theme4{
							background: #e4e4e4;
							}
							.subscribe-btn.theme5{
							background: #c8ba97;
							}
							.subscribe-btn.theme6{
							background: #623ceb;
							}
							.subscribe-btn {
							display:flex;
							width:100%;
							align-items:center;
							justify-content:center;
							color: #fff;
							border:none;
							}
						</div>
					</div>
					<div v-is="'script'" type="text/wxtag-template">
						<div class="subscribe-btn" :class="theme()" :style="{
							height: btnAtrrpx.height+'px',
							lineHeight: btnAtrrpx.height+'px',
							borderRadius: btnAtrrpx.borderRadius+'px',
							fontSize: btnAtrrpx.fontSize+'px',
						}">
							提交订单
						</div>
					</div>
				</wx-open-subscribe>
			</template>
			<template v-else>
				<button @click="SubmitOrder">提交订单</button>
			</template>
			<!-- #endif -->
			<!-- #ifndef  H5 -->
			<button @click="SubmitOrder">提交订单</button>
			<!-- #endif -->
		</view>

		<!--优惠券-->
		<Coupon :isCoupon="isCoupon" :couponList="couponList" @close="closeCouponFunc"></Coupon>
		<!--支付-->
		<Checkout :isCheckout="isCheckout" :order_id="order_id" @close="closeCheckoutFunc"></Checkout>
	</view>
</template>

<script>
	import Myinfo from './confirm-order/my-info';
	import Storeinfo from './confirm-order/store-info';
	import Coupon from './confirm-order/coupon';
	import {
		pay
	} from '@/common/pay.js';
	import Checkout from './checkout';
	//#ifdef H5
	import jweixin from 'weixin-js-sdk';
	//#endif
	export default {
		components: {
			Myinfo,
			Storeinfo,
			Coupon,
			Checkout
		},
		data() {
			return {
				/*是否加载完成*/
				loading: true,
				options: {},
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				tab_type: 0,
				/*商品id*/
				product_id: '',
				/*商品数量*/
				product_num: '',
				/*商品数据*/
				ProductData: [],
				/*订单数据数据*/
				OrderData: [],
				// 是否存在收货地址
				exist_address: false,
				/*默认地址*/
				Address: {
					region: []
				},
				extract_store: [],
				last_extract: {},
				product_sku_id: 0,
				/*配送方式*/
				delivery: 0,
				/*自提店id*/
				store_id: 0,
				/*优惠券id*/
				coupon_id: 0,
				/*是否使用积分*/
				is_use_points: 1,
				linkman: '',
				phone: '',
				remark: '',
				/*支付方式*/
				pay_type: 20,
				/*是否显示优惠券*/
				isCoupon: false,
				/*优惠券列表*/
				coupon_list: {},
				couponList: [],
				deliverySetting: [],
				/*优惠券数量*/
				coupon_num: 0,
				/*消息模板*/
				temlIds: [],
				// 支付相关
				isCheckout: false,
				order_id: 0,
				order_type: '',
				urldata: '',
				btnAtrrpx: {},
				// 1 公众号登录
				mpState: null,
				confirm: "",
				index: 0,
				date: "请选择",
				time: "请选择",
				defaultDate: "",
				defaultTime: "",
				giftData: []
			};
		},
		onLoad(options) {
			let self = this;
			let mpState = uni.getStorageSync('mpState');
			this.mpState = mpState;
			if (this.getPlatform() == 'mp') {
				this.urldata = window.location.href;
			}
			self.options = options;
			self.$fire.on('selectStoreId', function(store_id) {
				self.store_id = store_id;
				self.extract_store = store_id;
			});
			self.$fire.on('extract', function(params) {
				self.last_extract = params;
			});
			let btnAtrrpx = {
				height: 80,
				borderRadius: 40,
				fontSize: 32,
				width: 200,
			}
			uni.getSystemInfo({
				success: function(res) {
					let scale = res.screenWidth / 750;
					let newObj = {};
					for (let key in btnAtrrpx) {
						newObj[key] = btnAtrrpx[key] * scale;
					}
					self.btnAtrrpx = newObj;
				},
				fail() {
					self.btnAtrrpx = btnAtrrpx;
				},
			});
		},
		onShow() {
			this.getData();
			//#ifdef H5
			this.getTemplateId();
			//#endif
		},
		methods: {
			bindDateChange: function(e, index) {
				console.log(e, "e");
				this.confirm[index].value = e.detail.value;
			},
			bindTimeChange: function(e, index) {
				this.confirm[index].value = e.detail.value;
			},
			subscribeSuccess() {
				this.SubmitOrder();
				console.log("调用成功")
			},
			subscribeFail() {
				this.SubmitOrder();
				console.log("调用失败")
			},
			getTemplateId() {
				let self = this;
				self._post(
					'index/getSignPackage', {
						url: self.urldata,
						paySource: self.getPlatform(),
					},
					function(res) {
						// self.temlIds = res.data.templateArr;
						self.mpMessage(res.data.signPackage);
					}
				);
			},
			hasType(e) {
				if (this.deliverySetting.indexOf(e + "") != -1) {
					return true;
				} else {
					return false;
				}
			},
			/*是否使用积分选择*/
			onShowPoints: function(e) {
				let self = this;
				if (e.detail.value == true) {
					self.is_use_points = 1;
				} else {
					self.is_use_points = 0;
				}
				self.getData();
			},
			/*选择优惠卷*/
			onTogglePopupCoupon(e) {
				let self = this;
				self.isCoupon = true;
				self.couponList = e;
			},
			/*关闭优惠券*/
			closeCouponFunc(e) {
				this.isCoupon = false;
				if (e == null) {
					return
				}
				let self = this;
				self.coupon_id = e;
				self.getData();
			},
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});

				let callback = function(res) {
					self.OrderData = res.data.orderInfo;
					self.temlIds = res.data.template_arr;
					self.exist_address = self.OrderData.exist_address;
					self.Address = self.OrderData.address;

					self.extract_store = self.OrderData.extract_store;

					self.last_extract = uni.getStorageSync('extract');
					if (!self.last_extract) {
						self.last_extract = self.OrderData.last_extract;
					}
					self.giftData = self.OrderData.buyProduct;
					self.ProductData = self.OrderData.product_list;
					self.ProductData.forEach((product) => {
						self.confirm = product.custom_form;
					});
					if (self.confirm) {
						self.confirm.map((e) => {
							if (e.label === "img") e.value = [];
						});
					}
					console.log(self.confirm)
					if (self.OrderData.coupon_list) {
						self.coupon_list = self.OrderData.coupon_list;
						self.coupon_num = Object.keys(self.coupon_list).length;
					}

					if (self.OrderData.delivery == '20') {
						self.tab_type = 1;
					}
					self.deliverySetting = self.OrderData.deliverySetting;
					if (self.OrderData.order_pay_price == 0) {
						self.pay_type = 10;
					}
					self.loading = false;
				};

				// 请求的参数
				let params = {
					delivery: self.delivery,
					store_id: self.store_id,
					coupon_id: self.coupon_id,
					is_use_points: self.is_use_points,
					pay_source: self.getPlatform(),
				};

				//直接购买
				if (self.options.order_type === 'buy') {
					self._get(
						'order.order/buy',
						Object.assign({}, params, {
							product_id: self.options.product_id,
							product_num: self.options.product_num,
							product_sku_id: self.options.product_sku_id
						}),
						function(res) {
							callback(res);
						}, err => {
							uni.navigateBack()
						}
					);
				}
				//定金
				if (self.options.order_type === 'deposit') {
					self._get(
						'plus.advance.Order/frontBuy',
						Object.assign({}, params, {
							product_id: self.options.product_id,
							product_num: self.options.product_num,
							product_sku_id: self.options.product_sku_id,
							advance_product_sku_id: self.options.advance_product_sku_id,
							advance_product_id: self.options.advance_product_id
						}),
						function(res) {
							callback(res);
						}, err => {
							uni.navigateBack()
						}
					);
				}
				//尾款
				if (self.options.order_type === 'retainage') {
					self._get(
						'plus.advance.Order/buy',
						Object.assign({}, params, {
							order_id: self.options.order_id,
						}),
						function(res) {
							callback(res);
						}, err => {
							uni.navigateBack()
						}
					);
				}
				// 购物车结算
				else if (self.options.order_type === 'cart') {
					console.log(self.options.cart_ids)
					self._get(
						'order.order/cart',
						Object.assign({}, params, {
							cart_ids: self.options.cart_ids || 0,
							// order_type: self.options.cart_type
						}),
						function(res) {
							callback(res);
						}, err => {
							uni.navigateBack()
						}
					);
				}
				// 积分兑换结算
				else if (self.options.order_type == 'points') {
					self._get(
						'plus.points.order/buy',
						Object.assign({}, params, {
							point_product_id: self.options.point_product_id,
							product_sku_id: self.options.product_sku_id,
							point_product_sku_id: self.options.point_product_sku_id,
							product_num: self.options.product_num
						}),
						function(res) {
							callback(res);
						}, err => {
							uni.navigateBack()
						}
					);
				}
				// 限时秒杀
				else if (self.options.order_type === 'seckill') {
					params.num = self.options.num;
					self._get(
						'plus.seckill.order/buy',
						Object.assign({}, params, {
							seckill_product_id: self.options.seckill_product_id,
							product_sku_id: self.options.product_sku_id,
							seckill_product_sku_id: self.options.seckill_product_sku_id,
							product_num: self.options.product_num
						}),
						function(res) {
							callback(res);
						}, err => {
							uni.navigateBack()
						}
					);
				}
				//砍价
				else if (self.options.order_type === 'bargain') {
					self._get(
						'plus.bargain.order/buy',
						Object.assign({}, params, {
							bargain_product_id: self.options.bargain_product_id,
							product_sku_id: self.options.product_sku_id,
							bargain_product_sku_id: self.options.bargain_product_sku_id,
							bargain_task_id: self.options.bargain_task_id
						}),
						function(res) {
							callback(res);
						}, err => {
							uni.navigateBack()
						}
					);
				}
				//拼团
				else if (self.options.order_type === 'assemble') {
					self._get(
						'plus.assemble.order/buy',
						Object.assign({}, params, {
							assemble_product_id: self.options.assemble_product_id,
							product_sku_id: self.options.product_sku_id,
							assemble_product_sku_id: self.options.assemble_product_sku_id,
							product_num: self.options.product_num,
							assemble_bill_id: self.options.assemble_bill_id,
						}),
						function(res) {
							callback(res);
						}, err => {
							uni.navigateBack()
						}
					);
				}
			},

			/*选择配送方式*/
			tabFunc(e) {
				if (this.options.order_type == 'retainage') {
					return
				}
				this.tab_type = e;
				if (e == 0) {
					this.delivery = 10;
				} else {
					this.delivery = 20;
				}

				this.getData();
			},

			/*提交订单*/
			SubmitOrder() {
				let self = this;
				if (this.confirm) {
					for (var i = 0; i < this.confirm.length; i++) {
						let self = this.confirm[i];
						if (self.status) {
							if (
								self.label === "text" ||
								self.label === "data" ||
								self.label === "time" ||
								self.label === "id"
							) {
								if (!self.value.trim()) {
									return uni.showToast({
										title: `请输入` + `${self.title}`,
										icon: "none",
									});
								}
							}
							if (self.label === "number") {
								if (self.value <= 0) {
									return uni.showToast({
										title: `请输入` + `${self.title}`,
										icon: "none",
									});
								}
							}
							if (self.label === "email") {
								if (
									!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(
										self.value
									)
								) {
									return uni.showToast({
										title: `请输入正确的` + `${self.title}`,
										icon: "none",
									});
								}
							}
							if (self.label === "phone") {
								if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(self.value)) {
									return uni.showToast({
										title: `请输入正确的` + `${self.title}`,
										icon: "none",
									});
								}
							}
							if (self.label === "img") {
								if (!self.value.length > 0) {
									return uni.showToast({
										title: `请上传` + `${self.title}`,
										icon: "none",
									});
								}
							}
						}
					}
				}
				uni.showLoading({
					title: '加载中',
					mask: true
				});
				let _linkman = self.last_extract.linkman;
				let _phone = self.last_extract.phone;

				let params = {
					delivery: self.delivery,
					store_id: self.store_id.store_id || 0,
					coupon_id: self.coupon_id,
					is_use_points: self.is_use_points,
					phone: _phone,
					linkman: _linkman,
					remark: self.remark,
					custom_form: self.confirm,
					// pay_type: self.pay_type,
					// pay_source: self.getPlatform()
				};

				// 创建订单-直接下单
				let url = '';
				if (self.options.order_type === 'buy') {
					url = 'order.order/buy';
					params = Object.assign(params, {
						product_id: self.options.product_id,
						product_num: self.options.product_num,
						product_sku_id: self.options.product_sku_id
					});
				}
				//定金
				if (self.options.order_type === 'deposit') {
					url = 'plus.advance.Order/frontBuy';
					params = Object.assign(params, {
						product_id: self.options.product_id,
						product_num: self.options.product_num,
						product_sku_id: self.options.product_sku_id,
						advance_product_sku_id: self.options.advance_product_sku_id,
						advance_product_id: self.options.advance_product_id
					});
				}
				//尾款
				if (self.options.order_type === 'retainage') {
					url = 'plus.advance.Order/buy'
					params = Object.assign(params, {
						order_id: self.options.order_id
					});
				}
				// 创建订单-购物车结算
				if (self.options.order_type === 'cart') {
					url = 'order.order/cart';
					params = Object.assign(params, {
						cart_ids: self.options.cart_ids || 0
					});
				}

				// 创建订单-积分兑换
				if (self.options.order_type === 'points') {
					url = 'plus.points.order/buy';
					params = Object.assign(params, {
						point_product_id: self.options.point_product_id,
						product_sku_id: self.options.product_sku_id,
						point_product_sku_id: self.options.point_product_sku_id,
						product_num: self.options.product_num
					});
				}
				// 创建订单-限时秒杀
				if (self.options.order_type === 'seckill') {
					url = 'plus.seckill.order/buy';
					params.num = self.options.num;
					params = Object.assign(params, {
						seckill_product_id: self.options.seckill_product_id,
						product_sku_id: self.options.product_sku_id,
						seckill_product_sku_id: self.options.seckill_product_sku_id,
						product_num: self.options.product_num
					});
				}
				// 创建订单-砍价
				if (self.options.order_type === 'bargain') {
					url = 'plus.bargain.order/buy';
					params = Object.assign(params, {
						bargain_product_id: self.options.bargain_product_id,
						product_sku_id: self.options.product_sku_id,
						bargain_product_sku_id: self.options.bargain_product_sku_id,
						bargain_task_id: self.options.bargain_task_id
					});
				}

				// 创建订单-限时拼团
				if (self.options.order_type === 'assemble') {
					url = 'plus.assemble.order/buy';
					params = Object.assign(params, {
						assemble_product_id: self.options.assemble_product_id,
						product_sku_id: self.options.product_sku_id,
						assemble_product_sku_id: self.options.assemble_product_sku_id,
						assemble_bill_id: self.options.assemble_bill_id,
						product_num: self.options.product_num,
					});
				}
				let callback = function() {
					self._post(url, params, function(res) {
						let pages = '/pages/order/cashier?order_id=' + res.data.order_id;
						if (self.options.order_type == 'deposit') {
							pages = '/pages/order/cashier?order_type=40&order_id=' + res.data.order_id;
						}
						self.gotoPage(pages, 'reLaunch');
					});
				};
				self.subMessage(self.temlIds, callback);
			},
			toDecimal2(x) {
				var f = parseFloat(x);
				if (isNaN(f)) {
					return "0.00";
				}
				var f = Math.round(x * 100)
				var n = Math.round(x * 1000)
				var r = n.toString().split("");
				r = r[r.length - 1];
				if (r >= 5) {
					f = (f - 1) / 100
				} else {
					f = f / 100
				}
				var s = f.toString();
				var rs = s.indexOf('.');
				if (rs < 0) {
					rs = s.length;
					s += '.';
				}
				while (s.length <= rs + 2) {
					s += '0';
				}
				return s;
			},
			closeCheckoutFunc() {
				this.isCheckout = false;
			},
		}
	};
</script>

<style lang="scss" scoped>
	page {
		background-color: #F2F2F2;
	}

	.f-d-c {
		flex-direction: column;
	}

	.wrap {
		padding-bottom: 110rpx;
	}

	.d-f-c {
		display: flex;
		justify-content: space-between;
		align-items: center;
		overflow: hidden;
		padding: 30rpx;
		border-bottom: 1rpx solid #D9D9D9;
	}

	.dfjac {
		display: flex;
		align-items: center;
	}

	.d-txar {
		white-space: nowrap;
		width: 200px;
		margin-right: 34rpx;
	}

	.extp {
		color: #9e9e9e;
		margin-left: 34rpx;
	}

	.vender .list .item {
		border-bottom: none;
	}

	.vender .top-title {
		padding: 30rpx 25rpx;
	}

	.icon-you {
		margin-left: 15rpx;
	}

	.icon-dianpu1 {
		margin-right: 15rpx;
		font-size: 28rpx;
		color: #333333;
	}

	.sup_itemtit {
		padding: 40rpx 30rpx;
		display: flex;
		align-items: center;
	}

	.vender .total-box {
		height: 87rpx;
		line-height: 87rpx;
		border-bottom: 16rpx solid #f2f2f2;
	}

	.d-f {
		display: flex;
	}

	.zt {
		padding: 2rpx 10rpx;
		margin-right: 10rpx;
		background: #E2231A;
		color: #FFFFFF;
		border-radius: 8rpx;
		font-size: 22rpx;
	}

	.icon-box .icon-zhifubao {
		color: #1296db;
		font-size: 50rpx;
	}

	.icon-dianpu1 {}

	.text_area {
		width: 100%;
		height: 120rpx;
		background: #f2f2f2;
		border-radius: 6rpx;
		padding: 20rpx;
		box-sizing: border-box;
		font-size: 24rpx;
	}

	.icon-xuanze {
		font-size: 38rpx;
	}

	.tab-item {
		font-size: 28rpx;
		color: #333333;
		font-weight: 400;
	}

	.tab-item.active {
		font-size: 28rpx;
		color: #333333;
		font-weight: 400;
	}

	.tab-item.active::after {
		width: 115rpx;
		height: 6rpx;
	}

	.hascoupon {
		padding: 0 18rpx;
		box-sizing: border-box;
		height: 38rpx;
		line-height: 38rpx;
		border-radius: 200rpx;
		color: #FFFFFF;
		background: linear-gradient(180deg, #FC4133, #FF7A04);
		font-size: 20rpx;

		.icon.icon-you {
			color: #FFFFFF;
			font-size: 20rpx;
			margin-left: 2rpx;
		}
	}

	.buy-checkout {
		.item {
			padding: 16rpx 30rpx;
			border-bottom: none;
		}
	}

	.buy-checkout .item {
		padding: 30rpx 0;
		margin: 0 30rpx;
		border-bottom: 1px solid #eee;
	}

	.buy-checkout .iconfont.icon-jiantou {
		font-size: 24rpx;
		color: #333;
	}

	.vender .list .gift {
		.info {
			height: 84rpx;
			padding-left: 0;
		}

		.cover {
			width: 84rpx;
			height: 84rpx;
			margin-right: 16rpx;

			image {
				width: 84rpx;
				height: 84rpx;
				border-radius: 15rpx;
			}
		}
	}

	.gift-tips {
		display: inline-block;
		border: 1px solid #F83D3D;
		color: #F83D3D;
		margin-right: 13rpx;
		width: 72rpx;
		height: 36rpx;
		line-height: 36rpx;
		font-size: 24rpx;
		text-align: center;
	}
</style>