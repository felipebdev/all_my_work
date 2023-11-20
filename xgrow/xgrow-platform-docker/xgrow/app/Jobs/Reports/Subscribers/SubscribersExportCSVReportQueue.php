<?php

namespace App\Jobs\Reports\Subscribers;

use App\Jobs\Reports\Subscribers\Models\BaseReport;
use App\Services\Objects\SubscriberReportFilter;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class SubscribersExportCSVReportQueue extends BaseExportReportQueue
{
    public function __construct($user, BaseReport $report, SubscriberReportFilter $filters) {
        parent::__construct($user, $report, $filters);
    }

    protected function filename() {
        $reportName = $this->report->name();
        return date('Y_m_d') . "_{$reportName}_" . rand(11111111, 99999999) . ".csv";
    }

    protected function process(string $path) {
        $writer = WriterEntityFactory::createCSVWriter();
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
                    $data[] = WriterEntityFactory::createCell(
                        $row->$key = ((is_callable($value)) ? $value($row->$key, $row) : $row->$value) ?? '-'
                    );
                }
                $writer->addRow(WriterEntityFactory::createRow($data));
            }
        });
        $writer->close();
    }
}
