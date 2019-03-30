<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Paginator;

class BookManage extends Controller
{

    //书籍审核
    public function bookAudit()
    {
        $count = Db::table('书')->where(["state"=>"待审核"])->count();//计算总页面
        $books=Db::table("书")->where(["state"=>"待审核"])->paginate(4,$count,['query' =>request()->param()]);
        // 获取分页显示
        $page = $books->render();
        $this->assign("count",$count);
        $this->assign("books",$books);
        $this->assign('page', $page);
        return view("Bookmanage\bookAudit");
    }

    //通过审核
    public function passAudit($id=0,$userName)
    {
        $count=Db::table("书")->where(["userName"=>$userName,"id"=>$id])->update(["state"=>"在售"]);
        if($count>0){
            $this->success("审核成功","Bookmanage/bookAudit");
        }
        else{
            $this->error("审核失败,请联系开发人员");
        }
        
    }

    //删除书籍
    public function deleteBook($id=0,$userName)
    {
        $count=Db::table("书")->where(["userName"=>$userName,"id"=>$id])->delete();
        if($count>0){
            $this->success("删除成功","Bookmanage/bookManage");
        }
        else{
            $this->error("删除失败,请联系开发人员");
        }
    }

    //书籍管理
    public function bookManage(){
        $count = Db::table('书')->where(["state"=>"在售"])->count();//计算总页面
        $books=Db::table("书")->where(["state"=>"在售"])->paginate(5,$count,['query' =>request()->param()]);
        // 获取分页显示
        $page = $books->render();
        $this->assign("count",$count);
        $this->assign("books",$books);
        $this->assign('page', $page);
        return view();
    }

     //查询书的信息
     public function bookInfo()
     {
         $bookid=$_POST['id'];
         $userName=$_POST['userName'];
         $bookInfo=Db::table("书")->where(["userName"=>$userName,"id"=>$bookid])->select();
         if(empty($bookInfo)){
             $this->error("查询的书不存在");
         }
         $this->assign("bookInfo",$bookInfo[0]);
         return view();
     }

     //下架书籍(售出)
     public function bookLowerShelf($id=0,$userName){
        $data=[
            "state"=>"已售出"
        ];
        $count=Db::table("书")->where(["userName"=>$userName,"id"=>$bookid])->update($data);
        if($count>0){
            $this->success("下架成功","Bookmanage/bookManage");
        }
        else{
            $this->error("下架失败,请联系开发人员");
        }

     }

      //删除书籍
    public function deleteBookTwo($id=0,$userName)
    {
        $count=Db::table("书")->where(["userName"=>$userName,"id"=>$id])->delete();
        if($count>0){
            $this->success("删除成功","Bookmanage/bookManage");
        }
        else{
            $this->error("删除失败,请联系开发人员");
        }
    }

    //查询在售书籍--通过书名
    public function searchBookByBookName($name){
        $count = Db::table('书')->where(["state"=>"在售"])->where("bookname","like","%$name%")->count();//计算总页面
        $books=Db::table("书")->where(["state"=>"在售"])->where("bookname","like","%$name%")->paginate(5,$count,['query' =>request()->param()]);
        // 获取分页显示
        $page = $books->render();
        $this->assign("count",$count);
        $this->assign("books",$books);
        $this->assign('page', $page);
        return view("Bookmanage/BookManage");
    }

    //查询在售书籍--通过用户名
    public function searchBookByUserName($name){
        $count = Db::table('书')->where(["state"=>"在售","userName"=>$name])->count();//计算总页面
        $books=Db::table("书")->where(["state"=>"在售","userName"=>$name])->paginate(5,$count,['query' =>request()->param()]);
        // 获取分页显示
        $page = $books->render();
        $this->assign("count",$count);
        $this->assign("books",$books);
        $this->assign('page', $page);
        return view("Bookmanage/BookManage");
    }

    public function updateType($id,$userName){
         $book=Db::table("书")->where(["userName"=>$userName,"id"=>$id])->select();
         $this->assign("book",$book[0]);

        //查询学院信息并返回
        $adcdemys=Db::table("学院")->select();
        $this->assign("adcdemys",$adcdemys);
         return view();
    }

     //更新书籍信息
     public function updateBookInfo($id,$userName)
     {
          $data =[
              "type"=>$_POST["类型"],
              "academy"=>$_POST["所属学院"],
          ];
          Db::table("书")->where(["userName"=>$userName,"id"=>$id])->update($data);
          $this->success("修改成功","Bookmanage/bookmanage");
     }
}
