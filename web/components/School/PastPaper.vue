<i18n src="../../translation/frontend/School.json"></i18n>
<template>
    <div class="browser">

        <md-empty-state v-if="loading && !cache"
                        md-icon="access_time"
                        :md-label="$t('loading')"
                        :md-description=currentFile
                        style="width:100%;padding-bottom: 100%;">
        </md-empty-state>
        <div v-else>
            <span class="md-caption">
                <countdown :time=" 60 * 60 * 1000" v-if="!cache">
                    <template
                            slot-scope="props">{{ $t('expire') }}{{ props.minutes }}:{{ props.seconds }}{{ $t('expire-hint')}}</template>
                </countdown>
                <p v-else>{{ $t('loading') }}：{{ currentFile }}</p>
            </span>
            <md-card v-if="toDownload.length == 0">
                <md-card-header style="text-align: left;" v-if="header !== ''">
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
                        <md-list-item v-for="item in displayItems" :key="item.name" @click="enter(item)">
                            <md-icon v-if="item.size == 0">folder</md-icon>
                            <md-icon v-else-if="item.name.endsWith('pdf')">picture_as_pdf</md-icon>
                            <md-inco v-else>attachment</md-inco>
                            <span class="md-list-item-text">{{item.displayName}}</span>
                            <md-checkbox v-model="item.selected"/>
                        </md-list-item>
                    </md-list>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-raised md-accent" @click="clean">{{ $t('clean-cache') }}</md-button>
                    <md-button class="md-raised" @click="batch">{{ $t('bulk') }}</md-button>
                </md-card-actions>
            </md-card>
            <md-card v-else>
                <md-card-content>
                    <md-progress-bar md-mode="determinate"
                                     :md-value="(current)/(toDownload.length + current + 1)*100"></md-progress-bar>
                    <br/>
                    <p align="left">
                        <span class="md-title">{{ $t('bulk') }}</span><span class="md-subheading"> {{ $t('progress') }} {{current}} / {{current + toDownload.length + 1}}</span><br/>
                        <span class="md-caption">{{ $t('current-file') }}{{filename}} </span><br/>
                        <span class="md-body-1">{{ $t('bulk-hint') }}</span>
                    </p>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-raised" @click="cancel">{{ $t('cancel') }}</md-button>
                </md-card-actions>
            </md-card>
        </div>
        <md-dialog-alert
                :md-active.sync="error"
                :md-title="$t('error')"
                :md-content="reason"/>

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
        props: ["admin", "verified", 'loggedIn'],
        data: () => ({
            fileInfo: [],
            tempInfo: [],
            path: [],
            pathString: "",
            displayItems: [],
            client: null,
            loading: true,
            cache: false,
            header: "",
            error: false,
            current: 0,
            filename: "",
            toDownload: [],
            zipFile: null,
            currentFile: "",
            reason: ""
        }),
        mounted: function () {
            var self = this
            this.currentFile = this.$t('loading-hint')
            this.$emit("changeTitle", this.$t('pp-title'))
            this.axios.get("/school/pastpaper/header").then((response) => {
                this.header = response.data["data"]
            })
            this.axios.get("/school/pastpaper/token").then((response) => {
                if(response.data["code"] === 200) {
                    let data = response.data["data"]
                    let OSS = require('ali-oss').Wrapper;
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
                } else {
                    self.reason = response.data["data"]
                    self.error = true
                }

            }).catch(function (error) {
                console.error(error)
                self.reason = self.$t('not-verified')
                self.error = true
            });
            this.$getItem("pastpaper_list",function(err, readValue) {
                if(err) {
                    console.error(err)
                } else {
                    if(Array.isArray(readValue)) {
                        self.fileInfo = readValue
                        self.cache = true
                    }
                }
            })
        }, methods: {
            loadFiles(next) {
                let self = this
                this.client.list({
                    "max-keys": 1000,
                    "marker": next
                }).then(function (result) {
                    self.tempInfo = self.tempInfo.concat(result.objects)
                    if (result.objects.length === 1000) {
                        self.currentFile = result.nextMarker
                        self.loadFiles(result.nextMarker)
                    } else {
                        self.fileInfo = self.tempInfo
                        self.$setItem("pastpaper_list", self.fileInfo)
                        self.loading = false
                        self.cache = false
                    }
                }).catch((error) => {
                    this.$emit("generalError",error)
                })
            }, getCurrentPath() {
                var reducer = (accumulator, currentValue) => accumulator + "/" + currentValue;
                var uri = this.path.reduce(reducer, "") + "/"
                if (uri.startsWith("/"))
                    uri = uri.slice(1)
                this.pathString = uri
                return uri
            }, enter(item) {
                if (item.size === 0) {
                    this.path.push(item.displayName)
                } else {
                    var url = this.client.signatureUrl(item.name, {expires: 60})
                    this.downloadURI(url, item.displayName)
                }
            }, updateItems(val) {
                var self = this
                this.displayItems = this.fileInfo.filter(function (object) {
                    if (object.name.endsWith("/")) {
                        return object.name.split("/").length - 1 === self.path.length + 1 && object.name.startsWith(self.getCurrentPath())
                    } else {
                        return object.name.split("/").length === self.path.length + 1 && object.name.startsWith(self.getCurrentPath())
                    }
                })
                this.displayItems = this.displayItems.map(function (object) {
                    object.displayName = object.name.replace(self.getCurrentPath(), "").replace("/", "")
                    return object
                })
            }, back() {
                this.path.pop()
            }, downloadURI(uri, name) {
                let link = document.createElement("a");
                link.download = name;
                link.href = uri;
                link.target = "_blank";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, batch() {
                let items = this.displayItems.filter(function (item) {
                    return item.selected === "on"
                })
                this.toDownload = []
                items.forEach((value) => {
                    if (value.size === 0)
                        this.toDownload = this.toDownload.concat(this.fileInfo.filter(item => item.name.startsWith(value.name)))
                    else
                        this.toDownload = this.toDownload.concat(value)
                })
                var JSZip = require("jszip");
                this.zipFile = new JSZip();
                this.cuurent = 0
                this.downloadBatch()

            }, downloadBatch() {
                this.current++
                if (this.toDownload.length === 0) {
                    var FileSaver = require('file-saver')
                    this.zipFile.generateAsync({type: "blob"}).then(function (blob) {
                        FileSaver.saveAs(blob, "Past Papers@" + (Math.floor(Math.random() * 10000000000)) + ".zip");
                    })
                } else {
                    let item = this.toDownload.pop()
                    if (item.size === 0) {
                        this.downloadBatch()
                        return
                    }
                    this.filename = item.name
                    this.axios.get(this.client.signatureUrl(item.name), {
                        responseType: 'blob'
                    }).then((response) => {
                        this.zipFile.file(item.name, response.data)
                        this.downloadBatch()
                    }).catch((error) => {
                        console.error(error)
                        this.filename = this.$t('bulk-error')
                    })
                }
            }, cancel() {
                this.toDownload = []
            }, clean() {
                this.$removeItem("pastpaper_list")
                window.location.reload()
            }
        }, watch: {
            fileInfo: function (val) {
                this.updateItems(val)
            },
            path: function (val) {
                this.updateItems(this.fileInfo)
            }
        }
    }
</script>

<style scoped>

</style>