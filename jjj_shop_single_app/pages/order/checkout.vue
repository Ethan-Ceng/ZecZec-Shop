<template>
	<view :class="Visible ? 'usable-coupon open' : 'usable-coupon close'">
		<view class="popup-bg" @click="closePopup"></view>
		<view class="main pt30" v-on:click.stop>
			<!--支付方式-->
			<view class="buy-checkout br6">
				<view v-if="hasType(20)" :class="pay_type == 20 ? 'item active' : 'item'" @click="payTypeFunc(20)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><text class="icon iconfont icon-weixin"></text></view>
						<text class="key">微信支付：</text>
					</view>
					<view class="icon-box d-c-c"><text class="icon iconfont icon-xuanze"></text></view>
				</view>
				<view v-if="hasType(30)" :class="pay_type == 30 ? 'item active' : 'item'" @click="payTypeFunc(30)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><text class="icon iconfont icon-zhifubao"></text></view>
						<text class="key">支付宝支付：</text>
					</view>
					<view class="icon-box d-c-c"><text class="icon iconfont icon-xuanze"></text></view>
				</view>
				<view v-if="balance > 0" :class="use_balance == 1 ? 'item active' : 'item'" @click="useBalance">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><text class="icon iconfont icon-yue"></text></view>
						<text class="key">余额：</text><text class="theme-price">{{'(可用余额:'+balance+')'}}</text>
					</view>
					<view class="icon-box d-c-c"><text class="icon iconfont icon-xuanze"></text></view>
				</view>
			</view>
			<view>
				<button @click="payFunc">{{pay_type_str}}¥{{show_pay_price}}</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {pay} from '@/common/pay.js';
	export default {
		data() {
			return {
				/*是否可见*/
				Visible: false,
				pay_type: '',
				payTypes: [],
				balance: 0,
				payPrice: '',
				pay_type_str : '',
				use_balance: 0,
				show_pay_price: ''
			};
		},
		props: ['isCheckout', 'order_id', 'order_type'],

		onLoad() {},
		mounted() {
			
		},
		watch: {
			isCheckout: function(n, o) {
				if (n != o) {
					this.Visible = n;
					this.getData();
				}
			}
		},
		methods: {	
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._get(
					'order.checkout/pay', {
						payType: 20,
						order_id: self.order_id,
						pay_source: self.getPlatform()
					},
					function(res) {
						uni.hideLoading();
						self.payTypes = res.data.payTypes;
						self.payPrice = res.data.payPrice;
						self.show_pay_price = res.data.payPrice;
						self.balance = res.data.balance;
						for(let i=0;i<self.payTypes.length;i++){
							self.payTypes[i] = parseInt(self.payTypes[i]);
						}
						// 默认选中
						if(self.hasType(20)){
							self.pay_type = 20;
						}else if(self.hasType(30)){
							self.pay_type = 30;
						}else{
							self.pay_type = 10;
						}
						self.showPayTypeStr();
					}
				);
			},
			/*去支付*/
			payFunc() {
				let self = this;
				self.isPayPopup = false;
				uni.showLoading({
					title: '加载中'
				});
				self._post(
					'order.checkout/pay', {
						pay_type: self.pay_type,
						order_id: self.order_id,
						pay_source: self.getPlatform(),
						use_balance: self.use_balance
					},
					function(res) {
						pay(res, self);
					}
				);
			},
			/**/
			hasType(e) {
				if (this.payTypes.indexOf(e) != -1) {
					return true;
				} else {
					return false;
				}
			},
			payTypeFunc(e){
				this.pay_type = e;
				this.showPayTypeStr();
			},
			useBalance(){
				if(this.use_balance == 0){
					this.use_balance = 1;
					this.show_pay_price = this.payPrice - this.balance;
					if(this.show_pay_price < 0){
						this.show_pay_price = 0;
					}
				}else{
					this.use_balance = 0;
					this.show_pay_price = this.payPrice;
				}
			},
			showPayTypeStr(){
				if(this.pay_type == 10){
					this.pay_type_str = '余额支付';
				}else if(this.pay_type == 20){
					this.pay_type_str = '微信支付';
				}else if(this.pay_type == 30){
					this.pay_type_str = '支付宝支付';
				}
			},
			
			/*关闭弹窗*/
			closePopup(e) {
				this.$emit('close', e);
			}
		}
	};
</script>

<style lang="scss" scoped>
	.usable-coupon .popup-bg {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background: rgba(0, 0, 0, 0.6);
		z-index: 99;
	}

	.usable-coupon .main {
		position: fixed;
		width: 100%;
		bottom: 0;
		min-height: 200rpx;
		max-height: 900rpx;
		background-color: #fff;
		transform: translate3d(0, 980rpx, 0);
		transition: transform 0.2s cubic-bezier(0, 0, 0.25, 1);
		bottom: env(safe-area-inset-bottom);
		z-index: 99;
	}

	.usable-coupon .main {
		position: fixed;
		width: 100%;
		bottom: 0;
		min-height: 200rpx;
		max-height: 900rpx;
		background-color: #fff;
		transform: translate3d(0, 980rpx, 0);
		transition: transform 0.2s cubic-bezier(0, 0, 0.25, 1);
		bottom: env(safe-area-inset-bottom);
		z-index: 99;
	}

	.usable-coupon.open .main {
		transform: translate3d(0, 0, 0);
	}

	.usable-coupon.close .popup-bg {
		display: none;
	}

	.coupon-item-red .operation {
		/* background: #fdf1df; */
	}

	.coupon-btns .btn-cancel {
		height: 88rpx;
		line-height: 88rpx;
		font-size: 30rpx;
		background: #999999;
		color: #ffffff;
		border-radius: 0;
	}

	.coupon-item .w100 {
		padding: 0 75rpx;
	}

	.b-radio {
		border: 1rpx solid #FFFFFF;
		border-radius: 30rpx;
		padding: 6rpx 20rpx;
	}
</style>