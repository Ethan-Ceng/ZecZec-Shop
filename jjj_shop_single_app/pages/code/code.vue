<template>
	<view class="user-index">
		<!--礼品卡兑换-->
		<view class="thbox">
			<view class="thcontent">
				<image class="kabg" :src="img" mode="widthFix"></image>
				<view class="srkbox">
					<view class="kh">
						<text class="khtxt">卡号</text>
						<input class="khsr" v-model="code_no" placeholder="请输入兑换卡号" />
					</view>
					<view class="kh">
						<text class="khtxt">密码</text>
						<input class="khsr" v-model="code_pwd" placeholder="请输入兑换密码" />
					</view>
				</view>
				<button class="qubtn" @click="confirm">确认兑换</button>
			</view>
			<view class="wbtxt">
				<text class="dhjlbtn" @click="gotoOrder">兑换记录</text>
				<text class="zhushi">请在兑换时间内，按照礼券上的卡券密码填写，兑换成功后本券自动失效。</text>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				code_no: '',
				code_pwd: '',
				img: ''
			};
		},
		onLoad(e) {
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中',
					mask: true
				});
				self._get(
					'card.code/setting', {

					},
					function(res) {
						self.img = res.data.setting.image;
						uni.hideLoading();
					}
				);
			},
			/*获取数据*/
			confirm() {
				let self = this;
				uni.showLoading({
					title: '正在处理',
					mask: true
				});
				self._get(
					'card.code/check', {
						code_no: self.code_no,
						code_pwd: self.code_pwd
					},
					function(res) {
						uni.hideLoading();
						self.gotoPage('/pages/code/order?order_id=' + res.data.order_id);
					}
				);
			},
			gotoOrder() {
				this.gotoPage('/pages/order/codeorder');
			},
		},
	};
</script>

<style>
	.thbox {
		display: flex;
		flex-direction: column;
		align-items: center;
		background-color: #e4e4e4;
		position: absolute;
		left: 0;
		right: 0;
		bottom: 0;
		top: 0;

	}

	.thcontent {
		flex-direction: column;
		margin: 23rpx;
		padding: 45rpx;
		background: #fff;
		border-radius: 5rpx;
		position: relative;

	}

	.kabg {
		border-radius: 5rpx;
		margin-bottom: 50rpx;
		width: 615rpx;
		height: 617rpx;
	}

	.srkbox {
		display: flex;
		flex-direction: column;
		align-items: center;
		padding: 0 53rpx;
		box-sizing: border-box;
		padding-bottom: 100rpx;
	}

	.kh {
		display: flex;
		width: 100%;
		align-items: center;
		justify-content: space-between;
		height: 106rpx;
	}

	.khtxt {
		flex: 0.2;
		height: 64rpx;
		line-height: 64rpx;
		font-size: 34rpx;
	}

	.khsr {
		flex: 0.8;
		border-bottom: 1px solid #eee;
		height: 74rpx;
		line-height: 74rpx;
		font-size: 30rpx;
		color: #999999;
	}

	.tsbox {
		display: inline-block;
		color: #ff5649;
		background-color: #ffe5e5;
		font-size: 24rpx;
		padding: 6rpx 30rpx;
		border-radius: 30rpx;
		margin: 20rpx 0;

	}

	.qubtn {
		position: absolute;
		bottom: -50rpx;
		left: 10%;
		width: 80%;
		height: 80rpx;
		line-height: 80rpx;
		background: linear-gradient(to bottom, #ff7220, #fbb700);
		border-radius: 40rpx;
		color: #fff;
		font-size: 32rpx;
	}

	.wbtxt {
		display: flex;
		flex-direction: column;
		align-items: center;
		margin-top: 5%;
	}

	.dhjlbtn {
		color: #459aff;
		font-size: 28rpx;
		margin-bottom: 20rpx;
	}

	.zhushi {
		width: 80%;
		text-align: center;
		font-size: 24rpx;
		color: #999;
	}
</style>
