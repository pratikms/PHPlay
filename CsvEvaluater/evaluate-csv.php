<?php

require_once __DIR__ . '/CsvEvaluater.php';

$csvEvaluater = new CsvEvaluator(__DIR__ . '/sample.csv');
$csvEvaluater->evaluate();