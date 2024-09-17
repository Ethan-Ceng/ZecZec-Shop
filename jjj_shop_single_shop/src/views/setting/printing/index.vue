<template>

	<div class="product-add">
		<!--form表单-->
		<el-form size="small" ref="form" :model="form" label-width="200px">
			<!--小票打印设置-->
			<div class="common-form">小票打印设置</div>

			<el-form-item label="是否开启小票打印">
				<div>
					<el-radio v-model.trim="form.is_open" :label="1">开启</el-radio>
					<el-radio v-model.trim="form.is_open" :label="0">关闭</el-radio>
				</div>
			</el-form-item>
			<el-form-item label="选择订单打印机">
				<el-select v-model.trim="form.printer_id" placeholder="请选择" style="width: 460px;">
					<el-option v-for="(item,index) in printerList" :key="index" :label="item.printer_name"
						:value="item.printer_id+''"></el-option>
				</el-select>
			</el-form-item>

			<el-form-item label="订单打印方式">
				<el-checkbox v-model.trim="checked" @change="handleCheckedCitiesChange">订单付款时</el-checkbox>
			</el-form-item>

			<!--电子面单打印设置-->
			<div class="common-form">电子面单打印设置</div>
			<el-form-item label="电子面单打印方式">
				<div>
					<el-radio v-model.trim="form.label_print_type" label="1">快递100云打印</el-radio>
					<el-radio v-model.trim="form.label_print_type" label="0">本地打印</el-radio>
				</div>
			</el-form-item>
			<el-form-item label="快递100云打印机编码" prop="siid" v-if="form.label_print_type=='1'">
				<el-input v-model.trim="form.siid" placeholder="" class="max-w460"></el-input>
				<div class="tips">如需使用快递100云打印功能,请购买快递100云打印机,并在快递100打印机管理绑定该设备,快递100打印机编码查看贴在硬件上的标签SIID设备码</div>
			</el-form-item>


			<!--提交-->
			<div class="common-button-wrapper">
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
			/*切换菜单*/
			// activeIndex: '1',
			/*form表单数据*/
			form: {
				is_open: '',
				printer_id: '',
				order_status: [],
				label_print_type: '',
				siid: ''
			},
			checked: false,
			printerList: [],
			loading: false,


		};
	},
	created() {
		this.getData();
	},

	methods: {
		getData() {
			let self = this;
			SettingApi.printingDetail({}, true)
				.then(data => {
					let vars = data.data.vars.values;
					self.form.is_open = vars.is_open;
					self.form.printer_id = '' + vars.printer_id;
					self.form.order_status = vars.order_status;
					self.form.label_print_type = vars.label_print_type;
					self.form.siid = vars.siid;
					self.printerList = data.data.vars.printerList;
					if (vars.order_status != null && vars.order_status[0] == 20) {
						self.checked = true;
					}

				})
				.catch(error => {});
		},
		//提交表单
		onSubmit() {
			let self = this;
			let params = this.form;
			self.loading = true;
			SettingApi.editPrinting(params, true)
				.then(data => {
					self.loading = false;
					ElMessage({
						message: '恭喜你，打印设置成功',
						type: 'success'
					});
					// self.$router.push('/setting/Printing');

				})
				.catch(error => {
					self.loading = false;
				});
		},
		//监听复选框选中
		handleCheckedCitiesChange(e) {
			let self = this;
			if (e) {
				self.form.order_status[0] = 20;
			} else {
				self.form.order_status = [];
			}
		},

	}

};
</script>

<style></style>