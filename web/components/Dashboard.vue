<i18n src="../translation/frontend/Homepage.json"></i18n>
<template>
    <div>
        <md-card style="text-align:left;">
            <md-card-content>
                <vue-markdown v-if="loaded">{{announcement}}</vue-markdown>
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

<style scoped>
    .md-card {
        margin: 10px;
    }
</style>