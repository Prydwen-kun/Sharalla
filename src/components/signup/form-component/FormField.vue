<script setup>
const props = defineProps({
  type: String,
  name: String,
})
</script>
<template>
  <label :for="name">{{ name }}</label>
  <input :type="type" :id="name">
</template>
<style scoped>
input{
  border-radius: 1rem;
  max-width: 10rem;
}
</style>
