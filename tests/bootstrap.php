<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../inc/LimitLoginAttempts.php';
require_once __DIR__ . '/../inc/ns-form-validator.php';

WP_Mock::bootstrap();

require_once __DIR__ . '/../security.php';
