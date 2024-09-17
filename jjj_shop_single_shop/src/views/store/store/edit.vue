<template>

	<div class="product-add">
		<!--form表单-->
		<el-form size="small" ref="form" :model="form" label-width="80px">
			<!--编辑门店-->
			<div class="common-form">编辑门店</div>
			<el-form-item label="门店名称" prop="store_name" :rules="[{required: true,message: ' '}]">
				<el-input v-model.trim="form.store_name" placeholder="请输入门店名称" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="门店logo">
				<el-row>
					<el-button icon="Picture" @click="openUpload">选择图片</el-button>
					<div v-if="file_path!=''" class="img">
						<img :src="file_path" width="100" />
					</div>
					<div class="gray">建议上传图像的尺寸比例为100:84</div>
				</el-row>
			</el-form-item>
			<el-form-item label="联系人" prop="linkman" :rules="[{required: true,message: ' '}]">
				<el-input v-model.trim="form.linkman" placeholder="请输入门店联系人" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="联系电话" prop="phone" :rules="[{required: true,message: ' '}]">
				<el-input v-model.trim="form.phone" placeholder="请输入门店联系电话" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="营业时间" prop="shop_hours" :rules="[{required: true,message: ' '}]">
				<el-input v-model.trim="form.shop_hours" placeholder="请输入营业时间,例如:8:30-17:30"
					class="max-w460"></el-input>
			</el-form-item>

			<el-form-item label="门店区域">
				<el-select v-model.trim="form.province_id" placeholder="省" @change="initCity">
					<el-option :label="item.name" :value="item.id" v-for="(item,index) in areaList"
						:key='index'></el-option>
				</el-select>
				<el-select v-if="form.province_id!=''" v-model.trim="form.city_id" placeholder="市" @change="initRegion"
					class="ml4">
					<el-option :label="item1.name" :value="item1.id"
						v-for="(item1,index1) in areaList[form.province_id]['city']" :key='index1'></el-option>
				</el-select>
				<el-select v-if="form.city_id!=''" v-model.trim="form.region_id" placeholder="区" class="ml4">
					<el-option :label="item2.name" :value="item2.id"
						v-for="(item2,index2) in areaList[form.province_id]['city'][form.city_id]['region']"
						:key='index2'></el-option>
				</el-select>

			</el-form-item>
			<el-form-item label="详细地址" prop="address" :rules="[{required: true,message: ' '}]">
				<el-input v-model.trim="form.address" placeholder="请输入详细地址" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="门店坐标">
				<div class=" d-s-c">
					<el-button v-if="tx_key" icon="LocationInformation" @click="mapClick()">选择经纬度</el-button>
					<div class="ml10"></div>
					<div class="max-w460">
						<el-input v-model="form.coordinate"></el-input>
					</div>
					<div><a href="https://lbs.qq.com/getPoint/" target="_blank">腾讯地图坐标获取</a></div>
				</div>
				<div class="tips" style="color: red;">
					未申请腾讯地图key，在腾讯地图里面搜索地址获取经纬度，例40.002749,116.323467
				</div>
			</el-form-item>
			<el-form-item label="门店简介">
				<el-input type="textarea" v-model.trim="form.summary" placeholder="请输入门店简介" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="门店排序">
				<el-input type="number" v-model.trim="form.sort" placeholder="请输入门店排序" class="max-w460"></el-input>
			</el-form-item>
			<el-form-item label="自提核销">
				<el-radio-group v-model.trim="form.is_check">
					<el-radio :label="1">支持</el-radio>
					<el-radio :label="0">不支持</el-radio>
				</el-radio-group>
			</el-form-item>
			<el-form-item label="门店状态">
				<el-radio-group v-model.trim="form.status">
					<el-radio :label="1">启用</el-radio>
					<el-radio :label="0">禁用</el-radio>
				</el-radio-group>
			</el-form-item>
			<!--提交-->
			<div class="common-button-wrapper">
				<el-button size="small" type="info" @click="cancelFunc">取消</el-button>
				<el-button size="small" type="primary" @click="onSubmit" :loading="loading">提交</el-button>
			</div>
		</el-form>
		<!--上传图片组件-->
		<Upload v-if="isupload" :isupload="isupload" :type="type" @returnImgs="returnImgsFunc">上传图片</Upload>
		<!--地图组件-->
		<Map :open="open_map" :mapData="mapData" :tx_key="tx_key" @close="closeDialogFunc($event, 'map')"></Map>
	</div>
</template>

<script>
	import Map from './dialog/Map.vue';
	import StoreApi from '@/api/store.js';
	import Upload from '@/components/file/Upload.vue';
	import {
		formatModel
	} from '@/utils/base.js'
	export default {
		components: {
			/*腾讯地图拾取器*/
			Map,
			Upload
		},
		data() {
			return {
				/*form表单数据*/
				form: {
					store_id: null,
					store_name: 0,
					store_name: '',
					linkman: '',
					phone: '',
					shop_hours: '',
					province_id: '',
					city_id: '',
					region_id: '',
					address: '',
					coordinate: '',
					summary: '',
					sort: '',
					is_check: '',
					status: '',
					logo_image_id: 0
				},
				/*省市区*/
				areaList: [],
				/*查询信息*/
				list: [],
				/*是否上传图片*/
				isupload: false,
				loading: false,
				file_path: '',
				open_map: false,
				mapData: {},
				tx_key: ''
			};
		},
		created() {

			this.form.store_id = this.$route.query.store_id;

			this.getData();
		},

		methods: {
			/*修改*/
			onSubmit() {
				let self = this;
				let form = self.form;
				self.$refs.form.validate((valid) => {
					if (valid) {
						self.loading = true;
						StoreApi.editShop(form, true)
							.then(data => {
								self.loading = false;
								ElMessage({
									message: '恭喜你，门店修改成功',
									type: 'success'
								});
								self.$router.push('/store/store/index');
							}).catch(error => {
								self.loading = false;
							});
					}
				});
			},
			/*初始化城市id*/
			initCity() {
				this.form.city_id = ''
			},
			/*初始化区id*/
			initRegion() {
				this.form.region_id = ''
			},

			/*获取经纬度*/
			getMapdataFunc(e) {
				this.form.coordinate = e.data[0].toFixed(6) + ',' + e.data[1].toFixed(6);
			},

			/*获取参数*/
			getData() {
				let self = this;
				StoreApi.shopDetail({
						store_id: self.form.store_id
					}, true)
					.then(res => {
						self.form = formatModel(self.form, res.data.model);
						self.file_path = res.data.model.logo.file_path;
						self.form.is_check = res.data.model.is_check.value;
						self.form.status = res.data.model.status.value;
						self.areaList = res.data.regionData;
						self.tx_key = res.data.key;
					})
					.catch(error => {

					});
			},
			/*打开地图*/
			mapClick() {
				this.mapData.coordinate = this.form.coordinate;
				this.mapData.address = this.form.address;
				this.open_map = true;
			},
			/*关闭获取用户弹窗*/
			closeDialogFunc(e, f) {
				if (f == 'map') {
					if (e && e.type != 'error' && e.params.coordinate) {
						this.form.address = e.params.address;
						this.form.coordinate = e.params.coordinate;
					}
					this.open_map = false;
				}
			},
			/*上传*/
			openUpload(e) {
				this.type = e;
				this.isupload = true;
			},

			/*获取图片*/
			returnImgsFunc(e) {
				if (e != null) {
					this.file_path = e[0].file_path;
					this.form.logo_image_id = e[0].file_id;
				}
				this.isupload = false;
			},

			/*选择的地址*/
			choseFunc(e) {
				this.form.coordinate = e.location.lat + ',' + e.location.lng;
				this.form.address = e.address;
			},

			/*取消*/
			cancelFunc() {
				this.$router.back(-1);
			}

		}

	};
</script>

<style lang="scss" scoped>
	.basic-setting-content {}

	.product-add {
		padding-bottom: 50px;
	}

	.img {
		margin-top: 10px;
	}
</style>