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
                                            <md-option v-for="country in countries" :key="country.code" :value="country.prefix">{{country.name}} +{{country.prefix}}</md-option>
                                        </md-select>
                                    </md-field>
                                    <md-field :class="getValidationClass('username')">
                                        <label for="username">携帯電話番号</label>
                                        <md-input name="phone" id="phone" autocomplete="phone" v-model="form.phone" :disabled="sending"  />
                                        <md-button class="md-accent">センド</md-button>
                                    </md-field>
                                </div>
                            </md-tab>
                            <md-tab id="tab-home" md-label="メール">
                                <div class="md-flex md-flex-small-100">
                                    <md-field :class="getValidationClass('email')">
                                        <label for="">メールアドレス</label>
                                        <md-input name="email" id="email" autocomplete="email" v-model="form.email" :disabled="sending"  />
                                        <md-button class="md-accent">センド</md-button>
                                    </md-field>
                                </div>
                            </md-tab>
                        </md-tabs>
                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('code')">
                                <label for="username">認証コード</label>
                                <md-input name="code" id="code" autocomplete="code" v-model="form.code" :disabled="sending"  />
                                <span class="md-error" v-if="!$v.form.username.required">Username is required.</span>
                            </md-field>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('username')">
                                <label for="username">ニックネーム</label>
                                <md-input name="username" id="username" autocomplete="username" v-model="form.username" :disabled="sending"  />
                                <span class="md-error" v-if="!$v.form.username.required">Username is required.</span>
                            </md-field>
                        </div>

                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('password')">
                                <label for="password">パスワード</label>
                                <md-input name="password" id="password" autocomplete="password" v-model="form.password" :disabled="sending" type="password" />
                                <span class="md-error" v-if="!$v.form.password.required">Password is required</span>
                            </md-field>
                        </div>

                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('repass')">
                                <label for="password">パスワードの再入力</label>
                                <md-input name="repass" id="repass" autocomplete="repass" v-model="form.repass" :disabled="sending" type="repass" />
                                <span class="md-error" v-if="!$v.form.repass.required">Password is required</span>
                            </md-field>
                        </div>
                        下記の規約に同意：
                        <div class="md-flex md-flex-small-100">
                            <md-checkbox v-model="form.tos" :disabled="sending" style="width:100%" class="md-primary">NFLS.IO 利用規約</md-checkbox>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-checkbox v-model="form.privacy" :disabled="sending" style="width:100%" class="md-primary">NFLS.IO プライバシーポリシー</md-checkbox>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-button type="submit" class="md-raised md-primary" style="width:90%" @click="validate">登録</md-button>
                        </div>
                    </div>

                </md-card-content>

                <md-progress-bar md-mode="indeterminate" v-if="sending" />
            </md-card>

            <md-snackbar :md-active.sync="userSaved">{{message}}</md-snackbar>
        </form>
        <div id="recaptcha" class="g-recaptcha"></div>
    </div>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import {
        required,
        email,
        minLength,
        maxLength,
        numerics
    } from 'vuelidate/lib/validators'

    export default {
        name: 'Register',
        mixins: [validationMixin],
        data: () => ({
            form: {
                phone: null,
                email: null,
                code: null,
                country: "86",
                username: null,
                password: null,
                repass: null,
                privacy: false,
                tos: false
            },
            countries: [],
            sending: false,
            userSaved: false,
            lastUser: '',
            message: ''
        }),
        validations: {
            form: {
                username: {
                    required
                },
                password: {
                    required
                },
                email: {
                    email
                },
                phone: {
                    numerics
                },
                username: {
                    minLength: minLength(3),
                    maxLength: maxLength(20)
                },
                password: {
                    minLength: minLength(3),
                    maxLength: maxLength(20)
                },
                repass: {
                    minLength: minLength(3),
                        maxLength: maxLength(20)
                }
            }
        },
        methods: {
            initReCaptcha: function() {
                var self = this;
                setTimeout(function() {
                    if(typeof grecaptcha === 'undefined') {
                        self.initReCaptcha();
                    }
                    else {
                        grecaptcha.render('recaptcha', {
                            sitekey: '6Le32kIUAAAAAGZa00irP5FPovXsk1qZdpnx15H9',
                            size: 'invisible',
                            callback: this.register
                        });
                    }
                }, 100);
            },
            getValidationClass (fieldName) {
                const field = this.$v.form[fieldName]
                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },
            register () {
                this.$v.$touch()
                if (!this.$v.$invalid) {

                }
            },
            validate() {
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    grecaptcha.execute();
                }
            }
        },
        mounted: function(){
            this.axios.get("/code/available").then((response) => {
                this.countries = response.data["data"]
            })
            this.initReCaptcha();
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
        min-width:300px;
        max-width:500px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>
