<template>
    <div id="app">
        <div class="container">
            <div class="__cov-video-container" @mouseenter="mouseEnterVideo" @mouseleave="mouseLeaveVideo">
                <video :class="{ 'hide-cursor': !state.contrlShow }" class="__cov-video" :poster="options.poster">
                    <source v-for="source in sources" :src="source.src" :type="source.type">
                </video>
                <div class="__cov-contrl-content" transition="fade" v-show="state.contrlShow">
                    <div class="__cov-contrl-title">
                        <img @click="backPrev" style="margin-right: 22px;" src="../static/images/video_arrow.png" alt="">
                        {{name}}
                    </div>
                    <div class="__cov-contrl-play-btn-icon-else" v-show="!state.playing" @click="play"></div>
                    <button class="__cov-contrl-play-btn" @click="play" v-show="state.playing">
                        <svg class="__cov-contrl-play-btn-icon" viewBox="0 0 15 22" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <title>Combined Shape</title>
                            <desc>Created with Sketch.</desc>
                            <defs>
                                <path d="M0,0.979149244 L5,0.979149244 L5,22 L0,22 L0,0.979149244 Z M10,0.979149244 L15,0.979149244 L15,22 L10,22 L10,0.979149244 Z" id="path-1"></path>
                                <mask id="mask-2" maskContentUnits="userSpaceOnUse" maskUnits="objectBoundingBox" x="0" y="0" width="15" height="21.0208508" fill="white">
                                    <use xlink:href="#path-1"></use>
                                </mask>
                            </defs>
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <use id="Combined-Shape" stroke="#FFFFFF" mask="url(#mask-2)" stroke-width="2" fill="#FFFFFF" xlink:href="#path-1"></use>
                            </g>
                        </svg>
                    </button>
                    <div class="__cov-contrl-content_bottombar">
                        <div class="__cov-contrl-video-time">
                            <span class="__cov-contrl-video-time-text">{{video.displayTime}}</span>
                        </div>
                        <div class="__cov-contrl-video-slider" @click="slideClick" @mousedown="videoMove">
                            <div class="__cov-contrl-video-isplay" :style="'width:' + video.pos.current+'px'"></div>
                            <div class="__cov-contrl-video-inner" :style="{ 'transform': `translate3d(${video.pos.current}px, 0, 0)`}"></div>
                            <div class="__cov-contrl-video-rail">
                                <div class="__cov-contrl-video-rail-inner" :style="{ 'transform': 'translate3d(' +video.loaded + '%, 0, 0)'}"></div>
                            </div>
                        </div>
                        <div class="__cov-contrl-video-time">
                            <span class="__cov-contrl-video-time-text">{{video.duration}}</span>
                        </div>
                        <div class="__cov-contrl-vol-box">
                            <button class="__cov-contrl-play-btn" @click="volMuted">
                                <svg class="__cov-contrl-vol-btn-icon" viewBox="0 0 41 44" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <!-- Generator: Sketch 3.8.3 (29802) - http://www.bohemiancoding.com/sketch -->
                                    <title>vol</title>
                                    <desc>Created with Sketch.</desc>
                                    <defs>
                                        <path d="M8.61522369,12 L20,0.615223689 L20,37.3847763 L8.61522369,26 L1.99201702,26 C0.891856397,26 0,25.1029399 0,23.9941413 L0,14.0058587 C0,12.8980535 0.900176167,12 1.99201702,12 L8.61522369,12 L8.61522369,12 Z" id="cov-vol"></path>
                                    </defs>
                                    <g id="Page-1" stroke="none" stroke-width="2" fill="none" fill-rule="evenodd">
                                        <g id="vol" transform="translate(2.000000, 3.000000)">
                                            <g id="cov-vol-icon">
                                                <g id="Combined-Shape-Clipped">
                                                    <path v-show="volume.percent > 1 && !volume.muted" d="M25,29.5538997 C28.4589093,27.6757536 31.2629093,23.2984641 31.2629093,19.7769499 C31.2629093,16.2554357 28.4589093,11.8781461 25,10" id="vol-range-2" stroke="#FFFFFF"></path>
                                                    <path v-show="volume.percent > 70 && !volume.muted" d="M28,35.5538997 C33.5816016,32.5231573 38.1063837,25.4595762 38.1063837,19.7769499 C38.1063837,14.0943235 33.5816016,7.03074247 28,4" id="vol-range-2" stroke="#FFFFFF"></path>
                                                    <mask id="mask-2" fill="white">
                                                        <use xlink:href="#cov-vol"></use>
                                                    </mask>
                                                    <use id="vol-path" stroke="#FFFFFF" stroke-width="3" xlink:href="#cov-vol"></use>
                                                    <g id="Combined-Shape" mask="url(#mask-2)" stroke="#FFFFFF" stroke-width="2" fill="#FFFFFF">
                                                        <path d="M8.61522369,12 L20,0.615223689 L20,37.3847763 L8.61522369,26 L1.99201702,26 C0.891856397,26 0,25.1029399 0,23.9941413 L0,14.0058587 C0,12.8980535 0.900176167,12 1.99201702,12 L8.61522369,12 L8.61522369,12 Z" id="cov-vol"></path>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </button>
                            <div class="__cov-contrl-vol-slider" @click="volSlideClick" @mousedown="volMove">
                                <div class="__cov-contrl-vol-inner" :style="{ 'transform': `translate3d(${volume.pos.current}px, 0, 0)`}"></div>
                                <div class="__cov-contrl-vol-rail"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    const getMousePosition = function (e, type = 'x') {
        if (type === 'x') {
            return e.pageX
        }
        return e.pageY
    }
    const pad = (val) => {
        val = Math.floor(val)
        if (val < 10) {
            return '0' + val
        }
        return val + ''
    }
    const timeParse = (sec) => {
        let min = 0
        min = Math.floor(sec / 60)
        sec = sec - min * 60
        return pad(min) + ':' + pad(sec)
    }
    export default {
        props: {
            sources: Array,
            options: {
                type: Object,
                default () {
                    return {
                        autoplay: false,
                        volume: 0.9,
                        poster: ''
                    }
                }
            },
            name: {
                type: String,
                default: ''
            }
        },
        data () {
            return {
                $video: null,
                video: {
                    $videoSlider: null,
                    len: 0,
                    current: 0,
                    loaded: 0,
                    moving: false,
                    displayTime: '00:00',
                    pos: {
                        start: 0,
                        width: 0,
                        innerWidth: 0,
                        current: 0
                    },
                    duration: null
                },
                volume: {
                    $volBox: null,
                    muted: false,
                    percent: 60,
                    moving: false,
                    pos: {
                        start: 0,
                        width: 0,
                        innerWidth: 0,
                        current: 0
                    }
                },
                player: {
                    $player: null,
                    pos: null
                },
                tmp: {
                    contrlHideTimer: null
                },
                state: {
                    contrlShow: true,
                    vol: 0.5,
                    currentTime: 0,
                    fullScreen: true,
                    playing: false
                }
            }
        },
        ready () {
            this.init()
        },
        mounted () {
            this.init()
            this.fullScreen()
        },
        beforeDestroy () {
            document.body.removeEventListener('mousemove', this.mouseMoveAction)
            document.body.removeEventListener('mouseup', this.mouseUpAction)
        },
        methods: {
            init () {
                this.$video = this.$el.getElementsByTagName('video')[0]
                this.initCore()
                if (this.options.autoplay) {
                    this.play()
                }
                document.body.addEventListener('mousemove', this.mouseMoveAction, false)
                document.body.addEventListener('mouseup', this.mouseUpAction, false)
            },
            initCore () {
                this.initVol()
                this.initVideo()
                this.initPlayer()
                const vol = this.options.volume || 0.9
                this.setVol(vol)
            },
            initPlayer () {
                const $player = this.$el.getElementsByClassName('__cov-video-container')[0]
                this.player.pos = $player.getBoundingClientRect()
                this.player.$player = $player
            },
            initVol () {
                const $volBox = this.$el.getElementsByClassName('__cov-contrl-vol-slider')[0]
                const $volInner = $volBox.getElementsByClassName('__cov-contrl-vol-inner')[0]
                this.volume.$volBox = $volBox
                this.volume.pos.innerWidth = $volInner.getBoundingClientRect().width
                this.volume.pos.start = $volBox.getBoundingClientRect().left
                this.volume.pos.width = $volBox.getBoundingClientRect().width - this.volume.pos.innerWidth
            },
            initVideo () {
                const $videoSlider = this.$el.getElementsByClassName('__cov-contrl-video-slider')[0]
                const $videoInner = $videoSlider.getElementsByClassName('__cov-contrl-video-inner')[0]
                this.video.pos.start = $videoSlider.getBoundingClientRect().left
                this.video.pos.innerWidth = $videoInner.getBoundingClientRect().width
                this.video.pos.width = $videoSlider.getBoundingClientRect().width - this.video.pos.innerWidth
                this.getTime()
            },
            mouseEnterVideo () {
                if (this.tmp.contrlHideTimer) {
                    clearTimeout(this.tmp.contrlHideTimer)
                    this.tmp.contrlHideTimer = null
                }
                this.state.contrlShow = true
            },
            mouseLeaveVideo (e) {
                if (this.tmp.contrlHideTimer) {
                    clearTimeout(this.tmp.contrlHideTimer)
                }
                this.tmp.contrlHideTimer = setTimeout(() => {
                    this.state.contrlShow = false
                    this.tmp.contrlHideTimer = null
                }, 2000)
            },
            toggleContrlShow () {
                this.state.contrlShow = !this.state.contrlShow
            },
            getTime () {
                this.$video.addEventListener('durationchange', (e) => {
                    console.log(e)
                })
                this.$video.addEventListener('progress', (e) => {
                    this.video.loaded = (-1 + (this.$video.buffered.end(0) / this.$video.duration)) * 100
                })
                this.video.len = this.$video.duration
            },
            setVideoByTime (percent) {
                this.$video.currentTime = Math.floor(percent * this.video.len)
            },
            play () {
                this.state.playing = !this.state.playing
                if (this.$video) {
                    if (this.state.playing) {
                        this.$video.play()
                        this.mouseLeaveVideo()
                        this.$video.addEventListener('timeupdate', this.timeline)
                        this.$video.addEventListener('ended', (e) => {
                            this.state.playing = false
                            this.video.pos.current = 0
                            this.$video.currentTime = 0
                        })
                    } else {
                        this.$video.pause()
                    }
                }
            },
            timeline () {
                const percent = this.$video.currentTime / this.$video.duration
                this.video.pos.current = (this.video.pos.width * percent).toFixed(3)
                this.video.duration = timeParse(this.$video.duration)
                this.video.displayTime = timeParse(this.$video.currentTime)
            },
            volMove (e) {
                this.initVol()
                this.volume.moving = true
            },
            videoMove (e) {
                this.initVideo()
                this.video.moving = true
            },
            slideClick (e) {
                this.videoSlideMove(e)
            },
            volSlideClick (e) {
                this.volSlideMove(e)
            },
            volMuted () {
                this.$video.muted = !this.$video.muted
                this.volume.muted = this.$video.muted
            },
            setVol (val) {
                if (this.$video) {
                    this.volume.pos.current = val * this.volume.pos.width
                    this.volume.percent = val * 100
                    this.$video.volume = val
                }
            },
            fullScreen () {
                // if (!this.state.fullScreen) {
                    this.state.fullScreen = true
                    this.$video.webkitRequestFullScreen()
                // } else {
                //     this.state.fullScreen = false
                    // document.webkitCancelFullScreen()
                // }
                setTimeout(this.initVideo, 200)
            },
            mouseMoveAction (e) {
                if (this.volume.moving) {
                    this.volSlideMove(e)
                }
                if (this.video.moving) {
                    this.videoSlideMove(e)
                }
                this.contrlHider(e)
            },
            contrlHider (e) {
                const x = getMousePosition(e, 'x')
                const y = getMousePosition(e, 'y')
                if (!this.player.pos) return
                if (x > this.player.pos.left &&
                    x < this.player.pos.left + this.player.pos.width
                ) {
                    if (
                        y > this.player.pos.top + this.player.pos.height * 0.6 &&
                        y < this.player.pos.top + this.player.pos.height
                    ) {
                        return this.mouseEnterVideo()
                    }
                }
                return this.mouseLeaveVideo()
            },
            volSlideMove (e) {
                const x = getMousePosition(e) - this.volume.pos.start
                if (x > 0 && x < this.volume.pos.width) {
                    this.setVol(x / this.volume.pos.width)
                }
            },
            videoSlideMove (e) {
                const x = getMousePosition(e) - this.video.pos.start
                if (x > 0 && x < this.video.pos.width) {
                    this.video.pos.current = x
                    this.setVideoByTime(x / this.video.pos.width)
                }
            },
            mouseUpAction (e) {
                this.volume.moving = false
                this.video.moving = false
            },
            backPrev () {
                this.$emit('backPrev')
            }
        }
    }
</script>
<style lang="less" scoped>
    video{
        object-fit: cover;
    }
    .__cov-contrl-title{
        position: absolute;
        font-size: 31px;
        line-height: 1;
        padding: 38px 0 0 42px;
        color: white;
        img{
            width: 17px;
            height: 31px;
            vertical-align: top;
        }
    }
    .__cov-video-container {
        position: relative;
        width: 100%;
        background-color: #000;
    }
    .__cov-video {
        width: 100vw;
        height: 100vh;
        vertical-align: bottom;
    }
    .__cov-contrl-content {
        position: absolute;
        background: rgba(0, 0, 0, 0.1);
        width: 100vw;
        height: 100vh;
        top: 0;
    }
    .__cov-contrl-content_bottombar{
        position: absolute;
        display: flex;
        left: 0;
        bottom: 0;
        height: 78px;
        background-color: rgba(0, 0, 0, 0.3);
        width: 100%;
        z-index: 2147483647;
    }
    .__cov-contrl-play-btn {
        position: relative;
        background: rgba(0,0,0,.4);
        border-radius: 50%;
        border: none;
        height: 208px;
        width: 208px;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
        outline: none;
        vertical-align: top;
        -webkit-user-modify: inherit;
    }
    .__cov-contrl-play-btn-icon {
        position: absolute;
        height: 90px;
        width: 90px;
        top: 50%;
        left: 50%;
        margin-top: -45px;
        margin-left: -45px;
        -webkit-user-modify: inherit;
    }
    .__cov-contrl-play-btn-icon-else{
        position: absolute;
        width: 208px;
        height: 208px;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
        background: url("../static/images/video_play.png");
    }
    .__cov-contrl-vol-btn-icon {
        position: absolute;
        height: 1.1rem;
        width: 1.1rem;
        top: 50%;
        left: 50%;
        margin-top: -.55rem;
        margin-left: -.55rem;
    }
    .__cov-contrl-vol-slider {
        position: relative;
        display: inline-block;
        height: 100%;
        width: 6rem;
        height: 2rem;
        overflow: hidden;
        transition: all .2s ease-in;
    }
    .__cov-contrl-vol-rail {
        position: absolute;
        top: 50%;
        width: 6rem;
        height: .1rem;
        margin-top: -.05rem;
        background: #fff;
    }
    .__cov-contrl-vol-inner {
        position: absolute;
        display: inline-block;
        left: 0;
        top: 50%;
        background: #fff;
        width: .5rem;
        height: .5rem;
        border-radius: 50%;
        margin-top: -.25rem;
        z-index: 2;
        cursor: pointer;
    }
    .__cov-contrl-vol-box {
        /*display: flex;*/
        display: none;
    }
    .__cov-contrl-video-slider {
        position: relative;
        display: inline-block;
        height: 100%;
        width: 100%;
        overflow: hidden;
        margin: 0 .5rem;
        transition: all .2s ease-in;
    }
    .__cov-contrl-video-rail {
        position: absolute;
        top: 50%;
        width: 100%;
        height: 4px;
        margin-top: -2px;
        background: rgba(255, 255, 255, 0.5);
        overflow: hidden;
    }
    .__cov-contrl-video-rail-inner {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: rgb(255, 255, 255);
        transition: transform .2s;
    }
    .__cov-contrl-video-isplay{
        background: #20bc22;
        height: 4px;
        margin-top: 37px;
        position: absolute;
        left: 0;
        z-index: 3;
    }
    .__cov-contrl-video-inner {
        position: absolute;
        display: inline-block;
        left: 0;
        top: 50%;
        background: #fff;
        width: 20px;
        height: 24px;
        border-radius: 5px;
        margin-top: -12px;
        box-shadow: 0 0 10px rgba(8,147,231,.5);
        z-index: 2;
        cursor: pointer;
        transition: all 16ms;
    }
    .__cov-contrl-video-time {
        padding: 0 1rem;
    }
    .__cov-contrl-video-time-text {
        color: #fff;
        line-height: 78px;
        font-size: 20px;
    }
    ::-webkit-media-controls {
        display:none !important;
    }
    video::-webkit-media-controls {
        display:none !important;
    }
    video::-webkit-media-controls-enclosure {
        display:none !important;
    }
    .fade-transition {
        transition: opacity .3s ease;
    }
    .fade-enter{
        opacity: 1;
    }
    .fade-leave {
        opacity: 0;
    }
    .hide-cursor {
        cursor: none;
    }
    @media all and (max-width: 768px) {
        .__cov-contrl-vol-slider {
            width: 3rem;
        }
        .__cov-contrl-video-time {
            padding: 0 .2rem;
        }
        .__cov-contrl-vol-box .__cov-contrl-play-btn {
            width: 2rem;
        }
    }
</style>