<template>
  <div>
    <!-- 面包屑导航 -->
    <Breadcrumb titleTop="用户管理" titleBottom="用户列表"></Breadcrumb>

    <!-- 卡片 -->
    <el-card class="box-card">
      <el-row :gutter="20">
        <el-col :span="7">
          <el-input placeholder="请输入内容" v-model="query" class="input-with-select">
            <el-button @click="searchList()" slot="append" icon="el-icon-search"></el-button>
          </el-input>
        </el-col>
        <el-col :span="5">
          <el-button type="primary" @click="addDialogVisible = true">添加用户</el-button>
        </el-col>
      </el-row>
      <el-table :data="userList" border stripe style="width: 100%">
        <el-table-column type="index" label="#"></el-table-column>
        <el-table-column prop="username" label="用户名" width="180"></el-table-column>
        <el-table-column prop="mobile" label="手机号码"></el-table-column>
        <el-table-column prop="email" label="邮箱"></el-table-column>
        <el-table-column prop="role_name" label="身份"></el-table-column>
        <el-table-column prop="is_delete" label="状态">
          <template slot-scope="scope">
            <el-switch v-model="scope.row.status"></el-switch>
          </template>
        </el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
            <el-tooltip :enterable="false" effect="dark" content="编辑用户" placement="top">
              <el-button
                @click="openEditUser(scope.row.id)"
                type="primary"
                icon="el-icon-edit"
                size="mini"
              ></el-button>
            </el-tooltip>
            <el-tooltip :enterable="false" effect="dark" content="删除用户" placement="top">
              <el-button
                @click="deleteUser(scope.row.id)"
                type="danger"
                icon="el-icon-delete"
                size="mini"
              ></el-button>
            </el-tooltip>
            <el-tooltip :enterable="false" effect="dark" content="分配角色" placement="top">
              <el-button
                @click="setRole(scope.row.id,scope.row.rid)"
                type="warning"
                icon="el-icon-setting"
                size="mini"
              ></el-button>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :page-sizes="[5, 10, 15, 20]"
        :page-size="5"
        layout="total, sizes, prev, pager, next, jumper"
        :total="total"
      ></el-pagination>
    </el-card>

    <!-- 添加用户对话框 -->
    <el-dialog
      title="添加用户"
      :visible.sync="addDialogVisible"
      width="50%"
      @close="DialogClosed('addFormRef')"
    >
      <el-form ref="addFormRef" :rules="addFormRules" :model="addForm" label-width="70px">
        <el-form-item label="用户名" prop="user_name">
          <el-input v-model="addForm.user_name"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input v-model="addForm.password"></el-input>
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="addForm.email"></el-input>
        </el-form-item>
        <el-form-item label="手机" prop="mobile">
          <el-input v-model="addForm.mobile"></el-input>
        </el-form-item>
        <el-form-item label="角色" prop="role">
          <el-select v-model="addForm.rid" placeholder="请选择">
            <el-option
              v-for="item in roleSelectList"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="addUser()">确 定</el-button>
      </span>
    </el-dialog>

    <!-- 编辑用户窗口 -->
    <el-dialog
      title="编辑用户"
      :visible.sync="editDialogVisible"
      width="50%"
      @close="DialogClosed('editFormRef')"
    >
      <el-form ref="editFormRef" :rules="editFormRules" :model="editForm" label-width="70px">
        <el-form-item label="用户名" prop="user_name">
          <el-input v-model="editForm.user_name" :disabled="true"></el-input>
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="editForm.email"></el-input>
        </el-form-item>
        <el-form-item label="手机" prop="mobile">
          <el-input v-model="editForm.mobile"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="editDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="editUser()">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import Breadcrumb from "../../Common/Breadcrumb";
import {
  getList,
  getListByQuery,
  httpPost,
  httpDelete,
  httpGet
  // UserRoleChange
} from "../../../http/index";
export default {
  components: {
    Breadcrumb
  },
  created() {
    this.getUserList();
    this.getRoleSelectList();
  },
  data() {
    //   自定义邮箱规则
    let checkEmail = (rule, value, cb) => {
      const regEmail = /^[A-Za-z0-9\u4e00-\u9fa5]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
      if (regEmail.test(value)) {
        return cb;
      }
      cb(new Error("请输入合法的邮箱"));
    };
    // 自定义手机规则
    let checkPhone = (rule, value, cb) => {
      const regPhone = /^1[3456789]\d{9}$/;
      if (regPhone.test(value)) {
        return cb;
      }
      cb(new Error("请输入合法的手机号"));
    };
    return {
      userList: [],
      editID: "",
      total: 0,
      query: "",
      pageList: {
        pageNum: 1,
        pageSize: 5
      },
      roleSelectList: [],
      addDialogVisible: false,
      editDialogVisible: false,
      addFormRules: {
        user_name: [
          { required: true, message: "请输入用户名", trigger: "blur" },
          {
            min: 3,
            max: 10,
            message: "用户名在3到10个字符之间",
            trigger: "blur"
          }
        ],
        password: [
          { required: true, message: "请输入密码", trigger: "blur" },
          { min: 3, max: 16, message: "密码在3到10个字符之间", trigger: "blur" }
        ],
        email: [
          { required: true, message: "请输入邮箱", trigger: "blur" },
          { validator: checkEmail, trigger: "blur" }
        ],
        mobile: [
          { required: true, message: "请输入手机", trigger: "blur" },
          { validator: checkPhone, trigger: "blur" }
        ],
        role: [{ required: true, message: "请选择角色", trigger: "blur" }]
      },
      editFormRules: {
        user_name: [
          { required: true, message: "请输入用户名", trigger: "blur" },
          {
            min: 3,
            max: 10,
            message: "用户名在3到10个字符之间",
            trigger: "blur"
          }
        ],
        email: [
          { required: true, message: "请输入邮箱", trigger: "blur" },
          { validator: checkEmail, trigger: "blur" }
        ],
        mobile: [
          { required: true, message: "请输入手机", trigger: "blur" },
          { validator: checkPhone, trigger: "blur" }
        ]
      },
      addForm: {
        user_name: "",
        password: "",
        email: "",
        mobile: "",
        rid:""
      },
      editForm: {
        user_name: "",
        email: "",
        mobile: ""
      }
    };
  },
  methods: {
    async getUserList() {
      const { data: res } = await getList("users", "getUserList");
      if (res.meta.status == 200) {
        this.userList = res.data.users;
        this.total = res.data.total;
        this.pageList.pageNum = res.data.pageNum;
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async getUserListByChange() {
      let { data: res } = await getListByQuery(
        "users",
        this.query,
        "",
        this.pageList
      );
      if (res.meta.status == 200) {
        this.userList = res.data.users;
        this.total = res.data.total;
        this.pageList.pageNum = res.data.pageNum;
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async openEditUser(id) {
      this.editID = id;
      this.editDialogVisible = true;
      let { data: res } = await httpPost("user/detail", "", { id: id });
      if (res.meta.status == 200) {
        this.editForm.user_name = res.data.username;
        this.editForm.mobile = res.data.mobile;
        this.editForm.email = res.data.email;
        this.editForm.rid = res.data.rid;
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async editUser() {
      let editForm = this.editForm;
      editForm.id = this.editID;
      let { data: res } = await httpPost("user/edit", "editUser", editForm);
      if (res.meta.status == 200) {
        this.$message.success(res.meta.msg);
        this.editDialogVisible = false;
        this.getUserListByChange();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async deleteUser(id) {
      let { data: res } = await httpDelete(this, "user/delete", "deleteUser", {
        id: id
      });
      if (res.meta.status == 200) {
        this.$message.success(res.meta.msg);
        this.getUserListByChange();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async setRole(id, rid) {
      console.log(id, rid);
      // let res = await UserRoleChange(this, "role/change", { id: id, rid: rid });
      // if (res.meta.status == 200) {
      //   this.$message.success(res.meta.msg);
      //   this.getUserListByChange();
      // } else {
      //   this.$message.error(res.meta.msg);
      // }
    },
    // 监听每页个数变化
    handleSizeChange(newSize) {
      this.pageList.pageSize = newSize;
      this.getUserListByChange(this.query, this.pageList);
    },
    // 监听页码变化
    async handleCurrentChange(newNum) {
      this.pageList.pageNum = newNum;
      this.getUserListByChange(this.query, this.pageList);
    },
    // 搜索
    async searchList() {
      let { data: res } = await getListByQuery(
        "users",
        this.query,
        "searchList"
      );
      if (res.meta.status == 200) {
        this.userList = res.data.users;
        this.total = res.data.total;
        this.pageList.pageNum = res.data.pageNum;
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    //   监听对话框关闭清空
    DialogClosed(FormRef) {
      this.$refs[FormRef].resetFields();
    },
    // 新增用户
    async addUser() {
      let { data: res } = await httpPost("user/add", "addUser", this.addForm);
      if (res.meta.status == 200) {
        this.$message({
          message: "添加成功",
          type: "success"
        });
        this.addDialogVisible = false;
        this.getUserListByChange();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    // 获取选择框
    async getRoleSelectList() {
      let { data: res } = await httpGet("roles", "");
      if (res.meta.status === 200) {
        this.roleSelectList = res.data;
      } else {
        this.$message.error(res.meta.msg);
      }
    }
  }
};
</script>

<style lang="less" scoped>
</style>