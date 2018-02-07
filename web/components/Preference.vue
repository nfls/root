<template>
    <div>
        <div class="md-layout-item">
            <md-field>
                <label for="item">项目</label>
                <md-select v-model="item" name="item" id="item">
                    <md-option v-for="preference in info" :value="preference.identifier" :key="preference.identifier">{{preference.remark}}</md-option>
                </md-select>
            </md-field>
        </div>
        <div style="text-align: left;">
            <markdown-palettes v-model="content" v-if="type == 'markdown'"></markdown-palettes>
            <vue-json-editor v-model="content" :showBtns="true" v-if="type == 'json'"></vue-json-editor>
        </div>
    </div>

</template>

<script>
    import MarkdownPalettes from 'markdown-palettes'
    import vueJsonEditor from 'vue-json-editor'
    export default {
        components: {
            MarkdownPalettes,
            vueJsonEditor
        },
        name: "Preference",
        data: () => ({
            info: null,
            content: null,
            item: null,
            type: ""
        }),
        mounted: function() {
            this.axios.get("/admin/preference").then((response) => {
                this.info = response.data["data"]
            })
        },
        watch: {
            item: {
                handler(newVal) {
                    var item = this.info.filter(function(val){
                        return val.identifier === newVal
                    })
                    this.content = item[0].content
                    this.type = item[0].type
                    if(this.type == "json")
                        this.content = JSON.parse(this.content)
                    console.log(item)
                }
            }
        }
    }
</script>

<style scoped>

</style>