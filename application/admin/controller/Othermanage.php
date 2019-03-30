<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;

class Othermanage extends Controller
{
    //管理学院
    public function academymanage()
    {

        $count = Db::table('学院')->count();//计算总页面
        $academys=Db::table("学院")->paginate(6,$count);
        // 获取分页显示
        $page = $academys->render();
        $this->assign("academys",$academys);
        $this->assign("count",$count);
        $this->assign('page', $page);
        return view("Othermanage\academymanage");
    }

    public function toaddacademy(){
        return view();
    }

    //添加学院
    public function addAcademy(){
        $schoolName=$_POST['学院名'];

        $data=[
            "name"=> $schoolName
        ];

        $count=Db::table("学院")->insert($data);

        if($count>0){
            $this->success("添加成功","Othermanage/academymanage");
        }
        else{
            $this->error("添加失败，请重试");
        }
    }

    //删除学院
    public function deleteAcademy($name){
        dump($name);
        $count=Db::table("学院")->where("name=$name")->delete();

        if($count>0){
            $this->success("删除成功","Othermanage/academymanage");
        }
        else{
            $this->error("删除失败，请重试");
        }

    }

    
}
