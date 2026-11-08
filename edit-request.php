<?php
require_once 'includes/header.php';
require_once 'includes/nav.php';

/* Example request data.
   Replace these values with the request record retrieved from your database. */
$request = [
    'request_id' => 12,
    'course' => 'IS 3540',
    'topic' => 'Help with PHP form processing.',
    'preferreddate' => '2026-10-22',
    'preferred_time' => 'Afternoon',
    'topics' => ['Practice problems', 'Study plan']
];
?>

<main class="container py-4">
    <h1>Edit tutoring request</h1>

    <form action="#" method="post" class="mt-4">
        <input type="hidden" name="request_id"
               value="<?php echo htmlspecialchars($request['request_id']); ?>">

        <div class="mb-3">
            <label for="course" class="form-label">Course</label>
            <input
                type="text"
                id="course"
                name="course"
                class="form-control"
                value="<?php echo htmlspecialchars($request['course']); ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label for="topic" class="form-label">What would you like help with?</label>
            <textarea
                id="topic"
                name="topic"
                class="form-control"
                rows="4"
                maxlength="120"
                required
            ><?php echo htmlspecialchars($request['topic']); ?></textarea>
        </div>

        <div class="mb-4">
            <label for="preferreddate" class="form-label">Preferred date</label>
            <input
                type="date"
                id="preferreddate"
                name="preferreddate"
                class="form-control"
                value="<?php echo htmlspecialchars($request['preferreddate']); ?>"
                required
            >
        </div>

        <fieldset class="mb-4">
            <legend class="fs-6 fw-bold">Topics</legend>

            <?php
            $topicOptions = ['Exam review', 'Practice problems', 'Study plan'];

            foreach ($topicOptions as $topicOption) {
                $checked = in_array($topicOption, $request['topics']) ? 'checked' : '';
                $id = strtolower(str_replace(' ', '-', $topicOption));
            ?>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="topic-<?php echo $id; ?>"
                        name="topics[]"
                        value="<?php echo htmlspecialchars($topicOption); ?>"
                        <?php echo $checked; ?>
                    >
                    <label class="form-check-label" for="topic-<?php echo $id; ?>">
                        <?php echo htmlspecialchars($topicOption); ?>
                    </label>
                </div>
            <?php } ?>
        </fieldset>

        <fieldset class="mb-4">
            <legend class="fs-6 fw-bold">Preferred time</legend>

            <?php
            $timeOptions = ['Morning', 'Afternoon', 'Evening'];

            foreach ($timeOptions as $timeOption) {
                $checked = $request['preferred_time'] === $timeOption ? 'checked' : '';
                $id = strtolower($timeOption);
            ?>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        id="preferred-time-<?php echo $id; ?>"
                        name="preferredtime"
                        value="<?php echo $timeOption; ?>"
                        <?php echo $checked; ?>
                        required
                    >
                    <label class="form-check-label" for="preferred-time-<?php echo $id; ?>">
                        <?php echo $timeOption; ?>
                    </label>
                </div>
            <?php } ?>
        </fieldset>

        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Save changes</button>
          <button class="btn btn-outline-secondary" type="button" onclick="window.location.href='requests.php'">Cancel</button>
        </div>
      </form>
    </main>

<?php require_once 'includes/footer.php'; ?>