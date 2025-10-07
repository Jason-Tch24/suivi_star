<?php
$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - <?php echo $appConfig['name']; ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            text-align: center;
            padding: 2rem;
        }
        
        .error-content {
            max-width: 600px;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            opacity: 0.8;
        }
        
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .error-description {
            font-size: 1.1rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-light {
            background: white;
            color: #2c3e50;
        }
        
        .btn-light:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="error-code">403</div>
            <h1 class="error-message">Access Denied</h1>
            <p class="error-description">
                You don't have permission to access this page. 
                Please contact your administrator if you believe this is an error.
            </p>
            <div class="error-actions">
                <a href="/" class="btn btn-light">Go Home</a>
                <?php if (Auth::check()): ?>
                    <a href="<?php echo Auth::getDashboardUrl(); ?>" class="btn btn-secondary">Dashboard</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-secondary">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
