@extends('panel.layout.app')
@section('title', __('Mobile Subscriptions and Packs'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl( route('dashboard.index') ) }}" class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
                        </svg>
                        {{__('Back to dashboard')}}
                    </a>
                    <h2 class="page-title mb-2">
                        {{__('Manage Mobile Subscription and Token Packs')}}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    @if($settings_two->liquid_license_type != "Extended License")
        <div class="container-xl">
            <div class="bg-red-100 text-red-600 rounded-xl !p-3 dark:bg-orange-600/20 dark:text-orange-200 top-40 left-0 right-0 mx-auto text-center">
                {{__('To access this page, you should upgrade to Extended License.') }}  <a href="{{route('dashboard.admin.license.index')}}"><u> {{__('Upgrade License') }}</u></a>
            </div>
        </div>
    @else
        <div class="page-body pt-6">
            <div class="container-xl flex flex-wrap sm:flex-row gap-3">

                <div class="card flex grow">
                    <div id="table-default" class="card-table table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Name')}}&nbsp;/&nbsp;{{__('Type')}}</th>
                                <th>{{__('RC Package Id')}}</th>
                                <th>{{__('RC Entitlement Id')}}</th>
                                <th>{{__('RC Apple Id')}}</th>
                                <th>{{__('RC Google Id')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="table-tbody align-middle text-heading">
                            @foreach($plans as $entry)
                                <tr data-row-id="{{ $entry->id }}" class="transition duration-300 ease-in-out editable-row">
                                    <td class="sort-name">
                                        @if($entry->active == 1)
                                            <div class="badge bg-success">{{__('Active')}}</div>
                                        @else
                                            <div class="badge bg-danger">{{__('Passive')}}</div>
                                        @endif
                                    </td>
                                    @php
                                        $entryType = $entry->type == 'prepaid' ? __('Token Pack') : __('Subscription');
                                    @endphp
                                    <td class="sort-remaining-words">
                                        <div class="flex flex-col">
                                            <div>{{$entry->name}}</div>
                                            <div class="text-xs text-gray-400">{{ $entry->type == 'prepaid' ? __('Token Pack') : __('Subscription') }}</div>
                                        </div> 
                                    </td>

                                    <td class="sort-remaining-images">
                                        @php
                                            $foundRevenueCatProduct = false;
                                            $revenueCatProduct = "";
                                        @endphp
                                        @foreach ($entry->gateway_products as $prd)
                                            @if ($prd->gateway_code == 'revenuecat')
                                                @if($prd->product_id != null)
                                                    {{$prd->product_id}}
                                                    @php
                                                        $foundRevenueCatProduct = true;
                                                        $revenueCatProduct = $prd->product_id;
                                                    @endphp
                                                @else
                                                    {{__('Not Set')}}
                                                @endif
                                            @endif
                                        @endforeach
                                        @if (!$foundRevenueCatProduct)
                                            {{__('Not Set')}}
                                        @endif
                                    </td>

                                    <td class="sort-remaining-images">
                                        @php
                                            $foundRevenueCatPrice = false;
                                            $revenueCatPrice = "";
                                        @endphp
                                        @foreach ($entry->gateway_products as $prd)
                                            @if ($prd->gateway_code == 'revenuecat')
                                                @if($prd->price_id != null)
                                                    {{$prd->price_id}}
                                                    @php
                                                        $foundRevenueCatPrice = true;
                                                        $revenueCatPrice = $prd->price_id;
                                                    @endphp
                                                @else
                                                    {{__('Not Set')}}
                                                @endif
                                            @endif
                                        @endforeach
                                        @if (!$foundRevenueCatPrice)
                                            {{__('Not Set')}}
                                        @endif
                                    </td>

                                    <td class="sort-remaining-images">
                                        @php
                                            $foundRevenueCatApple = false;
                                            $revenueCatApple = "";
                                        @endphp
                                        @foreach ($entry->revenuecat_products as $prd)
                                            @if($prd->apple_id != null)
                                                {{$prd->apple_id}}
                                                @php
                                                    $foundRevenueCatApple = true;
                                                    $revenueCatApple = $prd->apple_id;
                                                @endphp
                                            @else
                                                {{__('Not Set')}}
                                            @endif
                                        @endforeach
                                        @if (!$foundRevenueCatApple)
                                            {{__('Not Set')}}
                                        @endif
                                    </td>

                                    <td class="sort-remaining-images">
                                        @php
                                            $foundRevenueCatGoogle = false;
                                            $revenueCatGoogle = "";
                                        @endphp
                                        @foreach ($entry->revenuecat_products as $prd)
                                            @if($prd->google_id != null)
                                                {{$prd->google_id}}
                                                @php
                                                    $foundRevenueCatGoogle = true;
                                                    $revenueCatGoogle = $prd->google_id;
                                                @endphp
                                            @else
                                                {{__('Not Set')}}
                                            @endif
                                        @endforeach
                                        @if (!$foundRevenueCatGoogle)
                                            {{__('Not Set')}}
                                        @endif
                                    </td>

                                    
                                    <td class="whitespace-nowrap">
                                        @if($app_is_demo)
                                        <a onclick="return toastr.info('This feature is disabled in Demo version.')"
                                            class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white" title="Edit">
                                            <svg width="13" height="12" viewBox="0 0 15 14" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.71875 2.43988L11.9688 5.58995M10.75 11.4963H14M4.25 13.0714L12.7812 4.80248C12.9946 4.59564 13.1639 4.35009 13.2794 4.07984C13.3949 3.8096 13.4543 3.51995 13.4543 3.22744C13.4543 2.93493 13.3949 2.64528 13.2794 2.37504C13.1639 2.10479 12.9946 1.85924 12.7812 1.6524C12.5679 1.44557 12.3145 1.28149 12.0357 1.16955C11.7569 1.05761 11.458 1 11.1562 1C10.8545 1 10.5556 1.05761 10.2768 1.16955C9.99799 1.28149 9.74465 1.44557 9.53125 1.6524L1 9.92135V13.0714H4.25Z" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        @else
                                            <button class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white edit-btn" title="Edit" type="button"
                                                onclick="fillEditableFields('{{ $entry->id }}', '{{$entry->name}}', '{{ $entryType }}', '{{$revenueCatProduct}}', '{{$revenueCatPrice}}', '{{$revenueCatGoogle}}', '{{$revenueCatApple}}')"
                                            >
                                                <svg width="13" height="12" viewBox="0 0 15 14" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.71875 2.43988L11.9688 5.58995M10.75 11.4963H14M4.25 13.0714L12.7812 4.80248C12.9946 4.59564 13.1639 4.35009 13.2794 4.07984C13.3949 3.8096 13.4543 3.51995 13.4543 3.22744C13.4543 2.93493 13.3949 2.64528 13.2794 2.37504C13.1639 2.10479 12.9946 1.85924 12.7812 1.6524C12.5679 1.44557 12.3145 1.28149 12.0357 1.16955C11.7569 1.05761 11.458 1 11.1562 1C10.8545 1 10.5556 1.05761 10.2768 1.16955C9.99799 1.28149 9.74465 1.44557 9.53125 1.6524L1 9.92135V13.0714H4.25Z" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </td>

                                    

                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

                <form action="" method="POST" >@csrf
                    <div class="w-72 flex flex-col gap-3" >
                        <div class="card w-72 flex flex-col px-3">
                            <input type="hidden" name="plan_id" id="plan_id"/>
                            <input type="hidden" name="plan_name_label" id="plan_name_input"/>
                            <input type="hidden" name="plan_type_label" id="plan_type_input"/>
                            <div class="h-12 w-full flex items-center text-center border-b fw-bold">{{__('Update Plan')}}</div>
                            <div class="flex flex-row"><label class="form-label">{{__('Plan Name')}} : </label><div id="plan_name_label" class="pl-1 fw-bold"></div></div>
                            <div class="flex flex-row"><label class="form-label">{{__('Plan Type')}} : </label><div id="plan_type_label" class="pl-1 fw-bold"></div></div>
                            
                            <label class="form-label">{{__('RevenueCat Package Id')}} : </label>
                            <div class="flex items-center h-12">
                                <input type="text" class="form-control" id="revenuecat_package_id" name="revenuecat_package_id" required placeholder="{{__('Please enter Package Identifier')}}">
                            </div>
                            <label class="form-label mt-2">{{__('RevenueCat Entitlement Id')}} : </label>
                            <div class="flex items-center h-12">
                                <input type="text" class="form-control" id="revenuecat_entitlement_id" name="revenuecat_entitlement_id" required placeholder="{{__('Please enter Entitlement Identifier')}}">
                            </div>
                            <label class="form-label mt-2">{{__('RevenueCat Apple Product Id')}} : </label>
                            <div class="flex items-center h-12">
                                <input type="text" class="form-control" id="revenuecat_apple_id" name="revenuecat_apple_id" required placeholder="{{__('Please enter Product Identifier')}}">
                            </div>
                            <label class="form-label mt-2">{{__('RevenueCat Google Product Id')}} : </label>
                            <div class="flex items-center h-12">
                                <input type="text" class="form-control" id="revenuecat_google_id" name="revenuecat_google_id" required placeholder="{{__('Please enter Product Identifier')}}">
                            </div>
                            
                            <div class="flex items-center h-12 mb-2"><button type="submit" class="btn btn-primary h-8 w-64">Save</button></div>
                        </div>
                        <div class="card w-72 p-3">
                            <p class="fw-bold">{{__('Important:')}}</p>
                            <p>{{__('In RevenueCat dashboard, create only one instance of offerings and set it as default. Mobile app checks for default offering and searches given package and entitlement ids in there.')}}</p>
                            <p>{{__('Also, please do not set both identifiers identical. For instance, you can use _ent and _pac at the end of ids.')}}</p>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    @endif

    <script>
        function fillEditableFields(planId, planName, planType, revenueCatPackageId, revenueCatEntitlementId, revenueCatGoogleId, revenueCatAppleId) {
            document.getElementById('plan_id').value = planId;
            document.getElementById('plan_name_label').innerText = planName;
            document.getElementById('plan_name_input').value = planName;
            document.getElementById('plan_type_label').innerText = planType;
            document.getElementById('plan_type_input').value = planType;
            document.getElementById('revenuecat_package_id').value = revenueCatPackageId;
            document.getElementById('revenuecat_entitlement_id').value = revenueCatEntitlementId;
            document.getElementById('revenuecat_google_id').value = revenueCatGoogleId;
            document.getElementById('revenuecat_apple_id').value = revenueCatAppleId;
        }
    </script>

@endsection
