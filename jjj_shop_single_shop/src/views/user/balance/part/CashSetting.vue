<template>

	<div class="pb50">
		<el-form ref="form" size="small" :model="form" label-width="200px">
			<div class="common-form">提现设置</div>
			<el-form-item label="是否开启提现">
				<el-radio-group v-model="form.is_open">
					<el-radio label="1">开启</el-radio>
					<el-radio label="0">关闭</el-radio>
				</el-radio-group>
				<div class="lh18 mt10 gray9">
					<p>注：如开启则移动端显示余额提现</p>
				</div>
			</el-form-item>
			<el-form-item label="提现方式">
				<el-checkbox-group v-model="form.pay_type">
					<el-checkbox v-for="(item, index) in list" :label="item.id"
						:key="index">{{ item.name }}</el-checkbox>
				</el-checkbox-group>
				<div class="tips">
					注：如使用微信支付，则需申请微信支付企业付款到零钱功能
				</div>
			</el-form-item>
			<el-form-item label="提现比例 " prop="cash_ratio" :rules="[{required: true,message: ' '}]">
				<el-input placeholder="请输入内容" v-model="form.cash_ratio" class="max-w460">
					<template #append>%</template>
				</el-input>
				<div class="lh18 mt10 gray9">
					<p> 注：提现比例请填写数字0~100</p>
					<p> 例：提现金额(100.00元) * 提现比例(100%) = 实际到账(100元)</p>
				</div>
			</el-form-item>
			<el-form-item label="最低提现金额" prop="min_money" :rules="[{required: true,message: ' '}]">
				<el-input placeholder="请输入内容" v-model="form.min_money" class="max-w460">
					<template #append>元</template>
				</el-input>
				<div class="lh18 mt10 gray9">
					<p> 注：最低提现金额多少元</p>
				</div>
			</el-form-item>
			<!--提交-->
			<div class="common-button-wrapper">
				<el-button type="primary" size="small" @click="onSubmit" :loading="loading">提交</el-button>
			</div>
		</el-form>
	</div>
</template>
<script>
	import BalanceApi from '@/api/balance.js';
	export default {
		data() {
			return {
				form: {
					pay_type: []
				},
				loading: false,
			};
		},
		created() {
			/*获取数据*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				let Params = {};
				BalanceApi.getCashSetting(Params, true).then(data => {
					self.form = data.data.values;
					self.list = data.data.pay_type;
				}).catch(error => {

				});
			},

			/*保存*/
			onSubmit() {
				let self = this;
				let form = self.form;
				if (!form.pay_type || form.pay_type.length < 1) {
					ElMessage({
						message: "提现方式至少选择一种！",
						type: "warning",
					});
					return;
				}
				self.$refs.form.validate((valid) => {
					if (valid) {
						self.loading = true;
						BalanceApi.cashSetting(form, true)
							.then(data => {
								self.loading = false;
								if (data.code == 1) {
									ElMessage({
										message: '恭喜你，保存成功',
										type: 'success'
									});
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
		}
	};
</script>

<style>
</style>