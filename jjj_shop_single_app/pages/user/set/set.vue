<template>
	<view class="address-form o-h" :data-theme='theme()' :class="theme() || ''">
		<view class="avatar-box">
			<!-- #ifndef MP-WEIXIN -->
			<view class="info-image" @click="changeAvatarUrl">
				<image class="ava-image" :src="userInfo.avatarUrl || '/static/login-default.png'" mode=""></image>
				<view class="icon iconfont icon-shangchuantupian_f"></view>
			</view>
			<!-- #endif -->
			<!-- #ifdef MP-WEIXIN -->
			<button class="info-image" open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
				<image class="ava-image" :src="userInfo.avatarUrl || '/static/login-default.png'" mode=""></image>
				<view class="icon iconfont icon-shangchuantupian_f"></view>
			</button>
			<!-- #endif -->
		</view>
		<view class="user-info bg-white f26">
			<view class="d-b-c ">
				<text class="key-name">会员ID</text>
				<view class="userinfo-text border-b">{{userInfo.user_id}}</view>
			</view>
			<view class="d-b-c">
				<text class="key-name">昵称</text>
				<input class="userinfo-input border-b" placeholder="请输入昵称" placeholder-class="gray9" type="text"
					v-model="userInfo.nickName" />
			</view>
			<view class="d-b-c" @click="isPhoneOpen" v-if="userInfo.mobile">
				<text class="key-name">手机</text>
				<view class="d-s-c border-b flex-1">
					<text class="userinfo-text">{{ maskPhone(userInfo.mobile) }}</text>
					<text class="theme-notice dominant">修改</text>
				</view>
			</view>
			<view class="d-b-c">
				<view class="key-name">性别</view>
				<radio-group class="userinfo-text border-b d-s-c" @change="changeGender">
					<label class="d-s-c make-item mr20">
						<view>
							<radio style="transform:scale(0.7)" :color='getThemeColor()' :checked="userInfo.gender == 1"
								:value="1" />
						</view>
						<view class="f26 color-57">男</view>
					</label>
					<label class="d-s-c make-item">
						<view>
							<radio style="transform:scale(0.7)" :color='getThemeColor()' :checked="userInfo.gender == 0"
								:value="0" />
						</view>
						<view class="f26 color-57">女</view>
					</label>
				</radio-group>
			</view>
			<view class="d-b-c" @click="isPasswordOpen" v-if="userInfo.password">
				<text class="key-name">密码</text>
				<view class="userinfo-text d-b-c">
					<text class="gray9">已设置</text>
					<text class="theme-notice dominant">修改</text>
				</view>
			</view>
			<!-- #ifdef APP-PLUS -->
			<view class="d-b-c">
				<text class="key-name">当前版本</text>
				<view class="userinfo-text d-b-c">
					<text class="gray9">version_no</text>
				</view>
			</view>
			<view class="d-b-c"  @click="deleteAccount()">
				<text class="key-name">删除账号</text>
				<view class="userinfo-text d-b-c">
					<text class="icon iconfont icon-jiantou"></text>
				</view>
			</view>
			<!-- #endif -->
		</view>
		<view class="pop-box" v-if="isPhone">
			<view class="pop-bg" @click.stop="isPhone = false"></view>
			<view class="pop-content">
				<view class="icon iconfont icon-guanbi1 close-btn" @click.stop="isPhone = false"></view>
				<view class="pop-title">温馨提示</view>
				<view class="pop-dsc">获取手机号作为用户入会的唯一身份标识,请确定新手机号没有绑定本平台</view>
				<view class="pop-formitem">
					<input class="flex-1 pop-formitem-input" type="text" v-model="mobileModel.mobile"
						placeholder="请填写手机号" />
				</view>
				<view class="pop-formitem" v-if="sms_open">
					<input class="flex-1 pop-formitem-input" type="number" v-model="mobileModel.code"
						placeholder="请填写验证码" />
					<button class="get-code-btn" @click.stop="sendCode('mobileModel')"
						:disabled="is_send">{{ send_btn_txt }}</button>
				</view>
				<view class="pop-sub-btn theme-btn" @click.stop="changePhone">确定</view>
			</view>
		</view>
		<view class="pop-box" v-if="isPassword">
			<view class="pop-bg" @click.stop="isPassword = false"></view>
			<view class="pop-content">
				<view class="icon iconfont icon-guanbi1 close-btn" @click.stop="isPassword = false"></view>
				<view class="pop-title">密码修改</view>
				<view class="pop-formitem">
					<view class="flex-1 pop-formitem-input">{{maskPhone(passwordModel.mobile)}}</view>
				</view>
				<view class="pop-formitem" v-if="sms_open">
					<input class="flex-1 pop-formitem-input" type="number" v-model="passwordModel.code"
						placeholder="请填写验证码" />
					<button class="get-code-btn" @click.stop="sendCode('passwordModel')"
						:disabled="is_send">{{ send_btn_txt }}</button>
				</view>
				<view class="pop-formitem">
					<input class="flex-1 pop-formitem-input" type="text" password v-model="passwordModel.password"
						placeholder="请输入密码" />
				</view>
				<view class="pop-formitem">
					<input class="flex-1 pop-formitem-input" type="text" password v-model="passwordModel.repassword"
						placeholder="请再次输入密码" />
				</view>
				<view class="pop-sub-btn theme-btn" @click.stop="changePassword">确定</view>
			</view>
		</view>
		<view class="sub-btn theme-btn" @click="update">保存</view>
		<view class="setup-btn" @tap="logout()">退出登录</view>
		<!-- 上传头像 -->
		<Upload v-if="isUpload" :num="1" @getImgs="getImgsFunc"></Upload>
	</view>
</template>

<script>
	import Popup from '@/components/uni-popup.vue';
	import Upload from '@/components/upload/upload.vue';
	import {
		gotopage
	} from '@/common/gotopage.js';
	export default {
		components: {
			Popup,
			Upload
		},
		data() {
			return {
				userInfo: {},
				isPopup: false,
				imageList: [],
				newName: '',
				type: '',
				isUpload: false,
				mobileModel: {
					mobile: '',
					code: ''
				},
				passwordModel: {
					mobile: '',
					code: '',
					password: '',
					repassword: ''
				},
				/*是否已发验证码*/
				is_send: false,
				/*发送按钮文本*/
				send_btn_txt: '获取验证码',
				/*当前秒数*/
				second: 60,
				isPhone: false,
				isPassword: false,
				sms_open: false
			};
		},
		onShow() {
			/*获取个人中心数据*/
			this.getData();
			this.getCodeType();
		},
		methods: {
			clearStorage() {
				uni.clearStorageSync();
			},
			getCodeType() {
				let self = this;
				self._post(
					'index/loginSetting', {},
					res => {
						self.sms_open = res.data.setting.sms_open;
					},
				);

			},
			maskPhone(phone) {
				if (!phone || phone.length !== 11) return phone; // 确保电话号码有效且长度为11位
				return phone.replace(/(\d{3})\d{4}(\d{4})/, '$1***$2');
			},
			isPasswordOpen() {
				this.isPassword = true;
				this.passwordModel = {
					mobile: this.userInfo.mobile,
					code: '',
					password: '',
					repassword: ''
				}
			},
			isPhoneOpen() {
				// if (this.userInfo.reg_source == 'mp' || this.userInfo.reg_source == 'wx') {
				// 	return
				// }
				this.isPhone = true;
				this.mobileModel = {
					mobile: '',
					code: ''

				}
			},
			changePassword() {
				let self = this;
				let params = self.passwordModel;
				if (!params.mobile) {
					uni.showToast({
						title: '请输入手机号',
						icon: 'none'
					})
					return
				}
				if (self.sms_open && !params.code) {
					uni.showToast({
						title: '请输入验证码',
						icon: 'none'
					})
					return
				}
				if (!params.password) {
					uni.showToast({
						title: '请输入密码',
						icon: 'none'
					})
					return
				}
				if (params.password.length < 6) {
					uni.showToast({
						title: '请输入6位以上的密码',
						icon: 'none'
					})
					return
				}
				if (params.password != params.repassword) {
					uni.showToast({
						title: '两次密码输入不一致',
						icon: 'none'
					})
					return
				}
				self._post('user.Useropen/changePassword', params, res => {
					uni.showModal({
						title: '提示',
						content: '修改成功',
						success() {
							self.isPassword = false;
							self.getData();
						}
					})
				})
			},
			changePhone() {
				let self = this;
				let params = self.mobileModel;
				if (self.sms_open && !params.code) {
					uni.showToast({
						title: '请输入验证码',
						icon: 'none'
					})
					return
				}
				self._post('user.Useropen/changeMobile', params, res => {
					uni.showModal({
						title: '提示',
						content: '修改成功',
						success() {
							self.isPhone = false;
							self.getData();
						}
					})
				})
			},
			changeName(type) {
				let self = this;
				if (type == 'mobile') {
					self.oldmobile = self.userInfo.mobile;
				}
				self.type = type;
				self.newName = self.userInfo[type];
				self.isPopup = true;
			},
			onChooseAvatar(e) {
				let self = this;
				console.log(e);
				self.uploadFile([e.detail.avatarUrl]);
			},
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._get('user.index/setting', {}, function(res) {
					self.userInfo = res.data.userInfo;
					uni.hideLoading();
				});
				// #ifdef APP-PLUS
				plus.runtime.getProperty(plus.runtime.appid, function(widgetInfo) {
					self.version_no = widgetInfo.version;
				});
				// #endif
			},
			gotoBind() {
				uni.navigateTo({
					url: '/pages/user/modify-phone/modify-phone'
				});
			},

			/* 修改头像 */
			changeAvatarUrl() {
				let self = this;
				self.isUpload = true;
			},
			changeinput(e) {
				this.newName = e.target.value;
			},
			changeGender(e) {
				this.userInfo.gender = e.detail.value;
			},
			subName(e) {
				let self = this;
				if (self.loading) {
					return
				}
				if (self.type != 'gender') {
					self.newName = e.detail.value.newName;
				}
				self.userInfo[self.type] = this.newName;
				self.update()

			},
			/*上传图片*/
			uploadFile: function(tempList) {
				let self = this;
				self.imageList = [];
				let i = 0;
				let img_length = tempList.length;
				let params = {
					token: uni.getStorageSync('token'),
					app_id: self.getAppId()
				};
				uni.showLoading({
					title: '图片上传中'
				});
				tempList.forEach(function(filePath, fileKey) {
					uni.uploadFile({
						url: self.websiteUrl + '/index.php?s=/api/file.upload/image',
						filePath: filePath,
						name: 'iFile',
						formData: params,
						success: function(res) {
							let result = typeof res.data === 'object' ? res.data : JSON.parse(res
								.data);
							if (result.code === 1) {
								self.imageList.push(result.data);
							} else {
								self.showError(result.msg);
							}
						},
						complete: function() {
							i++;
							if (img_length === i) {
								uni.hideLoading();
								// 所有文件上传完成
								self.getImgsFunc(self.imageList);
							}
						}
					});
				});
			},
			/*获取上传的图片*/
			getImgsFunc(e) {
				let self = this;
				if (e && typeof e != 'undefined') {
					let self = this;
					self.userInfo.avatarUrl = e[0].file_path;
					self.update();
					self.isUpload = false;
				}
			},
			hidePopupFunc() {
				this.isPopup = false;
			},
			logout() {
				let self = this;
				self._post('/user.User/logOut', {}, res => {
					uni.removeStorageSync('token');
					uni.removeStorageSync('user_id');
					self.gotoPage('/pages/index/index');
				})

			},
			update() {
				let self = this;
				if (self.loading) {
					return;
				}
				uni.showLoading({
					title: '加载中'
				});
				let params = self.userInfo;
				self.loading = true;
				self._post('user.user/updateInfo', params, function(res) {
					self.showSuccess(
						'修改成功',
						function() {
							self.loading = false;
							self.isPopup = false;
							uni.hideLoading();
							self.getData();
						},
						function(err) {
							uni.hideLoading();
							self.loading = false;
							self.isPopup = false;
						}
					);
				});
			},
			/*发送短信*/
			sendCode(name) {
				let self = this;
				let model = this[name];
				if (!/^1(3|4|5|6|7|8|9)\d{9}$/.test(model.mobile)) {
					uni.showToast({
						title: '手机有误,请重填！',
						duration: 2000,
						icon: 'none'
					});
					return;
				}
				let type = 'login'
				if (name == 'mobileModel') {
					type = 'register'
				}
				self._post(
					'user.userweb/sendCode', {
						mobile: model.mobile,
						type
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
			deleteAccount() {
				let self = this;
				uni.showModal({
					title: '提示',
					content: '是否确认删除账号？删除后您将无法用此账号登录，此账户下的数据也将删除',
					success: function(res) {
						if (res.confirm) {
							self._post('user.user/deleteAccount', {}, result => {
								self.showSuccess('删除成功', () => {
									uni.removeStorageSync('token');
									uni.removeStorageSync('user_id');
									self.gotoPage('/pages/index/index');
								});
							}, false, () => {
								uni.hideLoading();
							});
						}
					}
				})
			},
		}
	};
</script>

<style lang="scss">
	.avatar-box {
		height: 361rpx;
		background: #FFFFFF;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;

		.info-image {
			position: relative;
			width: 198rpx;
			height: 198rpx;
			padding: 0;
			background: none;
			border: none;
			border-radius: 50%;
			overflow: initial;

			.ava-image {
				width: 198rpx;
				height: 198rpx;
				border-radius: 50%;
			}

			.icon.iconfont {
				position: absolute;
				right: 0;
				bottom: 0;
				z-index: 1;
				width: 46rpx;
				height: 46rpx;
				border-radius: 50%;
				background: rgba(#000000, .55);
				color: #fff;
				font-size: 28rpx;
				display: flex;
				justify-content: center;
				align-items: center;
			}
		}
	}

	.user-info {
		margin-top: 22rpx;
		padding: 0 28rpx 0 34rpx;
		margin-bottom: 36rpx;

		.key-name {
			width: 134rpx;
		}

		.userinfo-text,
		.userinfo-input {
			flex: 1;
			height: 98rpx;
			line-height: 98rpx;
			font-size: 26rpx;
			color: #333;
		}
	}

	.sub-btn {
		width: 692rpx;
		height: 88rpx;
		line-height: 88rpx;
		border-radius: 44rpx;
		font-size: 30rpx;
		display: flex;
		justify-content: center;
		margin: 0 auto;
		font-weight: bold;
	}

	.setup-btn {
		position: fixed;
		bottom: 43rpx;
		width: 100%;
		font-size: 26rpx;
		color: #999;
		text-align: center;
	}

	.make-item {
		height: 60rpx;
	}

	.pop-input {
		width: 100%;
		margin: 26rpx 0;
		box-sizing: border-box;
		border-bottom: 2rpx solid #d9d9d9;
		line-height: 60rpx;
	}

	.pop-input input {
		width: 100%;
		padding-left: 15rpx;

		font-size: 26rpx;
		color: #333333;
		margin: 16rpx 0;
		text-align: left;
		height: 60rpx;
		line-height: 60rpx;
	}

	.pop-input .icon.icon-guanbi {
		width: 38rpx;
		height: 38rpx;
		background-color: #999999;
		color: #ffffff;
		font-size: 22rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 50%;
		opacity: 0.8;
	}

	.info-image {
		width: 112rpx;
		height: 112rpx;
		border-radius: 10rpx;
		margin-right: 20rpx;

		image {
			width: 112rpx;
			height: 112rpx;
			border-radius: 10rpx;
		}
	}

	.btns {
		border-radius: 44rpx;
		min-width: 220rpx;
	}



	.pop-box {
		position: fixed;
		left: 0;
		right: 0;
		top: 0;
		bottom: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		z-index: 995;

		.pop-bg {
			position: fixed;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 1000;
			background: rgba(#000, .65);
		}

		.pop-content {
			width: 578rpx;
			background: #FFFFFF;
			border-radius: 25rpx;
			padding: 45rpx 40rpx 40rpx 40rpx;
			box-sizing: border-box;
			position: relative;
			z-index: 1001;

			.close-btn {
				width: 44rpx;
				height: 44rpx;
				background: rgba(#000000, .45);
				border-radius: 50%;
				color: #fff;
				font-size: 26rpx;
				display: flex;
				justify-content: center;
				align-items: center;
				position: absolute;
				right: 17rpx;
				top: 17rpx;
				z-index: 1;
			}

			.pop-title {
				font-size: 30rpx;
				color: #333333;
				text-align: center;
				margin-bottom: 34rpx;
				font-weight: bold;
			}

			.pop-dsc {
				color: #666666;
				font-size: 24rpx;
				line-height: 1.5;
			}

			.pop-formitem {
				border-bottom: 1px solid #eee;
				display: flex;
				justify-content: center;
				align-items: center;
				padding-top: 38rpx;
				height: 74rpx;

				.pop-formitem-input {
					height: 74rpx;
					font-size: 26rpx;
				}

				.get-code-btn {
					min-width: 182rpx;
					height: 58rpx;
					line-height: 58rpx;
					padding: 0rpx 30rpx;
					border-radius: 50rpx;
					white-space: nowrap;
					background-color: #ffffff;
					@include font_color('notice-price');
					font-size: 26rpx;
					box-sizing: border-box;
				}

				.get-code-btn[disabled='true'] {
					background: #f7f7f7;
				}
			}

			.pop-sub-btn {
				width: 498rpx;
				height: 88rpx;
				border-radius: 44rpx;
				font-size: 30rpx;
				display: flex;
				justify-content: center;
				align-items: center;
				margin-top: 60rpx;
				font-weight: bold;
			}
		}
	}
</style>