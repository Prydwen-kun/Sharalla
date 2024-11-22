<script setup>
import { onMounted } from 'vue';


const props = defineProps({
  gridSpan: String
})

onMounted(() => {

})

function toggleMenu() {
  const rect1 = document.getElementById('rect1')
  const rect2 = document.getElementById('rect2')
  const rect3 = document.getElementById('rect3')
  const menu = document.getElementById('menuBurger')
  const aside = document.getElementById('asideMenu')

  if (menu.classList.contains('closed')) {
    //opening menu
    menu.classList.remove('closed')
    menu.classList.add('opened')
    rect2.setAttribute('style', 'display: none')
    rect1.setAttribute('style', 'transform: rotate(45deg) translateY(7px)')
    rect3.setAttribute('style', 'transform: rotate(-45deg) translateY(-7px)')

    //handling aside
    aside.setAttribute('style', 'display: block')

  } else {
    //closing menu
    menu.classList.remove('opened')
    menu.classList.add('closed')
    rect2.setAttribute('style', 'display: block')
    rect1.setAttribute('style', 'transform: rotate(0deg)')
    rect3.setAttribute('style', 'transform: rotate(0deg)')

    //aside
    aside.setAttribute('style', 'display: none')
  }

}
</script>
<template>
  <div id="menuBurger" class="menu closed" @click="toggleMenu" :style="'grid-column={{gridSpan}}'">
    <div class="rect" id="rect1"></div>
    <div class="rect" id="rect2"></div>
    <div class="rect" id="rect3"></div>
  </div>
  <aside class="asideMenu" id="asideMenu">
    <button>Log out</button>
  </aside>
</template>
<style scoped>
.asideMenu {
  display: none;
  height: calc(100vh - 100px);
  min-width: 10rem;
  padding: 1rem;
  background-color: var(--dark-blue-black);
  position: absolute;
  top: 10rem;
  left: 0;
}

.menu {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 5px;
}

.menu:hover {
  background-color: var(--dark-rose);
}

.rect {
  width: 3rem;
  height: 0.5rem;
  background-color: var(--rose);
}
</style>
