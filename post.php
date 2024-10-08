<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$post_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $mysqli->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $post_id, $user_id, $content);
    $stmt->execute();
    $stmt->close();
}

// Fetch post
$stmt = $mysqli->prepare("SELECT content, user_id FROM posts WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->bind_result($post_content, $post_user_id);
$stmt->fetch();
$stmt->close();

// Fetch comments
$comments = $mysqli->query("SELECT comments.content, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = $post_id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Post</h1>
    <p><?php echo htmlspecialchars($post_content); ?></p>
    <h2>Comments</h2>
    <?php while ($row = $comments->fetch_assoc()): ?>
        <div>
            <strong><?php echo htmlspecialchars($row['username']); ?></strong>
            <p><?php echo htmlspecialchars($row['content']); ?></p>
        </div>
    <?php endwhile; ?>

    <form method="post">
        <textarea name="content" required></textarea>
        <button type="submit">Comment</button>
    </form>
</body>
</html>
