<template>
    <div class="page-container">
        <md-app md-waterfall md-mode="fixed">
            <md-app-toolbar class="md-primary">

                <div class="md-toolbar-section-start">
                    <md-button class="md-icon-button" @click="menuVisible = !menuVisible">
                        <md-icon>menu</md-icon>
                    </md-button>
                    <span class="md-title">{{name}}</span>
                </div>
                <div class="md-toolbar-section-end">
                    <md-menu md-size="big" md-direction="bottom-start">
                        <md-button md-menu-trigger class="md-icon-button">
                            <md-icon>more_vert</md-icon>
                        </md-button>
                        <md-menu-content>
                            <md-list>
                                <md-list-item>
                                    <md-avatar>
                                        <img src="https://placeimg.com/40/40/people/5" alt="People">
                                    </md-avatar>
                                    <span class="md-list-item-text">这是用户名</span>
                                </md-list-item>
                                <md-divider class="md-inset"></md-divider>
                                <md-list-item>
                                    <md-icon>settings</md-icon>
                                    <span class="md-list-item-text">Settings</span>
                                </md-list-item>
                                <md-list-item>
                                    <md-icon>exit_to_app</md-icon>
                                    <span class="md-list-item-text">Logout</span>
                                </md-list-item>
                            </md-list>
                        </md-menu-content>
                    </md-menu>
                </div>
            </md-app-toolbar>

            <md-app-drawer :md-active.sync="menuVisible">
                <md-toolbar class="md-transparent" md-elevation="0">
                    NFLS.IO
                </md-toolbar>

                <md-list>
                    <md-list-item to="/user/dashboard">
                        <md-icon>dashboard</md-icon>
                        <span class="md-list-item-text">Dashboard</span>
                    </md-list-item>
                    <md-list-item md-expand>
                        <md-icon>library_books</md-icon>
                        <span class="md-list-item-text">School</span>
                        <md-list slot="md-expand">
                            <md-list-item class="md-inset" to="/school/pastpaper"><md-icon>book</md-icon><span class="md-list-item-text">Past Paper</span></md-list-item>
                            <md-list-item class="md-inset" to="/school/blackboard"><md-icon>speaker_notes</md-icon><span class="md-list-item-text">Blackboard</span></md-list-item>
                            <md-list-item class="md-inset" to="/school/assignment"><md-icon>assignment</md-icon><span class="md-list-item-text">Assignment</span></md-list-item>
                        </md-list>
                    </md-list-item>

                    <md-list-item md-expand>
                        <md-icon>school</md-icon>
                        <span class="md-list-item-text">Alumni</span>
                        <md-list slot="md-expand">
                            <md-list-item class="md-inset" to="/alumni/auth"><md-icon>perm_identity</md-icon><span class="md-list-item-text">Realname</span></md-list-item>
                            <md-list-item class="md-inset" to="/alumni/dir"><md-icon>info</md-icon><span class="md-list-item-text">Directory</span></md-list-item>
                        </md-list>
                    </md-list-item>

                    <md-list-item md-expand>
                        <md-icon>videogame_asset</md-icon>
                        <span class="md-list-item-text">Game</span>
                        <md-list slot="md-expand">
                            <md-list-item class="md-inset" to="/games/list"><md-icon>gamepad</md-icon><span class="md-list-item-text">List</span></md-list-item>
                            <md-list-item class="md-inset" to="/games/rank"><md-icon>list</md-icon><span class="md-list-item-text">Rank</span></md-list-item>
                            <md-list-item class="md-inset" to="/games/purchase"><md-icon>shopping_cart</md-icon><span class="md-list-item-text">Purchase</span></md-list-item>
                        </md-list>
                    </md-list-item>

                    <md-list-item md-expand>
                        <md-icon>video_library</md-icon>
                        <span class="md-list-item-text">Media</span>
                        <md-list slot="md-expand">
                            <md-list-item class="md-inset" to="/media/gallery"><md-icon>photo_library</md-icon><span class="md-list-item-text">Gallery</span></md-list-item>
                            <md-list-item class="md-inset" to="/media/video"><md-icon>video_library</md-icon><span class="md-list-item-text">Video</span></md-list-item>
                        </md-list>
                    </md-list-item>

                    <md-list-item md-expand>
                        <md-icon>account_box</md-icon>
                        <span class="md-list-item-text">Account</span>
                        <md-list slot="md-expand">
                            <md-list-item class="md-inset" to="/user/info"><md-icon>info</md-icon><span class="md-list-item-text">Info</span></md-list-item>
                            <md-list-item class="md-inset" to="/user/security"><md-icon>security</md-icon><span class="md-list-item-text">Security</span></md-list-item>
                            <md-list-item class="md-inset" to="/user/announcement"><md-icon>chat</md-icon><span class="md-list-item-text">Message</span></md-list-item>
                            <md-list-item class="md-inset" to="/user/announcement"><md-icon>announcement</md-icon><span class="md-list-item-text">Announcement</span></md-list-item>
                        </md-list>
                    </md-list-item>

                    <md-list-item to="/app">
                        <md-icon>apps</md-icon>
                        <span class="md-list-item-text">App</span>
                    </md-list-item>
                </md-list>
            </md-app-drawer>

            <md-app-content>
                <router-view v-model="name"/>
            </md-app-content>
        </md-app>
    </div>
</template>

<script>
    export default {
        name: "Dashboard",
        data: () => ({
            menuVisible: false,
            name: "Unknown Region",
            toggleCard: false
        })
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