<i18n src="../../translation/frontend/User.json"></i18n>
<template>
    <div class="security">

        <form novalidate class="md-layout" @submit.prevent="submit" style="align:left;text-align:left;">
            <md-card class="md-layout-item md-size-100">
                <md-card-header>
                    <div class="md-title">{{ $t('security-settings') }}</div>
                    <div class="md-subtitle">
                        <p align="left">
                            <span class="md-body-2" v-if="oauth"><strong>{{ $t('not-emailed') }}</strong></span><br/>
                            <span class="md-body-1"></span><br/>
                            <span class="md-caption">
                                {{ $t('notice-title') }}<br/>
                                {{ $t('notice-1') }}<br/>
                                {{ $t('notice-2') }}<br/>
                                {{ $t('notice-3') }}
                            </span>
                        </p>
                    </div>
                </md-card-header>

                <md-card-content>
                    <span class="md-caption">{{ $t('change-password') }}</span><br/>
                    <md-divider></md-divider>
                    <md-field>
                        <label>{{ $t('password-new') }}</label>
                        <md-input type="password" name="newPassword" id="newPassword" v-model="form.newPassword"/>
                    </md-field>
                    <md-field>
                        <label>{{ $t('password-repeat') }}</label>
                        <md-input type="password" name="repeatPassword" id="repeatPassword" v-model="form.repeatPassword"/>
                    </md-field>
                    <span class="md-caption">{{ $t('change-email') }}</span><br/>
                    <md-divider></md-divider>
                    <md-checkbox v-model="form.unbindEmail" v-if="phone !== $t('not-binded') && email !== $t('not-binded')"s>{{ $t('only-unbind') }}
                    </md-checkbox>
                    <md-field>
                        <label>{{ $t('email-current') }}</label>
                        <md-input name="email" id="email" autocomplete="email" v-model="email" disabled/>
                    </md-field>
                    <md-field v-if="!form.unbindEmail">
                        <label>{{ $t('email-new') }}</label>
                        <md-input name="newEmail" id="newEmail" v-model="form.newEmail"/>
                        <md-button class="md-primary" @click="sendEmail">Send</md-button>
                    </md-field>
                    <md-field v-if="!form.unbindEmail">
                        <label>{{ $t('code') }}</label>
                        <md-input name="emailCode" id="emailCode" v-model="form.code"/>
                    </md-field>
                    <span class="md-caption">{{ $t('change-phone') }}</span><br/>
                    <md-divider></md-divider>
                    <md-checkbox v-model="form.unbindPhone" v-if="phone !== $t('not-binded') && email !== $t('not-binded')">{{ $t('only-unbind') }}
                    </md-checkbox>
                    <md-field>
                        <label>{{ $t('phone-current') }}</label>
                        <md-input name="phone" id="phone" autocomplete="phone" v-model="phone" disabled/>
                    </md-field>
                    <md-field v-if="!form.unbindPhone">
                        <label>{{ $t('country') }}</label>
                        <md-select name="country" id="country" v-model="form.country">
                            <md-option v-for="country in countries" :key="country.code" :value="country.code">
                                {{country.name}} +{{country.prefix}}
                            </md-option>
                        </md-select>
                    </md-field>
                    <md-field v-if="!form.unbindPhone">
                        <label>{{ $t('phone-new') }}</label>
                        <md-input name="newPhone" id="newPhone" v-model="form.newPhone"/>
                        <md-button class="md-primary" @click="sendSMS()">Send</md-button>
                    </md-field>
                    <md-field v-if="!form.unbindPhone">
                        <label>{{ $t('code') }}</label>
                        <md-input name="phoneCode" id="phoneCode" v-model="form.code"/>
                    </md-field>
                    <span class="md-caption">{{ $t('verify') }}</span>
                    <md-divider></md-divider>
                    <md-field>
                        <label>{{ $t('password-current') }}</label>
                        <md-input type="password" name="password" id="password" autocomplete="password"
                                  v-model="form.password"/>
                    </md-field>
                </md-card-content>

                <md-progress-bar md-mode="indeterminate" v-if="sending"/>

                <md-card-actions>
                    <md-button type="submit" class="md-primary" :disabled="sending">{{ $t('submit') }}</md-button>
                </md-card-actions>
            </md-card>

        </form>
    </div>
</template>

<script>
    export default {
        name: "Security",
        props: ["isAdmin", "isLoggedIn", "isVerified", "gResponse"],
        data: () => ({
            sending: false,
            countries: [],
            task: "",
            email: "",
            phone: "",
            oauth: false,
            form: {}
        }),
        created: function () {
            this.resetForm()
        },
        mounted: function () {
            this.$emit("changeTitle", this.$t("security-title"))
            this.$emit("prepareRecaptcha")
            this.axios.get("/code/available").then((response) => {
                this.countries = response.data["data"]
            })
            if (this.$route.query.reason === "email")
                this.oauth = true
        },
        methods: {
            resetForm() {
                this.axios.get("/user/current").then((response) => {
                    this.email = response.data["data"]["email"]
                    if (this.email === null)
                        this.email = this.$t('not-binded')
                    this.phone = response.data["data"]["phone"]
                    if (this.phone === null)
                        this.phone = this.$t('not-binded')
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
                if (this.form.newPassword !== this.form.repeatPassword) {
                    this.showMsg("Passwords mismatch.")
                    return
                }
                this.axios.post("/user/change", this.form).then((response) => {
                    if (response.data["code"] === 200) {
                        this.showMsg(this.$t("operation-succeeded"))
                        this.resetForm()
                    } else {
                        if (response.data["data"])
                            this.showMsg(response.data["data"])
                        else
                            this.showMsg(this.$t("password-only-incorrect"))
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
                switch (this.task) {
                    case "email":
                        this.axios.post("/code/bind", {
                            "email": this.form.newEmail,
                            "captcha": grecaptcha.getResponse()
                        }).then((response) => {
                            if (response.data["code"] === 200) {
                                this.showMsg(this.$t("send-succeeded"))
                            } else {
                                this.showMsg(response.data["data"])
                            }
                        })
                        break
                    case "sms":
                        this.axios.post("/code/bind", {
                            "phone": this.form.newPhone,
                            "country": this.form.country,
                            "captcha": grecaptcha.getResponse()
                        }).then((response) => {
                            if (response.data["code"] === 200) {
                                this.showMsg(this.$t("send-succeeded"))
                            } else {
                                this.showMsg(response.data["data"])
                            }
                        })

                }
                this.task = ""
                grecaptcha.reset()
            },
            showMsg(msg) {
                this.$emit("showMsg", msg)
            }
        },
        watch: {
            gResponse: {
                handler: function (val, newVal) {
                    this.ct();
                }
            }
        }
    }
</script>

<style scoped>

</style>