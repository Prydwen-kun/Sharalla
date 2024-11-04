<script setup>

</script>
<template>
  <div class="headerTagContainer">
      <ul class="tags">
        <li><a href="">Tag</a></li>
        <li><a href=""> OTHER tag</a></li>
        <li><a href="">Another tag</a></li>
        <li><a href="">Placeholder</a></li>
        <li><a href="">Placeholder</a></li>
        <li><a href="">Placeholder</a></li>
        <li><a href="">Placeholder</a></li>
        <li><a href="">Placeholder</a></li>
        <li><a href="">Placeholder</a></li>
        <li><a href="">Placeholder</a></li>
        <li><a href="">Placeholder</a></li>
      </ul>
    </div>
</template>
<style scoped>
.headerTagContainer {
  color: var(--light-rose);
  grid-column: 1/13;
  grid-row: span 2;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: space-evenly;
  align-items: center;
  width: 100%;
  height: 100%;
  list-style-type: none; /* Removes the bullet points */
  padding-left: 0; /* Removes the default padding */
  margin: 0;
}

.tags a {
  height: 100%;
  border-radius: 3px;
}
.tags a:hover {
color: var(--white-mute);
}
</style>
