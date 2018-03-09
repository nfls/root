<i18n src="../../translation/frontend/Media.json"></i18n>
<template>
    <div>
        <md-drawer class="md-right" :md-active.sync="showSidepanel" v-if="showSidepanel">
            <md-toolbar class="md-transparent" md-elevation="0">
                <span class="md-title">{{ $t('comment') }}</span>
            </md-toolbar>
            <md-button class="md-icon-button" @click="writeComments()" v-if="verified">
                <md-icon>add</md-icon>
            </md-button>
            <span v-else>{{ $t('comment-not-realname') }}</span>
            <md-divider></md-divider>
            <div style="margin-top:20px">
                <md-card v-for="comment in comments" :key="comment.id">
                    <md-card-header>
                        <md-card-header-text>
                            <div class="md-subtitle">{{comment.postUser.username}} @ {{comment.time | moment("lll")}}
                            </div>
                        </md-card-header-text>
                    </md-card-header>
                    <md-card-content>
                        {{comment.content}}
                    </md-card-content>
                    <md-card-actions v-if="admin">
                        <md-button class="md-accent" @click="deleteComment(comment.id)">{{ $t('admin-delete') }}
                        </md-button>
                    </md-card-actions>
                </md-card>
            </div>

        </md-drawer>

        <md-dialog-prompt
                :md-active.sync="active"
                v-model="comment"
                :md-title="$t('write-comment')"
                :md-content="$t('write-comment-tips')"
                md-input-maxlength="128"
                :md-input-placeholder="$t('write-comment-placeholder')"
                :md-confirm-text="$t('submit')"
                :md-cancel-text="$t('cancel')"
                @md-confirm="submitComment()"
        />

        <md-card class="gist">
            <md-card-header>
                <div class="md-title">{{info.title}}</div>
                <div class="md-subtitle">{{info.time | moment("lll")}}</div>
            </md-card-header>

            <md-card-content>
                <span>{{info.description}}</span><br/>
                <span>{{ $t('total-count') }}{{info.photoCount}} / {{ $t('featured-count') }}{{info.originCount}}<br/></span>
                <span v-if="!verified">{{ $t('photo-not-realname') }}<br/></span>
                <span v-else-if="onlyOrigin">{{ $t('view-full-tip') }}<br/></span>
                <strong><span v-html="$t('license-prompt')"></span></strong><br/>
                <span v-if="!webpSupported"><strong>{{ $t('webp-not-supported-content') }}</strong><br/>

                </span>
                <md-divider></md-divider>
                <p align="left">
                    <span class="md-caption" v-if="info.likes.length > 0"><span v-for="like in info.likes"
                                                                                :key="like.username">&nbsp;{{ like.username }}</span> 赞了本相册。</span>
                </p>
            </md-card-content>
        </md-card>

        <div class="md-row" style="display:inline-block;margin:0px;min-width:90%">
            <figure v-for="(item, index) in items" class="photo">
                <img class="preview-img" v-lazy="item.msrc" @click="$preview.open(index, items, options)"
                     style="display:inline;">
                <figcaption v-if="showDebug">ID: {{item.id}}</figcaption>
            </figure>
        </div>

        <md-dialog-alert
                :md-active.sync="noFeature"
                :md-title="$t('no-featured')"
                :md-content="$t('no-featured-text')"
                :md-confirm-text="$t('confirm')"/>

        <md-dialog-alert
                :md-active.sync="showNotEnabled"
                :md-title="$t('webp-not-supported')"
                :md-content="$t('webp-not-supported-content')" />

        <md-speed-dial class="md-top-right back-button" md-direction="bottom" md-event="click">
            <md-speed-dial-target>
                <md-icon class="md-morph-initial">highlight</md-icon>
                <md-icon class="md-morph-final">close</md-icon>
            </md-speed-dial-target>

            <md-speed-dial-content>
                <md-button class="md-icon-button" @click="like()" v-if="loggedIn">
                    <md-tooltip v-if="isLiked">{{ $t('dislike') }}</md-tooltip>
                    <md-tooltip v-else>{{ $t('like') }}</md-tooltip>
                    <md-icon v-if="isLiked">thumb_down</md-icon>
                    <md-icon v-else>thumb_up</md-icon>
                </md-button>

                <md-button class="md-icon-button" @click="showComments()">
                    <md-tooltip>{{ $t('comment') }}</md-tooltip>
                    <md-icon>comment</md-icon>
                </md-button>

                <md-button class="md-icon-button" @click="switchPreference()">
                    <md-tooltip v-if="onlyOrigin">{{ $t('show-all') }}</md-tooltip>
                    <md-tooltip v-else>{{ $t('show-featured') }}</md-tooltip>
                    <md-icon v-if="onlyOrigin">launch</md-icon>
                    <md-icon v-else>featured_video</md-icon>
                </md-button>

                <md-button class="md-icon-button" @click="showDebug = !showDebug" v-if="admin">
                    <md-tooltip>{{ $t('debug') }}</md-tooltip>
                    <md-icon>report_problem</md-icon>
                </md-button>
            </md-speed-dial-content>
        </md-speed-dial>

    </div>
</template>

<script>
    export default {
        name: "Album",
        props: ["admin", "verified", 'loggedIn', 'webpSupported'],
        data: () => ({
            items: [],
            comments: [],
            options: {
                shareButtons: [
                    {id:'download', label:'Download', url:'{{raw_image_url}}', download:true}
                ]
            },
            info: {
                title: "",
                description: "",
                originCount: 0,
                photoCount: 0,
                isPublic: false,
                isVisible: false,
                time: null,
                likes: [],
            },
            showSidepanel: false,
            comment: null,
            active: false,
            isLiked: false,
            onlyOrigin: true,
            showDebug: false,
            csrf: null,
            noFeature: false,
            showNotEnabled: false,
        }),
        mounted: function () {
            this.loadData(true)
        },
        methods: {
            loadData(full) {
                this.$emit('changeTitle', this.$t('title-album'))
                if(!this.webpSupported){
                    this.showNotEnabled = true
                    return
                }

                this.getCsrf()
                this.axios.get("/media/gallery/detail", {
                    params: {
                        id: this.$route.params["id"]
                    }
                }).then((response) => {
                    if (full) {
                        var items = Object.values(response.data["data"]["photos"])
                        if (this.onlyOrigin) {
                            items = items.filter(item => item.osrc != null)
                            if (items.length === 0) {
                                this.noFeature = true
                                this.switchPreference()
                            }
                        }
                        items = items.map(function (val) {
                            val.msrc = "/storage/photos/thumb/" + val.msrc
                            val.src = "/storage/photos/hd/" + val.src
                            if (val.osrc != null) {
                                val.osrc = "/storage/photos/origin/" + val.osrc
                            } else {
                                val.osrc = null
                            }
                            return val
                        })
                        this.items = items.map(function (val) {
                            if (val.osrc != null) {
                                val.msrc = val.src
                                val.src = val.osrc
                            }
                            return val
                        })
                    }
                    this.comments = response.data["data"]["comments"]
                    this.$emit('changeTitle', this.$t('title-album') + " " + response.data["data"]["title"])
                    this.info = response.data["data"]
                })
                this.axios.get("/media/gallery/like", {
                    params: {
                        id: this.$route.params["id"]
                    }
                }).then((response) => {
                    this.isLiked = response.data.data
                    //console.log(this.isLiked)
                })
            },
            showComments: function () {
                this.showSidepanel = true
            },
            writeComments: function () {
                this.active = true
            },
            submitComment: function () {
                this.axios.post('/media/gallery/comment', {
                    id: this.$route.params["id"],
                    content: this.comment,
                    _csrf: this.csrf
                }).then((response) => {
                    this.loadData(false)
                    this.showSidepanel = true
                    if (response.data["code"] == 200) {
                        this.$emit("showMsg", this.$t("comment-succeeded"))
                    } else {
                        this.$emit("showMsg", response.data["data"])
                    }
                })
            },
            like: function () {
                this.axios.post('/media/gallery/like', {
                    id: this.$route.params["id"],
                    _csrf: this.csrf
                }).then((response) => {
                    if (this.isLiked) {
                        this.$emit("showMsg", this.$t("dislike-succeeded"))
                    } else {
                        this.$emit("showMsg", this.$t("like-succeeded"))
                    }
                    this.loadData(false)
                })
            },
            switchPreference: function () {
                this.onlyOrigin = !this.onlyOrigin
                if (this.onlyOrigin)
                    this.$emit("showMsg", this.$t("show-featured"))
                else
                    this.$emit("showMsg", this.$t("show-all"))
                this.loadData(true)
            },
            deleteComment: function (id) {
                this.axios.post("/admin/media/comment/edit", {
                    "delete": "[" + id + "]",
                    _csrf: this.csrf
                }).then((response) => {
                    this.loadData(false)
                })
            }, getCsrf() {
                this.axios.get("user/csrf", {
                    params: {
                        name: "media.gallery"
                    }
                }).then((response) => {
                    this.csrf = response.data["data"]
                })
            }
        },
        watch: {
            webpSupported: {
                handler: function (val) {
                    this.showNotEnabled = !val
                    this.loadData(true)
                }
            }
        }
    }
</script>

<style scoped>
    .photo {
        display: inline-block;
        float: none;
        vertical-align: top;
        max-width: 80px;
        min-width: 250px;
        width: 40%;
        margin: 0px;
    }

    .avatar {
        max-width: 100%;
        height: auto;
        padding: 10px;
    }

    .back-button {
        margin-top: 60px;
    }

    .gist {
        margin: 15px;
    }
</style>