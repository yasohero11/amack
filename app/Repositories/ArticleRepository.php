<?php
namespace App\Repositories;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Providers\RepositoryServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleRepository{

    public function createArticle($req){
        $data = $req->all();
        if(Auth::guard('author')->user()){
            $data["author_id"] = Auth::guard('author')->user()->id;
        }
        $article = Article::create($data);

        if($req->has("image"))
            $article->addMediaFromRequest("image")->toMediaCollection('article-image');

        return new ArticleResource($article);
    }

    public function updateArticle($req, $id){
        $data = $req->all();
        $article = Article::find($id);
        if(!$article)
            return "not found";

        if($req->has("image"))
            $article->addMediaFromRequest("image")->toMediaCollection('article-image');

        $article->update($data);


        return new ArticleResource($article);
    }

    public function getBannerArticles(){
            return  ArticleResource::collection(Article::where("featured" , "1")->where("active" , "1")->get());
    }

    public function getArticles(Request  $req){
        return  ArticleResource::collection(Article::where("featured" , "0")->where("active" , "1")->paginate($req->query->get("itemPerPage") ?? 15));
    }

    public function getFilteredArticles($req, $additionalFilter  ){
        $adminStatisticsRepository = app(AdminStatisticsRepository::class);
        return $adminStatisticsRepository->search($req , app(Article::class) , additionalFilters:  $additionalFilter);
    }
    public function deleteArticle($id){

        $article = Article::find($id);

        if($article)
            return $article->delete();

        return  "not Found";
    }

}
