<script>
    let paymentData = [{
            id: 1,
            method: 'Cash',
            selected: 'false'
        },
        {
            id: 2,
            method: 'Online-Payment',
            selected: 'false'
        },
        {
            id: 3,
            method: 'Transfer',
            selected: 'false',
            banks: [{
                    id: 1,
                    bank: 'Bank BCA',
                    remark: 'bank-bca',
                    bankOwner: '',
                    bankAccountNumber: '',
                    selected: 'false'
                },
                {
                    id: 2,
                    bank: 'Bank Mandiri',
                    remark: 'bank-mandiri',
                    bankOwner: '',
                    bankAccountNumber: '',
                    selected: 'false'
                },
                {
                    id: 3,
                    bank: 'Bank BNI',
                    remark: 'bank-bni',
                    bankOwner: '',
                    bankAccountNumber: '',
                    selected: 'false'
                },
                {
                    id: 4,
                    bank: 'Bank BRI',
                    remark: 'bank-bri',
                    bankOwner: '',
                    bankAccountNumber: '',
                    selected: 'false'
                },
                {
                    id: 5,
                    bank: 'Bank Lain',
                    remark: 'bank-lain',
                    bankOwner: '',
                    bankAccountNumber: '',
                    selected: 'false'
                },
                // Add more default banks as needed
            ]
        },
        {
            id: 4,
            method: 'COD',
            selected: 'false'
        },
        {
            id: 5,
            method: 'Marketplace',
            selected: 'false'
        },
        {
            id: 6,
            method: 'Piutang',
            selected: 'false'
        },
        {
            id: 7,
            method: 'QRIS',
            selected: 'false'
        }
    ];
</script>
