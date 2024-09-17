<template>

	<div class="user">
		<el-form size="small" ref="form" :model="form" label-width="150px">
			<!--添加门店-->
			<div class="common-form">预售活动设置</div>
			<el-form-item label=" 未支付定金订单" prop="end_time" :rules="[{ required: true, message: ' ' }]">
				<div style="width: 500px;">
					<el-input placeholder="请输入" v-model="form.end_time" type="number">
						<template #append>
							分钟后自动关闭
						</template>
					</el-input>
					<p class="gray">预售订单下单未付款，n分钟后自动关闭，设置0则不自动关闭</p>
				</div>
			</el-form-item>
			<el-form-item label="未支付尾款订单" prop="pay_time" :rules="[{ required: true, message: ' ' }]">
				<div style="width: 500px;">
					<el-input placeholder="请输入" v-model="form.pay_time" type="number">
						<template #append>
							小时后自动关闭
						</template>
					</el-input>
					<p class="gray">预售订单下单未付款，n小时后自动关闭，设置0则不自动关闭</p>
				</div>
			</el-form-item>
			<el-form-item label="是否允许退定金" prop="money_return">
				<el-switch v-model="form.money_return"></el-switch>
			</el-form-item>
			<el-form-item label="是否开启优惠券抵扣" prop="is_coupon">
				<el-switch v-model="form.is_coupon"></el-switch>
			</el-form-item>
			<el-form-item label="是否开启分销" prop="is_agent">
				<el-switch v-model="form.is_agent"></el-switch>
				<p class="ww100 gray">注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</p>
			</el-form-item>
			<el-form-item label="是否开启积分抵扣" prop="is_point">
				<el-switch v-model="form.is_point"></el-switch>
			</el-form-item>
			<el-form-item label="是否开启会员折扣" prop="is_user_grade">
				<el-switch v-model="form.is_user_grade"></el-switch>
			</el-form-item>
			<el-form-item label="活动图片：" :rules="[{ required: true, message: '请上传活动图片' }]" prop="image">
				<div class="draggable-list">
					<draggable v-model="form.image" class="draggable-list">
						<template #item="{ element, index }">
							<div class="item">
								<img v-img-url="element.file_path" />
								<a href="javascript:void(0);" class="delete-btn" @click.stop="deleteImg(index)">
									<el-icon>
										<Close />
									</el-icon>
								</a>
							</div>
						</template>
					</draggable>
					<div class="item img-select" @click="openUpload()">
						<el-icon>
							<Plus />
						</el-icon>
					</div>
				</div>
				<div class="gray9">建议图片尺寸为750px*300px</div>
			</el-form-item>
		</el-form>

		<!--提交-->
		<div class="common-button-wrapper">
			<el-button size="small" type="primary" @click="onSubmit" :disabled="loading">保存</el-button>
		</div>
		<!--商品图片组件-->
		<Upload v-if="isUpload" :config="config" :isupload="isUpload" @returnImgs="returnProductImgsFunc">上传图片
		</Upload>
	</div>
</template>
<script>
import AdvanceApi from '@/api/advance.js';
import Upload from '@/components/file/Upload.vue';
import draggable from 'vuedraggable';
export default {
	components: {
		Upload,
		draggable
	},
	data() {
		return {
			form: {
				end_time: 10,
				pay_time: 10,
				image: [],
				money_return: 0,
				is_coupon: false,
				is_agent: false,
				is_point: false,
				is_user_grade: false
			},
			setting: [],
			loading: false,
			isUpload: false,
			config: {
				total: 9,
				file_type: 'image'
			},
		};
	},
	created() {
		/*获取列表*/
		this.getSetting();
	},
	methods: {
		/*打开上传图片*/
		openUpload() {
			this.isUpload = true;
		},

		/*上传商品图片*/
		returnProductImgsFunc(e) {
			console.log(e);
			if (e != null) {
				let imgs = this.form.image.concat(e);
				this.form.image = imgs;
			}
			this.isUpload = false;
		},

		/*删除商品图片*/
		deleteImg(index) {
			this.form.image.splice(index, 1);
		},
		/*获取设置*/
		getSetting() {
			let self = this;
			let Params = {};
			AdvanceApi.getSetting(Params, true)
				.then(data => {
					self.loading = false;
					self.form = data.data.vars.values;
				})
				.catch(error => {});
		},
		/*点击保存*/
		onSubmit() {
			let self = this;
			let params = self.form;
			if (!(params.end_time > -1) || !params.end_time) {
				ElMessage({
					message: '未支付定金时间有误',
					type: 'error'
				});
				return false;
			}
			if (!(params.pay_time > -1) || !params.pay_time) {
				ElMessage({
					message: '未支付尾款时间有误',
					type: 'error'
				});
				return false;
			}
			self.loading = true;
			AdvanceApi.saveSetting(params, true)
				.then(data => {
					self.loading = false;
					if (data.code == 1) {
						ElMessage({
							message: '恭喜你，保存成功',
							type: 'success'
						});
						self.getSetting();
					} else {
						self.loading = false;
					}
				})
				.catch(error => {
					self.loading = false;
				});
		}
	}
};
</script>
<style lang="scss" scoped></style>