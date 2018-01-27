<template>
    <div class>
        <md-card>
            <md-card-header>
                我的实名认证
            </md-card-header>
            <md-card-content>
                <md-list>
                    <md-list-item v-for="auth in history" :key="auth.id" @click="click(auth.id)">{{auth.id}}</md-list-item>
                </md-list>
            </md-card-content>
        </md-card>
    </div>
</template>

<script>
    export default {
        name: "Realname",
        data: () => ({
            history: []
        }),
        mounted: function() {
            this.axios.get("/alumni/info").then((response) => {
                this.history = response.data["data"]
            })
        },
        methods: {
            click(id) {
                this.$router.push("/alumni/auth/" + id);
            }
        }
    }
</script>

<style scoped>

</style>