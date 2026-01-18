@extends('layouts.admin.app')

@section('title', translate('order_Details'))

@section('content')
    @php($shippingAddress = $order['shipping_address_data'] ?? null)
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/all-orders.png') }}" alt="">
                {{translate('order_Details')}}
            </h2>
        </div>

        <div class="row gy-3" id="printableArea">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                            <div class="d-flex flex-column gap-10">
                                <h4 class="text-capitalize">
                                    {{ translate('Order_ID') }} #{{ $order['id'] }}
                                    @if($order['order_type'] == 'POS')
                                        <span>({{ 'POS' }})</span>
                                    @endif
                                </h4>
                                <div class="">
                                    {{date('d M, Y , h:i A', strtotime($order['created_at']))}}
                                </div>

                                <div class="d-flex flex-wrap gap-3">
                                    @if ($linkedOrders->count() >0)
                                        <div
                                            class="color-caribbean-green-soft fw-bold d-flex align-items-center rounded py-1 px-2"> {{translate('linked_orders')}}
                                            ({{ $linkedOrders->count()}}) :
                                        </div>
                                        @foreach($linkedOrders as $linked)
                                            <a href="{{route('admin.orders.details',[$linked['id']])}}"
                                               class=" color-caribbean-green text-white rounded py-1 px-2">{{$linked['id'] }}</a>
                                        @endforeach
                                    @endif
                                    @if($order['payment_method'] == 'cash_on_delivery' && $order['bring_change_amount'] > 0)
                                        <div class="bg-success bg-opacity-10 fs-12 px-12 py-10 text-dark rounded d-flex gap-2 align-items-center">
                                            <div>
                                                {{ translate('please_ensure_the_deliveryman_has') }}
                                                <span class="fw-semibold">
                                                    {{ $order['bring_change_amount'] }} {{ $order['bring_change_amount_currency'] ?? '' }}
                                                </span> {{ translate('in_change_ready_for_the_customer') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>
                            <div class="text-sm-end flex-grow-1">
                                <div class="d-flex flex-wrap gap-10 justify-content-start justify-content-lg-end">
                                    @if ($order->verificationImages && count($order->verificationImages)>0 && $order->verification_status ==1)
                                        <div>
                                            <button class="btn btn-primary px-4" data-bs-toggle="modal"
                                                    data-bs-target="#order_verification_modal">
                                                    <i class="fi fi-sr-shield-trust"></i> {{translate('order_verification')}}
                                            </button>
                                        </div>
                                    @endif

                                    @if (getWebConfig('map_api_status') == 1 && isset($shippingAddress->latitude) && isset($shippingAddress->longitude))
                                        <div class="">
                                            <button class="btn btn-primary px-4" data-bs-toggle="modal"
                                                    data-bs-target="#locationModal">
                                                    <i class="fi fi-rr-map"></i> {{translate('show_locations_on_map')}}
                                            </button>
                                        </div>
                                    @endif

                                    <a class="btn btn-primary px-4" target="_blank"
                                       href={{route('admin.orders.generate-invoice',[$order['id']])}}>
                                        <img
                                            src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/icons/uil_invoice.svg') }}"
                                            alt="" class="mr-1">
                                        {{translate('print_Invoice')}}
                                    </a>
                                </div>
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="text-dark">{{translate('status')}}: </span>
                                        @if($order['order_status']=='pending')
                                            <span
                                                class="badge color-caribbean-green-soft fw-bold rounded-50 d-flex align-items-center py-1 px-2">{{translate(str_replace('_',' ',$order['order_status']))}}</span>
                                        @elseif($order['order_status']=='failed')
                                            <span
                                                class="badge badge-danger text-bg-danger fw-bold rounded-50 d-flex align-items-center py-1 px-2">{{translate(str_replace('_',' ',$order['order_status'] == 'failed' ? 'Failed to Deliver' : ''))}}
                                            </span>
                                        @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                                            <span
                                                class="badge badge-warning text-bg-warning fw-bold rounded-50 d-flex align-items-center py-1 px-2">
                                                {{translate(str_replace('_',' ',$order['order_status'] == 'processing' ? 'Packaging' : $order['order_status']))}}
                                            </span>
                                        @elseif($order['order_status']=='delivered' || $order['order_status']=='confirmed')
                                            <span
                                                class="badge badge-success text-bg-success fw-bold rounded-50 d-flex align-items-center py-1 px-2">
                                                {{translate(str_replace('_',' ',$order['order_status']))}}
                                            </span>
                                        @else
                                            <span
                                                class="badge badge-danger text-bg-danger fw-bold rounded-50 d-flex align-items-center py-1 px-2">
                                                {{translate(str_replace('_',' ',$order['order_status']))}}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="payment-method d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="text-dark">{{translate('payment_Method')}} :</span>
                                        <strong>{{ translate($order['payment_method']) }}</strong>
                                    </div>

                                    @if($order->payment_method != 'cash_on_delivery' && $order->payment_method != 'pay_by_wallet' && !isset($order->offlinePayments))
                                        <div
                                            class="reference-code d-flex justify-content-sm-end gap-10 text-capitalize">
                                            <span class="text-dark">{{translate('reference_Code')}} :</span>
                                            <strong>{{str_replace('_',' ',$order['transaction_ref'])}} {{ $order->payment_method == 'offline_payment' ? '('.$order->payment_by.')':'' }}</strong>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-sm-end gap-10">
                                        <span class="text-dark">{{translate('payment_Status')}}:</span>
                                        @if($order['payment_status']=='paid')
                                            <span class="text-success fw-bold">
                                                {{translate('paid')}}
                                            </span>
                                        @else
                                            <span class="text-danger fw-bold">
                                                {{translate('unpaid')}}
                                            </span>
                                        @endif
                                    </div>

                                    @if(getWebConfig('order_verification'))
                                        <span class="d-flex justify-content-sm-end gap-10">
                                            <b>
                                                {{translate('order_verification_code')}} : {{$order['verification_code'] }}
                                            </b>
                                        </span>
                                    @endif

                                </div>
                            </div>
                        </div>
                        @if ($order->order_note !=null)
                            <div class="mt-2 mb-5 w-100 d-block">
                                <div class="gap-10">
                                    <h5>{{ translate('order_Note') }}:</h5>
                                    <div class="text-justify">{{ $order->order_note }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table
                                class="table fs-12 table-hover table-borderless align-middl">
                                <thead class="text-capitalize">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('item_details')}}</th>
                                    <th>{{translate('item_price')}}</th>
                                    <th>{{translate('item_discount')}}</th>
                                    <th>{{translate('total_price')}}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @php($item_price=0)
                                @php($total_price=0)
                                @php($subtotal=0)
                                @php($total=0)
                                @php($discount=0)
                                @php($row=0)
                                @foreach($order->details as $key=>$detail)
                                    @php($productDetails = $detail?->productAllStatus ?? json_decode($detail->product_details))
                                    @if($productDetails)
                                        <tr>
                                            <td>{{ ++$row }}</td>
                                            <td>
                                                <div class="media align-items-center gap-10">
                                                    <img class="avatar avatar-60 rounded img-fit"
                                                         src="{{ getStorageImages(path:$detail?->productAllStatus?->thumbnail_full_url, type: 'backend-product') }}"
                                                         alt="{{translate('image_Description')}}">
                                                    <div>
                                                        <h5 class="text-dark">{{substr($productDetails->name, 0, 30)}}{{strlen($productDetails->name)>10?'...':'' }}</h5>
                                                        <div><strong>{{translate('qty')}} :</strong> {{$detail['qty']}}
                                                        </div>
                                                        <div>
                                                            <strong>{{translate('unit_price')}} :</strong>
                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $detail['price'])) }}
                                                        </div>
                                                        @if ($detail->variant)
                                                            <div class="max-w-150px text-wrap">
                                                                <strong>
                                                                    {{translate('variation')}} :
                                                                </strong>
                                                                {{$detail['variant']}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if(isset($productDetails->digital_product_type) && $productDetails->digital_product_type == 'ready_after_sell')
                                                    <button type="button" class="btn btn-sm btn-primary mt-2"
                                                            title="{{translate('file_upload')}}" data-bs-toggle="modal"
                                                            data-bs-target="#fileUploadModal-{{ $detail->id }}">
                                                            <i class="fi fi-rr-file"></i> {{translate('file')}}
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $detail['price']*$detail['qty']), currencyCode: getCurrencyCode()) }}
                                            </td>
                                            <td>{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $detail['discount']), currencyCode: getCurrencyCode())}}</td>
                                            @php($subtotal=($detail['price']*$detail['qty'])-$detail['discount'])
                                            <td>{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $subtotal), currencyCode: getCurrencyCode())}}</td>
                                        </tr>
                                        @php($item_price+=$detail['price']*$detail['qty'])
                                        @php($discount+=$detail['discount'])
                                        @php($total+=$subtotal)
                                    @endif
                                @endforeach
                                </tbody>
                            </table>


                            @foreach($order->details as $key=>$detail)
                                @php($productDetails = $detail?->productAllStatus ?? json_decode($detail->product_details))
                                @if(isset($productDetails->digital_product_type) && $productDetails->digital_product_type == 'ready_after_sell')
                                    @php($product_details = json_decode($detail->product_details))
                                    <div class="modal fade" id="fileUploadModal-{{ $detail->id }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form
                                                    action="{{ route('admin.orders.digital-file-upload-after-sell') }}"
                                                    method="post" enctype="multipart/form-data" class="form-advance-validation form-advance-inputs-validation form-advance-file-validation non-ajax-form-validate" novalidate="novalidate">
                                                    @csrf
                                                    <div class="modal-body">
                                                        @if($detail?->digital_file_after_sell_full_url && isset($detail->digital_file_after_sell_full_url['key']))
                                                            <div class="mb-4">
                                                                {{translate('uploaded_file').' : '}}
                                                                @php($downloadPathExist = $detail->digital_file_after_sell_full_url['status'])
                                                                <span data-file-path="{{ $downloadPathExist ? $detail->digital_file_after_sell_full_url['path'] : 'javascript:' }}" class="getDownloadFileUsingFileUrl btn btn-success btn-sm {{ $downloadPathExist ?  '' : 'download-path-not-found' }}" title="{{translate('download')}}">
                                                                    {{translate('download')}} <i class="fi fi-sr-down-to-line"></i>
                                                                </span>
                                                            </div>
                                                        @elseif($detail->digital_file_after_sell)
                                                            <div class="mb-4">
                                                                {{translate('uploaded_file').' : '}}
                                                                @php($downloadPath =dynamicStorage(path: 'storage/app/public/product/digital-product/'.$detail->digital_file_after_sell))
                                                                <span data-file-path="{{file_exists( $downloadPath) ?  $downloadPath : 'javascript:' }}" class="getDownloadFileUsingFileUrl btn btn-success btn-sm {{file_exists( $downloadPath) ?  $downloadPath : 'download-path-not-found'}}" title="{{translate('download')}}">
                                                                    {{translate('download')}} <i class="fi fi-sr-down-to-line"></i>
                                                                </span>
                                                            </div>
                                                        @else
                                                            <h4 class="text-center">{{translate('file_not_found').'!'}}</h4>
                                                        @endif
                                                        @if(($product_details->added_by == 'admin') && $detail->seller_id == 1)
                                                            <div class="inputDnD">
                                                                <div
                                                                    class="form-group inputDnD input_image input_image_edit"
                                                                    data-title="{{translate('drag_&_drop_file_or_browse_file')}}">
                                                                    <input type="file" data-max-size="{{ getFileUploadMaxSize(type: 'file') }}"
                                                                            name="digital_file_after_sell"
                                                                            class="form-control-file text-primary fw-bold image-input"
                                                                            accept=".jpg, .jpeg, .png, .gif, .zip, .pdf">
                                                                </div>
                                                            </div>
                                                            <div class="mt-1 text-info">
                                                                {{translate('file_type').' '.':'.' '.'jpg, jpeg, png, gif, zip, pdf'}}
                                                            </div>
                                                            <input type="hidden" value="{{ $detail->id }}"
                                                                    name="order_id">
                                                        @else
                                                            <h4 class="mt-3 text-center">{{translate('admin_have_no_permission_for_vendors_digital_product_upload')}}</h4>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{translate('close')}}</button>
                                                        @if(($product_details->added_by == 'admin') && $detail->seller_id == 1)
                                                            <button type="submit"
                                                                    class="btn btn-primary">{{translate('upload')}}</button>
                                                        @endif
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <hr/>
                        @php($orderTotalPriceSummary = \App\Utils\OrderManager::getOrderTotalPriceSummary(order: $order))
                        <div class="px-sm-5 overflow-x-auto">
                            <table class="table table-borderless table-sm mb-0 text-sm-right text-nowrap">
                                <tbody>
                                    <tr>
                                        <td class="text-end text-body text-capitalize"><strong>{{ translate('item_price') }}</strong></td>
                                        <td class="text-end text-dark">
                                            <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['itemPrice']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-body text-capitalize"><strong>{{ translate('item_discount') }}</strong></td>
                                        <td class="text-end text-dark">
                                            -
                                            <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['itemDiscount']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-body text-capitalize"><strong>{{ translate('sub_total') }}</strong></td>
                                        <td class="text-end text-dark">
                                            <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['subTotal']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-body">
                                            <strong>{{ translate('coupon_Discount') }}</strong>
                                            <br>
                                            {{(!in_array($order['coupon_code'], [0, NULL]) ? '('.translate('expense_bearer_').($order['coupon_discount_bearer']=='inhouse' ? 'admin' : ($order['coupon_discount_bearer'] == 'seller' ? 'vendor' : $order['coupon_discount_bearer'])).')': '' )}}
                                        </td>
                                        <td class="text-end text-dark">
                                            <strong>-
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['couponDiscount']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>
                                    @if($orderTotalPriceSummary['referAndEarnDiscount'] > 0)
                                    <tr>
                                        <td class="text-end text-body"><strong>{{ translate('referral_discount') }}</strong></td>
                                        <td class="text-end text-dark">
                                            <strong>-
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['referAndEarnDiscount']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <td class="text-end text-body text-uppercase"><strong>{{ translate('vat') }}/{{ translate('tax') }}</strong></td>
                                        <td class="text-end text-dark">
                                            <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['totalTaxAmount']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="text-end text-body text-capitalize">
                                            <strong>{{ translate('shipping_fee') }}</strong>
                                            @if($order['is_shipping_free'])
                                                <br>
                                                ({{ translate('expense_bearer_').($order['free_delivery_bearer'] == 'seller' ? 'vendor' : $order['free_delivery_bearer']) }})
                                            @endif
                                        </td>
                                        <td class="text-end text-dark">
                                            <strong>
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['shippingTotal'])) }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-body">
                                            <strong>{{ translate('total') }}</strong>
                                            <span class="fs-10 fw-medium">{{ $orderTotalPriceSummary['tax_model'] == 'include' ? '('.translate('Tax_:_Inc.').')' : '' }}</span>
                                        </td>
                                        <td class="text-end text-dark">
                                            <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['totalAmount']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>
                                    @if ($order->order_type == 'pos' || $order->order_type == 'POS')
                                    <tr>
                                        <td class="text-end text-body"><strong>{{ translate('paid_amount') }}</strong></td>
                                        <td class="text-end text-dark">
                                            <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['paidAmount']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-body"><strong>{{ translate('change_amount') }}</strong></td>
                                        <td class="text-end text-dark">
                                            <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['changeAmount']), currencyCode: getCurrencyCode()) }}</strong>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
                @if($order->payment_method == 'offline_payment' && isset($order->offlinePayments))
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                                <h3 class="d-flex gap-2">
                                    <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/product_setup.png')}}"
                                         alt=""
                                         width="20">
                                    {{translate('Payment_Information')}}
                                </h3>
                            </div>
                            <div>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td>{{translate('payment_Method')}}</td>
                                        <td class="py-1 px-2">:</td>
                                        <td><strong>{{ translate($order['payment_method']) }}</strong></td>
                                    </tr>
                                    @foreach ($order->offlinePayments->payment_info as $key=>$item)
                                        @if (isset($item) && $key != 'method_id')
                                            <tr>
                                                <td>{{translate($key)}}</td>
                                                <td class="py-1 px-2">:</td>
                                                <td><strong>{{ $item }}</strong></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if(isset($order->payment_note) && $order->payment_method == 'offline_payment')
                                <div class="mt-3">
                                    <h4>{{translate('payment_Note')}}:</h4>
                                    <p class="text-justify">
                                        {{ $order->payment_note }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex flex-column align-items-center gap-2">
                            <h2 class="mb-0 text-center">{{translate('order_&_Shipping_Info')}}</h2>
                        </div>
                        <div class="">
                            <label
                                class="form-label fw-semibold">{{translate('change_order_status')}}</label>
                            <div class="select-wrapper">
                                <select name="order_status" id="order_status"
                                    class="status form-select" data-id="{{$order['id']}}">

                                <option
                                    value="pending" {{$order->order_status == 'pending'?'selected':''}} > {{translate('pending')}}</option>
                                <option
                                    value="confirmed" {{$order->order_status == 'confirmed'?'selected':''}} > {{translate('confirmed')}}</option>
                                <option
                                    value="processing" {{$order->order_status == 'processing'?'selected':''}} >{{translate('packaging')}} </option>
                                <option class="text-capitalize"
                                        value="out_for_delivery" {{$order->order_status == 'out_for_delivery'?'selected':''}} >{{translate('out_for_delivery')}} </option>
                                <option
                                    value="delivered" {{$order->order_status == 'delivered'?'selected':''}} >{{translate('delivered')}} </option>
                                <option
                                    value="returned" {{$order->order_status == 'returned'?'selected':''}} > {{translate('returned')}}</option>
                                <option
                                    value="failed" {{$order->order_status == 'failed'?'selected':''}} >{{translate('failed_to_Deliver')}} </option>
                                <option
                                    value="canceled" {{$order->order_status == 'canceled'?'selected':''}} >{{translate('canceled')}} </option>
                            </select>
                            </div>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center gap-10 form-control h-auto flex-wrap">
                            <span class="text-dark">
                                {{translate('payment_status')}}
                            </span>
                            <div class="d-flex justify-content-end min-w-100 align-items-center gap-2">
                                <span
                                    class="text-primary fw-bold">{{ $order->payment_status=='paid' ? translate('paid'):translate('unpaid')}}</span>
                                <label
                                    class="switcher payment-status-text">
                                    <input class="switcher_input payment-status" type="checkbox" name="status"
                                           data-id="{{$order->id}}"
                                           value="{{$order->payment_status}}"
                                        {{ $order->payment_status == 'paid' ? 'checked':''}} >
                                    <span class="switcher_control switcher_control_add
                                        {{ $order->payment_status=='paid' ? 'checked':'unchecked'}}"></span>
                                </label>
                            </div>
                        </div>
                        @if($physicalProduct)

                            <ul class="list-unstyled list-unstyled-py-4">
                                <li class="form-group">
                                    <label class="form-label fw-semibold">
                                        {{translate('shipping_type')}}
                                    </label>
                                    @if ($order->shipping_type == 'order_wise')
                                        <label class="form-label fw-semibold">
                                            {{translate('shipping_Method')}}
                                            ({{$order->shipping ? translate(str_replace('_',' ',$order->shipping->title)) :translate('no_shipping_method_selected')}}
                                            )
                                        </label>
                                    @endif
                                   <div class="select-wrapper">
                                        <select class="form-select" name="delivery_type"
                                        id="choose_delivery_type" {{ $order->order_status == 'delivered' || $order->third_party_delivery_tracking_id ? 'disabled' : '' }}>
                                            <option value="0">
                                                {{translate('choose_delivery_type')}}
                                            </option>
                                            <option
                                                value="self_delivery" {{$order->delivery_type=='self_delivery'?'selected':''}}>
                                                {{translate('by_self_delivery_man')}}
                                            </option>
                                            <option
                                                value="third_party_delivery" {{$order->delivery_type=='third_party_delivery'?'selected':''}} >
                                                {{translate('by_third_party_delivery_service')}}
                                            </option>
                                        </select>
                                   </div>
                                </li>

                                <li class="choose_delivery_man form-group">
                                    <label class="form-label fw-semibold">
                                        {{translate('delivery_man')}}
                                    </label>
                                    <select class="custom-select"
                                        name="delivery_man_id"
                                        id="addDeliveryMan"
                                        data-order-id="{{$order['id']}}"
                                        data-placeholder="Select from dropdown"
                                        {{ $order->order_status == 'delivered' || $order->third_party_delivery_tracking_id ? 'disabled' : '' }}
                                        >
                                        <option></option>
                                        <option
                                            value="0" {{ isset($order->deliveryMan) ? 'disabled':''}}>{{translate('select')}}</option>
                                        @foreach($deliveryMen as $deliveryMan)
                                            <option
                                                value="{{$deliveryMan['id']}}" {{$order['delivery_man_id']==$deliveryMan['id']?'selected':''}}>
                                                {{$deliveryMan['f_name'].' '.$deliveryMan['l_name'].' ('.$deliveryMan['phone'].' )'}}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if (isset($order->deliveryMan))
                                        <div class="p-2 bg-light rounded mt-4">
                                            <div class="media m-1 gap-3">
                                                <img class="avatar rounded-circle"
                                                     src="{{ getStorageImages(path: $order->deliveryMan?->image_full_url, type: 'backend-profile') }}"
                                                     alt="{{translate('Image')}}">
                                                <div class="media-body">
                                                    <h5 class="mb-1">{{ $order->deliveryMan?->f_name.' '.$order->deliveryMan?->l_name}}</h5>
                                                    <a href="tel:{{$order->deliveryMan?->phone}}"
                                                       class="fs-12 text-dark">{{$order->deliveryMan?->phone}}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-2 bg-light rounded mt-4">
                                            <div class="media m-1 gap-3">
                                                <img class="avatar rounded-circle"
                                                     src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/delivery-man.png')}}"
                                                     alt="{{translate('Image')}}">
                                                <div class="media-body">
                                                    <h5 class="mt-3">{{translate('no_delivery_man_assigned')}}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                                @if (isset($order->deliveryMan))
                                    <li class="choose_delivery_man form-group">
                                        <label class="form-label fw-semibold">
                                            {{translate('delivery_man_incentive')}} ({{ getCurrencySymbol() }})
                                            <span class="tooltip-icon cursor-pointer"
                                                data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  aria-label="{{translate('encourage_your_deliveryman_by_giving_him_incentive').' '.translate('this_amount_will_be_count_as_admin_expense').'.'}}"
                                                  data-bs-title="{{translate('encourage_your_deliveryman_by_giving_him_incentive').' '.translate('this_amount_will_be_count_as_admin_expense').'.'}}">
                                                  <i class="fi fi-sr-info"></i>
                                            </span>
                                        </label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number"
                                                   value="{{ usdToDefaultCurrency(amount: $order->deliveryman_charge) }}"
                                                   name="deliveryman_charge" data-order-id="{{$order['id']}}"
                                                   class="form-control" placeholder="{{translate('ex').': 20'}}"
                                                   {{$order['order_status']=='delivered' ? 'readonly':''}} required>
                                            <button
                                                class="btn btn-primary h-40 {{$order['order_status']=='delivered' ? 'disabled deliveryman-charge-alert':'deliveryman-charge'}}">{{translate('update')}}</button>
                                        </div>
                                    </li>
                                    <li class="choose_delivery_man form-group">
                                        <label
                                            class="form-label fw-semibold">{{translate('expected_delivery_date')}}</label>
                                        <input type="date" data-order-id="{{$order['id']}}"
                                               value="{{ $order->expected_delivery_date }}"
                                               name="expected_delivery_date" id="expected_delivery_date"
                                               class="form-control"  {{ $order->order_status == 'delivered' ? 'disabled' : 'required' }}>
                                    </li>
                                @endif

                                <li class="mt-1  form-group" id="by_third_party_delivery_service_info">
                                    <div class="p-2 bg-light rounded" style="cursor: pointer;"
                                        onclick="openCourierInfoModal('{{$order->delivery_service_name}}', '{{$order->third_party_delivery_tracking_id}}')">
                                        <div class="media overflow-hidden m-1 gap-3">
                                            <img class="avatar rounded-circle"
                                                 src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/third-party-delivery.png')}}"
                                                 alt="{{translate('image')}}">
                                            <div class="media-body w-100">
                                                <h5 class="">{{$order->delivery_service_name ?? translate('not_assign_yet')}}</h5>
                                                <span
                                                    class="fs-12 text-dark text-wrap d-block">{{translate('track_ID').' '.':'.' '.$order->third_party_delivery_tracking_id}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
                @if(!$order->is_guest && $order->customer)
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                                <h3 class="d-flex gap-2 fw-semibold">
                                    <img
                                        src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/vendor-information.png')}}"
                                        alt="">
                                    {{translate('customer_information')}}
                                </h3>
                            </div>
                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img class="avatar rounded-circle avatar-70"
                                         src="{{ getStorageImages(path: $order->customer->image_full_url , type: 'backend-basic') }}"
                                         alt="{{translate('Image')}}">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span
                                        class="text-dark"><span class="fw-semibold">{{$order->customer['f_name'].' '.$order->customer['l_name']}} </span></span>
                                    <span class="text-dark"> <span class="fw-semibold">{{ $orderCount }}</span> {{translate('orders')}}</span>
                                    <span
                                        class="text-dark break-all"><span class="fw-semibold">{{$order->customer['phone']}}</span></span>
                                    <span class="text-dark break-all">{{$order->customer['email']}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @php($billing=$order['billing_address_data'])
                @if($physicalProduct || !$billing)
                    <div class="card">
                        @if($shippingAddress)
                            <div class="card-body">
                                <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                                    <h3 class="d-flex gap-2 fw-semibold">
                                        <img
                                            src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/vendor-information.png')}}"
                                            alt="">
                                        {{translate('shipping_address')}}
                                    </h3>
                                    @if($order['order_status'] != 'delivered')
                                        <button class="btn btn-outline-primary icon-btn" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#shippingAddressUpdateModal">
                                            <i class="fi fi-sr-pencil"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <span>{{translate('name')}} :</span>
                                        <span class="fw-semibold">{{$shippingAddress->contact_person_name}}</span> {{ $order->is_guest ? '('. translate('guest_customer') .')':''}}
                                    </div>
                                    <div>
                                        <span>{{translate('contact')}} :</span>
                                        <span class="fw-semibold">{{$shippingAddress->phone}}</span>
                                    </div>
                                    @if ($order->is_guest && $shippingAddress->email)
                                        <div>
                                            <span>{{translate('email')}} :</span>
                                            <span class="fw-semibold">{{$shippingAddress->email}}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <span>{{translate('country')}} :</span>
                                        <span class="fw-semibold">{{$shippingAddress->country}}</span>
                                    </div>
                                    <div>
                                        <span>{{translate('city')}} :</span>
                                        <span class="fw-semibold">{{$shippingAddress->city}}</span>
                                    </div>
                                    <div>
                                        <span>{{translate('zip_code')}} :</span>
                                        <span class="fw-semibold">{{$shippingAddress->zip}}</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2">
                                        <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/location.png')}}"
                                             alt="">
                                        {{$shippingAddress->address  ?? translate('empty')}}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card-body">
                                <div class="media align-items-center">
                                    <span>{{translate('no_shipping_address_found')}}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                @if($billing)
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                            <h3 class="d-flex gap-2 fw-semibold">
                                <img
                                    src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/vendor-information.png')}}"
                                    alt="">
                                {{translate('billing_address')}}
                            </h3>
                            @if($order['order_status'] != 'delivered')
                                <button
                                    class="btn btn-outline-primary icon-btn billing-address-update-modal"
                                    title="{{translate('edit')}}"
                                    data-bs-toggle="modal" data-bs-target="#billingAddressUpdateModal">
                                    <i class="fi fi-sr-pencil"></i>
                                </button>
                            @endif
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <span>{{translate('name')}} :</span>
                                <span class="fw-semibold">{{$billing->contact_person_name}}</span> {{ $order->is_guest ? '('. translate('guest_customer') .')':''}}
                            </div>
                            <div>
                                <span>{{translate('contact')}} :</span>
                                <span class="fw-semibold">{{$billing->phone}}</span>
                            </div>
                            @if ($order->is_guest && $billing->email)
                                <div>
                                    <span>{{translate('email')}} :</span>
                                    <span class="fw-semibold">{{$billing->email}}</span>
                                </div>
                            @endif
                            <div>
                                <span>{{translate('country')}} :</span>
                                <span class="fw-semibold">{{$billing->country}}</span>
                            </div>
                            <div>
                                <span>{{translate('city')}} :</span>
                                <span class="fw-semibold">{{$billing->city}}</span>
                            </div>
                            <div>
                                <span>{{translate('zip_code')}} :</span>
                                <span class="fw-semibold">{{$billing->zip}}</span>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/location.png')}}" alt="">
                                {{$billing->address}}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        <h3 class="d-flex gap-2 mb-4">
                            <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/shop-information.png') }}" alt="">
                            {{ translate('shop_Information') }}
                        </h3>
                        <div class="media d-flex gap-3 align-items-center">
                            @if($order->seller_is == 'admin')
                                <div class="me-3">
                                    <img class="avatar rounded avatar-70 img-fit-contain "
                                         src="{{ getStorageImages(path: getInHouseShopConfig(key: 'image_full_url'), type: 'shop') }}"
                                         alt="">
                                </div>
                                <div class="media-body d-flex flex-column gap-2">
                                    <h5>{{ getInHouseShopConfig(key: 'name') }}</h5>
                                    <span class="text-dark"><strong>{{ $totalDelivered }}</strong> {{translate('orders_Served')}}</span>
                                </div>
                            @else
                                @if(!empty($order->seller->shop))
                                    <div class="me-3">
                                        <img class="avatar rounded avatar-70 img-fit"
                                             src="{{ getStorageImages(path:$order->seller->shop->image_full_url , type: 'backend-basic') }}"
                                             alt="">
                                    </div>
                                    <div class="media-body d-flex flex-column gap-2">
                                        <h5>{{ $order->seller->shop->name }}</h5>
                                        <span class="text-dark"><strong>{{ $totalDelivered }}</strong> {{translate('orders_Served')}}</span>
                                        <span class="text-dark"> <strong>{{ $order->seller->shop->contact }}</strong></span>
                                        <div class="d-flex align-items-start gap-2">
                                            <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/location.png')}}"
                                                 class="mt-1" alt="">
                                            {{ $order->seller->shop->address }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center p-4">
                                        <img class="w-25" src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/empty-state-icon/shop-not-found.png')}}"
                                             alt="{{translate('image_description')}}">
                                        <p class="mb-0">{{ translate('no_shop_found').'!'}}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($order->verificationImages && count($order->verificationImages)>0)
        <div class="modal fade" id="order_verification_modal" tabindex="-1" aria-labelledby="order_verification_modal"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center pb-4">
                        <h3 class="mb-0">{{translate('order_verification_images')}}</h3>
                        <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                            data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body px-4 px-sm-5 pt-0">
                        <div class="d-flex flex-column align-items-center gap-2">
                            <div class="row gx-2">
                                @foreach ($order->verificationImages as $image)
                                    <div class="col-lg-4 col-sm-6 ">
                                        <div class="mb-2 mt-2 border-1">
                                            <img
                                                src="{{ getStorageImages(path: $image->image_full_url , type: 'backend-basic') }}"
                                                class="w-100" alt="">
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-3">
                                        <button type="button" class="btn btn-secondary px-5"
                                                data-bs-dismiss="modal">{{translate('close')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="shippingAddressUpdateModal" tabindex="-1" aria-labelledby="shippingAddressUpdateModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-4 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{translate('shipping_address')}}</h3>
                    <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 px-sm-5 pt-0">
                    <form action="{{route('admin.orders.address-update')}}" method="post">
                        @csrf
                        <div class="d-flex flex-column align-items-center gap-2">
                            <input name="address_type" value="shipping" hidden>
                            <input name="order_id" value="{{$order->id}}" hidden>
                            <div class="row gx-3 gy-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name"
                                               class="form-label">{{translate('contact_person_name')}}</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                               value="{{$shippingAddress? $shippingAddress->contact_person_name : ''}}"
                                               placeholder="{{ translate('ex') }}: {{translate('john_doe')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone_number"
                                               class="form-label">{{translate('phone_number')}}</label>
                                        <input class="form-control form-control-user"
                                               type="tel" value="{{$shippingAddress ? $shippingAddress->phone  : ''}}"
                                               placeholder="{{ translate('ex').': 017xxxxxxxx' }}" name="phone_number" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country" class="form-label">{{translate('country')}}</label>
                                        <select name="country" id="country" class="form-control">
                                            @forelse($countries as $country)
                                                <option
                                                    value="{{ $country['name'] }}" {{ isset($shippingAddress) && $country['name'] == $shippingAddress->country ? 'selected'  : ''}}>{{ $country['name'] }}</option>
                                            @empty
                                                <option value="">{{ translate('No_country_to_deliver') }}</option>
                                            @endforelse
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="city" class="form-label">{{translate('city')}}</label>
                                        <input type="text" name="city" id="city"
                                               value="{{$shippingAddress ? $shippingAddress->city : ''}}"
                                               class="form-control"
                                               placeholder="{{ translate('ex') }}:{{translate('dhaka')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="zip_code" class="form-label">{{translate('zip')}}</label>
                                        @if($zipRestrictStatus == 1)
                                            <select name="zip" class="form-control" data-live-search="true" required>
                                                @forelse($zipCodes as $code)
                                                    <option
                                                        value="{{ $code->zipcode }}"{{ isset($shippingAddress) && $code->zipcode == $shippingAddress->zip ? 'selected'  : ''}}>{{ $code->zipcode }}</option>
                                                @empty
                                                    <option value="">{{ translate('No_zip_to_deliver') }}</option>
                                                @endforelse
                                            </select>
                                        @else
                                            <input type="text" class="form-control"
                                                   value="{{$shippingAddress ? $shippingAddress->zip  : ''}}" id="zip"
                                                   name="zip"
                                                   placeholder="{{ translate('ex') }}: 1216" {{$shippingAddress?'required':''}}>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="address" class="form-label">{{translate('address')}}</label>
                                        <textarea name="address" id="address" name="address" rows="3"
                                                  class="form-control"
                                                  placeholder="{{ translate('ex') }} : {{translate('street_1,_street_2,_street_3,_street_4')}}">{{$shippingAddress ? $shippingAddress->address : ''}}</textarea>
                                    </div>
                                </div>
                                <input type="hidden" id="latitude"
                                       name="latitude" class="form-control d-inline"
                                       placeholder="{{ translate('Ex') }} : -94.22213"
                                       value="{{$shippingAddress->latitude ?? 0}}" required readonly>
                                <input type="hidden"
                                       name="longitude" class="form-control"
                                       placeholder="{{ translate('Ex') }} : 103.344322" id="longitude"
                                       value="{{$shippingAddress->longitude ??0}}" required readonly>
                                @if(getWebConfig('map_api_status') ==1 )
                                    <div class="col-12 ">
                                        <input id="pac-input" class="form-control rounded w-200 mt-1"
                                               title="{{translate('search_your_location_here')}}" type="text"
                                               placeholder="{{translate('search_here')}}"/>
                                        <div class="dark-support rounded w-100 h-200 mb-2" id="location_map_canvas_shipping"></div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-3">
                                        <button type="button" class="btn btn-secondary px-5"
                                                data-bs-dismiss="modal">{{translate('cancel')}}</button>
                                        <button type="submit"
                                                class="btn btn-primary px-5">{{translate('update')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if($billing)
        <div class="modal fade" id="billingAddressUpdateModal" tabindex="-1" aria-labelledby="billingAddressUpdateModal"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-4 d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{translate('billing_address')}}</h3>
                        <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 px-sm-5 pt-0">
                        <div class="d-flex flex-column align-items-center gap-2">
                            <form action="{{route('admin.orders.address-update')}}" method="post">
                                @csrf
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <input name="address_type" value="billing" hidden>
                                    <input name="order_id" value="{{$order->id}}" hidden>
                                    <div class="row gx-3 gy-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name"
                                                       class="form-label">{{translate('contact_person_name')}}</label>
                                                <input type="text" name="name" id="name" class="form-control"
                                                       value="{{$billing? $billing->contact_person_name : ''}}"
                                                       placeholder="{{ translate('ex') }}: {{translate('john_doe')}}"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone_number"
                                                       class="form-label">{{translate('phone_number')}}</label>
                                                <input class="form-control form-control-user"
                                                       type="tel" value="{{$billing ? $billing->phone  : ''}}"
                                                       placeholder="{{ translate('ex').': 017xxxxxxxx' }}" name="phone_number" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="country"
                                                       class="form-label">{{translate('country')}}</label>
                                                <select name="country" id="country" class="form-control">
                                                    @forelse($countries as $country)
                                                        <option
                                                            value="{{ $country['name'] }}" {{ isset($billing) && $country['name'] == $billing->country ? 'selected'  : ''}}>{{ $country['name'] }}</option>
                                                    @empty
                                                        <option
                                                            value="">{{ translate('No_country_to_deliver') }}</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="city" class="form-label">{{translate('city')}}</label>
                                                <input type="text" name="city" id="city"
                                                       value="{{$billing ? $billing->city : ''}}" class="form-control"
                                                       placeholder="{{ translate('ex') }}:{{translate('dhaka')}}"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="zip_code" class="form-label">{{translate('zip')}}</label>
                                                @if($zipRestrictStatus == 1)
                                                    <select name="zip" class="form-control" data-live-search="true"
                                                            required>
                                                        @forelse($zipCodes as $code)
                                                            <option
                                                                value="{{ $code->zipcode }}"{{ isset($billing) && $code->zipcode == $billing->zip ? 'selected'  : ''}}>{{ $code->zipcode }}</option>
                                                        @empty
                                                            <option
                                                                value="">{{ translate('no_zip_to_deliver') }}</option>
                                                        @endforelse
                                                    </select>
                                                @else
                                                    <input type="text" class="form-control"
                                                           value="{{$billing ? $billing->zip  : ''}}" id="zip"
                                                           name="zip"
                                                           placeholder="{{ translate('ex').': 1216' }}" {{$billing?'required':''}}>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="address"
                                                       class="form-label">{{translate('address')}}</label>
                                                <textarea name="address" id="billing_address" rows="3"
                                                          class="form-control"
                                                          placeholder="{{ translate('ex') }} : {{translate('street_1,_street_2,_street_3,_street_4')}}">{{$billing ? $billing->address : ''}}</textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" id="billing_latitude"
                                               name="latitude" class="form-control d-inline"
                                               placeholder="{{ translate('ex') }} : -94.22213"
                                               value="{{$billing->latitude ?? 0}}" required readonly>
                                        <input type="hidden"
                                               name="longitude" class="form-control"
                                               placeholder="{{ translate('ex') }} : 103.344322" id="billing_longitude"
                                               value="{{$billing->longitude ?? 0}}" required readonly>
                                        @if(getWebConfig('map_api_status') ==1 )
                                            <div class="col-12 ">
                                                <input id="billing-pac-input" class="form-control rounded w-200 mt-1"
                                                       title="{{translate('search_your_location_here')}}" type="text"
                                                       placeholder="{{translate('search_here')}}"/>
                                                <div class="rounded w-100 h-200 mb-2" id="location_map_canvas_billing"></div>
                                            </div>
                                        @endif
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-3">
                                                <button type="button" class="btn btn-secondary px-5"
                                                        data-bs-dismiss="modal">{{translate('cancel')}}</button>
                                                <button type="submit"
                                                        class="btn btn-primary px-5">{{translate('update')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                    <h3 class="modal-title text-center flex-grow-1" id="locationModalLabel">{{translate('location_on_Map')}}</h3>
                    <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                        data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="row">
                            <div class="col-md-12 rounded border p-3">
                                <div class="h3 text-cyan-blue text-center">{{ translate('order') }} #{{ $order->id }}</div>
                                <ul class="nav nav-tabs border-0 media-tabs nav-justified order-track-info">
                                    <li class="nav-item">
                                        <div class="nav-link active-status">
                                            <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                <div class="media-tab-media mx-sm-auto mb-3">
                                                    <img
                                                        src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/order-placed.png') }}"
                                                        alt="">
                                                </div>
                                                <div class="media-body">
                                                    <div class="text-sm-center text-start">
                                                        <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">{{ translate('order_placed') }}</h6>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                <span
                                                    class="text-muted fs-12">{{date('h:i A, d M Y',strtotime($order->created_at))}}</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </li>


                                    @if ($order['order_status']!='returned' && $order['order_status']!='failed' && $order['order_status']!='canceled')
                                        @if(!$isOrderOnlyDigital)
                                            <li class="nav-item ">
                                                <div
                                                    class="nav-link {{ ($order['order_status']=='confirmed') || ($order['order_status']=='processing') || ($order['order_status']=='processed') || ($order['order_status']=='out_for_delivery') || ($order['order_status']=='delivered')?'active-status' : ''}}">
                                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                        <div class="media-tab-media mb-3 mx-sm-auto">
                                                            <img
                                                                src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/order-confirmed.png') }}"
                                                                alt="">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="text-sm-center text-start">
                                                                <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">{{ translate('order_confirmed') }}</h6>
                                                            </div>
                                                            @if(($order['order_status']=='confirmed') || ($order['order_status']=='processing') || ($order['order_status']=='processed') || ($order['order_status']=='out_for_delivery') || ($order['order_status']=='delivered') && \App\Utils\order_status_history($order['id'],'confirmed'))
                                                                <div class="d-flex align-items-center justify-content-sm-center mt-2 gap-1">
                                                            <span class="text-muted fs-12">
                                                                {{date('h:i A, d M Y',strtotime(\App\Utils\order_status_history($order['id'],'confirmed')))}}
                                                            </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="nav-item">
                                                <div
                                                    class="nav-link {{ ($order['order_status']=='processing') || ($order['order_status']=='processed') || ($order['order_status']=='out_for_delivery') || ($order['order_status']=='delivered')?'active-status' : ''}}">
                                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                        <div class="media-tab-media mb-3 mx-sm-auto">
                                                            <img alt=""
                                                                 src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/shipment.png') }}">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="text-sm-center text-start">
                                                                <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">
                                                                    {{ translate('preparing_shipment') }}
                                                                </h6>
                                                            </div>
                                                            @if( ($order['order_status']=='processing') || ($order['order_status']=='processed') || ($order['order_status']=='out_for_delivery') || ($order['order_status']=='delivered')  && \App\Utils\order_status_history($order['id'],'processing'))
                                                                <div class="d-flex align-items-center justify-content-sm-center mt-2 gap-2">
                                                            <span class="text-muted fs-12">
                                                                {{date('h:i A, d M Y',strtotime(\App\Utils\order_status_history($order['id'],'processing')))}}
                                                            </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="nav-item">
                                                <div
                                                    class="nav-link {{ ($order['order_status']=='out_for_delivery') || ($order['order_status']=='delivered')?'active-status' : ''}}">
                                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                        <div class="media-tab-media mb-3 mx-sm-auto">
                                                            <img
                                                                src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/on-the-way.png') }}"
                                                                alt="">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="text-sm-center text-start">
                                                                <h6 class="media-tab-title text-nowrap mb-0 fs-14">{{ translate('order_is_on_the_way') }}</h6>
                                                            </div>
                                                            @if( ($order['order_status']=='out_for_delivery') || ($order['order_status']=='delivered') && \App\Utils\order_status_history($order['id'],'out_for_delivery'))
                                                                <div class="d-flex align-items-center justify-content-sm-center mt-2 gap-2">
                                                            <span class="text-muted fs-12">
                                                                {{date('h:i A, d M Y',strtotime(\App\Utils\order_status_history($order['id'],'out_for_delivery')))}}
                                                            </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="nav-item">
                                                <div
                                                    class="nav-link {{ ($order['order_status']=='delivered')?'active-status' : ''}}">
                                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                        <div class="media-tab-media mb-3 mx-sm-auto">
                                                            <img
                                                                src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/delivered.png') }}"
                                                                alt="">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="text-sm-center text-start">
                                                                <h6 class="media-tab-title text-nowrap mb-0 fs-14">{{ translate('order_Shipped') }}</h6>
                                                            </div>
                                                            @if(($order['order_status']=='delivered') && \App\Utils\order_status_history($order['id'],'delivered'))
                                                                <div class="d-flex align-items-center justify-content-sm-center mt-2 gap-2">
                                                            <span class="text-muted fs-12">
                                                                {{date('h:i A, d M Y',strtotime(\App\Utils\order_status_history($order['id'],'delivered')))}}
                                                            </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @else

                                                <?php
                                                $digitalProductProcessComplete = true;
                                                foreach ($order->orderDetails as $detail) {
                                                    $productData = json_decode($detail->product_details, true);
                                                    if (isset($productData->digital_product_type) && $productData->digital_product_type == 'ready_after_sell' && $detail->digital_file_after_sell == null) {
                                                        $digitalProductProcessComplete = false;
                                                    }
                                                }
                                                ?>

                                            <li class="nav-item">
                                                <div
                                                    class="nav-link {{ ($order['order_status']=='confirmed') ? 'active-status' : ''}}">
                                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                        <div class="media-tab-media mb-3 mx-sm-auto">
                                                            <img alt=""
                                                                 src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/shipment.png') }}">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="text-sm-center text-start">
                                                                <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">
                                                                    {{ translate('processing') }}
                                                                </h6>
                                                            </div>
                                                            @if($order['order_status']=='confirmed' && \App\Utils\order_status_history($order['id'],'confirmed'))
                                                                <div class="d-flex align-items-center justify-content-sm-center mt-2 gap-2">
                                                            <span class="text-muted fs-12">
                                                                {{date('h:i A, d M Y',strtotime(\App\Utils\order_status_history($order['id'],'confirmed')))}}
                                                            </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="nav-item">
                                                <div
                                                    class="nav-link {{ ($order['order_status']=='confirmed' && $digitalProductProcessComplete)?'active-status' : ''}}">
                                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                        <div class="media-tab-media mb-3 mx-sm-auto">
                                                            <img
                                                                src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/delivered.png') }}"
                                                                alt="">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="text-sm-center text-start">
                                                                <h6 class="media-tab-title text-nowrap mb-0 fs-14">{{ translate('delivery_complete') }}</h6>
                                                            </div>

                                                            @if(($order['order_status']=='confirmed') && $digitalProductProcessComplete && \App\Utils\order_status_history($order['id'],'confirmed'))
                                                                <div class="d-flex align-items-center justify-content-sm-center mt-2 gap-2">
                                                            <span class="text-muted fs-12">
                                                                {{date('h:i A, d M Y',strtotime(\App\Utils\order_status_history($order['id'],'confirmed')))}}
                                                            </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @elseif(in_array($order['order_status'], ['returned', 'canceled']))
                                        <li class="nav-item">
                                            <div class="nav-link active-status">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mx-sm-auto mb-3">
                                                        <img
                                                            src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/'.$order['order_status'].'.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center text-start">
                                                            <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">
                                                                {{ translate('order') }} {{ translate($order['order_status']) }}
                                                            </h6>
                                                        </div>
                                                        @if(\App\Utils\order_status_history($order['id'], $order['order_status']))
                                                            <div class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                        <span class="text-muted fs-12">
                                                            {{ date('h:i A, d M Y', strtotime(\App\Utils\order_status_history($order['id'], $order['order_status']))) }}
                                                        </span>
                                                            </div>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <div class="nav-link active-status">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mx-sm-auto mb-3">
                                                        <img
                                                            src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/track-order/order-failed.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center text-start">
                                                            <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">{{ translate('Failed_to_Deliver') }}</h6>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                <span class="text-muted fs-12">
                                                    {{ translate('sorry_we_can_not_complete_your_order') }}
                                                </span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <div class="col-md-12 modal_body_map mt-5 pl-0 pr-0">
                                <div class="mb-2">
                                    <img src="{{ dynamicAsset('assets/new/back-end/img/location-blue.png') }}" alt="">
                                    <span>{{ $shippingAddress ? $shippingAddress->address : ($billing ? $billing->address : '') }}</span>
                                </div>
                                @if(getWebConfig('map_api_status') ==1 )
                                    <div class="location-map" id="location-map">
                                        <div class="w-100 h-200" id="location_map_canvas"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="courierInfoModal" tabindex="-1" aria-labelledby="courierInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courierInfoModalLabel">{{translate('courier_shipment_info')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="courierInfoModalBody">
                    <!-- Content will be loaded dynamically -->
                    <div class="text-center">
                         <span class="spinner-border text-primary" role="status"></span>
                         <span class="ms-2">{{translate('loading')}}...</span>
                    </div>
                </div>
                <!-- Hidden Template for Static/Fallback Data -->
                <div id="staticCourierInfo" style="display: none;">
                    @if($order->courierShipmentInfo)
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>{{translate('courier_name')}}</th>
                                    <td>{{$order->courierShipmentInfo->courier_name}}</td>
                                </tr>
                                <tr>
                                    <th>{{translate('consignment_id')}}</th>
                                    <td>{{$order->courierShipmentInfo->consignment_id}}</td>
                                </tr>
                                <tr>
                                    <th>{{translate('merchant_order_id')}}</th>
                                    <td>{{$order->courierShipmentInfo->merchant_order_id}}</td>
                                </tr>
                                <tr>
                                    <th>{{translate('delivery_fee')}}</th>
                                    <td>{{$order->courierShipmentInfo->delivery_fee}}</td>
                                </tr>
                                <tr>
                                    <th>{{translate('order_status')}}</th>
                                    <td>{{$order->courierShipmentInfo->order_status}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-3">
                             <h6>{{translate('full_response')}}:</h6>
                             <pre class="bg-light p-2" style="max-height: 200px; overflow-y: auto;">{{ json_encode(json_decode($order->courierShipmentInfo->response_data ?? '{}'), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @else
                        <div class="text-center">
                            <p>{{translate('no_info_found')}}</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('close')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="third_party_delivery_service_modal" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{translate('update_third_party_delivery_info')}}</h5>
                    <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{route('admin.orders.update-deliver-info')}}" method="POST">
                                @csrf
                                <input type="hidden" name="order_id" value="{{$order['id']}}">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="">{{translate('delivery_service_name')}}</label>
                                        <select class="form-control" name="delivery_service_name" id="courier_service_select" required>
                                            <option value="">{{translate('select_courier_service')}}</option>
                                            @foreach($activeCouriers as $courier)
                                                <option value="{{$courier->title}}" {{ $order['delivery_service_name'] == $courier->title ? 'selected' : '' }}>
                                                    {{$courier->title}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Pathao-specific fields (hidden by default) -->
                                    <div id="pathao_fields" style="display: none;">
                                        <div class="alert alert-info">
                                            <strong>{{translate('customer_info')}}:</strong><br>
                                            {{translate('name')}}: {{$shippingAddress ? $shippingAddress->contact_person_name : ($order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'N/A')}}<br>
                                            {{translate('phone')}}: {{$shippingAddress ? $shippingAddress->phone : ($order->customer ? $order->customer->phone : 'N/A')}}<br>
                                            {{translate('address')}}: {{$shippingAddress ? $shippingAddress->address : 'N/A'}}<br>
                                            {{translate('city')}}: {{$shippingAddress ? $shippingAddress->city : 'N/A'}}<br>
                                            {{translate('zip_code')}}: {{$shippingAddress ? $shippingAddress->zip : 'N/A'}}<br>
                                            {{translate('country')}}: {{$shippingAddress ? $shippingAddress->country : 'N/A'}}<br>
                                            {{translate('order_amount')}}: {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['order_amount']))}}
                                        </div>

                                        <div class="form-group">
                                            <label for="pathao_city_id">{{translate('pathao_city')}} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="pathao_city_id" id="pathao_city_id" required>
                                                <option value="">{{translate('select_city')}}</option>
                                            </select>
                                            <small class="text-muted">{{translate('loading_cities')}}...</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="pathao_zone_id">{{translate('pathao_zone')}} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="pathao_zone_id" id="pathao_zone_id" disabled>
                                                <option value="">{{translate('select_zone')}}</option>
                                            </select>
                                            <small class="text-muted">{{translate('select_city_first')}}</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="pathao_area_id">{{translate('pathao_area')}} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="pathao_area_id" id="pathao_area_id" disabled>
                                                <option value="">{{translate('select_area')}}</option>
                                            </select>
                                            <small class="text-muted">{{translate('select_zone_first')}}</small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pathao_recipient_phone">{{translate('recipient_phone')}} <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="pathao_recipient_phone" id="pathao_recipient_phone" 
                                                           value="{{$shippingAddress ? $shippingAddress->phone : ($order->customer ? $order->customer->phone : '')}}" 
                                                           placeholder="{{translate('ex')}}: 01700000000" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pathao_item_weight">{{translate('item_weight')}} (kg) <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" class="form-control" name="pathao_item_weight" id="pathao_item_weight" 
                                                           value="0.5" placeholder="{{translate('ex')}}: 0.5" required>
                                                    <small class="text-muted">{{translate('total_weight_in_kg')}}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="pathao_recipient_address">{{translate('recipient_address')}} <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="pathao_recipient_address" id="pathao_recipient_address" 
                                                      rows="2" placeholder="{{translate('enter_delivery_address')}}" required>{{$shippingAddress ? $shippingAddress->address : ''}}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="pathao_item_description">{{translate('item_description')}}</label>
                                            <textarea class="form-control" name="pathao_item_description" id="pathao_item_description" 
                                                      rows="2" placeholder="{{translate('ex')}}: {{translate('order_items_description')}}">Order #{{$order->id}} - {{$order->details->sum('qty')}} items</textarea>
                                        </div>
                                    </div>

                                    <div id="steadfast_fields" style="display: none;">
                                        <div class="alert alert-info">
                                            <strong>{{translate('customer_info')}}:</strong><br>
                                            {{translate('name')}}: {{$shippingAddress ? $shippingAddress->contact_person_name : ($order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'N/A')}}<br>
                                            {{translate('phone')}}: {{$shippingAddress ? $shippingAddress->phone : ($order->customer ? $order->customer->phone : 'N/A')}}<br>
                                            {{translate('address')}}: {{$shippingAddress ? $shippingAddress->address : 'N/A'}}<br>
                                            {{translate('city')}}: {{$shippingAddress ? $shippingAddress->city : 'N/A'}}<br>
                                            {{translate('zip_code')}}: {{$shippingAddress ? $shippingAddress->zip : 'N/A'}}<br>
                                            {{translate('country')}}: {{$shippingAddress ? $shippingAddress->country : 'N/A'}}<br>
                                            {{translate('order_amount')}}: {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['order_amount']))}}
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">{{ translate('recipient_name') }}</label>
                                                    <input type="text" class="form-control" name="steadfast_recipient_name" value="{{ $shippingAddress ? $shippingAddress->contact_person_name : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">{{ translate('recipient_phone') }}</label>
                                                    <input type="text" class="form-control" name="steadfast_recipient_phone" value="{{ $shippingAddress ? $shippingAddress->phone : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">{{ translate('recipient_address') }}</label>
                                                    <textarea class="form-control" name="steadfast_recipient_address">{{ $shippingAddress ? $shippingAddress->address : '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">{{ translate('delivery_type') }}</label>
                                                    <select class="form-control mb-2" name="steadfast_delivery_type">
                                                        <option value="0">{{ translate('home_delivery') }}</option>
                                                        <option value="1">{{ translate('point_delivery') }} / {{ translate('hub_pickup') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Redx-specific fields -->
                                    <div id="redx_fields" style="display: none;">
                                        <div class="alert alert-info">
                                            <strong>{{translate('customer_info')}}:</strong><br>
                                            {{translate('name')}}: {{$shippingAddress ? $shippingAddress->contact_person_name : ($order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'N/A')}}<br>
                                            {{translate('phone')}}: {{$shippingAddress ? $shippingAddress->phone : ($order->customer ? $order->customer->phone : 'N/A')}}<br>
                                            {{translate('address')}}: {{$shippingAddress ? $shippingAddress->address : 'N/A'}}<br>
                                            {{translate('city')}}: {{$shippingAddress ? $shippingAddress->city : 'N/A'}}<br>
                                            {{translate('zip_code')}}: {{$shippingAddress ? $shippingAddress->zip : 'N/A'}}<br>
                                            {{translate('country')}}: {{$shippingAddress ? $shippingAddress->country : 'N/A'}}<br>
                                            {{translate('order_amount')}}: {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['order_amount']))}}
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">{{translate('recipient_name')}}</label>
                                                    <input type="text" class="form-control" name="redx_recipient_name" 
                                                        value="{{$order->customer->f_name ?? ''}} {{$order->customer->l_name ?? ''}}" >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">{{translate('recipient_phone')}}</label>
                                                    <input type="text" class="form-control" name="redx_recipient_phone" 
                                                        value="{{$order->customer->phone ?? ''}}" >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">{{translate('recipient_address')}}</label>
                                                    <textarea class="form-control" name="redx_recipient_address" >{{$shippingAddress->address ?? ''}}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">{{translate('area_district')}}</label>
                                                    <input type="text" class="form-control" name="redx_district" id="redx_district" placeholder="{{translate('district')}}">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">{{translate('area_post_code')}}</label>
                                                    <input type="text" class="form-control" name="redx_post_code" id="redx_post_code" placeholder="{{translate('post_code')}}">
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">{{translate('delivery_area')}} <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="redx_delivery_area_id" id="redx_delivery_area_id">
                                                        <option value="">{{translate('select_area')}}</option>
                                                    </select>
                                                    <input type="hidden" name="redx_delivery_area" id="redx_delivery_area">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">{{translate('parcel_weight')}} (g)</label>
                                                    <input type="number" class="form-control" name="redx_parcel_weight" value="500">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="form-label">{{translate('instruction')}}</label>
                                                    <input type="text" class="form-control" name="redx_instruction" value="" placeholder="{{translate('instruction')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary" type="submit">{{translate('update')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="message-status-title-text"
          data-text="{{$order['payment_method'] != 'cash_on_delivery' && $order['order_status']=='delivered' ? translate("Order_is_already_delivered_and_transaction_amount_has_been_disbursed_changing_status_can_be_the_reason_of_miscalculation") : translate("are_you_sure_change_this") }}"></span>
    <span id="message-status-subtitle-text"
          data-text="{{ $order['payment_method'] != 'cash_on_delivery' && $order['order_status']=='delivered' ? translate('think_before_you_proceed') : translate("you_will_not_be_able_to_revert_this") }}!"></span>
    <span id="payment-status-message" data-title="{{translate('confirm_payments_before_change_the_status').'.'}}"
          data-message="{{translate('change_the_status_paid_only_when_you_received_the_payment_from_customer').translate('_once_you_change_the_status_to_paid').','.translate('_you_cannot_change_the_status_again').'!' }}"></span>
    <span id="message-status-confirm-text" data-text="{{ translate("yes_change_it") }}!"></span>
    <span id="message-status-cancel-text" data-text="{{ translate("cancel") }}"></span>
    <span id="message-status-success-text" data-text="{{ translate("status_change_successfully") }}"></span>
    <span id="message-status-warning-text"
          data-text="{{ translate("account_has_been_deleted_you_can_not_change_the_status") }}"></span>
    <span id="message-order-status-delivered-text"
          data-text="{{ translate("order_is_already_delivered_you_can_not_change_it") }}!"></span>
    <span id="message-order-status-paid-first-text"
          data-text="{{ translate("before_delivered_you_need_to_make_payment_status_paid") }}!"></span>
    <span id="order-status-url" data-url="{{route('admin.orders.status')}}"></span>
    <span id="payment-status-url" data-url="{{ route('admin.orders.payment-status') }}"></span>

    <span id="message-deliveryman-add-success-text"
          data-text="{{ translate("delivery_man_successfully_assigned/changed") }}"></span>
    <span id="message-deliveryman-add-error-text"
          data-text="{{ translate("deliveryman_man_can_not_assign_or_change_in_that_status") }}"></span>
    <span id="message-deliveryman-add-invalid-text"
          data-text="{{ translate("deliveryman_man_can_not_assign_or_change_in_that_status") }}"></span>
    <span id="delivery-type" data-type="{{ $order->delivery_type }}"></span>
    <span id="add-delivery-man-url" data-url="{{url('/admin/orders/add-delivery-man/'.$order['id'])}}/"></span>

    <span id="message-deliveryman-charge-success-text"
          data-text="{{ translate("deliveryman_charge_add_successfully") }}"></span>
    <span id="message-deliveryman-charge-error-text"
          data-text="{{ translate("failed_to_add_deliveryman_charge") }}"></span>
    <span id="message-deliveryman-charge-invalid-text" data-text="{{ translate("add_valid_data") }}"></span>
    <span id="add-date-update-url" data-url="{{route('admin.orders.amount-date-update')}}"></span>

    <span id="customer-name" data-text="{{$order->customer['f_name']??""}} {{$order->customer['l_name']??""}}}"></span>
    <span id="is-shipping-exist" data-status="{{$shippingAddress ? 'true':'false'}}"></span>
    <span id="shipping-address" data-text="{{$shippingAddress->address??''}}"></span>
    <span id="shipping-latitude" data-latitude="{{$shippingAddress->latitude??'-33.8688'}}"></span>
    <span id="shipping-longitude" data-longitude="{{$shippingAddress->longitude??'151.2195'}}"></span>
    <span id="billing-latitude" data-latitude="{{$billing->latitude??'-33.8688'}}"></span>
    <span id="billing-longitude" data-longitude="{{$billing->longitude??'151.2195'}}"></span>
    <span id="location-icon"
          data-path="{{ dynamicAsset(path: 'public/assets/front-end/img/customer_location.png')}}"></span>
    <span id="customer-image"
          data-path="{{dynamicStorage(path: 'storage/app/public/profile/')}}{{$order->customer->image??""}}"></span>
    <span id="deliveryman-charge-alert-message"
          data-message="{{translate('when_order_status_delivered_you_can`t_update_the_delivery_man_incentive').'.'}}"></span>
    <span id="payment-status-alert-message"
          data-message="{{translate('when_payment_status_paid_then_you_can`t_change_payment_status_paid_to_unpaid').'.'}}"></span>

@endsection

@push('script')
    @if(getWebConfig('map_api_status') == 1)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ getWebConfig('map_api_key') }}&callback=mapCallBackFunction&loading=async&libraries=places&v=3.56"
                defer>
        </script>
    @endif
    
    <script>
        'use strict';
        
        $(document).ready(function() {
            // Handle courier service selection
            // Handle courier service selection
            $('#courier_service_select').on('change', function() {
                const selectedCourier = $(this).val();
                
                // Reset fields
                $('#pathao_fields').slideUp();
                $('#steadfast_fields').slideUp();
                $('#redx_fields').slideUp();
                $('#pathao_fields :input').prop('required', false);
                $('#steadfast_fields :input').prop('required', false);
                $('#redx_fields :input').prop('required', false);

                if (selectedCourier === 'Pathao') {
                    $('#pathao_fields').slideDown();
                    // Enable required for Pathao inputs, except disabled unused ones
                    $('#pathao_fields :input').not(':disabled').prop('required', true);
                    // Specifically ensure city is required
                    $('#pathao_city_id').prop('required', true);
                    loadPathaoCities();
                } else if (selectedCourier === 'SteadFast') {
                    $('#steadfast_fields').slideDown();
                    // Enable required for Steadfast inputs
                     $('#steadfast_fields :input').prop('required', true);
                } else if (selectedCourier === 'Redx') {
                    $('#redx_fields').slideDown();
                    $('#redx_fields :input').not('#redx_post_code, #redx_district, #redx_instruction').prop('required', true);
                }
            });

            // Redx Area Search
            let redxSearchTimeout;
            $('#redx_post_code, #redx_district').on('keyup change', function() {
                clearTimeout(redxSearchTimeout);
                const postCode = $('#redx_post_code').val();
                const district = $('#redx_district').val();
                
                redxSearchTimeout = setTimeout(function() {
                    if (postCode.length > 2 || district.length > 2) {
                        loadRedxAreas(postCode, district);
                    }
                }, 500);
            });

            function loadRedxAreas(postCode, district) {
                 $.ajax({
                    url: '{{ route("admin.third-party.redx.get-areas") }}',
                    type: 'GET',
                    data: { post_code: postCode, district_name: district },
                    beforeSend: function() {
                        $('#redx_delivery_area_id').empty().append('<option value="">{{translate("loading")}}...</option>');
                    },
                    success: function(response) {
                        $('#redx_delivery_area_id').empty().append('<option value="">{{translate("select_area")}}</option>');
                        
                        let areas = [];
                        if (response.success && response.data) {
                            areas = response.data;
                        } else if (response.areas) {
                             areas = response.areas;
                        } else if (Array.isArray(response)) {
                             areas = response;
                        }

                        if (areas.length > 0) {
                             areas.forEach(function(area) {
                                $('#redx_delivery_area_id').append(
                                    `<option value="${area.id}" data-name="${area.name}">${area.name} (${area.post_code})</option>`
                                );
                            });
                        } else {
                            $('#redx_delivery_area_id').append('<option value="" disabled>{{translate("no_areas_found")}}</option>');
                        }
                    },
                    error: function() {
                        $('#redx_delivery_area_id').empty().append('<option value="">{{translate("error_loading_areas")}}</option>');
                    }
                });
            }

            $('#redx_delivery_area_id').on('change', function() {
                const areaName = $(this).find('option:selected').data('name');
                $('#redx_delivery_area').val(areaName);
            });

    function loadPathaoCities() {
                $.ajax({
                    url: '{{ route("admin.third-party.pathao.get-cities") }}',
                    type: 'GET',
                    success: function(response) {
                        $('#pathao_city_id').empty().append('<option value="">{{translate("select_city")}}</option>');
                        
                        if (response.success && response.data) {
                            response.data.forEach(function(city) {
                                $('#pathao_city_id').append(
                                    `<option value="${city.city_id}">${city.city_name}</option>`
                                );
                            });
                            $('#pathao_city_id').next('small').text('{{translate("select_a_city")}}');
                        }
                    },
                    error: function() {
                        $('#pathao_city_id').next('small').text('{{translate("failed_to_load_cities")}}').addClass('text-danger');
                    }
                });
            }

            // Load zones when city is selected
            $('#pathao_city_id').on('change', function() {
                const cityId = $(this).val();
                
                if (cityId) {
                    $.ajax({
                        url: '{{ route("admin.third-party.pathao.get-zones") }}',
                        type: 'GET',
                        data: { city_id: cityId },
                        success: function(response) {
                            $('#pathao_zone_id').empty().append('<option value="">{{translate("select_zone")}}</option>');
                            $('#pathao_area_id').empty().append('<option value="">{{translate("select_area")}}</option>').prop('disabled', true);
                            
                            if (response.success && response.data) {
                                response.data.forEach(function(zone) {
                                    $('#pathao_zone_id').append(
                                        `<option value="${zone.zone_id}">${zone.zone_name}</option>`
                                    );
                                });
                                $('#pathao_zone_id').prop('disabled', false);
                                $('#pathao_zone_id').next('small').text('{{translate("select_a_zone")}}').removeClass('text-danger');
                            }
                        },
                        error: function() {
                            $('#pathao_zone_id').next('small').text('{{translate("failed_to_load_zones")}}').addClass('text-danger');
                        }
                    });
                } else {
                    $('#pathao_zone_id').empty().append('<option value="">{{translate("select_zone")}}</option>').prop('disabled', true);
                    $('#pathao_area_id').empty().append('<option value="">{{translate("select_area")}}</option>').prop('disabled', true);
                }
            });

            // Load areas when zone is selected
            $('#pathao_zone_id').on('change', function() {
                const zoneId = $(this).val();
                
                if (zoneId) {
                    $.ajax({
                        url: '{{ route("admin.third-party.pathao.get-areas") }}',
                        type: 'GET',
                        data: { zone_id: zoneId },
                        success: function(response) {
                            $('#pathao_area_id').empty().append('<option value="">{{translate("select_area")}}</option>');
                            
                            if (response.success && response.data) {
                                response.data.forEach(function(area) {
                                    $('#pathao_area_id').append(
                                        `<option value="${area.area_id}">${area.area_name}</option>`
                                    );
                                });
                                $('#pathao_area_id').prop('disabled', false);
                                $('#pathao_area_id').next('small').text('{{translate("select_an_area")}}').removeClass('text-danger');
                            }
                        },
                        error: function() {
                            $('#pathao_area_id').next('small').text('{{translate("failed_to_load_areas")}}').addClass('text-danger');
                        }
                    });
                } else {
                    $('#pathao_area_id').empty().append('<option value="">{{translate("select_area")}}</option>').prop('disabled', true);
                }
            });

            // Trigger change to set initial state
            $('#courier_service_select').trigger('change');
    });

    function openCourierInfoModal(courierName, consignmentId) {
        if (!consignmentId) return;

        $('#courierInfoModal').modal('show');
        const modalBody = $('#courierInfoModalBody');
        
        // Default loading Spinner
        modalBody.html('<div class="text-center"><span class="spinner-border text-primary" role="status"></span><span class="ms-2">{{translate("loading")}}...</span></div>');
        
        if (courierName === 'Pathao') {
            $.ajax({
                url: '{{ route("admin.third-party.pathao.get-order-details") }}',
                type: 'GET',
                data: { consignment_id: consignmentId },
                success: function(response) {
                    if (response.success && response.data) {
                        const data = response.data;
                        let html = `
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>{{translate('courier_name')}}</th>
                                        <td>${courierName}</td>
                                    </tr>
                                    <tr>
                                        <th>{{translate('consignment_id')}}</th>
                                        <td>${data.consignment_id}</td>
                                    </tr>
                                    <tr>
                                        <th>{{translate('merchant_order_id')}}</th>
                                        <td>${data.merchant_order_id}</td>
                                    </tr>
                                    <tr>
                                        <th>{{translate('order_status')}}</th>
                                        <td>${data.order_status}</td>
                                    </tr>
                                    <tr>
                                        <th>{{translate('last_updated')}}</th>
                                        <td>${data.updated_at}</td>
                                    </tr>
                                </tbody>
                            </table>
                        `;
                        modalBody.html(html);
                    } else {
                        modalBody.html('<div class="text-center text-danger"><p>{{translate("failed_to_fetch_live_data")}}</p></div>');
                    }
                },
                error: function() {
                    modalBody.html('<div class="text-center text-danger"><p>{{translate("error_fetching_data")}}</p></div>');
                }
            });
        } else if (courierName === 'Steadfast') {
            $.ajax({
                url: '{{ route("admin.third-party.steadfast.get-delivery-status") }}',
                type: 'GET',
                data: { consignment_id: consignmentId },
                success: function(response) {
                    if (response.success && response.data) {
                        const data = response.data;
                        let html = `
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>{{translate('courier_name')}}</th>
                                        <td>${courierName}</td>
                                    </tr>
                                    <tr>
                                        <th>{{translate('consignment_id')}}</th>
                                        <td>${consignmentId}</td>
                                    </tr>
                                    <tr>
                                        <th>{{translate('delivery_status')}}</th>
                                        <td>${data.delivery_status}</td>
                                    </tr>
                                </tbody>
                            </table>
                        `;
                        modalBody.html(html);
                    } else {
                        modalBody.html('<div class="text-center text-danger"><p>{{translate("failed_to_fetch_live_data")}}</p></div>');
                    }
                },
                error: function() {
                    modalBody.html('<div class="text-center text-danger"><p>{{translate("error_fetching_data")}}</p></div>');
                }
            });
        } else {
            // Fallback to static DB data for other couriers
            const staticContent = $('#staticCourierInfo').html();
            if (staticContent && staticContent.trim()) {
                modalBody.html(staticContent);
            } else {
                modalBody.html('<div class="text-center"><p>{{translate("no_info_found")}}</p></div>');
            }
        }
    }
</script>
    
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/order.js') }}"></script>
@endpush
