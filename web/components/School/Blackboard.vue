<template>
    <div class="class">
        <div class="info" v-if="!empty">
            <md-card>
                <md-card-content style="align:left;">
                    <md-field>
                        <label>我要看这块黑板：</label>
                        <md-select v-model="currentClass" v-for="cla in claz" key="currentClass">
                            <md-option :value="cla.id" :key="cla.id">{{cla.title}}</md-option>
                        </md-select>
                    </md-field>

                </md-card-content>
            </md-card>
            <md-card>
                <md-card-header>
                    <div class="md-title">{{classInfo.title}}</div>
                </md-card-header>

                <md-card-content style="text-align:left;align:left;">
                    <vue-markdown>{{classInfo.announcement}}</vue-markdown>
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
    </div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'
    export default {
        name: "Blackboard",
        props: ["admin","verified",'loggedIn'],
        components: {
            VueMarkdown
        },
        data: () => ({
            currentClass: null,
            empty: true,
            claz: [],
            classInfo: null
        }),
        mounted: function (){
            this.$emit("changeTitle","Blackboard")
            this.currentClass = this.$route.params["id"]
            this.init()
        },
        methods: {
            init() {
                this.axios.get("/school/blackboard/list").then((response) => {
                    this.claz = response.data["data"]
                    this.list()
                })
            },
            list() {
                this.axios.get("/school/blackboard/detail?id="+this.currentClass).then((response) => {
                    this.empty = false
                    this.classInfo = response.data["data"]
                }).catch((error) => {
                    if(this.claz.length > 0)
                        this.currentClass = this.claz[0].id
                })
            }
        },
        watch: {
            currentClass: {
                handler: function(newVal){
                    this.$router.push("/school/blackboard/"+newVal)
                    this.list()
                }
            }
        }
    }
</script>

<style scoped>

</style>