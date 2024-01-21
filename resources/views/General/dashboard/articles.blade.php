@extends('layouts.General.dashboard-layout')
@section("title" , "Articles")

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

        .step2 {
            top: 0;
            position: absolute;
            width: 100vw;
            height: 100vh;
            overflow-y: scroll;
            background-color: rgba(28, 30, 33, 0.8);
            -webkit-backdrop-filter: blur(50px) saturate(100%) contrast(45%) brightness(130%);
            backdrop-filter: blur(50px) saturate(100%) contrast(45%) brightness(130%);
            display: none;


        }

        .step2-body {


            background-color: #1c1e21;

            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            border-radius: 20px;
            padding: 20px;

        }

        .step2-input {


            width: 100%;
        }


        .myinput {
            background-color: rgb(63, 61, 61);
            color: white;
            border: none;
            border-radius: 2px;
            width: 100%;
        }

        .step2-input label {
            display: block;
            font-size: 15px;
            font-weight: bold;


        }

        .toggleWrapper {
            margin-top: 0px !important;

        }

        .articleToggle {
            margin-top: 8px;
        }

        .pdfBtn {
            color: rgb(110, 110, 110);
            display: inline-block;
            min-width: 150px;
            height: 50px;
            padding: 5px 15px;
            background-color: whitesmoke;
            text-decoration-style: none;
            text-decoration: none;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            padding-top: 8px;

        }


    </style>
    <link href="{{asset("css/tagsWrapper.css")}}" rel="stylesheet"/>
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
        arcTags = []
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
        const articlesDialog = document.querySelector('dialog')
        const titleInput = document.querySelector("#title")
        const descriptionInput = document.querySelector("#description")
        const authorSelector = document.querySelector("#author")
        const loc = document.querySelector("#location")
        const era = document.querySelector("#era")
        const long = document.querySelector("#long")
        const lat = document.querySelector("#lat")
        const startDate = document.querySelector("#start_date")
        const endDate = document.querySelector("#end_date")
        const factIndex = document.querySelector("#fact_index")
        const historyFact = document.querySelector("#history_fact")


        let selectedItem = -1;

        async function  createArticle() {
            let loading = popupAlert.showLoadingAlert("Creating Article ...")

            hideInvalidMessages()
            const data = new FormData(form);
            data.append("source_links" , tags.join(","))
            await AFetch({
                method: "POST",
                body: data,
                url: "{{route("articles")}}",
                onResponse: (response) => {
                    closeModel()
                    getArticles()
                    popupAlert.showSuccessAlert("Article Created")
                },
                onError : async(response, validationError) => {
                    console.log("aaasas")
                    console.log(response)
                    console.log(validationError)
                    if(validationError)
                        validateResponse(response)
                    else{
                        popupAlert.showFailureAlert(response.message)
                    }

                }

            })
            popupAlert.removeAlrt(loading)
        }

       async function articleAuthor() {
           let loading = popupAlert.showLoadingAlert("Updating Article ...")
            hideInvalidMessages()
            const data = new FormData(form);
           await AFetch({
                method: "POST",
                body: data,
                url: "{{route("update.articles",["id"=> ":id"])}}".replace(':id', selectedItem),
                onResponse: (response) => {
                    closeModel()
                    getArticles()
                },
                onError : async(response) => {
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
                titleInput.value = json["title"];

                img.src = json["image"] ?? "";
                descriptionInput.value = json["description"]
                console.log(img.src)
                if (img.src !== "") {
                    img.style.display = "block"
                    imageSelectorIcon.style.display = "none"
                }
                if (json["author"])
                    authorSelector.value = json["author"]["id"]
            }
            MiniDialog.showModal()
        }

        var closeModel = function () {
            hideInvalidMessages()
            selectedItem = -1
            titleInput.value = "";
            imagePickerInput.value = "";
            img.src = "";
            imageSelectorIcon.style.display = "block"
            descriptionInput.value = ""
            long.value = ""
            lat.value = ""
            loc.value = ""
            historyFact.value = ""
            factIndex.value = 0
            startDate.value = ""
            endDate.value = ""
            era.value = ""
            tags = ["Article"]
            document.querySelector("#p-fact_index").innerText = 0
            createTag()
            MiniDialog.close('cancel')
        }

        function getArticles(page = 1) {
            AFetch({
                url: "{{route("articles")}}",
                onResponse: async (response) => {
                    table.innerHTML = await response.text()
                },
                onError: (errors) => {
                    console.log(errors)
                }
            })
        }

        async function deleteArticle(id) {
            let loading = popupAlert.showLoadingAlert("Updating Article ...")
            await AFetch({
                method: "DELETE",
                url: `{{route("delete.articles" , ["id" => ":id"])}}`.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                onResponse: (response) => {

                    console.log(response)
                    selectedItem = -1
                    getArticles()
                    popupAlert.showSuccessAlert("Article Deleted")
                },
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
                    deleteArticle(selectedItem)
            },

            onCancel: () => {

                selectedItem = -1
            }
        });

    </script>

    <script src="{{asset("/js/tagWrapper.js")}}"></script>
@endsection


@section('content')

    <dialog id="MiniDialog">
        <form method="dialog" id="form">
            @csrf
            <div class="mb-3">

                <div class="row justify-content-center m-2">

                    <label for="image" class="image-picker">
                        <i class="fa-solid fa-camera-retro"></i>
                        <img style="display: none" class="img-fluid" src="">
                    </label>


                </div>
                <input type="file" hidden class="form-control image-picker-input" name="image" id="image">

            </div>
            <div class="mb-3">

                <input type="text" class="form-control" name="title" id="title" placeholder="title">
                <div class="title-invalid-feedback invalid-feedback">
                    Please choose a username.
                </div>
            </div>
            <div class="mb-3">

                <input type="text" class="form-control" name="location" id="location" placeholder="location">
                <div class="name-invalid-feedback invalid-feedback">
                    Please choose a username.
                </div>
            </div>
            <div class="mb-3">

                <input type="text" class="form-control" name="era" id="era" placeholder="Era">
                <div class="name-invalid-feedback invalid-feedback">
                    Please choose a username.
                </div>
            </div>
            <div class="mb-3 ">

                <div class="pt-3 d-flex">
                    <input type="text" class="form-control " name="long" id="long" placeholder="Long">
                    <input type="text" class="form-control mx-3" name="lat" id="lat" placeholder="Lat">
                </div>
                <div class="name-invalid-feedback invalid-feedback">
                    Please choose a username.
                </div>
            </div>

            <div class="mb-3 ">

                <div class="pt-3 d-flex">
                    <div>

                        <input type="date" class="form-control " name="start_date" id="start_date"
                               placeholder="Start Date">
                    </div>
                    <div>

                        <input type="date" class="form-control mx-3" name="end_date" id="end_date"
                               placeholder="End Date">
                    </div>

                </div>
                <div class="name-invalid-feedback invalid-feedback">
                    Please choose a username.
                </div>
            </div>
            @hasanyrole(["admin", "super-admin"])
            <div class="mb-3">

                <select class="form-select " id="author" name="author_id" aria-describedby="author">
                    @foreach($authors as $author)
                        <option value="{{$author->id}}">
                            {{$author->name}}
                        </option>
                    @endforeach

                </select>
            </div>
            @endhasanyrole

            <div class="mb-3">

                <select class="form-select " id="country" name="country_id" aria-describedby="Country">
                    @foreach($countries as $country)
                        <option value="{{$country->id}}">
                            {{$country->name}}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="step2-input">


                <div class="wrapper">
                    <div class="title">
                        <i class="fa-solid fa-link"></i>
                        <h2>Source Links</h2>
                    </div>
                    <div class="content">
                        <p>Press enter or add a comma after each tag</p>
                        <ul class="tagsUl">

                            <input class="tagsInput" name="source_links" type="text" spellcheck="false">
                        </ul>
                    </div>
                    <div class="details">
                        <p><span>13</span> tags are remaining</p>
                        <button type="button">Remove All</button>
                    </div>
                </div>
            </div>


            <div class="mb-3">

                <input type="range" name="fact_index" class="form-range dialog-range" min="0" max="10" step="1"
                       value="0" id="fact_index">
                <p id="p-fact_index">
                    0
                </p>
            </div>

            <div class="mb-3">

                <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                <div class="description-invalid-feedback invalid-feedback">
                    Please choose a username.
                </div>
            </div>

            <div class="mb-3">

                <textarea class="form-control" name="history_fact" id="history_fact" rows="2"></textarea>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="active" value="1" id="active">
                <label class="form-check-label" for="active">Active</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured">
                <label class="form-check-label" for="featured">Featured</label>
            </div>


            <footer>
                <menu style="padding: 0px">
                    <div class="ms-auto d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-white mb-0 me-2" onclick="closeModel()">
                            Close
                        </button>
                        <button type="button" onclick="selectedItem === -1? createArticle() : articleAuthor()"
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
                            <table class="table align-items-center justify-content-center mb-0">
                                <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">Title</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Active</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Featured</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Author</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Coordinates
                                    </th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Country</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Location</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Era</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Start Date
                                    </th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">End Date</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Created At
                                    </th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Last
                                        Updated
                                    </th>
                                    <th class="text-center text-secondary text-xs font-weight-semibold opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody id="table">

                                @include("General.parts.dashboard.tables.articles")
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
