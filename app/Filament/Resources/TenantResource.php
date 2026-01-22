<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Penyewa';

    protected static ?string $modelLabel = 'Penyewa';

    protected static ?string $pluralModelLabel = 'Penyewa';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Informasi Dasar')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Data Pribadi')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Lengkap')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Nomor Telepon')
                                            ->tel()
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\DatePicker::make('birth_date')
                                            ->label('Tanggal Lahir')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y'),
                                    ])->columns(2),

                                Section::make('Data Identitas')
                                    ->schema([
                                        Forms\Components\Select::make('identity_type')
                                            ->label('Jenis Identitas')
                                            ->options([
                                                'ktp' => 'KTP',
                                                'sim' => 'SIM',
                                                'passport' => 'Passport',
                                            ])
                                            ->default('ktp')
                                            ->required(),
                                        Forms\Components\TextInput::make('identity_number')
                                            ->label('Nomor Identitas')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Forms\Components\DatePicker::make('identity_expiry_date')
                                            ->label('Tanggal Kadaluarsa')
                                            ->native(false)
                                            ->displayFormat('d/m/Y'),
                                    ])->columns(3),

                                Section::make('Alamat')
                                    ->schema([
                                        Forms\Components\Textarea::make('address')
                                            ->label('Alamat Lengkap')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('city')
                                            ->label('Kota')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('province')
                                            ->label('Provinsi')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('postal_code')
                                            ->label('Kode Pos')
                                            ->required()
                                            ->maxLength(255),
                                    ])->columns(3),
                            ]),

                        Tabs\Tab::make('Status & Catatan')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'active' => 'Aktif',
                                                'inactive' => 'Tidak Aktif',
                                                'suspended' => 'Ditangguhkan',
                                            ])
                                            ->default('active')
                                            ->required(),
                                        Forms\Components\Textarea::make('notes')
                                            ->label('Catatan')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Foto KTP & Dokumen')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Forms\Components\FileUpload::make('ktp_photos')
                                            ->label('Foto KTP')
                                            ->image()
                                            ->multiple()
                                            ->directory('tenants/ktp')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                                            ->helperText('Upload foto KTP (maksimal 2MB per file)'),
                                        Forms\Components\FileUpload::make('room_photos')
                                            ->label('Foto Kamar')
                                            ->image()
                                            ->multiple()
                                            ->directory('tenants/rooms')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                                            ->helperText('Upload foto kamar (maksimal 2MB per file)'),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('identity_number')
                    ->label('No. Identitas')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'suspended',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
            'view' => Pages\ViewTenant::route('/{record}'),
        ];
    }
}
