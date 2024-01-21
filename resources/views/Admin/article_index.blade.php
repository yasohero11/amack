@extends('layouts.Admin.layout')
@section("title" , "Articles")
@section("links")
    <link href="{{asset('css/main.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')






<section class="galary" aria-label="Newest Photos">
    <div class="carousel container" data-carousel>
        <button class="carousel-button prev" data-carousel-button="prev">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <button class="carousel-button next" data-carousel-button="next">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <ul data-slides>

            <?php
                $entered = false;
            ?>
            @foreach($banner as $item)

                @if($entered !=true)
                    <li class="slide"   data-active >
                        <img
                            src="{{$item->media[0]->original_url}}"
                            alt="Nature Image #1"
                        />
                        <div class="artical-text">
                            <p>
                                {{$item->title}}
                            </p>
                            <button class="btn--active">Read more...</button>
                        </div>
                    </li>
                        <?php
                        $entered = true;
                        ?>
                @else
                    <li class="slide"   >
                        <img
                            src="{{$item->media[0]->original_url}}"
                            alt="Nature Image #1"
                        />
                        <div class="artical-text">
                            <p>
                                {{$item->title}}
                            </p>
                            <button class="btn--active">Read more...</button>
                        </div>
                    </li>
                @endif

            @endforeach



        </ul>
    </div>
</section>



{{--    Banner End--}}

<section class="product-container container">
    <div class="category-title__sort">
        <h1 class="cate-title">What's New</h1>
        <div class="filter-sort">
            <div class="sorting">
                <!-- <span class="optians-btn sort-by">
                  SORT BY
                  <div class="close-btn__x">
                    <div class="right plus-sgin__selected">
                      <span class="left"></span>
                    </div>
                  </div>
                </span> -->
                <span
                    data-sort="grid"
                    class="btn--active optians-btn sort-op sort-grid"
                >
              <i class="fa-solid fa-grip"></i>
              <div class="close-btn__x">
                <div class="right">
                  <span class="left"></span>
                </div>
              </div>
            </span>
                <span data-sort="row" class="optians-btn sort-op sort-row" title="">
              <i class="fa-solid fa-grip-lines"></i>
              <div class="close-btn__x">
                <div class="right">
                  <span class="left"></span>
                </div>
              </div>
            </span>
                <!-- <span data-sort="rating" class="optians-btn sort-op">
                  RATING
                  <div class="close-btn__x">
                    <div class="right">
                      <span class="left"></span>
                    </div>
                  </div>
                </span> -->
            </div>
        </div>
    </div>
    <div class="products-list listed-articals">



        @include('Admin.parts.articles_list')


    </div>

    <div class="pagination">
        <button class="btn--inline pagination__btn--prev">
            <i class="fa-solid fa-angle-left page-left"></i>
            <span id="leftNumber">Page 1</span>
        </button>
        <span class="curr-page">1</span>
        <button class="btn--inline pagination__btn--next">
            <span id="rightNumber">Page 2</span>
            <i class="fa-solid fa-angle-right page-right"></i>
        </button>
    </div>
</section>







@endsection
@section("scripts")

    <script src="{{asset('js/controller.js')}}"></script>

    <script>
         lastPage = parseInt("{{$lastPage}}")
         links = "{{$links}}".split(",")
         currentPage = 1

         productList = document.querySelector(".products-list");
         leftBtn = document.querySelector(".pagination__btn--prev");
         rightBtn = document.querySelector(".pagination__btn--next");
         leftNumber = document.querySelector("#leftNumber");
         rightNumber = document.querySelector("#rightNumber");
         currPage =  document.querySelector(".curr-page");
         loadingPage =  `@include('Admin.parts.loading_screen')`
         leftBtn.style.display = "none";

         function showLoadingPage(){
             document.querySelectorAll(".loading-item").forEach(e=>{
                 console.log("enn")
                 e.style.display = "block"
             })
         }

         function nextPage(){
             if(currentPage < lastPage){
                 currentPage++
                 getArticle();
             }
             checkPages()
         }

         function prevPage(){

             if(currentPage > 1) {
                 currentPage--
                 getArticle();
             }
             checkPages()
         }

         function checkPages(){
             if(currentPage === 1)
                 leftBtn.style.display = "none";
             else
                 leftBtn.style.display = "block";

             if(currentPage === lastPage)
                 rightBtn.style.display = "none";
             else
                 rightBtn.style.display = "block";

             leftNumber.innerText = `Page ${currentPage-1}`
             rightNumber.innerText = `Page ${currentPage+1}`
             currPage.innerText = currentPage

         }


         async function getArticle() {
            try {
                productList.innerHTML = loadingPage;

                const response = await fetch(`{{route('home')}}?page=${currentPage}`,{
                    headers: {
                        "fetch" : "true"
                        // or 'Content-Type': 'application/x-www-form-urlencoded'
                    },
                });
                const html = await response.text();
                productList.innerHTML = "";
                productList.innerHTML = html;

            } catch (error) {
                if (error instanceof NetworkError) {
                    console.error('Network error fetching HTML:', error);

                } else {
                    console.error('Error fetching HTML:', error);

                }
            }
        }

        rightBtn.addEventListener("click" , nextPage)
        leftBtn.addEventListener("click" , prevPage)



    </script>
@endsection
