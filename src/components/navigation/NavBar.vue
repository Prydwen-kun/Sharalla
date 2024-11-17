<script setup>
import { onMounted } from 'vue';
import config from '../../../config/config';
import NavItem from './navbarItem/NavItem.vue'
import SearchBar from './navbarItem/SearchBar.vue';
import UserIcon from './navbarItem/UserIcon.vue';
import axios from 'axios';

onMounted(async () => {
  const response = await axios.get(
    `${config.APIbaseUrl}${config.endpoints.getConnectedUserData}`
  )
  const data = response.data
  const usernameGreet = document.getElementById('userGreet')
  if (data.response !== 'error' || data.response !== 'forbidden') {
    usernameGreet.innerHTML = data.response
  }
})
</script>

<template>
  <nav class="navbar">
    <ul class="navigation">
      <NavItem :link-title="'Home'" :link="'/'" :grid-span="'1/3'" />
      <SearchBar />
      <p class="usernameGreet" id="userGreet">TEST</p>
      <UserIcon :link-title="'User Account'" :link="'/'" :grid-span="'11/12'" />
    </ul>
  </nav>
</template>
<style scoped>
nav {
  grid-column: 1/13;
  grid-row: span 2;
  background-color: var(--light-blue-black);
}

.navbar {
  min-width: 100%;
  display: grid;
  grid-template-columns: repeat(12, 1fr);
}

.navigation {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-column: 1/13;
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.usernameGreet {
  grid-column: 10/11;
  display: flex;
  justify-content: center;
  align-items: center;
  color: var(--rose);
}
</style>
