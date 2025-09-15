<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Magazine;

echo "Moving existing cover images to correct location...\n";
echo "================================================\n\n";

// Get all magazines with cover images
$magazines = Magazine::whereNotNull('cover_image_path')->get();

if ($magazines->count() === 0) {
    echo "No magazines with cover images found.\n";
    exit;
}

echo "Found {$magazines->count()} magazines with cover images.\n\n";

$movedCount = 0;
$errorCount = 0;

foreach ($magazines as $magazine) {
    echo "Processing: {$magazine->title}\n";
    echo "Cover path: {$magazine->cover_image_path}\n";
    
    // Check if file exists in old location
    $oldPath = storage_path('app/public/' . $magazine->cover_image_path);
    $newPath = public_path('storage/' . $magazine->cover_image_path);
    $newDir = dirname($newPath);
    
    if (file_exists($oldPath)) {
        // Create directory if it doesn't exist
        if (!file_exists($newDir)) {
            mkdir($newDir, 0755, true);
            echo "  Created directory: {$newDir}\n";
        }
        
        // Copy file to new location
        if (copy($oldPath, $newPath)) {
            echo "  ✓ Moved: {$oldPath} → {$newPath}\n";
            $movedCount++;
            
            // Set proper permissions
            chmod($newPath, 0644);
        } else {
            echo "  ✗ Failed to move file\n";
            $errorCount++;
        }
    } else {
        echo "  ⚠ File not found in old location: {$oldPath}\n";
        
        // Check if file already exists in new location
        if (file_exists($newPath)) {
            echo "  ✓ File already exists in new location\n";
            $movedCount++;
        } else {
            echo "  ✗ File not found anywhere\n";
            $errorCount++;
        }
    }
    
    echo "\n";
}

echo "================================================\n";
echo "Summary:\n";
echo "  Moved: {$movedCount} files\n";
echo "  Errors: {$errorCount} files\n";
echo "  Total: {$magazines->count()} magazines\n";

if ($movedCount > 0) {
    echo "\n✓ Cover images have been moved to the correct location!\n";
    echo "  New location: public/storage/magazines/covers/\n";
    echo "  Test URLs should now work.\n";
} else {
    echo "\n⚠ No files were moved. Check the file paths and permissions.\n";
}

echo "\nDone!\n";


