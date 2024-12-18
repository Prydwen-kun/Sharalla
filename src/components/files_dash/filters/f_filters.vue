<script setup>
// NO AUTO FORMAT << character
import config from '../../../../config/config';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter()
const emit = defineEmits(['update_filter', 'page_plus', 'page_minus', 'clear_filters'])

onMounted(() => {

})

function list_f_update() {
  emit('update_filter')
}

function p_plus() {
  emit('page_plus')
}
function p_minus() {
  emit('page_minus')
}
function clear_filt() {
  emit('clear_filters')
}
</script>
<template>
  <div class="u_filter">
    <div class="search_bar">
      <input type="text" class="u_search" name="search" id="u_search" placeholder="Search...">
      <button @click="list_f_update" class="u_search_button">></button>
    </div>
  </div>
  <div class="u_filter">
    <div>Order by</div>
    <div>
      <select @change="list_f_update" name="orderBy" id="orderBy" class="filter_select">
        <option value="" disabled>---Select a filter---</option>
        <option :value="value" v-for="(value, index) in config.f_filter" :key="index">{{ value }}</option>
      </select>
    </div>
  </div>
  <div class="u_filter">
    <div id="displayed_num">Displayed</div>
    <div><input @change="list_f_update" id="l_slider" class="l_size_slider" type="range" min="5" max="100" step="5"
        value="10" name="list_size"></div>
  </div>
  <div class="u_filter">
    <p class="pagin_title">--Page--</p>
    <div class="pagination">
      <button class="page_button" @click="p_minus">Prev</button>
      <input type="number" class="page_input" name="page_number" id="page_number" value="1" min="1">
      <button class="page_button" @click="p_plus">Next</button>
    </div>
  </div>
  <div class="u_filter c_button_container">
    <button @click="clear_filt" class="clear_button">Clear</button>
  </div>
</template>
<style scoped>
.filter_select {
  background-color: var(--light-blue-black);
  color: var(--rose);
  border-radius: 5px;
  min-height: 4rem;
  padding: 0.25rem;
}

.filter_select:hover {
  cursor: pointer;
  border: 2px solid var(--light-rose);
}

.filter_select:focus {
  outline: none;
  border: 3px solid var(--rose);
}

.no_padding {
  padding: 0;
}

.l_size_slider {
  appearance: none;
  -webkit-appearance: none;
  width: 100%;
  height: 0.75rem;
  border-radius: 3px;
  background: var(--light-blue-black);
  outline: var(--rose) solid 2px;
  opacity: 0.7;
  transition: opacity .2s
}

.l_size_slider:hover {
  opacity: 1;
}

.l_size_slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  /* Remove default appearance */
  appearance: none;
  width: 1.5rem;
  height: 2.5rem;
  background: var(--rose);
  cursor: pointer;
  border-radius: 25%;
}

.l_size_slider::-moz-range-thumb {
  width: 1.5rem;
  height: 2.5rem;
  background: var(--rose);
  cursor: pointer;
  border-radius: 25%;
}

.pagin_title {
  text-align: center;
  width: 100%;
}

.pagination {
  display: flex;
  flex-direction: row;
  margin-top: 0.5rem;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
}

.page_button {
  border: 1px solid var(--rose);
  border-radius: 5px;
  background-color: var(--light-blue-black);
  color: var(--rose);
  width: 4.5rem;
  height: 4rem;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 2rem;
}

.page_button:hover {
  cursor: pointer;
  background-color: var(--light-rose);
  color: var(--white-mute);
}

.page_input {
  width: 4rem;
  height: 3.5rem;
  border: none;
  border-bottom: 3px solid var(--light-blue-black);
  color: var(--rose);
  background-color: var(--dark-blue-black);
  text-align: center;
  font-size: 1.5rem;
}

.page_input:focus {
  outline: none;
  background-color: var(--light-blue-black);
  border-radius: 5px;
  border-bottom: 3px solid var(--light-rose);
}

/* Hide the arrows for WebKit browsers */
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* For a more consistent look, also hide the arrows in Firefox */
input[type=number] {
  -moz-appearance: textfield;
}

.search_bar {
  display: flex;
  flex-direction: row;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.u_search {
  background-color: var(--dark-blue-black);
  border: none;
  border-bottom: 2px solid var(--rose);
  color: var(--rose);
  font-size: 1.5rem;
  padding: 0;
  max-width: 8rem;
}

.u_search::placeholder {
  color: var(--dark-rose);
}

.u_search:focus {
  outline: none;
  background-color: var(--light-blue-black);
  border-radius: 5px;
}

.u_search_button {
  color: var(--rose);
  border: 2px solid var(--rose);
  border-radius: 50%;
  background-color: var(--dark-blue-black);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 4rem;
  width: 4rem;
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.5rem;
  text-align: center;
}

.u_search_button:hover {
  background-color: var(--light-rose);
  color: var(--white-mute);
  cursor: pointer;
}

.c_button_container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}

.clear_button {
  color: var(--rose);
  background-color: var(--dark-blue-black);
  border-radius: 2rem;
  border: 2px solid var(--rose);
  height: 4rem;
  width: 8rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.clear_button:hover {
  color: var(--white-mute);
  background-color: var(--light-rose);
  cursor: pointer;
}
</style>
