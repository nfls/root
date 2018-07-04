<i18n src="../../translation/frontend/User.json"></i18n>
<template>
    <div align="left">
        <md-progress-bar md-mode="indeterminate" v-if="sending"/>
        <span class="md-title">隐私及通知设置</span>
        <md-divider></md-divider>
        <span class="md-caption">
            反爬虫保护可以防止您账户信息被机器人或是网络爬虫自动抓取。<br/>
            启用该功能后，用户无法通过修改地址进入您的用户信息页。用户只能通过点击姓名标签进入，即只能通过校友录搜索或是私信页面进入。<br/>
        </span>
        <md-checkbox v-model="antiSpider">启用反爬虫保护</md-checkbox>
        <br/>
        <md-field>
            <label>实名认证的大学和工作信息，常住地及个人简介</label>
            <md-select v-model="general" name="general" id="general">
                <md-option v-for="(level, index) in privacyLevel" :key="index" :value="index">{{ level }}</md-option>
            </md-select>
        </md-field>
        <md-field>
            <label>实名认证表格中的联系方式</label>
            <md-select v-model="contact" name="contact" id="contact">
                <md-option v-for="(level, index) in privacyLevel" :key="index" :value="index">{{ level }}</md-option>
            </md-select>
        </md-field>
        <md-field>
            <label>注册邮箱或手机号</label>
            <md-select v-model="phoneOrEmail" name="phoneOrEmail" id="phoneOrEmail">
                <md-option v-for="(level, index) in privacyLevel" :key="index" :value="index">{{ level }}</md-option>
            </md-select>
        </md-field>

        <md-button class="md-raised md-primary" @click="submit">提交</md-button>
    </div>
</template>
<style>
    .md-divider {
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>

<script>
    export default {
        data: () => ({
            sending: false,
            privacyLevel: {
                0: "所有人",
                1: "仅同校同学（所有已实名用户）",
                2: "仅同届同学",
                3: "仅同班同学",
                4: "仅自己"
            },
            general: 0,
            contact: 0,
            phoneOrEmail: 0,
            privacy: "",
            antiSpider: true
        }),
        mounted: function() {
            this.axios.get("/user/privacy").then((response) => {
                this.antiSpider = response.data["data"].antiSpider
                this.privacy = response.data["data"].privacy
            })
        },
        methods: {
            submit() {
                this.sending = true
                this.privacy = this.general + this.contact * 10 + this.phoneOrEmail * 100
                this.axios.post("/user/privacy", {
                    privacy: this.privacy,
                    antiSpider: this.antiSpider
                }).then((response) => {
                    this.sending = false
                    this.antiSpider = response.data["data"].antiSpider
                    this.privacy = response.data["data"].privacy
                }).catch((error) => {
                    this.sending = false
                    this.$emit("generalError",error)
                })
            }
        },
        watch: {
            privacy(val) {
                this.general = val % 10;
                this.contact = parseInt(val / 10) % 10;
                this.phoneOrEmail = parseInt(val / 100) % 10;
            }
        }

    }
</script>