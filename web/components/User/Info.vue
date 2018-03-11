<i18n src="../../translation/frontend/User.json"></i18n>
<template>
    <div align="left">
        <md-progress-bar md-mode="indeterminate" v-if="sending"/>
        <span class="md-title">{{ $t('account-info') }}</span>
        <md-list class="md-douple-line" style="margin: 10px">
            <md-list-item>
                <md-avatar @click.native="avatarDialog = !avatarDialog">
                    <img :src="avatar" alt="People">
                </md-avatar>
                <div class="md-list-item-text">
                    <span>{{ $t('username') }}</span>
                    <span>{{info.username}}</span>
                </div>
                <md-button class="md-icon-button md-list-action" @click="usernameDialog = !usernameDialog" :disabled="sending">
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
                    <span>{{ $t('cas-hours') }}</span>
                    <span>{{info.point}}</span>
                </div>
                <md-tooltip md-direction="bottom">{{ $t('cas-prompt') }}</md-tooltip>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>{{ $t('jointime') }}</span>
                    <span>{{info.joinTime}}</span>
                </div>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>{{ $t('email') }}</span>
                    <span>{{info.email}}</span>
                </div>
                <md-button class="md-icon-button md-list-action" to="/user/security" :disabled="sending">
                    <md-icon>edit</md-icon>
                </md-button>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>{{ $t('phone') }}</span>
                    <span>{{info.phone}}</span>
                </div>
                <md-button class="md-icon-button md-list-action" to="/user/security" :disabled="sending">
                    <md-icon>edit</md-icon>
                </md-button>
            </md-list-item>
        </md-list>
        <md-dialog-prompt
                :md-active.sync="usernameDialog"
                v-model="username"
                :md-input-placeholder="$t('username')"
                md-input-maxlength="16"
                :md-title="$t('edit-username')"
                :md-confirm-text="$t('submit')"
                :md-cancel-text="$t('cancel')"
                @md-confirm="confirm"/>
        <md-dialog-prompt/>
        <md-dialog :md-active.sync="avatarDialog">
            <md-dialog-title>{{ $t('edit-avatar') }}</md-dialog-title>
            <md-dialog-content>
                <form @submit.prevent="changeAvatar">
                    <md-field>
                        <label>{{ $t('avatar') }}</label>
                        <md-file name="avatar" id="avatar" accept="image/*" @md-change="change"/>
                    </md-field>
                </form>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-primary" @click="avatarDialog = !avatarDialog">{{ $t('cancel') }}</md-button>
                <md-button type="submit" class="md-primary" :disabled="sending">{{ $t('submit') }}</md-button>
            </md-dialog-actions>
        </md-dialog>
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
            sending: false
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t("info-title"))
            this.load()
        },
        methods: {
            load() {
                this.axios.get('/user/current').then((response) => {
                    if (response.data['code'] === 200) {
                        var moment = require('moment-timezone');
                        this.info = response.data["data"]
                        this.info.joinTime = moment(this.joinTime).tz(moment.tz.guess()).format("lll")
                        if (this.info.email === null)
                            this.info.email = this.$t("not-binded")
                        if (this.info.phone === null)
                            this.info.phone = this.$t("not-binded")
                    } else {
                        this.$router.push("/user/login")
                    }
                }).catch((error) => {
                    console.error(error)
                    this.$router.push("/user/login")
                })
            }, changeAvatar() {
                this.sending = true
                if (this.avatarPath === null || this.avatarPath.length !== 1) {
                    this.avatarDialog = false
                    return
                }
                var formData = new FormData();
                formData.append('photo', this.avatarPath[0]);
                this.axios.post("/user/avatar", formData).then((response) => {
                    this.sending = false
                    this.avatarDialog = false
                    this.$emit("reload")
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError",error)
                })
            }, change(file) {
                this.avatarPath = file
            }, confirm(username) {
                this.sending = true
                this.axios.post("/user/rename", {
                    username: username
                }).then((response) => {
                    this.sending = false
                    if (response.data["code"] !== 200) {
                        this.$emit("showMsg", response.data["data"])
                    }
                    this.$emit("reload")
                    this.load()
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError",error)
                })
            }
        }
    }
</script>

<style scoped>
</style>