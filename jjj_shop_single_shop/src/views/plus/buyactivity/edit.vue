<template>
	<div class="user">
		<div class="common-form">买送设置</div>
		<div class="product-content">
			<el-form ref="form" :model="form" :rules="formRules" label-width="150px">
				<el-form-item label="名称" prop="name" :rules="[{required: true,message: ' '}]">
					<el-input type="text" v-model="form.name" :placeholder="请输入活动名称" class="max-w460"></el-input>
				</el-form-item>
				<el-form-item label="活动状态">
					<el-radio-group v-model="form.status">
						<el-radio :label="1">开启</el-radio>
						<el-radio :label="0">关闭</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item label="活动时间" :rules="[{ required: true, message: ' ' }]" prop="start_time">
					<el-date-picker v-model="form.start_time" type="datetime" value-format="YYYY-MM-DD HH:mm:ss"
						placeholder="选择开始日期">
					</el-date-picker>
					<span>-</span>
					<el-date-picker v-model="form.end_time" type="datetime" value-format="YYYY-MM-DD HH:mm:ss"
						placeholder="选择结束日期">
					</el-date-picker>
				</el-form-item>
				<el-form-item label="赠送类型">
					<el-radio-group v-model="form.send_type">
						<el-radio :label="10">单次</el-radio>
						<el-radio :label="20">倍数赠送</el-radio>
					</el-radio-group>
					<div class="gray9 f12">单次为达到赠送条件只赠送一次；倍数赠送为买2件A商品送1件B商品，买4件A商品送2件B商品</div>
				</el-form-item>
				<el-form-item v-if="form.send_type==20" label="最大赠送倍数" prop="max_times"
					:rules="[{required: true,message: ' '}]">
					<el-input type="number" min="1" v-model="form.max_times" :placeholder="请输入最大赠送倍数" class="max-w460"
						@input="checkTimes()"></el-input>
					<div class="gray9 f12">赠送类型为倍数赠送时的最大赠送倍数，最小数值为1</div>
				</el-form-item>
				<el-form-item label="排序" prop="sort" :rules="[{required: true,message: ' '}]">
					<el-input type="text" v-model="form.sort" placeholder="请输入排序" class="max-w460"></el-input>
					<div class="tips">值越小越靠前</div>
				</el-form-item>
				<div class="common-form">购买商品</div>
				<el-form-item label="">
					<el-button style="margin-bottom: 10px;" type="primary" @click="addLimitProduct()">添加购买商品</el-button>
					<div class="gray9 f12">满足任一条件赠送</div>
					<el-table :data="limitProdcutData" style="width: 60%">
						<el-table-column prop="product_id" label="商品id">
						</el-table-column>
						<el-table-column prop="product_name" label="商品名称">
						</el-table-column>
						<el-table-column prop="product_num" label="数量" :rules="[{required: true,message: ' '}]">
							<template #default="scope">
								<el-input type="number" v-model="scope.row.product_num" placeholder=""
									min="1"></el-input>
							</template>
						</el-table-column>
						<el-table-column label="操作">
							<template #default="scope">
								<el-button type="text" size="small" @click='delLimitProduct(scope.row)'>删除 </el-button>
							</template>
						</el-table-column>
					</el-table>
				</el-form-item>
				<div class="common-form">赠送商品</div>
				<el-form-item label="">
					<el-button type="primary" @click="addProduct()">添加赠送商品</el-button>
					<div class="gray9 f12">活动商品（拼团秒杀砍价等...）不参与赠送</div>
					<el-table :data="prodcutData" style="width: 60%">
						<el-table-column prop="product_id" label="商品id">
						</el-table-column>
						<el-table-column prop="product_name" label="商品名称">
						</el-table-column>
						<el-table-column prop="product_num" label="规格">
							<template #default="scope">
								<span v-if="scope.row.spec_type==20&&scope.row.spec_sku_id">
									{{scope.row.product_attr}}
									<el-button icon="CloseBold" text @click="deleteClick(scope.$index)"></el-button>
								</span>
								<span :rules="[{ required: true, message: ' ' }]"
									v-if="scope.row.spec_type==20&&!scope.row.spec_sku_id">
									<el-button size="small" icon="Plus" @click="specsFunc(scope.$index, scope.row)">选择规格
									</el-button>
								</span>
								<span v-if="scope.row.spec_type==10">单规格</span>
							</template>
						</el-table-column>
						<el-table-column prop="product_num" label="数量" :rules="[{required: true,message: ' '}]">
							<template #default="scope">
								<el-input type="number" v-model="scope.row.product_num" placeholder=""
									min="1"></el-input>
							</template>
						</el-table-column>
						<el-table-column prop="product_" label="操作">
							<template #default="scope">
								<el-button type="text" size="small" @click='delProduct(scope.row)'> 删除 </el-button>
							</template>
						</el-table-column>
					</el-table>
				</el-form-item>
			</el-form>
			<!--提交-->
			<div class="common-button-wrapper">
				<el-button type="info" @click="gotoBack">返回</el-button>
				<el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
			</div>
		</div>
		<!--选择赠送商品-->
		<Product :isproduct="isproduct" :excludeIds="exclude_ids" :islist="false" @closeDialog="closeProductFunc">
		</Product>
		<!--选择购买商品-->
		<Product :isproduct="islimitproduct" :islist="false" :excludeIds="limit_exclude_ids"
			@closeDialog="closeLimitProductFunc"></Product>
		<!--规格选择-->
		<Specs :isspecs="isspecs" :productId="curProduct.product_id" :excludeIds="SpecExcludeIds" :islist="false"
			@close="closeSpecs"></Specs>
	</div>
</template>
<script>
	import BuyApi from '@/api/buy.js';
	import Product from '@/components/product/Product.vue';
	import Specs from '@/components/product/Specs.vue';
	import {
		formatModel
	} from '@/utils/base.js';
	export default {
		components: {
			Product,
			/*选择规格*/
			Specs
		},
		data() {
			return {
				form: {
					buy_id: 0,
					name: '',
					status: 1,
					start_time: '',
					end_time: '',
					sort: '',
					limit_product: [],
					product_ids: [],
					send_type: '',
					max_times: ''
				},
				tableData: [],
				prodcutData: [],
				limitProdcutData: [],
				loading: false,
				/*是否打开选择商品*/
				isproduct: false,
				exclude_ids: [],
				/*是否打开选择商品*/
				islimitproduct: false,
				/*左边长度*/
				formLabelWidth: '120px',
				curProduct: {
					product_id: 0
				},
				SpecExcludeIds: [],
				isspecs: false,
				limit_exclude_ids: []
			};
		},
		created() {
			/*获取数据*/
			this.getData();
		},
		methods: {
			checkTimes() {
				this.form.max_times = Math.max(1, parseInt(this.form.max_times));
			},
			/*删除*/
			deleteClick(e, isType) {
				let curItem = this.prodcutData[e];
				curItem.spec_sku_id = 0;
				curItem.product_attr = '';

			},
			/*添加商品*/
			addProduct() {
				this.exclude_ids = []
				if (this.prodcutData.length > 0) {
					this.prodcutData.forEach((item, index) => {
						this.exclude_ids = this.exclude_ids.concat(item.product_id);
						if (index == this.prodcutData.length - 1) {
							this.isproduct = true;
						}
					});
				} else {
					this.isproduct = true;
				}
			},
			/*关闭商品*/
			closeProductFunc(e) {
				let self = this;
				self.isproduct = e.openDialog;
				if (e.type == 'success') {
					self.prodcutData.push({
						product_id: e.params.product_id,
						product_name: e.params.product_name,
						product_num: 1,
						product_attr: '',
						spec_type: e.params.spec_type,
						spec_sku_id: 0
					});
				}
			},
			/*获取数据*/
			getData() {
				let self = this;
				let id = self.$route.query.buy_id;
				BuyApi.detailBuy({
					buy_id: id
				}, true).then(data => {
					self.form = formatModel(self.form, data.data.model);
					self.form.product_ids = [];
					self.prodcutData = data.data.model.product_ids;
					self.limitProdcutData = data.data.model.limit_product;
					self.form.start_time = data.data.model.start_time_text;
					self.form.end_time = data.data.model.end_time_text;
				}).catch(error => {});
			},
			delProduct(item) {
				let self = this;
				let n = self.prodcutData.indexOf(item);
				self.prodcutData.splice(n, 1);
			},
			/*提交表单*/
			onSubmit() {
				let self = this;
				if (!self.checkGroup()) {
					ElMessage.error('未选择规格');
					return;
				}
				let form = self.form;
				form.product_ids = self.prodcutData;
				form.product_ids = JSON.stringify(form.product_ids);
				form.limit_product = self.limitProdcutData;
				self.$refs.form.validate((valid) => {
					if (valid) {
						self.loading = true;
						BuyApi.editBuy(form, true).then(data => {
								self.loading = false;
								if (data.code == 1) {
									ElMessage({
										message: data.msg,
										type: 'success'
									});
									self.$router.push('/plus/buyactivity/index');
								} else {
									self.loading = false;
								}
							})
							.catch(error => {
								self.loading = false;
							});
					}
				});
			},
			checkGroup() {
				let self = this;
				if (self.form.is_product == false) {
					return true;
				}
				let isCheck = true;
				console.log(self.prodcutData)
				self.prodcutData.forEach((item, index) => {
					if (item.spec_type == 20 && !item.spec_sku_id) {
						isCheck = false;
					}
				});
				return isCheck;
			},
			/*返回上一页面*/
			gotoBack() {
				this.$router.back(-1);
			},
			/*添加商品*/
			addLimitProduct() {
				this.limit_exclude_ids = []
				if (this.limitProdcutData.length > 0) {
					this.limitProdcutData.forEach((item, index) => {
						this.limit_exclude_ids = this.limit_exclude_ids.concat(item.product_id * 1);
						if (index == this.limitProdcutData.length - 1) {
							this.islimitproduct = true;
						}
					});
				} else {
					this.islimitproduct = true;
				}
			},
			/*关闭商品*/
			closeLimitProductFunc(e) {
				let self = this;
				self.islimitproduct = e.openDialog;
				if (e.type == 'success') {
					self.limitProdcutData.push({
						product_id: e.params.product_id,
						product_name: e.params.product_name,
						product_num: 1
					});
				}
			},
			delLimitProduct(item) {
				let self = this;
				let n = self.limitProdcutData.indexOf(item);
				self.limitProdcutData.splice(n, 1);
			},
			/*打开规格*/
			specsFunc(i, e, type) {
				console.log(i)
				this.curProduct = e;
				this.curIndex = i;
				if (type) {
					this.specType = true;
				} else {
					this.specType = false;
				}
				this.isspecs = true;
			},
			/*关闭规格*/
			closeSpecs(e) {
				this.isspecs = false;
				if (e && e.type == 'success') {
					this.prodcutData[this.curIndex].spec_sku_id = e.params.spec_sku_id;
					this.prodcutData[this.curIndex].product_sku_id = e.params.product_sku_id;
					this.prodcutData[this.curIndex].product_attr = e.params.spec_name;
				}
			},
		}
	};
</script>