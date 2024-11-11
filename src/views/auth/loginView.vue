<script setup>
import LoginForm from '@/components/login/LoginForm.vue';
import { Axios } from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';

//make API request to see if user already connected
//redirect on dash if true
const response = async () => {
  return await Axios.get(
    `${config.APIbaseUrl}${config.endpoints.isConnected}`
  )
}

const data = async () => { return await response.json() }

if (response.ok && data.response === 'connected') {
  const router = useRouter()
  router.push('/dashboard')
}

</script>
<template>
  <div class="LoginView">
    <div class="LoginTitle">
      <img src="" alt="logo" title="Sharalla logo" height="100" width="100">
      <h1>Sign in</h1>
    </div>
    <LoginForm form_id="loginForm" />
  </div>
</template>
<style scoped>
.LoginView {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  background-color: var(--light-blue-black);
  grid-column: 3/11;
  padding: 1rem;
}

.LoginTitle {
  grid-column: span 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.LoginTitle>h1 {
  color: var(--rose);
}

.LoginTitle>img {
  background-color: var(--white-mute);
}
</style>
