<template>
    <div class="login">
        <form novalidate class="md-layout-row md-gutter" @submit.prevent="submit">
            <div class="md-flex" v-for="item in formItems" :key="item.key">
                <md-field v-if="item.type == 'select'" :class="getValidationClass(item.key)">
                    <label :for="item.key">{{item.name}}</label>
                    <md-select :name="item.key" :id="item.key" v-model="form[item.key]">
                        <md-option v-for="value in item.values" :key="value.value" :value="value.value">{{value.name}}</md-option>
                    </md-select>
                </md-field>
                <md-field v-else-if="item.type == 'input'" :class="getValidationClass(item.key)">
                    <label :for="item.key">{{item.name}}</label>
                    <md-input :name="item.key" :id="item.key" :autocomplete="item.key" v-model="form[item.key]"></md-input>
                </md-field>
                <md-datepicker v-else-if="item.type == 'date'" :name="item.key" :id="item.key" :autocomplete="item.key"  :class="getValidationClass(item.key)" v-model="form[item.key]"/>
            </div>
            <div class="md-flex md-flex-small-100">
                <md-button type="submit" class="md-raised md-primary" style="width:90%">Login</md-button>
            </div>
            <md-progress-bar md-mode="indeterminate" v-if="sending" />
            <md-snackbar :md-active.sync="userSaved">{{message}}</md-snackbar>
        </form>
    </div>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import {
        required,
        minLength,
        maxLength
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