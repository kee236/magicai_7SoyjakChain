<div class="accordion mb-3" id="accordionExample">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            {{__('Have a coupon?')}}
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
            <div class="accordion-body">
              
                <div class="flex items-center relative">
                    <span class="flex items-center h-full absolute top-0 !start-0 ps-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_0_8387)">
                            <path d="M12.8398 18H16.2684" stroke="#3E3E3E" stroke-width="1.71429" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19.6973 18H23.1258" stroke="#3E3E3E" stroke-width="1.71429" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.75488 8.45142L13.6977 14.16" stroke="#3E3E3E" stroke-width="1.71429" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.71456 8.5714C5.73754 8.5714 6.71862 8.16503 7.44198 7.44167C8.16533 6.71832 8.57171 5.73724 8.57171 4.71426C8.57171 3.69128 8.16533 2.7102 7.44198 1.98685C6.71862 1.26349 5.73754 0.857117 4.71456 0.857117C3.69159 0.857117 2.71051 1.26349 1.98715 1.98685C1.2638 2.7102 0.857422 3.69128 0.857422 4.71426C0.857422 5.73724 1.2638 6.71832 1.98715 7.44167C2.71051 8.16503 3.69159 8.5714 4.71456 8.5714Z" stroke="#3E3E3E" stroke-width="1.71429" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.75488 15.5485L23.1435 4.3714" stroke="#3E3E3E" stroke-width="1.71429" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.71456 23.1428C5.73754 23.1428 6.71862 22.7364 7.44198 22.0131C8.16533 21.2897 8.57171 20.3086 8.57171 19.2857C8.57171 18.2627 8.16533 17.2816 7.44198 16.5583C6.71862 15.8349 5.73754 15.4285 4.71456 15.4285C3.69159 15.4285 2.71051 15.8349 1.98715 16.5583C1.2638 17.2816 0.857422 18.2627 0.857422 19.2857C0.857422 20.3086 1.2638 21.2897 1.98715 22.0131C2.71051 22.7364 3.69159 23.1428 4.71456 23.1428Z" stroke="#3E3E3E" stroke-width="1.71429" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_0_8387">
                            <rect width="24" height="24" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <input class="form-control h-10 ps-5 mx-3" type="text" name="code" placeholder="{{__('Coupon Code')}}">
                    <span class="flex items-center h-full absolute top-0 !end-0 pe-4">
                        <button onclick="applyCoupon();" class="btn btn-sm border-0 rounded-0 shadow-none p-1">
                            {{__('Apply')}} 
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-tabler icon-tabler-circle-arrow-right-filled m-1" width="15" height="15" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 2l.324 .005a10 10 0 1 1 -.648 0l.324 -.005zm.613 5.21a1 1 0 0 0 -1.32 1.497l2.291 2.293h-5.584l-.117 .007a1 1 0 0 0 .117 1.993h5.584l-2.291 2.293l-.083 .094a1 1 0 0 0 1.497 1.32l4 -4l.073 -.082l.064 -.089l.062 -.113l.044 -.11l.03 -.112l.017 -.126l.003 -.075l-.007 -.118l-.029 -.148l-.035 -.105l-.054 -.113l-.071 -.111a1.008 1.008 0 0 0 -.097 -.112l-4 -4z" stroke-width="0" fill="currentColor"></path>
                            </svg>
                        </button>
                    </span>
                </div>       
            </div>
        </div>
    </div>
</div>
<script>
function applyCoupon() {
    var couponCode = document.querySelector('input[name="code"]').value;
    // Check if the coupon code is not empty
    if (couponCode.trim() !== '') {
        // Make an AJAX request to the server-side endpoint
        fetch('/dashboard/coupons/validate-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ code: couponCode }),
        })
        .then(response => response.json())
        .then(data => {
            // Process the response data
            if (data.valid) {
                
            var currentURL = window.location.href;
            var couponCodeParam = encodeURIComponent(couponCode);

            // Check if the coupon code parameter already exists in the URL
            if (currentURL.includes('coupon=')) {
                // If it exists, replace the existing value
                currentURL = currentURL.replace(/coupon=([^&]+)/, 'coupon=' + couponCodeParam);
            } else {
                // If it doesn't exist, add it as a new parameter
                currentURL += (currentURL.includes('?') ? '&' : '?') + 'coupon=' + couponCodeParam;
            }

            // Redirect to the updated URL
            window.location.href = currentURL;

            } else {
                // Coupon is invalid, show an error message
                toastr.error("{{ __('Invalid coupon code. Please try again') }}");
            }
        })
        .catch(error => {
            // Handle any errors that occurred during the AJAX request
            console.error('Error:', error);
        });

        // Clear the input field after applying the coupon
        // document.querySelector('input[name="code"]').value = '';
    } else {
        // Display an error message if the coupon code is empty
        toastr.error("{{ __('Please enter a coupon code') }}");
    }
}

</script>