<template>
    <div>
        <span class="md-headline">欢迎使用南外人v2.0 beta!</span>
        <span class="md-body-2">

        </span>
    </div>
</template>

<script>
    export default {
        name: "Dashboard",
        props: ["name"],
        mounted: function () {
            this.$emit('input', "Dashboard")
        }
    }
</script>

<style scoped>
</style>