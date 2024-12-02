<script setup>
import config from '../../../config/config';
import { onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter()

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
    //user profile data request
  }
})

</script>
<template>
  <div class="friend_container">
    <div class="u_filters">
      <div class="u_filter">
        <div>Order by</div>
        <div>
          <select name="orderBy" id="orderBy" class="filter_select">
            <option value="" disabled>---Select a filter---</option>
            <option :value="value" v-for="(value, index) in config.filter" :key="index">{{ value }}</option>
          </select>
        </div>
      </div>
      <div class="u_filters">
        <div>Displayed</div>
        <div><input type="range" min="5" max="100" step="5" value="10" name="list_size"></div>
      </div>
    </div>
    <div class="list_container">
      <div class="u_list">

      </div>
      <div class="friend_list">

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

.list_container {
  grid-column: span 4;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
}

@media (max-width:600px) {
  .friend_container {
    grid-column: span 12;
  }
}
</style>
