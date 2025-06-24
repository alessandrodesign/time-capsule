<template>
  <div v-if="capsula">
    <h1>Cápsula do Tempo</h1>
    <p><strong>Remetente:</strong> {{ capsula.nome_remetente }} ({{ capsula.email_remetente }})</p>
    <p><strong>Mensagem:</strong></p>
    <p>{{ capsula.mensagem }}</p>
    <p><strong>Data de Abertura:</strong> {{ capsula.data_abertura }}</p>
    <h3>Destinatários</h3>
    <ul>
      <li v-for="dest in capsula.destinatarios" :key="dest.contato">
        {{ dest.nome }} - {{ dest.tipo_contato }}: {{ dest.contato }}
      </li>
    </ul>
  </div>
  <p v-else>Carregando...</p>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const capsula = ref(null);

async function fetchCapsula() {
  try {
    const res = await fetch(`/capsulas/${route.params.id}`, {
      headers: {
        'Authorization': 'Bearer seu_token_simples_aqui'
      }
    });
    const data = await res.json();
    if (!res.ok) {
      alert(data.error || 'Erro ao carregar cápsula');
      return;
    }
    capsula.value = data;
  } catch {
    alert('Erro ao conectar com o servidor.');
  }
}

onMounted(fetchCapsula);
</script>