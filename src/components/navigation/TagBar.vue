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
  display: block;
  height: 100%;
  width: 100vw;
}

.tags {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  list-style-type: none;
  /* Removes the bullet points */
  padding: 0;
  /* Removes the default padding */
  margin: 0;
}

.tags a {
  height: 100%;
  border-radius: 3px;
}

.tags a:hover {
  color: var(--white-mute);
}

.tags>li {
  grid-column: span 1;
  padding: 0 0.5rem;
}
</style>
