<?php
namespace App\Repositories;
use App\Http\Requests\AuthorRequest;

use App\Http\Resources\AuthorResources;

use App\Models\Author;
use App\Models\Country;
use Illuminate\Http\Request;

class AuthorRepository{


    public function store(AuthorRequest $req){
        $req["password"] = bcrypt($req->password);
        $author = Author::create($req->all());
        $author->assignRole("author");
        if($req->has("image"))
            $author->addMediaFromRequest("image")->toMediaCollection('person-image');

        return $author;
    }

    public function update(AuthorRequest $req , $id){

        if($req->has("password")){
            $req["password"] = bcrypt($req->password);
        }

        $author = Author::find($id);

        if($author){
            if($req->has("image"))
                $author->addMediaFromRequest("image")->toMediaCollection('person-image');
            return $author->update($req->all());
        }
        return AuthorResources::make($author);
    }

    public function destroy($id){
        $author = Author::find($id);

        if($author){
            return $author->delete();
        }
        return "not found ya m3lem";
    }
}
