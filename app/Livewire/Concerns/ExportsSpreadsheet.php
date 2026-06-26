<?php

namespace App\Livewire\Concerns;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a real .xlsx file (so Excel shows dates/numbers correctly instead of
 * the "####" you get from a CSV with an unparsed/narrow date column).
 *
 * Row values are typed automatically:
 *   - DateTimeInterface  -> real Excel date, formatted dd-mm-yyyy hh:mm
 *   - int / float        -> numeric cell (right-aligned, thousands separated)
 *   - everything else    -> text (keeps leading zeros on mobiles, order numbers)
 *
 * Every column is auto-sized, which is what guarantees the date is visible.
 */
trait ExportsSpreadsheet
{
    /**
     * @param  array<int,string>  $headings
     * @param  \Closure  $eachRow  receives a writer callback: function (callable $write): void
     */
    protected function streamXlsx(string $filename, array $headings, \Closure $eachRow): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ];

        return response()->streamDownload(function () use ($headings, $eachRow) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Report');

            // Header row (bold).
            foreach (array_values($headings) as $i => $heading) {
                $sheet->setCellValueExplicit(
                    Coordinate::stringFromColumnIndex($i + 1) . '1',
                    (string) $heading,
                    DataType::TYPE_STRING
                );
            }
            $lastCol = Coordinate::stringFromColumnIndex(max(1, count($headings)));
            $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
            $sheet->freezePane('A2');

            $rowNum = 2;
            $write = function (array $row) use (&$rowNum, $sheet): void {
                $col = 1;
                foreach ($row as $value) {
                    $cell = Coordinate::stringFromColumnIndex($col) . $rowNum;

                    if ($value instanceof \DateTimeInterface) {
                        $sheet->setCellValue($cell, ExcelDate::PHPToExcel($value));
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('dd-mm-yyyy hh:mm');
                    } elseif (is_int($value) || is_float($value)) {
                        $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_NUMERIC);
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
                    } else {
                        $sheet->setCellValueExplicit($cell, (string) ($value ?? ''), DataType::TYPE_STRING);
                    }
                    $col++;
                }
                $rowNum++;
            };

            $eachRow($write);

            // Auto-size every column so the date column is always fully visible.
            for ($i = 1; $i <= count($headings); $i++) {
                $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }
            $sheet->getStyle('A1:' . $lastCol . '1')
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, $headers);
    }
}
