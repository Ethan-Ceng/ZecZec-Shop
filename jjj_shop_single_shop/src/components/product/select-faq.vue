<template>
    <el-select
      v-model="innerValue"
      :loading="loading"
      multiple
      filterable
      remote
      reserve-keyword
      :placeholder="placeholder"
      remote-show-suffix
      :remote-method="remoteMethod"
      @change="handleChange"
    >
      <el-option
        v-for="item in faqList"
        :key="item.value"
        :label="item.question"
        :value="item.faq_id"
      />
    </el-select>
</template>

<script>
import PorductApi from '@/api/product.js';

export default {
  name: 'SelectFaq',
  components: {},
  props: {
    value: {
      type: [String, Number],
      default: null
    },
    placeholder: {
      type: String,
      default: '请选择'
    },
  },
  data() {
    return {
      loading: false,
      faqList: [],
      innerValue: this.value
    };
  },
  created() {
    this.remoteMethod()
  },
  watch: {
    // 选中更新
    value (val) {
      console.log('modelValue', val)
      this.innerValue = val
    },
  },
  methods: {
    // 处理值的变化并同步到外部
    handleChange(value) {
      this.$emit('change', value);
      this.$emit('input', value);
    },
    async remoteMethod(queryString) {
      console.log('remoteMethod', queryString)
      let {code, data} = await PorductApi.getFaqList({question: queryString})
      if (code === 1) {
        this.faqList = data.list.data || []
      }
    }
  },
}

</script>

<style lang='scss' scoped>
</style>
