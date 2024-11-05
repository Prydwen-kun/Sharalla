<script setup>
import FormSign from '@/components/signup/FormSign.vue';

//catch form submit and make API call to signup

</script>
<template>
  <div class="authView">
    <div class="signupTitle">
      <img src="" alt="logo" title="Sharalla logo" height="100" width="100">
      <h1>Create Account</h1>
    </div>
    <FormSign>
    </FormSign>
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

.signupTitle>h1{
  color: var(--rose);
}

.signupTitle>img {
  background-color: var(--white-mute);
}
</style>
