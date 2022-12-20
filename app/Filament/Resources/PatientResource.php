<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Filament\Resources\PatientResource\Widgets\PatientStatsOverview;
use App\Models\City;
use App\Models\Country;
use App\Models\Patient;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    TextInput::make('first_name')->required()->maxLength(255),
                    TextInput::make('last_name')->required()->maxLength(255),
                    Select::make('state_id')
                        ->label('State')
                        ->options(State::all()->pluck('name', 'id')->toArray())
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set) => $set ('city_id', null)),
                    Select::make('city_id')
                        ->label('City')
                        ->options(function (callable $get) {
                            $states = State::find($get('state_id'));
                            if (!$states) {
                                return City::all()->pluck('name', 'id');
                            }
                            return $states->cities->pluck('name', 'id');
                        })
                        ->reactive(),
                    TextInput::make('address'),
                    TextInput::make('zipcode'),
                    DatePicker::make('birthdate')->required(),
                    TextInput::make('schooling')->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('birthdate')->date()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')->relationship('city', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PatientStatsOverview::class,
        ];
    }
}
