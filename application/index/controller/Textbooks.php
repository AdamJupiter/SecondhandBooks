<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\File;
use think\Request;
use think\Session;

class TextBooks extends Controller
{
    public function index()
    {
     //查询教科书书并返回
       $count=Db::table("书")->where(["type"=>"教科书","state"=>"在售"])->count();
       $books=Db::table("书")->where(["type"=>"教科书","state"=>"在售"])->paginate(27,$count,['query' =>request()->param()]);
       $page = $books->render();
       $this->assign("books",$books);
       $this->assign("count",$count);
       $this->assign('page', $page);

       //查询学院信息并返回
       $academy=Db::table("学院")->select();
       $this->assign('academy', $academy);
       return view();
    }

    public function salebook()
    {
        //提醒登录
        $isLogin=Session::get("isLogin");
        if(!$isLogin){
            $this->error("请先登录");
        }

        //查询学院信息并返回
       $academy=Db::table("学院")->select();
       $this->assign('academy', $academy);

       //查询书状态信息并返回
       $bookstate=Db::table("书状态")->select();
       $this->assign('bookstate', $bookstate);

        return view();
    }

    public function addbook(Request $request)
    {
        //提醒登录
        $isLogin=Session::get("isLogin");
        if(!$isLogin){
            $this->error("请先登录");
        }

        $file = $request->file("uploadImage");
        if(empty($file)){
            $this->error("请选择需要上传的图片");
        }

        if(empty($file)) {  
            $this->error("请选择上传图片");  
        }  
        $dir="static/bookImages";
        $info = $file->move($dir); 
        if ($info) { 
            $bookImageUrl=(date("Ymd",time()))."/".($info->getFilename()); 
        } else { 
        $this->error($file->getError()); 
        } 

        $imgurl = "D:\wamp\www\SecondHandBooks\public\static\bookImages\\".$bookImageUrl;
        $imgurl=str_replace("/","\\",$imgurl);
        $image = \think\Image::open($imgurl);
        $image->thumb(250, 340,1)->save($imgurl);//生成缩略图、删除原图

        //生成用户的书id
        $userMaxIdBook=Db::table("书")->where(["userName"=>Session::get('userName')])->order('id','desc')->limit(1)->select();
        if(empty($userMaxIdBook)){
            $newBookId=1;
        }
        else{
            $newBookId=$userMaxIdBook[0]["id"]+1;
        }

        $data =[
            "userName"=> Session::get('userName'),
            "id"=>$newBookId,
            "bookname"=>$_POST["书名"],
            "degree"=>$_POST["新旧程度"],
            "type"=>$_POST["类型"],
            "academy"=>$_POST["所属学院"],
            "original_price"=>$_POST["原价"],
            "now_price"=>$_POST["售价"],
            "sellerWeChat"=>$_POST["微信"],
            "main_image"=>$bookImageUrl,
            "remarks"=>$_POST["备注"],
        ];
        $count=Db::table("书")->insert($data);
        if($count>0){
            $this->success("提交成功，请等待审核","textbooks/index");
        }
        else{
            $this->error("提交失败");
        }
    }

    //我发布的
    public function myReleased(){
        //提醒登录
        $isLogin=Session::get("isLogin");
        if(!$isLogin){
            $this->error("请先登录");
        }

        //查询我发布的图书
        $userName=Session::get("userName");
        $books=Db::table("书")->where(["type"=>'教科书',"userName"=>$userName])->select();
        $this->assign("books",$books);
        
        return view();
        
    }

    //书详情
    public function bookdetails($id,$userName){
        $bookDetails=Db::table("书")->where(["id"=>$id,"userName"=>$userName])->select();
        $this->assign("bookDetails",$bookDetails[0]);
        return view();
    }

    public function sortBookByAcademy($academy){
        //查询教科书书并返回
       $count=Db::table("书")->where(["type"=>"教科书","state"=>"在售","academy"=>$academy])->count();
       $books=Db::table("书")->where(["type"=>"教科书","state"=>"在售","academy"=>$academy])->paginate(27,$count,['query' =>request()->param()]);
       $page = $books->render();
       $this->assign("books",$books);
       $this->assign("count",$count);
       $this->assign('page', $page);

       //查询学院信息并返回
       $academy=Db::table("学院")->select();
       $this->assign('academy', $academy);
       return view("index");
    }

}
