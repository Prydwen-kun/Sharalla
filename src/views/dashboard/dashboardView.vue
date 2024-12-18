<script setup>
import axios from 'axios';
import config from '../../../config/config';
import { useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import f_filters from '@/components/files_dash/filters/f_filters.vue';
//make API request to see if user not connected
//redirect to login if true
const router = useRouter()

let files = ref({})

onMounted(async () => {
  const getResponse = async () => {
    return await axios.get(
      `${config.APIbaseUrl}${config.endpoints.isConnected}`
    )
  }
  const response = await getResponse()
  const data = response.data

  if (data.response !== 'connected') {
    router.push('/login')
  } else {
    //files list data request
    const response2 = await axios.get(`${config.APIbaseUrl}${config.endpoints.getUserList}`)
    const data2 = response2.data
    if (data2.response === 'no_cookie' || data2.response === 'req_error' || data2.response === 'forbidden') {
      alert('You\'re not connected or an error occured !')
    } else {
      files.value = data2.response
    }

    const list_files = document.getElementById('list_files')
    if (files.value === undefined) {
      list_files.innerHTML = '<div class="list_item">No result...</div>'
    }

  }
})


</script>
<template>
  <div class="dashboard_container">
    <div class="f_filters">
      <f_filters />
    </div>
    <div class="file_list">
      <!-- v-for on file list -->
      <div id="list_files" class="list_inject">
        <div class="list_item">

        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.dashboard_container {
  grid-column: 3/11;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  background-color: var(--light-blue-black);
  padding: 0.5rem;
  gap: 0.318rem;
  color: var(--rose);
}

.f_filters {
  grid-column: span 1;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
}

.file_list {
  grid-column: span 4;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
}

@media (max-width:600px) {
  .dashboard_container {
    grid-column: span 12;
  }
}
</style>
