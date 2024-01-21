<?php

namespace App\Http\Controllers\Author\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Author;
use App\Models\Country;
use App\Repositories\ArticleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    protected $articleRepository;
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index(Request $req){

        $this->articleRepository->getFilteredArticles($req,["author_id" => Auth::guard('author')->user()->id]);
        $data = ArticleResource::collection(Article::all())->toArray($req);

        if ($req->header("fetch"))
            return view('General.parts.dashboard.tables.articles', ["articles" => $data])->render();

        return view("General.dashboard.articles" ,["articles" => $data, "authors" => Author::all(), "countries" => Country::all()]);
    }

    public function store(ArticleRequest  $req){
        return $this->articleRepository->createArticle($req);
    }

    public function update(ArticleRequest  $req , $id){
        return $this->articleRepository->updateArticle($req, $id);
    }

    public function destroy($id){
        return $this->articleRepository->deleteArticle( $id);
    }
}
