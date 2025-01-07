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
let results = ref(0)
let totalResults = ref(0)
let loaded = ref(false)

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
    const response2 = await axios.get(`${config.APIbaseUrl}${config.endpoints.files.getFileList}`)
    const data2 = response2.data
    if (data2.response === 'no_cookie' || data2.response === 'req_error' || data2.response === 'forbidden') {
      alert('You\'re not connected or an error occured !')
    } else {
      files.value = data2.response
      totalResults.value = data2.param0
      loaded.value = true
      if (files.value !== undefined) {
        results.value = Object.keys(files.value).length
      } else {
        results.value = 0
      }
    }
    const list_files = document.getElementById('list_files')
    if (files.value === undefined) {
      list_files.innerHTML = '<div class="list_item">No result...</div>'
    }

  }
})

async function list_update() {
  let l_size_slider = document.getElementById('l_slider')
  let orderBy = document.getElementById('orderBy')

  //pagination
  const page_input_ = document.getElementById('page_number')
  let page_value = page_input_.value

  //SearchBar data to send
  const search = document.getElementById('u_search')
  let search_value = search.value

  //userlist data request
  const response = await axios.get(
    `${config.APIbaseUrl}${config.endpoints.files.getFileList}${config.endpoints.GET.l_size}${l_size_slider.value}${config.endpoints.GET.order}${orderBy.value}${config.endpoints.GET.page}${page_value}${config.endpoints.GET.search}${search_value}`)
  const data = response.data
  if (data.response === 'no_cookie' || data.response === 'req_error' || data.response === 'forbidden') {
    alert('You\'re not connected or an error occured !')
  } else {
    files.value = data.response
    totalResults.value = data.param0
    if (files.value !== undefined) {
      results.value = Object.keys(files.value).length
    } else {
      results.value = 0
    }

  }

  return files.value
}

function clearFilters() {
  //clear all values
  const l_size_slider = document.getElementById('l_slider')
  const orderBy = document.getElementById('orderBy')
  const page_input_ = document.getElementById('page_number')
  const search = document.getElementById('u_search')

  l_size_slider.value = 10
  page_input_.value = 1
  orderBy.value = 'id'
  search.value = ''

  list_update()
}

async function nextPage() {
  const page_input = document.getElementById('page_number')
  page_input.value++
  let update_return = null
  update_return = await list_update()
  if (update_return === undefined) {
    page_input.value--
    list_update()
  }
}

function prevPage() {
  const page_input = document.getElementById('page_number')
  if (page_input.value > 1) {
    page_input.value--
  } else {
    page_input.value = 1
  }
  list_update()
}

function routeToFile(fileId) {
  router.push({ name: 'Files', params: { id: fileId } })
}

</script>
<template>
  <div class="dashboard_container">
    <div class="f_filters">
      <f_filters v-if="loaded" @update_filter="list_update" @clear_filters="clearFilters" @page_plus="nextPage" @page_minus="prevPage"
        :results="results" :totalResult="totalResults" />
    </div>
    <table class="file_list">
      <thead>
        <tr>
          <th>Id</th>
          <th>Title</th>
          <th>Description</th>
          <th>Size</th>
          <th>Path</th>
          <th>Upload Date</th>
          <th>Uploader ID</th>
          <th>Uploader</th>
          <th>Extension</th>
          <th>Type</th>
        </tr>
      </thead>
      <!-- v-for on file list -->
      <tbody id="list_files" class="list_inject">
        <tr class="list_item" v-for="(file, key, index) in files" :key="index" @click="routeToFile(file.id)">
          <td class="item_info" v-for="(info, key, index) in file" :key="index">
            <div v-if="key === 'size'">{{ Math.floor(info / 1024) }}KB</div>
            <div v-else-if="key === 'description'">{{ info.slice(0, 10) + '...' }}</div>
            <div v-else>{{ info }}</div>
          </td>
        </tr>
        <tr class="list_item" v-if="files === undefined">
          <td class="item_info">No result...</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<style scoped>
.dashboard_container {
  grid-column: span 12;
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  background-color: var(--light-blue-black);
  padding: 0.5rem;
  gap: 0.318rem;
  color: var(--rose);
  overflow-x: auto;
}

.f_filters {
  grid-column: span 1;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 1rem;
}

.file_list {
  grid-column: span 5;
  background-color: var(--dark-blue-black);
  border-radius: 5px;
  padding: 0.5rem;
}

.list_inject {}

.list_item {
  background-color: var(--light-blue-black);
}

.list_item:hover {
  cursor: pointer;
  background-color: var(--dark-blue-black);
  border: 1px solid white;
}

.item_info {
  text-align: center;
}

@media (max-width:600px) {
  .dashboard_container {
    grid-column: span 12;
  }

  .f_filters {
    padding: 0.5rem;
  }
}
</style>
