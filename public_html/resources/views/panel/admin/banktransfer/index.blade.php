@extends('panel.layout.app')
@section('title', __('Bank Transactions'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center justify-between max-md:flex-col max-md:items-start max-md:gap-4">
                <div class="col">
                    <a href="/dashboard" class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
                        </svg>
                        {{__('Back to dashboard')}}
                    </a>
                    <h2 class="page-title mb-2">
                        {{__('Bank Transactions')}}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body p-1">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-table table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>{{__('Order ID')}}</th>
                                <th>{{__('Proof Of Purchase')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Date')}}</th>
                                <th>{{__('Info')}}</th>
                                <th colspan="3">{{__('Plan')}} / {{__('Words')}} / {{__('Images')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                        @foreach($bankOrders as $order)
                        <tr>
                            <td>
                                {{__($order->order_id)}}
                                <br>
                                <span class="opacity-70">{{$order->created_at}}</span>
                            </td>
                            <td>
                                <a href="{{ getImageUrlByOrderId($order->order_id) }}" download>
                                    <img src="{{ getImageUrlByOrderId($order->order_id) }}" alt="Proof of Purchase" style="max-width: 100px;">
                                </a>                            
                            </td>
                            @if($order->status == 'Success')
                            <td>
                                <span class="badge bg-success">{{__($order->status)}}</span>
                            </td>
                            @else
                            <td>
                                @switch($order->status)
                                    @case("Waiting")
                                        <span class="badge bg-danger">{{__($order->status)}}</span>
                                        @break
                                    @case("Approved")
                                        <span class="badge bg-primary">{{__($order->status)}}</span>
                                        @break
                                    @case("Rejected")
                                        <span class="badge bg-warning">{{__($order->status)}}</span>         
                                        @break
                                    @default
                                        <span class="badge bg-danger">{{__($order->status)}}</span>     
                                @endswitch
                            </td>
                            @endif
                            <td class="sort-date" data-date="{{strtotime($order->created_at)}}">
								<p class="m-0">{{date("j.n.Y", strtotime($order->created_at))}}</p>
								<p class="m-0 text-muted">{{date("H:i:s", strtotime($order->created_at))}}</p>
							</td>
                            <td class="text-muted">
                                <span class="text-[var(--lqd-heading-color)]">{{$order->user->fullName()}}</span>
                                <br>
                                <span class="opacity-70">{{__($order->type)}} / {{ ($order->type == "subscription") ? __(formatCamelCase(@$order->plan->frequency)) : __("One Time") }}</span>
                                <br>
                                <span class="opacity-70">
                                    {{__('Total')}} : 
                                    @if(currencyShouldDisplayOnRight(currency()->symbol))
                                        {{$order->price}}{{currency()->symbol}}
                                    @else
                                        {{currency()->symbol}}{{$order->price}}
                                    @endif 
                                </span>
                            </td>
                            <td class="w-1" colspan="3">
                                <span class="text-primary font-medium">{{@$order->plan->name ?? __('Archived Plan')}}</span>
                                /<span class="text-[var(--lqd-heading-color)] !ms-1">{{@$order->plan->total_words === "-1" ? __('Unlimited') : (@$order->plan->total_words ?? '-')}}</span>
                                /<span class="text-[var(--lqd-heading-color)] !ms-1">{{@$order->plan->total_images === "-1" ? __('Unlimited') : (@$order->plan->total_images ?? '-')}}</span>
                            </td>

                            {{-- <td>
								<a href="{{ LaravelLocalization::localizeUrl( route('dashboard.user.orders.invoice', $order->order_id) ) }}" class="btn border p-0 w-[36px] h-[36px] hover:bg-[var(--tblr-primary)] hover:text-white" title="{{__('Invoice')}}">
									<svg width="16" height="16" viewBox="0 0 18 18" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
										<path d="M13 3H16C16.2652 3 16.5196 3.10536 16.7071 3.29289C16.8946 3.48043 17 3.73478 17 4V15C17 15.5304 16.7893 16.0391 16.4142 16.4142C16.0391 16.7893 15.5304 17 15 17M15 17C14.4696 17 13.9609 16.7893 13.5858 16.4142C13.2107 16.0391 13 15.5304 13 15V2C13 1.73478 12.8946 1.48043 12.7071 1.29289C12.5196 1.10536 12.2652 1 12 1H2C1.73478 1 1.48043 1.10536 1.29289 1.29289C1.10536 1.48043 1 1.73478 1 2V14C1 14.7956 1.31607 15.5587 1.87868 16.1213C2.44129 16.6839 3.20435 17 4 17H15ZM5 5H9M5 9H9M5 13H9" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</a>
							</td> --}}

                            <td class="whitespace-nowrap">
                                <a class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white" title="{{__('View')}}" 
                                data-bs-toggle="modal" data-bs-target="#orderViewModal"
                                data-order="{{ json_encode($order) }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                    </svg>
                                </a>
                                @if($app_is_demo)
                                    <a  onclick="return toastr.info('This feature is disabled in Demo version.')" class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white" title="{{__('Edit')}}">
                                        <svg width="13" height="12" viewBox="0 0 16 15" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.3125 2.55064L12.8125 5.94302M11.5 12.3038H15M4.5 14L13.6875 5.09498C13.9173 4.87223 14.0996 4.60779 14.224 4.31676C14.3484 4.02572 14.4124 3.71379 14.4124 3.39878C14.4124 3.08377 14.3484 2.77184 14.224 2.48081C14.0996 2.18977 13.9173 1.92533 13.6875 1.70259C13.4577 1.47984 13.1849 1.30315 12.8846 1.1826C12.5843 1.06205 12.2625 1 11.9375 1C11.6125 1 11.2907 1.06205 10.9904 1.1826C10.6901 1.30315 10.4173 1.47984 10.1875 1.70259L1 10.6076V14H4.5Z" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                    <a  onclick="return toastr.info('This feature is disabled in Demo version.')" class="btn p-0 border w-[36px] h-[36px] hover:bg-red-600 hover:text-white" title="{{__('Delete')}}">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"/>
                                        </svg>
                                    </a>
                                @else
                                    @if($order->role != 'default')
                                        <a 
                                        data-bs-toggle="modal" data-bs-target="#orderStatusModal"
                                        data-id="{{$order->id}}"
                                        data-status="{{$order->status}}"
                                        class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white" title="{{__('Edit')}}">
                                            <svg width="13" height="12" viewBox="0 0 16 15" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.3125 2.55064L12.8125 5.94302M11.5 12.3038H15M4.5 14L13.6875 5.09498C13.9173 4.87223 14.0996 4.60779 14.224 4.31676C14.3484 4.02572 14.4124 3.71379 14.4124 3.39878C14.4124 3.08377 14.3484 2.77184 14.224 2.48081C14.0996 2.18977 13.9173 1.92533 13.6875 1.70259C13.4577 1.47984 13.1849 1.30315 12.8846 1.1826C12.5843 1.06205 12.2625 1 11.9375 1C11.6125 1 11.2907 1.06205 10.9904 1.1826C10.6901 1.30315 10.4173 1.47984 10.1875 1.70259L1 10.6076V14H4.5Z" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        {{-- <a href="{{ LaravelLocalization::localizeUrl( route('dashboard.admin.bank.transactions.delete', $order->id) ) }}" onclick="return confirm('{{__('Are you sure? This is permanent.')}}')" class="btn p-0 border w-[36px] h-[36px] hover:bg-red-600 hover:text-white" title="{{__('Delete')}}">
                                            <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"/>
                                            </svg>
                                        </a> --}}
                                    @endif
                                @endif
                            </td>

                        </tr>
                        @endforeach

                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="orderStatusModal" tabindex="-1" role="dialog" aria-labelledby="orderStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderStatusModalLabel">{{__('Change Order Status')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="orderStatusForm" action="{{ route('dashboard.admin.bank.transactions.update') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="orderStatusSelect" class="form-label">{{__('Current Status')}}: [<span style="color: red;" id="currsts"></span>]</label>
                            <select class="form-select" id="orderStatusSelect" name="order_status">
                                <option value="0" disabled selected>{{__('Select Status')}}</option>
                                <option value="Waiting">{{__('Waiting')}}</option>
                                <option value="Approved">{{__('Approved')}}</option>
                                <option value="Rejected">{{__('Rejected')}}</option>
                            </select>
                        </div>   
                        <input type="hidden" id="orderIdInput" name="order_id">                     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('Save Changes')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderViewModal" tabindex="-1" role="dialog" aria-labelledby="orderViewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderViewModalLabel">{{__('Order')}}: <span id="orid"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Ok')}}</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var orderStatusModal = new bootstrap.Modal(document.getElementById('orderStatusModal'));
    orderStatusModal._element.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; 
        var orderId = button.getAttribute('data-id'); 
        var status = button.getAttribute('data-status'); 
        document.getElementById('orderIdInput').value = orderId; 
        document.getElementById('currsts').textContent  = status; 
    });

    var orderViewModal = new bootstrap.Modal(document.getElementById('orderViewModal'));

    orderViewModal._element.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var orderData = JSON.parse(button.getAttribute('data-order'));
        document.getElementById('orid').textContent = orderData.order_id;
        var modalBody = document.querySelector('#orderViewModal .modal-body');
        modalBody.innerHTML = '';
        
        if (orderData.user) {
            var rowUser = document.createElement('div');
            rowUser.innerHTML = `<strong>{{__('User')}}:</strong> ${orderData.user.name} ${orderData.user.surname}`;
            modalBody.appendChild(rowUser);

            var rowUser = document.createElement('div');
            rowUser.innerHTML = `<strong>{{__('Mail')}}:</strong> ${orderData.user.email}`;
            modalBody.appendChild(rowUser);
        }
        
        if (orderData.plan) {
            var row = document.createElement('div');
            row.innerHTML = `<strong>{{__('Plan Name')}}:</strong> ${orderData.plan.name}`;
            modalBody.appendChild(row);

            var row = document.createElement('div');
            row.innerHTML = `<strong>{{__('Plan Price')}}:</strong> {{currency()->symbol}}${orderData.plan.price}`;
            modalBody.appendChild(row);
        }

        var row = document.createElement('div');
        row.innerHTML = `<strong>{{__('Tax Rate')}}:</strong> ${orderData['tax_rate']}%`;
        modalBody.appendChild(row);
        
        var row = document.createElement('div');
        row.innerHTML = `<strong>{{__('Tax')}}:</strong> {{currency()->symbol}}${orderData['tax_value']}`;
        modalBody.appendChild(row);

        var row = document.createElement('div');
        row.innerHTML = `<strong>{{__('Total')}}:</strong> {{currency()->symbol}}${orderData['price']}`;
        modalBody.appendChild(row);

    });
});
</script>
@endsection
