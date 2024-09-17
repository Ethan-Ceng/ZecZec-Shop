<template>
	<div class="basic-setting-content pl16 pr16">
		<!--基本信息-->
		<div class="common-form">会员设置</div>
		<el-form-item label="默认昵称" :rules="[{required: true,message: ' '}]" prop="user_name">
			<el-input v-model.trim="form.user_name" placeholder="默认昵称" class="max-w460"></el-input>
			<div class="tips">小程序和H5端用户默认昵称前缀，和用户id组成默认昵称</div>
		</el-form-item>
		<el-form-item label="默认头像" :rules="[{required: true,message: '请选择默认头像'}]">
			<div class="ww100">
				<el-button @click="chooseImg('avatarUrl')">选择图片</el-button>
			</div>
			<img class="mt10" v-img-url="form.avatarUrl" :width="100">
			<div class="gray">建议图片上传尺寸为240px*240px</div>
		</el-form-item>
		<div class="common-form">小程序设置</div>
		<el-form-item label="登录LOGO" :rules="[{required: true,message: ' '}]" prop="login_logo">
			<div class="ww100">
				<el-button @click="chooseImg('login_logo')">选择图片</el-button>
			</div>
			<div class="d-c">
				<img class="mt10" v-img-url="form.login_logo" :width="100">
				<div class="gray">建议上传图像大小尺寸为100px*100px</div>
			</div>
		</el-form-item>
		<el-form-item label="授权登录描述" :rules="[{required: true,message: ' '}]" prop="login_desc">
			<el-input v-model.trim="form.login_desc" placeholder="授权登录描述" class="max-w460"></el-input>
			<div class="tips">小程序授权登录文字描述</div>
		</el-form-item>
		<el-form-item label="小程序发货" prop="is_send_wx">
			<el-checkbox v-model.trim="form.is_send_wx">向小程序发货</el-checkbox>
			<div class="tips">后台发货后自动向小程序发货</div>
		</el-form-item>
		<el-form-item label="是否开启微信授权" prop="wx_open">
			<el-checkbox v-model.trim="form.wx_open">开启微信授权</el-checkbox>
			<div class="tips">小程序用户登录是否开启微信授权</div>
		</el-form-item>
		<el-form-item label="是否开启手机号授权" prop="wx_phone">
			<el-checkbox v-model.trim="form.wx_phone">开启手机号授权</el-checkbox>
			<div class="tips">小程序用户登录是否开启手机号授权</div>
		</el-form-item>
		<div class="common-form">H5/公众号设置</div>
		<el-form-item label="是否开启短信验证" prop="sms_open">
			<el-checkbox v-model.trim="form.sms_open">开启短信验证</el-checkbox>
			<div class="tips">用户H5注册是否开启短信验证</div>
		</el-form-item>
		<el-form-item label="是否开启微信授权" prop="mp_open">
			<el-checkbox v-model.trim="form.mp_open">开启微信授权</el-checkbox>
			<div class="tips">公众号用户登录是否开启微信授权</div>
		</el-form-item>
		<el-form-item label="是否开启手机号绑定" prop="mp_phone">
			<el-checkbox v-model.trim="form.mp_phone">开启手机号绑定</el-checkbox>
			<div class="tips">公众号是否开启手机号绑定</div>
		</el-form-item>
		<!--上传图片-->
		<Upload v-if="isupload" :isupload="isupload" :type="type" :config="{ total: 1 }" @returnImgs="returnImgsFunc">
		</Upload>
	</div>
</template>

<script>
	import Upload from '@/components/file/Upload.vue';
	export default {
		components: {
			Upload
		},
		data() {
			return {
				/*是否打开图片选择*/
				isupload: false,
				type: '',
			};
		},
		inject: ['form'],
		created() {

		},
		methods: {
			/*选择图片*/
			chooseImg(e) {
				this.type = e;
				this.isupload = true;
			},
			/*关闭选择图片*/
			returnImgsFunc(e) {
				this.isupload = false;
				if (e != null && e.length > 0) {
					if (this.type == 'avatarUrl') {
						this.form.avatarUrl = e[0].file_path;
					} else if (this.type == 'login_logo') {
						this.form.login_logo = e[0].file_path;
					}
				}
			}
		}
	};
</script>
<style lang="scss" scoped>
	.addCustom_content {
		margin-top: 20px;

		.custom_box {
			margin-bottom: 10px;
		}
	}

	.addCustomBox {
		margin-top: 12px;
		font-size: 13px;
		font-weight: 400;
		color: var(--prev-color-primary);

		.btn {
			cursor: pointer;
			width: max-content;
			background-color: rgba(64, 149, 229, 1);
			color: rgba(255, 255, 255, 1);
		}
	}

	.titTip {
		display: inline-bolck;
		font-size: 12px;
		line-height: 24px;
		font-weight: 400;
		color: #999999;
	}

	.addfont {
		display: inline-block;
		font-size: 12px;
		font-weight: 400;
		color: #4095e5;
		margin-left: 14px;
		cursor: pointer;
	}
</style>