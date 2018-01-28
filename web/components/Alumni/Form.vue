<template>
    <div class="realname">
        <md-card class="form">
            <md-card-header>
                <div class="md-title">实名认证</div>
            </md-card-header>
            <md-card-content>
                <form novalidate class="md-layout-row md-gutter" @submit.prevent="save">
                    <div class="md-flex" v-for="item in formItems" :key="item.key">
                        <div v-if="hide[item.key]">
                            <md-field v-if="item.type == 'select'" :class="getValidationClass(item.key)">
                                <label :for="item.key">{{item.name}}</label>
                                <md-select :name="item.key" :id="item.key" v-model="form[item.key]" :disabled="isDisabled">
                                    <md-option v-for="value in item.values" :key="value.value" :value="value.value">{{value.name}}</md-option>
                                </md-select>
                                <span class="md-error" v-if="!$v.form[item.key].required">本项必填</span>
                            </md-field>
                            <md-field v-else-if="item.type == 'input'" :class="getValidationClass(item.key)">
                                <label :for="item.key">{{item.name}}</label>
                                <md-input :name="item.key" :id="item.key" :autocomplete="item.key" v-model="form[item.key]" :disabled="isDisabled"></md-input>
                                <span class="md-error" v-if="!$v.form[item.key].required">本项必填</span>
                                <span class="md-error" v-else-if="!$v.form[item.key].minLength">填写的内容太少</span>
                                <span class="md-error" v-else-if="!$v.form[item.key].maxLength">填写的内容太多</span>
                                <span class="md-error" v-else-if="!$v.form[item.key].minValue">填写的内容不正确</span>
                                <span class="md-error" v-else-if="!$v.form[item.key].maxValue">填写的内容不正确</span>
                            </md-field>
                            <md-field v-else-if="item.type == 'textarea'" :class="getValidationClass(item.key)">
                                <label :for="item.key">{{item.name}}</label>
                                <md-textarea :name="item.key" :id="item.key" :autocomplete="item.key" v-model="form[item.key]" :disabled="isDisabled"></md-textarea>
                                <span class="md-error" v-if="!$v.form[item.key].required">本项必填</span>
                            </md-field>
                            <md-field v-if="item.type == 'country'" :class="getValidationClass(item.key)">
                                <label :for="item.key">{{item.name}}</label>
                                <md-select :name="item.key" :id="item.key" v-model="form[item.key]" :disabled="isDisabled">
                                    <md-option v-for="country in countries" :key="country.code" :value="country.code">{{country.code}} - {{country.name}}</md-option>
                                </md-select>
                                <span class="md-error" v-if="!$v.form[item.key].required">本项必填</span>
                            </md-field>
                            <md-datepicker format="MM/dd/yy" v-else-if="item.type == 'date'" :name="item.key" :id="item.key" :autocomplete="item.key"  :class="getValidationClass(item.key)" v-model="form[item.key]"/>
                            <div v-else-if="item.type == 'divider'">
                                <md-divider></md-divider>
                                <md-content>{{item.name}}</md-content>
                            </div>
                        </div>
                    </div>
                    <div class="md-flex md-flex-small-100" v-if="changed" :disabled="isDisabled">
                        <md-button type="save" class="md-raised md-primary" style="width:90%">保存</md-button>
                    </div>
                    <div class="md-flex md-flex-small-100" v-if="!changed" :disabled="isDisabled">
                        <md-button type="save" class="md-raised md-primary" style="width:90%">提交</md-button>
                    </div>
                    <md-progress-bar md-mode="indeterminate" v-if="sending" />
                    <md-snackbar :md-active.sync="showMessage">{{message}}</md-snackbar>
                </form>
            </md-card-content>
        </md-card>
        <md-dialog-confirm
                :md-active.sync="active"
                md-title="您确定要提交吗"
                md-content="一旦提交，在审核之前，您将<strong>无法</strong>编辑您填写的任何信息！"
                md-confirm-text="提交"
                md-cancel-text="取消"
                @md-confirm="submit" />
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
        name: 'Form',
        mixins: [validationMixin],
        data: () => ({
            formItems: [],
            validatorItems: [],
            form: [],
            hide: [],//反过来的。。。
            reactor: [],
            countries: [],
            showForm: false,
            isDisabled: false,
            sending: false,
            changed: false,
            showMessage: false,
            active: false,
            message: ""
        }),
        validations() {
            return {
                form: this.validatorItems
            }
        },
        mounted: function () {
            this.axios.get("/alumni/detail",{
                params: {
                    id: this.$route.params["id"]
                }
            }).then((response) => {
                var formData = response.data["data"]
                if(formData.status > 0)
                    this.isDisable = true
                this.axios.get("/alumni/form").then((response) => {
                    var objects = response.data["data"]
                    this.form = Object.keys(objects).reduce(function (previous, key) {
                        if(objects[key].type != "divider"){
                            previous[objects[key].key] = formData[objects[key].key]
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
                                if(values[key].hidden){
                                    prev[values[key].value] = values[key].hidden
                                }else{
                                    prev[values[key].value] = []
                                }
                                return prev
                            }, {})
                        }
                        return previous
                    }, {})
                    this.refreshValidator(objects)
                    this.formItems = response.data["data"]
                })
            })


            this.axios.get("/alumni/countries").then((response) => {
                this.countries = response.data["data"]
            })
            this.$emit('input', "实名认证 - 表格填写")
        },
        methods: {
            getValidationClass (fieldName) {
                const field = this.$v.form[fieldName]
                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            }, save() {
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    if(this.changed){
                        this.isDisabled = true
                        this.sending = true
                        this.axios.post("alumni/save?id="+this.$route.params["id"],this.form).then((response) => {
                            this.isDisabled = false
                            this.changed = false
                            this.sending = false
                            this.message = "保存成功，您可以提交了"
                            this.showMessage = true
                        })
                    }else{
                        this.active = true
                    }

                }

            }, submit(){
                this.isDisabled = true
                this.sending = true
                this.axios.post("alumni/submit?id="+this.$route.params["id"],this.form).then((response) => {
                    var data = response.data["data"]
                    this.sending = false
                    this.isDisabled = true
                    if(data.code == 200){
                        this.message = "提交成功，请等待审核。您可以返回上个页面查询实名认证的状态"
                    }else{
                        this.message = "提交失败，请检查您填写的内容是否正确！"
                        this.isDisabled = false
                    }
                    this.showMessage = true

                })
            }, getModel(key) {
                return this.form[key]
            }, getValidationClass (fieldName) {
                const field = this.$v.form[fieldName]
                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            }, refreshValidator(objects){
                var hide = this.hide
                this.validatorItems = Object.keys(objects).reduce(function (previous, key){
                    if(objects[key].type != "divider" && hide[objects[key].key]) {
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
            }
        },
        watch: {
            form: {
                handler(newVal) {
                    this.changed = true
                    var reactor = this.reactor
                    var hide = this.hide
                    Object.keys(hide).reduce(function (previous, key){
                        hide[key] = true
                    })
                    Object.keys(newVal).reduce(function (previous, key){
                        if(reactor[key] && newVal[key]){
                            reactor[key][newVal[key]].reduce(function (previous, val){
                                hide[val] = false
                            },{})
                        }
                    },{})
                    this.hide = hide
                    this.refreshValidator(this.formItems)
                },
                deep: true
            }
        }
    }
</script>

<style scoped>
    .md-progress-bar {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
    }
    .realname {
        width: 100%;
        margin-left: auto;
        margin-right: auto;
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