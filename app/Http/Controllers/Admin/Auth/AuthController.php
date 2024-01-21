<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Auth\AuthRegistrationRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function getLogin(){

        if(Auth::guard("admin")->user())
            return Auth::guard("admin")->user();

        return view("Admin.Auth.login_page");
    }

    public function executeLogin(Request $request){
        if(Auth::guard("admin")->attempt($request->only(["email","password"]))){
            return redirect("/admin");
        }


        return "wrong username or password";
    }



    public function register(AuthRegistrationRequest $req){
        $req["password"] = bcrypt($req->password);

    return Admin::create($req->all());
    }
}
