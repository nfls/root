<template>
    <div align="left">
        <span class="md-title">iOS推送发送</span>
        <md-field>
            <label>接收用户ID（填all为所有人）</label>
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
        <md-button class="md-raised md-primary" @click="push">发送</md-button>
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

            }
        }),
        methods: {
            push() {
                this.axios.post("/admin/push", this.ios)
            }
        }
    }
</script>

<style scoped>

</style>