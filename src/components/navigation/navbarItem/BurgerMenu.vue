<script setup>
import { onMounted } from 'vue';
import AsideButton from '../link_button/AsideButton.vue';
import AlertWindow from '@/components/alerts/AlertWindow.vue';

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
  const shadow_blocker = document.getElementById('shadow_blocker')

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
    shadow_blocker.setAttribute('style', 'display: block')

    //handling aside
    aside.setAttribute('style', 'left: 0')

  } else {
    //closing menu
    menu.classList.remove('opened')
    menu.classList.add('closed')
    rect2.setAttribute('style', 'display: block')
    rect1.setAttribute('style', 'transform: rotate(0deg)')
    rect3.setAttribute('style', 'transform: rotate(0deg)')
    shadow_blocker.setAttribute('style', 'display: none')

    if (htmlElement.clientWidth <= 600) {
      aside.setAttribute('style', 'left: -100vw')
    } else {
      //aside
      aside.setAttribute('style', 'left: -16.6vw')
    }
  }

}

function logout() {
  //display confirmation box to logout
  const alert_popup = document.getElementById('alert_popup')
  const alert_blocker = document.getElementById('alert_blocker')
  alert_popup.setAttribute('style', 'display: flex')
  alert_blocker.setAttribute('style', 'display: block')
}
</script>
<template>
  <div id="menuBurger" class="menu closed" @click="toggleMenu" :style="'grid-column={{gridSpan}}'">
    <div class="rect" id="rect1"></div>
    <div class="rect" id="rect2"></div>
    <div class="rect" id="rect3"></div>
  </div>
  <aside class="asideMenu" id="asideMenu">
    <AsideButton class="asideButtons" :button_link="'/'" :button_text="'Home'" :button_id="'asideHome'" />
    <AsideButton class="asideButtons" :button_link="'/'" :button_text="'Profile'" :button_id="'asideProfile'" />
    <AsideButton class="asideButtons" :button_link="'/'" :button_text="'Settings'" :button_id="'asideSettings'" />
    <AsideButton class="asideButtons" :button_link="'/'" :button_text="'Friends'" :button_id="'asideFriends'" />
    <button class="asideButtons" @click="logout" id="logoutButton">Log out</button>
  </aside>
  <div class="shadow_blocker" id="shadow_blocker" @click="toggleMenu"></div>
  <AlertWindow :alert-title="'Confirm'" :alert-text="'Do you want to log out ?'" :alert-call="'alert_confirm_logout'" />
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
  z-index: 99;
}

.asideButtons {
  border: none;
  color: var(--rose);
  background-color: var(--dark-blue-black);
  min-height: 4rem;
  padding: 0;
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
  z-index: 100;
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

.shadow_blocker {
  display: none;
  position: absolute;
  z-index: 98;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.31);
}

@media (max-width:600px) {
  .asideMenu {
    min-width: 100vw;
    left: -100vw;
  }
}
</style>
