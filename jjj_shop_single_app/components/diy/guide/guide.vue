<template>
	<view class="diy-guide" :style="{ padding: itemData.style.paddingTop*2 + 'rpx ' + itemData.style.paddingLeft*2 + 'rpx ' + itemData.style.paddingBottom*2 + 'rpx ' + itemData.style.paddingLeft*2 + 'rpx', background: itemData.style.background }">
		<view class="line" :style="{ borderTopWidth: itemData.style.lineHeight + 'px', borderTopColor: itemData.style.lineColor, borderTopStyle: itemData.style.lineStyle }"></view>
	</view>
</template>

<script>
export default {
	data() {
		return {};
	},
	props: ['itemData'],
	methods: {}
};
</script>

<style></style>
