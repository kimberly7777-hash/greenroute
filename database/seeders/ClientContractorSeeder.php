<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Contractor;
use App\Models\Schedule;
use App\Models\Invoice;
use App\Models\Feedback;
use Carbon\Carbon;

class ClientContractorSeeder extends Seeder
{
    public function run(): void
    {
        // Create contractor user (James Harden)
        $contractorUser = User::updateOrCreate(
            ['email' => 'harden@gmail.com'],
            [
                'name' => 'JAMES HARDEN',
                'user_type' => 'contractor',
                'password' => bcrypt('password'),
                'subscription_completed' => true,
                'subscription_status' => 'active'
            ]
        );

        // Create contractor profile
        Contractor::updateOrCreate(
            ['user_id' => $contractorUser->id],
            [
                'company_name' => 'Harden Waste Management',
                'name' => 'JAMES HARDEN',
                'email' => 'harden@gmail.com',
                'phone' => '+255789123456',
                'address' => '456 Industrial Ave, Dar es Salaam',
                'license_number' => 'WM2024001',
                'vehicle_type' => 'Waste Collection Truck',
                'license_plate' => 'T123ABC'
            ]
        );

        // Create client user (Juma Hassan)
        $clientUser = User::updateOrCreate(
            ['email' => 'Mwalimu@gmail.com'],
            [
                'name' => 'Juma Hassan',
                'user_type' => 'client',
                'password' => bcrypt('password')
            ]
        );

        // Update existing client or create new one
        $client = Client::updateOrCreate(
            ['registration_number' => 'CL041204'],
            [
                'contractor_id' => $contractorUser->id,
                'user_id' => $clientUser->id,
                'name' => 'Juma Hassan',
                'contact_name' => 'Juma Hassan',
                'email' => 'Mwalimu@gmail.com',
                'phone' => '0789047590',
                'phone_2' => '0712345678',
                'address' => '123 Uhuru Street, Kinondoni',
                'city' => 'Dar es Salaam',
                'state' => 'Dar es Salaam',
                'zip_code' => '12345',
                'category' => 'residential',
                'status' => 'active',
                'latitude' => -6.7924,
                'longitude' => 39.2083
            ]
        );

        // Create schedules
        $schedules = [
            [
                'pickup_date' => Carbon::now()->addDays(3),
                'pickup_time' => '08:00:00',
                'service_type' => 'collection',
                'status' => 'scheduled'
            ],
            [
                'pickup_date' => Carbon::now()->subDays(7),
                'pickup_time' => '09:30:00',
                'service_type' => 'collection',
                'status' => 'completed'
            ],
            [
                'pickup_date' => Carbon::now()->subDays(14),
                'pickup_time' => '08:15:00',
                'service_type' => 'collection',
                'status' => 'completed'
            ],
            [
                'pickup_date' => Carbon::now()->subDays(21),
                'pickup_time' => '08:00:00',
                'service_type' => 'collection',
                'status' => 'cancelled'
            ]
        ];

        foreach ($schedules as $scheduleData) {
            Schedule::create([
                'client_id' => $client->id,
                'contractor_id' => $contractorUser->id,
                'pickup_date' => $scheduleData['pickup_date'],
                'pickup_time' => $scheduleData['pickup_time'],
                'pickup_location' => 'Client Location',
                'pickup_address' => $client->address,
                'city' => $client->city,
                'state' => $client->state,
                'zip_code' => $client->zip_code,
                'service_type' => $scheduleData['service_type'],
                'status' => $scheduleData['status']
            ]);
        }

        // Create invoices
        $invoices = [
            [
                'invoice_number' => 'INV-2024-001',
                'invoice_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->subDays(5)->addDays(30),
                'total_amount' => 150.00,
                'status' => 'paid',
                'paid_at' => Carbon::now()->subDays(3)
            ],
            [
                'invoice_number' => 'INV-2024-002',
                'invoice_date' => Carbon::now()->subDays(35),
                'due_date' => Carbon::now()->subDays(35)->addDays(30),
                'total_amount' => 150.00,
                'status' => 'paid',
                'paid_at' => Carbon::now()->subDays(30)
            ],
            [
                'invoice_number' => 'INV-2024-003',
                'invoice_date' => Carbon::now()->subDays(65),
                'due_date' => Carbon::now()->subDays(65)->addDays(30),
                'total_amount' => 150.00,
                'status' => 'paid',
                'paid_at' => Carbon::now()->subDays(60)
            ],
            [
                'invoice_number' => 'INV-2024-004',
                'invoice_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(30),
                'total_amount' => 150.00,
                'status' => 'sent'
            ]
        ];

        foreach ($invoices as $invoiceData) {
            Invoice::create([
                'client_id' => $client->id,
                'contractor_id' => $contractorUser->id,
                'invoice_number' => $invoiceData['invoice_number'],
                'invoice_date' => $invoiceData['invoice_date'],
                'due_date' => $invoiceData['due_date'],
                'subtotal' => $invoiceData['total_amount'],
                'total_amount' => $invoiceData['total_amount'],
                'service_type' => 'Waste Collection',
                'status' => $invoiceData['status'],
                'paid_at' => $invoiceData['paid_at'] ?? null
            ]);
        }

        // Create feedback
        Feedback::create([
            'client_id' => $client->id,
            'contractor_id' => $contractorUser->id,
            'subject' => 'Service Quality',
            'message' => 'Very satisfied with the waste collection service. Always on time and professional.',
            'status' => 'resolved'
        ]);

        Feedback::create([
            'client_id' => $client->id,
            'contractor_id' => $contractorUser->id,
            'subject' => 'Schedule Change Request',
            'message' => 'Would like to change pickup time from 8 AM to 9 AM due to work schedule.',
            'status' => 'open'
        ]);
    }
}