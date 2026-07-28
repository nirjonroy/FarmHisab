<?php

namespace App\Policies;

use App\Models\MedicineRecord;
use App\Models\User;

class MedicineRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('medicine.view');
    }

    public function view(User $user, MedicineRecord $medicineRecord): bool
    {
        return $user->can('medicine.view');
    }

    public function create(User $user): bool
    {
        return $user->can('medicine.manage') || $user->can('vaccinations.manage');
    }

    public function update(User $user, MedicineRecord $medicineRecord): bool
    {
        return $user->can('medicine.manage') || $user->can('vaccinations.manage');
    }

    public function delete(User $user, MedicineRecord $medicineRecord): bool
    {
        return $user->can('medicine.manage');
    }
}
