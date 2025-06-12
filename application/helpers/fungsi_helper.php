<?php
function cek_login()
{
    $ci = &get_instance();
    $admin_session = $ci->session->userdata('id_admin');
    if ($admin_session) {
        redirect('home');
    }
}

function cek_masuk()
{
    $ci = &get_instance();
    $admin_session = $ci->session->userdata('id_admin');
    if ($admin_session) {
        redirect('home');
    }
}

function cek_not_login()
{
    $ci = &get_instance();
    $user_session = $ci->session->userdata('id_admin');
    if (!$user_session) {
        redirect('auth/login');
    }
}
