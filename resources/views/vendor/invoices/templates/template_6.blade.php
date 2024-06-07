<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>DISEE - Invoice HTML5 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="invoices/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="invoices/fonts/font-awesome/css/font-awesome.min.css">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="invoices/img/favicon.ico" type="image/x-icon" >

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="invoices/css/style.css">
</head>
<body>

<!-- Invoice 6 start -->
<div class="invoice-6 invoice-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner clearfix">
                    <div class="invoice-info clearfix" id="invoice_wrapper">
                        <div class="invoice-headar">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="invoice-logo">
                                        <!-- logo started -->
                                        <div class="logo">
                                            <img src="invoices/img/logos/logo.png" alt="logo">
                                        </div>
                                        <!-- logo ended -->
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="invoice-contact-us">
                                        <h1>Contact Us</h1>
                                        <ul class="link">
                                            <li>
                                                <i class="fa fa-map-marker"></i> 169 Teroghoria, Bangladesh
                                            </li>
                                            <li>
                                                <i class="fa fa-envelope"></i> <a href="mailto:sales@hotelempire.com">info@themevessel.com</a>
                                            </li>
                                            <li>
                                                <i class="fa fa-phone"></i> <a href="tel:+55-417-634-7071">+00 123 647 840</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-contant">
                            <div class="invoice-top">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h1 class="invoice-name">Invoice</h1>
                                    </div>
                                    <div class="col-sm-6 mb-30">
                                        <div class="invoice-number-inner">
                                            <h2 class="name">Invoice No: #45613</h2>
                                            <p class="mb-0">Invoice Date: <span>21 Sep 2021</span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-30">
                                        <div class="invoice-number">
                                            <h4 class="inv-title-1">Invoice To</h4>
                                            <h2 class="name mb-10">Jhon Smith</h2>
                                            <p class="invo-addr-1 mb-0">
                                                Theme Vessel <br/>
                                                info@themevessel.com <br/>
                                                21-12 Green Street, Meherpur, Bangladesh <br/>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-30">
                                        <div class="invoice-number">
                                            <div class="invoice-number-inner">
                                                <h4 class="inv-title-1">Invoice From</h4>
                                                <h2 class="name mb-10">Animas Roky</h2>
                                                <p class="invo-addr-1 mb-0">
                                                    Apexo Inc  <br/>
                                                    billing@apexo.com <br/>
                                                    169 Teroghoria, Bangladesh <br/>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-center">
                                <div class="order-summary">
                                    <div class="table-outer">
                                        <table class="default-table invoice-table">
                                            <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Price</th>
                                                <th>VAT (20%)</th>
                                                <th>Total</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <tr>
                                                <td>Standard Plan</td>
                                                <td>$443.00 </td>
                                                <td>$921.80</td>
                                                <td>$9243</td>
                                            </tr>
                                            <tr>
                                                <td>Extra Plan</td>
                                                <td>$413.00 </td>
                                                <td>$912.80 </td>
                                                <td>$5943</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Due</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td><strong>$9,750</strong></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-bottom">
                                <div class="row">
                                    <div class="col-lg-7 col-md-7 col-sm-7">
                                        <div class="terms-conditions mb-30">
                                            <h3 class="inv-title-1 mb-10">Terms & Conditions</h3>
                                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy.
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                        <div class="payment-method mb-30">
                                            <h3 class="inv-title-1 mb-10">Payment Method</h3>
                                            <ul class="payment-method-list-1 text-14">
                                                <li><strong>Account No:</strong> 00 123 647 840</li>
                                                <li><strong>Account Name:</strong> Jhon Doe</li>
                                                <li><strong>Branch Name:</strong> xyz</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Invoice 6 end -->

<script src="invoices/js/jquery.min.js"></script>
<script src="invoices/js/jspdf.min.js"></script>
<script src="invoices/js/html2canvas.js"></script>
<script src="invoices/js/app.js"></script>
</body>
</html>
