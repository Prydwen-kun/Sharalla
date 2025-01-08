<script setup>
import axios from 'axios';
import config from '../../../config/config';
import { useRoute, useRouter } from 'vue-router';
import { computed, onMounted, ref } from 'vue';
import AdminPanel from '@/components/admin/adminPanel.vue';

const router = useRouter()
const route = useRoute()

const userId = computed(() => route.params.id)
let admin = ref(false)


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
    //request connected user data
    const response0 = await axios.post(`${config.APIbaseUrl}${config.endpoints.getConnectedUserData}`)

    const data0 = await response0.data
    if (data0.response.rank === 'ADMIN') {
      admin.value = true

    } else {
      router.push('/dashboard')
    }
  }

})

</script>
<template>
  <Suspense>
    <section class="admin_view">
      <AdminPanel :user-id="userId" />
    </section>
  </Suspense>

</template>
<style scoped>
.admin_view {
  grid-column: span 12;
  background-color: var(--dark-blue-black);
}
</style>
