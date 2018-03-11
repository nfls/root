<i18n src="../../translation/frontend/Admin.json"></i18n>
<template>
    <div id="upload" align="left">
        <span>{{ $t('target-server') }} hk2.nfls.io</span><br/>
        <span><strong>{{ $t('upload-prompt') }}</strong></span><br/>
        <span>{{ $t('upload-warning') }}</span><br/>
        <span>{{ $t('upload-markdown') }}</span>
        <form>
            <md-progress-bar md-mode="indeterminate" v-if="loading"/>
            <md-field>
                <label>{{ $t('file') }}</label>
                <md-file @md-change="change"/>
                <md-button @click="upload" :disabled="loading">{{ $t('submit') }}</md-button><br/>
            </md-field>
        </form>

        <span>{{ $t('address')}}<code>{{url}}</code></span>
    </div>
</template>

<script>
    export default {
        name: "Upload",
        data: () => ({
            file: null,
            url: "",
            loading: false
        }),
        mounted: function() {
            this.$emit("changeTitle", this.$t('upload-title'))
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
                    this.url = this.$t('upload-failed')
                    this.loading = false
                })
            }
        }
    }
</script>

<style scoped>
</style>