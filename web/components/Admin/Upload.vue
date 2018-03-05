<template>
    <div id="upload" align="left">
        <span>目标服务器是 hk2.nfls.io</span><br/>
        <span><strong>仅供图片、游戏类资源使用。目前没有进度条，上传时请耐心等待</strong></span><br/>
        <span>注意：此处上传的文件访问时不需要鉴权。您的文件可以被任何拥有链接的人访问及下载。此上传空间有定期垃圾清理，未在网页上引用的将会在一定时间后被删除。</span>
        <form>
            <md-field>
               <label>文件</label>
                <md-file @md-change="change"/>
            </md-field>
        </form>
        <md-button @click="upload" :disabled="loading">提交</md-button><br/>
        <span>{{url}}</span>
    </div>
</template>

<script>
    export default {
        name: "Upload",
        data: () => ({
            file: null,
            url: "",
            loading:false
        }),
        mounted: function() {
            this.$emit("changeTitle", "资源上传")
        },
        methods: {
            change(file){
                this.file = file
            },
            upload(){
                if(!this.file)
                    return
                let formData = new FormData();
                formData.append("file",this.file[0],this.file[0].name)
                this.loading = true
                this.axios.post("/admin/upload",formData).then((response) => {
                    this.url = response.data["data"]
                    this.file = null
                    this.loading = false
                }).catch((error) => {
                    this.url = "上传失败"
                    this.loading = false
                })
            }
        }
    }
</script>

<style scoped>

</style>