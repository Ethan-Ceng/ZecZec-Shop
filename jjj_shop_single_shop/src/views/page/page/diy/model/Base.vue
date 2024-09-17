<template>

	<div
		@click.stop="$parent.$parent.onEditer(index)"
		:style="{
			background: item.style.bgcolor,
			padding: item.style.paddingTop + 'px ' + item.style.paddingLeft + 'px ' + item.style.paddingBottom + 'px ' + item.style.paddingLeft + 'px'
		}"
	>
		<div class="drag optional" :class="{ selected: index === selectedIndex }">
			<div class="bg-box">
				<div class="d-s-c" style="z-index: 1;position: relative;margin-left: 10px;margin-top: 40px;">
					<div class="item-image"><img v-img-url="'/src/assets/img/login-name.png'" /></div>
					<div class="d-c d-c-s white" style="height: 51px;">
						<div class="f12">用户昵称</div>
						<div>ID:123456</div>
					</div>
				</div>
				<div class="bg-base" :class="'bg-base-' + item.style.type"></div>
				<div class="diy-Base" :style="{ background: item.style.background }">
					<div class="list column-3">
						<div class="item">
							<div class="item-text fb f16">100.00</div>
							<div class="item-text text-ellipsis">账户余额</div>
						</div>
						<div class="item" :style="'margin:0 ' + item.style.padding + 'px;'">
							<div class="item-text fb f16">20.00</div>
							<div class="item-text text-ellipsis">积分</div>
						</div>
						<div class="item">
							<div class="item-text fb f16">10</div>
							<div class="item-text text-ellipsis">优惠券</div>
						</div>
					</div>
				</div>
			</div>

			<div class="btn-edit-del"><div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div></div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {};
	},
	props: ['item', 'index', 'selectedIndex'],
	methods: {}
};
</script>

<style lang="scss" scoped>
.diy-Base {
	position: absolute;
	bottom: 0;
	width: 355px;
	left: 8px;
	padding: 0 10px;
	box-sizing: border-box;
	border-radius: 8px;
}
.diy-Base .list {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 100%;
}
.diy-Base .list .item {
	padding: 10px 0;
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
.diy-Base .list .item-image img {
	width: 100%;
}
.diy-Base .list .item-text {
	width: 100%;
	padding: 4px 0;
	text-align: center;
}
.bg-box {
	overflow: hidden;
	height: 205px;
	position: relative;
}
.bg-base {
	width: 160%;
	height: 300px;
	position: absolute;
	right: 0;
	left: -30%;
	top: -97px;
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
	width: 51px;
	height: 51px;
	border-radius: 50%;
	background-color: #fff;
	overflow: hidden;
	margin-right: 10px;
	img {
		width: 51px;
		height: 51px;
		border-radius: 50%;
		background-color: #fff;
	}
}
.d-c-s {
	display: flex;
	justify-content: center;
	align-items: flex-start;
}
</style>
