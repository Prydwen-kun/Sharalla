<script setup>

const props = defineProps({
  button_id: String,
  button_text: String,
  button_link: String,
  button_class: String
})

</script>
<template>
  <a :href="button_link" :class="'inner_anchor '.concat(button_class)" :id="button_id">{{ button_text }}</a>
</template>
<style scoped>
.inner_anchor {
  display: flex;
  align-items: center;
  justify-content: center;
  height: max-content;
  width: max-content;
}

.form_button {
  border-radius: 2rem;
  color: var(--rose);
  background-color: var(--light-blue-black);
  min-height: 4rem;
  min-width: 8rem;
  border: 1px solid var(--dark-rose);
}

.form_button:hover {
  color: var(--white-mute);
  background-color: var(--dark-rose);
}

.link_button {
  color: var(--rose);
}

.link_button:hover {
  color: var(--white-mute);
  background-color: var(--dark-rose);
}
</style>
