<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('loans:notify-due')->dailyAt('08:00');
