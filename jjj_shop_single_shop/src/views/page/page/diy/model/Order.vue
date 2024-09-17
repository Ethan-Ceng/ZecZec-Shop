<template>

	<div
		@click.stop="$parent.$parent.onEditer(index)"
		:style="{
			background: item.style.bgcolor,
			padding: item.style.paddingTop + 'px ' + item.style.paddingLeft + 'px ' + item.style.paddingBottom + 'px ' + item.style.paddingLeft + 'px'
		}"
	>
		<div class="drag optional" :class="{ selected: index === selectedIndex }">
			<div
				class="diy-Order"
				:style="{
					background: item.style.background,
					borderRadius: item.style.topRadio + 'px ' + item.style.topRadio + 'px ' + item.style.bottomRadio + 'px ' + item.style.bottomRadio + 'px'
				}"
			>
				<ul class="list column-5">
					<li class="item" :key="index" v-for="(num, index) in 5">
						<div class="item-image"><img v-img-url="imgList[item.style.type - 1][index]" /></div>
						<div class="item-text text-ellipsis">{{ nameList[index] }}</div>
					</li>
				</ul>
			</div>
			<div class="btn-edit-del"><div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div></div>
		</div>
	</div>
</template>

<script>
/* 风格一 */
import order10 from '@/assets/img/order/1-0.png';
import order11 from '@/assets/img/order/1-1.png';
import order12 from '@/assets/img/order/1-2.png';
import order13 from '@/assets/img/order/1-3.png';
import order14 from '@/assets/img/order/1-4.png';
/* 风格二 */
import order20 from '@/assets/img/order/2-0.png';
import order21 from '@/assets/img/order/2-1.png';
import order22 from '@/assets/img/order/2-2.png';
import order23 from '@/assets/img/order/2-3.png';
import order24 from '@/assets/img/order/2-4.png';
/* 风格三 */
import order30 from '@/assets/img/order/3-0.png';
import order31 from '@/assets/img/order/3-1.png';
import order32 from '@/assets/img/order/3-2.png';
import order33 from '@/assets/img/order/3-3.png';
import order34 from '@/assets/img/order/3-4.png';
/* 风格四 */
import order40 from '@/assets/img/order/4-0.png';
import order41 from '@/assets/img/order/4-1.png';
import order42 from '@/assets/img/order/4-2.png';
import order43 from '@/assets/img/order/4-3.png';
import order44 from '@/assets/img/order/4-4.png';
/* 风格五 */
import order50 from '@/assets/img/order/5-0.png';
import order51 from '@/assets/img/order/5-1.png';
import order52 from '@/assets/img/order/5-2.png';
import order53 from '@/assets/img/order/5-3.png';
import order54 from '@/assets/img/order/5-4.png';
/* 风格六 */
import order60 from '@/assets/img/order/6-0.png';
import order61 from '@/assets/img/order/6-1.png';
import order62 from '@/assets/img/order/6-2.png';
import order63 from '@/assets/img/order/6-3.png';
import order64 from '@/assets/img/order/6-4.png';
/* 风格七 */
import order70 from '@/assets/img/order/7-0.png';
import order71 from '@/assets/img/order/7-1.png';
import order72 from '@/assets/img/order/7-2.png';
import order73 from '@/assets/img/order/7-3.png';
import order74 from '@/assets/img/order/7-4.png';
/* 风格八 */
import order80 from '@/assets/img/order/8-0.png';
import order81 from '@/assets/img/order/8-1.png';
import order82 from '@/assets/img/order/8-2.png';
import order83 from '@/assets/img/order/8-3.png';
import order84 from '@/assets/img/order/8-4.png';
/* 风格九 */
import order90 from '@/assets/img/order/9-0.png';
import order91 from '@/assets/img/order/9-1.png';
import order92 from '@/assets/img/order/9-2.png';
import order93 from '@/assets/img/order/9-3.png';
import order94 from '@/assets/img/order/9-4.png';
/* 风格十 */
import order100 from '@/assets/img/order/10-0.png';
import order101 from '@/assets/img/order/10-1.png';
import order102 from '@/assets/img/order/10-2.png';
import order103 from '@/assets/img/order/10-3.png';
import order104 from '@/assets/img/order/10-4.png';
export default {
	data() {
		return {
			nameList: ['待付款', '待发货', '待收货', '待评价', '退款/售后'],
			imgList: [
				[order10, order11, order12, order13, order14],
				[order20, order21, order22, order23, order24],
				[order30, order31, order32, order33, order34],
				[order40, order41, order42, order43, order44],
				[order50, order51, order52, order53, order54],
				[order60, order61, order62, order63, order64],
				[order70, order71, order72, order73, order74],
				[order80, order81, order82, order83, order84],
				[order90, order91, order92, order93, order94],
				[order100, order101, order102, order103, order104]
			]
		};
	},
	props: ['item', 'index', 'selectedIndex'],
	methods: {}
};
</script>

<style lang="scss" scoped>
.diy-Order .list {
	display: flex;
	flex-wrap: wrap;
	align-items: flex-start;
}
.diy-Order .list .item {
	padding: 10px 0;
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
}
.diy-Order .list.column-3 .item {
	width: 33.333333333%;
}
.diy-Order .list.column-4 .item {
	width: 25%;
}
.diy-Order .list.column-5 .item {
	width: 20%;
}
.diy-Order .list .item-image {
	width: 60%;
}
.diy-Order .list .item-image img {
	width: 80%;
	margin: 0 auto;
}
.diy-Order .list .item-text {
	width: 100%;
	padding: 4px 0;
	text-align: center;
}
</style>
