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
        </form>
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
                id: "",
                title: "",
                content: "",
                options: "[]",
                enabled: false
            }

        }),
        mounted() {
            this.list()
        },
        methods: {
            submit() {
                this.axios.post("/school/vote/edit?id="+this.form.id, this.form).then((response) => {
                    this.form = response.data["data"]
                    this.axios.get("/school/vote/list").then((response) => {
                        this.votes = response.data["data"]
                        this.list()
                    })
                })
            },
            load() {
                this.axios.get("/school/vote/edit?id="+this.form.id, this.form).then((response) => {
                    this.form = response.data["data"]
                })
            },
            list() {
                this.axios.get("/school/vote/list").then((response) => {
                    this.votes = response.data["data"]
                    if(this.votes.length > 0 && this.id === "")
                        this.form.id = this.votes[0].id
                })
            }
        }
    }
</script>

<style scoped>

</style>