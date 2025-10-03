<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $user_id = 2;
    
    // Delete existing budget categories for clean setup
    $stmt = $pdo->prepare("DELETE FROM budget_categories WHERE user_id = ?");
    $stmt->execute([$user_id]);
    echo "Cleared existing budget categories.\n";
    
    echo "Creating sample budget categories with proper amounts...\n";
    
    $categories = [
        ['Food & Dining', 12000.00, 9500.00, 'Monthly', 'bg-red-500', 'bg-red-100 text-red-600 rounded-full p-1', 'Groceries and restaurant expenses'],
        ['Transportation', 8000.00, 4200.00, 'Monthly', 'bg-blue-500', 'bg-blue-100 text-blue-600 rounded-full p-1', 'Gas, public transport, parking'],
        ['Entertainment', 9000.00, 7200.00, 'Monthly', 'bg-purple-500', 'bg-purple-100 text-purple-600 rounded-full p-1', 'Movies, games, hobbies'],
        ['Shopping', 9000.00, 3200.00, 'Monthly', 'bg-green-500', 'bg-green-100 text-green-600 rounded-full p-1', 'Clothing and personal items'],
        ['Utilities', 5000.00, 4800.00, 'Monthly', 'bg-yellow-500', 'bg-yellow-100 text-yellow-600 rounded-full p-1', 'Electric, water, internet']
    ];
    
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, title, allocated_amount, spent_amount, time_frame, color_class, icon_class, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $cat[0], // title
            $cat[1], // allocated_amount
            $cat[2], // spent_amount
            $cat[3], // time_frame
            $cat[4], // color_class
            $cat[5], // icon_class
            $cat[6]  // notes
        ]);
        echo "Created: " . $cat[0] . " - ₱" . number_format($cat[2]) . " / ₱" . number_format($cat[1]) . "\n";
    }
    
    echo "Sample budget categories created successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>