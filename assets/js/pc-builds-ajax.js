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
          nonce: pcBuildsAjax.nonce,
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
    var form = $(this);

    var data = {
      action: "pc_build_create",
      nonce: pcBuildsAjax.nonce,
      title: form.find('[name="pc_build_title"]').val(),
      description: form.find('[name="pc_build_description"]').val(),
      category: form.find('[name="pc_build_category"]').val(),
      products: form.find('[name="pc_build_products[]"]').val(),
    };

    $.post(pcBuildsAjax.ajax_url, data, function (response) {
      if (response.success) {
        $("#pc-build-message").html(
          '<p>PC Build created! <a href="' +
            response.data.url +
            '">View it here</a></p>'
        );
        form[0].reset();
        $("#pc_build_products").val(null).trigger("change");
      } else {
        $("#pc-build-message").html(
          '<p style="color:red">' + response.data + "</p>"
        );
      }
    });
  });
});
