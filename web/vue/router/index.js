import Vue from 'vue'
import Router from 'vue-router'
import HelloWorld from '../components/HelloWorld'
import Login from '../components/User/Login'
import Dashboard from '../components/User/Dashboard'
import App from '../components/Components/App'

Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/user/login',
            component: Login
        }, {
            path: '/user',
            component: App,
            subRoutes: {
                'dashboard': {
                    component: Dashboard
                }
            }
        }
    ]
})
