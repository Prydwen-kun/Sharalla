<script setup>
import { RouterView } from 'vue-router';
import NavBar from './components/navigation/NavBar.vue';
import TagBar from './components/navigation/TagBar.vue';
import axios from 'axios';

//check if user is connected and display navbar + redirect to dashboard

//important for setting cookies 
axios.defaults.withCredentials = true
</script>

<template>
  <header>
    <NavBar />
    <TagBar />
  </header>

  <main class="content-view">
    <RouterView />
  </main>
</template>

<style scoped>
header {
  line-height: 1.5;
  background-color: var(--dark-blue-black);
  grid-column: 1/13;
  grid-row: span 1;
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-template-rows: repeat(4, 25%);
}

.content-view {
  grid-column: span 12;
  grid-row: auto;
  display: grid;
  grid-template-columns: repeat(12, 1fr);
}
</style>
