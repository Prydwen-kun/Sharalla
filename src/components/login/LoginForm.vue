<script setup>
import FormField from '../signup/form-component/FormField.vue';

</script>
<template>
  <form class="login-form" action="" method="post" enctype="multipart/form-data" :id="form_id">
    <FormField name="Username" type="text" />
    <FormField name="Password" type="password" />
    <button type="submit" class="submit">Login</button>
  </form>
</template>
<style scoped>
.login-form {
  background-color: var(--light-blue-black);
  color: var(--rose);
  font-size: 2rem;
  grid-column: span 3;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
  width: 100%;
  padding: 1rem;
}

.submit {
  border-radius: 2rem;
  color: var(--rose);
  background-color: var(--light-blue-black);
  min-height: 4rem;
  min-width: 8rem;
  border: 1px solid var(--dark-rose);
}

.submit:hover {
  color: var(--white-mute);
  background-color: var(--dark-rose);
}
</style>
