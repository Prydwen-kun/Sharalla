<script setup>
// create param for item title and route
const props = defineProps({
  linkTitle: String,
  link: String,
  gridSpan: String,
  icon: String,
}
)

</script>

<template>
  <li class="navItem" :style="{ '--gridSpan': gridSpan }">
    <router-link :to="link" class="nav-link">
      <img :src="icon" alt="User avatar" :title="linkTitle" class="userIcon" height="40" width="40">
    </router-link>
  </li>
</template>
<style scoped>
.navItem {
  grid-column: var(--gridSpan);
}

.nav-link {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  width: 100%;
}

.nav-link:hover {
  color: var(--white-mute);
}

.userIcon {
  background-color: var(--white-mute);
  border-radius: 50%;
}
</style>
