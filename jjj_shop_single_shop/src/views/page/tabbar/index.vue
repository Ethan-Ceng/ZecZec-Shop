<template>

	<div class="d-s-s pb50 diy-tabbar">
		<!--分类不同样式展示-->
		<div class="flex-1 mr30 pt30">
			<div class="model-container d-c d-e-c">
				<div class="img-box p10 ww100" :style="'background-color:'+formData.backgroundColor+';'">
					<div class="d-a-c">
						<div v-for="(item,index) in formData.list" :key="index" class="d-c d-c-c"
							@mouseenter="mouseenter" @mouseleave="mouseleave">
							<div v-if="formData.type != 2">
								<img class="nav_img" v-img-url="index==0?item.selectedIconPath:item.iconPath" alt="">
							</div>
							<div v-if="formData.type != 1"
								:style="index==0?'color:'+formData.textHoverColor+';':'color:'+formData.textColor+';'">
								{{item.text}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--图片展示参数-->
		<div class="param-container">
			<div class="form-title">底部导航</div>
			<el-form size="small" :model="formData" label-width="130px">
				<div class="f16 gray3 form-subtitle">样式设置</div>
				<div class="form-item">
					<div class="form-label">是否开启自定义：</div>
					<div class="flex-1">
						<el-radio-group v-model="formData.is_auto">
							<el-radio label="1">开启</el-radio>
							<el-radio label="0">关闭</el-radio>
						</el-radio-group>
					</div>
				</div>
				<div class="form-item">
					<div class="form-label">导航类型：</div>
					<div class="flex-1">
						<el-radio-group v-model="formData.type">
							<el-radio label="0">图文</el-radio>
							<el-radio label="1">图片</el-radio>
							<el-radio label="2">文字</el-radio>
						</el-radio-group>
					</div>
				</div>
				<div class="form-item">
					<div class="form-label">背景颜色：</div>
					<div class="flex-1 d-s-c" style="height: 36px;">
						<el-color-picker size="default" v-model="formData.backgroundColor"></el-color-picker>
						<el-input class="ml10" v-model="formData.backgroundColor" />
						<el-button style="margin-left: 10px;"
							@click.stop="onEditorResetColor(formData, 'backgroundColor', '#ffffff')" type="primary"
							link>重置</el-button>
					</div>
				</div>
				<div class="form-item">
					<div class="form-label">文字颜色：</div>
					<div class="flex-1 d-s-c" style="height: 36px;">
						<el-color-picker size="default" v-model="formData.textColor"></el-color-picker>
						<el-input class="ml10" v-model="formData.textColor" />
						<el-button style="margin-left: 10px;"
							@click.stop="onEditorResetColor(formData, 'textColor', '#333333')" type="primary"
							link>重置</el-button>
					</div>
				</div>
				<div class="form-item">
					<div class="form-label">选中颜色：</div>
					<div class="flex-1 d-s-c" style="height: 36px;">
						<el-color-picker size="default" v-model="formData.textHoverColor"></el-color-picker>
						<el-input class="ml10" v-model="formData.textHoverColor" />
						<el-button style="margin-left: 10px;"
							@click.stop="onEditorResetColor(formData, 'textHoverColor', '#E2231A')" type="primary"
							link>重置</el-button>
					</div>
				</div>
				<div class="form-chink"></div>
				<div class="f16 gray3 form-subtitle">内容设置 <span class="gray f12">图片建议宽度88*88px；鼠标拖拽左侧圆点可调整导航顺序</span>
				</div>
				<template v-if=" formData.list&& formData.list.length>0">
					<draggable v-model="formData.list" group="people" item-key="index" class="draggable-list">
						<template #item="{ element,index }">
							<div class="d-b-c param-img-item">
								<div class="d-c d-c-c mr10" style="width: 50px;">
									<el-icon style="height: 6px;" color="#999" size="12px" v-for="item in 6" :key="item">
										<MoreFilled />
									</el-icon>
								</div>
								<div class="right">
									<el-icon class="el-icon-DeleteFilled" @click="onEditorDeleleData(index)">
										<CloseBold />
									</el-icon>
									<el-form-item label="显示隐藏：" v-if="element.text=='店铺'">
										<el-radio-group v-model="element.is_show">
											<el-radio :label="false">隐藏</el-radio>
											<el-radio :label="true">显示</el-radio>
										</el-radio-group>
									</el-form-item>
									<div class="d-s-c mb16 ww100">
										<div class="url-box  d-s-c">
											<span class="key-name">图标</span>
										</div>
										<div class="d-a-c flex-1">
											<div class="icon">
												<div class="icon-text">未选中</div>
												<img v-img-url="element.iconPath" alt=""
													@click="onEditorSelectImage(element, 'iconPath')">
											</div>
											<div class="icon">
												<div class="icon-text">已选中</div>
												<img v-img-url="element.selectedIconPath" alt=""
													@click="onEditorSelectImage(element, 'selectedIconPath')">
											</div>
										</div>
									</div>
									<div class="url-box mb16 flex-1 d-s-c ww100">
										<span class="key-name">名称</span>
										<el-input maxlength="6" show-word-limit v-model="element.text"></el-input>
									</div>
									<div class="d-s-c  ww100">
										<div class="url-box flex-1 d-s-c">
											<span class="key-name">链接</span>
											<el-input :suffix-icon="ArrowRight" @click="changeLink(index)"
												v-model="element.link_url">
												<template #suffix>
													<el-icon color='#333' size="16px">
														<ArrowRight />
													</el-icon>
												</template>
											</el-input>
										</div>
									</div>

								</div>
							</div>
						</template>
					</draggable>
				</template>
				<div class="d-c-c pb16" v-if="formData.list.length<5">
					<el-button plain type="primary" @click="onEditorAddData">+添加图文导航{{formData.list.length}}/5</el-button>
				</div>
			</el-form>
		</div>
		<!--提交-->
		<div class="common-button-wrapper">
			<el-button size="small" type="primary" @click="Submit()">保存</el-button>
		</div>
		<!--上传图片-->
		<Upload v-if="isupload" :isupload="isupload" :config="{ total: 3 }" @returnImgs="returnImgsFunc"></Upload>
		<Setlink v-if="is_linkset" :is_linkset="is_linkset" @closeDialog="closeLinkset">选择链接</Setlink>
	</div>
</template>

<script>
import PageApi from '@/api/page.js';
import Upload from '@/components/file/Upload.vue';
import Setlink from '@/components/setlink/SetTab.vue';
import draggable from 'vuedraggable';
export default {
	data() {
		return {
			/*是否上传图片*/
			isupload: false,
			/*表单数据对象*/
			formData: {
				/* 导航类型*/
				type: '0',
				is_auto: '0',
				/* 背景颜色*/
				backgroundColor: '#ffffff',
				textColor: '#333333',
				textHoverColor: 'red',
				/* 悬浮 */
				bulge: false,
				list: [
					// {
					// text:'',
					// iconPath:'',
					// is_show:true,
					// selectedIconPath:'',
					// link:{
					// wap_url:''
					// }
					// }
				]
			},
			is_linkset: false,
			index: 0,
		};
	},
	components: {
		Upload,
		Setlink,
		draggable
	},
	created() {

		this.getData();

	},
	methods: {

		/*获取数据*/
		getData() {
			let self = this;
			PageApi.getTabbar({}, true).then(res => {
				// let data = JSON.parse(res.data.data.data);
				// console.log(data)
				self.formData = res.data.vars.data;
			}).catch(error => {
				self.loading = false;
			});
		},

		/*提交*/
		Submit() {
			let self = this;
			self.loading = true;
			let Params = self.formData;
			if (Params.list.length <= 0 || !Params.list) {
				Params = {};
			}
			PageApi.editTabbar({
				data: Params
			}, true)
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
		},
		/*选择链接*/
		changeLink(index) {
			this.is_linkset = true;
			this.index = index;
		},

		/*获取链接并关闭弹窗*/
		closeLinkset(e) {
			this.is_linkset = false;
			this.formData.list[this.index].link_url = '/' + e.url;
			this.formData.list[this.index].text = e.name;
		},
		onEditorDeleleData: function(index) {
			let self = this;
			if (self.formData.list.length <= 1) {
				ElMessage({
					message: '至少保留一个',
					type: 'error'
				});
				return false;
			}
			self.formData.list.splice(index, 1);
		},
		onEditorSelectImage: function(index, imgUrl) {
			this.isupload = true;
			this.imgModel = {
				index: index,
				imgUrl: imgUrl
			};
		},
		/*上传图片*/
		returnImgsFunc(e) {
			if (e != null) {
				this.imgModel.index[this.imgModel.imgUrl] = e[0]['file_path'];
			}
			this.isupload = false;
		},
		onEditorResetColor: function(holder, attribute, color) {
			holder[attribute] = color;
		},
		onEditorAddData: function() {
			let self = this;
			// 新增data数据
			if (self.formData.list) {
				if (self.formData.list.length >= 5) {
					ElMessage({
						message: '最多添加5个',
						type: 'error'
					});
					return false;
				} else {
					let newDataItem = {
						iconPath: '',
						text: '',
						is_show: true,
						selectedIconPath: '',
						link: {
							wap_url: ''
						}
					};
					self.formData.list.push(newDataItem);
				}
			} else {
				let newDataItem = {
					iconPath: '',
					text: '',
					is_show: true,
					selectedIconPath: '',
					link: {
						wap_url: ''
					}
				};
				let list = [{
					...newDataItem
				}];
				self.formData = {
					type: 0,
					backgroundColor: '',
					textColor: '',
					textHoverColor: '',
					bulge: false,
					list: []
				};
				self.formData.list = list;
			}

		},
		mouseenter(e) {
			e.target.childNodes[2].style.color = this.formData.textHoverColor;
		},
		mouseleave(e) {
			e.target.childNodes[2].style.color = this.formData.textColor;
		}
	}
};
</script>

<style scoped="scoped">
	.diy-tabbar {
		margin: 0;
		padding: 0;
		background: none;
	}

	.model-container {
		width: 375px;
		height: calc(100vh - 150px);
		margin: 0 auto;
		background-color: #fff;
	}

	.mr30 {
		margin-right: 30px;
	}

	.model-container img {
		width: 100%;
	}

	.model-container .img-box {
		box-shadow: 0 0 16px 0 rgba(0, 0, 0, .1);
	}

	.param-container {
		width: 476px;
		max-height: calc(100vh - 98px);
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

	.form-subtitle {
		padding-top: 18px;
		padding-bottom: 18px;
		padding-left: 20px;
	}

	.form-chink {
		width: 438px;
		height: 6px;
		background: #EEEEEE;
		border-radius: 3px;
		margin: 0 auto;
		margin-top: 10px;
	}

	.icon {
		position: relative;
	}

	.icon img {
		width: 88px;
		height: 88px;
	}

	.icon .icon-text {
		width: 88px;
		height: 28px;
		text-align: center;
		color: #fff;
		font-size: 16px;
		line-height: 28px;
		position: absolute;
		z-index: 1;
		bottom: 0;
		left: 0;
		background-color: rgba(0, 0, 0, .45);
	}

	.nav_img {
		width: 30px !important;
		height: 30px;
	}

	.delete-box {
		z-index: 99;
		display: flex;
		justify-content: flex-end;
	}

	.param-img-item {
		position: relative;
		border: 1px solid #eee;
		margin-left: 20px;
		margin-top: 23px;
		padding: 0 22px 0 6px;
		border-radius: 10px;
		width: 100%;
		box-sizing: border-box;
	}
	.param-img-item .right{
		padding: 6px 0 26px 0;
		flex: 1;
	}
	.param-img-item .el-icon-DeleteFilled {
		font-size: 26px;
		position: absolute;
		right: -6px;
		top: -6px;
		background-color: #666;
		color: #fff;
		border-radius: 50%;
		padding: 4px;
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
	}

	.form-label {
		width: 122px;
		text-align: right;
	}

	.el-color-picker--small .el-color-picker__trigger {
		width: 36px;
		height: 36px;
	}

	.f12 {
		font-size: 12px;
	}
	.draggable-list{
		padding-bottom: 20px;
		padding-right: 18px;
	}
</style>