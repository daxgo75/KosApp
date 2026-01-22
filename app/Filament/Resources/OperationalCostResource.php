<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperationalCostResource\Pages;
use App\Models\OperationalCost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Auth;

class OperationalCostResource extends Resource
{
    protected static ?string $model = OperationalCost::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Biaya Operasional';

    protected static ?string $modelLabel = 'Biaya Operasional';

    protected static ?string $pluralModelLabel = 'Biaya Operasional';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Biaya')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'electricity' => 'Listrik',
                                'water' => 'Air',
                                'maintenance' => 'Perawatan',
                                'cleaning' => 'Kebersihan',
                                'security' => 'Keamanan',
                                'internet' => 'Internet',
                                'tax' => 'Pajak',
                                'insurance' => 'Asuransi',
                                'other' => 'Lainnya',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Biaya')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(999999999),
                        Forms\Components\DatePicker::make('cost_date')
                            ->label('Tanggal Biaya')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                    ])->columns(2),

                Section::make('Status & Persetujuan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'recorded' => 'Tercatat',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('recorded')
                            ->required(),
                        Forms\Components\FileUpload::make('receipt_file')
                            ->label('Bukti Kwitansi')
                            ->directory('operational-costs/receipts')
                            ->visibility('public')
                            ->image()
                            ->maxSize(2048)
                            ->helperText('Upload bukti pembayaran (maksimal 2MB)'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Hidden::make('created_by')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cost_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Kategori')
                    ->colors([
                        'warning' => 'electricity',
                        'info' => 'water',
                        'success' => 'maintenance',
                        'primary' => 'cleaning',
                        'danger' => 'security',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'electricity' => 'Listrik',
                        'water' => 'Air',
                        'maintenance' => 'Perawatan',
                        'cleaning' => 'Kebersihan',
                        'security' => 'Keamanan',
                        'internet' => 'Internet',
                        'tax' => 'Pajak',
                        'insurance' => 'Asuransi',
                        'other' => 'Lainnya',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'recorded',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'recorded' => 'Tercatat',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'electricity' => 'Listrik',
                        'water' => 'Air',
                        'maintenance' => 'Perawatan',
                        'cleaning' => 'Kebersihan',
                        'security' => 'Keamanan',
                        'internet' => 'Internet',
                        'tax' => 'Pajak',
                        'insurance' => 'Asuransi',
                        'other' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'recorded' => 'Tercatat',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\Filter::make('cost_date')
                    ->form([
                        Forms\Components\DatePicker::make('cost_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('cost_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['cost_from'], fn ($q, $date) => $q->whereDate('cost_date', '>=', $date))
                            ->when($data['cost_until'], fn ($q, $date) => $q->whereDate('cost_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (OperationalCost $record) {
                        $record->approve(Auth::id());
                    })
                    ->visible(fn (OperationalCost $record) => $record->status === 'recorded'),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (OperationalCost $record) => $record->reject())
                    ->visible(fn (OperationalCost $record) => $record->status === 'recorded'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('cost_date', 'desc');
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
            'index' => Pages\ListOperationalCosts::route('/'),
            'create' => Pages\CreateOperationalCost::route('/create'),
            'edit' => Pages\EditOperationalCost::route('/{record}/edit'),
        ];
    }
}
