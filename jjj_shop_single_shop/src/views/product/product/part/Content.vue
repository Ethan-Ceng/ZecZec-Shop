<template>
	<div class="basic-setting-content pl16 pr16">
		<!--商品详情-->
		<div class="common-form">商品详情</div>
		<el-form-item label="详情类型：">
			<el-radio-group v-model="form.model.is_picture">
				<el-radio :label="0">图文</el-radio>
				<el-radio :label="1">纯图</el-radio>
			</el-radio-group>
			<div class="gray9 f12">尺寸无限制</div>
		</el-form-item>
		<el-form-item label="详情内容：" v-show="form.model.is_picture == 0">
			<div class="edit_container">
				<Uediter :text="form.model.content" :config="ueditor.config" ref="ue"
					@contentChange="contentChangeFunc"></Uediter>
			</div>
		</el-form-item>
		<template v-if="form.model.is_picture == 1">
			<el-form-item label="详情图片：">
				<div class="draggable-list">
					<template v-if="form.model && form.model.contentImage && form.model.contentImage.length>0">
						<draggable v-model="form.model.contentImage" group="people" item-key="id"
							class="draggable-list">
							<template #item="{element}">
								<div class="item">
									<img v-img-url="element.file_path" />
									<a href="javascript:void(0);" class="delete-btn" @click.stop="deleteImg(element)">
										<el-icon>
											<Close />
										</el-icon>
									</a>
								</div>
							</template>
						</draggable>
					</template>
					<div class="item img-select" @click="openProductUpload('image', 'image')"><el-icon>
							<Plus />
						</el-icon></div>
				</div>
			</el-form-item>
		</template>

		<!--商品图片组件-->
		<Upload v-if="is_upload" :config="config" :isupload="is_upload" @returnImgs="returnImgsFunc">上传图片</Upload>
	</div>
</template>

<script>
import Uediter from '@/components/UE.vue';
import Upload from '@/components/file/Upload.vue';
import draggable from 'vuedraggable';
export default {
	components: {
		/*编辑器*/
		Uediter,
		Upload,
		draggable
	},
	data() {
		return {
			/*富文本框配置*/
			ueditor: {
				text: '',
				config: {
					initialFrameWidth: 400,
					initialFrameHeight: 500
				}
			},
			is_upload: false,
			config: {
				total: 9,
				file_type: 'image',
			},
			file_name: 'image'
		};
	},
	created() {
		//this.ueditor.text = this.form.model.content;
	},
	inject: ['form'],
	methods: {

		/*获取富文本框内容*/
		getContent: function() {
			//return this.$refs.ue.getUEContent();
		},

		/*获取富文本内容*/
		contentChangeFunc(e) {
			this.form.model.content = e;
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
			this.is_upload = true;
		},
		/*上传商品图片*/
		returnImgsFunc(e) {
			if (e != null) {
				if (this.file_name == 'video') {
					this.form.model.content_video_id = e[0].file_id;
					this.form.model.contentVideo = e[0];
				} else if (this.file_name == 'image') {
					let imgs = this.form.model.contentImage.concat(e);
					this.form.model.contentImage = imgs;
				} else if (this.file_name == 'poster') {
					this.form.model.content_poster_id = e[0].file_id;
					this.form.model.contentPoster = e[0];
				}
			}
			this.is_upload = false;
		},
		/*删除商品图片*/
		deleteImg(e) {
			let n = -1;
			this.form.model.contentImage.forEach((item, index) => {
				if (item.file_path == e.file_path) {
					n = index;
					return;
				}
			});
			if (n >= 0) {
				this.form.model.contentImage.splice(n, 1);
			}
		},
		delVideo() {
			this.form.model.content_video_id = 0;
			this.form.model.contentVideo = {};
		},
		deleteVideo() {
			this.form.model.content_poster_id = 0;
			this.form.model.contentPoster = {};
		},

	}
};
</script>

<style></style>