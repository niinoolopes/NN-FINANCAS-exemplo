import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

import 'bootstrap/dist/js/bootstrap.min.js';
import '@/assets/css/bootstrap.css';


Vue.component('TemplateDefault',  () => import('@/template/Default'))
Vue.component('PageNav',          () => import('@/components/PageNav'))
Vue.component('PageSection',      () => import('@/components/PageSection'))
Vue.component('PageContentNav',   () => import('@/components/PageContent_Nav'))
Vue.component('PageContentTitle', () => import('@/components/PageContent_Title'))
Vue.component('Message',          () => import('@/components/Message'))
Vue.component('Alert',            () => import('@/components/Alert'))
Vue.component('Column',           () => import('@/components/Column'))


Vue.config.productionTip = false

Vue.filter("vReal", v => {
  if(v == undefined) return v
  if(v == '_ _ _') return v

  v = Number(v)
  if(!isNaN(v)){
    return v.toLocaleString('pt-BR',{minimumFractionDigits: 2})
    // return v.toLocaleString('pt-BR',{ style: 'currency', currency: 'BRL' })
  }
  return v
})

Vue.filter("convertDate", d => {
  if(d == undefined) return d
  if(d == '_ _ _') return d

  var ano = d.split('-')[0]
  var mes = d.split('-')[1]
  var dia = +d.split('-')[2]
  dia = `${dia}`.length == 1 ? `0${dia}` : dia
  return `${dia}/${mes}/${ano}`
})


new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
