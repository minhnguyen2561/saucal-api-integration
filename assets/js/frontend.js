jQuery(function ($) {

    $(document).on('submit', 'form.woocommerce-saucal-api-form', function (e) {
        e.preventDefault();
        let submitBtnEle = $(this).find("button[type='submit']");
        let resultContainer = $("body").find(".saucal-api-result-container");
        if (!submitBtnEle.length) {
            return false;
        }

        let submitBtnTxt = submitBtnEle.html();
        submitBtnEle.prop("disabled", true).html(window.saucal_ajax_obj.i18n_loading_state);
        resultContainer.hide();
        $(".saucal-api-message").html();

        $.ajax({
            url: window.saucal_ajax_obj.ajaxurl,
            data: {
                elements: $(this).find("#sc_api_data").val(),
                user_id: window.saucal_ajax_obj.current_user_id,
                sc_api_request: window.saucal_ajax_obj.nonce,
                action: 'saucal_request_api',
            },
            type: 'POST',
            success: function (resp) {
                if (resp.success === false) {
                    $(".saucal-api-message").html(resp.data.message);
                } else {
                    if (!resultContainer.length) {
                        return false;
                    }
                    resultContainer.html(resp.data.data).fadeIn();
                }
                submitBtnEle.prop("disabled", false).html(submitBtnTxt);

            }
        });
    })
})