<script setup>
import { onMounted } from 'vue';
import FormField from './form-component/FormField.vue';
import { Axios } from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';

const props = defineProps({
  form_id: String,
})

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
      } else {
        //catch form submit and make API call to signup

        //form data object
        const form = document.querySelector('signupForm')
        const formData = new FormData(form)
        const formDataObject = Object.fromEntries(formData.entries())
        //form object to text
        const jsonData = JSON.stringify(formDataObject)

        //axios request to API endpoint
        const response = await Axios.post(
          `${config.APIbaseUrl}${config.endpoints.createUser}`,
          jsonData,
          { headers: { 'Content-Type': 'multipart/form-data' } }
        )

        if (response.status === 200) {
          //user created
          console.log('User created !')
          const router = useRouter()
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
