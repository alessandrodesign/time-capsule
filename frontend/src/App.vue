<script setup>
import { RouterView, RouterLink } from 'vue-router';
</script>

<template>
  <div>
    <nav>
      <RouterLink to="/">Início</RouterLink> |
      <RouterLink to="/capsulas">Minhas Cápsulas</RouterLink>
    </nav>
    <main>
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
nav {
  margin-bottom: 1rem;
}

nav a {
  text-decoration: none;
  color: #42b983;
  font-weight: bold;
  margin-right: 1rem;
}

nav a.router-link-exact-active {
  color: #35495e;
}
</style>