<?php

sleep(1);

$data = [
    [
        [
            'id' => 1,
            'name' => 'Volta Redonda'
        ],
        [
            'id' => 2,
            'name' => 'Barra Mansa'
        ],
    ],
    [
        [
            'id' => 3,
            'name' => 'Resende'
        ],
        [
            'id' => 4,
            'name' => 'Barra do Piraí'
        ],
    ]
];

echo json_encode($data[$_GET['value']]);

?>