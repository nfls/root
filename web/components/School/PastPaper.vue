<template>
    <div class="browser">
        <md-list>
            <md-list-item v-if="path.length > 0" @click="back">
                <md-icon>move_to_inbox</md-icon>
                <span class="md-list-item-text">Back</span>
            </md-list-item>
            <md-list-item v-for="item in displayItems" :key="item.etaf" @click="enter(item.name)">
                <md-icon>move_to_inbox</md-icon>
                <span class="md-list-item-text">{{item.name}}</span>
            </md-list-item>
        </md-list>
    </div>
</template>

<script>
    export default {
        name: "Past-paper",
        data: () => ({
            fileInfo: [],
            path: [],
            displayItems: []
        }),
        mounted: function() {
            this.axios.get("/oauth/downloadSts").then((response) => {
                var data = response.data["data"]
                var OSS = require('ali-oss').Wrapper;

                var client = new OSS({
                    region: 'oss-cn-shanghai',
                    accessKeyId: data["AccessKeyId"],
                    accessKeySecret: data["AccessKeySecret"],
                    stsToken: data["SecurityToken"],
                    bucket: 'nfls-papers',
                    secure: true
                });
                this.loadFiles(client,"")
            })
        }, methods: {
            loadFiles(client,next){
                var self = this
                var result = client.list({
                    "max-keys": 1000,
                    "marker": next
                }).then(function (result) {
                    self.fileInfo = self.fileInfo.concat(result.objects)
                    if(result.objects.length == 1000 && false){
                        self.loadFiles(client,result.nextMarker)
                    }else{
                        console.log(self.fileInfo)
                    }
                }).catch(function (err) {
                    console.error(err);
                });
            }, getCurrentPath(){
                var reducer = (accumulator, currentValue) => accumulator + "/" + currentValue;
                var uri = this.path.reduce(reducer,"")
                if(uri.startsWith("/"))
                    uri = uri.slice(1)
                return uri
            }, enter(name) {
                this.path.push(name)
            }, updateItems(val){
                var self = this
                this.displayItems = val.filter(function(object){
                    if(object.name.endsWith("/")){
                        return object.name.split("/").length - 1 == self.path.length + 1 && object.name.startsWith(self.getCurrentPath())
                    }else{
                        return object.name.split("/").length == self.path.length + 1 && object.name.startsWith(self.getCurrentPath())
                    }
                }).map(function(object){
                    object.name = object.name.replace(self.getCurrentPath(),"").replace("/","")
                    return object
                })
                console.log(this.displayItems)
            }, back() {
                this.path.pop()
                console.log(this.path)
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