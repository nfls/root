import Vue from 'vue'
import Router from 'vue-router'
import Jump from '../components/Jump'
import App from '../components/Components/App'
import Dashboard from '../components/Dashboard'

import About from'../components/About/About'

import Directory from '../components/Alumni/Directory'
import Realname from '../components/Alumni/Realname'
import Form from '../components/Alumni/Form'

import Game from '../components/Media/Game'
import Gallery from '../components/Media/Gallery'
import Album from '../components/Media/Album'

import Blackboard from '../components/School/Blackboard'
import PastPaper from '../components/School/PastPaper'
import Vote from '../components/School/Vote'

import Login from '../components/User/Login'
import Register from '../components/User/Register'
import Reset from '../components/User/Reset'
import Security from '../components/User/Security'
import Message from '../components/User/Message'
import Info from '../components/User/Info'

import Preference from '../components/Admin/Preference'


Vue.use(Router)

export default new Router({
    routes: [
         {
            path: '/',
            component: App,
            children: [
                {
                    path: 'dashboard',
                    component: Dashboard
                }, {
                    path: 'about',
                    component: About
                }, {
                    path: 'alumni/auth',
                    component: Realname
                }, {
                    path: 'alumni/auth/:id',
                    component: Form
                }, {
                    path: 'alumni/auth/admin/:id',
                    component: Form
                }, {
                    path: 'alumni/directory',
                    component: Directory
                }, {
                    path: 'media/game',
                    component: Game
                }, {
                    path: 'media/gallery',
                    component: Gallery
                }, {
                    path: 'media/gallery/:id',
                    component: Album
                }, {
                    path: 'school/vote',
                    component: Vote
                }, {
                    path: 'school/pastpaper',
                    component: PastPaper
                }, {
                    path: 'school/blackboard',
                    component: Blackboard
                }, {
                    path: 'user/security',
                    component: Security
                }, {
                    path: 'user/message',
                    component: Message
                }, {
                    path: '/user/login',
                    component: Login
                }, {
                    path: '/user/register',
                    component: Register
                }, {
                    path: '/user/reset',
                    component: Reset
                }, {
                    path: 'user/info',
                    component: Info
                }, {
                    path: '/admin/preference',
                    component: Preference
                }, {
                    path: '*',
                    component: Jump
                }
            ]
        }
    ]
})
