<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;

class CsvEvaluator {

    private $reader;
    private $spreadsheet;
    private $sheetData;

    public function __construct($csvFilePath) {
        $this->initializeReader();
        $this->initializeSpreasheet($csvFilePath);
    }

    private function initializeReader() {
        $this->reader = new Csv();
        $this->reader->setDelimiter(',');
        $this->reader->setEnclosure('');
        $this->reader->setSheetIndex(0);
    }    

    private function initializeSpreasheet($csvFilePath) {
        $this->spreadsheet = $this->reader->load($csvFilePath);
    }

    private function getEvaluatedCellValue($cell) {
        $evaluatedCellValue = 0;
        // TODO: Handle non-digit
        // TODO: Handle tab
        // TODO: Handle infinite recursion
        $cellValue = explode(' ', preg_replace('/\s{2,}/', ' ', $cell));
        echo PHP_EOL. PHP_EOL . 'Cell Value recurse: ' . var_export($cellValue, true) . PHP_EOL;
        foreach ($cellValue as $value) {
            echo 'Individual value recurse: ' . var_export($value, true) . PHP_EOL;
            if (is_numeric($value)) {
                echo 'Is numeric recurse' . PHP_EOL;
                $evaluatedCellValue += (int) $value;
            } elseif (!is_null($this->spreadsheet->getActiveSheet()->getCell($value)->getValue())) {
                echo 'Is not null recurse: ' . var_export($this->spreadsheet->getActiveSheet()->getCell($value)->getValue(), true) . ', value: ' . var_export($this->spreadsheet->getActiveSheet()->getCell($value)->getValue(), true) . PHP_EOL;
                if (is_numeric($this->spreadsheet->getActiveSheet()->getCell($value)->getValue())) {
                    $evaluatedCellValue += (int) $this->spreadsheet->getActiveSheet()->getCell($value)->getValue();    
                } else {
                    $tempEvaluatedCellValue = $this->getEvaluatedCellValue($this->spreadsheet->getActiveSheet()->getCell($value)->getValue());
                    if (is_numeric($tempEvaluatedCellValue)) {
                        $evaluatedCellValue += $tempEvaluatedCellValue;
                    } else {
                        $evaluatedCellValue = $tempEvaluatedCellValue;
                        break;
                    }
                }
                // $evaluatedCellValue += (int) $this->spreadsheet->getActiveSheet()->getCell($value)->getValue();
            } else {
                echo 'Final else recurse' . PHP_EOL;
                $evaluatedCellValue = '#ERR';
                break;
            }
        }
        return $evaluatedCellValue;
    }

    public function evaluate() {
        // return $this->getCsvData();
        $fp = fopen('output.csv', 'w');
        foreach ($this->getCsvData() as $row) {
            $evaluatedRow = array();
            foreach ($row as $cell) {
                $evaluatedCellValue = 0;
                // TODO: Handle non-digit
                // TODO: Handle tab
                // TODO: Handle infinite recursion
                $cellValue = explode(' ', preg_replace('/\s{2,}/', ' ', $cell));
                echo PHP_EOL. PHP_EOL . 'Cell Value: ' . var_export($cellValue, true) . PHP_EOL;
                foreach ($cellValue as $value) {
                    echo 'Individual value: ' . var_export($value, true) . PHP_EOL;
                    if (is_numeric($value)) {
                        echo 'Is numeric' . PHP_EOL;
                        $evaluatedCellValue += (int) $value;
                    } elseif (!is_null($this->spreadsheet->getActiveSheet()->getCell($value)->getValue())) {
                        echo 'Is not null: ' . var_export($this->spreadsheet->getActiveSheet()->getCell($value)->getValue(), true) . ', value: ' . var_export($this->spreadsheet->getActiveSheet()->getCell($value)->getValue(), true) . PHP_EOL;
                        // $evaluatedCellValue += (int) $this->spreadsheet->getActiveSheet()->getCell($value)->getValue();
                        if (is_numeric($this->spreadsheet->getActiveSheet()->getCell($value)->getValue())) {
                            $evaluatedCellValue += (int) $this->spreadsheet->getActiveSheet()->getCell($value)->getValue();
                        } else {
                            $tempEvaluatedCellValue = $this->getEvaluatedCellValue($this->spreadsheet->getActiveSheet()->getCell($value)->getValue());
                            if (is_numeric($tempEvaluatedCellValue)) {
                                $evaluatedCellValue += $tempEvaluatedCellValue;
                            } else {
                                $evaluatedCellValue = $tempEvaluatedCellValue;
                                break;
                            }
                        }
                    } else {
                        echo 'Final else' . PHP_EOL;
                        $evaluatedCellValue = '#ERR';
                        break;
                    }








                    // if (
                    //     is_numeric($value) ||
                    //     !is_null($this->spreadsheet->getActiveSheet()->getCell($value))
                    // ) {
                    //     echo PHP_EOL . PHP_EOL . PHP_EOL . $value;
                    //     if (is_numeric($value)) {
                    //         echo PHP_EOL . ' Numeric: ' . var_export(is_numeric($value), true) . PHP_EOL;
                    //     } else {
                    //         echo PHP_EOL . ' Not null: ' . var_export(!is_null($this->spreadsheet->getActiveSheet()->getCell($value)), true) . PHP_EOL;
                    //         echo $this->spreadsheet->getActiveSheet()->getCell($value);
                    //     }
                    //     $evaluatedCellValue += (int)$value;
                    //     echo $evaluatedCellValue;
                    // } else {
                    //     $evaluatedCellValue = '#ERR';
                    //     break;
                    // }
                }
                echo PHP_EOL. PHP_EOL . 'Evaluated Cell Value: ' . var_export($evaluatedCellValue, true) . PHP_EOL. PHP_EOL;
                $evaluatedRow[] = $evaluatedCellValue;
            }
            echo PHP_EOL. PHP_EOL . 'Evaluated Row Value: ' . var_export($evaluatedRow, true) . PHP_EOL . PHP_EOL;
            fputcsv($fp, $evaluatedRow);
        }
        fclose($fp);
    }

    private function getCsvData() {
        $this->sheetData = $this->spreadsheet->getActiveSheet()->toArray();
        return $this->sheetData;
    }
}

// $reader = new Csv();
// $reader->setDelimiter(',');
// $reader->setEnclosure('');
// $reader->setSheetIndex(0);

// $spreadsheet = $reader->load(__DIR__ . '/../sample.csv');

// // echo var_export($spreadsheet, true);

// // foreach ($spreadsheet->getRowIterator() as $row) {
// //     echo var_export($row, true);
// // }

// $sheetData = $spreadsheet->getActiveSheet()->toArray();
// // echo var_export($sheetData, true);
// echo var_export($spreadsheet->getActiveSheet()->getCell('Y23')->getValue(), true);