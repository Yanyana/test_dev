<script setup>
import NavBar from './components/NavBar.vue'
import RequestForm from './components/RequestForm.vue'

</script>

<template>
  <header>
    <NavBar />
  </header>

  <main>
    <RequestForm />
  </main>
</template>

<style scoped>
header {
  line-height: 1.5;
}

</style>
