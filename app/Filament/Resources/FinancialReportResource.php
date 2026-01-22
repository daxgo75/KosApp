<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialReportResource\Pages;
use App\Models\FinancialReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Auth;

class FinancialReportResource extends Resource
{
    protected static ?string $model = FinancialReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Keuangan';

    protected static ?string $modelLabel = 'Laporan Keuangan';

    protected static ?string $pluralModelLabel = 'Laporan Keuangan';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Laporan')
                    ->schema([
                        Forms\Components\Select::make('report_type')
                            ->label('Tipe Laporan')
                            ->options([
                                'monthly' => 'Bulanan',
                                'quarterly' => 'Triwulan',
                                'yearly' => 'Tahunan',
                                'custom' => 'Kustom',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\DatePicker::make('period_start')
                            ->label('Periode Mulai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('period_end')
                            ->label('Periode Akhir')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Dipublikasi',
                                'archived' => 'Diarsipkan',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),

                Section::make('Data Keuangan')
                    ->schema([
                        Forms\Components\TextInput::make('total_income')
                            ->label('Total Pemasukan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Forms\Components\TextInput::make('total_operational_cost')
                            ->label('Total Biaya Operasional')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Forms\Components\TextInput::make('net_profit')
                            ->label('Laba Bersih')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->reactive(),
                        Forms\Components\TextInput::make('outstanding_payment')
                            ->label('Tunggakan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])->columns(2),

                Section::make('Statistik')
                    ->schema([
                        Forms\Components\TextInput::make('total_tenants')
                            ->label('Total Penyewa')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('occupied_rooms')
                            ->label('Kamar Terisi')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('available_rooms')
                            ->label('Kamar Tersedia')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Section::make('Ringkasan')
                    ->schema([
                        Forms\Components\Textarea::make('summary')
                            ->label('Ringkasan Laporan')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Hidden::make('created_by')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('report_type')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'monthly',
                        'success' => 'quarterly',
                        'warning' => 'yearly',
                        'secondary' => 'custom',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'Bulanan',
                        'quarterly' => 'Triwulan',
                        'yearly' => 'Tahunan',
                        'custom' => 'Kustom',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Periode Mulai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_end')
                    ->label('Periode Akhir')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_income')
                    ->label('Pemasukan')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),
                Tables\Columns\TextColumn::make('total_operational_cost')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),
                Tables\Columns\TextColumn::make('net_profit')
                    ->label('Laba Bersih')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->weight('bold')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'warning' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'published' => 'Dipublikasi',
                        'archived' => 'Diarsipkan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('report_type')
                    ->label('Tipe Laporan')
                    ->options([
                        'monthly' => 'Bulanan',
                        'quarterly' => 'Triwulan',
                        'yearly' => 'Tahunan',
                        'custom' => 'Kustom',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Dipublikasi',
                        'archived' => 'Diarsipkan',
                    ]),
                Tables\Filters\Filter::make('period')
                    ->form([
                        Forms\Components\DatePicker::make('period_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('period_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['period_from'], fn ($q, $date) => $q->whereDate('period_start', '>=', $date))
                            ->when($data['period_until'], fn ($q, $date) => $q->whereDate('period_end', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label('Publikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (FinancialReport $record) => $record->publish())
                    ->visible(fn (FinancialReport $record) => $record->status === 'draft'),
                Tables\Actions\Action::make('archive')
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (FinancialReport $record) => $record->archive())
                    ->visible(fn (FinancialReport $record) => $record->status === 'published'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('period_start', 'desc');
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
            'index' => Pages\ListFinancialReports::route('/'),
            'create' => Pages\CreateFinancialReport::route('/create'),
            'edit' => Pages\EditFinancialReport::route('/{record}/edit'),
        ];
    }
}
