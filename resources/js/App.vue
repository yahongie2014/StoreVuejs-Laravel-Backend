<template>
    <div class="container">
        <div class="navbar">
            <div class="navbar__brand">
                <router-link to="/">SafwaSoft Product</router-link>
            </div>
            <ul class="navbar__list">
                <li class="navbar__item" v-if="guest">
                    <router-link to="/login">LOGIN</router-link>
                </li>
                <li class="navbar__item" v-if="guest">
                    <router-link to="/register">REGISTER</router-link>
                </li>
                <li class="navbar__item" v-if="auth">
                    <div class="cartid">
                        <router-link to="/cart-page">
                            <i class="icon-shopping-cart"><a class="counter">{{cartCount}}</a></i>
                        </router-link>
                    </div>
                </li>
                <li class="navbar__item" v-if="auth">
                    <a @click.stop="logout">LOGOUT</a>
                </li>
            </ul>
        </div>
        <div class="flash flash__error" v-if="flash.error">
            {{flash.error}}
        </div>
        <div class="flash flash__success" v-if="flash.success">
            {{flash.success}}
        </div>
        <loading
            :active.sync="isLoading"
            :can-cancel="true"
            :is-full-page="fullPage"></loading>
        <router-view></router-view>
    </div>
</template>
<script type="text/javascript">
    import Auth from './store/auth'
    import Flash from './helpers/flash'
    import {post, interceptors, get} from './helpers/api'
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    export default {
        data() {
            return {
                authState: Auth.state,
                flash: Flash.state,
                isLoading: false,
                fullPage: true
            }
        },
        created() {
            // global error http handler
            interceptors((err) => {
                if (err.response.status === 401) {
                    Auth.remove()
                    this.$router.push('/login')
                }

                if (err.response.status === 500) {
                    Flash.setError(err.response.statusText)
                }

                if (err.response.status === 404) {
                    this.$router.push('/not-found')
                }
            })
            Auth.initialize();
        },
        components: {
            Loading
        },
        computed: {
            auth() {
                if (this.authState.api_token) {
                    return true
                }
                return false
            },
            guest() {
                return !this.auth
            },
            cartCount() {
                if (this.authState.api_token) {

                    return this.authState.StoreCart;
                }
                return false
            },
        },
        methods: {
            logout() {
                this.isLoading = true
                post('/api/logout')
                    .then((res) => {
                        if (res.data.done) {
                            // remove token
                            Auth.remove()
                            this.isLoading = false
                            Flash.setSuccess('You have successfully logged out.')
                            this.$router.push('/login')
                        }
                    })
            },
            doAjax() {
                this.isLoading = true;
                setTimeout(() => {
                    this.isLoading = false
                }, 5000)
            },
        }
    }
</script>
