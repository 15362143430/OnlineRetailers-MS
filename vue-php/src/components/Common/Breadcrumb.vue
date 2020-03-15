<template>
  <div>
    <el-breadcrumb separator-class="el-icon-arrow-right">
      <el-breadcrumb-item :to="{ path: '/home' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>{{titleTop}}</el-breadcrumb-item>
      <el-breadcrumb-item>{{titleBottom}}</el-breadcrumb-item>
    </el-breadcrumb>
  </div>
</template>

<script>
export default {
    props: ["titleTop","titleBottom"]
};
</script>

<style lang="less" scoped>
</style>