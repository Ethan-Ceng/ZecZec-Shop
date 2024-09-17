<template>

  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>
    <el-form size="small" :model="curItem" label-width="100px">
		<div class="f16 gray3 form-subtitle">样式设置</div>
      <!--底边距-->
	  <div class="form-item">
	  	<div class="form-label">底边距：</div>
	  	<el-slider v-model="curItem.style.bottom" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
	  </div>
      <!--右边距-->
	  <div class="form-item">
	  	<div class="form-label">右边距：</div>
	  	<el-slider v-model="curItem.style.right" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
	  </div>
      <!--不透明度-->
	  <div class="form-item">
	  	<div class="form-label">不透明度：</div>
	  	<el-slider v-model="curItem.style.opacity" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
	  </div>
      <!--客服类型-->
      <el-form-item label="客服类型：">
        <el-radio-group v-model="curItem.params.type">
          <el-radio :label="'chat'">在线聊天(仅微信小程序)</el-radio>
          <el-radio :label="'phone'">拨打电话</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="电话号码：">
        <el-input v-model="curItem.params.phone_num"></el-input>
        <div class="gray">非微信小程序环境，在线聊天将使用拨打电话</div>
      </el-form-item>
      <!--客服图标-->
      <el-form-item label="客服图标：">
        <div class="diy-service-icon"><img v-img-url="curItem.params.image" alt="" @click="$parent.onEditorSelectImage(curItem.params, 'image')" /></div>
        <div  class="gray">建议尺寸90×90</div>
      </el-form-item>
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
		this.curItem.style.bottom = parseInt(this.curItem.style.bottom);
		this.curItem.style.right = parseInt(this.curItem.style.right);
		this.curItem.style.opacity = parseInt(this.curItem.style.opacity);
	},
	methods: {}
};
</script>

<style lang="scss" scoped>
.diy-service-icon,
.diy-service-icon img {
  width: 40px;
  height: 40px;
}
</style>
