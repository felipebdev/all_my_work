<?php

namespace App\Jobs\Reports\Platforms;

use App\Jobs\Reports\Platforms\Models\BaseReport;
use App\Services\Objects\PlatformFilter;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class PlatformsExportCSVReportQueue extends BaseExportReportQueue
{
    public function __construct($user, BaseReport $report, PlatformFilter $filters) {
        parent::__construct($user, $report, $filters);
    }

    protected function filename() {
        $reportName = $this->report->name();
        return date('Y_m_d') . "_{$reportName}_" . rand(11111111, 99999999) . ".csv";
    }

    protected function process(string $path) {

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToFile($path);

        $header = $this->report->header();
        $writer->addRow(WriterEntityFactory::createRowFromArray($header));

        $query = $this->report->query($this->filters);
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
