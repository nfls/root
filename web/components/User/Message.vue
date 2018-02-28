<i18n src="../../translation/User.json"></i18n>
<template>
    <div align="left" v-infinite-scroll="loadMore">
        <span class="md-title" v-if="!verified">
            {{ $t('not-realname' )}}
        </span>
        <div>
            <md-card v-for="chat in list" :key="chat.id">
                <md-card-content>
                    <vue-markdown>{{chat.content}}</vue-markdown>
                    <md-divider></md-divider>
                    <span class="md-caption">
                        <span v-if="chat.canReply">From:<span v-html="chat.sender.htmlUsername"></span></span>
                        <span v-else>To:<span v-html="chat.receiver.htmlUsername"></span></span>
                        @{{chat.time | moment("lll")}}
                </span>
                </md-card-content>
                <md-card-actions>
                    <md-button @click="reply(chat.sender.id)" v-if="chat.canReply">{{ $t('reply') }}</md-button>
                </md-card-actions>
            </md-card>
        </div>

        <md-dialog :md-active.sync="showDialog">
            <md-dialog-content>
                <md-field>
                    <label>{{ $t('receiver') }}</label>
                    <md-input v-model="receiver"></md-input>
                </md-field>
                <markdown-palettes v-model="content" ></markdown-palettes>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-primary" @click="showDialog = false">{{ $t('cancel') }}</md-button>
                <md-button class="md-primary" @click="send">{{ $t('submit') }}</md-button>
            </md-dialog-actions>

        </md-dialog>
        <md-speed-dial class="md-bottom-right">
            <md-speed-dial-target @click="reply(null)">
                <md-icon>create</md-icon>
            </md-speed-dial-target>
        </md-speed-dial>
    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'
    import MarkdownPalettes from 'markdown-palettes'
    import infiniteScroll from 'vue-infinite-scroll'

    export default {
        name: "Message",
        props: ["admin","loggedIn","verified","gResponse"],
        components: {
            VueMarkdown,
            MarkdownPalettes
        },
        directives: {infiniteScroll},
        data: () => ({
            page: 1,
            content: "",
            receiver: null,
            list: [],
            active: null,
            showDialog: false,
            csrf: ""
        }),
        mounted: function() {
            this.load()
            this.$emit("changeTitle","消息")
            if(this.$route.params["id"] != null){
                this.receiver = this.$route.params["id"]
            }
        },
        methods: {
            load() {
                this.axios.get("/chat/list",{
                    params: {
                        page: this.page
                    }
                }).then((response) => {
                    if(this.page == 1)
                        this.list = response.data["data"]
                    else
                        this.list = this.list.concat(response.data["data"])
                    this.getCsrf()
                })

            },
            send(){
                this.axios.post("/chat/send",{
                    id: this.receiver,
                    content: this.content,
                    _csrf: this.csrf
                }).then((response) => {
                    if(response.data["code"] != 200){
                        this.$emit("showMsg",response.data["data"])
                    }else{
                        this.$emit("showMsg",this.$t('send-succeeded'))
                        this.receiver = null
                        this.content = ""
                        this.showDialog = false
                        this.page = 1
                        this.load()
                    }
                }).catch((error) => {
                    this.$emit("showMsg",this.$t("send-failed"))
                })
            },
            reply(id){
                this.showDialog = true
                this.content = ""
                this.receiver = id
            },
            loadMore(){
                this.page ++
                this.load()
            },
            getCsrf() {
                this.axios.get("user/csrf",{
                    params: {
                        name: "user"
                    }
                }).then((response) => {
                    this.csrf = response.data["data"]
                })
            }
        }
    }
</script>

<style scoped>
    .md-card{
        margin:10px;
    }
    .CodeMirror-gutters{
        width:29px;
    }
</style>