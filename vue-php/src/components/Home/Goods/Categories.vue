<template>
  <div>
    <!-- 面包屑导航 -->
    <Breadcrumb titleTop="商品管理" titleBottom="商品分类"></Breadcrumb>

    <!-- 卡片 -->
    <el-card class="box-card">
      <el-row :gutter="20">
        <el-col :span="5">
          <el-button type="primary" @click="openAddCate()">添加分类</el-button>
        </el-col>
      </el-row>
      <tree-table
        :data="cateList"
        :columns="columns"
        :selection-type="false"
        :expand-type="false"
        show-index
        index-text="#"
        border
        :show-row-hover="false"
      >
        <template slot="is_delete" slot-scope="scope">
          <i class="el-icon-success" v-if="scope.row.is_delete == 0"></i>
          <i class="el-icon-error" v-else></i>
        </template>
        <template slot="level" slot-scope="scope">
          <el-tag v-if="scope.row.level === 1">一级</el-tag>
          <el-tag type="success" v-if="scope.row.level === 2">二级</el-tag>
          <el-tag type="warning" v-if="scope.row.level === 3">三级</el-tag>
        </template>
        <template slot="operation" slot-scope="scope">
          <el-button
            @click="openEditRole(scope.row.id)"
            type="primary"
            icon="el-icon-edit"
            size="mini"
          >编辑</el-button>
          <el-button
            @click="deleteRoles(scope.row.id)"
            type="danger"
            icon="el-icon-delete"
            size="mini"
          >删除</el-button>
        </template>
      </tree-table>
    </el-card>

    <!-- 添加商品分类对话框 -->
    <el-dialog
      title="添加商品分类"
      :visible.sync="addCateDialogVisible"
      width="50%"
      @close="DialogClosed('addFormRef')"
    >
      <el-form ref="addFormRef" :model="addForm" label-width="20%">
        <el-form-item label="商品分类名称">
          <el-input v-model="addForm.cate_name"></el-input>
        </el-form-item>
        <el-form-item label="商品分类名称">
          <el-cascader
            v-model="addForm.parent_id"
            :options="typeTwoList"
            :props="cascaderProps"
            clearable
          ></el-cascader>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addRoleDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="addCate()">确 定</el-button>
      </span>
    </el-dialog>

    <!-- 编辑商品分类对话框 -->
    <!-- <el-dialog
      title="编辑商品分类"
      :visible.sync="addCateDialogVisible"
      width="50%"
      @close="DialogClosed('addFormRef')"
    >
      <el-form ref="addFormRef" :model="editForm" label-width="20%">
        <el-form-item label="父级ID">
          <el-input v-model="editForm.parent_id"></el-input>
        </el-form-item>
        <el-form-item label="商品分类名称">
          <el-input v-model="editForm.cate_name"></el-input>
        </el-form-item>
        <el-form-item label="商品层级">
          <el-input v-model="editForm.level"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="editRoleDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="editCate()">确 定</el-button>
      </span>
    </el-dialog>-->
  </div>
</template>

<script>
import { getListbyGET,httpPost, httpGet } from "../../../http/index";
export default {
  created() {
    this.getCateList();
  },
  data() {
    return {
      addCateDialogVisible: false,
      addForm: {},
      typeTwoList : [],
      cascaderProps:{
        expandTrigger:"hover",
        checkStrictly: true,//任意选中一级
        value:"id",
        label:"cate_name",
        children:"children"
      },
      columns: [
        {
          label: "分类名称",
          prop: "cate_name"
        },
        {
          label: "是否有效",
          type: "template",
          template: "is_delete"
        },
        {
          label: "排序",
          type: "template",
          template: "level"
        },
        {
          label: "操作",
          type: "template",
          template: "operation"
        }
      ],
      cateList: []
    };
  },
  methods: {
    async getCateList() {
      let { data: res } = await getListbyGET("categories", "");
      if (res.meta.status === 200) {
        for (let i in res.data) {
          this.cateList.push(res.data[i]);
        }
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async openAddCate() {
      this.addCateDialogVisible = true;
      let { data: res } = await httpGet("categories", "", {
        type: 2,
        pageNum: 1,
        pageSize: 5
      });
      if(res.meta.status === 200){
        for(let i in res.data){
          this.typeTwoList.push(res.data[i]);
        }
      }else{
        this.$message.error(res.meta.msg);
      }
    },
    async addCate() {
      if(!this.addForm.parent_id || this.addForm.parent_id.length ===0){
        this.addForm.level = 1;
        this.addForm.parent_id = 0;
      }else if(this.addForm.parent_id.length ===1){
        this.addForm.level = 2;
        this.addForm.parent_id = this.addForm.parent_id[0];
      }else{
        this.addForm.level = 3;
        this.addForm.parent_id = this.addForm.parent_id[1];
      }
      let { data: res } = await httpPost("categories", "", this.addForm);
      if (res.meta.status === 200) {
        this.addCateDialogVisible = false;
        this.$message.success(res.meta.msg);
        this.getCateList();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    //   监听对话框关闭清空
    async DialogClosed(FormRef) {
      this.$refs[FormRef].resetFields();
    }
  }
};
</script>

<style lang="less" scoped>
</style>