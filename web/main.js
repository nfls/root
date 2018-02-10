// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import VueMaterial from 'vue-material'
import axios from 'axios'
import VueAxios from 'vue-axios'
import VuePreview from 'vue-preview'
import 'vue-material/dist/vue-material.min.css'
import 'vue-material/dist/theme/default.css'
import VueMarkdown from 'vue-markdown'
import VueAnalytics from 'vue-analytics'
import Raven from 'raven-js'
import RavenVue from 'raven-js/plugins/vue'
import Editor from 'markdown-palettes'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)
Vue.use(VuePreview)
Vue.use(VueAxios, axios)
Vue.use(VueMaterial)
Vue.use(require('vue-moment'))
Vue.use(VueMarkdown)
Vue.use(Editor)
Vue.use(VueAnalytics, {
    id: 'UA-113518783-1',
    router,
    autoTracking: {
        exception: true
    }
})
const i18n = new VueI18n({
    locale: 'en',
    messages: {
        en: {
        }
    }
})

Raven
    .config('https://a9dbb410043f46369cd2f27763a1be82@sentry.io/282834')
    .addPlugin(RavenVue, Vue)
    .install();

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  i18n,
  el: '#app',
  router,
  components: {
    App
  },
  template: '<App/>'
})


