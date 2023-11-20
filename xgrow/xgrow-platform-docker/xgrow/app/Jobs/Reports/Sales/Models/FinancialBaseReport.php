<?php

namespace App\Jobs\Reports\Sales\Models;

use App\Payment;

/**
 *
 */
abstract class FinancialBaseReport
{
    /**
     * @param int|null $recurrence
     * @return string
     */
    protected function recurrenceLabel(int $recurrence = null): string
    {
        switch ($recurrence) {
            case 7:
                $recurrenceTransform = 'Semanal';
                break;

            case 30:
                $recurrenceTransform = 'Mensal';
                break;

            case 60:
                $recurrenceTransform = 'Bimestral';
                break;

            case 90:
                $recurrenceTransform = 'Trimestral';
                break;

            case 180:
                $recurrenceTransform = 'Semestral';
                break;

            case 360:
                $recurrenceTransform = 'Anual';
                break;

            default:
                $recurrenceTransform = '-';
                break;
        }

        return $recurrenceTransform;
    }

    /**
     * @param string|null $status
     * @return string
     */
    protected function changeStatus(string $status = null): string
    {
        return Payment::listStatus()[$status] ?? '-';
    }
}
