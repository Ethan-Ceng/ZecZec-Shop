<template>

	<div class="product-add">
		<el-tabs v-model="activeName" type="card">
			<el-tab-pane label="平台设置" name="plate"></el-tab-pane>
			<el-tab-pane label="会员设置" name="user"></el-tab-pane>
		</el-tabs>
		<!--form表单-->
		<el-form size="small" ref="form" :model="form" label-width="150px">
			<!--平台设置-->
			<Plate v-show="activeName == 'plate'" :allType="all_type"></Plate>
			<!--会员设置-->
			<User v-show="activeName == 'user'"></User>
			<!--提交-->
			<div class="common-button-wrapper">
				<el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
			</div>
		</el-form>
	</div>

</template>

<script>
	import SettingApi from '@/api/setting.js';
	import {
		useUserStore
	} from '@/store';
	const {
		changShop
	} = useUserStore();
	import {
		formatModel
	} from '@/utils/base.js';
	import Plate from './part/Plate.vue';
	import User from './part/User.vue';
	export default {
		components: {
			Plate,
			User
		},
		data() {
			return {
				activeName: 'plate',
				/*是否正在加载*/
				loading: false,
				/*form表单数据*/
				form: {
					name: '',
					customer: '',
					key: '',
					checkedCities: [],
					is_get_log: 0,
					is_send_wx: 0,
					logoUrl: '',
					sms_open: '',
					wx_open: '',
					wx_phone: '',
					mp_open: '',
					avatarUrl: '',
					tx_key: '',
					user_name: '',
					login_logo: '',
					login_desc: '',
					mp_phone: ''
				},
				all_type: [],
				delivery_type: [],
				/*是否打开图片选择*/
				// isupload: false
			};
		},
		provide: function() {
			return {
				form: this.form
			}
		},
		created() {
			this.getParams();
		},

		methods: {

			/*获取配置数据*/
			getParams() {
				let self = this;
				SettingApi.storeDetail({}, true).then(res => {
						let vars = res.data.vars.values;
						self.form = formatModel(self.form, vars);
						self.all_type = res.data.all_type;
						self.form.customer = vars.kuaidi100.customer;
						self.form.secret = vars.kuaidi100.secret;
						self.form.key = vars.kuaidi100.key;
						self.form.checkedCities = vars.delivery_type;
						// 转成整数，兼容组件
						for (let i = 0; i < self.form.checkedCities.length; i++) {
							self.form.checkedCities[i] = parseInt(self.form.checkedCities[i]);
						}
						self.loading = false;
					})
					.catch(error => {

					});

			},
			/*提交*/
			onSubmit() {
				let self = this;
				let params = this.form;
				if (params.checkedCities.length < 1) {
					ElMessage({
						message: '配送方式至少选择一种！',
						type: 'warning'
					});
					return;
				}

				self.$refs.form.validate((valid) => {
					if (valid) {
						self.loading = true;
						SettingApi.editStore(params, true)
							.then(data => {
								self.loading = false;
								ElMessage({
									message: '恭喜你，商城设置成功',
									type: 'success'
								});
								changShop(self.form.name);
								self.$router.push('/setting/store/index');
							})
							.catch(error => {
								self.loading = false;
							});
					}
				});
			},
			// /*选择图片*/
			// chooseImg(e) {
			// 	this.type = e;
			// 	this.isupload = true;
			// },
			// /*关闭选择图片*/
			// returnImgsFunc(e) {
			// 	this.isupload = false;
			// 	if (e != null && e.length > 0) {
			// 		if (this.type == 'logoUrl') {
			// 			this.form.logoUrl = e[0].file_path;
			// 		} else if (this.type == 'avatarUrl') {
			// 			this.form.avatarUrl = e[0].file_path;
			// 		}
			// 	}
			// }
		}

	};
</script>
<style></style>