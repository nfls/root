<template>
    <div class="login">
        <form novalidate class="md-layout-row md-gutter" @submit.prevent="validate">
            <md-card class="md-flex-50 md-flex-small-100 login-card">
                <md-card-header>
                    <md-card-header-text>
                        <div class="md-title"> aaa </div>
                        <div class="md-subtitle">Login with your nfls.io account.</div>

                    </md-card-header-text>
                </md-card-header>

                <md-card-content>
                   <div class="md-layout-row md-layout-wrap md-gutter">
                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('username')">
                                <label for="username">Username/Email/E.164 Phone</label>
                                <md-input name="username" id="username" autocomplete="username" v-model="form.username" :disabled="sending"  />
                                <span class="md-error" v-if="!$v.form.username.required">Username is required.</span>
                            </md-field>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('password')">
                                <label for="password">Password</label>
                                <md-input name="password" id="password" autocomplete="password" v-model="form.password" :disabled="sending" type="password" />
                                <span class="md-error" v-if="!$v.form.password.required">Password is required</span>
                            </md-field>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-checkbox v-model="form.remember" :disabled="sending" style="width:100%" class="md-primary">Remember Me</md-checkbox>
                        </div>
                        <div class="md-flex md-flex-small-100">
                            <md-button type="submit" class="md-raised md-primary">LINK START</md-button>
                        </div>
                    </div>

                </md-card-content>
                <md-divider></md-divider>
                <md-card-actions md-alignment="left">
                    <md-button to="/user/register">Register</md-button>
                    <md-button to="/user/reset">Reset Password</md-button>
                </md-card-actions>

                <md-progress-bar md-mode="indeterminate" v-if="sending" />
            </md-card>

            <md-snackbar :md-active.sync="userSaved">{{message}}</md-snackbar>
        </form>
    </div>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import {
        required
    } from 'vuelidate/lib/validators'

    export default {
        name: 'Login',
        mixins: [validationMixin],
        props: ['gResponse'],
        data: () => ({
            form: {
                username: null,
                remember: false,
                password: null
            },
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
                }
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
            validate() {
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    grecaptcha.execute()
                }
            },
            ct () {
                this.sending = true
                this.axios.post('/user/login', {
                    username: this.form.username,
                    password: this.form.password,
                    remember: this.form.remember,
                    captcha: grecaptcha.getResponse()
                }).then((response) => {
                    if (response.data.code === 200) {
                        this.userSaved = true
                        this.message = 'Login succeeded.'
                        window.setTimeout(() => {
                            this.$emit("reload")
                        },1500)
                    } else {
                        this.userSaved = true
                        this.message = 'Invalid Username or Password.'
                        grecaptcha.reset()
                        window.setTimeout(() => {
                            this.userSaved = false
                        }, 3000)
                    }
                    this.sending = false
                })

            }
        }, mounted: function() {
            this.$emit("changeTitle","Login")
            this.$emit("prepareRecaptcha")
            this.$i18n.locale = "en"
            console.log("aaa")
            console.log(this.$t('login.title'))
        }, watch: {
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

<i18n>
    {
        "en": {
            "hello": "登录"
        }
    }
</i18n>