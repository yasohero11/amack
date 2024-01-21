<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResources;
use App\Models\Author;
use App\Models\Country;
use App\Repositories\AdminStatisticsRepository;
use App\Repositories\AuthorRepository;
use Illuminate\Http\Request;

class AuthorController extends Controller
{

    protected $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function index(Request $req, AdminStatisticsRepository $adminStatisticsRepository)
    {

        $data = $adminStatisticsRepository->search($req, app(Author::class));
        AuthorResources::collection($data)->toArray($req);

        if ($req->header("fetch"))
            return view('General.parts.dashboard.tables.authors', ["authors" => $data])->render();

        return view('General/dashboard/authors', ["authors" => $data, "countries" => Country::all()]);
    }

    public function store(AuthorRequest $req)
    {

        return AuthorResources::make($this->authorRepository->store($req));
    }

    public function update(AuthorRequest $req, $id)
    {
        return $this->authorRepository->update($req, $id);
    }

    public function destroy($id)
    {
        return $this->authorRepository->destroy($id);
    }
}
