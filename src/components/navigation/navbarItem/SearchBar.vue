<script setup>


</script>

<template>
  <form action="" method="post" enctype="application/x-www-form-urlencoded" class="searchField">
    <input type="text" id="search" class="searchBar" name="search">
    <button class="searchButton">Search</button>
  </form>
</template>

<style scoped>
.searchField {
  grid-column: 3/7;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.searchBar {
  border-top: none;
  border-left: none;
  border-right: none;
  border-bottom: 1px solid var(--dark-blue-black);
  background-color: var(--light-blue-black);
  color: var(--rose);
  min-width: max-content;
  outline: none;
  padding: 0.5rem;
}

.searchBar:focus {
  background-color: var(--dark-blue-black);
}

.searchButton {
  border-radius: 2rem;
  color: var(--rose);
  background-color: var(--light-blue-black);
  min-height: 4rem;
  min-width: 8rem;
  border: 1px solid var(--dark-rose);
}

.searchButton:hover {
  color: var(--white-mute);
  background-color: var(--dark-rose);
}
</style>
