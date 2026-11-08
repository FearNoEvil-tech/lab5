<?php require_once 'includes/header.php'; ?>
<?php require_once 'includes/nav.php'; ?>
<?php
$requestId = filter_input(INPUT_GET, 'request_id', FILTER_VALIDATE_INT);

if ($requestId === false || $requestId === null || $requestId < 1) {
    exit('Choose a valid request.');
}
?>

<main class="container py-4">
  <h1>Cancel tutoring request</h1>
  <p>Cancel request <?php echo $requestId; ?>?</p>
  <form action="#" method="post">
    <input type="hidden" name="request_id" value="12">
    <button class="btn btn-danger" type="submit">Yes, cancel request</button>
    <a class="btn btn-secondary" href="request-details.php?request_id=<?php echo $requestId; ?>">Keep request</a>
  </form>
</main>
<?php require_once 'includes/footer.php'; ?>
