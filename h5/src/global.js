import SocketIO from "socket.io-client";
import WS from "@/utils/ws";

const _room_uri = process.env.VUE_APP_ROOM_SOCKET_IO_URL+'?token=' + window.localStorage['token']
// console.log(_room_uri)

const _uri = process.env.VUE_APP_HANDSHAKE_SOCKET_IO_URL+'?token=' + window.localStorage['token']
// console.log(_uri)

export default {
    userInfo: null, // 玩家信息
    socketIo: new WS()
}
