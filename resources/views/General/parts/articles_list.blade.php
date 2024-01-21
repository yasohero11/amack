
        @foreach($articles as $article)
            <div data-wished="false" data-id="1" class="product-item">
                <div class="card" href="productView.html">
                    <div class="img-container">
                        <img
                            src="{{$article->media[0]->original_url}}"
                            alt="Nature Image #1"
                        />
                    </div>
                    <div class="div-card">
                        <p class="item-title">
                            {{$article->title}}
                        </p>
                        <div class="rating" data-rate="">
                            <i class="fa-solid fa-book-open-reader"></i> ${
                            result.rating.rate }/5
                        </div>
                        <div class="praice-discount">
                <span class="wish-logo" title="add to wishlist">
                  <i class="fa-regular fa-bookmark"></i
                  ></span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


