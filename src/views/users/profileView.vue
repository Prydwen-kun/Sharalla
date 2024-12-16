<script setup>
import axios from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import UpdatePopup from '@/components/popup/profileUpdate.vue';

//make API request to see if user not connected
//redirect to login if true
const router = useRouter()

//default value
let avatar = ref('')

onMounted(async () => {
  const getResponse = async () => {
    return await axios.get(
      `${config.APIbaseUrl}${config.endpoints.isConnected}`
    )
  }
  const response = await getResponse()
  const data = response.data

  if (data.response !== 'connected') {
    router.push('/login')
  } else {
    //request connected user data
    const response = await axios.post(
      `${config.APIbaseUrl}${config.endpoints.getConnectedUserData}`
    )
    const data = await response.data

    //retrieve elements
    const p_username = document.getElementById('p_username')
    const p_last_online = document.getElementById('p_last_online')
    const p_signup_date = document.getElementById('p_signup_date')

    if (data.response !== 'req_error' && data.response !== 'forbidden' && data.response !== 'no_cookie') {
      p_username.innerHTML = `${data.response.username}'s profile`
      p_last_online.innerHTML = data.response.last_login.slice(0, 10)
      p_signup_date.innerHTML = data.response.signup_date.slice(0, 10)
      avatar.value = data.response.avatar
    } else {
      //handle error
      p_username.innerHTML = 'No user'
      p_last_online.innerHTML = 'No user'
      p_signup_date.innerHTML = 'No user'
    }
  }
})

let popup_flag = ref(false)
function popup() {
  popup_flag.value = !popup_flag.value
}

//event update
async function data_update(){
//request connected user data
const response = await axios.post(
      `${config.APIbaseUrl}${config.endpoints.getConnectedUserData}`
    )
    const data = await response.data

    //retrieve elements
    const p_username = document.getElementById('p_username')
    const p_last_online = document.getElementById('p_last_online')
    const p_signup_date = document.getElementById('p_signup_date')

    if (data.response !== 'req_error' && data.response !== 'forbidden' && data.response !== 'no_cookie') {
      p_username.innerHTML = `${data.response.username}'s profile`
      p_last_online.innerHTML = data.response.last_login.slice(0, 10)
      p_signup_date.innerHTML = data.response.signup_date.slice(0, 10)
      avatar.value = data.response.avatar
    } else {
      //handle error
      p_username.innerHTML = 'No user'
      p_last_online.innerHTML = 'No user'
      p_signup_date.innerHTML = 'No user'
    }
}
</script>
<template>
  <Suspense>
    <UpdatePopup v-if="popup_flag" @need_update="data_update" @closePopup="popup" />
  </Suspense>
  <div class="profile_container">
    <div class="profile_head">
      <div class="userprofile_name" id="p_username"></div>
      <div class="profile_action">
        <button class="modify_button" @click="popup">Modify</button>
      </div>
    </div>
    <div class="side_stats_container">
      <div class="stat_row">
        <img :src="avatar" alt="avatar" class="p_avatar">
      </div>
      <div class="stat_row">
        <div>Last Online</div>
        <div id="p_last_online"></div>
      </div>
      <div class="stat_row">
        <div>Joined</div>
        <div id="p_signup_date"></div>
      </div>
    </div>
    <div class="profile_content_container">
      <div>1</div>
      <div>2</div>
    </div>
  </div>
</template>
<style scoped>
.profile_container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-template-rows: 4rem auto;
  grid-column: 3/11;
  background-color: var(--light-blue-black);
  color: var(--rose);
  padding: 0.5rem;
  gap: 0.318rem;
}

.profile_head {
  grid-column: span 4;
  grid-row: span 1;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.25rem 1rem;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.userprofile_name {
  font-size: 2rem;
}

.modify_button {
  color: var(--rose);
  background-color: rgba(0, 0, 0, 0);
  border: none;
  text-decoration: underline;
  cursor: pointer;
}

.modify_button:hover {
  color: var(--white-mute);
  text-decoration: none;
}

.side_stats_container {
  grid-column: 1/2;
  background-color: var(--dark-blue-black);
  padding: 0.5rem 0.5rem;
  border-radius: 5px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.p_avatar {
  background-color: var(--light-rose);
  border-radius: 1rem;
  height: 16rem;
  width: 12rem;
  margin: auto auto;
}

.stat_row {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}

.profile_content_container {
  grid-column: 2/5;
  display: grid;
  grid-template-columns: 1fr 1fr;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.25rem;
}

@media(max-width:600px) {
  .profile_container {
    grid-column: span 12;
  }

  .side_stats_container {
    grid-column: span 5;
  }

  .profile_content_container {
    grid-column: span 5;
  }
}
</style>
