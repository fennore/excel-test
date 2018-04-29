<?php

namespace Fennore\Excel\Util;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Gettext\Translations;
use Gettext\Translation;
use Gettext\Generators\Po as PoGenerator;

class FileConverter
{
    /**
     * Converts an excel with translation data to a po file.
     * Supports a two column layout with A being the original and B the translation.
     * Skips first row when matching msgid and msgstr.
     *
     * @param string $filename the path to the file to read, can be relative or absolute
     * @param string $saveAs   the file to write to, can be relative or absolute. Defaults to "files/po/$name.po With $name being the filename used for the excel file.
     */
    public function convertExcelToPo($filename, &$saveAs = null, $progress = null)
    {
        // read file
        // do this first as this will also handle filename validation
        $spreadsheet = IOFactory::load($filename);
        // extract path information
        $pathinfo = pathinfo($filename);
        // if no path was given to save the file to, use default
        if (empty($saveAs)) {
            $saveAs = 'files/po/'.$pathinfo['filename'].'.po';
        }
        $this->createDirectoryIfNotExists($saveAs);
        // read rows and columns
        $worksheet = $spreadsheet->getActiveSheet();

        $translations = new Translations();

        $rows = $worksheet->getRowIterator();
        $progress->total(iterator_count($rows));
        foreach ($rows as $row) {
            $progress->current($rows->key());
            $cells = $row->getCellIterator('A', 'B');
            $cells->setIterateOnlyExistingCells(false);
            $msgid = $cells->seek('A')->current()->getValue();
            $msgstr = $cells->seek('B')->current()->getValue();
            // Skip possible column names
            if ('msgid' === $msgid && 'msgstr' === $msgstr && 1 === $rows->key()) {
                continue;
            }
            // A - msgid
            $translation = new Translation(null, $msgid);
            // B - msgstr
            $translation->setTranslation($msgstr);

            //Add new translations using the array syntax
            $translations[] = $translation;
        }

        //Save to a file
        PoGenerator::toFile($translations, $saveAs);
    }

    /**
     * Converts a po file with translation data to an excel file.
     * Result Excel is a two column layout with A being the original and B the translation.
     * Creates column header on first row.
     *
     * @param string $filename the path to the file to read, can be relative or absolute
     * @param string $saveAs   the file to write to, can be relative or absolute. Defaults to "files/po/$name.po With $name being the filename used for the excel file.
     */
    public function convertPoToExcel($filename, &$saveAs = null, $progress = null)
    {
        // Create a Translations instance using a po file
        $translations = Translations::fromPoFile($filename);
        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        // Create a Writer
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // extract path information
        $pathinfo = pathinfo($filename);
        // if no path was given to save the file to, use default
        if (empty($saveAs)) {
            $saveAs = 'files/excel/'.$pathinfo['filename'].'.xlsx';
        }
        $this->createDirectoryIfNotExists($saveAs);
        // Iterate through translations (since it is an ArrayObject it should be Iterable)
        $progress->total(iterator_count($translations));
        $row = 1; // 1 based row counts for Excel
        foreach ($translations as $translation) {
            $progress->current($row++); // send row value first because it is 0 based, and increment by 1 afterwards
            $worksheet
                      ->setCellValue('A'.($row), $translation->getOriginal())
                      ->setCellValue('B'.$row, $translation->getTranslation());
        }
        // Set headers and style
        $worksheet
                  ->setCellValue('A1', 'msgid')
                  ->setCellValue('B1', 'msgstr')
                  ->getStyle('A1:B1')->getFont()->setBold(true);

        $writer->save($saveAs);
    }

    /**
     * Check if a given directory exists, and creates it if it does not.
     *
     * @param string $dirname the directory to check
     */
    private function createDirectoryIfNotExists($filename)
    {
        $pathinfo = pathinfo($filename);
        if (!file_exists($pathinfo['dirname'])) {
            mkdir($pathinfo['dirname'], null, true);
        }
    }
}
