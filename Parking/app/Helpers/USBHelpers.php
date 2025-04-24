<?php

namespace App\Helpers;

class USBHelpers
{
    /**
     * Find USB drive named "Parking"
     * 
     * @return string|null The path to the secret key file on the Parking USB drive, or null if not found
     */
    public static function findParkingUSB()
    {
        // Get all Windows drive letters
        $drives = array_filter(range('A', 'Z'), function ($drive) {
            return file_exists($drive . ':\\');
        });

        // Check each drive for the name "Parking"
        foreach ($drives as $drive) {
            // Try to get volume name using Windows 'vol' command
            $volume_info = shell_exec('vol ' . $drive . ': 2>nul');
            if ($volume_info) {
                // Extract volume name from the output
                if (preg_match('/Volume in drive .* is (.*)/', $volume_info, $matches)) {
                    $volume_name = trim($matches[1]);
                    if (strcasecmp($volume_name, 'Parking') === 0) {
                        return $drive . ':/.strangeThing';
                    }
                }
            }
        }

        return null;
    }
}
