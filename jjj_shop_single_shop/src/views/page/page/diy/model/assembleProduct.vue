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
			:style="{
				background: item.style.background,
				borderTopLeftRadius: item.style.topRadio + 'px',
				borderTopRightRadius: item.style.topRadio + 'px',
				borderBottomLeftRadius: item.style.bottomRadio + 'px',
				borderBottomRightRadius: item.style.bottomRadio + 'px'
			}"
			class="diy-sharpproduct"
		>
			<div
				class="sharpproduct-head d-b-c"
				:style="{
					backgroundImage: item.style.bgimage ? 'url(' + item.style.bgimage + ')' : ''
				}"
			>
				<div class="left d-s-c">
					<div
						v-if="item.style.titleType == 1"
						:style="{
							color: item.style.titleColor,
							fontSize: item.style.titleSize + 'px'
						}"
						class="name"
					>
						{{ item.params.title }}
					</div>
					<img v-if="item.style.titleType == 2" class="titleImg" :src="item.style.title_image" alt="" />
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
			<ul class="product-list assemble column__3 d-s-c" :style="getUlwidth(item)">
				<li
					class="product-item"
					:style="{
						background: item.style.productBg_color,
						borderBottomLeftRadius: item.style.product_bottomRadio + 'px',
						borderBottomRightRadius: item.style.product_bottomRadio + 'px',
						borderTopLeftRadius: item.style.product_topRadio + 'px',
						borderTopRightRadius: item.style.product_topRadio + 'px'
					}"
					v-for="(product, index) in item.data"
					:key="index"
				>
					<div class="product-cover">
						<img :style="{ borderRadius: item.style.product_imgRadio + 'px' }" v-img-url="product.image" />
						<div
							class="desc-situation product-sale"
							v-if="item.style.product_numberbtn == 1"
							:style="{
								color: item.style.number_color,
								backgroundImage: 'linear-gradient(to right, ' + (item.style.title_color1 || '#fff') + ', ' + (item.style.title_color2 || '#fff') + ')'
							}"
						>
			<span  class=""  :style="{color: item.style.number_color}"
              >2人团</span>
						</div>
					</div>
					<div class="d-c d-c-c">
						<div v-if="item.style.product_name == 1" class="product-title text-ellipsis tc">{{ product.product_name }}</div>
						<div
							:style="{
								color: item.style.productPrice_color
							}"
							v-if="item.style.product_price == 1"
						>
							<span class="f12">¥</span>
							<span class="f14 fb">233.00</span>
						</div>
						<div
							:style="{
								color: item.style.productLine_color
							}"
							v-if="item.style.product_lineprice == 1"
							class="text-d-line-through gray9 f12"
						>
							¥233
						</div>
						<div
							:style="{
								background: item.style.productLine_btnBackground,
								borderRadius: item.style.productLine_btnRadius + 'px',
								color: item.style.productLine_btnColor
							}"
							class="assemble_btns text-ellipsis tc"
							v-if="item.style.product_btn == 1"
						>
							{{ item.params.btntext }}
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
			/*商品列表*/
			tableData: [],
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

<style scoped lang="scss">
.diy-sharpproduct {
	overflow: hidden;
}

.diy-sharpproduct .product-list.assemble {
	display: flex;
	justify-content: flex-start;
	flex-wrap: wrap;
	background-color: #ffffff;
	padding-left: 10px;
	padding-top: 10px;
}

.diy-sharpproduct .product-list.assemble .product-title {
	margin-bottom: 18px;
}

.diy-sharpproduct .product-list.assemble .price {
	margin: 0 auto;
	margin-top: 10px;
	margin-bottom: 15px;
	width: 100%;
	display: -webkit-box;
	overflow: hidden;
	-webkit-line-clamp: 1;
	-webkit-box-orient: vertical;
	text-align: center;
}

.diy-sharpproduct .display__list .column__3 {
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
}

.diy-sharpproduct .product-list.assemble.column__3 {
	flex-wrap: nowrap;
	overflow-x: auto;
}

.diy-sharpproduct .product-list.assemble.column__3 .product-item {
	width: 99px;
	margin-right: 10px;
	overflow: hidden;
	margin-bottom: 10px;
	background: #ffffff;
	flex-shrink: 0;
	margin-right: 18px;
	background: #fff;
	// padding: 15px;
	box-sizing: border-box;
}

.diy-sharpproduct .product-list.assemble.column__3 .product-item .product-cover {
	width: 99px;
	height: 99px;
	overflow: hidden;
	position: relative;
}

.diy-sharpproduct .product-list.assemble.column__3 .product-item .product-cover image {
	width: 99px;
	height: 99px;
}

.diy-sharpproduct .product-list.assemble.column__3 .product-item .product-cover .desc-situation {
	position: absolute;
	top: 6px;
	left: 0px;
	background: linear-gradient(60deg, #fc4528 0%, #fc573c 43%, #fc7639 100%);
	color: #ffffff !important;
	padding: 0 12px;
	height: 20px;
	display: flex;
	justify-content: center;
	align-items: center;
	line-height: 1;
	border-bottom-left-radius: 6px;
	border-top-right-radius: 6px;
}

.diy-sharpproduct .product-list.assemble.column__3 .product-title {
	margin-bottom: 8px;
}

.sharpproduct-head {
	height: 44px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	box-sizing: border-box;
	margin: 0;
	padding: 0 10px;
	background-repeat: no-repeat;
	background-size: cover;
	line-height: 1;
}

.sharpproduct-head .name.assemble_name {
	font-size: 18px;
	font-weight: bold;
	width: 100px;
}

.sharpproduct-head .datetime {
	margin-left: 20px;
}

.sharpproduct-head .datetime > span {
	display: inline-block;
}

.sharpproduct-head .datetime .text {
	padding: 0 2px;
}

.sharpproduct-head .datetime .box {
	padding: 2px;
	background: #000000;
	color: #ffffff;
}

.diy-sharpproduct .product-list.assemble .assemble_btns {
	font-size: 10px;
	text-align: center;
	width: 63px;
	height: 20px;
	line-height: 1;
	display: flex;
	justify-content: center;
	align-items: center;
}
.titleImg {
	width: 100%;
	height: 44px;
}
</style>
