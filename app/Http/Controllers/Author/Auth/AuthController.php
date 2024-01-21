<?php

namespace App\Http\Controllers\Author\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Auth\AuthRegistrationRequest;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function getLogin(){

//        if(Auth::guard("authors")->user())
//            return Auth::guard("authors")->user();

        return view("Author.Auth.login_page");
    }

    public function executeLogin(Request $request){
        if(Auth::guard("author")->attempt($request->only(["email","password"]))){
            return redirect("/admin/dashboard");
        }


        return "wrong username or password";
    }



    public function register(AuthRegistrationRequest $req){
        $req["password"] = bcrypt($req->password);

        return Author::create($req->all());
    }
}
