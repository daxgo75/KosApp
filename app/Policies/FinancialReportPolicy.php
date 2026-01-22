<?php

namespace App\Policies;

use App\Models\FinancialReport;
use App\Models\User;

class FinancialReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    public function view(User $user, FinancialReport $report): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    public function update(User $user, FinancialReport $report): bool
    {
        return $user->hasRole('admin') && $report->status === 'draft';
    }

    public function delete(User $user, FinancialReport $report): bool
    {
        return $user->hasRole('admin');
    }

    public function publish(User $user, FinancialReport $report): bool
    {
        return $user->hasRole('admin') && $report->status === 'draft';
    }

    public function archive(User $user, FinancialReport $report): bool
    {
        return $user->hasRole('admin') && $report->status === 'published';
    }
}
