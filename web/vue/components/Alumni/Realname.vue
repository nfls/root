<template>
    <div class="login">
        <form novalidate class="md-layout-row md-gutter" @submit.prevent="submit">
            <div class="md-flex" v-for="item in formItems" :key="item.key">
                <md-field v-if="item.type == 'select'">
                    <label :for="item.key">{{item.name}}</label>
                    <md-select :name="item.key" :id="item.key">
                        <md-option v-for="value in item.values" :key="value.value" :value="value.value">{{value.name}}</md-option>
                    </md-select>
                </md-field>
                <md-field v-else-if="item.type == 'input'">
                    <label :for="item.key">{{item.name}}</label>
                    <md-input :name="item.key" :id="item.key" :autocomplete="item.key"></md-input>
                </md-field>
                <md-datepicker v-else-if="item.type == 'date'" :name="item.key" :id="item.key" :autocomplete="item.key"/>
            </div>
            <md-progress-bar md-mode="indeterminate" v-if="sending" />
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
        mounted: function () {
            this.axios.get("/alumni/form").then((response) => {
                this.formItems = response.data["data"]
                var objects = response.data["data"];
                this.validatorItems = Object.keys(objects).reduce(function (previous, key) {
                    previous[objects[key].key] = null
                    return previous;
                }, {});
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
            }
        }
    }
</script>

<style scoped>

</style>