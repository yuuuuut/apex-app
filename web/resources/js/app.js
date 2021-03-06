import './bootstrap'

import Vue from 'vue'

import router  from './router'
import store   from './store'
import Vuetify from 'vuetify'

import 'vuetify/dist/vuetify.min.css'
import '@mdi/font/css/materialdesignicons.css'

import InfiniteLoading from 'vue-infinite-loading';
import App from './App.vue'

Vue.use(Vuetify);
Vue.use(InfiniteLoading);

const createApp = async () => {
  await store.dispatch('auth/currentUser')

  new Vue({
    el: '#app',
    router,
    store,
    vuetify: new Vuetify({
      icons: {
        iconfont: 'mdi',
      },
    }),
    components: { App },
    template: '<App />'
  })
}

createApp()