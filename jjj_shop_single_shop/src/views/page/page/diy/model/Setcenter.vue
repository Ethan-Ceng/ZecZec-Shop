<template>
	<div class="phone-top" v-if="diyData.page" @click.stop="$parent.onEditer(-1)"></div>
</template>

<script>
export default {
	data() {
		return {};
	},
	props: {
		diyData: Object
	},
	computed: {
		searchStyle() {
			const colorRgb = this.$props.diyData.page.style.titleBackgroundColor || '#ffffff';
			const activeList = ['#ffffff', '#FFFFFF'];
			const flag = activeList.includes(colorRgb);
			return {
				color: flag ? '#ccc' : colorRgb,
				borderColor: flag ? '#ccc' : colorRgb
			};
		}
	},
	methods: {
		getImg(src) {
			var img_url = src;
			var img = new Image();
			img.src = img_url;
			return Math.ceil((img.width * 60) / img.height) + 'px';
		}
	}
};
</script>

<style lang="scss" scoped></style>
