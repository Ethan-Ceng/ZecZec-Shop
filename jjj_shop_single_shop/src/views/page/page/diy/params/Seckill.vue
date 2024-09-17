<template>

	<div>
		<div class="common-form">
			<span>{{ curItem.name }}</span>
		</div>
		<el-form size="small" :model="curItem" label-width="100px">
			<div class="f16 gray3 form-subtitle">商品数据</div>
			<!--商品数量-->
			<div class="form-item">
				<div class="form-label">商品数量：</div>
				<el-slider v-model="curItem.params.showNum" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">头部风格</div>
			<el-form-item label="主标题类型：">
				<el-radio-group v-model="curItem.style.titleType" size="medium">
					<el-radio-button :label="1">文字</el-radio-button>
					<el-radio-button :label="2">图片</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<!--图片-->
			<el-form-item label="标题图片：" v-if="curItem.style.titleType == 2">
				<div class="diy-special-cover">
					<img  style="width: 220px;" v-img-url="curItem.style.title_image" alt="" @click="$parent.onEditorSelectImage(curItem.style, 'title_image')" />
					<!-- <div class="gray9 f12">建议图片高度88px</div> -->
				</div>
			</el-form-item>
			<el-form-item label="标题文字：" v-if="curItem.style.titleType == 1"><el-input v-model="curItem.params.title" class="w-auto"></el-input></el-form-item>
			<!--文字大小-->
			<div class="form-item" v-if="curItem.style.titleType == 1">
				<div class="form-label">标题文字大小：</div>
				<el-slider v-model="curItem.style.titleSize" :min="12" :max="24" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<div class="form-item" v-if="curItem.style.titleType == 1">
				<div class="form-label">标题文字颜色：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.titleColor"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.titleColor" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'titleColor', '#DDDDDD')" type="primary" link>重置</el-button>
				</div>
			</div>
			<el-form-item label="右侧文字："><el-input v-model="curItem.params.more" class="w-auto"></el-input></el-form-item>
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">右侧文字样式</div>
			<!--文字大小-->
			<div class="form-item">
				<div class="form-label">文字大小：</div>
				<el-slider v-model="curItem.style.moreSize" :min="12" :max="40" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<div class="form-item">
				<div class="form-label">文字颜色：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.moreColor"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.moreColor" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'moreColor', '#DDDDDD')" type="primary" link>重置</el-button>
				</div>
			</div>
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">显示内容</div>
			<!--商品名称-->
			<el-form-item label="商品名称：">
				<el-radio-group v-model="curItem.style.product_name" size="medium">
					<el-radio-button :label="1">显示</el-radio-button>
					<el-radio-button :label="2">不显示</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<!--销售价-->
			<el-form-item label="销售价：">
				<el-radio-group v-model="curItem.style.product_price" size="medium">
					<el-radio-button :label="1">显示</el-radio-button>
					<el-radio-button :label="2">不显示</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<!--划线价-->
			<el-form-item label="划线价：">
				<el-radio-group v-model="curItem.style.product_lineprice" size="medium">
					<el-radio-button :label="1">显示</el-radio-button>
					<el-radio-button :label="2">不显示</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<!--进度条-->
			<el-form-item label="进度条：">
				<el-radio-group v-model="curItem.style.product_schedule" size="medium">
					<el-radio-button :label="1">显示</el-radio-button>
					<el-radio-button :label="2">不显示</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<!--进度条-->
			<el-form-item label="按钮：">
				<el-radio-group v-model="curItem.style.product_btn" size="medium">
					<el-radio-button :label="1">显示</el-radio-button>
					<el-radio-button :label="2">不显示</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">头部样式</div>
			<el-form-item label="背景图片：">
				<div class="diy-special-cover">
					<img  style="width: 220px;" v-img-url="curItem.style.bgimage" alt="" @click="$parent.onEditorSelectImage(curItem.style, 'bgimage')" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'bgimage', '')" type="primary" link>重置</el-button>
					<div class="gray f12">建议图片宽度750px(根据组件左右边距修改)高度88px</div>
				</div>
			</el-form-item>
			<!-- 时间颜色 -->
			<div class="form-item">
				<div class="form-label">数字背景颜色：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-input class="ml10" v-model="curItem.style.title_color1" placeholder="透明" />
					<view class="ml10"><el-color-picker size="default" v-model="curItem.style.title_color1"></el-color-picker></view>
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'title_color1', '#ffffff')" type="primary" link>重置</el-button>
				</div>
			</div>
			<!-- 数字颜色 -->
			<div class="form-item">
				<div class="form-label">数字颜色：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.number_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.number_color" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'number_color', '#ffffff')" type="primary" link>重置</el-button>
				</div>
			</div>
			<!-- 文字颜色 -->
			<div class="form-item">
				<div class="form-label">文字颜色：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.color" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'color', '#ffffff')" type="primary" link>重置</el-button>
				</div>
			</div>
			<!--组件样式-->
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">商品样式</div>
			<!--图片圆角-->
			<div class="form-item">
				<div class="form-label">图片圆角：</div>
				<el-slider v-model="curItem.style.product_imgRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<div class="form-item">
				<div class="form-label">商品背景：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.productBg_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.productBg_color" placeholder="透明" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'productBg_color', '')" type="primary" link>重置</el-button>
				</div>
			</div>
			<!--商品上圆角-->
			<div class="form-item">
				<div class="form-label">上圆角：</div>
				<el-slider v-model="curItem.style.product_topRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--商品下圆角-->
			<div class="form-item">
				<div class="form-label">下圆角：</div>
				<el-slider v-model="curItem.style.product_bottomRadio" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
			</div>
			<div class="form-item" v-if="curItem.style.product_name">
				<div class="form-label">商品名称：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.productName_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.productName_color" placeholder="透明" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'productName_color', '#333333')" type="primary" link>
						重置
					</el-button>
				</div>
			</div>
			<div class="form-item" v-if="curItem.style.product_name">
				<div class="form-label">进度条：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.productSlider_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.productSlider_color" placeholder="透明" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'productSlider_color', '#FF4C01')" type="primary" link>
						重置
					</el-button>
				</div>
			</div>
			<div class="form-item" v-if="curItem.style.product_price">
				<div class="form-label">销售价：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.productPrice_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.productPrice_color" placeholder="透明" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'productPrice_color', '#FF4C01')" type="primary" link>
						重置
					</el-button>
				</div>
			</div>
			<div class="form-item" v-if="curItem.style.product_lineprice">
				<div class="form-label">划线价：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.productLine_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.productLine_color" placeholder="透明" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'productLine_color', '#999999')" type="primary" link>
						重置
					</el-button>
				</div>
			</div>
			<template v-if="curItem.style.product_btn == 1">
				<!--购买按钮-->
				<div class="form-chink"></div>
				<div class="f16 gray3 form-subtitle">购买按钮</div>
				<el-form-item label="按钮文字："><el-input v-model="curItem.params.btntext" class="w-auto"></el-input></el-form-item>
				<div class="form-item">
					<div class="form-label">按钮颜色：</div>
					<div class="flex-1 d-s-c" style="height: 36px;">
						<el-color-picker size="default" v-model="curItem.style.productLine_btnBackground"></el-color-picker>
						<el-input class="ml10" v-model="curItem.style.productLine_btnBackground" placeholder="透明" />
						<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'productLine_btnBackground', '#FF4C01')" type="primary" link>
							重置
						</el-button>
					</div>
				</div>
				<div class="form-item">
					<div class="form-label">文字颜色：</div>
					<div class="flex-1 d-s-c" style="height: 36px;">
						<el-color-picker size="default" v-model="curItem.style.productLine_btnColor"></el-color-picker>
						<el-input class="ml10" v-model="curItem.style.productLine_btnColor" placeholder="透明" />
						<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'productLine_btnColor', '#ffffff')" type="primary" link>
							重置
						</el-button>
					</div>
				</div>
				<!--上下边距-->
				<div class="form-item">
					<div class="form-label">圆角：</div>
					<el-slider :max="30" v-model="curItem.style.productLine_btnRadius" size="small" show-input :show-input-controls="false" input-size="small"></el-slider>
				</div>
			</template>

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
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'bgcolor', '')" type="primary" link>重置</el-button>
				</div>
			</div>
			<div class="form-item">
				<div class="form-label">组件背景：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.background"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.background" placeholder="透明" />
					<el-button style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'background', '#ffffff')" type="primary" link>重置</el-button>
				</div>
			</div>
		</el-form>
	</div>
</template>

<script>
export default {
	data() {
		return {};
	},
	props: ['curItem', 'selectedIndex'],
	created() {},
	methods: {
		ResetColor(name1, name2) {
			this.$parent.onEditorResetColor(this.curItem.style, name1, '');
			if (name2) {
				this.$parent.onEditorResetColor(this.curItem.style, name2, '');
			}
		}
	}
};
</script>

<style lang="scss" scoped></style>
