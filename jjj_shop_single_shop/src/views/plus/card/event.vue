<template>
  <div class="common-seach-wrap">
    <!--基础设置-->
    <Card v-if="activeName=='card'"></Card>
    <!--分享设置-->
    <CategoryIndex v-if="activeName=='category'"></CategoryIndex>
    <Code v-if="activeName=='code'"></Code>
    <Order v-if="activeName=='order'"></Order>
    <SettingIndex v-if="activeName=='setting'"></SettingIndex>
  </div>
</template>
<script>
  import { reactive, toRefs, defineComponent } from 'vue';
  import { useUserStore } from "@/store";
  import Card from './card/index.vue';
  import CategoryIndex from './category/index.vue';
  import Code from './code/index.vue';
  import Order from './order/index.vue';
  import SettingIndex from './setting/index.vue';

  export default defineComponent({
    components: {
      Card,
      CategoryIndex,
      Code,
      Order,
      SettingIndex
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
              key: 'card',
              value: '卡券管理',
              path:'/plus/card/card/index'
          },
          {
              key: 'category',
              value: '分类管理',
              path:'/plus/card/category/index'
          },
          {
              key: 'code',
              value: '提货码管理',
              path:'/plus/card/code/index'
          },
          {
              key: 'order',
              value: '提货订单',
              path:'/plus/card/order/index'
          },
          {
              key: 'setting',
              value: '卡券设置',
              path:'/plus/card/setting/index'
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
          tab_type: 'card',
      }
      this.bus_emit('tabData', params);

    },
    beforeUnmount () {
        //发送类别切换
        this.bus_emit('tabData', {active: null,tab_type:'card', list: []});
        this.bus_off('activeValue'); 
    },
    methods: {

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
