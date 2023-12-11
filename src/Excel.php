<?php

namespace mb\helper;

use Exception;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel
{
    /**
     * @param $filename string    导入文件
     * @param $columns  array    列定义, ['field'=>'#字段名称', 'column'=>'#列定义']
     * @return array        读取的数据集
     */
    public static function read($filename, $columns)
    {
        try {
            $excel = IOFactory::load($filename);
            $sheet = $excel->getSheet(0);
            $dataSet = [];
            for ($i = 2; ; $i++) {
                $allEmpty = true;

                $row = [];
                foreach ($columns as $column) {
                    $row[$column['field']] = $sheet->getCell("{$column['column']}{$i}")->getValue();
                    if ($allEmpty && !empty($row[$column['field']])) {
                        $allEmpty = false;
                    }
                }
                if ($allEmpty) {
                    break;
                }
                $dataSet[] = $row;
            }

            return $dataSet;
        } catch (Exception $e) {
            return error(-1, $e->getMessage());
        }
    }

    /**
     * @param $title    string    文件名称
     * @param $headers   array   标题定义 ['列名称1', '列名称2']
     * @param $dataSet  array    导出的数据集, 和列名称对应
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public static function write($title, $headers, $dataSet)
    {
        $o = new Spreadsheet();
        try {
            $o->createSheet(0);
            $sheet = $o->getSheet(0);
            $sheet->setTitle($title);
            $i = 0;
            foreach ($headers as $header) {
                $sheet->setCellValueExplicitByColumnAndRow($i, 1, $header, DataType::TYPE_STRING);
                $i++;
            }
            $i = 2;
            foreach ($dataSet as $row) {
                $j = 0;
                foreach ($row as $cell) {
                    $sheet->setCellValueExplicitByColumnAndRow($j, $i, $cell, DataType::TYPE_STRING);
                    $j++;
                }
                $i++;
            }
            $filename = urlencode($title);
            header('content-type: application/xls');
            header('content-disposition: attachment; filename="' . $filename . '.xlsx"');
            $writer = new Xlsx($o);
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }
}