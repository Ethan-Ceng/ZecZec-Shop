<template>

	<div @click.stop="$parent.$parent.onEditer(index)">
		<div class="drag optional" :class="{ selected: index === selectedIndex }">
			<div
				class="diy-imageSingle"
				:style="{
					paddingLeft: item.style.paddingLeft + 'px',
					paddingRight: item.style.paddingLeft + 'px',
					paddingTop: item.style.paddingTop + 'px',
					paddingBottom: item.style.paddingBottom + 'px',
					background: item.style.background
				}"
			>
				<div
					:style="{
						borderTopLeftRadius: item.style.topRadio + 'px',
						borderTopRightRadius: item.style.topRadio + 'px',
						borderBottomLeftRadius: item.style.bottomRadio + 'px',
						borderBottomRightRadius: item.style.bottomRadio + 'px'
					}"
					class="item-image"
					:key="index"
					v-for="(imageSingle, index) in item.data"
				>
					<img v-img-url="imageSingle.imgUrl" />
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
.item-image {
	overflow: hidden;
}
.diy-imageSingle img {
	max-width: 100%;
}
</style>
