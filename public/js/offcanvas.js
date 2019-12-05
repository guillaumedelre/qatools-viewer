$(function () {
    'use strict';

    var toggleMenuIcons = function () {
        if (false === $('.offcanvas-collapse').hasClass('open')) {
            $('#slidebar i.fas').hide();
        } else {
            $('#slidebar i.fas').show();
        }
    }

    $('[data-toggle="offcanvas"]').on('click', function () {
        $('.offcanvas-collapse').toggleClass('open');
        toggleMenuIcons();
    })

    $('.datatable').DataTable();

    $('.dataTables_paginate').addClass('p-3')
    $('.dataTables_info').addClass('p-3 text-left')
    $('.dataTables_length').addClass('p-3')
    $('.dataTables_length > label').addClass('col')
    $('.dataTables_filter').addClass('p-3')

    toggleMenuIcons();
})
