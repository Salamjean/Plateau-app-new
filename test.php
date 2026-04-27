<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Kreait\Firebase\JWT\IdTokenVerifier::createWithProjectId(config('firebase.projects.app.project_id', 'plateau-apps-user'));
    echo "Verifier OK\n";
    $factory = app('firebase.auth');
    echo "Auth OK\n";
} catch (\Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
