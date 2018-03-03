<i18n src="../../translation/User.json"></i18n>
<template>
    <div align="left">
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
                    <span>{{ $t('cas-horus') }}</span>
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
                <md-button class="md-icon-button md-list-action" to="/user/security">
                    <md-icon>edit</md-icon>
                </md-button>
            </md-list-item>
            <md-divider></md-divider>
            <md-list-item>
                <div class="md-list-item-text">
                    <span>{{ $t('phone') }}</span>
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
                :md-input-placeholder="$t('username')"
                md-input-maxlength="16"
                :md-title="$t('edit-username')"
                :md-confirm-text="$t('submit')"
                :md-cancel-text="$t('cancel')"
                @md-confirm="confirm"/>
        <md-dialog-prompt/>
        <md-dialog :md-active.sync="avatarDialog">
            <form @submit.prevent="changeAvatar">
                <md-card style="padding: 10px;">
                    <md-card-header>
                        <span class="md-title">{{ $t('edit-avatar') }}</span><br/>
                        <span class="md-subtitle" :v-html="$t('edit-avatar-prompt')"></span>
                    </md-card-header>
                    <md-card-content>
                        <md-field>
                            <label>{{ $t('avatar') }}</label>
                            <md-file name="avatar" id="avatar" accept="image/*" @md-change="change"/>
                        </md-field>
                    </md-card-content>
                    <md-card-actions>
                        <md-button class="md-primary" @click="avatarDialog = !avatarDialog">{{ $t('cancel') }}
                        </md-button>
                        <md-button type="submit" class="md-primary">{{ $t('submit') }}</md-button>
                    </md-card-actions>
                </md-card>
            </form>
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
            username: null
        }),
        mounted: function () {
            this.$emit("changeTitle", "User Info")
            this.load()
        },
        methods: {
            load() {
                this.axios.get('/user/current').then((response) => {
                    if (response.data['code'] == 200) {
                        var moment = require('moment-timezone');
                        this.info = response.data["data"]
                        this.info.joinTime = moment(this.joinTime).tz(moment.tz.guess()).format("lll")
                        if (this.info.email === null)
                            this.info.email = this.$t("not-binded")
                        if (this.info.phone === null)
                            this.info.phone = this.$t("not-binded")
                    } else {

                    }
                })
            }, changeAvatar() {
                if (this.avatarPath === null || this.avatarPath.length != 1) {
                    this.avatarDialog = false
                    return
                }

                var formData = new FormData();
                formData.append('photo', this.avatarPath[0]);
                this.axios.post("/user/avatar", formData).then((response) => {
                    this.avatarDialog = false
                    this.$emit("reload")
                })
            }, change(file) {
                this.avatarPath = file
            }, confirm(username) {
                this.axios.post("/user/rename", {
                    username: username
                }).then((response) => {
                    if (response.data["code"] != 200) {
                        this.$emit("showMsg", response.data["data"])
                    }
                    this.$emit("reload")
                    this.load()
                })
            }
        }
    }
</script>

<style scoped>

</style>