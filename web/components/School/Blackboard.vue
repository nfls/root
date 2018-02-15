<template>
    
</template>

<script>
    export default {
        name: "Blackboard",
        props: ["admin","verified",'loggedIn'],
        mounted: function (){
            this.$emit("changeTitle","Blackboard")
        }
    }
</script>

<style scoped>

</style>