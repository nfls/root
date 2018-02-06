<template>
    <div style="text-align:left;">
    </div>
</template>

<script>
    export default {
        name: "Dashboard",
        props: ["isAdmin","isLoggedIn","isVerified"],
        mounted: function () {
            this.$emit("changeTitle","Dashboard")
            this.axios.get("https://wiki.nfls.io/api.php?action=query&format=json&list=recentchanges&generator=categorymembers&rcshow=!bot&rclimit=50&gcmtitle=Category%3A*&gcmlimit=100").then((response) => {
                
            })
        }
    }
</script>

<style scoped>
</style>