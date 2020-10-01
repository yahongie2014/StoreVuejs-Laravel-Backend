<template>
    <div>
        <div class="recipe__list" v-if="auth">
            <div class="recipe__wrapper">
                <input class="recipe__search" type="text" v-model="search" placeholder="Search title.."/>
                <br>
                <br>
            </div>
            <loading
                :active.sync="isLoading"
                :can-cancel="true"
                :is-full-page="fullPage"></loading>

            <div class="recipe__item" v-for="(product, index) in filteredList">
                <router-link class="recipe__inner" :to="`/product/${product.Identifier}`">
                    <img :src="`${product.Image}`" v-if="product.Image">
                    <p class="recipe__name">Name: {{product.Name}}</p>
                    <p class="recipe__price">Price : {{product.Price}} SAR</p>
                </router-link>
                <div class="recipe__order">
                    <button class="btn btn__danger" @click="retrieveList(product)"
                            v-on:click="addToCart(product)">Add To Cart
                    </button>
                </div>
            </div>
            <paginate
                :page-count="count"
                :margin-pages="2"
                :page-range="4"
                :container-class="'pagination'"
                :page-class="'page-item'"
                :page-link-class="'page-link-item'"
                :prev-class="'prev-item'"
                :prev-link-class="'prev-link-item'"
                :next-class="'next-item'"
                :next-link-class="'next-link-item'"
                :break-view-class="'break-view'"
                :break-view-link-class="'break-view-link'"
                :click-handler="clickCallback">
            <span slot="breakViewContent">
          <svg width="16" height="4" viewBox="0 0 16 4">
            <circle fill="#999999" cx="2" cy="2" r="2"/>
            <circle fill="#999999" cx="8" cy="2" r="2"/>
            <circle fill="#999999" cx="14" cy="2" r="2"/>
          </svg>
        </span>
            </paginate>

        </div>
        <div v-if="!auth">
            <h1 style="text-align: center;">You Are Not Login ! 🤦‍♂️</h1>
            <hr>
            <router-link to="/login">
                <button style="margin-left: 410px;width: 22%;" class="btn btn__primary">Login</button>
            </router-link>
        </div>
    </div>

</template>
<script type="text/javascript">
    import {del, get, post} from '../../helpers/api'
    import Flash from "../../helpers/flash";
    import Auth from '../../store/auth'
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        data() {
            return {
                products: [],
                authState: Auth.state,
                authAction: Auth.actions,
                search: '',
                page: 0,
                count: 0,
                next_page: '',
                selected: '',
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
            if (this.auth) {
                this.isLoading = true
                get('/api/products')
                    .then((res) => {
                        this.products = res.data.data
                    })
                get('/api/products')
                    .then((res) => {
                        this.count = res.data.meta.last_page
                    })
                get('/api/products')
                    .then((res) => {
                        this.isLoading = false
                        this.next_page = res.data.links.next
                    })
                this.isLoading = false
            }
        },

        methods: {
            retrieveList: function (index) {
                this.select_id = index.Identifier;
                this.select_name = index.Name;
                this.select_price = index.Price;
                this.select_image = index.Image;
                console.log(this.select_id);
            },

            clickCallback(pageNum) {
                get(`/api/products?page=${pageNum}`)
                    .then((res) => {
                        this.products = res.data.data
                    })
            },
            addToCart(Product) {
                this.isLoading = true
                this.form.cart.push({
                    id: this.select_id,
                    name: this.select_name,
                    price: this.select_price,
                    image: this.select_image,
                })
                let items = localStorage.getItem('cart')
                let test = Object.keys(JSON.stringify(this.select_id)).length
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
                                this.form.cart = [];
                                Flash.setSuccess("You have Successfully add Product To Cart!")
                            }
                        }
                    )
            },
            doAjax() {
                this.isLoading = true;
                setTimeout(() => {
                    this.isLoading = false
                }, 5000)
            }
            ,
        },
        computed: {
            filteredList() {
                return this.products.filter(post => {
                    return post.Name.toLowerCase().includes(this.search.toLowerCase())
                })
            }
            ,
            auth() {
                if (this.authState.api_token) {
                    return true
                }
                this.$router.push('/login')
                return false
            }
            ,
            guest() {
                return !this.auth
            }
            ,
            StoreCart() {
                return this.$store.setter.StoreCart();
            },
        }

    }
</script>
