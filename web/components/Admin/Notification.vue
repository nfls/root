<template>
    <div align="left">
        <md-card>
            <md-card-header><span class="md-title">iOS推送</span></md-card-header>
            <md-card-content>
                <md-field>
                    <label>接收人</label>
                    <md-input v-model="ios.receiver"></md-input>
                </md-field>
                <md-field>
                    <label>标题</label>
                    <md-input v-model="ios.title"></md-input>
                </md-field>
                <md-field>
                    <label>副标题</label>
                    <md-input v-model="ios.subtitle"></md-input>
                </md-field>
                <md-field>
                    <label>内容</label>
                    <md-textarea v-model="ios.body"></md-textarea>
                </md-field>
                <md-field>
                    <label>图片</label>
                    <md-input v-model="ios.imageUrl"></md-input>
                </md-field>
                <md-field>
                    <label>App内跳转链接</label>
                    <md-input v-model="ios.link"></md-input>
                </md-field>
                <span class="md-caption">Tips: 接收人可为数字ID或all。</span>
            </md-card-content>
            <md-card-actions>
                <md-button class="md-raised md-primary" @click="push">发送</md-button>
            </md-card-actions>
        </md-card>

        <md-card>
            <md-card-header><span class="md-title">邮件</span></md-card-header>
            <md-card-content>
                <md-field>
                    <label>接收人</label>
                    <md-input v-model="email.receiver"></md-input>
                </md-field>
                <md-field>
                    <label>标题</label>
                    <md-input v-model="email.title"></md-input>
                </md-field>
                <span class="md-caption">内容</span>
                <mavon-editor v-model="email.content"></mavon-editor>
                <span class="md-caption">Tips: 接收人可为数字ID或all。</span>
            </md-card-content>
            <md-card-actions>
                <md-button class="md-raised md-primary" @click="mail">发送</md-button>
            </md-card-actions>

        </md-card>
    </div>
</template>

<script>
    export default {
        name: "Notification",
        data: () => ({
            ios: {
                receiver: "",
                title: "",
                subtitle: "",
                body: "",
                imageUrl: "",
                link: "",
                badge: 0
            },
            email: {
                receiver: "",
                title: "",
                content: ""
            }
        }),
        mounted() {
            this.$emit("changeTitle", "通知发送")
        },
        methods: {
            push() {
                this.axios.post("/admin/push", this.ios).then((_) => {
                    this.$emit("showMsg", "发送成功")
                })
            },
            mail() {
                this.axios.post("/admin/mail", this.email).then((_) => {
                    this.$emit("showMsg", "发送成功")
                })
            }
        }
    }
</script>

<style scoped>
    .md-card {
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>