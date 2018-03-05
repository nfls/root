<i18n src="../../translation/frontend/School.json"></i18n>
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
            this.$emit("changeTitle", "在线校友录")
        }
    }
</script>

<style scoped>

</style>