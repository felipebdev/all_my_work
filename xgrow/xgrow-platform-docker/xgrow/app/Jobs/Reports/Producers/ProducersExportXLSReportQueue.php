<?php

namespace App\Jobs\Reports\Producers;

use App\Jobs\Reports\Producers\Models\ProducersReport;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;

class ProducersExportXLSReportQueue extends BaseExportReportQueue
{

    private ProducersReport $report;

    protected function report(): ProducersReport
    {
        return $this->report ??= resolve(ProducersReport::class);
    }

    protected function filename()
    {
        $reportName = $this->report()->name();
        return date('Y_m_d')."_{$reportName}_".rand(11111111, 99999999).".xlsx";
    }

    protected function getWriter(): WriterInterface
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->setShouldUseInlineStrings(true);
        $writer->setTempFolder(sys_get_temp_dir());
        return $writer;
    }
}
