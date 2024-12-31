<script setup>
import axios from 'axios';
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import config from '../../../config/config';

const router = useRouter()
const route = useRoute()
const fileId = computed(() => route.params.id)
let file = ref({})

async function getFileData() {
  const response = await axios.post(`${config.APIbaseUrl}${config.endpoints.files.getFileData}${config.endpoints.GET.fileId}${fileId.value}`)

  const data = response.data
  file.value = data.response
}

getFileData()
</script>
<template>
  <main class="main_content">
    <header>{{ file.title }}</header>
    <section class="file_display"></section>
    <footer class="content_footer">
      <div class="file_info">
        <p>Title: {{ file.title }}</p>
        <p>Size: {{ Math.floor(file.size / 1024) }}KB</p>
        <p>Extension: {{ file.extension }}</p>
        <p>File Type: {{ file.type }}</p>
        <p>Upload date: {{ file.upload_date }}</p>
        <p>Uploaded by: {{ file.uploader }}</p>
      </div>
      <div class="file_description">{{ file.description }}</div>
      <div class="comments"></div>
    </footer>
  </main>
</template>
<style scoped>
.main_content {
  grid-column: span 12;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: var(--dark-blue-black);
  color: var(--rose);
}

.content_footer {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}

.file_info {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
</style>
