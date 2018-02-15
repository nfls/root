<template>
    <div class="class">
        <div class="info" v-if="!empty">
            <md-card>
                <md-card-content style="align:left;">
                    <md-field>
                        <label>我要看这块黑板：</label>
                        <md-select v-model="currentClass" v-for="cla in claz" key="currentClass">
                            <md-option :value="cla.id" :key="cla.id">{{cla.title}}</md-option>
                        </md-select>
                    </md-field>

                </md-card-content>
            </md-card>
            <md-card>
                <md-card-header>
                    <div class="md-title">{{classInfo.title}}</div>
                </md-card-header>



                <md-card-content style="text-align:left;align:left;">
                    <span>老师：{{classInfo.teacher.username}}</span>
                    <md-divider></md-divider>
                    <vue-markdown>{{classInfo.announcement}}</vue-markdown>
                </md-card-content>
            </md-card>
            <md-card v-for="notice in classInfo.notices" :key="notice.id" style="margin:10px">
                <md-card-content style="text-align:left;align:left;">
                    <vue-markdown>{{notice.content}}</vue-markdown>
                    <div v-if="notice.deadline">
                        <md-divider></md-divider>
                        <span class="md-caption">截止日期</span><br/>
                        <span class="md-body-2">{{notice.deadline | moment("lll")}}</span><br/>
                        <span class="md-body-1">{{notice.title}}</span>
                    </div>
                    <div v-if="notice.attachment.length > 0">
                        <md-divider></md-divider>
                        <span class="md-caption">附件</span>
                        <md-button class="md-primary" v-for="href in notice.attachment" :href="href.href" :key="href.href" target="_blank">{{href.name}}</md-button>
                    </div>
                    <md-divider></md-divider>
                    <span class="md-caption">{{notice.time | moment("lll")}}</span>
                </md-card-content>
            </md-card>
            <!-- Admin Part-->
            <md-speed-dial class="md-bottom-right" md-effect="opacity" md-direction="top">
                <md-speed-dial-target class="md-plain">
                    <md-icon>edit</md-icon>
                </md-speed-dial-target>

                <md-speed-dial-content>
                    <md-button class="md-icon-button" @click="showNewPost = !showNewPost">
                        <md-icon>create</md-icon>
                    </md-button>

                    <md-button class="md-icon-button">
                        <md-icon>person</md-icon>
                    </md-button>
                </md-speed-dial-content>
            </md-speed-dial>

            <md-dialog :md-active.sync="showNewPost" class="new-post" style="width:80%" :md-close-on-esc="false" :md-click-outside-to-close="false">
                <md-dialog-title>新的公告</md-dialog-title>
                <md-dialog-content>
                    <markdown-palettes v-model="post.content" :disabled="sending"></markdown-palettes>
                    <form>
                        <md-field>
                            <label>附件</label>
                            <md-file v-model="post.file" multiple @md-change="changeUpload" :disabled="sending"/>
                        </md-field>
                        <span>截止日期（留空为没有）<datetime v-model="post.deadline" type="datetime" :disabled="sending"></datetime></span>
                        <md-field>
                            <label>用于提示截止日期的小标题</label>
                            <md-input v-model="post.title" :disabled="sending" />
                        </md-field>
                        <span>发布日期（留空为立即发布）<datetime v-model="post.time" type="datetime" :disabled="sending"></datetime></span>
                    </form>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-primary" @click="close" :disabled="sending">取消</md-button>
                    <md-button class="md-primary" @click="submit" :disabled="sending">发布</md-button>
                </md-dialog-actions>
            </md-dialog>
            <md-snackbar :md-active.sync="showSnackBar" md-persistent>
                <span>{{message}}</span>
            </md-snackbar>
        </div>
    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'
    import MarkdownPalettes from 'markdown-palettes'
    import vueJsonEditor from 'vue-json-editor'
    import { Datetime } from 'vue-datetime';
    export default {
        name: "Blackboard",
        props: ["admin","verified",'loggedIn'],
        components: {
            VueMarkdown,
            MarkdownPalettes,
            vueJsonEditor,
            datetime: Datetime
        },
        data: () => ({
            currentClass: null,
            empty: true,
            claz: [],
            classInfo: null,
            showNewPost: false,
            post: {},
            sending: false,
            message: "",
            showSnackBar: false
        }),
        mounted: function (){
            this.$emit("changeTitle","Blackboard")
            this.currentClass = this.$route.params["id"]
            this.init()
            this.resetForm()
        },
        methods: {
            init() {
                this.axios.get("/school/blackboard/list").then((response) => {
                    this.claz = response.data["data"]
                    this.list()
                })
            },
            list() {
                this.axios.get("/school/blackboard/detail?id="+this.currentClass).then((response) => {
                    this.empty = false
                    this.classInfo = response.data["data"]
                }).catch((error) => {
                    if(this.claz.length > 0)
                        this.currentClass = this.claz[0].id
                })
            },
            submit() {
                this.upload(0)
            },
            upload(index){
                this.sending = true
                if(index >= this.post.toUpload.length){
                    this.post.toUpload = []
                    this.sendPost()
                    return
                }
                var file = this.post.toUpload[index]
                var name = this.getPreffix(file.name) + "." + this.randomString(8) + this.getSuffix(file.name)
                this.axios.get("/school/blackboard/signature",{
                    params: {
                        object: name,
                        type: file.type
                    }
                }).then((response) => {
                    console.log(file)
                    let header = {
                        headers: {
                            "Authorization": "Signature",
                            "Content-Type": file.type
                        }
                    }
                    this.axios.put(response.data["data"],file,header).then((response) => {
                        this.post.files.push(name)
                        this.upload(index+1)
                    }).catch(error => {
                        this.sending = false
                        this.showMsg("文件上传失败")
                    });
                })
            },
            sendPost(){
                this.axios.post("/school/blackboard/post?id="+this.currentClass,this.post).then((response) => {
                    this.resetForm()
                    this.showNewPost = false
                    this.sending = false
                    this.showMsg("发布成功")
                    this.list()
                }).catch((error) => {
                    this.sending = false
                    this.showMsg("你的公告中存在一些问题，请检查每项是否填写正确")
                })
            },
            close(){
                this.showNewPost = false
            },
            changeUpload(list) {
                this.post.toUpload = list
                this.post.files = []
            },
            resetForm() {
                this.post = {
                    content: null,
                    title: null,
                    deadline: "",
                    time: "",
                    toUpload: [],
                    file: [],
                    files: []
                }
            },
            randomString(len) {
                len = len || 32;
                var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
                var maxPos = chars.length;
                var pwd = '';
                for (var i = 0; i < len; i++) {
                    pwd += chars.charAt(Math.floor(Math.random() * maxPos));
                }
                return pwd;
            },
            getSuffix(filename) {
                var pos = filename.lastIndexOf('.');
                var suffix = '';
                if (pos != -1) {
                    suffix = filename.substring(pos)
                }
                return suffix;
            },
            getPreffix(filename) {
                var pos = filename.lastIndexOf('.');
                var suffix = '';
                if (pos != -1) {
                    suffix = filename.substring(0,pos)
                }
                return suffix;
            },
            showMsg(msg){
                this.showSnackBar = true
                this.message = msg
            }
        },
        watch: {
            currentClass: {
                handler: function(newVal){
                    this.$router.push("/school/blackboard/"+newVal)
                    this.list()
                }
            }
        }
    }
</script>

<style scoped>
    .new-post{
        min-width: 500px;
    }
</style>