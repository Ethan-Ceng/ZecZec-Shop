<template>
	<div class="basic-setting-content pl16 pr16">
		<!--其他设置-->
		<div class="common-form">其他设置</div>
		<el-form-item label="商品属性：">
			<el-radio-group v-model="form.model.is_virtual">
				<el-radio :label="0">实物商品</el-radio>
				<el-radio :label="1">虚拟商品(无需发货)</el-radio>
			</el-radio-group>
		</el-form-item>
		<el-form-item label="运费模板：" :rules="[{ required: true, message: '选择运费模板' }]" prop="model.delivery_id"
			v-if="form.model.is_virtual==0">
			<el-select v-model="form.model.delivery_id" style="width: 460px;">
				<el-option v-for="item in form.delivery" :value="item.delivery_id" :key="item.delivery_id"
					:label="item.name"></el-option>
			</el-select>
		</el-form-item>

    <el-form-item label="常見問題：">
      <select-faq v-model="form.model.faq_ids" class="max-w460"></select-faq>
    </el-form-item>
		<el-form-item label="初始销量：">
			<el-input type="number" min="0" v-model="form.model.sales_initial" class="max-w460"></el-input>
		</el-form-item>
		<el-form-item label="商品排序：" :rules="[{ required: true, message: ' ' }]" prop="model.product_sort">
			<el-input type="number" min="0" v-model="form.model.product_sort" class="max-w460"></el-input>
		</el-form-item>
		<el-form-item label="限购数量：" :rules="[{ required: true, message: ' ' }]" prop="model.limit_num">
			<el-input type="number" min="0" v-model="form.model.limit_num" class="max-w460"></el-input>
			<div class="gray9">每个会员购买的最大数量，0为不限购</div>
		</el-form-item>
		<el-form-item label="发货类型：" v-if="form.model.is_virtual==1">
			<el-radio-group v-model="form.model.virtual_auto">
				<el-radio :label="1">自动</el-radio>
				<el-radio :label="0">手动</el-radio>
			</el-radio-group>
		</el-form-item>
		<el-form-item label="虚拟内容：" :rules="[{ required: true, message: '请填写虚拟内容' }]" prop="model.virtual_content"
			v-if="form.model.is_virtual==1">
			<el-input type="text" v-model="form.model.virtual_content" class="max-w460"></el-input>
			<div class="gray9">虚拟物品内容</div>
		</el-form-item>
		<el-form-item label="会员等级限制：">
			<el-select v-model="form.model.grade_ids" multiple placeholder="请选择" style="width: 460px;">
				<el-option v-for="item in form.gradeList" :key="item.grade_id" :label="item.name"
					:value="item.grade_id">
				</el-option>
			</el-select>
			<div class="gray9">仅设置的等级会员可购买，不设置则都可以购买</div>
		</el-form-item>
		<div class="common-form mt50">表单信息收集</div>
		    <el-form-item label="关联表单：">
		      <div class="max-w460">
		        <el-select v-model="form.model.table_id" placeholder="请选择" style="width: 460px;">
		          <el-option v-for="item in form.tableList" :value="item.table_id" :key="item.table_id" :label="item.name"></el-option>
		        </el-select>
		      </div>
		    </el-form-item>
		<el-form-item label="自定义表单：">
			<div>
				<el-switch :active-value="true" :inactive-value="false" v-model="customBtn" @change="customMessBtn"
					size="large">
					<span slot="open">开启</span>
					<span slot="close">关闭</span>
				</el-switch>
				<div class="addCustom_content" v-if="customBtn">
					<div v-for="(item, index) in form.model.custom_form" :key="index" class="custom_box">
						<el-input v-model.trim="item.title" :placeholder="'表单标题' + (index + 1)"
							style="width: 150px; margin-right: 10px" :maxlength="10" />
						<el-select v-model="item.label" style="width: 200px; margin-left: 6px; margin-right: 10px">
							<el-option v-for="items in CustomList" :value="items.value" :key="items.value"
								:label="items.label"></el-option>
						</el-select>
						<el-checkbox v-model="item.status">必填</el-checkbox>
						<div class="addfont" @click="delcustom(index)">删除</div>
					</div>
				</div>
				<div class="addCustomBox" v-if="customBtn">
					<el-button class="btn" @click="addcustom">+ 添加表单</el-button>
					<div class="titTip">
						用户下单时需填写的信息，最多可设置10条，设置了自定义表单的商品不能加入购物车
					</div>
				</div>
			</div>

		</el-form-item>
		<!--商品图片组件-->
		<Upload v-if="isProductUpload" :config="config" :isupload="isProductUpload" @returnImgs="returnProductImgsFunc">
			上传图片</Upload>
	</div>
</template>

<script>
	import Upload from '@/components/file/Upload.vue';
	import draggable from 'vuedraggable';
  import SelectFaq from "@/components/product/select-faq.vue";
	export default {
		components: {
      SelectFaq,
			Upload,
			draggable
		},
		data() {
			return {
				isProductUpload: false,
				config: {},
				file_name: 'image',
				customBtn: false,
				CustomList: [{
						value: "text",
						label: "文本框",
					},
					{
						value: "number",
						label: "数字",
					},
					{
						value: "email",
						label: "邮件",
					},
					{
						value: "data",
						label: "日期",
					},
					{
						value: "time",
						label: "时间",
					},
					{
						value: "id",
						label: "身份证",
					},
					{
						value: "phone",
						label: "手机号",
					}
				],
			};
		},
		inject: ['form'],
		created() {
			if (this.form.model.custom_form && this.form.model.custom_form.length != 0) {
				this.customBtn = true;
			}
		},
		methods: {
			customMessBtn(e) {
				if (!e) {
					this.form.model.custom_form = [];
				}
			},
			addcustom() {
				if (!this.form.model.custom_form) {
					this.form.model.custom_form = [];
				}
				if (this.form.model.custom_form && this.form.model.custom_form.length > 9) {
					ElMessage.warning("最多添加10条");
				} else {
					this.form.model.custom_form.push({
						title: "",
						label: "text",
						value: "",
						status: false,
					});
				}
			},
			delcustom(index) {
				this.form.model.custom_form.splice(index, 1);
			},
			/*打开上传图片*/
			openProductUpload: function(file_type, file_name) {
				this.file_name = file_name;
				if (file_type == 'image') {
					this.config = {
						total: 9,
						file_type: 'image'
					};
				} else {
					this.config = {
						total: 1,
						file_type: 'video'
					};
				}
				this.isProductUpload = true;
			},

			/*上传商品图片*/
			returnProductImgsFunc(e) {
				console.log(e);
				if (e != null) {
					if (this.file_name == 'video') {
						this.form.model.video_id = e[0].file_id;
						this.form.model.video = e[0];
					} else if (this.file_name == 'image') {
						let imgs = this.form.model.image.concat(e);
						this.form.model.image = imgs;
					} else if (this.file_name == 'poster') {
						this.form.model.poster_id = e[0].file_id;
						this.form.model.poster = e[0];
					}
				}
				this.isProductUpload = false;
			},

			/*删除商品图片*/
			deleteImg(index) {
				this.form.model.image.splice(index, 1);
			},
			delVideo() {
				this.form.model.video_id = 0;
				this.form.model.video = {};
			},
			deleteVideo() {
				this.form.model.poster_id = 0;
				this.form.model.poster = {};
			},
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