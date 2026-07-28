<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Enums\ExpenseCategory;
use App\Enums\PaymentMethod;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-07-29 10:00:00');
        $this->seed(RolePermissionSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('expenses.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_expenses(): void
    {
        $this->actingAs($this->viewer())->get(route('expenses.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('expenses.index'))->assertForbidden();
    }

    public function test_worker_cannot_access_or_manage_expenses(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $record = Expense::factory()->create(['batch_id' => $this->batch()->id]);

        $this->actingAs($worker)->get(route('expenses.index'))->assertForbidden();
        $this->actingAs($worker)->get(route('expenses.create'))->assertForbidden();
        $this->actingAs($worker)->post(route('expenses.store'), $this->payload())->assertForbidden();
        $this->actingAs($worker)->get(route('expenses.edit', $record))->assertForbidden();
        $this->actingAs($worker)->delete(route('expenses.destroy', $record))->assertForbidden();
    }

    public function test_manager_can_create_update_and_soft_delete_expense(): void
    {
        $manager = $this->manager();
        $batch = $this->batch();

        $this->actingAs($manager)->post(route('expenses.store'), $this->payload([
            'batch_id' => $batch->id,
            'title' => '  Shed cleaning labor  ',
            'payee' => '  Cleaning Team  ',
            'amount' => 1250.50,
        ]))->assertRedirect();

        $record = Expense::firstOrFail();

        $this->assertSame('Shed cleaning labor', $record->title);
        $this->assertSame('Cleaning Team', $record->payee);
        $this->assertEquals(1250.50, (float) $record->amount);
        $this->assertSame($manager->id, $record->created_by);

        $this->actingAs($manager)->put(route('expenses.update', $record), $this->payload([
            'batch_id' => $batch->id,
            'expense_date' => '2026-07-22',
            'category' => ExpenseCategory::MAINTENANCE,
            'payment_method' => PaymentMethod::BANK,
            'amount' => 2000,
        ]))->assertRedirect(route('expenses.show', $record));

        $record->refresh();
        $this->assertSame('2026-07-22', $record->expense_date->format('Y-m-d'));
        $this->assertSame(ExpenseCategory::MAINTENANCE, $record->category->value);
        $this->assertSame(PaymentMethod::BANK, $record->payment_method->value);

        $this->actingAs($manager)->delete(route('expenses.destroy', $record))->assertRedirect(route('expenses.index'));
        $this->assertSoftDeleted('expenses', ['id' => $record->id]);
    }

    public function test_validation_rejects_invalid_expense_data(): void
    {
        $batch = $this->batch(['purchase_date' => '2026-07-10']);

        $this->actingAs($this->manager())->post(route('expenses.store'), $this->payload([
            'batch_id' => $batch->id,
            'expense_date' => '2026-07-01',
            'category' => 'invalid',
            'title' => '',
            'amount' => 0,
            'payment_method' => 'invalid',
        ]))->assertSessionHasErrors(['expense_date', 'category', 'title', 'amount', 'payment_method']);
    }

    public function test_search_category_payment_batch_and_date_filters_work(): void
    {
        $firstBatch = $this->batch(['batch_no' => 'B-2026-110', 'batch_name' => 'Expense Filter']);
        $secondBatch = $this->batch(['batch_no' => 'B-2026-111', 'batch_name' => 'Hidden Expense']);
        $visible = Expense::factory()->create([
            'batch_id' => $firstBatch->id,
            'expense_date' => '2026-07-20',
            'category' => ExpenseCategory::LABOR,
            'payment_method' => PaymentMethod::CASH,
            'title' => 'Cleaning labor',
            'payee' => 'Growth Team',
        ]);
        Expense::factory()->create([
            'batch_id' => $secondBatch->id,
            'expense_date' => '2026-06-20',
            'category' => ExpenseCategory::UTILITIES,
            'payment_method' => PaymentMethod::BANK,
            'title' => 'Hidden expense',
            'payee' => 'Hidden Team',
        ]);

        $this->actingAs($this->viewer())
            ->get(route('expenses.index', [
                'search' => 'Cleaning',
                'batch_id' => $firstBatch->id,
                'category' => ExpenseCategory::LABOR,
                'payment_method' => PaymentMethod::CASH,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->title)
            ->assertSee('Tk'.number_format((float) $visible->amount, 2))
            ->assertDontSee('Hidden expense');
    }

    public function test_expenses_update_batch_live_details(): void
    {
        $batch = $this->batch([
            'total_purchase_cost' => 25000,
            'medicine_budget' => 0,
            'other_budget' => 0,
        ]);

        Expense::factory()->create([
            'batch_id' => $batch->id,
            'amount' => 3500,
        ]);

        $this->actingAs($this->viewer(['expenses.view', 'batches.view']))->get(route('batches.show', $batch))
            ->assertOk()
            ->assertSee('Tk28,500.00')
            ->assertSee('Tk-28,500.00');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'batch_id' => $this->batch()->id,
            'expense_date' => '2026-07-20',
            'category' => ExpenseCategory::LABOR,
            'title' => 'Shed cleaning labor',
            'payee' => 'Farm worker',
            'amount' => 1200,
            'payment_method' => PaymentMethod::CASH,
            'reference_no' => 'EXP-001',
            'notes' => 'Expense notes',
        ], $overrides);
    }

    private function batch(array $overrides = []): Batch
    {
        $parent = FarmCategory::factory()->create(['parent_id' => null, 'name_en' => 'Poultry']);
        $birdType = FarmCategory::factory()->create(['parent_id' => $parent->id, 'name_en' => 'Broiler']);
        $breed = FarmVariety::factory()->create(['farm_category_id' => $birdType->id]);

        return Batch::factory()->create(array_merge([
            'farm_id' => Farm::factory()->create(['is_active' => true])->id,
            'bird_type_id' => $birdType->id,
            'breed_id' => $breed->id,
            'purchase_date' => '2026-07-10',
            'status' => BatchStatus::ACTIVE,
        ], $overrides));
    }

    private function manager(): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('manager');

        return $user;
    }

    private function viewer(array $permissions = ['expenses.view']): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
