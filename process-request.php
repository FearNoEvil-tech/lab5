<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed");
}

//incoming data from the form
$course = trim($_POST["course"] ?? "");
$reason = trim($_POST["reason"] ?? "");
$preferredTime = $_POST["preferredtime"] ?? "";
$preferredDate = $_POST["preferreddate"] ?? "";
$topics = $_POST["topics"] ?? [];
$formName = $_POST["form_name"] ?? "";

if (!is_array($topics)) {
    $topics = [];
}

/*
echo "Course: $course\n <br>";
echo "Topic: $topic\n <br>";
echo "Preferred Time: $preferredTime\n <br>";
echo "Preferred Date: $preferredDate\n <br>";
echo "Topics selected: " . implode(", ", $topics) . "<br>";
echo "Form name: $formName\n <br>";
*/

//Validation
$errors = [];
$allowedCourses = ["IS 6465", "IS 6495", "IS 6570"];
$allowedTimes = ["Morning", "Afternoon", "Evening"];

if (!in_array($course, $allowedCourses, true)) {
    $errors["course"] = "Select a valid course.";
}

if (!in_array($preferredTime, $allowedTimes, true)) {
    $errors["preferred_time"] = "Select a valid preferred time.";
}

if (mb_strlen($reason) < 10 || mb_strlen($reason) > 500) {
    $errors["reason"] = "Describe your request using 10 to 500 characters.";
}

$today = new DateTimeImmutable("today");
$preferredDateTime = DateTimeImmutable::createFromFormat("!Y-m-d", $preferredDate);
$validDate = $preferredDateTime !== false && $preferredDateTime->format("Y-m-d") === $preferredDate;

if (!$validDate) {
    $errors["preferred_date"] = "Enter a valid preferred date.";
} elseif ($preferredDateTime < $today) {
    $errors["preferred_date"] = "Preferred date cannot be in the past.";
}

$allowedTopics = ["Exam review", "Practice problems", "Study plan"];
$validTopics = [];

foreach ($topics as $selectedTopic) {
    if (in_array($selectedTopic, $allowedTopics, true)) {
        $validTopics[] = $selectedTopic;
    }
}

if ($validTopics === []) {
    $errors["topics"] = "Select at least one valid topic.";
}

$safeTopics = array_map(
    fn($topic) => htmlspecialchars($topic, ENT_QUOTES, "UTF-8"),
    $validTopics
);

if ($formName !== "tutoring_request") {
    $errors["form_name"] = "This form submission is not valid.";
}

//if there are no errors, display the request details
if ($errors === []) {
    $safeTopics = array_map(
        fn($selectedTopic) => htmlspecialchars($selectedTopic, ENT_QUOTES, "UTF-8"),
        $validTopics
    );

    echo "<h1>Request received</h1>";
    echo "<p>Course: " . htmlspecialchars($course, ENT_QUOTES, "UTF-8") . "</p>";
    echo "<p>Request details: " . htmlspecialchars($reason, ENT_QUOTES, "UTF-8") . "</p>";
    echo "<p>Topics: " . implode(", ", $safeTopics) . "</p>";
    echo "<p>Status: Pending review</p>";
} else {
    http_response_code(400);

    echo "<h1>Request could not be submitted</h1>";
    echo "<ul>";

    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error, ENT_QUOTES, "UTF-8") . "</li>";
    }

    echo "</ul>";
}

?>