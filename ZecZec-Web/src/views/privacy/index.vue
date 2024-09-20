<script setup lang="ts">
import {onMounted, ref} from "vue";
import {getPolicy} from "@/api/common";


const content = ref(null)
const fetchPolicy = async () => {
  try {
    const {data, code} = await getPolicy();
    console.log(data)
    if (code === 1 && data.privacy) {
      content.value = data.privacy
    }
  } catch (error) {
    console.error('Error fetching:', error);
    throw error;
  }
}


onMounted(async () => {
  await fetchPolicy();
});
</script>

<template>
  <div class="container px-4 lg:px-0">
    <div class="text-justify mt-8 mb-16 nested-media">
      <div v-html="content"></div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.container {
  max-width: 1140px;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
</style>
