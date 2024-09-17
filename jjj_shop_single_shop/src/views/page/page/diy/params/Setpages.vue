<template>
	<div>
		<div class="common-form">
			<span>{{ curItem.name }}</span>
		</div>
		<div class="f16 gray3 form-subtitle">样式设置</div>
		<el-form size="small" :model="curItem" label-width="100px">

			<template v-if="!isDiy">
				<!--页面标题-->
				<el-form-item label="标题类型：">
					<el-radio-group @change="logechange" v-model="curItem.params.title_type">
						<el-radio-button :label="'text'">文本</el-radio-button>
						<el-radio-button :label="'image'">图片</el-radio-button>
					</el-radio-group>
				</el-form-item>
				<div class="form-item" v-if="curItem.params.title_type == 'image'">
					<div class="form-label">标题内容：</div>
					<div class="diy-setpages-cover">
						<div :style="'background-color:' + curItem.style.titleBackgroundColor + ' ;'">
							<img v-img-url="curItem.params.toplogo" :width="120" alt=""
								@click="$parent.onEditorSelectImage(curItem.params, 'toplogo')" />
						</div>

						<div>建议尺寸高度 X*60px</div>
					</div>
				</div>
				<div class="form-item" v-if="curItem.params.title_type == 'text'">
					<div class="form-label">标题内容：</div>
					<el-input v-model="curItem.params.title"></el-input>
				</div>
				<div class="form-item" v-if="curItem.params.title_type == 'text'">
					<div class="form-label">logo文字颜色：</div>
					<div class="flex-1 d-s-c" style="height: 36px;">
						<el-color-picker size="default" v-model="curItem.style.titleTextColor"></el-color-picker>
						<el-input class="ml10" v-model="curItem.style.titleTextColor" />
						<el-button style="margin-left: 10px;"
							@click.stop="$parent.onEditorResetColor(curItem.style, 'titleTextColor', '#333')"
							type="primary" link>重置</el-button>
					</div>
				</div>
			</template>
			<!--页面名称-->
			<div class="form-item" style="margin-bottom: 4px;">
				<div class="form-label">页面名称：</div>
				<el-input v-model="curItem.params.name"></el-input>
			</div>
			<p class="gray" style="margin-left: 60px;margin-bottom: 16px;width: auto;">页面名称仅用于后台查找</p>
			<!--分享标题-->
			<div class="form-item" style="margin-bottom: 4px;">
				<div class="form-label">分享标题：</div>
				<el-input v-model="curItem.params.share_title"></el-input>
			</div>
			<p class="gray" style="margin-left: 60px;margin-bottom: 16px;width: auto;">小程序端转发时显示的标题</p>
			<div class="form-item" style="margin-bottom: 4px;">
				<div class="form-label">分享logo：</div>
				<div><img :width="40" v-img-url="curItem.params.share_img" alt=""
						@click="$parent.onEditorSelectImage(curItem.params, 'share_img')" /></div>
			</div>
			<p class="gray" style="margin-left: 60px;margin-bottom: 16px;width: auto;">公众号分享logo，建议尺寸80×80</p>

			<template v-if="!isDiy">
				<!-- 背景颜色 -->
				<div class="form-item">
					<div class="form-label">背景颜色：</div>
					<div class="flex-1 d-s-c" style="height: 36px;">
						<el-color-picker size="default" v-model="curItem.style.titleBackgroundColor"></el-color-picker>
						<el-input class="ml10" v-model="curItem.style.titleBackgroundColor" />
						<el-button style="margin-left: 10px;"
							@click.stop="$parent.onEditorResetColor(curItem.style, 'titleBackgroundColor', '#333')"
							type="primary" link>
							重置
						</el-button>
					</div>
				</div>
				<!--商品分类-->
				<div class="form-item" style="margin-bottom: 4px;">
					<div class="form-label">隐藏搜索框：</div>
				</div>
				<el-form-item label=" ">
					<el-radio-group @change="categorySearch" v-model="curItem.style.hide_search">
						<el-radio-button :label="0">显示</el-radio-button>
						<el-radio-button :label="1">隐藏</el-radio-button>
					</el-radio-group>
				</el-form-item>
				<!--商品分类-->
				<div class="form-item" style="margin-bottom: 4px;">
					<div class="form-label">商品分类：</div>
					<div class="gray9">仅首页有效</div>
				</div>
				<el-form-item label=" ">
					<el-radio-group @change="categorychange" v-model="curItem.category.open">
						<el-radio-button :label="1">开启</el-radio-button>
						<el-radio-button :label="0">关闭</el-radio-button>
					</el-radio-group>
				</el-form-item>
				<!-- 分类颜色 -->
				<div class="form-item">
					<div class="form-label">分类颜色：</div>
					<div class="flex-1 d-s-c" style="height: 36px;">
						<el-color-picker size="default" v-model="curItem.category.color"></el-color-picker>
						<el-input class="ml10" v-model="curItem.category.color" />
						<el-button style="margin-left: 10px;"
							@click.stop="$parent.onEditorResetColor(curItem.category, 'color', '#ffffff')"
							type="primary" link>重置</el-button>
					</div>
				</div>
			</template>

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
		props: ['curItem', 'selectedIndex', 'opts','isDiy'],
		created() {},
		methods: {
			logechange(e) {
				console.log(e);
				this.curItem.params.title_type = e;
			},
			categorychange(e) {
				this.curItem.category.open = e;
			},
			categorySearch(e) {
				this.curItem.style.is_search = e;
			}
		}
	};
</script>

<style lang="scss" scoped>
	.diy-setpages-cover>img {
		width: 60px;
		height: 60px;
	}
</style>