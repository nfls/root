<template>

</template>

<script>
    export default {
        name: "Jump",
        props: ["name"],
        mounted: function () {
            this.$router.push("/")
        }
    }
</script>

<style scoped>

</style>