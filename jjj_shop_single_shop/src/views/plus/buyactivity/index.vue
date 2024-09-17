<template>
	<div class="user">
		<!--搜索表单-->
		<div class="common-seach-wrap">
			<el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
				<el-form-item label="">
					<el-select v-model="formInline.status" placeholder="活动状态">
						<el-option label="全部" :value="-1"></el-option>
						<el-option v-for="(item,index) in status" :key="index" :label="item" :value="index"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="">
					<el-input v-model="formInline.name" placeholder="请输入活动标题"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" icon="Search" @click="onSubmit">查询</el-button>
					<el-button size="small" type="primary" @click="addClick" icon="Plus"
						v-auth="'/plus/buyactivity/add'">添加活动</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="common-level-rail">
			
		</div>
		<div class="product-content point-list">
			<div class="table-wrap">
				<el-table :data="tableData" border style="width: 100%" v-loading="loading">
					<el-table-column prop="buy_id" label="ID"></el-table-column>
					<el-table-column prop="name" label="活动名称"></el-table-column>
					<el-table-column prop="start_time_text" label="开始时间" width="150"></el-table-column>
					<el-table-column prop="end_time_text" label="结束时间" width="150"></el-table-column>
					<el-table-column prop="status_text" label="活动状态" width="120"></el-table-column>
					<el-table-column prop="sort" width="80" label="排序"></el-table-column>
					<el-table-column fixed="right" width="200" label="操作">
						<template #default="scope">
							<el-button v-auth="'/plus/buyactivity/edit'" @click="editClick(scope.row.buy_id)"
								type="text" size="small">
								编辑
							</el-button>
							<el-button v-auth="'/plus/buyactivity/delete'" @click="deleteClick(scope.row.buy_id)"
								type="text" size="small"> 删除
							</el-button>
						</template>
					</el-table-column>
				</el-table>
			</div>
			<!--分页-->
			<div class="pagination">
				<el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" background
					:current-page="curPage" :page-size="pageSize" layout="total, prev, pager, next, jumper"
					:total="totalDataNumber">
				</el-pagination>
			</div>
		</div>
	</div>
</template>
<script>
	import BuyApi from '@/api/buy.js';
	export default {
		components: {},
		data() {
			return {
				form: {},
				tableData: [],
				/*一页多少条*/
				pageSize: 20,
				/*一共多少条数据*/
				totalDataNumber: 0,
				/*当前是第几页*/
				curPage: 1,
				/*是否加载完成*/
				loading: true,
				formInline: {
					status: -1,
					name: ''
				},
				status: ['未开始', '进行中', '已结束']
			};
		},
		created() {
			/*获取列表*/
			this.getTableList();
		},
		methods: {
			/*获取列表*/
			getTableList() {
				let self = this;
				let Params = this.formInline;
				Params.page = self.curPage;
				Params.list_rows = self.pageSize;
				self.loading = true;
				BuyApi.BuyList(Params, true)
					.then(data => {
						self.loading = false;
						self.tableData = data.data.list.data;
						self.totalDataNumber = data.data.list.total
					}).catch(error => {
						self.loading = false;
					});
			},
			/*选择第几页*/
			handleCurrentChange(val) {
				let self = this;
				self.curPage = val;
				self.loading = true;
				self.getTableList();
			},

			/*每页多少条*/
			handleSizeChange(val) {
				let self = this;
				self.curPage = 1;
				self.pageSize = val;
				self.getTableList();
			},
			/*搜索查询*/
			onSubmit() {
				this.curPage = 1;
				this.getTableList();
			},
			/*添加*/
			addClick() {
				this.$router.push({
					path: '/plus/buyactivity/add'
				});
			},
			/*编辑*/
			editClick(e) {
				let self = this;
				this.$router.push({
					path: '/plus/buyactivity/edit',
					query: {
						buy_id: e
					}
				})
			},

			/*删除*/
			deleteClick(e) {
				let self = this;
				ElMessageBox.confirm('此操作将永久删除该记录,是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					self.loading = true;
					BuyApi.delBuy({
						buy_id: e
					}, true).then(data => {
						self.loading = false;
						ElMessage({
							message: data.msg,
							type: 'success'
						});
						self.getTableList();

					}).catch(error => {
						self.loading = false;
					});

				}).catch(() => {
					self.loading = false;
					ElMessage({
						type: 'info',
						message: '已取消删除'
					});
				});
			},
		}
	};
</script>

<style>
	.point-list .el-input-number--mini {
		width: auto;
	}
</style>