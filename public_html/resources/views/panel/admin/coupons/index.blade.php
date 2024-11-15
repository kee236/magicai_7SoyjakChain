@extends('panel.layout.app')
@section('title', __('Manage Coupons'))

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
                        {{__('Manage Coupons')}}
                    </h2>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addingModal">
						<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" stroke-width="2" stroke="none" fill="currentColor" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
							<path d="M12 2l.324 .001l.318 .004l.616 .017l.299 .013l.579 .034l.553 .046c4.785 .464 6.732 2.411 7.196 7.196l.046 .553l.034 .579c.005 .098 .01 .198 .013 .299l.017 .616l.005 .642l-.005 .642l-.017 .616l-.013 .299l-.034 .579l-.046 .553c-.464 4.785 -2.411 6.732 -7.196 7.196l-.553 .046l-.579 .034c-.098 .005 -.198 .01 -.299 .013l-.616 .017l-.642 .005l-.642 -.005l-.616 -.017l-.299 -.013l-.579 -.034l-.553 -.046c-4.785 -.464 -6.732 -2.411 -7.196 -7.196l-.046 -.553l-.034 -.579a28.058 28.058 0 0 1 -.013 -.299l-.017 -.616c-.003 -.21 -.005 -.424 -.005 -.642l.001 -.324l.004 -.318l.017 -.616l.013 -.299l.034 -.579l.046 -.553c.464 -4.785 2.411 -6.732 7.196 -7.196l.553 -.046l.579 -.034c.098 -.005 .198 -.01 .299 -.013l.616 -.017c.21 -.003 .424 -.005 .642 -.005zm0 6a1 1 0 0 0 -1 1v2h-2l-.117 .007a1 1 0 0 0 .117 1.993h2v2l.007 .117a1 1 0 0 0 1.993 -.117v-2h2l.117 -.007a1 1 0 0 0 -.117 -1.993h-2v-2l-.007 -.117a1 1 0 0 0 -.993 -.883z" stroke-width="0"></path>
						</svg>
					</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="card">
				<div id="table-default-2" class="card-table table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{__('Name')}}</th>
							<th>{{__('Code')}}</th>
							<th>{{__('Discount (%)')}}</th>
							<th>{{__('Limit')}}</th>
							<th>{{__('Used')}}</th>
							<th>{{__('Created By')}}</th>
							<th>{{__('Action')}}</th>
						</tr>
						</thead>
						<tbody class="table-tbody align-middle text-heading">
						@foreach($list ?? [] as $entry)
							<tr>
								<td>{{$entry->name}}</td>
								<td>{{$entry->code}}</td>
								<td>{{$entry->discount}}%</td>
								<td>{{$entry->limit}}</td>
								<td>{{$entry->usersUsed->count()}}</td>
								<td>{{$entry->createdBy->name}} <br>{{date("j.n.Y", strtotime($entry->created_at))}}</td>

								<td class="whitespace-nowrap">
                                    <a href="{{ LaravelLocalization::localizeUrl( route('dashboard.admin.coupons.used', $entry->id) ) }}" class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white" title="{{__('View')}}">
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
										<a data-row-id="{{ $entry->id }}" data-row-name="{{ $entry->name }}" data-row-code="{{ $entry->code }}" data-row-discount="{{ $entry->discount }}" data-row-limit="{{ $entry->limit }}" data-bs-toggle="modal" data-bs-target="#editModal" class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white edit-button" title="{{__('Edit')}}">
											<svg width="13" height="12" viewBox="0 0 16 15" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path d="M9.3125 2.55064L12.8125 5.94302M11.5 12.3038H15M4.5 14L13.6875 5.09498C13.9173 4.87223 14.0996 4.60779 14.224 4.31676C14.3484 4.02572 14.4124 3.71379 14.4124 3.39878C14.4124 3.08377 14.3484 2.77184 14.224 2.48081C14.0996 2.18977 13.9173 1.92533 13.6875 1.70259C13.4577 1.47984 13.1849 1.30315 12.8846 1.1826C12.5843 1.06205 12.2625 1 11.9375 1C11.6125 1 11.2907 1.06205 10.9904 1.1826C10.6901 1.30315 10.4173 1.47984 10.1875 1.70259L1 10.6076V14H4.5Z" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</a>
										<a href="{{ LaravelLocalization::localizeUrl( route('dashboard.admin.coupons.delete', $entry->id) ) }}" onclick="confirm('{{__('Are you sure? This is permanent.')}}')" class="btn p-0 border w-[36px] h-[36px] hover:bg-red-600 hover:text-white" title="{{__('Delete')}}">
											<svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"/>
											</svg>
										</a>
                                    @endif
                                </td>

							</tr>
						@endforeach

						@if(count($list ?? []) == 0)
							<tr>
								<td colspan="8" class="text-center">{{__('There is no coupons yet')}}</td>
							</tr>
						@endif

						</tbody>
					</table>
				</div>
            </div>
        </div>
    </div>


	<!-- Adding Coupon Modal -->
	<div class="modal fade" id="addingModal" tabindex="-1" role="dialog" aria-labelledby="addingModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addingModalLabel">{{__('Add New Coupon')}}</h5>
			</div>
			<form method="POST" action="{{route('dashboard.admin.coupons.add')}}">
				@csrf
				<div class="modal-body">
					<div class="mb-[15px]">
						<label class="form-label">{{__('Name')}}</label>
						<input type="text" class="form-control" name="name" value="{{old('name')}}" required>
					</div>

					<div class="row mb-[15px]">
						<div class="col-6">
							<div class="mb-[15px]">
								<label class="form-label">{{__('Discount')}} (%)</label>
								<input type="number" class="form-control" name="discount"  value="{{old('discount')}}" required min="0" max="99" step="0.01">
							</div>
						</div>
						<div class="col-6">
							<div class="mb-[15px]">
								<label class="form-label">{{__('Limit')}}</label>
								<input type="number" class="form-control" name="limit" value="{{old('limit')}}" placeholder="{{ __('Enter -1 for unlimited usage.') }}" min="-1" required>
							</div>
						</div>
					</div>

					<div class="row mb-[15px]">
						<label class="form-label">{{__('Code')}}</label>
						<label class="form-selectgroup-item-image-gen col-6">
							<input type="radio" name="code" value="auto" class="form-selectgroup-input" checked/>
							<h3 class="form-selectgroup-label rounded dark:!text-white">{{__('Auto Generate')}}</h3>
						</label>
						<label class="form-selectgroup-item-image-gen col-6">
							<input type="radio" name="code" value="manual" class="form-selectgroup-input" />
							<h3 class="form-selectgroup-label rounded dark:!text-white">{{__('Manual Generate')}}</h3>
						</label>
					</div>

					<div class="box hidden">
						<div class="mb-[15px]">
							<input type="text" class="form-control" name="codeInput" onkeydown="return (event.key !== ' ')" oninput="this.value = this.value.toUpperCase()" maxlength="7">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
					@if($app_is_demo)
						<button type="button"  onclick="return toastr.info('This feature is disabled in Demo version.')" class="btn btn-primary">{{__('Save changes')}}</button>
					@else
						<button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
					@endif
				</div>
			</form>
		</div>
		</div>
	</div>

	<!-- Editing Coupon Modal -->
	<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editModalLabel">{{ __('Edit Coupon') }} - [<span id="Ecode"></span>]</h5>
				</div>
				<form id="editForm" method="post">
					<div class="modal-body">
						@csrf
						<div class="mb-[15px]">
							<label class="form-label">{{__('Name')}}</label>
							<input type="text" class="form-control" name="name" id="Ename" required>
						</div>

						<div class="row mb-[15px]">
							<div class="col-6">
								<div class="mb-[15px]">
									<label class="form-label">{{__('Discount')}} (%)</label>
									<input type="number" class="form-control" name="discount" id="Ediscount" required min="0" max="99" step="0.01">
								</div>
							</div>
							<div class="col-6">
								<div class="mb-[15px]">
									<label class="form-label">{{__('Limit')}}</label>
									<input type="number" class="form-control" name="limit" id="Elimit" placeholder="{{ __('Enter -1 for unlimited usage.') }}" min="-1" required>
								</div>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
						<button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>


@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function(){
		'use strict';

        $('input[type="radio"]').click(function(){
            if($(this).attr("value")=="manual"){
                $(".box").show();
            }
			else{
				$(".box").hide();
			}
        });


		const radioAuto = document.querySelector('input[name="code"][value="auto"]');
		const radioManual = document.querySelector('input[name="code"][value="manual"]');
		const codeInput = document.querySelector('input[name="codeInput"]');

		radioAuto.addEventListener('change', () => {
			codeInput.removeAttribute('required');
		});

		radioManual.addEventListener('change', () => {
			codeInput.setAttribute('required', 'required');
		});

    });
</script>
<script>
    $('.edit-button').on('click', function () {
        var rowId = $(this).data('row-id');
        var name = $(this).data('row-name');
        var discount = $(this).data('row-discount');
        var limit = $(this).data('row-limit');
        var code = $(this).data('row-code');

        $('#Ename').val(name);
        $('#Ediscount').val(discount);
        $('#Elimit').val(limit);
        $('#Ecode').text(code);

      	var editForm = $('#editForm');
        var actionUrl = "{{ route('dashboard.admin.coupons.edit', ':id') }}";
        actionUrl = actionUrl.replace(':id', rowId);
        editForm.attr('action', actionUrl);
    });
</script>

@endsection
