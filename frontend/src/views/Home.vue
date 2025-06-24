<template>
  <div>
    <h1>Criar Cápsula do Tempo</h1>
    <CapsulaForm @created="onCreated" />
  </div>
</template>

<script setup>
import CapsulaForm from '../components/CapsulaForm.vue';
import { useRouter } from 'vue-router';

const router = useRouter();

function onCreated(id) {
  alert('Cápsula criada com sucesso! ID: ' + id);
  router.push('/capsulas');
}
</script>