<template>
    <div class="realname">
        <md-card class="review" v-if="adminMode">
            <md-card-header>
                <div class="md-title">审核</div>
            </md-card-header>
            <md-card-content>
                <form novalidate class="md-layout-row md-gutter">
                    <div class="md-flex">
                        <md-datepicker format="MM/dd/yy" v-model="reviewDate"></md-datepicker>
                    </div>
                </form>
            </md-card-content>
            <md-card-actions md-alignment="left">
                <md-button @click="acceptWithLimit">接受有期限</md-button>
                <md-button @click="acceptWithoutLimit">接受无期限</md-button>
                <md-button @click="reject">拒绝</md-button>
            </md-card-actions>
        </md-card>
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
                                <span class="md-error" v-else-if="!$v.form[item.key].minLength">填写的内容不正确</span>
                                <span class="md-error" v-else-if="!$v.form[item.key].maxLength">填写的内容不正确</span>
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
                    <div v-if="!isDisabled">
                        <div class="md-flex md-flex-small-100" v-if="changed">
                            <md-button type="save" class="md-raised md-primary" style="width:90%">保存</md-button>
                        </div>
                        <div class="md-flex md-flex-small-100" v-if="!changed">
                            <md-button type="save" class="md-raised md-primary" style="width:90%">提交</md-button>
                        </div>
                    </div>
                    <div class="md-flex md-flex-small-100" v-else-if="status == 1" >
                        <md-button type="button" class="md-raised md-accent" style="width:90%" @click="cancel">取消本次申请</md-button>
                    </div>
                    <div v-else>
                        <md-button type="button" class="md-raised md-primary" style="width:90%" disabled>此验证只读</md-button>
                    </div>
                    <md-progress-bar md-mode="indeterminate" v-if="sending" />
                </form>
            </md-card-content>
        </md-card>
        <md-dialog-confirm
                :md-active.sync="active"
                md-title="您确定要提交吗"
                md-content="除了取消审核，审核期间您将无法修改您所提交的内容。"
                md-confirm-text="提交"
                md-cancel-text="取消"
                @md-confirm="submit" />
        <md-dialog-alert
                :md-active.sync="error"
                md-title="表格内容填写错误"
                :md-content="errors"
                md-confirm-text="确认" />
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
        props: ["admin","verified",'loggedIn'],
        data: () => ({
            formItems: [],
            validatorItems: [],
            form: [],
            hide: [],//反过来的。。。
            reviewDate: null,
            reactor: [],
            countries: [],
            showForm: false,
            isDisabled: true,
            status: 0,
            sending: false,
            changed: false,
            active: false,
            adminMode: false,
            csrf: null,
            error: false,
            errors: ""
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
                this.isDisabled = (formData.status != 0)
                this.status = formData.status
                this.axios.get("/alumni/form").then((response) => {
                    var objects = response.data["data"]
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
                    this.form = Object.keys(objects).reduce(function (previous, key) {
                        if(objects[key].type != "divider"){
                            if(formData[objects[key].key] !== null)
                                previous[objects[key].key] = String(formData[objects[key].key])
                            else
                                previous[objects[key].key] = null
                        }
                        return previous
                    }, {})
                    this.refreshValidator(objects)
                    this.formItems = response.data["data"]
                    this.ddlHelper()
                    this.getCsrf()
                })
            })


            this.axios.get("/alumni/countries").then((response) => {
                this.countries = response.data["data"]
            })
            this.$emit('changeTitle', "实名认证 - 表格填写")
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
                        this.form._csrf = this.csrf;
                        this.axios.post("alumni/save?id="+this.$route.params["id"],this.form).then((response) => {
                            this.getCsrf()
                            this.isDisabled = false
                            if(!this.adminMode)
                                this.changed = false
                            this.sending = false
                            this.$emit("showMsg", "保存成功，您可以提交了")
                        })
                    }else{
                        this.active = true
                    }

                }

            }, submit(){
                this.isDisabled = true
                this.sending = true
                this.form._csrf = this.csrf;
                this.axios.post("alumni/submit?id="+this.$route.params["id"],this.form).then((response) => {
                    var self = this
                    this.getCsrf()
                    var data = response.data
                    this.sending = false
                    this.isDisabled = true
                    if(data.code == 200){
                        this.$emit("showMsg", "提交成功，请等待审核！")
                        var self = this
                        setTimeout(function(){
                            self.$router.push("/alumni/auth")
                        },2000)
                    }else{
                        self.errors = ""
                        Object.values(response.data["data"]).forEach(function(val){
                            self.errors = self.errors + ", " + val
                        })
                        self.errors = self.errors.substring(1)
                        self.error = true
                        this.isDisabled = false
                    }

                })
            }, cancel(){
                this.form._csrf = this.csrf;
                this.axios.post("alumni/cancel?id="+this.$route.params["id"],this.form).then((response) => {
                    this.$emit("showMsg", "本次验证已被取消，您可以重新提交了。")
                    var self = this
                    setTimeout(function(){
                        self.$router.push("/alumni/auth")
                    },3000)
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
            }, acceptWithLimit() {
                this.axios.post("/admin/alumni/auth/update",{
                    id: this.$route.params["id"],
                    action: "accept",
                    time: this.reviewDate,
                    _csrf: this.csrf
                }).then((response) => {
                    if(response.data["code"] == 200)
                        window.close()
                })
            }, acceptWithoutLimit() {
                this.axios.post("/admin/alumni/auth/update",{
                    id: this.$route.params["id"],
                    action: "accept",
                    _csrf: this.csrf
                }).then((response) => {
                    if(response.data["code"] == 200)
                    window.close()
                })
            }, reject() {
                this.axios.post("/admin/alumni/auth/update",{
                    id: this.$route.params["id"],
                    action: "reject",
                    _csrf: this.csrf
                }).then((response) => {
                    if(response.data["code"] == 200)
                    window.close()
                })
            }, ddlHelper() {
                if(this.form.userStatus == "0" && this.form.juniorRegistration){
                    this.reviewDate = new Date(this.form.juniorRegistration + "/06/30")
                }
                if(this.form.userStatus == "1" && this.form.seniorRegistration){
                    this.reviewDate = new Date(this.form.seniorRegistration + "/06/30")
                }
            }, getCsrf() {
                this.axios.get("user/csrf",{
                    params: {
                        name: "alumni.form"
                    }
                }).then((response) => {
                    this.csrf = response.data["data"]
                })
            }
        },
        watch: {
            form: {
                handler(newVal) {
                    this.changed = true
                    var reactor = this.reactor
                    var hide = this.hide
                    var form = this.form
                    Object.keys(hide).reduce(function (previous, key){
                        hide[key] = true
                    })
                    Object.keys(newVal).reduce(function (previous, key){
                        if(reactor[key] && newVal[key]){
                            reactor[key][newVal[key]].reduce(function (previous, val){
                                hide[val] = false
                                form[val] = null
                            },{})
                        }
                    },{})
                    this.hide = hide
                    this.form = form
                    this.refreshValidator(this.formItems)
                },
                deep: true
            },
            admin: {
                handler(newVal) {
                    var path = this.$route.fullPath
                    if(path.startsWith('/alumni/auth/admin')){
                        this.adminMode = this.admin
                        if(this.adminMode)
                            this.isDisabled = false
                    }
                }
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
    .md-card {
        margin:10px;
    }
</style>