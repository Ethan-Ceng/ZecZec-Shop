<template>
  <div class="common-seach-wrap">
    <!--入驻申请-->
    <Apply v-if="activeName == 'apply'"></Apply>
    <!--分销商用户-->
    <User v-if="activeName == 'user'" @chang-active="changActive"></User>
    <!--分销商等级-->
    <Grade v-if="activeName == 'grade'"></Grade>
    <!--分销商品-->
    <ProductIndex v-if="activeName == 'product'"></ProductIndex>
    <!--分销订单-->
    <Order v-if="activeName == 'order'"></Order>
    <!--提现申请-->
    <Cash v-if="activeName == 'cash'"></Cash>
    <!--分销设置-->
    <Setting v-if="activeName == 'setting'"></Setting>
    <!--分销海报-->
    <Poster v-if="activeName == 'poster'"></Poster>
  </div>
</template>
<script>
  import { reactive, toRefs, defineComponent } from 'vue';
  import { useUserStore } from "@/store";
  import Apply from './apply/Apply.vue';
  import User from './user/User.vue';
  import Grade from './grade/index.vue';
  import ProductIndex from './product/index.vue';
  import Order from './order/Order.vue';
  import Cash from './cash/Cash.vue';
  import Setting from './setting/Setting.vue';
  import Poster from './poster/Poster.vue';

  export default defineComponent({
    components: {
      Apply,
      User,
      Grade,
      ProductIndex,
      Order,
      Cash,
      Setting,
      Poster,
    },
    setup(){
      const { bus_emit, bus_off, bus_on } = useUserStore();
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
        activeName: '',
        /*切换数组原始数据*/
        sourceList: [
          {
            key: 'apply',
            value: '入驻申请',
            path: '/plus/agent/apply/index'
          },
          {
            key: 'user',
            value: '分销商用户',
            path: '/plus/agent/user/index'
          },
          {
            key: 'grade',
            value: '分销商等级',
            path: '/plus/agent/grade/index'
          },
          {
            key: 'product',
            value: '分销商品',
            path: '/plus/agent/product/index'
          },
          {
            key: 'order',
            value: '分销订单',
            path: '/plus/agent/order/index'
          },
          {
            key: 'cash',
            value: '提现申请',
            path: '/plus/agent/cash/index'
          },
          {
            key: 'setting',
            value: '分销设置',
            path: '/plus/agent/setting/index'
          },
          {
            key: 'poster',
            value: '分销海报',
            path: '/plus/agent/setting/qrcode'
          }
        ],
        /*权限筛选后的数据*/
        tabList:[],
        paramsFlag: false
      })
      return {
			  ...toRefs(state),
      };
    },
    created() {
      this.tabList = this.authFilter();
      if(this.tabList.length>0){
        this.activeName=this.tabList[0].key;
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
          tab_type: 'agent',
      }
      this.bus_emit('tabData', params);

    },
    beforeUnmount () {
        //发送类别切换
        this.bus_emit('tabData', {active: null,tab_type:'agent', list: []});
        this.bus_off('activeValue');
    },
    methods: {
      changActive(name){
        this.activeName = name;
        this.bus_emit('activeValue', name);
      },
      /*权限过滤*/
      authFilter(){
        let list=[];
        for(let i=0;i<this.sourceList.length;i++){
          let item=this.sourceList[i];
          if(this.$filter.isAuth(item.path)){
            list.push(item);
          }
        }
        return list;
      },

    }
  });
</script>

<style></style>
