<template>
    <view class="physical-wrap">
        <view class="physical-title">
            物流详情
        </view>
        <view class="step-wrap">
            <template v-for="(v,idx) in expressData" :key="idx">
                <view class="step-item" :class="{'active': idx == 0}">
                    <view class="step-circle"></view>
                    <view class="step-title">{{ v.status }}  {{ v.time }}</view>
                    <view class="step-desc">
                        {{ v.context }}
                    </view>
                </view>
            </template>
        </view>
    </view>
</template>

<script>
	export default {
        props:['expressData'],
		data() {
			return {};
		},
		methods: {

		}
	};
</script>

<style lang="scss" scoped>
.physical-wrap{
	background: #FFFFFF;
	margin-top: 25rpx;
	padding: 0 20rpx;
	.physical-title{
		font-size: 32rpx;
		font-family: Source Han Sans CN;
		font-weight: bold;
		color: #333333;
		padding: 20rpx 0;
		border-bottom: 1px solid #EEEEEE;
	}
	.step-wrap{
		padding-top: 29rpx;
		padding-bottom: 34rpx;
	}
	.step-item{
		position: relative;
		padding-left: 40rpx;
		&::before{
			content: "";
			position: absolute;
			left: 10rpx;
			width: 2rpx;
			height: 100%;
			background: #999;
		}
		.step-circle{
			width: 20rpx;
			height: 20rpx;
			background: #999;
			border-radius: 10rpx;
			position: absolute;
			top: 0;
			left: 0;
		}
		.step-title{
			font-size: 29rpx;
			color: #979797;
		}
		.step-desc{
			padding: 18rpx 0;
			padding-bottom: 20rpx;
			color: #A7A7A7;
		}
		&.active{
			.step-circle{
				background: #FF5704;
			}
			.step-title{
				color: #FF5704;
				font-weight: bold;
				font-size: 30rpx;
			}
			.step-desc{
				color: #333;
			}
		}
	}
}
</style>
