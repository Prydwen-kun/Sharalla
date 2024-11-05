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
input {
  border-top: none;
  border-left: none;
  border-right: none;
  border-bottom: 1px solid var(--dark-blue-black);
  background-color: var(--light-blue-black);
  color: var(--rose);
  max-width: 10rem;
  outline: none;
  padding: 0.5rem;
  margin-bottom: 3rem;
}

input:focus {
  background-color: var(--dark-blue-black);
}
</style>
