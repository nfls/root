<template>
    <div class="class">
        <md-card>
            <md-card-header>
                <div class="md-title">E的魔都</div>
            </md-card-header>

            <md-card-content>
                This is edomodo(chinese: E的魔都) for all math students.
            </md-card-content>
        </md-card>
        <md-card>
            <md-card-header>
                <div class="md-title">Assignment #2</div>
            </md-card-header>

            <md-card-content>
                Finish the revision shit.
            </md-card-content>
        </md-card>
    </div>
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