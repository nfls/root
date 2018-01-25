<template>
    <div class="md-layout md-gutter md-alignment-center">
        <div class="md-layout-item md-medium-size-33 md-small-size-50 md-xsmall-size-100 gallery-card" v-for="item in items" :key="item.id">
            <md-card @click.native="onclick(item.id)">
                <md-card-media-cover md-solid>
                    <md-card-media md-ratio="4:3">
                        <img :src=item.photos[0].hd alt="Skyscraper">
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
                this.items = response.data["data"]
                console.log(this.items)
            })
            this.$emit('input', "Gallery")
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
    }
</style>