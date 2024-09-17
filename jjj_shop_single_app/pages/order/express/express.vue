<template>
	<view class="wrap">
		<view class="order d-b-c">
			<view class="order-num">订单编号：{{ detail.order_no }}</view>
			<view class="order-total" v-if="detail.is_single==1">{{ orderDeliverList.length || 0 }}个包裹
			</view>
		</view>
		<view class="package-box">
			<template v-if="detail.is_single==1">
				<scroll-view class="package-wrap" scroll-x="true">
					<template v-for="(v,idx) in orderDeliverList" :key="idx">
						<view class="package-item" :class="{'active': currentIndex == idx}" @click="changPackage(idx)">
							<view class="d-c-c">
								<view class="iconfont icon-baoguofahuo"></view>
								<view>包裹{{ idx+1 }}</view>
							</view>
						</view>
					</template>
				</scroll-view>
			</template>
			<view class="package-info">
				<view class="notice">
					快递走件、催派等物流信息，请联系物流公司，如需平台接入请联系客服
				</view>
				<view class="base-info">
					<template v-if="detail.is_single==1">
						<view class="base-title">{{ orderDeliver.express_name }}</view>
						<view class="base-order-no d-s-c">
							<view>运单号:{{ orderDeliver.express_no }}</view>
							<view class="base-order-copy" @click="copy(orderDeliver.express_no)">复制</view>
						</view>
					</template>
					<template v-else>
						<view class="base-title">{{ detail.express.express_name }}</view>
						<view class="base-order-no d-s-c">
							<view>运单号:{{ detail.express_no }}</view>
							<view class="base-order-copy" @click="copy(detail.express_no)">复制</view>
						</view>
					</template>
					<view class="base-product-list d-b-c"
						v-if="detail.is_single == 1">
						<view>
							<template v-for="(img,imgIdx) in orderDeliver.product_list" :key="imgIdx">
								<image class="product-img" :src="img.image_path" mode="aspectFill" />
							</template>

						</view>
						<view class="product-total" @click="openProduct">
							共{{ orderDeliver.product_list && orderDeliver.product_list.length || 0 }}种商品<text
								class="iconfont icon-jiantou1"></text></view>
					</view>
				</view>
			</view>
			<physicalStepVue ref="physicalStepRef" :expressData="express.express && express.express.list" />
		</view>
		<expressMaskVue ref="expressMaskRef" />
	</view>
</template>

<script>
	import expressMaskVue from './expressMask.vue';
	import physicalStepVue from './physicalStep.vue';
	export default {
		components: {
			expressMaskVue,
			physicalStepVue,
		},
		data() {
			return {
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				/*订单id*/
				order_id: 0,
				/*快递信息*/
				detail: {
					express:{}
				},
				currentIndex: 0,
				orderDeliverList: [],
				orderDeliver: {},
				express: {},
			};
		},
		onLoad(e) {
			uni.showLoading({
				title: '加载中'
			});
			this.order_id = e.order_id;
			this.getData();
		},
		onShow() {

		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				let order_id = self.order_id
				self._get('user.order/detail', {
					order_id: order_id
				}, function(res) {
					self.orderDeliver = {};
					self.detail = res.data.order;
					self.orderDeliverList = res.data.order.orderDeliverList;
					if (self.orderDeliverList && self.orderDeliverList.length > 0) {
						self.orderDeliver = self.orderDeliverList[self.currentIndex];
					}
					console.log(1111111111)	
					if (self.detail.is_single == 0) {
						self.getSingleData();
					} else if (self.detail.is_single == 1) {
						self.getMultiData();
					}
					self.loadding = false;
					uni.hideLoading();
				});
			},
			// 单包裹获取物流信息
			getSingleData() {
				let self = this;
				let order_id = self.order_id
				self._get('user.order/express', {
					order_id: order_id
				}, function(res) {
					self.express = res.data;
					self.loadding = false;
					uni.hideLoading();
				});
			},
			// 多包裹获取物流信息
			getMultiData() {
				let self = this;
				let order_id = self.order_id
				let params = {
					express_id: self.orderDeliver.express_id,
					express_no: self.orderDeliver.express_no,
					order_id: order_id
				}
				self._post('user.order/multiExpress', params, function(res) {
					self.express = res.data;
					self.loadding = false;
					uni.hideLoading();
				});
			},
			changPackage(idx) {
				if (this.currentIndex == idx) {
					return
				}
				uni.showLoading({
					title: '加载中'
				});
				this.currentIndex = idx;
				this.orderDeliver = this.orderDeliverList[this.currentIndex];
				this.getMultiData();
			},
			openProduct() {
				this.$refs.expressMaskRef.open(this.orderDeliver.product_list);
			},
			/*复制*/
			copy(message) {
				console.log("message", message)
				uni.setClipboardData({
					data: message,
					success: function(res) {
						uni.showToast({
							title: '复制成功',
							icon: 'success',
							mask: true,
							duration: 2000
						});
					},
					fail: function(res) {
						console.log("error", res)
					}
				});
			}
		}
	};
</script>

<style lang="scss" scoped>
	.order {
		height: 94rpx;
		line-height: 94rpx;
		background: #FFFFFF;
		color: #666666;
		font-size: 24rpx;
		padding: 0 20rpx;
	}

	.package-box {
		padding: 20rpx;
		box-sizing: border-box;
	}

	.package-wrap {
		white-space: nowrap;
		width: 100%;
		margin-bottom: 20rpx;
	}

	.package-item {
		display: inline-block;
		width: 210rpx;
		height: 92rpx;
		line-height: 92rpx;
		background: #FFFFFF;
		border-radius: 15rpx;
		font-size: 30rpx;
		text-align: center;
		color: #333333;
		position: relative;
		margin-right: 20rpx;

		.iconfont {
			margin-right: 9rpx;
		}

		&::before {
			content: "";
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 7rpx;
			background: transparent;
			border-radius: 28rpx;
		}

		&.active {
			.iconfont {
				color: #FF5704;
			}

			&::before {
				background: #FF5704;
			}
		}
	}

	.package-info {
		background: #FFFFFF;
		border-radius: 25rpx;
		overflow: hidden;

		.notice {
			height: 72rpx;
			background: #FFF8EE;
			border-radius: 25rpx 25rpx 0px 0px;
			font-size: 24rpx;
			line-height: 38rpx;
			color: #FF5704;
			padding: 22rpx 19rpx;
		}

		.base-info {
			padding: 20rpx;

			.base-title {
				font-size: 32rpx;
				font-weight: bold;
				color: #333333;
				padding: 40rpx 0;
			}

			.base-order-no {
				font-size: 24rpx;
				color: #666666;
				padding-bottom: 29rpx;
			}

			.base-product-list {
				border-top: 1px solid #EEEEEE;
				padding-top: 20rpx;

				.product-img {
					width: 80rpx;
					height: 80rpx;
					border-radius: 15rpx;
					margin-right: 8rpx;
					display: inline-block;
				}

				.product-total {
					font-size: 24rpx;
					color: #666666;

					.iconfont {
						font-size: 24rpx;
						color: #090000;
					}
				}
			}

			.base-order-copy {
				margin-left: 34rpx;
				color: #333333;
			}
		}
	}

	.physical-wrap {
		background: #FFFFFF;
		margin-top: 25rpx;
		padding: 0 20rpx;

		.physical-title {
			font-size: 32rpx;
			font-family: Source Han Sans CN;
			font-weight: bold;
			color: #333333;
			padding: 20rpx 0;
			border-bottom: 1px solid #EEEEEE;
		}

		.step-wrap {
			padding-top: 29rpx;
			padding-bottom: 34rpx;
		}

		.step-item {
			position: relative;
			padding-left: 40rpx;

			&::before {
				content: "";
				position: absolute;
				left: 10rpx;
				width: 2rpx;
				height: 100%;
				background: #999;
			}

			.step-circle {
				width: 20rpx;
				height: 20rpx;
				background: #999;
				border-radius: 10rpx;
				position: absolute;
				top: 0;
				left: 0;
			}

			.step-title {
				font-size: 30rpx;
				font-weight: bold;
				color: #979797;
			}

			.step-desc {
				padding: 12rpx 0;
				color: #A7A7A7;
			}

			&.active {
				.step-circle {
					background: #FF5704;
				}

				.step-title {
					color: #FF5704;
				}

				.step-desc {
					color: #333;
				}
			}
		}
	}
</style>