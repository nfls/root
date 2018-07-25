<i18n src="../../translation/frontend/Alumni.json"></i18n>
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
                            <md-input name="size" id="size" v-model="form.size" type="number"/>
                        </md-field>
                    </div>
                    <div class="md-layout-item md-small-size-100">
                        <md-field >
                            <label>页码</label>
                            <md-input name="page" id="page" v-model="form.page" type="number"/>
                        </md-field>
                    </div>
                </div>
                <div>
                    <md-checkbox v-model="form.verified">仅显示已实名南外用户</md-checkbox>
                    <md-checkbox v-model="form.notNfls">仅显示已实名非南外用户</md-checkbox>
                    <md-checkbox v-model="form.reverse">倒序输出</md-checkbox>
                </div>
                <span class="md-caption">提示：输入空格可检索不为空的值。</span><br/>
                <span class="md-caption">共{{ current.length }}条结果展示。</span>
            </md-card-content>
            <md-card-actions>
                <md-button class="md-raised md-primary" @click="submit">搜索</md-button>
            </md-card-actions>
        </md-card>
        <md-table v-model="current" md-card style="margin-top: 20px;" @md-selected="onSelect">
            <md-table-toolbar>
                <h1 class="md-title">用户</h1>
            </md-table-toolbar>
            <md-table-row slot="md-table-row" slot-scope="{ item }" md-selectable="single">
                <md-table-cell md-label="ID" md-numeric>{{ item.id }}</md-table-cell>
                <md-table-cell md-label="用户名">{{ item.username }}</md-table-cell>
                <md-table-cell md-label="邮箱">{{ item.email || "!未绑定" }}</md-table-cell>
                <md-table-cell md-label="手机">{{ item.phone || "!未绑定"}}</md-table-cell>
                <md-table-cell md-label="中文名">{{ item.chineseName || "!未实名"}}</md-table-cell>
                <md-table-cell md-label="加入时间">{{ item.joinTime | moment("lll") }}</md-table-cell>
            </md-table-row>
        </md-table>
        <md-dialog :md-active.sync="detail">
            <md-dialog-title>ID{{info.detail.id}}: {{info.detail.username}}</md-dialog-title>
            <md-dialog-content>
                <md-tabs md-dynamic-height>
                    <md-tab md-label="实名认证">
                        <md-list>
                            <md-list-item v-for="auth in info.tickets" :key="auth.id" @click="click(auth.id)"><code>{{auth.readableText}}</code>
                            </md-list-item>
                        </md-list>
                    </md-tab>
                    <md-tab md-label="iOS推送">
                        <md-list class="md-triple-line">
                            <md-list-item v-for="device in info.ios" :key="device.token">
                                <div class="md-list-item-text">
                                    <span>{{ device.model }} [{{device.remark}}]</span>
                                    <span>{{ device.time | moment("LLL" )}}</span>
                                    <span>{{ getStatus(device.status) }}</span>
                                </div>
                            </md-list-item>
                        </md-list>
                    </md-tab>

                    <md-tab md-label="微信通知">
                        <md-list class="md-triple-line">
                            <md-list-item v-for="device in info.wechat" :key="device.token">
                                <div class="md-list-item-text">
                                    <span>{{ device.model }} [{{ device.remark }}]</span>
                                    <span>{{ device.time | moment("lll" )}}</span>
                                    <span>{{ getStatus(device.status) }}</span>
                                </div>
                            </md-list-item>
                        </md-list>
                    </md-tab>
                    <md-tab md-label="账户日志">
                        <md-list class="md-triple-line">
                            <md-list-item v-for="detail in info.log" :key="detail.id">
                                <div class="md-list-item-text">
                                    <span>{{ detail.id }} - {{ detail.identifier}}</span>
                                    <span>{{ detail.time | moment("lll" )}} ({{ detail.ip }})</span>
                                    <span>{{ detail.message || "无" }}</span>
                                </div>
                            </md-list-item>
                        </md-list>
                    </md-tab>
                    <md-tab md-label="其他">
                        <md-button class="md-primary" @click="page(info.detail.id)">用户主页</md-button><br/>
                        <md-button class="md-accent" @click="disable(info.detail.id)">禁用启用</md-button><br/>
                        <md-button class="md-accent" @click="remove(info.detail.id)">移除头像</md-button>
                    </md-tab>
                </md-tabs>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-primary" @click="detail = false">关闭</md-button>
            </md-dialog-actions>
        </md-dialog>
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
                notNfls: false,
                reverse: true,
                size: 10,
                page: 1
            },
            detail: false,
            info: {
                detail: {},
                basic: {},
                tickets: [],
                ios: [],
                wechat: [],
                log: []
            }
        }),
        mounted() {
            this.submit()
            this.$emit("changeTitle", "用户管理")
        },
        methods: {
            submit() {
                this.axios.post("/admin/user", this.form).then((response) => {
                    this.current = response.data["data"]
                })
            },
            onSelect(row) {
                if(row == null)
                    return
                this.info.detail = row
                this.axios.get("/admin/detail?id="+row.id).then((response) => {
                    let content = response.data["data"]
                    this.info.ios = content["ios"]
                    this.info.wechat = content["wechat"]
                    this.info.log = content["log"]
                    this.info.tickets = content["tickets"].map((object)=>{
                        object["readableText"] = this.$t("status-" + object.status)
                        object["readableText"] += " " + object.id
                        if (object.submitTime) {
                            object["readableText"] += " " + this.$t('submit-time')  + this.$moment(object.submitTime).format("lll") + " "
                        }
                        return object
                    })
                    this.detail = true
                })
            },
            getStatus(type) {
                switch(type) {
                    case -1:
                        return "Token已失效"
                    case 0:
                        return "推送服务错误"
                    case 1:
                        return "设备注册正常"
                    case 2:
                        return "推送返回正常"
                    case 3:
                        return "设备已收通知"
                }
            },
            click(id) {
                let url = this.$router.resolve("/alumni/auth/admin/" + id)
                window.open(url.href, "_blank");
            },
            page(id) {
                let url = this.$router.resolve("/user/page/" + id)
                window.open(url.href, "_blank");
            },
            disable(id) {
                this.axios.post("/admin/disable?id="+id).then((response) => {
                    this.$emit("showMsg", "操作成功，请返回列表页刷新查看效果。")
                })
            },
            remove(id) {
                this.axios.post("/admin/avatar?id="+id).then((response) => {
                    this.$emit("showMsg", "操作成功，请返回列表页刷新查看效果。")
                })
            }
        }
    }
</script>

<style scoped>

</style>