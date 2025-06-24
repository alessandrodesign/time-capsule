<template>
  <form @submit.prevent="submit">
    <div>
      <label>Nome do Remetente:</label>
      <input v-model="form.nome_remetente" required />
    </div>
    <div>
      <label>E-mail do Remetente:</label>
      <input type="email" v-model="form.email_remetente" required />
    </div>
    <div>
      <label>Mensagem:</label>
      <textarea v-model="form.mensagem" required></textarea>
    </div>
    <div>
      <label>Data/Hora de Abertura:</label>
      <input type="datetime-local" v-model="form.data_abertura" required />
    </div>
    <div>
      <label>Destinatários:</label>
      <div v-for="(dest, index) in form.destinatarios" :key="index" style="margin-bottom: 10px;">
        <input placeholder="Nome" v-model="dest.nome" required />
        <input placeholder="Contato" v-model="dest.contato" required />
        <select v-model="dest.tipo_contato" required>
          <option value="email">Email</option>
          <option value="whatsapp">WhatsApp</option>
          <option value="telegram">Telegram</option>
          <option value="sms">SMS</option>
        </select>
        <button type="button" @click="removeDestinatario(index)">Remover</button>
      </div>
      <button type="button" @click="addDestinatario">Adicionar Destinatário</button>
    </div>
    <button type="submit">Criar Cápsula</button>
    <div v-if="errors.length" style="color: red;">
      <ul>
        <li v-for="(err, i) in errors" :key="i">{{ err }}</li>
      </ul>
    </div>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue';

const form = reactive({
  nome_remetente: '',
  email_remetente: '',
  mensagem: '',
  data_abertura: '',
  destinatarios: [
    { nome: '', contato: '', tipo_contato: 'email' }
  ],
});

const errors = ref([]);

function addDestinatario() {
  form.destinatarios.push({ nome: '', contato: '', tipo_contato: 'email' });
}

function removeDestinatario(index) {
  form.destinatarios.splice(index, 1);
}

async function submit() {
  errors.value = [];

  // Validação simples frontend
  if (!form.nome_remetente) errors.value.push('Nome do remetente é obrigatório.');
  if (!form.email_remetente || !/\S+@\S+\.\S+/.test(form.email_remetente)) errors.value.push('E-mail do remetente inválido.');
  if (!form.mensagem) errors.value.push('Mensagem é obrigatória.');
  if (!form.data_abertura) errors.value.push('Data de abertura é obrigatória.');
  else if (new Date(form.data_abertura) <= new Date()) errors.value.push('Data de abertura deve ser no futuro.');

  form.destinatarios.forEach((d, i) => {
    if (!d.nome) errors.value.push(`Nome do destinatário #${i + 1} é obrigatório.`);
    if (!d.contato) errors.value.push(`Contato do destinatário #${i + 1} é obrigatório.`);
    if (!['email', 'whatsapp', 'telegram', 'sms'].includes(d.tipo_contato)) errors.value.push(`Tipo de contato do destinatário #${i + 1} inválido.`);
  });

  if (errors.value.length) return;

  // Ajustar data para formato 'YYYY-MM-DD HH:mm:ss'
  const dt = new Date(form.data_abertura);
  const dataFormatada = dt.toISOString().slice(0, 19).replace('T', ' ');

  const payload = {
    nome_remetente: form.nome_remetente,
    email_remetente: form.email_remetente,
    mensagem: form.mensagem,
    data_abertura: dataFormatada,
    destinatarios: form.destinatarios,
  };

  try {
    const res = await fetch('/capsulas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer seu_token_simples_aqui'
      },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (!res.ok) {
      errors.value = data.errors || [data.error || 'Erro desconhecido'];
      return;
    }
    // Emitir evento para pai
    emit('created', data.id);
  } catch (e) {
    errors.value = ['Erro ao conectar com o servidor.'];
  }
}
</script>