<template>

  <div>

    <div class="common-form">活动时间</div>

    <el-form-item label="活动时间" :rules="[{ required: true, message: ' ' }]" prop="start_date">
      <el-date-picker
        v-model="form.start_date"
        type="date"
        value-format="YYYY-MM-DD"
        placeholder="选择开始日期">
      </el-date-picker>
      <span>-</span>
      <el-date-picker
        v-model="form.end_date"
        type="date"
        value-format="YYYY-MM-DD"
        placeholder="选择结束日期">
      </el-date-picker>
    </el-form-item>

    <el-form-item label="每天活动时间"
    prop="active_time"
    :rules="[{ required: true, message: '请选择每天活动时间', trigger: 'change' }]">
    <div>
      <el-time-picker
          is-range
          v-model="form.active_time"
          range-separator="至"
          start-placeholder="开始时间"
          end-placeholder="结束时间"
          placeholder="选择时间范围"
          format="HH:mm"
          value-format="HH:mm"
          >
        </el-time-picker>
    </div>
      
    </el-form-item>
  </div>
</template>

<script>
export default {
  data(){
    return{
      /*时间*/
      timeVal: new Date(2016, 9, 10, 8, 0, 0),
      /*如果没有后面的-8.64e7就是不可以选择今天的*/
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() < Date.now() - 8.64e7;
        }
      }
    }
  },
  props: {
    form: Object
  }
};
</script>

<style lang="scss" scoped></style>
