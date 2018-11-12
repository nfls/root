<template>
    <div id="app">
        <router-view/>
    </div>
</template>

<script>
    export default {
        name: 'App'
    }
</script>

<style>
    #app {
        font-family: 'Noto Sans Japanese', 'Noto Sans SC', 'Roboto', 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
    }
</style>

<style lang="scss">
    @import "~vue-material/dist/theme/engine"; // Import the theme engine

    @include md-register-theme("default", (
    primary: green, // The primary color of your application
    accent: red // The accent or secondary color
    ));

    @import "~vue-material/dist/theme/all"; // Apply the theme
</style>
