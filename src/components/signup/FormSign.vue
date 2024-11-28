<script setup>
import { onMounted } from 'vue';
import FormField from './form-component/FormField.vue';
import axios from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';

const props = defineProps({
  form_id: String,
})

const router = useRouter()//injection outside of onMounted !!
//onMounted assures that all DOM is rendered BEFORE searching for an element ID
onMounted(() => {
  document.getElementById('signupForm').addEventListener('submit',
    async (event) => {
      event.preventDefault();
      const Username = document.getElementById('Username').value;
      const Email = document.getElementById('Email').value;
      const Password = document.getElementById('Password').value;
      const PasswordConfirm = document.getElementById('PasswordConfirm').value;

      //form front-end validation
      if (Username === '' || Email === '' || Password === '' || PasswordConfirm === '') {
        alert('Please fill in all fields.');
      } else if (Username.length < 3) {
        alert('Username must be at least 3 characters long !')
      } else if (Password.length < 8) {
        alert('Password must be at least 8 characters long !')
      } else if (Password !== PasswordConfirm) {
        alert('Please confirm your password by typing it a second time !')
      } else {
        //catch form submit and make API call to signup
        //form data object
        const form = document.getElementById('signupForm')
        const formData = new FormData(form)
        //axios request to API endpoint
        const response = await axios.post(
          `${config.APIbaseUrl}${config.endpoints.signup}`,
          formData
        )
        const data = await response.data
        console.log(data)
        if (data.response === 'user_created') {
          //user created
          console.log('User created !')

          router.push('/login')
        } else {
          //error
          alert('Error creating user !')
        }


      }
    });

})

</script>
<template>
  <form class="signup-form" action="" method="post" enctype="multipart/form-data" :id="form_id">
    <FormField name="Username" type="text" label="Username" />
    <FormField name="Email" type="email" label="Email" />
    <FormField name="Password" type="password" label="Password" />
    <FormField name="PasswordConfirm" type="password" label="Confirm Password" />
    <button type="submit" class="submit">SUBMIT</button>
  </form>
</template>
<style scoped>
.signup-form {
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
