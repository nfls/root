<template>
    
</template>

<script>
    export default {
        name: "Blackboard",
        props: ["admin","verified",'loggedIn'],
        mounted: function (){
            this.$emit("changeTitle","Blackboard")
        },
        methods: {
            list() {
                if(this.$route.params["id"]){
                    this.axios.get("/school/blackboard/detail?id="+this.$route.params["id"])
                }
            }
        }
    }
</script>

<style scoped>

</style>