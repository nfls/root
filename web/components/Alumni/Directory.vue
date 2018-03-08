<i18n src="../../translation/frontend/Alumni.json"></i18n>
<template>
    <div>
        <md-empty-state
                md-icon="new_releases"
                :md-label="$t('developing')"
                :md-description="$t('developing-hint')">
        </md-empty-state>
    </div>
</template>

<script>
    export default {
        name: "Directory",
        mounted: function () {
            this.$emit("changeTitle", this.$t('title-directory'))
        }
    }
</script>

<style scoped>

</style>