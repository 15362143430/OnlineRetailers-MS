<?php
namespace app\api\controller;

use think\Cache;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;//验证

class Common extends Controller
{
    // protected $header;//接收请求头
    protected $request; //用来处理参数
    protected $validate; //用来验证数据/参数
    protected $params; //过滤后符合要求的参数
    protected $rules = [
        "User"  => [
            "look"          => [
                "username" => "require",
                "token"    => "require",
            ],
            "login"         => [
                "username" => "require",
                "password" => "require",
            ],
            "get_user_list" => [
                "pageNum"  => "require|number",
                "pageSize" => "require|number",
            ],
            "add_user"      => [
                "user_name" => "require",
                "password"  => "require",
                "email"     => "email",
                "mobile"    => ["require", "regex" => "/^1[34578]\d{9}$/"],
                "rid"       => "require|number"
            ],
            "user_detail"   => [
                "id" => "require|number",
            ],
            "user_edit"     => [
                "id"        => "require|number",
                "mobile"    => "require",
                "user_name" => "require",
                "email"     => "require|email",
            ],
            "user_delete"   => [
                "id" => "require|number",
            ],
        ],
        "Menu"  => [
            "get_menus" => [

            ],
            "ll"        => [
            ],
        ],
        "Role"  => [
            "role_change"    => [
                "id"  => "require|number",
                "rid" => "require|number",
            ],
            "get_right_list" => [
                "type" => "require",
            ],
            "get_role_list"  => [

            ],
            "right_delete"   => [
                "role_id"      => "require|number",
                "operation_id" => "require|number",
            ],
            "right_set"      => [
                "role_id" => "require|number",
                "id_arr"  => "require|array",
            ],
            "role_detail"    => [
                "id" => "require|number",
            ],
            "role_edit"      => [
                "id"        => "require|number",
                "role_name" => "require|chsAlphaNum",
            ],
            "role_delete"    => [
                "id" => "require|number",
            ],
            "role_add"       => [
                "role_name" => "require|chsAlphaNum",
            ],
            "role_all"=>[

            ]
        ],
        "Goods" => [
            "get_categories_list" => [
                "type"     => "number",
                "pageNum"  => "require|number",
                "pageSize" => "require|number",
            ],
            "categories_add"=>[
                "parent_id"=>"require|number",
                "cate_name"=>"require|chsAlphaNum",
                "level"=>"require|number"
            ],
            "categories_detail"=>[
                "id"=>"require|number",
                "level"=>"require|number"
            ],
            "categories_edit"=>[
                "id"=>"require|number",
                "cate_name"=>"require|chsAlphaNum",
                "level"=>"require|number"
            ],
            "categories_delete"=>[
                "id"=>"require|number",
                "level"=>"require|number"
            ]
        ],
    ];
    protected function _initialize()
    {
        parent::_initialize();
        // $this->header = getallheaders();
        $this->request = Request::instance();
        //涉及到file上传走try，没有file则报错走catch
        try {
            $this->check_params($this->request->param(true));
        } catch (\Throwable $th) {
            $this->check_params($this->request->param());
        }
        $this->check_rights($this->params);
    }

    public function check_token($username, $token)
    {
        // dump($this->header);die;
        $redis_token = Cache::get($username . "_token");
        // dump($username);
        // dump($token);
        // dump($redis_token);die;
        if ($token !== $redis_token) {
            $this->return_msg(401, "token过期了");
        }
    }

    // $exist是0表示注册，1表示登录
    public function check_exist($username, $exist)
    {
        $username_res = db("user")->where("username", $username)->find();
        switch ($exist) {
            case 0:
                if ($username_res) {
                    $this->return_msg(400, "此用户名已被占用");
                }
                break;
            case 1:
                if (!$username_res) {
                    $this->return_msg(400, "此用户名不存在");
                }
                break;
        }
    }

    public function check_params($arr)
    {
        //规则，后面为调用的控制器和方法名称
        $rule = $this->rules[$this->request->controller()][$this->request->action()];
        // 验证规则并返回错误
        $this->validate = new Validate($rule);
        if (!$this->validate->check($arr)) {
            return $this->return_msg(400, $this->validate->getError());
        }
        $this->params = $arr;
    }

    public function check_rights($arr)
    {
        if (isset($arr["method_name"]) && $arr["method_name"] !== "getMenuList"  && $arr["method_name"] !== "") {
            $res = db("power")->alias("p")
                              ->join("api_menu_operation o", "o.id=p.operation_id")
                              ->where(["p.role_id"=>$arr["roleID"],"o.method_name"=>$arr["method_name"],"p.is_delete"=>0,"o.is_delete"=>0])
                              ->find();
            if ($res) {
                unset($this->params["roleID"]);
                unset($this->params["method_name"]);
            } else {
                $res_name = db("menu_operation")->where("method_name", $arr["method_name"])
                                                ->field("auth_name")
                                                ->find();
                $this->return_msg(4001, "你并没有".$res_name["auth_name"]."的权限哦");
            }
        }
        // unset($this->params["role_id"]);
    }

    public function return_msg($status, $msg = "", $data = [])
    {
        $return_data = ["data" => $data, "meta" => ["msg" => $msg, "status" => $status]];
        echo json_encode($return_data, JSON_UNESCAPED_UNICODE);
        die; //一定要die哦
    }
}
