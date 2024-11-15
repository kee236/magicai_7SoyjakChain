@extends('panel.layout.app')
@section('title', isset($subscription) ? __('Edit') : __('Create').''.__('Subscription'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
					<div class="hstack gap-1">
						<a href="{{ LaravelLocalization::localizeUrl( route('dashboard.index') ) }}" class="page-pretitle flex items-center">
							<svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
								<path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
							</svg>
							{{__('Back to dashboard')}}
						</a>
						<a href="{{route('dashboard.admin.finance.plans.index')}}" class="page-pretitle flex items-center">
                            / {{__('Back to Manage Plans')}}
                        </a>
                    </div>
                    <h2 class="page-title mb-2">
                       {{isset($subscription) ? __('Edit') : __('Create')}} {{__('Subscription')}}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
			<div class="row">
				<div class="col-md-12 ">
					<form id="item_edit_form" onsubmit="return subscriptionSave({{$subscription->id ?? null}});" action="">
						<div class="row">
							<div class="col-md-6 col-xl-6">
								@if($isActiveGateway == 0)
								<div class="row">
									<div class="col-md-12 mb-3">
                                        <div class="bg-amber-100 text-amber-600 rounded-xl !p-3 !mt-2 dark:bg-amber-600/20 dark:text-amber-200  mb-2">
                                            <svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
                                            {{ __('Please enable at least one gateway!') }}
                                        </div>
                                    </div>
								</div>
								@endif
									@if(\App\Models\Gateways::query()->where('code', 'coingate')->count())
										<div class="bg-blue-100 text-blue-600 rounded-xl !p-3 dark:bg-blue-600/20 dark:text-blue-200 mb-2">
											<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
											{{__("Congate subscriptions require you to set up cron jobs on your server. You can find detailed instructions in ")}} <a class="text-dark" href="https://magicaidocs.liquid-themes.com/how-to-configure-cron-jobs-on-cpanel/">{{ __('the documentation.') }}</a>
										</div>
									@endif
								<div class="row">
									<div class="col-md-12">
										<div class="mb-3">
											<div id="cron-alert"  role="alert" style="display: none;">
												<div class="bg-blue-100 text-blue-600 rounded-xl !p-3 !mt-2 dark:bg-blue-600/20 dark:text-blue-200">
													<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
													{!! __('<b>Free</b> and <b>Lifetime</b> plans require you to set up cron jobs on your server and <b>Trial Days</b> cannot be set for these plans. You can find detailed instructions in the') !!} <a style="text-decoration: underline" target="_blank" href="https://magicaidocs.liquid-themes.com/how-to-configure-cron-jobs-on-cpanel">{{__('documentation')}}</a>.
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-12">
										<div class="mb-3">
											<label class="form-label">{{__('Plan Name')}}</label>
											<input type="text" class="form-control" id="name" name="name" value="{{isset($subscription) ? $subscription->name : null}}" required>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">{{__('Price')}}</label>
											<input type="number" class="form-control" id="price" min="0" step="0.1" name="price" value="{{isset($subscription) ? $subscription->price : null}}" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">{{__('Trial Days')}}
												<x-info-tooltip text="{{__('Trial days cannot be set for free and lifetime plans.')}}" />
											</label>
											<select class="form-select" id="trial_days" name="trial_days" value="{{isset($subscription) ? $subscription->trial_days : 0}}" required>
												@php($selectedValue = isset($subscription) ? (isset($subscription->trial_days) ? $subscription->trial_days : '0' ) : '0' )
												<option value="7">7 Days</option>
												<option value="14">14 Days</option>
												<option value="0" {{ $selectedValue == 0 ? 'selected' : '' }}>0 - No Trial</option>
												<option value="1" {{ $selectedValue == 1 ? 'selected' : '' }}>1 Day</option>
												@for ($i = 2; $i <= 28; $i++)
												<option value="{{ $i }}" {{ $selectedValue == $i ? 'selected' : '' }}>{{ $i }} Days</option>
												@endfor
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<div class="form-label">{{__('Renewal Type')}}</div>
											<select class="form-select" id="frequency" name="frequency" required>
                                                @if(isset($subscription))
                                                    <option value="monthly" {{$subscription->frequency == 'monthly' ? 'selected' : ''}}>{{__('Monthly')}}</option>
                                                    <option value="yearly" {{$subscription->frequency == 'yearly' ? 'selected' : ''}}>{{__('Yearly')}}</option>
                                                    <option value="lifetime_monthly" {{$subscription->frequency == 'lifetime_monthly' ? 'selected' : ''}}>{{__('Lifetime - Monthly Renewal')}}</option>
                                                    <option value="lifetime_yearly" {{$subscription->frequency == 'lifetime_yearly' ? 'selected' : ''}}>{{__('Lifetime - Yearly Renewal')}}</option>
                                                @else
                                                    <option value="monthly">{{__('Monthly')}}</option>
                                                    <option value="yearly">{{__('Yearly')}}</option>
                                                    <option value="lifetime_monthly">{{__('Lifetime - Monthly Renewal')}}</option>
                                                    <option value="lifetime_yearly">{{__('Lifetime - Yearly Renewal')}}</option>
                                                @endif
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<div class="form-label">{{__('Featured Plan')}}</div>
											<select class="form-select" id="is_featured" name="is_featured" required>
                                                @if(isset($subscription))
                                                    <option value="1" {{$subscription->is_featured == 1 ? 'selected' : ''}}>{{__('Yes')}}</option>
                                                    <option value="0" {{$subscription->is_featured == 0 ? 'selected' : ''}}>{{__('No')}}</option>
                                                @else
                                                    <option value="0">{{__('No')}}</option>
                                                    <option value="1">{{__('Yes')}}</option>
                                                @endif
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-12">
										<div class="mb-3">
											<div class="d-flex justify-content-between  align-items-center mb-1">
												<div class="d-flex">
													<label class="form-label">{{__('Is team plan')}}</label>
													<x-info-tooltip text="{{__('A team will be create in this plan')}}" />
												</div>

												<label class="form-check form-switch">
													<input class="form-check-input" type="checkbox" id="is_team_plan" name="is_team_plan" @if((isset($subscription) && $subscription?->is_team_plan) || !isset($subscription)) checked @endif>
												</label>
											</div>
											<input type="number" name="plan_allow_seat" id="plan_allow_seat" class="form-control" placeholder="{{__('Number of Seats')}}"  value="{{isset($subscription) ? $subscription->plan_allow_seat : null}}"/>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<div class="d-flex justify-content-between  align-items-center mb-1">
												<div class="d-flex">
													<label class="form-label">{{__('Total Words')}}</label>
													<x-info-tooltip text="{{__('ON to display word count in plans')}}" />
												</div>


												<label class="form-check form-switch">
													<input class="form-check-input" type="checkbox" id="display_word" name="display_word" @if((isset($subscription) && $subscription?->display_word_count) || !isset($subscription)) checked @endif>
												</label>
											</div>
											<input type="number" name="total_words" id="total_words" class="form-control"  value="{{isset($subscription) ? $subscription->total_words : null}}"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<div class="d-flex justify-content-between  align-items-center mb-1">
												<div class="d-flex">
													<label class="form-label">{{__('Total Images')}}</label>
													<x-info-tooltip text="{{__('ON to display image count in plans')}}" />
												</div>

												<label class="form-check form-switch">
													<input class="form-check-input" type="checkbox" id="display_img"  name="display_img" @if((isset($subscription) && $subscription?->display_imag_count) || !isset($subscription)) checked @endif>
												</label>
											</div>
											<input type="number" name="total_images" id="total_images" class="form-control"  value="{{isset($subscription) ? $subscription->total_images : null}}"/>
										</div>
									</div>
									
                                    <div class="col-md-12 mb-3">
                                        <div class="bg-blue-100 text-blue-600 rounded-xl !p-3 !mt-2 dark:bg-blue-600/20 dark:text-blue-200">
                                            <svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
                                            {{ __('Enter -1 for unlimited usage.') }}
                                        </div>
                                    </div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<div class="form-label">{{__('AI Name')}}</div>
											<select class="form-select" id="ai_name" name="ai_name" required>
												@if(isset($subscription))
													<option value="text-davinci-003"{{$subscription->ai_name == 'text-davinci-003' ? 'selected' : null}}>Davinci</option>
													<option value="gpt-3.5-turbo"{{$subscription->ai_name == 'gpt-3.5-turbo' ? 'selected' : null}}>ChatGPT 3.5</option>
													<option value="gpt-3.5-turbo-16k" {{$subscription->ai_name == 'gpt-3.5-turbo-16k' ? 'selected' : null}}>{{__('ChatGTP (3.5-turbo-16k)')}}</option>
													<option value="gpt-4"{{$subscription->ai_name == 'gpt-4' ? 'selected' : null}}>ChatGPT 4</option>
													<option value="gpt-4-1106-preview" {{$subscription->ai_name == 'gpt-4-1106-preview' ? 'selected' : null}}>{{__('GPT-4 Turbo (Updated Knowleddge cutoff of April 2023, 128k)')}}</option>
												@else
                                                    <option value="text-davinci-003">{{__('Davinci')}}</option>
                                                    <option value="gpt-3.5-turbo">{{__('ChatGPT 3.5')}}</option>
													<option value="gpt-3.5-turbo-16k">{{__('ChatGTP (3.5-turbo-16k)')}}</option>
                                                    <option value="gpt-4" selected>{{__('ChatGPT 4')}}</option>
													<option value="gpt-4-1106-preview">{{__('GPT-4 Turbo (Updated Knowleddge cutoff of April 2023, 128k)')}}</option>
                                                @endif
											</select>
											
										</div>
									</div>
									
									<div class="col-md-6 hidden">
										<div class="mb-3">
											<label class="form-label">{{__('Max Tokens')}}</label>
											<input type="number" name="max_tokens" id="max_tokens" class="form-control"  value="{{isset($subscription) ? $subscription->max_tokens : null}}"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<div class="form-label">{{__('Can Create AI Images')}}</div>
											<select class="form-select" id="can_create_ai_images" name="can_create_ai_images" required>
												@if(isset($subscription))
                                                    <option value="1" {{$subscription->can_create_ai_images == 1 ? 'selected' : ''}}>{{__('Yes')}}</option>
                                                    <option value="0" {{$subscription->can_create_ai_images == 0 ? 'selected' : ''}}>{{__('No')}}</option>
                                                @else
												<option value="1">{{__('Yes')}}</option>
												<option value="0">{{__('No')}}</option>
                                                @endif
											</select>
										</div>
									</div>

									<div class="col-md-12">
										<div class="mb-3 bg-blue-100 text-blue-600 rounded-xl !p-3 dark:bg-blue-600/20 dark:text-blue-200">
											<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
											{{__('Please note GPT-4 is not working with every api_key. You have to have an api key which can work with GPT-4.')}}
											<br>
											{{__('Also please note that Chat models works with ChatGPT and GPT-4 models. So if you choose below it will automatically use ChatGPT.')}}
										</div>
									</div>

{{--									<input type="hidden" name="plan_type" value="All">--}}
{{--									<input type="hidden" name="features" value="">--}}

									<div class="col-md-12">
										<div class="mb-3">
											<div class="form-label">{{__('Template Access')}}</div>
											<select class="form-select" id="plan_type" name="plan_type" required>
												@if(isset($subscription))
                                                    <option value="All" {{$subscription->plan_type == 'All' ? 'selected' : ''}}>{{__('All')}}</option>
                                                    <option value="Premium" {{$subscription->plan_type == 'Premium' ? 'selected' : ''}}>{{__('Premium')}}</option>
                                                    <option value="Regular" {{$subscription->plan_type == 'Regular' ? 'selected' : ''}}>{{__('Regular')}}</option>
                                                @else
												<option value="All">{{__('All')}}</option>
												<option value="Premium">{{__('Premium')}}</option>
												<option value="Regular">{{__('Regular')}}</option>
                                                @endif
											</select>
										</div>
									</div>

									<div class="col-md-12">
										<div class="mb-3">
											<div class="form-label">{{__('Features (Comma Seperated)')}}</div>
											<textarea class="form-control" name="features" id="features" cols="30" rows="10" required>{{isset($subscription) ? $subscription->features : null}}</textarea>
										</div>
									</div>

									<div class="col-md-12 mb-3">
										<!-- WHAT HAPPENS WHEN YOU SAVE -->
										@if(isset($subscription))
											<div class="bg-blue-100 text-blue-600 rounded-xl !p-3 mt-2 dark:bg-blue-600/20 dark:text-blue-200">
												<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
												{{__("What happens when you save?")}}
												<ul class="ml-2 mt-2">
													<li>{{__('Save your settings.')}}</li>
													<li>{{__('Check all subscriptions for this plan.')}}</li>
													<li>{{__('Remove all products and prices defined before for old settings.')}}</li>
													<li>{{__('Cancel all old subscriptions. Acquired amounts do not reset.')}}</li>
													<li>{{__('Generate new product definitions in your gateway accounts.')}}</li>
													<li>{{__('Generate new price definitions in your gateway accounts.')}}</li>
													<!-- <li>{{__('')}}</li> -->
												</ul>
												<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
												{{__('This process will take time. So, please be patient and wait until success message appears.')}}
											</div>
										@else
											<div class="bg-blue-100 text-blue-600 rounded-xl !p-3 mt-3 dark:bg-blue-600/20 dark:text-blue-200">
												<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
												{{__("What happens when you save?")}}
												<ul class="ml-2 mt-2">
													<li>{{__('Save your settings.')}}</li>
													<li>{{__('Generate new product definitions in your gateway accounts.')}}</li>
													<li>{{__('Generate new price definitions in your gateway accounts.')}}</li>
												</ul>
												<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
												{{__('This process will take time. So, please be patient and wait until success message appears.')}}
											</div>
										@endif
									</div>

								</div>


								@if($isActiveGateway == 0)
								<div class="row">
									<div class="col-md-12 mb-3">
                                        <div class="bg-amber-100 text-amber-600 rounded-xl !p-3 !mt-2 dark:bg-amber-600/20 dark:text-amber-200">
                                            <svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
                                            {{ __('Please enable at least one gateway!') }}
                                        </div>
                                    </div>
								</div>
								@else
								<button form="item_edit_form" id="item_edit_button" class="btn btn-primary w-100">
									{{__('Save')}}
								</button>
								@endif
								
							</div>
							<div class="col-md-6 col-xl-6">
								<div class="row">
									<div class="col-md-12 mb-4">
										<div class="card">
											<div class="card-body d-flex justify-between">
												<div>
													<span class="inline-flex items-center justify-center !w-6 !h-6 -mt-6 relative peer">
														<svg class="fill-[#D8DBE5]" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M6.66732 0.333313C10.3473 0.333313 13.334 3.31998 13.334 6.99998C13.334 10.68 10.3473 13.6666 6.66732 13.6666C2.98732 13.6666 0.000652313 10.68 0.000652313 6.99998C0.000652313 3.31998 2.98732 0.333313 6.66732 0.333313ZM6.00065 10.3333H7.33398V6.33331H6.00065V10.3333ZM6.00065 4.99998H7.33398V3.66665H6.00065V4.99998Z"/>
														</svg>
													</span>
													<b>Choose Available Templates</b>
												</div>
												<span onclick="selectAll('{{ $openAiList->count() }}')" id="select_text_id" style="cursor:pointer;" class="text-black"><b>@lang('Select All')</b></span>
												<input type="hidden" id="pages_total_count" value="{{ $openAiList->count() }}">
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									@foreach($openAiList->groupBy('filters') as $key => $items)
										<div class="col-md-12">
											<div class="d-flex justify-content-between  align-items-center {{ $key == 'blog' ? '' : 'mt-4' }}">
												<label class="form-label">{{ ucfirst($key) }}</label>
												<label class="form-check form-switch form-switch-sm">
													<input class="form-check-input" style="flex-shrink: 0; width: 1.5rem;height: 1rem; border-radius: 1.5rem; margin-left: 13px;background-size: 0.8rem;" type="checkbox" id="{{ $key }}"  data-filter="check" name="display_word">
												</label>
											</div>
											<div class="row">
												@foreach($items as $keyItem => $item)
													<div class="col-md-4 {{ $keyItem > 2 ? 'mt-2' : 'mt-1' }}">
														<div class="border p-3 rounded">
															<div class="form-check mb-0 pb-0">
																<input class="form-check-input checked-item" {{ in_array($item->slug, $selectedAiList) ? 'checked': '' }} data-filter="{{ $key }}" type="checkbox" name="openaiItems[]" value="{{ $item->slug }}" id="flex_check_{{ $item->id }}">
																<label class="form-check-label" for="flex_check_{{ $item->id }}">
																	<small>{{ $item->title }}</small>
																</label>
															</div>
														</div>
													</div>
												@endforeach
											</div>
										</div>
									@endforeach
								</div>
							</div>
						</div>
					</form>



					@if(isset($generatedData))
					<div class="mt-5">
						<h4 class="">{{ __('These values are generated for you') }}</h4>
						<table class="table table-sm table-responsive table-striped">
							<thead>
								<th>{{ __('Gateway') }}</th>
								<th>{{ __('Product ID') }}</th>
								<th>{{ __('Plan / Price ID') }}</th>
							</thead>
							<tbody>
								@foreach($generatedData as $data)
								<tr>
									<td>{{ $data->gateway_title }}</td>
									<td>{{ $data->product_id }}</td>
									<td>{{ $data->price_id }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@endif
				</div>
			</div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/js/panel/finance.js"></script>
	<script>
		function selectAll(total_count)
		{
			let count = $('.checked-item:checked').length;

			let pages_total_count = $('#pages_total_count').val();

			if(count == pages_total_count) {
				$('.checked-item').prop('checked', false);
				$('#select_text_id').text('Select All');
			} else {
				$('#select_text_id').text('Unselect All');
				$('.checked-item').prop('checked', true);
			}
		}

		$('[data-filter="check"]').on('change', function() {

			if($(this).is(':checked')) {
				$('[data-filter="'+$(this).attr('id')+'"]').prop('checked', true);
			} else {
				$('[data-filter="'+$(this).attr('id')+'"]').prop('checked', false);
			}

		});

        document.addEventListener('DOMContentLoaded', function () {

			let count = $('.checked-item:checked').length;

			let pages_total_count = $('#pages_total_count').val();

			if(pages_total_count == count)
			{
				$('#select_text_id').text('Unselect All');
			}

			$('.checked-item').on('change', function() {
				let count = $('.checked-item:checked').length;

				let pages_total_count = $('#pages_total_count').val();

				if(count == pages_total_count) {
					$('#select_text_id').text('Unselect All');
				} else {
					$('#select_text_id').text('Select All');
				}
			});

            var priceInput = document.getElementById('price');
			var frequencySelect = document.getElementById('frequency');
            var cronAlert = document.getElementById('cron-alert');
			
			function updateAlertAndBorder() {
                var priceValue = parseFloat(priceInput.value);
                var selectedFrequency = frequencySelect.value;

                if (priceValue === 0 || (selectedFrequency === 'lifetime_monthly' || selectedFrequency === 'lifetime_yearly')) {
                    cronAlert.style.display = 'block';
                    priceInput.classList.add('border-primary');
					frequencySelect.classList.add('border-primary');
                } else {
                    cronAlert.style.display = 'none';
                    priceInput.classList.remove('border-primary');
					frequencySelect.classList.remove('border-primary');
                }
            }

			priceInput.addEventListener('input', updateAlertAndBorder);
            frequencySelect.addEventListener('change', updateAlertAndBorder);

            updateAlertAndBorder();
        });
    </script>
@endsection
