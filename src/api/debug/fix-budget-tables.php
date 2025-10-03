<?php
// Fix budget tables structure
$host = 'localhost';
$dbname = 'pesopal';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Fixing Budget Tables Structure</h2>";
    
    // Drop existing tables if they exist
    echo "Dropping existing tables...<br>";
    $pdo->exec("DROP TABLE IF EXISTS budget_categories");
    $pdo->exec("DROP TABLE IF EXISTS savings_goals");
    
    // Create budget_categories table with correct structure
    echo "Creating budget_categories table...<br>";
    $pdo->exec("
        CREATE TABLE budget_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            allocated DECIMAL(10,2) NOT NULL DEFAULT 0,
            spent DECIMAL(10,2) NOT NULL DEFAULT 0,
            time_frame VARCHAR(50) DEFAULT 'Monthly',
            icon_class TEXT,
            color_class VARCHAR(100),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    // Create savings_goals table with correct structure
    echo "Creating savings_goals table...<br>";
    $pdo->exec("
        CREATE TABLE savings_goals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            target_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            saved_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            target_date DATE,
            icon_class TEXT,
            color VARCHAR(50),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    echo "Tables created successfully!<br><br>";
    
    // Get the first user ID to use for sample data
    $userStmt = $pdo->prepare("SELECT id FROM users LIMIT 1");
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "No users found. Please register a user first.<br>";
        exit;
    }
    
    $user_id = $user['id'];
    echo "Using user ID: $user_id<br><br>";
    
    // Insert sample budget categories
    $categories = [
        [
            'title' => 'Food & Dining',
            'allocated' => 12000,
            'spent' => 9500,
            'time_frame' => 'Monthly',
            'icon_class' => 'bg-red-100 text-red-600 rounded-full p-1',
            'color_class' => 'bg-red-500',
            'notes' => 'Groceries and restaurant expenses'
        ],
        [
            'title' => 'Transportation',
            'allocated' => 8000,
            'spent' => 4200,
            'time_frame' => 'Monthly',
            'icon_class' => 'bg-blue-100 text-blue-600 rounded-full p-1',
            'color_class' => 'bg-blue-500',
            'notes' => 'Gas, public transport, parking'
        ],
        [
            'title' => 'Entertainment',
            'allocated' => 9000,
            'spent' => 7200,
            'time_frame' => 'Monthly',
            'icon_class' => 'bg-purple-100 text-purple-600 rounded-full p-1',
            'color_class' => 'bg-purple-500',
            'notes' => 'Movies, games, activities'
        ],
        [
            'title' => 'Shopping',
            'allocated' => 9000,
            'spent' => 3200,
            'time_frame' => 'Monthly',
            'icon_class' => 'bg-green-100 text-green-600 rounded-full p-1',
            'color_class' => 'bg-green-500',
            'notes' => 'Clothing, personal items'
        ]
    ];
    
    $categoryStmt = $pdo->prepare("
        INSERT INTO budget_categories (user_id, title, allocated, spent, time_frame, icon_class, color_class, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($categories as $cat) {
        $categoryStmt->execute([
            $user_id,
            $cat['title'],
            $cat['allocated'],
            $cat['spent'],
            $cat['time_frame'],
            $cat['icon_class'],
            $cat['color_class'],
            $cat['notes']
        ]);
    }
    
    echo "Inserted " . count($categories) . " budget categories.<br><br>";
    
    // Insert sample savings goals
    $goals = [
        [
            'title' => 'Emergency Fund',
            'target_amount' => 120000,
            'saved_amount' => 78500,
            'target_date' => '2024-12-31',
            'icon_class' => 'bg-blue-100 text-blue-600 rounded-full p-1',
            'color' => '#2563eb',
            'notes' => '6 months of expenses'
        ],
        [
            'title' => 'Vacation Fund',
            'target_amount' => 50000,
            'saved_amount' => 32000,
            'target_date' => '2024-06-30',
            'icon_class' => 'bg-green-100 text-green-600 rounded-full p-1',
            'color' => '#22c55e',
            'notes' => 'Summer vacation to Japan'
        ],
        [
            'title' => 'New Laptop',
            'target_amount' => 80000,
            'saved_amount' => 45000,
            'target_date' => '2024-03-31',
            'icon_class' => 'bg-purple-100 text-purple-600 rounded-full p-1',
            'color' => '#a855f7',
            'notes' => 'Macbook Pro for work'
        ]
    ];
    
    $goalStmt = $pdo->prepare("
        INSERT INTO savings_goals (user_id, title, target_amount, saved_amount, target_date, icon_class, color, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($goals as $goal) {
        $goalStmt->execute([
            $user_id,
            $goal['title'],
            $goal['target_amount'],
            $goal['saved_amount'],
            $goal['target_date'],
            $goal['icon_class'],
            $goal['color'],
            $goal['notes']
        ]);
    }
    
    echo "Inserted " . count($goals) . " savings goals.<br><br>";
    
    // Verify table structure
    echo "<h3>Verifying Table Structure:</h3>";
    
    echo "<strong>Budget Categories Table:</strong><br>";
    $result = $pdo->query("DESCRIBE budget_categories");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']}<br>";
    }
    
    echo "<br><strong>Savings Goals Table:</strong><br>";
    $result = $pdo->query("DESCRIBE savings_goals");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']}<br>";
    }
    
    // Show current budget data
    echo "<br><h3>Current Budget Categories:</h3>";
    $categories = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ?");
    $categories->execute([$user_id]);
    while ($cat = $categories->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$cat['title']}: ₱{$cat['spent']} / ₱{$cat['allocated']}<br>";
    }
    
    echo "<br><h3>Current Savings Goals:</h3>";
    $goals = $pdo->prepare("SELECT * FROM savings_goals WHERE user_id = ?");
    $goals->execute([$user_id]);
    while ($goal = $goals->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$goal['title']}: ₱{$goal['saved_amount']} / ₱{$goal['target_amount']}<br>";
    }
    
    echo "<br><strong>Budget tables fixed and sample data created successfully!</strong>";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>