<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import config from '../../../config/config';
import { useRouter } from 'vue-router';

const router = useRouter()

async function isConnected() {
  const response0 = await axios.post(`${config.APIbaseUrl}${config.endpoints.isConnected}`)
  const data = await response0.data
  if (data.response !== 'connected') {
    router.push('/login')
  } else {
    return true
  }
}
const connected = isConnected()

</script>
<template>
  <div></div>
</template>
<style scoped></style>
