<!-- Footer Component -->
<footer class="bg-primary text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center mb-4">
                    <i class="fas fa-shield-alt text-action text-2xl mr-3"></i>
                    <span class="text-action text-xl font-bold">{{ config('app.name', 'Net On You') }}</span>
                </div>
                <p class="text-white/80 mb-4">
                    Empowering users with premium digital content and seamless experiences. 
                    Your trusted platform for quality magazines and digital services.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-white/80 hover:text-action transition-colors">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="#" class="text-white/80 hover:text-action transition-colors">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-white/80 hover:text-action transition-colors">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    <a href="#" class="text-white/80 hover:text-action transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-action font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('home') }}" class="text-white/80 hover:text-action transition-colors">
                            <i class="fas fa-home mr-2"></i>Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('magazines.index') }}" class="text-white/80 hover:text-action transition-colors">
                            <i class="fas fa-book mr-2"></i>Magazines
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="text-white/80 hover:text-action transition-colors">
                            <i class="fas fa-info-circle mr-2"></i>About
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="text-white/80 hover:text-action transition-colors">
                            <i class="fas fa-envelope mr-2"></i>Contact
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-action font-semibold mb-4">Support</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('help') }}" class="text-white/80 hover:text-action transition-colors">
                            <i class="fas fa-question-circle mr-2"></i>Help Center
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}" class="text-white/80 hover:text-action transition-colors">
                            <i class="fas fa-comments mr-2"></i>FAQ
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('support') }}" class="text-white/80 hover:text-action transition-colors">
                            <i class="fas fa-headset mr-2"></i>Contact Support
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms') }}" class="text-white/80 hover:text-action transition-colors">
                            <i class="fas fa-file-contract mr-2"></i>Terms of Service
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/20 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-white/60 text-sm mb-4 md:mb-0">
                    © {{ date('Y') }} {{ config('app.name', 'Net On You') }}. All rights reserved.
                </div>
                <div class="flex space-x-6 text-sm">
                    <a href="{{ route('privacy') }}" class="text-white/60 hover:text-action transition-colors">
                        Privacy Policy
                    </a>
                    <a href="{{ route('terms') }}" class="text-white/60 hover:text-action transition-colors">
                        Terms of Service
                    </a>
                    <a href="{{ route('cookies') }}" class="text-white/60 hover:text-action transition-colors">
                        Cookie Policy
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
