<template>

  <div>
    <div class="common-form">活动时间</div>

    <el-form-item label="活动时间" :rules="[{ required: true, message: ' ' }]" prop="start_time">
      <el-date-picker
        v-model="form.start_time"
        type="datetime"
        value-format="YYYY-MM-DD HH:mm:ss"
        placeholder="选择开始时间">
      </el-date-picker>
      <span>-</span>
      <el-date-picker
        v-model="form.end_time"
        type="datetime"
        value-format="YYYY-MM-DD HH:mm:ss"
        placeholder="选择结束时间">
      </el-date-picker>
    </el-form-item>

    <el-form-item label="凑团时间" :rules="[{ required: true, message: ' ' }]" prop="together_time">
      <div class="max-w460 d-s-c">
        <div class="flex-1">
          <el-input v-model="form.together_time" type="number" min="0"></el-input>
        </div>
        <span class="pl16">小时</span>
      </div>
    </el-form-item>
  </div>
</template>

<script>
export default {
  data() {
    return {
      /*如果没有后面的-8.64e7就是不可以选择今天的*/
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() < Date.now() - 8.64e7;
        }
      }
    };
  },
  props: {
    form: Object
  }
};
</script>

<style></style>
