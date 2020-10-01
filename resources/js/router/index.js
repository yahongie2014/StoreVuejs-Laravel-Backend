import Vue from 'vue'
import VueRouter from 'vue-router'

import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'
import ProductIndex from '../views/Products/Index.vue'
import ProductShow from '../views/Products/Show.vue'
import NotFound from '../views/NotFound.vue'
import Checkout from '../views/Products/Checkout'

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [
        {path: '/', component: ProductIndex},
        {path: '/product/:id', component: ProductShow},
        {path: '/register', component: Register},
        {path: '/login', component: Login},
        {path: '/cart-page', component: Checkout},
        {path: '/not-found', component: NotFound},
        {path: '*', component: NotFound},
        {
            path: '/auth/:provider/callback',
            component: ProductIndex
        },
    ]
})

export default router
