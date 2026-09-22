<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    public function create(Request $request)
    {
        $pending = $request->session()->get('pending_workspace');
        if (!$pending) {
            $landlordUser = Auth::guard('landlord')->user();
            if ($landlordUser) {
                $tenant = Tenant::where('settings->email', $landlordUser->email)->first();
                if ($tenant) {
                    $port = $request->getPort();
                    $portSuffix = ($port && $port != 80 && $port != 443) ? ':' . $port : '';
                    $url = $request->getScheme() . '://' . $tenant->domain . $portSuffix . '/login';
                    return Inertia::render('Auth/WorkspaceReady', [
                        'url' => $url
                    ]);
                }
            }
            Auth::guard('landlord')->logout();
            return redirect()->route('register');
        }
        return Inertia::render('Auth/CreateWorkspace');
    }

    public function store(Request $request)
    {
        $pending = $request->session()->get('pending_workspace');
        if (!$pending) {
            return response()->json(['error' => 'No pending workspace found'], 400);
        }

        $company_name = $pending['company_name'];
        $company_code = $pending['company_code'];
        $domain = $company_code . '.' . env('APP_DOMAIN', 'localhost'); // e.g. company.localhost

        $landlordUser = Auth::guard('landlord')->user();

        DB::connection('landlord')->beginTransaction();

        try {
            // 1. Create Tenant DB
            $dbName = 'tenant_' . str_replace('-', '_', $company_code) . '_' . rand(1000, 9999);
            DB::connection('landlord')->statement("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2. Create Tenant in Landlord
            $tenant = Tenant::create([
                'name' => $company_name,
                'code' => $company_code,
                'domain' => $domain, // Use the domain
                'database' => $dbName,
                'is_active' => true,
                'settings' => [
                    'email' => $landlordUser->email,
                    'phone' => $landlordUser->phone ?? '',
                ],
            ]);
            $tenant->makeCurrent();

            // 3. Clear connections
            DB::purge('mysql');
            DB::purge('landlord');

            // 4. Import Schema (Without Triggers)
            $schemaPath = database_path('tenant_schema.sql');
            if (file_exists($schemaPath)) {
                $schemaSql = file_get_contents($schemaPath);
                DB::connection('mysql')->unprepared("USE `{$dbName}`; " . $schemaSql);
            }

            // 4.5. Create Triggers manually (PDO cannot handle DELIMITER in dump files)
            $triggers = [
                "CREATE TRIGGER `update_balance_trigger` AFTER INSERT ON `fat_details` FOR EACH ROW BEGIN UPDATE myitems SET itmqty = ( SELECT COALESCE(SUM(qty_in), 0)  - COALESCE(SUM(qty_out), 0) FROM fat_details WHERE item_id = NEW.item_id  AND isdeleted = 0 ) WHERE id = NEW.item_id ; END",
                "CREATE TRIGGER `update_after_update` AFTER UPDATE ON `fat_details` FOR EACH ROW BEGIN UPDATE myitems SET itmqty = ( SELECT COALESCE(SUM(qty_in), 0) - COALESCE(SUM(qty_out), 0) FROM fat_details WHERE item_id = NEW.item_id AND isdeleted = 0 ) WHERE id = NEW.item_id AND isdeleted = 0 ; END",
                "CREATE TRIGGER `balance_after_insert` BEFORE INSERT ON `journal_entries` FOR EACH ROW BEGIN UPDATE acc_head SET balance = ( SELECT SUM(debit) - SUM(credit) FROM journal_entries WHERE account_id = NEW.account_id AND isdeleted != 1 ) WHERE id = NEW.account_id ; END",
                "CREATE TRIGGER `update_all_ins` AFTER INSERT ON `journal_entries` FOR EACH ROW BEGIN DECLARE debit_sum DECIMAL(18,2); DECLARE credit_sum DECIMAL(18,2); SELECT COALESCE(SUM(debit), 0), COALESCE(SUM(credit), 0) INTO debit_sum, credit_sum FROM journal_entries WHERE account_id = NEW.account_id; UPDATE acc_head SET balance = balance + NEW.debit - NEW.credit WHERE id = NEW.account_id; END",
                "CREATE TRIGGER `balance_after_update` AFTER UPDATE ON `journal_entries` FOR EACH ROW BEGIN UPDATE acc_head SET balance = ( SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) FROM journal_entries WHERE account_id = NEW.account_id AND isdeleted = 0 ) WHERE id = NEW.account_id; END",
                "CREATE TRIGGER `balance_after_delete` AFTER DELETE ON `journal_entries` FOR EACH ROW BEGIN UPDATE acc_head SET balance = ( SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) FROM journal_entries WHERE account_id = OLD.account_id AND isdeleted = 0 ) WHERE id = OLD.account_id; END"
            ];
            foreach ($triggers as $trigger) {
                DB::connection('mysql')->unprepared($trigger);
            }

            // 5. Create Admin User inside the new DB
            $user = User::create([
                'uname' => $landlordUser->name,
                'email' => $landlordUser->email,
                'password' => $landlordUser->password,
                'tenant' => $tenant->id,
                'userrole' => 1, // Admin
                'usertype' => 1,
                'is_waiter' => 0,
                'img' => 'default.png',
            ]);

            // 6. Seed the Chart of Accounts Recursively
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\ChartOfAccountsSeeder',
                '--database' => 'mysql', // Ensure it runs on the tenant DB
                '--force' => true
            ]);

            DB::connection('landlord')->commit();

            // Clear the session pending workspace
            $request->session()->forget('pending_workspace');

            // Optionally, we DO NOT log them into web guard here, because session cookies are often tied to domains.
            // If the user is redirected to a subdomain, their session might not carry over depending on SESSION_DOMAIN.
            // But we will give them the URL to the subdomain so they can login there!
            $port = $request->getPort();
            $portSuffix = ($port && $port != 80 && $port != 443) ? ':' . $port : '';
            $url = $request->getScheme() . '://' . $domain . $portSuffix . '/login';

            return response()->json([
                'url' => $url
            ]);

        } catch (\Exception $e) {
            try {
                DB::connection('landlord')->rollBack();
            } catch (\Exception $rollbackEx) {}

            try {
                if (isset($tenant)) {
                    DB::connection('landlord')->table('tenants')->where('id', $tenant->id)->delete();
                }
                if (isset($dbName)) {
                    DB::connection('landlord')->statement("DROP DATABASE IF EXISTS `{$dbName}`");
                }
            } catch (\Exception $cleanupEx) {}
            
            \Illuminate\Support\Facades\Log::error("Provisioning Failed: " . $e->getMessage() . " \n " . $e->getTraceAsString());
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء الشركة: ' . $e->getMessage()], 500);
        }
    }
}
