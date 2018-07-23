<i18n src="../translation/frontend/Homepage.json"></i18n>
<template>
    <div>
        <md-card style="text-align:left;">
            <md-card-content>
                <vue-markdown v-if="loaded">{{announcement}}</vue-markdown>
            </md-card-content>
        </md-card>
        <md-card>
            <md-card-header>
                <div class="md-title">精选功能</div>
            </md-card-header>
            <md-card-content>
                <div class="md-layout md-gutter md-alignment-center">
                    <div class="md-layout-item md-medium-size-25 md-small-size-50 md-xsmall-size-100">
                        <md-empty-state
                                md-icon="pool"
                                md-description="新一代学习系统">
                            <md-button class="md-primary md-raised" disabled>划水(未开放)</md-button>
                        </md-empty-state>
                    </div>
                    <div class="md-layout-item md-medium-size-25 md-small-size-50 md-xsmall-size-100">
                        <md-empty-state
                                md-icon="photo_library"
                                md-description="表情包的发源地">
                            <md-button class="md-primary md-raised" to="/media/gallery">相册</md-button>
                        </md-empty-state>
                    </div>
                    <div class="md-layout-item md-medium-size-25 md-small-size-50 md-xsmall-size-100">
                        <md-empty-state
                                md-icon="forum"
                                md-description="荒无人烟的地方">
                            <md-button class="md-primary md-raised" href="/forum">论坛</md-button>
                        </md-empty-state>
                    </div>
                    <div class="md-layout-item md-medium-size-25 md-small-size-50 md-xsmall-size-100">
                        <md-empty-state
                                md-icon="info"
                                md-description="校友们都在干啥">
                            <md-button class="md-primary md-raised" to="/alumni/directory">校友</md-button>
                        </md-empty-state>
                    </div>
                </div>
            </md-card-content>
        </md-card>
    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'

    export default {
        name: "Dashboard",
        components: {
            VueMarkdown
        },
        props: ["isAdmin", "isLoggedIn", "isVerified"],
        data: () => ({
            wiki: null,
            forum: null,
            announcement: null,
            loaded: false
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t("title"))
            this.axios.get("/message/announcement").then((response) => {
                this.announcement = response.data["data"]
                this.loaded = true
            })
        }
    }
</script>

<style scoped lang="scss">
    .md-card {
        margin: 10px;
    }
</style>