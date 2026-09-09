<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('audiences:rappel')->dailyAt('07:00');
