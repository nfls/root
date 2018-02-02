import Vue from 'vue'
import Router from 'vue-router'
import Jump from '../components/Jump'
import App from '../components/Components/App'
import Dashboard from '../components/Dashboard'

import Apps from '../components/About/App'
import Feedback from '../components/About/Feedback'
import Team from'../components/About/Team'

import Directory from '../components/Alumni/Directory'
import Realname from '../components/Alumni/Realname'
import Form from '../components/Alumni/Form'

import List from '../components/Game/List'
import Rank from '../components/Game/Rank'
import History from '../components/Game/History'

import Gallery from '../components/Media/Gallery'
import Album from '../components/Media/Album'
import Video from '../components/Media/Video'

import Assignment from '../components/School/Assignment'
import Blackboard from '../components/School/Blackboard'
import Lunch from '../components/School/Lunch'
import PastPaper from '../components/School/PastPaper'
import Vote from '../components/School/Vote'

import Login from '../components/User/Login'
import Register from '../components/User/Register'
import Security from '../components/User/Security'
import Message from '../components/User/Message'
import Info from '../components/User/Info'



Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/user/login',
            component: Login
        },{
            path: '/user/register',
            component: Register
        }, {
            path: '/',
            component: App,
            children: [
                {
                    path: 'dashboard',
                    component: Dashboard
                }, {
                    path: 'about/app',
                    component: Apps
                }, {
                    path: 'about/team',
                    component: Team
                }, {
                    path: 'about/Feedback',
                    component: Feedback
                }, {
                    path: 'alumni/auth',
                    component: Realname
                }, {
                    path: 'alumni/auth/:id',
                    component: Form
                }, {
                    path: 'alumni/directory',
                    component: Directory
                }, {
                    path: 'game/list',
                    component: List
                }, {
                    path: 'game/history',
                    component: History
                }, {
                    path: 'game/rank',
                    component: Rank
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
                    path: 'school/assignment',
                    component: Assignment
                }, {
                    path: 'school/blackboard',
                    component: Blackboard
                }, {
                    path: 'school/lunch',
                    component: Lunch
                }, {
                    path: 'user/security',
                    component: Security
                }, {
                    path: 'user/message',
                    component: Message
                }, {
                    path: 'user/info',
                    component: Info
                }, {
                    path: '*',
                    component: Jump
                }
            ]
        }
    ]
})
