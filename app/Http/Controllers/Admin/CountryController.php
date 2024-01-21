<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Models\country;
use Illuminate\Http\Request;

class CountryController extends Controller
{

    public function index(Request $req){

        if ($req->header("fetch"))
            return view('General.parts.dashboard.tables.countries', ["countries" => Country::all()])->render();

        return view('General/dashboard/countries' , ["countries" => Country::all()]);

    }

    public function store(CountryRequest $req){

        Country::create($req->all());
        return view('General/dashboard/countries' , ["countries" => Country::all()]);
    }

    public function update(CountryRequest $req , $id){

        $country = Country::find($id);

        if($country){
           return $country->update($req->all());
        }
        return "not found ya m3lem";
    }

    public function destroy($id){
        $country = Country::find($id);

        if($country){
            return $country->delete();
        }
        return "not found ya m3lem";
    }

}
