<script setup>

import axios from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';

//make API request to see if user not connected
//redirect to login if true
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
  <div class="dashboard_container">
    <div class="filters">
      <div>file filters</div>
    </div>
    <div class="file_list">
      <div>file</div>
      <div>file</div>
      <div>file</div>
    </div>
  </div>
</template>
<style scoped>
.dashboard_container {
  grid-column: 3/11;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  background-color: var(--light-blue-black);
  padding: 0.5rem;
  gap: 0.318rem;
  color: var(--rose);
}

.filters {
  grid-column: span 1;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
}

.file_list {
  grid-column: span 4;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
}

@media (max-width:600px) {
  .dashboard_container {
    grid-column: span 12;
  }
}
</style>
