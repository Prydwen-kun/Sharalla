<script setup>
import { onMounted, ref } from 'vue';
import config from '../../../config/config';
import NavItem from './navbarItem/NavItem.vue'
import SearchBar from './navbarItem/SearchBar.vue';
import BurgerMenu from './navbarItem/BurgerMenu.vue';
import UserIcon from './navbarItem/UserIcon.vue';
import axios from 'axios';

let user = ref({})
let usernameGreet = ref('')

async function getConnectedUserData() {
  const response = await axios.post(
    `${config.APIbaseUrl}${config.endpoints.getConnectedUserData}`
  )
  const data = await response.data
  user.value = data.response

  if (data.response !== 'req_error' && data.response !== 'forbidden' && data.response !== 'no_cookie') {
    usernameGreet.value = data.response.username
  } else {
    usernameGreet.value = 'Not connected'
  }
}

getConnectedUserData()
</script>

<template>
  <nav class="navbar">
    <ul class="navigation">
      <BurgerMenu :grid-span="'1/2'" />
      <NavItem :link-title="'Home'" :link="'/'" :grid-span="'2/3'" />
      <SearchBar />
      <p class="usernameGreet" id="userGreet">{{ usernameGreet }}</p>
      <UserIcon :link-title="'User Account'" :link="'/profile'" :grid-span="'11/12'"
        :icon="config.AvatarBaseUrl + user.avatar" />
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

@media (max-width: 805px) {
  .usernameGreet {
    display: none;
  }

  .navItem {
    display: none;
  }

  .searchField {
    grid-column: 6/12;
  }

}
</style>
