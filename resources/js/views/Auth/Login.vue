<template>
    <div class="auth-component">
        <form class="form" @submit.prevent="login">
            <h1 class="form__title">Welcome back!</h1>
            <div class="form__group">
                <label>Phone</label>
                <input type="text" class="form__control" v-model="form.phone">
                <small class="error__control" v-if="error.phone">{{error.phone[0]}}</small>
            </div>
            <loading
                :active.sync="isProcessing"
                :can-cancel="true"
                :is-full-page="fullPage"></loading>
            <div class="form__group">
                <label>Password</label>
                <input type="password" class="form__control" v-model="form.password">
                <small class="error__control" v-if="error.password">{{error.password[0]}}</small>
            </div>
            <div class="form__group">
                <button :disabled="isProcessing" class="btn btn__primary">Login</button>
            </div>
        </form>
        <SocialLogin/>

    </div>

</template>
<script type="text/javascript">
    import Flash from '../../helpers/flash'
    import Auth from '../../store/auth'
    import {post} from '../../helpers/api'
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import SocialLogin from "../../Component/SocialLogin";

    export default {
        data() {
            return {
                form: {
                    phone: '',
                    password: ''
                },
                error: {},
                isProcessing: false,
                fullPage: true
            }
        },

        components: {
            Loading,
            SocialLogin
        },

        methods: {
            login() {
                this.isProcessing = true
                this.error = {}
                post('api/login', this.form)
                    .then((res) => {
                        if (res.data.authenticated) {
                            // set token
                            Auth.set(res.data.api_token, res.data.user_id)
                            Flash.setSuccess('You have successfully logged in.')
                            this.$router.push('/')
                        }
                        this.isProcessing = false
                    })
                    .catch((err) => {
                        if (err.response.status === 422) {
                            this.error = err.response.data
                            Flash.setError('Please Check Phone Or Password.')

                        }
                        this.isProcessing = false
                    })
            },

            doAjax() {
                this.isProcessing = true;
                setTimeout(() => {
                    this.isProcessing = false
                }, 5000)
            },

            AuthProvider(provider) {

                var self = this

                this.$auth.authenticate(provider).then(response => {
                    self.SocialLogin(provider, response)
                }).catch(err => {
                    console.log({err: err})
                })

            },

            SocialLogin(provider, response) {
                this.isProcessing = true
                this.error = {}
                this.$http.post('/api/socialLogin/' + provider, response).then(response => {
                    if (response.data.authenticated) {
                        // set token
                        Auth.set(response.data.api_token, response.data.user_id)
                        Flash.setSuccess('You have successfully logged in.')
                        this.$router.push('/')
                    }
                    this.isProcessing = false

                }).catch(err => {
                    if (err.response.status === 422) {
                        this.error = err.response.data
                        Flash.setError('Please Check Phone Or Password.')

                    }
                    this.isProcessing = false
                })
            },

        }

    }
</script>
