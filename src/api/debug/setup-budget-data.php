<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $user_id = 2;
    
    // Clear existing budget categories for clean setup
    $pdo->prepare("DELETE FROM budget_categories WHERE user_id = ?")->execute([$user_id]);
    echo "Cleared existing budget categories for user $user_id\n";
    
    // Create realistic budget categories
    $categories = [
        [
            'title' => 'Food & Dining',
            'allocated_amount' => 15000.00,
            'spent_amount' => 11883.00, // Food & Groceries + Restaurant from your actual data
            'time_frame' => 'Monthly',
            'color_class' => 'bg-red-500',
            'icon_class' => 'bg-red-100 text-red-600 rounded-full p-1',
            'notes' => 'Groceries, restaurants, and food delivery'
        ],
        [
            'title' => 'Transportation', 
            'allocated_amount' => 8000.00,
            'spent_amount' => 4805.00, // Gas from your actual transactions
            'time_frame' => 'Monthly',
            'color_class' => 'bg-blue-500',
            'icon_class' => 'bg-blue-100 text-blue-600 rounded-full p-1',
            'notes' => 'Gas, public transport, parking fees'
        ],
        [
            'title' => 'Utilities & Bills',
            'allocated_amount' => 6000.00,
            'spent_amount' => 4200.00,
            'time_frame' => 'Monthly', 
            'color_class' => 'bg-yellow-500',
            'icon_class' => 'bg-yellow-100 text-yellow-600 rounded-full p-1',
            'notes' => 'Electricity, water, internet, phone bills'
        ],
        [
            'title' => 'Shopping & Personal',
            'allocated_amount' => 10000.00,
            'spent_amount' => 6500.00,
            'time_frame' => 'Monthly',
            'color_class' => 'bg-green-500', 
            'icon_class' => 'bg-green-100 text-green-600 rounded-full p-1',
            'notes' => 'Clothing, personal care, household items'
        ],
        [
            'title' => 'Entertainment',
            'allocated_amount' => 7000.00,
            'spent_amount' => 4200.00,
            'time_frame' => 'Monthly',
            'color_class' => 'bg-purple-500',
            'icon_class' => 'bg-purple-100 text-purple-600 rounded-full p-1', 
            'notes' => 'Movies, games, hobbies, recreation'
        ],
        [
            'title' => 'Health & Medical',
            'allocated_amount' => 4000.00,
            'spent_amount' => 2000.00,
            'time_frame' => 'Monthly',
            'color_class' => 'bg-pink-500',
            'icon_class' => 'bg-pink-100 text-pink-600 rounded-full p-1', 
            'notes' => 'Doctor visits, medicine, health insurance'
        ]
    ];
    
    echo "Creating budget categories...\n\n";
    
    $totalAllocated = 0;
    $totalSpent = 0;
    
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("
            INSERT INTO budget_categories 
            (user_id, title, allocated_amount, spent_amount, time_frame, color_class, icon_class, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $success = $stmt->execute([
            $user_id,
            $cat['title'],
            $cat['allocated_amount'], 
            $cat['spent_amount'],
            $cat['time_frame'],
            $cat['color_class'],
            $cat['icon_class'],
            $cat['notes']
        ]);
        
        if ($success) {
            $percentage = round(($cat['spent_amount'] / $cat['allocated_amount']) * 100, 1);
            echo "✓ {$cat['title']}: ₱" . number_format($cat['spent_amount']) . " / ₱" . number_format($cat['allocated_amount']) . " ({$percentage}%)\n";
            
            $totalAllocated += $cat['allocated_amount'];
            $totalSpent += $cat['spent_amount'];
        }
    }
    
    // Summary
    $overallPercentage = round(($totalSpent / $totalAllocated) * 100, 1);
    $remaining = $totalAllocated - $totalSpent;
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "MONTHLY BUDGET SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    echo "Total Allocated: ₱" . number_format($totalAllocated) . "\n";
    echo "Total Spent:     ₱" . number_format($totalSpent) . "\n";
    echo "Remaining:       ₱" . number_format($remaining) . "\n";
    echo "Budget Usage:    {$overallPercentage}%\n";
    echo "Status:          " . ($overallPercentage <= 100 ? "On Track" : "Over Budget") . "\n";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM budget_categories WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn();
    
    echo "\nBudget categories created: $count\n";
    echo "Budget data is now ready!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>