<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class OverduePaymentsWidget extends BaseWidget
{
    protected static ?string $heading = '🚨 Penyewa Nunggak';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->with(['tenant', 'room'])
                    ->overdue()
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Penyewa')
                    ->searchable()
                    ->weight('bold')
                    ->color('danger'),

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
                    ->color('danger'),

                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('Telat')
                    ->state(function (Payment $record): string {
                        $days = Carbon::parse($record->due_date)->diffInDays(Carbon::today());
                        return $days . ' hari';
                    })
                    ->badge()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('remind')
                    ->label('Ingatkan')
                    ->icon('heroicon-o-bell-alert')
                    ->color('warning')
                    ->url(fn (Payment $record): string => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->tenant->phone) . '?text=' . urlencode("Halo {$record->tenant->name}, ini adalah pengingat pembayaran kos untuk periode " . $record->month_year->format('F Y') . " sebesar Rp " . number_format($record->amount, 0, ',', '.') . ". Mohon segera melakukan pembayaran. Terima kasih."))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('confirm')
                    ->label('Konfirmasi')
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
            ->emptyStateHeading('Tidak ada tunggakan')
            ->emptyStateDescription('Semua penyewa sudah membayar tepat waktu. Bagus!')
            ->emptyStateIcon('heroicon-o-face-smile')
            ->paginated(false);
    }
}
