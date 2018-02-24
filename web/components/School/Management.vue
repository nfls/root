<template>
    <div class="management">
        <span class="md-caption">警告：请不要将自己移出课堂之外！</span><br/>
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
        <md-dialog>
            <md-dialog-content>
                <div>
                    <md-radio v-model="form.seniorSchool" value="2">南外IB国际部</md-radio>
                    <md-radio v-model="form.seniorSchool" value="3">南外剑桥国际部</md-radio>
                </div>
                <md-field style="width:200px;">
                    <label for="seniorSchool">高中毕业年份</label>
                    <md-input v-model="form.seniorRegistration" id="seniorRegistration" name="seniorRegistration"/>
                </md-field>
                <md-button @click="search">搜索</md-button><br/>
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
        </md-dialog>
        <md-dialog>
            <md-card>
                <md-card-content>
                    <form>
                        <md-field>
                            <label for="seniorSchool">标题</label>
                            <md-input v-model="info.title" id="title" name="title"/>
                        </md-field>
                        <span class="md-caption">公告</span>
                        <markdown-palettes v-model="info.announcement"></markdown-palettes>
                    </form>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-accent" @click="destroy">删除本黑板</md-button>
                    <md-button @click="preference">提交</md-button>
                </md-card-actions>
            </md-card>
        </md-dialog>
    </div>
</template>

<script>
    export default {
        name: "Management",
        data: () => ({
            currentClass: null,
            classInfo: [],
            studentsInfo:[],
            info: {
                title: "",
                deadline: ""
            },
            form: {
                seniorSchool: "2",
                seniorRegistration: "2019"
            },
        }),
        methods: {
            list() {
                this.empty = true
                this.axios.get("/school/blackboard/detail?id="+this.currentClass).then((response) => {
                    this.page = 1
                    this.classInfo = response.data["data"]
                    this.info.title = this.classInfo.title
                    this.info.announcement = this.classInfo.announcement
                }).catch((error) => {
                    this.$router.push("/school/blackboard")
                })
            },
            destroy(){
                this.axios.post("/school/blackboard/preference?id="+this.currentClass,{
                    "delete":true
                }).then((response)=>{
                    this.list()
                })
            },
            preference(){
                this.axios.post("/school/blackboard/preference?id="+this.currentClass,this.info).then((response)=>{
                    this.list()
                })
            },
            search(){
                this.active = "tab-list"
                this.axios.post("/alumni/directory/search",this.form).then((response) => {
                    this.studentsInfo = response.data["data"]
                    this.active = "tab-add"
                })
            },
        },
        mounted: function() {
            this.currentClass = this.$route.params["id"]
            this.list()
        }
    }
</script>

<style scoped>

</style>