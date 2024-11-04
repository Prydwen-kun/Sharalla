<script setup>
import FormField from './form-component/FormField.vue';
</script>
<template>
  <form class="signup-form" action="" method="post" enctype="multipart/form-data">
    <FormField name="Username" type="text" />
    <FormField name="Email" type="email" />
    <FormField name="Password" type="password" />
    <FormField name="PasswordConfirm" type="password" />
    <button type="submit" class="submit">SUBMIT</button>
  </form>
</template>
<style scoped>
.signup-form {
  background-color: var(--light-blue-black);
  color: var(--rose);
  grid-column: 3/11;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  height: 100%;
  width: 100%;
  padding: 1rem;
}

.submit{
  border-radius: 1rem;
  color: var(--rose);
  background-color: var(--light-blue-black);
  min-height: 2rem;
  min-width: 5rem;
  border: 1px solid var(--dark-rose);
}
.submit:hover{
  color: var(--white-mute);
  background-color: var(--dark-rose);
}
</style>
