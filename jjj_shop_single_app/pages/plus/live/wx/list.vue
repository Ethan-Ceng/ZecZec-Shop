<template>
	<view class="live-wx-list">
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50">
			<view class="list d-s-c f-w">
				<view class="item" v-for="(item, index) in listData" :key="index" @click="gotoLive(item)">
					<view class="pic pr">
						<text class="state" :class="{bg101:item.live_status==101,bg102:item.live_status==102,bg103:item.live_status==103}" v-if="item.live_status==101||item.live_status==102||item.live_status==103">
						{{ convertStatus(item.live_status) }}
						</text>
						<image :src="item.cover_img" mode="aspectFit"></image>
					</view>
					<view class="p-20-0 text-ellipsis f28">{{ item.name }}</view>
				</view>
			</view>

			<!-- 没有记录 -->
			<view>
				<view class="d-c-c p30" v-if="listData.length == 0 && !loading">
					<text class="iconfont icon-wushuju"></text>
					<text class="cont">亲，暂无相关记录哦</text>
				</view>
				<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
			</view>
		</scroll-view>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more.vue';
export default {
	components: {
		uniLoadMore
	},
	data() {
		return {
			/*手机高度*/
			phoneHeight: 0,
			/*可滚动视图区域高度*/
			scrollviewHigh: 0,
			/*底部加载*/
			loading: true,
			/*没有更多*/
			no_more: false,
			/*列表*/
			listData: [],
			/*一页多少条*/
			list_rows: 10,
			/*当前第几页*/
			last_page: 0
		};
	},
	computed: {
		/*加载中状态*/
		loadingType() {
			if (this.loading) {
				return 1;
			} else {
				if (this.listData.length != 0 && this.no_more) {
					return 2;
				} else {
					return 0;
				}
			}
		}
	},
	mounted() {
		this.init();
		this.getLive();
	},
	/*分享*/
	onShareAppMessage() {
		let self = this;
		// 构建页面参数
		let params = self.getShareUrlParams();
		return {
			title: '直播列表',
			path: '/pages/plus/live/wx?' + params
		};
	},
	methods: {
		/*初始化*/
		init() {
			let _this = this;
			uni.getSystemInfo({
				success(res) {
					_this.phoneHeight = res.windowHeight;
					_this.scrollviewHigh = _this.phoneHeight;
					// 计算组件的高度
					/* let view = uni.createSelectorQuery().select('.top-box');
						view.boundingClientRect(data => {
							let h = _this.phoneHeight - data.height;
							_this.scrollviewHigh = h;
						}).exec(); */
				}
			});
		},

		convertStatus(num) {
			let str = '';
			switch (num) {
				case 101:
					str = '直播中';
					break;
				case 102:
					str = '未开始';
					break;
				case 103:
					str = '已结束';
					break;
				case 104:
					str = '禁播';
					break;
				case 105:
					str = '暂停';
					break;
				case 106:
					str = '异常';
					break;
				case 107:
					str = '已过期';
					break;
			}
			return str;
		},

		/*获取商品*/
		getLive() {
			let self = this;
			uni.showLoading({
				title: '加载中'
			});
			let page = self.page;
			let list_rows = self.list_rows;
			self.loading = true;
			self._get(
				'plus.live.wx/lists',
				{
					page: page || 1,
					list_rows: list_rows
				},
				function(res) {
					self.listData = self.listData.concat(res.data.list.data);
					self.last_page = res.data.list.last_page;
					if (res.data.list.last_page <= 1) {
						self.no_more = true;
					}
					self.loading = false;
				}
			);
		},

		/*可滚动视图区域到底触发*/
		scrolltolowerFunc() {
			let self = this;
			self.bottomRefresh = true;
			self.page++;
			self.loading = true;
			if (self.page > self.last_page) {
				self.loading = false;
				self.no_more = true;
				return;
			}
			self.getData();
		},

		/*跳转详情页*/
		gotoLive(item) {
			let roomId = item.roomid;
			let customParams = {
				path: 'pages/index/index'
			};
			wx.navigateTo({
				url: `plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin?room_id=${roomId}&custom_params=${encodeURIComponent(JSON.stringify(customParams))}`
			});
		},
		
	}
};
</script>

<style>
.live-wx-list .list {
	padding: 20rpx;
	background: #ffffff;
}
.live-wx-list .list .item {
	width: 345rpx;
	margin-right: 20rpx;
}
.live-wx-list .list .item:nth-child(2n + 0) {
	margin-right: 0;
}
.live-wx-list .list .item .pic {
	width: 340rpx;
	height: 340rpx;
}
.live-wx-list .list .item .pic .state{ 
	position: absolute;
	top: 20rpx;
	left: 20rpx;
	padding: 4rpx 8rpx;
	background: red;
	color: #FFFFFF;
	border-radius: 8rpx;
}
.live-wx-list .list .item .pic .state.bg101{
	background: red;
}
.live-wx-list .list .item .pic .state.bg102{
	background: #ffb100;
}
.live-wx-list .list .item .pic .state.bg103{
	background: #333333;
}
.live-wx-list .list .item image {
	width: 340rpx;
	height: 340rpx;
	border-radius: 8rpx;
}
</style>
