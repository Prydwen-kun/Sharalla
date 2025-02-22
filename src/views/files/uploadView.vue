<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import config from '../../../config/config';
import { useRouter } from 'vue-router';
import uploadConfirm from '@/components/popup/uploadConfirm.vue';

const router = useRouter()

let selected_file = ref('Select a file to upload...')
let file_size = ref(0)
let file_error = ref('')
let upload_popup = ref(false)

async function isConnected() {
  const response0 = await axios.post(`${config.APIbaseUrl}${config.endpoints.isConnected}`)
  const data = await response0.data
  if (data.response !== 'connected') {
    router.push('/login')
  } else {
    return true
  }
}
const connected = isConnected()

function file_selected() {
  const file_input = document.getElementById('file_input')
  const default_file_prompt = 'Select a file to upload...'

  if (file_input.files.length > 0) {
    const file = file_input.files[0]
    selected_file.value = file.name
    file_size.value = file.size
    file_error.value = ''
    if (file.size > 2000000000) {
      file_error.value = 'File size exceeds 2GB'
    }
  } else {
    selected_file.value = default_file_prompt
    file_size.value = 0
    file_error.value = 'No file selected'
  }
}

async function upload() {
  const file_input = document.getElementById('file_input')
  if (file_input.files.length > 0) {
    const form = document.getElementById('upload_form')
    const formData = new FormData(form)

    const response = await axios.post(`${config.APIbaseUrl}${config.endpoints.files.uploadFile}`, formData)
    const data = response.data
    if (data.response === 'upload_ok') {
      upload_popup.value = false
      file_error.value = ''
      alert('Upload success !')
    } else {
      switch (data.response) {
        case 'error':
          upload_popup.value = false
          file_error.value = 'Unknown error occured !'
          break;
        case 'req_error':
          upload_popup.value = false
          file_error.value = 'Upload Request error !'
          break;
        case 'file_upload_error':
          upload_popup.value = false
          file_error.value = 'Upload error please retry'
          break;
        case 'file_size_error':
          upload_popup.value = false
          file_error.value = 'File too big limit is 2GB'
          break;
        case 'file_ext_error':
          upload_popup.value = false
          file_error.value = 'File extension error !'
          break;
        case 'file_title_size_err':
          upload_popup.value = false
          file_error.value = 'File title too long limit 255 !'
          break;
        case 'file_desc_size_err':
          upload_popup.value = false
          file_error.value = 'File description too long limit 255 !'
          break;

        default:
          upload_popup.value = false
          file_error.value = 'Upload error please retry'
          break;
      }

    }

  } else {
    upload_popup.value = false
    file_error.value = 'No file selected'
  }

}

function upload_confirm() {
  const file_input = document.getElementById('file_input')
  if (file_input.files.length > 0) {
    upload_popup.value = !upload_popup.value
  } else {
    file_error.value = 'No file selected'
    upload_popup.value = false
  }

}

</script>
<template>
  <section class="upload_view">
    <form action="" method="post" id="upload_form" class="upload_form" enctype="multipart/form-data">
      <div>
        <div class="selected_file_path" id="selected_file_path">{{ selected_file }}</div>
        <div class="file_error">{{ file_error }}</div>
      </div>
      <label for="file_input" class="select_button">Choose File</label>
      <div class="file_size">{{ (file_size / 1048576).toPrecision(5) }} MB / 2GB Limit</div>
      <input @change="file_selected" type="file" id="file_input" name="Upload">
      <input type="text" id="file_title" name="Title" placeholder="Title...">
      <textarea name="Description" id="file_description" placeholder="Description..." cols="40" rows="15"></textarea>
      <button @click="upload_confirm" form="noform" class="upload_button">UPLOAD <div class="upload_arrow">=></div>
      </button>
    </form>
    <uploadConfirm v-if="upload_popup" @uploadConfirm="upload" @closePopup="upload_confirm" />
  </section>
</template>
<style scoped>
.upload_view {
  grid-column: span 12;
  background-color: var(--dark-blue-black);
  color: var(--rose);
  padding: 2rem;
}

.upload_form {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3rem;
}

.file_error {
  color: rgb(200, 0, 0);
}

.select_button {
  background-color: var(--dark-blue-black);
  color: var(--rose);
  height: 4rem;
  border: 1px solid var(--rose);
  border-radius: 2rem;
  font-weight: 600;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
}

.select_button:hover {
  background-color: var(--light-rose);
  color: var(--white-mute);
  cursor: pointer;
}

#file_input {
  display: none;
}

#file_title {
  outline: none;
  border: 1px solid var(--rose);
  background-color: var(--dark-blue-black);
  color: var(--rose);
  height: 4rem;
  border-radius: 2rem;
  padding: 1rem;
}

#file_title:focus {
  border: 3px solid var(--rose);
  background-color: var(--light-blue-black);
}

#file_title::placeholder {
  color: var(--rose-inactive);
}

#file_description {
  background-color: var(--dark-blue-black);
  color: var(--rose);
  outline: none;
  border: 1px solid var(--rose);
  border-radius: 5px;
  padding: 1rem;
  resize: vertical;
}

#file_description:focus {
  border: 3px solid var(--rose);
  background-color: var(--light-blue-black);
}

#file_description::placeholder {
  color: var(--rose-inactive);
}

.upload_button {
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  background-color: var(--dark-blue-black);
  border: 2px solid var(--rose);
  height: 4rem;
  border-radius: 2rem;
  color: var(--rose);
  padding: 1rem;
  gap: 1rem;
}

.upload_button:hover {
  color: var(--white-mute);
  background-color: var(--light-rose);
  cursor: pointer;

  .upload_arrow {
    border-left: 3px solid var(--white-mute);
  }
}

.upload_arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  transform: rotate(-90deg);
  border-left: 3px solid var(--rose);
  font-weight: 600;
}
</style>
