<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title'     => 'About Us',
                'slug'      => 'about-us',
                'published' => true,
                'body_html' => <<<HTML
<h2>Welcome to Watch Accessories Store</h2>
<p>We are passionate watch enthusiasts dedicated to bringing you the finest watch accessories from around the world. Founded in 2018, our store offers a curated selection of straps, bands, storage solutions, and tools for every watch lover.</p>

<h3>Our Mission</h3>
<p>To help every watch owner personalise, protect, and enjoy their timepieces to the fullest — at prices that don't require a second mortgage.</p>

<h3>Why Choose Us?</h3>
<ul>
  <li>Curated selection from trusted manufacturers worldwide</li>
  <li>Fast shipping with real-time order tracking</li>
  <li>30-day hassle-free returns</li>
  <li>Expert customer support 7 days a week</li>
</ul>

<h3>Where We Ship</h3>
<p>We ship to Pakistan, USA, UK, UAE, and most countries worldwide. Free shipping on orders over $50.</p>
HTML,
                'meta_title'       => 'About Us – Watch Accessories Store',
                'meta_description' => 'Learn about Watch Accessories Store, our mission, and our passion for watches.',
            ],
            [
                'title'     => 'Contact Us',
                'slug'      => 'contact-us',
                'published' => true,
                'body_html' => <<<HTML
<h2>Get in Touch</h2>
<p>We'd love to hear from you. Whether you have a question about a product, need help with an order, or just want to talk watches — we're here.</p>

<h3>Customer Support</h3>
<ul>
  <li><strong>Email:</strong> hello@watchstore.com</li>
  <li><strong>Phone:</strong> +92-300-1234567</li>
  <li><strong>Hours:</strong> Monday – Saturday, 9am – 6pm (PKT)</li>
</ul>

<h3>Headquarters</h3>
<p>Plot 12, Block A, SITE Industrial Area<br>Karachi, Sindh 75730<br>Pakistan</p>

<h3>Response Time</h3>
<p>We typically respond within 24 hours on business days. For urgent order issues, please include your order number in your message.</p>
HTML,
                'meta_title'       => 'Contact Us – Watch Accessories Store',
                'meta_description' => 'Contact our customer support team for help with orders, products, and more.',
            ],
            [
                'title'     => 'FAQ',
                'slug'      => 'faq',
                'published' => true,
                'body_html' => <<<HTML
<h2>Frequently Asked Questions</h2>

<h3>Orders & Shipping</h3>
<p><strong>How long does shipping take?</strong><br>
Pakistan: 2–4 business days. International: 7–15 business days depending on destination.</p>

<p><strong>Do you offer free shipping?</strong><br>
Yes! Free standard shipping on all orders over $50 / PKR 14,000.</p>

<p><strong>Can I track my order?</strong><br>
Yes. Once your order ships, you'll receive a tracking number by email.</p>

<h3>Returns & Refunds</h3>
<p><strong>What is your return policy?</strong><br>
We accept returns within 30 days of delivery. Items must be unused and in original packaging.</p>

<p><strong>How long do refunds take?</strong><br>
Refunds are processed within 3–5 business days after we receive the returned item.</p>

<h3>Products</h3>
<p><strong>Are your straps compatible with all watches?</strong><br>
Our straps are made in standard lug widths (18mm, 20mm, 22mm). Check your watch's lug width before ordering.</p>

<p><strong>Do you sell genuine leather straps?</strong><br>
Yes. All leather products are clearly labelled with their material. We stock genuine leather, top-grain leather, and vegan alternatives.</p>

<h3>Payments</h3>
<p><strong>What payment methods do you accept?</strong><br>
We accept Stripe (all major cards), PayPal, bank transfer, and Cash on Delivery (Pakistan only).</p>
HTML,
                'meta_title'       => 'FAQ – Watch Accessories Store',
                'meta_description' => 'Find answers to common questions about shipping, returns, products, and payments.',
            ],
            [
                'title'     => 'Shipping Policy',
                'slug'      => 'shipping-policy',
                'published' => true,
                'body_html' => <<<HTML
<h2>Shipping Policy</h2>
<p><em>Last updated: January 2026</em></p>

<h3>Processing Time</h3>
<p>All orders are processed within 1–2 business days. Orders placed on weekends or public holidays are processed the next business day.</p>

<h3>Shipping Rates</h3>
<table>
  <thead><tr><th>Destination</th><th>Standard</th><th>Express</th></tr></thead>
  <tbody>
    <tr><td>Pakistan</td><td>PKR 200 (Free over PKR 5,000)</td><td>PKR 500</td></tr>
    <tr><td>USA / Canada</td><td>$8 (Free over $50)</td><td>$18</td></tr>
    <tr><td>UK / Europe</td><td>$10 (Free over $50)</td><td>$22</td></tr>
    <tr><td>UAE / GCC</td><td>$7 (Free over $50)</td><td>$15</td></tr>
    <tr><td>Rest of World</td><td>$15 (Free over $80)</td><td>$35</td></tr>
  </tbody>
</table>

<h3>Delivery Times</h3>
<ul>
  <li>Pakistan: 2–4 business days (TCS / Leopards Courier)</li>
  <li>UAE & GCC: 4–7 business days</li>
  <li>USA & Canada: 7–14 business days</li>
  <li>UK & Europe: 7–14 business days</li>
  <li>Rest of World: 10–21 business days</li>
</ul>
HTML,
                'meta_title'       => 'Shipping Policy – Watch Accessories Store',
                'meta_description' => 'Learn about our shipping rates, processing times, and delivery estimates.',
            ],
            [
                'title'     => 'Return Policy',
                'slug'      => 'return-policy',
                'published' => true,
                'body_html' => <<<HTML
<h2>Return &amp; Refund Policy</h2>
<p><em>Last updated: January 2026</em></p>

<h3>Returns</h3>
<p>We accept returns within <strong>30 days</strong> of the delivery date. To be eligible, items must be:</p>
<ul>
  <li>Unused and in original condition</li>
  <li>In original packaging with all tags attached</li>
  <li>Not marked as final sale</li>
</ul>

<h3>How to Return</h3>
<ol>
  <li>Email us at returns@watchstore.com with your order number and reason for return.</li>
  <li>We'll send you a return shipping label (Pakistan only; international customers cover return shipping).</li>
  <li>Drop off the package at any TCS / Leopards outlet.</li>
</ol>

<h3>Refunds</h3>
<p>Once we receive and inspect your return, we'll notify you by email. Approved refunds are processed within <strong>3–5 business days</strong> back to your original payment method.</p>

<h3>Non-Returnable Items</h3>
<ul>
  <li>Personalized or custom-engraved items</li>
  <li>Hygiene products (cleaning cloths, polishing kits) once opened</li>
</ul>
HTML,
                'meta_title'       => 'Return Policy – Watch Accessories Store',
                'meta_description' => 'Learn how to return items and get a refund from Watch Accessories Store.',
            ],
            [
                'title'     => 'Privacy Policy',
                'slug'      => 'privacy-policy',
                'published' => true,
                'body_html' => <<<HTML
<h2>Privacy Policy</h2>
<p><em>Last updated: January 2026</em></p>

<p>Watch Accessories Store ("we", "our", "us") respects your privacy. This policy explains what data we collect, how we use it, and your rights.</p>

<h3>Data We Collect</h3>
<ul>
  <li>Name, email, phone, and address when you place an order or create an account</li>
  <li>Payment information (processed securely via Stripe/PayPal — we never store card details)</li>
  <li>Browser and device information for analytics</li>
</ul>

<h3>How We Use Your Data</h3>
<ul>
  <li>To process and fulfill your orders</li>
  <li>To send order confirmations and shipping updates</li>
  <li>To improve our website and services</li>
  <li>To send marketing emails (only with your consent)</li>
</ul>

<h3>Your Rights</h3>
<p>You have the right to access, correct, or delete your personal data at any time. Contact us at privacy@watchstore.com.</p>
HTML,
                'meta_title'       => 'Privacy Policy – Watch Accessories Store',
                'meta_description' => 'Read our privacy policy to understand how we handle your personal data.',
            ],
            [
                'title'     => 'Terms of Service',
                'slug'      => 'terms-of-service',
                'published' => true,
                'body_html' => <<<HTML
<h2>Terms of Service</h2>
<p><em>Last updated: January 2026</em></p>

<p>By accessing our website and purchasing from Watch Accessories Store, you agree to these terms.</p>

<h3>Orders</h3>
<p>All orders are subject to product availability. We reserve the right to cancel any order due to pricing errors or stock issues, with a full refund issued.</p>

<h3>Pricing</h3>
<p>All prices are in USD unless otherwise stated. We reserve the right to change prices at any time. Orders placed before a price change will be honoured at the original price.</p>

<h3>Intellectual Property</h3>
<p>All content on this website — including images, text, and logos — is the property of Watch Accessories Store and may not be used without written permission.</p>

<h3>Limitation of Liability</h3>
<p>We are not liable for indirect, incidental, or consequential damages arising from the use of our products or website.</p>

<h3>Governing Law</h3>
<p>These terms are governed by the laws of Pakistan.</p>
HTML,
                'meta_title'       => 'Terms of Service – Watch Accessories Store',
                'meta_description' => 'Read the terms and conditions for using Watch Accessories Store.',
            ],
        ];

        foreach ($pages as $data) {
            Page::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['published_at' => now()->subDays(rand(10, 90))])
            );
        }
    }
}
