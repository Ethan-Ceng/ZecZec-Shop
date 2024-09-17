<template>
	<view :data-theme="theme()" :class="theme() || ''">
		<diy style="position: relative;" :diyItems="items" :userInfo="{
					detail,
					coupon,
					orderCount
				}">
			<view class="bind_phone" v-if="getPhone">
				<view class="bind_content">
					<view class="bind_txt">确保账户安全，请绑定手机号</view>
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="getPhoneNumber" class="bind_btn" @getphonenumber="getPhoneNumber"
						v-if="wxBinding">去绑定</button>
					<button class="bind_btn" v-else @click="bindMobile">去绑定</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<button class="bind_btn" @click="bindMobile">去绑定</button>
					<!-- #endif -->
				</view>
			</view>
		</diy>
		<tabBar></tabBar>
	</view>
</template>

<script>
	import diy from '@/components/diy/diy.vue';
	export default {
		components: {
			diy
		},
		data() {
			return {
				items: [],
				/*是否加载完成*/
				loadding: true,
				detail: {
					balance: 0,
					points: 0,
					grade: {
						name: ''
					}
				},
				orderCount: {},
				coupon: 0,
				sessionKey: '',
				wxBinding: false,
				getPhone:false
			};
		},
		onShow() {
			/*获取个人中心数据*/
			this.getData();
		},
		onReady() {
			uni.hideTabBar();
		},
		onLoad(e) {
			let self = this;
			self.wxBinding = uni.getStorageSync('wxBinding');
			if (e.invitation_id) {
				uni.setStorageSync('invitation_id', e.invitation_id);
			}
			if (e.referee_id) {
				uni.setStorageSync('referee_id', e.referee_id);
			}
			//#ifdef MP-WEIXIN
			wx.login({
				success(res) {
					// 发送用户信息
					self._post(
						'user.user/getSession', {
							code: res.code
						},
						result => {
							self.sessionKey = result.data.session_key;
						}
					);
				}
			});
			//#endif
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				self._post(
					'user.index/detail', {
						source: self.getPlatform()
					},
					function(res) {
						self.getPage();
						self.getPhone = res.data.getPhone;
						// if (res.data.getPhone) {
						// 	//#ifdef MP-WEIXIN
						// 	self.gotoPage('/pages/login/bindmobile');
						// 	//#endif
						// 	//#ifndef MP-WEIXIN
						// 	self.bindMobile();
						// 	//#endif
						// 	return;
						// }
						self.detail = res.data.userInfo;
						self.coupon = res.data.coupon;
						self.orderCount = res.data.orderCount;
					}
				);
			},
			/*获取数据*/
			getPage() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._post('user.index/center', {}, function(res) {
					self.items = res.data.page.items;
					self.setPage(res.data.page.page);
					self.loadding = false;
					uni.hideLoading();
				});
			},
			/*设置页面*/
			setPage(page) {
				uni.setNavigationBarTitle({
					title: page.params.name
				});
			},
			bindMobile() {
				this.gotoPage('/pages/user/modify-phone/modify-phone');
			},
			getPhoneNumber(e) {
				var self = this;
				console.log(e.detail);
				if (e.detail.errMsg !== 'getPhoneNumber:ok') {
					return false;
				}
				uni.showLoading({
					title: '正在提交',
					mask: true
				});
				// 发送用户信息
				self._post(
					'user.user/bindMobile', {
						session_key: self.sessionKey,
						encrypted_data: e.detail.encryptedData,
						iv: e.detail.iv
					},
					result => {
						uni.showToast({
							title: '绑定成功'
						});
						// 执行回调函数
						self.detail.mobile = result.data.mobile;
					},
					false,
					() => {
						uni.hideLoading();
					}
				);
			}
		}
	};
</script>

<style lang="scss">
	page {
		background-color: #ebebeb;
	}

	.w100 {
		width: 100%;
	}

	.bind_phone {
		width: 100%;
		height: 80rpx;
		padding: 0 20rpx;
		box-sizing: border-box;
		margin-top: 30rpx;
	}

	.bind_content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		background: #ffffff;
		/* box-shadow: 0 0 6rpx 0 rgba(0, 0, 0, 0.1); */
		border-radius: 16rpx;
		height: 100%;
		padding: 0 20rpx;
	}

	.bind_txt {}

	.bind_btn {
		width: 134rpx;
		height: 50rpx;
		line-height: 50rpx;
		font-size: 22rpx;
		border-radius: 25rpx;
		text-align: center;
		color: #ffffff;
		@include background_color('background_color');
	}
</style>