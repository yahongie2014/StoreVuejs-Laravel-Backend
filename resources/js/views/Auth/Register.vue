<template>
    <div class="auth-component">

        <form class="form" @submit.prevent="register">
            <h1 class="form__title">Create an Account</h1>
            <div class="form__group" v-if="error.length">
                <b>Please correct the following error(s):</b>
                <ul>
                    <li v-for="errors in error">{{ errors }}</li>
                </ul>
            </div>
            <loading
                :active.sync="isProcessing"
                :can-cancel="true"
                :is-full-page="fullPage"></loading>
            <div class="form__group">
                <label>Name English</label>
                <input type="text" class="form__control" v-model="form.name">
                <small class="error__control" v-if="error.name">{{error.name[0]}}</small>
            </div>
            <div class="form__group">
                <label>Name Arabic</label>
                <input type="text" class="form__control" v-model="form.name_ar" @keypress="preventEnglishInput"
                       @input="handleInput(form.name_ar)">
                <small class="error__control" v-if="msg.name_ar">{{msg.name_ar}}</small>
            </div>
            <div class="form__group">
                <label>Birth Date</label>
                <date-picker
                    v-model="form.date"
                    :locale="'ar-sa'"
                    :calendar="'hijri'"
                    input-class="form-control form-control-lg"
                ></date-picker>
                <small class="error__control" v-if="error.date">{{error.date[0]}}</small>
            </div>

            <div class="form__group">
                <label>Mobile</label>
                <input type="text" class="form__control" v-model="form.phone">
                <small class="error__control" v-if="error.phone">{{error.phone[0]}}</small>
            </div>
            <div class="form__group">
                <label>Password</label>
                <input type="password" class="form__control" v-model="form.password">
                <small class="error__control" v-if="error.password">{{error.password[0]}}</small>
            </div>
            <div class="form__group">
                <label>Confirm Password</label>
                <input type="password" class="form__control" v-model="form.password_confirmation">
            </div>
            <div class="form__group">
                <button class="btn btn__primary">Register</button>
            </div>
        </form>
        <SocialLogin/>

    </div>
</template>
<script type="text/javascript">
    import Flash from '../../helpers/flash'
    import {post} from '../../helpers/api'
    import VueDatetimeJs from 'vue-datetime-js'
    import Loading from "vue-loading-overlay";
    import 'vue-loading-overlay/dist/vue-loading.css';
    import SocialLogin from "../../Component/SocialLogin";

    export default {
        data() {
            return {
                form: {
                    name: '',
                    name_ar: '',
                    phone: '',
                    date: '',
                    password: '',
                    password_confirmation: ''
                },
                msg: [],
                error: [],
                isProcessing: false,
                fullPage: true,
            }
        },
        watch: {
            name_ar(value) {
                this.name_ar = value;
                this.validateArabic(value);
            }
        },
        components: {
            datePicker: VueDatetimeJs,
            Loading,
            SocialLogin: SocialLogin
        },
        methods: {
            isEnglish(charCode) {
                return (charCode >= 97 && charCode <= 122)
                    || (charCode >= 65 && charCode <= 90);
            },
            preventEnglishInput($event) {
                if (this.isEnglish($event.charCode)) {
                    $event.preventDefault();
                }
            },
            handleInput(value) {
                if (!this.isEnglish(value)) {
                    Flash.setSuccess("Great Continue")
                } else {
                    Flash.setError("Not Arabic Character")
                }

            },
            validateArabic(value) {
                let arabic = /[\u0600-\u06FF]/;
                if (arabic.test(value)) {
                    this.msg['name_ar'] = '';
                } else {
                    this.msg['name_ar'] = 'Invalid Arabic Name';
                }
            },
            register: function (e) {
                this.isProcessing = true
                this.error = []
                var arabic = /[\u0600-\u06FF]/;
                if (!arabic.test(this.name_ar)) {
                    this.error.push("Arabic Name Must Be Arabic.");
                }
                if (!this.name) {
                    this.error.push("Name English required.");
                }
                if (!this.date) {
                    this.error.push('Birth Date required.');
                }
                if (!this.phone) {
                    this.error.push('Phone required.');
                }
                if (!this.name_ar) {
                    this.error.push("Name Arabic required.");
                }
                if (!this.password) {
                    this.error.push("Password required.");
                }
                if (!this.password_confirmation) {
                    this.error.push("Password Confirmation required.");
                }

                if (!this.error.length) {
                    return true;
                }
                post('api/register', this.form)
                    .then((res) => {
                        if (res.data.registered) {
                            Flash.setSuccess('Congratulations! You have now successfully registered.')
                            this.$router.push('/login')
                        }
                        this.isProcessing = false
                    })
                    .catch((err) => {
                        if (err.response.status === 422 || err.response.status === 500) {
                            Flash.setError("Invalid Data !")
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

        },
    }
</script>
