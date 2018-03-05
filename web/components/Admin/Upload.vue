<template>
    <div id="upload" align="left">
        <span>Target server is hk2.nfls.io</span>
        <form>
            <md-field>
               <label>文件</label>
                <md-file @md-change="change"/>
            </md-field>
        </form>
        <md-button @click="upload">提交</md-button><br/>
        <span>{{url}}</span>
    </div>
</template>

<script>
    export default {
        name: "Upload",
        data: () => ({
            file: null,
            url: ""
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
                this.axios.post("/admin/upload",formData).then((response) => {
                    this.url = response.data["data"]
                })
            }
        }
    }
</script>

<style scoped>

</style>