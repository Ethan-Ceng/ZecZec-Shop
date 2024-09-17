<template>
	<el-dialog title="编辑" v-model="dialogVisible" @close="cancelFunc" :close-on-click-modal="false"
		:close-on-press-escape="false" width="70%">
		<el-form :model="formData">
			<el-form-item label="微信昵称" :label-width="formLabelWidth">
				<el-input type="text" v-model="formData.nickName" autocomplete="off" :disabled="true"></el-input>
			</el-form-item>
			<el-form-item label="微信头像" :label-width="formLabelWidth">
				<img v-img-url="formData.avatarUrl" :width="50" :height="50" />
			</el-form-item>
			<el-form-item label="姓名" :label-width="formLabelWidth">
				<el-input type="text" v-model="formData.real_name" autocomplete="off"></el-input>
			</el-form-item>
			<el-form-item label="手机号" :label-width="formLabelWidth">
				<el-input type="text" v-model="formData.mobile" autocomplete="off"></el-input>
			</el-form-item>
			<el-form-item label="等级" :label-width="formLabelWidth">
				<el-select v-model="formData.grade_id" placeholder="-请选择等级-">
					<el-option v-for="(item, index) in gradeList" :key="index" :label="item.name"
						:value="item.grade_id"></el-option>
				</el-select>
			</el-form-item>
			<el-form-item label="推荐人ID" :label-width="formLabelWidth">
				<el-input type="text" v-model="formData.referee_id" autocomplete="off"></el-input>
				<div class="tips">如果没有上级则设置为0</div>
			</el-form-item>
			<el-form-item label="推荐人昵称" :label-width="formLabelWidth">
				<span v-if="formData.referee">{{ formData.referee.nickName }}</span>
			</el-form-item>
		</el-form>
		<template #footer>
			<div class="dialog-footer">
				<el-button @click="cancelFunc">取 消</el-button>
				<el-button type="primary" @click="confirmFunc">确 定</el-button>
			</div>
		</template>
	</el-dialog>
</template>

<script>
	import PlusApi from '@/api/plus.js';
	export default {
		data() {
			return {
				/*左边长度*/
				formLabelWidth: '100px',
				/*是否显示*/
				dialogVisible: false,
				/*表单数据模型*/
				formData: {},
				old_referee_id: 0
			};
		},
		props: {
			open_edit: Boolean,
			userModel: Object,
			gradeList: Array
		},
		watch: {
			open_edit: function(n, o) {
				if (n != o) {
					this.dialogVisible = this.open_edit;
					if (n) {
						this.formData = this.userModel;
						this.old_referee_id = this.formData.referee_id;
					}
				}
			}
		},
		created() {},
		methods: {
			/*修改用户*/
			confirmFunc() {
				let self = this;
				let params = {};
				params.user_id = this.formData.user_id;
				params.real_name = this.formData.real_name;
				params.mobile = this.formData.mobile;
				params.grade_id = this.formData.grade_id;
				if (self.formData.referee_id > 0 && self.formData.referee_id != self.old_referee_id) {
					PlusApi.toAgentUserEdit({
							user_id: self.formData.referee_id
						}, true)
						.then(res => {
							if (!res.data.model) {
								ElMessage({
									message: '上级用户不存在',
									type: 'error'
								});
								return;
							}
							if (this.formData.user_id == self.formData.referee_id) {
								ElMessage({
									message: '不能绑定自己',
									type: 'error'
								});
								return;
							}
							ElMessageBox.confirm('确定要修改上级到' + res.data.model.real_name + '?', '提示', {
								type: 'warning'
							}).then(() => {
								params.referee_id = self.formData.referee_id;
								self.editUser(params);
							});
						})
						.catch(error => {});
				} else {
					self.editUser(params);
				}
			},
			editUser(params) {
				let self = this;
				PlusApi.agentUserEdit(params, true)
					.then(data => {
						ElMessage({
							message: '恭喜你，修改成功',
							type: 'success'
						});
						self.cancelFunc(true);
					})
					.catch(error => {});
			},
			/*关闭弹窗*/
			cancelFunc(e) {
				let type = 'cancel';
				if (e) {
					type = 'success';
				}
				this.$emit('close', {
					type: type
				});
			}
		}
	};
</script>

<style></style>