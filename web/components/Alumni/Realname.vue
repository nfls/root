<template>
    <div class>
        <md-card>
            <md-card-header>
                我的实名认证状态
            </md-card-header>
            <md-card-content>
                <form>
                    <div>
                        <md-checkbox v-model="valid" class="md-primary" readonly disabled>实名用户</md-checkbox>
                        <md-field v-if="valid">
                            <label for="chineseName">中文名</label>
                            <md-input name="chineseName" id="chineseName" v-model="chineseName" readonly />
                        </md-field>
                        <md-field v-if="valid">
                            <label for="englishName">英文名</label>
                            <md-input name="englishName" id="englishName" v-model="englishName" readonly />
                        </md-field>
                        <md-field v-if="valid">
                            <label for="submitTime">验证时间</label>
                            <md-input name="submitTime" id="submitTime" v-model="submitTime" readonly />
                        </md-field>
                        <md-field v-if="valid">
                            <label for="submitTime">过期时间</label>
                            <md-input name="submitTime" id="submitTime" v-model="expireAt" readonly />
                        </md-field>
                    </div>
                </form>
            </md-card-content>
            <md-divider></md-divider>
            <md-card-actions md-alignment="left">
                <md-button v-if="allowNew" @click="newForm()">填写新的表格
                </md-button>
                <md-button disabled v-else>
                    您有未提交或待审核的表格
                </md-button>
            </md-card-actions>
        </md-card>
        <md-card>
            <md-card-header>
                我的实名认证记录
            </md-card-header>
            <md-card-content>
                <md-list>
                    <md-list-item v-for="auth in history" :key="auth.id" @click="click(auth.id)">{{auth.readableText}}</md-list-item>
                </md-list>
            </md-card-content>
        </md-card>
        <md-dialog-alert
                :md-active.sync="error"
                md-title="错误"
                md-content="请先登录您的账户！" />
    </div>
</template>

<script>
    export default {
        name: "Realname",
        data: () => ({
            history: [],
            allowNew: false,
            valid: false,
            chineseName: "",
            englishName: "",
            submitTime: "",
            expireAt: "",
            error: false,
            csrf: null,
        }),
        mounted: function() {
            this.loadData()
            this.loadStatus()
            this.loadCsrf()
            this.$emit('changeTitle', "实名认证")
        },
        methods: {
            loadData() {
                this.axios.get("/alumni/info").then((response) => {
                    var self = this
                    this.history = response.data["data"].map(function(val){
                        val["readableText"] = self.getStatus(val.status)
                        if(val.submitTime){
                            var moment = require('moment-timezone');
                            val["readableText"] += "（提交时间：" + moment(val.submitTime).tz(moment.tz.guess()).format("lll") + "）"
                        }
                        val["readableText"] += " " + val.id
                        return val
                    })
                    this.allowNew = (this.history.filter(function(val){
                        return val.status <= 1
                    }).length == 0)
                }).catch((error) => {
                    this.error = true
                })
            },
            loadStatus() {
                this.axios.get("/alumni/current").then((response) => {
                    //var self = this
                    var data = response.data["data"]
                    if(data){
                        var moment = require('moment-timezone');
                        this.valid = true
                        this.chineseName = data["chineseName"]
                        this.englishName = data["englishName"]
                        this.submitTime = moment(data["submitTime"]).tz(moment.tz.guess()).format("lll")
                        if(data["expireAt"])
                            this.expireAt = moment(data["expireAt"]).tz(moment.tz.guess()).format("L")
                        else
                            this.expireAt = "无期限"
                    }else{
                        this.valid = false
                    }
                })
            },
            click(id) {
                this.$router.push("/alumni/auth/" + id);
            },
            getStatus(status) {
                switch(status){
                    case 0:
                        return "未提交"
                    case 1:
                        return "待审核"
                    case 2:
                        return "已取消"
                    case 3:
                        return "审核中"
                    case 4:
                        return "被退回"
                    case 5:
                        return "已通过"
                    case 6:
                        return "已过期"
                }
            },
            newForm() {
                this.axios.post("alumni/new",{
                    _csrf: this.csrf
                })
                this.loadData()
            },
            loadCsrf() {
                this.axios.get("user/csrf",{
                    params: {
                        id: "aa"
                    }
                }).then((rsponse) => {
                    this.csrf = response.data["data"]
                })
            }
        }
    }
</script>

<style scoped>
    .md-checkbox {
        display: flex;
    }
    .md-card {
        margin: 10px;
    }
</style>