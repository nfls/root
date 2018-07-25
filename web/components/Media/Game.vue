<i18n src="../../translation/frontend/Media.json"></i18n>
<template>
    <div id="game-list" align="left">
        <div md-card v-if="ranks.length > 0">
            <span class="md-title" style="margin-left: 16px;">{{ $t('my-rank') }}</span>
            <md-list class="md-triple-line">
                <div v-for="rank in ranks">
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span>{{ rank.game }}</span>
                            <span>{{ $t('score') }}: {{ rank.score }}, {{ $t('rank' )}}: {{ rank.rank }}</span>
                            <span>{{ rank.time | moment("lll") }}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                </div>
            </md-list>
        </div>
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-xlarge-size-20 md-large-size-25 md-medium-size-33 md-small-size-50 md-xsmall-size-100 game-card"
                 v-for="item in list" :key="item.id">
                <md-card>
                    <md-card-media>
                        <img :src="item.thumb" alt="People">
                    </md-card-media>

                    <md-card-header>
                        <div class="md-title">{{item.title}}</div>
                        <div class="md-subhead">{{item.subTitle}}</div>
                    </md-card-header>

                    <md-card-content>
                        <div class="max-lines md-caption">{{item.description}}</div>
                    </md-card-content>

                    <md-card-actions>
                        <md-button class="md-icon-button" v-for="button in item.content" :key="button.name"
                                   :href="button.key">
                            <md-icon>{{getIcon(button.name)}}</md-icon>
                            <md-tooltip md-direction="top">{{getTip(button.name)}}</md-tooltip>
                        </md-button>
                    </md-card-actions>
                </md-card>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Game",
        props: ["admin", "verified", 'loggedIn'],
        data: () => ({
            list: [],
            ranks: [],
            dialogContent: {
                name: "",
                donwloadUrl: ""
            }
        }),
        mounted: function () {
            this.$moment.locale(this.$i18n.locale)
            this.axios.get("/game/list").then((response) => {
                this.list = response.data["data"]
            }).catch((error) => {
                this.$emit("generalError",error)
            })
            this.axios.get("/game/listRank").then((response) => {
                this.ranks = response.data["data"]
            }).catch((error) => {
                this.$emit("generalError",error)
            })
            this.$emit("changeTitle", this.$t('title-game'))
        }, methods: {
            getIcon(name) {
                switch (name) {
                    case "android":
                        return "android"
                    case "ios":
                        return "phone_iphone"
                    case "windows":
                        return "desktop_windows"
                    case "mac":
                        return "desktop_mac"
                    case "web":
                        return "web"
                }
            }, getHref(item, button) {
                switch (button.displayName) {
                    case "android":
                        return "android"
                    case "ios":
                        return "phone_iphone"
                    case "windows":
                        this.getJenkins(button.key)
                        return "desktop_windows"
                    case "mac":
                        return "desktop_mac"
                    case "web":
                        return "web"
                }
            }, getTip(name) {
                switch (name) {
                    case "android":
                        return "Android APK下载"
                    case "ios":
                        return "App Store链接"
                    case "windows":
                        return "Windows EXE下载"
                    case "mac":
                        return "macOS DMG下载"
                    case "web":
                        return "网页版"
                }
            }
        }
    }
</script>

<style scoped>
    .game-card {
        margin-top: 10px;
        margin-bottom: 10px;
        max-width: 400px;
    }

    .card-container {
        display: table;
        width: 100%;
    }

    .max-lines {
        overflow:hidden;
        display:-webkit-box;
        -webkit-box-orient:vertical;
        -webkit-line-clamp:4;
        height: 6.0em;
    }
</style>