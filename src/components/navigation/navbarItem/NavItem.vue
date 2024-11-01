<script setup>
//create param for item title and route
const props = defineProps({
  linkTitle: String,
  link: String
})
</script>

<template>
  <li class="navItem">
    <router-link :to="link">{{ linkTitle }}</router-link>
  </li>
</template>
<style scoped>
.navItem{
  grid-column: auto;
}
</style>
