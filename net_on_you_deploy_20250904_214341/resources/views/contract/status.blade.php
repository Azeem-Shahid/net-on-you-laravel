@extends('layouts.app')

@section('title', 'Contract Status')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                @if($user->language === 'en')
                    Contract Status
                @else
                    Estado del Contrato
                @endif
            </h1>
        </div>

        <!-- Current Contract Status -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                @if($user->language === 'en')
                    Current Contract
                @else
                    Contrato Actual
                @endif
            </h2>
            
            @if($contract)
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $contract->title }}</h3>
                            <p class="text-gray-600">
                                @if($user->language === 'en')
                                    Version {{ $contract->version }} - Effective {{ $contract->effective_date->format('M d, Y') }}
                                @else
                                    Versión {{ $contract->version }} - Vigente desde {{ $contract->effective_date->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            @if($hasAccepted)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    @if($user->language === 'en')
                                        Accepted
                                    @else
                                        Aceptado
                                    @endif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    @if($user->language === 'en')
                                        Not Accepted
                                    @else
                                        No Aceptado
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    @if(!$hasAccepted && !$user->hasSpecialAccess())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-yellow-800">
                                        @if($user->language === 'en')
                                            You need to accept the current contract to proceed with your subscription.
                                        @else
                                            Necesitas aceptar el contrato actual para proceder con tu suscripción.
                                        @endif
                                    </p>
                                    <a href="{{ route('contract.show') }}" class="text-yellow-800 underline hover:text-yellow-600">
                                        @if($user->language === 'en')
                                            View and Accept Contract
                                        @else
                                            Ver y Aceptar Contrato
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">
                        @if($user->language === 'en')
                            No contract available for your language.
                        @else
                            No hay contrato disponible para tu idioma.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Free Access Status -->
        @if($user->getsFreeAccess())
            <div class="bg-[#00ff00] border border-[#00e600] rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-[#1d003f] mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-lg font-semibold text-[#1d003f]">
                            @if($user->language === 'en')
                                🎉 Free Access Granted!
                            @else
                                🎉 ¡Acceso Gratuito Otorgado!
                            @endif
                        </h4>
                        <p class="text-[#1d003f] font-medium">
                            {{ $user->getFreeAccessReason() }}
                        </p>
                        <p class="text-[#1d003f] text-sm mt-2">
                            @if($user->language === 'en')
                                You have full access to all magazines and features without any payment required.
                            @else
                                Tienes acceso completo a todas las revistas y funciones sin ningún pago requerido.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @elseif($user->hasSpecialAccess())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-lg font-semibold text-blue-900">
                            @if($user->language === 'en')
                                Special Access Privileges
                            @else
                                Privilegios de Acceso Especial
                            @endif
                        </h4>
                        <p class="text-blue-700">
                            @if($user->language === 'en')
                                You have special access privileges and can proceed with your subscription without contract acceptance.
                            @else
                                Tienes privilegios de acceso especial y puedes proceder con tu suscripción sin aceptar el contrato.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Acceptance History -->
        @if($acceptanceHistory->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    @if($user->language === 'en')
                        Acceptance History
                    @else
                        Historial de Aceptación
                    @endif
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @if($user->language === 'en')
                                        Contract Version
                                    @else
                                        Versión del Contrato
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @if($user->language === 'en')
                                        Language
                                    @else
                                        Idioma
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @if($user->language === 'en')
                                        Accepted Date
                                    @else
                                        Fecha de Aceptación
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @if($user->language === 'en')
                                        IP Address
                                    @else
                                        Dirección IP
                                    @endif
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($acceptanceHistory as $acceptance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $acceptance->contract->version }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ strtoupper($acceptance->contract->language) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $acceptance->accepted_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $acceptance->ip_address ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Navigation -->
        <div class="flex justify-between mt-8">
            <a href="{{ route('dashboard') }}" class="text-[#1d003f] hover:text-[#2a0057] font-medium">
                @if($user->language === 'en')
                    ← Back to Dashboard
                @else
                    ← Volver al Panel
                @endif
            </a>
            
            @if($contract && !$hasAccepted && !$user->hasSpecialAccess())
                <a href="{{ route('contract.show') }}" class="bg-[#1d003f] hover:bg-[#2a0057] text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    @if($user->language === 'en')
                        View Contract
                    @else
                        Ver Contrato
                    @endif
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
