<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:newsapi')->dailyAt('00:00');
Schedule::command('app:nytimes')->dailyAt('02:00');
Schedule::command('app:opennews')->dailyAt('04:00');
