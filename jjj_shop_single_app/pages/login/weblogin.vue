<template>
	<view class="login-container" :data-theme="theme()" :class="theme() || ''">
		<view class="skip" @click="gotoPage('/pages/index/index')">跳过→</view>
		<view class="" v-if="is_login!=0">
			<view class="login_topbpx">
				<view class="login_tit" v-if="is_login==2">快速登录</view>
				<view class="login_tit" v-if="is_login==1">账号登录</view>
				<view class="login_top" v-if="is_login==2">首次登录会自动注册</view>
			</view>
			<view class="group-bd">
				<view class="form-level d-s-c">
					<input class="login-input" :adjust-position="false" type="text" v-model="formData.mobile"
						placeholder="请填写手机号" :disabled="is_send" />
				</view>
				<view class="form-level d-s-c" v-if="is_login==1">
					<input class="login-input" :adjust-position="false" type="text" password="true"
						v-model="formData.password" placeholder="请输入密码" />
					<button class="get-code-btn" @click="is_login = 0">忘记密码？</button>
				</view>
				<view class="form-level d-b-c" v-if="is_login==2&&sms_open">
					<input class="login-input" type="number" v-model="formData.code" placeholder="请填写验证码" />
					<button class="get-code-btn" @click="sendCode" :disabled="is_send">{{ send_btn_txt }}</button>
				</view>
			</view>
		</view>
		<view class="" v-if="is_login==0">
			<view class="login_topbpx">
				<view class="login_tit">找回密码</view>
				<view class="login_top">请确认您的账号已绑定此手机号码</view>
			</view>
			<view class="group-bd">
				<view class="form-level d-s-c">
					<view class="val flex-1"><input type="text" v-model="resetpassword.mobile" placeholder="请填写手机号"
							:disabled="is_send" /></view>
				</view>
				<view class="form-level d-s-c">
					<view class="val flex-1 d-b-c">
						<input class="flex-1" :adjust-position="false" type="number" v-model="resetpassword.code"
							placeholder="请填写验证码" />
						<button class="get-code-btn" @click="sendCode" :disabled="is_send">{{ send_btn_txt }}</button>
					</view>
				</view>
				<view class="form-level d-s-c">
					<view class="val flex-1"><input type="text" :adjust-position="false" password="true"
							v-model="resetpassword.password" placeholder="请输入密码" /></view>
				</view>
				<view class="form-level d-s-c">
					<view class="val flex-1"><input type="text" :adjust-position="false" password="true"
							v-model="resetpassword.repassword" placeholder="请再次输入密码" /></view>
				</view>
			</view>
		</view>
		<view @click="isRead=!isRead" class="d-s-c gray6 mt30">
			<view class="icon iconfont icon-tijiaochenggong" :class="isRead?'active agreement':'agreement'"></view>我已阅读并接受<text class="dominant" @click="xieyi('service')">《用户协议》</text>和<text class="dominant"
				@click="xieyi('privacy')">《隐私政策》</text>
		</view>
		<button v-if="is_login==2" class="theme-btn sub-btn" @click="formSubmit">立即登录</button>
		<button v-if="is_login==1" class="theme-btn sub-btn" @click="formSubmit">立即登录</button>
		<button v-if="is_login==0" class="theme-btn sub-btn" @click="resetpasswordSub">确认</button>
		<view class="f30 gray6 d-c-c">
			<view v-if="is_login ==1 " @click="is_login =2">手机登录</view>
			<view v-if="is_login ==2" @click="is_login =1">账号登录</view>
			<view v-if="is_login ==0" @click="is_login =1">立即登录</view>
		</view>

	</view>
</template>

<script>
	import {
		gotopage
	} from '@/common/gotopage.js';
	export default {
		data() {
			return {
				/*表单数据对象*/
				formData: {
					mobile: '',
					password: '',
					/*验证码*/
					code: '',
				},
				resetpassword: {
					mobile: '',
					password: '',
					repassword: '',
					code: ''
				},
				/*是否已发验证码*/
				is_send: false,
				/*发送按钮文本*/
				send_btn_txt: '获取验证码',
				/*当前秒数*/
				second: 60,
				ip: '',
				is_login: 2,
				sms_open: false,
				isRead: false
			};
		},
		onLoad() {

		},
		onShow() {
			this.getCodeType();
		},
		methods: {
			xieyi(type) {
				this.gotoPage('/pages/webview/ue?type=' + type)
			},
			getCodeType() {
				let self = this;
				self._post(
					'index/loginSetting', {},
					res => {
						self.sms_open = res.data.setting.sms_open;
						if (!self.sms_open) {
							self.is_login = 1;
						}
					},
				);

			},
			/*提交*/
			formSubmit() {
				let self = this;
				if (!self.isRead) {
					uni.showToast({
						title: '请先阅读并接受用户协议及隐私政策',
						duration: 2000,
						icon: 'none'
					});
					return
				}
				let formdata = {
					mobile: self.formData.mobile,
					invitation_id: self.invitation_id || 0,
					referee_id: uni.getStorageSync('referee_id') || 0
				}
				let url = ''
				if (!/^1(3|4|5|6|7|8|9)\d{9}$/.test(self.formData.mobile)) {
					uni.showToast({
						title: '手机有误,请重填！',
						duration: 2000,
						icon: 'none'
					});
					return;
				}
				if (self.is_login == 2) {
					if (self.sms_open && self.formData.code == '') {
						uni.showToast({
							title: '验证码不能为空！',
							duration: 2000,
							icon: 'none'
						});
						return;
					}
					formdata.code = self.formData.code;
					url = 'user.useropen/smslogin'
				} else {
					formdata.password = self.formData.password;
					if (self.formData.password == '') {
						uni.showToast({
							title: '密码不能为空！',
							duration: 2000,
							icon: 'none'
						});
						return;
					}
					url = 'user.useropen/phonelogin'
				}

				uni.showLoading({
					title: '正在提交'
				});
				self._post(
					url,
					formdata,
					result => {
						// 记录token user_id
						uni.setStorageSync('token', result.data.token);
						uni.setStorageSync('user_id', result.data.user_id);
						// 获取登录前页面
						let url = '/' + uni.getStorageSync('currentPage');
						let pageOptions = uni.getStorageSync('currentPageOptions');
						if (Object.keys(pageOptions).length > 0) {
							url += '?';
							for (let i in pageOptions) {
								url += i + '=' + pageOptions[i] + '&';
							}
							url = url.substring(0, url.length - 1);
						}
						// 执行回调函数
						self.gotoPage(url, 'redirect');
					},
					false,
					() => {
						uni.hideLoading();
					}
				);
			},
			resetpasswordSub() {
				let self = this;
				if (!/^1(3|4|5|6|7|8|9)\d{9}$/.test(self.resetpassword.mobile)) {
					uni.showToast({
						title: '手机有误,请重填！',
						duration: 2000,
						icon: 'none'
					});
					return;
				}
				if (self.resetpassword.code == '') {
					uni.showToast({
						title: '验证码不能为空！',
						duration: 2000,
						icon: 'none'
					});
					return;
				}
				if (self.resetpassword.password.length < 6) {
					uni.showToast({
						title: '密码至少6位数！',
						duration: 2000,
						icon: 'none'
					});
					return;
				}
				if (self.resetpassword.password !== self.resetpassword.repassword) {
					uni.showToast({
						title: '两次密码输入不一致！',
						duration: 2000,
						icon: 'none'
					});
					return;
				}

				uni.showLoading({
					title: '正在提交'
				});
				self._post(
					'user.useropen/resetpassword',
					self.resetpassword,
					result => {
						uni.showToast({
							title: '重置成功',
							duration: 3000
						})
						self.formData.mobile = self.resetpassword.mobile;
						self.resetpassword = {
							mobile: '',
							password: '',
							repassword: '',
							code: ''
						};
						self.second = 0;
						self.changeMsg();
						self.is_login = 1;
					},
					false,
					() => {
						uni.hideLoading();
					}
				);
			},
			/*发送短信*/
			sendCode() {
				let self = this;
				if (self.is_login != 0) {
					if (!/^1(3|4|5|6|7|8|9)\d{9}$/.test(self.formData.mobile)) {
						uni.showToast({
							title: '手机有误,请重填！',
							duration: 2000,
							icon: 'none'
						});
						return;
					}
				} else if (self.is_login == 0) {
					if (!/^1(3|4|5|6|7|8|9)\d{9}$/.test(self.resetpassword.mobile)) {
						uni.showToast({
							title: '手机有误,请重填！',
							duration: 2000,
							icon: 'none'
						});
						return;
					}
				}

				let type = 'sms'
				let mobile = self.formData.mobile
				if (self.is_login == 0) {
					mobile = self.resetpassword.mobile;
					let type = 'login'
				}
				self._post(
					'user.useropen/sendCode', {
						mobile: mobile,
						type: type
					},
					result => {
						if (result.code == 1) {
							uni.showToast({
								title: '发送成功'
							});
							self.is_send = true;
							self.changeMsg();
						}
					}
				);
			},
			/*改变发送验证码按钮文本*/
			changeMsg() {
				if (this.second > 0) {
					this.send_btn_txt = this.second + '秒';
					this.second--;
					setTimeout(this.changeMsg, 1000);
				} else {
					this.send_btn_txt = '获取验证码';
					this.second = 60;
					this.is_send = false;
				}
			},
		}
	};
</script>

<style lang="scss" scoped>
	page {
		background: #ffffff;
	}

	@import './login.scss';
</style>