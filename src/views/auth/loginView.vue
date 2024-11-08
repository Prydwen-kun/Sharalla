<script setup>
import LoginForm from '@/components/login/LoginForm.vue';
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
