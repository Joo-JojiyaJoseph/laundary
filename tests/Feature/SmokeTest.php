<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Service;
use App\Models\Product;
use App\Models\ProductCategory;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

function admin(): User
{
    $u = User::first() ?: User::factory()->create();
    return $u;
}

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders public home, track and login', function () {
    $this->get('/')->assertOk();
    $this->get('/track')->assertOk();
    $this->get('/login')->assertOk();
});

it('home page has SEO meta tags, favicon and the feedback section', function () {
    $res = $this->get('/');
    $res->assertOk()
        ->assertSee('name="description"', false)
        ->assertSee('name="keywords"', false)
        ->assertSee('property="og:image"', false)
        ->assertSee('/favicon.svg', false)
        ->assertSee('id="feedback"', false)
        ->assertSee('What our customers say');
});

it('serves the favicon and icon assets', function () {
    foreach (['/favicon.ico', '/favicon.svg', '/icons/icon-192.png', '/icons/og-image.png'] as $asset) {
        expect(file_exists(public_path(ltrim($asset, '/'))))->toBeTrue("missing {$asset}");
    }
});

it('admin can view every admin index page', function () {
    $this->actingAs(admin());
    foreach ([
        '/admin/dashboard', '/admin/pos', '/admin/orders', '/admin/payments',
        '/admin/customers', '/admin/services', '/admin/categories', '/admin/items',
        '/admin/price-lists', '/admin/riders', '/admin/branches',
    ] as $url) {
        $res = $this->get($url);
        expect($res->status())->toBe(200, "URL {$url} returned {$res->status()}");
    }
});

it('creates a customer through the livewire form', function () {
    $this->actingAs(admin());
    Livewire::test(App\Livewire\Admin\Customers\Index::class)
        ->call('create')
        ->set('name', 'Test Person')
        ->set('mobile', '9876500001')
        ->set('address', '12 Test Lane')
        ->call('save')
        ->assertHasNoErrors();
    expect(Customer::where('mobile', '9876500001')->exists())->toBeTrue();
});

it('contact form validates and saves', function () {
    Livewire::test(App\Livewire\Public\ContactSection::class)
        ->set('name', 'Asha')
        ->set('email', 'asha@example.com')
        ->set('phone', '9876500002')
        ->set('message', 'I would like a pickup please.')
        ->call('submit')
        ->assertHasNoErrors();
});

it('requires name, mobile and address for a customer', function () {
    $this->actingAs(admin());
    Livewire::test(App\Livewire\Admin\Customers\Index::class)
        ->call('create')
        ->set('name', '')
        ->set('mobile', '')
        ->set('address', '')
        ->call('save')
        ->assertHasErrors(['name', 'mobile', 'address']);
});

it('does not require optional customer fields', function () {
    $this->actingAs(admin());
    Livewire::test(App\Livewire\Admin\Customers\Index::class)
        ->call('create')
        ->set('name', 'Minimal Person')
        ->set('mobile', '9876511111')
        ->set('address', 'Some street 5')
        ->call('save')
        ->assertHasNoErrors();
    expect(Customer::where('mobile', '9876511111')->exists())->toBeTrue();
});

it('rejects a duplicate mobile with a friendly message instead of crashing', function () {
    $this->actingAs(admin());
    Customer::create(['name' => 'First', 'mobile' => '9876522222', 'address' => 'A']);
    Livewire::test(App\Livewire\Admin\Customers\Index::class)
        ->call('create')
        ->set('name', 'Second')
        ->set('mobile', '9876522222')
        ->set('address', 'B')
        ->call('save')
        ->assertHasErrors(['mobile']);
    expect(Customer::where('mobile', '9876522222')->count())->toBe(1);
});

it('only stores feedback after phone verification', function () {
    $component = Livewire::test(App\Livewire\Public\FeedbackSection::class)
        ->set('name', 'Ravi')
        ->set('mobile', '9876533333')
        ->set('rating', 5)
        ->set('message', 'Fantastic service, very fast!')
        ->call('sendCode')
        ->assertHasNoErrors()
        ->assertSet('awaitingCode', true);

    // No feedback yet — only an OTP was created.
    expect(App\Models\Feedback::count())->toBe(0);
    $otp = App\Models\OtpCode::where('mobile', '9876533333')->latest()->first();
    expect($otp)->not->toBeNull();

    // Wrong code is rejected.
    $component->set('code', '000000')->call('verifyAndSubmit')->assertHasErrors(['code']);
    expect(App\Models\Feedback::count())->toBe(0);

    // Correct code stores a verified review.
    $component->set('code', $otp->code)->call('verifyAndSubmit')->assertHasNoErrors();
    $fb = App\Models\Feedback::first();
    expect($fb)->not->toBeNull()
        ->and($fb->is_verified)->toBeTrue()
        ->and($fb->mobile)->toBe('9876533333');
});

it('validates the feedback form fields', function () {
    Livewire::test(App\Livewire\Public\FeedbackSection::class)
        ->set('name', '')
        ->set('mobile', 'abc')
        ->set('message', '')
        ->call('sendCode')
        ->assertHasErrors(['name', 'mobile', 'message']);
});

it('shows only verified feedback publicly', function () {
    App\Models\Feedback::create(['name' => 'Shown', 'mobile' => '111', 'rating' => 5, 'message' => 'Verified review here', 'is_verified' => true, 'verified_at' => now()]);
    App\Models\Feedback::create(['name' => 'Hidden', 'mobile' => '222', 'rating' => 1, 'message' => 'Unverified review here', 'is_verified' => false]);

    Livewire::test(App\Livewire\Public\FeedbackSection::class)
        ->assertSee('Verified review here')
        ->assertDontSee('Unverified review here');
});

it('creates a category and rejects duplicates with a friendly message', function () {
    $this->actingAs(admin());
    Livewire::test(App\Livewire\Admin\Categories\Index::class)
        ->call('create')->set('name', 'Unique Cat')->call('save')->assertHasNoErrors();
    expect(ProductCategory::where('name', 'Unique Cat')->count())->toBe(1);

    Livewire::test(App\Livewire\Admin\Categories\Index::class)
        ->call('create')->set('name', 'Unique Cat')->call('save')->assertHasErrors(['name']);
    expect(ProductCategory::where('name', 'Unique Cat')->count())->toBe(1);
});

it('creates a service, item and branch through their forms', function () {
    $this->actingAs(admin());
    $cat = ProductCategory::first();
    $svc = Service::first();

    Livewire::test(App\Livewire\Admin\Services\Index::class)
        ->call('create')->set('name', 'Smoke Service')->set('product_category_id', $cat->id)
        ->call('save')->assertHasNoErrors();
    expect(Service::where('name', 'Smoke Service')->exists())->toBeTrue();

    Livewire::test(App\Livewire\Admin\Products\Index::class)
        ->call('create')->set('form_category_id', $cat->id)->set('service_id', $svc->id)
        ->set('name', 'Smoke Item')->set('uom', 'pc')->set('price', 99)
        ->call('save')->assertHasNoErrors();
    expect(Product::where('name', 'Smoke Item')->exists())->toBeTrue();

    Livewire::test(App\Livewire\Admin\Branches\Index::class)
        ->call('create')->set('name', 'Smoke Branch')->call('save')->assertHasNoErrors();
    expect(Branch::where('name', 'Smoke Branch')->exists())->toBeTrue();
});

it('rejects an empty service name', function () {
    $this->actingAs(admin());
    Livewire::test(App\Livewire\Admin\Services\Index::class)
        ->call('create')->set('name', '')->call('save')->assertHasErrors(['name']);
});

it('completes a POS checkout and creates an order', function () {
    $this->actingAs(admin());
    $customer = Customer::create(['name' => 'POS Buyer', 'mobile' => '9876599999', 'address' => 'POS Street']);
    $product = Product::first();

    $before = App\Models\Order::count();

    Livewire::test(App\Livewire\Pos\Terminal::class)
        ->set('customerId', $customer->id)
        ->call('addToCart', $product->id)
        ->set('pickupDate', now()->toDateString())
        ->set('deliveryDate', now()->addDay()->toDateString())
        ->set('deliveryTime', '18:00')
        ->call('checkout')
        ->assertHasNoErrors();

    expect(App\Models\Order::count())->toBe($before + 1);
    $order = App\Models\Order::where('customer_id', $customer->id)->latest()->first();
    expect($order)->not->toBeNull()
        ->and($order->items()->count())->toBeGreaterThan(0)
        ->and($order->invoice)->not->toBeNull();
});
