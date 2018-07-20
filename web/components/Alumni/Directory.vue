<i18n src="../../translation/frontend/Alumni.json"></i18n>
<template>
    <div>
        <md-card>
            <md-card-header>
                <div class="md-title">校友查询</div>
            </md-card-header>

            <md-card-content>
                <md-field>
                    <label>名称</label>
                    <md-input v-model="name"></md-input>
                </md-field>
            </md-card-content>

            <md-card-actions>
                <md-button class="md-raised md-primary" @click="search">检索</md-button>
            </md-card-actions>
        </md-card>
    </div>

</template>

<script>
    export default {
        name: "Directory",
        data: () => ({
            name: ""
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t('title-directory'))
            this.load()
        },
        methods: {
            load() {
            },
            search() {
                this.axios.post("alumni/directory/query", {
                    name: this.name
                }).then((response) => {

                })
            }
        }
    }
</script>

<style scoped>

</style>