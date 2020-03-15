<?php
namespace app\api\controller;

class Role extends Common
{
    public function role_change()
    {
        // echo "role_change";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        $role_id = $data["rid"] === "1" ? "2" : "1";
        $res     = db("user")->where("id", $data["id"])->setField("rid", $role_id);
        if ($res !== false) {
//返回1和0都是修改成功
            $this->return_msg(200, "角色切换成功");
        } else {
            $this->return_msg(400, "角色切换失败");
        }
    }

    public function get_right_list()
    {
        // echo "get_rights_list";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        $field_one      = "id,auth_name,level,path";
        $field_two      = ["u.id", "u.father_id", "u.auth_name", "u.level", "u.path"];
        $field_three    = ["o.id", "o.father_id", "o.auth_name", "o.level", "u.path"];
        $join_one_two   = [["api_menu_top t", "t.id = u.father_id"]];
        $join_two_three = [["api_menu_up u", "o.father_id = u.id"]];
        $res_one        = db("menu_top")->field($field_one)->where("is_delete", 0)->select();
        $res_two        = db("menu_up")->alias("u")
            ->join($join_one_two)
            ->where("u.is_delete", 0)
            ->field($field_two)
            ->select();
        $res_three = db("menu_operation")->alias("o")
            ->join($join_two_three) //注意join的顺序
            ->join($join_one_two)
            ->where("o.is_delete", 0)
            ->field($field_three)
            ->select();
        if (!$res_one || !$res_two || !$res_three) {
            $this->return_msg(400, "获取权限列表失败");
        }
        if ($data["type"] == "list") {
            $res = array_merge($res_one, $res_two, $res_three);
            shuffle($res);
            $this->return_msg(200, "获取权限列表成功", $res);
        }
        if ($data["type"] == "tree") {
            for ($i = 0; $i < count($res_one); $i++) {
                $res_one[$i]["children"] = [];
                for ($j = 0; $j < count($res_two); $j++) {
                    $res_two[$j]["children"] = [];
                    for ($k = 0; $k < count($res_three); $k++) {
                        if ($res_three[$k]["father_id"] === $res_two[$j]["id"]) {
                            if ($res_three[$k]["id"] / 1000000 < 1) {
                                $res_three[$k]["id"] = $res_three[$k]["id"] * 1000000;
                            }
                            array_push($res_two[$j]["children"], $res_three[$k]);
                        }
                    }
                    if ($res_two[$j]["father_id"] === $res_one[$i]["id"]) {
                        $res_two[$j]["id"] = $res_two[$j]["id"] * 1000;
                        array_push($res_one[$i]["children"], $res_two[$j]);
                    }
                }
            }
            $this->return_msg(200, "获取权限列表成功", $res_one);
        }
    }

    public function get_role_list()
    {
        // echo "get_role_list";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        $join = [["api_menu_up u", "u.father_id = t.id"]];
        $res  = db("menu_top")->alias("t")
            ->join($join)
            ->where("t.is_delete", 0)
            ->field(["t.id", "t.auth_name as authName", "t.path", "u.id as u_id", "u.auth_name", "u.path as u_path"])
            ->select();
        if ($res) {
            $filter_res                = [];
            $filter_res[0]["id"]       = $res[0]["id"];
            $filter_res[0]["authName"] = $res[0]["authName"];
            $filter_res[0]["path"]     = $res[0]["path"];
            $filter_res[0]["children"] = [["id" => ($res[0]["id"] * 100 + $res[0]["u_id"]),
                "authName"                          => $res[0]["auth_name"],
                "path"                              => $res[0]["u_path"],
                "children"                          => []]];
            // dump($filter_res);die;
            for ($i = 1; $i < count($res); $i++) {
                $flag     = 1;
                $children = [];
                for ($j = 0; $j < count($filter_res); $j++) {
                    if ($res[$i]["id"] === $filter_res[$j]["id"]) {
                        $children["id"]       = $res[$i]["id"] * 100 + $res[$i]["u_id"];
                        $children["authName"] = $res[$i]["auth_name"];
                        $children["path"]     = $res[$i]["u_path"];
                        $children["children"] = [];
                        array_push($filter_res[$j]["children"], $children);
                        $flag = 0;
                        break;
                    }
                }
                if ($flag) {
                    $children["id"]       = $res[$i]["id"];
                    $children["authName"] = $res[$i]["authName"];
                    $children["path"]     = $res[$i]["path"];
                    $children["children"] = [["id" => ($res[$i]["id"] * 100 + $res[$i]["u_id"]),
                        "authName"                     => $res[$i]["auth_name"],
                        "path"                         => $res[$i]["u_path"],
                        "children"                     => []]];
                    array_push($filter_res, $children);
                }
            }
            $operation_join_one = [["api_menu_operation o", "o.id=p.operation_id"]];
            $operation_join_two = [["api_role r", "r.id=p.role_id"]];
            $operation_field    = ["r.id", "r.role_name", "r.role_remark", "o.father_id", "o.id as o_id", "o.auth_name"];
            $operation_res      = db("power")->alias("p")
                ->join($operation_join_one)
                ->join($operation_join_two)
                ->field($operation_field)
                ->where(["p.is_delete" => 0, "r.is_delete" => 0, "o.is_delete" => 0])
                ->select();
            // $this->return_msg(200,"haha",$operation_res);
            if ($operation_res) {
                $operation_res_length                   = count($operation_res);
                $filter_operation_res                   = [];
                $filter_operation_res[0]["id"]          = $operation_res[0]["id"];
                $filter_operation_res[0]["role_name"]   = $operation_res[0]["role_name"];
                $filter_operation_res[0]["role_remark"] = $operation_res[0]["role_remark"];
                $filter_operation_res[0]["children"]    = $filter_res;
                for ($j = 1; $j < $operation_res_length; $j++) {
                    $flag = 0;
                    for ($k = 0; $k < count($filter_operation_res); $k++) {
                        if ($operation_res[$j]["id"] === $filter_operation_res[$k]["id"]) {
                            $flag = 1;
                        }
                    }
                    if (!$flag) {
                        $filter_operation_res_mumber = [
                            "id"          => $operation_res[$j]["id"],
                            "role_name"   => $operation_res[$j]["role_name"],
                            "role_remark" => $operation_res[$j]["role_remark"],
                            "children"    => $filter_res,
                        ];
                        array_push($filter_operation_res, $filter_operation_res_mumber);
                    }
                }
                $filter_operation_res_length = count($filter_operation_res);
                for ($q = 0; $q < $filter_operation_res_length; $q++) {
                    $filter_operation_res_children = $filter_operation_res[$q]["children"];
                    for ($r = 0; $r < $operation_res_length; $r++) {
                        if ($filter_operation_res[$q]["id"] === $operation_res[$r]["id"]) {
                            for ($y = 0; $y < count($filter_operation_res_children); $y++) {
                                for ($x = 0; $x < count($filter_operation_res_children[$y]["children"]); $x++) {
                                    // dump($filter_operation_res_children[$y]["children"][$x]["id"]);
                                    if ($filter_operation_res_children[$y]["children"][$x]["id"] % 100 === $operation_res[$r]["father_id"]) {
                                        $operation_do = [
                                            "id"        => $operation_res[$r]["o_id"],
                                            "auth_name" => $operation_res[$r]["auth_name"],
                                        ];
                                        // dump($filter_operation_res_children[$y]["children"][$x]["children"]);
                                        array_push($filter_operation_res[$q]["children"][$y]["children"][$x]["children"], $operation_do);
                                    }
                                }
                            }
                        }
                    }
                }

                $this->return_msg(200,"获取列表成功", $filter_operation_res);
            } else {
                $this->return_msg(400, "获取列表失败");
            }
        } else {
            $this->return_msg(400, "获取列表失败");
        }
    }

    public function right_delete()
    {
        // echo "right_delete";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        $field = ["role_id" => $data["role_id"], "operation_id" => $data["operation_id"]];
        $res   = db("power")->where($field)->setField("is_delete", 1);
        if ($res !== false) {
//返回1和0都是修改成功
            $this->return_msg(200, "权限删除成功");
        } else {
            $this->return_msg(400, "权限删除失败");
        }
    }

    public function right_set()
    {
        // echo "right_set";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        $data_id_arr        = $data["id_arr"];
        $data_id_arr_filter = [];
        for ($i = 0; $i < count($data_id_arr); $i++) {
            if ($data_id_arr[$i] / 1000000 >= 1) {
                array_push($data_id_arr_filter, $data_id_arr[$i] / 1000000);
            }
        }
        $res_in = db("power")->where("role_id=1 and operation_id in (" . implode(",", $data_id_arr_filter) . ")")->setField("is_delete", 0);
        if ($res_in !== false) {
            $res_not = db("power")->where("role_id=1 and operation_id not in (" . implode(",", $data_id_arr_filter) . ")")->setField("is_delete", 1);
            if ($res_not !== false) {
                $this->return_msg(200, "分配权限成功");
            } else {
                $this->return_msg(400, "分配权限失败");
            }
        } else {
            $this->return_msg(400, "分配权限失败");
        }
    }

    public function role_edit()
    {
        // echo "role_edit";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        unset($data["username"]);
        unset($data["token"]);
        $res = db("role")->where("id", $data["id"])->update($data);
        if ($res !== false) {
            $this->return_msg(200, "角色信息修改成功");
        } else {
            $this->return_msg(400, "角色信息修改失败");
        }
    }

    public function role_delete()
    {
        // echo "role_delete";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        $res = db("role")->where("id", $data["id"])->setField("is_delete", 1);
        if ($res !== false) {
            $this->return_msg(200, "角色删除成功");
        } else {
            $this->return_msg(400, "角色删除失败");
        }
    }

    public function role_detail()
    {
        // echo "role_detail";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        $res = db("role")->where("id", $data["id"])->find();
        if ($res) {
            $this->return_msg(200, "获取角色信息成功", $res);
        } else {
            $this->return_msg(400, "获取角色信息失败");
        }
    }

    public function role_add()
    {
        // echo "role_add";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        unset($data["username"]);
        unset($data["token"]);
        $res = db("role")->insertGetId($data);
        if ($res) {
            $operation_res = db("menu_operation")->where("is_delete", 0)->field("id")->select();
            if ($operation_res) {
                $power_arr            = [];
                $operation_res_length = count($operation_res);
                for ($i = 0; $i < $operation_res_length; $i++) {
                    array_push($power_arr, ["role_id" => $res, "operation_id" => $operation_res[$i]["id"]]);
                }
                $add_res = db("power")->insertAll($power_arr);
                if ($add_res) {
                    $this->return_msg(200, "角色添加成功");
                } else {
                    $this->return_msg(400, "角色添加失败");
                }
            } else {
                $this->return_msg(400, "角色添加失败");
            }
        } else {
            $this->return_msg(400, "角色添加失败");
        }

    }

    public function role_all(){
        // echo "role_all";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"],$data["token"]);
        // 查询数据库
        $res = db("role")->where("is_delete",0)
                         ->field(["role_name as label","id as value"])
                         ->select();
        if($res){
            $this->return_msg(200,"获取成功",$res);
        }
        $this->return_msg(400,"获取失败");
    }
}
