<?php

namespace App\Enums;

use App\Enums\BasicEnum;

abstract class AntecipationTaxEnum extends BasicEnum {
    const INSTALLMENTS_1 = 0.0000;
    const INSTALLMENTS_2 = 0.0050;
    const INSTALLMENTS_3 = 0.0100;
    const INSTALLMENTS_4 = 0.0150;
    const INSTALLMENTS_5 = 0.0200;
    const INSTALLMENTS_6 = 0.0250;
    const INSTALLMENTS_7 = 0.0300;
    const INSTALLMENTS_8 = 0.0350;
    const INSTALLMENTS_9 = 0.0399;
    const INSTALLMENTS_10 = 0.0449;
    const INSTALLMENTS_11 = 0.0499;
    const INSTALLMENTS_12 = 0.0549;
}