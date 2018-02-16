<template>
    <div class="security">

        <form novalidate class="md-layout" @submit.prevent="submit">
            <md-card class="md-layout-item md-size-100">
                <md-card-header>
                    <div class="md-title">安全设置</div>
                    <div class="md-subtitle" style="align:left;text-align:left;">
                        <p align="left">
                            <span class="md-body-2" v-if="oauth"><strong>清先绑定邮箱后再使用论坛或百科功能！</strong></span><br/>
                            <span class="md-body-1">您可以在此处修改您的账户安全设置。</span><br/>
                            <span class="md-caption">
                                注意事项：<br/>
                                1. 手机和邮箱至少需要绑定一个。在只有一个绑定的情况下您将无法进行解除绑定的操作<br/>
                                2. 在使用百科或论坛功能前，您必须绑定您的邮箱，并请不要修改，以免绑定失效<br/>
                                3. 隐私设置仅针对实名用户有效
                            </span>
                        </p>
                    </div>
                </md-card-header>

                <md-card-content>
                    <md-tabs md-sync-route>
                        <md-tab id="tab-home" md-label="密码">

                            <md-field>
                                <label for="newPassword">新的密码</label>
                                <md-input type="password" name="newPassword" id="newPassword" v-model="form.newPassword"/>
                            </md-field>
                            <md-field>
                                <label for="repeatPassword">重复密码</label>
                                <md-input type="password" name="repeatPassword" id="repeatPassword" v-model="form.repeatPassword"/>
                            </md-field>
                        </md-tab>
                        <md-tab id="tab-pages" md-label="邮箱">
                            <md-checkbox v-model="form.unbindEmail" :disabled="phone === '未绑定' || email === '未绑定'">仅解除绑定，不更换新邮箱</md-checkbox>
                            <md-field>
                                <label for="email">当前邮箱</label>
                                <md-input name="email" id="email" autocomplete="email" v-model="email" disabled/>
                            </md-field>
                            <md-field>
                                <label for="">新的邮箱</label>
                                <md-input name="newEmail" id="newEmail" v-model="form.newEmail" :disabled="form.unbind"/>
                                <md-button class="md-primary" @click="sendEmail" :disabled="form.unbind">Send</md-button>
                            </md-field>
                            <md-field>
                                <label for="emailCode">验证码</label>
                                <md-input name="emailCode" id="emailCode" v-model="form.code" :disabled="form.unbind"/>
                            </md-field>
                        </md-tab>
                        <md-tab id="tab-posts" md-label="手机">
                            <md-checkbox v-model="form.unbindPhone"  :disabled="phone === '未绑定' || email === '未绑定'">仅解除绑定，不更换新手机</md-checkbox>
                            <md-field>
                                <label for="phone">当前手机</label>
                                <md-input name="phone" id="phone" autocomplete="phone" v-model="phone" disabled/>
                            </md-field>
                            <md-field>
                                <label for="country">国家</label>
                                <md-select name="country" id="country" v-model="form.country" :disabled="form.unbind">
                                    <md-option v-for="country in countries" :key="country.code" :value="country.code">{{country.name}} +{{country.prefix}}</md-option>
                                </md-select>
                            </md-field>
                            <md-field>
                                <label for="">新的手机号</label>
                                <md-input name="newPhone" id="newPhone" v-model="form.newPhone" :disabled="form.unbind"/>
                                <md-button class="md-primary" @click="sendSMS()" :disabled="form.unbind">Send</md-button>
                            </md-field>
                            <md-field>
                                <label for="phoneCode">验证码</label>
                                <md-input name="phoneCode" id="phoneCode" v-model="form.code" :disabled="form.unbind"/>
                            </md-field>
                        </md-tab>
                        <md-tab id="tab-settings" md-label="隐私">
                            Comming Soon
                        </md-tab>
                    </md-tabs>
                    <md-field>
                        <label for="password">当前密码</label>
                        <md-input type="password" name="password" id="password" autocomplete="password" v-model="form.password"/>
                    </md-field>
                </md-card-content>

                <md-snackbar :md-active.sync="showMessage">{{message}}</md-snackbar>

                <md-progress-bar md-mode="indeterminate" v-if="sending" />

                <md-card-actions>
                    <md-button type="submit" class="md-primary" :disabled="sending">提交</md-button>
                </md-card-actions>
            </md-card>

        </form>
    </div>
</template>

<script>
    export default {
        name: "Security",
        props: ["isAdmin","isLoggedIn","isVerified","gResponse"],
        data: () => ({
            sending: false,
            countries: [],
            task: "",
            showMessage: false,
            message: "",
            email: "",
            phone: "",
            oauth: false,
            form:{
            }
        }),
        created: function () {
            this.resetForm()
        },
        mounted: function () {
            this.$emit("changeTitle","Security")
            this.$emit("prepareRecaptcha")
            this.axios.get("/code/available").then((response) => {
                this.countries = response.data["data"]
            })
            if(this.$route.query.reason = "email")
                this.oauth = true
        },
        methods: {
            resetForm() {
                this.axios.get("/user/current").then((response) => {
                    this.email = response.data["data"]["email"]
                    if(this.email === null)
                        this.email = "未绑定"
                    this.phone = response.data["data"]["phone"]
                    if(this.phone === null)
                        this.phone = "未绑定"
                }).catch((error) => {
                    this.$router.push('/user/login')
                })
                this.form = {
                    password: null,
                    newPassword: null,
                    repeatPassword: null,
                    newEmail: null,
                    code: null,
                    country: null,
                    newPhone: null,
                    unbindEmail: false,
                    unbindPhone: false
                }
            },
            submit() {
                if(this.form.newPassword != this.form.repeatPassword){
                    this.showMsg("Passwords mismatch.")
                    return
                }
                this.axios.post("/user/change",this.form).then((response) => {
                    if(response.data["code"] == 200){
                        this.showMsg("Operation succeeded.")
                        this.resetForm()
                    }else{
                        if(response.data["data"])
                            this.showMsg(response.data["data"])
                        else
                            this.showMsg("Password incorrect.")
                    }
                })
            },
            sendEmail() {
                this.task = "email"
                grecaptcha.execute()
            },
            sendSMS() {
                this.task = "sms"
                grecaptcha.execute()
            },
            ct() {
                switch(this.task){
                    case "email":
                        this.axios.post("/code/bind",{
                            "email": this.form.newEmail,
                            "captcha": grecaptcha.getResponse()
                        }).then((response) => {
                            if(response.data["code"] == 200){
                                this.showMsg("Email Sent.")
                            }else{
                                this.showMsg(response.data["data"])
                            }
                        })
                        break
                    case "sms":
                        this.axios.post("/code/bind",{
                            "phone": this.form.newPhone,
                            "country": this.form.country,
                            "captcha": grecaptcha.getResponse()
                        }).then((response) => {
                            if(response.data["code"] == 200){
                                this.showMsg("SMS Sent.")
                            }else{
                                this.showMsg(response.data["data"])
                            }
                        })

                }
                this.task = ""
                grecaptcha.reset()
            },
            showMsg(message) {
                this.message = message
                this.showMessage = true
            }
        },
        watch: {
            gResponse: {
                handler: function(val,newVal){
                    this.ct();
                }
            }
        }
    }
</script>

<style scoped>

</style>