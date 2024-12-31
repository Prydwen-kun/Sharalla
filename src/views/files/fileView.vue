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
    <header class="content_header">{{ file.title }}</header>
    <section class="content_display">
      <img v-if="file.type === 'image'" :src="config.AvatarBaseUrl + file.path" :alt="file.title">
    </section>
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
      <div class="comments">comments</div>
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
  gap: 5px;
}

.content_header {
  background-color: var(--light-blue-black);
  border-radius: 5px;
  text-align: center;
  padding: 1rem;
  width: 100%;
  font-size: 2rem;
  font-weight: 600;
}

.content_display {
  background-color: var(--light-blue-black);
  border-radius: 5px;
  width: 100%;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.content_footer {
  width: 100%;
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  background-color: var(--light-blue-black);
  border-radius: 5px;
  padding: 1rem;
  gap: 1rem;
}

.file_info {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1 1 98%;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
  gap: 0.5rem;
}

.file_description {
  display: flex;
  justify-content: center;
  align-items: center;
  flex: 1 1 98%;
  border-radius: 5px;
  background-color: var(--dark-blue-black);
}
</style>
