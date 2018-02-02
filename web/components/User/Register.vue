/* eslint-disable */
<template>
    <div class="login">
        <form novalidate class="md-layout-row md-gutter" @submit.prevent="register">
            <md-card class="md-flex-50 md-flex-small-100 login-card">
                <md-card-header>
                    <md-card-header-text>
                        <div class="md-title">新規登録</div>
                        <div class="md-subtitle">NFLS.IO 会員登録</div>
                    </md-card-header-text>
                </md-card-header>

                <md-card-content>
                    <div class="md-layout-row md-layout-wrap md-gutter">
                        <md-tabs class="md-flex md-flex-small-100" md-sync-route>
                            <md-tab id="tab-pages" md-label="携帯電話">
                                <div class="md-flex md-flex-small-100">
                                    <md-field  :class="getValidationClass('country')">
                                        <label for="country">国家</label>
                                        <md-select name="country" id="country" v-model="form.country">
                                            <md-option v-for="country in countries" :key="country.code" :value="country.code">{{country.name}} +{{country.prefix}}</md-option>
                                        </md-select>
                                        <span class="md-error" v-if="$v.form.country && !$v.form.country.required">入力必須項目です。</span>
                                    </md-field>
                                    <md-field :class="getValidationClass('phone')">
                                        <label for="phone">携帯電話番号</label>
                                        <md-input name="phone" id="phone" autocomplete="phone" v-model="form.phone" :disabled="sending"  />
                                        <span class="md-error" v-if="$v.form.phone && !$v.form.phone.required">入力必須項目です。</span>
                                        <span class="md-error" v-else-if="$v.form.phone && !$v.form.phone.numeric">無効な携帯電話番号</span>
                                        <md-button class="md-primary" @click="sendSMS">センド</md-button>
                                    </md-field>
                                </div>
                            </md-tab>
                            <md-tab id="tab-home" md-label="メール">
                                <div class="md-flex md-flex-small-100">
                                    <md-field :class="getValidationClass('email')">
                                        <label for="">メールアドレス</label>
                                        <md-input name="email" id="email" autocomplete="email" v-model="form.email" :disabled="sending"  />
                                        <md-button class="md-primary" @click="sendEmail">センド</md-button>
                                        <span class="md-error" v-if="$v.form.email && !$v.form.email.required">入力必須項目です。</span>
                                        <span class="md-error" v-else-if="$v.form.email && !$v.form.email.email">無効なメールアドレスです</span>
                                    </md-field>
                                </div>
                            </md-tab>
                        </md-tabs>
                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('code')">
                                <label for="username">認証コード</label>
                                <md-input name="code" id="code" autocomplete="code" v-model="form.code" :disabled="sending"  />
                                <span class="md-error" v-if="$v.form.code && !$v.form.code.required">入力必須項目です。</span>
                            </md-field>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('username')">
                                <label for="username">ニックネーム</label>
                                <md-input name="username" id="username" autocomplete="username" v-model="form.username" :disabled="sending"  />
                                <span class="md-error" v-if="$v.form.username && !$v.form.username.required">入力必須項目です。</span>
                                <span class="md-error" v-else-if="$v.form.code && !$v.form.username.maxLength">ニックネームが長</span>
                                <span class="md-error" v-else-if="$v.form.code && !$v.form.username.minLength">ニックネームが短</span>
                            </md-field>
                        </div>

                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('password')">
                                <label for="password">パスワード</label>
                                <md-input name="password" id="password" autocomplete="password" v-model="form.password" :disabled="sending" type="password" />
                                <span class="md-error" v-if="$v.form.password && !$v.form.password.required">入力必須項目です。</span>
                                <span class="md-error" v-else-if="$v.form.code && !$v.form.password.maxLength">パスワードの最大長20</span>
                                <span class="md-error" v-else-if="$v.form.code && !$v.form.password.minLength">パスワードの最小長8</span>
                            </md-field>
                        </div>

                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('repass')">
                                <label for="password">パスワードの再入力</label>
                                <md-input name="repass" id="repass" autocomplete="repass" v-model="form.repass" :disabled="sending" type="password" />
                                <span class="md-error" v-if="$v.form.repass && !$v.form.repass.required">入力必須項目です。</span>
                                <span class="md-error" v-else-if="passwordMismatch">パスワードが一致しません。もう一度入力してください。</span>
                            </md-field>
                        </div>
                        下記の規約に同意：
                        <div class="md-flex md-flex-small-100" >
                            <md-checkbox v-model="form.tos" :disabled="sending" style="width:100%" class="md-primary">NFLS.IO 利用規約</md-checkbox>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-checkbox v-model="form.privacy" :disabled="sending" style="width:100%" class="md-primary">NFLS.IO プライバシーポリシー</md-checkbox>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-button type="submit" class="md-raised md-primary" @click="register" :disabled="sending">登録</md-button>
                        </div>
                    </div>

                </md-card-content>
                <md-divider></md-divider>
                <md-card-actions md-alignment="left">
                    <md-button to="/user/login">Login</md-button>
                </md-card-actions>

                <md-progress-bar md-mode="indeterminate" v-if="sending" />
            </md-card>

            <md-snackbar :md-active.sync="showMessage">{{message}}</md-snackbar>
            <md-dialog-alert
                    :md-active.sync="warning"
                    :md-content="text"
                    md-confirm-text="OK" />
        </form>
    </div>

</template>

<script>
    import { validationMixin } from 'vuelidate'
    import {
        required,
        email,
        minLength,
        maxLength,
        numeric,
        sameAs
    } from 'vuelidate/lib/validators'

    export default {
        name: 'Register',
        mixins: [validationMixin],
        props: ['gResponse'],
        data: () => ({
            form: {
                phone: null,
                email: null,
                code: null,
                country: null,
                username: null,
                password: null,
                repass: null,
                privacy: false,
                tos: false
            },
            validateItems: {
                phone: {},
                email: {},
                code: {},
                country: {},
                username: {},
                password: {},
                repass: {}
            },
            countries: [],
            sending: false,
            showMessage: false,
            lastUser: '',
            passwordMismatch: false,
            message: '',
            text: '',
            warning: false,
            task: ''
        }),
        validations() {
            return {
                form: this.validateItems
            }
        },
        methods: {
            getValidationClass (fieldName) {
                const field = this.$v.form[fieldName]
                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },
            ct() {
                this.sending = true
                switch(this.task){
                    case "register":
                        this.form["recaptcha"] = grecaptcha.getResponse()
                        this.axios.post("/user/register",this.form).then((response) => {
                            if(response.data["code"] == 200){
                                this.showMsg("正常に登録")
                                window.setTimeout(() => {
                                    this.$router.push("/user/login");
                                },3000)
                            }else{
                                this.showMsg(response.data["data"])
                                this.sending = false
                            }
                        })
                        break
                    case "phone":
                        this.axios.post("/code/register",{
                            "country": this.form.country,
                            "phone": this.form.phone,
                            "captcha": grecaptcha.getResponse()
                        }).then((response) => {
                            this.sending = false
                            if(response.data["code"] == 200){
                                this.showMsg("正常に送信")
                            }else{
                                this.showMsg(response.data["data"])
                            }
                        })
                        break
                    case "email":
                        this.axios.post("/code/register", {
                            "email": this.form.email,
                            "captcha": grecaptcha.getResponse()
                        }).then((response) => {
                            this.sending = false
                            if (response.data["code"] == 200) {
                                this.showMsg("送信成功")
                            } else {
                                this.showMsg(response.data["data"])
                            }
                        })
                        break
                }
                this.task = ""
                grecaptcha.reset()
            },
            register () {
                this.validateItems = {
                    code: {
                        required
                    },
                    username: {
                        required,
                        minLength: minLength(3),
                        maxLength: maxLength(20)
                    },
                    password: {
                        required,
                        minLength: minLength(8),
                        maxLength: maxLength(20)
                    },
                    repass: {
                        required
                    }
                }
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    if(!this.form.tos || !this.form.privacy){
                        this.text = "あなたは条件に同意する必要があります。"
                        this.warning = true
                    }else if(this.form.password != this.form.repass){
                        this.text = "パスワードが一致している必要があります"
                        this.warning = true
                    }else{
                        this.task = "register"
                        grecaptcha.execute()
                    }
                }
            },
            sendSMS() {
                this.validateItems = {
                    phone: {
                        required,
                        numeric
                    },
                    country: {
                        required
                    }
                }
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    this.task = "phone"
                    grecaptcha.execute()
                }
            },
            sendEmail() {
                this.validateItems = {
                    email: {
                        required,
                        email
                    }
                }
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    this.task = "email"
                    grecaptcha.execute()
                }
            },
            showMsg(msg){
                this.message = msg
                this.showMessage = true
            }
        },
        mounted: function(){
            this.axios.get("/code/available").then((response) => {
                this.countries = response.data["data"]
            })
            this.$emit("changeTitle","Register")
            this.$emit("prepareRecaptcha")
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

<style lang="scss" scoped>
    .md-progress-bar {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
    }
    .login {
        margin-left: auto;
        margin-right: auto;
        width:70%;
        height:100%;
        min-width:260px;
        max-width:500px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>
