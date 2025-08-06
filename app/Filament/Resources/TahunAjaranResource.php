<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunAjaranResource\Pages;
use App\Filament\Resources\TahunAjaranResource\RelationManagers;
use App\Models\TahunAjaran;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TahunAjaranResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = TahunAjaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $title = 'Tahun Ajaran';
    protected static ?string $navigationLabel = 'Tahun Ajaran';
    protected static ?string $pluralModelLabel = 'Tahun Ajaran';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('kode')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(5)
                            ->disabled(fn (?Model $record) => $record !== null)
                            ->dehydrated(fn ($state) => $state !== null)
                            ->helperText('Format: YYYYS (S=1 untuk Ganjil, 2 untuk Genap)'),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('semester')
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap' => 'Genap',
                            ])
                            ->required()
                            ->disabled(fn (?Model $record) => $record !== null)
                            ->dehydrated(fn ($state) => $state !== null),
                        Forms\Components\TextInput::make('tahun_akademik')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Otomatis dihitung dari kode'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Periode')
                    ->schema([
                        Forms\Components\DatePicker::make('tgl_mulai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('tgl_selesai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('tgl_mulai'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Jika diaktifkan, tahun ajaran lain akan dinonaktifkan')
                    ->required()
                    ->afterStateUpdated(function (Model $record, $state) {
                        if ($state) {
                            TahunAjaran::whereNot('id', $record->id)->update(['is_active' => false]);
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun_akademik')
                    ->label('Tahun Akademik')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ganjil' => 'success',
                        'Genap' => 'primary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semester')
                    ->options([
                        'Ganjil' => 'Ganjil',
                        'Genap' => 'Genap',
                    ]),
                Tables\Filters\Filter::make('is_active')
                    ->label('Hanya Aktif')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (TahunAjaran $record) => $record->is_active),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn (TahunAjaran $record) => $record->is_active),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generate')
                    ->label('Generate Tahun Ajaran')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-sparkles')
                    ->form([
                        Select::make('start_year')
                            ->label('Tahun Awal')
                            ->options(fn () => self::getYearOptions())
                            ->required()
                            ->default(now()->subYears(2)->year),
                        Select::make('end_year')
                            ->label('Tahun Akhir')
                            ->options(fn () => self::getYearOptions())
                            ->required()
                            ->default(now()->addYears(2)->year),
                    ])
                    ->action(function (array $data) {
                        return self::handleGenerateTahunAjaran($data);
                    })
            ]);
    }

    protected static function getYearOptions(): array
    {
        $currentYear = now()->year;
        $years = [];
        
        // Generate 10 years before and after current year
        for ($i = $currentYear - 10; $i <= $currentYear + 10; $i++) {
            $years[$i] = $i;
        }
        
        return $years;
    }

    protected static function handleGenerateTahunAjaran(array $data)
    {
        $startYear = (int) $data['start_year'];
        $endYear = (int) $data['end_year'];
        $generated = 0;
        $skipped = 0;

        DB::beginTransaction();
        
        try {
            for ($year = $startYear; $year <= $endYear; $year++) {
                // Generate Ganjil (1) and Genap (2) semesters
                foreach (['1' => 'Ganjil', '2' => 'Genap'] as $semesterCode => $semesterName) {
                    $kode = $year . $semesterCode;
                    
                    // Skip if already exists
                    if (TahunAjaran::where('kode', $kode)->exists()) {
                        $skipped++;
                        continue;
                    }
                    
                    // Calculate dates based on semester
                    if ($semesterCode === '1') {
                        // Ganjil: March - July
                        $tglMulai = "$year-03-01";
                        $tglSelesai = "$year-07-31";
                        $tahunAkademik = "$year/" . ($year + 1);
                    } else {
                        // Genap: September - December
                        $tglMulai = "$year-09-01";
                        $tglSelesai = "$year-12-31";
                        $tahunAkademik = ($year - 1) . "/$year";
                    }
                    
                    TahunAjaran::create([
                        'kode' => $kode,
                        'nama' => "Tahun Ajaran $tahunAkademik Semester $semesterName",
                        'semester' => $semesterName,
                        'tahun_akademik' => $tahunAkademik,
                        'tgl_mulai' => $tglMulai,
                        'tgl_selesai' => $tglSelesai,
                        'is_active' => false,
                    ]);
                    
                    $generated++;
                }
            }
            
            DB::commit();
            
            Notification::make()
                ->title('Generate Tahun Ajaran Berhasil')
                ->body("Berhasil menambahkan $generated tahun ajaran baru. " . 
                       ($skipped > 0 ? "$skipped tahun ajaran sudah ada dan dilewati." : ''))
                ->success()
                ->send();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Gagal menambahkan tahun ajaran: ' . $e->getMessage())
                ->danger()
                ->send();
            
            return false;
        }
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
            'index' => Pages\ListTahunAjarans::route('/'),
            'create' => Pages\CreateTahunAjaran::route('/create'),
            'edit' => Pages\EditTahunAjaran::route('/{record}/edit'),
        ];
    }
}
