<template>
    <div class="realname">
        <md-card class="form">
            <md-card-header>
                <div class="md-title">实名认证表格</div>
            </md-card-header>
            <md-card-content>
                <form novalidate class="md-layout-row md-gutter" @submit.prevent="submit">
                    <div class="md-flex" v-for="item in formItems" :key="item.key">
                        <div v-if="hide[item.key]">
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
                            <md-field v-if="item.type == 'country'" :class="getValidationClass(item.key)">
                                <label :for="item.key">{{item.name}}</label>
                                <md-select :name="item.key" :id="item.key" v-model="form[item.key]" :disabled="sending">
                                    <md-option v-for="country in countries" :key="country.country_code" :value="country.country_code">{{country.country_name}}</md-option>
                                </md-select>
                            </md-field>
                            <md-datepicker v-else-if="item.type == 'date'" :name="item.key" :id="item.key" :autocomplete="item.key"  :class="getValidationClass(item.key)" v-model="form[item.key]" :disabled="sending"/>
                            <div v-else-if="item.type == 'divider'">
                                <md-divider></md-divider>
                                <md-content>{{item.name}}</md-content>
                            </div>
                        </div>
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
            hide: [],
            reactor: [],
            countries: [],
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
                    if(objects[key].type != "divider") {
                        var validates = objects[key].validate
                        previous[objects[key].key] = Object.keys(validates).reduce(function (prev, key) {
                            switch (key) {
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
                        }, {});
                    }
                    return previous
                }, {})
                this.form = Object.keys(objects).reduce(function (previous, key) {
                    if(objects[key].type != "divider"){
                        previous[objects[key].key] = null
                    }
                    return previous
                }, {})
                this.hide = Object.keys(objects).reduce(function (previous, key) {
                    previous[objects[key].key] = true
                    return previous
                }, {})
                this.reactor = Object.keys(objects).reduce(function (previous, key) {
                    if(objects[key].type == "select") {
                        var values = objects[key].values
                        previous[objects[key].key] = Object.keys(values).reduce(function (prev, key) {
                            if(objects[key].hidden){
                                prev[values[key].value] = objects[key].hidden
                            }else{
                                prev[values[key].value] = []
                            }
                            return prev
                        },{})
                    }
                    return previous
                },{})
                console.log(this.reactor)
                this.formItems = response.data["data"]
            })
            this.axios.get("/alumni/countries").then((response) => {
                this.countries = response.data["data"]
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
        },
        watch: {
            form: {
                handler(newVal) {
                    Object.keys(newVal).reduce(function (previous, key){
                        console.log(key)
                    })
                },
                deep: true
            }
        }
    }
</script>

<style scoped>
    .realname {
        width: 70%;
        margin-left: auto;
        margin-right: auto;
        min-width: 200px;
    }
    .md-field {
        width: 90%;
        margin-left: auto;
        margin-right: auto;
    }
    .md-content {
        margin:5px;
    }
</style>