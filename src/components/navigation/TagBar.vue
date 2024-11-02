<script setup>

</script>
<template>
  <div class="headerTagContainer">
      <ul class="tags">
        <li><a href="">Tag</a></li>
        <li><a href=""> OTHER tag</a></li>
        <li><a href="">Another tag</a></li>
        <li><a href="">tag</a></li>
      </ul>
    </div>
</template>
<style scoped>
.headerTagContainer {
  color: var(--light-rose);
  grid-column: 1/13;
  grid-row: span 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tags {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  width: 100%;
  list-style-type: none; /* Removes the bullet points */
  padding-left: 0; /* Removes the default padding */
  margin: 0;
}

.tags a {
  border-radius: 3px;
}
</style>
