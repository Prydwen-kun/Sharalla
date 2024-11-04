<script setup>


</script>

<template>
  <form action="" method="post" enctype="application/x-www-form-urlencoded" class="searchField">
    <input type="text" id="search" class="searchBar" name="search">
    <button class="searchButton">Search</button>
  </form>
</template>

<style scoped>
.searchField{
  grid-column: 5/9;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.searchBar{
  border-radius: 1rem;
}

.searchButton{
  border-radius: 1rem;
  color: var(--rose);
  background-color: var(--light-blue-black);
  min-height: 2rem;
  min-width: 5rem;
  border: 1px solid var(--dark-rose);
}
.searchButton:hover{
  color: var(--white-mute);
  background-color: var(--dark-rose);
}
</style>
