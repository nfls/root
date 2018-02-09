<template>
    <div align="left">
        <span class="md-title">我的账户信息</span>
        <md-list class="md-douple-line" style="margin: 10px">
            <md-list-item>
                <md-avatar @click.native="avatarDialog = !avatarDialog">
                    <img :src="avatar" alt="People">
                </md-avatar>
                <div class="md-list-item-text">
                    <span>用户名</span>
                    <span>{{info.username}}</span>
                </div>
                <md-button class="md-icon-button md-list-action" @click="usernameDialog = !usernameDialog">
                    <md-icon>edit</md-icon>
                </md-button>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>ID</span>
                    <span>{{info.id}}</span>
                </div>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>"创意、活动、服务"小时数（仅限游戏）</span>
                    <span>{{info.point}}</span>
                </div>
                <md-tooltip md-direction="bottom">您可以通过游戏来赚取本积分</md-tooltip>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>加入时间</span>
                    <span>{{info.joinTime}}</span>
                </div>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>邮箱</span>
                    <span>{{info.email}}</span>
                </div>
                <md-button class="md-icon-button md-list-action" to="/user/security">
                    <md-icon>edit</md-icon>
                </md-button>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>手机号</span>
                    <span>{{info.phone}}</span>
                </div>
                <md-button class="md-icon-button md-list-action" to="/user/security">
                    <md-icon>edit</md-icon>
                </md-button>
            </md-list-item>
        </md-list>
        <md-dialog-prompt
                :md-active.sync="usernameDialog"
                v-model="username"
                md-input-placeholder="用户名"
                md-input-maxlength="16"
                md-title='您可以在此修改您的用户名。用户名支持中文、英文、日文，长度在3-16之间。每次改名需要2小时的"创意、活动、服务"。 如果您需要修改您的头像，请在左侧单击您的头像进行修改。'
                md-confirm-text="提交"
                md-cancel-text="取消"
                @md-confirm="confirm"/>
        <md-dialog-prompt />
        <md-dialog :md-active.sync="avatarDialog">
            <form @submit.prevent="changeAvatar">
                <md-card style="padding: 10px;">
                    <md-card-header>
                        <span class="md-title">修改头像</span><br/>
                        <span class="md-subtitle">如果要调整的麻烦自己本地裁一下啦<br/>正常情况下是取最大且最居中的正方形<br/><del>其实是我懒得写个页面让你们裁</del></span>
                    </md-card-header>
                    <md-card-content>
                        <md-field>
                            <label>头像</label>
                            <md-file name="avatar" id="avatar" accept="image/*" @md-change="change"/>
                        </md-field>
                    </md-card-content>
                    <md-card-actions>
                        <md-button class="md-primary" @click="avatarDialog = !avatarDialog">取消</md-button>
                        <md-button type="submit" class="md-primary">提交</md-button>
                    </md-card-actions>
                </md-card>

            </form>
        </md-dialog>
        <md-snackbar :md-active.sync="showMessage">{{message}}</md-snackbar>
    </div>
</template>

<script>
    export default {
        name: "Info",
        props: ["avatar"],
        data: () => ({
            avatarPath: null,
            info: [],
            usernameDialog: false,
            avatarDialog: false,
            username: null,
            message: null,
            showMessage: false
        }),
        mounted: function() {
            this.$emit("changeTitle","User Info")
            this.load()
        },
        methods: {
            load() {
                this.axios.get('/user/current').then((response) =>{
                    if(response.data['code'] == 200){
                        var moment = require('moment-timezone');
                        this.info = response.data["data"]
                        this.info.joinTime =  moment(this.joinTime).tz(moment.tz.guess()).format("lll")
                        if(this.info.email === null)
                            this.info.email = "未绑定"
                        if(this.info.phone === null)
                            this.info.phone = "未绑定"
                    }else{

                    }
                })
            }, changeAvatar() {
                if(this.avatarPath === null || this.avatarPath.length != 1){
                    this.avatarDialog = false
                    return
                }

                var formData = new FormData();
                formData.append('photo', this.avatarPath[0]);
                this.axios.post("/user/avatar",formData).then((response) => {
                    this.avatarDialog = false
                    this.$emit("reload")
                })
            }, change(file) {
                this.avatarPath = file
            }, confirm(username) {
                this.axios.post("/user/rename",{
                    username: username
                }).then((response) => {
                    if(response.data["code"] != 200){
                        this.showMsg(response.data["data"])
                    }
                    this.$emit("reload")
                    this.load()
                })
            }, showMsg(msg){
                this.showMessage = true
                this.message = msg
            }
        }
    }
</script>

<style scoped>

</style>