<template>
    <div>
            <md-card class="md-primary" v-for="dev in devs" :key="dev.title" :md-theme="dev.group">
                <md-card-media>
                    <img :src="dev.image" alt="People">
                </md-card-media>

                <md-card-header>
                    <div class="md-title">{{dev.title}}</div>
                    <div class="md-subtitle">{{dev.subtitle}}</div>
                </md-card-header>
            </md-card>

    </div>
</template>

<script>
    export default {
        name: "Team",
        data: () => ({
            devs: null
        }),
        mounted: function() {
            this.$emit("changeTitle","Developers")
            this.axios.get("/about/devs").then((response) => {
                this.devs = response.data["data"].reduce(function(previous,current){
                    console.log(current.people)
                    return previous.concat(current.people.map(function(val){
                        val.group = current.group
                        return val
                    }))
                },[])
            })
        }
    }
</script>

<style scoped lang="scss">

    @import "~vue-material/dist/theme/engine";

    @include md-register-theme("ui", (
            primary: #DD9CDF,
    ));

    @include md-register-theme("photo", (
            primary: #3CDBC0
    ));

    @include md-register-theme("game", (
            primary: #2DCCD3
    ));

    @include md-register-theme("investor", (
            primary: #00C389
    ));

    @include md-register-theme("leader", (
            primary: #71C5E8
    ));

    @include md-register-theme("pink-card", (
            primary: #F57EB6
    ));

    @import "~vue-material/dist/base/theme";
    @import "~vue-material/dist/components/MdCard/theme";

    .md-card {
        max-width: 200px;
        display: inline-block;
        margin-top: 10px;
        margin-bottom: 10px;
    }

</style>