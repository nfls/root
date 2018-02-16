<template>
    <md-empty-state
            md-icon="new_releases"
            md-label="开发中"
            md-description="此功能正在开发中">
    </md-empty-state>
</template>

<script>
    export default {
        name: "Vote",
        mounted: function () {
            this.$emit('changeTitle', "投票")
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