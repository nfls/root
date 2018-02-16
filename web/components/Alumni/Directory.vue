<template>
    <div>
        <md-empty-state
                md-icon="new_releases"
                md-label="开发中"
                md-description="此功能正在开发中">
        </md-empty-state>
    </div>
</template>

<script>
    export default {
        name: "Directory",
        mounted: function (){
            this.$emit("changeTitle","在线校友录")
        }
    }
</script>

<style scoped>

</style>