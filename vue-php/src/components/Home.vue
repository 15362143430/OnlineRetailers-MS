<template>
  <el-container class="home_container">
    <el-header>
      <div>
        <img height="50" src="../assets/logo.png" alt />
        <span>电商后台管理系统</span>
      </div>
      <el-button type="primary" @click="exit()">退出</el-button>
    </el-header>
    <el-container>
      <el-aside :width="is_collapse?'64px':'200px'">
        <div class="toggle-button" @click="toggleCollapse()">|||</div>
        <el-menu
          :default-active="nav"
          router
          :collapse-transition="false"
          :collapse="is_collapse"
          :unique-opened="true"
          background-color="#333744"
          text-color="#fff"
          active-text-color="#409EFF"
        >
          <el-submenu :index="item.id+''" v-for="item in menuList" :key="item.id">
            <template slot="title">
              <i :class="icon[item.id]"></i>
              <span>{{item.authName}}</span>
            </template>
            <el-menu-item
              @click="saveNav(up_item.path)"
              v-for="up_item in item.children"
              :key="up_item.id"
              :index="up_item.path"
            >
              <template slot="title">
                <i class="el-icon-s-grid"></i>
                <span>{{up_item.authName}}</span>
              </template>
            </el-menu-item>
          </el-submenu>
        </el-menu>
      </el-aside>
      <el-main>
        <router-view></router-view>
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
import { httpPost } from "../http/index";
export default {
  data() {
    return {
      menuList: [],
      icon: {
        1: "el-icon-user",
        2: "el-icon-open",
        3: "el-icon-shopping-bag-1",
        4: "el-icon-document-copy",
        5: "el-icon-s-data"
      },
      is_collapse: false,
      nav: ""
    };
  },
  created() {
    this.getMenuList();
    this.nav = sessionStorage.getItem("nav");
  },
  methods: {
    exit() {
      sessionStorage.clear();
      this.$router.push("/login");
    },
    //获取左侧菜单
    async getMenuList() {
      const { data: res } = await httpPost("menus","getMenuList");
      console.log(res);
      if (res.meta.status == 200) {
        this.menuList = res.data;
      } else {
        this.$message.error("获取菜单失败了啊");
      }
    },
    //点击按钮切换
    toggleCollapse() {
      this.is_collapse = !this.is_collapse;
    },
    saveNav(path) {
      sessionStorage.setItem("nav", path);
    }
  }
};
</script>

<style lang="less" scoped>
.el-header {
  background-color: #373d41;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-left: 0;
  color: white;
  font-size: 20px;
  > div {
    display: flex;
    align-items: center;
    > img {
      border-radius: 50%;
    }
    > span {
      padding-left: 15px;
    }
  }
}
.el-aside {
  background-color: #333744;
  .el-menu {
    border: none;
  }
  .toggle-button {
    background-color: #4a5064;
    text-align: center;
    height: 24px;
    line-height: 24px;
    color: #fff;
    letter-spacing: 0.2em;
    cursor: pointer;
  }
}
.el-main {
  background-color: #eaedf1;
}
.home_container {
  height: 100%;
}
</style>