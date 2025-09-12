jQuery(function ($) {
  // Initialize select2 with AJAX search
  $("#pc_build_products").select2({
    placeholder: "Search products",
    width: "100%",
    minimumInputLength: 3, // only search after typing 3+ characters
    ajax: {
      url: pcBuildsAjax.ajax_url,
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          action: "pc_build_products_search",
          q: params.term, // search term
          nonce: pcBuildsAjax.search_nonce,
        };
      },
      processResults: function (data) {
        return { results: data };
      },
    },
  });

  // Handle form submission
  $("#pc-build-form").on("submit", function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const formData = new FormData(form);

    formData.append("action", "pc_build_create");
    formData.append("nonce", pcBuildsAjax.nonce);

    // Add selected products (Select2)
    const products = $("#pc_build_products").val();
    if (products) {
      products.forEach(function (id) {
        formData.append("products[]", id);
      });
    }

    $.ajax({
      url: pcBuildsAjax.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          $("#pc-build-message").html(
            '<p>PC Build created! <a href="' +
              response.data.url +
              '">View it here</a></p>'
          );
          $("#pc-build-form")[0].reset();
          $("#pc_build_products").val(null).trigger("change");
        } else {
          $("#pc-build-message").html(
            '<p style="color:red">' + response.data + "</p>"
          );
        }
      },
    });
  });
});
