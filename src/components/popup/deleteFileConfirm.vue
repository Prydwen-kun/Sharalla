<script setup>

const emit = defineEmits(['closePopup', 'deleteConfirm'])

function closePopup() {
  emit('closePopup')
}

function delete_confirm() {
  emit('deleteConfirm')
}
</script>
<template>
  <div class="popwrapper">
    <div class="delete_popup">
      <div>Confirm deletion ?</div>
      <div class="buttons">
        <button class="deletion_button button" @click="delete_confirm">YES</button>
        <button class="cancel_button button" @click="closePopup">CANCEL</button>
      </div>
    </div>
    <div class="blocker" @click="closePopup"></div>
  </div>
</template>
<style scoped>
.popwrapper {
  position: absolute;
  top: 0;
  height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.delete_popup {
  z-index: 106;
  width: 25rem;
  height: 18rem;
  background-color: var(--dark-blue-black);
  color: var(--rose);
  border: 3px solid var(--rose);
  border-radius: 5px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 2rem;
}

.buttons {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.deletion_button {
  color: rgb(199, 37, 37);
}

.cancel_button {
  color: rgb(0, 173, 0);
}

.button {
  background-color: var(--dark-blue-black);
  border: 1px solid var(--rose);
  height: 4rem;
  width: 8rem;
  border-radius: 2rem;
  font-weight: 600;
}

.button:hover {
  background-color: var(--light-rose);
  /* color: var(--white-mute); */
}

.blocker {
  position: absolute;
  height: 100vh;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.449);
}
</style>
