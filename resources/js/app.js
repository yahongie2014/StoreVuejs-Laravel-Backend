import Vue from 'vue'
import App from './App.vue'
import router from './router'
import VueDatetimeJs from 'vue-datetime-js';
import Paginate from 'vuejs-paginate';
import VueMaterial from 'vue-material';
import 'vue-material/dist/vue-material.min.css';
import Vuex from 'vuex';
import storeData from "./store/auth";
import {Settings} from 'luxon'
import VueAxios from 'vue-axios'
import VueSocialauth from 'vue-social-auth'
import axios from 'axios';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserSecret } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
Settings.defaultLocale = 'ar-SA'
Vue.use(Vuex)

Vue.use(VueMaterial);

Vue.use(VueAxios, axios)

Vue.use(VueSocialauth, {

    providers: {
        github: {
            clientId: '',
            redirectUri: '/auth/github/callback' // Your client app URL
        }, facebook: {
            clientId: '2554097754684287',
            clientSecret: 'c1cec279cdcb7695b612944e5834b082',
            redirectUri: 'http://localhost:8000/auth/facebook/callback' // Your client app URL
        },
        google: {
            clientId: '504183812000-4sfvhhng7vnp6e1ee97bk234un8q4dg1.apps.googleusercontent.com',
            clientSecret: 'QXw3PMmgzUonLbjklhAl-3Yc',
            redirectUri: 'http://localhost:8000/auth/google/callback' // Your client app URL
        },

    }
})

Vue.component('paginate', Paginate);
const store = new Vuex.Store(
    storeData
)

const app = new Vue({
    el: '#root',
    template: `
        <app></app>`,
    components: {App},
    store,
    router
})
