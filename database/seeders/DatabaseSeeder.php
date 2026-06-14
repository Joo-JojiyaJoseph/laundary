<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles & granular permissions
        $permissions = [
            "orders.view", "orders.create", "orders.update", "orders.delete",
            "customers.view", "customers.create", "customers.update", "customers.delete",
            "payments.view", "payments.create", "reports.view", "settings.manage",
            "branches.manage", "pricing.manage", "riders.manage", "chat.use",
        ];
        foreach ($permissions as $p) Permission::findOrCreate($p);

        $roles = [
            "super-admin"    => $permissions,
            "admin"          => array_diff($permissions, ["branches.manage"]),
            "branch-manager" => ["orders.view", "orders.create", "orders.update", "customers.view", "customers.create",
                                 "customers.update", "payments.view", "payments.create", "reports.view", "pricing.manage", "riders.manage", "chat.use"],
            "counter-staff"  => ["orders.view", "orders.create", "customers.view", "customers.create", "payments.create", "chat.use"],
            "rider"          => ["orders.view", "chat.use"],
            "customer"       => [],
        ];
        foreach ($roles as $role => $perms) Role::findOrCreate($role)->syncPermissions($perms);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $branch = Branch::firstOrCreate(["code" => "HQ"], [
            "name" => "Laundrix HQ", "city" => "Kochi", "pincode" => "682001", "phone" => "+91 90000 00000",
        ]);

        $admin = User::firstOrCreate(["email" => "admin@laundrix.ai"], [
            "name" => "Super Admin", "password" => "password", "branch_id" => $branch->id, "mobile" => "9000000001",
        ]);
        $admin->assignRole("super-admin");

        // Services & sample products
        $catalog = [
            "Dry Cleaning"    => ["icon" => "beaker",  "turnaround" => "48h", "price" => 99,  "items" => [["Shirt", "pc", 99], ["Suit (2 pc)", "set", 449], ["Saree", "pc", 199]]],
            "Wash & Fold"     => ["icon" => "sparkles", "turnaround" => "24h", "price" => 59,  "items" => [["Mixed load", "kg", 79], ["Bedsheet", "pc", 99]]],
            "Premium Laundry" => ["icon" => "star",    "turnaround" => "48h", "price" => 149, "items" => [["Designer wear", "pc", 299]]],
            "Steam Ironing"   => ["icon" => "fire",    "turnaround" => "12h", "price" => 15,  "items" => [["Shirt / Top", "pc", 15], ["Trousers", "pc", 18]]],
            "Shoe Cleaning"   => ["icon" => "cube",    "turnaround" => "72h", "price" => 249, "items" => [["Sneakers", "pair", 349], ["Leather shoes", "pair", 449]]],
            "Curtain Cleaning"=> ["icon" => "swatch",  "turnaround" => "72h", "price" => 129, "items" => [["Curtain panel", "pc", 179]]],
            "Stain Removal"   => ["icon" => "shield-check", "turnaround" => "48h", "price" => 79, "items" => [["Spot treatment", "pc", 99]]],
            "Toy Cleaning"    => ["icon" => "heart",   "turnaround" => "48h", "price" => 99,  "items" => [["Soft toy (M)", "pc", 149]]],
            "Bag Cleaning"    => ["icon" => "briefcase","turnaround" => "72h", "price" => 299, "items" => [["Handbag", "pc", 399]]],
        ];

        // Product categories first (the hierarchy is Category → Service → Item)
        $categories = [];
        foreach (["Garments", "Household", "Footwear", "Accessories"] as $k => $categoryName) {
            $categories[$categoryName] = \App\Models\ProductCategory::firstOrCreate(
                ["name" => $categoryName], ["priority" => $k]
            );
        }

        // Which category each service belongs under
        $serviceCategory = [
            "Dry Cleaning" => "Garments", "Wash & Fold" => "Household", "Premium Laundry" => "Garments",
            "Steam Ironing" => "Garments", "Shoe Cleaning" => "Footwear", "Curtain Cleaning" => "Household",
            "Stain Removal" => "Garments", "Toy Cleaning" => "Accessories", "Bag Cleaning" => "Accessories",
        ];

        foreach (array_values(array_keys($catalog)) as $i => $name) {
            $meta = $catalog[$name];
            $categoryId = $categories[$serviceCategory[$name] ?? "Garments"]->id ?? null;
            $service = Service::firstOrCreate(["slug" => str()->slug($name)], [
                "name" => $name, "icon" => $meta["icon"], "starting_price" => $meta["price"],
                "turnaround" => $meta["turnaround"], "priority" => $i,
                "product_category_id" => $categoryId,
                "description" => "Professional {$name} with eco-friendly detergents, garment-level tagging and live tracking.",
                "benefits" => ["Live order tracking", "Free pickup & delivery", "Fabric-safe chemistry", "48h express available"],
                "faqs" => [["q" => "How long does it take?", "a" => "Standard turnaround is {$meta["turnaround"]}; express options at checkout."]],
            ]);
            // Backfill category on existing rows
            if ($categoryId && ! $service->product_category_id) {
                $service->update(["product_category_id" => $categoryId]);
            }
            foreach ($meta["items"] as $j => [$pname, $uom, $price]) {
                Product::firstOrCreate(["service_id" => $service->id, "name" => $pname], [
                    "uom" => $uom, "price" => $price, "priority" => $j,
                ]);
            }
        }

        // Map products to categories (for item grouping)
        $categoryMap = [
            "Garments"   => ["Shirt", "Suit (2 pc)", "Saree", "Designer wear", "Shirt / Top", "Trousers", "Spot treatment"],
            "Household"  => ["Mixed load", "Bedsheet", "Curtain panel"],
            "Footwear"   => ["Sneakers", "Leather shoes"],
            "Accessories"=> ["Handbag", "Soft toy (M)"],
        ];
        foreach ($categoryMap as $categoryName => $names) {
            Product::whereIn("name", $names)
                ->whereNull("product_category_id")
                ->update(["product_category_id" => $categories[$categoryName]->id]);
        }
    }
}
