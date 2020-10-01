<template>
    <div>
        <div v-if="items">
            <i v-bind="GetCartItems"></i>
            <router-link to="/">
                <button class="btn btn__primary">Continue Shooping</button>
            </router-link>
            <button class="btn btn__danger" @click="emptyCart(key)">Empty Cart</button>
            <hr>
            <p style="display: inherit;align-content: center;text-align: center;">Final Total : {{total}}</p>
            <loading
                :active.sync="isLoading"
                :can-cancel="true"
                :is-full-page="fullPage"></loading>
            <div class="col-lg-3" style="float:left" v-for="(qt, index) in items" v-bind:qt="qt">
                <br>
                <div class="card">
                    <button class="btn btn__danger" @click="TrashedFromCart(qt.cart_id,qt.quantity)">X</button>
                    <img class="card-img-top" :src="qt.image" alt="Card image" style="width:100%;height:100%;">
                    <div class="card-body">
                        <h4 class="text-center" type="count">{{ qt.price }} X {{qt.quantity}}</h4>
                        <hr>
                        <div class="btn_card">
                            <button class="btn btn__success" v-on:click="inc(qt)">+</button>
                            <span type="count" class="count_display" disabled>{{qt.quantity}}</span>
                            <button class="btn btn__danger" v-on:click="dec(qt)">-</button>
                        </div>
                        <h4 class="card-title">Name :{{ qt.name }}</h4>
                        <div>
                            <h4 class="card-text">Price : {{ qt.price }} SAR</h4>
                        </div>
                        <div>

                            <strong class="card-text">Total : {{getTotal(qt.price,qt.quantity)}} SAR</strong>
                            <hr>
                            <button class="btn btn__primary"
                                    v-model="total"
                                    v-on:click="updatQty(qt.quantity,qt.price,qt.cart_id)">
                                Confirm
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div v-if="empty">
            <h1 style="text-align: center;">No Product Here 😒 !</h1>
            <router-link to="/">
                <button style="margin-left: 400px;" class="btn btn__primary">Continue Shopping</button>
            </router-link>

        </div>
    </div>
</template>
<script>

    import {get, post, del} from "../../helpers/api";
    import Flash from "../../helpers/flash";
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import Auth from "../../store/auth";

    export default {
        data() {
            return {
                items: [],
                form: {
                    qty: []
                },
                cache: '',
                key: '',
                empty: false,
                count: 0,
                isLoading: false,
                fullPage: true,
            }
        },
        components: {
            Loading,
        },
        computed: {
            GetCartItems() {
                this.isLoading = true
                get('/api/cart')
                    .then((res) => {
                        this.isLoading = false
                        this.items = res.data.cart
                        this.key = res.data.cartKey
                        this.count = res.data.count
                        if (this.count == 0) {
                            this.isLoading = false
                            this.empty = true
                        }
                    })
            },

            total() {
                return this.items.reduce((acc, item) => acc + item.price * item.quantity, 0);
            }
        },
        methods: {
            TrashedFromCart(cartKey, qty) {
                this.isLoading = true
                let items = localStorage.getItem('cart')
                let sub = parseInt(items) - parseInt(qty)
                this.cache = cartKey
                del(`/api/cart/${this.cache}`)
                    .then((res) => {
                        if (res.data) {
                            this.cache = ''
                            this.isLoading = false
                            if (sub == null) {
                                Auth.setcountCart(0)
                            } else {
                                Auth.setcountCart(sub)
                            }
                            Auth.setcountCart(sub)
                            Flash.setSuccess("You have Successfully Delete Cart Item!")
                            this.$router.push('/')
                        }
                    })

            },

            updatQty(num, total, key) {
                this.isLoading = true
                this.form.qty.push({
                    key: key,
                    price: total,
                    quantity: num,
                })
                post(`/api/updateQty`, this.form.qty[0])
                    .then((res) => {
                        if (res.data) {
                            this.form.qty = [];
                            this.isLoading = false
                            Flash.setSuccess("You Cart Updated!")
                        }
                    })
            },

            emptyCart(key) {
                this.isLoading = true
                del(`/api/emptyCart/${key}`)
                    .then((res) => {
                        if (res.data) {
                            this.isLoading = false
                            Auth.RemovecountCart()
                            Flash.setSuccess("Your Card Is Empty!")
                            this.$router.push('/')
                        }
                    })
            },

            inc(qt) {
                qt.quantity++
            },

            dec(qt) {
                qt.quantity--
            },

            getTotal(price, qt) {
                return qt * price;
            },

        },
    };

</script>


<style scoped>
    #del {
        font-size: 20px;
        margin: 5px auto;
        cursor: pointer;
    }

    #del:hover {
        color: red;
    }

    .clear {
        width: 100%;
        height: 1px;
        clear: both;
    }

    .CheckOut {
        cursor: pointer;
    }
</style>
