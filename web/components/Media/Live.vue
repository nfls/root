<template>
    <video-player
            class="video-player-box"
            ref="videoPlayer"
            :options="options"
            @ready="play"
    >
    </video-player>
</template>

<script>
    export default {
        name: "Live",
        mounted() {
            this.$emit("changeTitle", "直播")
            this.options = {
                muted: false,
                autoplay: true,
                fluid: true,
                sources: [{
                    type: "application/x-mpegURL",
                    src: "https://live.nfls.io/" + this.$route.params["id"] + ".m3u8"
                }],
                poster: "/static/images/author.jpg",
            }
        },
        data: () => ({
            options: {}
        }),
        methods: {
            play() {
                this.$refs.videoPlayer.player.play()
            }

        }
    }
</script>

<style scoped>

</style>