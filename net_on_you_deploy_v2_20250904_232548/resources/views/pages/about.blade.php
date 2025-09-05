@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-primary mb-4">About Net On You</h1>
                <p class="text-xl text-gray-600">Empowering knowledge through digital magazines</p>
            </div>
            
            <div class="prose prose-lg max-w-none">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h2 class="text-2xl font-semibold text-primary mb-4">Our Mission</h2>
                        <p class="text-gray-700 mb-4">
                            Net On You is dedicated to providing high-quality, informative content through our bimonthly digital magazines. 
                            We believe in making knowledge accessible to everyone, regardless of their location or background.
                        </p>
                        <p class="text-gray-700">
                            Our platform connects readers with expert insights, industry trends, and valuable information that helps 
                            them stay ahead in their personal and professional lives.
                        </p>
                    </div>
                    
                    <div>
                        <h2 class="text-2xl font-semibold text-primary mb-4">What We Offer</h2>
                        <ul class="text-gray-700 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-book text-action mr-3"></i>
                                Bimonthly digital magazines
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-globe text-action mr-3"></i>
                                Multi-language support
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-users text-action mr-3"></i>
                                Community-driven content
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-mobile-alt text-action mr-3"></i>
                                Mobile-friendly platform
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="text-center">
                    <h2 class="text-2xl font-semibold text-primary mb-4">Join Our Community</h2>
                    <p class="text-gray-700 mb-6">
                        Become part of our growing community of knowledge seekers and content creators.
                    </p>
                    <a href="{{ route('register') }}" class="bg-primary text-white px-8 py-3 rounded-lg hover:bg-primary/90 transition-colors inline-block">
                        Get Started Today
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
