<?php

function validatePreferredTime(string $preferredTime): string
{
    $allowedTimes = ['Morning', 'Afternoon', 'Evening'];
    if (!in_array($preferredTime, $allowedTimes, true)) {
        return 'Choose a valid preferred time.';
    }
    return '';
}

function validateTopics(array $topics): string
{
    $allowedTopics = ["Exam review", "Practice problems", "Study plan"];

    if ($topics === []) {
        return "Select at least one valid topic.";
    }

    foreach ($topics as $topic) {
        if (!in_array($topic, $allowedTopics, true)) {
            return "Select at least one valid topic.";
        }
    }

    return "";
}

function validateReason(string $reason): string
{
    $length = mb_strlen(trim($reason));

    if ($length < 10 || $length > 500) {
        return "Describe your request using 10 to 500 characters.";
    }

    return "";
}