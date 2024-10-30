<script setup>
//create param for item title and route
const props = defineProps({
  linkTitle: String,
  link: String
})
</script>

<template>
  <li>
    <router-link :to="link">{{ linkTitle }}</router-link>
  </li>
</template>
<style scoped></style>
