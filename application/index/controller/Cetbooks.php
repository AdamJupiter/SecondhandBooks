<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Db;

class Cetbooks extends Controller
{
    public function index()
    {
        $count=Db::table("书")->where(["type"=>"考研与教辅","state"=>"在售"])->count();
        $books=Db::table("书")->where(["type"=>"考研与教辅","state"=>"在售"])->paginate(27,$count);
        $page = $books->render();
        $this->assign("books",$books);
        $this->assign("count",$count);
        $this->assign('page', $page);
        return view();

        
    }

    public function myReleased(){
        //提醒登录
        $isLogin=Session::get("isLogin");
        if(!$isLogin){
            $this->error("请先登录");
        }

        //查询我发布的图书
        $userName=Session::get("userName");
        $books=Db::table("书")->where(["type"=>'考研与教辅',"userName"=>$userName])->select();
        $this->assign("books",$books);
        
        return view();
    }

    //书详情
    public function bookdetails($id,$userName){
        $bookDetails=Db::table("书")->where(["id"=>$id,"userName"=>$userName])->select();
        $this->assign("bookDetails",$bookDetails[0]);
        return view();
    }
}
