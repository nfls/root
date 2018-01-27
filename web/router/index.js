import Vue from 'vue'
import Router from 'vue-router'
import App from '../components/Components/App'
import Login from '../components/User/Login'
import Dashboard from '../components/User/Dashboard'
import Vote from '../components/School/Vote'
import Realname from '../components/Alumni/Realname'
import Form from '../components/Alumni/Form'
import Gallery from '../components/Media/Gallery'
import Jump from '../components/Jump'
import Album from '../components/Media/Album'


Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/user/login',
            component: Login
        }, {
            path: '/',
            component: App,
            children: [
                {
                    path: 'user/dashboard',
                    component: Dashboard
                }, {
                    path: 'school/vote',
                    component: Vote
                }, {
                    path: 'alumni/auth',
                    component: Realname
                }, {
                    path: 'alumni/auth/:id',
                    component: Form
                }, {
                    path: 'media/gallery',
                    component: Gallery
                }, {
                    path: 'media/gallery/:id',
                    component: Album
                }, {
                    path: '*',
                    component: Jump
                }
            ]
        }
    ]
})
