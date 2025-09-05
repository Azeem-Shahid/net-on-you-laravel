@extends('layouts.app')

@section('title', 'Contract - ' . $contract->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $contract->title }}</h1>
            <p class="text-gray-600">
                @if($contract->language === 'en')
                    Version {{ $contract->version }} - Effective {{ $contract->effective_date->format('M d, Y') }}
                @else
                    Versión {{ $contract->version }} - Vigente desde {{ $contract->effective_date->format('d/m/Y') }}
                @endif
            </p>
        </div>

        <!-- Contract Content -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <div class="prose prose-lg max-w-none">
                {!! nl2br(e($contract->content)) !!}
            </div>
        </div>

        <!-- Acceptance Section -->
        <div class="bg-white rounded-lg shadow-md p-8">
            @if($hasAccepted)
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full mb-4">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        @if($contract->language === 'en')
                            Contract Accepted
                        @else
                            Contrato Aceptado
                        @endif
                    </div>
                    <p class="text-gray-600">
                        @if($contract->language === 'en')
                            You have already accepted this contract version. You can proceed with your subscription.
                        @else
                            Ya has aceptado esta versión del contrato. Puedes proceder con tu suscripción.
                        @endif
                    </p>
                </div>
            @else
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        @if($contract->language === 'en')
                            Accept Contract to Continue
                        @else
                            Aceptar Contrato para Continuar
                        @endif
                    </h3>
                    
                    <form action="{{ route('contract.accept') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="bg-[#1d003f] hover:bg-[#2a0057] text-white font-semibold py-3 px-8 rounded-lg transition duration-200">
                            @if($contract->language === 'en')
                                I Accept the Terms and Conditions
                            @else
                                Acepto los Términos y Condiciones
                            @endif
                        </button>
                    </form>
                    
                    <p class="text-sm text-gray-500 mt-4">
                        @if($contract->language === 'en')
                            By clicking "I Accept", you agree to be bound by the terms and conditions outlined above.
                        @else
                            Al hacer clic en "Acepto", usted acepta estar sujeto a los términos y condiciones descritos anteriormente.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Free Access Notice -->
        @if($user->getsFreeAccess())
            <div class="bg-[#00ff00] border border-[#00e600] rounded-lg p-6 mt-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-[#1d003f] mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-lg font-semibold text-[#1d003f]">
                            @if($contract->language === 'en')
                                🎉 Free Access Granted!
                            @else
                                🎉 ¡Acceso Gratuito Otorgado!
                            @endif
                        </h4>
                        <p class="text-[#1d003f] font-medium">
                            {{ $user->getFreeAccessReason() }}
                        </p>
                        <p class="text-[#1d003f] text-sm mt-2">
                            @if($contract->language === 'en')
                                You have full access to all magazines and features without any payment required.
                            @else
                                Tienes acceso completo a todas las revistas y funciones sin ningún pago requerido.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @elseif($user->hasSpecialAccess())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-lg font-semibold text-blue-900">
                            @if($contract->language === 'en')
                                Special Access Granted
                            @else
                                Acceso Especial Otorgado
                            @endif
                        </h4>
                        <p class="text-blue-700">
                            @if($contract->language === 'en')
                                You have special access privileges and can proceed with your subscription without contract acceptance.
                            @else
                                Tienes privilegios de acceso especial y puedes proceder con tu suscripción sin aceptar el contrato.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Navigation -->
        <div class="flex justify-between mt-8">
            <a href="{{ route('dashboard') }}" class="text-[#1d003f] hover:text-[#2a0057] font-medium">
                @if($contract->language === 'en')
                    ← Back to Dashboard
                @else
                    ← Volver al Panel
                @endif
            </a>
            
            @if($user->getsFreeAccess())
                <a href="{{ route('dashboard') }}" class="bg-[#00ff00] hover:bg-[#00e600] text-[#1d003f] font-semibold py-2 px-6 rounded-lg transition duration-200">
                    @if($contract->language === 'en')
                        Go to Dashboard
                    @else
                        Ir al Panel
                    @endif
                </a>
            @elseif($hasAccepted || $user->hasSpecialAccess())
                <a href="{{ route('payment.checkout') }}" class="bg-[#00ff00] hover:bg-[#00e600] text-[#1d003f] font-semibold py-2 px-6 rounded-lg transition duration-200">
                    @if($contract->language === 'en')
                        Continue to Payment
                    @else
                        Continuar al Pago
                    @endif
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
