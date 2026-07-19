<?php

namespace Tests\Feature;

use App\Enums\ProductUsageType;
use App\Models\FarmCategory;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('products.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_products(): void
    {
        $this->actingAs($this->viewer())->get(route('products.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('products.index'))->assertForbidden();
    }

    public function test_manager_can_create_and_update_a_product(): void
    {
        $manager = $this->manager();
        $category = $this->childCategory('Fertilizer');
        $unit = MeasurementUnit::factory()->create(['name_en' => 'Kilogram', 'short_name_en' => 'kg']);

        $this->actingAs($manager)->get(route('products.create'))->assertOk();

        $this->actingAs($manager)->post(route('products.store'), [
            'farm_category_id' => $category->id,
            'measurement_unit_id' => $unit->id,
            'name_en' => 'Urea Fertilizer',
            'name_bn' => 'ইউরিয়া সার',
            'sku' => ' urea-001 ',
            'barcode' => ' 123456 ',
            'usage_type' => ProductUsageType::INPUT,
            'sort_order' => 10,
            'is_stock_tracked' => '1',
            'is_active' => '1',
        ])->assertRedirect(route('products.index'));

        $product = Product::where('sku', 'UREA-001')->firstOrFail();

        $this->actingAs($manager)->get(route('products.edit', $product))->assertOk();

        $this->actingAs($manager)->put(route('products.update', $product), [
            'farm_category_id' => $category->id,
            'measurement_unit_id' => $unit->id,
            'name_en' => 'Hybrid Seed',
            'name_bn' => 'হাইব্রিড বীজ',
            'sku' => 'seed-001',
            'barcode' => null,
            'usage_type' => ProductUsageType::BOTH,
            'sort_order' => 20,
            'is_stock_tracked' => '0',
            'is_active' => '0',
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name_en' => 'Hybrid Seed',
            'sku' => 'SEED-001',
            'barcode' => null,
            'usage_type' => ProductUsageType::BOTH,
            'sort_order' => 20,
            'is_stock_tracked' => false,
            'is_active' => false,
        ]);
    }

    public function test_worker_can_view_but_cannot_manage_products(): void
    {
        $worker = User::factory()->create();
        $worker->syncRoles('worker');
        $product = $this->product();

        $this->actingAs($worker)->get(route('products.index'))->assertOk();
        $this->actingAs($worker)->get(route('products.create'))->assertForbidden();
        $this->actingAs($worker)->put(route('products.update', $product), $this->payload([
            'name_en' => 'Blocked',
            'sku' => $product->sku,
        ], $product))->assertForbidden();
    }

    public function test_product_can_be_created_with_both_language_names(): void
    {
        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'name_en' => 'Paddy',
            'name_bn' => 'ধান',
            'sku' => 'PADDY-001',
        ]))->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['name_en' => 'Paddy', 'name_bn' => 'ধান']);
    }

    public function test_product_can_be_created_with_only_one_language_name(): void
    {
        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'name_en' => null,
            'name_bn' => 'বাঁশ',
            'sku' => 'BAMBOO-001',
        ]))->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['name_en' => null, 'name_bn' => 'বাঁশ', 'sku' => 'BAMBOO-001']);
    }

    public function test_product_cannot_be_created_without_any_name(): void
    {
        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'name_en' => null,
            'name_bn' => null,
        ]))->assertSessionHasErrors(['name_en', 'name_bn']);
    }

    public function test_sku_must_be_unique(): void
    {
        $this->product(['sku' => 'DUPLICATE']);

        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'sku' => 'duplicate',
        ]))->assertSessionHasErrors('sku');
    }

    public function test_sku_is_normalized_to_uppercase(): void
    {
        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'sku' => ' egg-001 ',
        ]))->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['sku' => 'EGG-001']);
    }

    public function test_barcode_must_be_unique_when_provided(): void
    {
        $this->product(['barcode' => '12345']);

        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'sku' => 'BARCODE-TEST',
            'barcode' => '12345',
        ]))->assertSessionHasErrors('barcode');
    }

    public function test_valid_usage_type_is_required(): void
    {
        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'usage_type' => 'invalid',
        ]))->assertSessionHasErrors('usage_type');
    }

    public function test_product_must_belong_to_a_child_category(): void
    {
        $topLevel = FarmCategory::factory()->create(['parent_id' => null]);

        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'farm_category_id' => $topLevel->id,
        ]))->assertSessionHasErrors('farm_category_id');
    }

    public function test_top_level_category_cannot_be_selected(): void
    {
        $topLevel = FarmCategory::factory()->create(['name_en' => 'Agricultural Inputs', 'parent_id' => null]);
        $child = FarmCategory::factory()->create(['name_en' => 'Fertilizer', 'parent_id' => $topLevel->id]);

        $response = $this->actingAs($this->manager())->get(route('products.create'));

        $response->assertOk()->assertSee('Agricultural Inputs')->assertSee('Fertilizer');
        $html = $response->getContent();

        $this->assertStringNotContainsString('<option value="'.$topLevel->id.'"', $html);
        $this->assertStringContainsString('<option value="'.$child->id.'"', $html);
    }

    public function test_active_measurement_unit_can_be_selected(): void
    {
        $unit = MeasurementUnit::factory()->create(['is_active' => true]);

        $this->actingAs($this->manager())->post(route('products.store'), $this->payload([
            'measurement_unit_id' => $unit->id,
        ]))->assertRedirect(route('products.index'));
    }

    public function test_locale_specific_product_names_work(): void
    {
        $product = $this->product([
            'name_en' => 'Fish Feed',
            'name_bn' => 'মাছের খাবার',
        ]);

        App::setLocale('bn');
        $this->assertSame('মাছের খাবার', $product->display_name);

        App::setLocale('en');
        $this->assertSame('Fish Feed', $product->display_name);
    }

    public function test_search_works_with_bengali_name(): void
    {
        $this->product(['name_en' => 'Paddy', 'name_bn' => 'ধান', 'sku' => 'PADDY']);

        $this->actingAs($this->viewer('bn'))
            ->get(route('products.index', ['search' => 'ধান']))
            ->assertOk()
            ->assertSee('ধান')
            ->assertSee('Paddy');
    }

    public function test_search_works_with_english_name_and_sku(): void
    {
        $this->product(['name_en' => 'Bamboo', 'name_bn' => 'বাঁশ', 'sku' => 'BAMBOO']);

        $this->actingAs($this->viewer('en'))
            ->get(route('products.index', ['search' => 'Bamboo']))
            ->assertOk()
            ->assertSee('Bamboo')
            ->assertSee('BAMBOO');

        $this->actingAs($this->viewer('en'))
            ->get(route('products.index', ['search' => 'BAMBOO']))
            ->assertOk()
            ->assertSee('Bamboo')
            ->assertSee('BAMBOO');
    }

    public function test_category_filter_works(): void
    {
        $firstCategory = $this->childCategory('Fertilizer');
        $secondCategory = $this->childCategory('Seed');
        $first = $this->product(['name_en' => 'Urea', 'sku' => 'UREA', 'farm_category_id' => $firstCategory->id]);
        $second = $this->product(['name_en' => 'Rice Seed', 'sku' => 'RICE-SEED', 'farm_category_id' => $secondCategory->id]);

        $this->actingAs($this->viewer())
            ->get(route('products.index', ['farm_category_id' => $firstCategory->id]))
            ->assertOk()
            ->assertSee($first->sku)
            ->assertDontSee($second->sku);
    }

    public function test_usage_type_filter_works(): void
    {
        $input = $this->product(['name_en' => 'Fertilizer', 'sku' => 'FERT', 'usage_type' => ProductUsageType::INPUT]);
        $output = $this->product(['name_en' => 'Eggs', 'sku' => 'EGGS', 'usage_type' => ProductUsageType::OUTPUT]);

        $this->actingAs($this->viewer())
            ->get(route('products.index', ['usage_type' => ProductUsageType::INPUT]))
            ->assertOk()
            ->assertSee($input->sku)
            ->assertDontSee($output->sku);
    }

    public function test_status_filter_works(): void
    {
        $active = $this->product(['sku' => 'ENABLED-PRODUCT', 'is_active' => true]);
        $inactive = $this->product(['sku' => 'DISABLED-PRODUCT', 'is_active' => false]);

        $this->actingAs($this->viewer())
            ->get(route('products.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee($inactive->sku)
            ->assertDontSee($active->sku);
    }

    public function test_stock_tracked_filter_works(): void
    {
        $tracked = $this->product(['sku' => 'COUNTED-PRODUCT', 'is_stock_tracked' => true]);
        $notTracked = $this->product(['sku' => 'SERVICE-PRODUCT', 'is_stock_tracked' => false]);

        $this->actingAs($this->viewer())
            ->get(route('products.index', ['stock_tracked' => 'not-tracked']))
            ->assertOk()
            ->assertSee($notTracked->sku)
            ->assertDontSee($tracked->sku);
    }

    public function test_created_by_stores_authenticated_user(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager)->post(route('products.store'), $this->payload([
            'sku' => 'CREATED-BY',
        ]))->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'sku' => 'CREATED-BY',
            'created_by' => $manager->id,
        ]);
    }

    private function payload(array $overrides = [], ?Product $product = null): array
    {
        return array_merge([
            'farm_category_id' => $product?->farm_category_id ?? $this->childCategory()->id,
            'measurement_unit_id' => $product?->measurement_unit_id ?? MeasurementUnit::factory()->create(['is_active' => true])->id,
            'name_en' => 'Test Product',
            'name_bn' => 'টেস্ট পণ্য',
            'sku' => 'TEST-SKU-'.mt_rand(1000, 9999),
            'barcode' => null,
            'usage_type' => ProductUsageType::BOTH,
            'sort_order' => 0,
            'is_stock_tracked' => '1',
            'is_active' => '1',
        ], $overrides);
    }

    private function product(array $overrides = []): Product
    {
        return Product::factory()->create(array_merge([
            'farm_category_id' => $this->childCategory()->id,
            'measurement_unit_id' => MeasurementUnit::factory()->create(['is_active' => true])->id,
        ], $overrides));
    }

    private function childCategory(string $name = 'Broiler'): FarmCategory
    {
        $parent = FarmCategory::factory()->create(['parent_id' => null, 'name_en' => 'Parent '.$name]);

        return FarmCategory::factory()->create(['parent_id' => $parent->id, 'name_en' => $name]);
    }

    private function manager(string $locale = 'en'): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->syncRoles('manager');

        return $user;
    }

    private function viewer(string $locale = 'en'): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->givePermissionTo('products.view');

        return $user;
    }
}
