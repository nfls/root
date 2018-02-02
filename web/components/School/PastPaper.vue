<template>
    <div class="browser">

        <md-empty-state v-if="loading"
                md-rounded
                md-icon="access_time"
                md-label="Loading"
                md-description="Please wait for a few seconds."
                style="width:100%;padding-bottom: 100%;">
        </md-empty-state>
        <div v-else>
            <span class="md-body-1">废话啰嗦的说明文字废话啰嗦的说明文字废话啰嗦的说明文字废话啰嗦的说明文字废话啰嗦的说明文字废话啰嗦的说明文字废话啰嗦的说明文字废话啰嗦的说明文字废话啰嗦的说明文字</span>
            <md-field>
                <label for="path">Path</label>
                <md-input name="path" id="path" v-model="pathString" readonly disabled/>
            </md-field>
            <md-list>
                <md-list-item v-if="path.length > 0" @click="back">
                    <md-icon>folder_open</md-icon>
                    <span class="md-list-item-text">..</span>
                </md-list-item>
                <md-list-item v-for="item in displayItems" :key="item.etaf" @click="enter(item)">
                    <md-icon v-if="item.size == 0">folder</md-icon>
                    <md-icon v-else-if="item.name.endsWith('pdf')">picture_as_pdf</md-icon>
                    <md-inco v-else>attachment</md-inco>
                    <span class="md-list-item-text">{{item.displayName}}</span>
                </md-list-item>
            </md-list>
        </div>

    </div>
</template>

<script>
    export default {
        name: "Past-paper",
        data: () => ({
            fileInfo: [],
            path: [],
            pathString: "",
            displayItems: [],
            client: null,
            loading: true
        }),
        mounted: function() {
            this.axios.get("/oauth/downloadSts").then((response) => {
                var data = response.data["data"]
                var OSS = require('ali-oss').Wrapper;

                this.client = new OSS({
                    region: 'oss-cn-shanghai',
                    accessKeyId: data["AccessKeyId"],
                    accessKeySecret: data["AccessKeySecret"],
                    stsToken: data["SecurityToken"],
                    bucket: 'nfls-papers',
                    secure: true
                });
                this.loadFiles("")
            })
        }, methods: {
            loadFiles(next){
                var self = this
                var result = this.client.list({
                    "max-keys": 1000,
                    "marker": next
                }).then(function (result) {
                    self.fileInfo = self.fileInfo.concat(result.objects)
                    if(result.objects.length == 1000){
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
                    var url = this.client.signatureUrl(item.name, {expires: 120})
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