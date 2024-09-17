<template>
  <svg aria-hidden="true" class="svg-icon" :width="props.size" :height="props.size">
    <use :xlink:href="symbolId" :fill="props.color" :class="fillClass"/>
  </svg>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  name: {
    type: String,
    required: true
  },
  color: {
    type: String,
    default: '#333'
  },
  size: {
    type: String,
    default: '1em'
  },
  fillClass: {
    type: String
  }
})

const symbolId = computed(() => `#icon-${props.name}`)
</script>
