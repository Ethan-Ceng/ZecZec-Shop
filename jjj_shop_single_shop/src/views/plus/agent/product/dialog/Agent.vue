<template>

  <el-dialog title="商品规则" v-model="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false"
    :close-on-press-escape="false" width="600px">
    <el-form size="small" :model="form" ref="form">
      <el-form-item label="商品名称" :label-width="formLabelWidth">
        <span>{{productModel.product_name}}</span>
      </el-form-item>
      <el-form-item label="是否参与推广" :label-width="formLabelWidth">
        <el-radio-group v-model="is_agent">
          <el-radio :label="1">参与</el-radio>
          <el-radio :label="0">不参与</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="是否开启单独分销：" v-if="is_agent" :label-width="formLabelWidth">
        <el-radio-group v-model="form.is_ind_agent">
          <el-radio :label="1">开启</el-radio>
          <el-radio :label="0">关闭</el-radio>
        </el-radio-group>
      </el-form-item>
      <template v-if="form.is_ind_agent == 1 && is_agent == 1">
        <el-form-item label="分销佣金类型：" :label-width="formLabelWidth">
          <el-radio-group v-model="form.agent_money_type" @change="changeMoneyType">
            <el-radio :label="10">百分比</el-radio>
            <el-radio :label="20">固定金额</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="单独分销设置：" :label-width="formLabelWidth">
          <el-input type="number" min="0" v-model="form.first_money" class="max-w460">
            <template #prepend>
              一级佣金：
            </template>
            <template #append>
              {{ unit }}
            </template>
          </el-input>
        </el-form-item>
        <el-form-item label="" v-if="basicSetting.level >= 2" :label-width="formLabelWidth">
          <el-input type="number" min="0" v-model="form.second_money" class="max-w460">
            <template #prepend>
              二级佣金：
            </template>
            <template #append>
              {{ unit }}
            </template>
          </el-input>
        </el-form-item>
        <el-form-item label="" v-if="basicSetting.level >= 3" :label-width="formLabelWidth">
          <el-input type="number" min="0" v-model="form.third_money" class="max-w460">
            <template #prepend>
              三级佣金：
            </template>
            <template #append>
              {{ unit }}
            </template>
          </el-input>
        </el-form-item>
      </template>
    </el-form>
    <template #footer> 
        <div class="dialog-footer">
          <el-button @click="dialogFormVisible(false)">取 消</el-button>
          <el-button type="primary" @click="saveProduct" :loading="loading">确 定</el-button>
        </div>
    </template>
  </el-dialog>

</template>

<script>
import AgentPorductApi from '@/api/plus/agent/product.js';
export default {
	data() {
		return {
			form: {
				is_ind_agent: 0
			},
			unit: '%',
			is_agent: 0,
			basicSetting: {},
			/*左边长度*/
			formLabelWidth: '120px',
			/*是否显示*/
			dialogVisible: false,
			loading: false,
		};
	},
	props: ['open_agent', 'productModel'],
	created() {
		this.is_agent = this.productModel.is_agent;
		this.dialogVisible = this.open_agent;
		this.getData();
	},
	methods: {
		getData(){
			let self = this;
			AgentPorductApi.toEditProduct({
				product_id: self.productModel.product_id
			}).then(res => {
				self.form = res.data.agent_product;
				if(self.form == null){
					self.form = {
						is_ind_agent: 0,
						agent_money_type: 10
					};
				}
				self.basicSetting = res.data.basicSetting;
			}).catch(error => {

			});
		},
		/*换算单位*/
		changeMoneyType: function(val) {
			if (val == '10') {
				this.unit = '%';
			} else {
				this.unit = '元';
			}
		},
		/*保存*/
		saveProduct() {
			let self = this;
			let params = self.form;
			params.product_id = self.productModel.product_id;
			params.is_agent = self.is_agent;
			self.loading = true;
			AgentPorductApi.editProduct(params).then(data => {
				self.loading = false;
				ElMessage({
					message: data.msg,
					type: 'success'
				});
				self.dialogFormVisible(true);
			}).catch(error => {
				self.loading = false;
			});
		},
		/*关闭弹窗*/
		dialogFormVisible(e) {
			if (e) {
				this.$emit('closeDialog', {
					type: 'success',
					openDialog: false
				});
			} else {
				this.$emit('closeDialog', {
					type: 'error',
					openDialog: false
				});
			}
		},
	}
};
</script>

<style scoped>
 .img {
    margin-top: 10px;
  }
</style>
