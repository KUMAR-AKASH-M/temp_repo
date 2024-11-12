<?php
require_once 'config.php';
require_once 'SocialMediaAnalytics.php';

$analytics = new SocialMediaAnalytics($conn);

// Initialize variables
$facebook_data = $instagram_data = $twitter_data = [];
$facebook_current = $instagram_current = $twitter_current = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facebook_page = filter_input(INPUT_POST, 'facebook_page', FILTER_SANITIZE_STRING);
    $instagram_username = filter_input(INPUT_POST, 'instagram_username', FILTER_SANITIZE_STRING);
    $twitter_username = filter_input(INPUT_POST, 'twitter_username', FILTER_SANITIZE_STRING);

    // Fetch data for the last 30 days
    $start_date = date('Y-m-d', strtotime('-30 days'));
    $end_date = date('Y-m-d');

    // Generate random data for each platform
    if (!empty($facebook_page)) {
        $facebook_current = $analytics->fetchFacebookData($facebook_page);
        $facebook_data = $analytics->getAnalytics($facebook_page, 'facebook', $start_date, $end_date);
    }

    if (!empty($instagram_username)) {
        $instagram_current = $analytics->fetchInstagramData($instagram_username);
        $instagram_data = $analytics->getAnalytics($instagram_username, 'instagram', $start_date, $end_date);
    }

    if (!empty($twitter_username)) {
        $twitter_current = $analytics->fetchTwitterData($twitter_username);
        $twitter_data = $analytics->getAnalytics($twitter_username, 'twitter', $start_date, $end_date);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media Analytics Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Social Media Analytics Dashboard</h1>
        
        <form method="POST" action="" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="facebook_page">Facebook Page Name</label>
                    <input type="text" class="form-control" id="facebook_page" name="facebook_page" placeholder="e.g., cocacola">
                </div>
                <div class="form-group col-md-4">
                    <label for="instagram_username">Instagram Username</label>
                    <input type="text" class="form-control" id="instagram_username" name="instagram_username" placeholder="e.g., instagram">
                </div>
                <div class="form-group col-md-4">
                    <label for="twitter_username">Twitter Username</label>
                    <input type="text" class="form-control" id="twitter_username" name="twitter_username" placeholder="e.g., twitter">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Generate Analytics</button>
        </form>

        <?php if ($facebook_current || $instagram_current || $twitter_current): ?>
            <div class="row">
                <?php if ($facebook_current): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Facebook Engagement</h5>
                                <p>Followers: <?php echo number_format($facebook_current['followers']); ?></p>
                                <p>Likes: <?php echo number_format($facebook_current['likes']); ?></p>
                                <p>Recent Posts: <?php echo number_format($facebook_current['recent_posts']); ?></p>
                                <div class="chart-container">
                                    <canvas id="facebookChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($instagram_current): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Instagram Engagement</h5>
                                <p>Followers: <?php echo number_format($instagram_current['followers']); ?></p>
                                <p>Posts: <?php echo number_format($instagram_current['posts']); ?></p>
                                <div class="chart-container">
                                    <canvas id="instagramChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($twitter_current): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Twitter Engagement</h5>
                                <p>Followers: <?php echo number_format($twitter_current['followers']); ?></p>
                                <p>Tweets: <?php echo number_format($twitter_current['tweets']); ?></p>
                                <div class="chart-container">
                                    <canvas id="twitterChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h2>Engagement Insights</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Platform</th>
                                <th>Followers</th>
                                <th>Engagement Metric</th>
                                <th>Engagement Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($facebook_current): 
                                $fb_engagement = $facebook_current['followers'] > 0 ? 
                                    ($facebook_current['likes'] / $facebook_current['followers']) * 100 : 0;
                            ?>
                                <tr>
                                    <td>Facebook</td>
                                    <td><?php echo number_format($facebook_current['followers']); ?></td>
                                    <td><?php echo number_format($facebook_current['likes']); ?> likes</td>
                                    <td><?php echo number_format($fb_engagement, 2); ?>%</td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($instagram_current):
                                $ig_engagement = $instagram_current['followers'] > 0 ? 
                                    ($instagram_current['posts'] / $instagram_current['followers']) * 100 : 0;
                            ?>
                                <tr>
                                    <td>Instagram</td>
                                    <td><?php echo number_format($instagram_current['followers']); ?></td>
                                    <td><?php echo number_format($instagram_current['posts']); ?> posts</td>
                                    <td><?php echo number_format($ig_engagement, 2); ?>%</td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($twitter_current):
                                $tw_engagement = $twitter_current['followers'] > 0 ? 
                                    ($twitter_current['tweets'] / $twitter_current['followers']) * 100 : 0;
                            ?>
                                <tr>
                                    <td>Twitter</td>
                                    <td><?php echo number_format($twitter_current['followers']); ?></td>
                                    <td><?php echo number_format($twitter_current['tweets']); ?> tweets</td>
                                    <td><?php echo number_format($tw_engagement, 2); ?>%</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // JavaScript code for creating charts and populating data
        const facebookData = <?php echo json_encode($facebook_data); ?>;
        const instagramData = <?php echo json_encode($instagram_data); ?>;
        const twitterData = <?php echo json_encode($twitter_data); ?>;

        function createChart(elementId, data, label) {
            const ctx = document.getElementById(elementId);
            if (ctx && data.length > 0) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(item => item.date),
                        datasets: [{
                            label: label,
                            data: data.map(item => item.metrics.followers),
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        }

        createChart('facebookChart', facebookData, 'Facebook Followers');
        createChart('instagramChart', instagramData, 'Instagram Followers');
        createChart('twitterChart', twitterData, 'Twitter Followers');
    </script>
</body>
</html>