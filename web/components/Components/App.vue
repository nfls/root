<template>
    <div class='page-container'>
        <md-app md-waterfall md-mode='fixed'>
            <md-app-toolbar class='md-primary'>

                <div class='md-toolbar-section-start'>
                    <md-button class='md-icon-button' @click='menuVisible = !menuVisible' v-if='!menuVisible'>
                        <md-icon>menu</md-icon>
                    </md-button>
                    <span class='md-title'>{{title}}</span>
                </div>
                <div class='md-toolbar-section-end' align="right" style="margin: 10px;">
                    <span class='md-title' >{{username}}</span>&nbsp;&nbsp;&nbsp;
                    <md-menu md-size='big' md-direction='bottom-start'>
                        <md-button md-menu-trigger class='md-icon-button'>
                            <md-avatar>
                                <img :src="avatar">
                            </md-avatar>
                        </md-button>
                        <md-menu-content>
                            <md-list v-if='loggedIn'>
                                <md-list-item to='/user/info'><md-icon>info</md-icon><span class='md-list-item-text'>Info</span></md-list-item>
                                <md-list-item to='/user/security'><md-icon>security</md-icon><span class='md-list-item-text'>Security</span></md-list-item>
                                <md-list-item to='/user/message'><md-icon>chat</md-icon><span class='md-list-item-text'>Message</span></md-list-item>
                                <md-divider></md-divider>
                                <md-list-item @click="logout"><md-icon>exit_to_app</md-icon><span class='md-list-item-text'>Logout</span></md-list-item>
                            </md-list>
                            <md-list v-else>
                                <md-list-item to='/user/login'>
                                    <md-icon>flight_land</md-icon>
                                    <span class='md-list-item-text'>Login</span>
                                </md-list-item>
                                <md-list-item to='/user/register'>
                                    <md-icon>create</md-icon>
                                    <span class='md-list-item-text'>Register</span>
                                </md-list-item>
                            </md-list>
                        </md-menu-content>
                    </md-menu>

                </div>
            </md-app-toolbar>

            <md-app-drawer :md-active.sync='menuVisible' md-persistent='mini'>
                <md-toolbar class='md-transparent' md-elevation='0'>
                    NFLS.IO
                    <div class='md-toolbar-section-end'>
                        <md-button class='md-icon-button md-dense' @click='menuVisible = !menuVisible'>
                            <md-icon>keyboard_arrow_left</md-icon>
                        </md-button>
                    </div>
                </md-toolbar>

                <md-list>
                    <md-list-item to='/user/dashboard'>
                        <md-icon>dashboard</md-icon>
                        <span class='md-list-item-text'>Dashboard</span>
                    </md-list-item>

                    <md-list-item md-expand>
                        <md-icon>class</md-icon>
                        <span class='md-list-item-text'>School</span>
                        <md-list slot='md-expand'>
                            <md-list-item class='md-inset' to='/school/pastpaper'><md-icon>book</md-icon><span class='md-list-item-text'>PP</span></md-list-item>
                            <md-list-item class='md-inset' to='/school/blackboard'><md-icon>speaker_notes</md-icon><span class='md-list-item-text'>Blackboard</span></md-list-item>
                            <md-list-item class='md-inset' to='/school/vote'><md-icon>plus_one</md-icon><span class='md-list-item-text'>Vote</span></md-list-item>
                        </md-list>
                    </md-list-item>

                    <md-list-item md-expand>
                        <md-icon>school</md-icon>
                        <span class='md-list-item-text'>Alumni</span>
                        <md-list slot='md-expand'>
                            <md-list-item class='md-inset' to='/alumni/auth'><md-icon>perm_identity</md-icon><span class='md-list-item-text'>Realname</span></md-list-item>
                            <md-list-item class='md-inset' to='/alumni/directory'><md-icon>info</md-icon><span class='md-list-item-text'>Directory</span></md-list-item>
                        </md-list>
                    </md-list-item>



                    <md-list-item md-expand>
                        <md-icon>video_library</md-icon>
                        <span class='md-list-item-text'>Media</span>
                        <md-list slot='md-expand'>
                            <md-list-item class='md-inset' to='/media/game'><md-icon>gamepad</md-icon><span class='md-list-item-text'>Game</span></md-list-item>
                            <md-list-item class='md-inset' to='/media/gallery'><md-icon>photo_library</md-icon><span class='md-list-item-text'>Gallery</span></md-list-item>
                        </md-list>
                    </md-list-item>

                    <md-list-item href='https://forum.nfls.io'><md-icon>forum</md-icon><span class='md-list-item-text'>Forum</span></md-list-item>
                    <md-list-item href='https://wiki.nfls.io'><md-icon>library_books</md-icon><span class='md-list-item-text'>Wiki</span></md-list-item>
                    <md-list-item to='/about'><md-icon>adb</md-icon><span class='md-list-item-text'>About</span></md-list-item>
                    <md-list-item href='/admin' v-if="admin"><md-icon>developer_mode</md-icon><span class='md-list-item-text'>Admin</span></md-list-item>
                </md-list>
            </md-app-drawer>

            <md-app-content>
                <router-view :gResponse='gResponse' :loggedIn='loggedIn' :admin='admin' :verified='verified' :avatar="avatar" @changeTitle="changeTitle" @prepareRecaptcha="prepareRecaptcha" @reload="reload"/>
            </md-app-content>
        </md-app>
    </div>
</template>

<script>
    export default {
        name: 'Dashboard',
        data: () => ({
            menuVisible: false,
            title: 'Unknown Region',
            toggleCard: false,
            loggedIn: false,
            username: '未登录',
            admin: false,
            verified: false,
            avatar: "/avatar/0.png",
            gResponse: '',
            reloadC: 0
        }),
        methods: {
            initReCaptcha() {
                var self = this;
                setTimeout(function() {
                    if(typeof grecaptcha === 'undefined') {
                        self.initReCaptcha();
                    }
                    else {
                        grecaptcha.render('recaptcha', {
                            sitekey: '6LfV3UIUAAAAADBXL7tfgvUs9rt2gw-4GBxLO9Pj',
                            size: 'invisible',
                            callback: self.ct
                        });
                    }
                }, 100);
            }, logout() {
                this.axios.get('/user/logout').then((response) => {
                    this.reload
                    location.reload()
                })
            }, ct(response){
                this.gResponse = response
            }, changeTitle(title) {
                this.title = title
            }, prepareRecaptcha() {
                document.getElementById('recaptcha').style.visibility = 'visible';
                if(typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset()
                }
            }, reload() {
                this.avatar = "/avatar/0.png"
                this.axios.get('/user/current').then((response) =>{
                    if(response.data['code'] == 200){
                        this.username = response.data['data']['username'];
                        this.admin = response.data['data']['admin'];
                        this.verified = response.data['data']['verified']
                        this.avatar = "/avatar/" + response.data['data']['id'] + ".png?" + this.reloadC
                        this.loggedIn = true
                        this.reloadC++
                        var path = this.$route.fullPath
                        if ( path === "/user/login" || path === "/user/register" || path === "user/reset")
                            this.$router.push("/dashboard")
                    }else{
                        this.loggedIn = false
                    }
                }).catch((error) => {
                    this.loggedIn = false
                })
            }
        },created: function(){
            document.getElementById('recaptcha').style.visibility = 'hidden';
        },mounted: function (){
            this.reload()
            this.initReCaptcha()
        }, watch: {
            $route () {
                document.getElementById('recaptcha').style.visibility = 'hidden';
            }
        }

    }
</script>

<style scoped>
    .md-app {
        min-height: 100vh;
        max-height: 100vh;
        border: 1px solid rgba(0, 0, 0, 0.12);
    }

    .md-drawer {
        width: 230px;
        max-width: calc(100vw - 125px);
    }

    .md-list {
        width: 320px;
        max-width: 100%;
        display: inline-block;
        vertical-align: top;
        border: 1px solid rgba(#000, .12);
    }


</style>
<style lang='scss'>
    @import '~vue-material/dist/theme/engine'; // Import the theme engine
    @import '~vue-material/dist/theme/all'; // Apply the theme
</style>
