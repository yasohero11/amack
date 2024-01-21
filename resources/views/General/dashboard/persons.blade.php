@extends('layouts.General.dashboard-layout')
@section("title" , "Admin - Countries")

@section("links")
    <style>
        .a-avatar {

            width: 45px;
            height: 45px;
            display: flex;
            align-content: center;
            border-radius: 100%;
            overflow: hidden;
        }


    </style>
    <link href="{{asset("css/popUpAlerts.css")}}" rel="stylesheet"/>
@endsection

@section("js")
    <script src="{{asset("/js/fetch-lib.js")}}"></script>
    <script src="{{asset("/js/confirm-dialog.js")}}"></script>
    <script src="{{asset("/js/popupAlert.js")}}"></script>
    <script>
        const imagePickerInput = document.querySelector(".image-picker-input")
        const imagePicker = document.querySelector(".image-picker")
        const reader = new FileReader();
        const img = imagePicker.querySelector("img")
        const imageSelectorIcon = imagePicker.querySelector("i")
        reader.onload = (event) => {

            img.src = event.target.result
            img.style.display = "block"
            imageSelectorIcon.style.display = "none"
        };

        imagePickerInput.addEventListener("change", (event) => {
            const file = event.target.files[0]; // Get the selected file

            // Check if the selected file is an image
            if (file.type.startsWith("image/")) {


                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                // Handle non-image files (optional)
                alert("Please select an image file.");
            }
        });


    </script>

    <script>
        const popupAlert = new PopupAlert();
        const form = document.querySelector("#form")
        const table = document.querySelector("#table")
        const personsDialog = document.querySelector('dialog')
        const nameInput = document.querySelector("#name")
        const ageInput = document.querySelector("#age")
        const addressInput = document.querySelector("#address")
        const roleInput = document.querySelector("#role")
        const countrySelector = document.querySelector("#country")


        let selectedItem = -1;

        async function createPeron() {
            let loading = popupAlert.showLoadingAlert("Creating Author ...")
            hideInvalidMessages()
            const data = new FormData(form);

            await AFetch({
                method: "POST",
                body: data,
                url: "{{route("persons")}}",
                onResponse: (response) => {
                    closeModel()
                    getPersons()
                    popupAlert.showSuccessAlert("Person Created")
                },
                onError : async(response) => {
                    console.log(response)
                    if(response.status === 422)
                        validateResponse(await response.json())
                    else{
                        popupAlert.showFailureAlert("Ops!! Error Occurred")
                    }

                }
            })
            popupAlert.removeAlrt(loading)
        }

        async function updatePersons() {
            let loading = popupAlert.showLoadingAlert("Updating Person ...")
            hideInvalidMessages()
            const data = new FormData(form);
            await AFetch({
                method: "POST",
                body: data,
                url: "{{route("update.persons",["id"=> ":id"])}}".replace(':id', selectedItem),
                onResponse: (response) => {
                    closeModel()
                    getPersons()
                    popupAlert.showSuccessAlert("Person Updated")
                },
                onError : async(response) => {
                    console.log(response)
                    if(response.status === 422)
                        validateResponse(await response.json())
                    else{
                        popupAlert.showFailureAlert("Ops!! Error Occurred")
                    }

                }
            })

            popupAlert.removeAlrt(loading)
        }

        function showModel(id, json) {
            json = JSON.parse(json)
            console.log(json)
            selectedItem = id || -1;
            if (json) {
                nameInput.value = json["name"];
                ageInput.value = json["age"];
                addressInput.value = json["address"];
                img.src = json["image"] ?? "";
                console.log(img.src)
                if (img.src !== "") {
                    img.style.display = "block"
                    imageSelectorIcon.style.display = "none"
                }
                roleInput.value = json["role"];
                countrySelector.value = json["country"]["id"]
            }
            MiniDialog.showModal()
        }

        var closeModel = function () {
            hideInvalidMessages()
            selectedItem = -1
            nameInput.value = "";
            ageInput.value = "";
            addressInput.value = "";
            imagePickerInput.value = "";
            img.src = "";
            imageSelectorIcon.style.display = "block"
            roleInput.value = "";
            MiniDialog.close('cancel')
        }

        function getPersons(page = 1) {
            AFetch({
                url: "{{route("persons")}}",
                onResponse: async (response) => {
                    table.innerHTML = await response.text()
                },
                onError: (errors) => {
                    console.log(errors)
                }
            })
        }

        async function deleteCountry(id) {
            let loading = popupAlert.showLoadingAlert("Deleting Article ...")
            await AFetch({
                method: "DELETE",
                url: `{{route("delete.persons" , ["id" => ":id"])}}`.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                onResponse: (response) => {
                    selectedItem = -1
                    getPersons()
                    popupAlert.showSuccessAlert("Person Deleted")
                },
                onError : async(response) => {
                    console.log(response)
                    if(response.status === 422)
                        validateResponse(await response.json())
                    else{
                        popupAlert.showFailureAlert("Ops!! Error Occurred")
                    }

                }
            })
            popupAlert.removeAlrt(loading)
        }

        const confirmModel = new ConfirmDialog({
            text: "Are you sure you want to remove this element?",

            onShow: (id) => {
                selectedItem = id[0]
            },
            onConfirm: function () {
                console.log("on Confirm called")
                if (selectedItem !== -1)
                    deleteCountry(selectedItem)
            },

            onCancel: () => {

                selectedItem = -1
            }
        });

    </script>
@endsection


@section('content')

    <dialog id="MiniDialog">
        <form method="dialog" id="form">
            @csrf
            <div class="mb-3">
                <label for="image" class="form-label">Profile Image</label>
                <div class="row justify-content-center m-2">

                    <label for="image" class="image-picker">
                        <i class="fa-solid fa-camera-retro"></i>
                        <img style="display: none" class="img-fluid" src="">
                    </label>


                </div>
                <input type="file" hidden class="form-control image-picker-input" name="image" id="image">

            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Person Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Person Name">
                <div class="name-invalid-feedback invalid-feedback">
                    Please choose a username.
                </div>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Person Age</label>
                <input type="number" class="form-control" name="age" id="age" placeholder="Person Age">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="Address">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control" name="role" id="role" placeholder="Role">
            </div>
            <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <select class="form-select " id="country" name="country_id" aria-describedby="Country">
                    @foreach($countries as $country)
                        <option value="{{$country->id}}">
                            {{$country->name}}
                        </option>
                    @endforeach

                </select>
            </div>

            <footer>
                <menu style="padding: 0px">
                    <div class="ms-auto d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-white mb-0 me-2" onclick="closeModel()">
                            Close
                        </button>
                        <button type="button" onclick="selectedItem === -1? createPeron() : updatePersons()"
                                class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0">

                            <span class="btn-inner--text">Confirm</span>
                        </button>


                    </div>
                </menu>
            </footer>
        </form>
    </dialog>





    <div class="container-fluid py-4 px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-md-flex align-items-center mb-3 mx-2">
                    <div class="mb-md-0 mb-3">
                        <h3 class="font-weight-bold mb-0">Hello, Noah</h3>
                        <p class="mb-0">Apps you might like!</p>
                    </div>
                    <button type="button"
                            class="btn btn-sm btn-white btn-icon d-flex align-items-center mb-0 ms-md-auto mb-sm-0 mb-2 me-2">
              <span class="btn-inner--icon">
                <span class="p-1 bg-success rounded-circle d-flex ms-auto me-2">
                  <span class="visually-hidden">New</span>
                </span>
              </span>
                        <span class="btn-inner--text">Messages</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0">
              <span class="btn-inner--icon">
                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="d-block me-2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
              </span>
                        <span class="btn-inner--text">Sync</span>
                    </button>
                </div>
            </div>
        </div>
        <hr class="my-0">

        <div class="row mt-5">

            <div class="col-xl-3 col-sm-6 mb-xl-0">
                <div class="card border shadow-xs mb-4">
                    <div class="card-body text-start p-3 w-100">
                        <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm4.5 7.5a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0v-2.25a.75.75 0 01.75-.75zm3.75-1.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V12zm2.25-3a.75.75 0 01.75.75v6.75a.75.75 0 01-1.5 0V9.75A.75.75 0 0113.5 9zm3.75-1.5a.75.75 0 00-1.5 0v9a.75.75 0 001.5 0v-9z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="w-100">
                                    <p class="text-sm text-secondary mb-1">Avg. Transaction</p>
                                    <h4 class="mb-2 font-weight-bold">$450.53</h4>
                                    <div class="d-flex align-items-center">
                      <span class="text-sm text-success font-weight-bolder">
                        <i class="fa fa-chevron-up text-xs me-1"></i>22%
                      </span>
                                        <span class="text-sm ms-1">from $369.30</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row my-4">

            <div class="col-lg-12 col-md-12">
                <div class="card shadow-xs border">
                    <div class="card-header border-bottom pb-0">
                        <div class="d-sm-flex align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-semibold text-lg mb-0">Recent Countries</h6>

                            </div>
                            <div class="ms-auto d-flex">
                                <button type="button" class="btn btn-sm btn-white mb-0 me-2">
                                    View report
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0">
                    <span class="btn-inner--icon">
                      <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                           stroke-width="1.5" stroke="currentColor" class="d-block me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                      </svg>
                    </span>
                                    <span class="btn-inner--text">Download</span>
                                </button>

                                <button type="button" onclick="MiniDialog.showModal()"
                                        class="btn btn-sm btn-dark btn-icon d-flex align-items-center p-0 mb-0"
                                        style="width: 35px; text-align: center;justify-content: center; margin-left: 5px">

                                    <span class="btn-inner--text"><i style="font-size: 10px"
                                                                     class="fa-solid fa-plus"></i></span>
                                </button>
                            </div>
                        </div>
                        <div class="pb-3 d-sm-flex align-items-center">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable1"
                                       autocomplete="off" checked>
                                <label class="btn btn-white px-3 mb-0" for="btnradiotable1">Name</label>
                                <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable2"
                                       autocomplete="off">
                                <label class="btn btn-white px-3 mb-0" for="btnradiotable2">Code</label>
                                <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable4"
                                       autocomplete="off">
                                <label class="btn btn-white px-3 mb-0" for="btnradiotable4">Region</label>
                                <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable3"
                                       autocomplete="off">
                                <label class="btn btn-white px-3 mb-0" for="btnradiotable3">Creation Date</label>
                            </div>
                            <div class="input-group w-sm-25 ms-auto">
                  <span class="input-group-text text-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                    </svg>
                  </span>
                                <input type="text" class="form-control" placeholder="Search">
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 py-0">
                        <div class="table-responsive p-0">
                            <table id="table" class="table align-items-center justify-content-center mb-0">
                                <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">Name</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Age</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Address</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Role</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Country</th>
                                    <th class="text-center text-secondary text-xs font-weight-semibold opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>

                                @include("General.parts.dashboard.tables.persons")
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection
@section("scripts")


@endsection
