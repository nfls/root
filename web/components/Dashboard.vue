<i18n src="../translation/frontend/Homepage.json"></i18n>
<template>
    <div>
        <md-card style="text-align:left;">
            <md-card-content>
                <vue-markdown v-if="loaded">{{announcement}}</vue-markdown>
            </md-card-content>
        </md-card>
        <div class="md-layout md-gutter md-alignment-center">
            <div class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-100 md-xsmall-size-100">
                <md-card>
                    <md-card-header>
                        <span class="md-title">{{ $t('new-wiki') }}</span>
                    </md-card-header>
                    <md-divider></md-divider>
                    <md-card-content>
                        <md-list>
                            <md-list-item v-for="title in wiki" :key="title" :href="'https://nfls.io/wiki/w/' + title"
                                          target="_blank">
                                {{title}}
                            </md-list-item>
                        </md-list>
                    </md-card-content>
                </md-card>
            </div>
            <div class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-100 md-xsmall-size-100">
            <md-card>
                <md-card-header>
                    <span class="md-title">{{ $t('new-forum') }}</span>
                </md-card-header>
                <md-divider></md-divider>
                <md-card-content>
                    <md-list>
                        <md-list-item v-for="post in forum" :key="post.id" :href="'https://nfls.io/forum/d/' + post.id"
                                      target="_blank">
                            {{post.attributes.title}}
                        </md-list-item>
                    </md-list>
                </md-card-content>
            </md-card>
        </div>
    </div>
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
            this.axios.get("https://nfls.io/wiki/api.php?action=query&format=json&list=recentchanges&generator=categorymembers&rcshow=!bot&rclimit=50&gcmtitle=Category%3A*&gcmlimit=100").then((response) => {
                var changes = response.data["query"]["recentchanges"].map(function (val) {
                    return val.title
                }).filter(function (val) {
                    if (val.startsWith("用户"))
                        return false
                    else if (val.startsWith("MediaWiki"))
                        return false
                    else if (val.startsWith("文件"))
                        return false
                    else
                        return true
                })
                this.wiki = Array.from(new Set(changes)).slice(0, 10)
            }).catch((error) => {
                this.$emit("generalError",error)
            })
            this.axios.get("https://nfls.io/forum/api/discussions").then((response) => {
                this.forum = response.data["data"].slice(0, 10)
            }).catch((error) => {
                this.$emit("generalError",error)
            })
        }
    }
</script>

<style scoped>
    .md-card {
        margin: 10px;
    }
</style>