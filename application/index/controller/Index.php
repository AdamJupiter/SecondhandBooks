<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller
{
    public function index()
    {
        //查询教科书书并返回
       $textbooks=Db::table("书")->where(["type"=>"教科书","state"=>"在售"])->limit(3)->select();
       $this->assign("textbooks",$textbooks);

       //查询在售课外书并返回
       $extrabooks=Db::table("书")->where(["type"=>"课外书","state"=>"在售"])->limit(3)->select();
       $this->assign("extrabooks",$extrabooks);

       //查询考研与四六级版块的书并返回
       $cetbooks=Db::table("书")->where(["type"=>"考研与教辅","state"=>"在售"])->limit(3)->select();
       $this->assign("cetbooks",$cetbooks);

        return view();
    }

    public function searchBooks(){
        //查搜索图书
        $key=$_POST["key"];
        $count=Db::table("书")->where(["state"=>"在售"])->where('bookname','like',"%".$key."%")->count();
        $books=Db::table("书")->where(["state"=>"在售"])->where('bookname','like',"%".$key."%")->select();
        $this->assign("key",$key);
        $this->assign("count",$count);
        $this->assign("books",$books);
        return view();
    }

    //书详情
    public function bookdetails($id,$userName){
        $bookDetails=Db::table("书")->where(["id"=>$id,"userName"=>$userName])->select();
        $this->assign("bookDetails",$bookDetails[0]);
        return view();
    }

    //我发布的所有书
    public function myReleased(){
        //提醒登录
        $isLogin=Session::get("isLogin");
        if(!$isLogin){
            $this->error("请先登录");
        }
        $userName=Session::get("userName");
        $count=Db::table("书")->where(["userName"=>$userName])->count();
        $books=Db::table("书")->where(["userName"=>$userName])->paginate(4,$count);
        $page = $books->render();
        $this->assign("books",$books);
        $this->assign("count",$count);
        $this->assign('page', $page);
        return view();
    }

     //删除书籍
     public function deleteBook($id=0)
     {
          //提醒登录
        $isLogin=Session::get("isLogin");
        if(!$isLogin){
            $this->error("请先登录");
        }
        $userName=Session::get("userName");
        $bookInfo=Db::table("书")->find($id);
         $count=Db::table("书")->where(["userName"=>$userName,"id"=>$id])->delete();
         if($count>0){
             $this->success("删除成功","Index/myReleased");
         }
         else{
             $this->error("删除失败,请联系开发人员");
         }
     }

      //修改书籍
      public function changeBookInfo($id=0)
      {
         $userName=Session::get("userName");
         $book=Db::table("书")->where(["userName"=>$userName,"id"=>$id])->select();
         $this->assign("book",$book[0]);

        //查询学院信息并返回
        $adcdemys=Db::table("学院")->select();
        $this->assign("adcdemys",$adcdemys);
         return view();
      }

       //更新书籍信息
       public function updateBookInfo($id=0)
       {
           //提醒登录
            $isLogin=Session::get("isLogin");
            if(!$isLogin){
                $this->error("请先登录");
            }

            $userName=Session::get("userName");
            $data =[
                "userName"=> Session::get('userName'),
                "id"=>$id,
                "bookname"=>$_POST["书名"],
                "degree"=>$_POST["新旧程度"],
                "type"=>$_POST["类型"],
                "academy"=>$_POST["所属学院"],
                "original_price"=>$_POST["原价"],
                "now_price"=>$_POST["售价"],
                "sellerWeChat"=>$_POST["微信"],
                "remarks"=>$_POST["备注"],
            ];
            Db::table("书")->where(["userName"=>$userName,"id"=>$id])->update($data);
            $this->success("修改成功,请等待管理员重新审核","Index/myReleased");
           
       }


       public function imageHandle(){
            $books=Db::table("书")->select();
            for($i=0;$i<count($books);$i++){
                $imgurl = "D:\wamp\www\SecondHandBooks\public\static\bookImages\\".$books[$i]["main_image"];
                $imgurl=str_replace("/","\\",$imgurl);
                dump($imgurl);
                $image = \think\Image::open($imgurl);
                dump($image);
                $image->thumb(250, 340,1)->save($imgurl);//生成缩略图、删除原图
            }
       }


}
