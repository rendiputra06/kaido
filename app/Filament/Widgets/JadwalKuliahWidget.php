<?php

namespace App\Filament\Widgets;

use App\Models\JadwalKuliah;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class JadwalKuliahWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $query = JadwalKuliah::query();

        // Filter query based on user role
        if ($user->hasRole('dosen')) {
            // Assuming 'JadwalKuliah' has a 'dosen_id' relationship
            $query->where('dosen_id', $user->dosen->id);
        } elseif ($user->hasRole('mahasiswa')) {
            // This is more complex and depends on your schema.
            // Assuming a many-to-many relationship through a 'krs' or 'kelas' table.
            // This is a placeholder and needs to be adapted to your actual schema.
            // For now, it will show nothing for students until the query is corrected.
            $query->whereRaw('false'); // Placeholder
        }

        // For admins, it shows all schedules.

        return $table
            ->query($query->limit(10))
            ->heading('Jadwal Kuliah Terdekat')
            ->columns([
                Tables\Columns\TextColumn::make('kelas.mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hari')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Waktu')
                    ->formatStateUsing(fn ($record) => "{$record->jam_mulai} - {$record->jam_selesai}"),
                Tables\Columns\TextColumn::make('ruangKuliah.nama')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.dosen.nama')
                    ->label('Dosen Pengajar')
                    ->visible(fn() => !$user->hasRole('dosen')) // Hide if user is a lecturer
                    ->searchable()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
