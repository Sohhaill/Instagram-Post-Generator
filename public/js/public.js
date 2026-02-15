document.addEventListener("DOMContentLoaded", function () {

    const btn = document.querySelector(".ipg-btn");
    if (!btn) return;

    btn.addEventListener("click", function (e) {
        e.preventDefault();

        const form = document.querySelector(".ipg-form");
        const formData = new FormData(form);

        // Add action to formData (this is correct way)
        formData.append("action", "ipg_save_user_request");

        fetch(ipg_ajax.ajax_url, {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(response => {

            // Safely read the message
            const message =
                response?.data?.message ||
                response?.message ||
                "Something went wrong";

            alert(message);

        })
        .catch(err => {
            console.error(err);
            alert("Request failed. Check console.");
        });
    });

});
jQuery(document).ready(function ($) {

    // DELETE BUTTON
    $(document).on("click", ".ipg-delete-btn", function () {

        if (!confirm("Are you sure you want to delete this entry?")) return;

        let id = $(this).data("id");

        $.ajax({
            url: ipg_ajax.ajax_url,
            type: "POST",
            data: {
                action: "ipg_delete_entry",
                id: id
            },
            success: function (response) {

                if (response.success) {
                    $("#ipg-row-" + id).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data.message);
                }
            }
        });
    });

});


jQuery(document).ready(function ($) {

    // OPEN EDIT PROMPT PANEL
    $(".ipg-prompt-btn").on("click", function () {
        const id = $(this).data("id");
        const row = $("#ipg-row-" + id);

        $("#ipg-prompt-editor").show();

        // Fill fields
        $("#ipg-pe-user").text(row.find("td:nth-child(2)").text());
        $("#ipg-pe-email").text(row.find("td:nth-child(3)").text());
        $("#ipg-pe-category").text(row.find("td:nth-child(4)").text());
        $("#ipg-pe-styles").text(row.find("td:nth-child(5)").text());

        $("#ipg-prompt-editor").attr("data-id", id);
    });

    // GENERATE IMAGE
    $("#ipg-generate-image-btn").on("click", function () {

        const id = $("#ipg-prompt-editor").attr("data-id");
        const prompt = $("#ipg-pe-prompt").val();

        $("#ipg-generated-img").html("<p>Generating image...</p>");

        $.post(ipg_ajax.ajax_url, {
            action: "ipg_generate_image",
            id: id,
            prompt: prompt
        }, function (response) {

            if (response.success) {
                const img = `<img src="${response.data.image_url}" style="max-width:300px; margin-top:20px;">`;
                $("#ipg-generated-img").html(img);
            } else {
                $("#ipg-generated-img").html("<p style='color:red;'>" + response.data.message + "</p>");
            }

        });
    });

});
