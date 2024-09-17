<template>

	<div class="product-add">
		<!--form表单-->
		<el-form ref="form" :model="form" label-width="150px">
			<div class="common-form">运营后台设置</div>
			<el-form-item label="商城运营名称" :rules="[{required: true,message: ' '}]" prop="shop_name">
				<el-input v-model="form.admin_name" placeholder="商城运营名称" class="max-w460"></el-input>
				<div class="tips">
					saas端商城运营名称，显示在登录页
				</div>
			</el-form-item>
			<el-form-item label="商城运营登录背景" prop="shop_bg_img">
				<el-input v-model="form.admin_bg_img" placeholder="商城运营登录背景" class="max-w460"></el-input>
				<div class="tips">
					saas端商城运营登录背景，不填则为系统默认登录背景，填写网络地址
				</div>
			</el-form-item>
			<div class="common-form">商城后台设置</div>
			<el-form-item label="商城系统名称" :rules="[{required: true,message: ' '}]" prop="shop_name">
				<el-input v-model="form.shop_name" placeholder="商城名称" class="max-w460"></el-input>
				<div class="tips">
					shop端商城名称，显示在登录页
				</div>
			</el-form-item>
			<el-form-item label="商城登录背景" prop="shop_bg_img">
				<el-input v-model="form.shop_bg_img" placeholder="商城登录背景" class="max-w460"></el-input>
				<div class="tips">
					shop端商城登录背景，不填则为系统默认登录背景，填写网络地址
				</div>
			</el-form-item>
			<div class="common-form">微信服务商支付设置</div>
			<el-form-item label="是否开启服务商支付">
				<div>
					<el-radio v-model="form.weixin_service.is_open" :label="1">开启</el-radio>
					<el-radio v-model="form.weixin_service.is_open" :label="0">关闭</el-radio>
				</div>
			</el-form-item>
			<template v-if="form.weixin_service.is_open == 1">
				<el-form-item label="服务商户号">
					<el-input v-model="form.weixin_service.mch_id" placeholder="服务商户号" class="max-w460"></el-input>
					<div class="tips">
						填写服务商户号、10位数字
					</div>
				</el-form-item>
				<el-form-item label="服务商支付秘钥apikey">
					<el-input v-model="form.weixin_service.apikey" placeholder="服务商支付秘钥apikey"
						class="max-w460"></el-input>
					<div class="tips">
						填写服务商户支付秘钥apikey
					</div>
				</el-form-item>
				<el-form-item label="服务商appid">
					<el-input v-model="form.weixin_service.app_id" placeholder="服务商appid" class="max-w460"></el-input>
					<div class="tips">
						填写服务商户号绑定的公众号appid
					</div>
				</el-form-item>
				<el-form-item label="apiclient_cert.pem">
					<el-input type="textarea" :rows="4" placeholder="使用文本编辑器打开apiclient_cert.pem文件，将文件的全部内容复制进来"
						v-model="form.weixin_service.cert_pem" class="max-w460"></el-input>
					<div class="tips">使用文本编辑器打开apiclient_key.pem文件，将文件的全部内容复制进来</div>
				</el-form-item>
				<el-form-item label="apiclient_key.pem">
					<el-input type="textarea" :rows="4" placeholder="使用文本编辑器打开apiclient_cert.pem文件，将文件的全部内容复制进来"
						v-model="form.weixin_service.key_pem" class="max-w460"></el-input>
					<div class="tips">使用文本编辑器打开apiclient_key.pem文件，将文件的全部内容复制进来</div>
				</el-form-item>
			</template>
			<!--文件上传设置-->
			<div class="common-form">文件上传设置</div>
			<el-form-item label="最大图片上传">
				<el-input v-model="form.storage.max_image" class="max-w460">
					<template v-slot:append>M</template>
				</el-input>
				<div class="tips">修改后请修改php上传配置后生效</div>
			</el-form-item>
			<el-form-item label="最大视频上传">
				<el-input v-model="form.storage.max_video" class="max-w460">
					<template v-slot:append>M</template>
				</el-input>
				<div class="tips">修改后请修改php上传配置后生效</div>
			</el-form-item>
			<el-form-item label="默认上传方式">
				<div>
					<el-radio v-model="form.storage.default" label="local" @change="getRadio">本地 (不推荐)</el-radio>
					<el-radio v-model="form.storage.default" label="qiniu" @change="getRadio">七牛云存储</el-radio>
					<el-radio v-model="form.storage.default" label="aliyun" @change="getRadio">阿里云OSS</el-radio>
					<el-radio v-model="form.storage.default" label="qcloud" @change="getRadio">腾讯云COS</el-radio>
				</div>
			</el-form-item>
			<!--七牛云存储-->
			<div v-if="form.storage.default == 'qiniu'">
				<el-form-item label="存储空间名称 Bucket"><el-input v-model="form.storage.engine.qiniu.bucket"
						class="max-w460"></el-input></el-form-item>
				<el-form-item label="ACCESS_KEY AK"><el-input v-model="form.storage.engine.qiniu.access_key"
						class="max-w460"></el-input></el-form-item>
				<el-form-item label="SECRET_KEY SK"><el-input v-model="form.storage.engine.qiniu.secret_key"
						class="max-w460"></el-input></el-form-item>
				<el-form-item label="空间域名 Domain">
					<el-input v-model="form.storage.engine.qiniu.domain" class="max-w460"></el-input>
					<div class="tips">
						请补全http:// 或 https://，例如：http://static.cloud.com
					</div>
				</el-form-item>
			</div>
			<!--阿里云OSS-->
			<div v-if="form.storage.default == 'aliyun'">
				<el-form-item label="存储空间名称 Bucket"><el-input v-model="form.storage.engine.aliyun.bucket"
						class="max-w460"></el-input></el-form-item>
				<el-form-item label="AccessKeyId"><el-input v-model="form.storage.engine.aliyun.access_key_id"
						class="max-w460"></el-input></el-form-item>
				<el-form-item label="AccessKeySecret"><el-input v-model="form.storage.engine.aliyun.access_key_secret"
						class="max-w460"></el-input></el-form-item>
				<el-form-item label="空间域名 Domain">
					<el-input v-model="form.storage.engine.aliyun.domain" class="max-w460"></el-input>
					<div class="tips">
						请补全http:// 或 https://，例如：http://static.cloud.com
					</div>
				</el-form-item>
			</div>
			<!--腾讯云COS-->
			<div v-if="form.storage.default == 'qcloud'">
				<el-form-item label="存储空间名称 Bucket"><el-input
						v-model="form.storage.engine.qcloud.bucket"></el-input></el-form-item>
				<el-form-item label="所属地域 Region">
					<el-input v-model="form.storage.engine.qcloud.region"></el-input>
					<div class="tips">
						请填写地域简称，例如：ap-beijing、ap-hongkong、eu-frankfurt
					</div>
				</el-form-item>
				<el-form-item label="SecretId"><el-input
						v-model="form.storage.engine.qcloud.secret_id"></el-input></el-form-item>
				<el-form-item label="SecretKey"><el-input
						v-model="form.storage.engine.qcloud.secret_key"></el-input></el-form-item>
				<el-form-item label="空间域名 Domain">
					<el-input v-model="form.storage.engine.qcloud.domain"></el-input>
					<div class="tips">
						请补全http:// 或 https://，例如：http://static.cloud.com
					</div>
				</el-form-item>
			</div>
			<!--提交-->
			<div class="button-wrapper">
				<el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
			</div>
		</el-form>

	</div>

</template>

<script>
import SettingApi from '@/api/setting.js';
export default {
	data() {
		return {
			/*是否正在加载*/
			loading: false,
			/*form表单数据*/
			form: {
				admin_name: '',
				admin_bg_img: '',
				shop_name: '',
				shop_bg_img: '',
				weixin_service: {},
				storage: {
					default: 'local',
					max_image: '',
					max_video: '',
					engine: {
						qiniu: {},
						aliyun: {},
						qcloud: {},
					}
				},
			},

		};
	},
	created() {
		this.getParams();
	},

	methods: {

		/*获取配置数据*/
		getParams() {
			let self = this;
			SettingApi.serviceDetail({}, true).then(res => {
				let vars = res.data.vars.values;
				self.form = res.data.vars.values;
				self.form.weixin_service.is_open = parseInt(self.form.weixin_service.is_open);
				if (vars.storage) {
					self.form.storage.default = vars.storage.default;
					self.form.storage.max_image = vars.storage.max_image;
					self.form.storage.max_video = vars.storage.max_video;
					if(!vars.storage.engine){
						self.form.storage.engine = {
							qiniu: {},
							aliyun: {},
							qcloud: {},
						}
					}else{
						self.form.storage.engine.qiniu = vars.storage.engine.qiniu;
						self.form.storage.engine.aliyun = vars.storage.engine.aliyun;
						self.form.storage.engine.qcloud = vars.storage.engine.qcloud;
					}
					
				} else {
					self.form.storage = {
						default: 'local',
						max_image: '',
						max_video: '',
						engine: {
							qiniu: {},
							aliyun: {},
							qcloud: {},
						}
					};
				}

				self.loading = false;
			})
				.catch(error => {
					self.loading = false;
				});

		},
		/*提交*/
		onSubmit() {
			let self = this;
			let params = this.form;
			self.$refs.form.validate((valid) => {
				if (valid) {
					self.loading = true;
					SettingApi.editService(params, true)
						.then(data => {
							self.loading = false;
							ElMessage({
								message: '恭喜你，设置成功',
								type: 'success'
							});
							self.$router.push('/setting/Index');
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
	.tips {
		color: #ccc;
		width: 100%;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}

	input[type="number"] {
		-moz-appearance: textfield;
	}

	.button-wrapper {
		display: flex;
	}
</style>