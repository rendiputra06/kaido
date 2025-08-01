<?php

namespace App\Filament\Resources\KrsMahasiswaResource\RelationManagers;

use App\Enums\KrsStatusEnum;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Services\KrsService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KrsDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'krsDetails';
    protected static ?string $title = 'Mata Kuliah yang Diambil';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kelas_id')
                    ->label('Mata Kuliah')
                    ->options(function () {
                        $mahasiswa = $this->ownerRecord->mahasiswa;
                        
                        // Get student's curriculum
                        $kurikulum = $mahasiswa->programStudi->kurikulums()
                            ->where('status', 'aktif')
                            ->first();
                            
                        if (!$kurikulum) {
                            return [];
                        }
                        
                        // Get current semester from active period
                        $currentSemester = $this->getCurrentSemester();
                        
                        // Smart filtering: prioritize courses from current semester
                        $query = Kelas::with(['mataKuliah.kurikulums', 'dosen', 'jadwalKuliahs'])
                            ->whereHas('mataKuliah.kurikulums', function ($q) use ($kurikulum, $currentSemester) {
                                $q->where('kurikulum_id', $kurikulum->id)
                                  ->where('semester_ditawarkan', $currentSemester);
                            })
                            ->where('sisa_kuota', '>', 0);
                            
                        return $query->get()->mapWithKeys(function ($kelas) {
                            $jadwal = $kelas->jadwalKuliahs->map(function ($j) {
                                return "{$j->hari} {$j->jam_mulai}-{$j->jam_selesai}";
                            })->implode(', ');
                            
                            return [
                                $kelas->id => "{$kelas->mataKuliah->nama_mk} ({$kelas->mataKuliah->sks} SKS) - {$kelas->dosen->nama} | {$jadwal} | Kuota: {$kelas->sisa_kuota}/{$kelas->kuota}"
                            ];
                        });
                    })
                    ->searchable()
                    ->required()
                    ->helperText('Menampilkan mata kuliah yang sesuai dengan kurikulum dan semester Anda')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state) {
                            $this->validateEnrollment($state);
                        }
                    }),
                    
                Forms\Components\Toggle::make('show_all_courses')
                    ->label('Tampilkan semua mata kuliah (termasuk dari semester lain)')
                    ->helperText('Aktifkan untuk melihat mata kuliah dari semester lain atau untuk mengulang')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        // Reset kelas_id when toggling
                        $set('kelas_id', null);
                    }),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kelas.mataKuliah.nama_mk')
            ->columns([
                Tables\Columns\TextColumn::make('kelas.mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.dosen.nama')
                    ->label('Dosen Pengampu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.mataKuliah.sks')
                    ->label('SKS')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Mata Kuliah')
                    ->visible(fn() => $this->ownerRecord->status === KrsStatusEnum::DRAFT)
                    ->using(function (array $data, KrsService $krsService): \App\Models\KrsDetail {
                        try {
                            return $krsService->addMataKuliah($this->ownerRecord->id, $data['kelas_id']);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal menambahkan mata kuliah')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            throw $e;
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Mata kuliah berhasil ditambahkan')
                            ->body('Mata kuliah telah ditambahkan ke KRS Anda.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => $this->ownerRecord->status === KrsStatusEnum::DRAFT),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => $this->ownerRecord->status === KrsStatusEnum::DRAFT)
                    ->using(function (\App\Models\KrsDetail $record, KrsService $krsService): bool {
                        try {
                            return $krsService->removeMataKuliah($this->ownerRecord->id, $record->id);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal menghapus mata kuliah')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            throw $e;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->visible(fn() => $this->ownerRecord->status === KrsStatusEnum::DRAFT),
            ])
            ->filters([
                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options([
                        1 => 'Semester 1',
                        2 => 'Semester 2',
                        3 => 'Semester 3',
                        4 => 'Semester 4',
                        5 => 'Semester 5',
                        6 => 'Semester 6',
                        7 => 'Semester 7',
                        8 => 'Semester 8',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value'])) {
                            return $query->whereHas('kelas.mataKuliah.kurikulums', function ($q) use ($data) {
                                $q->where('semester_ditawarkan', $data['value']);
                            });
                        }
                        return $query;
                    }),
                    
                SelectFilter::make('jenis')
                    ->label('Jenis Mata Kuliah')
                    ->options([
                        'wajib' => 'Wajib',
                        'pilihan' => 'Pilihan',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value'])) {
                            return $query->whereHas('kelas.mataKuliah.kurikulums', function ($q) use ($data) {
                                $q->where('jenis', $data['value']);
                            });
                        }
                        return $query;
                    }),
            ]);
    }
    
    /**
     * Get current semester based on active period
     */
    private function getCurrentSemester(): int
    {
        // This is a simplified logic. You might want to implement more sophisticated logic
        // based on your academic calendar system
        $activePeriod = \App\Models\PeriodeKrs::where('status', 'aktif')->first();
        
        if ($activePeriod && $activePeriod->tahun_ajaran) {
            // Extract semester from period name or implement your logic
            // For now, return a default semester
            return 1; // You can implement more sophisticated logic here
        }
        
        return 1;
    }
    
    /**
     * Validate enrollment for selected course
     */
    private function validateEnrollment(int $kelasId): void
    {
        $krsService = app(KrsService::class);
        $mahasiswa = $this->ownerRecord->mahasiswa;
        $kelas = Kelas::find($kelasId);
        
        if (!$kelas) {
            return;
        }
        
        $validation = $krsService->canEnroll($mahasiswa, $kelas);
        
        if (!$validation['success']) {
            Notification::make()
                ->title('Peringatan Validasi')
                ->body($validation['message'])
                ->warning()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->title('Validasi Berhasil')
                ->body($validation['message'])
                ->success()
                ->send();
        }
    }
}
