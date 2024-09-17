<template>

  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>
    <el-form size="small" :model="curItem" label-width="100px">
		<div class="f16 gray3 form-subtitle">样式设置</div>
	  <!--上下边距-->
	  <div class="form-item">
	  	<div class="form-label">上下边距：</div>
	  	<el-slider v-model="curItem.style.paddingTop" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
	  </div>
	  <!--左右边距-->
	  <div class="form-item">
	  	<div class="form-label">左右边距：</div>
	  	<el-slider v-model="curItem.style.paddingLeft" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
	  </div>
      <!--背景颜色-->
	  <div class="form-item">
	  	<div class="form-label">背景颜色：</div>
	  	<div class="flex-1 d-s-c" style="height: 36px;">
	  		<el-color-picker size="default" v-model="curItem.style.background"></el-color-picker>
	  		<el-input class="ml10" v-model="curItem.style.background" />
	  		<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'background', '#333333')" type="primary" link>重置</el-button>
	  	</div>
	  </div>
	  <div style="margin: 0 auto;width: 375px;">
		  <!--内容-->
		   <Uediter :text="content" :config="ueditor.config" @contentChange="contentChangeFunc" ref="ue"></Uediter>
	  </div>
      
    </el-form>
  </div>
</template>

<script>
import Uediter from '@/components/UE.vue';
export default {
	components:{
		/*编辑器*/
		Uediter
	},
	data() {
		return {
			ueditor: {
				text: '',
				config: {
					initialFrameWidth: 375,
					initialFrameHeight: 500,
				}
			},
			content:''
		};
	},
	props: ['curItem', 'selectedIndex', 'opts'],
	watch:{
	},
	created() {
		this.curItem.style.paddingTop = parseInt(this.curItem.style.paddingTop);
		this.curItem.style.paddingLeft = parseInt(this.curItem.style.paddingLeft);
		this.content=this.curItem.params.content;
	},
	methods: {
		contentChangeFunc(e){
			this.content=e;
			this.curItem.params.content=e;
		}
	}
};
</script>

<style lang="scss" scoped></style>
