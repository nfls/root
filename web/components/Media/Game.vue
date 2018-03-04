<i18n src="../../translation/frontend/Media.json"></i18n>
<template>
    <div id="game-list">
        <div v-if="!verified" style="margin:20px;">
            <span class="md-display-1" v-html="$t('game-not-realname')"></span>
        </div>
        <div style="row">
            <md-table v-model="ranks" md-card v-if="loggedIn">
                <md-table-toolbar>
                    <h1 class="md-title">{{ $t('my-rank') }}</h1>
                </md-table-toolbar>

                <md-table-row slot="md-table-row" slot-scope="{ item }" style="text-align: left;">
                    <md-table-cell :md-label="$t('game')" md-sort-by="id" md-numeric>{{ item.game.title }}
                    </md-table-cell>
                    <md-table-cell :md-label="$t('score')" md-sort-by="name">{{ item.score }}</md-table-cell>
                    <md-table-cell :md-label="$t('rank')" md-sort-by="name">{{ item.rank }}</md-table-cell>
                    <md-table-cell :md-label="$t('time')" md-sort-by="name">{{ item.time | moment("lll") }}
                    </md-table-cell>
                </md-table-row>
            </md-table>
        </div>
        <div class="md-layout md-gutter md-alignment-center">
            <div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-33 md-small-size-50 md-xsmall-size-100 game-card"
                 v-for="item in list" :key="item.id">
                <md-card>
                    <md-card-media>
                        <img :src="item.thumb" alt="People">
                    </md-card-media>

                    <md-card-header>
                        <div class="md-title">{{item.title}}</div>
                        <div class="md-subhead">{{item.subTitle}}</div>
                    </md-card-header>

                    <md-card-expand>
                        <md-card-actions md-alignment="space-between">
                            <div>
                                <md-button class="md-icon-button" v-for="button in item.content" :key="button.name"
                                           :href="button.key">
                                    <md-icon>{{getIcon(button.name)}}</md-icon>
                                </md-button>
                            </div>
                            <md-card-expand-trigger>
                                <md-button class="md-icon-button">
                                    <md-icon>keyboard_arrow_down</md-icon>
                                </md-button>
                            </md-card-expand-trigger>
                        </md-card-actions>

                        <md-card-expand-content>
                            <md-card-content>
                                {{item.description}}
                            </md-card-content>
                        </md-card-expand-content>
                    </md-card-expand>
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
            this.axios.get("/game/list").then((response) => {
                this.list = response.data["data"]
            })
            this.axios.get("/game/listRank").then((response) => {
                this.ranks = response.data["data"]
            })
            this.$emit("changeTitle", "Game List")
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
</style>