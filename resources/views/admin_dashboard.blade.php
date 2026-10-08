<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Dashboard | {{ setting('institution_name', 'eBoard System') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- App favicon -->
    <!-- Plugins css -->
    <link href="{{ asset('backend/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/selectize/css/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" />
    <!-- Plugins css -->
    <link href="{{asset('backend/assets/libs/quill/quill.core.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/assets/libs/quill/quill.bubble.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/assets/libs/quill/quill.snow.css')}}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style"/>
    <!-- icons -->
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Modern theme overrides -->
    <link href="{{ asset('backend/assets/css/custom-modern.css') }}?v={{ filemtime(public_path('backend/assets/css/custom-modern.css')) }}" rel="stylesheet" type="text/css" />
    <!-- toastr -->

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >
    <!-- toastr -->

    <!-- Head js -->
    <script src="{{ asset('backend/assets/js/head.js') }}"></script>


    <!-- dataTables -->
    <link href="{{ asset('backend/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables.net-select-bs5/css//select.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- dataTables end -->


    @stack('styles')


</head>

<!-- body start -->
<body data-layout-mode="default" data-theme="light" data-topbar-color="dark" data-menu-position="fixed" data-leftbar-color="dark" data-leftbar-size='default' data-sidebar-user='false'>

<!-- Begin page -->
<div id="wrapper">


    <!-- Topbar Start -->
  @include('body.header')
    <!-- end Topbar -->

    <!-- ========== Left Sidebar Start ========== -->
    @include('body.sidebar')
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->


    <div class="content-page">

        @yield('admin')

        <!-- Footer Start -->
        @include('body.footer')
        <!-- end Footer -->

    </div>

    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->


</div>
<!-- END wrapper -->

<!-- Right Sidebar -->

<!-- /Right-bar -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<!-- Vendor js -->
<script src="{{ asset('backend/assets/js/vendor.min.js')}}"></script>

<!-- Plugins js-->
<script src="{{ asset('backend/assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js')}}"></script>

<script src="{{ asset('backend/assets/libs/selectize/js/standalone/selectize.min.js')}}"></script>
<!-- Plugins js -->
<script src="{{asset('backend/assets/libs/quill/quill.min.js')}}"></script>

<!-- Init js-->
<script src="{{asset('backend/assets/js/pages/form-quilljs.init.js')}}"></script>

<!-- Dashboar 1 init js-->
<!-- datatables js -->
<script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
<!-- third party js ends -->


<script src="{{ asset('backend/assets/js/pages/datatables.init.js') }}"></script>
<!-- Datatables Eend -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ asset('backend/assets/js/code.js') }}"></script>
<script src="{{ asset('backend/assets/js/validate.min.js') }}"></script>

<!-- App js-->
<script src="{{ asset('backend/assets/js/app.min.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    @if(Session::has('message'))
    var type = "{{ Session::get('alert-type','info') }}"
    switch(type){
        case 'info':
            toastr.info(" {{ Session::get('message') }} ");
            break;
        case 'success':
            toastr.success(" {{ Session::get('message') }} ");
            break;
        case 'warning':
            toastr.warning(" {{ Session::get('message') }} ");
            break;
        case 'error':
            toastr.error(" {{ Session::get('message') }} ");
            break;
    }
    @endif
</script>
<script>
    $(document).ready(function() {
        // Legacy support for #example
        if ($('#example').length && !$.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable({
                dom: 'Bfrtip',
                pageLength: 100,
                buttons: ['copy', 'csv', 'excel',
                    { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4' },
                    'print'
                ]
            });
        }
        // Legacy support for #example2
        if ($('#example2').length && !$.fn.DataTable.isDataTable('#example2')) {
            var table = $('#example2').DataTable({
                pageLength: 100,
                lengthChange: false,
                buttons: ['copy', 'excel',
                    { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4' }
                ]
            });
            table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        }

        // Modern DataTable initializer for .twwda-datatable
        $('.twwda-datatable').each(function() {
            var $tbl = $(this);
            if ($.fn.DataTable.isDataTable($tbl)) return;

            var colCount = $tbl.find('thead th').length;
            var title = $tbl.data('export-title') || 'TWWDA Report';

            // Build column filter row
            var filterRow = $('<tr class="dt-column-filters"></tr>');
            $tbl.find('thead th').each(function(i) {
                var text = $(this).text().trim();
                if (i === colCount - 1 && text.toLowerCase() === 'actions') {
                    filterRow.append('<th></th>');
                } else {
                    filterRow.append('<th><input type="text" class="form-control form-control-sm" placeholder="Filter ' + text + '" /></th>');
                }
            });
            $tbl.find('thead').append(filterRow);

            var dt = $tbl.DataTable({
                dom: "<'row align-items-center mb-3'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 text-center'B><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                order: [],
                responsive: true,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="mdi mdi-file-excel-outline"></i> Excel',
                        className: 'btn btn-sm',
                        title: title,
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="mdi mdi-file-pdf-box"></i> PDF',
                        className: 'btn btn-sm',
                        title: title,
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'print',
                        text: '<i class="mdi mdi-printer"></i> Print',
                        className: 'btn btn-sm',
                        title: title,
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="mdi mdi-file-delimited-outline"></i> CSV',
                        className: 'btn btn-sm',
                        title: title,
                        exportOptions: { columns: ':not(:last-child)' }
                    }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search records...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    paginate: {
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>'
                    }
                }
            });

            // Per-column filter
            $tbl.find('.dt-column-filters th').each(function(i) {
                var $input = $('input', this);
                $input.on('keyup change', function() {
                    if (dt.column(i).search() !== this.value) {
                        dt.column(i).search(this.value).draw();
                    }
                });
            });
        });
    });
</script>

<!-- Core JS first -->

<!-- Place stacks AFTER core scripts -->
@stack('scripts')


</body>
</html>
