<template>
	<div>
		<div class="common-form">
			<span>{{ curItem.name }}</span>
		</div>
		<el-form size="small" :model="curItem" label-width="100px">
			<!--组件样式-->
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">组件样式</div>
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
			<!--上圆角-->
			<div class="form-item">
				<div class="form-label">上圆角：</div>
				<el-slider v-model="curItem.style.topRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--下圆角-->
			<div class="form-item">
				<div class="form-label">下圆角：</div>
				<el-slider v-model="curItem.style.bottomRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<div class="form-item">
				<div class="form-label">底部背景：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.bgcolor"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.bgcolor" placeholder="透明" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'bgcolor', '#f2f2f2')" type="primary" link>重置</el-button>
				</div>
			</div>
			<!--视频高度-->
			<el-form-item label="视频高度：">
				<el-slider max="800" v-model="curItem.style.height" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
				<div class="gray">滑块可用键盘的左右方向键精确调整</div>
			</el-form-item>
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">视频设置</div>
			<!--视频封面-->
			<el-form-item label="视频封面：">
				<div class="diy-video-cover"><img v-img-url="curItem.params.poster" alt="" @click="$parent.onEditorSelectImage(curItem.params, 'poster')" /></div>
			</el-form-item>
			<!--视频地址-->
			<el-form-item label="视频地址："><el-input v-model="curItem.params.videoUrl"></el-input></el-form-item>
			<!--自动播放-->
			<el-form-item label="自动播放：">
				<el-radio-group v-model="curItem.params.autoplay">
					<el-radio :label="0">否</el-radio>
					<el-radio :label="1">是</el-radio>
				</el-radio-group>
			</el-form-item>
		</el-form>
	</div>
</template>

<script>
export default {
	data() {
		return {
			paddingTop: 0,
			height: 0
		};
	},
	props: ['curItem', 'selectedIndex'],
	created() {
		this.curItem.style.paddingTop = parseInt(this.curItem.style.paddingTop);
		this.curItem.style.height = parseInt(this.curItem.style.height);
	},
	watch: {},
	methods: {}
};
</script>

<style lang="scss" scoped>
.diy-video-cover {
	width: 200px;
}
.diy-video-cover img {
	width: 100%;
}
</style>
