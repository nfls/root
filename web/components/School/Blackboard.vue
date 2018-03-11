<i18n src="../../translation/frontend/School.json"></i18n>
<template>
    <div class="class" v-infinite-scroll="loadMore" :infinite-scroll-disabled="empty || loading" :infinite-scroll-immediate-check="false">
        <div class="info" v-if="!empty">
            <md-card>
                <md-card-content style="align:left;">
                    <md-progress-bar md-mode="indeterminate" v-if="sending"/>
                    <md-field>
                        <label>{{ $t('current-blackboard') }}</label>
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
                    <span>{{ $t('teacher') }}</span><span v-html="classInfo.teacher.htmlUsername"></span>
                    <md-divider></md-divider>
                    <vue-markdown>{{classInfo.announcement}}</vue-markdown>
                </md-card-content>
            </md-card>
            <md-card>
                <md-card-header>
                    <div class="md-title">{{ $t('calender') }}</div>
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
                        <span class="md-caption">{{ $t('deadline') }}</span><br/>
                        <span class="md-body-2">{{notice.deadline | moment("lll")}}</span><br/>
                        <span class="md-body-1">{{notice.title}}</span>
                    </div>
                    <div v-if="notice.attachment.length > 0">
                        <md-divider></md-divider>
                        <span class="md-caption">{{ $t('attachment') }}</span>
                        <md-button class="md-primary" v-for="href in notice.attachment" :href="href.href"
                                   :key="href.href" target="_blank">{{href.name}}
                        </md-button>
                    </div>
                    <md-divider></md-divider>
                    <span class="md-caption" v-if="notice.preview">{{ $t('unreleased') }}&nbsp;</span>
                    <span class="md-caption">{{notice.time | moment("lll")}}</span>
                </md-card-content>
                <md-card-actions v-if="classInfo.admin">
                    <md-button @click="deadline(notice.id)" v-if="notice.deadline">{{ $t('deadline-notify') }}</md-button>
                    <md-button @click="removeNotice(notice.id)">{{ $t('delete') }}</md-button>
                </md-card-actions>
            </md-card>
            <!--Admin-->
            <md-dialog :md-active.sync="showNewPost" class="new-post" :md-close-on-esc="false"
                       :md-click-outside-to-close="false">
                <md-progress-bar md-mode="indeterminate" v-if="sending"/>
                <md-dialog-title>{{ $t('new-note') }}</md-dialog-title>
                <md-dialog-content>
                    <span>{{ $t('google-docs' )}}<a href="https://dev.nfls.io/confluence/x/AQAO"
                                                                       target="_blank">Confluence</a></span>
                    <markdown-palettes v-model="post.content"></markdown-palettes>
                    <form>
                        <md-field>
                            <label>{{ $t('attachment' )}}</label>
                            <md-file v-model="post.file" multiple @md-change="changeUpload" :disabled="sending"/>
                        </md-field>
                        <span>{{ $t('deadline-entry') }}<datetime v-model="post.deadline" type="datetime" :disabled="sending"></datetime></span>
                        <md-field>
                            <label>{{ $t('title-entry') }}</label>
                            <md-input v-model="post.title" :disabled="sending" :md-counter="20"/>
                        </md-field>
                        <span>{{ $t('releasing-date') }}<datetime v-model="post.time" type="datetime" :disabled="sending"></datetime></span>
                    </form>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-primary" @click="showUpload = true" :disabled="sending">{{ $t('image-upload') }}</md-button>
                    <md-button class="md-primary" @click="close" :disabled="sending">{{ $t('cancel') }}</md-button>
                    <md-button class="md-primary" @click="submit" :disabled="sending">{{ $t('release') }}</md-button>
                </md-dialog-actions>
            </md-dialog>

            <md-dialog :md-active.sync="showAdmin" class="new-post">
                <md-progress-bar md-mode="indeterminate" v-if="sending"/>
                <md-dialog-title>{{ $t('admin') }}</md-dialog-title>
                <md-dialog-content>
                    <form>
                        <md-field>
                            <label>{{ $t('title') }}</label>
                            <md-input id="seniorSchool" v-model="info.title" name="title" :disabled="sending"/>
                        </md-field>
                        <span class="md-caption">{{ $t('announcement') }}</span>
                        <markdown-palettes v-model="info.announcement"></markdown-palettes>
                    </form>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-accent" @click="showAdmin = false;showDestroy = true" :disabled="sending">{{ $t('remove') }}</md-button>
                    <md-button class="md-primary" @click="showUpload = true" :disabled="sending">{{ $t('image-upload') }}</md-button>
                    <md-button class="md-primary" @click="preference" :disabled="sending">{{ $t('submit') }}</md-button>
                    <md-button class="md-primary" @click="showAdmin = false" :disabled="sending">{{ $t('close') }}</md-button>
                </md-dialog-actions>
            </md-dialog>
            <md-dialog :md-active.sync="showListStu" class="new-post">
                <md-progress-bar md-mode="indeterminate" v-if="sending"/>
                <md-dialog-title>{{ $t('user-list') }}</md-dialog-title>
                <md-dialog-content>
                    <span class="md-caption">{{ $t('list-warning') }}</span><br/>
                    <md-list>
                        <md-list-item v-for="student in classInfo.students" :key="student.id">
                            <md-avatar>
                                <img :src="'/avatar/' + student.id + '.png'" alt="Avatar">
                            </md-avatar>
                            <div class="md-list-item-text">
                                <span v-html="student.htmlUsername"></span>
                            </div>
                            <md-button class="md-icon-button md-list-action" @click="remove(student.id)" :disabled="sending"><md-icon>delete</md-icon></md-button>
                            <md-button class="md-icon-button md-list-action" @click="send(student.id)" :disabled="sending"><md-icon>send</md-icon></md-button>
                        </md-list-item>
                    </md-list>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button @click="showAddStu = true" :disabled="sending">{{ $t('add-new') }}</md-button>
                    <md-button @click="showListStu = false" :disabled="sending">{{ $t('close') }}</md-button>
                </md-dialog-actions>
            </md-dialog>
            <md-dialog :md-active.sync="showAddStu" class="new-post">
                <md-progress-bar md-mode="indeterminate" v-if="sending"/>
                <md-dialog-title>{{ $t('add') }}</md-dialog-title>
                <md-dialog-content>
                    <div>
                        <md-radio v-model="form.seniorSchool" value="2" :disabled="sending">{{ $t('ib') }}</md-radio>
                        <md-radio v-model="form.seniorSchool" value="3" :disabled="sending">{{ $t('alevel') }}</md-radio>
                    </div>
                    <md-field style="width:200px;">
                        <label>{{ $t('senior-graduation') }}</label>
                        <md-input v-model="form.seniorRegistration" id="seniorRegistration" name="seniorRegistration" :disabled="sending"/>
                    </md-field>
                    <md-button @click="search" :disabled="sending">{{ $t('search') }}</md-button>
                    <br/>
                    <md-list>
                        <md-list-item v-for="student in studentsInfo" :key="student.id">
                            <md-avatar>
                                <img :src="'/avatar/' + student.id + '.png'" alt="Avatar">
                            </md-avatar>
                            <div class="md-list-item-text">
                                <span v-html="student.htmlUsername"></span>
                            </div>
                            <md-button class="md-icon-button md-list-action" @click="add(student.id)" :disabled="sending"><md-icon>add</md-icon></md-button>
                        </md-list-item>
                    </md-list>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button @click="showAddStu = false" :disabled="sending">{{ $t('done') }}</md-button>
                </md-dialog-actions>
            </md-dialog>
            <md-dialog :md-active.sync="showUpload" class="new-post">
                <md-dialog-title>{{ $t('image-upload') }}</md-dialog-title>
                <md-dialog-content>
                    <upload-page></upload-page>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button @click="showUpload = false">{{ $t('done') }}</md-button>
                </md-dialog-actions>
            </md-dialog>
        </div>
        <div v-else>
            <md-empty-state
                    md-icon="access_time"
                    :md-label="$t('nothing')"
                    :md-description="$t('waiting')">
            </md-empty-state>

        </div>
        <!-- Admin Part-->
        <md-dialog-prompt
                :md-active.sync="showNewClass"
                v-model="classTitle"
                :md-title="$t('create-new')"
                md-input-maxlength="20"
                :md-input-placeholder="$t('naming')"
                :md-confirm-text="$t('confirm')"
                :md-cancel-text="$t('cancel')"
                @md-confirm="newClass"/>

        <md-dialog-confirm
                :md-active.sync="showDestroy"
                :md-title="$t('removal-title')"
                :md-content="$t('removal')"
                :md-confirm-text="$t('confirm')"
                :md-cancel-text="$t('cancel')"
                @md-confirm="destroy"/>


        <md-speed-dial class="md-top-right" style="margin-top: 60px;" md-effect="opacity" md-direction="bottom"
                       v-if="eligibility">
            <md-speed-dial-target class="md-plain">
                <md-icon>edit</md-icon>
            </md-speed-dial-target>

            <md-speed-dial-content>
                <md-button class="md-icon-button" @click="showNewClass = !showNewClass">
                    <md-icon>fiber_new</md-icon>
                    <md-tooltip md-direction="left">{{ $t('create-new') }}</md-tooltip>
                </md-button>
                <md-button class="md-icon-button" @click="showNewPost = !showNewPost"
                           v-if="classInfo && classInfo.admin">
                    <md-icon>create</md-icon>
                    <md-tooltip md-direction="left">{{ $t('new-note') }}</md-tooltip>
                </md-button>
                <md-button class="md-icon-button" @click="showListStu = !showListStu"
                           v-if="classInfo && classInfo.admin">
                    <md-icon>person</md-icon>
                    <md-tooltip md-direction="left">{{ $t('user-list') }}</md-tooltip>
                </md-button>
                <md-button class="md-icon-button" @click="showAdmin = !showAdmin" v-if="classInfo && classInfo.admin">
                    <md-icon>build</md-icon>
                    <md-tooltip md-direction="left">{{ $t('admin') }}</md-tooltip>
                </md-button>
                <md-button class="md-icon-button md-raised" href='https://dev.nfls.io/confluence/x/GAAO'>
                    <md-icon>help</md-icon>
                    <md-tooltip md-direction="left">{{ $t('help') }}</md-tooltip>
                </md-button>
            </md-speed-dial-content>
        </md-speed-dial>


    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'
    import MarkdownPalettes from 'markdown-palettes'
    import vueJsonEditor from 'vue-json-editor'
    import {Datetime} from 'vue-datetime'
    import CalendarView from "vue-simple-calendar"
    import infiniteScroll from 'vue-infinite-scroll'
    import Upload from '../Admin/Upload.vue'
    import UploadPage from "../Admin/Upload"

    export default {
        name: "Blackboard",
        props: ["admin", "verified", 'loggedIn', 'gResponse'],
        directives: {infiniteScroll},
        components: {
            UploadPage,
            VueMarkdown,
            MarkdownPalettes,
            vueJsonEditor,
            CalendarView,
            datetime: Datetime,
            "upload-page": Upload
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
            showAdmin: false,
            showAddStu: false,
            showListStu: false,
            showUpload: false,
            post: {},
            sending: false,
            loading: false,
            page: 1,
            sendId: null,
            csrf: null,
            form: {
                seniorSchool: "2",
                seniorRegistration: "2019"
            },
            info: {
                title: "",
                announcement: ""
            },
            studentsInfo: [],
            showDestroy: false
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t("blackboard-title"))
            this.$emit("prepareRecaptcha")
            this.$moment.locale(this.$i18n.locale)
            this.currentClass = this.$route.params["id"]
            this.init()
            this.resetForm()
        },
        methods: {
            init() {
                this.check()
                this.loading = true
                this.axios.get("/school/blackboard/list").then((response) => {
                    this.claz = response.data["data"]
                    this.list()
                }).catch((error) => {
                    console.error(error)
                    this.$router.push("/user/login")
                })
            },
            check() {
                this.axios.get("/school/blackboard/eligibility").then((response) => {
                    this.eligibility = response.data["data"]
                }).catch((error) => {
                    console.error(error)
                    this.$router.push("/user/login")
                })
            },
            list() {
                this.empty = true
                if(!this.currentClass){
                    if (this.claz.length > 0)
                        this.currentClass = this.claz[0].id
                    return
                }
                this.axios.get("/school/blackboard/detail?id=" + this.currentClass).then((response) => {
                    if(response.data["code"] === 200){
                        this.page = 1
                        this.classInfo = response.data["data"]
                        var self = this
                        this.classInfo.deadlines = this.classInfo.deadlines.map(function (val) {
                            val.startDate = self.$moment(val.time).toDate()
                            return val
                        })
                        this.classInfo.notices.map(function (val) {
                            val.preview = self.$moment(val.time).toDate() > new Date()
                            return val
                        })
                        this.info.title = this.classInfo.title
                        this.info.announcement = this.classInfo.announcement
                        this.empty = false
                    } else {
                        if (this.claz.length > 0)
                            this.currentClass = this.claz[0].id
                        this.$emit("showMsg",response.data["data"])
                    }
                    this.loading = false
                    this.getCsrf()
                }).catch((error) => {
                    this.$emit("generalError",error)
                })
            },
            submit() {
                this.upload(0)
            },
            upload(index) {
                this.sending = true
                if (index >= this.post.toUpload.length) {
                    this.post.toUpload = []
                    this.sendPost()
                    return
                }
                var file = this.post.toUpload[index]
                var name = this.getPreffix(file.name) + "." + this.randomString(8) + this.getSuffix(file.name)
                this.axios.get("/school/blackboard/signature", {
                    params: {
                        object: name,
                        type: file.type
                    }
                }).then((response) => {
                    if(response.data["code"] !== 200) {
                        this.showMsg(response.data["data"])
                    }
                    else {
                        let header = {
                            headers: {
                                "Authorization": "Signature",
                                "Content-Type": file.type
                            }
                        }
                        this.axios.put(response.data["data"], file, header).then((response) => {
                            this.post.files.push(name)
                            this.upload(index + 1)
                        }).catch(error => {
                            this.sending = false
                            console.error(error)
                            this.showMsg(this.$t('upload-failed'))
                        });
                    }

                })
            },
            sendPost() {
                this.post._csrf = this.csrf
                this.axios.post("/school/blackboard/post?id=" + this.currentClass, this.post).then((response) => {
                    if(response.data["code"] === 200){
                        this.resetForm()
                        this.showNewPost = false
                        this.sending = false
                        this.showMsg(this.$t('release-succeeded'))
                    }else{
                        this.showMsg(response.data["data"])
                    }
                    this.list()
                }).catch((error) => {
                    this.sending = false
                    console.error(error)
                    this.showMsg(this.$t('release-failed'))
                })
            },
            close() {
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
                if (pos !== -1) {
                    suffix = filename.substring(pos)
                }
                return suffix;
            },
            getPreffix(filename) {
                var pos = filename.lastIndexOf('.');
                var suffix = '';
                if (pos !== -1) {
                    suffix = filename.substring(0, pos)
                }
                return suffix;
            },
            showMsg(msg) {
                this.$emit("showMsg", msg)
            },
            remove(id) {
                this.sending = true
                this.axios.post("/school/blackboard/edit?id=" + this.currentClass, {
                    id: id,
                    remove: true,
                    _csrf: this.csrf
                }).then((response) => {
                    if(response.data["code"] !== 200)
                        this.showMsg(response.data["data"])
                    this.sending = false
                    this.list()
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError", error)
                })
            },
            removeNotice(id) {
                this.sending = true
                this.axios.post("/school/blackboard/delete?id=" + this.currentClass, {
                    id: id,
                    _csrf: this.csrf
                }).then((response) => {
                    if(response.data["code"] !== 200)
                        this.showMsg(response.data["data"])
                    this.sending = false
                    this.list()
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError", error)
                })
            },
            add(id) {
                this.sending = true
                this.axios.post("/school/blackboard/edit?id=" + this.currentClass, {
                    id: id,
                    add: true,
                    _csrf: this.csrf
                }).then((response) => {
                    if(response.data["code"] !== 200)
                        this.showMsg(response.data["data"])
                    else
                        this.$emit("showMsg", this.$t('add-succeeded'))
                    this.sending = false
                    this.list()
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError", error)
                })
            },
            destroy() {
                this.sending = true
                this.axios.post("/school/blackboard/preference?id=" + this.currentClass, {
                    "delete": true,
                    _csrf: this.csrf
                }).then((response) => {
                    if(response.data["code"] !== 200)
                        this.showMsg(response.data["data"])
                    this.showAdmin = false
                    this.sending = false
                    this.init()
                }).catch((error) => {
                    this.sending = fallse
                    this.$emit("generalError", error)
                })
            },
            preference() {
                this.info._csrf = this.csrf
                this.sending = true
                this.axios.post("/school/blackboard/preference?id=" + this.currentClass, this.info).then((response) => {
                    if(response.data["code"] !== 200)
                        this.showMsg(response.data["data"])
                    this.init()
                    this.sending = false
                    this.showAdmin = false
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError", error)
                })
            },
            search() {
                this.form._csrf = this.csrf
                this.sending = true
                this.axios.post("/alumni/directory/search", this.form).then((response) => {
                    this.sending = false
                    if(response.data["data"] === 200)
                        this.studentsInfo = response.data["data"]
                    else
                        this.showMsg(response.data["data"])
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError", error)
                })
            },
            send(id) {
                this.$router.push("/user/message/" + id)
            },
            setShowDate(d) {
                this.showDate = d;
            },
            newClass(val) {
                this.sending = true
                this.axios.post("/school/blackboard/create", {
                    title: this.classTitle,
                    _csrf: this.csrf
                }).then((response) => {
                    this.sending = false
                    if(response.code["data"] === 200)
                        this.$emit("showMsg",this.$t("new-succeeded"))
                    else
                        this.showMsg(response.data["data"])
                    this.init()
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError", error)
                })
            },
            loadMore() {
                if(!this.currentClass)
                    return
                this.loading = true
                this.page++
                this.axios.get("/school/blackboard/detail", {
                    params: {
                        id: this.currentClass,
                        page: this.page
                    }
                }).then((response) => {
                    this.loading = false
                    if(response.data["data"] === 200)
                    {
                        this.$emit("showMsg",this.$t("new-succeeded"))
                        var self = this
                        var info = response.map(function (val) {
                            val.preview = self.$moment(val.time).toDate() > new Date()
                            return val
                        })
                        this.classInfo.notices = this.classInfo.notices.concat(info)
                    }else{
                        this.$emit("showMsg",response.data["data"])
                    }

                    this.getCsrf()
                }).catch((error) => {
                    this.loading = false
                    this.$emit("generalError", error)
                })
            },
            deadline(id) {
                this.sendId = id
                grecaptcha.execute()
            },
            getCsrf() {
                this.axios.get("user/csrf", {
                    params: {
                        name: "school.blackboard"
                    }
                }).then((response) => {
                    this.csrf = response.data["data"]
                }).catch((error) => {
                    this.$emit("generalError", error)
                })
            }
        },
        watch: {
            currentClass: {
                handler: function (newVal) {
                    this.$router.push("/school/blackboard/" + newVal)
                    this.list()
                }
            },
            gResponse: {
                handler: function (newVal) {
                    this.axios.post("/school/blackboard/notify?id=" + this.currentClass, {
                        captcha: grecaptcha.getResponse(),
                        id: this.sendId,
                        _csrf: this.csrf
                    }).then((response) => {
                        if(response.data["code"] === 200)
                            this.showMsg(this.$t('send-succeeded'))
                        else
                            this.showMsg(response.data["data"])
                    }).catch((error) => {
                        this.showMsg(this.$t('send-failed'))
                    })
                }
            }
        }
    }
</script>

<style scoped>
    .new-post {
        min-width: 500px;
        width: 80%;
    }

    .admin-class {
        min-width: 200px;
    }

    .md-card {
        margin: 10px;
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

    .md-progress-bar {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
    }
</style>