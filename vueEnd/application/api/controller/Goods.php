<?php
namespace app\api\controller;

class Goods extends Common
{
    public function get_categories_list()
    {
        // echo "get_categories_list";
        // 接收参数
        $data = $this->params;
        // 验证token
        // $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        $res_cate_2_3 = db("cate2")->alias("c21")
                ->join("api_cate2 c22", "c22.parent_id=c21.id", "LEFT")
                ->field("c21.id,c21.cate_name,c21.level,c21.is_delete,c22.id as id2,c22.cate_name as cate_name2,c22.level as level2,c22.is_delete as is_delete2")
                ->where(["c21.level" => 2])
                ->order("c21.id")
                ->select();
        // $this->return_msg(200, "", $res_cate_2_3);
        $arr_cate_2_3[$res_cate_2_3[0]["id"]] = [
                "id"        => $res_cate_2_3[0]["id"],
                "cate_name" => $res_cate_2_3[0]["cate_name"],
                "level"     => $res_cate_2_3[0]["level"],
                "is_delete" => $res_cate_2_3[0]["is_delete"],
                "children"  => [
                    [
                    "id"        => $res_cate_2_3[0]["id2"],
                    "cate_name" => $res_cate_2_3[0]["cate_name2"],
                    "level"     => $res_cate_2_3[0]["level2"],
                    "is_delete" => $res_cate_2_3[0]["is_delete2"]
                    ]
                ],

            ];
            for ($i = 1; $i < count($res_cate_2_3); $i++) {
                if (isset($arr_cate_2_3[$res_cate_2_3[$i]["id"]])) {
                    $res_cate_2_3_children = [
                            "id"        => $res_cate_2_3[$i]["id2"],
                            "cate_name" => $res_cate_2_3[$i]["cate_name2"],
                            "level"     => $res_cate_2_3[$i]["level2"],
                            "is_delete" => $res_cate_2_3[$i]["is_delete2"]
                        ];
                    array_push($arr_cate_2_3[$res_cate_2_3[$i]["id"]]["children"], $res_cate_2_3_children);
                } else {
                    if ($res_cate_2_3[$i]["cate_name2"] !== null) {
                        $arr_cate_2_3[$res_cate_2_3[$i]["id"]] = [
                            "id"        => $res_cate_2_3[$i]["id"],
                            "cate_name" => $res_cate_2_3[$i]["cate_name"],
                            "level"     => $res_cate_2_3[$i]["level"],
                            "is_delete" => $res_cate_2_3[$i]["is_delete"],
                            "children"  => [
                                [
                                "id"        => $res_cate_2_3[$i]["id2"],
                                "cate_name" => $res_cate_2_3[$i]["cate_name2"],
                                "level"     => $res_cate_2_3[$i]["level2"],
                                "is_delete" => $res_cate_2_3[$i]["is_delete2"]
                                ]
                            ]

                        ];
                    } else {
                        $arr_cate_2_3[$res_cate_2_3[$i]["id"]] = [
                            "id"        => $res_cate_2_3[$i]["id"],
                            "cate_name" => $res_cate_2_3[$i]["cate_name"],
                            "level"     => $res_cate_2_3[$i]["level"],
                            "is_delete" => $res_cate_2_3[$i]["is_delete"]
                        ];
                    }
                }
            }
        $res_cate_1_2 = db("cate1")->alias("c1")
                ->join("api_cate2 c2", "c2.parent_id=c1.id", "RIGHT")
                ->field("c1.id,c1.cate_name,c1.level,c1.is_delete,c2.id as id2,c2.cate_name as cate_name2,c2.level as level2,c2.is_delete as is_delete2")
                ->where(["c1.level" => 1])
                ->order("c1.id")
                ->select();
        $this->return_msg(200,"",$res_cate_1_2);
        $arr_cate_1_2_3[$res_cate_1_2[0]["id"]] = [
                        "id"        => $res_cate_1_2[0]["id"],
                        "cate_name" => $res_cate_1_2[0]["cate_name"],
                        "level"     => $res_cate_1_2[0]["level"],
                        "is_delete" => $res_cate_1_2[0]["is_delete"],
                        "children"  => [
                            $arr_cate_2_3[$res_cate_1_2[0]["id2"]]
                        ]
            ];
        for ($i=1; $i < count($res_cate_1_2); $i++) {
            if (isset($arr_cate_1_2_3[$res_cate_1_2[$i]["id"]])) {
                array_push($arr_cate_1_2_3[$res_cate_1_2[$i]["id"]]["children"], $arr_cate_2_3[$res_cate_1_2[$i]["id2"]]);
            } else {
                $arr_cate_1_2_3[$res_cate_1_2[$i]["id"]] = [
                        "id"        => $res_cate_1_2[$i]["id"],
                        "cate_name" => $res_cate_1_2[$i]["cate_name"],
                        "level"     => $res_cate_1_2[$i]["level"],
                        "is_delete" => $res_cate_1_2[$i]["is_delete"],
                        "children"  => [
                            $arr_cate_2_3[$res_cate_1_2[$i]["id2"]]
                        ]

                    ];
            }
        }

        $arr_cat1_2[$res_cate_1_2[0]["id"]] = [
            "id"        => $res_cate_1_2[0]["id"],
            "cate_name" => $res_cate_1_2[0]["cate_name"],
            "level"     => $res_cate_1_2[0]["level"],
            "is_delete" => $res_cate_1_2[0]["is_delete"],
            "children"  => [
                [
                "id"        => $res_cate_1_2[0]["id2"],
                "cate_name" => $res_cate_1_2[0]["cate_name2"],
                "level"     => $res_cate_1_2[0]["level2"],
                "is_delete" => $res_cate_1_2[0]["is_delete2"]
                ]
            ],

        ];
        for ($i = 1; $i < count($res_cate_1_2); $i++) {
            if (isset($arr_cate_1_2[$res_cate_1_2[$i]["id"]])) {
                $res_cate_1_2_children = [
                        "id"        => $res_cate_1_2[$i]["id2"],
                        "cate_name" => $res_cate_1_2[$i]["cate_name2"],
                        "level"     => $res_cate_1_2[$i]["level2"],
                        "is_delete" => $res_cate_1_2[$i]["is_delete2"]
                    ];
                array_push($arr_cate_1_2[$res_cate_1_2[$i]["id"]]["children"], $res_cate_1_2_children);
            } else {
                if ($res_cate_1_2[$i]["cate_name2"] !== null) {
                    $arr_cate_1_2[$res_cate_1_2[$i]["id"]] = [
                        "id"        => $res_cate_1_2[$i]["id"],
                        "cate_name" => $res_cate_1_2[$i]["cate_name"],
                        "level"     => $res_cate_1_2[$i]["level"],
                        "is_delete" => $res_cate_1_2[$i]["is_delete"],
                        "children"  => [
                            [
                            "id"        => $res_cate_1_2[$i]["id2"],
                            "cate_name" => $res_cate_1_2[$i]["cate_name2"],
                            "level"     => $res_cate_1_2[$i]["level2"],
                            "is_delete" => $res_cate_1_2[$i]["is_delete2"]
                            ]
                        ]

                    ];
                } else {
                    $arr_cate_2_3[$res_cate_2_3[$i]["id"]] = [
                        "id"        => $res_cate_2_3[$i]["id"],
                        "cate_name" => $res_cate_2_3[$i]["cate_name"],
                        "level"     => $res_cate_2_3[$i]["level"],
                        "is_delete" => $res_cate_2_3[$i]["is_delete"]
                    ];
                }
            }
        }

        $type = isset($data["type"]) ? $data["type"] : 3;
        if ($type == 3) {
            $this->return_msg(200, "获取列表成功", $arr_cate_1_2_3);
        }

        if ($type == 2) {
            $this->return_msg(200, "获取列表成功", $arr_cate_1_2);
        }
    }

    public function categories_add()
    {
        // echo "categories_add";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        unset($data["username"]);
        unset($data["token"]);
        if ($data["level"] === "1") {
            $res = db("cate1")->insert($data);
        } else {
            $res = db("cate2")->insert($data);
        }

        if (!$res) {
            $this->return_msg(400, "添加分类失败");
        }
        $this->return_msg(200, "添加分类成功");
    }

    public function categories_detail()
    {
        // echo "categories_detail";
        // 接收参数
        $data = $this->params;
        // 验证token
        // $this->check_token($data["username"],$data["token"]);
        // 查询数据库
        if ($data["level"] === "1") {
            $res = db("cate1")->where(["id"=>$data["id"],"level"=>$data["level"],"is_delete"=>0])->find();
        } else {
            $res = db("cate2")->where(["id"=>$data["id"],"level"=>$data["level"],"is_delete"=>0])->find();
        }
        if (!$res) {
            $this->return_msg(400, "查询分类信息失败");
        }
        $this->return_msg(200, "查询分类信息成功", $res);
    }

    public function categories_edit()
    {
        // echo "categories_edit";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        unset($data["username"]);
        unset($data["token"]);
        if ($data["level"] === "1") {
            $res = db("cate1")->where(["id"=>$data["id"],"level"=>$data["level"],"is_delete"=>0])->update($data);
        } else {
            $res = db("cate2")->where(["id"=>$data["id"],"level"=>$data["level"],"is_delete"=>0])->update($data);
        }
        if ($res !== false) {
            $this->return_msg(200, "编辑分类信息成功");
        }
        $this->return_msg(400, "编辑分类信息失败");
    }

    public function categories_delete()
    {
        // echo "categories_delete";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"], $data["token"]);
        // 查询数据库
        if ($data["level"] === "1") {
            $res = db("cate1")->where(["id"=>$data["id"],"level"=>$data["level"],"is_delete"=>0])->setField("is_delete", 1);
        } else {
            $res = db("cate2")->where(["id"=>$data["id"],"level"=>$data["level"],"is_delete"=>0])->setField("is_delete", 1);
        }
        if ($res !== false) {
            $this->return_msg(200, "删除分类成功");
        }
        $this->return_msg(400, "删除分类失败");
    }
}
