<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $mysqli->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $content);
    $stmt->execute();
    $stmt->close();
}

$posts = $mysqli->query("SELECT posts.id, posts.content, users.username, posts.created_at FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DodzMed</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Welcome to DodzMed</h1>
    <form method="post">
        <textarea name="content" required></textarea>
        <button type="submit">Post</button>
    </form>

    <h2>Posts</h2>
    <?php while ($row = $posts->fetch_assoc()): ?>
        <div>
            <strong><?php echo htmlspecialchars($row['username']); ?></strong>
            <p><?php echo htmlspecialchars($row['content']); ?></p>
            <small><?php echo $row['created_at']; ?></small>
            <a href="post.php?id=<?php echo $row['id']; ?>">View Comments</a>
        </div>
    <?php endwhile; ?>
</body>
</html>
