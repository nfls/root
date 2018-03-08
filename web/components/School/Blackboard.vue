<i18n src="../../translation/frontend/School.json"></i18n>
<template>
    <div class="class" v-infinite-scroll="loadMore" :infinite-scroll-disabled="empty || loading">
        <div class="info" v-if="!empty">
            <md-card>
                <md-card-content style="align:left;">
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
            <md-dialog :md-active.sync="showNewPost" class="new-post" style="width:80%" :md-close-on-esc="false"
                       :md-click-outside-to-close="false">
                <md-dialog-title>{{ $t('new-note') }}</md-dialog-title>

                <md-dialog-content>
                    <span>{{ $t('google-docs' )}}<a href="https://dev.nfls.io/confluence/x/AQAO"
                                                                       target="_blank">Confluence</a></span>
                    <markdown-palettes v-model="post.content" :disabled="sending"></markdown-palettes>
                    <form>
                        <md-field>
                            <label>{{ $t('attachment' )}}</label>
                            <md-file v-model="post.file" multiple @md-change="changeUpload" :disabled="sending"/>
                        </md-field>
                        <span>{{ $t('deadline-entry') }}<datetime v-model="post.deadline" type="datetime"
                                                   :disabled="sending"></datetime></span>
                        <md-field>
                            <label>{{ $t('title-entry') }}</label>
                            <md-input v-model="post.title" :disabled="sending" :md-counter="20"/>
                        </md-field>
                        <span>{{ $t('releasing-date') }}<datetime v-model="post.time" type="datetime" :disabled="sending"></datetime></span>
                    </form>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-primary" @click="close" :disabled="sending">{{ $t('cancel') }}</md-button>
                    <md-button class="md-primary" @click="submit" :disabled="sending">{{ $t('release') }}</md-button>
                </md-dialog-actions>
            </md-dialog>

            <md-dialog :md-active.sync="showAdmin" class="admin-class">
                <md-dialog-title>{{ $t('admin') }}</md-dialog-title>
                <md-dialog-content>
                    <form>
                        <md-field>
                            <labe>{{ $t('title') }}</labe>
                            <md-input id="seniorSchool" v-model="info.title" name="title"/>
                        </md-field>
                        <span class="md-caption">{{ $t('announcement') }}</span>
                        <markdown-palettes v-model="info.announcement"></markdown-palettes>
                    </form>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-accent" @click="showAdmin = false;showDestroy = true">{{ $t('remove') }}</md-button>
                    <md-button class="md-primary" @click="preference">{{ $t('submit') }}</md-button>
                    <md-button class="md-primary" @click="showAdmin = false">{{ $t('close') }}</md-button>
                </md-dialog-actions>
            </md-dialog>
            <md-dialog :md-active.sync="showListStu">
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
                            <md-button class="md-icon-button md-list-action" @click="remove(student.id)">
                                <md-icon>delete</md-icon>
                            </md-button>
                            <md-button class="md-icon-button md-list-action" @click="send(student.id)">
                                <md-icon>send</md-icon>
                            </md-button>
                        </md-list-item>
                    </md-list>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button @click="showAddStu = true">{{ $t('add-new') }}</md-button>
                    <md-button @click="showListStu = false">{{ $t('close') }}</md-button>
                </md-dialog-actions>
            </md-dialog>
            <md-dialog :md-active.sync="showAddStu">
                <md-dialog-title>{{ $t('add') }}</md-dialog-title>
                <md-dialog-content>
                    <div>
                        <md-radio v-model="form.seniorSchool" value="2">{{ $t('ib') }}</md-radio>
                        <md-radio v-model="form.seniorSchool" value="3">{{ $t('alevel') }}</md-radio>
                    </div>
                    <md-field style="width:200px;">
                        <label>{{ $t('senior-graduation') }}</label>
                        <md-input v-model="form.seniorRegistration" id="seniorRegistration" name="seniorRegistration"/>
                    </md-field>
                    <md-button @click="search">{{ $t('search') }}</md-button>
                    <br/>
                    <md-list>
                        <md-list-item v-for="student in studentsInfo" :key="student.id">
                            <md-avatar>
                                <img :src="'/avatar/' + student.id + '.png'" alt="Avatar">
                            </md-avatar>
                            <div class="md-list-item-text">
                                <span v-html="student.htmlUsername"></span>
                            </div>
                            <md-button class="md-icon-button md-list-action" @click="add(student.id)">
                                <md-icon>add</md-icon>
                            </md-button>
                        </md-list-item>
                    </md-list>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button @click="showAddStu = false">{{ $t('done') }}</md-button>
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

    export default {
        name: "Blackboard",
        props: ["admin", "verified", 'loggedIn', 'gResponse'],
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
            showAdmin: false,
            showAddStu: false,
            showListStu: false,
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
            moment: require('moment-timezone'),
            showDestroy: false
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t("blackboard-title"))
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
                }).catch((error) => {
                    this.$router.push("/user/login")
                })
            },
            check() {
                this.axios.get("/school/blackboard/eligibility").then((response) => {
                    this.eligibility = response.data["data"]
                }).catch((error) => {
                    this.$router.push("/user/login")
                })
            },
            list() {
                this.empty = true
                this.axios.get("/school/blackboard/detail?id=" + this.currentClass).then((response) => {
                    this.page = 1
                    this.classInfo = response.data["data"]
                    var moment = require('moment-timezone');
                    this.classInfo.deadlines = this.classInfo.deadlines.map(function (val) {
                        val.startDate = moment(val.time).toDate()
                        return val
                    })
                    this.classInfo.notices.map(function (val) {
                        val.preview = moment(val.time).toDate() > new Date()
                        return val
                    })
                    this.info.title = this.classInfo.title
                    this.info.announcement = this.classInfo.announcement
                    this.empty = false
                    this.getCsrf()
                }).catch((error) => {
                    if (this.claz.length > 0)
                        this.currentClass = this.claz[0].id
                    else
                        this.getCsrf()
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
                    console.log(file)
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
                        this.showMsg(this.$t('upload-failed'))
                    });
                })
            },
            sendPost() {
                this.post._csrf = this.csrf
                this.axios.post("/school/blackboard/post?id=" + this.currentClass, this.post).then((response) => {
                    this.resetForm()
                    this.showNewPost = false
                    this.sending = false
                    this.showMsg(this.$t('release-succeeded'))
                    this.list()
                }).catch((error) => {
                    this.sending = false
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
                if (pos != -1) {
                    suffix = filename.substring(pos)
                }
                return suffix;
            },
            getPreffix(filename) {
                var pos = filename.lastIndexOf('.');
                var suffix = '';
                if (pos != -1) {
                    suffix = filename.substring(0, pos)
                }
                return suffix;
            },
            showMsg(msg) {
                this.$emit("showMsg", msg)
            },
            remove(id) {
                this.active = "tab-add"
                this.axios.post("/school/blackboard/edit?id=" + this.currentClass, {
                    id: id,
                    remove: true,
                    _csrf: this.csrf
                }).then((response) => {
                    this.list()
                })
            },
            removeNotice(id) {
                this.axios.post("/school/blackboard/delete?id=" + this.currentClass, {
                    id: id,
                    _csrf: this.csrf
                }).then((response) => {
                    this.list()
                })
            },
            add(id) {
                this.axios.post("/school/blackboard/edit?id=" + this.currentClass, {
                    id: id,
                    add: true,
                    _csrf: this.csrf
                }).then((response) => {
                    this.$emit("showMsg", this.$t('add-succeeded') )
                    this.list()
                })
            },
            destroy() {
                this.axios.post("/school/blackboard/preference?id=" + this.currentClass, {
                    "delete": true,
                    _csrf: this.csrf
                }).then((response) => {
                    this.showAdmin = false
                    this.init()
                })
            },
            preference() {
                this.info._csrf = this.csrf
                this.axios.post("/school/blackboard/preference?id=" + this.currentClass, this.info).then((response) => {
                    this.init()
                    this.showAdmin = false
                })
            },
            search() {
                this.active = "tab-list"
                this.form._csrf = this.csrf
                this.axios.post("/alumni/directory/search", this.form).then((response) => {
                    this.studentsInfo = response.data["data"]
                    this.active = "tab-add"
                })
            },
            send(id) {
                this.$router.push("/user/message/" + id)
            },
            setShowDate(d) {
                this.showDate = d;
            },
            newClass(val) {
                //console.log(val)
                this.axios.post("/school/blackboard/create", {
                    title: this.classTitle,
                    _csrf: this.csrf
                }).then((response) => {
                    this.init()
                })
            },
            loadMore() {
                //console.log("aa")
                this.loading = true
                this.page++
                this.axios.get("/school/blackboard/detail", {
                    params: {
                        id: this.currentClass,
                        page: this.page
                    }
                }).then((response) => {
                    this.loading = false
                    var moment = require('moment-timezone');
                    var info = response.data["data"].map(function (val) {
                        val.preview = moment(val.time).toDate() > new Date()
                        return val
                    })
                    this.classInfo.notices = this.classInfo.notices.concat(info)
                    this.getCsrf()
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
                        this.showMsg(this.$t('send-succeeded'))
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

</style>