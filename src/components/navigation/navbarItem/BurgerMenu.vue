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

  //html body
  const htmlElementCollection = document.getElementsByTagName('html')
  const htmlElement = htmlElementCollection.item(0)

  if (menu.classList.contains('closed')) {
    //opening menu
    menu.classList.remove('closed')
    menu.classList.add('opened')
    rect2.setAttribute('style', 'display: none')
    rect1.setAttribute('style', 'transform: rotate(45deg) translateY(7px)')
    rect3.setAttribute('style', 'transform: rotate(-45deg) translateY(-7px)')

    //handling aside
    aside.setAttribute('style', 'left: 0')

  } else {
    //closing menu
    menu.classList.remove('opened')
    menu.classList.add('closed')
    rect2.setAttribute('style', 'display: block')
    rect1.setAttribute('style', 'transform: rotate(0deg)')
    rect3.setAttribute('style', 'transform: rotate(0deg)')

    if (htmlElement.clientWidth <= 600) {
      aside.setAttribute('style', 'left: -100vw')
    } else {
      //aside
      aside.setAttribute('style', 'left: -16.6vw')
    }
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
    <button class="asideButtons">Home</button>
    <button class="asideButtons">Profile</button>
    <button class="asideButtons">Settings</button>
    <button class="asideButtons">Friends</button>
    <button class="asideButtons">Log out</button>
  </aside>
</template>
<style scoped>
.asideMenu {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  height: calc(100vh - 6rem);
  min-width: 16.6vw;
  padding: 1rem;
  background-color: var(--light-blue-black);
  position: absolute;
  top: 6rem;
  left: -16.6vw;
  transition: all 0.5s ease-in-out;
}

.asideButtons {
  border: none;
  color: var(--rose);
  background-color: var(--dark-blue-black);
  min-height: 4rem;
}

.asideButtons:hover {
  background-color: var(--dark-rose);
  color: var(--white-mute);
  border: 3px solid var(--dark-blue-black);
  border-radius: 3px;
  transition: all 0.25s ease-in;
}

.menu {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 5px;
  padding: 1rem;
}

.menu:hover {
  background-color: var(--dark-rose);
}

.rect {
  width: 3rem;
  height: 0.5rem;
  background-color: var(--rose);
  transition: all 0.25s ease-in;
}

@media (max-width:600px) {
  .asideMenu {
    min-width: 100vw;
    left: -100vw;
  }
}
</style>
