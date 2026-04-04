<?php
$dataFile = 'data.json';

// Load data
$data = json_decode(file_get_contents($dataFile), true);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['about'])) {
        $data['about'] = $_POST['about'];
    }
    if (isset($_POST['skills'])) {
        $data['skills'] = explode(',', $_POST['skills']);
    }
    if (isset($_POST['project_title'])) {
        $newProject = [
            'title' => $_POST['project_title'],
            'description' => $_POST['project_description'],
            'image' => $_POST['project_image'],
            'link' => $_POST['project_link']
        ];
        $data['projects'][] = $newProject;
    }
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { margin-bottom: 20px; }
        input, textarea { display: block; margin: 10px 0; width: 100%; padding: 8px; }
        button { padding: 10px 20px; background: #333; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Admin Panel</h1>

    <h2>Edit About</h2>
    <form method="post">
        <textarea name="about" rows="4"><?php echo htmlspecialchars($data['about']); ?></textarea>
        <button type="submit">Update About</button>
    </form>

    <h2>Edit Skills (comma-separated)</h2>
    <form method="post">
        <input type="text" name="skills" value="<?php echo htmlspecialchars(implode(',', $data['skills'])); ?>">
        <button type="submit">Update Skills</button>
    </form>

    <h2>Add Project</h2>
    <form method="post">
        <input type="text" name="project_title" placeholder="Project Title" required>
        <textarea name="project_description" placeholder="Description" required></textarea>
        <input type="text" name="project_image" placeholder="Image URL" required>
        <input type="text" name="project_link" placeholder="Project Link" required>
        <button type="submit">Add Project</button>
    </form>

    <h2>Current Projects</h2>
    <ul>
        <?php foreach ($data['projects'] as $project): ?>
            <li><?php echo htmlspecialchars($project['title']); ?> - <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank">Link</a></li>
        <?php endforeach; ?>
    </ul>

    <a href="index.html">View Portfolio</a>
</body>
</html>