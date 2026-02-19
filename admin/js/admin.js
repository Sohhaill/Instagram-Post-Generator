jQuery(document).ready(function ($) {

    // ── OPEN EDIT PROMPT PANEL ───────────────────────────────────────────────
    $(".ipg-prompt-btn").on("click", function () {
        const id  = $(this).data("id");
        const row = $("#ipg-row-" + id);

        // Reset everything when opening a new row
        $("#ipg-pe-prompt1").val("");
        $("#ipg-pe-prompt2").val("");
        $("#ipg-image1-result").html("");
        $("#ipg-image2-result").html("");
        $("#ipg-image1-result-row").hide();
        $("#ipg-image2-result-row").hide();
        $("#ipg-prompt2-row").hide();
        $("#ipg-generate-image1-btn").prop("disabled", false);
        $("#ipg-generate-image2-btn").prop("disabled", false);

        // Fill user info fields
        $("#ipg-pe-user").text(row.find("td:nth-child(2)").text());
        $("#ipg-pe-email").text(row.find("td:nth-child(3)").text());
        $("#ipg-pe-category").text(row.find("td:nth-child(4)").text());
        $("#ipg-pe-styles").text(row.find("td:nth-child(5)").text());

        $("#ipg-prompt-editor").attr("data-id", id).show();
    });


    // ── GENERATE IMAGE 1 ────────────────────────────────────────────────────
    $("#ipg-generate-image1-btn").on("click", function () {

        const id     = $("#ipg-prompt-editor").attr("data-id");
        const prompt = $("#ipg-pe-prompt1").val().trim();

        if ( !prompt ) {
            alert("Please enter a prompt for Image 1.");
            return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true);
        $("#ipg-spinner1").show();
        $("#ipg-image1-result-row").hide();
        $("#ipg-prompt2-row").hide();
        $("#ipg-image2-result-row").hide();

        $.post(ipg_ajax.ajax_url, {
            action: "ipg_generate_image1",
            id:     id,
            prompt: prompt,
            nonce:  ipg_ajax.nonce
        })
        .done(function (response) {

            if ( response.success ) {

                // Show image 1
                $("#ipg-image1-result").html(`
                    <img src="${response.data.image_url}" 
                         style="max-width:300px; border:1px solid #ddd; border-radius:4px; display:block; margin-bottom:8px;">
                    <a href="${response.data.image_url}" target="_blank" class="button button-secondary">
                        Open Full Size
                    </a>
                `);
                $("#ipg-image1-result-row").show();

                // Unlock Image 2 prompt
                $("#ipg-prompt2-row").show();
                $("html, body").animate({
                    scrollTop: $("#ipg-prompt2-row").offset().top - 50
                }, 400);

            } else {
                alert("Error: " + response.data.message);
                $btn.prop("disabled", false);
            }
        })
        .fail(function () {
            alert("AJAX request failed. Please try again.");
            $btn.prop("disabled", false);
        })
        .always(function () {
            $("#ipg-spinner1").hide();
        });
    });


    // ── GENERATE IMAGE 2 ────────────────────────────────────────────────────
    $("#ipg-generate-image2-btn").on("click", function () {

        const id     = $("#ipg-prompt-editor").attr("data-id");
        const prompt = $("#ipg-pe-prompt2").val().trim();

        if ( !prompt ) {
            alert("Please enter a prompt for Image 2.");
            return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true);
        $("#ipg-spinner2").show();
        $("#ipg-image2-result-row").hide();

        $.post(ipg_ajax.ajax_url, {
            action: "ipg_generate_image2",
            id:     id,
            prompt: prompt,
            nonce:  ipg_ajax.nonce
        })
        .done(function (response) {

            if ( response.success ) {

                // Show image 2
                $("#ipg-image2-result").html(`
                    <img src="${response.data.image_url}" 
                         style="max-width:300px; border:1px solid #ddd; border-radius:4px; display:block; margin-bottom:8px;">
                    <a href="${response.data.image_url}" target="_blank" class="button button-secondary">
                        Open Full Size
                    </a>
                `);
                $("#ipg-image2-result-row").show();

            } else {
                alert("Error: " + response.data.message);
                $btn.prop("disabled", false);
            }
        })
        .fail(function () {
            alert("AJAX request failed. Please try again.");
            $btn.prop("disabled", false);
        })
        .always(function () {
            $("#ipg-spinner2").hide();
        });
    });

});