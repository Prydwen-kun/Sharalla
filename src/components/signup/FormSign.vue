<script setup>
import FormField from './form-component/FormField.vue';
</script>
<template>
  <form action="" method="post" enctype="multipart/form-data">
    <FormField name="Username" type="text" />
    <FormField name="Email" type="email" />
    <FormField name="Password" type="password" />
    <FormField name="PasswordConfirm" type="password" />
  </form>
</template>
<style scoped></style>
