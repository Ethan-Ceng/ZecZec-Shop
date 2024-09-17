<template>
	<view @click.stop>
		<!-- #ifdef MP-WEIXIN -->
		<view class="foot-bottom" v-if="!isScroll"></view>
		<!-- #endif -->
		<!-- #ifndef MP-WEIXIN -->
		<view class="foot-bottom"></view>
		<!-- #endif -->
		<view class="foot-tavbar-container d-a-c" :style="'background:'+detail.backgroundColor || '' + ';' ">
			<template v-for="(item, index) in detail.list" :key="index">
				<view class="item d-c-c" :class="{'active':item.link_url == getRoute()}" @click="tabBarFunc(item)">
					<view style="height: 0;width: 0;opacity: 0;">{{getRoute()}}</view>
					<view class="inner d-c-c d-c">
						<image v-if="detail.type!=2"
							:src="item.link_url == getRoute()?item.selectedIconPath:item.iconPath" mode="aspectFill">
						</image>
						<template v-if="detail.type!=1">
							<text
								:style="item.link_url == getRoute()?'color:'+detail.textHoverColor+';':'color:'+detail.textColor+';'"
								class="text-name">{{ item.text}}</text>
						</template>
					</view>
				</view>
			</template>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				/*当前选中*/
				activeTabber: '首页',
				/*打开直播菜单*/
				open_liveMenu: false,
				/*底部菜单*/
				detail: {
					list: []
				},
				hasTab: false
			};
		},
		props: {
			isScroll: {
				default: false
			}
		},
		created() {
			let pages = getCurrentPages();
			if (pages.length) {
				let currentPage = pages[pages.length - 1];
				if (currentPage.route == 'pages/index/index') {
					this.$store.commit('changefootTab', '首页');
				}
			}
			this.getData();
		},
		methods: {
			getNav() {
				let theme = this.$store.state.theme;
				let data = {
					backgroundColor: "#FFFFFF",
					is_auto: "0",
					textColor: "#000000",
					textHoverColor: this.getThemeColor(),
					type: "0",
					list: [{
						iconPath: "/static/tabbar/home.png",
						link_url: "/pages/index/index",
						selectedIconPath: `/static/tabbar/home_${theme}.png`,
						text: "首页"
					}, {
						iconPath: "/static/tabbar/category.png",
						link_url: "/pages/product/category",
						selectedIconPath: `/static/tabbar/category_${theme}.png`,
						text: "分类"
					}, {
						iconPath: "/static/tabbar/cart.png",
						is_show: true,
						link_url: "/pages/cart/cart",
						selectedIconPath: `/static/tabbar/cart_${theme}.png`,
						text: "购物车"
					}, {
						iconPath: "/static/tabbar/user.png",
						is_show: true,
						link_url: "/pages/user/index/index",
						selectedIconPath: `/static/tabbar/user_${theme}.png`,
						text: "我的",
					}]
				}
				this.detail = data;
				uni.setStorageSync('TabBar', data);
			},
			// hasmenu() {
			// 	let self = this;
			// 	let curRoute = self.getRoute();
			// 	let res = self.detail.list.some(item => {
			// 		if (item.link_url == curRoute) {
			// 			return true
			// 		}
			// 	})
			// 	self.hasTab = res;
			// 	if (uni.getStorageSync('TabBar').is_auto && uni.getStorageSync('TabBar').is_auto != 0) {
			// 		uni.hideTabBar()
			// 	}
			// },
			getRoute() {
				let self = this;
				let routes = getCurrentPages(); // 获取当前打开过的页面路由数组
				let curRoute = '/' + routes[routes.length - 1].route //获取当前页面路由
				if (curRoute == '/pages/diy-page/diy-page' || curRoute == '/pages/article/detail/detail') {
					if (routes[routes.length - 1]['$page']) {
						curRoute = routes[routes.length - 1]['$page'].fullPath
					}
				}
				return curRoute
			},
			/*点击菜单*/
			tabBarFunc(e) {
				if (this.footTabberData.active == e.text) {
					return
				}
				this.$store.commit('changefootTab', e.text);
				this.gotoPage(e.link_url);
			},
			/*获取首页分类*/
			getData() {
				let self = this;
				self._get('index/nav', {}, function(res) {
					self.detail = res.data.vars.data;
					if (!self.detail || self.detail.is_auto == '0') {
						self.getNav();
					} else {
						console.log('is_auto:1')
						uni.setStorageSync('TabBar', self.detail);
					}
					// this.hasmenu();
					// self.hasmenu();
				});
			},

		}
	};
</script>

<style lang="scss">
	$footer-tabBar-height: 50px;

	.foot-bottom {
		width: 100%;
		height: $footer-tabBar-height;
		padding-bottom: env(safe-area-inset-bottom);
	}

	.foot-tavbar-container {
		box-shadow: 0 0 6rpx 0 rgba(0, 0, 0, 0.3);
		position: fixed;
		right: 0;
		bottom: 0;
		left: 0;
		height: $footer-tabBar-height;
		background: #FFFFFF;
		z-index: 90;
		padding-bottom: env(safe-area-inset-bottom);
	}

	.foot-tavbar-container .item {
		flex: 1;
		height: $footer-tabBar-height;
	}

	.foot-tavbar-container .item.add-btn .inner {
		margin-bottom: 70rpx;
		width: 100rpx;
		height: $footer-tabBar-height;
		border-radius: 50%;
		background: $dominant-color;
		box-shadow: 0 0 10rpx 0 rgba(232, 38, 100, .6);
	}

	.foot-tavbar-container image {
		width: 50rpx;
		height: 50rpx;
	}

	.foot-tavbar-container .text-name {
		font-size: 24rpx;
		color: #666666;
	}

	.foot-tavbar-container .item.active .text-name {
		color: #f8c341;
	}
</style>