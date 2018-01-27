<template>
    <div class="realname">
        <md-table md-card>
            <md-table-toolbar>
                <h1 class="md-title">Users</h1>
            </md-table-toolbar>

            <md-table-row>
                <md-table-head md-numeric>ID</md-table-head>
                <md-table-head>Name</md-table-head>
                <md-table-head>Email</md-table-head>
                <md-table-head>Gender</md-table-head>
                <md-table-head>Job Title</md-table-head>
            </md-table-row>

            <md-table-row>
                <md-table-cell md-numeric>1</md-table-cell>
                <md-table-cell>Shawna Dubbin</md-table-cell>
                <md-table-cell>sdubbin0@geocities.com</md-table-cell>
                <md-table-cell>Male</md-table-cell>
                <md-table-cell>Assistant Media Planner</md-table-cell>
            </md-table-row>

            <md-table-row>
                <md-table-cell md-numeric>2</md-table-cell>
                <md-table-cell>Odette Demageard</md-table-cell>
                <md-table-cell>odemageard1@spotify.com</md-table-cell>
                <md-table-cell>Female</md-table-cell>
                <md-table-cell>Account Coordinator</md-table-cell>
            </md-table-row>

            <md-table-row>
                <md-table-cell md-numeric>3</md-table-cell>
                <md-table-cell>Vera Taleworth</md-table-cell>
                <md-table-cell>vtaleworth2@google.ca</md-table-cell>
                <md-table-cell>Male</md-table-cell>
                <md-table-cell>Community Outreach Specialist</md-table-cell>
            </md-table-row>
        </md-table>
        <md-card>
            <md-card-header>
                <div class="md-title">实名认证表格</div>
            </md-card-header>
            <md-card-content>
                <form novalidate class="md-layout-row md-gutter" @submit.prevent="submit">
                    <div class="md-flex" v-for="item in formItems" :key="item.key">
                        <md-field v-if="item.type == 'select'" :class="getValidationClass(item.key)">
                            <label :for="item.key">{{item.name}}</label>
                            <md-select :name="item.key" :id="item.key" v-model="form[item.key]" :disabled="sending">
                                <md-option v-for="value in item.values" :key="value.value" :value="value.value">{{value.name}}</md-option>
                            </md-select>
                        </md-field>
                        <md-field v-else-if="item.type == 'input'" :class="getValidationClass(item.key)">
                            <label :for="item.key">{{item.name}}</label>
                            <md-input :name="item.key" :id="item.key" :autocomplete="item.key" v-model="form[item.key]" :disabled="sending"></md-input>
                        </md-field>
                        <md-field v-else-if="item.type == 'textarea'" :class="getValidationClass(item.key)">
                            <label :for="item.key">{{item.name}}</label>
                            <md-textarea :name="item.key" :id="item.key" :autocomplete="item.key" v-model="form[item.key]" :disabled="sending"></md-textarea>
                        </md-field>
                        <md-datepicker v-else-if="item.type == 'date'" :name="item.key" :id="item.key" :autocomplete="item.key"  :class="getValidationClass(item.key)" v-model="form[item.key]" :disabled="sending"/>
                    </div>
                    <div class="md-flex md-flex-small-100">
                        <md-button type="submit" class="md-raised md-primary" style="width:90%"><md-icon>send</md-icon></md-button>
                    </div>
                    <md-progress-bar md-mode="indeterminate" v-if="sending" />
                    <md-snackbar :md-active.sync="userSaved">{{message}}</md-snackbar>
                </form>
            </md-card-content>
        </md-card>

    </div>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import {
        required,
        minLength,
        maxLength,
        minValue,
        maxValue,
        numeric
    } from 'vuelidate/lib/validators'

    export default {
        name: 'Realname',
        mixins: [validationMixin],
        data: () => ({
            formItems: [],
            validatorItems: [],
            form: [],
            sending: false,
            userSaved: false,
            lastUser: '',
            message: ''
        }),
        validations() {
            return {
                form: this.validatorItems
            }
        },
        mounted: function () {
            this.axios.get("/alumni/form").then((response) => {

                var objects = response.data["data"]

                this.validatorItems = Object.keys(objects).reduce(function (previous, key){
                    var validates = objects[key].validate
                    previous[objects[key].key] = Object.keys(validates).reduce(function(prev, key){
                        switch(key){
                            case "required":
                                prev["required"] = required
                                return prev
                            case "minLength":
                                prev["minLength"] = minLength(validates[key])
                                return prev
                            case "maxLength":
                                prev["maxLength"] = maxLength(validates[key])
                                return prev
                            case "minValue":
                                prev["minValue"] = minValue(validates[key])
                                return prev
                            case "maxValue":
                                prev["maxValue"] = maxValue(validates[key])
                                return prev
                            case "numeric":
                                prev["numeric"] = numeric
                                return prev
                        }
                    },{});
                    return previous
                }, {})
                this.form = Object.keys(objects).reduce(function (previous, key) {
                    previous[objects[key].key] = null
                    return previous
                }, {})
                this.formItems = response.data["data"]
            })
            this.$emit('input', "实名认证")
        },
        methods: {
            getValidationClass (fieldName) {
                const field = this.$v.form[fieldName]
                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            }, submit() {
                this.$v.$touch()
                console.log(this.form)
                console.log(this.$v.$invalid)
            }, getModel(key) {
                return this.form[key]
            }, getValidationClass (fieldName) {
                const field = this.$v.form[fieldName]
                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            }
        }
    }
</script>

<style scoped>
</style>