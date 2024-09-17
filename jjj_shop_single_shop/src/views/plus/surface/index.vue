<template>

	<div class="user">
		<div class="common-seach-wrap">
			<el-tabs v-model="activeName">
				<el-tab-pane label="面单模板" name="template">
					<TemplateIndex v-if="activeName=='template'"></TemplateIndex>
				</el-tab-pane>
				<el-tab-pane label="面单配置" name="setting">
					<SettingIndex v-if="activeName=='setting'"></SettingIndex>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</template>

<script>
	import TemplateIndex from './template/index.vue';
	import SettingIndex from './setting/index.vue';
	export default {
		components: {
			TemplateIndex,
			SettingIndex,
		},
		data() {
			return {
				activeName: 'template',
				/*是否加载完成*/
				loading: true,
			}
		},
		created() {


		},
		methods: {}
	}
</script>