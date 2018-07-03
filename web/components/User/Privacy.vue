<i18n src="../../translation/frontend/User.json"></i18n>
<template>
    <div align="left">
        <md-progress-bar md-mode="indeterminate" v-if="sending"/>
        <span class="md-title">Privacy Settings</span>
        <p>您可以在此处修改您的隐私设置。请注意，您必须对所有</p>
        <md-field>
            <label>初高中班级信息</label>
            <md-select v-model="movie" name="movie" id="movie">
                <md-option v-for="(level, index) in privacyLevel" :key="index" :value="index">{{ level }}</md-option>
            </md-select>
        </md-field>
        <md-field>
            <label>大学信息</label>
            <md-select v-model="movie" name="movie" id="movie">
                <md-option v-for="(level, index) in privacyLevel" :key="index" :value="index">{{ level }}</md-option>
            </md-select>
        </md-field>
        <md-field>
            <label>工作信息</label>
            <md-select v-model="movie" name="movie" id="movie">
                <md-option v-for="(level, index) in privacyLevel" :key="index" :value="index">{{ level }}</md-option>
            </md-select>
        </md-field>
        <md-field>
            <label>常住地</label>
            <md-select v-model="movie" name="movie" id="movie">
                <md-option v-for="(level, index) in privacyLevel" :key="index" :value="index">{{ level }}</md-option>
            </md-select>
        </md-field>
        <md-field>
            <label>个人简介</label>
            <md-select v-model="movie" name="movie" id="movie">
                <md-option v-for="(level, index) in privacyLevel" :key="index" :value="index">{{ level }}</md-option>
            </md-select>
        </md-field>
    </div>
</template>

<script>
    export default {
        data: () => ({
            sending: true,
            privacyLevel: {
                0: "所有人",
                1: "仅同校同学（所有已实名用户）",
                2: "仅同届同学",
                3: "仅同班同学",
                4: "仅自己"
            },
            movie: ""
        })
    }
</script>