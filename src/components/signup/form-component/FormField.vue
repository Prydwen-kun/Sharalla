<script setup>
const props = defineProps({
  type: String,
  name: String,
  label: String
})
</script>
<template>
  <label :for="name" class="field_label">{{ label }}</label>
  <input :type="type" :id="name" :name="name">
</template>
<style scoped>
.field_label {
  color: var(--light-rose);
  text-shadow: 0px 0px 3px var(--dark-rose);
}

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
