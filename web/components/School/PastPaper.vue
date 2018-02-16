<template>
    <div class="browser">

        <md-empty-state v-if="loading"
                md-icon="access_time"
                md-label="加载中"
                :md-description=currentFile
                style="width:100%;padding-bottom: 100%;">
        </md-empty-state>
        <div v-else>
            <span class="md-caption">
                <countdown :time=" 60 * 60 * 1000">
                    <template slot-scope="props">剩余有效时间：{{ props.minutes }}:{{ props.seconds }}。超出时间后请返回Dashboard并重新进入。</template>
                </countdown>
            </span>
            <md-card v-if="toDownload.length == 0">
                <md-card-header style="text-align: left;">
                    <vue-markdown>{{header}}</vue-markdown>
                    <md-divider></md-divider>

                </md-card-header>
                <md-divider></md-divider>
                <md-card-content>
                    <p align="left">
                        <span class="md-caption">Path: {{pathString}}</span>
                    </p>
                    <md-list>
                        <md-list-item v-if="path.length > 0" @click="back">
                            <md-icon>folder_open</md-icon>
                            <span class="md-list-item-text">..</span>
                        </md-list-item>
                        <md-list-item v-for="item in displayItems" :key="item.etag" @click="enter(item)">
                            <md-icon v-if="item.size == 0">folder</md-icon>
                            <md-icon v-else-if="item.name.endsWith('pdf')">picture_as_pdf</md-icon>
                            <md-inco v-else>attachment</md-inco>
                            <span class="md-list-item-text">{{item.displayName}}</span>
                            <md-checkbox v-model="item.selected"/>
                        </md-list-item>
                    </md-list>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-raised" @click="batch">批量下载</md-button>
                </md-card-actions>
            </md-card>
            <md-card v-else>
                <md-card-content>
                    <md-progress-bar md-mode="determinate" :md-value="(current)/(toDownload.length + current + 1)*100"></md-progress-bar><br/>
                    <p align="left">
                        <span class="md-title">批量下载</span><span class="md-subheading"> 第 {{current}} 个，共 {{current + toDownload.length + 1}} 个</span><br/>
                        <span class="md-caption">当前文件：{{filename}} </span><br/>
                        <span class="md-body-1">下载过程中，请保持网络畅通，并请不要做任何无关操作！</span>
                    </p>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-raised" @click="cancel">取消</md-button>
                </md-card-actions>
            </md-card>
        </div>
        <md-dialog-alert
                :md-active.sync="error"
                md-title="错误"
                md-content="请确保您已登录您的账户并通过实名认证。如果实名认证已提交，请耐心等待审核！" />

    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'
    import VueCountdown from '@xkeshi/vue-countdown'
    export default {
        name: "Past-paper",
        components: {
            VueMarkdown,
            'countdown': VueCountdown
        },
        props: ["admin","verified",'loggedIn'],
        data: () => ({
            fileInfo: [],
            path: [],
            pathString: "",
            displayItems: [],
            client: null,
            loading: true,
            header: "",
            error: false,
            current: 0,
            filename: "",
            toDownload: [],
            zipFile: null,
            currentFile: "如果长期卡住，请考虑刷新或更换浏览器。"
        }),
        mounted: function() {
            var self = this
            this.$emit("changeTitle","PP(Past Papers)")
            this.axios.get("/school/pastpaper/header").then((response) => {
                this.header = response.data["data"]
            })
            this.axios.get("/school/pastpaper/token").then((response) => {
                var data = response.data["data"]
                var OSS = require('ali-oss').Wrapper;
                this.client = new OSS({
                    region: 'oss-cn-shanghai',
                    accessKeyId: data["AccessKeyId"],
                    accessKeySecret: data["AccessKeySecret"],
                    stsToken: data["SecurityToken"],
                    bucket: 'nfls-papers',
                    secure: true,
                    timeout: "60s"
                });
                this.loadFiles("")
            }).catch(function (error) {
                self.error = true
            });

        }, methods: {
            loadFiles(next){
                var self = this
                var result = this.client.list({
                    "max-keys": 1000,
                    "marker": next
                }).then(function (result) {
                    self.fileInfo = self.fileInfo.concat(result.objects)
                    if(result.objects.length == 1000){
                        self.currentFile = result.nextMarker
                        self.loadFiles(result.nextMarker)
                    }else{
                        self.loading = false
                    }
                }).catch(function (err) {
                    console.error(err);
                });
            }, getCurrentPath(){
                var reducer = (accumulator, currentValue) => accumulator + "/" + currentValue;
                var uri = this.path.reduce(reducer,"") + "/"
                if(uri.startsWith("/"))
                    uri = uri.slice(1)
                this.pathString = uri
                return uri
            }, enter(item) {
                if(item.size == 0){
                    this.path.push(item.displayName)
                } else {
                    var url = this.client.signatureUrl(item.name, {expires: 60})
                    this.downloadURI(url,item.displayName)
                }
            }, updateItems(val){
                var self = this
                this.displayItems = this.fileInfo.filter(function(object){
                    if(object.name.endsWith("/")){
                        return object.name.split("/").length - 1 == self.path.length + 1 && object.name.startsWith(self.getCurrentPath())
                    }else{
                        return object.name.split("/").length == self.path.length + 1 && object.name.startsWith(self.getCurrentPath())
                    }
                })
                this.displayItems = this.displayItems.map(function(object){
                    object.displayName = object.name.replace(self.getCurrentPath(),"").replace("/","")
                    return object
                })
            }, back() {
                this.path.pop()
            }, downloadURI(uri, name) {
                var link = document.createElement("a");
                link.download = name;
                link.href = uri;
                link.target = "_blank";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, batch() {
                let items = this.displayItems.filter(function(item){
                    return item.selected == "on"
                })
                this.toDownload = []
                items.forEach((value) => {
                    if(value.size == 0)
                        this.toDownload = this.toDownload.concat(this.fileInfo.filter(item => item.name.startsWith(value.name)))
                    else
                        this.toDownload = this.toDownload.concat(value)
                })
                var JSZip = require("jszip");
                this.zipFile = new JSZip();
                this.cuurent = 0
                this.downloadBatch()

            }, downloadBatch() {
                this.current ++
                if(this.toDownload.length == 0) {
                    var FileSaver = require('file-saver')
                    this.zipFile.generateAsync({type:"blob"}).then(function (blob) {
                        FileSaver.saveAs(blob, "Past Papers@" + (Math.floor(Math.random() * 10000000000)) + ".zip");
                    })
                } else {
                    var item = this.toDownload.pop()
                    if(item.size == 0){
                        this.downloadBatch()
                        return
                    }
                    this.filename = item.name
                    this.axios.get(this.client.signatureUrl(item.name),{
                        responseType: 'blob'
                    }).then((response) => {
                        this.zipFile.file(item.name,response.data)
                        this.downloadBatch()
                    }).catch((error) => {
                        this.filename = "下载出错。"
                    })
                }
            }, cancel() {
                this.toDownload = []
            }
        }, watch: {
            fileInfo: function(val) {
                this.updateItems(val)
            },
            path: function(val) {
                this.updateItems(this.fileInfo)
            }
        }
    }
</script>

<style scoped>

</style>