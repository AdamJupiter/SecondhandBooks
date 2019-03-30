<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;

class Usermanage extends Controller
{

    //用户审核
    public function userAudit()
    {
        $count = Db::table('用户')->where(["state"=>'待审核'])->count();//计算总页面
        $users=Db::table("用户")->where(["state"=>'待审核'])->paginate(10,$count);
        // 获取分页显示
        $page = $users->render();
        $this->assign("users",$users);
        $this->assign("count",$count);
        $this->assign('page', $page);
        return view("Usermanage\userAudit");
    }

    //通过审核
    public function passAudit($username=0)
    {
        $count=Db::table("用户")->where("username=$username")->update(["state"=>"1"]);
        if($count>0){
            $this->success("审核成功","Usermanage\userAudit");
        }
        else{
            $this->error("审核失败,请联系开发人员");
        }
        
    }

    //删除用户
    public function deleteUser($username=0)
    {
        $count=Db::table("用户")->where("username=$username")->delete();
        if($count>0){
            $this->success("删除成功","Usermanage\userAudit");
        }
        else{
            $this->error("删除失败,请联系开发人员");
        }
    }

    //权限管理
    public function userRankManage()
    {
        return view();
    }

    //查询用户权限和信息
    public function userRankInfo()
    {
        $userName=$_POST['username'];
        $userInfo=Db::table("用户")->where(["userName"=>$userName])->select();
        if(empty($userInfo)){
            $this->error("查询的用户不存在");
        }
        $this->assign("userInfo",$userInfo[0]);
        return view();
    }

    //管理管理员
    public function managerManage()
    {

        $count = Db::table('用户')->where("rank=1")->count();//计算总页面
        $managers=Db::table("用户")->where("rank=1")->paginate(10,$count);
        // 获取分页显示
        $page = $managers->render();
        $this->assign("managers",$managers);
        $this->assign("count",$count);
        $this->assign('page', $page);
        return view("Usermanage\managerManage");
    }

    //删除管理员
    public function removeManager($username=0)
    {
        $data=[
            "rank"=>0
        ];
        $count=Db::table("用户")->where("username=$username")->update($data);
        if($count>0){
            $this->success("移除管理员身份成功","Usermanage\managerManage");
        }
        else{
            $this->error("移除管理员身份失败,请联系开发人员");
        }
    }

    //添加管理员
    public function addManager($username)
    {
        $userInfo=Db::table("用户")->find($username);

        if(empty($userInfo)){
            $this->error("用户不存在");
        }

        $data=[
            "rank"=>1
        ];
        $count=Db::table("用户")->where("username=$username")->update($data);
        if($count>0){
            $this->success("添加管理员成功","Usermanage\managerManage");
        }
        else{
            $this->error("添加管理员失败,请联系开发人员");
        }
    }

    //添加管理员
    public function passwordServers()
    {
       return view();
    }

    //重置密码为123456
    public function resetUserPassword(){
        $userName=$_POST["username"];
        $userInfo=Db::table("用户")->where(["userName"=>$userName])->select();
        if(empty($userInfo)){
            $this->error("用户不存在");
        }

        $data=[
            "password"=>md5("123456")
        ];

        $count=Db::table("用户")->where(["userName"=>$userName])->update($data);
        if($count>0){
            $this->success("密码重置成功");
        }
        else{
            $this->error("密码重置失败,请重试或联系开发人员");
        }
    }

    //封禁准备
    public function userProhibition(){
        return view();
    }

    //用户封禁
    public function userProhibitionSure(){
        $userName=$_POST["username"];
        $userInfo=Db::table("用户")->where("username=$userName")->select();
        if(empty($userInfo)){
            $this->error("用户不存在");
        }
        if($userInfo[0]["state"]=='封禁'){
            $this->error("用户已在封禁中");
        }
      
        $data=[
            "state"=>"封禁"
        ];

        $count=Db::table("用户")->where("username=$userName")->update($data);
        if($count>0){
            $this->success("用户封禁成功");
        }
        else{
            $this->error("用户封禁失败,请重试或联系开发人员");
        }
    }

    //解封准备
    public function userUnprohibition(){
        return view();
    }

    //用户解封
    public function userUnprohibitionSure(){
        $userName=$_POST["username"];
        $userInfo=Db::table("用户")->where("username=$userName")->select();
        if(empty($userInfo)){
            $this->error("用户不存在");
        }
        if($userInfo[0]["state"]!='封禁'){
            $this->error("用户不在封禁中");
        }
      
        $data=[
            "state"=>"正常"
        ];

        $count=Db::table("用户")->where("username=$userName")->update($data);
        if($count>0){
            $this->success("用户解封成功");
        }
        else{
            $this->error("用户解封失败,请重试或联系开发人员");
        }
    }
}
