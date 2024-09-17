<template>

	<div
		class="drag optional"
		:style="{
			background: item.style.bgcolor,
			paddingLeft: item.style.paddingLeft + 'px',
			paddingRight: item.style.paddingLeft + 'px',
			paddingTop: item.style.paddingTop + 'px',
			paddingBottom: item.style.paddingBottom + 'px'
		}"
		:class="{ selected: index === selectedIndex }"
		@click.stop="$parent.$parent.onEditer(index)"
	>
		<div
			class="diy-seckill"
			:style="{
				background: item.style.background,
				borderTopLeftRadius: item.style.topRadio + 'px',
				borderTopRightRadius: item.style.topRadio + 'px',
				borderBottomLeftRadius: item.style.bottomRadio + 'px',
				borderBottomRightRadius: item.style.bottomRadio + 'px'
			}"
		>
			<div
				class="diy-head d-b-c"
				:style="{
					backgroundImage: item.style.bgimage ? 'url(' + item.style.bgimage + ')' : ''
				}"
			>
				<div class="left d-s-c">
					<div
						v-if="item.style.titleType == 1"
						class="name"
						:style="{
							color: item.style.titleColor,
							fontSize: item.style.titleSize + 'px'
						}"
					>
						{{ item.params.title }}
					</div>
					<img v-if="item.style.titleType == 2" class="titleImgt" :src="item.style.title_image" alt="" />
					<div class="datetime d-s-c">
						<text class="text" :style="{ color: item.style.color }">| 距结束</text>
						<span
							class="hour"
							:style="{
								color: item.style.number_color,
								background: item.style.title_color1
							}"
						>
							30
						</span>
						<span class="text" :style="{ color: item.style.color }">:</span>
						<span
							class="hour"
							:style="{
								color: item.style.number_color,
								background: item.style.title_color1
							}"
						>
							00
						</span>
						<span class="text" :style="{ color: item.style.color }">:</span>
						<span
							class="hour"
							:style="{
								color: item.style.number_color,
								background: item.style.title_color1
							}"
						>
							00
						</span>
					</div>
				</div>
				<div
					class="right white d-c-c"
					style="line-height: 1;"
					:style="{
						color: item.style.moreColor,
						fontSize: item.style.moreSize + 'px'
					}"
				>
					{{ item.params.more }}
					<el-icon size="14px"><ArrowRight /></el-icon>
				</div>
			</div>
			<ul class="product-list column__3" :style="getUlwidth(item)">
				<li
					class="product-item"
					v-for="(product, index) in item.data"
					:key="index"
					:style="{
						background: item.style.productBg_color,
						borderTopLeftRadius: item.style.product_topRadio + 'px',
						borderTopRightRadius: item.style.product_topRadio + 'px',
						borderBottomLeftRadius: item.style.product_bottomRadio + 'px',
						borderBottomRightRadius: item.style.product_bottomRadio + 'px'
					}"
				>
					<!-- 两列三列 -->
					<div class="product-cover"><img :style="{ borderRadius: item.style.product_imgRadio + 'px' }" v-img-url="product.image" /></div>
					<div class="product-info d-c d-b-s p-0-10 flex-1">
						<div
							class="f14 text-ellipsis"
							v-if="item.style.product_name == 1"
							:style="{
								color: item.style.productName_color
							}"
						>
							商品名称
						</div>
						<div class="d-b-c ww100" v-if="item.style.product_schedule == 1">
							<div
								class="slider-box flex-1"
								:style="{
									background: (item.style.productSlider_color || '#ffffff') + '30'
								}"
							>
								<div
									:style="{
										background: item.style.productSlider_color
									}"
									class="slider-content"
									style="width: 36.4%;"
								></div>
							</div>
							<div class="f12 gray9">已抢36.4%</div>
						</div>
						<div class="d-b-c ww100">
							<div class="price tc flex-1" style="text-align: left;">
								<span
									class="f14"
									v-if="item.style.product_price == 1"
									:style="{
										color: item.style.productPrice_color
									}"
								>
									¥120
								</span>
								<span
									v-if="item.style.product_lineprice == 1"
									:style="{
										color: item.style.productLine_color
									}"
									class="text-d-line"
								>
									¥233
								</span>
							</div>
							<div
								:style="{
									background: item.style.productLine_btnBackground,
									borderRadius: item.style.productLine_btnRadius + 'px',
									color: item.style.productLine_btnColor
								}"
								class="btn text-ellipsis tc"
								v-if="item.style.product_btn == 1"
							>
								{{ item.params.btntext }}
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<div class="btn-edit-del"><div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div></div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			/*分类id*/
			category_id: 0
		};
	},
	created() {},
	props: ['item', 'index', 'selectedIndex'],
	methods: {
		/*计算宽度*/
		getUlwidth(item) {
			if (item.style.display == 'slide') {
				let total = 0;
				if (item.params.source == 'choice') {
					total = item.data.length;
				} else {
					total = item.defaultData.length;
				}
				let w = 0;
				if (item.style.column == 2) {
					w = total * 150;
				} else {
					w = total * 100;
				}
				return 'width:' + w + 'px;';
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.titleImgt {
	width: 100%;
	height: 44px;
}
.diy-seckill {
	overflow: hidden;
}

.diy-seckill .diy-head {
	padding: 0 10px;
	height: 45px;
	background-size: 100% 100% !important;
}

.diy-seckill .diy-head .name {
	font-size: 18px;
	font-weight: bold;
	/* width: 100px; */
}

.diy-seckill .diy-head .datetime {
	margin-left: 20px;
	border: none;
	white-space: nowrap;
}

.diy-seckill .diy-head .datetime > span {
	display: inline-block;
}

.diy-seckill .diy-head .datetime .text {
	margin: 0 3px;
	line-height: 1;
}
.diy-seckill .diy-head .datetime .hour {
	background: linear-gradient(to right, rgb(255, 255, 255), rgb(255, 255, 255));
	color: rgb(253, 59, 84);
	font-size: 12px;
	border-radius: 3px;
	padding: 2px;
	line-height: 1;
}
.diy-seckill .diy-head .datetime .box {
	padding: 2px;
	border-radius: 4px;
	background: #000000;
	color: #ffffff;
}

.diy-seckill .product-list {
	display: flex;
	justify-content: flex-start;
	flex-wrap: wrap;
	// box-shadow: 0px 4px 2px 0px rgba(6, 0, 1, 0.03);
	padding: 10px;
}

.diy-seckill .product-list .product-title {
	margin-top: 4px;
	height: 40px;
	line-height: 20px;
	display: -webkit-box;
	overflow: hidden;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
}

.diy-seckill .display__list .column__3 {
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
}

.diy-seckill .product-list.column__3 .product-item {
	/* width: 66px; */
	margin-bottom: 10px;
	// margin-right: 23px;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	overflow: hidden;
}
.diy-seckill .product-list.column__3 .product-item:last-child {
	margin-bottom: 0;
}
.diy-seckill .product-list.column__3 .product-item .product-cover {
	width: 100px;
	height: 100px;
	overflow: hidden;
}
.product-info {
	height: 100px;
	box-sizing: border-box;
}
.diy-seckill .product-list.column__3 .product-item .product-cover img {
	width: 100px;
	height: 100px;
}

.diy-seckill .product-list.column__3 .product-title {
	height: 20px;
	overflow: hidden;
}

.slider-box {
	height: 9px;
	border-radius: 9px;
	position: relative;
	overflow: hidden;
	margin-right: 10px;
	background: rgba(#ff6417, 0.1);
}
.slider-box .slider-content {
	position: absolute;
	left: 0;
	top: 0;
	height: 9px;
	background: #ff6417;
}
.diy-seckill .product-list.column__3 .product-item .btn {
	width: 81px;
	height: 29px;
	background: #ff6417;
	border-radius: 15px;
	font-size: 14px;
	color: #ffffff;
	display: flex;
	justify-content: center;
	align-items: center;
}
</style>
