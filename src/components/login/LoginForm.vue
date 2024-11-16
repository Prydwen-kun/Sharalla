<script setup>
import FormField from '../signup/form-component/FormField.vue';
import { onMounted } from 'vue';
import axios from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';

const props = defineProps({
  form_id: String,
})

const router = useRouter()
//onMounted assures that all DOM is rendered BEFORE searching for an element ID
onMounted(() => {
  document.getElementById('loginForm').addEventListener('submit',
    async (event) => {
      event.preventDefault();
      const Username = document.getElementById('UserLogin').value;
      const Password = document.getElementById('UserPwd').value;

      //form front-end validation
      if (Username === '' || Password === '') {
        alert('Please fill in all fields.');
      } else {
        //catch form submit and make API call to signup
        //form data object
        const form = document.getElementById('loginForm')
        const formData = new FormData(form)
        //axios request to API endpoint
        const response = await axios.post(
          `${config.APIbaseUrl}${config.endpoints.login}`,
          formData
        )
        const data = await response.data
        if (data.response === 'connected') {
          //user created
          console.log('Login Success !')
          router.push('/dashboard')
        } else {
          //error
          alert('Login error !')
        }


      }
    });

})


</script>
<template>
  <form class="login-form" action="" method="post" enctype="multipart/form-data" :id="form_id">
    <FormField name="UserLogin" type="text" />
    <FormField name="UserPwd" type="password" />
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
