<template>
    <div align="left">
        <md-tabs>
            <md-tab id="tab-inbox" md-label="收件箱">
                <md-card>
                    <md-card-content>
                        <vue-markdown>### A Message</vue-markdown>
                        <md-divider></md-divider>
                        <span class="md-caption">aaaa</span>
                    </md-card-content>
                </md-card>
            </md-tab>
            <md-tab id="tab-outbox" md-label="发件箱">

            </md-tab>
            <md-tab id="tab-new" md-label="撰写信息">
                <markdown-palettes v-model="content"></markdown-palettes>
            </md-tab>
        </md-tabs>
    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'
    import MarkdownPalettes from 'markdown-palettes'
    export default {
        name: "Message",
        components: {
            VueMarkdown,
            MarkdownPalettes
        },
        data: () => ({
            content: "",
            inbox: [],
            outbox: []
        }),
        methods: {
            
        }
    }
</script>

<style scoped>
    .md-card{
        margin:10px;
    }
</style>