<?php
// Define CSV file paths
$pollsFile = 'polls.csv';

// Initialize variables
$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Function to generate unique poll ID
function generatePollID() {
    return uniqid('poll_', true);
}

// Function to read all polls
function readPolls($pollsFile) {
    $polls = [];
    if (file_exists($pollsFile)) {
        $rows = array_map('str_getcsv', file($pollsFile));
        foreach ($rows as $row) {
            if (count($row) >= 3) {
                $polls[] = [
                    'id' => $row[0],
                    'question' => $row[1],
                    'options' => explode('|', $row[2])
                ];
            }
        }
    }
    return $polls;
}

// Function to get poll by ID
function getPollByID($polls, $poll_id) {
    foreach ($polls as $poll) {
        if ($poll['id'] === $poll_id) {
            return $poll;
        }
    }
    return null;
}

// Handle creating a new poll
if ($action === 'create_poll' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $options = array_map('trim', explode(',', $_POST['options']));
    $options = array_filter($options);
    
    if (empty($question) || count($options) < 2) {
        $message = 'Please enter a question and at least two options.';
    } else {
        $poll_id = generatePollID();
        $optionsStr = implode('|', $options);
        $file = fopen($pollsFile, 'a');
        fputcsv($file, [$poll_id, $question, $optionsStr]);
        fclose($file);
        $message = 'Poll created successfully!';
        header("Location: ?action=view_poll&poll_id=$poll_id&message=" . urlencode($message));
        exit();
    }
}

// Handle voting
if ($action === 'vote' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $poll_id = $_GET['poll_id'];
    $name = trim($_POST['name']);
    $vote = $_POST['vote'];
    
    $polls = readPolls($pollsFile);
    $poll = getPollByID($polls, $poll_id);
    
    if (!$poll) {
        $message = 'Poll not found.';
    } elseif (empty($name)) {
        $message = 'Please enter your name.';
    } elseif (!in_array($vote, $poll['options'])) {
        $message = 'Invalid poll option selected.';
    } else {
        $votesFile = "votes_$poll_id.csv";
        if (!file_exists($votesFile)) {
            $file = fopen($votesFile, 'w');
            fclose($file);
        }
        $votes = array_map('str_getcsv', file($votesFile));
        $hasVoted = false;
        foreach ($votes as $row) {
            if (strtolower($row[0]) === strtolower($name)) {
                $hasVoted = true;
                break;
            }
        }
        if ($hasVoted) {
            $message = 'You have already voted.';
        } else {
            $file = fopen($votesFile, 'a');
            fputcsv($file, [$name, $vote]);
            fclose($file);
            $message = 'Thank you for voting!';
            header("Location: ?action=view_poll&poll_id=$poll_id&message=" . urlencode($message));
            exit();
        }
    }
}

// Handle viewing poll results
if ($action === 'view_poll') {
    $poll_id = $_GET['poll_id'];
    $polls = readPolls($pollsFile);
    $poll = getPollByID($polls, $poll_id);
    if (!$poll) {
        $message = 'Poll not found.';
    } else {
        $votesFile = "votes_$poll_id.csv";
        $voteCounts = array_fill_keys($poll['options'], 0);
        if (file_exists($votesFile)) {
            $votes = array_map('str_getcsv', file($votesFile));
            foreach ($votes as $row) {
                if (isset($voteCounts[$row[1]])) {
                    $voteCounts[$row[1]]++;
                }
            }
        }
    }
}

// Read all polls for dashboard
$allPolls = readPolls($pollsFile);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Poll System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            height: 80px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            color: #ff0000;
            text-align: center;
            margin-top: 10px;
        }
        .poll-list {
            margin-top: 30px;
        }
        .poll-item {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .poll-item:last-child {
            border-bottom: none;
        }
        .poll-actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .poll-actions a:hover {
            text-decoration: underline;
        }
        .chart-container {
            width: 100%;
            height: 400px;
            margin-top: 30px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <?php if ($action === 'dashboard'): ?>
        <h2>Poll Dashboard</h2>
        <?php if (isset($_GET['message'])): ?>
            <p class="message"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <h3>Create a New Poll</h3>
        <?php if ($message && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST" action="?action=create_poll">
            <label for="question">Poll Question:</label>
            <textarea id="question" name="question" required></textarea>
            
            <label for="options">Options (separated by commas):</label>
            <input type="text" id="options" name="options" placeholder="Option 1, Option 2, Option 3" required>
            
            <button type="submit">Create Poll</button>
        </form>
        
        <div class="poll-list">
            <h3>Existing Polls</h3>
            <?php if (empty($allPolls)): ?>
                <p>No polls available.</p>
            <?php else: ?>
                <?php foreach ($allPolls as $poll): ?>
                    <div class="poll-item">
                        <strong><?php echo htmlspecialchars($poll['question']); ?></strong>
                        <div class="poll-actions">
                            <a href="?action=vote&poll_id=<?php echo urlencode($poll['id']); ?>">Vote</a>
                            <a href="?action=view_poll&poll_id=<?php echo urlencode($poll['id']); ?>">View Results</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php elseif ($action === 'vote' && isset($_GET['poll_id'])): ?>
        <?php
            $poll_id = $_GET['poll_id'];
            $poll = getPollByID($allPolls, $poll_id);
            if (!$poll) {
                echo "<p class='message'>Poll not found.</p>";
            } else {
        ?>
        <h2><?php echo htmlspecialchars($poll['question']); ?></h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST" action="?action=vote&poll_id=<?php echo urlencode($poll_id); ?>">
            <input type="text" name="name" placeholder="Enter your name" required>
            <div>
                <?php foreach ($poll['options'] as $option): ?>
                    <label>
                        <input type="radio" name="vote" value="<?php echo htmlspecialchars($option); ?>" required>
                        <?php echo htmlspecialchars($option); ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
            <button type="submit">Submit Vote</button>
        </form>
        <p><a href="?action=dashboard">Back to Dashboard</a></p>
        <?php } ?>
    <?php elseif ($action === 'view_poll' && isset($_GET['poll_id'])): ?>
        <?php
            $poll_id = $_GET['poll_id'];
            $poll = getPollByID($allPolls, $poll_id);
            if (!$poll) {
                echo "<p class='message'>Poll not found.</p>";
            } else {
                $votesFile = "votes_$poll_id.csv";
                $voteCounts = array_fill_keys($poll['options'], 0);
                if (file_exists($votesFile)) {
                    $votes = array_map('str_getcsv', file($votesFile));
                    foreach ($votes as $row) {
                        if (isset($voteCounts[$row[1]])) {
                            $voteCounts[$row[1]]++;
                        }
                    }
                }
        ?>
        <h2>Results for: <?php echo htmlspecialchars($poll['question']); ?></h2>
        <?php if (isset($_GET['message'])): ?>
            <p class="message"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <div class="chart-container">
            <canvas id="resultsChart"></canvas>
        </div>
        <script>
            const ctx = document.getElementById('resultsChart').getContext('2d');
            const resultsData = {
                labels: <?php echo json_encode(array_keys($voteCounts)); ?>,
                datasets: [{
                    label: 'Number of Votes',
                    data: <?php echo json_encode(array_values($voteCounts)); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            };
            const resultsChart = new Chart(ctx, {
                type: 'bar',
                data: resultsData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision:0
                        }
                    }
                }
            });
        </script>
        <p><a href="?action=dashboard">Back to Dashboard</a></p>
        <?php } ?>
    <?php else: ?>
        <h2>Invalid Action</h2>
        <p><a href="?action=dashboard">Go to Dashboard</a></p>
    <?php endif; ?>
</div>
</body>
</html>
