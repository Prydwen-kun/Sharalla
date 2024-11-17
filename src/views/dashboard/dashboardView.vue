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
  }
})


</script>
<template>
  <main class="dashboard_container">Dashboard</main>
</template>
<style scoped>
.dashboard_container {
  display: grid;
  grid-template-columns: repeat(5, 1fr);

}
</style>
