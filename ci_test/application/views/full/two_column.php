<?php
    $this->load->view('inc/header_frontend.inc.php', $data);
    $this->load->view('inc/form_mast.php', $data);
    $this->load->view('inc/navigation.inc.php', $data);
    echo $page_content;
    $this->load->view('inc/footer_frontend.inc.php', $data);
?>