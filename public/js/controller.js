// import * as model from "./model.js";

// import * as sliderView from "./views/sliderView.js";
// import productView from "./views/productView.js";

// import sortView from "./views/sortView.js";
// import  from "./views/.js";
// import selectedProView from "./views/selectedProView.js";

// ------------------------ pasted code DON'T DELETE
const burgerBars = document.querySelector(".bars-list");
const navList = document.querySelector(".links");
const topSearchBarContanier = document.querySelector(".bar-top");
const bottomSearchBarContanier = document.querySelector(".bar-bottom");
const showBottomSearchBar = document.querySelector(".glass-show");
const topSearchBar = topSearchBarContanier.querySelector(".search-bar");
const toggleSideBar = () => {
  navList.classList.remove("show-listed__items");
  navList.classList.add("animeate-mov");
};
burgerBars.addEventListener("click", () => {
  console.log("here");
  navList.classList.toggle("show-listed__items");
  navList.classList.toggle("animeate-mov");
});

/// Search

document.addEventListener("click", (e) => {
  const nothing = e.target.closest(".links");
  const glassLogo = e.target.closest(".glass-show");
  const z = e.target.closest(".search-bar") || e.target.closest(".search-btn");

  if (e.target === burgerBars || e.target === glassLogo || e.target === z)
    return;

  if (!nothing) toggleSideBar();
  if (e.target !== glassLogo)
    bottomSearchBarContanier.classList.remove("appear");
});

topSearchBar.addEventListener("focus", () => {
  topSearchBarContanier.classList.toggle("search-shrink");
  topSearchBarContanier.classList.toggle("search-extend");
});

topSearchBar.addEventListener("blur", () => {
  topSearchBarContanier.classList.toggle("search-extend");
  topSearchBarContanier.classList.toggle("search-shrink");
});

showBottomSearchBar.addEventListener("click", () => {
  bottomSearchBarContanier.classList.toggle("appear");

  // bottomSearchBarContanier.querySelector(".search-bar").focus();
});

document.addEventListener("keydown", (e) => {
  const esc = "Escape";
  if (e.key == esc) {
    toggleSideBar();
  }
});

const goUp = document.querySelector(".go-up");

const landing = document.querySelector(".galary");

// goUp.addEventListener("click", () => {
//   document.body.scrollHeight;
// });

const stickyBtn = function (entries) {
  const [entry] = entries;
  // console.log(entry, "HERE!");

  if (!entry.isIntersecting) goUp.classList.remove("hidden");
  else goUp.classList.add("hidden");
};

const headerObserver = new IntersectionObserver(stickyBtn, {
  root: null, // mmsh 3arf
  threshold: 0, // kam fel % men el target bayn fel shasha
  // rootMargin: `-${navHight}px`, // abl el threshold b -90px e3mel call ll function
});
headerObserver.observe(landing);

window.addEventListener("popstate", function (event) {
  if (event.state === null) {
    let product = document.querySelector(".layout-fade");
    document.body.style.overflowY = "scroll";
    if (product.classList.contains("show-product")) {
      product.classList.remove("show-product");
    }
  }
});

history.pushState({}, "");

const buttons = document.querySelectorAll("[data-carousel-button]");

console.log(buttons);

buttons.forEach((button) => {
  button.addEventListener("click", () => {
    const offset = button.dataset.carouselButton === "next" ? 1 : -1;

    const slides = button
      .closest("[data-carousel]")
      .querySelector("[data-slides]");

    const activeSlide = slides.querySelector("[data-active]");

    let newIndex = [...slides.children].indexOf(activeSlide) + offset;

    console.log(newIndex, "here");
    if (newIndex < 0) newIndex = slides.children.length - 1;
    console.log(newIndex);
    if (newIndex >= slides.children.length) newIndex = 0;

    slides.children[newIndex].dataset.active = true;
    delete activeSlide.dataset.active;
  });
});
// ------------------------ pasted code DON'T DELETE

/// ----------------- Changing Layouts  [DON'T Delete]
const removeActive = (btn) => {
  btn.forEach((btn) => btn.classList.remove("btn--active"));
};
const toggle2 = function (btn, selectedBtns) {
  removeActive(selectedBtns);
  btn.classList.toggle("btn--active");
};

const list = document.querySelector(".products-list");
const allSortBtn = document.querySelectorAll(".sort-op");
const type = function (sortingBy) {
  if (sortingBy === "grid") {
    list.classList.remove("layout-row");
  } else if (sortingBy === "row") {
    list.classList.add("layout-row");
  }
};

const handleLayout = document.querySelector(".sorting");
console.log(handleLayout);
handleLayout.addEventListener("click", (e) => {
  const btn = e.target.closest(".sort-op");
  if (!btn) return;

  type(btn.dataset.sort);

  allSortBtn.forEach((btn) => btn.classList.remove("btn--active"));
  btn.classList.add("btn--active");
});

const controlSorting = function (sortingBy) {
  console.log(sortingBy);
  type(sortingBy);
  // model.sorting(sortingBy, type(sortingBy));

  // productView.render(model.sortedResults);
};
// controlInitProducts();
// sortView.addEventHandler(controlSorting);
// searchView.addHandlerSearch(controlSearch);
/// ----------------- Changing Layouts  [DON'T Delete]

//
//
//

//
//
//

///
//

// const toggleFunc = function () {
//   if (!btn.classList.contains("btn--active")) {
//     allBtn.forEach((btn) => btn.classList.remove("btn--active"));
//     btn.classList.add("btn--active");
//   } else {
//     btn.classList.remove("btn--active");
//   }
// };

// to add btn--active based on search queries
// const addOnSearch = function (searchQuery) {
//   allFilterSec.forEach((el) => {
//     if (el.dataset.query === searchQuery) {
//       console.log(el);
//       removeActive(allFilterSec);
//       el.classList.toggle("btn--active");
//     } else {
//       removeActive(allFilterSec);
//     }
//   });
// };
// addOnSearch("clothing");

// export const getSearchResultsPage = function (page = state.search.page) {
//   state.search.page = page;

//   const start = (page - 1) * 10; // 0
//   const end = page * 10; // 9
//   return state.search.results.slice(start, end);
// };
// const x = function () {
//   if (model.sortedResults.length !== 0) {
//     return model.sortedResults;
//   } else if (model.filtedResults.length !== 0) {
//     return model.filtedResults;
//   } else if (model.results.length !== 0) {
//     return model.results;
//   } else {
//     return model.Products;
//   }
// };

// const b = function () {
//   if (model.Products) {
//     return model.Products;
//   }
// };
// const controlWishList = function (id) {
//   // model.addToWishlist(id);
//   // console.log(model.Products);
//   // console.log(model.results);
//   // console.log(model.filtedResults);
//   const item = model.Products.find((el) => el.id === id);
//   console.log(item, "ASASad");
//   if (!item.wished) {
//     model.addToWishlist(id);
//   } else {
//     model.deleteWishList(id);
//   }
//   productView.update(x());
// };

// productView.addToWishListHandler(controlWishList);

// const controlSelectedProduct = function () {
//   const pro = model.Products.find((el) => el.id === productView.curProduct);
//   console.log(pro, "PRO PRO");
//   selectedProView.render(pro);
// };

// selectedProView.addToWishListHandler(controlSelectedProduct);

// ---------------------------- FROM HERE IT'S HAS SOMETHING TO DO WITH THE VIEWING OF THE PRODUCTS/ARTICAL

const productsContainer = document.querySelector(".products-list");

const perentElement = document.querySelector(".layout-fade");
const selectedProductContainer =
  perentElement.querySelector(".selected-product");
const imgContainer = perentElement.querySelector(".images-container");

const slides = imgContainer.querySelectorAll(".images-container img");
const dotsContainer = document.querySelector(".dots");
let curSlide = 0;
// const maxSlides = slides.length;

const checkImages = function (data) {
  if (Array.isArray(data.image)) {
    return data.image
      .map((el, i) => {
        return `<img class="img${i}" src="${el}" alt="image">`;
      })
      .join("");
  } else {
    return `<img src="${data.image}" alt="image">`;
  }
};

const clear = () => {
  selectedProductContainer.innerHTML = "";
};

const CreateDots = function (objImg) {
  if (Array.isArray(objImg.image)) {
    return objImg.image

      .map((el, i) => {
        return `    <div class="dot dot--active" data-slide="${i}">
       <img src="${el}" alt="${objImg.title}"> 
   </div>`;
      })
      .join("");
  } else {
    return `    <div class="dot dot--active" data-slide="0">
    <img src="${objImg.image}" alt="${objImg.title}"> 
</div>`;
  }
};

const activeDot = function (slide) {
  document
    .querySelectorAll(".dot")
    .forEach((el) => el.classList.remove("dot--active"));

  document
    .querySelector(`.dot[data-slide="${slide}"]`)
    .classList.add("dot--active");
};

const goToNextSlide = function (slidess, slide) {
  slidess.forEach((el, i) => {
    el.style.transform = `translateX(${100 * (i - slide)}%)`;
  });
};

const starting = function (slides) {
  // CreateDots();
  activeDot(0);
  goToNextSlide(slides, 0);
  // autoSlide = waitFiveSecondAndthenStart();
};

// starting();

const nextSlide = function (sildess, maxSlides) {
  if (curSlide === maxSlides - 1) {
    curSlide = 0;
  } else {
    curSlide++;
  }
  goToNextSlide(sildess, curSlide);
  activeDot(curSlide);
};

const prevSlide = function (sildess, maxSlides) {
  if (curSlide === 0) {
    curSlide = maxSlides - 1;
  } else {
    curSlide--;
  }
  goToNextSlide(sildess, curSlide);
  activeDot(curSlide);
};

/// listner for the product preview
let maxSlide;
let allImg;
let currentProduct;
productsContainer.addEventListener("click", (e) => {
  const img = e.target.closest(".img-container");
  const title = e.target.closest(".item-title");

  if (img || title) {
    const curProductID = +e.target.closest(".product-item").dataset.id;
    currentProduct = model.Products.find((el) => el.id === curProductID);
    console.log(currentProduct);
    clear();

    selectedProductContainer.insertAdjacentHTML(
      "afterbegin",
      generateMarkUp(currentProduct)
    );
    allImg = document.querySelectorAll(".images-container img");
    maxSlide = allImg.length;
    starting(allImg);

    perentElement.classList.add("show-product");
    document.body.style.overflowY = "hidden";
  }
});

perentElement.addEventListener("click", (e) => {
  if (
    e.target.classList.contains("close-btn") ||
    e.target.classList.contains("layout-fade")
  ) {
    console.log("working1");
    document.body.style.overflowY = "scroll";
    perentElement.classList.remove("show-product");
    console.log("workign2");
  }

  const rightBtn = e.target.closest(".arrow-right");
  const leftBtn = e.target.closest(".arrow-left");
  const dot = e.target.closest(".dot");
  const wishBtn = e.target.closest(".wish-logo");
  const allSizeBtns = document.querySelectorAll(".size-unit");
  const sizeBtn = e.target.closest(".size-unit");
  const amount = document.querySelector(".amout");
  const incBtn = e.target.closest(".up-arrow");
  const decBtn = e.target.closest(".down-arrow");
  if (incBtn) ++amount.textContent;
  if (decBtn) amount.textContent !== "1" ? --amount.textContent : "";
  if (sizeBtn) toggle2(sizeBtn, allSizeBtns);
  if (rightBtn) nextSlide(allImg, maxSlide);
  if (leftBtn) prevSlide(allImg, maxSlide);
  if (dot) goToNextSlide(allImg, +dot.dataset.slide);
  if (wishBtn) {
    if (!currentProduct.wished) {
      model.addToWishlist(currentProduct.id);
    } else {
      model.deleteWishList(currentProduct.id);
    }
    update(currentProduct);
    productView.update(x());
  }
});

const checkIfClothing = function (obj) {
  const catetitle = !Array.isArray(obj.category) ? obj.category.split(" ") : "";
  if (
    obj.generalCategory == "clothing" ||
    catetitle.some((el) => el === "clothing")
  ) {
    return `     <div class="sizes" >
    <h3>SIZES</h3>
    <div class="size-containers" >

      <span data-size="xs" class="size-unit optians-btn" >XS</span>
      <span data-size="s" class="size-unit optians-btn btn--active" >S</span>
      <span data-size="m" class="size-unit optians-btn" >M</span>
      <span data-size="l" class="size-unit optians-btn" >L</span>
      <span data-size="xl" class="size-unit optians-btn" >XL</span>
    </div>
   </div>`;
  } else {
    return "";
  }
};

const checkIfClothing2 = function (obj, categoryName, word1, word2) {
  const catetitle = !Array.isArray(obj.category) ? obj.category.split(" ") : "";
  if (
    (catetitle && catetitle.some((el) => el === categoryName)) ||
    obj.generalCategory === categoryName
  ) {
    return word1;
  } else {
    return word2;
  }
};
let bb = "1word 2word 3 word";
console.log(bb.split(" ")[1], "YOU ARE LOOKING FOR ME");

const cheackIfArray = function (Obj) {
  if (
    (Array.isArray(Obj.image) && Obj.image.length == 1) ||
    !Array.isArray(Obj.image)
  ) {
    return "";
  }
  if (Array.isArray(Obj.image)) {
    return `    <div class="arrow arrow-right" ><i class="fa-solid fa-arrow-right-long"></i></div>
 <div class="arrow arrow-left" ><i class="fa-solid fa-arrow-left-long"></i></div>`;
  }
};

function getRandomYear() {
  // Set the minimum and maximum year values
  var min = 2001;
  var max = 2023;

  // Generate a random number between min and max, inclusive
  var random = Math.floor(Math.random() * (max - min + 1)) + min;

  // Return the random year
  return random;
}

function calcRating(obj) {
  if (obj.rating.rate === 0) {
    return `
    <div class="rating-stars" >
      <span class="fa fa-star "></span>
      <span class="fa fa-star "></span>
      <span class="fa fa-star "></span>
      <span class="fa fa-star"></span>
      <span class="fa fa-star"></span>
      <span class="rating-count" >${obj.rating.count}</span>
     </div>
  
  </div>
    `;
  }

  if (obj.rating.rate <= 1) {
    return `
    <div class="rating-stars " >
      <span class="fa fa-star checked"></span>
      <span class="fa fa-star "></span>
      <span class="fa fa-star "></span>
      <span class="fa fa-star"></span>
      <span class="fa fa-star"></span>
      <span class="rating-count" >${obj.rating.count}</span>
     </div>
  
  </div>
    `;
  }

  if (obj.rating.rate <= 2) {
    return `
    <div class="rating-stars " >
      <span class="fa fa-star  checked"></span>
      <span class="fa fa-star  checked"></span>
      <span class="fa fa-star "></span>
      <span class="fa fa-star"></span>
      <span class="fa fa-star"></span>
      <span class="rating-count" >${obj.rating.count}</span>
     </div>
  
  </div>
    `;
  }

  if (obj.rating.rate <= 3) {
    return `
  <div class="rating-stars " >
    <span class="fa fa-star checked"></span>
    <span class="fa fa-star checked"></span>
    <span class="fa fa-star  checked"></span>
    <span class="fa fa-star"></span>
    <span class="fa fa-star"></span>
    <span class="rating-count" >${obj.rating.count}</span>
   </div>

</div>
  `;
  }

  if (obj.rating.rate <= 4) {
    return `
  <div class="rating-stars" >
    <span class="fa fa-star  checked "></span>
    <span class="fa fa-star  checked"></span>
    <span class="fa fa-star  checked"></span>
    <span class="fa fa-star checked"></span>
    <span class="fa fa-star"></span>
    <span class="rating-count" >${obj.rating.count}</span>
   </div>

</div>
  `;
  }

  if (obj.rating.rate <= 5) {
    return `
  <div class="rating-stars" >
    <span class="fa fa-star checked "></span>
    <span class="fa fa-star  checked"></span>
    <span class="fa fa-star checked "></span>
    <span class="fa fa-star checked"></span>
    <span class="fa fa-star" checked></span>
    <span class="rating-count" >${obj.rating.count}</span>
   </div>

</div>
  `;
  }
}

const generateMarkUp = function (productOBJ) {
  return `
  <div  data-id ="${productOBJ.id}" class="left-side">
  <i class="fa-solid fa-xmark close-btn"></i>
  <div class="product-preview">
    <div class="images-container" >
    ${checkImages(productOBJ)}
   ${cheackIfArray(productOBJ)}
    </div>


    <div class="dots">

       ${CreateDots(productOBJ)}

    </div>
  </div>


<div class="product-detials" >
   <h2 class="product-title" >${productOBJ.title}</h2>
   <div class="prices-rating" > 
    <div class="prices">
      <del class="discount">${
        productOBJ.discountedPrice ? `$${productOBJ.discountedPrice}` : ""
      }</del> <span class="${
    productOBJ.discountedPrice ? "price" : "only-price"
  }" >$${productOBJ.price}</span>
  
    </div>
  
    ${calcRating(productOBJ)}
    <p class="discreption" >${productOBJ.description}.</p>

     ${checkIfClothing(productOBJ)}

     <div class="add-cart add-wish" >
        <span class="add-cart__btn optians-btn btn--active" >ADD TO CART</span>
      
        <div class="count--wish" >
          <div class="amount-purchsed">
            <div class="arrows up-arrow" > <i class="fa-solid fa-sort-up"></i></div>
            <div class="amout" >1</div>
            <div class=" arrows down-arrow" ><i class="fa-solid fa-sort-down"></i></div>
         </div>
 
         <span class="wish-logo"  title="add to wishlist"> <i class="fa-${
           productOBJ.wished ? "solid" : "regular"
         } fa-bookmark"></i></span>
        </div>
      
     </div>
     <div class="delivery">
       <span >
        <i class="fa-solid fa-truck"></i>
       </span>
       <span class="fonts">Free delivery on orders over $30.0</span>
     </div>
     <div class="detials" >
        <h3 class="det-title" >CHARACTERISTICS</h3>
        <div class="del-section brand" >
          <span class="tag" >Brand</span>
          <span class="value" >${productOBJ.title.split(" ")[0]}</span>
        </div>
 
        <div class="del-section collection" >
          <span class="tag" >Collection</span>
          <span class="value" >${getRandomYear()}</span>
        </div>

        <div class="del-section item-no" >
          <span class="tag" >item no.</span>
          <span class="value" >${productOBJ.id}</span>
        </div>

        <div class="del-section warranty" >
          <span class="tag" >warranty</span>
          <span class="value" >${checkIfClothing2(
            productOBJ,
            "electronics",
            "2 years",
            "unset"
          )}</span>
        </div>

        <div class="brand" >
          <span class="tag" >Care recommendations</span>
          <span class="value" >${checkIfClothing2(
            productOBJ,
            "clothing",
            "wash machine",
            "refer to warranty"
          )}</span>
        </div>
     </div>
   


   </div>
</div>
 
 `;
};

const update = function (data) {
  // if (!data || (Array.isArray(data) && data.length === 0))
  //   return this.renderError();

  const newMarkup = generateMarkUp(data);

  const newDOM = document.createRange().createContextualFragment(newMarkup);
  console.log(newDOM, "LOK!!");
  const newElements = Array.from(newDOM.querySelectorAll("*"));
  const curElements = Array.from(
    selectedProductContainer.querySelectorAll("*")
  );
  // console.log(newElements, "LOK!!");
  // console.log(curElements, "LOK!!");

  // ------------  VERY!! ! IMPORTANT ----// NodeValue see notes
  newElements.forEach((newEl, i) => {
    const curEl = curElements[i];
    // console.log(curEl, newEl.isEqualNode(curEl));
    // Cheacking the the elment is differant and checking if it is a text or not , and then updates changed text
    if (
      !newEl.isEqualNode(curEl) &&
      newEl.firstChild?.nodeValue.trim() !== ""
    ) {
      // console.log(newEl.firstChild?.nodeValue.trim(), '🎆🎆'); nodeValue always returns text it can't return elements.
      curEl.textContent = newEl.textContent;
    }
    // Update changed attribues (dataset)
    if (!newEl.isEqualNode(curEl)) {
      // console.log(newEl.attributes, '!!!!');
      // console.log(Array.from(newEl.attributes));
      Array.from(newEl.attributes).forEach((attr) =>
        curEl.setAttribute(attr.name, attr.value)
      );
    }
  });
};
