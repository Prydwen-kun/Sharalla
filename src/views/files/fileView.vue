<script setup>
import axios from 'axios';
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import config from '../../../config/config';

const router = useRouter()
const route = useRoute()
const fileId = computed(() => route.params.id)
let file = ref({})

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

async function getFileData() {
  const response = await axios.post(`${config.APIbaseUrl}${config.endpoints.files.getFileData}${config.endpoints.GET.fileId}${fileId.value}`)

  const data = response.data
  file.value = data.response
}

getFileData()

async function download(file_id, file_name, file_type, file_ext) {
  try {

    const response = await axios.post(`${config.APIbaseUrl}${config.endpoints.files.download}${config.endpoints.GET.fileId}${file_id}`, { responseType: 'blob' })
    const data = await response.data
    const url = window.URL.createObjectURL(new Blob([data]), { type: file_type + '/' + file_ext });
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', file_name + '.' + file_ext);
    // Set the default filename
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

  } catch (error) { console.error('Error downloading file:', error); }
}
</script>
<template>
  <main class="main_content">
    <header class="content_header">{{ file.title }}</header>
    <section class="content_display">
      <img class="img_content" v-if="file.type === 'image'" :src="config.AvatarBaseUrl + file.path" :alt="file.title">
      <video v-else-if="file.type === 'video'" class="video_content" controls poster="">
        <source :src="config.AvatarBaseUrl + file.path" :type="'video/' + file.extension" />
        <a :href="config.AvatarBaseUrl + file.path">Fallback download link</a>
      </video>
      <audio v-else-if="file.type === 'audio'" class="audio_content" controls>
        <source :src="config.AvatarBaseUrl + file.path" :type="'audio/' + file.extension" />
      </audio>
      <embed v-else-if="file.type === 'application' && file.extension === 'pdf'" :src="config.AvatarBaseUrl + file.path"
        :type="'application/pdf'" class="pdf_content">
      <a class="download_content" @click="download(file.id, file.title, file.type, file.extension)">Download File
        <div class="down_arrow">=></div>
      </a>
    </section>
    <footer class="content_footer">
      <div class="file_info">
        <p class="info_title">Title</p>
        <p> {{ file.title }}</p>

        <p class="info_title">Size</p>
        <p>{{ Math.floor(file.size / 1024) }}KB</p>

        <p class="info_title">Extension</p>
        <p>{{ file.extension }}</p>

        <p class="info_title">File Type</p>
        <p>{{ file.type }}</p>

        <p class="info_title">Upload date</p>
        <p>{{ file.upload_date }}</p>

        <p class="info_title">Uploaded by</p>
        <p>{{ file.uploader }}</p>
      </div>
      <div class="file_description">
        <div class="info_title">Description</div>
        <div>{{ file.description }}</div>
      </div>
      <!-- create comment component later -->
      <div class="comments">
        <div class="info_title">Comments</div>
        <div class="comment_input">
          <input type="text" name="comment" id="comment" placeholder="Type comment...">
          <button class="send_button">Send</button>
        </div>
        <div class="comment_section">
          <div class="comment">
            <div class="comment_header">
              <img src="" class="comment_avatar" alt="Avatar">
              <div>User1234</div>
            </div>
            <div>Lorem ipsum dolor sit amet consectetur adipisicing elit. Atque, explicabo. Pariatur enim quaerat nisi.
              Aspernatur suscipit, saepe non ex natus minus voluptate quidem alias sequi eveniet enim animi iure sed!
            </div>
          </div>
          <div class="comment">
            <div class="comment_header">
              <img src="" class="comment_avatar" alt="Avatar">
              <div>User1234</div>
            </div>
            <div>Lorem ipsum dolor sit amet consectetur adipisicing elit. Atque, explicabo. Pariatur enim quaerat nisi.
              Aspernatur suscipit, saepe non ex natus minus voluptate quidem alias sequi eveniet enim animi iure sed!
            </div>
          </div>
        </div>
      </div>
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
  max-width: 100%;
  padding: 0.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  object-fit: contain;
  gap: 1rem;
}

.img_content {
  object-fit: contain;
  max-width: 100%;
}

.video_content {
  object-fit: contain;
  max-width: 100%;
}

.audio_content {
  max-width: 100%;
  color: var(--rose);
  background-color: var(--dark-blue-black);
}

.pdf_content {
  width: 98vw;
  height: 80vh;
}

.download_content {
  border: 2px solid var(--rose);
  border-radius: 2rem;
  height: 4rem;
  background-color: var(--dark-blue-black);
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  gap: 0.5rem;
}

.download_content:hover {
  cursor: pointer;
  color: var(--white-mute);
  background-color: var(--light-rose);

  .down_arrow {
    border-right: 3px solid var(--white-mute);
  }
}

.down_arrow {
  border-right: 3px solid var(--rose);
  font-weight: 800;
  transform: rotate(90deg);
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
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr;
  flex: 1 1 100%;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
  gap: 1rem;
  max-width: 100%;
}

.file_info>* {
  grid-column: span 1;
  overflow: hidden;
}

.info_title {
  border-right: 2px solid var(--rose);
  border-bottom: 2px solid var(--rose);
  border-radius: 5px;
  padding: 5px;
  font-weight: 600;
}

.file_description {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: start;
  flex: 1 1 100%;
  border-radius: 5px;
  background-color: var(--dark-blue-black);
  padding: 1rem;
}

.comments {
  background-color: var(--dark-blue-black);
  padding: 1rem;
  border-radius: 5px;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.comment_input {
  display: flex;
  flex-direction: row;
  gap: 1rem;
}

.comment_input>input {
  flex: 1 1 auto;
  background-color: var(--dark-blue-black);
  height: 4rem;
  border: none;
  border-bottom: 2px solid var(--rose);
  outline: none;
  padding: 0.5rem;
}

.comment_input>input:focus {
  background-color: var(--light-blue-black);
  color: var(--rose);
  border: none;
  border-radius: 2rem;
}

.comment_input>input::placeholder {
  color: var(--rose-inactive);
}

.send_button {
  width: 8rem;
  height: 4rem;
  font-weight: 600;
  color: var(--rose);
  background-color: var(--dark-blue-black);
  border: 2px solid var(--rose);
  border-radius: 2rem;
}

.send_button:hover {
  background-color: var(--light-rose);
  color: var(--white-mute);
}

.comment_section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.comment {}

.comment_header {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 1rem;
}

.comment_avatar {
  background-color: var(--white-mute);
  height: 4rem;
  width: 4rem;
  border-radius: 2rem;
}

@media (max-width:600px) {
  .file_info {
    padding: 2px;
    gap: 2px;
    max-width: 100%;
  }

  .pdf_content {
    width: 98vw;
  }
}
</style>
