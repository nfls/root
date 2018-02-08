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
            <vue-json-editor v-model="content" v-if="type == 'json'"></vue-json-editor>
        </div>
        <md-button class="md-raised md-primary" @click="save">保存</md-button>
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
            identifier: null,
            content: null,
            item: null,
            type: ""
        }),
        methods: {
            save() {
                this.axios.post("/admin/preference",{
                    identifier: this.identifier,
                    content: this.content
                }).then((response) => {
                    this.update()
                })
            }, update() {
                this.axios.get("/admin/preference").then((response) => {
                    this.info = response.data["data"]
                })
            }
        },
        mounted: function() {
            this.update()
        },
        watch: {
            item: {
                handler(newVal) {
                    var item = this.info.filter(function(val){
                        return val.identifier === newVal
                    })
                    this.content = item[0].content
                    this.type = item[0].type
                    this.identifier = item[0].identifier
                    if(this.type == "json")
                        this.content = JSON.parse(this.content)
                }
            }
        }
    }
</script>

<style scoped>

</style>