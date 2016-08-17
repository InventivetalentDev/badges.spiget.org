<?php
require "../vendor/autoload.php";
include("../internal/getjson.php");
include("../internal/helpers.php");

$app = new \Slim\Slim();

$app->get("/", function () {
    include("about.html");
});

//// Stats

$app->get("/stats/:query", function ($query) {
    $format = "svg";
    $label = "Unknown";
    $color = "blue";

    extractFormat($query, $format);
    extractOptions($query, $label, $color);

    if ("resources" === $query) {
        $label = "Resources";
    } else if ("authors" === $query) {
        $label = "Authors";
    }


    if ($json = getJson("http://api.spiget.org/v2/status")) {
        displayBadge($label, $json["stats"][$query], $color, $format);
    } else {
        // Resource or Version not found
        displayBadge($label, "unknown", "red", $format);
    }
});


//// Status

$app->get("/status/:query", function ($query) {
    $format = "svg";
    $label = "Unknown";
    $color = "blue";

    extractFormat($query, $format);
    extractOptions($query, $label, $color);

    if ("fetcher" === $query) {
        $label = "Fetcher";
    } else if ("fetcher-page" === $query) {
        $label = "Page";
    }

    if ($json = getJson("http://api.spiget.org/v2/status")) {
        if ("fetcher" === $query) {
            $active = $json["status"]["fetch"]["active"];
            displayBadge($label, $active ? "active" : "up-to-date", $active ? "orange" : "green", $format);
        }
        if ("fetcher-page" === $query) {
            displayBadge($label, $json["status"]["fetch"]["page"]["index"] . "/" . $json["status"]["fetch"]["page"]["amount"], $color, $format);
        }
    } else {
        // Resource or Version not found
        displayBadge($label, "unknown", "red", $format);
    }

});


//// Resources

$app->get("/resources/version/:query", function ($query) {
    $format = "svg";
    $label = "Version";
    $color = "blue";

    extractFormat($query, $format);
    extractOptions($query, $label, $color);

    if ($json = getJson("http://api.spiget.org/v2/resources/$query/versions/latest")) {
        displayBadge($label, $json["name"], $color, $format);
    } else {
        // Resource or Version not found
        displayBadge($label, "unknown", "red", $format);
    }
});

$app->get("/resources/rating/:query", function ($query) {
    $format = "svg";
    $label = "Rating";
    $color = "blue";

    $txtFormat = "%average_(%count)";
    if (isset($_GET["format"])) {
        $txtFormat = $_GET["format"];
    }

    extractFormat($query, $format);
    extractOptions($query, $label, $color);

    if ($json = getJson("http://api.spiget.org/v2/resources/$query")) {
        $formatted = $txtFormat;
        $formatted = str_replace("%average", $json["rating"]["average"], $formatted);
        $formatted = str_replace("%count", $json["rating"]["count"], $formatted);
        displayBadge($label, $formatted, $color, $format);
    } else {
        // Resource or Version not found
        displayBadge($label, "unknown", "red", $format);
    }
});

$app->get("/resources/downloads/:query", function ($query) {
    $format = "svg";
    $label = "Downloads";
    $color = "blue";

    extractFormat($query, $format);
    extractOptions($query, $label, $color);

    if ($json = getJson("http://api.spiget.org/v2/resources/$query")) {
        displayBadge($label, $json["downloads"], $color, $format);
    } else {
        // Resource or Version not found
        displayBadge($label, "unknown", "red", $format);
    }
});

$app->run();