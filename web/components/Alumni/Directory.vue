<i18n src="../../translation/frontend/Alumni.json"></i18n>
<template>
    <div>
        <md-card>
            <md-card-header>
                <div class="md-title">校友查询</div>
            </md-card-header>

            <md-card-content>
                <md-field>
                    <label>姓名</label>
                    <md-input v-model="name"></md-input>
                </md-field>
                <md-field>
                    <label>初中学校</label>
                    <md-select v-model="juniorSchool" name="movies" id="movies" multiple>
                        <md-option value="fight-club">Fight Club</md-option>
                        <md-option value="godfather">Godfather</md-option>
                        <md-option value="godfather-ii">Godfather II</md-option>
                        <md-option value="godfather-iii">Godfather III</md-option>
                        <md-option value="godfellas">Godfellas</md-option>
                        <md-option value="pulp-fiction">Pulp Fiction</md-option>
                        <md-option value="scarface">Scarface</md-option>
                    </md-select>
                </md-field>
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
            juniorSchool: ""
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t('title-directory'))
        }
    }
</script>

<style scoped>

</style>