<template>
    <div>
        <img class="preview-img" v-for="(item, index) in items" :src="item.msrc" @click="$preview.open(index, items)">
    </div>
</template>

<script>
    export default {
        name: "Album",
        data: () => ({
            items: []
        }),
        mounted: function () {
            this.axios.get("/media/gallery/detail",{
                params: {
                    id: this.$route.params["id"]
                }
            }).then((response) => {
                this.items = response.data["data"]["photos"]
                this.$emit('input', "Gallery - " + response.data["data"]["title"])
                console.log(this.items)
            })

        },
    }
</script>

<style scoped>
img {
    margin:5px;
    max-width:80px;
    min-width:250px;
    width:40%;
}
</style>