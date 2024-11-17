<script setup>
import LoginForm from '@/components/login/LoginForm.vue';
import axios from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';
import LinkButton from '@/components/navigation/link_button/LinkButton.vue';
import { onMounted } from 'vue';

//make API request to see if user already connected
//redirect on dash if true
const router = useRouter()
onMounted(async () => {
  const getResponse = async () => {
    return await axios.get(
      `${config.APIbaseUrl}${config.endpoints.isConnected}`
    )
  }
  const response = await getResponse()
  const data = await response.data

  if (data.response === 'connected') {

    router.push('/dashboard')
  }
})


</script>
<template>
  <div class="LoginView">
    <div class="LoginTitle">
      <img src="" alt="logo" title="Sharalla logo" height="100" width="100">
      <h1>Sign in</h1>
      <h2>Need to create an account ?</h2>
      <LinkButton button_class="form_button" button_text="Signup" button_link="/" />
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

.LoginTitle>h2 {
  color: var(--rose);
}

.LoginTitle>img {
  background-color: var(--white-mute);
}
</style>
