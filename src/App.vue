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
    <NavBar :key="navbar_key" />
    <TagBar />
  </header>

  <main class="content-view">
    <Suspense>
      <RouterView />
    </Suspense>
  </main>
</template>

<script>
export default {
  components: {
    NavBar, TagBar
  },
  data() {
    return {
      navbar_key: 0
    }
  },
  methods: {
    reloadComponent() {
      this.navbar_key += 1
    }
  },
  watch: {
    '$route'(to, from) {
      // This will be triggered when the route changes
      this.reloadComponent()
    }
  }
};
</script>

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
