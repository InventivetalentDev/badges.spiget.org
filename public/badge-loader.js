$(document).ready(function () {
    console.log(badges);
    $.each(badges, function (bIndex, badge) {
        var $badgeWrapper = $("<div class='badge-element-wrapper well'></div>");

        $badgeWrapper.append("<h4>" + badge.name + "</h4>");

        var $badgeItem = $("<div class='form-inline badge-item-wrapper'><li class='badge-item' title='" + badge.name + "'></li></div>");
        var $resultImage = $("<img class='badge-result-image' src='https://i.inventivetalent.org/Badge-loading-e2e2e2.svg'/>");
        var $resultUrl = $("<input type='text' class='form-control badge-result-url' readonly>");

        var remainingName = badge.name;
        var badgeSegments = [];
        $.each(badge.variables, function (vIndex, variable) {
            var split = remainingName.split(variable.key);
            remainingName = split[1];

            $("<span>" + split[0] + "</span>").appendTo($badgeItem);
            badgeSegments.push({
                value: function () {
                    return split[0];
                }
            });

            var $input = $("<input class='form-control variable-input' type='text'/>");
            $input.data("variable", variable.key);
            if (variable.def !== undefined) {
                $input.prop("placeholder", variable.def);
                $input.val(variable.def);
            }
            if (variable.placeholder !== undefined) {
                $input.prop("placeholder", variable.placeholder);
            }
            if (variable.optional) {
                $input.addClass("optional-variable");
            }
            badgeSegments.push({
                value: function () {
                    return $input.val();
                }
            });

            $input.on("change", function () {
                refreshResult(badge, badgeSegments, $resultImage, $resultUrl);
            });

            $input.appendTo($badgeItem);
        });
        if (remainingName.length > 0) {
            $("<span>" + remainingName + "</span>").appendTo($badgeItem);
            badgeSegments.push({
                value: function () {
                    return remainingName;
                }
            });
        }


        var $resultDisplay = $("<div class='badge-result-wrapper'></div>")
        $resultUrl.appendTo($resultDisplay);
        $resultDisplay.append("&nbsp;");

        $resultImage.appendTo($badgeWrapper);
        $badgeItem.appendTo($badgeWrapper);
        $resultDisplay.appendTo($badgeWrapper);

        $badgeWrapper.appendTo($("#badgeList"));

        setTimeout(function () {
            refreshResult(badge, badgeSegments, $resultImage, $resultUrl);
        }, 2000);
    });

    function refreshResult(badge, badgeSegments, $img, $url) {
        var path = "";
        $.each(badgeSegments, function (index, segment) {
            path += segment.value();
        });

        path = badgeRoot + path;
        $url.val(path);
        preloadImage(path, $img);
    }
});

function preloadImage(source, $element, callback) {
    $preload = $("<img src='" + source + "' class='_image_preloader' style='display:none'>");
    $preload.on("load", function () {
        $element.attr("src", source);
        if (typeof callback === "function") {
            callback(source, $element);
        }
    });
    $preload.appendTo($("body"));
}