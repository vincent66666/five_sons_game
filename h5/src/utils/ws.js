import SocketIO from "socket.io-client";

export default class WS {
    constructor(){
        this.roomIo = null;
        this.handshakeIo = null;
    }
    IO(){
        console.log("io-load+++++++++++++++++++")
        const _this = this
        if (window.localStorage['token']) {
            if (!this.handshakeIo) {
                const _uri = process.env.VUE_APP_HANDSHAKE_SOCKET_IO_URL+'?token=' + window.localStorage['token']
                console.log("handshakeConnection +++++++++++++")
                this.handshakeIo = SocketIO(_uri, {
                    transports: ["websocket"]
                });
                this.handshakeIo.on("connect", function (data) {
                    console.log("io-load+++handshakeIo++receive-1++++++++++++++")
                    _this.handshakeIo.emit("receive", {token: window.localStorage['token']}, console.log);
                });
            }

            this.handshakeIo.on("disconnect", function (data) {
                alert('handshake 断线了')
                location.reload()
            });
            this.handshakeIo.on("connect_error", function (data) {
                alert('连接异常')
                location.reload()
            });
            this.handshakeIo.on("error", function (data) {
                alert('handshake 连接错误')
                location.reload()
            });

            if (!this.roomIo) {
                const _room_uri = process.env.VUE_APP_ROOM_SOCKET_IO_URL+'?token=' + window.localStorage['token']
                this.roomIo = SocketIO(_room_uri, {
                    transports: ["websocket"]
                });
                console.log("roomConnection +++++++++++++")

            }
            this.roomIo.on("disconnect", function (data) {
                alert('room 断线了')
                location.reload()
            });
            this.roomIo.on("connect_error", function (data) {
                alert('连接异常')
                location.reload()
            });
            this.roomIo.on("error", function (data) {
                alert('room 连接错误')
                location.reload()
            });
        }
    }
}