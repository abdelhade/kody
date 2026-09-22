<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountService
{
    /**
     * Create a new chart of account record safely.
     *
     * @param array $data
     * @return Account
     * @throws ValidationException
     */
    public function createAccount(array $data): Account
    {
        $aname = trim($data['aname'] ?? '');
        if (empty($aname)) {
            throw ValidationException::withMessages([
                'aname' => 'اسم الحساب مطلوب.'
            ]);
        }

        // Check if account name already exists
        if (Account::where('aname', $aname)->where('isdeleted', 0)->exists()) {
            throw ValidationException::withMessages([
                'aname' => 'يوجد حساب آخر بنفس هذا الاسم في شجرة الحسابات.'
            ]);
        }

        $parentId = (int) ($data['parent_id'] ?? 0);
        $parent = $parentId ? Account::find($parentId) : null;

        // Auto-generate code if not provided
        $code = trim($data['code'] ?? '');
        if (empty($code) && $parent) {
            $code = $this->generateNextCode($parent);
        }

        return DB::transaction(function () use ($data, $aname, $code, $parentId, $parent) {
            return Account::create([
                'aname'         => $aname,
                'code'          => $code,
                'parent_id'     => $parentId,
                'kind'          => $parent ? $parent->kind : ($data['kind'] ?? 1),
                'nature'        => $parent ? $parent->nature : ($data['nature'] ?? 1),
                'is_basic'      => !empty($data['is_basic']) ? 1 : 0,
                'is_stock'      => !empty($data['is_stock']) ? 1 : 0,
                'is_fund'       => !empty($data['is_fund']) ? 1 : 0,
                'phone'         => $data['phone'] ?? null,
                'address'       => $data['address'] ?? null,
                'e_mail'        => $data['e_mail'] ?? null,
                'info'          => $data['info'] ?? null,
                'secret'        => !empty($data['secret']) ? 1 : 0,
                'start_balance' => $data['start_balance'] ?? 0,
                'balance'       => $data['start_balance'] ?? 0,
                'debit'         => 0,
                'credit'        => 0,
                'isdeleted'     => 0,
                'tenant'        => $data['tenant'] ?? 0,
                'branch'        => $data['branch'] ?? 0,
            ]);
        });
    }

    /**
     * Generate next hierarchical code under parent account
     */
    public function generateNextCode(Account $parent): string
    {
        $maxChildCode = Account::where('parent_id', $parent->id)->max('code');
        if ($maxChildCode && is_numeric($maxChildCode)) {
            return (string) ((int) $maxChildCode + 1);
        }

        return $parent->code . '001';
    }
}
