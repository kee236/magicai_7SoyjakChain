@php
    $statusArr = [
        'not-trained' => [
            'class' => 'secondary',
            'text' => 'Not Trained',
        ],
        'trained' => [
            'class' => 'success',
            'text' => 'Trained',
        ],
        'training' => [
            'class' => 'info',
            'text' => 'Training',
        ],
    ];
@endphp

<span class="badge bg-{{ data_get($statusArr, $status.'.class') ?: 'secondary' }}">{{ data_get($statusArr, $status.'.text') ?: 'Waiting' }}</span>