<?php
namespace app\api\controller;
use think\Cache;
class User extends Common{
    public function look(){
        // $data = ["id"=>1,"name"=>"lin"];
        // $this->return_msg(200,"登录成功",$data);
        // $username_res = db("user")->where("username","admin")->find();
        // dump($username_res);
        $data = $this->params;
        $this->check_token($data["username"],$data["token"]);
        $data = Cache::get("admin_token");
        // dump($data);
        $this->return_msg(200,$data);
    }

    public function login(){
        // 接收参数
        $data = $this->params;
        // dump($data);
        // 判断用户名存在否
        $this->check_exist($data["username"],1);
        // 查询数据库
        $pwd = db("user")->where("username",$data["username"])->value("password");
        if($pwd == $data["password"]){
            $field = "id,rid,username,mobile,email";
            $res = db("user")->where("username",$data["username"])->field($field)->find();
            // dump($res);
            $user_token = time().$data["password"];
            $redis_token = Cache::set($data["username"]."_token",md5($user_token),3600);
            // dump($redis_token);
            if($redis_token){
                $res["token"] = Cache::get($data["username"]."_token");
                $this->return_msg(200,"登录成功",$res);
            }else{
                $this->return_msg(400,"服务器炸了");
            }
        }else{
            $this->return_msg(400,"密码不对哦");
        }        
    }

    public function get_user_list(){
        // echo "get_user_list";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"],$data["token"]);
        // 查询数据库
        $field = "u.id,rid,role_name,username,mobile,email,status";
        $join = [["api_role r","r.id = u.rid"]];
        if(!isset($data["query"])){
            $count = db("user")->count();
            $where = ["u.is_delete"=>0];
            $res = db("user")->alias("u")
            ->field($field)
            ->join($join)
            ->where($where)
            ->page($data["pageNum"],$data["pageSize"])
            ->select();
        }else{
            $count = db("user")->where(["username"=>$data["query"]])->count();
            $where = ["username"=>$data["query"],"u.is_delete"=>0];
            $res = db("user")->alias("u")
            ->field($field)
            ->join($join)
            ->where($where)
            ->page($data["pageNum"],$data["pageSize"])
            ->select();
        }
        // dump($count);
        if($res === false){
            $this->return_msg(400,"查询失败");
        }elseif(empty($res)){
            $this->return_msg(200,"暂无数据");
        }else{
            $return_data["users"] = $res;
            $return_data["total"] = $count;
            $return_data["pageNum"] = $data["pageNum"];
            $this->return_msg(200,"查询成功",$return_data);
        }

    }

    public function add_user(){
        // echo "add_user";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"],$data["token"]);
        // 判断用户名是不是已存在
        $this->check_exist($data["user_name"],0);
        // 查询数据库
        $data["username"] = $data["user_name"];
        unset($data["user_name"]);
        unset($data["token"]);
        $data["create_time"] = time();
        $res = db("user")->insert($data);
        if(!$res){
            $this->return_msg(400,"用户添加失败");
        }else{
            $field = "id,username,mobile,email,create_time";
            $return_data = db("user")->field($field)->where("username",$data["username"])->find();
            $this->return_msg(200,"用户添加成功",$return_data);
        }
    }

    public function user_detail(){
        // echo "user_detail";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"],$data["token"]);
        // 查询数据库
        $field = "id,rid,username,mobile,email";
        $res = db("user")->field($field)->where("id",$data["id"])->find();
        if($res){
            $this->return_msg(200,"返回信息成功",$res);
        }else{
            $this->return_msg(400,"返回信息失败");
        }
    }

    public function user_edit(){
        // echo "user_edit";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"],$data["token"]);
        // 查询数据库
        $data["username"] = $data["user_name"];
        unset($data["user_name"]);
        unset($data["token"]);
        $res = db("user")->where("id",$data["id"])->update($data);
        if($res !== false){//返回1和0都是修改成功
            $this->return_msg(200,"用户编辑成功");
        }else{
            $this->return_msg(400,"用户编辑失败");
        }
    }

    public function user_delete(){
        // echo "user_delete";
        // 接收参数
        $data = $this->params;
        // 验证token
        $this->check_token($data["username"],$data["token"]);
        // 查询数据库
        $res = db("user")->where("id",$data["id"])->setField("is_delete",1);
        if($res){
            $this->return_msg(200,"删除成功");
        }else{
            $this->return_msg(400,"删除失败");
        }
    }
}

?>