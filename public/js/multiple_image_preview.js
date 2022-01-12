$(document).ready(function() {
    if (window.File && window.FileList && window.FileReader) {
        $("#single_files").on("change", function(e) {

            var files = e.target.files,
                filesLength = files.length;

            var f = files[0];
            var fileReader = new FileReader();
            fileReader.onload = (function(e) {
                var file = e.target;
                if (!$(".pip_single").length) {
                    $("<span class=\"pip_single\">" +
                        "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                        "<br/><span class=\"remove_single\">Remove image</span>" +
                        "</span>").insertAfter("#single_files");
                }
                $(".remove_single").click(function() {
                    $(this).parent(".pip_single").remove();
                    $("#single_files").val(null);
                });

                // Old code here
                /*$("<img></img>", {
                  class: "imageThumb",
                  src: e.target.result,
                  title: file.name + " | Click to remove"
                }).insertAfter("#files").click(function(){$(this).remove();});*/

            });
            fileReader.readAsDataURL(f);

        });

        $("#multiple_files").on("change", function(e) {

            var files = e.target.files,
                filesLength = files.length;
            for (var i = 0; i < filesLength; i++) {
                var f = files[i]
                var fileReader = new FileReader();
                fileReader.onload = (function(e) {
                    var file = e.target;
                    $("<span class=\"pip_multiple\">" +
                        "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                        "<br/><span class=\"remove_multiple\">Remove image</span>" +
                        "</span>").insertAfter("#multiple_files");
                    $(".remove_multiple").click(function() {
                        $(this).parent(".pip_multiple").remove();
                    });

                    // Old code here
                    /*$("<img></img>", {
                      class: "imageThumb",
                      src: e.target.result,
                      title: file.name + " | Click to remove"
                    }).insertAfter("#files").click(function(){$(this).remove();});*/

                });
                fileReader.readAsDataURL(f);
            }
        });


    } else {
        alert("Your browser doesn't support to File API")
    }
});