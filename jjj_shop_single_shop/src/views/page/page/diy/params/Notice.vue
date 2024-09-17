<template>
	<div>
		<div class="common-form">
			<span>{{ curItem.name }}</span>
		</div>
		<el-form size="small" :model="curItem" label-width="100px">
			<div class="f16 gray3 form-subtitle">边距设置</div>
			<div class="form-item">
				<div class="form-label">底部背景：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.background"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.background" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'background', '#ffffff')" type="primary" link>重置</el-button>
				</div>
			</div>
			<div class="form-item">
				<div class="form-label">组件背景：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.bgcolor"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.bgcolor" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'bgcolor', '#f2f2f2')" type="primary" link>重置</el-button>
				</div>
			</div>
			<!--上下边距-->
			<div class="form-item">
				<div class="form-label">上边距：</div>
				<el-slider v-model="curItem.style.paddingTop" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--上下边距-->
			<div class="form-item">
				<div class="form-label">下边距：</div>
				<el-slider v-model="curItem.style.paddingBottom" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--左右边距-->
			<div class="form-item">
				<div class="form-label">左右边距：</div>
				<el-slider v-model="curItem.style.paddingLeft" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--左右边距-->
			<div class="form-item">
				<div class="form-label">左右内边距：</div>
				<el-slider v-model="curItem.style.padding" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--左右边距-->
			<div class="form-item">
				<div class="form-label">上圆角：</div>
				<el-slider v-model="curItem.style.topRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--左右边距-->
			<div class="form-item">
				<div class="form-label">下圆角：</div>
				<el-slider v-model="curItem.style.bottomRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<div class="form-item">
				<div class="form-label">文字颜色：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.textColor"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.textColor" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'textColor', '#000000')" type="primary" link>重置</el-button>
				</div>
			</div>
			<!--公告图标-->
			<el-form-item label="公告图标：">
				<div class="diy-notice-icon">
					<img v-img-url="curItem.params.icon" alt="" style="width: 100%;height: auto;" @click="$parent.onEditorSelectImage(curItem.params, 'icon')" />
				</div>
				<div class="ww100 gray">建议尺寸32×32</div>
			</el-form-item>
			<!-- 公告内容 -->
			<el-form-item label="公告内容："><el-input v-model="curItem.params.text" class="w-auto"></el-input></el-form-item>
		</el-form>
	</div>
</template>

<script>
export default {
	data() {
		return {};
	},
	props: ['curItem', 'selectedIndex', 'opts'],
	created() {
		this.curItem.style.paddingTop = parseInt(this.curItem.style.paddingTop);
	},
	methods: {}
};
</script>

<style lang="scss" scoped>
.diy-notice-icon,
.diy-notice-icon img {
	width: 32px;
	height: 32px;
}
</style>
