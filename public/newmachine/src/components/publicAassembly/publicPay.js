import PayComponent from './publicPay.vue';
const Pay={
    install:function(Vue){
        Vue.component('Pay',PayComponent)
    }
}
export default Pay;