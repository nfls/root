<template>
    <div id="game-list">
        <div class="md-layout md-gutter md-alignment-center">
            <div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-33 md-small-size-50 md-xsmall-size-100 game-card" v-for="item in list" :key="item.id">
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
                                <md-button class="md-icon-button" v-for="button in item.content" :key="button.displayName"><md-icon>{{getIcon(button.displayName)}}</md-icon></md-button>
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
        name: "List",
        data: () => ({

            list: []
        }),
        mounted: function() {
            this.axios.get("/game/list").then((response) => {
                this.list = response.data["data"]
            })
        },methods:{
            getIcon(name){
                return "android"
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
        width:100 %;
    }
</style>