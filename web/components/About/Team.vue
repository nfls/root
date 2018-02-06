<template>
    <div>
        <md-card class="md-primary" md-theme="purple-card">
            <md-card-media>
                <img src="/storage/yyf.png" alt="People">
            </md-card-media>

            <md-card-header>
                <div class="md-title">杨奕非<md-tooltip md-direction="bottom">2019届IB2班</md-tooltip></div>
                <div class="md-subtitle">特约摄影记者</div>
            </md-card-header>
        </md-card>
        <md-card class="md-primary" md-theme="purple-card">
            <md-card-media>
                <img src="/storage/yxy.png" alt="People">
            </md-card-media>

            <md-card-header>
                <div class="md-title">姚馨瑜<md-tooltip md-direction="bottom">2019届IB2班</md-tooltip></div>
                <div class="md-subtitle">特约摄影记者</div>
            </md-card-header>
        </md-card>

        <md-card class="md-primary" md-theme="mint-green-card">
            <md-card-media>
                <img src="/storage/hzk.png" alt="People">
            </md-card-media>
            <md-card-header>
                <div class="md-title">侯子康</div>
                <div class="md-subtitle">配音演员</div>
            </md-card-header>
        </md-card>
        <md-card class="md-primary" md-theme="mint-green-card">
            <md-card-media>
                <img src="/storage/xzd.png" alt="People">
            </md-card-media>
            <md-card-header>
                <div class="md-title">谢祖地</div>
                <div class="md-subtitle">首席游戏开发师</div>
            </md-card-header>
        </md-card>
        <md-card class="md-primary" md-theme="blue-green-card">
            <md-card-media>
                <img src="/storage/lyc.png" alt="People">
            </md-card-media>

            <md-card-header>
                <div class="md-title">李逸宸</div>
                <div class="md-subtitle">哲♂学思维指导</div>
            </md-card-header>
        </md-card>
        <md-card class="md-primary" md-theme="grass-card">
            <md-card-media>
                <img src="/storage/gpd.png" alt="People">
            </md-card-media>

            <md-card-header>
                <div class="md-title">顾平德</div>
                <div class="md-subtitle">官方二手手机供货商</div>
            </md-card-header>
        </md-card>
        <md-card class="md-primary" md-theme="grass-card">
            <md-card-media>
                <img src="/storage/lxr.png" alt="People">
            </md-card-media>

            <md-card-header>
                <div class="md-title">刘欣然</div>
                <div class="md-subtitle">（还没想好写啥）</div>
            </md-card-header>
        </md-card>
        <md-card class="md-primary" md-theme="fog-card">
            <md-card-media>
                <img src="/storage/lrk.png" alt="People">
            </md-card-media>
            <md-card-header>
                <div class="md-title">刘瑞楷</div>
                <div class="md-subtitle">另外一个投资方</div>
            </md-card-header>
        </md-card>
    </div>
</template>

<script>
    export default {
        name: "Team",
        mounted: function() {
            this.$emit("changeTitle","Developers")
        }
    }
</script>

<style scoped lang="scss">

    @import "~vue-material/dist/theme/engine";

    @include md-register-theme("purple-card", (
            primary: #DD9CDF,
    ));

    @include md-register-theme("mint-green-card", (
            primary: #3CDBC0
    ));

    @include md-register-theme("blue-green-card", (
            primary: #2DCCD3
    ));

    @include md-register-theme("grass-card", (
            primary: #00C389
    ));

    @include md-register-theme("fog-card", (
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