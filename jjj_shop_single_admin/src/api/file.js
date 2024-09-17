import request from '@/utils/request';

let FileApi = {
	/**
	 * @description 文件类别列表
	 * @param
	 * @returns
	 */
	SystemPictureList(data, errorback) {
		return request._post('/admin/file.image/list', data, errorback);
	},
	/**
	 * @description 系统列表
	 * @param
	 * @returns
	 */
	PictureIndex(data, errorback) {
		return request._post('/admin/file.image/index', data, errorback);
	},
	/**
	 * @description 删除多文件
	 * @param
	 * @returns
	 */
	deleteFiles(data, errorback) {
		return request._post('/admin/file.image/deleteFiles', data, errorback);
	},
	/**
	 * @description 添加文件分类
	 * @param
	 * @returns
	 */
	addCategory(data, errorback) {
		return request._post('/admin/file.image/addCategory', data, errorback);
	},
	/**
	 * @description 修改文件分类
	 * @param
	 * @returns
	 */
	editCategory(data, errorback) {
		return request._post('/admin/file.image/edit', data, errorback);
	},
	/**
	 * @description 删除文件分类
	 * @param
	 * @returns
	 */
	deleteCategory(data, errorback) {
		return request._post('/admin/file.image/delete', data, errorback);
	},
	/**
	 * @description 上传
	 * @param
	 * @returns
	 */
	uploadFile(formData, errorback) {
		return request._upload('/admin/file.upload/image', formData, errorback);
	},
	
};

export default FileApi;
