<template>
	<div class="product-add">
		<!--form表单-->
		<el-form size="small" ref="form" :model="form" label-width="200px">
			<!--店员修改-->
			<div class="common-form">添加会员</div>
			<el-form-item label="昵称" prop="nickName" :rules="[{ required: true, message: '请填写昵称' }]">
				<el-input class="max-w460" v-model="form.nickName" placeholder="请输入昵称"></el-input>
			</el-form-item>
			<el-form-item label="头像">
				<el-row>
					<el-button icon="Upload" @click="openUpload('avatarUrl')">选择图片</el-button>
					<div v-if="form.avatarUrl!=''" class="img">
						<img :src="form.avatarUrl" width="100" height="100" />
					</div>
					<div class="gray">建议上传图片尺寸为100px*100px</div>
				</el-row>
			</el-form-item>
			<el-form-item label="性别">
				<el-radio-group v-model="form.gender">
					<el-radio :label="1">男</el-radio>
					<el-radio :label="0">女</el-radio>
					<el-radio :label="2">保密</el-radio>
				</el-radio-group>
			</el-form-item>
			<el-form-item label="手机号" prop="mobile" :rules="[{ required: true, message: '请填写手机号' }]">
				<el-input class="max-w460" v-model="form.mobile" placeholder="请输入手机号"></el-input>
			</el-form-item>
			<el-form-item label="密码" prop="password" :rules="[{ required: true, message: '请填写密码' }]">
				<el-input class="max-w460" type="password" v-model="form.password" placeholder="请输入密码"></el-input>
			</el-form-item>
			<div class="common-button-wrapper">
				<el-button size="small" type="info" @click="cancelFunc">取消</el-button>
				<el-button size="small" type="primary" @click="onSubmit" :loading="loading">提交</el-button>
			</div>
		</el-form>
		<!--上传图片组件-->
		<Upload v-if="isupload" :isupload="isupload" :config="{ total: 1}" :type="type" @returnImgs="returnImgsFunc">
			上传图片</Upload>
	</div>
</template>

<script>
	import UserApi from '@/api/user.js';
	import Upload from '@/components/file/Upload.vue';
	import {
		formatModel
	} from '@/utils/base.js'
	export default {
		components: {
			/*上传组件*/
			Upload,
		},
		data() {
			return {
				form: {
					nickName: '',
					avatarUrl: '',
					gender: 2,
					mobile: '',
					password: '',
				},
				loading: false,
				/*是否上传图片*/
				isupload: false,
			};
		},
		created() {},

		methods: {
			onSubmit() {
				let self = this;
				let form = self.form;
				self.$refs.form.validate((valid) => {
					if (valid) {
						if (!/^1(3|4|5|6|7|8|9)\d{9}$/.test(form.mobile)) {
							ElMessage({
								message: '手机号格式错误',
								type: 'error'
							});
							return;
						}
						self.loading = true;
						UserApi.adduser(form, true)
							.then(data => {
								ElMessage({
									message: '恭喜你，添加成功',
									type: 'success'
								});
								self.$router.push('/user/user/index');
							})
							.catch(error => {
								self.loading = false;
							});
					}
				});
			},
			/*上传*/
			openUpload(e) {
				this.type = e;
				this.isupload = true;
			},
			/*获取图片*/
			returnImgsFunc(e) {
				if (e != null && e.length > 0) {
					if (this.type == 'avatarUrl') {
						this.form.avatarUrl = e[0].file_path;
					}
				}
				this.isupload = false;
			},
			/*取消*/
			cancelFunc() {
				this.$router.back(-1);
			},

		}

	};
</script>

<style lang="scss" scoped>
	.basic-setting-content {}

	.product-add {
		padding-bottom: 50px;
	}
</style>