@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-primary mb-4">Contact Us</h1>
                <p class="text-xl text-gray-600">We'd love to hear from you</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Get in Touch</h2>
                    <p class="text-gray-700 mb-6">
                        Have questions about our platform? Need support with your subscription? 
                        We're here to help! Reach out to us through any of the channels below.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-action mr-3 text-xl"></i>
                            <div>
                                <p class="font-semibold text-gray-800">Email</p>
                                <p class="text-gray-600">support@netonyou.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-clock text-action mr-3 text-xl"></i>
                            <div>
                                <p class="font-semibold text-gray-800">Response Time</p>
                                <p class="text-gray-600">Within 24 hours</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-globe text-action mr-3 text-xl"></i>
                            <div>
                                <p class="font-semibold text-gray-800">Platform</p>
                                <p class="text-gray-600">Available 24/7</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Send us a Message</h2>
                    <form class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" id="subject" name="subject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea id="message" name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-primary/90 transition-colors">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
