<template>

	<el-dialog title="选择商品" :modal-append-to-body="true" :append-to-body="true" v-model="dialogVisible"
		:close-on-click-modal="false" :close-on-press-escape="false" width="900px">
		<!--内容-->
		<div class="product-content">
			<div class="table-wrap">
				<el-table size="small" :data="product_arr" border style="width: 100%" highlight-current-row
					v-loading="loading" @selection-change="tableCurrentChange" ref="tableRef">
					<el-table-column width="70" label="商品图片">
						<template #default="scope">
							<img v-if="scope.row.image" v-img-url="scope.row.image.file_path" :width="30"
								:height="30" />
						</template>
					</el-table-column>
					<el-table-column prop="product_name" label="商品名称"></el-table-column>
					<el-table-column prop="total_num" width="100" label="商品数量"></el-table-column>
					<el-table-column prop="create_time" width="140" label="添加时间"></el-table-column>
					<el-table-column type="selection" :selectable="selectableFunc" width="44"
						v-if="islist"></el-table-column>
					<el-table-column width="80" label="单选" v-if="!islist">
						<template #default="scope">
							<el-button size="small" v-if="scope.row.no_choose"
								@click="SingleFunc(scope.row)">选择</el-button>
							<el-button size="small" v-else disabled>已选</el-button>
						</template>
					</el-table-column>
				</el-table>
			</div>

			<!--分页-->
			<div class="pagination">
				<el-pagination background :current-page="curPage" :page-sizes="[2, 10, 20, 50, 100]"
					:page-size="pageSize" layout="total, prev, pager, next, jumper"
					:total="totalDataNumber"></el-pagination>
			</div>
		</div>
		<template #footer>
			<div class="dialog-footer">
				<el-button size="small" @click="closeCheck">取 消</el-button>
				<el-button size="small" type="primary" @click="addClerk" v-if="islist">确 定</el-button>
			</div>
		</template>
	</el-dialog>
</template>

<script>
export default {
	data() {
		return {
			/*是否加载完成*/
			loading: true,
			/*当前是第几页*/
			curPage: 1,
			/*一页多少条*/
			pageSize: 20,
			/*一共多少条数据*/
			totalDataNumber: 0,
			formInline: {},
			//商品分类列表
			categoryList: [],
			//类别列表
			status: [{
				id: 10,
				name: '上架'
			},
			{
				id: 20,
				name: '下架'
			}
			],
			multipleSelection: [],
			/*左边长度*/
			formLabelWidth: '120px',
			/*是否显示*/
			dialogVisible: false,
			/*结果类别*/
			type: 'error',
			/*传出去的参数*/
			params: null,
			check_ds: null,
			product_arr: [],
			beforeDeliveryNumIds: [],
		};
	},
	props: ['is_product', 'exclude_ids', 'islist', 'product_list'],
	created() {},
	methods: {
		open(beforeDeliveryNumIds) {
			// console.log("之前选中的ids",beforeDeliveryNumIds)
			this.beforeDeliveryNumIds = beforeDeliveryNumIds;
			this.type = 'error';
			this.dialogVisible = true;
			this.loading = false;
			if (this.$refs.tableRef) {
				this.$refs.tableRef.clearSelection();
			}
			console.log('----------------------------------');
			this.$nextTick(() => {
				this.getData();
			});
		},
		/*是否可以勾选*/
		selectableFunc(e) {
			if (typeof e.no_choose !== 'boolean') {
				return true;
			}
			return e.no_choose;
		},
		closeCheck() {
			this.dialogVisible = false;
		},

		// /*选择第几页*/
		// handleCurrentChange (val) {
		//   this.curPage = val;
		//   this.getData();
		// },

		// /*每页多少条*/
		// handleSizeChange (val) {
		//   this.curPage = 1;
		//   this.pageSize = val;
		//   this.getData();
		// },

		/*获取商品列表*/
		getData() {
			let self = this;
			let exclude_ids = this.$props.exclude_ids;
			let product_arr = JSON.parse(JSON.stringify(this.$props.product_list));
			console.log('xxxxxxxxxxxxxxxx');
			console.log(product_arr);
			/*判断是否需要去重*/
			if (self.islist) {
				product_arr.forEach(item => {
					if (exclude_ids && exclude_ids.length > 0) {
						if (exclude_ids.indexOf(item.product_id) > -1) {
							item.no_choose = false;
						} else {
							item.no_choose = true;
						}
					}
					if (item.total_num == item.delivery_num || item.delivery_num > item.total_num) {
						item.no_choose = false;
					}
					if (self.beforeDeliveryNumIds && self.beforeDeliveryNumIds.length > 0) {
						self.beforeDeliveryNumIds.forEach((product) => {
							if (product.product_id == item.product_id) {
								let delivery_num = Number(product.delivery_num);
								item.delivery_num = delivery_num;
							}
						});
					}
				});
			} else {
				if (!self.islist) {
					product_arr.forEach(item => {
						item.no_choose = true;
					});
				}
			}
			// console.log("product_arr",product_arr)
			this.product_arr = product_arr;
		},

		/*单选*/
		SingleFunc(row) {
			this.multipleSelection = [row];
			this.addClerk();
		},

		//点击确定
		addClerk() {
			let self = this;
			let params = null;
			let type = 'success';
			if (self.multipleSelection.length < 1) {
				ElMessage({
					message: '请至少选择一件产品商品！',
					type: 'error'
				});
				return;
			}
			let checkList = [];
			let check_ds = [];
			self.multipleSelection.forEach(item => {
				let obj = {
					order_product_id: item.order_product_id,
					/* 
            @ApiModelProperty("购买数量")
            private Integer total_num;

            @ApiModelProperty("已发货总数量")
            private Integer delivery_num;
              */
					// 最大发货数量
					maxDeliveryNum: Number(item.total_num) - Number(item.delivery_num || 0),
					total_num: item.total_num,
					// 实际填写的发货数量
					delivery_num: Number(item.total_num) - Number(item.delivery_num || 0),
					image_path: item.image ? item.image.file_path : item.image_path,
					product_name: item.product_name,
					product_id: item.product_id,
				};
				checkList.push(obj);
				check_ds.push(item.product_id);
			});
			params = checkList;
			self.params = params;
			self.check_ds = check_ds;
			self.type = 'success';
			this.$emit('closeDialog', {
				params: this.params,
				check_ds: this.check_ds,
			});
			self.dialogVisible = false;
		},

		/*监听复选按钮选中事件*/
		tableCurrentChange(val) {
			let exclude_ids = this.$props.exclude_ids;
			let list = [];
			if (val && val.length > 0) {
				val.forEach((items) => {
					if (!exclude_ids.includes(items.product_id)) {
						list.push(items);
					}
				});
			}
			this.multipleSelection = list;
		}
	}
};
</script>

<style scoped>
	.no-list .el-checkbox {
		display: none;
	}
</style>