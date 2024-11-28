<script setup>
import { onMounted } from 'vue'
import config from '../../../config/config';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter()
onMounted(async () => {

})

const props = defineProps({
  alertTitle: String,
  alertText: String,
  alertCall: String
})

async function alert_confirm_logout() {
  const response = await axios.post(
    `${config.APIbaseUrl}${config.endpoints.logout}`
  )
  const data = await response.data
  console.log(data)
  if (data.response === 'login_out') {
    closeAlert()
    router.push('/')
  } else {
    alert('Error login out ! No user connected or an error occured with the request')
  }
}

function closeAlert() {
  const alert_popup = document.getElementById('alert_popup')
  const alert_blocker = document.getElementById('alert_blocker')
  alert_popup.setAttribute('style', 'display: none')
  alert_blocker.setAttribute('style', 'display: none')
}

</script>
<template>
  <div class="alert_popup" id="alert_popup">
    <div class="alertTitle">{{ alertTitle }}</div>
    <div class="alertText">{{ alertText }}</div>
    <div class="confirm">
      <button @click="alert_confirm_logout">Confirm</button>
      <button @click="closeAlert">Cancel</button>
    </div>
  </div>
  <div class="alert_blocker" id="alert_blocker" @click="closeAlert"></div>
</template>
<style scoped>
.alert_popup {
  height: 20.4vw;
  width: 33vw;
  background-color: var(--light-blue-black);
  color: var(--rose);
  position: absolute;
  top: calc(50vh - 10.2vw);
  left: calc(50vw - 16.5vw);
  z-index: 102;

  display: none;
  flex-direction: column;
  justify-content: space-evenly;
  align-items: center;
  border: 3px solid var(--rose);
  border-radius: 3px;
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

.alert_blocker {
  display: none;
  position: absolute;
  z-index: 101;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.452);
}

@media (max-width:600px) {
  .alert_popup {
    height: 40.8vw;
    width: 66vw;
    top: calc(50vh - 20.4vw);
    left: calc(50vw - 33vw);
  }
}
</style>
