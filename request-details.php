<?php
require_once 'includes/header.php';
require_once 'includes/nav.php';

$requestId = filter_input(INPUT_GET, 'request_id', FILTER_VALIDATE_INT);

if ($requestId === false || $requestId === null || $requestId < 1) {
    exit('Choose a valid request.');
}

$requests = [
    [
        'request_id' => 12,
        'course' => 'IS 6465',
        'topic' => 'I need help with studying for the exam.',
        'preferreddate' => '2026-10-22',
        'preferredtime' => 'Morning',
        'topics' => ['Exam review', 'Study plan'],
        'status' => 'Pending'
    ],
    [
        'request_id' => 13,
        'course' => 'IS 6495',
        'topic' => 'I need help understanding arrays.',
        'preferreddate' => '2026-10-24',
        'preferredtime' => 'Afternoon',
        'topics' => ['Practice problems'],
        'status' => 'Pending'
    ],
    [
        'request_id' => 14,
        'course' => 'IS 6570',
        'topic' => 'I need help preparing a study plan.',
        'preferreddate' => '2026-10-27',
        'preferredtime' => 'Evening',
        'topics' => ['Study plan'],
        'status' => 'Pending'
    ]

];

$request = null;

foreach ($requests as $item) {
    if ($item['request_id'] === $requestId) {
        $request = $item;
        break;
    }
}

if ($request === null) {
    exit('Request not found.');
}
?>

<main class="container py-4">
    <h1>Request <?php echo $request['request_id']; ?></h1>

    <dl>
        <dt>Course</dt>
        <dd><?php echo htmlspecialchars($request['course'], ENT_QUOTES, 'UTF-8'); ?></dd>

        <dt>Request details</dt>
        <dd><?php echo htmlspecialchars($request['topic'], ENT_QUOTES, 'UTF-8'); ?></dd>

        <dt>Preferred date</dt>
        <dd><?php echo htmlspecialchars($request['preferreddate'], ENT_QUOTES, 'UTF-8'); ?></dd>

        <dt>Preferred time</dt>
        <dd><?php echo htmlspecialchars($request['preferredtime'], ENT_QUOTES, 'UTF-8'); ?></dd>

        <dt>Topics</dt>
        <dd>
            <?php
            $safeTopics = array_map(
                fn($topic) => htmlspecialchars($topic, ENT_QUOTES, 'UTF-8'),
                $request['topics']
            );

            echo implode(', ', $safeTopics);
            ?>
        </dd>

        <dt>Status</dt>
        <dd><?php echo htmlspecialchars($request['status'], ENT_QUOTES, 'UTF-8'); ?></dd>
    </dl>

    <a class="btn btn-secondary"
       href="edit-request.php?request_id=<?php echo $request['request_id']; ?>">
        Edit
    </a>

    <a class="btn btn-outline-danger"
       href="delete-request.php?request_id=<?php echo $request['request_id']; ?>">
        Cancel request
    </a>
</main>

<?php require_once 'includes/footer.php'; ?>