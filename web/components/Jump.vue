<template>
    
</template>

<script>
    export default {
        name: "Jump",
        props: ["name"],
        mounted: function () {
            this.$router.push("/user/dashboard")
            this.$emit('input', "Unknown Field")
        }
    }
</script>

<style scoped>

</style>