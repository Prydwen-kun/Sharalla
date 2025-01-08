<script setup>
import config from '../../../config/config';
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  userId: String,
})

const router = useRouter()

const emit = defineEmits(['need_update'])
let user = ref({})
let user_avatar = ref('')
let saved = ref(false)

async function fetch_user() {
  const response2 = await axios.post(
    `${config.APIbaseUrl}${config.endpoints.getUserAdmin}${config.endpoints.GET.userId}${props.userId}`)
  const data2 = await response2.data
  return data2.response
}

user.value = await fetch_user()
user_avatar.value = await ('background-image: url(' + config.AvatarBaseUrl + user.value.avatar + ');')

onMounted(async () => {
  const fileInput = document.getElementById('Avatar')
  fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
      const fileName = fileInput.files[0].name
      const file = fileInput.files[0]
      const avatar_wrap = document.getElementById('avatar_wrap')
      const back_img_url = `background-image: url(${URL.createObjectURL(file)});`
      avatar_wrap.setAttribute('style', back_img_url)
    }


  })

})

async function save_admin(userId) {
  //recup input value to transmit
  const Username = document.getElementById('Username').value
  const Email = document.getElementById('Email').value
  const Password = document.getElementById('Password').value
  const PasswordConfirm = document.getElementById('PwdConfirm').value
  const Avatar = document.getElementById('Avatar')

  //form data object
  const form = document.getElementById('profile_form')
  const formData = new FormData(form)

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  let alertArray = []

  if (Username.length < 3) {
    alertArray.push('Username must be at least 3 characters !')
  }
  if (!emailRegex.test(Email)) {
    alertArray.push('Enter a valid email !')
  }
  if (Password.length < 8 && Password !== '') {
    alertArray.push('Password minimum length is 8 !')
  }
  if (Password !== PasswordConfirm) {
    alertArray.push('Enter the same password twice to confirm !')
  }
  if (Avatar.files.length > 0) {
    const file = Avatar.files[0]
    if (file.size >= 5000000) {
      alertArray.push('Image size must be under 5 Mo !')
    }
  }

  if (alertArray.length === 0) {
    const response = await axios.post(
      `${config.APIbaseUrl}${config.endpoints.updateUser}${config.endpoints.GET.userId}${userId}`,
      formData
    )
    const data = await response.data
    saved.value = true
    user.value = await fetch_user()
    user_avatar.value = await ('background-image: url(' + config.AvatarBaseUrl + user.value.avatar + ');')
    console.log(data.response)
  } else {
    //can use alertString = alertArray.join() too
    let alertString = ''
    alertArray.forEach((alert) => {
      alertString += alert + '\n'
    })
    alert(alertString)
  }
}

function return_to_profile(userId) {
  router.push({ name: 'Users', params: { id: userId } })
}

function onInputChange() {
  saved.value = false
}

async function deleteUser(userId) {
  const delete_confirm = document.getElementById('delete_prompt').value
  if (delete_confirm !== 'DELETE') {
    alert('Please enter "DELETE" in all CAPS to confirm user deletion !')
    return
  } else {
    const response_delete = await axios.post(`${config.APIbaseUrl}${config.endpoints.deleteUser}${config.endpoints.GET.userId}${userId}`)
    const data = await response_delete.data
    if (data.response === 'delete_success') {
      alert('User deleted')
      router.push('/friend')
    } else {
      alert('Error occured deleting user :' + `${userId}`)
    }
  }

}
</script>
<template>
  <div class="popup">
    <h2>Admin Panel - User modification</h2>
    <form action="" method="post" class="profile_form" id="profile_form" enctype="multipart/form-data">
      <div class="input_wrapper">
        <label for="Username">Username</label>
        <input type="text" name="Username" id="Username" :value="user.username" :placeholder="user.username"
          :onfocus="onInputChange">
      </div>
      <div class="input_wrapper">
        <label for="Email">Email</label>
        <input type="email" name="Email" id="Email" :value="user.email" :placeholder="user.email">
      </div>
      <div class="input_wrapper">
        <label for="Password">Password</label>
        <input type="password" name="Password" id="Password" value="" placeholder="Change password...">
      </div>
      <div class="input_wrapper">
        <label for="PwdConfirm">Confirm password</label>
        <input type="password" name="PasswordConfirm" id="PwdConfirm" value="" placeholder="Confirm password...">
      </div>
      <div class="input_wrapper">
        <label for="Avatar">Avatar</label>
        <div class="avatar" id="avatar_wrap" :style="user_avatar">
          <label for="Avatar">Change avatar</label>
          <input type="file" accept="image/*" name="Avatar" id="Avatar">
        </div>
      </div>
    </form>
    <div class="confirm">
      <button @click="save_admin(userId)">Save</button>
      <button @click="return_to_profile(userId)">Return</button>
    </div>
    <div class="delete_prompt">
      <input type="text" id="delete_prompt" name="delete_prompt" placeholder="Type DELETE to delete user">
      <button @click="deleteUser(userId)">DELETE</button>
    </div>

    <!-- Save confirmation -->
    <div class="saved" v-if="saved">Saved</div>
  </div>
</template>
<style scoped>
.popup {
  background-color: var(--light-blue-black);
  color: var(--rose);
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  gap: 2rem;
  padding: 1rem;
}

.input_wrapper>input {
  border-top: none;
  border-left: none;
  border-right: none;
  border-bottom: 1px solid var(--dark-blue-black);
  background-color: var(--light-blue-black);
  color: var(--dark-rose);
  outline: none;
  padding: 0.5rem;
}

.input_wrapper>input:focus {
  background-color: var(--dark-blue-black);
}

.input_wrapper>input::placeholder {
  color: var(--rose-inactive);
}

.confirm {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  gap: 1rem;
}

.confirm>button {
  border: none;
  color: var(--rose);
  background-color: var(--dark-blue-black);
  min-height: 4rem;
  padding: 0.5rem;
  border: 3px solid rgba(0, 0, 0, 0);
  border-radius: 3px;
}

.confirm>button:hover {
  background-color: var(--dark-rose);
  color: var(--white-mute);
  border: 3px solid var(--dark-blue-black);
  border-radius: 3px;
  transition: all 0.25s ease-in;
}

.profile_form {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.input_wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.avatar {
  display: flex;
  flex-direction: column-reverse;
  justify-content: center;
  align-items: center;
  color: var(--rose);
  background-color: var(--dark-blue-black);
  min-height: 4rem;
  padding: 0.5rem;
  border: 3px solid rgba(0, 0, 0, 0);
  border-radius: 25%;
  height: 10rem;
  width: 10rem;
  background-size: contain;
  background-position: center;
  background-repeat: no-repeat;
}

.avatar:hover {
  background-color: var(--dark-rose);
  color: var(--white-mute);
  border: 3px solid var(--dark-blue-black);
  border-radius: 25%;
  transition: all 0.25s ease-in;
}

.avatar>label {
  font-size: 1.2rem;
}

#Avatar {
  opacity: 0;
  height: inherit;
  width: inherit;
}

.saved {
  color: rgb(1, 203, 1);
  font-size: 2.5rem;
}

.delete_prompt {
  display: flex;
  flex-direction: row;
  gap: 1rem;
}

.delete_prompt>button {
  border: 2px solid red;
  color: red;
  background-color: var(--dark-blue-black);
  min-height: 4rem;
  padding: 0.5rem;
  border-radius: 3px;
}

.delete_prompt>button:hover {
  background-color: var(--dark-rose);
  color: var(--white-mute);
  border: 3px solid var(--dark-blue-black);
  border-radius: 3px;
  transition: all 0.25s ease-in;
}

#delete_prompt {
  height: 4rem;
  outline: none;
  background-color: var(--dark-blue-black);
  color: red;
  font-size: 2rem;
}

#delete_prompt::placeholder {
  color: rgb(146, 65, 65);
}
</style>
