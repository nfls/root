<i18n src="../../translation/frontend/School.json"></i18n>
<template>
    <md-empty-state
            md-icon="new_releases"
            :md-label="$t('developing')"
            :md-description="$t('developing-hint')">
    </md-empty-state>
</template>

<script>
    export default {
        name: "Vote",
        mounted: function () {
            this.$emit('changeTitle', this.$t("vote-title"))
        }
    }
</script>

<style scoped>
    md-empty-state {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>