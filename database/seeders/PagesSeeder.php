<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // About Us page
        Page::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => '<h2>Welcome to our Electronics Product Comparison System</h2>
            <p>Our mission is to help customers make informed decisions by providing comprehensive, unbiased comparisons of electronic products.</p>
            <p>Founded in 2023, we\'ve grown to become one of the most trusted sources for electronics comparison and reviews. Our team of experts thoroughly tests and evaluates products across various categories to provide you with accurate and reliable information.</p>
            <h3>Why Choose Us?</h3>
            <ul>
                <li><strong>Unbiased Reviews:</strong> We don\'t accept payments for favorable reviews.</li>
                <li><strong>Comprehensive Comparisons:</strong> Side-by-side comparison of specifications, features, and performance.</li>
                <li><strong>Expert Analysis:</strong> Our team has years of experience in the electronics industry.</li>
                <li><strong>User-Friendly Interface:</strong> Easy to navigate and find what you\'re looking for.</li>
            </ul>
            <p>Thank you for choosing our platform for your electronic product comparison needs. We\'re committed to helping you make the best purchasing decisions.</p>',
            'meta_title' => 'About Us | Electronics Product Comparison System',
            'meta_description' => 'Learn about our mission to provide comprehensive, unbiased comparisons of electronic products to help you make informed purchasing decisions.',
            'is_active' => true,
        ]);

        // Contact Us page
        Page::create([
            'title' => 'Contact Us',
            'slug' => 'contact-us',
            'content' => '<h2>Get in Touch</h2>
            <p>We\'d love to hear from you! Whether you have a question about our services, need help with a comparison, or want to provide feedback, our team is here to assist you.</p>
            <h3>Contact Information</h3>
            <ul>
                <li><strong>Email:</strong> info@electrocompare.com</li>
                <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                <li><strong>Address:</strong> 123 Tech Street, Digital City, EC 12345</li>
                <li><strong>Business Hours:</strong> Monday to Friday, 9:00 AM - 5:00 PM</li>
            </ul>
            <p>Fill out the form below, and we\'ll get back to you as soon as possible, usually within 24-48 hours during business days.</p>',
            'meta_title' => 'Contact Us | Electronics Product Comparison System',
            'meta_description' => 'Have questions or feedback? Contact our team for assistance with our electronics product comparison services.',
            'is_active' => true,
        ]);
    }
} 