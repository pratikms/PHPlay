<?php

require_once __DIR__ . '/CsvEvaluater.php';

$csvEvaluater = new CsvEvaluator(__DIR__ . '/sample.csv');
echo var_export($csvEvaluater->evaluate(), true);