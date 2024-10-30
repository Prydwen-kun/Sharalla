<script setup>
import NavItem from './navbarItem/NavItem.vue'
</script>

<template>
  <nav>
    <ul>
      <NavItem :link-title="'Home'" :link="'/'" />
      <NavItem :link-title="'Home'" :link="'/'" />
      <NavItem :link-title="'Sign Out'" :link="'/'" />
    </ul>
  </nav>
</template>
<style scoped></style>
