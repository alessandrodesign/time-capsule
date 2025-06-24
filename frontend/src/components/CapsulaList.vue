<template>
  <div>
    <input v-model="email" placeholder="Digite seu e-mail" />
    <button @click="fetchCapsulas">Buscar</button>

    <ul v-if="capsulas.length">
      <li v-for="capsula in capsulas" :key="capsula.id">
        <a href="#" @click.prevent="selectCapsula(capsula.id)">
          {{ capsula.nome_remetente }} - {{ capsula.data_abertura }}
        </a>
      </li>
    </ul>
    <p v-else>Nenhuma cápsula encontrada.</p>
  </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue';

const email = ref('');
const capsulas = ref([]);
const emit = defineEmits(['selected']);

async function fetchCapsulas() {
  if (!email.value) {
    alert('Informe seu e-mail para buscar as cápsulas.');
    return;
  }
  try {
    const res = await fetch(`/capsulas?email=${encodeURIComponent(email.value)}`, {
      headers: {
        'Authorization': 'Bearer seu_token_simples_aqui'
      }
    });
    const data = await res.json();
    if (!res.ok) {
      alert(data.error || 'Erro ao buscar cápsulas');
      return;
    }
    capsulas.value = data;
  } catch {
    alert('Erro ao conectar com o servidor.');
  }
}

function selectCapsula(id) {
  emit('selected', id);
}
</script>