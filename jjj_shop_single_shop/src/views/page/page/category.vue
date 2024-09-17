<template>

	<div class="d-s-s diy-category ">
		<!--分类不同样式展示-->
		<div class="model-container flex-1">
			<div class="img-box">
				<template v-if="formData.category_style==10">
					<el-image @click="formData.wind_style = 1" :class="{active:formData.wind_style == 1}" :src="category_10_1" fit="fill"></el-image>
					<el-image @click="formData.wind_style = 2" :class="{active:formData.wind_style == 2}" :src="category_10_2" fit="fill"></el-image>
					<el-image @click="formData.wind_style = 3" :class="{active:formData.wind_style == 3}" :src="category_10_3" fit="fill"></el-image>
					<el-image @click="formData.wind_style = 4" :class="{active:formData.wind_style == 4}" :src="category_10_4" fit="fill"></el-image>
				</template>
				<template v-else-if="formData.category_style==20">
					<el-image @click="formData.wind_style = 1" :class="{active:formData.wind_style == 1}" :src="category_20_1" fit="fill"></el-image>
					<el-image @click="formData.wind_style = 2" :class="{active:formData.wind_style == 2}" :src="category_20_2" fit="fill"></el-image>
					<el-image @click="formData.wind_style = 3" :class="{active:formData.wind_style == 3}" :src="category_20_3" fit="fill"></el-image>
				</template>
			</div>
		</div>
		<!--图片展示参数-->
		<div class="param-container ">
			<div class="form-title">分类页面</div>
			<el-form size="small" :model="formData" label-width="100px">
				<div class="form-item" style="align-items: flex-start;">
					<div class="form-label">分类：</div>
					<div class="flex-1">
						<el-radio-group v-model="formData.category_style" @change="changeCategory">
							<el-radio :label="10">一级分类</el-radio>
							<el-radio :label="20">二级分类</el-radio>
							<!-- <el-radio :label="30">三级分类</el-radio> -->
						</el-radio-group>
						<p class="gray f12">建议：一级分类(大图)尺寸建议710*300，其它均为等比图片即可，如：240*240</p>
					</div>
				</div>
				<div class="form-item flex-1 d-s-c ww100">
					<span class="form-label">分享标题:</span>
					<el-input v-model="formData.share_title"></el-input>
				</div>
			</el-form>
		</div>

		<!--提交-->
		<div class="common-button-wrapper"><el-button size="small" type="primary" @click="Submit()">保存</el-button></div>
	</div>
</template>

<script>
import PageApi from '@/api/page.js';
import category_10_1 from '@/assets/img/category_10_1.png';
import category_10_2 from '@/assets/img/category_10_2.png';
import category_10_3 from '@/assets/img/category_10_3.png';
import category_10_4 from '@/assets/img/category_10_4.jpg';
import category_20_1 from '@/assets/img/category_20_1.png';
import category_20_2 from '@/assets/img/category_20_2.jpg';
import category_20_3 from '@/assets/img/category_20_3.jpg';
export default {
	data() {
		return {
			/*表单数据对象*/
			formData: {
				category_style: null,
				style: 1
			},
			/*展示样式*/
			category_10_1,
			category_10_2,
			category_10_3,
			category_10_4,
			category_20_1,
			category_20_2,
			category_20_3
		};
	},
	created() {

		this.getData();

	},
	methods: {
		lookProduct(){
			this.$refs.categoryMaskRef.open();
		},
		isBuyFast(){
			if(this.isLogin){
				if(this.isLogin && (this.showType == 10 && this.style == 4) || (this.showType == 20 && this.style == 3)){
					this.scrollviewHigh = this.phoneHeight - this.searchHeight - this.shoppingHeight;
					return true
				}
			}
			this.scrollviewHigh = this.phoneHeight - this.searchHeight - this.footerHeight;
			return false
		},
		showTwo(){
			if(this.showType == 20 && (this.style == 2 || this.style == 3)){
				return true
			}
			if(this.showType == 10 && this.style == 2){
				return true
			}
			return false
		},
		changeCategory(e) {
			this.formData.wind_style = 1;
			console.log(e);
		},
		/*获取数据*/
		getData() {
			let self = this;
			PageApi.getCategory({}, true).then(res => {
				self.formData = res.data.model;
				if (!self.formData.wind_style) {
					self.formData.wind_style = 1;
				}
				if(self.formData.category_style == 30){
					self.formData.category_style = 20;
				}
			}).catch(error => {
				self.loading = false;
			});
		},

		/*提交*/
		Submit() {
			let self = this;
			self.loading = true;
			let Params = self.formData;
			PageApi.postCategory(Params, true)
				.then(data => {
					self.loading = false;
					ElMessage({
						message: '恭喜你，修改成功',
						type: 'success'
					});
					self.getData();
				})
				.catch(error => {
					self.loading = false;
				});
		}
	}
};
</script>

<style lang="scss" scoped="scoped">
	.diy-category {
		margin: 0;
		padding: 0;
		background: none;
	}

	.model-container {
		width: 300px;
		height: calc(100vh - 98px);
		margin-right: 30px;
	}

	.model-container .img-box .el-image {
		max-width: 362px;
		// height: 645px;
		flex: 1;
		margin-right: 60px;
		margin-bottom: 20px;
		border: 3px solid;
		border-color: rgba(0, 0, 0, 0);
		box-sizing: border-box;
	}

	.model-container .img-box .el-image.active {
		border-color: #409EFF;
	}

	.model-container .img-box .el-image:last-child {
		margin-right: 0;
	}

	.model-container .img-box {
		// box-shadow: 0 0 16px 0 rgba(0, 0, 0, .1);
		display: flex;
		justify-content: space-around;
		align-items: center;
		flex-wrap: wrap;
		padding: 70px 40px 0 40px;
	}

	.param-container {
		width: 376px;
		height: calc(100vh - 98px);
		overflow-y: auto;
		background: #fff;
		/* border: 1px solid #cccccc; */
	}

	.param-container .el-form-item {

		--font-size: 14px !important;
	}


	.form-title {
		padding: 0 22px;
		height: 62px;
		display: flex;
		justify-content: flex-start;
		align-items: center;
		border-bottom: 1px solid #eee;
		font-size: 16px;
		color: #666;
		font-weight: bold;
	}

	.key-name {
		font-size: 16px;
		color: #666;
		white-space: nowrap;
		margin-right: 30px;
	}

	.form-item {
		display: flex;
		justify-content: center;
		align-items: center;
		font-size: 14px;
		padding: 10px;
		padding-right: 18px;
	}

	.form-label {
		width: 122px;
		margin-right: 10px;
		text-align: right;
	}
</style>