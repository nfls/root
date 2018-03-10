<template>

</template>

<script>
    export default {
        name: "Jump",
        props: ["name"],
        mounted: function () {
            this.$router.push("/dashboard")
        }
    }
</script>

<style scoped>

</style>