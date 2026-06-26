<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Illuminate\Database\Seeder;

/**
 * Optional: seeds a few approved + pending reviews so the public "Reviews"
 * section and the admin Feedback screen have content out of the box.
 *
 *   php artisan db:seed --class=FeedbackSeeder
 */
class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $approved = [
            ['name' => 'Anjali Menon', 'rating' => 5, 'message' => 'Picked up and delivered the same day — my shirts came back spotless. Easily the best laundry service in town.'],
            ['name' => 'Rahul Nair', 'rating' => 5, 'message' => 'Booked through WhatsApp, tracked my order live, and the pricing was exactly as quoted. Highly recommended.'],
            ['name' => 'Fathima Beevi', 'rating' => 4, 'message' => 'Great quality and friendly riders. Would love an even earlier morning pickup slot.'],
            ['name' => 'Joseph Thomas', 'rating' => 5, 'message' => 'A stubborn curry stain on my favourite kurta is completely gone. Brilliant dry cleaning.'],
            ['name' => 'Sneha Pillai', 'rating' => 5, 'message' => 'Reliable, on time and the app makes everything simple. I have switched to them for all my laundry now.'],
        ];

        foreach ($approved as $row) {
            Feedback::create($row + [
                'is_approved' => true,
                'approved_at' => now()->subDays(random_int(1, 20)),
            ]);
        }

        // A couple awaiting moderation so the admin "Pending" tab isn't empty.
        Feedback::create(['name' => 'Vikram Das', 'rating' => 4, 'message' => 'Good service overall, the folding was very neat. Will use again next week.', 'is_approved' => false]);
        Feedback::create(['name' => 'Meera Krishnan', 'rating' => 5, 'message' => 'Loved the premium packaging and the gentle detergent they use for delicate fabrics.', 'is_approved' => false]);
    }
}
