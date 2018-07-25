<template>
    <div align="left">
        <span class="md-body-2">投票系统管理</span>
        <md-field>
            <label>请选择</label>
            <md-select v-model="form.id" @md-selected="load">
                <md-option value="new">新建</md-option>
                <md-option v-for="vote in votes" :key="vote.id" :value="vote.id">{{ vote.title }} - {{ vote.id }}</md-option>
            </md-select>
        </md-field>
        <form>
            <md-field>
                <label>标题</label>
                <md-input v-model="form.title"></md-input>
            </md-field>
            <span class="md-caption">内容</span>
            <mavon-editor v-model="form.content"/>
            <br/>
            <span class="md-caption">选项</span>
            <vue-json-editor v-model="form.options" :show-btns="false"></vue-json-editor>
            <md-switch v-model="form.enabled">允许进行投票</md-switch><br/>
            <md-button class="md-raised md-primary" @click="submit">保存</md-button>
            <md-button class="md-raised md-primary" @click="r" :disabled="form.id === 'new'">结果</md-button>
        </form>
        <md-dialog :md-active.sync="showDialog">
            <md-dialog-title>结果</md-dialog-title>
            <md-dialog-content>
                <div v-if="result.length > 0">
                    <div v-for="(item, itemKey) in form.options" :key="item.text">
                        <span class="md-subheading"> 项目{{itemKey + 1}} ： {{ item.text }} </span>
                        <div v-for="(option, key) in item.options" :key="key">
                            <span class="md-body-1">{{ option }} : {{result[itemKey][key]}} 票</span><br/>
                        </div>
                        <md-divider></md-divider>
                    </div>
                    <span class="md-caption">总计：{{ total }} 票</span>
                </div>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-primary" @click="showDialog = false">关闭</md-button>
                <md-button class="md-primary" @click="r" :disabled="form.id === 'new'">刷新</md-button>
            </md-dialog-actions>
        </md-dialog>

    </div>
</template>

<script>
    import vueJsonEditor from 'vue-json-editor'

    export default {
        components: {
            vueJsonEditor
        },
        name: "VoteAdmin",
        data: () => ({
            votes: [],
            form: {
                id: "new",
                title: "",
                content: "",
                options: "[]",
                enabled: false
            },
            total: 0,
            result: [],
            showDialog: false

        }),
        mounted() {
            this.list()
            this.$emit("changeTitle", "投票管理")
        },
        methods: {
            submit() {
                this.axios.post("/school/vote/edit?id="+this.form.id, this.form).then((response) => {
                    this.form = response.data["data"]
                    this.list()
                })
            },
            load() {
                this.axios.get("/school/vote/edit?id="+this.form.id, this.form).then((response) => {
                    this.form = response.data["data"]
                    this.result = []
                })
            },
            list() {
                this.axios.get("/school/vote/list").then((response) => {
                    let t_new = this.form.id === "new";
                    this.votes = response.data["data"]
                    if(this.votes.length > 0 && t_new)
                        this.form.id = this.votes[0].id
                })
            },
            r() {
                this.axios.get("/school/vote/result?id="+this.form.id, this.form).then((response) => {
                    if(response.data["data"] == null)
                        return
                    this.result = response.data["data"]["detail"]
                    this.total = response.data["data"]["total"]
                    this.showDialog = true
                })
            }
        }
    }
</script>

<style scoped>

</style>