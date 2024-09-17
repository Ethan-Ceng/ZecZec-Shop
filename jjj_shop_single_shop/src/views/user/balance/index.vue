<template>

	<div class="common-seach-wrap">
		<!--余额明细-->
		<Log v-if="activeName == 'log'"></Log>
		<!--余额设置-->
		<Setting v-if="activeName == 'settings'"></Setting>
		<!--充值套餐-->
		<Plan v-if="activeName == 'plan'"></Plan>
		<!--充值记录-->
		<Record v-if="activeName == 'record'"></Record>
		<!--提现设置-->
		<CashSetting v-if="activeName == 'cashsetting'"></CashSetting>
		<!--提现记录-->
		<Cash v-if="activeName == 'cash'"></Cash>
	</div>
</template>
<script>
	import Setting from './part/Setting.vue';
	import Log from './part/Log.vue';
	import Plan from './part/Plan.vue';
	import Record from './part/Record.vue';
	import CashSetting from './part/CashSetting.vue';
	import Cash from './part/Cash.vue';
	import {
		reactive,
		toRefs,
		defineComponent
	} from 'vue';
	import {
		useUserStore
	} from "@/store";
	export default defineComponent({
		components: {
			Setting,
			Log,
			Plan,
			Record,
			CashSetting,
			Cash
		},
		setup() {
			const {
				bus_emit,
				bus_off,
				bus_on
			} = useUserStore();
			const state = reactive({
				bus_emit,
				bus_off,
				bus_on,
				/*是否加载完成*/
				loading: true,
				form: {},
				/*参数*/
				param: {},
				/*当前选中*/
				activeName: 'log',
				/*切换数组原始数据*/
				sourceList: [{
						key: 'log',
						value: '余额明细',
						path: '/user/balance/log'
					},
					{
						key: 'settings',
						value: '充值设置',
						path: '/user/balance/setting'
					},
					{
						key: 'plan',
						value: '充值套餐',
						path: '/user/plan/index'
					},
					{
						key: 'record',
						value: '充值记录',
						path: '/user/plan/log'
					},
					{
						key: 'cashsetting',
						value: '提现设置',
						path: '/user/cash/setting'
					},
					{
						key: 'cash',
						value: '提现记录',
						path: '/user/cash/index'
					},
				],
				/*权限筛选后的数据*/
				tabList: [],
			})
			return {
				...toRefs(state),
			};
		},
		created() {
			this.tabList = this.authFilter();
			if (this.tabList.length > 0) {
				this.activeName = this.tabList[0].key;
			}
			if (this.$route.query.type != null) {
				this.activeName = this.$route.query.type;
			}
			/*监听传插件的值*/

			this.bus_on('activeValue', res =>

				{
					this.activeName = res;
				});
			//发送类别切换
			let params = {
				active: this.activeName,
				list: this.tabList,
				tab_type: 'balanceDetail',

			}
			this.bus_emit('tabData', params);

		},
		beforeUnmount() {
			//发送类别切换
			this.bus_emit('tabData', {
				active: null,
				tab_type: 'balanceDetail',
				list: []
			});
			this.bus_off('activeValue');
		},
		methods: {
			/*权限过滤*/
			authFilter() {
				let list = [];
				for (let i = 0; i < this.sourceList.length; i++) {
					let item = this.sourceList[i];
					if (this.$filter.isAuth(item.path)) {
						list.push(item);
					}
				}
				return list;
			},
		}
	});
</script>

<style>

</style>