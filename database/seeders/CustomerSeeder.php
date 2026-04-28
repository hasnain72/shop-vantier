<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'customer' => [
                    'first_name'         => 'Ahmed',
                    'last_name'          => 'Khan',
                    'email'              => 'ahmed.khan@example.com',
                    'phone'              => '+92-300-1111111',
                    'password'           => Hash::make('password'),
                    'state'              => 'enabled',
                    'accepts_marketing'  => true,
                    'verified_email'     => true,
                    'total_spent'        => 389.97,
                    'orders_count'       => 2,
                    'currency'           => 'USD',
                    'locale'             => 'en',
                    'note'               => 'Loyal customer. Prefers leather straps.',
                    'tags'               => ['vip', 'repeat-buyer'],
                ],
                'address' => [
                    'first_name'  => 'Ahmed',
                    'last_name'   => 'Khan',
                    'company'     => '',
                    'address1'    => 'House 5, Street 12, F-7/2',
                    'address2'    => '',
                    'city'        => 'Islamabad',
                    'province'    => 'Islamabad Capital Territory',
                    'country'     => 'Pakistan',
                    'country_code'=> 'PK',
                    'zip'         => '44000',
                    'phone'       => '+92-300-1111111',
                    'is_default'  => true,
                ],
            ],
            [
                'customer' => [
                    'first_name'         => 'Sara',
                    'last_name'          => 'Ali',
                    'email'              => 'sara.ali@example.com',
                    'phone'              => '+92-321-2222222',
                    'password'           => Hash::make('password'),
                    'state'              => 'enabled',
                    'accepts_marketing'  => false,
                    'verified_email'     => true,
                    'total_spent'        => 49.99,
                    'orders_count'       => 1,
                    'currency'           => 'USD',
                    'locale'             => 'en',
                ],
                'address' => [
                    'first_name'  => 'Sara',
                    'last_name'   => 'Ali',
                    'address1'    => 'Flat 3B, Gulshan-e-Iqbal Block 13D',
                    'city'        => 'Karachi',
                    'province'    => 'Sindh',
                    'country'     => 'Pakistan',
                    'country_code'=> 'PK',
                    'zip'         => '75300',
                    'phone'       => '+92-321-2222222',
                    'is_default'  => true,
                ],
            ],
            [
                'customer' => [
                    'first_name'         => 'James',
                    'last_name'          => 'Wilson',
                    'email'              => 'james.wilson@example.com',
                    'phone'              => '+1-555-333-4444',
                    'password'           => Hash::make('password'),
                    'state'              => 'enabled',
                    'accepts_marketing'  => true,
                    'verified_email'     => true,
                    'total_spent'        => 219.96,
                    'orders_count'       => 1,
                    'currency'           => 'USD',
                    'locale'             => 'en',
                ],
                'address' => [
                    'first_name'  => 'James',
                    'last_name'   => 'Wilson',
                    'address1'    => '742 Evergreen Terrace',
                    'city'        => 'Springfield',
                    'province'    => 'IL',
                    'country'     => 'United States',
                    'country_code'=> 'US',
                    'zip'         => '62704',
                    'phone'       => '+1-555-333-4444',
                    'is_default'  => true,
                ],
            ],
            [
                'customer' => [
                    'first_name'         => 'Fatima',
                    'last_name'          => 'Malik',
                    'email'              => 'fatima.malik@example.com',
                    'phone'              => '+92-333-5555555',
                    'password'           => Hash::make('password'),
                    'state'              => 'enabled',
                    'accepts_marketing'  => true,
                    'verified_email'     => true,
                    'total_spent'        => 0.00,
                    'orders_count'       => 0,
                    'currency'           => 'USD',
                    'locale'             => 'en',
                    'note'               => 'Signed up via newsletter.',
                    'tags'               => ['newsletter'],
                ],
                'address' => [
                    'first_name'  => 'Fatima',
                    'last_name'   => 'Malik',
                    'address1'    => 'Plot 44, DHA Phase 5',
                    'city'        => 'Lahore',
                    'province'    => 'Punjab',
                    'country'     => 'Pakistan',
                    'country_code'=> 'PK',
                    'zip'         => '54792',
                    'phone'       => '+92-333-5555555',
                    'is_default'  => true,
                ],
            ],
            [
                'customer' => [
                    'first_name'         => 'Omar',
                    'last_name'          => 'Farooq',
                    'email'              => 'omar.farooq@example.com',
                    'phone'              => '+971-50-6666666',
                    'password'           => Hash::make('password'),
                    'state'              => 'disabled',
                    'accepts_marketing'  => false,
                    'verified_email'     => false,
                    'total_spent'        => 0.00,
                    'orders_count'       => 0,
                    'currency'           => 'USD',
                    'locale'             => 'en',
                ],
                'address' => [
                    'first_name'  => 'Omar',
                    'last_name'   => 'Farooq',
                    'address1'    => 'Villa 7, JBR',
                    'city'        => 'Dubai',
                    'province'    => 'Dubai',
                    'country'     => 'United Arab Emirates',
                    'country_code'=> 'AE',
                    'zip'         => '00000',
                    'phone'       => '+971-50-6666666',
                    'is_default'  => true,
                ],
            ],
        ];

        foreach ($customers as $data) {
            $customer = Customer::firstOrCreate(
                ['email' => $data['customer']['email']],
                $data['customer']
            );

            if ($customer->wasRecentlyCreated) {
                CustomerAddress::create(
                    array_merge(['customer_id' => $customer->id], $data['address'])
                );
            }
        }
    }
}
