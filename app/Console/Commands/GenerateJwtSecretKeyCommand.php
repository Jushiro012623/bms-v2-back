<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateJwtSecretKeyCommand extends Command
{
    protected $signature = 'jwt:secret';
    protected $description = 'Command to generate a JWT secret and save it to the .env file.';

    public function handle(): void
    {
        $jwtSecret = base64_encode(random_bytes(64));

        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        $keyValue = "JWT_SECRET=$jwtSecret";

        if (str_contains($envContent, 'JWT_SECRET=')) {
            $envContent = preg_replace('/JWT_SECRET=.*/', $keyValue, $envContent);
        } else {
            $envContent .= PHP_EOL . $keyValue;
        }

        File::put($envPath, $envContent);

        $this->info('JWT Secret generated successfully');
        $this->info($keyValue);
    }
}
