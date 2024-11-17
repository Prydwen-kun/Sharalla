<!-- src/views/NotFound.vue -->
<template>
  <div>
    <h1>403 - Access Denied</h1>
    <p>Sorry, you are not authorized to see this page.</p>
    <router-link to="/">Return to Login</router-link>
  </div>
</template>

<script>
export default {
  name: 'NotFound',
};
</script>
