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
        foreach ($cellValue as $value) {
            if (is_numeric($value)) {
                $evaluatedCellValue += (int) $value;
            } elseif (!is_null($this->spreadsheet->getActiveSheet()->getCell($value)->getValue())) {
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
                $evaluatedCellValue = '#ERR';
                break;
            }
        }
        return $evaluatedCellValue;
    }

    public function evaluate() {
        $fp = fopen('output.csv', 'w');
        foreach ($this->getCsvData() as $row) {
            $evaluatedRow = array();
            foreach ($row as $cell) {
                $evaluatedCellValue = 0;
                // TODO: Handle non-digit
                // TODO: Handle tab
                // TODO: Handle infinite recursion
                $cellValue = explode(' ', preg_replace('/\s{2,}/', ' ', $cell));
                foreach ($cellValue as $value) {
                    if (is_numeric($value)) {
                        $evaluatedCellValue += (int) $value;
                    } elseif (!is_null($this->spreadsheet->getActiveSheet()->getCell($value)->getValue())) {
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
                        $evaluatedCellValue = '#ERR';
                        break;
                    }
                }
                $evaluatedRow[] = $evaluatedCellValue;
            }
            fputcsv($fp, $evaluatedRow);
        }
        fclose($fp);
    }

    private function getCsvData() {
        $this->sheetData = $this->spreadsheet->getActiveSheet()->toArray();
        return $this->sheetData;
    }
}