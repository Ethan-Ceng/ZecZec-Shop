<template>

	<div class="product-add">
		<!--form表单-->
		<el-form size="small" ref="form" :model="form" label-width="200px">
			<!--添加门店-->
			<div class="common-form">新增电子面单</div>
			<el-form-item label="电子面单模板名称 " prop="template_name" :rules="[{required: true,message: ' '}]">
				<el-input v-model="form.template_name" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="电子面单模板编码 " prop="template_num" :rules="[{required: true,message: ' '}]">
				<el-input v-model="form.template_num" class="max-w460"></el-input>
				<div class="tips">用于快递100电子面单打印，务必填写正确</div>
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
	import SurfaceApi from '@/api/surface.js';
	export default {
		data() {
			return {
				loading: false,
				/*form表单数据*/
				form: {
					template_name: '',
					template_num: '',
					sort: 1,
				},


			};
		},
		created() {},

		methods: {
			//提交表单
			onSubmit() {
				let self = this;
				let form = this.form;
				self.$refs.form.validate((valid) => {
					if (valid) {
						self.loading = true;
						SurfaceApi.addtemplate(form, true)
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
		color: #ccc;
	}
</style>