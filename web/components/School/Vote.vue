<i18n src="../../translation/frontend/School.json"></i18n>
<template>
    <div align="left">
        <md-field>
            <label>请选择</label>
            <md-select v-model="id" @md-selected="load">
                <md-option v-for="vote in votes" :key="vote.id" :value="vote.id">{{ vote.title }}</md-option>
            </md-select>
        </md-field>
        <div v-if="current">

            <vue-markdown> {{ current.content }}</vue-markdown>
            <md-card>
                <md-card-header>
                    <div class="md-title">{{ current.title }}</div>
                </md-card-header>
                <md-card-content>
                    <span v-if="code === 401 && !current.enabled" class="md-caption">投票已结束，或尚未开始</span>
                    <span v-if="code === 401 && current.enabled" class="md-caption">您所在的用户组无法投票</span>
                    <span v-if="code === 403" class="md-caption">您已经投过票了</span>
                    <form>
                        <div v-for="(item, itemKey) in current.options" :key="item.text">
                            <span class="md-body-2"> 项目{{itemKey + 1}} ： {{ item.text }} </span>
                            <div>
                                <md-radio v-for="(option, key) in item.options" :key="option" v-model="choices[itemKey]" :value="key" :disabled="!current.enabled || code !== 400">{{ option }}</md-radio>
                            </div>
                        </div>
                    </form>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-raised md-primary" :disabled="!current.enabled || code !== 400" @click="confirm">提交</md-button>
                </md-card-actions>
            </md-card>
        </div>
        <md-dialog-confirm
                :md-active.sync="confirmation"
                md-title="确认提交"
                md-content="提交后，您将无法修改您的选择。您确认要提交吗？"
                md-confirm-text="确认"
                md-cancel-text="取消"
                @md-confirm="submit" />
        <md-dialog-alert
                :md-active.sync="error"
                :md-content="message"
                md-confirm-text="好的" />
        <md-dialog :md-active.sync="result">
            <md-dialog-title>投票成功</md-dialog-title>
            <div style="margin-left: 20px; margin-right: 20px;">
                <span class="md-body-2">
                您已投票成功。该查询码可用于查询自己的投票信息。请注意保护好该查询码，不要告诉他人。
            </span><br/><br/>
                <span class="md-body-1">{{ query }}</span>
            </div>
            <md-dialog-actions>
                <md-button class="md-primary" @click="result = false">关闭</md-button>
            </md-dialog-actions>
        </md-dialog>
    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'

    export default {
        components: {
            VueMarkdown
        },
        name: "Vote",
        mounted: function () {
            this.$emit('changeTitle', "投票")
            this.list()
        },
        data: () => ({
            id: "",
            votes: [],
            current: {},
            choices: [],
            confirmation: false,
            error: false,
            message: "",
            result: false,
            code: "",
            query: ""
        }),
        methods: {
            list() {
                this.axios.get("/school/vote/list").then((response) => {
                    this.votes = response.data["data"]
                    if(this.votes.length > 0)
                        this.id = this.votes[0].id
                })
            },
            load() {
                this.axios.get("/school/vote/detail?id="+this.id).then((response) => {
                    this.current = response.data["data"]
                    this.status()
                })
            },
            confirm() {
                this.confirmation = true
            },
            submit() {
                this.axios.post("/school/vote/vote", {
                    id: this.id,
                    choices: this.choices
                }).then((response) => {
                    if(response.data["code"] === 200){
                        this.result = true
                        this.query = response.data["data"].code
                        this.status()
                    }else{
                        this.message = response.data["data"]
                        this.error = true
                    }
                }).catch((error) => {
                    this.message = "网络错误，请联系管理员"
                    this.error = true
                    console.error(error)
                })
            },
            status() {
                this.axios.post("/school/vote/vote", {
                    id: this.id
                }).then((response) => {
                    this.code = response.data["code"]
                })
            }
        }
    }
</script>

<style scoped>
</style>