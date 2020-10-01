export default {
    state: {
        api_token: null,
        user_id: null,
        StoreCart: [],
    },
    mutations: {
        ADD_Item(state, id) {
            state.StoreCart.push(id);
        },

        REMOVE_Item(state, index) {
            state.StoreCart.splice(index, 1);
        },
    },
    getters: {
        products: (state) => state.products,
        StoreCart: (state) => state.StoreCart,
    },
    setter: {
        products: (state) => state.products,
        StoreCart: (state) => state.StoreCart,

    },
    actions: {
        addItem(context, index) {
            context.commit("ADD_Item", index);
        },

        removeItem(context, index) {
            context.commit("REMOVE_Item", index);
        },
    },
    initialize() {
        this.state.api_token = localStorage.getItem('api_token')
        this.state.user_id = parseInt(localStorage.getItem('user_id'))
        this.state.StoreCart = localStorage.getItem('cart')
    },
    set(api_token, user_id) {
        localStorage.setItem('api_token', api_token)
        localStorage.setItem('user_id', user_id)

        this.initialize()
    },
    setcountCart(CART) {
        localStorage.setItem('cart', CART)
        this.initialize()
    },
    RemovecountCart() {
        localStorage.removeItem('cart')
        this.initialize()
    },
    remove() {
        localStorage.removeItem('api_token')
        localStorage.removeItem('user_id')
        this.initialize()
    }
}
