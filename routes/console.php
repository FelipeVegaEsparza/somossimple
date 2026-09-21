<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:apply-billing')->dailyAt('06:00');
