@extends('layouts.app')

@section('meta_title', $page->meta_title ?? 'Contact Us')
@section('meta_description', $page->meta_description ?? 'Get in touch with our team')

@section('content')
<!-- Hero section with overlay -->
<div class="contact-header position-relative mb-5">
    <div class="overlay"></div>
    <div class="container position-relative py-5 text-white">
        <div class="row py-5">
            <div class="col-lg-8">
                <div class="contact-badge mb-3">CONTACT US</div>
                <h1 class="display-4 fw-bold mb-3">{{ $page->title ?? 'Get In Touch' }}</h1>
                <p class="lead">We're here to help and answer any questions you might have. We look forward to hearing from you.</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="row g-4">
                <!-- Contact Information -->
                <div class="col-md-5">
                    <div class="card contact-card shadow border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title mb-4">Contact Information</h3>
                            
                            <!-- Address -->
                            <div class="contact-info-item mb-4">
                                <div class="contact-icon bg-primary text-white">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Our Address</h5>
                                    <p>{{ $contactInfo->address ?? '123 Comparison Ave, Tech City, 10001' }}</p>
                                </div>
                            </div>
                            
                            <!-- Phone -->
                            <div class="contact-info-item mb-4">
                                <div class="contact-icon bg-primary text-white">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Phone Number</h5>
                                    <p>{{ $contactInfo->phone ?? '+1 (123) 456-7890' }}</p>
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="contact-info-item mb-4">
                                <div class="contact-icon bg-primary text-white">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Email Address</h5>
                                    <p>{{ $contactInfo->email ?? 'info@compareelectronics.com' }}</p>
                                </div>
                            </div>
                            
                            <!-- Social Links -->
                            <div class="social-links mt-5">
                                <h5 class="mb-3">Follow Us</h5>
                                <div class="d-flex gap-3">
                                    <a href="#" class="social-link facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="social-link twitter">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="social-link instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="#" class="social-link linkedin">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="col-md-7">
                    <div class="card contact-card shadow border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title mb-4">Send Message</h3>
                            
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            <form action="{{ route('contact.send') }}" method="POST" class="contact-form">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Your Name" value="{{ old('name') }}">
                                            <label for="name">Your Name</label>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Your Email" value="{{ old('email') }}">
                                            <label for="email">Your Email</label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" placeholder="Subject" value="{{ old('subject') }}">
                                        <label for="subject">Subject</label>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" placeholder="Your Message" style="height: 150px">{{ old('message') }}</textarea>
                                        <label for="message">Your Message</label>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i> Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Google Map -->
            <div class="mt-5">
                <div class="card contact-card shadow border-0">
                    <div class="card-body p-0">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.305935303!2d-74.25986548248684!3d40.69714941932609!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1613088584816!5m2!1sen!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 