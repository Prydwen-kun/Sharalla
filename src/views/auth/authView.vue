<script setup>
import FormSign from '@/components/signup/FormSign.vue';
import { Axios } from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';
import LinkButton from '@/components/navigation/link_button/LinkButton.vue';

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
  <div class="authView">
    <div class="signupTitle">
      <img src="" alt="logo" title="Sharalla logo" height="100" width="100">
      <h1>Create Account</h1>
      <h2>Already have an account ?</h2>
      <LinkButton button_class="form_button" button_text="Login" button_link="/login" />
    </div>
    <FormSign form_id="signupForm" />
  </div>
</template>
<style scoped>
.authView {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  background-color: var(--light-blue-black);
  grid-column: 3/11;
  padding: 1rem;
}

.signupTitle {
  grid-column: span 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.signupTitle>h1 {
  color: var(--rose);
}

.signupTitle>h2 {
  color: var(--rose);
}

.signupTitle>img {
  background-color: var(--white-mute);
}
</style>
