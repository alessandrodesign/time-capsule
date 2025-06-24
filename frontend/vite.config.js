import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    watch: {
      usePolling: true,         // Ativa polling (útil para VM/Docker)
      interval: 100             // Tempo entre verificações (ms)
    },
    host: true,                 // Permite acesso por IP da rede
    port: 5173,                 // Porta padrão do Vite (pode mudar se quiser)
    strictPort: true,          // Falhar se a porta estiver em uso
    open: true                 // Abre o navegador automaticamente
  }
})
