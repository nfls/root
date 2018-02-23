<i18n src="../../translation/Media.json"></i18n>
<template>
    <div>
        <md-drawer class="md-right" :md-active.sync="showSidepanel" v-if="showSidepanel" >
            <md-toolbar class="md-transparent" md-elevation="0">
                <span class="md-title">Comments</span>
            </md-toolbar>
            <md-button class="md-icon-button" @click="writeComments()" v-if="verified">
                <md-icon>add</md-icon>
            </md-button>
            <span v-else>只有实名认证后的账户才能发表评论！</span>
            <md-divider></md-divider>
            <div style="margin-top:20px">
                <md-card v-for="comment in comments" :key="comment.id">
                    <md-card-header>
                        <md-card-header-text>
                            <div class="md-subtitle">{{comment.postUser.username}} @ {{comment.time | moment("lll")}}</div>
                        </md-card-header-text>
                    </md-card-header>
                    <md-card-content>
                        {{comment.content}}
                    </md-card-content>
                    <md-card-actions v-if="admin">
                        <md-button class="md-accent" @click="deleteComment(comment.id)">Delete</md-button>
                    </md-card-actions>
                </md-card>
            </div>

        </md-drawer>

        <md-dialog-prompt
                :md-active.sync="active"
                v-model="comment"
                md-title="Write a comment"
                md-content="Please abide by the relevant policies and regulations of the Internet in China. We strictly prohibit any pornography, violence or reactionary information."
                md-input-maxlength="128"
                md-input-placeholder="Your thoughts on these photos"
                md-confirm-text="Submit"
                @md-confirm="submitComment()"
        />
        <md-card class="gist">
            <md-card-header>
                <div class="md-title">{{info.title}}</div>
                <div class="md-subtitle">{{info.time | moment("lll")}}</div>
            </md-card-header>

            <md-card-content>
                <span>{{info.description}}</span><br/>
                <span>Total count: {{info.photoCount}} / Featured count: {{info.originCount}}</span><br/>
                <span v-if="!verified">实名认证后即可查看更多照片！</span>
                <span v-else>{{ $t('view-full-tip') }}</span>
                <br/>
                <span v-if="!webpSupported"><strong>您的浏览器不支持WebP格式图片的渲染。</strong>正在使用备用渲染方案，可能会无法正常显示，仅前五张将会被显示。</span>
            </md-card-content>
        </md-card>
        <md-progress-spinner md-mode="indeterminate" v-if="!loaded"></md-progress-spinner>
        <div class="md-row" style="display:inline-block;margin:0px;min-width:90%" v-if="loaded">
            <figure v-for="(item, index) in items" class="photo">
                <img class="preview-img"  :src="item.msrc" @click="$preview.open(index, items, options)" style="display:inline;">
                <figcaption v-if="showDebug">ID: {{item.id}}</figcaption>
            </figure>
        </div>


        <md-speed-dial class="md-top-right back-button" md-direction="bottom" md-event="click">
            <md-speed-dial-target>
                <md-icon class="md-morph-initial">highlight</md-icon>
                <md-icon class="md-morph-final">close</md-icon>
            </md-speed-dial-target>

            <md-speed-dial-content>
                <md-button class="md-icon-button" @click="like()" v-if="loggedIn">
                    <md-tooltip v-if="isLiked">Dislike</md-tooltip>
                    <md-tooltip v-else>Like</md-tooltip>
                    <md-icon v-if="isLiked">thumb_down</md-icon>
                    <md-icon v-else>thumb_up</md-icon>
                </md-button>

                <md-button class="md-icon-button" @click="showComments()">
                    <md-tooltip>Comment</md-tooltip>
                    <md-icon>comment</md-icon>
                </md-button>

                <md-button class="md-icon-button" @click="switchPreference()">
                    <md-tooltip v-if="onlyOrigin">Show all the photos</md-tooltip>
                    <md-tooltip v-else>Show only featured photos</md-tooltip>
                    <md-icon v-if="onlyOrigin">launch</md-icon>
                    <md-icon v-else>featured_video</md-icon>
                </md-button>

                <md-button class="md-icon-button" @click="showDebug = !showDebug" v-if="admin">
                    <md-tooltip>Debug</md-tooltip>
                    <md-icon>report_problem</md-icon>
                </md-button>
            </md-speed-dial-content>
        </md-speed-dial>
        <md-snackbar md-positoin="center" :md-active.sync="showSnackbar" md-persistent>
            <span>{{message}}</span>
        </md-snackbar>
    </div>
</template>

<script>
    export default {
        name: "Album",
        props: ["admin","verified",'loggedIn','webpSupported'],
        data: () => ({
            items: [],
            comments: [],
            options: {
                /*
                shareButtons: [
                    {id:'download', label:'Download image', url:'{{raw_image_url}}', download:true}
                ]
                */
            },
            info: {
                title: "",
                description: "",
                originCount: 0,
                photoCount: 0,
                isPublic: false,
                isVisible: false,
                time: null
            },
            showSidepanel: false,
            comment: null,
            active: false,
            isLiked: false,
            showSnackbar: false,
            onlyOrigin: true,
            message: "",
            showDebug: false,
            csrf: null,
            worker: null,
            loaded: true,
            counter: [0],
            current: 0
        }),
        mounted: function () {
            const webp = function(data){
                //console.log("concurrent!")
                importScripts("https://nfls.io/js/libwebp-0.1.3.min.js")
                var decoder = new WebPDecoder()
                var WebPImage = {width: {value: 0}, height: {value: 0}}
                var bitmap = decoder.WebPDecodeARGB(data, data.length, WebPImage.width, WebPImage.height)
                return {
                    attribute: WebPImage,
                    bitmap: bitmap
                }
            }
            const actions = [
                { message: 'worker1', func: webp}
            ]
            this.worker = this.$worker.create(actions)
            this.loadData(true)
        },
        methods: {
            loadData: function(full){
                this.getCsrf()
                this.axios.get("/media/gallery/detail",{
                    params: {
                        id: this.$route.params["id"]
                    }
                }).then((response) => {
                    if(full){
                        this.loaded = false
                        var items = Object.values(response.data["data"]["photos"])
                        if(this.onlyOrigin){
                            //console.log(items)
                            items = items.filter(item => item.osrc != null)
                        }
                        this.items = items.map(function(val){
                            val.msrc = "/storage/photos/thumb/" + val.msrc
                            val.src = "/storage/photos/hd/" + val.src
                            if(val.osrc != null){
                                val.osrc = "/storage/photos/origin/" + val.osrc
                            }else{
                                val.osrc = null
                            }
                            return val
                        })
                        if(!this.webpSupported) {
                            this.items = this.items.slice(0, 5);
                            this.current = 0;
                            this.decodeToPNG();
                        } else {
                            this.loaded = true
                            this.items.map(function(val){
                                if(val.osrc != null){
                                    val.msrc = val.src
                                    val.src = val.osrc
                                }
                                return val
                            })
                        }
                    }
                    this.comments = response.data["data"]["comments"]
                    this.$emit('changeTitle', "相册 " + response.data["data"]["title"])
                    this.info.title = response.data["data"]["title"]
                    this.info.description = response.data["data"]["description"]
                    this.info.originCount = response.data["data"]["originCount"]
                    this.info.photoCount = response.data["data"]["photoCount"]
                    this.info.isPublic = response.data["data"]["public"]
                    this.info.isVisible = response.data["data"]["visible"]
                    this.info.time = response.data["data"]["time"]
                })
                this.axios.get("/media/gallery/like",{
                    params: {
                        id: this.$route.params["id"]
                    }
                }).then((response) => {
                    this.isLiked = response.data.data
                    //console.log(this.isLiked)
                })
            },
            showComments: function(){
                this.showSidepanel = true
            },
            writeComments: function(){
                this.active = true
            },
            submitComment: function(){
                this.axios.post('/media/gallery/comment', {
                    id: this.$route.params["id"],
                    content: this.comment,
                    _csrf: this.csrf
                }).then((response) => {
                    this.loadData(false)
                    this.showSidepanel = true
                    this.message = "You have successfully submmited your comment."
                    this.showSnackbar = true
                })
            },
            like: function(){
                this.axios.post('/media/gallery/like', {
                    id: this.$route.params["id"],
                    _csrf: this.csrf
                }).then((response) => {
                    if(this.isLiked){
                        this.message = "You have canceled your like to this gallery."
                    }else{
                        this.message = "You liked this gallery!"
                    }
                    this.showSnackbar = true
                    this.loadData(false)
                })
            },
            switchPreference: function(){
                this.onlyOrigin = !this.onlyOrigin
                if(this.onlyOrigin)
                    this.message = "Only featured photos will be shown!"
                else
                    this.message = "All the photos will be shown."
                this.showSnackbar = true
                this.loadData(true)
            },
            deleteComment: function(id){
                this.axios.post("/admin/media/comment/edit",{
                    "delete": "[" + id + "]",
                    _csrf: this.csrf
                }).then((response) => {
                    this.loadData(false)
                })
            }, getCsrf() {
                this.axios.get("user/csrf",{
                    params: {
                        name: "media.gallery"
                    }
                }).then((response) => {
                    this.csrf = response.data["data"]
                })
            }, webp() {
                this.$emit("renderWebp");
            }, decodeToPNG() {
                var index = this.current
                if(index >= this.items.length){
                    this.loaded = true
                    return
                }else{
                    this.loaded = false
                }
                this.axios.get(this.items[index].msrc, {
                    responseType: 'arraybuffer'
                }).then((response) => {
                    var data = convertBinaryToArray(atob(new Buffer(response.data, 'binary').toString('base64')))
                    var worker = this.getWorker()
                    this.worker.postMessage(worker,[data]).then((response) => {
                        this.items[index].msrc = this.bitmapToPNGFromCanvas(response.bitmap, response.attribute)
                        this.removeWorker(worker)
                    })
                })
                this.axios.get(this.items[index].src, {
                    responseType: 'arraybuffer'
                }).then((response) => {
                    var data = convertBinaryToArray(atob(new Buffer(response.data, 'binary').toString('base64')))
                    var worker = this.getWorker()
                    this.worker.postMessage(worker,[data]).then((response) => {
                        this.items[index].src = this.bitmapToPNGFromCanvas(response.bitmap, response.attribute)
                        this.removeWorker(worker)
                    })
                })
            }, bitmapToPNGFromCanvas(bitmap, attribute) {
                if (bitmap != null) {
                    var height = attribute.height.value
                    var width = attribute.width.value
                    var canvas = document.createElement("canvas")
                    canvas.innerHTML = "text"
                    document.body.appendChild(canvas)
                    canvas.style.display = "none"
                    canvas.height = height
                    canvas.width = width
                    var content = canvas.getContext("2d")
                    var image = content.createImageData(canvas.width, canvas.height)
                    var arr = image.data
                    for (var h = 0; h < height; h++)
                        for (var w = 0; w < width; w++) {
                            arr[2 + w * 4 + width * 4 * h] = bitmap[3 + w * 4 + width * 4 * h]
                            arr[1 + w * 4 + width * 4 * h] = bitmap[2 + w * 4 + width * 4 * h]
                            arr[0 + w * 4 + width * 4 * h] = bitmap[1 + w * 4 + width * 4 * h]
                            arr[3 + w * 4 + width * 4 * h] = bitmap[0 + w * 4 + width * 4 * h]
                        }
                    content.putImageData(image, 0, 0)
                    var k = canvas.toDataURL("image/png")
                    document.body.removeChild(canvas)
                } else k = attribute.URL;
                return k
            }, getWorker(){
                var minC = 100;
                var pos = 0;
                for(var i=0;i<=0;i++){
                    if(this.counter[i] < minC){
                        minC = this.counter[i]
                        pos = i
                    }
                }
                this.counter[pos] ++
                return "worker" + (pos+1)

            }, removeWorker(name){
                switch(name){
                    case "worker1":
                        this.counter[0] --
                        break
                    case "worker2":
                        this.counter[1] --
                        break
                    case "worker3":
                        this.counter[1] --
                        break
                    case "worker4":
                        this.counter[1] --
                        break
                }
                //console.log(this.counter)
                if(this.counter[0] == 0){
                    this.current ++
                    this.decodeToPNG()
                }
            }
        }
    }
</script>

<style scoped>
    .photo {
        display:inline-block;
        float:none;
        vertical-align: top;
        max-width:80px;
        min-width:250px;
        width:40%;
        margin:0px;
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