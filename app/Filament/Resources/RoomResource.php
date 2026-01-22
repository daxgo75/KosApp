<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Kamar Kos';

    protected static ?string $modelLabel = 'Kamar';

    protected static ?string $pluralModelLabel = 'Kamar Kos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kamar')
                    ->schema([
                        Forms\Components\TextInput::make('room_number')
                            ->label('Nomor Kamar')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('room_type')
                            ->label('Tipe Kamar')
                            ->options([
                                'standard' => 'Standard',
                                'deluxe' => 'Deluxe',
                                'premium' => 'Premium',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('monthly_price')
                            ->label('Harga Sewa/Bulan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(999999999),
                        Forms\Components\TextInput::make('capacity')
                            ->label('Kapasitas (orang)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'available' => 'Tersedia',
                                'occupied' => 'Terisi',
                                'maintenance' => 'Perbaikan',
                            ])
                            ->default('available')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Nomor Kamar')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('room_type')
                    ->label('Tipe')
                    ->colors([
                        'secondary' => 'standard',
                        'primary' => 'deluxe',
                        'success' => 'premium',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'standard' => 'Standard',
                        'deluxe' => 'Deluxe',
                        'premium' => 'Premium',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('monthly_price')
                    ->label('Harga/Bulan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->suffix(' orang')
                    ->alignCenter(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'available',
                        'danger' => 'occupied',
                        'warning' => 'maintenance',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'maintenance' => 'Perbaikan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'maintenance' => 'Perbaikan',
                    ]),
                Tables\Filters\SelectFilter::make('room_type')
                    ->label('Tipe Kamar')
                    ->options([
                        'standard' => 'Standard',
                        'deluxe' => 'Deluxe',
                        'premium' => 'Premium',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('room_number');
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
