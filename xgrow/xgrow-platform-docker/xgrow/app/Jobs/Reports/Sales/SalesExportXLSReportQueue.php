<?php

namespace App\Jobs\Reports\Sales;

use App\Services\Objects\SaleReportFilter;
use App\Jobs\Reports\Sales\Models\BaseReport;
use App\Jobs\Reports\Sales\BaseExportReportQueue;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
class SalesExportXLSReportQueue extends BaseExportReportQueue
{
    public function __construct($user, BaseReport $report, SaleReportFilter $filters) {
        parent::__construct($user, $report, $filters);
    }

    protected function filename() {
        $reportName = $this->report->name();
        return date('Y_m_d') . "_financeiro_{$reportName}_" . rand(11111111, 99999999) . ".xlsx";
    }

    protected function process(string $path)
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->setShouldUseInlineStrings(true);
        $writer->setTempFolder(sys_get_temp_dir());
        $writer->openToFile($path);

        /* CREATE A HEADER FOR CSV */
        $header = $this->report->header();
        $writer->addRow(WriterEntityFactory::createRowFromArray($header));
        /* CREATE A HEADER FOR CSV */

        $query = $this->report->query($this->platform_id, $this->filters);
        $normalizedRows = $this->report->rows();
        $query->chunk($this->chunkSize, function ($rows) use ($writer, $normalizedRows) {
            foreach ($rows as $row) {
                $data = [];
                foreach ($normalizedRows as $key => $value) {
                    $data[] = $row->$key = ((is_callable($value)) ? $value($row->$key, $row) : $row->$value) ?? '-';
                }
                $writer->addRow(WriterEntityFactory::createRowFromArray($data));
            }
        });
        $writer->close();
    }
}