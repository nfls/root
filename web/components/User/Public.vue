<i18n src="../../translation/frontend/Alumni.json"></i18n>
<template>
    <div>
        <div v-if="visible">
            <span class="md-title">{{info.username}} 的个人页面</span>
            <br/>
            <div v-if="alumni == null">
                <span class="md-caption">该用户尚未通过实名认证。</span>
            </div>
            <div v-else>
                <md-list class="md-douple-line">
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">ID</span>
                            <span>{{info.id}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">用户名</span>
                            <span>{{info.username}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">中文名</span>
                            <span>{{alumni.chineseName}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">英文名</span>
                            <span>{{alumni.englishName}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">性别</span>
                            <span>{{alumni.gender}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">生日</span>
                            <span>{{alumni.birthday}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">初中信息</span>
                            <span>{{alumni.juniorSchool}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">高中信息</span>
                            <span>{{alumni.seniorSchool}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <md-list-item>
                        <div class="md-list-item-text">
                            <span class="md-caption">常住国家或地区</span>
                            <span>{{alumni.country}}</span>
                        </div>
                    </md-list-item>
                    <md-divider></md-divider>
                    <div v-if="alumni.university">
                        <md-list-item>
                            <div class="md-list-item-text">
                                <span class="md-caption">常住地</span>
                                <span>{{alumni.location}}</span>
                            </div>
                            <md-tooltip md-direction="bottom">{{ alumni.location }}</md-tooltip>
                        </md-list-item>
                        <md-divider></md-divider>
                        <md-list-item>
                            <div class="md-list-item-text">
                                <span class="md-caption">联系方式</span>
                                <span>{{alumni.onlineContact}}</span>
                            </div>
                        </md-list-item>
                        <md-divider></md-divider>
                    </div>

                </md-list>
                <div v-if="alumni.university">
                    <md-field>
                        <label>大学</label>
                        <md-textarea v-model="alumni.university" disabled></md-textarea>
                    </md-field>
                    <md-field>
                        <label>专业</label>
                        <md-textarea v-model="alumni.major" disabled></md-textarea>
                    </md-field>
                    <md-field>
                        <label>工作</label>
                        <md-textarea v-model="alumni.workInfo" disabled></md-textarea>
                    </md-field>
                </div>
                <md-field>
                    <label>个人简介</label>
                    <md-textarea v-model="alumni.personalInfo" disabled></md-textarea>
                </md-field>
                <md-speed-dial class="md-bottom-right" md-direction="top">
                    <md-speed-dial-target class="md-primary" @click="visit">
                        <md-icon>send</md-icon>
                    </md-speed-dial-target>
                </md-speed-dial>
            </div>
        </div>
        <div v-else>
            <span class="md-caption">该用户启用了反爬虫保护。</span>
        </div>
    </div>

</template>

<script>
    export default {
        name: "Public",
        data: () => ({
            visible: false,
            alumni: {},
            info: {},
            countries:[]
        }),
        mounted() {
            this.request()
        },
        methods: {
            request() {
                this.axios.get("/alumni/countries").then((response) => {
                    this.countries = response.data["data"]
                })
                this.axios.get("/user/page",{
                    params: {
                        id: this.$route.params["id"]
                    }
                }).then((response) => {
                    if(response.data["code"] === 200){
                        this.visible = true
                        this.alumni = response.data["data"]["alumni"]
                        this.info = response.data["data"]["info"]
                        if(this.alumni){
                            this.alumni.juniorSchool = this.getJuniorSchool(this.alumni.juniorSchool, this.alumni.juniorRegistration, this.alumni.juniorClass)
                            this.alumni.seniorSchool = this.getSeniorSchool(this.alumni.seniorSchool, this.alumni.seniorRegistration, this.alumni.seniorClass)
                            this.alumni.gender = this.getGender(this.alumni.gender)
                            this.alumni.country = this.countries.filter((object)=>{
                                return object.code === this.alumni.country
                            })[0].name
                            this.alumni.birthday = this.$moment(this.alumni.birthday).format("LL")
                        }
                    } else {
                        this.visible = false
                    }
                })
            },
            visit() {
                this.$router.push("/user/message/" + this.info.id)
            },
            getSeniorSchool(school, registration, cla) {
                return this.$t("form-seniorSchool-" + school) + " " + registration + "届 " + cla + "班"
            },
            getJuniorSchool(school, registration, cla) {
                return this.$t("form-juniorSchool-" + school) + " " + registration + "届 " + cla + "班"
            },
            getGender(gender) {
                return this.$t('form-gender-'+gender)
            }
        }
    }
</script>

<style scoped>

</style>