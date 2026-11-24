<?php
declare(strict_types=1);

define('APP_NAME', 'LMS ASJ');
define('SCHOOL_NAME', 'SMKN 1 Kepulauan Selayar');
define('BASE_URL', '/lms-pilajara'); // Ubah sesuai nama folder instalasi; gunakan '' jika di root domain.
define('DB_HOST', 'localhost');
define('DB_NAME', 'lms_asj');
define('DB_USER', 'root');
define('DB_PASS', '');
define('UPLOAD_DIR', dirname(__DIR__) . '/storage/uploads');
define('MAX_UPLOAD_BYTES', 10 * 1024 * 1024);
date_default_timezone_set('Asia/Makassar');
