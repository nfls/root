<i18n src="../../translation/frontend/Media.json"></i18n>
<template>
    <div id="gallery">
        <div id="searchBox" align="left">
            <md-switch v-model="originOnly">{{ $t('only-featured') }}</md-switch><br/>
            <md-divider></md-divider>
            <span class="md-body-1" v-if="!originOnly">{{ $t('star-represent') }}<br/></span>
            <span class="md-body-1" v-html="$t('license-prompt')"></span><br/>
            <md-divider></md-divider>
            <span class="md-caption">{{ $t('rank-prompt') }}<br/></span>
        </div>

        <md-dialog-alert
                :md-active.sync="showNotEnabled"
                :md-title="$t('webp-not-supported')"
                :md-content="$t('webp-not-supported-content')" />

        <div class="md-layout md-gutter md-alignment-center">
            <div class="md-layout-item md-xlarge-size-20 md-large-size-33 md-medium-size-50 md-small-size-100 md-xsmall-size-100 gallery-card"
                 v-for="item in items" :key="item.id" v-if="item.cover !== null && (!originOnly || item.originCount > 0)">
                <md-card @click.native="onclick(item.id)">
                    <md-card-media-cover md-solid>
                        <md-card-media md-ratio="4:3">
                            <img v-lazy="item.cover.src" alt="Skyscraper">
                        </md-card-media>
                        <md-card-area>
                            <md-card-header>
                                <span class="md-title"><span v-if="item.originCount > 0 && !originOnly">⭐</span>️️{{item.title}}</span>
                                <span class="md-subhead">{{item.description}}</span>
                            </md-card-header>
                        </md-card-area>
                    </md-card-media-cover>
                </md-card>
            </div>
        </div>

        <div v-if="!webpSupported">
            <span class="md-headline"><br/><br/>{{ $t('webp-not-supported-content') }}</span>
        </div>

        <md-snackbar md-position="center" :md-duration="Infinity" :md-active.sync="showLicense" md-persistent>
            <span v-html="">{{ $t('license-short') }}</span>
            <md-button class="md-accent" @click="showLicense = false">{{ $t('known') }}</md-button>
        </md-snackbar>

    </div>

</template>

<script>
    import infiniteScroll from 'vue-infinite-scroll'

    export default {
        name: "gallery",
        props: ["name","webpSupported"],
        directives: {infiniteScroll},
        data: () => ({
            items: [],
            originOnly: false,
            showNotEnabled: false,
            showLicense: true
        }),
        mounted: function () {
            this.$emit("changeTitle", this.$t('title-gallery'))
            this.loadMore()
        },
        methods: {
            onclick(id) {
                this.$router.push("/media/gallery/" + id)
            },
            loadMore() {
                if(!this.webpSupported){
                    this.showNotEnabled = true
                    return
                }
                this.axios.get("/media/gallery/list").then((response) => {
                    var items = response.data["data"]
                    this.items = this.items.concat(items.filter(function (val) {
                        if (val.cover !== null) {
                            val.cover.src = "/storage/photos/hd/" + val.cover.src
                        }
                        return val
                    }))
                })
            }
        },
        watch: {
            webpSupported: {
                handler: function (val) {
                    this.showNotEnabled = !val
                    this.loadMore()
                }
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