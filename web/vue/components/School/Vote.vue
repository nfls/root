<template>
    <md-empty-state
            md-rounded
            md-icon="access_time"
            md-label="无投票"
            md-description="目前没有可以投票的项目">
    </md-empty-state>
</template>

<script>
    export default {
        name: "Vote",
        mounted: function () {
            this.$emit('input', "投票")
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