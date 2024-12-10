<script setup>
import config from '../../../config/config';
import axios from 'axios';
import { onMounted } from 'vue';

async function fetch_user() {
  const response = await axios.post(
    `${config.APIbaseUrl}${config.endpoints.getConnectedUserData}`
  )
  const data = await response.data
  return data.response
}

const user = await fetch_user()

const emit = defineEmits(['closePopup'])

async function save_profile() {
  const response = await axios.post(
    `${config.APIbaseUrl}${config.endpoints.updateUser}${config.endpoints.GET.userId}${user.id}`
  )
  const data = await response.data
}

function close_profile() {
  emit('closePopup')
}

onMounted(() => {
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
</script>
<template>
  <div class="popup">
    <h2>Profile Update</h2>
    <form action="" method="post" class="profile_form">
      <div class="input_wrapper">
        <label for="Username">Username</label>
        <input type="text" name="Username" id="Username" :value="user.username" :placeholder="user.username">
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
        <div class="avatar" id="avatar_wrap">
          <label for="Avatar">Change avatar</label>
          <input type="file" accept="image/*" name="Avatar" id="Avatar">
        </div>

      </div>

    </form>
    <div class="confirm">
      <button @click="save_profile">Save</button>
      <button @click="close_profile">Close</button>
    </div>
  </div>
  <div class="blocker" @click="close_profile"></div>
</template>
<style scoped>
.popup {
  height: 50vw;
  width: 33vw;
  background-color: var(--light-blue-black);
  color: var(--rose);
  position: absolute;
  top: calc(50vh - 25vw);
  left: calc(50vw - 16.5vw);
  z-index: 92;

  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  border: 3px solid var(--rose);
  border-radius: 3px;
  gap: 2rem;
  padding: 1rem;
}

.blocker {
  display: block;
  position: absolute;
  top: 0;
  z-index: 91;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.452);
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

@media (max-width:600px) {
  .popup {
    height: 120vw;
    width: 66vw;
    top: calc(50vh - 60vw);
    left: calc(50vw - 33vw);
  }
}
</style>
