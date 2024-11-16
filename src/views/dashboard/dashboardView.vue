<script setup>

import axios from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';

//make API request to see if user not connected
//redirect to login if true
const router = useRouter()
const response = async () => {
  return await axios.get(
    `${config.APIbaseUrl}${config.endpoints.isConnected}`
  )
}

const data = async () => { return await response.data }

if (data.response === 'not_connected') {
  router.push('/login')
}

</script>
<template>
  <main>Dashboard</main>
</template>
<style scoped></style>
