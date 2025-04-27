@extends('layouts.app')

@section('meta_title', $page->meta_title ?? 'About Us')
@section('meta_description', $page->meta_description ?? 'Learn more about our electronic product comparison platform')

@section('content')
<!-- Hero section with overlay -->
<div class="about-header position-relative mb-5">
    <div class="overlay"></div>
    <div class="container position-relative py-5 text-white">
        <div class="row py-5">
            <div class="col-lg-8">
                <div class="about-badge mb-3">ABOUT US</div>
                <h1 class="display-4 fw-bold mb-3">{{ $page->title ?? 'Our Story' }}</h1>
                <p class="lead">Dedicated to helping you make informed decisions about electronic products through transparent, comprehensive comparisons.</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Mission Section -->
    <div class="row mb-5">
        <div class="col-lg-12 text-center">
            <h2 class="section-title">Our Mission</h2>
            <p class="lead mb-5">We believe in transparency, accuracy, and putting our users first.</p>
        </div>
    </div>
    
    <div class="row g-4 mb-5">
        <!-- Transparency -->
        <div class="col-md-3">
            <div class="card mission-card shadow border-0">
                <div class="card-body text-center p-4">
                    <div class="mission-icon mx-auto">
                        <i class="fas fa-check fa-lg"></i>
                    </div>
                    <h4 class="mb-3">Transparency</h4>
                    <p class="mb-0">We provide clear, unbiased information so you can make informed decisions without hidden agendas.</p>
                </div>
            </div>
        </div>
        
        <!-- Accuracy -->
        <div class="col-md-3">
            <div class="card mission-card shadow border-0">
                <div class="card-body text-center p-4">
                    <div class="mission-icon mx-auto">
                        <i class="fas fa-balance-scale fa-lg"></i>
                    </div>
                    <h4 class="mb-3">Accuracy</h4>
                    <p class="mb-0">Our team meticulously verifies all data to ensure you receive only the most accurate information.</p>
                </div>
            </div>
        </div>
        
        <!-- User First -->
        <div class="col-md-3">
            <div class="card mission-card shadow border-0">
                <div class="card-body text-center p-4">
                    <div class="mission-icon mx-auto">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <h4 class="mb-3">User First</h4>
                    <p class="mb-0">Every feature we build is designed with your needs in mind, prioritizing user experience above all.</p>
                </div>
            </div>
        </div>
        
        <!-- Innovation -->
        <div class="col-md-3">
            <div class="card mission-card shadow border-0">
                <div class="card-body text-center p-4">
                    <div class="mission-icon mx-auto">
                        <i class="fas fa-rocket fa-lg"></i>
                    </div>
                    <h4 class="mb-3">Innovation</h4>
                    <p class="mb-0">We're constantly improving our platform with new features and technologies to better serve you.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="row mb-5">
        <div class="col-lg-12 text-center">
            <h2 class="section-title">Meet Our Team</h2>
            <p class="lead mb-5">The dedicated professionals behind our platform</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- John Doe -->
        <div class="col-lg-4 col-md-6">
            <div class="team-card card shadow border-0">
                <div class="team-img-container">
                    <img src="{{ asset('img/team/team1.jpg') }}" class="team-img" alt="John Doe">
                </div>
                <div class="team-info text-center">
                    <h4 class="team-name">John Doe</h4>
                    <p class="team-position">Founder & CEO</p>
                    <p class="mb-0">John's vision and passion for technology led to the creation of this platform. With over 15 years of experience in the tech industry, he brings extensive knowledge and insight.</p>
                </div>
            </div>
        </div>

        <!-- Jane Smith -->
        <div class="col-lg-4 col-md-6">
            <div class="team-card card shadow border-0">
                <div class="team-img-container">
                    <img src="{{ asset('img/team/team2.jpg') }}" class="team-img" alt="Jane Smith">
                </div>
                <div class="team-info text-center">
                    <h4 class="team-name">Jane Smith</h4>
                    <p class="team-position">Technical Director</p>
                    <p class="mb-0">Jane leads our technical team with her deep understanding of electronics and software development. She ensures our comparison algorithms are accurate and unbiased.</p>
                </div>
            </div>
        </div>

        <!-- Mike Johnson -->
        <div class="col-lg-4 col-md-6">
            <div class="team-card card shadow border-0">
                <div class="team-img-container">
                    <img src="{{ asset('img/team/team3.jpg') }}" class="team-img" alt="Mike Johnson">
                </div>
                <div class="team-info text-center">
                    <h4 class="team-name">Mike Johnson</h4>
                    <p class="team-position">Head of Content</p>
                    <p class="mb-0">Mike's expertise in product research and content creation ensures that our platform provides comprehensive, accurate, and useful information for all users.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="cta-section mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card bg-primary text-white shadow border-0 rounded-lg">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-4">Ready to start comparing products?</h3>
                            <p class="lead mb-4">Join thousands of users who make informed decisions with our platform.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-search me-2"></i> Explore Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 