<template>
	<view class="qrcode" :data-theme="theme()" :class="theme() || ''">
		<image class="qrposter" :src="qrcode_url" mode="widthFix"></image>
		<view class="btns-wrap">
			<!-- #ifdef MP || APP-PLUS -->
			<button class="btn-red theme-btn" type="default" @click="savePosterImg">保存图片</button>
			<!-- #endif  -->
			<!-- #ifdef H5 -->
			<view class="f22 gray9 tc ww100" type="default">长按图片即可保存到相册</view>
			<!-- #endif -->
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			qrcode_url: ''
		};
	},
	mounted() {
		/*获取数据*/
		this.getData();
	},
	methods: {
		/*获取数据*/
		getData() {
			let self = this;
			uni.showLoading({
				title: '加载中'
			});
			let source = self.getPlatform();
			self._get(
				'plus.agent.qrcode/poster',
				{
					source: source
				},
				function(data) {
					uni.hideLoading();
					self.qrcode_url = data.data.qrcode;
				}
			);
		},

		/*保存图片*/
		savePosterImg() {
			let self = this;
			uni.showLoading({
				title: '加载中'
			});
			// 下载海报图片
			uni.downloadFile({
				url: self.qrcode_url,
				success(res) {
					uni.hideLoading();
					// 图片保存到本地
					uni.saveImageToPhotosAlbum({
						filePath: res.tempFilePath,
						success(data) {
							uni.showToast({
								title: '保存成功',
								icon: 'success',
								duration: 2000
							});
							// 关闭商品海报
							self.isCreatedImg = false;
						},
						fail(err) {
							console.log(err.errMsg);
							if (err.errMsg === 'saveImageToPhotosAlbum:fail auth deny') {
								uni.showToast({
									title: '请允许访问相册后重试',
									icon: 'none',
									duration: 1000
								});
								setTimeout(() => {
									uni.openSetting();
								}, 1000);
							}
						},
						complete(res) {
							console.log('complete');
						}
					});
				}
			});
		}
	}
};
</script>

<style>
.qrcode {
	padding-top: 50rpx;
}
.qrcode .qrposter {
	width: 550rpx;
	display: block;
	margin: 0 auto;
	border-radius: 12rpx;
}
.btns-wrap {
	margin-top: 40rpx;
}
.btns-wrap .btn-red {
	width: 550rpx;
	margin: 0 auto;
	height: 88rpx;
	line-height: 88rpx;
	border-radius: 0;
	border-radius: 15rpx;
}
</style>
