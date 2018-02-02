<template>
    
</template>

<script>
    export default {
        name: "Security",
        props: ["isAdmin","isLoggedIn","isVerified"],
        mounted: function () {
            this.$emit("changeTitle","Security")
        }
    }
</script>

<style scoped>

</style>