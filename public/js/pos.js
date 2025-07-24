const {
    createApp,
    ref,
    reactive
} = Vue;

createApp({
    setup() {
        const data = reactive({
            tables: [],
            products: [],
            value: {
                table: null,
                products: [],
            },
            modal: {
                product: null,
                variantGroupActive: [], // tempat simpan sementara ketika user memiliki varian group
                variantActive: null, // aktif ketika menambahkan catatan pada varian yang di mau di edit data nya
            }
        })

        const needOpenShift = ref(false)
        const methods = {
            init: () => {
                const apiUrl = `/v1/check-status-cashier`;

                axios.get(apiUrl)
                    .then(response => {
                        if (response.data.status === false) {
                            $('.shift-open-kasir').html(response.data.data.fullname)

                            $('#modalOpenShift').modal('show');
                            needOpenShift.value = true
                        } else {
                            $('#btn-close-shift').removeClass('d-none')
                        }
                        $('.product-content').removeClass('d-none')
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                    });
            },
            onShowingModalCategory: () => {
                $('#modalKategori').modal('show')

                $('.category-products').select2({
                    ajax: {
                        url: '/v1/product-categories',
                        dataType: 'json',
                        processResults: function (data) {
                            return {
                                results: data.data.map(item => ({
                                    id: item.id,
                                    text: item.name
                                }))
                            };
                        }
                    }
                }).on('change', function () {
                    // const selectedCategory = $(this).val();
                    const selectedData = $(this).select2('data');
                    if (selectedData.length > 0) {
                        const {
                            text,
                            id
                        } = selectedData[0];
                        $('.kategoriText').text(text)

                        axios.get('/v1/product', {
                            params: {
                                category_id: id
                            }
                        }).then(res => {
                            data.products = res.data.data.data
                            $('#modalKategori').modal('hide');
                        })
                    }
                });
            },
            onCloseShiftClick: () => {
                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah anda yakin untuk metutup kasir?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#375ed1",
                    cancelButtonColor: "#d33",
                    cancelButtonText: "Batal",
                    confirmButtonText: "Iya, Tutup Kasir!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('/v1/close-cashier')
                            .then(response => {
                                if (response.data.status) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.data.message,
                                        icon: 'success',
                                        confirmButtonText: 'Ok'
                                    }).then((sresult) => {
                                        if (sresult.isConfirmed) {
                                            if (!modalOpenShift) {
                                                modalOpenShift = new bootstrap
                                                    .Modal(
                                                        document.getElementById(
                                                            'modalOpenShift'));
                                            }

                                            modalOpenShift.show();
                                            $('#btn-close-shift').addClass(
                                                'd-none')
                                        }
                                    })
                                } else {
                                    Swal.fire({
                                        title: 'Fail!',
                                        text: response.data.message,
                                        icon: 'error'
                                    })
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Fail!',
                                    text: error.response.data.message,
                                    icon: 'error'
                                })
                            });
                    }
                });
            },
            onSubmitOpenShiftForm: () => {
                const initialCashAmount = $('#shift-open-kas-awal').val(); // Ambil nilai kas awal

                // API URL untuk membuka shift
                const openCashierApiUrl = `/v1/open-cashier`;

                // Data yang akan dikirim ke API
                const data = {
                    initial_cash_amount: parseFloat(initialCashAmount)
                };


                // Request ke API menggunakan axios
                axios.post(openCashierApiUrl, data)
                    .then(response => {
                        if (response.data.status) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.data.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            })
                            $('#modalOpenShift').modal('hide')
                            $('#btn-close-shift').removeClass('d-none')
                        } else {
                            Swal.fire({
                                title: 'Fail!',
                                text: response.data.message,
                                icon: 'error'
                            })
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Fail!',
                            text: error.response.data.message,
                            icon: 'error'
                        })
                    });
            },
            onShowingModalSelectTable: () => {
                data.tables.length = 0
                axios.get('/v1/tables').then(response => {
                    data.tables = response.data.data
                    $('#modalSelectTable').modal('show')
                })
            },
            onSelectTable: (no_meja_data) => {
                data.value.table = no_meja_data
                $('#modalSelectTable').modal('hide')
            },
            onResetTable: () => {
                data.value.table = null
                $('#modalSelectTable').modal('hide')
            },
            onSelectProduct: (item) => {
                let match = 0
                if (item.is_variant) {
                    data.modal.product = item
                }
                data.value.products.forEach((pro, index) => {
                    if (pro.id === item.id) {
                        match++
                        data.value.products[index].quantity++
                    }
                })

                if (!match) {
                    let variant = item.variant
                    variant.forEach((item, index) => {
                        variant[index].quantity = 0
                        variant[index].note = null
                    })

                    // jika belum ada
                    data.value.products.push({
                        ...item,
                        quantity: 1,
                        variant
                    })
                } else {
                    //
                }

                setTimeout(() => {
                    if (item.is_variant) {
                        data.modal.variantGroupActive = item.variant_groups
                        console.log(data.modal.variantGroupActive);
                        $('#modalVarian').modal('show')
                    }
                }, 300);
            },
            addNoteForVariant: (item) => {
                data.modal.variantActive = item
                setTimeout(() => {
                    $('#addNoteForVariantModal').modal('show')
                }, 100)
            },
            onSubmitVariantAddNote: () => {
                const currVar = data.modal.variantActive
                const product = data.modal.product
                data.value.products.forEach((pro, index) => {
                    if (pro.id === product.id) {
                        data.value.products[index].variant.forEach((variant, varIndex) => {
                            if (variant.id === currVar.id) {
                                data.value.products[index].variant[varIndex].note =
                                    currVar.note
                            }
                        })
                    }
                })
                $('#addNoteForVariantModal').modal('hide')
            }
        }

        methods.init()

        return {
            data,
            methods
        };
    }
}).mount('#app');