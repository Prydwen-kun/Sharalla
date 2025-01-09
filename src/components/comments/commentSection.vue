<script setup>
import axios from 'axios';
import config from '../../../config/config';
import { onMounted, ref } from 'vue';

let comments = ref(Object)
let sent_status = ref('')
let comment_loaded = ref(false)

const props = defineProps({
  file_id: Number
})

const emit = defineEmits(['need_update'])

async function sendComment() {
  const comment_input = document.getElementById('comment')
  const comment = comment_input.value
  if (comment.length <= 0) {
    alert('Comment empty')
    return
  }
  const response = await axios.post(`${config.APIbaseUrl}${config.endpoints.files.sendComment}${config.endpoints.GET.fileId}${props.file_id}`, { comment: comment }, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
  const data = await response.data
  console.log(data.response)
  if (data.response === 'comment_ok') {
    sent_status.value = 'Sent'
    comment_input.value = ''
    await retrieveComments()
  } else {
    sent_status.value = 'Error sending comment'
  }
}

async function retrieveComments() {
  const response = await axios.post(`${config.APIbaseUrl}${config.endpoints.files.getComments}${config.endpoints.GET.fileId}${props.file_id}`)
  const data = await response.data
  console.log(data.response)
  if (data.message === 'Comment List') {
    comments.value = data.response
  } else {
    comments.value = { Comment: 'No comments...' }
  }
}

await retrieveComments()

function reset_sentStatus() {
  sent_status.value = ''
}

</script>
<template>
  <div class="comments">
    <div class="info_title">Comments</div>
    <div class="comment_input">
      <input @change="reset_sentStatus" type="text" name="comment" id="comment" placeholder="Type comment...">
      <button class="send_button" @click="sendComment">Send</button>
      <div>{{ sent_status }}</div>
    </div>
    <div v-if="comments.Comment !== 'No comments...'" class="comment_section">
      <div v-for="(comment, key, index) in comments" :key="index" class="comment">
        <div class="comment_header">
          <img :src="config.AvatarBaseUrl + comment.avatar" class="comment_avatar" alt="Avatar">
          <div class="comment_user">
            <div>{{ comment.author }}</div>
            <div>
              {{ Object.keys(comments).length !== 0 ? comment.post_date.split(' ').reverse().toString().replace(',', ' '):'' }}
            </div>
          </div>
        </div>
        <div>{{ comment.content }}</div>
      </div>
    </div>
    <div v-else>No comment...</div>
  </div>
</template>
<style scoped>
.comments {
  background-color: var(--dark-blue-black);
  width: 100%;
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
  color: var(--rose);
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

.comment_user {
  display: flex;
  flex-direction: row;
  gap: 1rem;
}

.comment_avatar {
  background-color: var(--white-mute);
  height: 4rem;
  width: 4rem;
  border-radius: 2rem;
}
</style>
