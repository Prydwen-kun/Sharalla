<script setup>
import NavItem from './navbarItem/NavItem.vue'
</script>

<template>
  <nav>
    <ul class="navigation">
      <NavItem :link-title="'Home'" :link="'/'" />
      <label for="search">Search</label>
      <input type="text" id="search" name="search">
      <NavItem :link-title="'Profile'" :link="'/'" />
      <NavItem :link-title="'Sign Out'" :link="'/'" />
    </ul>
  </nav>
</template>
<style scoped>
nav {
  grid-column: 1/13;
  grid-row: span 2;
  background-color: var(--light-blue-black);
}

.navigation {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  min-width: 100%;
}
</style>
