<?php

require_once __DIR__ . '/CsvEvaluator.php';

// TODO: Handle file input from CLI
$csvEvaluater = new CsvEvaluator(__DIR__ . '/sample.csv');
$csvEvaluater->evaluate();