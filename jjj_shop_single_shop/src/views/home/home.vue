<template>
	<div class="home" v-loading="loading">
		<!-- 第一行 -->
		<div class="home-t1">
			<div class="home-t1-item">
				<div class="t1-item-t">
					<div class="t1-item-t-tips">今日</div>
					<div class="f14 mb10">商品</div>
					<div class="f30 fb mb10">{{top_data.product_today}}</div>
					<div class="d-s-c f13" :class="top_data.product_rate>0?'up':'down'">
						<div class="gray6 mr25">昨日：{{top_data.product_yesterday}}</div>
						<div>{{top_data.product_rate}}%</div>
						<div class="icon iconfont icon-shuzhixiajiang" v-if="top_data.product_rate<0"></div>
						<div class="icon iconfont icon-shuzhishangsheng" v-if="top_data.product_rate>0"></div>
					</div>
				</div>
				<div class="t1-item-b">
					<div>累计商品总数</div>
					<div>{{top_data.product_total}}</div>
				</div>
			</div>
			<div class="home-t1-item">
				<div class="t1-item-t">
					<div class="t1-item-t-tips">今日</div>
					<div class="f14 mb10">用户</div>
					<div class="f30 fb mb10">{{top_data.user_today}}</div>
					<div class="d-s-c f13" :class="top_data.user_rate>0?'up':'down'">
						<div class="gray6 mr25">昨日：{{top_data.user_yesterday}}</div>
						<div>{{top_data.user_rate}}%</div>
						<div class="icon iconfont icon-shuzhixiajiang" v-if="top_data.user_rate<0"></div>
						<div class="icon iconfont icon-shuzhishangsheng" v-if="top_data.user_rate>0"></div>
					</div>
				</div>
				<div class="t1-item-b">
					<div>累计用户总数</div>
					<div>{{top_data.user_total}}</div>
				</div>
			</div>
			<div class="home-t1-item">
				<div class="t1-item-t">
					<div class="t1-item-t-tips">今日</div>
					<div class="f14 mb10">订单</div>
					<div class="f30 fb mb10">{{top_data.order_today}}</div>
					<div class="d-s-c f13" :class="top_data.order_rate>0?'up':'down'">
						<div class="gray6 mr25">昨日：{{top_data.order_yesterday}}</div>
						<div>{{top_data.order_rate}}%</div>
						<div class="icon iconfont icon-shuzhixiajiang" v-if="top_data.order_rate<0"></div>
						<div class="icon iconfont icon-shuzhishangsheng" v-if="top_data.order_rate>0"></div>
					</div>
				</div>
				<div class="t1-item-b">
					<div>累计订单</div>
					<div>{{top_data.order_total}}</div>
				</div>
			</div>
			<div class="home-t1-item">
				<div class="t1-item-t">
					<div class="t1-item-t-tips">今日</div>
					<div class="f14 mb10">评价</div>
					<div class="f30 fb mb10">{{top_data.comment_today}}</div>
					<div class="d-s-c f13" :class="top_data.comment_rate>0?'up':'down'">
						<div class="gray6 mr25">昨日：{{top_data.comment_yesterday}}</div>
						<div>{{top_data.comment_rate}}%</div>
						<div class="icon iconfont icon-shuzhixiajiang" v-if="top_data.comment_rate<0"></div>
						<div class="icon iconfont icon-shuzhishangsheng" v-if="top_data.comment_rate>0"></div>
					</div>
				</div>
				<div class="t1-item-b">
					<div>累计评价总数</div>
					<div>{{top_data.comment_total}}</div>
				</div>
			</div>
		</div>
		<!-- 第二行 -->
		<div class="d-b-s home-t2">
			<div class="flex-1 home-t2-l">
				<div class="d-s-c home-t ">
					<div class="common-form ">销售额概况</div>
					<div class="f13 gray9 flex-1">更新时间：{{update_time}}</div>
					<div class="">
						<el-radio-group v-model="formData.sale_time" size="small" @change="getData()">
							<el-radio-button :label="1">近7天</el-radio-button>
							<el-radio-button :label="2">近15天</el-radio-button>
							<el-radio-button :label="3">近30天</el-radio-button>
						</el-radio-group>
					</div>
				</div>
				<div>
					<div class="home-t2-l-t">
						<div class="d-s-c">
							<span class="f16">销售金额(元)</span>
							<el-popover placement="top-start" :width="200" trigger="hover" content="统计时间内，订单的实际支付金额。">
								<template #reference>
									<el-icon size="20px" color="#999">
										<InfoFilled />
									</el-icon>
								</template>
							</el-popover>
						</div>
						<div class="f30 fb">{{saleData && saleData.saleMoney}}</div>
					</div>
					<div>
						<SaleChart ref="SaleChart" :listData="saleData"></SaleChart>
					</div>

				</div>
			</div>
			<div class="flex-1 home-t2-r">
				<div class="d-s-c home-t">
					<div class="common-form">待办事项 </div>
					<div class="f13 gray9 flex-1">请尽快处理，以免影响营业</div>
				</div>
				<div class="t2-b">
					<div class="t2-b-title">订单</div>
					<div class="d-s-c t2-b-content">
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/order/order/index')">
								{{wait_data.order.disposal}}
							</div>
							<div class="f13">待处理</div>
						</div>
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/order/refund/index')">
								{{wait_data.order.refund}}
							</div>
							<div class="f13">待售后</div>
						</div>
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/plus/card/event')">
								{{wait_data.order.card_count}}
							</div>
							<div class="f13">提货卡待发货</div>
						</div>
					</div>
					<div class="t2-b-title">分销商</div>
					<div class="d-s-c t2-b-content">
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/plus/agent/index')">
								{{wait_data.agent.apply}}
							</div>
							<div class="f13">入驻审核</div>
						</div>
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/plus/agent/index?type=cash')">
								{{wait_data.agent.cash_apply}}
							</div>
							<div class="f13">提现审核</div>
						</div>
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/plus/agent/index?type=cash')">
								{{wait_data.agent.cash_money}}
							</div>
							<div class="f13">待打款审核</div>
						</div>
					</div>
					<div class="t2-b-title">审核</div>
					<div class="d-s-c t2-b-content">
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/user/balance/index?type=cash')">
								{{wait_data.review.balance_apply}}
							</div>
							<div class="f13">余额提现</div>
						</div>
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/user/balance/index?type=cash')">
								{{wait_data.review.balance_money}}
							</div>
							<div class="f13">余额待打款</div>
						</div>
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/product/comment/index')">
								{{wait_data.review.comment}}
							</div>
							<div class="f13">商品评价</div>
						</div>
					</div>
					<div class="t2-b-title">库存</div>
					<div class="d-s-c t2-b-content">
						<div class="t2-b-item">
							<div class="f20 fb t2-r-number" @click="gotoPage('/product/product/index')">
								{{wait_data.stock.product}}
							</div>
							<div class="f13">库存告急</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="home-t3">
			<div class="flex-1 home-t3-l">
				<div class="d-s-c home-t">
					<div class="common-form flex-1">用户数据</div>
					<div class="">
						<el-radio-group v-model="formData.user_time" size="small" @change="getData()">
							<el-radio-button :label="1">近7天</el-radio-button>
							<el-radio-button :label="2">近15天</el-radio-button>
							<el-radio-button :label="3">近30天</el-radio-button>
						</el-radio-group>
					</div>
				</div>
				<div>
					<UserChart ref="UserChart" :listData="userData"></UserChart>
				</div>
			</div>
			<div class="flex-1 home-t3-r">
				<div class="d-s-c home-t">
					<div class="common-form flex-1">商品销售情况排行</div>
					<div class="">
						<el-radio-group v-model="formData.product_time" size="small" @change="getData()">
							<el-radio-button :label="1">近30天</el-radio-button>
							<el-radio-button :label="2">本日</el-radio-button>
							<el-radio-button :label="3">近7天</el-radio-button>
							<el-radio-button :label="4">本月</el-radio-button>
							<el-radio-button :label="5">本年</el-radio-button>
						</el-radio-group>
					</div>
				</div>
				<el-table class="rank-table" :data="productRank" @sort-change="sortChange">
					<el-table-column prop="index" label="排名" width="80px">
						<template #default="scope">
							<div class="rankball" :class="'rankball_' + scope.$index">
								{{ scope.$index + 1 }}
							</div>
						</template>
					</el-table-column>
					<el-table-column prop="product_name" label="商品名称"></el-table-column>
					<el-table-column align="center" sortable="custom" prop="total_num" label="销量"
						width="120px"></el-table-column>
					<el-table-column align="center" sortable="custom" prop="total_price" label="销售额"
						width="120px"></el-table-column>
				</el-table>
			</div>
		</div>
	</div>
</template>

<script>
	import indexApi from '@/api/index.js';
	import SaleChart from '@/views/home/part/SaleChart.vue';
	import UserChart from '@/views/home/part/UserChart.vue';
	export default {
		components: {
			SaleChart,
			UserChart
		},
		data() {
			return {
				/*是否加载完成*/
				loading: true,
				/*统计信息*/
				top_data: {},
				/*待办事项*/
				wait_data: {
					order: {},
					agent: {},
					review: {},
					stock: {}
				},
				productRank: [],
				formData: {
					/* 1,2,3,4 */
					product_type: 2,
					/* 1今日  2 7天 3本月 4本年*/
					product_time: 1,
					sale_time: 1,
					user_time: 1
				},
				type: 1,
				/* 销售额折线图列表 */
				saleData: null,
				userData: null,
				update_time: ''

			};
		},
		created() {
			console.log('created');
			/*获取数据*/
			this.getData();
		},
		methods: {
			sortChange(column) {
				console.log(column);
				//判断排序规则
				if (column.order) {
					/* 降序 */
					if (column.order === "descending") {
						/*  */
						if (column.prop == "total_price") {
							this.formData.product_type = 4;
							console.log("销售额降序");
						} else {
							this.formData.product_type = 2;
							console.log("销量降序");
						}
					} /* 升序*/
					else if (column.order === "ascending") {
						if (column.prop == "total_price") {
							this.formData.product_type = 3;
							console.log("销售额升序");
						} else {
							this.formData.product_type = 1;
							console.log("销量升序");
						}
					}
				} else {
					this.formData.product_type = 0;
				}
				//调用查询list接口
				this.getData();
			},
			/*获取数据*/
			getData() {
				let self = this;
				indexApi.getCount(this.formData).then(data => {
					self.loading = false;
					let res = data.data.data;
					self.saleData = res.saleData;
					self.userData = res.userData;
					self.top_data = res.top_data;
					self.wait_data = res.wait_data;
					self.productRank = res.productRank;
					self.update_time = res.update_time;
					self.$refs.UserChart.changeData(self.userData);
					self.$refs.SaleChart.changeData(self.saleData);
				}).catch(error => {

				});
			},
			gotoPage(url) {
				this.$router.push(url);
			}
		}
	};
</script>

<style lang="scss" scoped>
	.subject-wrap>.home {
		background: none;
		padding: 0;
		margin: 13px;
	}

	.home-t1 {
		display: flex;
		justify-content: center;
		align-items: center;
		margin-bottom: 14px;

		.home-t1-item {
			position: relative;
			flex: 1;
			margin-right: 12px;
			padding: 27px 12px 0 12px;
			background-color: #fff;

			// height: 192px;
			.t1-item-t {
				border-bottom: 1px solid #EBF3FC;
				height: 114px;

				.t1-item-t-tips {
					border: 1px solid #3A8EE6;
					border-radius: 5px;
					color: #3A8EE6;
					font-size: 14px;
					background: #EBF3FC;
					width: 38px;
					height: 28px;
					display: flex;
					justify-content: center;
					align-items: center;
					position: absolute;
					right: 12px;
					top: 20px;
				}

				.up {
					color: #EA7C7C;

					.icon-shuzhishangsheng {
						color: #EA7C7C;
						font-size: 12px;
					}
				}

				.down {
					color: #5EC3C3;

					.icon-shuzhixiajiang {
						color: #5EC3C3;
						font-size: 12px;
					}
				}
			}

			.t1-item-b {
				font-size: 13px;
				color: #666;
				display: flex;
				justify-content: space-between;
				align-items: center;
				height: 50px;
			}
		}

		.home-t1-item:last-child {
			margin: 0;
		}
	}

	.home-t2 {
		margin-bottom: 14px;


		.home-t2-l,
		.home-t2-r {
			height: 532px;
		}

		.home-t2-l {
			background-color: #fff;
			margin-right: 13px;
			padding: 0 12px;

			.home-t2-l-t {
				padding-top: 16px;
			}
		}

		.home-t2-r {
			background-color: #fff;
			padding: 0 12px;

			.t2-b {
				padding-top: 20px;

				.t2-b-title {
					color: #333;
					font-size: 14px;
					margin-bottom: 6px;
				}

				.t2-b-content {
					margin-bottom: 12px;
				}

				.t2-b-item {
					width: 20%;
					display: flex;
					justify-content: flex-start;
					align-items: flex-start;
					flex-direction: column;

					.t2-r-number {
						cursor: pointer;
						color: #3A8EE6;
					}
				}

			}
		}

	}

	.home-t3 {
		display: flex;
		justify-content: space-between;
		align-items: flex-start;

		.home-t3-l {
			background-color: #fff;
			margin-right: 13px;
			padding: 0 12px;
		}

		.home-t3-r {
			background-color: #fff;
			padding: 0 12px;

			:deep(.el-table th.el-table__cell) {
				background-color: #fff;
			}

			:deep(.el-table td.el-table__cell) {
				border: none;
			}

			:deep(.el-table th.el-table__cell.is-leaf) {
				border: none;
			}

			:deep(.el-table__inner-wrapper::before) {
				display: none;
			}

			.rankball {
				width: 26px;
				height: 26px;
				border-radius: 50%;
				background: #D0D0D0;
				font-size: 14px;
				color: #FFFFFF;
				display: flex;
				justify-content: center;
				align-items: center;
			}

			.rankball_0 {
				background: #f1aaaa;
			}

			.rankball_1 {
				background: #ABB4C7;
			}

			.rankball_2 {
				background: #EBCA80;
			}
		}

		.home-t3-l,
		.home-t3-r {
			height: 420px;
		}
	}

	.home-t {
		height: 60px;
		border-bottom: 1px solid #eee;
	}

	.common-form {
		font-size: 16px;
		margin: 0;
		padding-left: 12px;
		margin-right: 17px;
	}
	.mr25{
		margin-right: 25px;
	}
</style>