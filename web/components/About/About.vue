<template>
    <div align="left">
        <p class="text">
            <span class="md-title">NFLS.IO 南外人</span><br/>
            © 2016-2018 NFLS.IO Dev Group. All rights reserved.<br/>
            <del>谨以此项目纪念即将过去的高中生活。 —— 胡清阳</del><br/>
            <md-button class="md-raised text" @click="showDialog = !showDialog">反馈</md-button>
        </p>
        <p class="text">
            <span class="md-title">App</span><br/>
            <span class="md-body">开发中</span><br/>
        </p>
        <p class="text">
            <span class="md-title">版本控制信息</span><br/>
            <span class="md-body">仅显示最近10条 Commits</span>
            <code v-if="loaded" v-html="version"></code>
        </p>
        <p class="text">
            <span class="md-title">开发组博客</span><br/>
            <md-button href="https://hqy.moe" class="md-primary">胡清阳的博客</md-button>
            <md-button href="https://xzd.nfls.io" class="md-primary">谢祖地的博客</md-button>
            <md-button href="https://blog.mrtunnel.club" class="md-primary">MrTunnel's Blog</md-button>
            <br/>
        </p>
        <p class="text">
            <span class="md-title">团队</span><br/>
            <span class="md-caption">图例</span>&nbsp;
            <span class="md-caption" style="background-color: #DD9CDF">界面图标设计</span>&nbsp;
            <span class="md-caption" style="background-color: #3CDBC0">摄影摄像</span>&nbsp;
            <span class="md-caption" style="background-color: #2DCCD3">游戏开发</span>&nbsp;
            <span class="md-caption" style="background-color: #00C389">投资人</span>&nbsp;
            <span class="md-caption" style="background-color: #71C5E8">总负责</span>&nbsp;
        </p>
        <div v-if="loaded" >
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
        <md-dialog :md-active.sync="showDialog">
            <form novalidate class="md-layout" @submit.prevent="submit">
                <md-card class="md-size-100">
                    <md-card-content>
                        <span class="md-caption">您可以通过本表格与开发组取得联系。注意，我们可能需要与您进行进一步交流，或是要求提供额外信息，所以请务必准确填写好自己的联系方式。此外，问题反馈请注明问题发生时间，及您的相关操作，以便我们进行调试。</span>
                        <br/>
                        <md-field>
                            <label>联系方式</label>
                            <md-textarea v-model="contact"></md-textarea>
                        </md-field>
                        <md-field>
                            <label>反馈</label>
                            <md-textarea v-model="content"></md-textarea>
                        </md-field>
                    </md-card-content>
                    <md-card-actions>
                        <md-button type="submit" class="md-primary">提交</md-button>
                    </md-card-actions>
                </md-card>
            </form>
        </md-dialog>

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
            showDialog: false,
            contact: null,
            content: null,
            devs: null
        }),
        mounted: function (){
            this.$emit("changeTitle","About")
            this.axios.get("/about/devs").then((response) => {
                this.devs = response.data["data"].reduce(function(previous,current){
                    return previous.concat(current.people.map(function(val){
                        val.group = current.group
                        return val
                    }))
                },[])
                this.axios.get("/about/version").then((response) => {
                    this.version = response.data["data"]
                    this.loaded = true
                })
            })

        },
        methods: {
            submit() {
                this.axios.post("/about/feedback",{
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