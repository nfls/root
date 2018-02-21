<template>
    <div align="left" v-infinite-scroll="loadMore">
        <span class="md-title" v-if="!verified">
            您尚未完成实名认证，将无法进行发送消息等操作！
        </span>
        <md-tabs @md-changed="change" :md-active-tab="active" md-dynamic-height>
            <md-tab id="tab-inbox" md-label="收件箱">
                <md-empty-state
                        v-if="inbox.length == 0"
                        md-icon="devices_other"
                        md-label="收件箱内没有消息"
                        md-description="要不去找一个人聊聊？">
                </md-empty-state>
                <md-card v-for="chat in inbox" :key="chat.id">
                    <md-card-content>
                        <vue-markdown>{{chat.content}}</vue-markdown>
                        <md-divider></md-divider>
                        <span class="md-caption">From: {{chat.sender.username}}@{{chat.time | moment("lll")}}</span>
                    </md-card-content>
                    <md-card-actions>
                        <md-button @click="reply(chat.sender.id)">回复</md-button>
                    </md-card-actions>
                </md-card>
            </md-tab>
            <md-tab id="tab-outbox" md-label="发件箱">
                <md-empty-state
                        v-if="outbox.length == 0"
                        md-icon="devices_other"
                        md-label="发件箱内没有消息"
                        md-description="要不去找一个人聊聊？">
                </md-empty-state>
                <md-card v-for="chat in outbox" :key="chat.id">
                    <md-card-content>
                        <vue-markdown>{{chat.content}}</vue-markdown>
                        <md-divider></md-divider>
                        <span class="md-caption">To: {{chat.receiver.username}}@{{chat.time | moment("lll")}}</span>
                    </md-card-content>
                </md-card>
            </md-tab>
            <md-tab id="tab-new" md-label="撰写信息">
                <md-card>
                    <md-card-content>
                        <md-field>
                            <label>收件用户ID</label>
                            <md-input v-model="receiver"></md-input>
                        </md-field>
                        <markdown-palettes v-model="content" ></markdown-palettes>
                    </md-card-content>
                    <md-card-actions>
                        <md-button @click="send">发送</md-button>
                    </md-card-actions>
                </md-card>

            </md-tab>
        </md-tabs>
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
            inboxPage: 1,
            outboxPage: 1,
            active: "tab-inbox",
            content: "\n\n\n\n\n\n\n\n\n\n\n\n\n",
            receiver: null,
            inbox: [],
            outbox: [],
            active: null
        }),
        mounted: function() {
            this.listInbox(1)
            this.listOutbox(1)
            this.$emit("changeTitle","消息")
            if(this.$route.params["id"] != null){
                this.receiver = this.$route.params["id"]
                this.active = "tab-new"
            }
        },
        methods: {
            listInbox() {
                this.axios.get("/chat/inbox",{
                    params: {
                        page: this.inboxPage
                    }
                }).then((response) => {
                    if(this.inboxPage == 1)
                        this.inbox = response.data["data"]
                    else
                        this.inbox = this.inbox.concat(response.data["data"])
                })

            },
            listOutbox(page){
                this.axios.get("/chat/outbox",{
                    params: {
                        page: this.outboxPage
                    }
                }).then((response) => {
                    if(this.outboxPage == 1)
                        this.outbox = response.data["data"]
                    else
                        this.outbox = this.outbox.concat(response.data["data"])
                })
            },
            send(){
                this.axios.post("/chat/send",{
                    id: this.receiver,
                    content: this.content
                }).then((response) => {
                    this.showMsg("发送成功")
                    this.active = "tab-outbox"
                    this.outboxPage = 1
                    this.listOutbox()
                }).catch((error) => {
                    this.showMsg("发送失败，请检查您是否完成实名认证，或是接受用户是否存在！")
                })
                this.receiver = null
                this.content = ""
            },
            reply(id){
                this.active = "tab-new"
                this.content = ""
                this.receiver = id
            },
            showMsg(message) {
                this.message = message
                this.showMessage = true
            },
            change(id){
                this.active = id
                if (id == "tab-new")
                    this.content = ""
            },
            loadMore(){
                if(this.active == "tab-inbox"){
                    this.inboxPage ++
                    this.listInbox()
                }else if(this.active == "tab-outbox"){
                    this.outboxPage ++
                    this.listOutbox()
                }
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