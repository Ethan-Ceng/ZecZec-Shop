<template>

  <div class="marketing-box">
    <el-tabs v-model="activeTab">
      <el-tab-pane label="签到" name="signin"></el-tab-pane>
      <el-tab-pane label="积分商城" name="points"></el-tab-pane>
      <el-tab-pane label="预售" name="presale"></el-tab-pane>
      <el-tab-pane label="秒杀" name="seckill"></el-tab-pane>
      <el-tab-pane label="预告" name="preview"></el-tab-pane>
      <el-tab-pane label="拼团" name="assemble"></el-tab-pane>
      <el-tab-pane label="砍价" name="bargain"></el-tab-pane>
      <el-tab-pane label="礼包购" :disabled="packageList.length<=0" name="package"></el-tab-pane>
      <el-tab-pane label="邀请有礼" :disabled="invitationList.length<=0" name="invitation"></el-tab-pane>
      <el-tab-pane label="优惠券" name="coupon"></el-tab-pane>
      <el-tab-pane label="幸运转盘" name="lottery"></el-tab-pane>
      <el-tab-pane label="万能表单" :disabled="tableList.length<=0" name="table"></el-tab-pane>
      <el-tab-pane label="任务中心" name="task"></el-tab-pane>
    </el-tabs>
    <el-select v-model="activePage" placeholder="请选择" class="percent-w100" @change="changeFunc" value-key="id">
      <el-option v-for="(item, index) in pages" :key="index" :label="item.name" :value="item"></el-option>
    </el-select>
  </div>
</template>

<script>
  import LinkApi from '@/api/link.js';
  export default {
    data() {
      return {
        /*tab切换选择中值*/
        activeTab: 'signin',
        /*页面数据*/
        pages: [],
        /*选中的值*/
        activePage: null,
        /*秒杀数据*/
        presaleList: [{
          id: 0,
          url: 'pages/plus/presale/list',
          name: '预售',
          type: '营销'
        }],
        seckillList: [{
          id: 0,
          url: 'pages/plus/seckill/list/list',
          name: '秒杀',
          type: '营销'
        }],
        /*签到数据*/
        signinList: [{
          id: 0,
          url: 'pages/plus/signin/signin',
          name: '签到',
          type: '营销'
        }],
        /*积分商城数据*/
        pointsList: [{
          id: 0,
          url: 'pages/plus/points/list/list',
          name: '积分商城',
          type: '营销'
        }],
        /*拼团*/
        assembleList: [{
          id: 0,
          url: 'pages/plus/assemble/list/list',
          name: '拼团',
          type: '营销'
        }],
        /*砍价*/
        bargainList: [{
          id: 0,
          url: 'pages/plus/bargain/list/list',
          name: '砍价',
          type: '营销'
        }],
        // 礼包购活动
        packageList: [],
        // 邀请有礼活动
        invitationList: [],
        /*优惠券*/
        couponList: [{
          id: 0,
          url: 'pages/coupon/coupon',
          name: '优惠券',
          type: '营销'
        }],
        /*幸运大转盘*/
        lotteryList: [{
          id: 0,
          url: 'pages/plus/lottery/lottery',
          name: '幸运转盘',
          type: '营销'
        }],
        // 万能表单
        tableList: [],
        // 任务
        taskList: [{
          id: 0,
          url: 'pages/task/index',
          name: '任务中心',
          type: '营销'
        }],
        previewList:[{
          id: 0,
          url: 'pages/plus/preview/list',
          name: '预告',
          type: '营销'
        }]
      };
    },
    watch: {
      activeTab: function(n, o) {
        let self = this;
        self.pages = [];
        if (n != o) {
          if (n == 'signin') {
            self.pages = self.signinList;
          } else if (n == 'points') {
            self.pages = self.pointsList;
          } else if (n == 'presale') {
            self.pages = self.presaleList;
          }  else if (n == 'seckill') {
            self.pages = self.seckillList;
          } else if (n == 'assemble') {
            self.pages = self.assembleList;
          } else if (n == 'bargain') {
            self.pages = self.bargainList;
          } else if (n == 'package') {
            self.pages = self.packageList;
          } else if (n == 'invitation') {
            self.pages = self.invitationList;
          } else if (n == 'coupon') {
            self.pages = self.couponList;
          } else if (n == 'lottery') {
            self.pages = self.lotteryList;
          } else if (n == 'table') {
            self.pages = self.tableList;
          } else if (n == 'task') {
            self.pages = self.taskList;
          } else if (n == 'preview') {
            self.pages = self.previewList;
          }
          self.autoSend();
        }
      }
    },
    created() {
      this.pages = this.signinList;
      this.autoSend();
      this.getData();
    },
    methods: {
      /*获取数据*/
      getData() {
        let self = this;
        LinkApi.getList({},true)
          .then(res => {
            self.packageList = res.data.packageList;
            self.invitationList = res.data.invitationList;
            self.tableList = res.data.tableList;
          })
          .catch(error => {});
      },
      /*自动发送*/
      autoSend() {
        if (this.pages.length > 0) {
          this.activePage = this.pages[0];
          this.changeFunc();
        }
      },
      /*选中的值*/
      changeFunc(e) {
        this.$emit('changeData', this.activePage);
      },
    }
  };
</script>

<style></style>
