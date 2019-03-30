<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Loader;
use think\Request;

class User extends Controller
{
    //用户登录
    public function login()
    {
        $userName=$_POST["用户名"];
        $password=md5($_POST["密码"]);

        // 查询用户是否存在，存在返回用户信息
        $isUserExist=Db::table("用户")->where(["userName"=>$userName])->select();
      
        // 如果用户不存在
        if(empty($isUserExist)){
           $this->error("用户不存在");
        }

        //判断用户是否被封号
        if($isUserExist[0]["state"]=='封禁'){
            $this->error("该号已被封禁，请联系管理员");
        }

        //判断用户是否被封号
        if($isUserExist[0]["state"]=='审核中'){
            $this->error("该号正在审核中");
        }


        //比对用户密码
        if($password!=$isUserExist[0]["password"]){
            $this->error("密码错误");
        }
        else{
            Session::set('userName',$isUserExist[0]['userName']);
            Session::set('isLogin',true);
            $this->success("登录成功","index/index");
        }
    }
    
    //注销登录
    public function logout(){
        #清除Session
        Session::clear();
        $this->success("注销成功","index/index");
    }

    //用户注册
    public function register(){
        return view();
    }

    //添加用户
    public function addUser(){
        $userName=$_POST["用户名"];
        $password=$_POST["密码"];
        $confirmPassWord=$_POST["确认密码"];
        if($password!=$confirmPassWord){
            $this->error("两次输入的密码不一致");
        }

        // 查询用户是否存在，存在返回用户信息
        $isUserExist=Db::table("用户")->where(["userName"=>$userName])->select();
      
        // 如果用户已存在
        if(!empty($isUserExist)){
           $this->error("用户名已存在");
        }

        $data=[
            "userName"=>$_POST["用户名"],
             "password"=>md5($_POST["密码"]),
             "studentId"=>$_POST["学号"],
             "name"=>$_POST["姓名"],
             "QQ"=>$_POST["QQ"],
             "weChat"=>$_POST["微信"]
        ];

        $count=Db::table("用户")->insert($data);
        if($count>0){
            Session::set('userName',$_POST["用户名"]);
            Session::set('isLogin',true);
            $this->success("注册成功","index/index");
        }
        else{
            $this->error("注册失败，请重试");
        }
    }

    //用户详情
    public function userDetails(){
        //提醒登录
        $isLogin=Session::get("isLogin");
        if(!$isLogin){
            $this->error("请先登录");
        }

        //查询个人信息
        $useruserName=Session::get("userName");
        $userDetails=Db::table("用户")->find($useruserName);
        $this->assign("userDetails",$userDetails);

        return view();
    }

    //修改密码
    public function changePassword(){
        //提醒登录
        $isLogin=Session::get("isLogin");
        if(!$isLogin){
            $this->error("请先登录");
        }

        $password=$_POST["密码"];
        $confirmPassWord=$_POST["确认密码"];
        if($password!=$confirmPassWord){
            $this->error("两次输入的密码不一致");
        }

        $useruserName=Session::get("userName");

        $data=[
            "userName"=>$useruserName,
             "password"=>md5($_POST["密码"]),
        ];

        $count=Db::table("用户")->update($data);
        if($count>0){
            $this->success("修改成功","Index/index");
        }
        else{
            $this->error("修改失败，请重试");
        }
    }
}
