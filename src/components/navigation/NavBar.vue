<script setup>
import { onMounted, ref } from 'vue';
import config from '../../../config/config';
import NavItem from './navbarItem/NavItem.vue'
import BurgerMenu from './navbarItem/BurgerMenu.vue';
import UserIcon from './navbarItem/UserIcon.vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

let user = ref({})
let usernameGreet = ref('')

const router = useRouter()

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

function routeToUpload() {
  router.push('/upload')
}

function routeToMyFiles() {
  router.push('/myFiles')
}
</script>

<template>
  <nav class="navbar">
    <ul class="navigation">
      <BurgerMenu :grid-span="'1/2'" />
      <NavItem :link-title="'Home'" :link="'/'" :grid-span="'2/3'" />
      <div class="myFiles_container">
        <button class="myFiles_button" @click="routeToMyFiles">My Files</button>
      </div>
      <div class="upload_button_container">
        <button class="upload_button" @click="routeToUpload">Upload</button>
      </div>
      <p class="usernameGreet" id="userGreet">{{ usernameGreet }}</p>
      <UserIcon :link-title="'User Account'" :link="'/profile'" :grid-span="'11/12'"
        :icon="config.AvatarBaseUrl + user.avatar" />
    </ul>
  </nav>
</template>
<style scoped>
nav {
  grid-column: 1/13;
  grid-row: span 3;
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

.upload_button_container {
  grid-column: 9/10;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--rose);
}

.upload_button:hover {
  background-color: var(--dark-rose);
  color: var(--white-mute);
}

.upload_button {
  border: 1px solid var(--dark-rose);
  background-color: var(--light-blue-black);
  color: var(--rose);
  padding: 1rem;
  min-height: 4rem;
  border-radius: 2rem;
}

.myFiles_container {
  grid-column: 3/4;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--rose);
}

.myFiles_button:hover {
  background-color: var(--dark-rose);
  color: var(--white-mute);
}

.myFiles_button {
  border: 1px solid var(--dark-rose);
  background-color: var(--light-blue-black);
  color: var(--rose);
  padding: 1rem;
  min-height: 4rem;
  border-radius: 2rem;
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

  .upload_button {
    grid-column: 9/11;
  }

  .myFiles_container {
    grid-column: 3/5;
  }

}

@media (max-width:500px) {
  .myFiles_container {
    grid-column: 3/6;
  }
}
</style>
