<template>
	<div class="phone-top" v-if="diyData.page" @click.stop="$parent.onEditer(-1)"
		:style="'background:' + bgColor">
		<div class="status-bar">
			<span class="icon iconfont icon-wifi" :style="'color:' + diyData.page.style.titleTextColor">
			</span>
			<span class="time" :style="'color:' + diyData.page.style.titleTextColor">19:00</span>
			<span class="icon iconfont icon-xinhao" :style="'color:' + diyData.page.style.titleTextColor">
			</span>
			<span class="ml4 icon iconfont icon-iconset0250" :style="'color:' + diyData.page.style.titleTextColor">
			</span>
		</div>
		<template v-if="!isDiy">
			<div class="navigation d-s-c">
				<div v-if="diyData.page.params.title_type == 'text'"
					:style="'color:' + diyData.page.style.titleTextColor">
					<span>{{diyData.page.params.title}}</span>
				</div>
				<div v-else>
					<img style="width: 100%;height: 30px;" v-img-url="diyData.page.params.toplogo" alt="" />
				</div>
				<!-- <div class="phone-top-search-box" :style="'color:' + diyData.page.style.titleBackgroundColor == '#ffffff' ? '#ccc' : ''"> -->
				<div class="phone-top-search-box" v-if="!diyData.page.style.hide_search">
					<i class="Search"></i>搜索商品
				</div>
				<!-- <div style="width: 100px;" class=""></div> -->
			</div>
			<div v-if="diyData.page.category.open" class="d-s-c" :style="'color:' +  diyData.page.category.color">
				<div class="f16 mr10">分类一</div>
				<div class="f16 mr10">分类二</div>
				<div class="f16 mr10">分类三</div>
				<div class="f16 mr10">分类四</div>
			</div>
		</template>

	</div>
</template>

<script>
	export default {
		data() {
			return {};
		},
		props: {
			diyData: Object,
			isDiy: Boolean
		},
		computed: {
			bgColor(){
				return this.$props.isDiy? '#fff' : this.$props.diyData.page.style.titleBackgroundColor
			},
			searchStyle() {
				const colorRgb = this.$props.diyData.page.style.titleBackgroundColor || '#999';
				const activeList = ['#ffffff', '#FFFFFF'];
				const flag = activeList.includes(colorRgb);
				return {
					color: flag ? '#ccc' : colorRgb,
					borderColor: flag ? '#ccc' : colorRgb,
				}
			},
		},
		methods: {
			getImg(src) {
				var img_url = src
				var img = new Image()
				img.src = img_url;
				return Math.ceil(img.width * 60 / img.height) + 'px'
			},
		}
	};
</script>

<style>
	.phone-top-search-box {
		flex: 1;
		height: 32px;
		line-height: 32px;
		background-color: #f7f7f7;
		border-radius: 3px;
		margin-left: 10px;
		color: #999999;
		font-size: 13px;
		text-align: left;
		padding-left: 10px;
		border-radius: 15px;
		font-weight: 400;
		border: 1px solid transparent;
	}

	.phone-top-search-box .Search {
		font-weight: 800;
		margin-right: 4px;
	}

	.navigation>img {
		width: 30px;
		height: 30px;
	}
</style>