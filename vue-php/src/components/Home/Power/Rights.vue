<template>
  <div>
    <!-- 面包屑导航 -->
    <Breadcrumb titleTop="权限管理" titleBottom="权限列表"></Breadcrumb>

    <!-- 卡片 -->
    <el-card class="box-card">
      <el-table :data="rightList" border stripe style="width: 100%">
        <el-table-column type="index" width="50" label="#"></el-table-column>
        <el-table-column prop="auth_name" label="用户名"></el-table-column>
        <el-table-column prop="path" width="450" label="路径"></el-table-column>
        <el-table-column prop="level" width="450" label="层级">
          <template slot-scope="scope">
            <el-tag :type="levelList[scope.row.level]">{{levelList[scope.row.level*10]}}</el-tag>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>
<script>
import { httpPost } from "../../../http/index";
import Breadcrumb from "../../Common/Breadcrumb";
export default {
  components: {
    Breadcrumb
  },
  created() {
    this.getRightList();
  },
  data() {
    return {
      rightList: [],
      levelList: {
        1: "",
        10: "顶层",
        2: "success",
        20: "二层",
        3: "warning",
        30: "三层"
      }
    };
  },
  methods: {
    //获取权限列表
    async getRightList() {
      let {data:res} = await httpPost("rights","getRightList", { type: "list" });
      if (res.meta.status == 200) {
        this.rightList = res.data;
      } else {
        this.$message.error(res.meta.msg);
      }
    }
  }
};
</script>


<style lang="less" scoped>
</style>