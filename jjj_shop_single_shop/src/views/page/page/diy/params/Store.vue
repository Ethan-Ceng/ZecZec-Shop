<template>
	<div>
		<div class="common-form">
			<span>{{ curItem.name }}</span>
		</div>
		<el-form size="small" :model="curItem" label-width="100px">
			<div class="f16 gray3 form-subtitle">内容设置</div>
			<!-- 商品来源 -->
			<el-form-item label="商品来源：">
				<el-radio-group v-model="curItem.params.source"  size="medium">
					<el-radio :label="'auto'">自动获取</el-radio>
					<el-radio :label="'choice'">手动选择</el-radio>
				</el-radio-group>
			</el-form-item>
			<!--手动选择-->
			<template v-if="curItem.params.source == 'choice'">
				<el-form-item label="门店列表：">
					<div class="choice-shop-list">
						<div :key="index" v-for="(shop, index) in curItem.data" class="item" @click="log(shop)">
							<div class="delete-box">
								<el-icon @click="$parent.onEditorDeleleData(index, selectedIndex)"><CircleCloseFilled /></el-icon>
							</div>
							<img v-if="shop.logo" :src="shop.logo.file_path" alt="" />
							<img v-else :src="shop.logo_image" alt="" />
						</div>
						<div class="item plus-btn" @click.stop="$parent.openStore(true)">
							<el-icon><Plus /></el-icon>
							<p>选择门店</p>
						</div>
					</div>
				</el-form-item>
			</template>
			<!-- 自动获取 -->
			<template v-if="curItem.params.source == 'auto'">
				<!-- 展示数量 -->
				<el-form-item label="展示数量："><el-input  v-model="curItem.params.auto.showNum" class="w-auto"></el-input></el-form-item>
			</template>
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">样式设置</div>
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
				<div class="form-label">上圆角：</div>
				<el-slider v-model="curItem.style.topRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--左右边距-->
			<div class="form-item">
				<div class="form-label">下圆角：</div>
				<el-slider v-model="curItem.style.bottomRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
		</el-form>
	</div>
</template>

<script>
export default {
	data() {
		return {};
	},
	props: ['curItem', 'selectedIndex', 'opts'],
	mounted() {},
	methods: {}
};
</script>

<style lang="scss" scoped>
.choice-shop-list {
	display: flex;
	justify-content: flex-start;
	flex-wrap: wrap;
}

.choice-shop-list .item {
	position: relative;
	width: 80px;
	height: 80px;
	margin-right: 10px;
	border: 1px solid #dddddd;
}

.choice-shop-list .item .delete-box {
	position: absolute;
	width: 20px;
	height: 20px;
	top: -10px;
	right: -10px;
	font-size: 20px;
	cursor: pointer;
	color: #999999;
}

.choice-shop-list .item .delete-box:hover {
	color: rgb(255, 51, 0);
}

.choice-shop-list .item.plus-btn {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
}

.choice-shop-list .item.plus-btn > i {
	font-size: 30px;
	color: #cccccc;
}

.choice-shop-list img {
	width: 100%;
	height: 100%;
}
</style>
