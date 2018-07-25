<template>
    <div align="left">
        <md-card>
            <md-card-header>
                <span class="md-title">用户查询及管理</span>
            </md-card-header>
            <md-card-content>

                <div class="md-layout md-gutter">
                    <div class="md-layout-item md-small-size-100">
                        <div class="md-layout-item md-small-size-100">
                            <md-field >
                                <label>用户名</label>
                                <md-input name="username" id="username" v-model="form.username"/>
                            </md-field>
                        </div>
                    </div>
                    <div class="md-layout-item md-small-size-100">
                        <md-field>
                            <label>类型</label>
                            <md-select v-model="form.enabled" >
                                <md-option value=" ">未指定</md-option>
                                <md-option value="true">正常</md-option>
                                <md-option value="false">封禁</md-option>
                            </md-select>
                        </md-field>
                    </div>
                </div>
                <div class="md-layout md-gutter">

                    <div class="md-layout-item md-small-size-100">
                        <md-field >
                            <label>邮箱</label>
                            <md-input name="email" id="email" v-model="form.email"/>
                        </md-field>
                    </div>
                    <div class="md-layout-item md-small-size-100">
                        <md-field >
                            <label>手机</label>
                            <md-input name="phone" id="phone" v-model="form.phone"/>
                        </md-field>
                    </div>
                </div>
                <div class="md-layout md-gutter">

                    <div class="md-layout-item md-small-size-100">
                        <md-field >
                            <label>每页数量</label>
                            <md-input name="size" id="size" v-model="form.size"/>
                        </md-field>
                    </div>
                    <div class="md-layout-item md-small-size-100">
                        <md-field >
                            <label>页码</label>
                            <md-input name="page" id="page" v-model="form.page"/>
                        </md-field>
                    </div>
                </div>
                <div>
                    <md-checkbox v-model="form.verified">已实名用户</md-checkbox>
                    <md-checkbox v-model="form.reverse">倒序输出</md-checkbox>
                </div>
                <span class="md-caption">提示：输入空格可检索不为空的值。</span>
            </md-card-content>
            <md-card-actions>
                <md-button class="md-raised md-primary" @click="submit">搜索</md-button>
            </md-card-actions>
        </md-card>
        <md-table v-model="current" md-sort="name" md-sort-order="asc" md-card md-fixed-header style="margin-top: 20px;">
            <md-table-toolbar>
                <h1 class="md-title">用户</h1>
            </md-table-toolbar>
            <md-table-row slot="md-table-row" slot-scope="{ item }">
                <md-table-cell md-label="ID" md-numeric>{{ item.id }}</md-table-cell>
                <md-table-cell md-label="用户名">{{ item.username }}</md-table-cell>
                <md-table-cell md-label="邮箱">{{ item.email || "!未绑定" }}</md-table-cell>
                <md-table-cell md-label="手机">{{ item.phone || "!未绑定"}}</md-table-cell>
                <md-table-cell md-label="中文名">{{ item.chineseName || "!未实名"}}</md-table-cell>
                <md-table-cell md-label="加入时间">{{ item.joinTime | moment("LLL") }}</md-table-cell>
            </md-table-row>
        </md-table>
    </div>
</template>

<script>
    export default {
        name: "User",
        data: () => ({
            current: [],
            form: {
                username: "",
                email: "",
                phone: "",
                enabled: " ",
                verified: false,
                reverse: true,
                size: 10,
                page: 1
            }
        }),
        mounted() {
            this.submit()
        },
        methods: {
            submit() {
                this.axios.post("/admin/user", this.form).then((response) => {
                    this.current = response.data["data"]
                })
            }
        }
    }
</script>

<style scoped>

</style>