import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'


import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: config.env.MIX_PUSHER_APP_KEY,
    cluster: config.env.MIX_PUSHER_APP_CLUSTER,
    // wsHost: window.location.hostname,
    // wsPort: 6001,
    // encrypted: true,
    // forceTLS: true,
    // disableStats: true
});


// window.Vue = require('vue');

// import InstantSearch from 'vue-instantsearch'
// Vue.use(InstantSearch);

import '~/plugins'
import '~/components'

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  Echo,
  router,
  ...App
})


require('vue-flash-message/dist/vue-flash-message.min.css');