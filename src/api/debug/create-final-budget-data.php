<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $user_id = 2;
    
    // Clear existing data
    $pdo->prepare("DELETE FROM budget_categories WHERE user_id = ?")->execute([$user_id]);
    
    echo "Creating comprehensive budget data...\n";
    
    // Insert budget categories one by one with error checking
    $categories = [
        ['Food & Dining', 12000.00, 9500.00, 'Monthly', 'bg-red-500', 'bg-red-100 text-red-600 rounded-full p-1', 'Groceries and restaurant expenses'],
        ['Transportation', 8000.00, 4200.00, 'Monthly', 'bg-blue-500', 'bg-blue-100 text-blue-600 rounded-full p-1', 'Gas, public transport, parking'],
        ['Entertainment', 9000.00, 7200.00, 'Monthly', 'bg-purple-500', 'bg-purple-100 text-purple-600 rounded-full p-1', 'Movies, games, hobbies'],
        ['Shopping', 9000.00, 3200.00, 'Monthly', 'bg-green-500', 'bg-green-100 text-green-600 rounded-full p-1', 'Clothing and personal items'],
        ['Utilities', 5000.00, 4800.00, 'Monthly', 'bg-yellow-500', 'bg-yellow-100 text-yellow-600 rounded-full p-1', 'Electric, water, internet']
    ];
    
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, title, allocated_amount, spent_amount, time_frame, color_class, icon_class, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([
            $user_id,
            $cat[0], // title
            $cat[1], // allocated_amount
            $cat[2], // spent_amount
            $cat[3], // time_frame
            $cat[4], // color_class
            $cat[5], // icon_class
            $cat[6]  // notes
        ])) {
            echo "✓ Created: " . $cat[0] . " - ₱" . number_format($cat[2]) . " / ₱" . number_format($cat[1]) . "\n";
        } else {
            echo "✗ Failed to create: " . $cat[0] . "\n";
            print_r($stmt->errorInfo());
        }
    }
    
    // Verify data was inserted
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM budget_categories WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn();
    
    echo "\nTotal categories created: $count\n";
    
    if ($count > 0) {
        echo "\n=== Budget Categories Created ===\n";
        $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ?");
        $stmt->execute([$user_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['title'] . ": ₱" . number_format($row['spent_amount']) . " / ₱" . number_format($row['allocated_amount']) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>