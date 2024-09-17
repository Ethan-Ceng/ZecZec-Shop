<template>
	<div>
		<div class="common-form">
			<span>{{ curItem.name }}</span>
		</div>
		<div class="f16 gray3 form-subtitle">样式设置</div>
		<el-form size="small" :model="curItem" label-width="100px">
			<!--页面名称-->
			<div class="form-item" style="margin-bottom: 4px;">
				<div class="form-label">页面名称：</div>
				<el-input v-model="curItem.params.name"></el-input>
			</div>
			<p class="gray" style="margin-left: 60px;margin-bottom: 16px;">页面名称仅用于后台查找</p>
			<!--分享标题-->
			<div class="form-item" style="margin-bottom: 4px;">
				<div class="form-label">分享标题：</div>
				<el-input v-model="curItem.params.share_title"></el-input>
			</div>
			<p class="gray" style="margin-left: 60px;margin-bottom: 16px;">小程序端转发时显示的标题</p>
			<div class="form-item" style="margin-bottom: 4px;">
				<div class="form-label">分享logo：</div>
				<div class="diy-notice-icon"><img v-img-url="curItem.params.share_img" alt="" @click="$parent.onEditorSelectImage(curItem.params, 'share_img')" /></div>
			</div>
			<p class="gray" style="margin-left: 60px;margin-bottom: 16px;">公众号分享logo，建议尺寸80×80</p>
		</el-form>
	</div>
</template>

<script>
export default {
	data() {
		return {
			logo_type: 'image'
		};
	},
	props: ['curItem', 'selectedIndex', 'opts'],
	created() {},
	methods: {
		logechange(e) {
			console.log(e);
			this.curItem.params.title_type = e;
		},
		categorychange(e) {
			this.curItem.category.open = e;
		}
	}
};
</script>

<style lang="scss" scoped>
.diy-setpages-cover > img {
	width: 60px;
	height: 60px;
}
</style>
