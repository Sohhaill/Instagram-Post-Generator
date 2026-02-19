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


