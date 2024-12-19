<?php

namespace App\APIs;

use Carbon\Carbon;

interface ApiInterface
{
    public function setBeginDate(Carbon $dateTime):static;
    public function setEndDate(Carbon $dateTime):static;
    public function fetchSourceAndUpdateNews():void;
}
