<i18n src="../../translation/frontend/Alumni.json"></i18n>
<template>
    <div>
        <md-card>
            <md-card-header>
                <div class="md-title">校友查询</div>
            </md-card-header>

            <md-card-content>
                <div v-for="item in formItems" :key="item.key">
                    <md-field v-if="item.search === 'fuzzy' || item.search === 'precise'">
                        <label>{{ $t('form-' + item.key) }}</label>
                        <md-input :name="item.key" :id="item.key" :autocomplete="item.key"
                                  v-model="form[item.key]"></md-input>
                    </md-field>
                    <md-field v-if="item.search === 'multi'">
                    <label>{{ $t('form-' + item.key) }}</label>
                        <md-select :name="item.key" :id="item.key" :autocomplete="item.key"
                                   v-model="form[item.key]" multiple>
                            <md-option value="fight-club">Fight Club</md-option>
                            <md-option value="godfather">Godfather</md-option>
                            <md-option value="godfather-ii">Godfather II</md-option>
                            <md-option value="godfather-iii">Godfather III</md-option>
                            <md-option value="godfellas">Godfellas</md-option>
                            <md-option value="pulp-fiction">Pulp Fiction</md-option>
                            <md-option value="scarface">Scarface</md-option>
                        </md-select>
                    </md-field>
                </div>

            </md-card-content>

            <md-card-actions>
                <md-button>检索</md-button>
            </md-card-actions>
        </md-card>
    </div>

</template>

<script>
    export default {
        name: "Directory",
        data: () => ({
            name: "",
            juniorSchool: "",
            formItems: [],
            form: {}
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t('title-directory'))
            this.load()
        },
        methods: {
            load() {
                this.axios.get("/alumni/form").then((response) => {
                    this.formItems = response.data["data"].filter((object) => {
                        return object.search
                    })
                    console.log(this.formItems)
                })
            }
        }
    }
</script>

<style scoped>

</style>