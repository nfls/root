<template>
    <div class="login">
        <form novalidate class="md-layout-row md-gutter" @submit.prevent="login">
            <md-card class="md-flex-50 md-flex-small-100 login-card">
                <md-card-header>
                    <md-card-header-text>
                        <div class="md-title">Login</div>
                        <div class="md-subtitle">Login with your nfls.io account.</div>

                    </md-card-header-text>
                </md-card-header>

                <md-card-content>
                    <div class="md-layout-row md-layout-wrap md-gutter">
                        <div class="md-flex md-flex-small-100">
                            <md-field :class="getValidationClass('username')">
                                <label for="username">Username</label>
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
                            <md-button type="submit" class="md-raised md-primary" style="width:90%">Login</md-button>
                        </div>
                    </div>

                </md-card-content>

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
        data: () => ({
        form: {
            username: null,
            remember: true,
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
        login () {
            this.$v.$touch()
            if (!this.$v.$invalid) {
                this.sending = true
                this.axios.post('/user/login', {
                    username: this.form.username,
                    password: this.form.password,
                    remember: this.form.remember
                }).then((response) => {
                    if (response.data.code === 200) {
                    window.location.href = "#/user/dashboard"
                    } else {
                        this.userSaved = true
                        this.message = 'Invalid Username or Password.'
                        window.setTimeout(() => {
                            this.userSaved = false
                    }, 3000)
                }
                console.log(response.data)
                this.sending = false
            })
            }
        },
        reset () {

        },
        register () {

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
        min-width:300px;
        max-width:500px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>
