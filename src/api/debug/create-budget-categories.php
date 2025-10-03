<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $user_id = 2;
    
    // Check if budget categories exist for user 2
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM budget_categories WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $categoryCount = $stmt->fetchColumn();
    
    echo "Current budget categories for user 2: $categoryCount\n";
    
    if ($categoryCount == 0) {
        echo "Creating sample budget categories...\n";
        
        $categories = [
            ['Food & Dining', 12000, 9500, 'Monthly', 'bg-red-500', 'bg-red-100 text-red-600 rounded-full p-1'],
            ['Transportation', 8000, 4200, 'Monthly', 'bg-blue-500', 'bg-blue-100 text-blue-600 rounded-full p-1'],
            ['Entertainment', 9000, 7200, 'Monthly', 'bg-purple-500', 'bg-purple-100 text-purple-600 rounded-full p-1'],
            ['Shopping', 9000, 3200, 'Monthly', 'bg-green-500', 'bg-green-100 text-green-600 rounded-full p-1'],
            ['Utilities', 5000, 4800, 'Monthly', 'bg-yellow-500', 'bg-yellow-100 text-yellow-600 rounded-full p-1']
        ];
        
        foreach ($categories as $cat) {
            $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, title, allocated_amount, spent_amount, time_frame, color_class, icon_class) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id,
                $cat[0], // title
                $cat[1], // allocated
                $cat[2], // spent
                $cat[3], // time_frame
                $cat[4], // color_class
                $cat[5]  // icon_class
            ]);
            echo "Created: " . $cat[0] . "\n";
        }
        
        echo "Sample budget categories created successfully!\n";
    } else {
        echo "Budget categories already exist:\n";
        $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ?");
        $stmt->execute([$user_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['title'] . ": ₱" . number_format($row['spent_amount']) . " / ₱" . number_format($row['allocated_amount']) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>