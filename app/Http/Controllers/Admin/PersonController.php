<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeronRequest;
use App\Http\Resources\PersonResource;
use App\Models\Country;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index(Request $req){

        $data = PersonResource::collection(Person::all())->toArray($req);

        if ($req->header("fetch"))
            return view('General.parts.dashboard.tables.persons', ["persons" => $data])->render();

        return view('General/dashboard/persons' ,["persons" => $data, "countries" => Country::all()]);
    }

    public function store(PeronRequest $req){
        $person = Person::create($req->all());
        $person->addMediaFromRequest("image")->toMediaCollection('person-image');
        return PersonResource::make($person);
    }

    public function update(PeronRequest $req , $id){

        $person = Person::find($id);

        if($person){
            if($req->has("image"))
                $person->addMediaFromRequest("image")->toMediaCollection('person-image');
            return $person->update($req->all());
        }
        return PersonResource::make($person);
    }

    public function destroy($id){
        $person = Person::find($id);

        if($person){
            return $person->delete();
        }
        return "not found ya m3lem";
    }
}
