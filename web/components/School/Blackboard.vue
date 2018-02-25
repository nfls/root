<template>
    <div class="class" v-infinite-scroll="loadMore" :infinite-scroll-disabled="empty || loading">
        <div class="info" v-if="!empty" >
            <md-card>
                <md-card-content style="align:left;">
                    <md-field>
                        <label>我要看这块黑板：</label>
                        <md-select v-model="currentClass">
                            <md-option v-for="cla in claz" :value="cla.id" :key="cla.id">{{cla.title}}</md-option>
                        </md-select>
                    </md-field>

                </md-card-content>
            </md-card>
            <md-card>
                <md-card-header>
                    <div class="md-title">{{classInfo.title}}</div>
                </md-card-header>
                <md-card-content style="text-align:left;align:left;">
                    <span>老师：</span><span v-html="classInfo.teacher.htmlUsername"></span>
                    <md-divider></md-divider>
                    <vue-markdown>{{classInfo.announcement}}</vue-markdown>
                </md-card-content>
            </md-card>
            <md-card>
                <md-card-header>
                    <div class="md-title">截止日期</div>
                </md-card-header>
                <md-card-content>
                    <calendar-view
                            :events="classInfo.deadlines"
                            :disable-past="false"
                            :disable-future="false"
                            :show-date="showDate"
                            @setShowDate="setShowDate"
                    />
                </md-card-content>
            </md-card>
            <md-card v-for="notice in classInfo.notices" :key="notice.id">
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
                    <span class="md-caption" v-if="notice.preview">未发布&nbsp;</span>
                    <span class="md-caption">{{notice.time | moment("lll")}}</span>
                </md-card-content>
                <md-card-actions v-if="classInfo.admin">
                    <md-button @click="deadline(notice.id)" v-if="notice.deadline">截止日期提醒</md-button>
                    <md-button @click="removeNotice(notice.id)">删除</md-button>
                </md-card-actions>
            </md-card>
            <!--Admin-->
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
                            <md-input v-model="post.title" :disabled="sending" :md-counter="20"/>
                        </md-field>
                        <span>发布日期（留空为立即发布）<datetime v-model="post.time" type="datetime" :disabled="sending"></datetime></span>
                    </form>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-primary" @click="close" :disabled="sending">取消</md-button>
                    <md-button class="md-primary" @click="submit" :disabled="sending">发布</md-button>
                </md-dialog-actions>
            </md-dialog>

            <md-dialog :md-active.sync="showAdmin" class="admin-class">
                <md-dialog-title>管理</md-dialog-title>
                <md-dialog-content>
                    <md-tabs md-dynamic-height :md-active-tab="active" @md-changed="tabChanged">
                        <md-tab id="tab-list" md-label="列表" >

                        </md-tab>
                        <md-tab id="tab-add" md-label="添加">

                        </md-tab>
                        <md-tab id="tab-edit" md-label="配置" >

                        </md-tab>
                    </md-tabs>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-primary" @click="showAdmin = false">关闭</md-button>
                </md-dialog-actions>
            </md-dialog>
        </div>
        <div v-else>
            <md-empty-state
                    md-icon="access_time"
                    md-label="什么都没有发现"
                    md-description="看起来你还没有加入任何课堂呢，或者再等等？">
            </md-empty-state>

        </div>
        <!-- Admin Part-->
        <md-dialog-prompt
                :md-active.sync="showNewClass"
                v-model="classTitle"
                md-title="创建一个新黑板"
                md-input-maxlength="20"
                md-input-placeholder="起个名字吧"
                md-confirm-text="提交"
                md-cancel-text="取消"
                @md-confirm="newClass"/>
        <md-speed-dial class="md-top-right" style="margin-top: 60px;" md-effect="opacity" md-direction="bottom" v-if="eligibility">
            <md-speed-dial-target class="md-plain">
                <md-icon>edit</md-icon>
            </md-speed-dial-target>

            <md-speed-dial-content>
                <md-button class="md-icon-button" @click="showNewClass = !showNewClass">
                    <md-icon>fiber_new</md-icon>
                </md-button>
                <md-button class="md-icon-button" @click="showNewPost = !showNewPost" v-if="classInfo && classInfo.admin">
                    <md-icon>create</md-icon>
                </md-button>
                <md-button class="md-icon-button" @click="showAdmin = !showAdmin" v-if="classInfo && classInfo.admin">
                    <md-icon>person</md-icon>
                </md-button>
            </md-speed-dial-content>
        </md-speed-dial>


    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'
    import MarkdownPalettes from 'markdown-palettes'
    import vueJsonEditor from 'vue-json-editor'
    import { Datetime } from 'vue-datetime'
    import CalendarView from "vue-simple-calendar"
    import infiniteScroll from 'vue-infinite-scroll'
    export default {
        name: "Blackboard",
        props: ["admin","verified",'loggedIn','gResponse'],
        directives: {infiniteScroll},
        components: {
            VueMarkdown,
            MarkdownPalettes,
            vueJsonEditor,
            CalendarView,
            datetime: Datetime
        },
        data: () => ({
            eligibility: false,
            showDate: new Date(),
            currentClass: null,
            empty: true,
            claz: [],
            classTitle: [],
            classInfo: null,
            showNewPost: false,
            showNewClass: false,
            post: {},
            sending: false,
            showAdmin: false,
            loading: false,
            active: "",
            page: 1,
            sendId: null,
            moment: require('moment-timezone')
        }),
        mounted: function (){
            this.$emit("changeTitle","Blackboard")
            this.$emit("prepareRecaptcha")
            this.currentClass = this.$route.params["id"]
            this.init()
            this.resetForm()
        },
        methods: {
            init() {

                this.check()
                this.axios.get("/school/blackboard/list").then((response) => {
                    this.claz = response.data["data"]
                    this.list()
                })
            },
            check(){
                this.axios.get("/school/blackboard/eligibility").then((response) => {
                    this.eligibility = response.data["data"]
                })
            },
            list() {
                this.empty = true
                this.axios.get("/school/blackboard/detail?id="+this.currentClass).then((response) => {
                    this.page = 1
                    this.classInfo = response.data["data"]
                    var moment = require('moment-timezone');
                    this.classInfo.deadlines = this.classInfo.deadlines.map(function(val){
                        val.startDate = moment(val.time).toDate()
                        return val
                    })
                    this.classInfo.notices.map(function(val){
                        val.preview = moment(val.time).toDate() > new Date()
                        return val
                    })
                    this.info.title = this.classInfo.title
                    this.info.announcement = this.classInfo.announcement
                    this.empty = false
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
                    files: [],
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
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
                this.$emit("showMsg",msg)
            },
            remove(id){
                this.active = "tab-add"
                this.axios.post("/school/blackboard/edit?id="+this.currentClass,{
                    id: id,
                    remove: true
                }).then((response)=>{
                    this.active = "tab-list"
                    this.list()
                })
            },
            removeNotice(id){
                this.axios.post("/school/blackboard/delete?id="+this.currentClass,{
                    id: id
                }).then((response)=>{
                    this.list()
                })
            },
            add(id){
                this.axios.post("/school/blackboard/edit?id="+this.currentClass,{
                    id: id,
                    add: true
                }).then((response)=>{
                    this.list()
                })
            },
            send(id){
                this.$router.push("/user/message/"+id)
            },
            setShowDate(d) {
                this.showDate = d;
            },
            newClass(val) {
                //console.log(val)
                this.axios.post("/school/blackboard/create",{
                    title: this.classTitle
                }).then((response) => {
                    this.init()
                })
            },
            loadMore(){
                //console.log("aa")
                this.loading = true
                this.page ++
                this.axios.get("/school/blackboard/detail",{
                    params: {
                        id: this.currentClass,
                        page: this.page
                    }
                }).then((response) => {
                    this.loading = false
                    var moment = require('moment-timezone');
                    var info = response.data["data"].map(function(val){
                        val.preview = moment(val.time).toDate() > new Date()
                        return val
                    })
                    //console.log(info)
                    this.classInfo.notices = this.classInfo.notices.concat(info)
                        //console.log(this.classInfo.notices)
                })
            },
            deadline(id) {
                this.sendId = id
                grecaptcha.execute()
            },
            tabChanged(newVal){
                console.log(newVal)
                if(newVal == "tab-edit"){
                    var old = this.info.announcement
                    this.info.announcement = "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
                    setTimeout( () => {
                        this.info.announcement = old;
                },500)

                }
            }
        },
        watch: {
            currentClass: {
                handler: function(newVal){
                    this.$router.push("/school/blackboard/"+newVal)
                    this.list()
                }
            },
            gResponse: {
                handler: function(newVal){
                    this.axios.post("/school/blackboard/notify?id="+this.currentClass, {
                        captcha: grecaptcha.getResponse(),
                        id: this.sendId
                    }).then((response) => {
                        this.showMsg("发送成功")
                    }).catch((error) => {
                        this.showMsg("发送失败")
                    })
                }
            }
        }
    }
</script>

<style scoped>
    .new-post{
        min-width: 500px;
    }
    .admin-class{
        min-width: 200px;
    }
    .md-card{
        margin:10px;
    }
    .calendar-view {
        .header {
            display: none
        }
    }
    .md-list {
        max-width: 100%;
        display: inline-block;
        vertical-align: top;
    }

</style>