<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class DueTodayPaymentsWidget extends BaseWidget
{
    protected static ?string $heading = '⚠️ Jatuh Tempo Hari Ini';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->with(['tenant', 'room'])
                    ->where('status', 'pending')
                    ->whereDate('due_date', Carbon::today())
                    ->orderBy('tenant_id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Penyewa')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tenant.phone')
                    ->label('No. HP')
                    ->copyable()
                    ->copyMessage('Nomor HP disalin!'),

                Tables\Columns\TextColumn::make('room.room_number')
                    ->label('Kamar')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->weight('bold')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('month_year')
                    ->label('Periode')
                    ->date('M Y'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color('warning'),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Konfirmasi Bayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?')
                    ->action(function (Payment $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'payment_date' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (Payment $record): string => route('filament.admin.resources.payments.edit', $record)),
            ])
            ->emptyStateHeading('Tidak ada jatuh tempo hari ini')
            ->emptyStateDescription('Semua pembayaran sudah lunas atau belum ada yang jatuh tempo.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}
