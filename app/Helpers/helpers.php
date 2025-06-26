<?php

use App\Models\MachineLog;
use App\Models\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

if (! function_exists('generateQRCode')) {
    function generateQRCode($data, $size = '')
    {
        $logoPath = storage_path('app/public/assets/etap-logo-500x499.png');

        // Convert the data to JSON if it's an array or object, else keep as string
        $jsonData = (is_array($data) || is_object($data)) ? json_encode($data) : $data;

        if ($size == '') {
            $size = 300; // Default size
            if (strlen($jsonData) > 100) {
                $size = 400; // Larger size for longer data
            }
            if (strlen($jsonData) > 250) {
                $size = 500; // Even larger size for very long data
            }
        }
        
        // Generate the QR code as a PNG image and encode it as base64
        $qrCode = QrCode::format('png')->size($size)->generate($jsonData);
        $base64QrCode = base64_encode($qrCode);
        $qrCodeImage = 'data:image/png;base64,' . $base64QrCode;

        // Create an image manager instance
        $manager = new ImageManager(new Driver());

        // Convert QR code binary to Intervention Image instance
        $qrImage = $manager->read($qrCodeImage);

        // Load QRPh logo (Ensure storage path is correct)
        $logoPath = storage_path('app/public/assets/etap-logo-500x499.png');

        if (file_exists($logoPath)) {
            $logo = $manager->read($logoPath)->scale(80, 80); // Resize for QR compatibility
            $qrImage->place($logo, 'center');
        }

        // Always return the QR code, with or without the logo
        return 'data:image/png;base64,' . base64_encode($qrImage->toPng());
    }
}

if (! function_exists('generateReferenceNo')) {
    function generateReferenceNo($name, $prefix = 'SYSTEM')
    {
        // Use DB transaction for concurrency safety
        return DB::transaction(function () use ($name, $prefix) {
            $sequence = Sequence::lockForUpdate()->where('sequence_name', $name)->first();

            if (! $sequence) {
                throw new \Exception("Sequence '{$name}' not found.");
            }

            $sequence->sequence_value += 1;
            $sequence->save();
            $number = str_pad($sequence->sequence_value, 10, '0', STR_PAD_LEFT);

            return "{$prefix}-{$number}";
        });
    }
}
