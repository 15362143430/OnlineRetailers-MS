<?php
namespace app\api\controller;

use think\Db;

//验证
class Menu extends Common
{
    public function get_menus()
    {
        // echo "get_menus";
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
        // dump($res);die;
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
            $this->return_msg(200, "列表获取成功", $filter_res);
        } else {
            $this->return_msg(400, "获取列表失败");
        }
    }

    public function ll()
    {
        $info = $this->request->header();
        // dump($info);die;
        $this->return_msg(200, "heh",$info["token"]);
    }
}
