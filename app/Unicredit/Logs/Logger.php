<?php

namespace App\Unicredit\Logs;

interface Logger {
    public function log($resource, $data);
}