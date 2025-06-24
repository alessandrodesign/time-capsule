import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Listagem from '../views/Listagem.vue';
import Visualizacao from '../views/Visualizacao.vue';

const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/capsulas', name: 'Listagem', component: Listagem },
    { path: '/capsulas/:id', name: 'Visualizacao', component: Visualizacao, props: true },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;