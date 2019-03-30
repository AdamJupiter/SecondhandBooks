<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller
{
    public function index()
    {
        return view();
    }

    //后台登录
    public function login()
    {
        $userName=$_POST["用户名"];
        $password=md5($_POST["密码"]);

        $this->userControl($userName);

        $isUserExist=Db::table("用户")->where(["userName"=>$userName])->select();
    
        //比对用户密码
        if($password!=$isUserExist[0]["password"]){
            $this->error("密码错误");
        }
        else{
            Session::set('adminUserName',$isUserExist[0]['userName']);
            Session::set('password',$isUserExist[0]['password']);
            $this->success("登录成功","usermanage/userAudit");
        }
    }

    //用户审核
    public function userAudit()
    {
        $users=Db::table("用户")->where("state='待审核'")->select();
        $this->assign("users",$users);
        return view("userAudit");
    }

    public function userControl($userName=null){
        if(empty($userName)){
            $userName=session("adminUserName");
        }
        // 查询用户是否存在，存在返回用户信息
        $isUserExist=Db::table("用户")->where(["userName"=>$userName])->select();
      
        // 如果用户不存在
        if(empty($isUserExist)){
           $this->error("用户不存在");
        }

        //判断用户是否是管理员或者超级管理员
        if(!($isUserExist[0]["rank"]=="管理员"||$isUserExist[0]["rank"]=="超级管理员")){
            $this->error("您不是管理员或超级管理员，无法进入后台");
         }

        //判断用户是否被封号
        if($isUserExist[0]["state"]=='封禁'){
            $this->error("该号已被封禁或者注册审核未通过，请联系管理员");
        }

        //判断用户是否通过审核
        if($isUserExist[0]["state"]=='审核中'){
            $this->error("该号正在审核中");
        }

    }

}
