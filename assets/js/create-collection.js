document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("bdc-product-modal");
  const addBtn = document.getElementById("bdc-add-product");
  const closeBtn = document.getElementById("bdc-close-modal");
  const results = document.getElementById("bdc-product-results");
  const selectedList = document.getElementById("bdc-selected-products");
  const searchInput = document.getElementById("bdc-product-search");
  const categoryFilter = document.getElementById("bdc-product-category-filter");

  addBtn.addEventListener("click", () => (modal.style.display = "block"));
  closeBtn.addEventListener("click", () => (modal.style.display = "none"));

  // Debounce-funktion för att inte skicka request varje knapptryck
  function debounce(fn, delay) {
    let timeout;
    return function (...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  function fetchProducts() {
    const search = searchInput.value.trim();
    if (search.length < 3) {
      results.innerHTML = "";
      return;
    }

    const category = categoryFilter.value;

    fetch(
      `${
        bdc_ajax_object.ajax_url
      }?action=bdc_search_products&search=${encodeURIComponent(
        search
      )}&category=${category}`
    )
      .then((res) => res.json())
      .then((products) => {
        results.innerHTML = "";
        products.forEach((product) => {
          const li = document.createElement("li");
          li.textContent = product.name;
          li.dataset.id = product.id;
          li.addEventListener("click", () => {
            // Add to selected products
            const selectedLi = document.createElement("li");
            selectedLi.textContent = product.name;
            selectedLi.dataset.id = product.id;
            selectedList.appendChild(selectedLi);
          });
          results.appendChild(li);
        });
      });
  }

  const debouncedFetch = debounce(fetchProducts, 300);
  searchInput.addEventListener("input", debouncedFetch);
  categoryFilter.addEventListener("change", fetchProducts);

  const form = document.querySelector(".bdc-collection-form");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const title = form.querySelector("#collection_title").value;
    const products = Array.from(selectedList.children).map(
      (li) => li.dataset.id
    );
    const nonce = form.querySelector("#bdc_collection_nonce").value;

    const formData = new FormData();
    formData.append("action", "bdc_save_collection");
    formData.append("bdc_collection_nonce", nonce);
    formData.append("collection_title", title);
    products.forEach((id) => formData.append("products[]", id));

    const imageInput = form.querySelector("#collection_image");
    if (imageInput.files.length > 0) {
      formData.append("collection_image", imageInput.files[0]);
    }

    fetch(bdc_ajax_object.ajax_url, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          alert(data.data.message);
          form.reset();
          selectedList.innerHTML = "";
        } else {
          alert(data.data || "Något gick fel");
        }
      });
  });
});
