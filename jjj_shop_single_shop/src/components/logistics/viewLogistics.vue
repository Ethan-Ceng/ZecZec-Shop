<template>
	<el-dialog v-model="dialogVisible" title="查看物流" width="900px" @close="dialogFormVisible">
		<!-- <span>This is a message</span> -->
		<el-timeline>
			<el-timeline-item v-for="(activity, index) in activities" :key="index" :color="index==0?'#2ECC40':''"
				:timestamp="activity.time">
				{{activity.context}}
			</el-timeline-item>
		</el-timeline>
	</el-dialog>
</template>

<script>
export default {
	data() {
		return {
			/*是否显示*/
			dialogVisible: false,
			/*结果类别*/
			type: 'error',
			/*传出去的参数*/
			params: null,
			reverse: false,
			order_id: 0,
			activities: []
		};
	},
	created() {
		this.dialogVisible = true;
		this.activities = this.logisticsData;
		this.type = 'error';
	},
	props: ['isLogistics', 'logisticsData'],
	methods: {
		/*关闭弹窗*/
		dialogFormVisible() {
			this.$emit('closeDialog', {
				type: this.type,
				openDialog: false,
				params: this.params
			});
		},
	}
};
</script>

<style>
	.el-step__main .el-step__description {
		padding-bottom: 10px;
	}
</style>