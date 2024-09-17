<template>
	<div class="product-add">
		<!--form表单-->
		<el-form size="small" ref="form" :model="form" label-width="200px">
			<!--添加门店-->
			<div class="common-form">修改电子面单模板</div>
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
				/*切换菜单*/
				// activeIndex: '1',
				/*form表单数据*/
				form: {
					template_id: '',
					template_name: '',
					template_num: '',
					wx_code: '',
					sort: ''
				},
				loading: false
			};
		},
		created() {
			this.getData();
		},

		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				// 取到路由带过来的参数
				const params = this.$route.query.template_id;
				SurfaceApi.templateDetail({
							template_id: params
						},
						true
					)
					.then(res => {
						let detail = res.data.model;
						// 将数据放在当前组件的数据内
						self.form.template_id = detail.template_id;
						self.form.template_name = detail.template_name;
						self.form.template_num = detail.template_num;
						self.form.sort = detail.sort;
						self.form.wx_code = detail.wx_code;
					})
					.catch(error => {});
			},

			/*提交表单*/
			onSubmit() {
				let self = this;
				self.loading = true;
				let params = this.form;
				SurfaceApi.edittemplate(params, true)
					.then(data => {
						self.loading = false;
						ElMessage({
							message: '恭喜你，修改成功',
							type: 'success'
						});
						self.$router.push('/plus/surface/index');
					})
					.catch(error => {
						self.loading = false;
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