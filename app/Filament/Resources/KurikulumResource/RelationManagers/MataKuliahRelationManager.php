<?php

namespace App\Filament\Resources\KurikulumResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MataKuliahRelationManager extends RelationManager
{
    protected static string $relationship = 'mataKuliahs';

    protected static ?string $recordTitleAttribute = 'nama_mk';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mata_kuliah_id')
                    ->options(\App\Models\MataKuliah::pluck('nama_mk', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('semester_ditawarkan')
                    ->label('Semester')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(8)
                    ->required(),
                Forms\Components\Select::make('jenis')
                    ->options([
                        'wajib' => 'Wajib',
                        'pilihan' => 'Pilihan',
                    ])
                    ->required()
                    ->default('wajib'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_mk')
            ->columns([
                Tables\Columns\TextColumn::make('kode_mk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_mk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.semester_ditawarkan')
                    ->label('Semester')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('pivot_semester_ditawarkan', $direction);
                    }),
                Tables\Columns\TextColumn::make('pivot.jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'wajib' => 'success',
                        'pilihan' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('sks')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model) {
                        $owner = $this->getOwnerRecord();
                        $owner->mataKuliahs()->attach(
                            $data['mata_kuliah_id'],
                            [
                                'semester_ditawarkan' => $data['semester_ditawarkan'],
                                'jenis' => $data['jenis'],
                            ]
                        );
                        return $owner->mataKuliahs()->where('mata_kuliahs.id', $data['mata_kuliah_id'])->first();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('semester_ditawarkan')
                            ->label('Semester')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(8)
                            ->required(),
                        Forms\Components\Select::make('jenis')
                            ->options([
                                'wajib' => 'Wajib',
                                'pilihan' => 'Pilihan',
                            ])
                            ->required(),
                    ])
                    ->using(function (array $data, $record) {
                        $this->getOwnerRecord()->mataKuliahs()->updateExistingPivot($record->id, [
                            'semester_ditawarkan' => $data['semester_ditawarkan'],
                            'jenis' => $data['jenis'],
                        ]);
                        return $record;
                    })
                    ->fillForm(function ($record) {
                        return [
                            'semester_ditawarkan' => $record->pivot->semester_ditawarkan,
                            'jenis' => $record->pivot->jenis,
                        ];
                    }),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('pivot_semester_ditawarkan');
    }
}
