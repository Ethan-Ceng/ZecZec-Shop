<template>

	<div class="product-add">
		<!--form表单-->
		<el-form size="small" ref="form" :model="form" label-width="260px">
			<!--添加门店-->
			<div class="common-form">新增电子面单配置</div>
			<el-form-item label="电子面单配置名称 " prop="setting_name" :rules="[{required: true,message: ' '}]">
				<el-input v-model="form.setting_name" class="max-w460"></el-input>
			</el-form-item>

			<el-form-item label="快递公司">
				<el-select size="small" v-model="form.express_id" placeholder="请选择" style="width: 460px;">
					<el-option v-for="(item, index) in expressList" :key="index" :label="item.express_name"
						:value="item.express_id">
					</el-option>
				</el-select>
			</el-form-item>
			<el-form-item label="电子面单月结账号(partner_id)" prop="partner_id" :rules="[{required: true,message: ' '}]">
				<el-input v-model="form.partner_id" class="max-w460"></el-input>
				<div class="tips max-w460">电子面单客户编码或月结账号，使用电子面单下单时必填，需贵司向当地快递公司网点申请，其余字段请对照快递100参数字典根据快递公司类型填写
					<el-link :underline="false" href="https://api.kuaidi100.com/document/5f0ff6e82977d50a94e10237.html"
						target="_blank" type="primary">快递100参数字典
					</el-link>
				</div>
				<div class="tips"></div>
			</el-form-item>
			<el-form-item label="电子面单密码(partner_key)">
				<el-input v-model="form.partner_key" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="电子面单密钥(partner_secret)">
				<el-input v-model="form.partner_secret" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="电子面单客户账户名称(partner_name)">
				<el-input v-model="form.partner_name" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="收件网点名称(net)">
				<el-input v-model="form.net" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="电子面单承载编号(code)">
				<el-input v-model="form.code" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="电子面单承载快递员名(check_man)">
				<el-input v-model="form.check_man" class="max-w460"></el-input>
			</el-form-item>

			<el-form-item label="排序">
				<el-input v-model="form.sort" type="number" class="max-w460"></el-input>
				<div class="tips">数字越小越靠前</div>
			</el-form-item>

			<!--提交-->
			<div class="common-button-wrapper">
				<el-button size="small" type="info" @click="cancelFunc">取消</el-button>
				<el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
			</div>


		</el-form>


	</div>

</template>

<script>
	import SettingApi from '@/api/surface.js';
	export default {
		data() {
			return {
				loading: false,
				/*form表单数据*/
				form: {
					sort: 1,
				},
				/*快递公司列表*/
				expressList: [],

			};
		},
		created() {
			/*获取列表*/
			this.getParams();
		},

		methods: {
			getParams() {
				let self = this;
				SettingApi.toAddlabel({},
						true
					)
					.then((res) => {
						self.loading = false;
						self.expressList = res.data.expressList;
					})
					.catch((error) => {
						self.loading = false;
					});
			},

			//提交表单
			onSubmit() {
				let self = this;
				let form = this.form;
				self.$refs.form.validate((valid) => {
					if (valid) {
						self.loading = true;
						SettingApi.addlabel(form, true)
							.then(data => {
								self.loading = false;
								ElMessage({
									message: '恭喜你，添加成功',
									type: 'success'
								});
								self.$router.push('/plus/surface/index');
							})
							.catch(error => {
								self.loading = false;
							});
					}
				});

			},
			/*取消*/
			cancelFunc() {
				this.$router.push({
					path: '/plus/surface/index'
				});
			}

		}

	};
</script>

<style>
	.tips {
		margin-top: 5px;
		color: #ccc;
		line-height: 18px;
		
	}
</style>