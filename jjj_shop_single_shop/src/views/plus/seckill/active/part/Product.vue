<template>

	<div>
		<div class="common-form">活动商品</div>

		<el-form-item label="商品管理" prop="tableData" :rules="rulesProduct">
			<el-button icon="Plus" @click="changeProduct(form.tableData)">选择商品</el-button>
			<div class="d-s-c pt16 max-w460 active-all-set">
				<div style="width: 60px;">批量设置：</div>
				<div class="flex-1">
					<el-input v-model="all_seckill_price" type="number" min="0" placeholder="秒杀价"></el-input>
				</div>
				<div class="flex-1 ml10">
					<el-input v-model="all_seckill_stock" type="number" min="0" placeholder="秒杀库存"></el-input>
				</div>
				<div class="flex-1 ml10">
					<el-input v-model="all_limit_num" type="number" min="0" placeholder="限购数量"></el-input>
				</div>
				<div class="ml10"><el-button @click="allApply">应用</el-button></div>
			</div>
			<div class="pt16">
				<el-table size="small" :data="form.tableData" :span-method="objectSpanMethod" border
					style="width: 100%;">
					<el-table-column label="商品名称" width="300">
						<template #default="scope">
							<p class="text-ellipsis-2">{{ scope.row.product_name }}</p>
						</template>
					</el-table-column>
					<el-table-column label="规格" width="180">
						<template #default="scope">
							<div class="gray" v-if="scope.row.spec_type == 10">暂无规格</div>
							<div v-if="scope.row.spec_type == 20">
								<div class="spec-min-box"
									v-if="scope.row.spec_name != null && scope.row.spec_name != ''">
									<p class="text-ellipsis" :title="scope.row.spec_name">{{ scope.row.spec_name }}</p>
									<el-icon class="el-icon-close" @click="deleteClick(scope.$index)">
										<CloseBold />
									</el-icon>
									<el-icon class="el-icon-caret-right"
										@click="specsFunc(scope.$index, scope.row, true)">
										<CaretRight />
									</el-icon>

								</div>
								<el-button v-else size="small" icon="Plus"
									@click="specsFunc(scope.$index, scope.row)">添加规格</el-button>
							</div>
						</template>
					</el-table-column>
					<el-table-column prop="product_price" label="售价"></el-table-column>
					<el-table-column label="秒杀价">
						<template #default="scope">
							<el-input size="small" :class="{isvalid:scope.row.priceValid==true}" type="number" min="0"
								step="0.01" v-model="scope.row.seckill_price"></el-input>
						</template>
					</el-table-column>
					<el-table-column label="秒杀库存">
						<template #default="scope">
							<el-input size="small" :class="{isvalid:scope.row.stockValid==true}" type="number" min="0"
								v-model="scope.row.seckill_stock"></el-input>
						</template>
					</el-table-column>
					<el-table-column prop="sales_num" label="已售"></el-table-column>
					<el-table-column label="限购数量">
						<template #default="scope">
							<el-input size="small" :class="{isvalid:scope.row.limit_numValid==true}" type="number"
								min="1" v-model="scope.row.limit_num"></el-input>
						</template>
					</el-table-column>
					<el-table-column label="虚拟销量">
						<template #default="scope">
							<el-input size="small" :class="{isvalid:scope.row.sortValid==true}" type="number" min="0"
								v-model="scope.row.sales_initial"></el-input>
						</template>
					</el-table-column>
					<el-table-column label="排序">
						<template #default="scope">
							<el-input size="small" :class="{isvalid:scope.row.sortValid==true}" type="number" min="0"
								v-model="scope.row.sort"></el-input>
						</template>
					</el-table-column>
					<el-table-column fixed="right" label="操作" :width="type == 'edit' ? 140 : 60">
						<template #default="scope">
							<el-button v-if="type == 'edit'" @click="showFunc(scope.$index, scope.row)" type="text"
								size="small">{{ scope.row.is_delete == 0 ? '隐藏' : '显示' }}</el-button>
							<el-button @click="deleteClick(scope.$index, true)" type="text" size="small">删除</el-button>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</el-form-item>

		<!--商品选择-->
		<Product :isproduct="isproduct" :excludeIds="excludeIds" :islist="true" @closeDialog="closeDialogFunc">
		</Product>

		<!--规格选择-->
		<Specs :isspecs="isspecs" :productId="curProduct.product_id" :excludeIds="SpecExcludeIds" :islist="true"
			@close="closeSpecs"></Specs>
	</div>
</template>

<script>
	import PorductApi from '@/api/product.js';
	import Product from '@/components/product/Product.vue';
	import Specs from '@/components/product/Specs.vue';
	import {
		mergeTable
	} from '@/utils/table.js';
	export default {
		components: {
			/*选择商品组件*/
			Product,
			/*选择规格*/
			Specs
		},
		data() {

			/*验证公司名称*/
			const validatorProduct = (rule, value, callback) => {
				if (value.length < 1) {
					return callback(new Error('请选择商品'));
				} else {
					let flag = false;
					for (let i = 0; i < this.form.tableData.length; i++) {
						let item = this.form.tableData[i];

						if (!flag) {
							let reg = item.seckill_price == '' || item.seckill_stock == '' || item.limit_num == '' ||
								item.sort == '';
							let reg2 = parseFloat(item.seckill_price) == 0 || parseInt(item.seckill_stock) < 1 ||
								parseInt(item.limit_num) < 1 || (item.spec_type == 20 && !item.spec_name);
							if (reg || reg2) {
								flag = true;
							}
						}

						if (parseInt(item.seckill_stock) > 0) {
							item.stockValid = true;
						} else {
							item.stockValid = false;
						}

						if (parseInt(item.seckill_price) > 0) {
							item.priceValid = true;
						} else {
							item.priceValid = false;
						}

						if (parseInt(item.limit_num) > 0) {
							item.limit_numValid = true;
						} else {
							item.limit_numValid = false;
						}

						if (parseInt(item.sort) > 0) {
							item.sortValid = true;
						} else {
							item.sortValid = false;
						}
						this.form.tableData[i] = item;
					}
					if (flag) {
						return callback(new Error('请填写正确的信息'));
					} else {
						callback();
					}

				}
			};

			return {
				/*是否打开选择商品*/
				isproduct: false,
				/*产品去重*/
				excludeIds: [],
				/*是否打开规格选择*/
				isspecs: false,
				/*查询规格当前商品*/
				curProduct: {},
				/*当前的角标索引*/
				curIndex: -1,
				/*选择规格*/
				specType: false,
				/*规格去重*/
				SpecExcludeIds: [],
				/*批量秒杀价*/
				all_seckill_price: null,
				/*批量秒杀库存*/
				all_seckill_stock: null,
				/*批量限购*/
				all_limit_num: null,
				/*验证商品*/
				rulesProduct: [{
					validator: validatorProduct,
					required: true,
					trigger: 'blur'
				}]
			};
		},
		props: {
			form: Object,
			type: String
		},
		created() {},
		methods: {
			/*打开添加*/
			changeProduct(list) {
				let arr = [];
				list.forEach(item => {
					arr.push(item.product_id);
				});
				this.excludeIds = arr;
				this.isproduct = true;
			},

			/*关闭弹窗*/
			closeDialogFunc(e) {
				let self = this;
				self.isproduct = false;
				if (e && e.type == 'success') {
					let arr = [];
					for (let i = 0; i < e.params.length; i++) {
						let item = e.params[i];
						let obj = {
							product_name: item.product_name,
							product_id: item.product_id,
							product_price: item.product_price,
							stock_num: item.product_stock,
							product_sku_id: item.product_sku.product_sku_id,
							sales_num: 0,
							spec_type: item.spec_type,
							seckill_price: null,
							seckill_stock: 0,
							limit_num: 1,
							sort: 100,
							tempProduct: true,
							is_delete: 0,
							seckill_product_id: 0,
							seckill_product_sku_id: 0,
							sales_initial: 0
						};
						arr.push(obj);
					}
					self.form.tableData = self.form.tableData.concat(arr);
					self.form.tableData = mergeTable(this.form.tableData);
				}
			},

			/*打开规格*/
			specsFunc(i, e, type) {
				this.curProduct = e;
				this.curIndex = i;
				let arr = [];

				if (type) {
					this.specType = true;
					this.form.tableData.forEach(item => {
						if (this.curProduct.product_id == item.product_id) {
							arr.push(item.product_sku_id);
						}
					});
				} else {
					this.specType = false;
				}
				this.SpecExcludeIds = arr;
				this.isspecs = true;
			},

			/*关闭规格*/
			closeSpecs(e) {
				this.isspecs = false;
				if (e && e.type == 'success') {
					let arr = [];
					for (let i = 0; i < e.params.length; i++) {
						let item = e.params[i];
						let obj = {
							product_name: this.curProduct.product_name,
							product_id: this.curProduct.product_id,
							spec_type: this.curProduct.spec_type,
							product_sku_id: this.curProduct.product_sku_id,
							seckill_product_id: this.curProduct.seckill_product_id,
							product_price: item.spec_form.product_price,
							stock_num: item.product_stock,
							sales_num: 0,
							spec_name: item.spec_name,
							product_sku_id: item.product_sku_id,
							seckill_price: null,
							seckill_stock: 0,
							limit_num: 1,
							sort: 100,
							tempProduct: true,
							is_delete: 0,
							seckill_product_sku_id: 0,
							sales_initial: 0
						};
						arr.push(obj);
					}

					let params = [this.curIndex, this.specType ? 0 : 1].concat(arr);

					this.form.tableData.splice(...params);
					this.form.tableData = mergeTable(this.form.tableData);
				}
			},

			/*删除*/
			deleteClick(e, isType) {
				let curItem = this.form.tableData[e];
				if (curItem.join_num > 0) {
					ElMessage({
						message: '此商品有人参加活动，无法删除！',
						type: 'warning'
					});
					return;
				}
				if (isType) {
					/*判断能删除产品*/
					for (let i = 0; i < this.form.tableData.length; i++) {
						let item = this.form.tableData[i];
						if (curItem.product_id == item.product_id) {
							if (item.sales_num > 0) {
								ElMessage({
									message: '已售商品不能删除！',
									type: 'warning'
								});
								return;
							}
						}
					}
					for (let i = 0; i < this.form.tableData.length; i++) {
						let item = this.form.tableData[i];
						if (curItem.product_id == item.product_id) {
							if (this.type == 'edit' && e.tempProduct != true) {
								let seckill_product_id = item.seckill_product_id;
								this.form.product_del_ids.push(seckill_product_id);
							}
							this.form.tableData.splice(i, 1);
							i--;
						}
					}
				} else {
					/*删除规格*/
					if (parseInt(e.sales_num) > 0) {
						ElMessage({
							message: '已售商品规格不能删除！',
							type: 'warning'
						});
						return;
					}
					let count = 0;
					for (let i = 0; i < this.form.tableData.length; i++) {
						let item = this.form.tableData[i];
						if (curItem.product_id == item.product_id) {
							count++;
							if (count > 1) {
								break;
							}
						}
					}
					if (count > 1) {
						this.form.tableData.splice(e, 1);
					} else {
						curItem.spec_name = null;
					}
				}
				this.form.tableData = mergeTable(this.form.tableData);
			},

			/*表格合并行*/
			objectSpanMethod({
				row,
				column,
				rowIndex,
				columnIndex
			}) {
				if (columnIndex === 0 || columnIndex === 6 || columnIndex === 7 || columnIndex === 8 || columnIndex ===
					9) {
					if (row.rowSpan != null) {
						return {
							rowspan: row.rowSpan,
							colspan: 1
						};
					} else {
						return {
							rowspan: 0,
							colspan: 0
						};
					}
				}
			},

			/*全部应用*/
			allApply() {
				this.form.tableData.forEach((v) => {
					if (parseInt(this.all_seckill_price) && parseInt(this.all_seckill_price) > 0) {
						v.seckill_price = this.all_seckill_price;
					}
					if (parseInt(this.all_seckill_stock) && parseInt(this.all_seckill_stock) > 0) {
						v.seckill_stock = this.all_seckill_stock;
					}
					if (parseInt(this.all_limit_num) && parseInt(this.all_limit_num) > 0) {
						v.limit_num = this.all_limit_num;
					}
				})
				/* for (let i = 0; i < this.form.tableData.length; i++) {
				  let item = this.form.tableData[i];
				  if (item.seckill_price == null) {
				    if (parseInt(this.all_seckill_price) > parseInt(item.product_price)) {
				      item.seckill_price = item.product_price;
				    } else {
				      item.seckill_price = this.all_seckill_price;
				    }
				  }
				  item.seckill_stock = item.seckill_stock == 0 ? this.all_seckill_stock : item.seckill_stock;
				  item.limit_num = item.limit_num == 0 ? this.all_limit_num : item.limit_num;
				} */
			},

			/*显示隐藏*/
			showFunc(i, e) {
				if (e.is_delete === 0) {
					e.is_delete = 1;
				} else {
					e.is_delete = 0;
				}
			}
		}
	};
</script>

<style lang="scss" scoped>
	.spec-min-box {
		position: relative;
	}

	.spec-min-box>i {
		position: absolute;
		right: 0;
		font-size: 16px;
		cursor: pointer;
	}

	.spec-min-box .el-icon-close {
		top: 0;
		color: red;
		visibility: hidden;
	}

	td:hover .spec-min-box .el-icon-close {
		visibility: visible;
	}

	.spec-min-box .el-icon-caret-right {
		bottom: 0;
		transform: rotate(45deg);
		color: #3a8ee6;
	}

	.el-form-item.is-error .active-all-set .el-input__inner,
	.el-form-item.is-error .isvalid .el-input__inner {
		border-color: #C0C4CC;
	}
</style>