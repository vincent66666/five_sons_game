<template>
  <div class="home">
    <!-- 标题 -->
    <div class="center">
      <img alt="Vue logo" class="logo" src="../assets/logo.png" />
    </div>
    <div class="center">
      <img class="img-title" src="../assets/title.png" />
    </div>

    <!-- 开始、注册登录按钮层 -->
    <div id="start">
      <div v-if="logined">
        <p class="hello" v-if="GLOBAL">
          <img class="logined-thumb" :src="GLOBAL.userInfo.avatar" />
          你好，{{GLOBAL.userInfo.username}}！
        </p>
        <a id="btn-start" class="big-btn" @click="start">开始游戏</a>
      </div>
      <template v-else>
        <div>
          <a id="btn-login" class="big-btn" @click="openLogin">登录</a>
        </div>
        <div>
          <a id="btn-register" class="big-btn" @click="openRegister">注册</a>
        </div>
      </template>
    </div>

    <!-- 登录层 -->
    <div v-if="showLoginLayer">
      <div class="layer-mask" @click="closeLogin"></div>
      <div id="login-layer">
        <div class="center register-thumb"><img src="../assets/avatar/Aff2.png"/></div>
        <a class="btn-close-login" @click="closeLogin"></a>
        <div><input id="input-username" class="input" type="text" placeholder="请输入昵称" v-model="username"/></div>
        <div><input id="input-password" class="input" type="password" placeholder="请输入密码" v-model="password"/></div>
        <div>
          <a class="big-btn" id="btn-login-submit" @click="login">登录</a>
        </div>
      </div>
    </div>

    <!-- 注册层 -->
    <div v-if="showRegisterLayer">
      <div class="layer-mask" @click="closeRegister"></div>
      <div id="register-layer">
        <div class="center register-thumb"><img src="../assets/avatar/Aff1.png"/></div>
        <a class="btn-close-login" @click="closeRegister"></a>
        <div><input id="input-username" class="input" type="text" placeholder="请输入昵称" v-model="username"/></div>
        <div><input id="input-password" class="input" type="password" placeholder="请输入密码" v-model="password"/></div>
        <div>
          <a class="big-btn" id="btn-login-submit" @click="register">注册</a>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
if (window.localStorage['userInfo']) {
  const userInfo = JSON.parse(window.localStorage['userInfo'])
  console.log(userInfo.avatar)
}
// @ is an alias to /src
import request from '@/plugin/axios'
export default {
  name: "home",
  components: {
  },
  data() {
    return {
      logined: false,
      username: '',
      password: '',
      showLoginLayer: false,
      showRegisterLayer: false,
    };
  },
  mounted() {
    this.loadStatus()
  },
  methods: {
    // 加载登录状态
    loadStatus(){
      request({
        url: '/api/user',
        excludeCodes: [1001],
      }).then((result) => {
        this.GLOBAL.userInfo = result.data;
        window.localStorage['userInfo'] = JSON.stringify(result.data)
        this.logined = 200 === result.code;
      })
    },
    // 打开登录
    openLogin(){
      this.showLoginLayer = true;
    },
    // 关闭登录
    closeLogin(){
      this.showLoginLayer = false;
    },
    // 打开注册
    openRegister(){
      this.showRegisterLayer = true;
    },
    // 关闭注册
    closeRegister(){
      this.showRegisterLayer = false;
    },
    // 注册
    register(){
      request({
        headers: {
          'Content-Type': 'application/json'
        },
        transformRequest: [function(data) {      //在请求之前对data传参进行格式转换
          data = JSON.stringify(data)
          return data
        }],
        method: 'POST',
        url: '/api/register',
        data: {
          username: this.username,
          password: this.password,
        }
      }).then((result) => {
        if(200 === result.code)
        {
          this.password = '';
          this.closeRegister();
          alert('注册成功，请再次输入密码登录');
        }
      })
    },
    // 登录
    login(){
      request({
        headers: {
          'Content-Type': 'application/json'
        },
        transformRequest: [function(data) {      //在请求之前对data传参进行格式转换
          data = JSON.stringify(data)
          return data
        }],
        method: 'POST',
        url: '/api/login',
        params: {},
        data: {
          username: this.username,
          password: this.password,
        }
      }).then((result) => {
        console.log(result)
        if(200 === result.code)
        {
          window.localStorage['token'] = result.data.token_type + ' ' + result.data.token
        }
        this.loadStatus();
        this.closeLogin();
        alert('登录成功');
      })
    },
    // 开始游戏
    start() {
      if(!this.logined)
      {
        alert('请先登录');
      }
      this.$router.replace({name:'rooms'});
    },
  }
};
</script>

<style lang="less">
@import "../style/style.less";
.big-btn{
  background:linear-gradient(-18deg,rgba(67,234,128,1) 0%,rgba(56,248,212,1) 100%);
  border-radius:10px;
  text-align: center;
  box-sizing: border-box;
  display: inline-block;
  text-decoration: none;
  padding: 8px 24px;
  font-size: 24px;
  color: #323F49;
  width: 100%;
  line-height: 44px;
  font-weight: bold;
}
.home{
  .logo{
    width: 100px;
    height: 100px;
    margin-top: 60px;
  }
  .hello{
    color: #fff;
    font-size: 20px;
  }
  .logined-thumb{
    width: 36px;
    height: 36px;
    vertical-align: middle;
    padding-right: 8px;
    padding-bottom: 6px;
  }
  .img-title{
    max-width: 100%;
  }
  #btn-register{
    background:rgba(232,239,237,1);
  }
  #login-layer, #register-layer{
    background-color: #fff;
    border-radius:30px;
    width: 500px;
    height: 350px;
    max-width: 100%;
    position:absolute;
    top: 75%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 48px 40px 0 40px;
    box-sizing: border-box;
    .btn-close-login{
      display: block;
      width: 16px;
      height: 16px;
      background-image: url(../assets/close.png);
      background-size: cover;
      position: absolute;
      right: 20px;
      top: 20px;
    }
    #input-username{
      margin-top: 28px;
    }
    #btn-login-submit{
      margin-top: 10px;
    }
    .register-thumb{
      position: absolute;
      top: -48px;
      left: calc(50% - 48px);
      img{
        width: 96px;
        height: 96px;
      }
    }
  }
}
h1 {
  margin-top: 0;
  font-size: 24px;
  color: #fff;
  line-height: 64px;
  margin-bottom: 0;
  -webkit-text-stroke: 1px rgba(0, 0, 0, 0.4);
  text-align: center;
  line-height: 48px;
  margin-bottom: 20px;
}
#start:extend(.center) {
  margin: 36px 0;
  #btn-login,#btn-register,#btn-start {
    display: inline-block;
    text-decoration: none;
    border-radius:54px;
    padding: 8px 24px;
    font-size: 24px;
    color: #323F49;
    width: 290px;
    line-height: 44px;
    font-weight: bold;
    margin-bottom: 20px;
  }
  #btn-start{
    margin-top: 36px;
  }
}
.footer{
  left: 0;
  color: #fff;
  position: fixed;
  bottom: 0;
  text-align: center;
  width: 100%;
  a{
    color: #fff;
  }
}
</style>