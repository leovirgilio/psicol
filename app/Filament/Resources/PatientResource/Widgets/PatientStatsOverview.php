<?php

namespace App\Filament\Resources\PatientResource\Widgets;

use App\Models\City;
use App\Models\Country;
use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PatientStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $city = City::where('name', 'Adamantina')->withCount('patients')->first();

        return [
            Card::make('All Patients', Patient::all()->count()),
            Card::make($city->name . ' Patients', $city->patients_count),
        ];
    }
}
