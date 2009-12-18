require_once('../../../GovernmentForms/GovernmentForms.class.php');
$gf = new GovernmentForms();
$gf->tcpdf_dir = '../tcpdf';
$gf->fpdi_dir = '../fpdi';

    $f941_obj = $gf->getFormObject( '941', 'US' );
    $f941_obj->setDebug(FALSE);
    $f941_obj->setShowBackground(TRUE);
    $f941_obj->year = 2009;
    $f941_obj->ein = '12-3456789';
    $f941_obj->name = 'John Doe';
    $f941_obj->trade_name = 'ABC Company';
    $f941_obj->address = '#1232 Main St';
    $f941_obj->city = 'New York';
    $f941_obj->state = 'NY';
    $f941_obj->zip_code = '12345';

    $f941_obj->quarter = array(1,2,3,4);
    $f941_obj->l1 = 10;
    $f941_obj->l2 = 9999.99;
    $f941_obj->l3 = 9999.99;
    $f941_obj->l5 = 9999.99;

    $f941_obj->l5a = 9999.99;
    $f941_obj->l5b = 9999.99;
    $f941_obj->l5c = 9999.99;

    $f941_obj->l7a = 0.02;
    $f941_obj->l7b = 9999.99;
    $f941_obj->l7c = 9999.99;

    $f941_obj->l9 = 30000.99;

    $f941_obj->l11 = 9999.99;
    $f941_obj->l12a = 9999.99;
    $f941_obj->l12b = 9999.99;

    $f941_obj->l15a = TRUE;
    $f941_obj->l15b = TRUE;

    $f941_obj->l16 = 'NY';

    $f941_obj->l17_month1 = 9999.99;
    $f941_obj->l17_month2 = 9999.99;
    $f941_obj->l17_month3 = 9999.99;

    $gf->addForm( $f941_obj );

$output = $gf->output( 'PDF' );
file_put_contents( '941.pdf', $output );

