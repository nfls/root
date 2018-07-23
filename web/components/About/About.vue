<i18n src="../../translation/frontend/About.json"></i18n>
<template>
    <div align="left">
        <p class="text">
            <span class="md-title">NFLS.IO 南外人</span><br/>
            © 2016-2018 NFLS.IO Dev Group. All rights reserved.<br/>
            <br/>
            <md-button class="md-raised text" href="https://dev.nfls.io/jira/openid/login/2">{{ $t('feedback') }}</md-button>
        </p>
        <p class="text">
            <span class="md-title">{{ $t('version-control') }}</span><br/>
            <span class="md-body">{{ $t('commits-prompt') }}</span>
            <code v-if="loaded" v-html="version"></code>
        </p>
        <p class="text">
            <span class="md-title">{{ $t('developer-blog') }}</span><br/>
            <md-button href="https://hqy.moe" class="md-primary">{{ $t('blog-hqy') }}</md-button>
            <md-button href="https://xzd.nfls.io" class="md-primary">{{ $t('blog-xzd') }}</md-button>
            <md-button href="https://blog.mrtunnel.club" class="md-primary">{{ $t('blog-mrtunnel') }}</md-button>
            <br/>
        </p>
        <p class="text">
            <span class="md-title">{{ $t('team') }}</span><br/>
            <span class="md-caption">{{ $t('legend') }}</span>&nbsp;
            <span class="md-caption" style="background-color: #DD9CDF">{{ $t('logo') }}</span>&nbsp;
            <span class="md-caption" style="background-color: #3CDBC0">{{ $t('photography') }}</span>&nbsp;
            <span class="md-caption" style="background-color: #2DCCD3">{{ $t('game') }}</span>&nbsp;
            <span class="md-caption" style="background-color: #00C389">{{ $t('invest') }}</span>&nbsp;
            <span class="md-caption" style="background-color: #71C5E8">{{ $t('responsible') }}</span>&nbsp;
        </p>
        <div v-if="loaded">
            <md-card class="md-primary id-card" v-for="dev in devs" :key="dev.title" :md-theme="dev.group">
                <md-card-media>
                    <img :src="dev.image" alt="People">
                </md-card-media>
                <md-card-header style="text-align: center;">
                    <div class="md-body-2">{{dev.title}}</div>
                    <div class="md-caption">{{dev.subtitle}}</div>
                </md-card-header>
            </md-card>
        </div>

    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'

    export default {
        name: "About",
        components: {
            VueMarkdown
        },
        data: () => ({
            version: "aaa",
            loaded: false,
            contact: null,
            content: null,
            devs: null
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t("about-title"))
            this.axios.get("/about/devs").then((response) => {
                this.devs = response.data["data"].reduce(function (previous, current) {
                    return previous.concat(current.people.map(function (val) {
                        val.group = current.group
                        return val
                    }))
                }, [])
                this.axios.get("/about/version").then((response) => {
                    this.version = response.data["data"]
                    this.loaded = true
                })
            })

        },
        methods: {
            submit() {
                this.axios.post("/about/feedback", {
                    content: this.content,
                    contact: this.contact
                }).then((response) => {
                    this.showDialog = false
                    this.content = null
                    this.contact = null
                })
            }
        }
    }
</script>

<style scoped lang="scss">
    .text {
        text-align: left;
    }

    @import "~vue-material/dist/theme/engine";

    @include md-register-theme("ui", (
            primary: #DD9CDF,
    ));

    @include md-register-theme("photo", (
            primary: #3CDBC0
    ));

    @include md-register-theme("game", (
            primary: #2DCCD3
    ));

    @include md-register-theme("investor", (
            primary: #00C389
    ));

    @include md-register-theme("leader", (
            primary: #71C5E8
    ));

    @include md-register-theme("pink-card", (
            primary: #F57EB6
    ));

    @import "~vue-material/dist/base/theme";
    @import "~vue-material/dist/components/MdCard/theme";

    .id-card {
        max-width: 120px;
        display: inline-block;
        margin: 5px;
    }
</style>