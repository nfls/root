<template>
    <div style="text-align:left;">
        <span class="md-display-1">欢迎使用南外人v2.0 beta!</span><br/><br/>
        <span class="md-body-1">
            目前只有往卷和实名认证可以使用。
            推荐用Chrome<br/>
            代码都是人写的，有BUG是正常现象，刷新往往解决问题<br/>
            Human makes mistakes, and I am 100% of human. -- Ms. E <br/>
            右上角可以登录或者注册<br/>
            iOS将会在寒假后开始弄<br/>
            <strong>如果还是有问题，麻烦去找AS1刘欣然</strong><br/>
        </span>
        <br/>
        <span class="md-body-2">
            以上来自正在敲代码的hqy。寒假期间请勿来咨询相关问题。望周知。
        </span>
    </div>
</template>

<script>
    export default {
        name: "Dashboard",
        props: ["isAdmin","isLoggedIn","isVerified"],
        mounted: function () {
            this.$emit("changeTitle","Dashboard")
        }
    }
</script>

<style scoped>
</style>