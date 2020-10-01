<template>
    <div class="social-media">
        <button class="fb_btn" @click="AuthProvider('facebook')">
            <i class="fa fa-facebook fa-fw"></i> Login with Facebook
        </button>
        <button class="google_btn" @click="AuthProvider('google')">
            <i class="fa fa-google fa-fw"></i> Login with Google+
        </button>
    </div>
</template>

<script>
    import Loading from "vue-loading-overlay";
    import {post} from "../helpers/api";
    import Auth from "../store/auth";
    import Flash from "../helpers/flash";

    export default {
        name: "SocialLogin",
        components: {
            Loading
        },

        methods: {
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

<style scoped>

</style>
