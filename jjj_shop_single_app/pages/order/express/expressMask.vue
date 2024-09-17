<template>
	<view class="express-mask"  :data-theme="theme()"  v-if="show">
		<view class="express-content">
			<view class="close-icon iconfont icon-guanbi1" @click="close"></view>
			<view class="title">包裹中的商品({{ product.length || 0 }})</view>
			<view class="desc">若有特殊或组合商品，可能会拆分多个包裹发货</view>
			<scroll-view scroll-y="true" class="product-list">
				<template  v-for="v in product" :key="v">
					<view class="product-item d-s-s one-product">
						<view class="cover"> 
							<image :src="v.image_path" model="aspectFit" /> 
						</view> 
						<view class="product-r">
							<view class="product-title">{{ v.product_name }}</view>
							<view class="d-b-c">
								<!-- <view class="product-price">￥{{ v.productPrice }}</view> -->
								<!-- 
									@ApiModelProperty("购买数量")
									private Integer totalNum;

									@ApiModelProperty("已发货总数量")
									private Integer delivery_num;
								 -->
								<view class="product-num">共{{ v.delivery_num }}件</view>
							</view>
						</view>
					</view>
				</template>
			</scroll-view>
			<view class="express-btn theme-btn" @click="close">完成</view> 
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				show: false,
				product:[],
			};
		},
		methods: {
			open(product){
				this.product = product;
				console.log(product)
				this.show = true;
			},
			close(){
				this.show = false;
			},
		}
	};
</script>

<style lang="scss" scoped>
.express-mask{
	background: rgba(0,0,0,0.5);
	position: fixed;
	top: 0;
	bottom: 0;
	width: 100%;
	height: 100%;
	.express-content{
		background: #FFFFFF;
		border-radius: 25rpx 25rpx 0px 0px;
		position: absolute;
		width: 100%;
		bottom: 0;
		padding: 0 26rpx;
		box-sizing: border-box;
		padding-bottom: 20rpx;
		.close-icon{
			position: absolute;
			top: 0;
			right: 0;
			font-size: 32rpx;
			color: #999;
			padding: 30rpx;
		}
		.title{
			font-size: 32rpx;
			font-weight: bold;
			color: #333333;
			text-align: center;
			padding: 30rpx 0;
		}
		.desc{
			font-size: 28rpx;
			color: #999999;
		}
	}
	.product-list {
		height: 400rpx;
		padding: 30rpx 0;
		.product-item{
			height: 173rpx;
			.product-r{
				display: flex;
				justify-content: space-between;
				flex-direction: column;
				height: 100%;
				padding-left: 20rpx;
			}
			.product-title{
				font-size: 28rpx;
				color: #333333;
			}
			.product-price{
				color: #f6220c;
			}
			.product-num{
				color: #999999;
			}
		}
	}
}
.express-btn{
	height: 88rpx;
	line-height: 88rpx;
	background: #FF6904;
	border-radius: 44rpx;
	font-size: 32rpx;
	text-align: center;
}
	
	
</style>
