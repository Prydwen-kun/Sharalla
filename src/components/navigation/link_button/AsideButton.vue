<script setup>

const props = defineProps({
  button_id: String,
  button_text: String,
  button_link: String,
  button_class: String,
  anchor_class: String
})

</script>
<template>
  <button :class="button_class">
    <a :href="button_link" :class="'inner_anchor '.concat(anchor_class)" :id="button_id">{{ button_text }}</a>
  </button>
</template>
<style scoped>
.inner_anchor {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100%;
  min-width: 100%;
  color: var(--rose);
  transition: all 0.25s ease-in;
}

.inner_anchor:hover {
  color: var(--white-mute);
}
</style>
