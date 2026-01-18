@extends('layouts.admin.app')

@section('title', translate('Courier_Service'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 mb-sm-20">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <i class="fi fi-sr-truck-side"></i>
                {{ translate('Courier_Service') }}
            </h2>
        </div>

        <div class="card mb-3">
            <div class="card-header px-20 py-3">
                <div>
                    <h2>{{ translate('Courier_Service_Configuration') }}</h2>
                    <p class="mb-0 fs-12">
                        {{ translate('configure_courier_services_for_order_delivery_management') }}.
                    </p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column gap-20">
                    @forelse($couriers as $courier)
                        <div class="card card-sm shadow-1">
                            <div class="card-body">
                                <form action="{{ route('admin.third-party.courier-service.update', $courier->id) }}" 
                                      method="post" 
                                      class="form-advance-validation form-advance-inputs-validation non-ajax-form-validate" 
                                      novalidate>
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="view-details-container">
                                        <div class="d-flex justify-content-between align-items-center gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($courier->logo)
                                                    <img src="{{ dynamicAsset(path: 'public/' . $courier->logo) }}" 
                                                         alt="{{ $courier->title }}" 
                                                         class="rounded"
                                                         style="width: 50px; height: 50px; object-fit: contain;">
                                                @endif
                                                <div>
                                                    <h3 class="mb-1">
                                                        {{ $courier->title }}
                                                        <span class="badge badge-soft-{{ $courier->type == 'Fixed' ? 'primary' : ($courier->type == 'Weight-Based' ? 'info' : 'warning') }}">
                                                            {{ $courier->type }}
                                                        </span>
                                                    </h3>
                                                    <p class="mb-0 fs-12">
                                                        {{ translate('configure_settings_for') }} {{ $courier->title }} {{ translate('courier_service') }}.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="javascript:" class="fs-12 fw-semibold d-flex align-items-end view-btn">
                                                    {{ translate('View') }}
                                                    <i class="fi fi-rr-arrow-small-down fs-16 trans3"></i>
                                                </a>
                                                <label class="switcher">
                                                    <input class="switcher_input courier-status-toggle" 
                                                           type="checkbox" 
                                                           value="1" 
                                                           name="is_active"
                                                           data-id="{{ $courier->id }}"
                                                           {{ $courier->is_active ? 'checked' : '' }}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="d--none view-details mt-3 mt-sm-4">
                                            <div class="p-12 p-sm-20 bg-section rounded">
                                                <div class="row g-3">
                                                    <!-- General Information -->
                                                    <div class="col-12">
                                                        <h4 class="mb-3">{{ translate('General_Information') }}</h4>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Courier_Title') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="title"
                                                                   value="{{ $courier->title }}"
                                                                   placeholder="{{ translate('ex: Pathao, Bponi') }}"
                                                                   required>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Courier_Type') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-control" name="type" required>
                                                                <option value="Fixed" {{ $courier->type == 'Fixed' ? 'selected' : '' }}>{{ translate('Fixed') }}</option>
                                                                <option value="Weight-Based" {{ $courier->type == 'Weight-Based' ? 'selected' : '' }}>{{ translate('Weight-Based') }}</option>
                                                                <option value="Distance-Based" {{ $courier->type == 'Distance-Based' ? 'selected' : '' }}>{{ translate('Distance-Based') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Pricing Logic -->
                                                    @if($courier->title !== 'SteadFast' && $courier->title !== 'Redx')
                                                    <div class="col-12 mt-4">
                                                        <h4 class="mb-3">{{ translate('Pricing_Logic') }}</h4>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Minimum_Charge') }}</label>
                                                            <input type="number" 
                                                                   class="form-control" 
                                                                   name="min_charge"
                                                                   value="{{ $courier->min_charge }}"
                                                                   step="0.01"
                                                                   min="0"
                                                                   placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Maximum_Charge') }}</label>
                                                            <input type="number" 
                                                                   class="form-control" 
                                                                   name="max_charge"
                                                                   value="{{ $courier->max_charge }}"
                                                                   step="0.01"
                                                                   min="0"
                                                                   placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Delivery_Charge') }}</label>
                                                            <input type="number" 
                                                                   class="form-control" 
                                                                   name="delivery_charge"
                                                                   value="{{ $courier->delivery_charge }}"
                                                                   step="0.01"
                                                                   min="0"
                                                                   placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <!-- API Configuration -->
                                                    <div class="col-12 mt-4">
                                                        <h4 class="mb-3">{{ translate('API_Configuration') }}</h4>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ $courier->title == 'SteadFast' ? translate('Base_URL') : translate('Live_Base_URL') }}</label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="base_url"
                                                                   value="{{ $courier->base_url }}"
                                                                   placeholder="{{ translate('ex: https://api-hermes.pathao.com') }}">
                                                        </div>
                                                    </div>

                                                    @if($courier->title !== 'SteadFast')
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Test_Base_URL') }}</label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="test_base_url"
                                                                   value="{{ $courier->test_base_url }}"
                                                                   placeholder="{{ translate('ex: https://hermes-api.p-stageenv.xyz') }}">
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ $courier->title == 'SteadFast' ? translate('Api-Key') : ($courier->title == 'Redx' ? translate('Token') : translate('Client_ID')) }}</label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="client_id"
                                                                   value="{{ env('APP_MODE') != 'demo' ? $courier->client_id : '' }}"
                                                                   placeholder="{{ translate('Enter_Client_ID') }}">
                                                        </div>
                                                    </div>

                                                    @if($courier->title !== 'Redx')
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ $courier->title == 'SteadFast' ? translate('Secret-Key') : translate('Client_Secret') }}</label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="client_secret"
                                                                   value="{{ env('APP_MODE') != 'demo' ? $courier->client_secret : '' }}"
                                                                   placeholder="{{ translate('Enter_Client_Secret') }}">
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($courier->title !== 'SteadFast')
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ $courier->title == 'Redx' ? translate('Test_Token') : translate('Test_Client_ID') }}</label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="test_client_id"
                                                                   value="{{ env('APP_MODE') != 'demo' ? $courier->test_client_id : '' }}"
                                                                   placeholder="{{ translate('Enter_Test_Client_ID') }}">
                                                        </div>
                                                    </div>

                                                    @if($courier->title !== 'Redx')
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Test_Client_Secret') }}</label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="test_client_secret"
                                                                   value="{{ env('APP_MODE') != 'demo' ? $courier->test_client_secret : '' }}"
                                                                   placeholder="{{ translate('Enter_Test_Client_Secret') }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Client_Email') }}</label>
                                                            <input type="email" 
                                                                   class="form-control" 
                                                                   name="client_email"
                                                                   value="{{ env('APP_MODE') != 'demo' ? $courier->client_email : '' }}"
                                                                   placeholder="{{ translate('Enter_Client_Email') }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Client_Password') }}</label>
                                                            <input type="password" 
                                                                   class="form-control" 
                                                                   name="client_password"
                                                                   value="{{ env('APP_MODE') != 'demo' ? $courier->client_password : '' }}"
                                                                   placeholder="{{ translate('Enter_Client_Password') }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Grant_Type') }}</label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="grant_type"
                                                                   value="{{ $courier->grant_type }}"
                                                                   placeholder="{{ translate('ex: password') }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ translate('Store_ID') }}</label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="store_id"
                                                                   value="{{ env('APP_MODE') != 'demo' ? $courier->store_id : '' }}"
                                                                   placeholder="{{ translate('Enter_Store_ID') }}">
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label d-flex align-items-center gap-2">
                                                                {{ translate('Environment_Mode') }}
                                                                <span class="badge badge-soft-{{ $courier->is_live ? 'success' : 'warning' }}">
                                                                    {{ $courier->is_live ? translate('Live') : translate('Test') }}
                                                                </span>
                                                            </label>
                                                            <label class="switcher">
                                                                <input class="switcher_input" 
                                                                       type="checkbox" 
                                                                       value="1" 
                                                                       name="is_live"
                                                                       {{ $courier->is_live ? 'checked' : '' }}>
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                            <small class="form-text text-muted">
                                                                {{ translate('Toggle_between_test_and_live_environment') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-end flex-wrap gap-3 mt-4">
                                                <button type="reset" class="btn btn-secondary w-120 px-4">
                                                    {{ translate('reset') }}
                                                </button>
                                                <button type="{{ env('APP_MODE') != 'demo' ? 'submit' : 'button' }}" 
                                                        class="btn btn-primary w-120 px-4 {{ env('APP_MODE') != 'demo' ? '' : 'call-demo-alert' }}">
                                                    {{ translate('save') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        "use strict";

        // Handle courier status toggle
        $(document).on('change', '.courier-status-toggle', function() {
            const courierId = $(this).data('id');
            const isActive = $(this).is(':checked') ? 1 : 0;
            const $toggle = $(this);
            
            $.ajax({
                url: '{{ route("admin.third-party.courier-service.update-status") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: courierId,
                    is_active: isActive
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                        // Revert toggle if failed
                        $toggle.prop('checked', !isActive);
                    }
                },
                error: function(xhr) {
                    toastr.error('{{ translate("Failed_to_update_status") }}');
                    // Revert toggle on error
                    $toggle.prop('checked', !isActive);
                }
            });
        });
    </script>
@endpush

