<i18n src="../../translation/Media.json"></i18n>
<template>
    <div class="md-layout md-gutter md-alignment-center" v-infinite-scroll="loadMore"
         :infinite-scroll-disabled="loading">
        <div class="md-layout-item md-xlarge-size-20 md-large-size-33 md-medium-size-50 md-small-size-100 md-xsmall-size-100 gallery-card"
             v-for="item in items" :key="item.id">
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
    import infiniteScroll from 'vue-infinite-scroll'

    export default {
        name: "gallery",
        props: ["name"],
        directives: {infiniteScroll},
        data: () => ({
            items: [],
            page: 1,
            loading: false
        }),
        mounted: function () {

            this.$emit("changeTitle", "Gallery")

        },
        methods: {
            onclick(id) {
                this.$router.push("/media/gallery/" + id);
            },
            loadMore() {
                if (this.loading)
                    return
                this.loading = true
                this.axios.get("/media/gallery/list", {
                    params: {
                        page: this.page
                    }
                }).then((response) => {
                    var items = response.data["data"]
                    this.items = this.items.concat(items.filter(function (val) {
                        if (val.cover !== null) {
                            val.cover.src = "/storage/photos/hd/" + val.cover.src
                        }
                        return val
                    }))
                    this.loading = false
                    this.page++
                    this.$emit('renderWebp')
                })
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
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        overflow: hidden;
        margin: 0px;
        padding: 0px;
    }
</style>