<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\ArticleRepository;
use Illuminate\Http\Request;

class ArticleController extends Controller
{

    protected $articleRepository;
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index(Request $req){
        $banner = $this->articleRepository->getBannerArticles();
        $articles = $this->articleRepository->getArticles($req);

        if ($req->header("fetch"))
            return view('Admin.parts.articles_list', ["articles" => $articles])->render();



        return view("Admin.article_index" ,['banner' => $banner , "articles" => $articles ,
            "links" => implode(',',  $articles->links()->elements[0]),
            "lastPage" => $articles->lastPage()
        ]);
    }
}
