<i18n src="../../translation/Media.json"></i18n>
<template>
    <div class="md-layout md-gutter md-alignment-center">
        <div class="md-layout-item md-xlarge-size-20 md-large-size-33 md-medium-size-50 md-small-size-100 md-xsmall-size-100 gallery-card" v-for="item in items" :key="item.id">
            <md-card @click.native="onclick(item.id)" v-if="item.cover !== null">
                <md-card-media-cover md-solid>
                    <md-card-media md-ratio="4:3">
                        <img :src="item.cover.src" alt="Skyscraper">
                    </md-card-media>
                    <md-card-area>
                        <md-card-header>
                            <span class="md-title">{{item.title}}</span>
                            <span class="md-subhead">{{item.description}}</span>
                        </md-card-header>
                    </md-card-area>
                </md-card-media-cover>
            </md-card>
        </div>
    </div>
</template>

<script>
    export default {
        name: "gallery",
        props: ["name"],
        data: () => ({
            items: [
            ]
        }),
        mounted: function () {
            this.axios.get("/media/gallery/list").then((response) => {
                var items = response.data["data"]
                this.items = items.filter(function(val){
                    if (val.cover !== null){
                        val.cover.src = "/storage/photos/hd/" + val.cover.src
                    }
                    return val
                })
                this.$emit('renderWebp')
            })
            this.$emit("changeTitle","Gallery")

        },
        methods: {
            onclick: function(id) {
                this.$router.push("/media/gallery/" + id);
            }
        }
    }
</script>

<style scoped>
    .gallery-card {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    img {
        width:100%;
        height:100%;
        object-fit: cover;
        overflow: hidden;
        margin:0px;
        padding:0px;
    }
</style>