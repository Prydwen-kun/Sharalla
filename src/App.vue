<script setup>
import { RouterView } from 'vue-router';

import NavBar from './components/navigation/NavBar.vue'

//check if user is connected and display navbar + redirect to dashboard
</script>

<template>
  <header>
    <NavBar />
    <div class="headerContainer">
      <ul class="tags">
        <li><a href="">tag</a></li>
        <li><a href="">tag</a></li>
        <li><a href="">tag</a></li>
        <li><a href="">tag</a></li>
      </ul>
    </div>

  </header>

  <main class="content-view">
    <RouterView />
  </main>
</template>

<style scoped>
.headerContainer {
  color: var(--light-rose);
  grid-column: 1/13;
  grid-row: span 1;
}

.tags {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
}

.tags a {
  border-radius: 3px;
}

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
}
</style>
