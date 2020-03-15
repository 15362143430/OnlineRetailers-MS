<template>
  <div>
    <!-- 面包屑导航 -->
    <Breadcrumb titleTop="权限管理" titleBottom="角色列表"></Breadcrumb>

    <!-- 卡片 -->
    <el-card class="box-card">
      <el-row :gutter="20">
        <el-col :span="5">
          <el-button type="primary" @click="addRoleDialogVisible = true">添加角色</el-button>
        </el-col>
      </el-row>
      <el-table :data="roleList" border stripe style="width: 100%">
        <!-- 展开列 -->
        <el-table-column type="expand">
          <template slot-scope="scope">
            <!-- <pre style="font-size:6px">
              {{scope.row}}
            </pre>-->
            <el-row
              :class="['borderBottom',i1 === 0?'borderTop':'','vcenter']"
              v-for="(item1,i1) in scope.row.children"
              :key="item1.id"
            >
              <!-- 渲染一级权限 -->
              <el-col :span="5">
                <el-tag>{{item1.authName}}</el-tag>
                <i class="el-icon-caret-right"></i>
              </el-col>
              <!-- 渲染二级和三级权限 -->
              <el-col :span="19">
                <el-row
                  :class="[i2 === 0?'':'borderTop','vcenter']"
                  v-for="(item2,i2) in item1.children"
                  :key="item2.id"
                >
                  <!-- 渲染二级权限 -->
                  <el-col :span="6">
                    <el-tag type="success">{{item2.authName}}</el-tag>
                    <i class="el-icon-caret-right"></i>
                  </el-col>
                  <!-- 渲染三级权限 -->
                  <el-col :span="18">
                    <el-tag
                      type="warning"
                      v-for="item3 in item2.children"
                      :key="item3.id"
                      closable
                      @close="deleteRightByID(scope.row.id,item3.id)"
                    >{{item3.auth_name}}</el-tag>
                  </el-col>
                </el-row>
              </el-col>
            </el-row>
          </template>
        </el-table-column>
        <!-- 索引列 -->
        <el-table-column type="index" label="#"></el-table-column>
        <el-table-column prop="role_name" label="角色名称" width="180"></el-table-column>
        <el-table-column prop="role_remark" label="角色描述"></el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
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
            <el-button
              @click="openSetRight(scope.row)"
              type="warning"
              icon="el-icon-setting"
              size="mini"
            >分配权限</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 添加角色对话框 -->
    <el-dialog
      title="添加角色"
      :visible.sync="addRoleDialogVisible"
      width="50%"
      @close="DialogClosed('addFormRef')"
    >
      <el-form ref="addFormRef" :rules="addFormRules" :model="addForm" label-width="20%">
        <el-form-item label="角色名称" prop="role_name">
          <el-input v-model="addForm.role_name"></el-input>
        </el-form-item>
        <el-form-item label="角色描述" prop="role_remark">
          <el-input v-model="addForm.role_remark"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addRoleDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="addRole()">确 定</el-button>
      </span>
    </el-dialog>

    <!-- 编辑角色窗口 -->
    <el-dialog
      title="编辑角色"
      :visible.sync="editRoleDialogVisible"
      width="50%"
      @close="DialogClosed('editFormRef')"
    >
      <el-form ref="editFormRef" :rules="editFormRules" :model="editForm" label-width="70px">
        <el-form-item label="角色名" prop="role_name">
          <el-input v-model="editForm.role_name"></el-input>
        </el-form-item>
        <el-form-item label="角色描述" prop="role_remark">
          <el-input v-model="editForm.role_remark"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="editRoleDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="editRole()">确 定</el-button>
      </span>
    </el-dialog>

    <!-- 分配权限dialog -->
    <el-dialog
      title="分配权限"
      :visible.sync="setRightDialogVisible"
      width="50%"
      @close="setRightDialogClose()"
    >
      <el-tree
        ref="treeRef"
        :data="rightList"
        :props="treeProps"
        show-checkbox
        node-key="id"
        :default-checked-keys="defKeys"
        default-expand-all
      ></el-tree>
      <span slot="footer" class="dialog-footer">
        <el-button @click="setRightDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="SetRight()">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import Breadcrumb from "../../Common/Breadcrumb";
import { getList, httpDelete, httpPost } from "../../../http/index";
export default {
  components: { Breadcrumb },
  created() {
    this.getRoleList();
  },
  data() {
    return {
      roleList: [],
      rightList: [],
      treeProps: {
        label: "auth_name",
        children: "children"
      },
      defKeys: [],
      role_id_set: "",
      role_id_edit: "",
      setRightDialogVisible: false,
      editRoleDialogVisible: false,
      addRoleDialogVisible: false,
      addForm: {
        role_name: "",
        role_remark: ""
      },
      editForm: {
        role_name: "",
        role_remark: ""
      },
      addFormRules: {
        role_name: [
          { required: true, message: "请输入角色名称", trigger: "blur" },
          {
            min: 3,
            max: 10,
            message: "角色名称在3到10个字符之间",
            trigger: "blur"
          }
        ]
      },
      editFormRules: {
        role_name: [
          { required: true, message: "请输入角色名称", trigger: "blur" },
          {
            min: 3,
            max: 10,
            message: "角色名称在3到10个字符之间",
            trigger: "blur"
          }
        ]
      }
    };
  },
  methods: {
    async getRoleList() {
      let { data: res } = await getList("roles", "getRoleList", {});
      if (res.meta.status == 200) {
        this.roleList = res.data;
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    // 根据id删除rights
    async deleteRightByID(role_id, operation_id) {
      let { data: res } = await httpDelete(
        this,
        "role/right_delete",
        "deleteRightByID",
        {
          role_id: role_id,
          operation_id: operation_id
        }
      );
      if (res.meta.status == 200) {
        this.$message.success(res.meta.msg);
        this.getRoleList();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    // 打开分配权限dialog
    async openSetRight(role) {
      this.role_id_set = role.id;
      let { data: res } = await httpPost("rights", "", { type: "tree" });
      if (res.meta.status === 200) {
        this.rightList = res.data;
      } else {
        this.$message.error(res.meta.msg);
      }
      this.getLeafKeys(role, this.defKeys);
      this.setRightDialogVisible = true;
    },
    setRightDialogClose() {
      this.defKeys = [];
    },
    // 获取角色下所有三级权限的id
    getLeafKeys(node, arr) {
      let node_children = node.children;
      for (let i in node_children) {
        for (let j in node_children[i].children) {
          for (let k in node_children[i].children[j].children) {
            arr.push(node_children[i].children[j].children[k].id * 1000000);
          }
        }
      }
    },
    async SetRight() {
      let arr = [...this.$refs.treeRef.getCheckedKeys()];
      let { data: res } = await httpPost("role/right_set", "SetRight", {
        role_id: this.role_id_set,
        id_arr: arr
      });
      if (res.meta.status === 200) {
        this.setRightDialogVisible = false;
        this.$message.success(res.meta.msg);
        this.getRoleList();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async deleteRoles(role_id) {
      let { data: res } = await httpDelete(
        this,
        "role/role_delete",
        "deleteRoles",
        { id: role_id }
      );
      if (res.meta.status === 200) {
        this.$message.success(res.meta.msg);
        this.getRoleList();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async openEditRole(role_id) {
      this.editRoleDialogVisible = true;
      let { data: res } = await httpPost("role/detail", "", { id: role_id });
      if (res.meta.status === 200) {
        this.editForm = res.data;
      } else {
        this.$message.error(res.meta.msg);
      }
      this.role_id_edit = role_id;
    },
    async editRole() {
      let editForm = this.editForm;
      editForm.id = this.role_id_edit;
      let { data: res } = await httpPost(
        "role/role_edit",
        "editRole",
        editForm
      );
      if (res.meta.status === 200) {
        this.editRoleDialogVisible = false;
        this.$message.success(res.meta.msg);
        this.getRoleList();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    async addRole() {
      let { data: res } = await httpPost(
        "role/role_add",
        "addRole",
        this.addForm
      );
      if (res.meta.status === 200) {
        this.addRoleDialogVisible = false;
        this.$message.success(res.meta.msg);
        this.getRoleList();
      } else {
        this.$message.error(res.meta.msg);
      }
    },
    //   监听对话框关闭清空
    DialogClosed(FormRef) {
      this.$refs[FormRef].resetFields();
    }
  }
};
</script>

<style lang="less" scoped>
.el-tag {
  margin: 7px;
}
.borderTop {
  border-top: 1px solid #eee;
}
.borderBottom {
  border-bottom: 1px solid #eee;
}
.vcenter {
  display: flex;
  align-items: center;
}
</style>