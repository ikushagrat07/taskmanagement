
	this.__construct = function() {
		this.loader(), this.commonForm(), this.submitForm();
		  var captcha = generateArithmeticCaptcha();
          displayCaptcha(captcha);
	},

	this.loader = function() {
		$(document).ready(function() {
			$(".loadermain").fadeOut("slow");
		});
	},
	this.commonForm = function() {
	$(document).on("submit", "#common-form", function(event) {
    event.preventDefault();

    $(".loader").fadeIn("slow");



    var action = $(this).attr("action");
    var contact = $(this).data("contact");
    var formData = new FormData(this); // 'this' should refer to the form element itself
    var captchaResult = checkCaptcha(captcha);
    formData.append('captcha', captchaResult);

    $(".loadermain").fadeIn("slow");

    $.ajax({
        url: action,
        type: "POST",
        data: formData,
        processData: false, // Prevent jQuery from automatically processing data
        contentType: false, // Prevent jQuery from automatically setting content type
        success: function(response) {
            $(".loadermain").fadeOut("slow");
            $(".form-group > .text-danger").remove();

            if (response.result === 0) {
                if (contact === "") {
                    $("html, body").animate({
                        scrollTop: 0
                    }, 800);
                }
                for (var field in response.errors) {
                    $("#" + field)
                    .parents(".form-group")
                    .append('<span class="text-danger">' + response.errors[field] + "</span>");
                }
            } else if (response.result === 1 || response.result === 5) {
                toastr.options = {
                    closeButton: false,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toast-top-center",
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                };
                toastr.remove();
                toastr.success(response.msg);
                if (response.url !== undefined) {
                    window.setTimeout(function() {
                        window.location.href = response.url;
                    }, 1000);
                }
            } else if (response.result === -1 || response.result === -2 || response.result === -5) {
                toastr.remove();
                toastr.error(response.msg);
                if (response.url !== undefined) {
                    window.setTimeout(function() {
                        window.location.href = response.url;
                    }, 1000);
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus, errorThrown);
        }
    });
});


	},
	this.submitForm = function() {
        $(document).on("submit", "#submit", function(event) {
            event.preventDefault();
            $(".loader").fadeIn("slow");

            var formdata1 = new FormData(this); // Collect form data

            $.ajax({
                url: $(this).attr("action"),
                type: "post",
                data: formdata1,
                processData: false,
                contentType: false,
                success: function(response) {
                    $(".loader").fadeOut("slow");
                    $(".form-group > .text-danger").remove();

                    if (response.result === 0) {
                        for (var field in response.errors) {
                            $("#" + field).parents(".form-group").append('<span class="text-danger">' + response.errors[field] + "</span>");
                            $("#" + field).focus();
                        }
                    }

                    if (response.result === 1) {
                        toastr.remove();
                        toastr.success(response.msg);
                        window.location.reload();
                    }

                    if (response.result === -1) {
                        toastr.remove();
                        toastr.error(response.msg);
                        return false;
                    }
                },
            });
        });
    }


    $("#show-all-btn").click(function(event) {
        event.preventDefault();
        $(".loader").fadeIn("slow");

        // Use Laravel Blade to correctly insert the URL for the route
        var url = "{{ url('getAllTasks') }}"; // This is where you use the Blade directive to generate the URL

        $.ajax({
            url: url, // Use the variable to hold the correct URL
            type: "GET",
            success: function(response) {
                $(".loader").fadeOut("slow");

                if (response.result === 1) {
                    // Clear the current task list
                    $("#task-list").empty();

                    // Loop through tasks and append to the list
                    response.tasks.forEach(function(task) {
                        $("#task-list").append("<li>" + task.task + "</li>");
                    });
                } else {
                    toastr.remove();
                    toastr.error("Failed to fetch tasks.");
                }
            },
            error: function() {
                $(".loader").fadeOut("slow");
                toastr.remove();
                toastr.error("An error occurred while fetching tasks.");
            }
        });
    });

	this.__construct();
	obj = new Events();
