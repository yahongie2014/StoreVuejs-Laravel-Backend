<template>
    <div class="recipe__show">
        <div class="recipe__row">
            <div class="recipe__image">
                <div class="recipe__box">
                    <img :src="`${product.Image}`" v-if="product.Image">
                </div>
            </div>
            <loading
                :active.sync="isLoading"
                :can-cancel="true"
                :is-full-page="fullPage"></loading>
            <div class="recipe__details">
                <div class="recipe__details_inner">
                    <small>Submitted by: SafwaSoft</small>
                    <h1 class="recipe__title">{{product.Name}}</h1>
                    <p class="recipe__description">{{product.Desc}}</p>
                    <p class="recipe__price">Price : {{product.Price}} SAR</p>
                    <button class="btn btn__danger" @click="addtocart(product)">Add To Cart</button>
                </div>

            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Auth from '../../store/auth'
    import {get, post} from '../../helpers/api'
    import Flash from '../../helpers/flash'
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        data() {
            return {
                authState: Auth.state,
                isRemoving: false,
                product: {},
                form: {
                    cart: []
                },
                isLoading: false,
                fullPage: true

            }
        },
        components: {
            Loading
        },
        created() {
            get(`/api/products/${this.$route.params.id}`)
                .then((res) => {
                    this.product = res.data.data[0]
                })
        },
        methods: {
            addtocart(product) {
                this.isLoading = true
                this.form.cart.push({
                    id: this.$route.params.id,
                    name: product.Name,
                    price: product.Price,
                    image: product.Image,
                })
                let items = localStorage.getItem('cart')
                let test = Object.keys(JSON.stringify(product.Identifier)).length
                post(`/api/cart`, this.form.cart[0])
                    .then((res) => {
                        if (res.data) {
                            this.isLoading = false
                            if (items == null) {
                                let sub = test
                                Auth.setcountCart(sub)
                            } else {
                                let sub = parseInt(items) + parseInt(test)
                                Auth.setcountCart(sub)
                            }
                            Flash.setSuccess("You have Successfully add Product To Cart!"
                            )
                        }
                    })
            },
        },
        computed: {
            products() {
                return this.$store.getters.products;
            }
        }


    }
</script>
