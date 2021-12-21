<template>
  <div class="gobang-box">
    <template v-if="roomInfo">
      <p class="title" style="color: black">房间: <span style="color: black" v-text="roomInfo.title"></span></p>
      <div class="room-info-box">
        <div class="left-box">
          <p class="status" v-text="roomInfo.statusText"></p>
          <p style="color: black">观战：{{roomInfo.watchMemberIds.length}}</p>
        </div>
        <div class="player-box fr">
          <div class="info-box fl">
            <p class="username text-right" style="color: black" v-text="playerOther.username"></p>
            <p class="ready-status text-right">
              <template v-if="playerOther.playerId">
                <span style="color: black" v-if="2 == roomInfo.status">对方颜色：{{playerOther.colorText}}</span>
                <span style="color: black" v-else-if="playerOther.ready">已准备</span>
                <span style="color: black" v-else>未准备</span>
              </template>
              <span style="color: black" v-else>等待加入</span>
            </p>
          </div>
          <img class="player-thumb fr" style="margin-left: 12px" v-if="playerOther.playerId" :src="playerOther.avatar"/>
        </div>
      </div>
      <!-- 棋盘 -->
      <div class="gobang-area">
        <gobang ref="gobang" :disable="gobang.disable" v-on:go="onGo" :roomId="roomInfo.id" :lastGoX="gameInfo.last_go_x" :lastGoY="gameInfo.last_go_y"></gobang>
        <img class="img-wait" src="../assets/wait.png" v-if="1 == roomInfo.status"/>
      </div>
      <div class="bottom-box">
        <div class="player-box fl">
          <div class="info-box fr">
            <p class="username" style="color: black" v-text="playerMine.username"></p>
            <p class="ready-status">
              <template v-if="playerMine.playerId">
                <span style="color: black" v-if="2 == roomInfo.status">你的颜色：{{playerMine.colorText}}</span>
                <span style="color: black" v-else-if="playerMine.ready">已准备</span>
                <span style="color: black" v-else>未准备</span>
              </template>
              <span style="color: black" v-else>等待加入</span>
            </p>
          </div>
          <img class="player-thumb fl" style="margin-right: 12px;margin-top: 14px;" v-if="playerMine.playerId"  :src="playerMine.avatar"/>
        </div>
        <div class="btn-box">
          <template v-if="1 == roomInfo.status && !watchMode" class="center">
            <button class="btn-cancel-ready" style="color: black" v-if="isReady" @click="cancelReady">取消准备</button>
            <button class="btn-ready" style="color: black" v-else @click="ready">准备</button>
          </template>
          <button class="btn-leave" @click="leave"></button>
        </div>
      </div>
    </template>
    <!-- 聊天 -->
    <chat class="chat-box" v-if="roomInfo" :room="roomInfo.id" :rows="3"></chat>
    <!-- 游戏结果 -->
    <div v-if="showGameResultLayer">
      <div class="layer-mask" @click="closeGameResultLayer"></div>
      <div id="create-room-layer">
        <div class="title">
          <img src="../assets/victory.png" v-if="youWin"/>
          <img src="../assets/defeat.png" v-else/>
        </div>
        <div class="win-box">
          <div class="left">
            <img class="player-thumb" :src="playerMine.avatar"/>
            <p class="username" v-text="playerMine.username"></p>
            <img v-if="youWin" class="right-top" src="../assets/win.png"/>
            <img v-else class="right-top" src="../assets/lose.png"/>
          </div>
          <div class="right">
            <img class="player-thumb" :src="gameResultOtherAvatar"/>
            <p class="username" v-text="gameResultOtherUsername"></p>
            <img v-if="youWin" class="right-top" src="../assets/lose.png"/>
            <img v-else class="right-top" src="../assets/win.png"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Gobang from "@/components/Gobang.vue";
import Chat from "@/components/Chat.vue";
import piece from '../utils/piece';
import SocketIO from "socket.io-client";
export default {
  components: {
    Gobang,
    Chat,
  },
  data() {
    return {
      gobang: {
        disable: true,
      },
      roomInfo: null,
      gameInfo: {
        last_go_x: null,
        last_go_y: null,
      },
      isReady: false,
      watchMode: false,
      playerOther: {
        playerId: null,
        username: '-',
        avatar: '-',
        ready: false,
        color: null,
        colorText: '',
      },
      playerMine: {
        playerId: null,
        username: '-',
        avatar: '-',
        ready: false,
        color: null,
        colorText: '',
      },
      win: false,
      showGameResultLayer: false,
      youWin: false,
      gameResultOtherUsername: '',
      gameResultOtherAvatar: '',
    };
  },
  mounted(){
    const params = this.$route.params;
    console.log(params)
    if(!params.roomInfo)
    {
      this.$router.replace('/');
      return;
    }
    this.GLOBAL.socketIo.IO();

    this.watchMode = !!params.watchMode;
    this.roomInfo = params.roomInfo;
    const _this = this

    this.GLOBAL.socketIo.roomIo.emit("show", {id: params.roomInfo.id}, function (data) {
      const jsonData = JSON.parse(data);
      console.log(jsonData);
      _this.onJoinInfo(jsonData);
    });

    this.GLOBAL.socketIo.roomIo.on("show", function (data) {
      const jsonData = JSON.parse(data);
      console.log(jsonData);
      _this.onJoinInfo(jsonData);
    });

    this.GLOBAL.socketIo.roomIo.on("leave", function (data) {
      _this.onLeave();
    });

    this.GLOBAL.socketIo.roomIo.on("destroy", function (data) {
      _this.onRoomDestroy();
    });

    this.GLOBAL.socketIo.roomIo.on("game_show", function (data) {
      const jsonData = JSON.parse(data);
      console.log(jsonData);
      _this.onGobangInfo(jsonData);
    });

  },
  methods: {
    // 房间信息回调
    onJoinInfo(data){
      this.roomInfo = data.data;
      this.updatePlayer();
      this.updateGobangDisable(this.roomInfo);
    },
    // 准备
    ready(){
      const _this = this
      this.GLOBAL.socketIo.roomIo.emit("ready", {id: this.roomInfo.id}, function (data) {
        const jsonData = JSON.parse(data);
        console.log(jsonData);
        if (jsonData.code === 200) {
          _this.onRoomReady(jsonData);
        } else  {
          alert(jsonData.message)
        }
      });
    },
    // 离开房间
    leave(){
      const _this = this
      this.GLOBAL.socketIo.roomIo.emit("leave", {id: this.roomInfo.id}, function (data) {
        const jsonData = JSON.parse(data);
        console.log(jsonData);
        if (jsonData.code === 200) {
          _this.onLeave();
        } else  {
          alert(jsonData.message)
        }
      });
    },
    // 准备回调
    onRoomReady(data){
      this.isReady = true;
    },
    // 取消准备
    cancelReady(){
      const _this = this
      this.GLOBAL.socketIo.roomIo.emit("cancelReady", {id: this.roomInfo.id}, function (data) {
        const jsonData = JSON.parse(data);
        console.log(jsonData);
        if (jsonData.code === 200) {
          _this.onRoomCancelReady(data);
        } else  {
          alert(jsonData.message)
        }
      });
    },
    // 取消准备回调
    onRoomCancelReady(data){
      this.isReady = false;
    },
    // 房间销毁回调
    onRoomDestroy(data){
      alert('房间被销毁')
      this.$router.replace("/rooms")
    },
    // 对战信息回调
    onGobangInfo(data){
      if(data.data.game)
      {
        const game = data.data.game;
        this.gameInfo = game;
        this.$nextTick(()=>{
          if(this.$refs.gobang)
          {
            this.$refs.gobang.setMap(game.map);
          }
        })
        this.updatePlayer();
        this.updateGobangDisable(game);
      }
      if(data.data.winner)
      {
        // alert(data.winner.username + ' 赢啦！');
        this.gameResultOtherUsername = this.playerOther.username;
        this.gameResultOtherAvatar = this.playerOther.avatar;
        this.youWin = (data.data.winner.id === this.playerMine.playerId);
        this.openGameResultLayer();
        this.isReady = false;
      }
    },
    onLeave(data){
      this.$router.replace("/rooms")
    },
    updatePlayer(){
      console.log(this.GLOBAL.userInfo.id)
      console.log(this.roomInfo)
      console.log(this.gameInfo)
      if(!this.roomInfo)
      {
        return;
      }
      if(this.GLOBAL.userInfo.id === this.roomInfo.player1_id)
      {
        this.playerMine.playerId = this.roomInfo.player1_id;
        this.playerOther.playerId = this.roomInfo.player2_id;

        this.playerMine.username = this.roomInfo.player1.username;
        this.playerMine.avatar = this.roomInfo.player1.avatar_url;
        if(this.roomInfo.player2)
        {
          this.playerOther.username = this.roomInfo.player2.username;
          this.playerOther.avatar = this.roomInfo.player2.avatar_url;
        }
        else
        {
          this.playerOther.username = '';
          this.playerOther.avatar = '';
        }

        this.playerMine.ready = this.roomInfo.player1_ready;
        this.playerOther.ready = this.roomInfo.player2_ready;

        if(this.gameInfo)
        {
          this.playerMine.color = this.gameInfo.player1_color;
          this.playerOther.color = this.gameInfo.player2_color;
        }
      }
      else// if(this.GLOBAL.userInfo.id === this.roomInfo.playerId2)
      {
        this.playerMine.playerId = this.roomInfo.player2_id;
        this.playerOther.playerId = this.roomInfo.player1_id;

        if(this.roomInfo.player2)
        {
          this.playerMine.username = this.roomInfo.player2.username;
          this.playerMine.avatar = this.roomInfo.player2.avatar_url;
        }
        if(this.roomInfo.player1)
        {
          this.playerOther.username = this.roomInfo.player1.username;
          this.playerOther.avatar = this.roomInfo.player1.avatar_url;
        }
        else
        {
          this.playerOther.username = '';
          this.playerOther.avatar = '';
        }

        this.playerMine.ready = this.roomInfo.player2_ready;
        this.playerOther.ready = this.roomInfo.player1_ready;

        if(this.gameInfo)
        {
          this.playerMine.color = this.gameInfo.player2_color;
          this.playerOther.color = this.gameInfo.player1_color;
        }
      }
      // 颜色文字
      switch(this.playerMine.color)
      {
        case piece.BLACK_PIECE:
          this.playerMine.colorText = '黑';
          break;
        case piece.WHITE_PIECE:
          this.playerMine.colorText = '白';
          break;
        default:
          this.playerMine.colorText = '';
          break;
      }
      switch(this.playerOther.color)
      {
        case piece.BLACK_PIECE:
          this.playerOther.colorText = '黑';
          break;
        case piece.WHITE_PIECE:
          this.playerOther.colorText = '白';
          break;
        default:
          this.playerOther.colorText = '';
          break;
      }
      this.$nextTick(()=>{
        if(this.$refs.gobang)
        {
          this.$refs.gobang.setCurrentPiece(this.playerMine.color)
        }
      })
    },
    updateGobangDisable(game){
      if(game.current_piece)
      {
        this.gobang.disable = 1 === this.roomInfo.status || !(game.current_piece === this.playerMine.color)
      }
    },
    onGo(point){
      this.gobang.disable = true;
      // this.GLOBAL.websocketConnection.sendEx('gobang.go', {
      //   roomId: this.roomInfo.roomId,
      //   x: point.x,
      //   y: point.y,
      // });
      const goObject = {id: this.roomInfo.id, x: point.x, y: point.y,};

      this.GLOBAL.socketIo.roomIo.emit("go", goObject, function (data) {
        const jsonData = JSON.parse(data);
        console.log(jsonData);
        if (jsonData.code !== 200) {
          alert(jsonData.message)
        }
      });

    },
    openGameResultLayer(win){
      this.win = win;
      this.showGameResultLayer = true;
    },
    closeGameResultLayer(){
      this.showGameResultLayer = false;
    },
  },
};
</script>

<style lang="less" scoped>
.gobang-box{
  height: 100vh;
  display: flex;
  // flex-flow: column;
  align-content: flex-start;
  flex-direction:column;
  .chat-box{
    flex: auto;
    margin-bottom: 6px;
  }
}
.title{
  margin: 14px 0 0 0;
  color: #fff;
  font-weight: bold;
  font-size: 20px;
}
.center{
  text-align: center;
}
.readyed{
  color: green;
  font-weight: bold;
}
#player-info-box{
  display: flex;
  // justify-content: space-between;
  flex-flow: row wrap;
  align-content: flex-start;
  div{
    flex: 0 0 50%;
  }
}
.button-box{
  button{
    margin: 0 4px;
  }
}
.room-info-box{
  color: #fff;
  .left-box{
    float:left;
    .status{
      color: #FFEA00;
    }
  }
  p{
    margin: 10px 0;
  }
}
.player-box{
  color: #fff;
  .ready-status{
    color: #DEDEDE;
  }
  .player-thumb{
    width: 56px;
    height: 56px;
    margin-top: 4px;
  }
  .username{
    font-weight: bold;
  }
}
.btn-box{
  float:right;
  display:flex;
  padding-top: 18px;
}
.btn-ready, .btn-cancel-ready{
  background: #f3f4f8;
  border:none;
  outline: none;
  border-radius: 30px;
  width: 122px;
  line-height: 48px;
  text-align: center;
  font-size: 20px;
}
.btn-ready{
  color: #43BB43;
}
.btn-cancel-ready{
  color: #F24242;
}
.btn-leave{
  border:none;
  outline: none;
  border-radius: 8px;
  width: 50px;
  height: 50px;
  background-image: url(../assets/leave.png);
  background-position: center center;
  background-size: 32px;
  background-repeat: no-repeat;
  margin-left: 10px;
}
#create-room-layer{
  background-color: #fff;
  border-radius:30px;
  width: 500px;
  max-width: 100%;
  position:absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  .title{
    margin: 0;
    padding: 18px 55px;
    img{
      max-width: 100%;
    }
  }
  .win-box{
    display: flex;
    text-align: center;
    height: 164px;
    .left{
      width: 50%;
      background:rgba(233,236,243,1);
      border-radius:0px 0px 0px 30px;
      color: #323F49;
      font-weight: bold;
      position: relative;
    }
    .right{
      width: 50%;
      background:rgba(74,82,99,1);
      border-radius:0px 0px 30px 0px;
      color: #fff;
      font-weight: bold;
      position: relative;
    }
    .player-thumb{
      width: 56px;
      height: 56px;
      margin-top: 40px;
      border: 1px solid rgba(0, 0, 0, 0.25);
      border-radius: 100%;
    }
    .right-top{
      width: 60px;
      position: absolute;
      top: 0;
      right: 0;
    }
  }
}
.gobang-area{
  position: relative;
  .img-wait{
    position:absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 284px;
  }
}
</style>
