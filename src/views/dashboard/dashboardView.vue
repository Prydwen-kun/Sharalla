<script setup>

import { Axios } from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';

//make API request to see if user not connected
//redirect to login if true
const response = async () => {
  return await Axios.get(
    `${config.APIbaseUrl}${config.endpoints.isConnected}`
  )
}

const data = async () => { return await response.json() }

if (response.ok && data.response === 'not_connected') {
  const router = useRouter()
  router.push('/login')
}

</script>
<template>
  <main>Dashboard</main>
</template>
<style scoped></style>
