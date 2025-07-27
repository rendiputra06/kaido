<?php

namespace App\Filament\Resources\JadwalKuliahResource\Pages;

use App\Filament\Resources\JadwalKuliahResource;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LaporanJadwal extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = JadwalKuliahResource::class;

    protected static string $view = 'filament.resources.jadwal-kuliah-resource.pages.laporan-jadwal';

    protected static ?string $title = 'Laporan Jadwal Kuliah';

    public function table(Table $table): Table
    {
        return $table
            ->query(JadwalKuliah::query())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('kelas.mataKuliah.nama_mk')->label('Mata Kuliah'),
                \Filament\Tables\Columns\TextColumn::make('kelas.nama')->label('Kelas'),
                \Filament\Tables\Columns\TextColumn::make('kelas.dosen.nama')->label('Dosen'),
                \Filament\Tables\Columns\TextColumn::make('hari'),
                \Filament\Tables\Columns\TextColumn::make('jam_mulai'),
                \Filament\Tables\Columns\TextColumn::make('jam_selesai'),
                \Filament\Tables\Columns\TextColumn::make('ruangKuliah.nama')->label('Ruangan'),
            ])
            ->filters([
                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(TahunAjaran::pluck('nama', 'id'))
                    ->query(fn ($query, array $data) => $query->when($data['value'], fn ($q) => $q->whereHas('kelas', fn ($sq) => $sq->where('tahun_ajaran_id', $data['value'])))),
                SelectFilter::make('program_studi_id')
                    ->label('Program Studi')
                    ->options(ProgramStudi::pluck('nama', 'id'))
                    ->query(fn ($query, array $data) => $query->when($data['value'], fn ($q) => $q->whereHas('kelas.mataKuliah', fn ($sq) => $sq->where('program_studi_id', $data['value'])))),
                SelectFilter::make('dosen_id')
                    ->label('Dosen')
                    ->options(Dosen::pluck('nama', 'id'))
                    ->query(fn ($query, array $data) => $query->when($data['value'], fn ($q) => $q->whereHas('kelas', fn ($sq) => $sq->where('dosen_id', $data['value'])))),
            ])
            ->headerActions([
                ExportAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('kelas.mataKuliah.nama_mk')->heading('Mata Kuliah'),
                        Column::make('kelas.nama')->heading('Kelas'),
                        Column::make('kelas.dosen.nama')->heading('Dosen'),
                        Column::make('hari')->heading('Hari'),
                        Column::make('jam_mulai')->heading('Jam Mulai'),
                        Column::make('jam_selesai')->heading('Jam Selesai'),
                        Column::make('ruangKuliah.nama')->heading('Ruangan'),
                        Column::make('kelas.tahunAjaran.nama')->heading('Tahun Ajaran'),
                    ])->withFilename('laporan-jadwal-' . date('Y-m-d')),
                ]),
            ]);
    }
}
