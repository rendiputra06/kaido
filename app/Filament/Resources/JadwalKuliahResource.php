<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalKuliahResource\Pages;
use App\Filament\Resources\JadwalKuliahResource\RelationManagers;
use App\Interfaces\JadwalServiceInterface;
use App\Models\JadwalKuliah;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JadwalKuliahResource extends Resource
{
    protected static ?string $model = JadwalKuliah::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Akademik';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kelas_id')
                    ->relationship('kelas', 'nama')
                    ->required()
                    ->live(),
                Forms\Components\Select::make('ruang_kuliah_id')
                    ->relationship('ruangKuliah', 'nama')
                    ->required()
                    ->live()
                    ->rule(function (Forms\Get $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $kelas = \App\Models\Kelas::find($get('kelas_id'));
                            $ruangan = \App\Models\RuangKuliah::find($value);

                            if ($kelas && $ruangan && $kelas->kuota > $ruangan->kapasitas) {
                                $fail("Kapasitas ruangan ({$ruangan->kapasitas}) tidak mencukupi untuk kuota kelas ({$kelas->kuota}).");
                            }
                        };
                    }),
                Forms\Components\Select::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ])
                    ->required(),
                Forms\Components\TimePicker::make('jam_mulai')
                    ->required()
                    ->seconds(false),
                Forms\Components\TimePicker::make('jam_selesai')
                    ->required()
                    ->seconds(false)
                    ->after('jam_mulai')
                    ->rule(function (Forms\Get $get, ?JadwalKuliah $record) {
                        return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                            $kelasId = $get('kelas_id');
                            $ruangKuliahId = $get('ruang_kuliah_id');
                            $hari = $get('hari');
                            $jamMulai = $get('jam_mulai');

                            // Stop validation if prerequisites are not met
                            if (empty($kelasId) || empty($ruangKuliahId) || empty($hari) || empty($jamMulai)) {
                                return;
                            }

                            // Derive Dosen ID from Kelas
                            $dosenId = \App\Models\Kelas::find($kelasId)?->dosen_id;

                            if (is_null($dosenId)) {
                                // Stop validation to prevent crash if lecturer is not found.
                                return;
                            }

                            $jadwalService = app(JadwalServiceInterface::class);

                            $isConflict = $jadwalService->isScheduleConflict(
                                ruangKuliahId: $ruangKuliahId,
                                dosenId: $dosenId,
                                hari: $hari,
                                jamMulai: $jamMulai,
                                jamSelesai: $value, // Use the field's current value
                                exceptJadwalId: $record?->id
                            );

                            if ($isConflict) {
                                $fail('Jadwal bentrok dengan jadwal lain (dosen atau ruangan sudah terpakai).');
                            }
                        };
                    }),
                Forms\Components\Hidden::make('dosen_id')
                    ->default(fn (Forms\Get $get) => \App\Models\Kelas::find($get('kelas_id'))?->dosen_id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangKuliah.nama')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hari')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jam_mulai'),
                Tables\Columns\TextColumn::make('jam_selesai'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListJadwalKuliahs::route('/'),
            'create' => Pages\CreateJadwalKuliah::route('/create'),
            'edit' => Pages\EditJadwalKuliah::route('/{record}/edit'),
            'laporan-jadwal' => Pages\LaporanJadwal::route('/laporan'),
        ];
    }
}
