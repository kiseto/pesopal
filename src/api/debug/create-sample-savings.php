<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if user 2 has any savings goals
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM savings_goals WHERE user_id = 2");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    echo "Current savings goals for user 2: $count\n";
    
    if ($count == 0) {
        echo "Creating sample savings goals for user 2...\n";
        
        // Create some sample savings goals
        $goals = [
            [
                'title' => 'Emergency Fund',
                'target_amount' => 120000.00,
                'saved_amount' => 78500.00,
                'target_date' => '2025-12-31',
                'color' => '#dc2626',
                'status' => 'active'
            ],
            [
                'title' => 'Vacation Fund',
                'target_amount' => 50000.00,
                'saved_amount' => 25000.00,
                'target_date' => '2025-06-15',
                'color' => '#16a34a',
                'status' => 'active'
            ],
            [
                'title' => 'New Laptop',
                'target_amount' => 80000.00,
                'saved_amount' => 12000.00,
                'target_date' => '2025-03-30',
                'color' => '#2563eb',
                'status' => 'active'
            ]
        ];
        
        foreach ($goals as $goal) {
            $stmt = $pdo->prepare("INSERT INTO savings_goals (user_id, title, target_amount, saved_amount, target_date, color, status) VALUES (2, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $goal['title'],
                $goal['target_amount'],
                $goal['saved_amount'],
                $goal['target_date'],
                $goal['color'],
                $goal['status']
            ]);
            echo "Created: " . $goal['title'] . "\n";
        }
        
        echo "Sample savings goals created successfully!\n";
    } else {
        echo "Existing savings goals:\n";
        $stmt = $pdo->prepare("SELECT * FROM savings_goals WHERE user_id = 2");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['title'] . ": ₱" . number_format($row['saved_amount']) . " / ₱" . number_format($row['target_amount']) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>