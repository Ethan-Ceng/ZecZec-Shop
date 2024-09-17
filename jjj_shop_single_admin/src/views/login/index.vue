<template>
	<div class="login-bg" :style="'background-image:url(' + bgimg_url + ');'">
		<el-form :model="ruleForm" :rules="rules2" ref="ruleForm" label-position="left" label-width="0px"
			class="demo-ruleForm login-container">
			<h3 class="title">{{admin_name}}</h3>
			<el-form-item prop="account">
				<el-input type="text" v-model="ruleForm.account" auto-complete="off" placeholder="账号"></el-input>
			</el-form-item>
			<el-form-item prop="checkPass">
				<el-input type="password" v-model="ruleForm.checkPass" auto-complete="off" placeholder="密码"></el-input>
			</el-form-item>
			<el-form-item>
				<el-button type="primary" style="width:100%;" @click="handleSubmit" :loading="logining">登录</el-button>
			</el-form-item>
		</el-form>
	</div>
</template>

<script>
	import {
		useUserStore
	} from '@/store';
	import UserApi from '@/api/user.js'
	// import bgimg from '@/assets/img/login_bg.png';
	const {
		afterLogin
	} = useUserStore();
	export default {
		data() {
			return {
				logining: false,
				bgimg_url: '',
				admin_name: '',
				ruleForm: {
					account: '',
					checkPass: ''
				},
				rules2: {
					account: [{
						required: true,
						message: '请输入账号',
						trigger: 'blur'
					}],
					checkPass: [{
						required: true,
						message: '请输入密码',
						trigger: 'blur'
					}]
				},
				checked: true
			};
		},
		created() {
			this.getData();
		},
		methods: {
			/*获取基础配置*/
			getData() {
				let self = this;
				UserApi.base(true)
					.then(res => {
						self.loading = false;
						const data = res.data.settings;
						self.admin_name = data.admin_name;
						if (data.admin_bg_img) {
							self.bgimg_url = data.admin_bg_img;
						} else {
							self.bgimg_url = '';
						}
					})
					.catch(error => {
						self.loading = false;
					});
			},
			handleSubmit(ev) {
				var _this = this;
				this.$refs.ruleForm.validate((valid) => {
					if (valid) {
						_this.logining = true;
						var loginParams = {
							username: _this.ruleForm.account,
							password: _this.ruleForm.checkPass
						};
						UserApi.login(loginParams, true)
							.then(async (data) => {
								await afterLogin(data);
								_this.logining = false;
								_this.$router.push({
									path: '/Home'
								})
							})
							.catch(error => {
								_this.logining = false;
							});
					}
				});
			}
		}
	};
</script>

<style lang="scss" scoped>
	.login-bg {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background-repeat: no-repeat;
		background-position: center;
		background-size: 100%;
	}

	.login-container {
		box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);
		-webkit-border-radius: 5px;
		border-radius: 5px;
		-moz-border-radius: 5px;
		background-clip: padding-box;
		margin: 180px auto;
		width: 350px;
		padding: 35px 35px 15px 35px;
		background: #323a4a;
		border: 1px solid #384050;
		box-shadow: 0 0 12px 0 rgba(0, 0, 0, .4);

		.title {
			margin: 0px auto 30px auto;
			text-align: center;
			color: #FFFFFF;
			font-size: 16px;
		}

		.remember {
			margin: 0px 0px 35px 0px;
		}
	}
</style>