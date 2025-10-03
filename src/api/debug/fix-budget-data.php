<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $user_id = 2;
    
    // Simple direct insertion without clearing first
    echo "Creating budget categories directly...\n";
    
    // Check if categories already exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM budget_categories WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo "Found $count existing categories. Deleting them first...\n";
        $pdo->prepare("DELETE FROM budget_categories WHERE user_id = ?")->execute([$user_id]);
    }
    
    // Insert one category at a time with error checking
    $categories = [
        'Food & Dining' => [15000, 11883, 'bg-red-500', 'bg-red-100 text-red-600 rounded-full p-1'],
        'Transportation' => [8000, 4805, 'bg-blue-500', 'bg-blue-100 text-blue-600 rounded-full p-1'],
        'Utilities' => [6000, 4200, 'bg-yellow-500', 'bg-yellow-100 text-yellow-600 rounded-full p-1'],
        'Shopping' => [10000, 6500, 'bg-green-500', 'bg-green-100 text-green-600 rounded-full p-1'],
        'Entertainment' => [7000, 4200, 'bg-purple-500', 'bg-purple-100 text-purple-600 rounded-full p-1']
    ];
    
    foreach ($categories as $title => $data) {
        try {
            $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, title, allocated_amount, spent_amount, time_frame, color_class, icon_class) VALUES (?, ?, ?, ?, 'Monthly', ?, ?)");
            $result = $stmt->execute([$user_id, $title, $data[0], $data[1], $data[2], $data[3]]);
            
            if ($result) {
                echo "✅ Created: $title - ₱" . number_format($data[1]) . " / ₱" . number_format($data[0]) . "\n";
            } else {
                echo "❌ Failed: $title\n";
                print_r($stmt->errorInfo());
            }
        } catch (Exception $e) {
            echo "❌ Error creating $title: " . $e->getMessage() . "\n";
        }
    }
    
    // Verify data was created
    $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n📊 VERIFICATION:\n";
    echo "Categories in database: " . count($categories) . "\n";
    
    $totalAllocated = 0;
    $totalSpent = 0;
    
    foreach ($categories as $cat) {
        $totalAllocated += $cat['allocated_amount'];
        $totalSpent += $cat['spent_amount'];
        echo "- {$cat['title']}: ₱" . number_format($cat['spent_amount']) . " / ₱" . number_format($cat['allocated_amount']) . "\n";
    }
    
    echo "\n💰 TOTALS:\n";
    echo "Total Allocated: ₱" . number_format($totalAllocated) . "\n";
    echo "Total Spent: ₱" . number_format($totalSpent) . "\n";
    echo "Remaining: ₱" . number_format($totalAllocated - $totalSpent) . "\n";
    
    echo "\n✅ Budget data is ready! Refresh your Budgeting page.\n";
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString() . "\n";
}
?>