<?php
require_once 'includes/header.php';
require_once 'includes/nav.php';

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
?>

<main class="container py-4">
    <h1>Tutoring requests</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Request</th>
                <th>Course</th>
                <th>Preferred date</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request) { ?>
                <tr>
                    <td><?php echo $request['request_id']; ?></td>
                    <td><?php echo htmlspecialchars($request['course'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($request['preferreddate'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($request['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a class="btn btn-outline-primary btn-sm"
                           href="request-details.php?request_id=<?php echo $request['request_id']; ?>">
                            View
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</main>

<?php require_once 'includes/footer.php'; ?>