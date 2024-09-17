<template>
	<view :style="{
			background: itemData.style.bgcolor,
			padding: itemData.style.paddingTop + 'px ' + itemData.style.paddingLeft + 'px ' + itemData.style.paddingBottom + 'px ' + itemData.style.paddingLeft + 'px'
		}">
		<view class="drag optional" v-if="userInfo.detail">
			<view class="bg-box">
				<view class="d-s-c" style="z-index: 1;position: relative;margin-left: 20rpx;margin-top: 80rpx;"
					 @click="gotoPage('/pages/user/set/set')">
					<view class="item-image">
						<image :src="userInfo.detail.avatarUrl" mode=""></image>
					</view>
					<view class="d-c d-b-s white" style="height: 102rpx;">
						<view class="d-c-c ">
							<text class="fb f32">{{ userInfo.detail.nickName }}</text>
							<text class="ml20 grade"
								v-if="userInfo.detail.grade_id > 0">{{ userInfo.detail.grade.name }}</text>
						</view>
						<view>ID:{{ userInfo.detail.user_id }}</view>
					</view>
				</view>
				<view class="bg-base" :class="'bg-base-' + itemData.style.type"></view>
				<view class="diy-Base" :style="{ background: itemData.style.background }">
					<view class="list column-3">
						<view class="item" @click="gotoPage('/pages/user/my-wallet/my-wallet')">
							<view class="item-text fb f32">{{ userInfo.detail.balance }}</view>
							<view class="item-text text-ellipsis">账户余额</view>
						</view>
						<view class="item item-center" :style="'padding:0 ' + itemData.style.padding * 2 + 'rpx;'"
							@click="gotoPage('/pages/user/points/points')">
							<view class="item-text fb f32">{{ userInfo.detail.points }}</view>
							<view class="item-text text-ellipsis">{{ points_name() }}</view>
						</view>
						<view class="item" @click="gotoPage('/pages/user/my-coupon/my-coupon')">
							<view class="item-text fb f32">{{ userInfo.coupon }}</view>
							<view class="item-text text-ellipsis">优惠券</view>
						</view>
					</view>
				</view>
			</view>
			<slot />
		</view>
	</view>
</template>

<script>
	import Upload from '@/components/upload/upload.vue';
	export default {
		components: {
			Upload
		},
		data() {
			return {
				isPopup: false,
				avatarUrl: '',
				nickName: '',
				isUpload: false,
				imageList: []

			};
		},
		props: ['itemData', 'userInfo'],
		created() {},
		methods: {
			closeFunc() {
				this.isPopup = false;
			},
			changeInfo() {
				this.avatarUrl = this.userInfo.detail.avatarUrl;
				this.nickName = this.userInfo.detail.nickName;
				this.isPopup = true;
			},
			onChooseAvatar(e) {
				let self = this;
				self.uploadFile([e.detail.avatarUrl]);
			},
			/*跳转页面*/
			gotoDetail(e) {
				this.gotoPage(e.linkUrl);
			},
			changeinput(e) {
				this.nickName = e.detail.value;
			},
			/* 修改头像 */
			changeAvatarUrl() {
				let self = this;
				self.isUpload = true;
			},
			subName(e) {
				let self = this;
				if (self.loading) {
					return;
				}
				uni.showLoading({
					title: '加载中'
				});
				let params = {
					avatarUrl: self.avatarUrl,
					nickName: e.detail.value.nickName
				};
				self.loading = true;
				self._post('user.user/updateInfo', params, function(res) {
					self.showSuccess(
						'修改成功',
						function() {
							self.loading = false;
							self.isPopup = false;
							uni.hideLoading();
							// self.getData();
							self.userInfo.detail.avatarUrl = self.avatarUrl;
							self.userInfo.detail.nickName = self.nickName;
						},
						function(err) {
							uni.hideLoading();
							self.loading = false;
							self.isPopup = false;
						}
					);
				});
			},
			/*上传图片*/
			uploadFile: function(tempList) {
				let self = this;
				let i = 0;
				let img_length = tempList.length;
				let params = {
					token: uni.getStorageSync('token'),
					app_id: self.getAppId()
				};
				self.imageList = [];
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
							let result = typeof res.data === 'object' ? res.data : JSON.parse(
								res.data);
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
				self.isUpload = false;
				if (e && typeof e != 'undefined') {
					let self = this;
					self.avatarUrl = e[0].file_path;
					// self.update();
					self.isUpload = false;
				}
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
							self.userInfo.detail.avatarUrl = self.avatarUrl;
							self.userInfo.detail.nickName = self.nickName;
							// self.getData();
						},
						function(err) {
							uni.hideLoading();
							self.loading = false;
							self.isPopup = false;
						}
					);
				});
			},
		}
	};
</script>

<style lang="scss" scoped>
	.grade {
		display: block;
		padding: 0 16rpx;
		font-size: 22rpx;
		/* height: 36rpx; */
		line-height: 36rpx;
		border-radius: 40rpx;
		background-color: rgba($color: #000000, $alpha: 0.1);
		color: #ffffff;
	}

	.diy-Base {
		position: absolute;
		width: 710rpx;
		left: 0;
		right: 0;
		bottom: 0;
		margin: auto;
		padding: 0 20rpx;
		box-sizing: border-box;
		border-radius: 16rpx;
	}

	.diy-Base .list {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 100%;
	}

	.diy-Base .list .item {
		padding: 20rpx 0;
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
	}

	.diy-Base .list.column-3 .item {
		// width: 33.333333333%;
		width: 20%;
	}

	.diy-Base .list.column-4 .item {
		width: 25%;
	}

	.diy-Base .list.column-5 .item {
		width: 20%;
	}

	.diy-Base .list .item-image {
		width: 60%;
	}

	.diy-Base .list .item-image image {
		width: 100%;
	}

	.diy-Base .list .item-text {
		width: 100%;
		padding: 8rpx 0;
		text-align: center;
	}

	.bg-box {
		overflow: hidden;
		height: 410rpx;
		position: relative;
	}

	.bg-base {
		width: 160%;
		height: 600rpx;
		position: absolute;
		right: 0;
		left: -30%;
		top: -194rpx;
		bottom: 0;
		margin: auto;
		border-radius: 50% 50% 50% 50%;
	}

	.bg-base-1 {
		background-color: #ff5704;
	}

	.bg-base-2 {
		background-color: #19ad57;
	}

	.bg-base-3 {
		background-color: #ffcc00;
	}

	.bg-base-4 {
		background-color: #33a7ff;
	}

	.bg-base-5 {
		background-color: #e4e4e4;
	}

	.bg-base-6 {
		background-color: #c8ba97;
	}

	.bg-base-7 {
		background-color: #623ceb;
	}

	.item-image {
		width: 102rpx;
		height: 102rpx;
		border-radius: 50%;
		background-color: #fff;
		overflow: hidden;
		margin-right: 20rpx;

		image {
			width: 102rpx;
			height: 102rpx;
			border-radius: 50%;
			background-color: #fff;
		}
	}

	.d-c-s {
		display: flex;
		justify-content: center;
		align-items: flex-start;
	}

	.pop-bg {
		position: fixed;
		left: 0;
		top: 0;
		bottom: 0;
		right: 0;
		margin: auto;
		z-index: 98;
		background-color: rgba(0, 0, 0, 0.65);
	}

	.icon.icon-guanbi {
		font-size: 42rpx;
		color: #999;
		position: absolute;
		right: 25rpx;
		top: 22rpx;
		z-index: 101;
	}

	.pop-input {
		width: 750rpx;
		// height: 559rpx;
		background: #ffffff;
		border-radius: 25rpx 25rpx 0 0;
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		padding-bottom: env(safe-area-inset-bottom);
		// bottom: 0;
		margin: auto;
		z-index: 100;
	}

	.pop-top {
		padding: 105rpx 0 0 0;
		box-sizing: border-box;
	}

	.pop-title {
		font-size: 30rpx;
		color: #333;
		margin-bottom: 31rpx;
	}

	.input-box {
		width: 662rpx;
		height: 88rpx;
		background: #E2E1E6;
		border-radius: 43rpx;
		display: flex;

		input {
			font-size: 28rpx;
			height: 88rpx;
			line-height: 88rpx;
			flex: 1;
			text-align: center;
		}
	}

	.input-pop {
		box-sizing: border-box;
		border-radius: 32rpx;
		line-height: 1.5;
		font-size: 28rpx;
		color: #333;
		padding: 0 25rpx;
		border: none;
		flex: 1;
		outline-offset: 0;
	}

	.info-image {
		width: 136rpx;
		height: 136rpx;
		border-radius: 50%;

		image {
			width: 136rpx;
			height: 136rpx;
			border-radius: 50%;
		}
	}

	.pop-btn {
		width: 662rpx;
		height: 88rpx;
		background: #04BE01;
		border-radius: 43rpx;
		@include background_color('background_color');
		color: #ffffff;
		line-height: 1.5;
		border-radius: 38rpx;
		text-align: center;
		font-size: 32rpx;
		margin: 0 auto;
		margin-top: 32rpx;
		margin-bottom: 62rpx;
	}
</style>