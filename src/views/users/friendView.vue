<script setup>
import config from '../../../config/config';
import { onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter()
let users = {}

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
    //userlist data request
    const response2 = await axios.get(`${config.APIbaseUrl}${config.endpoints.getUserList}`)
    const data2 = response2.data
    if (data2.response === 'no_cookie' || data2.response === 'req_error' || data2.response === 'forbidden') {
      alert('You\'re not connected or an error occured !')
    } else {
      users = data2.response
      console.log(users)
    }
  }

  l_slider_created()

  const ul_users = document.getElementById('ul_users')
  for (const user of users) {
    ul_users.innerHTML +=
      `<div class="list_item">
          <img src="${user.avatar}" height="40" width="40" :alt="${user.avatar}" />
          <p>${user.username}</p>
          <p>${user.rank}</p>
        </div>`
  }
})

function l_slider_created() {
  let l_size_slider = document.getElementById('l_slider')
  let displayed_num = document.getElementById('displayed_num')
  displayed_num.innerHTML = 'Displayed - ' + l_size_slider.value
}

async function l_slider_update() {
  let l_size_slider = document.getElementById('l_slider')
  let displayed_num = document.getElementById('displayed_num')
  displayed_num.innerHTML = 'Displayed - ' + l_size_slider.value

  let orderBy = document.getElementById('orderBy')

  //userlist data request
  const response = await axios.get(`${config.APIbaseUrl}${config.endpoints.getUserList}${config.endpoints.GET.l_size}${l_size_slider.value}${config.endpoints.GET.order}${orderBy.value}`)
  const data = response.data
  if (data.response === 'no_cookie' || data.response === 'req_error' || data.response === 'forbidden') {
    alert('You\'re not connected or an error occured !')
  } else {
    users = data.response
    const ul_users = document.getElementById('ul_users')
    ul_users.innerHTML = ''
    for (const user of users) {
      ul_users.innerHTML +=
        `<div class="list_item">
          <img src="${user.avatar}" height="40" width="40" :alt="${user.avatar}" />
          <p>${user.username}</p>
          <p>${user.rank}</p>
        </div>`
    }
  }

  //request update
}

</script>
<template>
  <div class="friend_container">
    <div class="u_filters">
      <div class="u_filter">
        <div>Order by</div>
        <div>
          <select @change="l_slider_update" name="orderBy" id="orderBy" class="filter_select">
            <option value="" disabled>---Select a filter---</option>
            <option :value="value" v-for="(value, index) in config.filter" :key="index">{{ value }}</option>
          </select>
        </div>
      </div>
      <div class="u_filters no_padding">
        <div id="displayed_num">Displayed</div>
        <div><input @change="l_slider_update" id="l_slider" class="l_size_slider" type="range" min="5" max="100"
            step="5" value="10" name="list_size"></div>
      </div>
    </div>
    <div class="list_container">
      <div class="u_list">
        <h3 class="list_title">Users list</h3>
        <div id="ul_users" class="list_inject"></div>
      </div>
      <div class="friend_list">
        <h2 class="list_title">Friends list</h2>
        <div id="ul_friends" class="list_inject"></div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.friend_container {
  grid-column: 3/11;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  background-color: var(--light-blue-black);
  padding: 0.5rem;
  gap: 0.318rem;
  color: var(--rose);
}

.u_filters {
  grid-column: span 1;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
}

.filter_select {
  background-color: var(--light-blue-black);
  color: var(--rose);
  border-radius: 5px;
  min-height: 4rem;
  padding: 0.25rem;
}

.filter_select:focus {
  border: 3px solid var(--rose);
}

.no_padding {
  padding: 0;
}

.l_size_slider {
  appearance: none;
  -webkit-appearance: none;
  width: 100%;
  height: 0.75rem;
  border-radius: 3px;
  background: var(--light-blue-black);
  outline: var(--rose) solid 2px;
  opacity: 0.7;
  transition: opacity .2s
}

.l_size_slider:hover {
  opacity: 1;
}

.l_size_slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  /* Remove default appearance */
  appearance: none;
  width: 1.5rem;
  height: 2.5rem;
  background: var(--rose);
  cursor: pointer;
  border-radius: 25%;
}

.l_size_slider::-moz-range-thumb {
  width: 1.5rem;
  height: 2.5rem;
  background: var(--rose);
  cursor: pointer;
  border-radius: 25%;
}

.list_container {
  grid-column: span 4;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.25rem;
}

.list_container>div {
  padding: 5px;
}

.list_title {
  font-size: 2rem;
  text-align: center;
}

.list_inject {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

@media (max-width:600px) {
  .friend_container {
    grid-column: span 12;
  }
}
</style>
<style>
.list_item {
  padding: 0.2rem;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  background-color: var(--light-blue-black);
  border-radius: 5px;
}

.list_item>img {
  background-color: var(--light-rose);
  border-radius: 5px;
}
</style>
