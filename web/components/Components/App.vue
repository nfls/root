<i18n src="../../translation/frontend/App.json"></i18n>
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
                    <span class='md-title'>{{username}}</span>&nbsp;&nbsp;&nbsp;
                    <md-menu md-size='big' md-direction='bottom-start'>
                        <md-button md-menu-trigger class='md-icon-button'>
                            <md-avatar>
                                <img :src="avatar">
                            </md-avatar>
                        </md-button>
                        <md-menu-content>
                            <md-list v-if='loggedIn'>
                                <md-list-item to='/user/info'>
                                    <md-icon>info</md-icon>
                                    <span class='md-list-item-text'>{{ $t('info') }}</span></md-list-item>
                                <md-list-item to='/user/security'>
                                    <md-icon>security</md-icon>
                                    <span class='md-list-item-text'>{{ $t('security') }}</span></md-list-item>
                                <md-list-item to='/user/message'>
                                    <md-icon>chat</md-icon>
                                    <span class='md-list-item-text'>{{ $t('message') }}</span>
                                    <md-chip class="md-accent" v-if="unread > 0">{{unread}}</md-chip>
                                </md-list-item>
                                <md-divider></md-divider>
                                <md-list-item href="https://dev.nfls.io/jira/openid/login/2">
                                    <md-icon>help_outline</md-icon>
                                    <span class='md-list-item-text'>{{ $t('support') }}</span></md-list-item>
                                <md-list-item v-if="admin" @click="dropAdmin">
                                    <md-icon>delete</md-icon>
                                    <span class='md-list-item-text'>{{ $t('drop-admin') }}</span></md-list-item>
                                <md-list-item v-else-if="dropEnabled" @click="recover">
                                    <md-icon>autorenew</md-icon>
                                    <span class='md-list-item-text'>{{ $t('recover-admin') }}</span></md-list-item>
                                <md-divider></md-divider>
                                <md-list-item @click="lang">>
                                    <md-icon>translate</md-icon>
                                    <span class='md-list-item-text'>{{ language }}</span></md-list-item>
                                <md-divider></md-divider>
                                <md-list-item @click="logout">
                                    <md-icon>exit_to_app</md-icon>
                                    <span class='md-list-item-text'>{{ $t('logout') }}</span></md-list-item>

                            </md-list>
                            <md-list v-else>
                                <md-list-item to='/user/login'>
                                    <md-icon>flight_land</md-icon>
                                    <span class='md-list-item-text'>{{ $t('login') }}</span>
                                </md-list-item>
                                <md-list-item to='/user/register'>
                                    <md-icon>create</md-icon>
                                    <span class='md-list-item-text'>{{ $t('register') }}</span>
                                </md-list-item>
                            </md-list>
                        </md-menu-content>
                    </md-menu>

                </div>
            </md-app-toolbar>

            <md-app-drawer :md-active.sync='menuVisible' md-persistent='full'>
                <md-toolbar class='md-transparent' md-elevation='0'>
                    NFLS.IO 南外人
                    <div class='md-toolbar-section-end'>
                        <md-button class='md-icon-button md-dense' @click='menuVisible = !menuVisible'>
                            <md-icon>keyboard_arrow_left</md-icon>
                        </md-button>
                    </div>
                </md-toolbar>

                <md-list>
                    <md-list-item to='/user/dashboard'>
                        <md-icon>dashboard</md-icon>
                        <span class='md-list-item-text'>{{ $t('homepage') }}</span></md-list-item>

                    <md-divider></md-divider>

                    <md-subheader>{{ $t('in-school') }}</md-subheader>

                    <md-list-item to='/school/pastpaper'>
                        <md-icon>book</md-icon>
                        <span class='md-list-item-text'>{{ $t('resource') }}</span></md-list-item>
                    <md-list-item to='/school/blackboard'>
                        <md-icon>speaker_notes</md-icon>
                        <span class='md-list-item-text'>{{ $t('blackboard') }}</span></md-list-item>
                    <md-list-item to='/school/vote'>
                        <md-icon>plus_one</md-icon>
                        <span class='md-list-item-text'>{{ $t('vote') }}</span></md-list-item>

                    <md-divider></md-divider>

                    <md-subheader>{{ $t('graduate') }}</md-subheader>

                    <md-list-item to='/alumni/auth'>
                        <md-icon>perm_identity</md-icon>
                        <span class='md-list-item-text'>{{ $t('realname') }}</span></md-list-item>
                    <md-list-item to='/alumni/directory'>
                        <md-icon>info</md-icon>
                        <span class='md-list-item-text'>{{ $t('directory') }}</span></md-list-item>

                    <md-divider></md-divider>

                    <md-subheader>{{ $t('media') }}</md-subheader>

                    <md-list-item to='/media/game'>
                        <md-icon>gamepad</md-icon>
                        <span class='md-list-item-text'>{{ $t('game') }}</span></md-list-item>
                    <md-list-item to='/media/gallery'>
                        <md-icon>photo_library</md-icon>
                        <span class='md-list-item-text'>{{ $t('gallery') }}</span></md-list-item>

                    <md-divider></md-divider>

                    <md-subheader>{{ $t('external') }}</md-subheader>

                    <md-list-item href='https://forum.nfls.io'>
                        <md-icon>forum</md-icon>
                        <span class='md-list-item-text'>{{ $t('forum') }}</span></md-list-item>
                    <md-list-item href='https://wiki.nfls.io'>
                        <md-icon>library_books</md-icon>
                        <span class='md-list-item-text'>{{ $t('wiki') }}</span></md-list-item>
                    <md-list-item href='https://dev.nfls.io'>
                        <md-icon>developer_mode</md-icon>
                        <span class='md-list-item-text'>{{ $t('development') }}</span></md-list-item>
                    <md-list-item href='https://ib.nfls.io'>
                        <md-icon>pool</md-icon>
                        <span class='md-list-item-text'>{{ $t('managebac') }}</span></md-list-item>


                    <md-divider></md-divider>

                    <md-list-item to='/about'>
                        <md-icon>adb</md-icon>
                        <span class='md-list-item-text'>{{ $t('about') }}</span></md-list-item>

                    <div id="admin" v-if="admin">
                        <md-divider></md-divider>
                        <md-subheader>{{ $t('admin') }}</md-subheader>


                        <md-list-item href='/admin'>
                            <md-icon>build</md-icon>
                            <span class='md-list-item-text'>{{ $t('console') }}</span></md-list-item>
                        <md-list-item to="/admin/preference">
                            <md-icon>settings_input_composite</md-icon>
                            <span class='md-list-item-text'>{{ $t('preference') }}</span></md-list-item>
                        <md-list-item to="/admin/upload">
                            <md-icon>file_upload</md-icon>
                            <span class='md-list-item-text'>{{ $t('upload') }}</span></md-list-item>
                    </div>
                </md-list>

            </md-app-drawer>

            <md-app-content>
                <router-view :gResponse='gResponse' :webpSupported='webpSupported' :loggedIn='loggedIn' :admin='admin'
                             :verified='verified' :avatar="avatar" @changeTitle="changeTitle"
                             @prepareRecaptcha="prepareRecaptcha" @reload="reload" @renderWebp="renderWebp"
                             @showMsg="showMsg" @generalError="generalError"/>
                <md-snackbar md-positoin="center" :md-active.sync="showSnackbar" md-persistent>
                    <span>{{message}}</span>
                </md-snackbar>
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
            username: '',
            admin: false,
            dropEnabled: false,
            verified: false,
            unread: 0,
            avatar: "/avatar/0.png",
            gResponse: '',
            reloadC: 0,
            webpSupported: true,
            showSnackbar: false,
            message: false,
            language: ""
        }),
        methods: {
            initReCaptcha() {
                let self = this;
                setTimeout(function () {
                    if (typeof grecaptcha === 'undefined') {
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
                this.axios.get("user/csrf", {
                    params: {
                        name: "user"
                    }
                }).then((response) => {
                    this.axios.post("/user/logout", {
                        _csrf: response.data["data"]
                    }).then((response) => {
                        this.reload()
                        location.reload()
                    })
                })
            }, ct(response) {
                this.gResponse = response
            }, changeTitle(title) {
                document.title = title + " - 南外人 NFLS.IO "
                this.title = title
            }, prepareRecaptcha() {
                document.getElementById('recaptcha').style.visibility = 'visible';
                let self = this
                if (typeof grecaptcha !== 'undefined') {
                    try {
                        grecaptcha.reset()
                    } catch(e) {
                        setTimeout(function () {
                            self.prepareRecaptcha()
                        }, 100);
                    }
                }
            }, renderWebp() {
                if(typeof WebPJSInit === "undefined")
                    this.loadWebP()
                else
                    WebPJSInit()
            }, reload() {
                this.avatar = "/avatar/0.png"
                this.axios.get('/user/current').then((response) => {
                    if (response.data['code'] === 200) {
                        this.username = response.data['data']['username']
                        this.admin = response.data['data']['admin']
                        this.verified = response.data['data']['verified']
                        this.unread = response.data['data']['unread']
                        this.avatar = "/avatar/" + response.data['data']['id'] + ".png?" + this.reloadC
                        this.loggedIn = true
                        this.reloadC++
                        if (this.$cookie.get("drop") === "true")
                            this.dropEnabled = true
                        var path = this.$route.fullPath
                        if (path === "/user/login" || path === "/user/register" || path === "user/reset")
                            this.$router.push("/dashboard")
                    } else {
                        this.loggedIn = false
                    }
                }).catch((error) => {
                    this.loggedIn = false
                })
            }, loadWebP() {
                var WebP = new Image()
                var self = this
                WebP.onload = WebP.onerror = function () {
                    if (WebP.height != 2) {
                        var sc = document.createElement('script');
                        sc.type = 'text/javascript';
                        sc.async = true;
                        var s = document.getElementsByTagName('script')[0];
                        sc.src = '/js/webpjs-0.0.2.min.js';
                        s.parentNode.insertBefore(sc, s);
                        console.log(self.webpSupported)
                        self.webpSupported = false
                    }
                };
                WebP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
            }, showMsg(msg) {
                this.message = msg
                this.showSnackbar = true
            }, dropAdmin() {
                this.$cookie.set('drop', 'true')
                window.location.reload()
            }, recover() {
                this.$cookie.delete("drop")
                window.location.reload()
            }, lang() {
                if(this.$i18n.locale === "zh"){
                    this.$cookie.set("lang","en",{
                        expires: '1Y'
                    })
                    this.useEn()
                }else{
                    this.$cookie.set("lang","zh",{
                        expires: '1Y'
                    })
                    this.useZh()
                }
            }, useZh(){
                this.$i18n.locale = "zh"
                this.language = "English"
                window.location.reload()
            }, useEn(){
                this.$i18n.locale = "en"
                this.language = "简体中文"
                window.location.reload()
            }, generalError(error){
                this.showMsg(this.$t('errors'))
            }
        }, created: function () {
            document.getElementById('recaptcha').style.visibility = 'hidden';
        }, mounted: function () {
            this.username = this.$t('username')
            this.reload()
            this.initReCaptcha()
            this.loadWebP()
            if(this.$cookie.get('drop') === "true")
                this.dropEnabled = true
            console.log(this.$i18n.localeåå)
        }, watch: {
            $route() {
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
        width: 180px;
    }

</style>
<style lang='scss'>
    @import "~vue-material/dist/theme/engine"; // Import the theme engine

    @include md-register-theme("default", (
            primary: #263238, // The primary color of your application
            accent: #ff5722 // The accent or secondary color
    ));

    @import "~vue-material/dist/theme/all"; // Apply the theme
</style>
