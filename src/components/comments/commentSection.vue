<script setup>
import axios from 'axios';
import config from '../../../config/config';
import { ref } from 'vue';

let comments = ref({})
let sent_status = ref('')

async function sendComment() {
  const comment_input = document.getElementById('comment')
  const comment = comment_input.value

  const response = await axios.post(``, { comment: comment })
  const data = await response.data
  if (data.response === 'comment_ok') {
    sent_status.value = 'Sent'
  } else {
    sent_status.value = 'Error sending comment'
  }
}

async function retrieveComments() {
  const response = await axios.post(``)
  const data = await response.data

  if (data.param0 === 'comment_ok') {
    comments.value = data.response
  } else {
    comments.value = { Comment: 'No comments...' }
  }
}

</script>
<template>
  <div class="comments">
    <div class="info_title">Comments</div>
    <div class="comment_input">
      <input type="text" name="comment" id="comment" placeholder="Type comment...">
      <button class="send_button" @click="sendComment">Send</button>
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
</template>
<style scoped>
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
</style>
