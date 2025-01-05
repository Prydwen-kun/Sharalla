<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import config from '../../../config/config';
import { useRouter } from 'vue-router';

const router = useRouter()

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

</script>
<template>
  <section class="upload_view">
    <form action="" method="post" id="upload_form" class="upload_form">
      <div>
        <div class="selected_file_path" id="selected_file_path">Select a file to upload...</div>
      </div>
      <label for="file_input" class="select_button">Choose File</label>
      <div class="file_size"></div>
      <input type="file" id="file_input" name="Upload">
      <input type="text" id="file_title" name="Title" placeholder="Title...">
      <textarea name="Description" id="file_description" placeholder="Description..." cols="40" rows="15"></textarea>
      <button class="upload_button">UPLOAD <div class="upload_arrow">=></div></button>
    </form>
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
