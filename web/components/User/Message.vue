<template>
    <div align="left" v-infinite-scroll="loadMore">
        <span class="md-title" v-if="!verified">
            您尚未完成实名认证，将无法进行发送消息等操作！
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
                    <md-button @click="reply(chat.sender.id)" v-if="chat.canReply">回复</md-button>
                </md-card-actions>
            </md-card>
        </div>

        <md-dialog :md-active.sync="showDialog" style="min-width:300px;">
            <md-dialog-content>
                <md-field>
                    <label>收件用户ID</label>
                    <md-input v-model="receiver"></md-input>
                </md-field>
                <markdown-palettes v-model="content" ></markdown-palettes>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-primary" @click="showDialog = false">关闭</md-button>
                <md-button class="md-primary" @click="send">发送</md-button>
            </md-dialog-actions>

        </md-dialog>
        <md-speed-dial class="md-bottom-right">
            <md-speed-dial-target @click="reply(null)">
                <md-icon>create</md-icon>
            </md-speed-dial-target>
        </md-speed-dial>
        <md-snackbar :md-active.sync="showMessage">{{message}}</md-snackbar>
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
            message: "",
            showMessage: false,
            page: 1,
            content: "",
            receiver: null,
            list: [],
            active: null,
            showDialog: false
        }),
        mounted: function() {
            this.load()
            this.$emit("changeTitle","消息")
            if(this.$route.params["id"] != null){
                this.receiver = this.$route.params["id"]
                this.active = "tab-new"
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
                })

            },
            send(){
                this.axios.post("/chat/send",{
                    id: this.receiver,
                    content: this.content
                }).then((response) => {
                    this.showMsg("发送成功")
                    this.receiver = null
                    this.content = ""
                    this.showDialog = false
                    this.page = 1
                    this.load()
                }).catch((error) => {
                    this.showMsg("发送失败，请检查您是否完成实名认证，或是接受用户是否存在！")
                })
            },
            reply(id){
                this.showDialog = true
                this.content = ""
                this.receiver = id
            },
            showMsg(message) {
                this.message = message
                this.showMessage = true
            },
            loadMore(){
                this.page ++
                this.load()
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