<?php

namespace App\Http\Controllers\Admin;

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

    public function index(Request $req)
    {

        $data = [];

        if (Auth::guard("author")->user()) {
            $articles = ArticleResource::collection($this->articleRepository->getFilteredArticles($req, additionalFilter: ["author_id" => Auth::guard('author')->user()->id]))->toArray($req);

        } else {
            $articles = ArticleResource::collection(Article::all())->toArray($req);
            $data["authors"] = Author::all();
        }

        $data["articles"] = $articles;



        if ($req->header("fetch"))
            return view('General.parts.dashboard.tables.articles', $data)->render();


        $data["countries"] = Country::all();

        return view("General.dashboard.articles",$data);
    }

    public function store(ArticleRequest $req)
    {
        return $this->articleRepository->createArticle($req);
    }

    public function update(ArticleRequest $req, $id)
    {
        return $this->articleRepository->updateArticle($req, $id);
    }

    public function destroy($id)
    {
        return $this->articleRepository->deleteArticle($id);
    }

}
