<?php

namespace App\Jobs\Reports\Platforms;

use App\Jobs\Reports\Platforms\Models\BaseReport;
use App\Services\Objects\PlatformFilter;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class PlatformsExportXLSReportQueue extends BaseExportReportQueue
{
    public function __construct($user, BaseReport $report, PlatformFilter $filters) {
        parent::__construct($user, $report, $filters);
    }

    protected function filename()
    {
        $reportName = $this->report->name();
        return date('Y_m_d') . "_{$reportName}_" . rand(11111111, 99999999) . ".xlsx";
    }

    protected function process(string $path)
    {

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->setShouldUseInlineStrings(true);
        $writer->setTempFolder(sys_get_temp_dir());
        $writer->openToFile($path);

        $header = $this->report->header();
        $writer->addRow(WriterEntityFactory::createRowFromArray($header));

        $query = $this->report->query($this->filters);
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
