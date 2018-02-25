<template>
    <div class="management">
        <span class="md-caption">警告：请不要将自己移出课堂之外！</span><br/>
        <md-list>
            <md-list-item v-for="student in classInfo.students" :key="student.id">
                <md-avatar>
                    <img :src="'/avatar/' + student.id + '.png'" alt="Avatar">
                </md-avatar>
                <div class="md-list-item-text">
                    <span v-html="student.htmlUsername"></span>
                </div>
                <md-button class="md-icon-button md-list-action" @click="remove(student.id)">
                    <md-icon>delete</md-icon>
                </md-button>
                <md-button class="md-icon-button md-list-action" @click="send(student.id)">
                    <md-icon>send</md-icon>
                </md-button>
            </md-list-item>
        </md-list>
    </div>
</template>

<script>
    export default {
        name: "Management"
    }
</script>

<style scoped>

</style>